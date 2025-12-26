<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Invoice extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='invoice',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'awb_number',
        'inv_id',
        'invoice_date',
        'due_date',
        'total',
        'status',
        'invoice_number'
    ];
}
