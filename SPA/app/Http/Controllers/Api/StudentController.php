<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\UsersStudent;
use App\Log;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;

use Maatwebsite\Excel\Facades\Excel;

use Validator;

class StudentsController extends ApiController
{
    public function uploadedFile(Request $request)
    {
 
        $import = new StudentsTakesSectionsImport();
        $import->import($request->fileUrl);
        
        return $this->apiResponse(ResaultType::Error, $import->err, 'hatalar', 403);
    }
}