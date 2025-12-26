<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BrandedTracking extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='brand_tracking',$timestamps=false;
    protected $fillable = [
        'brand_logo',
        'banner1',
        'banner2',
        'offer_title',
        'product_image1',
        'product_image2',
        'product_image3',
        'product_image4',
        'product_back_image1',
        'product_back_image2',
        'product_back_image3',
        'product_back_image4',
        'product_title1',
        'product_title2',
        'product_title3',
        'product_title4',
        'product_amount1',
        'product_amount2',
        'product_amount3',
        'product_amount4',
        'seller_id',
        'link',
        'link1',
        'link2',
        'link3',
        'status'
    ];
}
