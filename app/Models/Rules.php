<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Rules extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='rules',$timestamps=false;
    protected $fillable = [
        'preferences_id',
        'criteria',
        'match_type',
        'match_value',
    ];
}
