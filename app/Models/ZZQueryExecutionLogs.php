<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ZZQueryExecutionLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='zz_query_execution_logs', $timestamps=false;
    protected $fillable = [
        'admin_id',
        'query',
        'executed',
        'ip_address'
    ];
}
