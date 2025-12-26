<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class RecordDndForNdr extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'record_dnd_for_ndr', $timestamps = false;
    protected $fillable = [
        'seller_id',
        'order_id',
        'inserted'
    ];
}
