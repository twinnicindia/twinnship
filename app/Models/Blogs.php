<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Blogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_blog',$timestamps=false;
    protected $fillable = [
        'image',
        'title',
        'description',
        'long_description',
        'by_name',
        'from_url',
        'date',
        'status',
    ];
}
