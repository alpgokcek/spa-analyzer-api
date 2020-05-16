<?php

namespace App\Imports;

use App\User;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Illuminate\Support\Facades\Hash;


use Maatwebsite\Excel\Concerns\WithStartRow;

class UserImport implements ToModel, SkipsOnError, SkipsOnFailure, WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;

    private $rowNo = 1;
    public $err = array();

    public function startRow():int {
        return 2;
    }
    public function model(array $row)
    {

        //$data = User::find($row[0]);
        ++$this->rowNo;
        /*if ($data) {
            return ' kullanıcı zaten kayıtlı';
        } else {*/
            return new User([
                'name' => $row[0],
                'email' => $row[1],
								'password' => Hash::make($row[2]),
								'phone' => $row[3],
								'level' => $row[4],
                'university' => $row[5],
                'faculty_id' => $row[6],
                'department_id' => $row[7],
                'student_id' => $row[8],
            ]);
        //}
    }

    public function onError(\Throwable $e)
    {
        $this->err = array_add($this->err, $this->rowNo, $e);
    }
}

