<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class KeywordController extends ApiController
{
    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = Keyword::query();

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
            return $this->apiResponse(ResaultType::Error, null, 'Keyword Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required',
            'type' => 'nullable',
            'title' => 'required',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = new Keyword();
        $data->website = request('website');
        $data->type = request('type');
        $data->title = request('title');
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Keyword Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Keyword not saved', 500);
        }
    }

    public function show($id)
    {
        $data = Keyword::find($id);
        if (count($data) >= 1) {
            return $this->apiResponse(ResaultType::Success, $data, 'Keyword Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Keyword Not Found', 404);
        }
    }

    public function update(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = Keyword::where('token','=',$token)->first();

        if (count($data) >= 1) {
            if (request('title') != '') {
                $data->title = request('title');
            }
            $data->save();

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'Keyword Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'Keyword not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = Keyword::find($id);
        if (count($data) >= 1) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'Keyword Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

