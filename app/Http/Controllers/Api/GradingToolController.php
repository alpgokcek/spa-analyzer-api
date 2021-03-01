<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\GradingTool;
use App\Log;
use App\User;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class GradingToolController extends ApiController
{
    public function index(Request $request)
    {
				$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
				$query = GradingTool::query();

        switch ($user->level) {
		    case 3:
						$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
            $query->join('course', 'course.id', '=', 'assessment.course_id');
            $query->join('department', 'department.id', '=', 'course.department_id');

						$query->where('department.faculty','=',$user->faculty_id);

						$query->select('grading_tool.*', 'course.title as courseName', 'course.id as course_id', 'assessment.name as assessmentName');

            break;
				case 4:
            $query->join('assessment', 'assessment.id', '=', 'grading_tool.assessment_id');
            $query->join('course', 'course.id', '=', 'assessment.course_id');
            $query->join('department', 'department.id', '=', 'course.department_id');

						$query->where('department.id','=',$user->department_id);

						$query->select('grading_tool.*', 'course.title as courseName', 'course.id as course_id', 'assessment.name as assessmentName');

          break;
					case 5:
						$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
            $query->join('course', 'course.id', '=', 'assessment.course_id');
            $query->join('section', 'section.course_id', '=', 'course.id');
            $query->join('instructors_gives_sections', 'instructors_gives_sections.section_id', '=', 'section.id');

						$query->where('instructors_gives_sections.instructor_email','=',$user->email);

						$query->select('grading_tool.*', 'course.title as courseName', 'course.id as course_id', 'assessment.name as assessmentName');
            break;
          case 6:
            // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
            // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
            return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
            break;
          default:
						// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
							$query->join('assessment','assessment.id','=','grading_tool.assessment_id');
							$query->join('course', 'course.id', '=', 'assessment.course_id');
              $query->select('grading_tool.*', 'course.title as courseName', 'course.id as course_id', 'assessment.name as assessmentName');

          break;
        }

        if ($request->has('assessment'))
            $query->where('assessment_id', '=', $request->query('assessment'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'GradingTool Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
				$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
				switch ($user->level) {
					case 6:
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
						break;
					default:
              $query = Course::query();
              $query->join('assessment','assessment.course_id','=','course.id');
							$query->join('section','section.course_id','=','course.id');
              $query->join('instructors_gives_sections','section.id','=','instructors_gives_sections.section_id');

						$query->where('instructors_gives_sections.instructor_email','=',$user->email);
						$query->where('assessment.id','=',request('assessment_id'));
						$length = count($query->get());

						if($length == 0){
							return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
						}
					$validator = Validator::make($request->all(), [
							'assessment_id' => 'required',
							'question_number' => 'required',
							'percentage' => 'required',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new GradingTool();
					$data->assessment_id = request('assessment_id');
					$data->question_number = request('question_number');
					$data->percentage = request('percentage');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'GradingTool';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'GradingTool '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Created', 201);
					} else {
							return $this->apiResponse(ResaultType::Error, null, 'GradingTool not saved', 500);
					}
						break;

				}
		}

    public function show($id)
    {
        $data = GradingTool::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'GradingTool Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_number' => 'nullable',
            'percentage' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = GradingTool::find($id);

        if ($data) {
            if (request('question_number') != '') {
                $data->question_number = request('question_number');
            }
            if (request('percentage') != '') {
                $data->percentage = request('percentage');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'GradingTool';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'GradingTool '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'GradingTool not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = GradingTool::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

