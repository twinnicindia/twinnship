<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employees extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='employees',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'code',
        'employee_name',
        'email',
        'mobile',
        'password',
        'permissions',
        'created',
        'code',
        'modified_at'
    ];
}
