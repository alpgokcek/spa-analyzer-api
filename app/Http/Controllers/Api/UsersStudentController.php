<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\UsersStudent;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class UsersStudentController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Section::query();

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Section Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'status' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Section();
        $data->user = request('user');
        $data->status = request('status');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'admin';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Section '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'Section Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Section not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Section::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Section Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Section Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'nullable',
            'status' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Section::find($id);

        if ($data) {
            if (request('user') != '') {
                $data->user = request('user');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            $data->status = request('status');
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'section';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Section '.$data->id.' Updated in University '.$data->university;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'Section Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Section not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Section::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Section Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

