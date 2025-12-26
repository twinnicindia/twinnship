<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CronLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='cron_logs',$timestamps=false;
    protected $fillable = [
        'cron_name',
        'status',
        'remark',
        'success',
        'errors',
        'row_inserted',
        'row_updated',
        'row_deleted',
        'started_at',
        'finished_at',
        'date'
    ];
}
