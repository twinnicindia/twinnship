<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BulkNDRActionFile extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='bulk_ndr_action_file',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'created',
        'file_url'
    ];
}
