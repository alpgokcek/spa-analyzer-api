<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Website;
use App\Canvas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CanvasController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Canvas::query();

        if ($request->has('type'))
            $query->where('type', '=', $request->query('type'));

        if ($request->has('sortBy'))
            $query->orderBy($request->query('sortBy'), $request->query('sort', 'DESC'));

        $query->join('website','website.id','=','canvas.website');
        if ($request->has('select')) {
            $selects = explode(',', $request->query('select'));
            $query->select($selects,'website.token as wsToken','website.title as wsTitle');
        } else {
            $query->select('canvas.*','website.token as wsToken','website.title as wsTitle');
        }

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

            'website' => 'required',
            'type' => 'required',
            'title' => 'required',
            'content' => 'nullable',
            'photo' => 'nullable',
            'slug' => 'required',
            'keyword' => 'required',
            'status' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $keylist = implode( ",", request('keyword') );

        $data = new Canvas();
        $data->website = request('website');
        $data->type = request('type');
        $data->title = request('title');
        $data->content = request('content');
        $data->photo = request('photo');
        $data->slug = request('slug');
        $data->keyword = $keylist;
        $data->status = request('status');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Successful', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
    }

    public function show($id)
    {
        $str = explode('&', $id);
        $token = $str[0];
        $site = Website::where('token','=',$token)->first();
        $data = Canvas::where('slug',$str[1])->where('website',$site->id)->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Canvas Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'content' => 'nullable',
            'photo' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Canvas::find($id);

        if ($data) {
            $data->title = request('title');
            $data->content = request('content');
            $data->photo = request('photo');
            $data->status = request('status');
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Canvas Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Canvas not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Canvas::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

