<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CareerExpect extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_career_expect',$timestamps=false;
    protected $fillable = [
        'image',
        'title',
        'description',
        'status'
    ];
}
