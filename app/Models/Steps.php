<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Steps extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='steps',$timestamps=false;
    protected $fillable = [
        'image',
        'title',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
