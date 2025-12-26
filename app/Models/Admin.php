<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='admin',$timestamps=false;
    protected $fillable = [
        'name',
        'type',
        'email',
        'mobile',
        'password',
        'image',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status',
        'ins',
        'del',
        'modi'
    ];
}
