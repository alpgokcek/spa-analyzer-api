<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Pins;
use App\PinCode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class PinsController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Pins::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('website'))
            $query->where('website', $request->query('website'));

        if ($request->has('slug'))
            $query->where('slug', $request->query('slug'));
        $query->join('operator','operator.id','pins.operator');
        $query->join('card_type','card_type.id','pins.type');
        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects, 'operator.title as operatorTitle', 'card_type.title as cardType');
        } else {
            $query->select('pins.*', 'operator.title as operatorTitle', 'card_type.title as cardType');
        }
        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        $data->each->setAppends(['pinsList']);

        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Pins Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required',
            'operator' => 'required',
            'lang' => 'required',
            'user' => 'required',
            'type' => 'nullable',
            'title' => 'required',
            'content' => 'nullable',
            'status' => 'nullable',
            'price' => 'nullable',
            'discount' => 'nullable',
            'vat' => 'nullable',
            'FreeAccessNumber' => 'nullable',
            'MobileAccessNumber' => 'nullable',
            'LocalAccessNumber' => 'nullable',
            'Telefonzelle' => 'nullable',
            'CustomerService' => 'nullable',
            'photo' => 'nullable',
            'slug' => 'required',
            'pincodes' => 'nullable'
            ]);

        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('user'))->first();
        $data = new Pins();
        $data->website = request('website');
        $data->operator = request('operator');
        $data->lang = request('lang');
        $data->user = 1;
        $data->type = request('type');
        $data->title = request('title');
        $data->content = request('content');
        $data->status = request('status');
        $data->price = request('price');
        $data->discount = request('discount');
        $data->vat = request('vat');
        $data->FreeAccessNumber = request('FreeAccessNumber');
        $data->MobileAccessNumber = request('MobileAccessNumber');
        $data->LocalAccessNumber = request('LocalAccessNumber');
        $data->Telefonzelle = request('Telefonzelle');
        $data->CustomerService = request('CustomerService');
        $data->photo = request('photo');
        $data->slug = request('slug');
        $data->save();
        if ($data) {
            $submitPins = request('pincodes');
            foreach ($submitPins as $key) {
                $pincode = new PinCode();
                $pincode->pin = $data->id;
                $pincode->serino = $key['serialno'];
                $pincode->code = $key['pincode'];
                // $pin->ended_at = $key['date'].' 00:00:00.000000';
                $pincode->status = 1;
                $pincode->save();
            }
            return $this->apiResponse(ResaultType::Success, $data, 'Pin and codes Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Pin Code not saved', 500);
        }

    }

    public function show($id)
    {
        $data = Pins::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Pins Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Pins Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'nullable',
            'type' => 'nullable',
            'title' => 'nullable',
            'content' => 'nullable',
            'status' => 'nullable',
            'discount' => 'nullable',
            'vat' => 'nullable',
            'FreeAccessNumber' => 'nullable',
            'MobileAccessNumber' => 'nullable',
            'LocalAccessNumber' => 'nullable',
            'Telefonzelle' => 'nullable',
            'CustomerService' => 'nullable',
            'photo' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Pins::find($id);

        if ($data) {
            if (request('lang') != '') {
                $data->lang = request('lang');
            }
            if (request('type') != '') {
                $data->type = request('type');
            }
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('content') != '') {
                $data->content = request('content');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            if (request('discount') != '') {
                $data->discount = request('discount');
            }
            if (request('vat') != '') {
                $data->vat = request('vat');
            }
            if (request('FreeAccessNumber') != '') {
                $data->FreeAccessNumber = request('FreeAccessNumber');
            }
            if (request('MobileAccessNumber') != '') {
                $data->MobileAccessNumber = request('MobileAccessNumber');
            }
            if (request('LocalAccessNumber') != '') {
                $data->LocalAccessNumber = request('LocalAccessNumber');
            }
            if (request('Telefonzelle') != '') {
                $data->Telefonzelle = request('Telefonzelle');
            }
            if (request('CustomerService') != '') {
                $data->CustomerService = request('CustomerService');
            }
            if (request('photo') != '') {
                $data->photo = request('photo');
            }
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Pins Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Pins not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Pins::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Pins Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

