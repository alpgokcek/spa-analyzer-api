<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{

    public function model(array $row)
    {

        $data = User::find($row[0]);

        if ($data) {
            return ' kullanıcı zaten kayıtlı';
        } else {
            return new User([
                'code' => $row[0],
                'name' => $row[1],
                'faculty' => $row[2],
                'department' => $row[3]
            ]);

        }



    }
}

