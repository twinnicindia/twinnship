<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OMS extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='oms', $timestamps=false;
    protected $fillable = [
        'seller_id',
        'oms_name',
        'title',
        'store_url',
        'easyship_bearer_token',
        'easyecom_api_token',
        'clickpost_username',
        'clickpost_key',
        'vineretail_api_owner',
        'vineretail_api_key',
        'auto_fulfill',
        'auto_cancel',
        'auto_cod_paid',
        'status',
        'inserted',
        'inserted_by',
        'easycom_username',
        'easycom_password'
    ];
}
