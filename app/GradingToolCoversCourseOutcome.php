<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradingToolCoversCourseOutcome extends Model
{
    protected $table = 'grading_tool_covers_course_outcome';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
