<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\GradingToolCoversCourseOutcome;
use App\Log;
use App\User;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class GradingToolCoversCourseOutcomeController extends ApiController
{
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = GradingToolCoversCourseOutcome::query();
        switch ($user->level) {
            case 3:
                $query->join('course_outcome','course_outcome.id','=','grading_tool_covers_course_outcome.course_outcome_id');
								$query->join('grading_tool','grading_tool.id','=','grading_tool_covers_course_outcome.grading_tool_id');
								$query->join('course','course.id','=','course_outcome.course_id');
                $query->join('department','department.id','=','course.department_id');
								$query->join('assessment','assessment.id','=','grading_tool.assessment_id');

                $query->where('department.faculty','=',$user->faculty_id);

                $query->select('assessment.name as assessment_name','course_outcome.code as co_code', 'course_outcome.course_id as course_id', 'grading_tool.question_number as question_number', 'grading_tool_covers_course_outcome.*');
            break;

            case 4:
                $query->join('course_outcome','course_outcome.id','=','grading_tool_covers_course_outcome.course_outcome_id');
								$query->join('grading_tool','grading_tool.id','=','grading_tool_covers_course_outcome.grading_tool_id');
                $query->join('course','course.id','=','course_outcome.course_id');
								$query->join('assessment','assessment.id','=','grading_tool.assessment_id');

                $query->where('course.department_id','=',$user->department_id);
                $query->select('assessment.name as assessment_name','course_outcome.code as co_code', 'course_outcome.course_id as course_id', 'grading_tool.question_number as question_number', 'grading_tool_covers_course_outcome.*');
            break;

            case 5:
                $query->join('course_outcome','course_outcome.id','=','grading_tool_covers_course_outcome.course_outcome_id');
								$query->join('grading_tool','grading_tool.id','=','grading_tool_covers_course_outcome.grading_tool_id');
                $query->join('course','course.id','=','course_outcome.course_id');
                $query->join('section','section.course_id','=','course.id');
                $query->join('instructors_gives_sections','instructors_gives_sections.section_id','=','section.id');
								$query->join('assessment','assessment.id','=','grading_tool.assessment_id');

                $query->where('instructors_gives_sections.instructor_id','=',$user->id);

                $query->select('assessment.name as assessment_name','course_outcome.code as co_code', 'course_outcome.course_id as course_id', 'grading_tool.question_number as question_number', 'grading_tool_covers_course_outcome.*');
            break;
            case 6:
                // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
                // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
                return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
            break;
            default:
				// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
                //$query->where('student_gets_measured_grade_program_outcome.student_id','=',$user->id);
								$query->join('course_outcome','course_outcome.id','=','grading_tool_covers_course_outcome.course_outcome_id');
								$query->join('grading_tool','grading_tool.id','=','grading_tool_covers_course_outcome.grading_tool_id');
								$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
                $query->select('assessment.name as assessment_name','course_outcome.code as co_code', 'course_outcome.course_id as course_id', 'grading_tool.question_number as question_number', 'grading_tool_covers_course_outcome.*');
            break;
        }

        if ($request->has('gradingTool'))
            $query->where('grading_tool_id', '=', $request->query('gradingTool'));
        if ($request->has('courseOutcome'))
						$query->where('course_outcome_id', '=', $request->query('courseOutcome'));
				if ($request->has('course'))
						$query->where('course_outcome.course_id', '=', $request->query('course'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'GradingToolCoversCourseOutcome Not Found', 0, 404);
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
            'grading_tool_id' => 'required',
            'course_outcome_id' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new GradingToolCoversCourseOutcome();
        $data->grading_tool_id = request('grading_tool_id');
        $data->course_outcome_id = request('course_outcome_id');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'GradingToolCoversCourseOutcome';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'GradingToolCoversCourseOutcome '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResultType::Success, $data, 'GradingToolCoversCourseOutcome Created', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'GradingToolCoversCourseOutcome not saved', 500);
				}
				break;
				default:
					return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
				break;
			}
		}

    public function show($id)
    {
        $data = GradingToolCoversCourseOutcome::find($id);
				$query = GradingToolCoversCourseOutcome::query();
				$query->join('course_outcome','course_outcome.id','=','grading_tool_covers_course_outcome.course_outcome_id');
				$query->join('grading_tool','grading_tool.id','=','grading_tool_covers_course_outcome.grading_tool_id');
				$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
				$query->where('grading_tool_covers_course_outcome.id', '=', $id);
				$query->select('assessment.name as assessment_name','course_outcome.code as co_code', 'course_outcome.course_id as course_id', 'grading_tool.question_number as question_number', 'grading_tool_covers_course_outcome.*');
				$data = $query->get()->first();
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'GradingToolCoversCourseOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'GradingToolCoversCourseOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'grading_tool_id' => 'nullable',
            'course_outcome_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = GradingToolCoversCourseOutcome::find($id);

        if ($data) {
            if (request('grading_tool_id') != '') {
                $data->grading_tool_id = request('grading_tool_id');
            }
            if (request('course_outcome_id') != '') {
                $data->course_outcome_id = request('course_outcome_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'GradingToolCoversCourseOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'GradingToolCoversCourseOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'GradingToolCoversCourseOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'GradingToolCoversCourseOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = GradingToolCoversCourseOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'GradingToolCoversCourseOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

