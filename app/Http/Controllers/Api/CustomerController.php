<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Customer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CustomerController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Customer::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects);
        }
        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        $data->each->setAppends(['fullAddress','balanceCredit','balanceTitle','salesStatus']);

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'code' => 'nullable',
            'title' => 'required',
            'address' => 'nullable',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'country' => 'nullable',
            'mwst_number' => 'nullable',
            'telephone' => 'nullable',
            'fax' => 'nullable',
            'website' => 'nullable',
            'type' => 'nullable',
            'balance' => 'nullable',
            'before' => 'nullable',
            'credit' => 'nullable',
            'disclaimer' => 'nullable',
            'discount' => 'nullable',
            'status' => 'required',
            'token' => 'unique:customer,token'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Customer();
        $data->company = request('company');
        $data->code = request('code');
        $data->title = request('title');
        $data->address = request('address');
        $data->city = request('city');
        $data->postal_code = request('postal_code');
        $data->country = request('country');
        $data->mwst_number = request('mwst_number');
        $data->telephone = request('telephone');
        $data->fax = request('fax');
        $data->website = request('website');
        $data->type = request('type');
        $data->balance = request('balance');
        $data->before = request('before');
        $data->credit = request('credit');
        $data->disclaimer = request('disclaimer');
        $data->discount = request('discount');
        $data->status = request('status');
        $data->token = str_random(64);
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Customer Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($token)
    {
        $data = Customer::where('token','=',$token)->get();
        $data->each->setAppends(['balanceCredit','balanceTitle','salesStatus']);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable',
            'title' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'country' => 'nullable',
            'mwst_number' => 'nullable',
            'telephone' => 'nullable',
            'fax' => 'nullable',
            'website' => 'nullable',
            'type' => 'nullable',
            'balance' => 'nullable',
            'before' => 'nullable',
            'credit' => 'nullable',
            'disclaimer' => 'nullable',
            'discount' => 'nullable',
            'status' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Customer::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('address') != '') {
                $data->address = request('address');
            }
            if (request('city') != '') {
                $data->city = request('city');
            }
            if (request('postal_code') != '') {
                $data->postal_code = request('postal_code');
            }
            if (request('country') != '') {
                $data->country = request('country');
            }
            if (request('mwst_number') != '') {
                $data->mwst_number = request('mwst_number');
            }
            if (request('telephone') != '') {
                $data->telephone = request('telephone');
            }
            if (request('fax') != '') {
                $data->fax = request('fax');
            }
            if (request('website') != '') {
                $data->website = request('website');
            }
            if (request('type') != '') {
                $data->type = request('type');
            }
            if (request('balance') != '') {
                $data->balance = request('balance');
            }
            if (request('before') != '') {
                $data->before = request('before');
            }
            if (request('credit') != '') {
                $data->credit = request('credit');
            }
            if (request('disclaimer') != '') {
                $data->disclaimer = request('disclaimer');
            }
            if (request('discount') != '') {
                $data->discount = request('discount');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
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
        $data = Customer::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

