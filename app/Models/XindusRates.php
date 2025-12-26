<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class XindusRates extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='xindus_rates',$timestamps=false;
    protected $fillable = [
        'weight',
        'rate',
        'is_additional',
        'initial_weight',
        'extra_charge',
        'extra_limit',
        'seller_id'
    ];
}
