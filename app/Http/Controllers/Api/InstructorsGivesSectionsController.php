<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\InstructorsGivesSections;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class InstructorsGivesSectionsController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = InstructorsGivesSections::query();

        if ($request->has('instructor'))
            $query->where('instructor_id', '=', $request->query('instructor'));
        if ($request->has('section'))
            $query->where('section_id', '=', $request->query('section'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'required',
            'section_id' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new InstructorsGivesSections();
        $data->instructor_id = request('instructor_id');
        $data->section_id = request('section_id');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'InstructorsGivesSections';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1;
            $log->info = 'InstructorsGivesSections '.$data->id.' Created for the University '.$data->university;
            $log->save();
            return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections not saved', 500);
        }
    }

    public function show($id)
    {
        $data = InstructorsGivesSections::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'nullable',
            'section_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = InstructorsGivesSections::find($id);

        if ($data) {
            if (request('instructor_id') != '') {
                $data->instructor_id = request('instructor_id');
            }
            if (request('section_id') != '') {
                $data->section_id = request('section_id');
            }
            $data->save();
            if ($data) {
                $log = new Log();
                $log->area = 'InstructorsGivesSections';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'InstructorsGivesSections '.$data->id;
                $log->save();
                return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'InstructorsGivesSections not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = InstructorsGivesSections::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'InstructorsGivesSections Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}
