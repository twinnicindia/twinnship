<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class ManifestOrder extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='manifest_order',$timestamps=false;
    protected $fillable = [
     'manifest_id',
     'order_id'
    ];
}
