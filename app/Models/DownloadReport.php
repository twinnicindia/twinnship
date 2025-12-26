<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DownloadReport extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'download_report', $timestamps = true;
    protected $fillable = [
        'seller_id',
        'report_name',
        'report_type',
        'report_status',
        'report_download_url',
        'extra_urls',
        'payload',
        'finished_at',
        'remark',
        'bucket_url'
    ];
}
