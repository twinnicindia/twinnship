<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomXbeesAwbnumber extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='custom_xbees_awb_numbers',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'seller_id',
        'order_type',
        'batch_number',
        'awb_number',
        'used',
        'used_time',
        'courier_partner',
        'assigned',
        'generated_id',
        'generated'
    ];
}
