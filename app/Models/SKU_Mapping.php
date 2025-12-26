<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class SKU_Mapping extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='sku_mapping', $timestamps=true;
    protected $fillable = [
        'seller_id',
        'parent_sku',
        'child_sku',
        'created_at',
        'updated_at',
    ];
}
