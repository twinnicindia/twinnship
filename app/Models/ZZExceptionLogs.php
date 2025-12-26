<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ZZExceptionLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='zz_tracking_exception_log',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'order_id',
        'exception_message',
        'courier_partner',
        'seller_id',
        'inserted'
    ];
}
