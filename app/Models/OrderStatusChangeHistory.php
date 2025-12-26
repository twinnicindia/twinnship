<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrderStatusChangeHistory extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='order_status_change_history',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'awb_number',
        'courier_partner',
        'from_status',
        'to_status',
        'updated_date_payload',
        'crm_id',
        'inserted',
        'ip',
    ];
}
