<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Business;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Validator;

class UserController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 10;
        $token = $request->token ? $request->token : null;
        $query = User::query();
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
            'name' => 'required',
            'company' => 'required|integer',
            'business' => 'required|integer',
            'email' => 'required|unique:users,email|email',
            'password' => 'required',
            'level' => 'required|integer',
            'userPhone' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new User();
        $data->name = request('name');
        $data->company = request('company');
        $data->business = request('business');
        $data->email = request('email');
        $data->password = Hash::make(request('password'));
        $data->level = request('level');
        $data->userPhone = request('userPhone');
        $data->api_token = Str::random(64);
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'User Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User Not Created', 500);
        }
    }

    public function show($email)
    {
        $data = User::where('email','=',$email)->first();
        if (count($data) >= 1) {
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Content Not Found'], 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'level' => 'nullable|string',
            'userPhone' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = User::find($token);
        $data->name = request('name');
        $data->email = request('email');
        $data->level = request('level');
        $data->userPhone = request('userPhone');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'User Updated', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User Not Updated', 500);
        }

    }

    public function destroy($token)
    {
        /*$data = User::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return response([
                'message'=> 'Content Deleted'
            ], 200);
        } else {
            return response()->json(['error' => 'Content Not Found'], 404);
        }*/


    }
}
