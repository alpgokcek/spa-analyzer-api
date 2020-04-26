<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Assessment;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class AssessmentController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Assessment::query();

        if ($request->has('course'))
            $query->where('course_id', '=', $request->query('course'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Assessment Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
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
    }

    public function show($id)
    {
        $data = Assessment::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Assessment Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Assessment Not Found', 404);
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

