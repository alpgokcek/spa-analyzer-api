<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Balance;
use App\User;
use App\Customer;
use App\Log;
use Illuminate\Support\Facades\Auth;
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
            $customer = Customer::where('token','=',$token)->first();
            $length = count($query->where('customer', '=', $customer->id)->get());
            $query->where('customer', '=', $customer->id);
        } else {
            $length = count($query->get());
        }
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('id', 'DESC'));

        $query->join('customer', 'customer.id', '=', 'balance.customer');
        $query->join('bank', 'bank.id', '=', 'balance.bank');
        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects,'customer.title as customerTitle', 'customer.code as customerCode', 'customer.token', 'bank.title as bankName', 'customer.before as beforeCredit');
        } else {
            $query->select('balance.*','customer.title as customerTitle', 'customer.code as customerCode', 'customer.token', 'bank.title as bankName', 'customer.before as beforeCredit');
        }

        if ($request->has('start')) {
            $start = $request->query('start');
            $end = $request->query('end');
            $query->whereBetween('created_at',[$start,$end]);
        }

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
            'user' => 'required',
            'customer' => 'required|integer',
            'recharge' => 'nullable|string',
            'type' => 'required|integer',
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
        if ($user && ($user->level == 1)) {
            $data = new Balance();
            $data->customer = request('customer');
            $data->recharge = request('recharge');
            $data->type = request('type');

            $data->remark = request('remark');
            $data->arrival_date = request('arrival_date');
            $data->status = request('status');
            $data->bank = request('bank');
            $data->save();
            if ($data) {
                $bsn = Customer::find($data->customer);
                if ($data->type == 5) {
                    $bsn->before = $bsn->balance;
                    $bsn->credit = $bsn->credit + $data->recharge;
                } else {
                    if ($data->recharge) {
                        $bsn->before = $bsn->balance;
                        $bsn->balance = $bsn->balance + $data->recharge;
                    }
                    if ($data->paid) {
                        // paid değeri bu güne kadar HARCANMIŞ parayı gösterir.
                    }
                }
                $bsn->save();
                $log = new Log();
                $log->area = 'balance';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 1;
                $log->info = 'Balance '.$data->id.' Created';
                $log->save();
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
        $customer = Customer::where('token','=',$token)->first();
        if (count($customer) >= 1) {
            $data = Balance::where('customer','=',$customer->id)->get();
            // join bank info
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'bank' => 'nullable',
            'paid_date' => 'nullable',
            'comment' => 'nullable',
            'status' => 'nullable'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Balance::find($token);
        $data->status = request('status');
        $data->comment = request('comment');
        $data->bank = request('bank');
        $data->paid_date = request('paid_date');

        // if(request('status') == 1)
        //     $data->paid = $data->recharge;
        // else
        //     $data->paid = 0;

        $data->save();
        if ($data) {
            $log = new Log();
            $log->area = 'balance';
            $log->areaid = $id;
            $log->user = Auth::id();
            $log->ip = \Request::ip();
            $log->type = 2;
            $log->info = 'Balance '.$id.' Updated';
            $log->save();

            return $this->apiResponse(ResaultType::Success, $data, 'Content Updated', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
        }
    }

    public function destroy($id)
    {
        $data = Balance::find($id);
        if ($data) {
            $customer = Customer::find($data->customer);
            if ($data->type === 5)
                $customer->credit = $customer->credit - $data->recharge;
            else
                $customer->balance = $customer->balance - $data->recharge;
            $customer->save();
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

