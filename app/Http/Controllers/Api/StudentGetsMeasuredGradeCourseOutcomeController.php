<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentGetsMeasuredGradeCourseOutcome;
use App\CourseOutcome;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class StudentGetsMeasuredGradeCourseOutcomeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentGetsMeasuredGradeCourseOutcome::query();
        if($request->has('course')){
            $testQuery = DB::table('course_outcome')->join('student_gets_measured_grade_course_outcome', 'course_outcome.id', '=', 'course_outcome_id')->where('course_outcome.course_id', $request->query('course'));

            if ($request->has('student'))
                $testQuery->where('student_id', '=', $request->query('student'));
            if ($request->has('courseOutcome'))
                $testQuery->where('course_outcome_id', '=', $request->query('courseOutcome'));
            if ($request->has('grade'))
                $testQuery->where('grade', '=', $request->query('grade'));

            $length = count($testQuery->get());//->get());
            $data = $testQuery->offset($offset)->limit($limit)->get();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeCourseOutcome Not Found', 0, 404);
            }
        } else{
            return $this->apiResponse(ResaultType::Error, null, 'Course ID is needed for this operation', 0, 400);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_outcome_id' => 'required',
            'student_id' => 'required',
            'grade' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new StudentGetsMeasuredGradeCourseOutcome();
        $data->course_outcome_id = request('course_outcome_id');
        $data->student_id = request('student_id');
        $data->grade = request('grade');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'StudentGetsMeasuredGradeCourseOutcome';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'StudentGetsMeasuredGradeCourseOutcome '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeCourseOutcome Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeCourseOutcome not saved', 500);
        }
    }

    public function show($id)
    {
        $data = StudentGetsMeasuredGradeCourseOutcome::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeCourseOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeCourseOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'course_outcome_id' => 'nullable',
            'student_id' => 'nullable',
            'grade' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentGetsMeasuredGradeCourseOutcome::find($id);

        if ($data) {
            if (request('course_outcome_id') != '') {
                $data->course_outcome_id = request('course_outcome_id');
            }
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('grade') != '') {
                $data->grade = request('grade');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentGetsMeasuredGradeCourseOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentGetsMeasuredGradeCourseOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeCourseOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeCourseOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = StudentGetsMeasuredGradeCourseOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeCourseOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

