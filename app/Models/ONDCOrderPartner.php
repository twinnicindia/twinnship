<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ONDCOrderPartner extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ondc_order_partner',$timestamps=false;
    protected $fillable = [
        'order_id',
        'seller_id',
        'courier_partner',
        'inserted'
    ];
}
