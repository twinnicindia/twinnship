<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DefaultInvoiceAmount extends Authenticatable {
    use HasFactory, Notifiable;
    public $table = 'default_invoice_amount';
    public $timestamps = false;
    protected $fillable = [
        'seller_id',
        'partner_id',
        'amount',
        'updated_at',
        'created_at',
        'status'
    ];

}
