<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvasSettingLabel extends Model
{
    protected $table = 'canvas_setting_label';
    protected $fillable = [
    ];

    public function getCanvas() {
        return $this->belongsTo('App\Canvas', 'canvas');
    }

}
