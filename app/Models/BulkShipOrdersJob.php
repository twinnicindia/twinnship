<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BulkShipOrdersJob extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bulk_ship_orders_job',$timestamps=false;
    protected $fillable = [
        'orders',
        'seller_id',
        'created',
        'total',
        'shipped',
        'failed',
        'status',
        'completed',
        'is_notified'
    ];
}
