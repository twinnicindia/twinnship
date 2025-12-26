<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CourierMissStatusCode extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='courier_miss_status_code',$timestamps=false;
    protected $fillable = [
        'order_id',
        'courier_keyword',
        'status',
        'status_description',
        'json',
        'created',
    ];
}
