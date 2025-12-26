<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DtdcSEAwbNumbers extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='dtdc_se_awb_numbers',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'used',
        'used_time',
        'used_by',
        'created'
    ];
}
