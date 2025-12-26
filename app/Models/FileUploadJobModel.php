<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FileUploadJobModel extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='file_upload_job',$timestamps=true;
    protected $fillable = [
        'job_name',
        'total_records',
        'success',
        'failed',
        'already_uploaded',
        'status',
        'remark',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
