<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinCode extends Model
{
    protected $table = 'pin_code';
    protected $fillable = [];

    public function allPinUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getPinUser() {
        return $this->belongsTo('App\User','users');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
