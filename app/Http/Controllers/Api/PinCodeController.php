<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\PinCode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class PinCodeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = PinCode::query();

        if ($request->has('search'))
            $query->where('pin', '=', $request->query('search') );

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'PinCode Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required',
            'title' => 'required',
            'serino' => 'required',
            'code' => 'required',
            'price' => 'required',
            'ended_at' => 'required',
            'status' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new PinCode();
        $data->pin = request('pin');
        $data->title = request('title');
        $data->serino = request('serino');
        $data->code = request('code');
        $data->price = request('price');
        $data->ended_at = request('ended_at');
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'PinCode Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'PinCode not saved', 500);
        }
    }

    public function show($id)
    {
        $data = PinCode::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'PinCode Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'PinCode Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = PinCode::find($id);

        if ($data) {
            $data->status = request('status');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'PinCode Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'PinCode not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = PinCode::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'PinCode Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

