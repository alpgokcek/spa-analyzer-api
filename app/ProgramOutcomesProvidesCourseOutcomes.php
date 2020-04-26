<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramOutcomesProvidesCourseOutcomes extends Model
{
    protected $table = 'program_outcomes_provides_course_outcomes';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
