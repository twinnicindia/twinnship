<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Channel_orders_log extends Authenticatable {
    use HasFactory, Notifiable;
    public $table = 'channel_orders_log';
    public $timestamps = false;
    protected $fillable = [
        'channel',
        'channel_id',
        'seller_id', 
        'order_response',
        'item_fetched',
        'item_response',
        'address_fetched',
        'address_response',
        'inserted'
    ];
}
