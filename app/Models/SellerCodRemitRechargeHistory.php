<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SellerCodRemitRechargeHistory extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='seller_cod_remit_recharge_history',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'order_ids',
        'awb_numbers',
        'request_recharge',
        'actual_recharge',
        'datetime'
    ];
}
