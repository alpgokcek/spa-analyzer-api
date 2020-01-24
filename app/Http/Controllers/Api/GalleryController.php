<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Gallery;
use App\User;
use App\Website;
use App\Content;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class GalleryController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $type = $request->type ? $request->type : null;
        $query = Gallery::query();
        $length = 1;
        $gallery = Gallery::all();
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));
        if ($request->has('start')) {
            $start = $request->query('start');
            $end = $request->query('end');
            $query->whereBetween('created_at',[$start,$end]);
        }
        if ($request->has('user'))
            $query->where('user', $request->query('user'));
        $data = $query->offset($offset)->limit($limit)->get();

        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Success, null, 'Content Not Found', 0, 202);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'website' => 'nullable',
            'title' => 'nullable',
            'order' => 'nullable',
            'photo' => 'required',
            'store' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('api_token'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
            $data = new Gallery();
            $data->website = request('website');
            $data->user = $user->id;
            $data->title = request('title');
            $data->order = request('order');
            $data->photo = request('photo');
            $data->store = request('store');
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $content->id, 'Gallery Added', 201);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Gallery not Added', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
        }
    }

    public function show($id)
    {
        $data = Gallery::where('website','=',$id)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Gallery Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'order' => 'nullable'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('user'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
            $data = Gallery::find($token);
            if (request('type')) {
                $data->type = request('type');
            }
            if (request('order')) {
                $data->order = request('order');
            }
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Gallery Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
        }
    }

    public function destroy($id)
    {
        $data = Gallery::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Gallery Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

