<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class OrderWhatsAppMessageLogs extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='order_whatsapp_message_logs',$timestamps=false;
    protected $fillable = [
        'order_id',
        'seller_id',
        'awb_number',
        'order_status',
        'sent',
        'sent_datetime',
    ];

    public static function CheckAndStoreWhatsAppMessage($order)
    {
        $resp = DB::table('order_whatsapp_message_logs')->where('order_id',$order->id)->where('order_status',$order->status)->first();
        if(empty($resp)){
            // insert record here
            $data = [
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'awb_number' => $order->awb_number,
                'order_status' => $order->status,
                'sent' => 1,
                'sent_datetime' => date('Y-m-d H:i:s')
            ];
            DB::table('order_whatsapp_message_logs')->insert($data);
            return true;
        }
        else{
            return false;
        }
    }
}
