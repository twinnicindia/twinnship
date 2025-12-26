<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupportSub extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='support_sub',$timestamps=false;
    protected $fillable = [
        'title',
        'support_id',
        'status'
    ];
}
