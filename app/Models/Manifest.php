<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Manifest extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='manifest',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'courier',
        'number_of_order',
        'status',
        'p_ref_no',
        'warehouse_name',
        'warehouse_contact',
        'warehouse_address',
        'warehouse_gst_no',
        'type',
        'created',
        'created_time'
    ];
}
