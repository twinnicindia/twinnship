<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Testimonial extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='testimonial',$timestamps=false;
    protected $fillable = [
        'name',
        'designation',
        'position',
        'image',
        'description',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status'
    ];
}
