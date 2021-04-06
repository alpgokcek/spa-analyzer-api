<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstructorsGivesSections extends Model
{
    protected $table = 'instructors_gives_sections';
    protected $fillable = ['instructor_id','section_id'];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
