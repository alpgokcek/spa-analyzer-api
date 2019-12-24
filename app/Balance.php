<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balance';
    protected $fillable = [];

    public function getBalanceCustomer() {
        return $this->belongsTo('App\Customer','customer');
    }

    public function getBankNameAttribute() {
        return $this->bank . ' bank';
    }

    protected $casts = [
        'created_at' => 'date:d-m-Y',
        'updated_at' => 'date:d-m-Y',
        'arrival_date' => 'date:d-m-Y',
        'paid_date' => 'date:d-m-Y'
    ];

}
