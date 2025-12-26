<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SalesSellerLogin extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='sales_seller_login',$timestamps=false;
    protected $fillable = [
        'name',
        'sales_id',
        'seller_id',
        'inserted_date',
        'modified_date',
        'status'

    ];
}
