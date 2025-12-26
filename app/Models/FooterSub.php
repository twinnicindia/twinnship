<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FooterSub extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_footer_sub',$timestamps=false;
    protected $fillable = [
        'title',
        'footer_id',
        'link',
        'status'
    ];
}
