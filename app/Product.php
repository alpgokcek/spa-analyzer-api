<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [];

    public function allProductUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getProductUser() {
        return $this->belongsTo('App\User','users');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
