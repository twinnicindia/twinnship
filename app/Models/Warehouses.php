<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Warehouses extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='warehouses',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'warehouse_name',
        'warehouse_code',
        'contact_name',
        'contact_number',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'pincode',
        'gst_number',
        'support_email',
        'support_phone',
        'created_at',
        'code',
        'modified_at',
        'default',
        'org_unit_id',
        'easyecom_warehouse_id',
        'pidge_address_id',
    ];

    /**
     * Get the seller details.
     */
    public function seller() {
        return $this->belongsTo(Seller::class);
    }
}
