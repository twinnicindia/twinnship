<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class OrderSMSLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='order_sms_logs',$timestamps=false;
    protected $fillable = [
        'order_id',
        'awb_number',
        'order_status',
        'sent',
        'seller_id',
        'sent_datetime'
    ];
    public static function CheckAndStoreSMS($order)
    {
        $resp = DB::table('order_sms_logs')->where('order_id',$order->id)->where('order_status',$order->status)->first();
        if(empty($resp)){
            // insert record here
            $data = [
                'order_id' => $order->id,
                'awb_number' => $order->awb_number,
                'order_status' => $order->status,
                'seller_id' => $order->seller_id,
                'sent' => 'y',
                'sent_datetime' => date('Y-m-d H:i:s')
            ];
            DB::table('order_sms_logs')->insert($data);
            return true;
        }
        else{
            return false;
        }
    }
}
