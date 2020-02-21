<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\DepartmentsHasInstructors;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class DepartmentsHasInstructorsController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = DepartmentsHasInstructors::query();

        if ($request->has('department'))
            $query->where('department_id', '=', $request->query('department'));

        if ($request->has('instructor'))
            $query->where('instructor_id', '=', $request->query('instructor'));


        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'DepartmentsHasInstructors Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
            'instructor_id' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new DepartmentsHasInstructors();
        $data->department_id = request('department_id');
        $data->instructor_id = request('instructor_id');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'departmentsHasInstructors';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'DepartmentsHasInstructors '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'DepartmentsHasInstructors Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'DepartmentsHasInstructors not saved', 500);
        }
    }

    public function show($id)
    {
        $data = DepartmentsHasInstructors::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'DepartmentsHasInstructors Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'DepartmentsHasInstructors Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'nullable',
            'instructor_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = DepartmentsHasInstructors::find($id);

        if ($data) {
            if (request('department_id') != '') {
                $data->department_id = request('department_id');
            }
            if (request('instructor_id') != '') {
                $data->instructor_id = request('instructor_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'departmentsHasInstructors';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'DepartmentsHasInstructors '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'DepartmentsHasInstructors Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'DepartmentsHasInstructors not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = DepartmentsHasInstructors::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'DepartmentsHasInstructors Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

