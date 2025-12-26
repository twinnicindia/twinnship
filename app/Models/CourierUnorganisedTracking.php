<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CourierUnorganisedTracking extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='unorganised_tracking_from_courier',$timestamps=false;
    protected $fillable = [
        'courier',
        'awb_number',
        'courier_status',
        'shipease_status',
        'received_date',
        'courier_scan_date',
        'inserted'
    ];
}
