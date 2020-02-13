<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\University;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class UniversityController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = University::query();

        if ($request->has('search'))
            $query->where('name', 'like', '%' . $request->query('search') . '%');

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects);
        }
        $length = count($query->get());
        $query->whereNotIn('status', [9]);

        $data = $query->offset($offset)->limit($limit)->get();

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account' => 'required',
            'name' => 'required',
            'address' => 'nullable',
            'tel' => 'nullable',
            'logo' => 'nullable',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new University();
        $data->bank_account = request('bank_account');
        $data->name = request('name');
        $data->address = request('address');
        $data->tel = request('tel');
        $data->logo = request('logo');
        $data->status = request('status');
        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'university';
            $log->areaid = $data->id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 1; // create
            $log->info = 'University Created';
            $log->save();

            return $this->apiResponse(ResaultType::Success, $data, 'University Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $data = University::where('id','=',$id)->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bank_account' => 'nullable',
            'name' => 'nullable',
            'address' => 'nullable',
            'tel' => 'nullable',
            'logo' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = University::where('id','=',$id)->first();

        if ($data) {
            if (request('bank_account') != '') {
                $data->bank_account = request('bank_account');
            }
            if (request('name') != '') {
                $data->name = request('name');
            }
            if (request('address') != '') {
                $data->address = request('address');
            }
            if (request('tel') != '') {
                $data->tel = request('tel');
            }
            if (request('logo') != '') {
                $data->logo = request('logo');
            }
                $data->status = request('status');
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'university';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2; // update
                $log->info = 'University Updated';
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'University Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'University not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = University::find($id);
        if ($data) {
            $data->status = 9;
            $data->save();
            // $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'University Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

