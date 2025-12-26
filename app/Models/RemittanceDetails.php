<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RemittanceDetails extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='remittance_details',$timestamps=false;
    protected $fillable = [
        'cod_transactions_id',
        'crf_id',
        'awb_number',
        'order_number',
        'cod_amount',
        'mode',
        'remittance_amount',
        'utr_number'
    ];
}
