<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ManifestationIssues extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='manifestation_issues',$timestamps=false;
    protected $fillable = [
        'order_id',
        'message',
        'created'
    ];
}
