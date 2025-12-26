<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BulkShipOrdersJobDetails extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bulk_ship_orders_job_details',$timestamps=false;
    protected $fillable = [
        'job_id',
        'order_id',
        'is_deleted',
        'is_shipped'
    ];
}
