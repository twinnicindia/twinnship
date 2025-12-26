<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Redeems extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='redeems',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'code_id',
        'value',
        'redeemed',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
