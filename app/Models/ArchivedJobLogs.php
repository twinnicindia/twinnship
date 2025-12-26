<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ArchivedJobLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='archived_job_logs',$timestamps=false;
    protected $fillable = [
        'table_name',
        'deleted_before',
        'executed',
        'no_of_records',
        'executed_by',
        'ip_address'
    ];
}
