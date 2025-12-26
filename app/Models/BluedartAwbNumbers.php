<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BluedartAwbNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bluedart_awb_numbers',$timestamps=false;
    protected $fillable = [
        'batch_number',
        'awb_number',
        'courier_keyword',
        'awb_type',
        'used',
        'used_time',
        'used_by',
        'inserted',
        'inserted_by'
    ];
}
