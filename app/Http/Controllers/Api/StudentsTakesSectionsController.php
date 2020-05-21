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

                $query->where('users.student_id','=',$user->id);

                $query->select('users.*', 'user.name as userName', 'section.title as sectionTitle');
            break;

			case 4:
                //********************************************* */
                $query->join('users','users.student_id','=','students_takes_sections.student_id');
                $query->join('section','section.id','=','students_takes_sections.section_id');
                $query->join('course','course.id','=','section.course_id');
                $query->join('faculty','faculty.id','=','course.faculty_id');

                $query->where('users.student_id','=',$user->id);

                $query->select('users.*');
            break;

            case 5:
                $query->join('instructors_gives_sections','instructors_gives_sections.section_id','=','students_takes_sections.section_id');

                $query->where('instructors_gives_sections.instructor_id','=',$user->id);

                $query->select('students_takes_sections.*');
            break;

            case 6:
                $query->where('students_takes_sections.student_id','=',$user->id);

                $query->select('students_takes_sections.*');
            break;

            default:
                $query->join('users','users.student_id','=','students_takes_sections.student_id');
                $query->join('section','section.id','=','students_takes_sections.section_id');
                $query->select('students_takes_sections.*', 'user.name as userName', 'section.title as sectionTitle');
            break;
        }

        if ($request->has('student'))
            $query->where('student_code', '=', $request->query('student'));
        if ($request->has('section'))
            $query->where('section_code', '=', $request->query('section'));

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
							'student_code' => 'required',
							'section_code' => 'required',
							'letter_grade' => 'nullable',
							'average' => 'nullable',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					foreach ($split as $key) {
							$user = UsersStudent::where('id','=', request('student_code'))->first();
							if ($user){
									$section = Section::where('id','=', request('section_code'))->first();
									if ($section) {
											$data = new StudentsTakesSections();
											$data->student_code = request('student_code');
											$data->section_code = request('section_code');
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
        $data = StudentsTakesSections::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_code' => 'nullable',
            'section_code' => 'nullable',
            'letter_grade' => 'nullable',
            'average' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentsTakesSections::find($id);

        if ($data) {
            if (request('student_code') != '') {
                $data->student_code = request('student_code');
            }
            if (request('section_code') != '') {
                $data->section_code = request('section_code');
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

