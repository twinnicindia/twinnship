<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Glossary extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_glossary',$timestamps=false;
    protected $fillable = [
        'title',
        'description',
        'wpr_description',
        'lease_description',
        'guide_description',
        'storage_description',
        'privacypolicy',
        'termcondiction',
        'name',
        'image',
        'image1',
        'status',
        'date',
    ];
}
