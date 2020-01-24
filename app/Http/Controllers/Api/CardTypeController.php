<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\CardType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CardTypeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = CardType::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

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
            'title' => 'required',
            'status' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new CardType();
        $data->company = request('company');
        $data->title = request('title');
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CardType Successful', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $data = CardType::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CardType Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CardType Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Website::where('token','=',$token)->first();

        if (count($data) >= 1) {
            $data->title = request('title');
            $data->status = request('status');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'CardType Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'CardType not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = CardType::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'CardType Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

