<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasSettingJson extends Model
{
    protected $table = 'canvas_settings_json';
    protected $fillable = [
    ];

    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }

}
