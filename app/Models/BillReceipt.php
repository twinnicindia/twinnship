<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class BillReceipt extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bill_receipt',$timestamps=false;
    protected $fillable = [
        'seller_id', 
        'note_number',
        'note_reason',
        'gstin',
        'total',
        'note_date',
    ];
}
