<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class NdrattempsWhastsapp extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'ndr_attempt_by_whatsapp', $timestamps = false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'type',
        'payload',
        'inserted'
    ];
}
