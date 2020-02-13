<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersStudent extends Model
{
    protected $table = 'users_student';
    protected $fillable = [
    ];
    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
