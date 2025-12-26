<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Plans extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='plans',$timestamps=false;
    protected $fillable = [
        'title',
        'description',
        'status',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by'
    ];
}
