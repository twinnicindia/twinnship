<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SMCNewAWB extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='smc_new_awb',$timestamps=false;
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
