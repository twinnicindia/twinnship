<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class States extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='states',$timestamps=false;
    protected $fillable = [
        'state',
        'code'
    ];
}
