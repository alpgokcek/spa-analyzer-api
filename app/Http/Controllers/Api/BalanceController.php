<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Balance;
use App\User;
use App\Business;
use Illuminate\Http\Request;
use Validator;

class BalanceController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $token = $request->token ? $request->token : null;
        $query = Balance::query();
        if ($request->has('token')){
            $business = Business::where('token','=',$token)->first();
            $length = count($query->where('business', '=', $business->id)->get());
            $query->where('business', '=', $business->id);
        } else {
            $length = count($query->get());
        }
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));
            $query->join('business', 'business.id', '=', 'balance.business');
        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects,'business.title as businessTitle', 'business.code as businessCode', 'business.token');
        } else {
            $query->select('balance.*','business.title as businessTitle', 'business.code as businessCode', 'business.token');
        }

        if ($request->has('start')) {
            $start = $request->query('start');
            $end = $request->query('end');
            $query->whereBetween('created_at',[$start,$end]);
        }

        $data = $query->offset($offset)->limit($limit)->get();
        $data->each->setAppends(['bankName']);

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'business' => 'required|integer',
            'recharge' => 'nullable|string',
            'paid' => 'nullable|string',
            'type' => 'required|integer',
            'action' => 'nullable|string',
            'bank' => 'nullable|integer',
            'remark' => 'nullable|string',
            'comment' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('user'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
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
                $bsn = Business::find($data->business);
                $bsnbalance = $bsn->balance;
                if ($data->recharge) {
                    $bsn->balance = $bsnbalance + $data->recharge;
                }
                if ($data->paid) {
                    $bsn->balance = $bsnbalance - $data->paid;
                }
                $bsn->save();
                return $this->apiResponse(ResaultType::Success, $data, 'Balance Added', 201);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Balance not Added', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
        }
    }

    public function show($id)
    {
        $business = Business::where('token','=',$token)->first();
        if (count($business) >= 1) {
            $data = Balance::where('business','=',$business->id)->get();
            // join bank info
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'bank' => 'required|integer',
            'arrival_date' => 'nullable',
            'comment' => 'nullable',
            'status' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('user'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
            $data = Balance::find($token);
            $data->bank = request('bank');
            $data->arrival_date = request('arrival_date');
            $data->comment = request('comment');
            $data->status = request('status');
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Content Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
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

