<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Section;
use App\Imports\SectionImport;
use App\User;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Validator;

class SectionController extends ApiController
{

    public function uploadedFile(Request $request)
    {

        $import = new SectionImport();
        $import->import($request->fileUrl);

        return $this->apiResponse(ResultType::Error, $import->err, 'hatalar', 403);
    }

    public function index(Request $request)
    {
      $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
      $offset = $request->offset ? $request->offset : 0;
      $limit = $request->limit ? $request->limit : 99999999999999;
      $query = Section::query();

      switch ($user->level) {
        case 3:
          $query->join('course', 'course.id', '=', 'section.course_id');
          $query->join('department', 'department.id', '=', 'course.department_id');
          $query->join('faculty', 'faculty.id', '=', 'department.faculty');
          $query->join('users', 'users.faculty_id','=','faculty.id');
          $query->where('users.id','=',$user->id);
          break;
        case 4:
          $query->join('course', 'course.id', '=', 'section.course_id');
          $query->join('department', 'department.id', '=', 'course.department_id');
          $query->join('users', 'users.department_id','=','department.id');
          $query->where('users.id','=',$user->id);
        break;
        case 5:
					$query->join('instructors_gives_sections', 'instructors_gives_sections.section_id', '=', 'section.id');
					$query->join('users', 'users.email','=','instructors_gives_sections.instructor_id');
					$query->join('faculty', 'faculty.id', '=', 'users.faculty_id');
					$query->join('department', 'department.faculty', '=', 'faculty.id');
					$query->join('course', 'course.department_id', '=', 'department.id');
					$query->where('instructors_gives_sections.instructor_id','=',$user->id);
					$query->where('course.id','=','course_id');
          break;
        case 6:
          // 6. seviyenin bu ekranda işi olmadığı için 403 verip gönderiyoruz.
          // 403ün yönlendirme fonksiyonu vue tarafında gerçekleştirilecek.
          return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
          break;
        default:
          // 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
          $query->join('course', 'course.id', '=', 'section.course_id');
        break;
      }
      // örnek olarak tüm assessment tablosunun yanında user.name değerini almak için
			// 'assessment.*', 'users.name as userName'...

			if ($request->has('course'))
				$query->where('course_id', intval($request->query('course')));

      $query->select('section.*', 'course.title as courseName', 'course.year_and_term as year_and_term', 'course.code as course_code', 'course.credit as credit');
      // bu örnek üzerinden yeni değerler gönderebilirsiniz.
      $length = count($query->get());
      $data = $query->offset($offset)->limit($limit)->get();
      if ($length >= 1) {
        return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
      } else {
        return $this->apiResponse(ResultType::Error, null, 'Section Not Found', 0, 404);
    }
  }

  public function store(Request $request)
  {
    $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
    switch ($user->level) {
      case 1:
        $validator = Validator::make($request->all(), [
          'course_id' => 'required',
          'title' => 'required',
          'status' => 'required'
        ]);
        if ($validator->fails()) {
          return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Section();
        $data->course_id = request('course_id');
        $data->is_file_uploaded = request('is_file_uploaded') || '0';
        $data->title = request('title');
        $data->status = request('status');
        $data->save();
        if ($data) {
          $log = new Log();
          $log->area = 'section';
          $log->areaid = $data->id;
          $log->user = Auth::id();
          $log->ip = \Request::ip();
          $log->type = 1;
          $log->info = 'Section '.$data->id.' Created for the University '.$data->university;
          $log->save();
          return $this->apiResponse(ResultType::Success, $data, 'Section Created', 201);
        } else {
          return $this->apiResponse(ResultType::Error, null, 'Section not saved', 500);
        }
        break;
      default:
        return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
      break;
    }
	}

    public function show($id)
    {
			$query = Section::query();
			$query->join('course', 'course.id', '=', 'section.course_id');
			$query->where('section.id', '=', $id);
      $query->select('section.*', 'course.title as courseName', 'course.year_and_term as year_and_term', 'course.code as course_code', 'course.credit as credit', 'course.department_id as department_id');
			$data = $query->get()->first();
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Section Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Section Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'code' => 'nullable',
            'status' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Section::find($id);

        if ($data) {
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            $data->status = request('status');
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'section';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Section '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'Section Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'Section not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Section::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'Section Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

