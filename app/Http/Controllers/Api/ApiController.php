<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function apiResponse($resaultType, $data, $message = null, $code = 200)
    {
        $response = [];
        $response['success'] = $resaultType == ResaultType::Success ? true : false;
        $response['message'] = $message;
        if ($resaultType != ResaultType::Error) {
            $response['data'] = $data;
        }
        if ($resaultType == ResaultType::Error) {
            $response['errors'] = $data;
        }

        return response()->json($response, $code);

    }
}

class ResaultType {
    const Success = 1;
    const Information = 2;
    const Warning = 3;
    const Error = 4;
}
