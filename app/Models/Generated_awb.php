<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Generated_awb extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='generated_awb',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'date',
        'no_of_awb',
        'partner_id',
        'inserted'
    ];
}
