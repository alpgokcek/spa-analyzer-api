<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balance';
    protected $fillable = [];
    // protected $guarded = [];

    public function getBalanceBusiness() {
        return $this->belongsTo('App\Business','business');
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y',
        'arrival_date' => 'date:d-m-Y H:i:s'
    ];

}
