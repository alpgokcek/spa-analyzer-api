<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class BankController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Bank::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects);
        }
        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();

        $data->each->setAppends(['bankCode']);

        if ($data) {
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
            'holder' => 'nullable',
            'number' => 'nullable',
            'code' => 'nullable',
            'comment' => 'nullable',
            'status' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Bank();
        $data->company = request('company');
        $data->title = request('title');
        $data->holder = request('holder');
        $data->number = request('number');
        $data->code = request('code');
        $data->comment = request('comment');
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Bank Successful', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Bank::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Bank Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Bank Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'holder' => 'nullable',
            'number' => 'nullable',
            'code' => 'nullable',
            'comment' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Bank::find($id);

        if ($data) {
            $data->title = request('title');
            $data->holder = request('holder');
            $data->number = request('number');
            $data->code = request('code');
            $data->comment = request('comment');
            $data->status = request('status');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Bank Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Bank not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Bank::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Bank Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

