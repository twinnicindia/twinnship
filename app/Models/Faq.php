<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Faq extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_faq',$timestamps=false;
    protected $fillable = [
        'question',
        'answer',
        'status',
    ];
}
