<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Stats extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='stats',$timestamps=false;
    protected $fillable = [
        'title',
        'number',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
