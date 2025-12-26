<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DelhiveryAWBNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='delhivery_awb_numbers',$timestamps=false;
    protected $fillable = [
        'courier_partner',
        'awb_number',
        'used',
        'used_time',
        'seller_id',
        'seller_type'
    ];
}
