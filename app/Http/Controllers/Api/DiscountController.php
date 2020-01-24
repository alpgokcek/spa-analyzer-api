<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Discount;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class DiscountController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Discount::query();

        if ($request->has('customer'))
            $query->where('customer', '=', $request->query('customer') );

        $query->join('pins','pins.id','=','discount.pin');
        $query->select('discount.*','pins.title as pinTitle','pins.price as pinPrice', 'pins.discount as pinDiscount');
        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();


        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Discount Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $discount = request('data');
        foreach ($discount as $key) {
            $data = new Discount();
            $data->customer = $key['customer'];
            $data->pin = $key['pin'];
            $data->discount = $key['discount'];
            $data->save();
        }
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Discount Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Discount not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Discount::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Discount Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Discount Not Found', 404);
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
        $data = Discount::find($id);

        if ($data) {
            $data->status = request('status');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Discount Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Discount not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Discount::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Discount Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

