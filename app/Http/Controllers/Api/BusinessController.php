<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Business;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class BusinessController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Business::query();

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
        $data->each->setAppends(['fullAddress','balanceCredit','balanceTitle','salesStatus']);

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        /* burası çok karışık oldu!!!! */
        /* user emaili gönderilecek! email kayıtlıysa id tabloya eklenecek. */
        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'code' => 'nullable',
            'title' => 'required|string',
            'address' => 'nullable',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'country' => 'nullable',
            'mwst_number' => 'nullable',
            'telephone' => 'nullable',
            'fax' => 'nullable',
            'website' => 'nullable',
            'type' => 'nullable',
            'balance' => 'nullable',
            'credit' => 'nullable',
            'disclaimer' => 'nullable',
            'discount' => 'nullable',
            'status' => 'nullable',
            'token' => 'unique:business,token',
            'name' => 'required|string',
            'email' => 'required|email',
            'userphone' => 'nullable',
            'level' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Business();
        $data->company = request('company');
        $data->code = request('code');
        $data->title = request('title');
        $data->address = request('address');
        $data->city = request('city');
        $data->postal_code = request('postal_code');
        $data->country = request('country');
        $data->mwst_number = request('mwst_number');
        $data->telephone = request('telephone');
        $data->fax = request('fax');
        $data->website = request('website');
        $data->type = request('type');
        $data->balance = request('balance');
        $data->credit = request('credit');
        $data->disclaimer = request('disclaimer');
        $data->discount = request('discount');
        $data->status = 2;
        $data->token = str_random(64);
        $data->save();
        if ($data) {
            $controlUser = User::where('email','=',request('email'))->first();
            if (!$controlUser) {
                $user = User::create([
                    'name' => request('name'),
                    'email' => request('email'),
                    'password' => Hash::make(request('password')),
                    'api_token' => Str::random(64),
                ]);
                if ($user) {
                    $bsns = Business::where('id','=',$data->id)->first();
                    $bsns->user = $user->id;
                    $bsns->status = 1;
                    $bsns->save();
                    $usr = User::where('id','=',$user->id)->first();
                    $usr->company = $bsns->company;
                    $usr->business = $bsns->id;
                    $usr->userphone = request('userphone');
                    $usr->level = 2;
                    $usr->save();
                    return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
                } else {
                    return $this->apiResponse(ResaultType::Error, null, 'User Not Created', 500);
                }
            } else {
                $bsns = Business::where('id','=',$data->id)->first();
                $bsns->user = $controlUser->id;
                $bsns->status = 1;
                $bsns->save();
                $controlUser->company = $bsns->company;
                $controlUser->business = $bsns->id;
                $controlUser->userphone = request('userphone');
                $controlUser->level = 2;
                $controlUser->save();
                return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($token)
    {
        $data = Business::where('token','=',$token)->get();
        $data->each->setAppends(['balanceCredit','balanceTitle','salesStatus']);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'nullable',
            'title' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'postal_code' => 'nullable',
            'country' => 'nullable',
            'mwst_number' => 'nullable',
            'telephone' => 'nullable',
            'fax' => 'nullable',
            'website' => 'nullable',
            'type' => 'nullable',
            'balance' => 'nullable',
            'credit' => 'nullable',
            'disclaimer' => 'nullable',
            'discount' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Business::where('token','=',$token)->first();

        if (count($data) >= 1) {
            $data->user = request('user');
            $data->title = request('title');
            $data->address = request('address');
            $data->city = request('city');
            $data->postal_code = request('postal_code');
            $data->country = request('country');
            $data->mwst_number = request('mwst_number');
            $data->telephone = request('telephone');
            $data->fax = request('fax');
            $data->website = request('website');
            $data->type = request('type');
            $data->balance = request('balance');
            $data->credit = request('credit');
            $data->disclaimer = request('disclaimer');
            $data->discount = request('discount');
            $data->status = request('status');
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Content Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($token)
    {
        $data = Business::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

