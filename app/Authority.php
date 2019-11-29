<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    protected $table = 'authority';
    protected $fillable = [
    ];
    // kullanılamayacak kolonlar (örn: 'password')
    // protected $guarded = [];

    public function getAuthorityUser() {
        return $this->belongsTo('App\User','user');
    }
    public function getAuthorityBusiness() {
        return $this->belongsTo('App\Business','business');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

    // birçok datayı alıp evirip çevirip farklı bir data haline getirebiliriz.
    public function getAuthorityStatusAttribute() {
        return 'Work: ' . $this->work .' Create: '. $this->c.' Read: '. $this->r. ' Update: ' . $this->u. ' Delete: ' . $this->d;
    }

}
