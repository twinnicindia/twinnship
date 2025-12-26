<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class ServiceablePincodeFM extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='serviceable_pincode_fm',$timestamps=false;
    protected $fillable = [
        'partner_id',
        'courier_partner',
        'pincode',
        'city',
        'state',
        'branch_code',
        'origin_code',
        'status',
        'inserted'
    ];
}
