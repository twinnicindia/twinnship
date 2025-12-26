<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MPS_AWB_Number extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='mps_awb_number',$timestamps=false;
    protected $fillable = [
        'order_id',
        'awb_number',
        'inserted',
        'label',
    ];

    /**
     * Get the order details.
     */
    public function order() {
        return $this->hasOne(Order::class);
    }
}
