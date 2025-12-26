<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class InvalidContact extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='invalid_contact',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'contact',
        'awb_number',
        'date',
        'status'

    ];
}
