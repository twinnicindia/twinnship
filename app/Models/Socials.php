<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Socials extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='social_links',$timestamps=false;
    protected $fillable = [
        'icon',
        'link',
        'image',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
