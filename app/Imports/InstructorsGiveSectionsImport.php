<?php

namespace App\Imports;

use App\InstructorsGiveSections;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Maatwebsite\Excel\Concerns\WithStartRow;

class InstructorsGiveSectionsImport implements ToModel, SkipsOnError, SkipsOnFailure, WithStartRow
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
        return new InstructorsGiveSections([
            'instructor_id' => $row[0],
            'section_id' => $row[1]
        ]);
    }

    public function onError(\Throwable $e)
    {
        $this->err = array_add($this->err, $this->rowNo, $e);
    }

}
