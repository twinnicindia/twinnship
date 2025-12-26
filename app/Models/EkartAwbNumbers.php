<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EkartAwbNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ekart_awb_numbers',$timestamps=false;
    protected $fillable = [
        'number',
        'awb_number',
        'courier_partner',
        'assigned',
        'generated_id',
        'generated',
        'seller_id',
        'used',
        'used_time',
        'used_by',
        'created'
    ];
}
