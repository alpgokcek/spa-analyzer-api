<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Assessment;
use App\User;
use App\Log;
use App\Course;
use App\InstructorsGiveSections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class AssessmentController extends ApiController
{
    public function index(Request $request)
    {
      $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
      $offset = $request->offset ? $request->offset : 0;
      $limit = $request->limit ? $request->limit : 99999999999999;
      $query = Assessment::query();

      switch ($user->level) {
        case 3:
          $query->join('course', 'course.id', '=', 'assessment.course_id');
          $query->join('department', 'department.id', '=', 'course.department_id');
          $query->join('faculty', 'faculty.id', '=', 'department.faculty');
          $query->join('users', 'users.faculty_id','=','faculty.id');
          $query->where('users.id','=',$user->id);
          break;
        case 4:
          $query->join('course', 'course.id', '=', 'assessment.course_id');
          $query->join('department', 'department.id', '=', 'course.department_id');
          $query->join('users', 'users.department_id','=','department.id');
          $query->where('users.id','=',$user->id);
        break;
        case 5:
          $query->join('course', 'course.id', '=', 'assessment.course_id');
          $query->join('section', 'section.course_id', '=', 'course.id');
          $query->join('instructors_gives_sections', 'instructors_gives_sections.section_id', '=', 'section.id');
          $query->join('department', 'department.id', '=', 'course.department_id');
          $query->join('faculty', 'faculty.id', '=', 'department.faculty');
          $query->join('users', 'users.faculty_id','=','faculty.id');
          $query->where('instructors_gives_sections.instructor_id','=',$user->id);
          break;
        case 6:
          // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
          // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
          return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
          break;
        default:
          // 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
          $query->join('course', 'course.id', '=', 'assessment.course_id');
        break;
			}
			$query->where('course.id','=',$request->course);
      // örnek olarak tüm assessment tablosunun yanında user.name değerini almak için
      // 'assessment.*', 'users.name as userName'...
      $query->select('assessment.*', 'course.title as courseName');
      // bu örnek üzerinden yeni değerler gönderebilirsiniz.
      $length = count($query->get());
      $data = $query->offset($offset)->limit($limit)->get();
      if ($length >= 1) {
        return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
      } else {
        return $this->apiResponse(ResaultType::Error, null, 'Assessment Not Found', 0, 404);
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
					$query->join('section','section.course_id','=','course.id');
					$query->join('instructors_gives_sections','section.id','=','instructors_gives_sections.section_id');
					$query->where('instructors_gives_sections.instructor_id','=',$user->id);
					$query->where('course.id','=',request('course_id'));
					$length = count($query->get());

					if($length == 0){
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
					}
          $validator = Validator::make($request->all(), [
            'name' => 'required',
            'percentage' => 'required',
            'course_id' => 'required',
            ]);
          if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
          }
          $data = new Assessment();
          $data->name = request('name');
          $data->percentage = request('percentage');
          $data->course_id = request('course_id');
          $data->save();
          if ($data) {
            $log = new Log();
            $log->area = 'assessment';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'Assessment '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'Assessment Created', 201);
          } else {
            return $this->apiResponse(ResaultType::Error, null, 'Assessment not saved', 500);
          }
          break;
      }
    }

    public function show($id)
    {
      $data = Assessment::find($id);
      if ($data) {
        return $this->apiResponse(ResaultType::Success, $data, 'Assessment Detail', 201);
      } else {
        return $this->apiResponse(ResaultType::Error, null, 'Assessment Not Found', 0, 404);
      }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'percentage' => 'nullable',
            'course_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Assessment::find($id);

        if ($data) {
            if (request('name') != '' ) {
                $data->name = request('name');
            }
            if (request('percentage') != '' ) {
                $data->percentage = request('percentage');
            }
            if (request('course_id') != '' ) {
                $data->course_id = request('course_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'assessment';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Assessment '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'Assessment Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Assessment not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Assessment::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Assessment Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

