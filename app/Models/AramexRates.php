<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AramexRates extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='aramex_rates',$timestamps=false;
    protected $fillable = [
        'weight',
        'rate_1',
        'rate_2',
        'rate_3',
        'rate_4',
        'rate_5',
        'rate_6',
        'rate_7',
        'rate_8',
        'rate_1',
        'rate_10',
        'rate_11',
        'rate_12',
        'rate_13',
        'is_additional',
        'initial_weight',
        'extra_limit',
        'extra_charge_1',
        'extra_charge_2',
        'extra_charge_3',
        'extra_charge_4',
        'extra_charge_5',
        'extra_charge_6',
        'extra_charge_7',
        'extra_charge_8',
        'extra_charge_9',
        'extra_charge_10',
        'extra_charge_11',
        'extra_charge_12',
        'extra_charge_13',
        'seller_id'
    ];
}
