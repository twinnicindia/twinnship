<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SmartrAwbs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='smartr_awbs',$timestamps=false;
    protected $fillable = [
        'courier_partner',
        'awb_number',
        'used',
        'used_by',
        'used_time'
    ];
}
