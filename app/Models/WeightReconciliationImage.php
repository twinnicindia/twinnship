<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class WeightReconciliationImage extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='weight_reconciliation_images',$timestamps=false;
    protected $fillable = [
        'weight_reconciliation_history_id',
        'image',
    ];
}
