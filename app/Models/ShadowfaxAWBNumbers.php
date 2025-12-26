<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShadowfaxAWBNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='shadowfax_awb_numbers',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'used',
        'used_datetime',
        'seller_id',
        'flow',
        'inserted'
    ];
}
