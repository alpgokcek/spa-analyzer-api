<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\StudentsTakesSections;
use App\Imports\StudentsTakesSectionsImport;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Validator;


class StudentsTakesSectionsController extends ApiController
{
    

    public function uploadedFile(Request $request)
    {
        /*
        $address = url('/storage/Book2.xlsx');
        
        Excel::load($address, function($reader) {
            $results = $reader->get();
            dd($results);
        });


        foreach ($split as $key) {
            $user = UserStudent::where('id','=', request('student_id'))->first();
            if ($user){
                $section = Section::where('id','=', request('section_id'))->first();
                if ($section) {
                    $data = new StudentsTakesSections();
                    $data->student_id = request('student_id');
                    $data->section_id = request('section_id');
                    $data->letter_grade = request('letter_grade');
                    $data->average = request('average');
                    $data->save();
                    if ($data) {
                        $log = new Log();
                        $log->area = 'StudentsTakesSections';
                        $log->areaid = $data->id;
                        $log->user = Auth::id();
                        $log->ip = \Request::ip();
                        $log->type = 1;
                        $log->info = 'StudentsTakesSections '.$data->id.' Created for the University '.$data->university;
                        $log->save();
                        return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Created', 201);
                    } else {
                        return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not saved', 500);
                    }
                } else {
                    return $this->apiResponse(ResaultType::Error, null, 'Section not found', 404);
                }
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'User not found', 404);
            }

        */
        $data = Excel::import(new StudentsTakesSectionsImport, $request->fileUrl);
        
        if ($data) {
            return response()->json('success', 201);
            
        } 
        else { return response()->json('error', 500);}

    }

    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 99999999999999;
        $query = StudentsTakesSections::query();

        if ($request->has('student'))
            $query->where('student_id', '=', $request->query('student'));
        if ($request->has('section'))
            $query->where('section_id', '=', $request->query('section'));

        $length = count($query->get());
        $data = $query->offset($offset)->limit($limit)->get();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections Not Found', 0, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'section_id' => 'required',
            'letter_grade' => 'nullable',
            'average' => 'nullable',
            ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        foreach ($split as $key) {
            $user = UserStudent::where('id','=', request('student_id'))->first();
            if ($user){
                $section = Section::where('id','=', request('section_id'))->first();
                if ($section) {
                    $data = new StudentsTakesSections();
                    $data->student_id = request('student_id');
                    $data->section_id = request('section_id');
                    $data->letter_grade = request('letter_grade');
                    $data->average = request('average');
                    $data->save();
                    if ($data) {
                        $log = new Log();
                        $log->area = 'StudentsTakesSections';
                        $log->areaid = $data->id;
                        $log->user = Auth::id();
                        $log->ip = \Request::ip();
                        $log->type = 1;
                        $log->info = 'StudentsTakesSections '.$data->id.' Created for the University '.$data->university;
                        $log->save();
                        return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Created', 201);
                    } else {
                        return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not saved', 500);
                    }
                } else {
                    return $this->apiResponse(ResaultType::Error, null, 'Section not found', 404);
                }
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'User not found', 404);
            }
        }

    }

    


    public function show($id)
    {
        $data = StudentsTakesSections::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable',
            'section_id' => 'nullable',
            'letter_grade' => 'nullable',
            'average' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = StudentsTakesSections::find($id);

        if ($data) {
            if (request('student_id') != '') {
                $data->student_id = request('student_id');
            }
            if (request('section_id') != '') {
                $data->section_id = request('section_id');
            }
            if (request('letter_grade') != '') {
                $data->letter_grade = request('letter_grade');
            }
            if (request('average') != '') {
                $data->average = request('average');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'StudentsTakesSections';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'StudentsTakesSections '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'StudentsTakesSections not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    

    public function destroy($id)
    {
        $data = StudentsTakesSections::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'StudentsTakesSections Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }

}

