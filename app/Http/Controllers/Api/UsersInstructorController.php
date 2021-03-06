<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\UsersInstructor;
use App\Imports\UsersInstructorImport;
use App\User;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Validator;

class UsersInstructorController extends ApiController
{
    public function uploadedFile(Request $request)
    {

        $import = new UsersInstructorImport();
        $import->import($request->fileUrl);

        return $this->apiResponse(ResultType::Error, $import->err, 'hatalar', 403);
    }

    public function index(Request $request)
    {
				$user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = UsersInstructor::query();

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if ($user->level == 1 && $data) {
					return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
				} elseif ($user->level != 1) {
					return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
				} else {
						return $this->apiResponse(ResultType::Error, null, 'Instructor Not Found', 0, 404);
				}
    }

    public function store(Request $request)
    {
			$user = User::find(Auth::id());
			if ($user->level == 1){
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'role' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new UsersInstructor();
        $data->user = request('user');
        $data->role = request('role');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'admin';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Instructor '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResultType::Success, $data, 'Instructor Created', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Instructor not saved', 500);
				}
			}
    }

    public function show($id)
    {
        $data = UsersInstructor::find($id);
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Instructor Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Instructor Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'nullable',
            'role' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = UsersInstructor::find($id);

        if ($data) {
            if (request('user') != '') {
                $data->user = request('user');
            }
            if (request('role') != '') {
                $data->role = request('role');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'section';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Instructor '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'Instructor Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'Instructor not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = UsersInstructor::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'Instructor Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

