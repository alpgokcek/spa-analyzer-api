<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\StudentsTakesSections;
use App\Imports\StudentsTakesSectionsImport;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Concerns\WithStartRow;


use Maatwebsite\Excel\Facades\Excel;
use Validator;


class StudentsTakesSectionsController extends ApiController implements ToModel,SkipsOnFailure, SkipsOnError,WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;

    public function startRow():int{
        return 2;
    }

    public function model(array $row)
    {       
        return new StudentsTakesSections([
            'student_id' => $row[0],
            'section_id' => $row[1],
            'letter_grade' => $row[2],
            'average' => $row[3]]
        );       
    }

    public function uploadedFile(Request $request)
    {
       
        //try{
            //Excel::import(new StudentsTakesSectionsController, $request->fileUrl);
        
        $import = new StudentsTakesSectionsController();
        $import->import($request->fileUrl);
        echo "başladı";
        foreach($import->errors() as $x){
            echo "1**************************************************1";
            echo "$x";
        }

        /*
        catch(\Throwable $e){
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                echo $failure; 
            }   
        }*/
        
        
       


        //$import = Excel::import(new StudentsTakesSectionsImport, $request->fileUrl);
        //$import = new StudentsTakesSectionsImport();
        //$import->import($request->fileUrl);
        
        // return($import->err);

        /*foreach ($import->failures() as $failure) {
            $failure->row(); // row that went wrong
            $failure->attribute(); // either heading key (if using heading row concern) or column index
            $failure->errors(); // Actual error messages from Laravel validator
            $failure->values(); // The values of the row that has failed.
            array_push($failureArray,$failure);  
        }*/

        //return $this->apiResponse(ResaultType::Error,null ,$import->failures(), 'hatalar', 403);
       
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

