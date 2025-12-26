<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AmazonAccessTokens extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='amazon_access_tokens',$timestamps=false;
    protected $fillable = [
        'refresh_token',
        'access_token',
        'generated_time',
        'valid_till'
    ];
}
