<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersAdmin extends Model
{
    protected $table = 'users_admin';
    protected $fillable = [
    ];
    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
