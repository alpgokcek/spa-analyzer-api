<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\ProgramOutcome;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProgramOutcomeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = ProgramOutcome::query();

        if ($request->has('code'))
            $query->where('code', '=', $request->query('code'));
        if ($request->has('department'))
            $query->where('department_id', '=', $request->query('department'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'explanation' => 'required',
            'code' => 'required',
            'department_id' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new ProgramOutcome();
        $data->explanation = request('explanation');
        $data->code = request('code');
        $data->department_id = request('department_id');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'ProgramOutcome';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'ProgramOutcome '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome not saved', 500);
        }
    }

    public function show($id)
    {
        $data = ProgramOutcome::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'explanation' => 'nullable',
            'code' => 'nullable',
            'department_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = ProgramOutcome::find($id);

        if ($data) {
            if (request('explanation') != '') {
                $data->explanation = request('explanation');
            }
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('department_id') != '') {
                $data->department_id = request('department_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'ProgramOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'ProgramOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = ProgramOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}
