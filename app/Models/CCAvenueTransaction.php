<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CCAvenueTransaction extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ccavenue_transaction',$timestamps=false;
    protected $fillable = [
        'order_id',
        'seller_id',
        'amount',
        'datetime',
        'status',
    ];
}
