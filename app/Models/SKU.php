<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SKU extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='sku',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'sku',
        'product_name',
        'product_price',
        'weight',
        'length',
        'width',
        'height',
        'brand_name'
    ];
}
