<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShipmentLossDamage extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='order_loss_damage_by_crm',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'awb_number',
        'order_id',
        'crm_id',
        'datetime',
        'status',
        'courier_keyword'
    ];
}
