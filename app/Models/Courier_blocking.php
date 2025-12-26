<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Courier_blocking extends Authenticatable {
    use HasFactory, Notifiable;
    public $table = 'courier_blocking';
    public $timestamps = true;
    protected $fillable = [
        'seller_id',
        'courier_partner_id',
        'is_blocked',
        'zone_a',
        'zone_b',
        'zone_c',
        'zone_d',
        'zone_e',
        'cod',
        'prepaid',
        'is_approved',
        'remark',
    ];

    /**
     * Get the sellers details.
     */
    public function seller() {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the courier partner details.
     */
    public function partner() {
        return $this->belongsTo(Partners::class, 'courier_partner_id', 'id');
    }
}
