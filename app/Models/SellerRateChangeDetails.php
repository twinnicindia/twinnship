<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class SellerRateChangeDetails extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='seller_rate_change_details',$timestamps=false;
    protected $fillable = [
        'plan_id',
        'seller_rate_change_id',
        'seller_id',
        'partner_id',
        'within_city',
        'within_state',
        'metro_to_metro',
        'rest_india',
        'north_j_k',
        'cod_charge',
        'cod_maintenance',
        'extra_charge_a',
        'extra_charge_b',
        'extra_charge_c',
        'extra_charge_d',
        'extra_charge_e',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by'
    ];
    function sellerRates(){
        return $this->belongsTo(SellerRateChanges::class,'seller_rate_change_id');
    }
    function sellers(){
        return $this->belongsTo(Seller::class,'seller_id');
    }
    function partners(){
        return $this->belongsTo(Partners::class,'partner_id');
    }
}
