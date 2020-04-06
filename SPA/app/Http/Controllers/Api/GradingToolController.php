<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\GradingTool;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class GradingToolController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = GradingTool::query();

        if ($request->has('assessment'))
            $query->where('assessment_id', '=', $request->query('assessment'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'GradingTool Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required',
            'question_number' => 'required',
            'percentage' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new GradingTool();
        $data->assessment_id = request('assessment_id');
        $data->question_number = request('question_number');
        $data->percentage = request('percentage');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'GradingTool';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'GradingTool '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'GradingTool not saved', 500);
        }
    }

    public function show($id)
    {
        $data = GradingTool::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'GradingTool Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_number' => 'nullable',
            'percentage' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = GradingTool::find($id);

        if ($data) {
            if (request('question_number') != '') {
                $data->question_number = request('question_number');
            }
            if (request('percentage') != '') {
                $data->percentage = request('percentage');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'GradingTool';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'GradingTool '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'GradingTool not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = GradingTool::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'GradingTool Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

