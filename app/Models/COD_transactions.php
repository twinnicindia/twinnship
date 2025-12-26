<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class COD_transactions extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='cod_transactions',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'crf_id',
        'amount',
        'mode',
        'type',
        'description',
        'datetime',
        'description',
        'redeem_type',
        'utr_no',
        'early_cod_charge',
        'pay_type',
        'remitted_by',
    ];
}
