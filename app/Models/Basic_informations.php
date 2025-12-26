<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Basic_informations extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='basic_informations',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'company_name',
        'website_url',
        'company_logo',
        'email',
        'mobile',
        'gst_certificate',
        'pan_number',
        'gst_number',
        'street',
        'city',
        'state',
        'pincode',
        'created_at',
        'modified_at'
    ];
}
