<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PickedUpOrders extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='picked_orders_list',$timestamps=false;
    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'datetime'
    ];
}
