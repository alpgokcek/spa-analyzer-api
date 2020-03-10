<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentGetsMeasuredGradeProgramOutcome;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class StudentGetsMeasuredGradeProgramOutcomeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentGetsMeasuredGradeProgramOutcome::query();

        if ($request->has('student'))
            $query->where('student_id', '=', $request->query('student'));
        if ($request->has('programOutcome'))
            $query->where('program_outcome_id', '=', $request->query('programOutcome'));
        if ($request->has('grade'))
            $query->where('grade', '=', $request->query('grade'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeProgramOutcome Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_outcome_id' => 'required',
            'student_id' => 'required',
            'grade' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new StudentGetsMeasuredGradeProgramOutcome();
        $data->program_outcome_id = request('program_outcome_id');
        $data->student_id = request('student_id');
        $data->grade = request('grade');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'StudentGetsMeasuredGradeProgramOutcome';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'StudentGetsMeasuredGradeProgramOutcome '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeProgramOutcome Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeProgramOutcome not saved', 500);
        }
    }

    public function show($id)
    {
        $data = StudentGetsMeasuredGradeProgramOutcome::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeProgramOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeProgramOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'program_outcome_id' => 'nullable',
            'student_id' => 'nullable',
            'grade' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentGetsMeasuredGradeProgramOutcome::find($id);

        if ($data) {
            if (request('program_outcome_id') != '') {
                $data->program_outcome_id = request('program_outcome_id');
            }
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('grade') != '') {
                $data->grade = request('grade');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentGetsMeasuredGradeProgramOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentGetsMeasuredGradeProgramOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeProgramOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentGetsMeasuredGradeProgramOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = StudentGetsMeasuredGradeProgramOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentGetsMeasuredGradeProgramOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}
