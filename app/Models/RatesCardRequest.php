<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class RatesCardRequest extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='rate_card_request',$timestamps=false;
    protected $fillable = [
        'plan_id',
        'seller_id',
        'status',
        'status_datetime',
        'created',
        'created_by'
    ];
}
