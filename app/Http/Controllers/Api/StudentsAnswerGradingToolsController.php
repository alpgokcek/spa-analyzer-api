<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentsAnswerGradingTools;
use App\Log;
use App\User;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class StudentsAnswerGradingToolsController extends ApiController
{
    public function index(Request $request)
    {
				$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentsAnswerGradingTools::query();
        switch ($user->level) {
		    case 3:
						$query->join('users','users.student_id','=','student_answers_grading_tool.student_id');
						$query->join('grading_tool','grading_tool.id','=','student_answers_grading_tool.grading_tool_id');
						$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
						$query->join('course','course.id','=','assessment.course_id');
            $query->where('users.faculty_id','=',$user->faculty_id);
            $query->where('users.level','=','6');
						$query->select('assessment.name as assessment_name', 'assessment.id as assessment_id', 'course.code as course_code', 'course.id as course_id', 'users.department_id as department_id', 'course.title as course_name', 'course.year_and_term as year_and_term', 'users.name as user_name', 'grading_tool.question_number as question_number', 'student_answers_grading_tool.*');
            break;
				case 4:
						$query->join('users','users.student_id','=','student_answers_grading_tool.student_id');
						$query->join('grading_tool','grading_tool.id','=','student_answers_grading_tool.grading_tool_id');
						$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
						$query->join('course','course.id','=','assessment.course_id');
						$query->where('users_student.department_id','=',$user->department_id);
            $query->where('users.level','=','6');
						$query->select('assessment.name as assessment_name', 'assessment.id as assessment_id', 'course.code as course_code', 'course.id as course_id', 'users.department_id as department_id', 'course.title as course_name', 'course.year_and_term as year_and_term', 'users.name as user_name', 'grading_tool.question_number as question_number', 'student_answers_grading_tool.*');
						break;
				case 5:
					$query->join('users','users.student_id','=','student_answers_grading_tool.student_id');
					$query->join('grading_tool','grading_tool.id','=','student_answers_grading_tool.grading_tool_id');
					$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
					$query->join('course','course.id','=','assessment.course_id');
					$query->join('students_takes_sections', 'students_takes_sections.student_id', '=', 'student_answers_grading_tool.student_id');
          $query->join('instructors_gives_sections', 'instructors_gives_sections.section_id', '=', 'students_takes_sections.section_id');

					$query->where('instructors_gives_sections.instructor_id','=',$user->id);
					$query->select('assessment.name as assessment_name', 'assessment.id as assessment_id', 'course.code as course_code', 'course.id as course_id', 'users.department_id as department_id', 'course.title as course_name', 'course.year_and_term as year_and_term', 'users.name as user_name', 'grading_tool.question_number as question_number', 'student_answers_grading_tool.*');

            break;
          case 6:
            // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
            // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
            return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
            break;
          default:
						// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
						//$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
						//$query->join('course', 'course.id', '=', 'assessment.course_id');
						$query->join('users','users.student_id','=','student_answers_grading_tool.student_id');
						$query->join('grading_tool','grading_tool.id','=','student_answers_grading_tool.grading_tool_id');
						$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
						$query->join('course','course.id','=','assessment.course_id');
						$query->select('assessment.name as assessment_name', 'assessment.id as assessment_id', 'course.code as course_code', 'course.id as course_id', 'users.department_id as department_id', 'course.title as course_name', 'course.year_and_term as year_and_term', 'users.name as user_name', 'grading_tool.question_number as question_number', 'student_answers_grading_tool.*');

          break;
        }
        if ($request->has('student'))
            $query->where('student_answers_grading_tool.student_id', '=', $request->query('student'));
        if ($request->has('gradingTool'))
            $query->where('student_answers_grading_tool.grading_tool_id', '=', $request->query('gradingTool'));
        if ($request->has('grade'))
            $query->where('student_answers_grading_tool.grade', '=', $request->query('grade'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'StudentsAnswerGradingTools Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
			$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
			switch ($user->level) {
				case 5:
					$query = Course::query();
					$query->join('section','section.course_id','=','course.id');
					$query->join('instructors_gives_sections','section.id','=','instructors_gives_sections.section_id');
					$query->where('instructors_gives_sections.instructor_id','=',$user->id);
					$query->where('course.id','=',request('course_id'));
					$length = count($query->get());

					if($length == 0){
						return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
					}
					$validator = Validator::make($request->all(), [
							'student_id' => 'required',
							'grading_tool_id' => 'required',
							'grade' => 'required',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new StudentsAnswerGradingTools();
					$data->student_id = request('student_id');
					$data->grading_tool_id = request('grading_tool_id');
					$data->grade = request('grade');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'StudentsAnswerGradingTools';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'StudentsAnswerGradingTools '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResultType::Success, $data, 'StudentsAnswerGradingTools Created', 201);
					} else {
							return $this->apiResponse(ResultType::Error, null, 'StudentsAnswerGradingTools not saved', 500);
					}
				break;
        default:
          return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
        break;
      }
    }

    public function show($id)
    {
				$query = StudentsAnswerGradingTools::query();
				$query->join('users','users.student_id','=','student_answers_grading_tool.student_id');
				$query->join('grading_tool','grading_tool.id','=','student_answers_grading_tool.grading_tool_id');
				$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
				$query->join('course','course.id','=','assessment.course_id');
				$query->where('student_answers_grading_tool.id', '=', $id);
				$query->select('assessment.name as assessment_name', 'assessment.id as assessment_id', 'course.code as course_code', 'course.id as course_id', 'users.department_id as department_id', 'course.title as course_name', 'course.year_and_term as year_and_term', 'users.name as user_name', 'grading_tool.question_number as question_number', 'student_answers_grading_tool.*');
				$data = $query->get()->first();

        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'StudentsAnswerGradingTools Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'StudentsAnswerGradingTools Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable',
            'grading_tool_id' => 'nullable',
            'grade' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentsAnswerGradingTools::find($id);

        if ($data) {
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('grading_tool_id') != '') {
                $data->grading_tool_id = request('grading_tool_id');
            }
            if (request('grade') != '') {
                $data->grade = request('grade');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentsAnswerGradingTools';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentsAnswerGradingTools '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'StudentsAnswerGradingTools Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'StudentsAnswerGradingTools not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = StudentsAnswerGradingTools::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'StudentsAnswerGradingTools Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

