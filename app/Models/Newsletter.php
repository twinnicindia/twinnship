<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Newsletter extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'newsletter', $timestamps = false;
    protected $fillable = [
        'email'
    ];
}
