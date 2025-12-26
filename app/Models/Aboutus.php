<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Aboutus extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_about_us',$timestamps=false;
    protected $fillable = [
        'image',
        'name',
        'post',
        'link',
        'status'
    ];
}
