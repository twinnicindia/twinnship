<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ChannelOrderStatusList extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='channel_order_status_list',$timestamps=false;
    protected $fillable = [
        'channel',
        'channel_id',
        'pickup_scheduled',
        'picked_up',
        'in_transit',
        'out_for_delivery',
        'delivered',
        'inserted',
        'modified'
    ];
}
