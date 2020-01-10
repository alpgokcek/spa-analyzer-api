<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\CanvasSettingJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CanvasSettingJsonController extends ApiController
{
    public function index(Request $request)
    {
        $query = CanvasSettingJson::query();
        if ($request->has('canvas'))
            $query->where('canvas', '=', $request->query('canvas'));
        $data = $query->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: Canvas Settings', '', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasSettingJson Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'canvas' => 'required',
            'settings' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new CanvasSettingJson();
        $data->canvas = request('canvas');
        $data->settings = request('settings');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Settings Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Canvas Settings not saved', 500);
        }
    }

    public function show($id)
    {
        $data = CanvasSettingJson::where('canvas','=',$id)->select('settings')->first();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Settings Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Canvas Settings Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = CanvasSettingJson::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('settings') != '') {
                $data->settings = request('settings');
            }
            $data->save();
            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Canvas Settings Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Canvas Settings not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = CanvasSettingJson::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Canvas Settings Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

