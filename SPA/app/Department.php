<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $fillable = [];

    public function infos() {
        return $this->morphMany('App\DepartmentInfo', 'department');
    }

    public function getDepartmentSite() {
        return $this->belongsTo('App\Website', 'website');
    }
    public function getDepartmentUser() {
        return $this->belongsTo('App\User','user');
    }

    public function departmentInfos() {
        return $this->hasMany('App\DepartmentInfo','department');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y'
    ];

}
