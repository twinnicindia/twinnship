<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class ZoneMapping extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='zone_mapping',$timestamps=false;
    protected $fillable = [
        'partner_id',
        'courier_partner',
        'city',
        'state',
        'has_cod',
        'has_dg',
        'has_prepaid',
        'has_reverse',
        'picker_zone',
        'pincode',
        'routing_code',
        'cod_limit'
    ];
}
