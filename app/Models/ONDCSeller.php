<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ONDCSeller extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ondc_seller',$timestamps=false;
    protected $fillable = [
        'domain',
        'country',
        'city',
        'bap_id',
        'bap_uri',
        'bpp_id',
        'bpp_uri',
        'transaction_id',
        'message_id',
        'timestamp',
        'key',
        'ttl',
        'balance',
        'status',
        'created',
        'last_active',
    ];
}
