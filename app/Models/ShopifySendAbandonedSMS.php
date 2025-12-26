<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShopifySendAbandonedSMS extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='shopify_abandon_sms_log',$timestamps=true;
    protected $fillable = [
        'channel_id',
        'abandon_id',
        'abandoned_checkout_url',
        'sub_total_amount',
        'presentment_currency',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'datetime',
        'sms_status',
        'sms_charge',
        'sms_sent_at'
    ];
}
