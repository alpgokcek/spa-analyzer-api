<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinCode extends Model
{
    protected $table = 'pin_code';
    protected $fillable = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
