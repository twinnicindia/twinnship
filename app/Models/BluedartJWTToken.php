<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BluedartJWTToken extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bluedart_jwt_token',$timestamps=false;
    protected $fillable = [
        'token',
        'inserted',
        'is_alpha',
        'expired'
    ];
}
