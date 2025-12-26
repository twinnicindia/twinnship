<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ReassignOrderDetails extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='reassign_order_details',$timestamps=false;
    protected $fillable = [
        'order_id',
        'old_awb_number',
        'new_awb_number',
        'courier_partner',
        'seller_id',
        'inserted'
    ];
}
