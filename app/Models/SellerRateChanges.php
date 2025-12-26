<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SellerRateChanges extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='seller_rate_changes',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'modified',
        'modified_ip'
    ];
    function rateDetails(){
        return $this->hasMany(SellerRateChangeDetails::class,'seller_rate_change_id');
    }
}
