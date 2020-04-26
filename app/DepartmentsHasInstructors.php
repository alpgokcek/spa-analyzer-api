<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentsHasInstructors extends Model
{
    protected $table = 'departments_has_instructors';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
