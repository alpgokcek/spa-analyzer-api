<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pins extends Model
{
    protected $table = 'pins';
    protected $fillable = [];

    public function allPinUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getPinUser() {
        return $this->belongsTo('App\User','users');
    }

    public function getPinsListAttribute() {
        return $this->title.' - %'.$this->discount. ' - €'.$this->price;
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
