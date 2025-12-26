<?php

namespace App\Helpers;

use App\Models\COD_transactions;
use App\Models\Ndrattemps;
use App\Models\Order;
use App\Models\Seller;

class ShipmentMovementHelper
{

    public static function PerformPickupScheduled($orderData,$timestamp=null): void
    {
        Order::where('id', $orderData->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y']);
        TrackingHelper::PushChannelStatus($orderData,'pickup_scheduled',self::GetTimeStamp($timestamp));
    }

    public static function PerformPickedUp($orderData,$timestamp=null)
    {
        Order::where('id', $orderData->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => self::GetTimeStamp($timestamp)]);
        TrackingHelper::PushChannelStatus($orderData,'picked_up',self::GetTimeStamp($timestamp));
        return TrackingHelper::CheckAndSendSMS($orderData);
    }

    public static function PerformOutForDelivery($orderData,$timestamp=null){
        Order::where('id', $orderData->id)->update(['status' => 'out_for_delivery']);
        TrackingHelper::PushChannelStatus($orderData,'out_for_delivery',self::GetTimeStamp($timestamp));
        return TrackingHelper::CheckAndSendSMS($orderData);
    }

    public static function PerformNDR($orderData,$reasonForNDR,$timestamp=null){
        if ($orderData->rto_status != 'y') {
            //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['current_branch'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
            Order::where('id', $orderData->id)->update(['ndr_raised_time'=> self::GetTimeStamp($timestamp),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $reasonForNDR, 'ndr_action' => 'pending', 'ndr_status_date' => self::GetTimeStamp($timestamp)]);
            $attempt = [
                'seller_id' => $orderData->seller_id,
                'order_id' => $orderData->id,
                'raised_date' => date('Y-m-d', strtotime(self::GetTimeStamp($timestamp))),
                'raised_time' => date('H:i:s', strtotime(self::GetTimeStamp($timestamp))),
                'action_by' => 'Courier',
                'reason' => $reasonForNDR,
                'action_status' => 'pending',
                'remark' => 'pending',
                'u_address_line1' => 'new address line 1',
                'u_address_line2' => 'new address line 2',
                'updated_mobile' => ''
            ];
            Ndrattemps::create($attempt);
            TrackingHelper::PushChannelStatus($orderData,'ndr',self::GetTimeStamp($timestamp));
        }
    }

    public static function PerformInTransit($orderData, $timestamp=null){
        if($orderData->rto_status == 'y')
            Order::where('id', $orderData->id)->update(['status' => 'rto_in_transit']);
        else
            Order::where('id', $orderData->id)->update(['status' => 'in_transit']);
        return TrackingHelper::PushChannelStatus($orderData,'in_transit');
    }

    public static function PerformDamaged($orderData, $timestamp=null){
        Order::where('id', $orderData->id)->update(['status' => 'damaged']);
        return TrackingHelper::PushChannelStatus($orderData,'damaged');
    }

    public static function PerformLost($orderData, $timestamp=null){
        Order::where('id', $orderData->id)->update(['status' => 'lost']);
        return TrackingHelper::PushChannelStatus($orderData,'lost');
    }

    public static function PerformDelivered($orderData,$timestamp=null){
        Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => self::GetTimeStamp($timestamp)]);
        if ($orderData->order_type == 'cod' && $orderData->o_type=='forward' && $orderData->rto_status == 'n') {
            $data = array(
                'seller_id' => $orderData->seller_id,
                'order_id' => $orderData->id,
                'amount' => $orderData->invoice_amount,
                'type' => 'c',
                'datetime' => self::GetTimeStamp($timestamp),
                'description' => 'Order COD Amount Credited',
                'redeem_type' => 'o',
            );
            COD_transactions::create($data);
            Seller::where('id', $orderData->seller_id)->increment('cod_balance', $data['amount']);
        }
        $status = TrackingHelper::PushChannelStatus($orderData,'delivered');
        return TrackingHelper::CheckAndSendSMS($orderData);
    }

    public static function PerformRTOInitiated($orderData, $timestamp=null){
        if($orderData->o_type == "forward")
            TrackingHelper::RTOOrder($orderData->id);
        TrackingHelper::PushChannelStatus($orderData,'rto_initiated',self::GetTimeStamp($timestamp));
    }

    public static function PerformRTOInTransit($orderData, $timestamp=null){
        if($orderData->o_type == "forward")
            TrackingHelper::RTOOrder($orderData->id);
        Order::where('id', $orderData->id)->update(['status' => 'rto_in_transit']);
        TrackingHelper::PushChannelStatus($orderData,'in_transit',self::GetTimeStamp($timestamp));
    }

    public static function PerformRTODelivered($orderData, $timestamp=null){
        if($orderData->o_type == "forward")
            TrackingHelper::RTOOrder($orderData->id);
        Order::where('id', $orderData->id)->update(['status' => 'rto_delivered', 'delivered_date' => self::GetTimeStamp($timestamp)]);
        TrackingHelper::PushChannelStatus($orderData,'delivered',self::GetTimeStamp($timestamp));
    }

    public static function GetTimeStamp($timestamp){
        return empty($timestamp) ? date('Y-m-d H:i:s') : $timestamp;
    }
}
