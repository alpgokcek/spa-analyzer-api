<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentGetsMeasuredGradeCourseOutcome extends Model
{
    protected $table = 'student_gets_measured_grade_course_outcome';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
