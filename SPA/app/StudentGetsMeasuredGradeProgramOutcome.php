<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentGetsMeasuredGradeProgramOutcome extends Model
{
    protected $table = 'student_gets_measured_grade_program_outcome';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
