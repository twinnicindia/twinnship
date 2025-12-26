<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Invoice_orders extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='invoice_orders',$timestamps=false;
    protected $fillable = [
     'invoice_id',
     'order_id'
    ];
}
