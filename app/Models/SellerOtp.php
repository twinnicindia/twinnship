<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SellerOtp extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='seller_otp',$timestamps=false;
    protected $fillable = [
        'mobile',
        'email',
        'otp',
        'created_at',
        'ip_address',
        'status',
    ];
}
