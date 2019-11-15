<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Balance;
use App\Business;
use Illuminate\Http\Request;
use Validator;

class BalanceController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 10;
        $token = $request->token ? $request->token : null;
        $query = Balance::query();
        if ($request->has('token')){
            $business = Business::where('token','=',$token)->first();
            $query->where('business', '=', $business->id);
        }
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        $data = $query->offset($offset)->limit($limit)->get();

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business' => 'required|integer',
            'recharge' => 'required|string',
            'paid' => 'nullable|string',
            'type' => 'required|integer',
            'action' => 'nullable|string',
            'bank' => 'nullable|integer',
            'remark' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Balance();
        $data->business = request('business');
        $data->recharge = request('recharge');
        $data->paid = request('paid');
        $data->type = request('type');
        $data->action = request('action');
        $data->bank = request('bank');
        $data->remark = request('remark');
        $data->arrival_date = request('arrival_date');
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($token)
    {
        $business = Business::where('token','=',$token)->first();
        if (count($business) >= 1) {
            $data = Balance::where('business','=',$business->id)->get();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'business' => 'nullable|integer',
            'recharge' => 'nullable|string',
            'paid' => 'nullable|string',
            'type' => 'nullable|integer',
            'action' => 'nullable|string',
            'bank' => 'nullable|integer',
            'remark' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'status' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Balance::find($token);
        $data->business = request('business');
        $data->recharge = request('recharge');
        $data->paid = request('paid');
        $data->type = request('type');
        $data->action = request('action');
        $data->bank = request('bank');
        $data->remark = request('remark');
        $data->arrival_date = request('arrival_date');
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Updated', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
        }
    }

    public function destroy($token)
    {
        $data = Balance::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

