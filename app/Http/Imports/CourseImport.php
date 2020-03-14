<?php
/*
namespace App\Imports;

use App\Course;
use App\Department;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class CourseImport implements ToCollection, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsErrors, SkipsFailures;
    public function collection(Collection $rows)
    {
        print_r($rows);
        try{
            foreach($rows as $row){
            $departments = preg_split('/[\ \n\,]+/', $row[0]);
            foreach($departments as $department){
                Course::create([
                    'department' => $department,
                    'code' => $row[1],
                    'title' => $row[2],
                    'credit' => $row[3],
                    'date_time' => $row[4]]);
            }
        }
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            echo "annen";
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                dd($failure);
            }
        }
       

            
    }
}
*/

namespace App\Imports;

use App\Course;
use App\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class CourseImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsErrors, SkipsFailures;
    public function collection(Collection $rows)
    {
        try{
            foreach ($rows as $row) 
            {
                $departments = preg_split('/[\ \n\,]+/', $row['department']);
                foreach($departments as $department){
                    Course::create([
                        'department' => $department,
                        'code' => $row['code'],
                        'year_and_term' => $row['year_and_term'],
                        'title' => $row['title'],
                        'credit' => $row['credit'],
                        'date_time' => $row['date_time'],
                        'status' => $row['status']
                    ]);
                }
            }
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            echo "annen";
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                dd($failure);
            }
        }
    }
}