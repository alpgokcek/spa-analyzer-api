<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\CanvasSetting;
use App\CanvasSettingLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CanvasSettingController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = CanvasSetting::query();

        if ($request->has('website'))
            $query->where('website', '=', $request->query('website'));
        if ($request->has('type'))
            $query->where('type', '=', $request->query('type'));
        $data = $query->get();
        foreach($data as $label)
          {
            $label->labels = CanvasSettingLabel::where('setting','=',$label->id)
            ->select('text','value')
            ->get()
            ->toArray();
          }
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: Canvas Settings', '', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasSetting Not Found', 0, 404);
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
        $data = new CanvasSetting();
        $data->old_content = request('old_content');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasSetting Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasSetting not saved', 500);
        }
    }

    public function show($id)
    {
        $data = CanvasSetting::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasSetting Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'CanvasSetting Not Found', 404);
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
        $data = CanvasSetting::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('old_content') != '') {
                $data->old_content = request('old_content');
            }
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'CanvasSetting Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'CanvasSetting not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = CanvasSetting::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'CanvasSetting Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

