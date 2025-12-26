<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class XbeesAwbnumberUnique extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='xbees_awb_numbers_unique',$timestamps=false;
    protected $fillable = [
        'awb_number',
        'batch_number',
        'used',
        'used_time',
        'courier_partner',
        'assigned',
        'seller_id',
        'generated_id',
        'generated'
    ];
}
