<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GatiPackageNumber extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='gati_package_numbers',$timestamps=false;
    protected $fillable = [
        'courier_partner',
        'package_number',
        'used',
        'used_by',
        'used_time'
    ];
}
