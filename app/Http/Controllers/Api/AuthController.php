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
}
