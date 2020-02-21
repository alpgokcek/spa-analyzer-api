<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentAnswersGradingTool;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class StudentAnswersGradingToolController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentAnswersGradingTool::query();

        if ($request->has('student'))
            $query->where('student_id', '=', $request->query('student'));
        if ($request->has('gradingTool'))
            $query->where('grading_tool_id', '=', $request->query('gradingTool'));
        if ($request->has('grade'))
            $query->where('grade', '=', $request->query('grade'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentAnswersGradingTool Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'grading_tool_id' => 'required',
            'grade' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new StudentAnswersGradingTool();
        $data->student_id = request('student_id');
        $data->grading_tool_id = request('grading_tool_id');
        $data->grade = request('grade');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'StudentAnswersGradingTool';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'StudentAnswersGradingTool '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentAnswersGradingTool Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentAnswersGradingTool not saved', 500);
        }
    }

    public function show($id)
    {
        $data = StudentAnswersGradingTool::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentAnswersGradingTool Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentAnswersGradingTool Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable',
            'grading_tool_id' => 'nullable',
            'grade' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentAnswersGradingTool::find($id);

        if ($data) {
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('grading_tool_id') != '') {
                $data->grading_tool_id = request('grading_tool_id');
            }
            if (request('grade') != '') {
                $data->grade = request('grade');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentAnswersGradingTool';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentAnswersGradingTool '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'StudentAnswersGradingTool Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentAnswersGradingTool not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = StudentAnswersGradingTool::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentAnswersGradingTool Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

