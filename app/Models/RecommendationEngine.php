<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class RecommendationEngine extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='recommendation_engine',$timestamps=true;
    protected $fillable = [
        'title',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];
}
