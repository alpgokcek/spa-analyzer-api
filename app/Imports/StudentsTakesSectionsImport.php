<?php

namespace App\Imports;

use App\StudentsTakesSections;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsTakesSectionsImport implements ToModel, SkipsOnError, SkipsOnFailure, WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;

    private $rowNo = 0;
    public $err = array();

    public function startRow():int {
        return 2;
    }
    public function model(array $row)
    {
        ++$this->rowNo;
        return new StudentsTakesSections([
            'student_id' => $row[0],
            'section_id' => $row[1],
            'letter_grade' => $row[2],
            'average' => $row[3]
        ]);
    }

    public function onError(\Throwable $e)
    {
        //++$this->rowNo;
        $this->err = array_add($this->err, $this->rowNo, $e);
        //array_push($this->err, $e);

    }

}

