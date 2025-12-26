<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class ServiceablePincode extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='serviceable_pincode',$timestamps=false;
    protected $fillable = [
        'partner_id',
        'courier_partner',
        'pincode',
        'city',
        'state',
        'active',
        'branch_code',
        'is_cod',
        'status',
        'inserted',
        'modified',
        'remark',
        'cluster_code'
    ];
}
