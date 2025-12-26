<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DownloadOrderReportModel extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'download_order_report', $timestamps = false;
    protected $fillable = [
        'report_name',
        'status',
        'report_download_url',
        'payload',
        'finished_at',
        'created_at',
        'remark'
    ];
}
