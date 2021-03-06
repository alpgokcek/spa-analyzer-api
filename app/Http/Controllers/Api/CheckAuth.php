<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;

class CheckAuth extends ApiController
{
    public function index(Request $request)
    {
        $user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
        if ($user->level == 1 || $user->level == 5) {
						return $this->apiResponse(ResultType::Success, 204, NULL, 0, 204);
        } else{
						return $this->apiResponse(ResultType::Error, 403, 'Authorization Error', 0, 403);
        }
    }
}
