<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\CourseOutcome;
use App\Log;
use App\Course;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class CourseOutcomeController extends ApiController
{
    public function index(Request $request)
    {
        // https://spaanalyzer.com/course-outcome : kayıtlı tüm outcome listesi
        // https://spaanalyzer.com/course-outcome?course=6 : 6 nolu kursa tanımlanmış outcome listesi

        $user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = CourseOutcome::query();

        switch ($user->level) {
            case 3:
                $query->join('course','course.id','=','course_outcome.course_id');
                $query->join('department','department.id','=','course.department_id');

                $query->where('department.faculty','=',$user->faculty_id);

                $query->select('course_outcome.*');
            break;

            case 4:
                $query->join('course','course.id','=','course_outcome.course_id');

                $query->where('course.department','=',$user->department_id);

                $query->select('course_outcome.*');
            break;

            case 5:
                $query->join('course','course.id','=','course_outcome.course_id');
                $query->join('section','section.course_id','=','course.id');
                $query->join('instructors_gives_sections','instructors_gives_sections.section_id','=','section.id');

                $query->where('instructors_gives_sections.instructor_email','=',$user->email);

                $query->select('course_outcome.*');
            break;
            case 6:
                // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
                // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
                return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
            break;
            default:
                $query->select('course_outcome.*');
            break;
        }

        if ($request->has('course'))
            $query->where('course_id', '=', $request->query('course'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CourseOutcome Not Found', 0, 404);
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
					$query->where('instructors_gives_sections.instructor_email','=',$user->email);
					$query->where('course.id','=',request('course_id'));
					$length = count($query->get());

					if($length == 0){
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
					}
						$query = Course::query();
						$query->join('section','section.course_id','=','course.id');
						$query->join('instructors_gives_sections','section.id','=','instructors_gives_sections.section_id');
						$query->where('instructors_gives_sections.instructor_email','=',$user->email);
						$query->where('course.id','=',request('course_id'));
						$length = count($query->get());

						if($length == 0){
							return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
						}

						$validator = Validator::make($request->all(), [
								'explanation' => 'required',
								'code' => 'required',
								'survey_average' => 'nullable',
								'measured_average' => 'nullable',
								'course_id' => 'required',
								]);
						if ($validator->fails()) {
								return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
						}
						$data = new CourseOutcome();
						$data->explanation = request('explanation');
						$data->code = request('code');
						$data->survey_average = request('survey_average');
						$data->measured_average = request('measured_average');
						$data->course_id = request('course_id');
						$data->save();
						if ($data) {
								$log = new Log();
								$log->area = 'courseOutcome';
								$log->areaid = $data->id;
								$log->user = Auth::id();
								$log->ip = \Request::ip();
								$log->type = 1;
								$log->info = 'CourseOutcome '.$data->id.' Created for the University '.$data->university;
								$log->save();
								return $this->apiResponse(ResaultType::Success, $data, 'CourseOutcome Created', 201);
						} else {
								return $this->apiResponse(ResaultType::Error, null, 'CourseOutcome not saved', 500);
						}
						break;
					default:
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
						break;
				}
    }

    public function show($id)
    {
        $data = CourseOutcome::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CourseOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CourseOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'explanation' => 'nullable',
            'code' => 'nullable',
            'survey_average' => 'nullable',
            'measured_average' => 'nullable',
            'course_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = CourseOutcome::find($id);

        if ($data) {
            if (request('explanation') != '') {
                $data->explanation = request('explanation');
            }
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('survey_average') != '') {
                $data->survey_average = request('survey_average');
            }
            if (request('measured_average') != '') {
                $data->measured_average = request('measured_average');
            }
            if (request('course_id') != '') {
                $data->course_id = request('course_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'courseOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'CourseOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'CourseOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'CourseOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = CourseOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'CourseOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

