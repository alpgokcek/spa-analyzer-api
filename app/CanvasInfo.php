<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasInfo extends Model
{
    protected $table = 'canvas_info';
    protected $fillable = [
    ];

    public function canvasLabel() {
        return $this->hasMany('App\CanvasLabel');
    }

    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }

    public function infoLabels() {
        return $this->hasMany('App\CanvasInfoLabel','info');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
