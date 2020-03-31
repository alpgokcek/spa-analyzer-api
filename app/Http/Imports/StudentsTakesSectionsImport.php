<?php

namespace App\Imports;

/*use App\StudentsTakesSections;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Validators\ValidationException;
*/
/*
class StudentsTakesSectionsImport implements ToModel//,SkipsOnFailure, SkipsOnError//,WithBatchInserts//,WithStartRow
{
    use Importable;//, SkipsErrors, SkipsFailures;

    //private $rowNum = 1;
    //public $err = Null;


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


    public function batchSize(): int{
        return 1000;
    }*/
//}
