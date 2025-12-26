<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Support extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_support',$timestamps=false;
    protected $fillable = [
        'image',
        'title',
        'status'
    ];
}
