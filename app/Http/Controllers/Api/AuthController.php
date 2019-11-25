<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use Str;
use App\User;
class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->input('password'), $user->password)) {
                $newToken = Str::random(64);
                $user->update(['api_token' => $newToken]);
                return response()->json([
                    'name' => $user->name,
                    'access_token' => $newToken,
                    'time' => time()
                ]);
            }
            return response()->json([
                'message' => 'Invalid Password'
            ]);
        }
        return response()->json([
            'message' => 'User Not Found'
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'password' => 'required',
            'userPhone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation Error',
            ], 422);
        }
        $data = new User();
        $data->name = request('name');
        $data->company = 1;
        $data->business = 1;
        $data->email = request('email');
        $data->password = Hash::make(request('password'));
        $data->level = 9;
        $data->userPhone = request('userPhone');
        $data->api_token = Str::random(64);
        $data->save();
        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'User Created',
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'errors' => null,
                'message' => 'User Not Created',
            ], 500);
        }
    }
}
