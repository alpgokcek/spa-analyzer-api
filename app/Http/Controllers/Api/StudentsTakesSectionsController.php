<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentsTakesSections;
use App\Imports\StudentsTakesSectionsImport;

use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;
use Validator;

class StudentsTakesSectionsController extends ApiController
{

  public function uploadedFile(Request $request)
  {
      $import = new StudentsTakesSectionsImport();
      $import->import($request->fileUrl);

      // return($import->err);
      return $this->apiResponse(ResaultType::Error, $import->err, 'hatalar', 403);
  }

  public function index(Request $request)
  {
    $user = User::find(Auth::id());
    $offset = $request->offset ? $request->offset : 0;
    $limit = $request->limit ? $request->limit : 99999999999999;
    $query = StudentsTakesSections::query();

    switch ($user->level) {
      case 3:
        //********************************************* */
        $query->join('users','users.student_id','=','students_takes_sections.student_id');
        $query->join('section','section.id','=','students_takes_sections.section_id');
        $query->join('course','course.id','=','section.course_id');
        $query->join('department','department.id','=','course.department_id');
        $query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'students_takes_sections.*');
      break;
			case 4:
        //********************************************* */
        return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
      break;
      case 5:
        return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
      break;
      case 6:
				$query->join('users','users.student_id','=','students_takes_sections.student_id');
				$query->join('section','section.id','=','students_takes_sections.section_id');
				$query->join('course','course.id','=','section.course_id');
        $query->where('students_takes_sections.student_id','=',$user->student_id);
        $query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'students_takes_sections.*');
      break;
      default:
        $query->join('users','users.student_id','=','students_takes_sections.student_id');
        $query->join('section','section.id','=','students_takes_sections.section_id');
				$query->join('course','course.id','=','section.course_id');
        $query->select('course.code as course_code', 'course.id as course_id', 'course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'students_takes_sections.*');
      break;
      }
      if ($request->has('student'))
        $query->where('users.student_id', '=', $request->query('student'));
      if ($request->has('section'))
        $query->where('section_id', '=', $request->query('section'));

      $length = count($query->get());
      $data = $query->offset($offset)->limit($limit)->get();
      if ($data) {
        return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
      } else {
        return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections Not Found', 0, 404);
      }
    }

    public function store(Request $request)
    {
			$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
			switch ($user->level) {
				case 1:
					$validator = Validator::make($request->all(), [
							'student_id' => 'required',
							'section_id' => 'required',
							'letter_grade' => 'nullable',
							'average' => 'nullable',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					if($split){
						foreach ($split as $key) {
							$user = Users::where('student_id','=', request('student_id'))->first();
							if ($user){
									$section = Section::where('id','=', request('section_id'))->first();
									if ($section) {
											$data = new StudentsTakesSections();
											$data->student_id = request('student_id');
											$data->section_id = request('section_id');
											$data->letter_grade = request('letter_grade');
											$data->average = request('average');
											$data->save();
											if ($data) {
													$log = new Log();
													$log->area = 'StudentsTakesSections';
													$log->areaid = $data->id;
													$log->user = Auth::id();
													$log->ip = \Request::ip();
													$log->type = 1;
													$log->info = 'StudentsTakesSections '.$data->id.' Created for the University '.$data->university;
													$log->save();
													return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Created', 201);
											} else {
													return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not saved', 500);
											}
									} else {
											return $this->apiResponse(ResaultType::Error, null, 'Section not found', 404);
									}
							} else {
									return $this->apiResponse(ResaultType::Error, null, 'User not found', 404);
							}
						}
					} else{
								$user = Users::where('student_id','=', request('student_id'))->first();
								if ($user){
										$section = Section::where('id','=', request('section_id'))->first();
										if ($section) {
												$data = new StudentsTakesSections();
												$data->student_id = request('student_id');
												$data->section_id = request('section_id');
												$data->letter_grade = request('letter_grade');
												$data->average = request('average');
												$data->save();
												if ($data) {
														$log = new Log();
														$log->area = 'StudentsTakesSections';
														$log->areaid = $data->id;
														$log->user = Auth::id();
														$log->ip = \Request::ip();
														$log->type = 1;
														$log->info = 'StudentsTakesSections '.$data->id.' Created for the University '.$data->university;
														$log->save();
														return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Created', 201);
												} else {
														return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not saved', 500);
												}
										} else {
												return $this->apiResponse(ResaultType::Error, null, 'Section not found', 404);
										}
								} else {
										return $this->apiResponse(ResaultType::Error, null, 'User not found', 404);
								}
						}
				break;
				default:
					return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
				break;
		}

    }

    public function show($id)
    {
        #$data = StudentsTakesSections::find($id);
				$query = StudentsTakesSections::query();
				$query->join('users','users.student_id','=','students_takes_sections.student_id');
        $query->join('section','section.id','=','students_takes_sections.section_id');
				$query->join('course','course.id','=','section.course_id');
        $query->where('students_takes_sections.id', '=', $id);
        $query->select('course.code as course_code', 'course.id as course_id', 'users.department_id as department_id','course.title as course_name', 'users.name as user_name', 'section.title as section_title', 'students_takes_sections.*');
				$data = $query->get()->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable',
            'section_id' => 'nullable',
            'letter_grade' => 'nullable',
            'average' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentsTakesSections::find($id);

        if ($data) {
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('section_id') != '') {
                $data->section_id = request('section_id');
            }
            if (request('letter_grade') != '') {
                $data->letter_grade = request('letter_grade');
            }
            if (request('average') != '') {
                $data->average = request('average');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentsTakesSections';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentsTakesSections '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = StudentsTakesSections::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

