<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MovinAWBNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='movin_awb_numbers',$timestamps=false;
    protected $fillable = [
        'mode',
        'awb_number',
        'used',
        'used_time',
        'seller_id',
        'inserted'
    ];
}
