<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class LogController extends ApiController
{
    public function index(Request $request)
    {
        $query = Log::query();
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 50;

        if ($request->has('area')) {
            $query->where('log.area', '=', $request->query('area'));
        }
        if ($request->has('areaid')) {
            $query->where('log.areaid', '=', $request->query('areaid'));
        }
        if ($request->has('user')) {
            $query->where('log.user', '=', $request->query('company'));
        }

        $query->join('users','users.id','=','log.user');
        $query->select('log.*','users.name as userName');
        $query->orderBy('id','DESC');

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Log Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'requred',
            'areaid' => 'nullable',
            'user' => 'requred',
            'type' => 'requred',
            'info' => 'requred',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Log();
        $data->area = request('area');
        $data->areaid = request('areaid');
        $data->user = request('user');
        $data->type = request('type');
        $data->info = request('info');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Logged', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Log error', 500);
        }
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($token)
    {
    }
}

