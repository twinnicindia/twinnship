<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WebSubscribe extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_subscribe',$timestamps=false;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'status',
        'inserted'
    ];
}
