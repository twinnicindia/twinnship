<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class WeightReconciliation extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='weight_reconciliation',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'awb_number',
        'e_weight',
        'e_length',
        'e_breadth',
        'e_height',
        'applied_amount',
        'c_weight',
        'c_length',
        'c_breadth',
        'c_height',
        'charged_amount',
        's_weight',
        's_length',
        's_breadth',
        's_height',
        'settled_amount',
        'remark',
        'status',
        'action_taken_by',
        'created',
        'is_error',
        'error_message',
        'uploaded_at',
    ];
}
