<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Course;
use App\Imports\CourseImport;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class CourseController extends ApiController
{
    public function uploadFile(Request $request)
    {
        echo "hereeee";
        $data = Excel::import(new CourseImport, $request->fileUrl);
        
        if ($data) {
            return response()->json('success', 201);
            
        } 
        else { return response()->json('error', 500);}

    }
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Course::query();

        $query->join('department','department.id','=','course.department');
        $query->join('faculty','faculty.id','=','department.faculty');
        $query->join('university','university.id','=','faculty.university');

        if ($request->has('department'))
            $query->where('department', $request->query('department'));

        if ($request->has('code'))
            $query->where('code', $request->query('code'));

            $length = count($query->get());
            $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Course Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department' => 'required',
            'code' => 'required',
            'year_and_term' => 'nullable',
            'title' => 'required',
            'credit' => 'nullable',
            'date_time' => 'nullable',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Course();
        $data->department = request('department');
        $data->code = request('code');
        $data->year_and_term = request('year_and_term');
        $data->title = request('title');
        $data->credit = request('credit');
        $data->date_time = request('date_time');
        $data->status = request('status');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'course';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Course '.$data->id.' Created for the Department '.$data->department;
            $log->save();

            return $this->apiResponse(ResaultType::Success, $data, 'Course Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Course not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Course::find($id)
        ->join('department','department.id','=','course.department')
        ->join('faculty','faculty.id','=','department.faculty')
        ->join('university','university.id','=','faculty.university')
        ->join('mix','mix.id','=','course.mix')
        ->select('course.*','department.id as departmentID', 'faculty.id as facultyID', 'university.id as universityID','department.title as departmentTitle', 'faculty.title as facultyTitle', 'university.name as universityName')
        ->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Course Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Course Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable',
            'year_and_term' => 'nullable',
            'title' => 'nullable',
            'credit' => 'nullable',
            'date_time' => 'nullable',
            'status' => 'nullable'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Course::find($id);

        if ($data) {
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('year_and_term') != '') {
                $data->year_and_term = request('year_and_term');
            }
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('credit') != '') {
                $data->credit = request('credit');
            }
            if (request('date_time') != '') {
                $data->date_time = request('date_time');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'course';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Course '.$data->id.' Updated in Department '.$data->department;
                $log->save();
                return $this->apiResponse(ResaultType::Success, $data, 'Course Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Course not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Course::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Course Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

