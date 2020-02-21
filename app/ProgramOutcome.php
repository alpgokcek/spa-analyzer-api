<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramOutcome extends Model
{
    protected $table = 'program_outcome';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
