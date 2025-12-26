<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Preferences extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='preferences',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'rule_name',
        'priority',
        'match_type',
        'priority1',
        'priority2',
        'priority3',
        'priority4',
    ];
}
