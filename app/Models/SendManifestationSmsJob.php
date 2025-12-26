<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SendManifestationSmsJob extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='send_manifestation_sms_job',$timestamps=false;
    protected $fillable = [
        'order_count',
        'order_ids',
        'start_time',
        'status',
        'end_time',
        'seller_id',
        'inserted'
    ];
}
