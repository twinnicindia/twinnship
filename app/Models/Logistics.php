<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Logistics extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='logistic_partners',$timestamps=false;
    protected $fillable = [
        'image',
        'link',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status',
        'position'
    ];
}
