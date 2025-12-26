<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EcomExpressAwbs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ecom_express_awbs',$timestamps=false;
    protected $fillable = [
        'courier_partner',
        'order_type',
        'awb_number',
        'used',
        'used_by',
        'used_time',
        'generated'
    ];
}
