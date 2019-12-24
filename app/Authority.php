<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    protected $table = 'authority';
    protected $fillable = [
    ];

    public function getAuthorityUser() {
        return $this->belongsTo('App\User','user');
    }
    public function getAuthorityCustomer() {
        return $this->belongsTo('App\Customer','customer');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

    public function getAuthorityStatusAttribute() {
        return 'Work: ' . $this->work .' Create: '. $this->c.' Read: '. $this->r. ' Update: ' . $this->u. ' Delete: ' . $this->d;
    }

}
