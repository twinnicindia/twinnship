<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MarutiEcomAwbs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='maruti_awbs_ecom',$timestamps=false;
    protected $fillable = [
        'courier_partner',
        'awb_number',
        'used',
        'used_by',
        'used_time',
        'assigned',
        'seller_id',
        'generated_id',
        'generated'
    ];
}
