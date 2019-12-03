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
        if ($this->credit - ($this->credit * 2) >= $this->balance) {
            $bcClass = 'bg-danger';
        } else if ( $this->balance <= 0 ) {
            $bcClass = 'bg-info';
        } else {
            $bcClass = 'bg-success';
        }
        return '<span class="'.$bcClass.' d-block mx-auto text-light rounded-sm py-1">'.$this->balance.'</span>';
    }

    public function getBalanceTitleAttribute() {
        if ($this->credit - ($this->credit * 2) >= $this->balance) {
            $bcTitle = 'Credit limit expired';
        } else if ( $this->balance <= 0 ) {
            $bcTitle = 'Credit limit is running out';
        } else {
            $bcTitle = '';
        }
        return $bcTitle;

    }

    public function getSalesStatusAttribute() {
        return true;
    }
}
