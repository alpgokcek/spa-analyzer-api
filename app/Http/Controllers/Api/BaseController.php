<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Business;
use Illuminate\Http\Request;
use Validator;

class BusinessController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 10;
        $query = Business::query();
        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        $data = $query->offset($offset)->limit($limit)->get();
        $data->each->setAppends(['fullAddress']);

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required|integer',
            'user' => 'required|integer',
            'title' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'mwst_nummer' => 'nullable|string',
            'telephone' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'type' => 'nullable|integer',
            'balance' => 'nullable|string',
            'credit' => 'nullable|string',
            'disclaimer' => 'nullable|string',
            'discount' => 'nullable|string',
            'status' => 'nullable|integer',
            'token' => 'unique:business,token',

            'name' => 'required|string',
            'email' => 'required|email',
            'userphone' => 'nullable|string',
            'level' => 'nullable',
            ]);

        if ($validator->fails())
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);

        $data = new Business();
        $data->company = request('company');
        $data->user = request('user');
        $data->title = request('title');
        $data->token = str_random(64);
        $data->status = 2;
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($token)
    {
        $data = Business::where('token','=',$token)->first();
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $data = Business::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->company = request('company');
            $data->user = request('user');
            $data->title = request('title');
            $data->token = request('token');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Content Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($token)
    {
        $data = Business::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

