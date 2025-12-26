<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agreement_informations extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='agreement_informations',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'document_upload',
        'created_at',
        'modified_at'
    ];
}
