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

class UsersStudentImport implements ToModel, SkipsOnError, SkipsOnFailure, WithStartRow
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
        return new UsersStudent([
            'id' => $row[0],
            'advisor_user_id' => $row[1],
            'department_id' => $row[2],
            'is_major' => $row[3],
            'status' => $row[4]
        ]);
    }

    public function onError(\Throwable $e)
    {
        $this->err = array_add($this->err, $this->rowNo, $e);
    }

}