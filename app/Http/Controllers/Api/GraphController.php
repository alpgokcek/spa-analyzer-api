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

class GraphController extends Controller
{
    public function getMeasuredGradesForCO(Request $request){
        $user = $request->user();
        $user_level = $user->level;
        if($user_level == 1){
            echo "student";
        }
        elseif($user_level == 2){
            echo "instructor";
        }
        else{
            echo "admin";
        }
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
                return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'StudentGetsMeasuredGradeCourseOutcome Not Found', 0, 404);
            }
        } else{
            return $this->apiResponse(ResultType::Error, null, 'Course ID is needed for this operation', 0, 400);
        }
    }
}
