<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersInstructor extends Model
{
    protected $table = 'users_instructor';
    protected $fillable = [
    ];
    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
