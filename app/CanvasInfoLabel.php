<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasInfoLabel extends Model
{
    protected $table = 'canvas_info_label';
    protected $fillable = [
    ];
    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }
    public function getCanvasInfo() {
        return $this->belongsTo('App\CanvasInfo', 'canvas_info');
    }
    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
