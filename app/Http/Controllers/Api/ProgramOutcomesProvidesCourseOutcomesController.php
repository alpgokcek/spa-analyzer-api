<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\ProgramOutcomesProvidesCourseOutcomes;
use App\Log;
use App\User;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProgramOutcomesProvidesCourseOutcomesController extends ApiController
{
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = ProgramOutcomesProvidesCourseOutcomes::query();
        switch ($user->level) {
            case 3:
                $query->join('program_outcome','program_outcome.id','=','program_outcomes_provides_course_outcomes.program_outcome_id');
                $query->join('department','department.id','=','program_outcome.department_id');

                $query->where('department.faculty','=',$user->faculty_id);

                $query->select('program_outcomes_provides_course_outcomes.*');
            break;

			case 4:
                $query->join('program_outcome','program_outcome.id','=','program_outcomes_provides_course_outcomes.program_outcome_id');

                $query->where('program_outcome.department_id', '=', $user->department_id);


                $query->select('program_outcomes_provides_course_outcomes.*');
            break;

            case 5:
                $query->join('course_outcome','course_outcome.id','=','program_outcomes_provides_course_outcomes.course_outcome_id');
                $query->join('course','course.id','=','course_outcome.course_id');
                $query->join('section','section.course_id','=','course.id');
                $query->join('instructors_gives_sections','instructors_gives_sections.section_id','=','section.id');

                $query->where('instructors_gives_sections.instructor_email','=',$user->email);

                $query->select('program_outcomes_provides_course_outcomes.*');
            break;
            case 6:
                // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
                // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
                return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
            break;
            default:
                $query->select('program_outcomes_provides_course_outcomes.*');
            break;
        }
        if ($request->has('courseOutcome'))
            $query->where('course_outcome_id', '=', $request->query('courseOutcome'));
        if ($request->has('programOutcome'))
            $query->where('program_outcome_id', '=', $request->query('programOutcome'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes Not Found', 0, 404);
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
					$validator = Validator::make($request->all(), [
							'course_outcome_id' => 'required',
							'program_outcome_id' => 'required',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new ProgramOutcomesProvidesCourseOutcomes();
					$data->course_outcome_id = request('course_outcome_id');
					$data->program_outcome_id = request('program_outcome_id');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'ProgramOutcomesProvidesCourseOutcomes';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'ProgramOutcomesProvidesCourseOutcomes '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Created', 201);
					} else {
							return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes not saved', 500);
					}
					break;
					default:
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
					break;
				}
    }

    public function show($id)
    {
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'course_outcome_id' => 'nullable',
            'program_outcome_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);

        if ($data) {
            if (request('course_outcome_id') != '') {
                $data->course_outcome_id = request('course_outcome_id');
            }
            if (request('program_outcome_id') != '') {
                $data->program_outcome_id = request('program_outcome_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'ProgramOutcomesProvidesCourseOutcomes';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'ProgramOutcomesProvidesCourseOutcomes '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

