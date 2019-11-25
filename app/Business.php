<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'business';
    // kullanılabilecek kolonlar
    protected $fillable = [
    ];
    // kullanılamayacak kolonlar (örn: 'password')
    // protected $guarded = [];

    public function allBusinessUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getBusinessUser() {
        return $this->belongsTo('App\User','users');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

    // birçok datayı alıp evirip çevirip farklı bir data haline getirebiliriz.
    public function getFullAddressAttribute() {
        return $this->address . ' - ' . $this->postal_code. ' - ' . $this->city;
    }
    public function getBalanceCreditAttribute() {
        $bcClass = $this->balance <= $this->credit ? 'bg-danger' : 'bg-success';
        return '<span class="'.$bcClass.'  d-inline-block w-75 mx-auto text-light rounded-sm py-1">'.$this->balance.'</span>';
    }

    public function getSalesStatusAttribute() {
        return true;
    }
}
