<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\ProgramOutcomesProvidesCourseOutcomes;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProgramOutcomesProvidesCourseOutcomesController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = ProgramOutcomesProvidesCourseOutcomes::query();

        if ($request->has('course'))
            $query->where('course_outcome_id', '=', $request->query('course'));
        if ($request->has('program'))
            $query->where('program_outcome_id', '=', $request->query('program'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_outcome_id' => 'required',
            'program_outcome_id' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new ProgramOutcomesProvidesCourseOutcomes();
        $data->course_outcome_id = request('course_outcome_id');
        $data->program_outcome_id = request('program_outcome_id');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'ProgramOutcomesProvidesCourseOutcomes';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'ProgramOutcomesProvidesCourseOutcomes '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes not saved', 500);
        }
    }

    public function show($id)
    {
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'course_outcome_id' => 'nullable',
            'program_outcome_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);

        if ($data) {
            if (request('course_outcome_id') != '') {
                $data->course_outcome_id = request('course_outcome_id');
            }
            if (request('program_outcome_id') != '') {
                $data->program_outcome_id = request('program_outcome_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'ProgramOutcomesProvidesCourseOutcomes';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'ProgramOutcomesProvidesCourseOutcomes '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcomesProvidesCourseOutcomes not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = ProgramOutcomesProvidesCourseOutcomes::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcomesProvidesCourseOutcomes Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

