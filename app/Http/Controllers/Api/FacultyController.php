<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Faculty;
use App\Log;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class FacultyController extends ApiController
{
	public function index(Request $request)
	{
	$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
	$offset = $request->offset ? $request->offset : 0;
	$limit = $request->limit ? $request->limit : 99999999999999;
	$query = Faculty::query();

	if ($request->has('status')) {
    $query->where('faculty.status', '=', $request->status);
	}
	if ($request->has('university')) {
    $query->where('faculty.university', '=', $request->university);
	}

	switch ($user->level) {
		case 3:
			return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
			break;

		case 4:
			return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
			break;

		case 5:
			return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
			break;

		case 6:
			return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
			break;
		default:
			// 1 ve 2. leveller kontrol edilmeyeceği için diğer sorguları default içine ekliyoruz
			$query->select('faculty.*');
			break;
	}


	$query->join('university','university.id','faculty.university');
	$query->select('faculty.*', 'university.name as universityName');
	$length = count($query->get());
	$data = $query->offset($offset)->limit($limit)->get();
	if ($length >= 1) {
		return $this->apiResponse(ResultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
	} else {
			return $this->apiResponse(ResultType::Error, null, 'Faculty Not Found', 0, 404);
	}
}

	public function store(Request $request)
	{
		$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
		switch ($user->level) {
			case 1:
				$validator = Validator::make($request->all(), [
					'university' => 'required',
					'title' => 'required',
					'status' => 'required'
				]);
				if ($validator->fails()) {
					return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
				}
				$data = new Faculty();
				$data->university = request('university');
				$data->title = request('title');
				$data->status = request('status');
				$data->save();
				if ($data) {
					$log = new Log();
					$log->area = 'faculty';
					$log->areaid = $data->id;
					$log->user = Auth::id();
					$log->ip = \Request::ip();
					$log->type = 1;
					$log->info = 'Faculty '.$data->id.' Created for the University '.$data->university;
					$log->save();
					return $this->apiResponse(ResultType::Success, $data, 'Faculty Created', 201);
				}
				else {
					return $this->apiResponse(ResultType::Error, null, 'Faculty not saved', 500);
				}
				default:
					return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
				break;
		}
	}

	public function show($id)
	{
		$data = Faculty::where('id', '=', $id)->first();
		if ($data) {
			return $this->apiResponse(ResultType::Success, $data, 'Faculty Detail', 201);
		} else {
			return $this->apiResponse(ResultType::Error, null, 'Faculty Not Found', 404);
		}
	}

	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'nullable',
			'status' => 'nullable'
		]);
		if ($validator->fails()) {
			return $this->apiResponse(ResultType::Error, $validator->errors(), 'Validation Error', 422);
		}
		$data = Faculty::find($id);

		if ($data) {
			if (request('title') != '') {
				$data->title = request('title');
			}
			if (request('status') != '') {
				$data->status = request('status');
			}
			$data->save();

			if ($data) {
				$log = new Log();
				$log->area = 'faculty';
				$log->areaid = $data->id;
				$log->user = Auth::id();
				$log->ip = \Request::ip();
				$log->type = 2;
				$log->info = 'Faculty '.$data->id;
				$log->save();

				return $this->apiResponse(ResultType::Success, $data, 'Faculty Updated', 200);
			} else {
				return $this->apiResponse(ResultType::Error, null, 'Faculty not updated', 500);
			}
		} else {
			return $this->apiResponse(ResultType::Warning, null, 'Data not found', 404);
		}
	}

	public function destroy($id)
	{
		$data = Faculty::find($id);
		if ($data) {
			$data->delete();
			return $this->apiResponse(ResultType::Success, $data, 'Faculty Deleted', 200);
		} else {
			return $this->apiResponse(ResultType::Error, $data, 'Deleted Error', 500);
		}
	}
}

