<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\UsersStudent;
use App\Imports\UsersStudentImport;
use App\User;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Validator;


class UsersStudentController extends ApiController
{
    public function uploadedFile(Request $request)
    {
			  $user = User::find(Auth::id());
        $import = new UsersStudentImport();
        $import->import($request->fileUrl);

        // return($import->err);
        return $this->apiResponse(ResultType::Error, $import->err, 'hatalar', 403);
    }

    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = UsersStudent::query();

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if ($user->level == 1 && $data) {
					return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
				} elseif ($user->level != 1) {
					return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
				} else {
						return $this->apiResponse(ResultType::Error, null, 'Student Not Found', 0, 404);
				}
		}

    public function store(Request $request)
    {
				$user = User::find(Auth::id());
				if ($user->level == 1){
					$validator = Validator::make($request->all(), [
							'user_id' => 'required',
							'advisor_user_id' => 'required',
							'department_id' => 'required',
							'is_major' => 'required',
							]);
					if ($validator->fails()) {
							return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new UsersStudent();
					$data->user = request('user');
					$data->status = request('status');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'admin';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'Student '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResultType::Success, $data, 'Student Created', 201);
					} else {
							return $this->apiResponse(ResultType::Error, null, 'Student not saved', 500);
					}
			}
    }

    public function show($id)
    {
        $data = UsersStudent::find($id);
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'Student Detail', 201);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Student Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'advisor_user_id' => 'nullable',
            'department_id' => 'nullable',
            'is_major' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = UsersStudent::find($id);

        if ($data) {
            if (request('advisor_user_id') != '') {
                $data->advisor = request('advisor');
            }
            if (request('department_id') != '') {
                $data->department = request('department');
            }
            if (request('is_major') != '') {
                $data->is_major = request('is_major');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'section';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'Student '.$data->id;
                $log->save();

                return $this->apiResponse(ResultType::Success, $data, 'Student Updated', 200);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'Student not updated', 500);
            }
        } else {
            return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = UsersStudent::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResultType::Success, $data, 'Student Deleted', 200);
        } else {
            return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

