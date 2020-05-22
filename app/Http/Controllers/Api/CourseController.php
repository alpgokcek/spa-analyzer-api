<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Course;
use App\Imports\CourseImport;

use App\User;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class CourseController extends ApiController
{
  public function uploadedFile(Request $request)
  {
    // dosya yükleme yetkisi hangi seviyelerde geçerli ?
    $import = new CourseImport();
    $import->import($request->fileUrl);
    return $this->apiResponse(ResaultType::Error, $import->err, 'hatalar', 403);
  }

  public function index(Request $request)
  {
    $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
    $offset = $request->offset ? $request->offset : 0;
    $limit = $request->limit ? $request->limit : 99999999999999;
    $query = Course::query();

    if ($request->has('department'))
      $query->where('department', $request->query('department'));

    if ($request->has('code'))
      $query->where('code', $request->query('code'));

    switch ($user->level) {
      case 3:
        $query->join('department','department.id','=','course.department_id');
        $query->join('faculty','faculty.id','=','department.faculty');
        $query->join('users', 'users.faculty_id','=','faculty.id');
        $query->where('users.id','=',$user->id);
      break;
      case 4:
        $query->join('department','department.id','=','course.department_id');
        $query->join('users', 'users.department_id','=','department.id');
        $query->where('users.id','=',$user->id);
      break;
      case 5:
        $query->join('section', 'section.course_id', '=', 'course.id');
        $query->join('instructors_gives_sections', 'instructors_gives_sections.section_id', '=', 'section.id');
        $query->join('department', 'department.id', '=', 'course.department_id');
        $query->join('users', 'users.department_id','=','department.id');
        $query->where('instructors_gives_sections.instructor_email','=',$user->email);
      break;
      case 6:
        return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
        break;
    default:
        $query->join('department','department.id','=','course.department_id');
      break;
    }
		$query->select('course.*', 'department.name as departmentName');
    $length = count($query->get());
    $data = $query->offset($offset)->limit($limit)->get();
    if ($length >= 1) {
      return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
    } else {
      return $this->apiResponse(ResaultType::Error, null, 'Course Not Found', 0, 404);
    }
  }

  public function store(Request $request)
  {
    $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
    switch ($user->level) {
      case 1:
        $validator = Validator::make($request->all(), [
          'department_id' => 'required',
          'code' => 'required',
          'year_and_term' => 'nullable',
          'title' => 'required',
          'credit' => 'nullable',
          'date_time' => 'nullable',
          'status' => 'required'
        ]);
        if ($validator->fails()) {
          return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Course();
        $data->department = request('department_id');
        $data->code = request('code');
        $data->year_and_term = request('year_and_term');
        $data->title = request('title');
        $data->credit = request('credit');
        $data->date_time = request('date_time');
        $data->status = request('status');
        $data->save();
        if ($data) {
          $log = new Log();
          $log->area = 'course';
          $log->areaid = $data->id;
          $log->user = Auth::id();
          $log->ip = \Request::ip();
          $log->type = 1;
          $log->info = 'Course '.$data->id.' Created for the Department '.$data->department;
          $log->save();
          return $this->apiResponse(ResaultType::Success, $data, 'Course Created', 201);
        } else {
          return $this->apiResponse(ResaultType::Error, null, 'Course not saved', 500);
        }
      default:
        return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
      break;
    }
  }

  public function show($id)
  {
    $data = Course::find($id)
    ->join('department_id','department.id','=','course.department')
    ->join('faculty','faculty.id','=','department.faculty')
    ->join('university','university.id','=','faculty.university')
    ->select('course.*','department.id as departmentID', 'faculty.id as facultyID', 'university.id as universityID','department.title as departmentTitle', 'faculty.title as facultyTitle', 'university.name as universityName')
    ->first();
    if ($data) {
      return $this->apiResponse(ResaultType::Success, $data, 'Course Detail', 201);
    } else {
      return $this->apiResponse(ResaultType::Error, null, 'Course Not Found', 404);
    }
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'code' => 'nullable',
      'year_and_term' => 'nullable',
      'title' => 'nullable',
      'credit' => 'nullable',
      'date_time' => 'nullable',
      'status' => 'nullable'
    ]);
    if ($validator->fails()) {
      return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
    }
    $data = Course::find($id);
    if ($data) {
      if (request('code') != '') {
        $data->code = request('code');
      }
      if (request('year_and_term') != '') {
        $data->year_and_term = request('year_and_term');
      }
      if (request('title') != '') {
        $data->title = request('title');
      }
      if (request('credit') != '') {
        $data->credit = request('credit');
      }
      if (request('date_time') != '') {
        $data->date_time = request('date_time');
      }
      if (request('status') != '') {
        $data->status = request('status');
      }
      $data->save();

      if ($data) {
        $log = new Log();
        $log->area = 'course';
        $log->areaid = $data->id;
        $log->user = Auth::id();
        $log->ip = \Request::ip();
        $log->type = 2;
        $log->info = 'Course '.$data->id.' Updated in Department '.$data->department;
        $log->save();
        return $this->apiResponse(ResaultType::Success, $data, 'Course Updated', 200);
      } else {
        return $this->apiResponse(ResaultType::Error, null, 'Course not updated', 500);
      }
    } else {
      return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
    }
  }

  public function destroy($id)
  {
    $data = Course::find($id);
    if ($data) {
      $data->delete();
      return $this->apiResponse(ResaultType::Success, $data, 'Course Deleted', 200);
    } else {
      return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
    }
  }
}
