<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Account_informations extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='account_informations',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'account_holder_name',
        'account_number',
        'bank_name',
        'ifsc_code',
        'bank_branch',
        'cheque_image',
        'created_at',
        'modified_at'
    ];
}
