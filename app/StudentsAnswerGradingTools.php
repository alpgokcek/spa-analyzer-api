<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsAnswerGradingTools extends Model
{
    protected $table = 'student_answers_grading_tool';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
