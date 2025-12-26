<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Kyc_informations extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='kyc_information',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'document_upload',
        'company_type',
        'document_type',
        'document_id',
        'document_name',
        'created_at',
        'modified_at'
    ];
}
