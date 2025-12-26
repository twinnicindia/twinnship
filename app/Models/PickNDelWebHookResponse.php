<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PickNDelWebHookResponse extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='pickndel_webhook_record',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'request',
        'inserted'
    ];
}
