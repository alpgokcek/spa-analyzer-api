<?php

namespace App\Imports;

use App\StudentsTakesSections;
use App\UsersStudent;
use App\Section;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class StudentsTakesSectionsImport implements ToModel,SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsErrors, SkipsFailures;
    public function model(array $row)
    {
        try{
            $import = new StudentsTakesSections([
                'student_code' => $row[0],
                'section_code' => $row[1],
                'letter_grade' => $row[2],
                'average' => $row[3]]);
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                dd($failure);
            }
        }
       
        return $import;
            
    }
}
