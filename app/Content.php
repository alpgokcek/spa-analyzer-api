<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'content';
    // kullanılabilecek kolonlar
    protected $fillable = [
    ];
    // kullanılamayacak kolonlar (örn: 'password')
    // protected $guarded = [];

    public function allContentUsers() {
        return $this->belongsToMany('App\User', 'users');
    }
    public function getContentUser() {
        return $this->belongsTo('App\User','users');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];
}
