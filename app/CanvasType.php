<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasType extends Model
{
    protected $table = 'canvas_type';
    protected $fillable = [
    ];

    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
