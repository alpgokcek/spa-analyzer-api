<?php

namespace App\Imports;

use App\UsersStudent;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToModel, SkipsOnError, SkipsOnFailure, WithBatchInserts,WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;

    private $rowNo = 1;
    public $err = array();

    public function startRow():int{
        return 1;
    }

    public function model(array $row)
    {
        ++$this->rowNo;
        return new UsersStudent([
            'user' => $row[0],
            'advisor' => $row[1],
            'department' => $row[2],
            'is_major' => $row[3],
            'status' => $row[4]
        ]);
    }

    public function onError(\Throwable $e)
    {

        $this->err = array_add($this->err, $this->rowNo, $e);
        //array_push($this->err, $e);

    }

    public function batchSize(): int
    {
        return 1000;
    }
}

