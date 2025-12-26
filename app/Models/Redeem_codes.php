<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Redeem_codes extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='redeem_codes',$timestamps=false;
    protected $fillable = [
        'title',
        'code',
        'value',
        'limit',
        'min_amount',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
