<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','api_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getCompanyUser() {
        return $this->belongsTo('App\Company','company');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'date:d-m-Y',
    ];
}
