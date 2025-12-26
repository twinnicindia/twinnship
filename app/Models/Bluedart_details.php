<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Bluedart_details extends Authenticatable {
    use HasFactory, Notifiable;
    public $table = 'bluedart_details';
    public $timestamps = true;
    protected $fillable = [
        'order_id',
        'pickup_token_number',
        'shipment_pickup_date',
    ];

    /**
     * Get the order details.
     */
    public function order() {
        return $this->belongsTo(Order::class);
    }
}
