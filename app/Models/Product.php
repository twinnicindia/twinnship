<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Product extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='products',$timestamps=false;
    protected $fillable = [
        'order_id',
        'product_sku',
        'product_name',
        'product_unitprice',
        'product_qty',
        'total_amount',
        'item_id',
        'invoice_reference_number',
        'export_reference_number',
    ];
}
