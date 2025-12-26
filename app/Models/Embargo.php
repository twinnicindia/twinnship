<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Embargo extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='embargo',$timestamps=false;
    protected $fillable = [
        'image',
        'pincodes',
        'remark',
        'pincode_ids',
        'status'
    ];
}
