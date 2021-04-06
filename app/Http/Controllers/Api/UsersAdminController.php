<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\UsersAdmin;
use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class UsersAdminController extends ApiController
{
    public function index(Request $request)
    {
				$user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = UsersAdmin::query();

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if ($user->level == 1 && $data) {
            return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
				} elseif ($user->level != 1) {
					return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
				} else {
            return $this->apiResponse(ResultType::Error, null, 'Admin Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
				$user = User::find(Auth::id());
				if ($user->level == 1){
        $validator = Validator::make($request->all(), [
            'user' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Admin();
        $data->user = request('user');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'admin';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Admin '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResultType::Success, $data, 'Admin Created', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Admin not saved', 500);
				}
			}
    }

    public function show($id)
    {
        $data = UsersAdmin::find($id);
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Admin Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Admin Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = UsersAdmin::find($id);

        if ($data) {
            if (request('user') != '') {
                $data->user = request('user');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'section';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Admin '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'Admin Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'Admin not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = UsersAdmin::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'Admin Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

