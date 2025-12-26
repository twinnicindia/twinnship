<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PendingShipments extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='pending_shipments',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'status',
        'inserted',
        'last_tried',
        'shipped',
        'notified'
    ];
}
