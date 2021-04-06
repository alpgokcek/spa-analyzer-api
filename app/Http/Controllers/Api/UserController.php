<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Project;

use App\User;
use App\Imports\UserImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class UserController extends ApiController
{

    public function uploadedFile(Request $request)
    {

        $import = new UserImport();
        $import->import($request->fileUrl);

        return $this->apiResponse(ResultType::Error, $import->err, 'hatalar', 403);
    }

    public function index(Request $request)
    {
				$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = User::query();

        if ($request->has('university'))
            $query->where('university', '=', $request->query('university'));

        switch ($user->level) {
            case 6:
                return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
                break;

            default:
								// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
								$query->join('university','university.id' ,'=','users.university');
								$query->join('faculty','faculty.id','=','users.faculty_id');
								$query->join('department', 'department.id', '=', 'users.department_id');
								if ($request->has('department')){
									$query->where( 'users.department_id', '=',$request->query('department'));
								}
                if ($request->has('level')){
                    $query->where( 'users.level', '=', $request->query('level'));
                }
                $query->select(
									'users.id as id',
									'users.name as name',
									'users.email as email',
									'users.phone as phone',
									'users.university as university_id',
									'users.faculty_id as faculty_id',
									'department.name as departmentName',
									'users.department_id as department_id',
									'users.student_id as studentID',
									'users.level as level',
									'university.name as universityName',
									'faculty.title as facultyTitle',
									'users.api_token as api_token',
								);
                break;
            }

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if (count($data) >= 1) {
            return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'password' => 'required',
            'level' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $control = User::where('email','=',request('email'))->first();
        if ($control) {
            return $this->apiResponse(ResultType::Error, $control->email, 'User Already Registered', 500);
        } else {
            $data = new User();
            $data->name = request('name');
            $data->university = request('university');
						$data->faculty_id = request('faculty_id');
						$data->department_id = request('department_id');
						$data->student_id = request('student_id');
            $data->email = request('email');
            $data->password = Hash::make(request('password'));
            $data->level = request('level');
            $data->phone = request('phone');
            $data->api_token = Str::random(64);
            $data->save();
            if ($data) {
                return $this->apiResponse(ResultType::Success, $data, 'User Created', 201);
            } else {
                return $this->apiResponse(ResultType::Error, null, 'User Not Created', 500);
            }
        }
    }

    public function show($token)
    {
        $data = User::where('api_token','=',$token)->first();
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Content Not Found'], 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'email' => 'nullable',
            'level' => 'nullable',
            'phone' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = User::where('api_token','=',$token)->first();

				$data->name = request('name');
        $data->university = request('university');
				$data->faculty_id = request('faculty_id');
				$data->department_id = request('department_id');
				$data->student_id = request('student_id');
        $data->email = request('email');
        $data->level = request('level');
        $data->phone = request('phone');

        $data->save();
        if ($data) {
            return $this->apiResponse(ResultType::Success, $data, 'User Updated', 200);
        } else {
            return $this->apiResponse(ResultType::Error, null, 'User Not Updated', 500);
        }
    }

    public function destroy($token)
    {
        $data = User::where('api_token','=',$token)->first();
        if ($data) {
            $data->status = 0;
            $data->save;
            if ($data)
                return response(['message'=> 'User Passived'], 200);
            else
                return response(['message'=> 'Error'], 500);
        }

    }
}
