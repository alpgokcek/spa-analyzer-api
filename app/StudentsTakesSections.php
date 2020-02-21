<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsTakesSections extends Model
{
    protected $table = 'students_takes_sections';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
