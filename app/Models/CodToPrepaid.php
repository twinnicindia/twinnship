<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CodToPrepaid extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='cod_to_prepaid_history',$timestamps=false;
    protected $fillable = [
        'order_id',
        'seller_id',
        'awb_number',
        'comment',
        'attachment',
        'employee_id',
        'created_at'
    ];
}
