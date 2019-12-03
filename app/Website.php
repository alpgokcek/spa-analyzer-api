<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $table = 'website';
    // kullanılabilecek kolonlar
    protected $fillable = [
    ];
    // kullanılamayacak kolonlar (örn: 'password')
    // protected $guarded = [];

    public function allWebsiteUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getWebsiteUser() {
        return $this->belongsTo('App\User','users');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
