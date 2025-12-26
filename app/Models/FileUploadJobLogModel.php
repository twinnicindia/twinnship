<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FileUploadJobLogModel extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='file_upload_job_log',$timestamps=true;
    protected $fillable = [
        'job_id',
        'awb_number',
        'weight',
        'length',
        'breadth',
        'height',
        'cod_transactions_id',
        'crf_id',
        'cod_amount',
        'remittance_amount',
        'utr_number',
        'status',
        'remark',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
