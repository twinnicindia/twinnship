<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WebContactUs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_contact_us',$timestamps=false;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'company_name',
        'website',
        'monthly_shipment',
        'type',
        'order_id',
        'purchase_date',
        'amount',
        'channel_name',
        'city',
        'message',
        'inserted',
        'inserted_ip'
    ];
}
