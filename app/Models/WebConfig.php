<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WebConfig extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='web_footer_config',$timestamps=false;
    protected $fillable = [
        'address1',
        'address2',
        'mobile1',
        'mobile2',
        'email',
        'whatsapp_image',
        'whatsapp_number',
        'footer_image',
    ];
}
