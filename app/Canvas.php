<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canvas extends Model
{
    protected $table = 'canvas';
    protected $fillable = [];

    public function infos() {
        return $this->morphMany('App\CanvasInfo', 'canvas');
    }

    public function getCanvasSite() {
        return $this->belongsTo('App\Website', 'website');
    }
    public function getCanvasUser() {
        return $this->belongsTo('App\User','user');
    }

    public function canvasInfos() {
        return $this->hasMany('App\CanvasInfo','canvas');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
