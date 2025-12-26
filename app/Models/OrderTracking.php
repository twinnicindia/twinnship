<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class OrderTracking extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='order_tracking',$timestamps=false;
    protected $fillable = [
        'order_id',
        'awb_number',
        'orderno',
        'status_code',
        'status',
        'status_description',
        'remarks',
        'location',
        'updated_date',
        'updated_by',
        'created_at'
    ];
}
