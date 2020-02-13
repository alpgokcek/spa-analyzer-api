<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Faculty;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class FacultyController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Faculty::query();

        // 0: passive, 1: active, 2: complete
        $query->join('university','university.id','=','faculty.university');
        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');
        if ($request->has('university'))
            $query->where('university', '=', $request->query('university'));
        if ($request->has('status'))
            $query->where('faculty.status', '=', $request->query('status'));

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects, 'university.name as universityName');
        } else {
            $query->select('faculty.*','university.name as universityName');
        }

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Faculty Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'university' => 'required',
            'title' => 'required',
            'status' => 'required',
            'token' => 'unique:faculty,token'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Faculty();
        $data->university = request('university');
        $data->title = request('title');
        $data->status = request('status');
        $data->token = str_random(64);
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'faculty';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Faculty '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'Faculty Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Faculty not saved', 500);
        }
    }

    public function show($token)
    {
        $data = Faculty::where('token','=',$token)->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Faculty Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Faculty Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'status' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Faculty::where('token','=',$token)->first();

        if ($data) {
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            $data->status = request('status');
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'faculty';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Faculty '.$data->id.' Updated in University '.$data->university;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'Faculty Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Faculty not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($token)
    {
        $data = Faculty::where('token','=',$token)->first();
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Faculty Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

