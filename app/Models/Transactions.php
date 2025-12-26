<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Transactions extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='transactions',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'amount',
        'balance',
        'type',
        'redeem_type',
        'datetime',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'method',
        'description',
        'ip_address',
    ];
}
