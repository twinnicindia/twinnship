<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class InternationalOrders extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='international_orders',$timestamps=false;
    protected $fillable = [
        'order_id',
        'iec_code',
        'invoice_number',
        'ad_code',
        'ioss',
        'eori',
        'hsn',
        'hts',
        'qc_help_description',
        'qc_label',
        'qc_value_to_check',
        'qc_image',
        'business_type',
        'selling_mode',
        'business_category',
        'international_volume',
        'order_volume',
        'selling_internationally',
        'ofd_date',
        'entry_type',
        'shopify_tag',
        'cancel_source',
        'cancel_datetime',
        'cancel_ip',
        'rto_initiated_date',
        'connection_datetime',
        'ofd_attempt'
    ];
}
