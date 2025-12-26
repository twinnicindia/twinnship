<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Master extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='master',$timestamps=false;
    protected $fillable = [
        'title',
        'text',
        'link',
        'icon',
        'position',
        'parent_id',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
