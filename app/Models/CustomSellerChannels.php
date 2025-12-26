<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomSellerChannels extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='custom_seller_channels',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'courier_partner',
        'key1',
        'key2',
        'key3',
        'key4',
        'key5',
        'key6',
        'key7',
        'key8',
        'key9',
        'key10',
        'status',
        'inserted'
    ];
}
