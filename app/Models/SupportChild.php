<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupportChild extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_support_child',$timestamps=false;
    protected $fillable = [
        'title',
        'firstname',
        'lastname',
        'support_id',
        'supportsub_id',
        'description',
        'date',
        'status'
    ];
}
