<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BulkCancelOrdersJob extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bulk_cancel_orders_job',$timestamps=false;
    protected $fillable = [
        'orders',
        'seller_id',
        'created',
        'total',
        'cancelled',
        'failed',
        'status',
        'completed',
        'is_notified'
    ];
}
