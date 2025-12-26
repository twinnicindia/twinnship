<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShipmentHoldHistory extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='shipment_hold_history',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'awb_number',
        'employee_id',
        'created_at'
    ];
}
