<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Canvas;
use App\User;
use App\Website;
use App\Content;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class CanvasController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $type = $request->type ? $request->type : null;
        $query = Canvas::query();
        $length = 1;
        $canvas = Canvas::all();
        if ($request->has('type')){
            switch ($type) {
                case 'content':
                    $query->join('content', 'content.canvas', '=', 'canvas.id');
                    $query->select('canvas.website as canvasWebsite','canvas.type as canvasType','canvas.user as canvasUser','canvas.title as canvasTitle','canvas.summary as canvasSummary','canvas.photo as canvasPhoto','canvas.slug as canvasSlug','content.*');
                    break;
                case 'product':
                    $query->join('product', 'product.canvas', '=', 'canvas.id');
                    $query->select('canvas.website as canvasWebsite','canvas.type as canvasType','canvas.user as canvasUser','canvas.title as canvasTitle','canvas.summary as canvasSummary','canvas.photo as canvasPhoto','canvas.slug as canvasSlug','product.*');
                    break;

                default:
                    # code...
                    break;
            }
        } else {
            $length = count($query->get());
        }
        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));


        if ($request->has('start')) {
            $start = $request->query('start');
            $end = $request->query('end');
            $query->whereBetween('created_at',[$start,$end]);
        }

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
            'api_token' => 'required',
            'website' => 'required|integer',
            'type' => 'required',
            'user' => 'required|integer',
            'title' => 'required',
            'summary' => 'nullable',
            'photo' => 'nullable',
            'slug' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('api_token'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
            $data = new Canvas();
            $data->website = request('website');
            $data->type = request('type');
            $data->user = request('user');
            $data->title = request('title');
            $data->summary = request('summary');
            $data->photo = request('photo');
            $data->slug = Str::slug(request('slug'), '-');
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Canvas Added', 201);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Canvas not Added', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
        }
    }

    public function show($id)
    {
        $data = Canvas::find($id);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable',
            'title' => 'nullable',
            'summary' => 'nullable',
            'photo' => 'nullable'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('api_token','=',request('user'))->first();
        if ((count($user) >= 1) && ($user->level == 1)) {
            $data = Canvas::find($token);
            if (request('type')) {
                $data->type = request('type');
            }
            if (request('title')) {
                $data->title = request('title');
            }
            if (request('summary')) {
                $data->summary = request('summary');
            }
            if (request('photo')) {
                $data->photo = request('photo');
            }
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Canvas Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Content not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'User not found', 500);
        }
    }

    public function destroy($id)
    {
        $data = Canvas::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

