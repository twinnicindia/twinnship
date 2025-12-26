<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DTDCPushLogData extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='dtdc_push_data_log',$timestamps=false;
    protected $fillable = [
        'awb',
        'request',
        'inserted'
    ];
}
