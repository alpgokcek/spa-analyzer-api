<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Authority;
use App\Customer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class AuthorityController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Authority::query();

        if ($request->has('token')) {
            $customer = Customer::where('token','=', $request->query('token'))->first();
            $query->where('customer', '=', $customer->id)
            ->join('users','users.id','authority.user')
            ->select('authority.*','users.name as userName')
            ->get();
        }

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        $data->each->setAppends(['authorityStatus']);

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'user' => 'required',
            'work' => 'required',
            'c' => 'required',
            'r' => 'required',
            'u' => 'required',
            'd' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Authority();
        $data->customer = request('customer');
        $data->user = request('user');
        $data->work = request('work');
        $data->c = request('c');
        $data->r = request('r');
        $data->u = request('u');
        $data->d = request('d');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Authorization Successful', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Authority::find($id);
        $data->each->setAppends(['authorityStatus']);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Authority Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Authority Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'c' => 'nullable',
            'r' => 'nullable',
            'u' => 'nullable',
            'd' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Customer::where('token','=',$token)->first();

        if ($data) {
            $data->c = request('c');
            $data->r = request('r');
            $data->u = request('u');
            $data->d = request('d');
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

    public function destroy($id)
    {
        $data = Authority::where('id','=',$id)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Authority Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

