<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Content;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class ContentController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Content::query();

        if ($request->has('search'))
            $query->where('title', 'like', '%' . $request->query('search') . '%');

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects,'canvas.title as canvasTitle', 'canvas.url as canvasUrl');
        } else {
            $query->select('balance.*','customer.title as customerTitle', 'customer.code as customerCode', 'customer.token');
        }


        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        $query->join('canvas', 'canvas.id', '=', 'content.canvas');
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 0, 404);
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
            'hot' => 'nullable|boolean',
            'spot' => 'nullable|boolean',
            'secret' => 'nullable|boolean',
            'locked' => 'nullable|boolean',
            'slider' => 'nullable|boolean',
            'homepage' => 'nullable|boolean',
            'comments' => 'nullable|boolean',
            'feed' => 'nullable|boolean',
            'keywords' => 'nullable',
            'related' => 'nullable',
            'photo' => 'nullable',
            'old_title' => 'nullable',
            'old_summary' => 'nullable',
            'old_content' => 'nullable'

            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Content();
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
        $data->secret = request('secret');
        $data->locked = request('locked');
        $data->slider = request('slider');
        $data->homepage = request('homepage');
        $data->comments = request('comments');
        $data->feed = request('feed');
        $data->keywords = request('keywords');
        $data->related = request('related');
        $data->photo = request('photo');
        $data->old_title = request('old_title');
        $data->old_summary = request('old_summary');
        $data->old_content = request('old_content');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Content::find($id);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'nullable',
            'type' => 'nullable',
            'title' => 'nullable',
            'summary' => 'nullable',
            'content' => 'nullable',
            'status' => 'nullable',
            'line' => 'nullable',
            'hot' => 'nullable',
            'spot' => 'nullable',
            'secret' => 'nullable',
            'locked' => 'nullable',
            'slider' => 'nullable',
            'homepage' => 'nullable',
            'comments' => 'nullable',
            'feed' => 'nullable',
            'keywords' => 'nullable',
            'related' => 'nullable',
            'photo' => 'nullable',
            'old_title' => 'nullable',
            'old_summary' => 'nullable',
            'old_content' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Content::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('lang') != '') {
                $data->lang = request('lang');
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
            if (request('secret') != '') {
                $data->secret = request('secret');
            }
            if (request('locked') != '') {
                $data->locked = request('locked');
            }
            if (request('slider') != '') {
                $data->slider = request('slider');
            }
            if (request('homepage') != '') {
                $data->homepage = request('homepage');
            }
            if (request('comments') != '') {
                $data->comments = request('comments');
            }
            if (request('feed') != '') {
                $data->feed = request('feed');
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
            if (request('old_title') != '') {
                $data->old_title = request('old_title');
            }
            if (request('old_summary') != '') {
                $data->old_summary = request('old_summary');
            }
            if (request('old_content') != '') {
                $data->old_content = request('old_content');
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

    public function destroy($id)
    {
        $data = Content::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

