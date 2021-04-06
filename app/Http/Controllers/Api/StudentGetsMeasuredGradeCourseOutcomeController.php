<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentGetsMeasuredGradeCourseOutcome;
use App\CourseOutcome;
use App\Log;
use App\User;
use App\Course;
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
        $user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentGetsMeasuredGradeCourseOutcome::query();

        switch ($user->level) {
            case 3:
                $query->join('users_student','users_student.id','=','student_gets_measured_grade_course_outcome.student_id');
                $query->join('department','department.id','=','users_student.department_id');
                $query->join('users','users.id','=','users_student.user_id');

                $query->where('department.faculty','=',$user->faculty_id);
                $query->where('users.level','=','6');

                $query->select('student_gets_measured_grade_course_outcome.*');
            break;

            case 4:
                $query->join('users_student','users_student.id','=','student_gets_measured_grade_course_outcome.student_id');
                $query->join('users','users.id','=','users_student.user_id');

                $query->where('users_student.department_id','=',$user->department_id);
                $query->where('users.level','=','6');

                $query->select('student_gets_measured_grade_course_outcome.*');
            break;

            case 5:
                $query->join('students_takes_sections','students_takes_sections.student_id','=','student_gets_measured_grade_course_outcome.student_id');
                $query->join('instructors_gives_sections','instructors_gives_sections.section_id','=','students_takes_sections.section_id');
                $query->join('users_student','users_student.id','=','student_gets_measured_grade_course_outcome.student_id');

								$query->where('instructors_gives_sections.instructor_id','=',$user->id);
								$query->select('student_gets_measured_grade_course_outcome.*');

            break;
          case 6:
            // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
            // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
            return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
            break;
          default:
					// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
                    //$query->where('student_gets_measured_grade_course_outcome.student_id','=',$user->id);
            $query->select('student_gets_measured_grade_course_outcome.*');
          break;
				}

          if ($request->has('student'))
              $testQuery->where('student_id', '=', $request->query('student'));
          if ($request->has('courseOutcome'))
              $testQuery->where('course_outcome_id', '=', $request->query('courseOutcome'));
          if ($request->has('grade'))
              $testQuery->where('grade', '=', $request->query('grade'));
					$length = count($query->get());
					$data = $query->offset($offset)->limit($limit)->get();
					if ($data) {
							return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
					} else {
							return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeProgramOutcome Not Found', 0, 404);
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
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
					}
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
				break;
				default:
					return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
				break;
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

