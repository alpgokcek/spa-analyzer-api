<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Department;
use App\Faculty;
use App\Log;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\DepartmentResource;

use Illuminate\Http\Request;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class DepartmentController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $type = $request->type ? $request->type : null;
        $query = Department::query();
        $length = 1;
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('status'))
            $query->where('department.status', '=', $request->query('status'));


        if ($request->has('faculty')) {
            $faculty = Faculty::where('faculty.token','=',$request->query('faculty'))->first();
            $query->where('department.faculty', $faculty->id);
        }
        $query->join('faculty','faculty.id','=','department.faculty');
        $query->join('university','university.id','=','faculty.university');
        $query->select('department.*','faculty.title as facultyName','faculty.token','university.name as universityName', 'university.id as university');
        $data = $query->offset($offset)->limit($limit)->get();

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Success, null, 'Content Not Found', 0, 202);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty' => 'required',
            'title' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Department();
        $data->faculty = request('faculty');
        $data->title = request('title');
        $data->status = request('status');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'department';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Department '.$data->id.' Created for the Faculty '.$data->faculty;
            $log->save();

            return $this->apiResponse(ResaultType::Success, $data, 'Department Added', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Department not Added', 500);
        }
    }

    public function show($id)
    {
        $data = Department::where('department.id','=', $id)->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Department Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Department::find($id);
        if (request('title')) {
            $data->title = request('title');
        }
        $data->status = request('status');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'department';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 2;
            $log->info = 'Department '.$data->id.' Updated in Faculty '.$data->faculty;
            $log->save();

            return $this->apiResponse(ResaultType::Success, $data, 'Department Updated', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
        }
    }

    public function destroy($id)
    {
        $data = Department::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Department Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

