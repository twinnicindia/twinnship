<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CourierCODRemittance extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'courier_cod_remittance', $timestamps = true;
    protected $fillable = [
        'order_id',
        'seller_id',
        'customer_order_number',
        'awb_number',
        'courier_partner',
        'awb_assigned_date',
        'delivery_date',
        'due_date_of_remittance',
        'actual_date_of_remittance',
        'invoice_date',
        'bank_name',
        'bank_reference_no',
        'transaction_mode',
        'cod_amount',
    ];
}
