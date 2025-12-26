<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FooterCategory extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_footercategory',$timestamps=false;
    protected $fillable = [
        'title',
        'status'
    ];
}
