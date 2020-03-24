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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsTakesSectionsImport implements ToModel,SkipsOnFailure, SkipsOnError, WithBatchInserts
{
    use Importable, SkipsErrors, SkipsFailures;

    private $rowNum = 1;
    private $err = array();


    public function startRow():int{
        return 2;
    }

    public function model(array $row)
    {
        ++$this->rowNum;
        return new StudentsTakesSections([
            'student_id' => $row[0],
            'section_id' => $row[1],
            'letter_grade' => $row[2],
            'average' => $row[3]]
        );       
    }

    public function onError(\Throwable $e){
        $this->err = array_add($this->err, $this->row, $e);
    }


    public function batchSize(): int{
        return 1000;
    }
}
