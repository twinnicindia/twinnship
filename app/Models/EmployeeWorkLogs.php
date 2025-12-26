<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EmployeeWorkLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='employee_work_logs',$timestamps=false;
    protected $fillable = [
        'order_id',
        'employee_id',
        'operation',
        'inserted'
    ];
}
