<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasSetting extends Model
{
    protected $table = 'canvas_settings';
    protected $fillable = [
    ];

    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }

}
