<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseOutcome extends Model
{
    protected $table = 'course_outcome';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];
}
