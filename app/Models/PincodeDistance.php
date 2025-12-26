<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PincodeDistance extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='pincode_distance',$timestamps=false;
    protected $fillable = [
        'pincode1', 
        'pincode2',
        'distance',
        'payload',
        'inserted'
    ];
}
