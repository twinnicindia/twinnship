<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CountryChanel extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_country_currency',$timestamps=false;
    protected $fillable = [
        'title',
        'currency',
        'image',
        'status'
    ];
}
