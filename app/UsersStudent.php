<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersStudent extends Model
{
    protected $table = 'users_student';
    protected $fillable = ['id','advisor_user_id','department_id','is_major','status'];
    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
