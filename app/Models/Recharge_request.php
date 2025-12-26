<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Recharge_request extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='recharge_request',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'utr_number',
        'amount',
        'status',
        'created',
        'approved',
        'type'
    ];
}
