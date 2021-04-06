<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function apiResponse($resultType, $data, $message = null, $count, $code = 200)
    {
        $response = [];
        $response['success'] = $resultType == ResultType::Success ? true : false;
        $response['message'] = $message;
        $response['count'] = $count;
        if ($resultType != ResultType::Error) {
            $response['data'] = $data;
        }
        if ($resultType == ResultType::Error) {
            $response['errors'] = $data;
        }

        return response()->json($response, $code);

    }
}

class ResultType {
    const Success = 1;
    const Information = 2;
    const Warning = 3;
    const Error = 4;
}
