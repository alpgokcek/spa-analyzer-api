<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Product::query();

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

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Product Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'canvas' => 'required',
            'lang' => 'required',
            'user' => 'required',
            'type' => 'nullable',
            'title' => 'required',
            'summary' => 'nullable',
            'content' => 'nullable',
            'status' => 'required',
            'line' => 'nullable',
            'hot' => 'nullable',
            'spot' => 'nullable',
            'slider' => 'nullable',
            'homepage' => 'nullable',
            'keywords' => 'nullable',
            'related' => 'nullable',
            'photo' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Product();
        $data->canvas = request('canvas');
        $data->lang = request('lang');
        $data->user = request('user');
        $data->type = request('type');
        $data->title = request('title');
        $data->summary = request('summary');
        $data->content = request('content');
        $data->status = request('status');
        $data->line = request('line');
        $data->hot = request('hot');
        $data->spot = request('spot');
        $data->slider = request('slider');
        $data->homepage = request('homepage');
        $data->keywords = request('keywords');
        $data->related = request('related');
        $data->photo = request('photo');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Product Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Product not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Product::find($id);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Product Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Product Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'nullable',
            'user' => 'nullable',
            'type' => 'nullable',
            'title' => 'nullable',
            'summary' => 'nullable',
            'content' => 'nullable',
            'status' => 'nullable',
            'line' => 'nullable',
            'hot' => 'nullable',
            'spot' => 'nullable',
            'slider' => 'nullable',
            'homepage' => 'nullable',
            'keywords' => 'nullable',
            'related' => 'nullable',
            'photo' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Product::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('lang') != '') {
                $data->lang = request('lang');
            }
            if (request('user') != '') {
                $data->user = request('user');
            }
            if (request('type') != '') {
                $data->type = request('type');
            }
            if (request('title') != '') {
                $data->title = request('title');
            }
            if (request('summary') != '') {
                $data->summary = request('summary');
            }
            if (request('content') != '') {
                $data->content = request('content');
            }
            if (request('status') != '') {
                $data->status = request('status');
            }
            if (request('line') != '') {
                $data->line = request('line');
            }
            if (request('hot') != '') {
                $data->hot = request('hot');
            }
            if (request('spot') != '') {
                $data->spot = request('spot');
            }
            if (request('slider') != '') {
                $data->slider = request('slider');
            }
            if (request('homepage') != '') {
                $data->homepage = request('homepage');
            }
            if (request('keywords') != '') {
                $data->keywords = request('keywords');
            }
            if (request('related') != '') {
                $data->related = request('related');
            }
            if (request('photo') != '') {
                $data->photo = request('photo');
            }
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Product Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Product not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Product::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Product Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

