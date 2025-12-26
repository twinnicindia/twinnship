<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Career extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='brand_images',$timestamps=false;
    protected $fillable = [
        'image',
        'status'
    ];
}
