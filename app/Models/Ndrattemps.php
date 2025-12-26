<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Ndrattemps extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'ndr_attemps', $timestamps = false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'position',
        'raised_date',
        'raised_time',
        'action_by',
        'reason',
        'action_date',
        'action_status',
        'remark',
        'u_address_line1',
        'u_address_line2',
        'updated_mobile',
        'ndr_data_type',
        'delivery_date'
    ];
}
