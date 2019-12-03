<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canvas extends Model
{
    protected $table = 'canvas';
    // kullanılabilecek kolonlar
    protected $fillable = [
    ];
    // kullanılamayacak kolonlar (örn: 'password')
    // protected $guarded = [];

    public function getCanvasSite() {
        return $this->belongsTo('App\Website', 'website');
    }
    public function getCanvasUser() {
        return $this->belongsTo('App\User','user');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
