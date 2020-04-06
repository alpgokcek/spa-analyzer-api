<?php

namespace App\Imports;

use App\Section;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Maatwebsite\Excel\Concerns\WithStartRow;

class SectionImport implements ToModel, SkipsOnError, SkipsOnFailure, WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;

    private $rowNo = 1;
    public $err = array();

    public function startRow():int {
        return 2;
    }
    public function model(array $row)
    {
        ++$this->rowNo;
        return new Section([
            'course_id' => $row[0],
            'title' => $row[1],
            'status' => $row[2],
        ]);
    }

    public function onError(\Throwable $e)
    {
        $this->err = array_add($this->err, $this->rowNo, $e);
    }

}