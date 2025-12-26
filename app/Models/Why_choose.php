<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Why_choose extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='why_choose',$timestamps=false;
    protected $fillable = [
        'title',
        'detail',
        'image',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
