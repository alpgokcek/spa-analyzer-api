<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\CanvasType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CanvasTypeController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = CanvasType::query();

        if ($request->has('website'))
            $query->where('website', '=', $request->query('website'));
        if ($request->has('type'))
            $query->where('type', '=', $request->query('type'));
        $query->select('id','type','title');
        $data = $query->get();
        $query->join('canvas', 'canvas.id', '=', 'content.canvas');
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: All Type', '', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasType Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [


            'old_content' => 'nullable'

            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new CanvasType();
        $data->old_content = request('old_content');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasType Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasType not saved', 500);
        }
    }

    public function show($id)
    {
        $data = CanvasType::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasType Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasType Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'old_content' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = CanvasType::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('old_content') != '') {
                $data->old_content = request('old_content');
            }
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'CanvasType Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'CanvasType not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = CanvasType::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasType Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

