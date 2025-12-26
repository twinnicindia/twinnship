<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Channels extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='channels',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'channel_name',
        'channel',
        'api_key',
        'password',
        'store_url',
        'shared_secret',
        'woo_consumer_key',
        'woo_consumer_secret',
        'magento_access_token',
        'store_hippo_access_key',
        'kart_rocket_api_key',
        'auto_fulfill',
        'auto_cancel',
        'auto_cod_paid',
        'last_sync',
        'last_id',
        'amazon_mws_token',
        'amazon_seller_id',
        'company_id',
        'company_token',
        'amazon_token',
        'company_carrier_id',
        'last_executed',
        'amazon_refresh_token',
        'amazon_report_id',
        'fetch_woocommerce_order_number',
        'scince_abandon_id',
        'send_abandon_sms',
        'updated'
    ];
}
