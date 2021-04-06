<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\InstructorsGivesSections;
use App\Imports\InstructorsGivesSectionsImport;

use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Validator;

class InstructorsGivesSectionsController extends ApiController
{
    public function uploadedFile(Request $request)
    {

        $import = new InstructorsGivesSectionsImport();
        $import->import($request->fileUrl);

        return $this->apiResponse(ResaultType::Error, $import->err, 'hatalar', 403);
    }



    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = InstructorsGivesSections::query();

        switch ($user->level) {
            case 3:
								$query->join('users','users.id','=','instructors_gives_sections.instructor_id');
                $query->join('section','section.id','=','instructors_gives_sections.section_id');
								$query->join('course','course.id','=','section.course_id');
                $query->join('department','department.id','=','course.department_id');

                $query->where('department.faculty','=',$user->faculty_id);

								$query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'instructors_gives_sections.*');
            break;

            case 4:
								$query->join('users','users.id','=','instructors_gives_sections.instructor_id');
                $query->join('section','section.id','=','instructors_gives_sections.section_id');
								$query->join('course','course.id','=','section.course_id');
                $query->join('department','department.id','=','course.department_id');

                $query->where('course.department','=',$user->department_id);

								$query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'instructors_gives_sections.*');
            break;

            case 5:
								$query->join('users','users.id','=','instructors_gives_sections.instructor_id');
								$query->join('section','section.id','=','instructors_gives_sections.section_id');
								$query->join('course','course.id','=','section.course_id');
                $query->where('instructors_gives_sections.instructor_id','=',$user->id);

								$query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'instructors_gives_sections.*');
            break;
            case 6:
                // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
                // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
                return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
            break;
            default:
								$query->join('users','users.id','=','instructors_gives_sections.instructor_id');
								$query->join('section','section.id','=','instructors_gives_sections.section_id');
								$query->join('course','course.id','=','section.course_id');
								$query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'instructors_gives_sections.*');

            break;
        }

        if ($request->has('instructor'))
            $query->where('instructor_id', '=', $request->query('instructor'));
        if ($request->has('section'))
            $query->where('section_id', '=', $request->query('section'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
			$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
			switch ($user->level) {
				case 1:
					$validator = Validator::make($request->all(), [
							'instructor_id' => 'required',
							'section_id' => 'required',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new InstructorsGivesSections();
					$data->instructor_id = request('instructor_id');
					$data->section_id = request('section_id');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'InstructorsGivesSections';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'InstructorsGivesSections '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Created', 201);
					} else {
							return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections not saved', 500);
					}
					break;
					default:
          return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
        break;
      }
    }

    public function show($id)
    {
				$query = InstructorsGiveSections::query();
				$query->join('users','users.id','=','instructors_gives_sections.instructor_id');
				$query->join('section','section.id','=','instructors_gives_sections.section_id');
				$query->join('course','course.id','=','section.course_id');
				$query->where('instructors_gives_sections.id', '=', $id);
				$query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'instructors_gives_sections.*');
				$data = $query->get()->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'nullable',
            'section_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = InstructorsGivesSections::find($id);

        if ($data) {
            if (request('instructor_id') != '') {
                $data->instructor_id = request('instructor_id');
            }
            if (request('section_id') != '') {
                $data->section_id = request('section_id');
            }
            $data->save();
            if ($data) {
                $log = new Log();
                $log->area = 'InstructorsGivesSections';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'InstructorsGivesSections '.$data->id;
                $log->save();
                return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = InstructorsGivesSections::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

