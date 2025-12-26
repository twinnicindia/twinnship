<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Countries extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='countries',$timestamps=false;
    protected $fillable = [
        'title',
        'code',
        'status'
    ];
}
