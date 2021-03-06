<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsTakesSections extends Model
{
    protected $table = 'students_takes_sections';
    protected $fillable = ['student_id','section_id','letter_grade','average'];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
