<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EarlyCod extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='early_cod',$timestamps=false;
    protected $fillable = [
        'title',
        'rate',
        'number_of_days',
        'icon',
        'status'
    ];
}
