<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Website;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class WebsiteController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Website::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects);
        }

        if ($request->has('company'))
            $query->where('company', $request->query('company'));

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
        /* burası çok karışık oldu!!!! */
        /* user emaili gönderilecek! email kayıtlıysa id tabloya eklenecek. */
        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'url' => 'unique:website,url',
            'title' => 'required|string',
            'description' => 'nullable',
            'keywords' => 'nullable',
            'lang' => 'nullable',
            'logo' => 'nullable',
            'favicon' => 'nullable',
            'socialicon' => 'nullable',
            'status' => 'nullable',
            'token' => 'unique:website,token'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Website();
        $data->company = request('company');
        $data->url = request('url');
        $data->title = request('title');
        $data->description = request('description');
        $data->keywords = request('keywords');
        $data->lang = request('lang');
        $data->logo = request('logo');
        $data->favicon = request('favicon');
        $data->socialicon = request('socialicon');
        $data->status = request('status');
        $data->token = str_random(64);
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Website Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($token)
    {
        $data = Website::where('token','=',$token)->get();
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'nullable',
            'url' => 'nullable',
            'title' => 'nullable',
            'description' => 'nullable',
            'keywords' => 'nullable',
            'lang' => 'nullable',
            'logo' => 'nullable',
            'favicon' => 'nullable',
            'socialicon' => 'nullable',
            'status' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Website::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('company') != '') {
                $data->company = request('company');
            }
            if (request('url') != '') {
                $data->url = request('url');
            }
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('description') != '') {
                $data->description = request('description');
            }
            if (request('keywords') != '') {
                $data->keywords = request('keywords');
            }
            if (request('lang') != '') {
                $data->lang = request('lang');
            }
            if (request('logo') != '') {
                $data->logo = request('logo');
            }
            if (request('favicon') != '') {
                $data->favicon = request('favicon');
            }
            if (request('socialicon') != '') {
                $data->socialicon = request('socialicon');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
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
        $data = Website::where('token','=',$token)->first();
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

