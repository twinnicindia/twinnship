<?php

namespace App\Helpers;

use App\Helper\Channels\ShopifyHelper;
use App\Helper\Channels\WooCommerceHelper;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\EcomExpress3kgController;
use App\Http\Controllers\EcomExpressController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\Utilities;
use App\Libraries\AmazonSWA;
use App\Libraries\BlueDart;
use App\Libraries\BluedartRest;
use App\Libraries\Bombax;
use App\Libraries\Custom\CustomDelhivery;
use App\Libraries\Custom\CustomDtdc;
use App\Libraries\CustomBlueDart;
use App\Libraries\CustomBluedartRest;
use App\Libraries\Delhivery;
use App\Libraries\Dtdc;
use App\Libraries\Ekart;
use App\Libraries\EkartSmall;
use App\Libraries\Gati;
use App\Libraries\Logger;
use App\Libraries\Maruti;
use App\Libraries\MarutiEcom;
use App\Libraries\Movin;
use App\Libraries\MyUtility;
use App\Libraries\PickNDel;
use App\Libraries\Prefexo;
use App\Libraries\Professional;
use App\Libraries\Shadowfax;
use App\Libraries\Smartr;
use App\Libraries\SMCNew;
use App\Libraries\XpressBees;
use App\Models\ChannelOrderStatusList;
use App\Models\Channels;
use App\Models\COD_transactions;
use App\Models\CourierMissStatusCode;
use App\Models\InternationalOrders;
use App\Models\MoveToIntransit;
use App\Models\Ndrattemps;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Seller;
use App\Models\Transactions;
use App\Models\ZZArchiveOrder;
use Automattic\WooCommerce\Client;
use DateTime;
use Illuminate\Support\Facades\Http;
use Exception;
use function Nette\Utils\in;

class TrackingHelper
{
    const EasyEcomStatus = [
            "pending" => 18,
            "shipped" => 18,
            "manifested" => 19,
            "pickup_scheduled" => 19,
            "picked_up" => 2,
            "cancelled" => 6,
            "in_transit" => 2,
            "out_for_delivery" => 20,
            "rto_initated" => 17,
            "rto_initiated" => 17,
            "rto_delivered" => 9,
            "delivered" => 3,
            "ndr" => 16,
            "lost" => 2,
            "damaged" => 2
        ];

    const PartnerNames = [
        'amazon_swa' => 'AmazonSwa',
        'amazon_swa_10kg' => 'AmazonSwa',
        'amazon_swa_1kg' => 'AmazonSwa',
        'amazon_swa_3kg' => 'AmazonSwa',
        'amazon_swa_5kg' => 'AmazonSwa',
        'bluedart' => 'Bluedart',
        'bluedart_surface' => 'Bluedart',
        'shadow_fax' => 'Shadowfax',
        'delhivery_surface' => 'Delhivery',
        'delhivery_surface_1kg' => 'Delhivery',
        'delhivery_surface_10kg' => 'Delhivery',
        'delhivery_surface_20kg' => 'Delhivery',
        'delhivery_b2b_20kg' => 'Delhivery',
        'delhivery_surface_2kg' => 'Delhivery',
        'delhivery_surface_5kg' => 'Delhivery',
        'delhivery_air' => 'Delhivery',
        'delhivery_lite' => 'Delhivery',
        'dtdc_surface' => 'DTDC',
        'dtdc_10kg' => 'DTDC',
        'dtdc_2kg' => 'DTDC',
        'dtdc_3kg' => 'DTDC',
        'dtdc_5kg' => 'DTDC',
        'dtdc_6kg' => 'DTDC',
        'dtdc_1kg' => 'DTDC',
        'dtdc_express' => 'DTDC',
        'ecom_express' => 'EcomExpress',
        'ecom_express_rvp' => 'EcomExpress',
        'ecom_express_3kg' => 'EcomExpress',
        'ecom_express_3kg_rvp' => 'EcomExpress',
        'fedex' => 'FedEx',
        'wow_express' => 'WowExpress',
        'udaan' => 'Udaan',
        'udaan_1kg' => 'Udaan',
        'udaan_2kg' => 'Udaan',
        'udaan_3kg' => 'Udaan',
        'udaan_10kg' => 'Udaan',
        'xpressbees_surface' => 'XpressBees',
        'xpressbees_surface_1kg' => 'XpressBees',
        'xpressbees_surface_3kg' => 'XpressBees',
        'xpressbees_surface_5kg' => 'XpressBees',
        'xpressbees_surface_10kg' => 'XpressBees',
        'xpressbees_sfc'  => 'XpressBees',
        'ekart' => 'Ekart Logistics',
        'ekart_250gm' => 'Ekart Logistics',
        'ekart_1kg' => 'Ekart Logistics',
        'ekart_2kg' => 'Ekart Logistics',
        'ekart_3kg' => 'Ekart Logistics',
        'ekart_5kg' => 'Ekart Logistics',
        'smartr' => 'Smartrlogistics',
        'shree_maruti' => 'Shree Maruti Courier',
        'shree_maruti_ecom' => 'Shree Maruti Courier',
        'smc_new' => 'Shree Maruti Courier',
        'shree_maruti_ecom_1kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_3kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_5kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_10kg' => 'Shree Maruti Courier',
        'bombax' => 'Bombax',
        'tpc_surface' => 'The Professional Courier',
        'tpc_1kg' => 'The Professional Courier 1KG',
        'pick_del' => 'Pick & Del',
    ];

    public static function PerformTracking($order)
    {
        try{
            $isSucceeded = false;
            if($order->status != 'delivered' && $order->status != "pending" && $order->status != "cancelled" && $order->status != "shipped" && $order->status != "pickup_requested" && $order->manifest_sent == 'y')
            {
                switch ($order->courier_partner) {
                    case 'delhivery_surface':
                    case 'delhivery_surface_2kg':
                        $isSucceeded = self::TrackOrderDelhiverySurface($order, "surface");
                        break;
                    case 'delhivery_air':
                        $isSucceeded = self::TrackOrderDelhiverySurface($order, "air");
                        break;
                    case 'delhivery_surface_5kg':
                        $isSucceeded = self::TrackOrderDelhiverySurface($order, "five");
                        break;
                    case 'delhivery_surface_10kg':
                        $isSucceeded = self::TrackOrderDelhiverySurface($order, "ten");
                        break;
                    case 'shadowfax':
                        $isSucceeded = self::TrackOrderShadowFax($order);
                        break;
                    case 'smc_new':
                    case 'smc_2kg':
                    case 'smc_5kg':
                    case 'smc_air':
                    case 'smc_air_2kg':
                        $isSucceeded = self::TrackSMCNewOrder($order);
                        break;
                    case 'bluedart':
                    case 'bluedart_10kg':
                    case 'bluedart_surface':
                    case 'bluedart_10kg_surface':
                        $isSucceeded = self::TrackOrderBlueDart($order);
                        break;
                    case 'xpressbees_surface_1kg':
                    case 'xpressbees_sfc':
                    case 'xpressbees_surface_3kg':
                    case 'xpressbees_5kg':
                    case 'xpressbees_2kg':
                    case 'xpressbees_10kg':
                    case 'xpressbees_20kg':
                        $obj = new XpressBees('three');
                        $isSucceeded = self::TrackOrderExpressBeesNew($order, $obj);
                        break;
                }
                $updateArray = ['last_sync' => date('Y-m-d H:i:s')];
                if($isSucceeded){
                    $updateArray['last_executed'] = date('Y-m-d H:i:s');
                }
                Order::where('id',$order->id)->update($updateArray);
            }
            return $isSucceeded;
        }
        catch(Exception $e){
            Logger::write("logs/tracking/tracking-exception-".date('Y-m-d').".text",['data' =>  $e->getMessage()."-".$e->getFile()."-".$e->getLine()]);
            return false;
        }
    }

    public static function TrackPickDelOrder($orderData){
        $returnValue = false;
        $professionalClient = new PickNDel();
        $trackingData = $professionalClient->TrackingOrder($orderData->awb_number);
        if(empty($trackingData))
        {
            return false;
        }
        $trackingDetail = $trackingData[count($trackingData)];
        if(empty($trackingDetail['short_code']))
            return false;
        $order_tracking = OrderTracking::where('awb_number', $orderData->awb_number)->orderBy('id', 'desc')->first();
        if(!empty($order_tracking)){
            if($order_tracking->status_code != $trackingDetail['short_code']){
                // Handle
                self::_HandlePickDelTracking($trackingDetail,$orderData);
                $returnValue = true;
            }
        }
        else{
            // Handle
            self::_HandlePickDelTracking($trackingDetail,$orderData);
            $returnValue = true;
        }
        return $returnValue;
    }

    public static function _HandlePickDelTracking($trackingData,$order){
        $edd = $trackingData['expected_delivery_date'] ?? null;
        if(!empty($edd)) {
            try{
                Order::where('id', $order->id)->update(['expected_delivery_date' => date('Y-m-d', strtotime($edd))]);
            }catch (Exception $e){}
        }
        $datetime = date('Y-m-d H:i:s');
        switch($trackingData['short_code']){
            case 'NEW':
            case 'RAP':
            case 'ARP':
            case 'OFP':
            case 'ARV':
            case 'CANT':
            case 'CER':
            case 'CIDR':
            case 'CIWA':
            case 'CLOC':
            case 'CNSP':
            case 'CNSA':
            case 'CPNM':
            case 'CSHI':
            case 'CPOS':
            case 'CPDOC':
            case 'CSNPP':
            case 'CCNAP':
            case 'CPA3D':
            case 'CCRTH':
            case 'CCROC':
            case 'CTAFC':
            case 'CLSV':
            case 'CCTZ':
            case 'CQCF':
            case 'CCLD':
            case 'PNR':
                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                self::PushChannelStatus($order,'pickup_scheduled',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'ITR':
            case 'PCK':
            case 'DTH':
            case 'RCH':
            case 'RAH':
            case 'RAD':
            case 'ARD':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$datetime);
                break;
            case 'ANT':
            case 'CLJ':
            case 'CNA':
            case 'ER':
            case 'IDR':
            case 'IWA':
            case 'LOC':
            case 'NSP':
            case 'NSA':
            case 'PNM':
            case 'RTA':
            case 'SHI':
            case 'POS':
            case 'CROC':
            case 'TAFC':
            case 'LSV':
            case 'CTZ':
            case 'CNR':
            case 'CAN':
            case 'CBD':
            case 'CDD':
            case 'PEN':
            case 'OSA':
            case 'PANT':
            case 'PCNA':
            case 'PH':
            case 'PL':
            case 'PNA':
            case 'PM':
            case 'CNC':
            case 'R3D':
            case 'PAWC':
            case 'LFV':
            case 'PFL':
            case 'RSC':
            case 'PCNR':
            case 'PPNM':
            case 'PHL':
            case 'PCOD':
                // NDR
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $datetime != $order->ndr_status_date) {
                        //make attempt here
                        $attempt = [
                            'seller_id' => $order->seller_id,
                            'order_id' => $order->id,
                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'PickNDel',
                            'reason' => $order->reason_for_ndr,
                            'action_status' => 'requested',
                            'remark' => 'requested',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $datetime]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$datetime);
                self::CheckAndSendSMS($order);
            case 'DLD':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
                if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $datetime ?? date('Y-m-d'),
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($order,'delivered',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'RTO':
            case 'CBH':
            case 'CRD':
                self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$datetime);
                break;
            case 'CFD':
                self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'RTO':
                self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'delivered']);
                self::PushChannelStatus($order,'delivered',$datetime);
                self::CheckAndSendSMS($order);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $trackingData['activity']."-".$trackingData['activity'],
                    'status_description' => $trackingData['activity'],
                    'json' => json_encode($trackingData),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $trackingData['short_code'],
            "status" => $trackingData['activity'],
            "status_description" => $trackingData['activity'],
            "remarks" =>  $trackingData['activity'],
            "location" =>  $trackingData['google_location'],
            "updated_date" => $datetime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    public static function TrackProfessionalOrder($orderData){
        $returnValue = false;
        $professionalClient = new Professional();
        $trackingData = $professionalClient->TrackingOrder($orderData->awb_number);
        if(empty($trackingData))
            return false;
        $trackingDetail = $trackingData[count($trackingData)-1];
        if(empty($trackingDetail['Type']))
            return false;
        $order_tracking = OrderTracking::where('awb_number', $orderData->awb_number)->orderBy('id', 'desc')->first();
        if(!empty($order_tracking)){
            if($order_tracking->status_code != $trackingDetail['Type'] || $order_tracking->status != $trackingDetail['Remarks']){
                // Handle
                self::_HandleProfessionalTracking($trackingDetail,$orderData);
                $returnValue = true;
            }
        }
        else{
            // Handle
            self::_HandleProfessionalTracking($trackingDetail,$orderData);
            $returnValue = true;
        }
        return $returnValue;
    }

    // Track Order For Prefexo Order
    public static function TrackPrefexoOrder($order)
    {
        $prefexo = new Prefexo();
        $awb_data = $prefexo->trackOrder($order->awb_number);
        $tracking_data = $awb_data['data'];

        if (isset($tracking_data['history'])) {
            $shipment_summary = $tracking_data['history'][count($tracking_data['history']) - 1];
            $order_tracking = OrderTracking::where('awb_number', $tracking_data['awb_number'])->orderBy('id', 'desc')->first();
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['status_code']) {
                    switch ($shipment_summary['status_code']) {
                        case 'PUC':
                            Order::where('id', $order->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                            self::PushChannelStatus($order, 'pickup_scheduled');
                            break;
                        case 'PUD':
                        case 'PKD':
                            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $shipment_summary['event_time']]);
                            self::PushChannelStatus($order, 'picked_up');
                            self::CheckAndSendSMS($order);
                            break;
                        case 'OFD':
                            if ($order->rto_status != 'y') {
                                if ($order->ndr_status == 'y' && $shipment_summary['event_time'] != $order->ndr_status_date) {
                                    //make attempt here
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'XpressBees',
                                        'reason' => $order->reason_for_ndr,
                                        'action_status' => 'requested',
                                        'remark' => 'requested',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    Order::where('id', $order->id)->update(['ndr_status_date' => $shipment_summary['event_time']]);
                                }
                            }
                            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                            self::PushChannelStatus($order, 'out_for_delivery');
                            self::CheckAndSendSMS($order);
                            break;
                        case 'IT':
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order, 'in_transit');
                            break;
                        case 'DLVD':
                        case 'DL':
                            $delivery_date = date('Y-m-d', strtotime($shipment_summary['event_time']));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            if ($order->order_type == 'cod') {
                                $data = array(
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'amount' => $order->invoice_amount,
                                    'type' => 'c',
                                    'datetime' => $delivery_date,
                                    'description' => 'Order COD Amount Credited',
                                    'redeem_type' => 'o',
                                );
                                COD_transactions::create($data);
                                Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                            }
                            self::PushChannelStatus($order, 'delivered');
                            self::CheckAndSendSMS($order);
                            break;
                        case 'RTD':
                        case 'RT-DL':
                            if ($order->o_type == "forward")
                                self::RTOOrder($order->id);
                            Order::where('id', $order->id)->update(['status' => 'delivered']);
                            $delivery_date = date('Y-m-d', strtotime($shipment_summary['event_time']));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            self::PushChannelStatus($order, 'delivered');
                            break;
                        case 'RTO-IT':
                            if ($order->o_type == "forward")
                                self::RTOOrder($order->id);
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order, 'in_transit');
                            break;
                        case 'UD':
                            if ($order->rto_status != 'y') {
                                //Order::where('id', $order->id)->update(['ndr_raised_time' => date('Y-m-d H:i:s'), 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $tracking_data['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['event_time']]);
                                $ndrRaisedDate =  date('Y-m-d H:i:s');
                                Order::where('id', $order->id)->update(['ndr_raised_time' =>  $ndrRaisedDate, 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $tracking_data['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['event_time']]);
                                $attempt = [
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                    'raised_time' => date('H:i:s',strtotime($ndrRaisedDate)),
                                    'action_by' => 'XpressBees',
                                    'reason' => $tracking_data['status'],
                                    'action_status' => 'pending',
                                    'remark' => 'pending',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);
                                self::PushChannelStatus($order, 'ndr');
                            }
                            break;
                        case 'LOST':
                            Order::where('id', $order->id)->update(['status' => 'lost']);
                            self::PushChannelStatus($order, 'lost');
                            break;
                        case 'RTON':
                            self::RTOOrder($order->id);
                            Order::where('id', $order->id)->update(['status' => 'rto_initiated', 'rto_status' => 'y']);
                            self::PushChannelStatus($order, 'rto_initiated');
                            break;
                        case 'STD':
                            Order::where('id', $order->id)->update(['status' => 'damaged']);
                            self::PushChannelStatus($order, 'damaged');
                            break;
                    }
                    $data = [
                        "awb_number" => $tracking_data['awb_number'],
                        "status_code" => $shipment_summary['status_code'],
                        "status" => $tracking_data['status'] == 'rto' ? $tracking_data['rto_status'] : $tracking_data['status'],
                        "status_description" => $shipment_summary['message'],
                        "remarks" => $shipment_summary['message'],
                        "location" => $shipment_summary['location'],
                        "updated_date" => $shipment_summary['event_time'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    return true;
                }
            } else {
                $data = [
                    "awb_number" => $tracking_data['awb_number'],
                    "status_code" => $shipment_summary['status_code'],
                    "status" => $tracking_data['status'] == 'rto' ? $tracking_data['rto_status'] : $tracking_data['status'],
                    "status_description" => $shipment_summary['message'],
                    "remarks" => $shipment_summary['message'],
                    "location" => $shipment_summary['location'],
                    "updated_date" => $shipment_summary['event_time'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                return true;
            }
        }
        return false;
    }

    public static function TrackOrderExpressBeesNew($order,XpressBees $client)
    {
        try{
            $returnValue = false;
            $awb_data = $client->GetTracking($order->awb_number);
            $tracking_data = $awb_data['tracking_data'];
            $trackingData = array_merge(...array_values($tracking_data));
            if (!empty($trackingData)) {
                $shipment_summary = $trackingData[0];
                $order_tracking = OrderTracking::where('awb_number', $shipment_summary['awb_number'])->orderBy('id', 'desc')->first();
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $shipment_summary['status_code']) {
                        self::HandleXpressBeesTracking($order,$shipment_summary,$trackingData);
                    }
                    $returnValue = true;
                } else {
                    self::HandleXpressBeesTracking($order,$shipment_summary,$trackingData);
                    $returnValue = true;
                }
            }
            return $returnValue;
        }catch (Exception $e){
            dd($e->getMessage(), $e->getLine(), $e->getFile());
            return false;
        }
    }

    // Handel Xpressbees Method Helper
    public static function HandleXpressBeesTracking($order,$shipment_summary,$tracking_data){
        $expected_date = $order->expected_delivery_date;
        if (!empty($shipment_summary['ExpectedDeliveryDate'])) {
            $expected_date = date('Y-m-d', strtotime($shipment_summary['ExpectedDeliveryDate']));
        }
        $latestUpdatedDatetime = date('Y-m-d H:i:s',strtotime($shipment_summary['event_time']));
        if(!MyUtility::isValidDateTime($latestUpdatedDatetime))
            $latestUpdatedDatetime = date('Y-m-d H:i:s');
        switch ($shipment_summary['status_code']) {
            case 'PUC':
            case 'OFP':
            case 'PND':
            case 'PP':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                break;
            case 'PUD':
            case 'PKD':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $latestUpdatedDatetime]);
                break;
            case 'OFD':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $shipment_summary['event_time'] != $order->ndr_status_date) {
                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $order->seller_id,
//                            'order_id' => $order->id,
//                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                            'raised_time' => date('H:i:s'),
//                            'action_by' => 'XpressBees',
//                            'reason' => $order->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $latestUpdatedDatetime]);
                    }
                }
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'out_for_delivery']);
                break;
            case 'IT':
            case 'RAD':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'in_transit']);
                break;
            case 'DL':
            case 'DLVD':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $latestUpdatedDatetime]);
                if ($order->order_type == 'cod') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $latestUpdatedDatetime,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                break;
            case 'RT-DL':
                if($order->o_type == "forward")
                    self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $latestUpdatedDatetime]);
                break;
            case 'RT':
            case 'RT-IT':
            case 'RTU':
            case 'RT-LT':
            case 'RT-DG':
                if($order->o_type == "forward")
                    self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'in_transit']);
                break;
            case 'RTO-OFD':
                if($order->o_type == "forward")
                    self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'out_for_delivery']);
                break;
            case 'UD':
                if ($order->rto_status != 'y') {
                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'expected_delivery_date' => $expected_date,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['StatusDate']]);
                    $ndrRaisedDate = date('Y-m-d H:i:s');
                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'expected_delivery_date' => $expected_date,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $latestUpdatedDatetime]);
                    $attempt = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                        'raised_time' => date('H:i:s'),
                        'action_by' => 'XpressBees',
                        'reason' => $shipment_summary['status'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                }
                break;
            case 'LT':
                Order::where('id', $order->id)->update(['status' => 'lost']);
                break;
            case 'RTON':
            case 'RTO':
                self::RTOOrder($order->id);
                break;
            case 'DG':
                Order::where('id', $order->id)->update(['status' => 'damaged']);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $shipment_summary['status_code'],
                    'status_description' => $shipment_summary['message'],
                    'json' => json_encode($tracking_data),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $shipment_summary['awb_number'],
            "status_code" => $shipment_summary['status_code'],
            "status" => $shipment_summary['status'],
            "status_description" => $shipment_summary['message'],
            "remarks" =>  $shipment_summary['message'],
            "location" =>  $shipment_summary['location'],
            "updated_date" => $latestUpdatedDatetime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    // Check and Send SMS for Order
    public static function CheckAndSendSMS($order){
//        if($order->seller_id == 5742 && $order->status == 'out_for_delivery')
//            self::CheckAndSendEmail($order);
//        try{
//            $utility = new Utilities();
//            $seller = Seller::select('sms_service','whatsapp_service')->where('id', $order->seller_id)->first();
//            if (!empty($seller)) {
//                if ($seller->sms_service == 'y') {
//                    $utility->send_sms($order);
//                }
//
//                if ($seller->whatsapp_service == 1)
//                    $utility->send_whatsapp_message($order);
//            }
//        }
//        catch(Exception $e){
//            return false;
//        }
        return true;
    }

    public static function CheckAndSendEmail($order){
//        try{
//            $utility = new Utilities();
//            $trackurl = url('order-tracking')."/$order->awb_number";
//            $sellerData = Seller::find($order->seller_id);
//            $sellerName = $sellerData->last_name." ".$sellerData->first_name;
//            $couriePartner = self::PartnerNames[$order->courier_partner] ?? $order->courier_partner;
//            $subject = "$sellerName : Your $sellerData->company_name Order is Out for Delivery";
//            $message = "<b>Hi $sellerName,</b><br><b>Your Order {$order->customer_order_number} having AWB # {$order->awb_number} is OUT FOR DELIVERY </b> <br> <b> Details Are : </b><br><b>Courier : {$couriePartner}</b><br><b>Product : {$order->product_name}</b><br><b>Booking Date : {$order->awb_assigned_date}</b><br><b>Contact Number : {$order->s_contact}</b><br><b>Tracking Url : {$trackurl}</b><br><br>Best Regards<br>Twinnship";
//            $data = [
//                'seller_id' => $order->seller_id,
//                'order_id' => $order->id,
//                'awb_number' => $order->awb_number,
//                'status' => $order->status,
//                'subject' => $subject,
//                'message' => $message
//            ];
//            Logger::write('logs/email-'.date('Y-m-d').'.text', [
//                'title' => "Email Request",
//                'data' => $data
//            ]);
//            $response = $utility->send_email($sellerData->email,"Twinnship Corporation",'',$message,$subject);
//            Logger::write('logs/email-'.date('Y-m-d').'.text', [
//                'title' => "Email Response",
//                'data' => $response
//            ]);
//        }catch (Exception $e){
//            return false;
//        }
        return true;
    }

    public static function storeDatetimeInformation($order,$status,$date){
        try {
            $latestDetails = Order::find($order->id);
            $checkOfdExist = InternationalOrders::where('order_id',$order->id)->first();
            if (empty($checkOfdExist)) {
                $ofdDate = [
                    'order_id' => $order->id,
                ];
                InternationalOrders::create($ofdDate);
            }
            switch ($status) {
                case "picked_up":
                    if ($latestDetails->rto_status == 'n') {
                        Order::where('id', $order->id)->whereNull('pickup_time')->update(['pickup_time' => $date]);
                    }
                    break;
                case "in_transit":
                    if ($latestDetails->rto_status == 'y') {
                        InternationalOrders::where('order_id', $order->id)->whereNull('rto_initiated_date')->update(['rto_initiated_date' => $date]);
                    }
                    else {
                        Order::where('id',$order->id)->whereNull('pickup_time')->update(['pickup_time' => $date]);
                        $checkIntransit = MoveToIntransit::where('order_id', $order->id)->first();
                        if (empty($checkIntransit)) {
                            MoveToIntransit::create(['order_id' => $order->id, 'to_status' => $status, 'datetime' => $date]);
                        }
                    }
                    break;
                case "out_for_delivery":
                    if ($latestDetails->rto_status == 'y') {
                        InternationalOrders::where('order_id', $order->id)->whereNull('rto_initiated_date')->update(['rto_initiated_date' => $date]);
                    }
                    if($latestDetails->rto_status == 'n')
                        InternationalOrders::where('order_id', $order->id)->increment('ofd_attempt',1);
                    InternationalOrders::where('order_id', $order->id)->whereNull('ofd_date')->update(['ofd_date' => $date]);
                    break;
                case "ndr":
                    InternationalOrders::where('order_id', $order->id)->whereNull('ofd_date')->update(['ofd_date' => $date]);
                    break;
                case "rto_initated":
                case "rto_initiated":
                    InternationalOrders::where('order_id', $order->id)->whereNull('rto_initiated_date')->update(['rto_initiated_date' => $date]);
                    break;
                case "delivered":
                    if ($latestDetails->rto_status == 'y') {
                        InternationalOrders::where('order_id', $order->id)->whereNull('rto_initiated_date')->update(['rto_initiated_date' => $date]);
                    }
                    Order::where('id',$order->id)->whereNull('delivered_date')->update(['delivered_date' => $date]);
                    break;
                default:
                    break;
            }
        }catch (Exception $e){
            Logger::write('logs/persit-date/persit-date-'.date('Y-m-d').'.text', [
                'title' => "Update status request",
                'data' => ['Line No' => $e->getLine(),'Message' => $e->getMessage()]
            ]);
        }
    }

    // Push Order Status to Channel
    public static function PushChannelStatus($order,$status,$date = null){
//        if(empty($date)){
//            $date = date('Y-m-d H:i:s');
//        }
//        try{
//            self::storeDatetimeInformation($order,$status,$date);
//            if($order->channel == 'shopify'){
//                ShopifyHelper::SendShopifyStatus($order,$status);
//            }else if($order->channel == 'easyecom'){
//                self::PushEasyecomStatus($order,$status);
//            }else if($order->channel == 'amazon'){
//                $channelController = new ChannelsController();
//                $channelController->_pushStatusToAmazon($order,$status);
//            }else if($order->channel == 'woocommerce'){
//                self::PushWooCommerceStatus($order,$status);
//            }else if($order->channel == 'custom' || $order->channel == 'api'){
//                return Myutility::PushWebHookStatusForCustomOrder($order,$status);
//            }
//        }
//        catch(Exception $e){
//            return false;
//        }
        return false;
    }

    // Push Order Status to Shopify
    public static function PushStatusToShopify($order,$status){
        if($order->channel != "shopify"){
            return false;
        }
        $fulfillmentId = $order->fulfillment_id;
        $shopifyCont = new ShopifyController();
        if($fulfillmentId == ""){
            $fulfillmentId = $shopifyCont->getOrderFulfillmentId($order);
        }
        if($fulfillmentId == ""){
            return false;
        }
        $pushStatus = self::GetShopifyStatus($status);
        $channel = Channels::where('seller_id', $order->seller_id)->where('id', $order->seller_channel_id)->where('channel','shopify')->first();
        if(empty($channel)){
            return false;
        }
        $url = "https://$channel->api_key:$channel->password@$channel->store_url/admin/api/2021-04/orders/$order->channel_id/fulfillments/$order->fulfillment_id/events.json";
        $data=[
            'event' => [
                'status' => $pushStatus
            ]
        ];
        Logger::write('logs/channels/shopify/shopify-'.date('Y-m-d').'.text', [
            'title' => "Update status request",
            'data' => $data
        ]);
        $response = Http::post($url,$data);
        $responseData = $response->json();
        Logger::write('logs/channels/shopify/shopify-'.date('Y-m-d').'.text', [
            'title' => "Update status response",
            'data' => $responseData
        ]);

        if(isset($responseData['fulfillment_event']['status']))
            return true;
        else
            return false;
    }

    // Get Shopify Push Status List
    public static function GetShopifyStatus($status){
        $status = strtolower($status);
        if($status == 'manifested'){
            return 'label_printed';
        }else if($status == 'picked_up'){
            return 'ready_for_pickup';
        }else if($status == 'picked_scheduled'){
            return 'ready_for_pickup';
        }else if($status == 'in_transit'){
            return 'in_transit';
        }else if($status == 'out_for_delivery'){
            return 'out_for_delivery';
        }else if($status == 'delivered'){
            return 'delivered';
        }else if($status == 'ndr'){
            return 'attempted_delivery';
        }else if($status == 'rto_initated'){
            return 'attempted_delivery';
        }else if($status == 'damaged'){
            return 'attempted_delivery';
        }else if($status == 'lost'){
            return 'attempted_delivery';
        }else{
            return 'in_transit';
        }
    }

    // Push Status to EasyEcom
    public static function PushEasyecomStatus($order,$status){
        $seller = Seller::where('id',$order->seller_id)->first();
        if(empty($seller)){
            return false;
        }
        if($status == 'delivered' && $order->rto_status == 'y')
            $status = 'rto_delivered';
        $sendStatus = self::EasyEcomStatus[$status] ?? 0;
        if($sendStatus == 0)
            return false;
        $data=[
            'current_shipment_status_id' =>  $sendStatus,
            'awb' => $order->awb_number ?? "",
            'estimated_delivery_date' => $order->expected_delivery_date ?? date('Y-m-d',strtotime('+3 days')),
        ];
        if($status == 'delivered'){
            $data['delivery_date'] = $order->delivered_date;
        }
        $history = OrderTracking::where('awb_number',$order->awb_number)->get();
        foreach ($history as $h){
            $data['history_scans'][]=[
                'status' => $h['status'],
                'time' => $h['updated_date'],
                'location' => $h['location']
            ];
        }

        Logger::write('logs/channels/easyecom/easyecom-'.date('Y-m-d').'.text', [
            'title' => "Update status request",
            'data' => $data
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post("https://api.easyecom.io/Carrier/updateTrackingStatus?api_token=$seller->easyecom_token",$data);
        $responseData = $response->json();

        Logger::write('logs/channels/easyecom/easyecom-'.date('Y-m-d').'.text', [
            'title' => "Update status response",
            'data' => $responseData
        ]);
        return true;
    }

    // Push Status to Woo-Commerce
    public static function PushWooCommerceStatus($order,$status){
        try{
            $seller = Seller::find($order->seller_id);
            if(empty($seller))
            {
                return false;
            }
            $details = Channels::where('channel','woocommerce')->where('seller_id', $order->seller_id)->where('id', $order->seller_channel_id)->first();
            if(empty($details))
            {
                return false;
            }
            $statusList = ChannelOrderStatusList::where('channel_id',$details->id)->first();
            $sendStatus = 'processing';
            if($status == 'pickup_scheduled' || $status == 'shipped' || $status == 'manifested')
                $sendStatus = 'confirmed';
            else if($status == 'picked_up')
                $sendStatus = 'on-the-way';
            else if($status == 'in_transit')
                $sendStatus = 'on-the-way';
            else if($status == 'delivered')
                $sendStatus = 'completed';
            else if($status == 'cancelled')
                $sendStatus = 'cancelled';
            if(!empty($statusList))
                $sendStatus = $statusList->{$status} ?? null;
            if(empty($sendStatus))
                return false;
            $data = [
                'status' => $sendStatus
            ];
//            $woocommerce = new Client(
//                $details['store_url'],
//                $details['woo_consumer_key'],
//                $details['woo_consumer_secret'],
//                [
//                    'version' => 'wc/v3',
//                ]
//            );
//            $response = $woocommerce->put("orders/{$order->channel_id}", $data);
            $response = WooCommerceHelper::PushOrdersStatus($details,$order->channel_id,$sendStatus);
            return $response;
        }
        catch(Exception $e){
            return false;
        }
    }
    public static function  CreateWooCommerceOrderNote($orderData){
        $details = Channels::where('channel','woocommerce')->where('seller_id', $orderData->seller_id)->where('id', $orderData->seller_channel_id)->first();
        if(empty($details))
            return false;
        $note = "Tracking Link : https://Twinnship.in/order-tracking/{$orderData->awb_number}";
        try{
//            $woocommerce = new Client(
//                $details['store_url'],
//                $details['woo_consumer_key'],
//                $details['woo_consumer_secret'],
//                [
//                    'version' => 'wc/v3',
//                ]
//            );
//            $response = $woocommerce->post("orders/{$orderData->channel_id}/notes", $data);
            WooCommerceHelper::CreateOrderNote($details,$orderData->channel_id,$note);
            return true;
        }
        catch(Exception $e){
            return true;
        }
    }

    // Mark Order as RTO
    public static function RTOOrder($orderId,$isForce=false){
        $order = Order::find($orderId);
        $seller = Seller::find($order->seller_id);
        if(empty($order)){
            $order = ZZArchiveOrder::find($orderId);
            if(empty($order))
                return false;
        }
        //self::PushChannelStatus($order,'rto_initiated');
        if(($order->status == 'pending' || $order->status == 'cancelled' || $order->status == 'pickup_requested') && $isForce == false)
            return true;
        if($order->rto_status == 'y')
            return true;
        // RTO Deduction Logic goes here
        $data = array(
            'seller_id' => $order->seller_id,
            'order_id' => $order->id,
            'amount' => floatval($order->rto_charges ?? $order->shipping_charges),
            'balance' => floatval($seller->balance) - floatval($order->rto_charges ?? $order->shipping_charges),
            'type' => 'd',
            'redeem_type' => 'o',
            'datetime' => date('Y-m-d H:i:s'),
            'method' => 'wallet',
            'description' => 'Order RTO Charge Deducted'
        );
        Transactions::create($data);
        Seller::where('id', $order->seller_id)->decrement('balance', $data['amount']);
        $balance = $seller->balance - $data['amount'];
        if($order->order_type == 'cod'){
            if($seller->floating_value_flag == 'y')
                $refundAmount = $order->cod_charges + $order->early_cod_charges;
            else
                $refundAmount = intval($order->cod_charges) + intval($order->early_cod_charges);
            $data = array(
                'seller_id' => $order->seller_id,
                'order_id' => $order->id,
                'amount' => $refundAmount ?? 0,
                'balance' => $balance + $refundAmount,
                'type' => 'c',
                'redeem_type' => 'o',
                'datetime' => date('Y-m-d H:i:s'),
                'method' => 'wallet',
                'description' => 'Order RTO COD Charge Refunded'
            );
            Transactions::create($data);
            Seller::where('id', $order->seller_id)->increment('balance', $refundAmount);
        }
        //Order::where('id',$orderId)->update();
        Order::where('id', $orderId)->update(['status' => 'rto_initiated', 'rto_status' => 'y']);
        ZZArchiveOrder::where('id', $orderId)->update(['status' => 'rto_initiated', 'rto_status' => 'y']);
        self::PushChannelStatus($order,"rto_initiated",date('Y-m-d H:i:s'));
        return true;
    }

    // Track Order Xpressbees Reverse
    public static function TrackReverseOrderExpressBees($order, $XBKey)
    {
        $returnValue = false;
        $data = [
            "XBkey" => $XBKey,
            "AWBNumber" => $order->awb_number
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://xbclientapi.xbees.in/TrackingService.svc/GetBulkReverseManifestStatus', $data);

        $awb_data = $response->json();

        $tracking_data = $awb_data[count($awb_data) - 1];
        if (isset($tracking_data['ReturnMessage'])) {
            $shipment_summary = $tracking_data;
            $dateTimeToStore = strtotime('Y-m-d H:i:s',strtotime($shipment_summary['StatusDate']));
            // dd($shipment_summary);
            $order_tracking = OrderTracking::where('awb_number', $tracking_data['AWBNumber'])->orderBy('id', 'desc')->first();
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['StatusCode']) {
                    switch ($shipment_summary['StatusCode']) {
                        case 'RPPickDone':
                            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y',strtotime('Y-m-d H:i:s',strtotime($shipment_summary['StatusDate'])),"pickup_time" => strtotime('Y-m-d H:i:s',strtotime($shipment_summary['StatusDate']))]);
                            self::PushChannelStatus($order,'picked_up',$dateTimeToStore);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'OFD':
                            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                            self::PushChannelStatus($order,'out_for_delivery',$dateTimeToStore);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'IT':
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order,'in_transit',$dateTimeToStore);
                            break;
                        case 'DLVD':
                            $delivery_date = date('Y-m-d', strtotime($shipment_summary['ActionDate']));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            self::PushChannelStatus($order,'delivered',$dateTimeToStore);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'RPCancel':
                            //Order::where('id', $order->id)->update(['status' => 'cancelled']);
                            self::PushChannelStatus($order,'cancelled',$dateTimeToStore);
                            break;
                    }
                    $data = [
                        "awb_number" => $tracking_data['AWBNumber'],
                        "status_code" => $shipment_summary['StatusCode'],
                        "status" => $shipment_summary['Status'],
                        "status_description" => $shipment_summary['Reason'],
                        "remarks" =>  $shipment_summary['Reason'],
                        "location" =>  $shipment_summary['DestinationAddress'],
                        "updated_date" => $shipment_summary['ActionDate'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    // if (isset($expected_date))
                    //     Order::where('awb_number', $order->awb_number)->update(['expected_delivery_date' => $expected_date]);
                    $returnValue = true;
                }
            } else {
                $data = [
                    "awb_number" => $tracking_data['AWBNumber'],
                    "status_code" => $shipment_summary['StatusCode'],
                    "status" => $shipment_summary['Status'],
                    "status_description" => $shipment_summary['Reason'],
                    "remarks" =>  $shipment_summary['Reason'],
                    "location" =>  $shipment_summary['DestinationAddress'],
                    "updated_date" => $shipment_summary['ActionDate'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                $returnValue = true;
            }
        }
        return $returnValue;
    }
    public static function TrackReverseOrderExpressBeesNew($order,XpressBees $client)
    {
        $returnValue = false;
        $awb_data = $client->GetTrackingReverse($order->awb_number);
        if(is_array($awb_data) && count($awb_data) > 0)
        {
            $tracking_data = $awb_data[count($awb_data) - 1];
            if (isset($tracking_data['ReturnMessage'])) {
                $shipment_summary = $tracking_data;
                $shipment_summary['ActionDate'] = $shipment_summary['ActionDate'] ?? date('Y-m-d H:i:s');
                $dateTimeToStore = date('Y-m-d H:i:s',strtotime($shipment_summary['ActionDate']));
                // dd($shipment_summary);
                $order_tracking = OrderTracking::where('awb_number', $tracking_data['AWBNumber'])->orderBy('id', 'desc')->first();
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $shipment_summary['StatusCode']) {
                        switch ($shipment_summary['StatusCode']) {
                            case 'Pending':
                            case 'RPOutForPickUp':
                            case 'RPAttemptNotPick':
                                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                                self::PushChannelStatus($order,'pickup_scheduled',$dateTimeToStore);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'RPPickDone':
                            case 'RP':
                            case 'CL-107':
                                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $dateTimeToStore]);
                                self::PushChannelStatus($order,'picked_up',$dateTimeToStore);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'OFD':
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                self::PushChannelStatus($order,'out_for_delivery',$dateTimeToStore);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'IT':
                            case 'ReachedAtDestination':
                                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                self::PushChannelStatus($order,'in_transit',$dateTimeToStore);
                                break;
                            case 'DLVD':
                            case 'RD':
                                $delivery_date = date('Y-m-d', strtotime($shipment_summary['ActionDate']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                self::PushChannelStatus($order,'delivered',$delivery_date);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'RPCancel':
                            case 'CAN':
                            case 'RVPCancelledQCFail':
                                MyUtility::PerformCancellation(Seller::find($order->seller_id),$order);
                                self::PushChannelStatus($order,'cancelled',$dateTimeToStore);
                                break;
                            case 'UD':
                                if ($order->rto_status != 'y') {
                                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $dateTimeToStore,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['ActionDate']]);
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => date('Y-m-d', strtotime($dateTimeToStore)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'XpressBees',
                                        'reason' => $shipment_summary['Status'],
                                        'action_status' => 'pending',
                                        'remark' => 'pending',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    self::PushChannelStatus($order,'ndr');
                                }
                                break;
                            default:
                                $missStatus = [
                                    'order_id' => $order->id,
                                    'courier_keyword' => $order->courier_partner,
                                    'status' => $shipment_summary['StatusCode'],
                                    'status_description' => $shipment_summary['Reason'],
                                    'json' => json_encode($tracking_data),
                                    'created_at' => date('Y-m-d h:i:s')
                                ];
                                CourierMissStatusCode::create($missStatus);
                                break;
                        }
                        $data = [
                            "awb_number" => $tracking_data['AWBNumber'],
                            "status_code" => $shipment_summary['StatusCode'],
                            "status" => $shipment_summary['Status'],
                            "status_description" => $shipment_summary['Reason'],
                            "remarks" =>  $shipment_summary['Reason'],
                            "location" =>  $shipment_summary['DestinationAddress'],
                            "updated_date" => $shipment_summary['ActionDate'],
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                        // if (isset($expected_date))
                        //     Order::where('awb_number', $order->awb_number)->update(['expected_delivery_date' => $expected_date]);
                        $returnValue = true;
                    }
                } else {
                    $data = [
                        "awb_number" => $tracking_data['AWBNumber'],
                        "status_code" => $shipment_summary['StatusCode'],
                        "status" => $shipment_summary['Status'],
                        "status_description" => $shipment_summary['Reason'],
                        "remarks" =>  $shipment_summary['Reason'],
                        "location" =>  $shipment_summary['DestinationAddress'],
                        "updated_date" => $shipment_summary['ActionDate'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                }
            }
            return $returnValue;
        }
    }

    // Track Order Ekart
    public static function TrackOrderEKart($order)
    {
        $returnValue = false;
        $ekart = new Ekart();
        $response = $ekart->getTracking($order->awb_number);
        if(!$response)
            return false;
        $shipment_summary = $response['history'][0];
        $expectedDeliveryDate = $response['expected_delivery_date'] != "Shipment yet to be dispatched" ? date('Y-m-d H:i:s',strtotime($response['expected_delivery_date'])) : date('Y-m-d H:i:s',strtotime('+5 days'));
        $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
        if ($order_tracking != null) {
            if ($order_tracking->status_code != $shipment_summary['status']) {
                self::HandleEkartTracking($order,$shipment_summary,$expectedDeliveryDate);
            }
            $returnValue = true;
        } else {
            $returnValue = true;
            self::HandleEkartTracking($order,$shipment_summary,$expectedDeliveryDate);
        }
        return $returnValue;
    }
    public static function TrackOrderEKartSmall($order)
    {
        $returnValue = false;
        $ekart = new EkartSmall();
        $response = $ekart->getTracking($order->awb_number);
        if(!$response)
            return false;
        $shipment_summary = $response['history'][0];
        $expectedDeliveryDate = $response['expected_delivery_date'] != "Shipment yet to be dispatched" ? date('Y-m-d H:i:s',strtotime($response['expected_delivery_date'])) : date('Y-m-d H:i:s',strtotime('+5 days'));
        $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
        if ($order_tracking != null) {
            if ($order_tracking->status_code != $shipment_summary['status']) {
                self::HandleEkartTracking($order,$shipment_summary,$expectedDeliveryDate);
            }
            $returnValue = true;
        } else {
            $returnValue = true;
            self::HandleEkartTracking($order,$shipment_summary,$expectedDeliveryDate);
        }
        return $returnValue;
    }
    public static function HandleEkartTracking($order,$shipment_summary,$expectedDeliveryDate){
        $date = $shipment_summary['event_date'] ?? date('Y-m-d H:i:s');
        $updatedDate = date('Y-m-d H:i:s',strtotime($date));
        // $expectedDeliveryDate = $shipment_summary['expected_delivery_date'] != "Shipment yet to be dispatched" ? date('Y-m-d H:i:s',strtotime($shipment_summary['expected_delivery_date'])) : date('Y-m-d H:i:s',strtotime('+5 days'));
        switch ($shipment_summary['status']) {
            case 'pickup_scheduled':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expectedDeliveryDate,'status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                self::PushChannelStatus($order,'pickup_scheduled',$updatedDate);
                break;
            case 'pickup_complete':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expectedDeliveryDate,'status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $updatedDate]);
                self::PushChannelStatus($order,'picked_up',$updatedDate);
                self::CheckAndSendSMS($order);
                break;
            case 'out_for_delivery':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $updatedDate != $order->ndr_status_date) {
                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $order->seller_id,
//                            'order_id' => $order->id,
//                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                            'raised_time' => date('H:i:s'),
//                            'action_by' => 'Ekart',
//                            'reason' => $order->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $updatedDate]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$updatedDate);
                self::CheckAndSendSMS($order);
                break;
            case 'in_transit':
            case 'shipment_expected':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$updatedDate);
                break;
            case stristr($shipment_summary['status'],'undelivered'):
                if ($order->rto_status != 'y') {
                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $updatedDate]);
                    $ndrRaisedDate = date('Y-m-d H:i:s');
                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $updatedDate]);
                    $attempt = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                        'raised_time' => date('H:i:s'),
                        'action_by' => 'Ekart',
                        'reason' => $shipment_summary['status'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                    self::PushChannelStatus($order,'ndr',$updatedDate);
                }
                break;
            case 'rto_in_transit':
            case 'rto_received':
                if($order->o_type == "forward")
                    self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$updatedDate);
                break;
            case 'rto_completed':
                if($order->o_type == "forward")
                    self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'delivered','delivered_date' => $updatedDate]);
                self::PushChannelStatus($order,'delivered',$updatedDate);
                break;
            case 'delivered':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $updatedDate]);
                if ($order->order_type == 'cod') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $updatedDate,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($order,'delivered',$updatedDate);
                self::CheckAndSendSMS($order);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $shipment_summary['status'],
                    'status_description' => $shipment_summary['public_description'],
                    'json' => json_encode($shipment_summary),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $shipment_summary['status'] ?? "",
            "status" => $shipment_summary['status'] ?? "",
            "status_description" => $shipment_summary['public_description'] ?? "",
            "remarks" =>  $shipment_summary['public_description'] ?? "",
            "location" =>  $shipment_summary['public_description'] ?? "",
            "updated_date" => $updatedDate ?? date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    // Track Order Delhivery Surface
    public static function TrackOrderDelhiverySurface($order,$keyword)
    {
        $returnValue = false;
        //$credentials = ShippingHelper::CheckSellerCustomChannel($order->courier_partner,$order->seller_id);
        $delhiveryClient = new Delhivery($keyword);
        $awb_data = $delhiveryClient->GetTracking($order->awb_number);
//            Logger::write('logs/partners/delhivery/delhivery-pull-tracking'.date('Y-m-d').'.text', [
//                'title' => "Order Tracking For Awb: " . $order->awb_number,
//                'data' => $awb_data
//            ]);
        if (isset($awb_data['ShipmentData'])) {
            $tracking_data = $awb_data['ShipmentData'][0]['Shipment'];
            $shipment_summary = $tracking_data['Scans'][count($tracking_data['Scans']) - 1]['ScanDetail'];
            $order_tracking = OrderTracking::where('awb_number', $tracking_data['AWB'])->orderBy('id', 'desc')->first();
            if ($order_tracking != null) {
                //dd($shipment_summary['StatusCode']);
                if ($order_tracking->status_code != $shipment_summary['StatusCode']) {
                    self::HandleDelhiveryStatusCodes($shipment_summary,$tracking_data,$order);
                }
            } else {
                self::HandleDelhiveryStatusCodes($shipment_summary,$tracking_data,$order);
                if($shipment_summary['ScanType'] == 'RT'){
                    self::RTOOrder($order->id);
                    $response = self::PushChannelStatus($order,'rto_initiated',$shipment_summary['StatusDateTime']);
                }
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    public static function HandleDelhiveryStatusCodes($shipment_summary,$tracking_data,$order){
        $edd = $tracking_data['ExpectedDeliveryDate'] ?? null;
        if(!empty($edd)) {
            try{
                Order::where('id', $order->id)->update(['expected_delivery_date' => date('Y-m-d', strtotime($edd))]);
            }catch (Exception $e){}
        }
        if($shipment_summary['ScanType'] == 'RT') {
            self::RTOOrder($order->id);
            self::PushChannelStatus($order,'rto_initiated',$shipment_summary['StatusDateTime']);
        }
        if($shipment_summary['StatusCode'] == 'X-PROM' || $shipment_summary['StatusCode'] == 'X-UNEX' || $shipment_summary['StatusCode'] == 'EOD-77')
        {
            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => date('Y-m-d H:i:s')]);
            self::PushChannelStatus($order,'picked_up',$shipment_summary['StatusDateTime']);
            self::CheckAndSendSMS($order);
        }
        else if(in_array($shipment_summary['StatusCode'],['ST-114','FMEOD-110','FMEOD-905','DTUP-214','FMEOD-109','PNP-101','ST-116','FMEOD-108','X-UCI','X-PNP','DTUP-205','FMEOD-152','FMPUR-101','DTUP-219','DTUP-210','FMEOD-103','X-ASP','X-DDD3FP','FMOFP-101','EOD-68','FMEOD-106'])){
//            Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_time' => date('Y-m-d H:i:s',strtotime('+1 day'))]);

            if($order->status == 'manifested') {
                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                self::PushChannelStatus($order, 'pickup_scheduled', $shipment_summary['StatusDateTime']);
            }
        }
        else if(in_array($shipment_summary['StatusCode'],['DLYMPS-101','EOD-43','EOD-3','EOD-148','EOD-6','DLYDC-107','DLYB2B-101','ST-117','EOD-73','ST-108','DLYB2B-108','ST-107','EOD-149','X-SC','ST-120','EOD-104','EOD-69','DLYRPC-417','X-PDASS','ST-118','ST-NTL','DLYLH-146','ST-NT','ST-NI','ST-NI6','EOD-138','EOD-74','EOD-6I','EOD-11','EOD-26','EOD-111','ST-111','ST-105','EOD-40','CL-106','CL-101','RD-PD10','EOD-15','EOD-121','EOD-83','EOD-137']))
        {
            // Handle NDR Code Here
            if($order->ndr_status != "y") {
                //Order::where('id', $order->id)->update(['ndr_raised_time' => date('Y-m-d H:i:s'), 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Instructions']]);
                $ndrRaisedDate = date('Y-m-d H:i:s');
                Order::where('id', $order->id)->update(['ndr_raised_time' => $ndrRaisedDate, 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Instructions']]);
                $attempt = [
                    'seller_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                    'raised_time' => date('H:i:s'),
                    'action_by' => 'Delhivery',
                    'reason' => $shipment_summary['Instructions'],
                    'action_status' => 'pending',
                    'remark' => 'pending',
                    'u_address_line1' => 'new address line 1',
                    'u_address_line2' => 'new address line 2',
                    'updated_mobile' => ''
                ];
                Ndrattemps::create($attempt);
            }
            if($shipment_summary['ScanType'] == 'RT') {
                self::RTOOrder($order->id);
                self::PushChannelStatus($order,'rto_initiated',$shipment_summary['StatusDateTime']);
            }
            else
                self::PushChannelStatus($order,'ndr',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
        }
        else if($order->o_type == 'forward' && ($shipment_summary['StatusCode'] == 'EOD-6O'))
        {
            self::RTOOrder($order->id);
            self::PushChannelStatus($order,'rto_initiated',$shipment_summary['StatusDateTime']);
        }
        else if($shipment_summary['StatusCode'] == 'X-DDD3FD')
        {
            if ($order->rto_status != 'y') {
                if ($order->ndr_status == 'y' && $shipment_summary['StatusDateTime'] != $order->ndr_status_date) {
                    //make attempt here
//                    $attempt = [
//                        'seller_id' => $order->seller_id,
//                        'order_id' => $order->id,
//                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                        'raised_time' => date('H:i:s'),
//                        'action_by' => 'Delhivery',
//                        'reason' => $order->reason_for_ndr,
//                        'action_status' => 'requested',
//                        'remark' => 'requested',
//                        'u_address_line1' => 'new address line 1',
//                        'u_address_line2' => 'new address line 2',
//                        'updated_mobile' => ''
//                    ];
                    //Ndrattemps::create($attempt);
                    Order::where('id', $order->id)->update(['ndr_status_date' => $shipment_summary['StatusDateTime']]);
                }
            }
            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
            self::PushChannelStatus($order,'out_for_delivery',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
            self::CheckAndSendSMS($order);
        }
        else if(in_array($shipment_summary['StatusCode'],['DLYRG-120','DLYDG-119','DTUP-209','DTUP-ZL','DLYLH-115','S-TAT2','DTUP-207','DLYLH-106','DLYRG-132','PNP-102','DLYLH-133','DLYLH-104','DLYRG-125','X-PIOM','X-PPOM','X-DLL2F','X-DBL1F','CS-CSL','X-IBD3F','X-DBL2F','DLYLH-105','DOFF-128','X-ILL1F','X-DWS','CS-101','X-ILL2F','DLYLH-126','ST-115','DLYSHRTBAG-115','S-MAR','X-OLL2F','DLYLH-152','DTUP-204','DLYDC-101','EOD-86','CS-104','DLYMR-118','DLYHD-007','DLYRG-135','DLYDG-120','DLYDC-105','DLYSOR-101','S-MDIN','ST-110','DLYDC-416','DLYLH-136','DLYDC-102','DLYLH-151','DLYRG-130','EOD-65','RD-PD7','S-XIN','DTUP-203','DLYSU-100','L-PXM','X-DBFR','DLYSEC-100','X-AWD','U-MSM']))
        {
//            if($order->status == 'manifested'){
//                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $shipment_summary['StatusDateTime']]);
//            }
//            else if($order->status != 'in_transit'){
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
                self::CheckAndSendSMS($order);
//            }
        }
        else if(in_array($shipment_summary['StatusCode'],['RD-PD12']))
        {
                Order::where('id', $order->id)->update(['status' => 'damaged']);
                self::PushChannelStatus($order,'damaged');
        }
        else if(in_array($shipment_summary['StatusCode'],['LT-100']))
        {
                Order::where('id', $order->id)->update(['status' => 'lost']);
                self::PushChannelStatus($order,'lost');
        }
        else if(in_array($shipment_summary['StatusCode'],['RT-108','RT-113','RT-109','ST-102','RD-PD22','DTUP-235','RT-114','RT-101','RD-PD24','X-DDD3FD','EOD-148','ST-118','RD-PD23'])){
            if($shipment_summary['ScanType'] == 'RT') {
                self::RTOOrder($order->id);
                self::PushChannelStatus($order,'lost',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
            }
        }
        else if($shipment_summary['StatusCode'] == 'RD-AC' || $shipment_summary['StatusCode'] == 'RT-110')
        {
            if($order->o_type == 'forward')
                self::RTOOrder($order->id);
            if($shipment_summary['ScanType'] == 'DL'){
                // mark shipment as rto delivered
                $delivery_date = date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime']));
                Order::where('id', $order->id)->update(['status' => 'rto_delivered', 'delivered_date' => $delivery_date]);
                self::PushChannelStatus($order,'delivered',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
            }
        }
        else if($shipment_summary['StatusCode'] == 'SC-101' || $shipment_summary['StatusCode'] == 'EOD-38' || $shipment_summary['StatusCode'] ==  'EOD-135' || $shipment_summary['StatusCode'] == 'EOD-37' || $shipment_summary['StatusCode'] == 'EOD-36' || $shipment_summary['StatusCode'] == 'ED-100' || $shipment_summary['StatusCode'] == 'EOD-600')
        {
            if($order->status == 'delivered')
                return true;
            $delivery_date = date('Y-m-d', strtotime($shipment_summary['StatusDateTime']));
            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
            if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                $data = array(
                    'seller_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'amount' => $order->invoice_amount,
                    'type' => 'c',
                    'datetime' => $delivery_date,
                    'description' => 'Order COD Amount Credited',
                    'redeem_type' => 'o',
                );
                $resp = COD_transactions::where('seller_id',$order->seller_id)->where('order_id',$order->id)->first();
                if(empty($resp)){
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
            }
            self::PushChannelStatus($order,'delivered',date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime'])));
            self::CheckAndSendSMS($order);
        }
        else{
            $missStatus = [
                'order_id' => $order->id,
                'courier_keyword' => $order->courier_partner,
                'status' => $shipment_summary['StatusCode'],
                'status_description' => $shipment_summary['Instructions'],
                'json' => json_encode($tracking_data),
                'created_at' => date('Y-m-d h:i:s')
            ];
            CourierMissStatusCode::create($missStatus);
        }
        $data = [
            "awb_number" => $tracking_data['AWB'],
            "status_code" => $shipment_summary['StatusCode'],
            "status" => $shipment_summary['Scan'],
            "status_description" => $shipment_summary['Instructions'],
            "remarks" =>  $shipment_summary['Instructions'],
            "location" =>  $shipment_summary['ScannedLocation'],
            "updated_date" => $shipment_summary['StatusDateTime'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
        $returnValue = true;
        return $returnValue;
    }

    // Track Order Shadowfax
    public static function TrackOrderShadowFax($order)
    {
        $returnValue = false;
        $shadowfax = new Shadowfax();
        $awb_data = $shadowfax->GetTracking($order);
        $tracking_data = $awb_data['tracking_details'] ?? null;
        if(empty($tracking_data))
            return $returnValue;
        $shipment_summary = $tracking_data[count($tracking_data) - 1];
        $order_tracking = OrderTracking::where('awb_number', $shipment_summary['awb_number'])->orderBy('id', 'desc')->first();
        if ($order_tracking == null || ( $order_tracking->status_code != null && $order_tracking->status_code != $shipment_summary['status_id'])) {
                $returnValue = self::HandleShadowfaxTracking($order, $shipment_summary);
        }
        return $returnValue;
    }
    public static function HandleShadowfaxTracking($order, $shipment_summary)
    {
        $EDD = $awb_data['order_details']['promised_delivery_date'] ?? null;
        if(!empty($EDD)) {
            try{
                Order::where('id', $order->id)->update(['expected_delivery_date' => date('Y-m-d', strtotime($EDD))]);
            }catch (Exception $e){}
        }
        switch ($shipment_summary['status_id']) {
            case 'assigned_for_seller_pickup':
                ShipmentMovementHelper::PerformPickupScheduled($order, $shipment_summary['created']);
                break;
            case 'picked':
                ShipmentMovementHelper::PerformPickedUp($order, $shipment_summary['created']);
                break;
            case 'ofd':
                ShipmentMovementHelper::PerformOutForDelivery($order, $shipment_summary['created']);
                break;
            case 'assigned_for_delivery':
            case 'recd_at_fwd_dc':
            case 'recd_at_fwd_hub':
                ShipmentMovementHelper::PerformInTransit($order, $shipment_summary['created']);
                break;
            case 'delivered':
                ShipmentMovementHelper::PerformDelivered($order, $shipment_summary['created']);
                break;
            case 'rts':
                ShipmentMovementHelper::PerformRTOInitiated($order, $shipment_summary['created']);
                break;
            case 'rts_d':
                ShipmentMovementHelper::PerformRTODelivered($order, $shipment_summary['created']);
                break;
            case 'nc':
            case 'na':
            case 'on_hold':
                ShipmentMovementHelper::PerformNDR($order,$shipment_summary['status'], $shipment_summary['created']);
                break;
            case 'lost':
                ShipmentMovementHelper::PerformLost($order, $shipment_summary['created']);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $shipment_summary['status_id'],
                    'status_description' => $shipment_summary['remarks'],
                    'json' => json_encode($shipment_summary),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $shipment_summary['awb_number'],
            "status_code" => $shipment_summary['status_id'],
            "status" => $shipment_summary['status'],
            "status_description" => $shipment_summary['remarks'],
            "remarks" =>  $shipment_summary['remarks'],
            "location" =>  $shipment_summary['location'],
            "updated_date" => $shipment_summary['created'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
        return true;
    }

    // Track Order For Udaan
    public static function TrackOrderUdaan($order)
    {
        $returnValue = false;
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC'
        ])->get("https://udaan.com/api/udaan-express/integration/v1/shipment/tracking/$order->awb_number");
        $awb_data = $response->json();
        $tracking_data = $awb_data['response']['externalShipmentScans'];
        $shipment_summary = $tracking_data[count($tracking_data) - 1];
        $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
        if(!empty($awb_data['response']['eta'])){
            $expected_date=date('Y-m-d H:i:s', $awb_data['response']['eta'] / 1000);
        }
        else{
            $expected_date = $order->expected_delivery_date;
        }
        if ($order_tracking != null) {
            $dateTimeToStore = date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000);
            if ($order_tracking->status_code != $shipment_summary['shipmentState']) {
                switch ($shipment_summary['shipmentState']) {
                    case 'PICKUP_CREATED':
                        Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                        self::PushChannelStatus($order,'pickup_scheduled',$dateTimeToStore);
                        break;
                    case 'PICKED_UP':
                        Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000)]);
                        self::PushChannelStatus($order,'picked_up',$dateTimeToStore);
                        self::CheckAndSendSMS($order);
                        break;
                    case 'OUT_FOR_DELIVERY':
                        if ($order->rto_status != 'y') {
                            if ($order->ndr_status == 'y' && date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000) != $order->ndr_status_date) {
                                //make attempt here
                                $attempt = [
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                                    'raised_time' => date('H:i:s'),
                                    'action_by' => 'Udaan',
                                    'reason' => $order->reason_for_ndr,
                                    'action_status' => 'requested',
                                    'remark' => 'requested',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);
                                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'ndr_status_date' => date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000)]);
                            }
                        }
                        Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'out_for_delivery']);
                        self::PushChannelStatus($order,'out_for_delivery',$dateTimeToStore);
                        self::CheckAndSendSMS($order);
                        break;
                    case 'HUB_INSCAN':
                    case 'HUB_OUTSCAN':
                    case 'RAD':
                        Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'in_transit']);
                    self::PushChannelStatus($order,'in_transit',$dateTimeToStore);
                        break;
                    case 'DELIVERED':
                        $delivery_date = date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000);
                        Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                        if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                            $data = array(
                                'seller_id' => $order->seller_id,
                                'order_id' => $order->id,
                                'amount' => $order->invoice_amount,
                                'type' => 'c',
                                'datetime' => $delivery_date,
                                'description' => 'Order COD Amount Credited',
                                'redeem_type' => 'o',
                            );
                            COD_transactions::create($data);
                            Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                        }
                        self::PushChannelStatus($order,'delivered',$dateTimeToStore);
                        self::CheckAndSendSMS($order);
                        break;
                    case 'RTO_MARKED':
                        self::RTOOrder($order->id);
                        self::PushChannelStatus($order,'rto_initated',$dateTimeToStore);
                        break;
                    case 'CANCELLED':
                        Order::where('id', $order->id)->update(['status' => 'cancelled']);
                        self::PushChannelStatus($order,'cancelled',$dateTimeToStore);
                        break;
                    default:
                        $missStatus = [
                            'order_id' => $order->id,
                            'courier_keyword' => $order->courier_partner,
                            'status' => $shipment_summary['shipmentState'],
                            'status_description' => $shipment_summary['comment'],
                            'json' => json_encode($awb_data),
                            'created_at' => date('Y-m-d h:i:s')
                        ];
                        CourierMissStatusCode::create($missStatus);
                        break;
                }
                $data = [
                    "awb_number" =>   $awb_data['response']['awbNumber'],
                    "status_code" => $shipment_summary['shipmentState'],
                    "status" => $shipment_summary['shipmentState'],
                    "status_description" => $shipment_summary['comment'],
                    "remarks" =>  $shipment_summary['comment'],
                    "location" =>  $shipment_summary['city'] . ',' . $shipment_summary['state'],
                    "updated_date" => date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                $returnValue = true;
            }
        } else {
            $data = [
                "awb_number" =>   $awb_data['response']['awbNumber'],
                "status_code" => $shipment_summary['shipmentState'],
                "status" => $shipment_summary['shipmentState'],
                "status_description" => $shipment_summary['comment'],
                "remarks" =>  $shipment_summary['comment'],
                "location" =>  $shipment_summary['city'] . ',' . $shipment_summary['state'],
                "updated_date" => date('Y-m-d H:i:s', $shipment_summary['timestamp'] / 1000),
                'created_at' => date('Y-m-d H:i:s')
            ];
            OrderTracking::create($data);
            $returnValue = true;
        }
        return $returnValue;
    }

    // Track Order for DTDC Surface
    public static function TrackOrderDtdcSurface($order)
    {
        $returnValue = false;
        $credentials = ShippingHelper::CheckSellerCustomChannel($order->courier_partner,$order->seller_id);
        $dtdc = new Dtdc();
        if($credentials['status']){
            $dtdc = new CustomDtdc($credentials['credentials']);
        }
        $awb_data = $dtdc->GetTracking($order);
        // dd($awb_data);
        if(empty($awb_data['status'])) {
            return false;
        }
        if ($awb_data['status'] == "SUCCESS") {
            $tracking_data = $awb_data['trackDetails'];
            $shipment_summary = $tracking_data[count($tracking_data) - 1];
            $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
            $expected_date=$order->expected_delivery_date;
            $date = substr($awb_data['trackHeader']['strExpectedDeliveryDate'], 0, 2);
            $month = substr($awb_data['trackHeader']['strExpectedDeliveryDate'], 2, 2);
            $year = substr($awb_data['trackHeader']['strExpectedDeliveryDate'], 4);
            try{
                $expected_date=date('Y-m-d',strtotime("$year-$month-$date"));
            }catch(Exception $e){}

            $date = substr($shipment_summary['strActionDate'], 0, 2);
            $month = substr($shipment_summary['strActionDate'], 2, 2);
            $year = substr($shipment_summary['strActionDate'], 4);
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['strCode']) {
                    $returnValue = self::_HandleDTDCTracking($shipment_summary,$order,$date,$month,$year,$expected_date);
                }
            } else {
                $returnValue = self::_HandleDTDCTracking($shipment_summary,$order,$date,$month,$year,$expected_date);
            }
        }
        return $returnValue;
    }

    public static function _HandleDTDCTracking($shipment_summary,$order,$date,$month,$year,$expected_date){
        $datetimetostore = date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year));
        switch ($shipment_summary['strCode']) {
            case 'OUTDLV':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year)) != $order->ndr_status_date) {
                        $attempt = [
                            'seller_id' => $order->seller_id,
                            'order_id' => $order->id,
                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'DTDC',
                            'reason' => $order->reason_for_ndr,
                            'action_status' => 'requested',
                            'remark' => 'requested',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'ndr_status_date' => date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year))]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$datetimetostore);
                self::CheckAndSendSMS($order);
                break;
            case 'OBMN':
            case 'CDOUT':
            case 'CDIN':
            case 'IBMN':
            case 'OPMF':
            case 'IBMD':
            case 'IMBM':
            case 'OMBM':
            case 'IRBO':
            case 'ORBO':
            case 'IPMF':
                Order::where('id', $order->id)->update(['expected_delivery_date' => $expected_date,'status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$datetimetostore);
                break;
            case 'PCUP':
                Order::where('id', $order->id)->update(['status' => 'picked_up','pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year))]);
                self::PushChannelStatus($order,'picked_up',$datetimetostore);
                break;
            case 'RTO':
                self::RTOOrder($order->id);
                self::PushChannelStatus($order,'rto_initated',$datetimetostore);
                break;
            case 'RTOCDOUT':
            case 'RTOOPMF':
            case 'RTOIBMN':
            case 'RTOIPMF':
            case 'RTOOBMD':
            case 'RTOIBMD':
            case 'RTOOBMN':
            case 'RTOIMBM':
            case 'RTOOMBM':
            case 'RTOORBO':
            case 'RTOIRBO':
            case 'RTOCDIN':
            case 'RTONONDLV':
                self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$datetimetostore);
                break;
            case 'RTOOUTDLV':
            case 'RETURND':
                self::RTOOrder($order->id);
                Order::where('id',$order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$datetimetostore);
                break;
            case 'RTODLV':
                self::RTOOrder($order->id);
                $delivery_date = date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                self::PushChannelStatus($order,'delivered',$datetimetostore);
                break;
            case 'NONDLV':
                if ($order->rto_status != 'y') {
                    //dd($shipment_summary);
                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'expected_delivery_date' => $expected_date,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['sTrRemarks'], 'ndr_action' => 'pending', 'ndr_status_date' => date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year))]);
                    $ndrRaisedDate = date('Y-m-d H:i:s');
                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'expected_delivery_date' => $expected_date,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['sTrRemarks'], 'ndr_action' => 'pending', 'ndr_status_date' => date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year))]);
                    $attempt = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                        'raised_time' => date('H:i:s'),
                        'action_by' => 'DTDC',
                        'reason' => $shipment_summary['sTrRemarks'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                    self::PushChannelStatus($order,'ndr',$datetimetostore);
                }
                break;
            case 'DLV':
                $delivery_date = date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $delivery_date,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($order,'delivered',$datetimetostore);
                self::CheckAndSendSMS($order);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $shipment_summary['strCode'],
                    'status_description' => $shipment_summary['sTrRemarks'],
                    'json' => json_encode($shipment_summary),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" =>   $order->awb_number,
            "status_code" => $shipment_summary['strCode'],
            "status" => $shipment_summary['strAction'],
            "status_description" => $shipment_summary['sTrRemarks'],
            "remarks" =>  $shipment_summary['sTrRemarks'],
            "location" =>  $shipment_summary['strDestination'],
            "updated_date" => date('Y-m-d H:i:s', strtotime($date . '-' . $month . '-' . $year)),
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
        return true;
    }

    // Track Order for Ecom Express
    public static function TrackOrderEcomExpress($order){
        $returnValue = false;
        $ecom = new EcomExpressController();
        if(!empty($order->alternate_awb_number) && $order->alternate_awb_number != 'None') {
            $awbNumber = $order->alternate_awb_number;
        } else {
            $awbNumber = $order->awb_number;
        }
        $trackingStatus = [
            'awb_number' => $order->awb_number,
            'status_code' => '',
            'status' => '',
            'status_description' => '',
            'remarks' => '',
            'location' => '',
            'updated_date' => '',
            'ref_awb' => '',
            'expected_date' => '',
        ];
        $response = $ecom->_TrackOrderEcom($awbNumber);
        file_put_contents("xml_response.xml", $response->body());
        try{
            $object = simplexml_load_file("xml_response.xml");
            $responseData = self::XmlToArray($object);
            if(isset($responseData['ecomexpress-objects']['object']['field'])){
                $current = $responseData['ecomexpress-objects']['object']['field'];
                foreach ($current as $c){
                    if($c['@name'] == 'reason_code_number')
                        $trackingStatus['status_code'] = $c['text'] ?? "";
                    if($c['@name'] == 'status')
                        $trackingStatus['status'] = $c['text'] ?? "";
                    if($c['@name'] == 'tracking_status'){
                        $trackingStatus['status_description'] = $c['text'] ?? "";
                        $trackingStatus['tracking_status'] = $c['text'] ?? "";
                    }
                    if($c['@name'] == 'last_update_datetime')
                        $trackingStatus['updated_date'] = $c['text'] ?? "";
                    if($c['@name'] == 'current_location_name')
                        $trackingStatus['location'] = $c['text'] ?? "";
                    if($c['@name'] == 'ref_awb')
                        $trackingStatus['ref_awb'] = $c['text'] ?? "";
                    if($c['@name'] == 'expected_date')
                        $trackingStatus['expected_date'] = $c['text'] ?? "";
                }
            }
            if($trackingStatus['awb_number'] !="" && $trackingStatus['status_code'] != ""){
                $expectedDate = $trackingStatus['expected_date'];
                if(!empty($expectedDate)) {
                    try{
                        Order::where('id', $order->id)->update(['expected_delivery_date' => date('Y-m-d', strtotime($expectedDate))]);
                    }catch (Exception $e){}
                }
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                $newAwb = $trackingStatus['ref_awb'] ?? null;
                if(!empty($newAwb) && $newAwb != "None" && ($order->alternate_awb_number == null || $order->alternate_awb_number == 'None')){
                    self::RTOOrder($order->id);
                    Order::where('id', $order->id)->update(['alternate_awb_number' => $newAwb]);
                }
                if(!empty($order_tracking)){
                    if($order_tracking->status_code != $trackingStatus['status_code']){
                        self::HandleEcomExpressResponse($order->awb_number,$trackingStatus,$order);
                        $returnValue = true;
                    }
                }else{
                    self::HandleEcomExpressResponse($order->awb_number,$trackingStatus,$order);
                    $returnValue = true;
                }
            }
        }
        catch(Exception $e){
            Logger::write('logs/partners/ecom-express/exception-tracking-'.date('Y-m-d').'.text', [
                'title' => "Order Tracking For Awb: " . $order->awb_number,
                'data' => $e->getMessage() . ' - ' . $e->getLine() . ' : ' . $response->body()
            ]);
        }
        return $returnValue;
    }

    // Track Order for Ecom Express 3kg
    public static function TrackOrderEcomExpress3kg($order){
        $returnValue = false;
        $ecom = new EcomExpress3kgController();
        if(!empty($order->alternate_awb_number)) {
            $awbNumber = $order->alternate_awb_number;
        } else {
            $awbNumber = $order->awb_number;
        }
        $trackingStatus = [
            'awb_number' => $order->awb_number,
            'status_code' => '',
            'status' => '',
            'status_description' => '',
            'remarks' => '',
            'location' => '',
            'updated_date' => '',
            'ref_awb' => ''
        ];
        $response = $ecom->_TrackOrderEcom($awbNumber);
        file_put_contents("xml_response.xml",$response->body());
        $object = simplexml_load_file("xml_response.xml");
        $responseData = self::XmlToArray($object);
        if(isset($responseData['ecomexpress-objects']['object']['field'])){
            $current = $responseData['ecomexpress-objects']['object']['field'];
            foreach ($current as $c){
                if($c['@name'] == 'reason_code_number')
                    $trackingStatus['status_code'] = $c['text'] ?? "";
                if($c['@name'] == 'status')
                    $trackingStatus['status'] = $c['text'] ?? "";
                if($c['@name'] == 'tracking_status'){
                    $trackingStatus['status_description'] = $c['text'] ?? "";
                    $trackingStatus['tracking_status'] = $c['text'] ?? "";
                }
                if($c['@name'] == 'last_update_datetime')
                    $trackingStatus['updated_date'] = $c['text'] ?? "";
                if($c['@name'] == 'current_location_name')
                    $trackingStatus['location'] = $c['text'] ?? "";
                if($c['@name'] == 'ref_awb')
                    $trackingStatus['ref_awb'] = $c['text'] ?? "";
            }
        }
        if($trackingStatus['awb_number'] !="" && $trackingStatus['status_code'] != ""){
            $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
            $newAwb = $trackingStatus['ref_awb'] ?? null;
            if(!empty($newAwb) && $newAwb != "None" && ($order->alternate_awb_number == null || $order->alternate_awb_number == 'None')){
                self::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['alternate_awb_number' => $newAwb]);
                self::PushChannelStatus($order,'rto_initiated',$trackingStatus['updated_date']);
            }
            if(!empty($order_tracking)){
                if($order_tracking->status_code != $trackingStatus['status_code']){
                    self::HandleEcomExpressResponse($order->awb_number,$trackingStatus,$order);
                    $returnValue = true;
                }
            }else{
                self::HandleEcomExpressResponse($order->awb_number,$trackingStatus,$order);
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    // Handle Ecom Express Response
    public static function HandleEcomExpressResponse($awb,$trackingStatus,$o){
        switch($trackingStatus['status_code']) {
            case 21002:
                $sellerData = Seller::find($o->seller_id);
                MyUtility::PerformCancellation($sellerData, $o);
                self::PushChannelStatus($o, 'cancelled');
                break;
            case '013':
            case '1220':
            case '1230':
            case '1310':
            case '1320':
            case '1330':
            case '1340':
            case '1350':
            case '1360':
            case '1370':
            case '1380':
            case '1390':
            case '1400':
            case '1410':
            case '1420':
            case '1430':
            case '011':
            case '014':
            case '326':
                //pickup scheduled
                Order::where('id', $o->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                self::PushChannelStatus($o, 'pickup_scheduled');
                break;
            case '0011':
            case '1260':
            case '24001':
            case '002':
            case '127':
            case '400':
                //'picked up
                if ($o->status == 'manifested' || $o->status == 'pickup_scheduled'){
                    Order::where('id', $o->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => date('Y-m-d H:i:s', strtotime($trackingStatus['updated_date']))]);
                    self::PushChannelStatus($o, 'picked_up', date('Y-m-d H:i:s', strtotime($trackingStatus['updated_date'])));
                    self::CheckAndSendSMS($o);
                }
                break;
            case '82':
            case '006':
                //out for delivery
                if ($o->rto_status != 'y') {
                    if ($o->ndr_status == 'y' && $trackingStatus['updated_date'] != $o->ndr_status_date) {
                        //make attempt here
                        $attempt = [
                            'seller_id' => $o->seller_id,
                            'order_id' => $o->id,
                            'raised_date' => date('Y-m-d', strtotime($o->ndr_status_date)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'Ecom Express',
                            'reason' => $o->reason_for_ndr,
                            'action_status' => 'requested',
                            'remark' => 'requested',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        //Ndrattemps::create($attempt);
                        Order::where('id', $o->id)->update(['ndr_status_date' => date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date']))]);
                    }
                }
                Order::where('id', $o->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($o,'out_for_delivery',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                self::CheckAndSendSMS($o);
                break;
            case '333':
                //lost
                Order::where('id', $o->id)->update(['status' => 'lost']);
                self::PushChannelStatus($o,'lost',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                break;
            case '303':
            case '309':
            case '308':
            case '306':
            case '305':
            case '229':
            case '301':
            case '003':
            case '004':
            case '005':
            case '304':
            case '100':
            case '83':
            case '307':
            case '207':
            case '230':
            case '101':
            case '205':
            case '312':
            case '313':
            case '314':
            case '315':
            case '316':
            case '235':
            case '20701':
            case '240':
            case '24002':
            case '24003':
            case '30201':
                //in transit
                Order::where('id', $o->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($o,'in_transit',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                break;
            case '302':
            case '888':
                //damaged
                Order::where('id', $o->id)->update(['status' => 'damaged']);
                self::PushChannelStatus($o,'damaged',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                break;
            case '300':
            case '228':
            case '227':
            case '226':
            case '225':
            case '224':
            case '223':
            case '222':
            case '221':
            case '220':
            case '219':
            case '218':
            case '217':
            case '216':
            case '215':
            case '214':
            case '213':
            case '212':
            case '211':
            case '210':
            case '209':
            case '208':
            case '203':
            case '201':
            case '200':
            case '331':
            case '666':
            case '231':
            case '232':
            case '233':
            case '234':
            case '1224':
            case '1225':
            case '428':
            case '236':
            case '334':
            case '30401':
            case '30402':
            case '30403':
            case '30404':
            case '30405':
            case '30406':
            case '22701':
            case '88801':
            case '88802':
            case '88803':
            case '88804':
            case '20801':
            case '21001':
            case '21003':
            case '21004':
            case '21501':
            case '21502':
            case '21503':
            case '21701':
            case '22101':
            case '22104':
            case '22105':
            case '22107':
            case '22106':
            case '22103':
            case '22102':
            case '22301':
            case '22303':
            case '22702':
            case '22801':
            case '22901':
            case '23101':
            case '23102':
            case '23103':
            case '23401':
            case '23402':
            case '22902':
            case '22903':
            case '22703':
            case '12241':
            case '12242':
            case '12243':
            case '12244':
            case '12245':
            case '12246':
            case '24203':
            case '12247':
            case '23201':
            case '23202':
            case '20001':
            case '20002':
                //handle NDR
                if ($o->rto_status != 'y') {
                    //Order::where('id', $o->id)->update(['status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingStatus['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $trackingStatus['updated_date']]);
                    $ndrRaisedDate = date('Y-m-d H:i:s');
                    Order::where('id', $o->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingStatus['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $trackingStatus['updated_date']]);
                    $attempt = [
                        'seller_id' => $o->seller_id,
                        'order_id' => $o->id,
                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                        'raised_time' => date('H:i:s'),
                        'action_by' => 'Ecom Express',
                        'reason' => $trackingStatus['status'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                    self::PushChannelStatus($o,'ndr',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                }
                break;
            case '77':
            case '777':
            case '206':
                self::RTOOrder($o->id);
                self::PushChannelStatus($o,'rto_initiated',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                break;
            case '999':
            case '204':
                //delivered
                $delivery_date = date('Y-m-d', strtotime($trackingStatus['updated_date']));
                Order::where('id', $o->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                if ($o->order_type == 'cod' && $o->o_type=='forward' && $o->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $o->seller_id,
                        'order_id' => $o->id,
                        'amount' => $o->invoice_amount,
                        'type' => 'c',
                        'datetime' => $delivery_date,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $o->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($o,'delivered',date('Y-m-d H:i:s',strtotime($trackingStatus['updated_date'])));
                self::CheckAndSendSMS($o);
                break;
            default:
                $missStatus = [
                    'order_id' => $o->id,
                    'courier_keyword' => $o->courier_partner,
                    'status' => $trackingStatus['status_code'],
                    'status_description' => $trackingStatus['status_description'],
                    'json' => json_encode($trackingStatus),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        unset($trackingStatus['ref_awb']);
        $trackingStatus['created_at'] = date('Y-m-d H:i:s');
        OrderTracking::create($trackingStatus);
    }
    public static function XmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(),   //array of xml tag names which should always become arrays
            'autoArray' => true,        //only create arrays for tags which appear more than once
            'textContent' => 'text',       //key used for the text content of elements
            'autoText' => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch' => false,       //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = self::XmlToArray($childXml, $options);
                list($childTagName, $childProperties) = self::AltEach($childArray);

                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }
    public static function AltEach(&$data)
    {
        $key = key($data);
        $ret = ($key === null)? false: [$key, current($data), 'key' => $key, 'value' => current($data)];
        next($data);
        return $ret;
    }

    // Track Order Bluedart
    public static function TrackOrderBlueDart($order) {
        $returnValue = false;
        $blueDart = new BluedartRest('NSE');
        $awbNumber = ($order->rto_status == 'y' && !empty($order->alternate_awb_number)) ? $order->alternate_awb_number : $order->awb_number;
        $res = $blueDart->trackOrder([
            'awb' => $awbNumber,
            'numbers' => $awbNumber
        ]);
        $trackingData = (array) @$res->Shipment ?? [];

        $scans = (array) @$trackingData['Scans'] ?? [];
        $scanDetail = (array) @$scans['ScanDetail'] ?? [];
        $scan = (array) @$scanDetail[0] ?? [];
        $trackingData['Scan'] = $scan['Scan'] ?? null;

        if(!empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5){
            Order::where('id',$order->id)->update(['alternate_awb_number' => $trackingData['NewWaybillNo']]);
            self::RTOOrder($order->id);
            self::PushChannelStatus($order,'rto_initiated',date('Y-m-d',strtotime($scan['ScanDate']))." ".date('H:i:s',strtotime($scan['ScanTime'])));
        }


        $edd = $trackingData['ExpectedDeliveryDate'] ?? null;
        if(!empty($edd)) {
            try{
                Order::where('id', $order->id)->update(['expected_delivery_date' => date('Y-m-d', strtotime($edd))]);
            }catch (Exception $e){}
        }
        $trackingData['ScanCode'] = $scan['ScanCode'] ?? null;
        $trackingData['ScanType'] = $scan['ScanType'] ?? null;
        $trackingData['ScanGroupType'] = $scan['ScanGroupType'] ?? null;
        $trackingData['ScannedLocation'] = $scan['ScannedLocation'] ?? null;
        $trackingData['ScannedLocationCode'] = $scan['ScannedLocationCode'] ?? null;
        $trackingData['ScanDate'] = $scan['ScanDate'] ?? null;
        $trackingData['ScanTime'] = $scan['ScanTime'] ?? null;
        if(!empty($trackingData['ScanCode']) && !empty($trackingData['ScanGroupType'])) {
            $statusCode = $trackingData['ScanCode'] . '-' . $trackingData['ScanGroupType'];
            $dateTime = date('Y-m-d', strtotime($trackingData['ScanDate'])) . " " . date('H:i:s', strtotime($trackingData['ScanTime']));
            if (!empty($trackingData)) {
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $statusCode) {
                        switch ($statusCode) {
                            case '508-T':
                            case '509-T':
                            case '510-T':
                            case '511-T':
                            case '512-T':
                            case '513-T':
                            case '592-T':
                            case '593-T':
                                $selleData = Seller::find($order->id);
                                MyUtility::PerformCancellation($selleData, $order);
                                break;
                            case '544-T':
                            case '351-T':
                            case '514-T':
                            case '531-T':
                            case '532-T':
                            case '533-T':
                            case '534-T':
                            case '535-T':
                            case '536-T':
                            case '537-T':
                            case '539-T':
                            case '540-T':
                            case '541-T':
                            case '542-T':
                            case '555-T':
                            case '591-T':
                                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                                self::PushChannelStatus($order, 'pickup_scheduled', $dateTime);
                                break;
                            case '015-S':
                            case '538-T':
                                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $dateTime]);
                                self::PushChannelStatus($order, 'picked_up', $dateTime);
                                self::CheckAndSendSMS($order);
                                break;
                            case '035-T':
                            case '002-T':
                            case '002-S':
                                if ($order->rto_status != 'y') {
                                    if ($order->ndr_status == 'y' && $dateTime != $order->ndr_status_date) {
                                        //make attempt here
//                                        $attempt = [
//                                            'seller_id' => $order->seller_id,
//                                            'order_id' => $order->id,
//                                            'raised_date' => $dateTime,
//                                            'raised_time' => $dateTime,
//                                            'action_by' => 'bluedart',
//                                            'reason' => $order->reason_for_ndr,
//                                            'action_status' => 'requested',
//                                            'remark' => 'requested',
//                                            'u_address_line1' => 'new address line 1',
//                                            'u_address_line2' => 'new address line 2',
//                                            'updated_mobile' => ''
//                                        ];
//                                        Ndrattemps::create($attempt);
                                        Order::where('id', $order->id)->update(['ndr_status_date' => $dateTime]);
                                    }
                                }
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                self::PushChannelStatus($order, 'out_for_delivery', $dateTime);
                                self::CheckAndSendSMS($order);
                                break;
                            case '001-S':
                            case '001-T':
                            case '003-S':
                            case '033-T':
                            case '174-T':
                            case '065-T':
                            case '221-T':
                            case '121-T':
                            case '020-S':
                                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                self::PushChannelStatus($order, 'in_transit', $dateTime);
                                break;
                            case '002-RT':
                            case '035-RT':
                                if ($order->o_type == "forward" && !empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
                                    self::PushChannelStatus($order, 'rto_initiated', $dateTime);
                                }
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                break;
                            case '000-T':
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $dateTime]);
                                if ($order->order_type == 'cod') {
                                    $data = array(
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'amount' => $order->invoice_amount,
                                        'type' => 'c',
                                        'datetime' => date('Y-m-d H:i:s'),
                                        'description' => 'Order COD Amount Credited',
                                        'redeem_type' => 'o',
                                    );
                                    COD_transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                                }
                                self::PushChannelStatus($order, 'delivered', $dateTime);
                                self::CheckAndSendSMS($order);
                                break;
                            case '188-RT':
                            case '000-RT':
                            case '105-T':
                                if ($order->o_type == "forward" && !empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
//                                    self::PushChannelStatus($order, 'rto_initiated', $dateTime);
                                }
                                $delivery_date = date('Y-m-d', strtotime($trackingData['ScanDate']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                self::PushChannelStatus($order, 'delivered', $dateTime);
                                break;
                            case '188-T':
                                self::RTOOrder($order->id);
                                $delivery_date = date('Y-m-d', strtotime($trackingData['ScanDate']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                self::PushChannelStatus($order, 'delivered', $dateTime);
                                break;
                            case '008-T':
                            case '009-T':
                            case '010-T':
                            case '011-T':
                            case '012-T':
                            case '013-T':
                            case '014-T':
                            case '015-T':
                            case '019-T':
                            case '034-T':
                            case '044-T':
                            case '048-T':
                            case '049-T':
                            case '057-T':
                            case '058-T':
                            case '071-T':
                            case '076-T':
                            case '500-T':
                            case '501-T':
                            case '502-T':
                            case '503-T':
                            case '504-T':
                            case '505-T':
                            case '506-T':
                            case '507-T':
                            case '003-T':
                            case '004-T':
                            case '005-T':
                            case '007-T':
                            case '017-T':
                            case '020-T':
                            case '024-T':
                            case '029-T':
                            case '032-T':
                            case '036-T':
                            case '037-T':
                            case '046-T':
                            case '052-T':
                            case '054-T':
                            case '055-T':
                            case '059-T':
                            case '060-T':
                            case '062-T':
                            case '066-T':
                            case '067-T':
                            case '068-T':
                            case '073-T':
                            case '077-T':
                            case '078-T':
                            case '080-T':
                            case '095-T':
                            case '096-T':
                            case '097-T':
                            case '099-T':
                            case '100-T':
                            case '101-T':
                            case '103-T':
                            case '106-T':
                            case '107-T':
                            case '110-T':
                            case '111-T':
                            case '130-T':
                            case '133-T':
                            case '136-T':
                            case '137-T':
                            case '139-T':
                            case '142-T':
                            case '143-T':
                            case '145-T':
                            case '146-T':
                            case '147-T':
                            case '148-T':
                            case '150-T':
                            case '151-T':
                            case '152-T':
                            case '154-T':
                            case '175-T':
                            case '178-T':
                            case '201-T':
                            case '202-T':
                            case '203-T':
                            case '204-T':
                            case '205-T':
                            case '206-T':
                            case '207-T':
                            case '208-T':
                            case '209-T':
                            case '210-T':
                            case '211-T':
                            case '212-T':
                            case '213-T':
                            case '214-T':
                            case '215-T':
                            case '070-T':
                            case '030-T':
                            case '301-T':
                            case '312-T':
                            case '353-T':
                            case '303-T':
                            case '308-T':
                            case '309-T':
                            case '313-T':
                            case '305-T':
                            case '314-T':
                            case '306-T':
                            case '311-T':
                            case '307-T':
                            case '166-T':
                            case '302-T':
                            case '304-T':
                            case '310-T':
                            case '315-T':
                            case '217-T':
                            case '218-T':
                            case '187-T':
                            case '185-T':
                            case '140-T':
                            case '179-T':
                            case '180-T':
                            case '181-T':
                            case '182-T':
                            case '183-T':
                            case '777-T':
                            case '219-T':
                            case '223-T':
                            case '189-T':
                            case '224-T':
                            case '222-T':
                            case '316-T':
                            case '220-T':
                            case '120-T':
                            case '042-T':
                            case '056-T':
                            case '190-T':
                            case '024-S':
                                if ($order->rto_status != 'y') {
                                    Order::where('id', $order->id)->update(['ndr_raised_time' => $dateTime, 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['Status'], 'ndr_action' => 'pending', 'ndr_status_date' => $trackingData['ScanDate']]);
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => $dateTime,
                                        'raised_time' => $dateTime,
                                        'action_by' => 'bluedart',
                                        'reason' => $trackingData['Status'],
                                        'action_status' => 'pending',
                                        'remark' => 'pending',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    self::PushChannelStatus($order, 'ndr', $dateTime);
                                }
                                break;
                            case '021-T':
                            case '129-T':
                            case '186-T':
                                Order::where('id', $order->id)->update(['status' => 'lost']);
                                self::PushChannelStatus($order, 'lost', $dateTime);
                                break;
                            case '129-RT':
                                if ($order->o_type == "forward" && !empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
//                                    self::PushChannelStatus($order, 'rto_initiated', $dateTime);
                                }
                                Order::where('id', $order->id)->update(['status' => 'lost']);
                                self::PushChannelStatus($order, 'lost', $dateTime);
                                break;
                            case '019-S':
                            case '503-S':
                            case '016-T':
                            case '074-T':
                            case '104-T':
                            case '123-T':
                            case '016-RT':
                            case '074-RT':
                            case '104-RT':
                            case '123-RT':
                            case '351-RT':
                            case '027-T':
                            case '132-T':
                            case '027-RT':
                            case '132-RT':
                            case '105-RT':
                            case '223-RT':
                                if(!empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
                                    self::PushChannelStatus($order, 'rto_initiated', $dateTime);
                                }
                                break;
                            case '025-T':
                                Order::where('id', $order->id)->update(['status' => 'damaged']);
                                self::PushChannelStatus($order, 'damaged', $dateTime);
                                break;
                            case '025-RT':
                                if(!empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
                                    Order::where('id', $order->id)->update(['status', 'damaged']);
                                    self::PushChannelStatus($order, 'rto_initiated', $dateTime);
                                }
                                break;
                            case '001-RT':
                            case '003-RT':
                            case '004-RT':
                            case '005-RT':
                            case '008-RT':
                            case '009-RT':
                            case '010-RT':
                            case '011-RT':
                            case '012-RT':
                            case '013-RT':
                            case '014-RT':
                            case '015-RT':
                            case '017-RT':
                            case '019-RT':
                            case '020-RT':
                            case '021-RT':
                            case '024-RT':
                            case '029-RT':
                            case '032-RT':
                            case '034-RT':
                            case '036-RT':
                            case '037-RT':
                            case '044-RT':
                            case '045-RT':
                            case '046-RT':
                            case '048-RT':
                            case '049-RT':
                            case '050-RT':
                            case '052-RT':
                            case '054-RT':
                            case '055-RT':
                            case '057-RT':
                            case '058-RT':
                            case '059-RT':
                            case '060-RT':
                            case '062-RT':
                            case '066-RT':
                            case '067-RT':
                            case '068-RT':
                            case '071-RT':
                            case '073-RT':
                            case '076-RT':
                            case '077-RT':
                            case '078-RT':
                            case '080-RT':
                            case '095-RT':
                            case '096-RT':
                            case '097-RT':
                            case '099-RT':
                            case '100-RT':
                            case '101-RT':
                            case '103-RT':
                            case '106-RT':
                            case '107-RT':
                            case '110-RT':
                            case '111-RT':
                            case '130-RT':
                            case '133-RT':
                            case '135-RT':
                            case '136-RT':
                            case '137-RT':
                            case '139-RT':
                            case '142-RT':
                            case '143-RT':
                            case '145-RT':
                            case '146-RT':
                            case '147-RT':
                            case '148-RT':
                            case '150-RT':
                            case '151-RT':
                            case '152-RT':
                            case '154-RT':
                            case '175-RT':
                            case '178-RT':
                            case '186-RT':
                            case '201-RT':
                            case '202-RT':
                            case '203-RT':
                            case '204-RT':
                            case '205-RT':
                            case '206-RT':
                            case '207-RT':
                            case '208-RT':
                            case '209-RT':
                            case '210-RT':
                            case '211-RT':
                            case '212-RT':
                            case '213-RT':
                            case '214-RT':
                            case '215-RT':
                            case '070-RT':
                            case '030-RT':
                            case '301-RT':
                            case '312-RT':
                            case '353-RT':
                            case '303-RT':
                            case '308-RT':
                            case '309-RT':
                            case '313-RT':
                            case '305-RT':
                            case '311-RT':
                            case '314-RT':
                            case '306-RT':
                            case '307-RT':
                            case '166-RT':
                            case '302-RT':
                            case '304-RT':
                            case '310-RT':
                            case '315-RT':
                            case '217-RT':
                            case '218-RT':
                            case '187-RT':
                            case '185-RT':
                            case '140-RT':
                            case '179-RT':
                            case '180-RT':
                            case '181-RT':
                            case '182-RT':
                            case '183-RT':
                            case '777-RT':
                            case '219-RT':
                            case '189-RT':
                            case '224-RT':
                            case '222-RT':
                            case '316-RT':
                            case '220-RT':
                            case '120-RT':
                            case '042-RT':
                            case '056-RT':
                            case '190-RT':
                            case '033-RT':
                            case '174-RT':
                            case '065-RT':
                            case '026-RT':
                            case '221-RT':
                            case '121-RT':
                                if(!empty($trackingData['NewWaybillNo']) && strlen($trackingData['NewWaybillNo']) > 5) {
                                    self::RTOOrder($order->id);
                                    Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                    self::PushChannelStatus($order, 'in_transit', $dateTime);
                                }
                                break;
                            default:
                                $missStatus = [
                                    'order_id' => $order->id,
                                    'courier_keyword' => $order->courier_partner,
                                    'status' => $statusCode,
                                    'status_description' => $trackingData['Scan'],
                                    'json' => json_encode($trackingData),
                                    'created_at' => date('Y-m-d h:i:s')
                                ];
                                CourierMissStatusCode::create($missStatus);
                                break;
                        }
                        $data = [
                            "awb_number" => $order->awb_number,
                            "status_code" => $statusCode,
                            "status" => $trackingData['Status'] ?? "",
                            "status_description" => $trackingData['Scan'],
                            "remarks" => $trackingData['Scan'],
                            "location" => $trackingData['ScannedLocation'],
                            "updated_date" => $dateTime,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                        $returnValue = true;
                    }
                } else {
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $statusCode,
                        "status" => $statusCode,
                        "status_description" => $trackingData['Scan'],
                        "remarks" => $trackingData['Scan'],
                        "location" => $trackingData['ScannedLocation'],
                        "updated_date" => $dateTime,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                }
            }
        }
        return $returnValue;
    }

    // Track Order Amazon SWA
    public static function TrackOrderAmazonSWA($awb,$order){
        $returnValue = false;
        $amazonSWA = new AmazonSWA();
        $trackingDetails = $amazonSWA->getTracking($awb);
        $lastStatus = OrderTracking::where('awb_number',$awb)->orderBy('id','desc')->first();
        if(isset($trackingDetails['payload']['eventHistory'][0])){
            $latestStatus = $trackingDetails['payload']['eventHistory'][count($trackingDetails['payload']['eventHistory'])-1];
            if($trackingDetails['payload']['promisedDeliveryDate'] != "")
                $expectedDeliveryDate = date('Y-m-d H:i:s',strtotime($trackingDetails['payload']['promisedDeliveryDate']));
            else
                $expectedDeliveryDate = date('Y-m-d H:i:s',strtotime('+5 days'));

            if(!isset($latestStatus['eventCode']))
                return true;

            if(empty($lastStatus) || $lastStatus->status_code != $latestStatus['eventCode']){
                switch($latestStatus['eventCode']){
                    case 'ReadyForReceive':
                        Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                        self::PushChannelStatus($order,'pickup_scheduled',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        break;
                    case 'PickupDone':
                        Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => date('Y-m-d H:i:s',strtotime($latestStatus['eventTime']))]);
                        self::PushChannelStatus($order,'picked_up',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        self::CheckAndSendSMS($order);
                        break;
                    case 'Rejected':
                        //self::RTOOrder($order->id);
                        break;
                    case 'Delivered':
                        $delivery_date = date('Y-m-d', strtotime($latestStatus['eventTime']));
                        Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                        if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                            $data = array(
                                'seller_id' => $order->seller_id,
                                'order_id' => $order->id,
                                'amount' => $order->invoice_amount,
                                'type' => 'c',
                                'datetime' => $delivery_date,
                                'description' => 'Order COD Amount Credited',
                                'redeem_type' => 'o',
                            );
                            COD_transactions::create($data);
                            Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                        }
                        self::PushChannelStatus($order,'delivered',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        self::CheckAndSendSMS($order);
                        break;
                    case 'Departed':
                    case 'ArrivedAtCarrierFacility':
                        Order::where('id', $order->id)->update(['status' => 'in_transit']);
                        self::PushChannelStatus($order,'in_transit',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        break;
                    case 'Lost':
                    case 'Destroyed':
                        Order::where('id', $order->id)->update(['status' => 'lost']);
                        self::PushChannelStatus($order,'lost',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        break;
                    case 'OutForDelivery':
                        if ($order->rto_status != 'y') {
                            if ($order->ndr_status == 'y' && $latestStatus['eventTime'] != $order->ndr_status_date) {
                                //make attempt here
//                                $attempt = [
//                                    'seller_id' => $order->seller_id,
//                                    'order_id' => $order->id,
//                                    'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                    'raised_time' => date('H:i:s'),
//                                    'action_by' => 'Amazon SWA',
//                                    'reason' => $order->reason_for_ndr,
//                                    'action_status' => 'requested',
//                                    'remark' => 'requested',
//                                    'u_address_line1' => 'new address line 1',
//                                    'u_address_line2' => 'new address line 2',
//                                    'updated_mobile' => ''
//                                ];
                                //Ndrattemps::create($attempt);
                                Order::where('id', $order->id)->update(['ndr_status_date' => $latestStatus['eventTime']]);
                            }
                        }
                        Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                        self::PushChannelStatus($order,'out_for_delivery',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        self::CheckAndSendSMS($order);
                        break;
                    case 'Undeliverable':
                        if ($order->rto_status != 'y') {
                            //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $latestStatus['eventCode'], 'ndr_action' => 'pending', 'ndr_status_date' => $latestStatus['eventTime']]);
                            $ndrRaisedDate = date('Y-m-d H:i:s');
                            Order::where('id', $order->id)->update(['ndr_raised_time'=>$ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $latestStatus['eventCode'], 'ndr_action' => 'pending', 'ndr_status_date' => $latestStatus['eventTime']]);
                            $attempt = [
                                'seller_id' => $order->seller_id,
                                'order_id' => $order->id,
                                'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                'raised_time' => date('H:i:s'),
                                'action_by' => 'Amazon SWA',
                                'reason' => $latestStatus['eventCode'],
                                'action_status' => 'pending',
                                'remark' => 'pending',
                                'u_address_line1' => 'new address line 1',
                                'u_address_line2' => 'new address line 2',
                                'updated_mobile' => ''
                            ];
                            Ndrattemps::create($attempt);
                            self::PushChannelStatus($order,'ndr',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        }
                        break;
                    case 'PickupCancelled':
//                        Order::where('id', $order->id)->update(['status' => 'cancelled']);
//                        self::PushChannelStatus($order,'cancelled',date('Y-m-d H:i:s',strtotime($latestStatus['eventTime'])));
                        break;
                    default:
                        $missStatus = [
                            'order_id' => $order->id,
                            'courier_keyword' => $order->courier_partner,
                            'status' => $latestStatus['eventCode'],
                            'status_description' => $latestStatus['eventCode'],
                            'json' => json_encode($trackingDetails),
                            'created_at' => date('Y-m-d h:i:s')
                        ];
                        CourierMissStatusCode::create($missStatus);
                        break;
                }
                $data = [
                    "awb_number" => $awb,
                    "status_code" => $latestStatus['eventCode'],
                    "status" => $latestStatus['eventCode'],
                    "status_description" => $latestStatus['eventCode'],
                    "remarks" =>  $latestStatus['eventCode'],
                    "location" =>  $latestStatus['location']['city'] ?? "",
                    "updated_date" => $latestStatus['eventTime'],
                    "updated_by" => $order->courier_partner,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                if($expectedDeliveryDate != ""){
                    Order::where('id',$order->id)->update(['expected_delivery_date' => $expectedDeliveryDate]);
                }
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    // Track Order Smartr
    public static function TrackOrderSmartr($order){
        $returnValue = false;
        $smartr = new Smartr();
        $trackingData = $smartr->getTracking($order);
        if(count($trackingData['Table6']) == 0)
            return false;
        $lastActivity = $trackingData['Table6'][count($trackingData['Table6'])-1]['LastActivity'] ?? "";
        $reasonNDR = $trackingData['Table6'][count($trackingData['Table6'])-1]['DeliveryAttemptReason'] ?? "";
        $lastDescription = $trackingData['Table6'][count($trackingData['Table6'])-1]['Description'] ?? "";
        // dd($lastActivity,$reasonNDR,$trackingData);
        $tracking_data = $trackingData['Table12'];
        if (count($tracking_data) > 0) {
            $shipment_summary = $tracking_data[count($tracking_data)-1];
            $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
            $datetime = \DateTime::createFromFormat("d/m/Y H:i", $shipment_summary['eventdatetime'])->format("Y-m-d");
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['Milestone']) {
                    switch ($shipment_summary['Milestone']) {
                        case 'Booked':
                            Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                            self::PushChannelStatus($order,'pickup_scheduled',$datetime);
                            break;
                        case 'Door Picked':
                            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $datetime]);
                            self::PushChannelStatus($order,'picked_up',$datetime);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'OutForDelivery':
                            if ($order->rto_status != 'y') {
                                if ($order->ndr_status == 'y' && $datetime != $order->ndr_status_date) {
                                    //make attempt here
//                                    $attempt = [
//                                        'seller_id' => $order->seller_id,
//                                        'order_id' => $order->id,
//                                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                        'raised_time' => date('H:i:s'),
//                                        'action_by' => 'Smartr',
//                                        'reason' => $order->reason_for_ndr,
//                                        'action_status' => 'requested',
//                                        'remark' => 'requested',
//                                        'u_address_line1' => 'new address line 1',
//                                        'u_address_line2' => 'new address line 2',
//                                        'updated_mobile' => ''
//                                    ];
                                    //Ndrattemps::create($attempt);
                                    Order::where('id', $order->id)->update(['ndr_status_date' => $datetime]);
                                }
                            }
                            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                            self::PushChannelStatus($order,'out_for_delivery',$datetime);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'Accepted':
                        case 'Voided':
                        case 'Departed':
                        case 'Arrived':
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order,'in_transit',$datetime);
                            break;
                        case 'Delivered':
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
                            if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                                $data = array(
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'amount' => $order->invoice_amount,
                                    'type' => 'c',
                                    'datetime' => $datetime ?? date('Y-m-d'),
                                    'description' => 'Order COD Amount Credited',
                                    'redeem_type' => 'o',
                                );
                                COD_transactions::create($data);
                                Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                            }
                            self::PushChannelStatus($order,'delivered',$datetime);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'Door Delivered':
                            if($order->o_type == "forward")
                                self::RTOOrder($order->id);
                            //Order::where('awb_number', $order->awb_number)->update(['status' => 'delivered']);
                            // $delivery_date = date('Y-m-d', strtotime($shipment_summary['eventdatetime']));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
                            self::PushChannelStatus($order,'delivered',$datetime);
                            break;
                        case 'Delivery Attempt':
                            if ($order->rto_status != 'y') {
                                //Order::where('awb_number', $order->awb_number)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $reasonNDR, 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                                $ndrRaisedDate = date('Y-m-d H:i:s');
                                Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $reasonNDR, 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                                $attempt = [
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                    'raised_time' => date('H:i:s'),
                                    'action_by' => 'Smartr',
                                    'reason' => $reasonNDR,
                                    'action_status' => 'pending',
                                    'remark' => 'pending',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);
                                self::PushChannelStatus($order,'ndr',$datetime);
                            }
                            break;
                        case 'RTO Locked':
                        case 'Return to Shipper':
                            self::RTOOrder($order->id);
                            self::PushChannelStatus($order,'rto_initated',$datetime);
                            break;
                        case 'Returned to Shipper':
                            $newAwb = $shipment_summary['ULDNO'] ?? "";
                            if($newAwb != "")
                                Order::where('id',$order->id)->update(['alternate_awb_number' => $newAwb]);
                            self::RTOOrder($order->id);
                            self::PushChannelStatus($order,'rto_initated',$datetime);
                            break;
                        default:
                            $missStatus = [
                                'order_id' => $order->id,
                                'courier_keyword' => $order->courier_partner,
                                'status' => $shipment_summary['Milestone'],
                                'status_description' => $lastActivity,
                                'json' => json_encode($tracking_data),
                                'created_at' => date('Y-m-d h:i:s')
                            ];
                            CourierMissStatusCode::create($missStatus);
                            break;
                    }
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $shipment_summary['Milestone'],
                        "status" => $lastActivity,
                        "status_description" => $lastActivity,
                        "remarks" =>  $lastActivity,
                        "location" =>  $lastDescription,
                        "updated_date" => $datetime,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                    // if (isset($expected_date))
                    //     Order::where('awb_number', $order->awb_number)->update(['expected_delivery_date' => $expected_date]);
                }
            } else {
                $data = [
                    "awb_number" => $order->awb_number,
                    "status_code" => $shipment_summary['Milestone'],
                    "status" => $lastActivity,
                    "status_description" => $lastActivity,
                    "remarks" =>  $lastActivity,
                    "location" =>  $lastDescription,
                    "updated_date" => $datetime,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    // Track Order Bombax
    public static function TrackBombaxOrder($order)
    {
        $returnValue = false;
        $bombax = new Bombax();
        $awb_data = $bombax->trackOrder($order->awb_number);
        $tracking_data = $awb_data;

        if (isset($tracking_data['ConsignmentHistoryMSTrack']) && !empty($tracking_data['ConsignmentHistoryMSTrack'])) {
            $shipment_summary = $tracking_data['ConsignmentHistoryMSTrack'][0];
            $order_tracking = OrderTracking::where('awb_number', $tracking_data['ConsignmentDetailsMSTrack']['ConsignmentNo'])->orderBy('id', 'desc')->first();
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['StatusCode']) {
                    switch ($shipment_summary['StatusCode']) {
                        case 'PRE':
                            Order::where('id', $order->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y' ]);
                            self::PushChannelStatus($order, 'pickup_scheduled',$shipment_summary['CurrentStatusDate']);
                            break;
                        case 'SP':
                            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $shipment_summary['CurrentStatusDate']]);
                            self::PushChannelStatus($order, 'picked_up',$shipment_summary['CurrentStatusDate']);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'IT':
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order, 'in_transit',$shipment_summary['CurrentStatusDate']);
                            break;
                        case 'OFD':
                            if ($order->rto_status != 'y') {
                                if ($order->ndr_status == 'y' && $shipment_summary['CurrentStatusDate'] != $order->ndr_status_date) {
                                    //make attempt here
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'Bombax',
                                        'reason' => $order->reason_for_ndr,
                                        'action_status' => 'requested',
                                        'remark' => 'requested',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    Order::where('id', $order->id)->update(['ndr_status_date' => $shipment_summary['CurrentStatusDate']]);
                                }
                            }
                            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                            self::PushChannelStatus($order, 'out_for_delivery',$shipment_summary['CurrentStatusDate']);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'DLY':
                            $delivery_date = date('Y-m-d', strtotime($shipment_summary['CurrentStatusDate']));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            if ($order->order_type == 'cod') {
                                $data = array(
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'amount' => $order->invoice_amount,
                                    'type' => 'c',
                                    'datetime' => $delivery_date,
                                    'description' => 'Order COD Amount Credited',
                                    'redeem_type' => 'o',
                                );
                                COD_transactions::create($data);
                                Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                            }
                            self::PushChannelStatus($order, 'delivered',$shipment_summary['CurrentStatusDate']);
                            self::CheckAndSendSMS($order);
                            break;
                        case 'RTO':
                            self::RTOOrder($order->id);
                            Order::where('id', $order->id)->update(['status' => 'rto_initated', 'rto_status' => 'y']);
                            self::PushChannelStatus($order, 'rto_initated',$shipment_summary['CurrentStatusDate']);
                            break;
                        case 'RIT':
                            if ($order->o_type == "forward")
                                self::RTOOrder($order->id);
                            Order::where('id', $order->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($order, 'in_transit',$shipment_summary['CurrentStatusDate']);
                            break;
                        case 'UD':
                            if ($order->rto_status != 'y') {
                                //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $tracking_data['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['event_time']]);
                                $ndrRaisedDate = date('Y-m-d H:i:s');
                                Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $tracking_data['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['event_time']]);
                                $attempt = [
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                    'raised_time' => date('H:i:s'),
                                    'action_by' => 'Bombax',
                                    'reason' => $tracking_data['status'],
                                    'action_status' => 'pending',
                                    'remark' => 'pending',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);

                                self::PushChannelStatus($order, 'ndr',$shipment_summary['CurrentStatusDate']);
                            }
                            break;
                        case 'DA':
                            Order::where('id', $order->id)->update(['status' => 'damaged']);
                            self::PushChannelStatus($order, 'damaged',$shipment_summary['CurrentStatusDate']);
                            break;
                        case 'LO':
                            Order::where('id', $order->id)->update(['status' => 'lost']);
                            self::PushChannelStatus($order, 'lost',$shipment_summary['CurrentStatusDate']);
                            break;
                        default:
                            $missStatus = [
                                'order_id' => $order->id,
                                'courier_keyword' => $order->courier_partner,
                                'status' => $tracking_data['ConsignmentDetailsMSTrack']['CurrentStatusCode'],
                                'status_description' => $shipment_summary['Reason'],
                                'json' => json_encode($tracking_data),
                                'created_at' => date('Y-m-d h:i:s')
                            ];
                            CourierMissStatusCode::create($missStatus);
                            break;
                    }
                    $data = [
                        "awb_number" => $tracking_data['ConsignmentDetailsMSTrack']['ConsignmentNo'],
                        "status_code" => $tracking_data['ConsignmentDetailsMSTrack']['CurrentStatusCode'],
                        "status" => $tracking_data['ConsignmentDetailsMSTrack']['CurrentStatusName'],
                        "status_description" => $shipment_summary['Reason'],
                        "remarks" =>  $shipment_summary['Remarks'],
                        "location" =>  $shipment_summary['DispatchedOrReceivedLocation'],
                        "updated_date" => $shipment_summary['CurrentStatusDate'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                }
            } else {
                $data = [
                    "awb_number" => $tracking_data['ConsignmentDetailsMSTrack']['ConsignmentNo'],
                    "status_code" => $tracking_data['ConsignmentDetailsMSTrack']['CurrentStatusCode'],
                    "status" => $tracking_data['ConsignmentDetailsMSTrack']['CurrentStatusName'],
                    "status_description" => $shipment_summary['Reason'],
                    "remarks" =>  $shipment_summary['Remarks'],
                    "location" =>  $shipment_summary['DispatchedOrReceivedLocation'],
                    "updated_date" => $shipment_summary['CurrentStatusDate'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    // Track Order Shree Maruti
    public static function TrackMarutiOrder($order)
    {
        if($order->status == 'cancelled' || $order->status == 'delivered' || $order->status == 'shipped' || $order->status == 'pickup_requested'){
            return true;
        }
        $returnValue = false;
        try {
            $maruti = new Maruti();
            $awb_data = $maruti->trackOrder($order->awb_number);
            if($awb_data['success'] == "0") {
                // Tracking data not found
                return;
            }
            $tracking_data = $awb_data['data'] ?? [];

            // check for booked or in transit
            if(!isset($tracking_data['deliveryinfo']) && !isset($tracking_data['travelinginfo'])) {
                // booked
                $tracking_data['deliveryinfo'] = [
                    "StatusName" => "BOOKED",
                    "Reason" => "",
                    "AreaID" => "",
                    "StatusDateTime" => date('Y-m-d H:i:s')
                ];
            } else if(!isset($tracking_data['deliveryinfo'])) {
                // in transit
                $tracking_data['deliveryinfo'] = [
                    "StatusName" => "IN TRANSIT",
                    "Reason" => "",
                    "AreaID" => "",
                    "StatusDateTime" => date('Y-m-d H:i:s')
                ];
            } else {
                $tracking_data['deliveryinfo'] = $tracking_data['deliveryinfo'][count($tracking_data['deliveryinfo'])-1];
            }

            if (isset($tracking_data['deliveryinfo'])) {
                $shipment_summary = $tracking_data['deliveryinfo'];
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $shipment_summary['StatusName']) {
                        switch ($shipment_summary['StatusName']) {
                            case 'BOOKED':
                                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                                self::PushChannelStatus($order, 'pickup_scheduled',$shipment_summary['StatusDateTime']);
                                break;
                            case 'PENDING':
                                if ($order->rto_status != 'y') {
                                    if ($order->ndr_status == 'y' && $shipment_summary['StatusDateTime'] != $order->ndr_status_date) {
                                        //make attempt here
//                                        $attempt = [
//                                            'seller_id' => $order->seller_id,
//                                            'order_id' => $order->id,
//                                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                            'raised_time' => date('H:i:s'),
//                                            'action_by' => 'Shree Maruti',
//                                            'reason' => $order->reason_for_ndr,
//                                            'action_status' => 'requested',
//                                            'remark' => 'requested',
//                                            'u_address_line1' => 'new address line 1',
//                                            'u_address_line2' => 'new address line 2',
//                                            'updated_mobile' => ''
//                                        ];
//                                        //Ndrattemps::create($attempt);
                                        Order::where('id', $order->id)->update(['ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    }
                                }
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                self::PushChannelStatus($order, 'out_for_delivery',$shipment_summary['StatusDateTime']);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'IN TRANSIT':
                                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                self::PushChannelStatus($order, 'in_transit',$shipment_summary['StatusDateTime']);
                                break;
                            case 'C/F TO NEXT DAY':
                                if ($order->rto_status != 'y') {
                                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Reason'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    $ndrRaisedDate = date('Y-m-d H:i:s');
                                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Reason'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'Shree Maruti',
                                        'reason' => $shipment_summary['Reason'],
                                        'action_status' => 'pending',
                                        'remark' => 'pending',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    self::PushChannelStatus($order, 'ndr',$shipment_summary['StatusDateTime']);
                                }
                                break;
                            case 'DELIVERED':
                                $delivery_date = date('Y-m-d', strtotime($shipment_summary['StatusDateTime']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                if ($order->order_type == 'cod') {
                                    $data = array(
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'amount' => $order->invoice_amount,
                                        'type' => 'c',
                                        'datetime' => $delivery_date,
                                        'description' => 'Order COD Amount Credited',
                                        'redeem_type' => 'o',
                                    );
                                    COD_transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                                }
                                self::PushChannelStatus($order, 'delivered',$shipment_summary['StatusDateTime']);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'RETURN':
                                self::RTOOrder($order->id);
                                self::PushChannelStatus($order,'rto_initated',$shipment_summary['StatusDateTime']);
                                break;
                            case 'RTO DELIVERED':
                            case 'RETURN DELIVERED':
                                self::RTOOrder($order->id);
                                // mark shipment as rto delivered
                                $delivery_date = date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                self::PushChannelStatus($order,'delivered',$shipment_summary['StatusDateTime']);
                                break;
                            default:
                                $missStatus = [
                                    'order_id' => $order->id,
                                    'courier_keyword' => $order->courier_partner,
                                    'status' => $shipment_summary['StatusName'],
                                    'status_description' => $shipment_summary['Reason'],
                                    'json' => json_encode($tracking_data),
                                    'created_at' => date('Y-m-d h:i:s')
                                ];
                                CourierMissStatusCode::create($missStatus);
                                break;
                        }
                        $data = [
                            "awb_number" => $order->awb_number,
                            "status_code" => $shipment_summary['StatusName'],
                            "status" => $shipment_summary['StatusName'],
                            "status_description" => $shipment_summary['Reason'],
                            "remarks" =>  $shipment_summary['Reason'],
                            "location" =>  $shipment_summary['AreaID'],
                            "updated_date" => $shipment_summary['StatusDateTime'],
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                        $returnValue = true;
                    }
                } else {
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $shipment_summary['StatusName'],
                        "status" => $shipment_summary['StatusName'],
                        "status_description" => $shipment_summary['Reason'],
                        "remarks" =>  $shipment_summary['Reason'],
                        "location" =>  $shipment_summary['AreaID'],
                        "updated_date" => $shipment_summary['StatusDateTime'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                }
            }
        } catch(Exception $e) {
            // dd($e->getMessage());
        }
        return $returnValue;
    }

    // Track SMC New Order
    // Track Order Shree Maruti Ecom
    public static function TrackSMCNewOrder($order)
    {
        $returnValue = false;
        try {
            if($order->status == 'delivered' || $order->status == 'cancelled')
                return false;
            $trackingData = SMCNew::TrackOrder($order->awb_number);
            if(empty($trackingData['events'])){
                return false;
            }
            $latestTrackingData = $trackingData['events'][0];
            $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
            if(!empty($order_tracking)){
                if($order_tracking->status_code != $latestTrackingData['type']){
                    self::HandleSMCNewOrder($order, $latestTrackingData);
                    return true;
                }
            }else{
                self::HandleSMCNewOrder($order, $latestTrackingData);
                return true;
            }
        } catch(Exception $e) {
            // dd($e->getMessage());
        }
        return $returnValue;
    }

    // Handle SMC Status Codes
    public static function HandleSMCNewOrder($orderData,$trackingData){
        try {
            $eventDateTime = date('Y-m-d H:i:s',($trackingData['event_time']/1000));
            switch ($trackingData['type']) {
                case 'softdata_upload':
                case 'softdata_update':
                case 'not_picked_up':
                case 'out_for_pickup':
                case 'pickup_started':
                case 'out_for_store_pickup':
                case 'pickup_awaited':
                case 'pickup_scheduled':
                    // pickup scheduled
                    ShipmentMovementHelper::PerformPickupScheduled($orderData, $eventDateTime);
                    break;
                case 'pickup_completed':
                    // picked_up
                    ShipmentMovementHelper::PerformPickedUp($orderData, $eventDateTime);
                    break;
                case 'rto_initiated':
                    // rto initiated
                    ShipmentMovementHelper::PerformRTOInitiated($orderData, $eventDateTime);
                    break;
                case 'rto_attempted':
                case 'rto_in_transit':
                case 'rto_inscan_at_hub':
                case 'rto_outfordelivery':
                    // rto in transit
                    ShipmentMovementHelper::PerformRTOInTransit($orderData, $eventDateTime);
                    break;
                case 'rto_delivered':
                    // rto delivered
                    ShipmentMovementHelper::PerformRTODelivered($orderData, $eventDateTime);
                    break;
                case 'added_to_bag':
                case 'assigned_to_hub':
                case 'bag_received':
                case 'consignment_verification':
                case 'customs_clearance_completed':
                case 'handed_in_customs_clearance':
                case 'handover_courier_partner':
                case 'inscan_at_hub':
                case 'intransittohub':
                case 'delay_at_airport':
                case 'on_hold':
                case 'outscan_at_hub':
                case 'reachedathub':
                case 'release_on_hold':
                case 'reschedule':
                case 'returned_at_hub':
                case 'revert_from_delivered':
                case 'revoke_rto':
                case 'seized':
                case 'shelved':
                    // in transit
                    ShipmentMovementHelper::PerformInTransit($orderData, $eventDateTime);
                    break;
                case 'accept':
                case 'assigned_for_delivery':
                    // out for delivery
                    ShipmentMovementHelper::PerformOutForDelivery($orderData, $eventDateTime);
                    break;
                case 'attempted':
                    // ndr
                    ShipmentMovementHelper::PerformNDR($orderData, $trackingData['notes'], $eventDateTime);
                    break;
                case 'delivered':
                    // delivered
                    ShipmentMovementHelper::PerformDelivered($orderData, $eventDateTime);
                    break;
                case 'deps_exception':
                    // damaged
                    ShipmentMovementHelper::PerformInTransit($orderData, $eventDateTime);
                    break;
                case 'lost':
                    // lost
                    ShipmentMovementHelper::PerformLost($orderData, $eventDateTime);
                    break;
                default:
                    // add missing mappings here
                    $missStatus = [
                        'order_id' => $orderData->id,
                        'courier_keyword' => $orderData->courier_partner,
                        'status' => $trackingData['type'],
                        'status_description' => $trackingData['customer_update'],
                        'json' => json_encode($trackingData),
                        'created_at' => date('Y-m-d h:i:s')
                    ];
                    CourierMissStatusCode::create($missStatus);
                    break;
            }
            $data = [
                "awb_number" => $orderData->awb_number,
                "status_code" => $trackingData['type'],
                "status" => $trackingData['customer_update'],
                "status_description" => $trackingData['customer_update'],
                "remarks" => $trackingData['notes'],
                "location" => $trackingData['hub_name'],
                "updated_date" => $eventDateTime,
                'created_at' => date('Y-m-d H:i:s')
            ];
            OrderTracking::create($data);
        }catch(Exception $e){
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            Logger::cronLog("tracking-job", 'failed', 'Cron job failed', null, $e->getMessage()."-".$e->getFile()."-".$e->getLine(), 0, 0, 0, $eventDateTime, now());
        }
    }
    // Track Order Shree Maruti Ecom
    public static function TrackMarutiEcomOrder($order)
    {
        $returnValue = false;
        try {
            $maruti = new MarutiEcom();
            $awb_data = $maruti->trackOrder($order->awb_number);
            if($awb_data['success'] == "0") {
                // Tracking data not found
                return;
            }
            if($order->status == 'delivered' || $order->status == 'cancelled')
                return;
            $tracking_data = $awb_data['data'] ?? [];
            // check for booked or in transit
            if(!isset($tracking_data['deliveryinfo']) && !isset($tracking_data['travelinginfo'])) {
                // booked
                $tracking_data['deliveryinfo'] = [
                    "StatusName" => "BOOKED",
                    "Reason" => "",
                    "AreaID" => "",
                    "StatusDateTime" => date('Y-m-d H:i:s')
                ];
            } else if(!isset($tracking_data['deliveryinfo'])) {
                // in transit
                $tracking_data['deliveryinfo'] = [
                    "StatusName" => "IN TRANSIT",
                    "Reason" => "",
                    "AreaID" => "",
                    "StatusDateTime" => date('Y-m-d H:i:s')
                ];
            } else {
                $tracking_data['deliveryinfo'] = $tracking_data['deliveryinfo'][count($tracking_data['deliveryinfo'])-1];
            }
            if (isset($tracking_data['deliveryinfo'])) {
                $shipment_summary = $tracking_data['deliveryinfo'];
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $shipment_summary['StatusName']) {
                        switch ($shipment_summary['StatusName']) {
                            case 'BOOKED':
                                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                                self::PushChannelStatus($order, 'pickup_scheduled',$shipment_summary['StatusDateTime']);
                                break;
                            case 'OUT FOR DELIVERY':
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                self::PushChannelStatus($order, 'out_for_delivery',$shipment_summary['StatusDateTime']);
                                break;
                            case 'PENDING':
                                if ($order->rto_status != 'y') {
                                    if ($order->ndr_status == 'y' && $shipment_summary['StatusDateTime'] != $order->ndr_status_date) {
                                        //make attempt here
//                                        $attempt = [
//                                            'seller_id' => $order->seller_id,
//                                            'order_id' => $order->id,
//                                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                            'raised_time' => date('H:i:s'),
//                                            'action_by' => 'Shree Maruti',
//                                            'reason' => $order->reason_for_ndr,
//                                            'action_status' => 'requested',
//                                            'remark' => 'requested',
//                                            'u_address_line1' => 'new address line 1',
//                                            'u_address_line2' => 'new address line 2',
//                                            'updated_mobile' => ''
//                                        ];
                                        //Ndrattemps::create($attempt);
                                        Order::where('awb_number', $order->awb_number)->update(['ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    }
                                }
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                self::PushChannelStatus($order, 'out_for_delivery',$shipment_summary['StatusDateTime']);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'IN TRANSIT':
                                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                self::PushChannelStatus($order, 'in_transit',$shipment_summary['StatusDateTime']);
                                break;
                            case 'C/F TO NEXT DAY':
                                if ($order->rto_status != 'y') {
                                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Reason'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    $ndrRaisedDate = date('Y-m-d H:i:s');
                                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Reason'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['StatusDateTime']]);
                                    $attempt = [
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'Shree Maruti',
                                        'reason' => $shipment_summary['Reason'],
                                        'action_status' => 'pending',
                                        'remark' => 'pending',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);

                                    self::PushChannelStatus($order, 'ndr',$shipment_summary['StatusDateTime']);
                                }
                                break;
                            case 'DELIVERED':
                                $delivery_date = date('Y-m-d', strtotime($shipment_summary['StatusDateTime']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                if ($order->order_type == 'cod') {
                                    $data = array(
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'amount' => $order->invoice_amount,
                                        'type' => 'c',
                                        'datetime' => $delivery_date,
                                        'description' => 'Order COD Amount Credited',
                                        'redeem_type' => 'o',
                                    );
                                    COD_transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                                }
                                self::PushChannelStatus($order, 'delivered',$shipment_summary['StatusDateTime']);
                                self::CheckAndSendSMS($order);
                                break;
                            case 'RETURN':
                                self::RTOOrder($order->id);
                                self::PushChannelStatus($order,'rto_initated',$shipment_summary['StatusDateTime']);
                                break;
                            case 'RTO DELIVERED':
                            case 'RETURN DELIVERED':
                                self::RTOOrder($order->id);
                                // mark shipment as rto delivered
                                $delivery_date = date('Y-m-d H:i:s',strtotime($shipment_summary['StatusDateTime']));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                self::PushChannelStatus($order,'delivered',$shipment_summary['StatusDateTime']);
                                break;
                            default:
                                $missStatus = [
                                    'order_id' => $order->id,
                                    'courier_keyword' => $order->courier_partner,
                                    'status' => $shipment_summary['StatusName'],
                                    'status_description' => $shipment_summary['Reason'],
                                    'json' => json_encode($tracking_data),
                                    'created_at' => date('Y-m-d h:i:s')
                                ];
                                CourierMissStatusCode::create($missStatus);
                                break;
                        }
                        $data = [
                            "awb_number" => $order->awb_number,
                            "status_code" => $shipment_summary['StatusName'],
                            "status" => $shipment_summary['StatusName'],
                            "status_description" => $shipment_summary['Reason'],
                            "remarks" =>  $shipment_summary['Reason'],
                            "location" =>  $shipment_summary['AreaID'],
                            "updated_date" => $shipment_summary['StatusDateTime'],
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                        $returnValue = true;
                    }
                } else {
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $shipment_summary['StatusName'],
                        "status" => $shipment_summary['StatusName'],
                        "status_description" => $shipment_summary['Reason'],
                        "remarks" =>  $shipment_summary['Reason'],
                        "location" =>  $shipment_summary['AreaID'],
                        "updated_date" => $shipment_summary['StatusDateTime'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    $returnValue = true;
                }
            }
        } catch(Exception $e) {
            // dd($e->getMessage());
        }
        return $returnValue;
    }

    // Track Order Gati
    public static function TrackOrderGati($awb)
    {
        $gati = new Gati();
        $awb_data = $gati->trackOrder($awb);
        if(isset($awb_data['Gatiresponse']) && !empty($awb_data['Gatiresponse']['dktinfo'][0])) {
            $tracking_data = $awb_data['Gatiresponse']['dktinfo'][0];
        } else {
            return false;
        }

        if (isset($tracking_data['TRANSIT_DTLS'])) {
            $shipment_summary = $tracking_data['TRANSIT_DTLS'][0];
            $order_tracking = OrderTracking::where('awb_number', $tracking_data['DOCKET_NUMBER'])->orderBy('id', 'desc')->first();
            $o = Order::where('awb_number', $awb)->first();
            if ($order_tracking != null) {
                if ($order_tracking->status_code != $shipment_summary['INTRANSIT_STATUS_CODE']) {
                    if (!empty($tracking_data['ASSURED_DELIVERY_DATE'])) {
                        $expected_date = date('Y-m-d', strtotime($tracking_data['ASSURED_DELIVERY_DATE']));
                    }
                    switch ($shipment_summary['INTRANSIT_STATUS_CODE']) {
                        // case 'DCRE':
                        //     Order::where('awb_number', $awb)->update(['status' => 'pickup_scheduled', 'pickup_time' => $shipment_summary['INTRANSIT_DATE']]);
                        //     @$this->pushChannelStatus($o,'pickup_scheduled');
                        //     break;
                        case 'DCRE':
                            Order::where('id', $o->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $shipment_summary['INTRANSIT_DATE']]);
                            self::PushChannelStatus($o,'picked_up',$shipment_summary['INTRANSIT_DATE']);
                            self::CheckAndSendSMS($o);
                            break;
                        case 'DPDCC':
                            if ($o->rto_status != 'y') {
                                if ($o->ndr_status == 'y' && $shipment_summary['INTRANSIT_DATE'] != $o->ndr_status_date) {
                                    //make attempt here
                                    $attempt = [
                                        'seller_id' => $o->seller_id,
                                        'order_id' => $o->id,
                                        'raised_date' => date('Y-m-d', strtotime($o->ndr_status_date)),
                                        'raised_time' => date('H:i:s'),
                                        'action_by' => 'Gati',
                                        'reason' => $o->reason_for_ndr,
                                        'action_status' => 'requested',
                                        'remark' => 'requested',
                                        'u_address_line1' => 'new address line 1',
                                        'u_address_line2' => 'new address line 2',
                                        'updated_mobile' => ''
                                    ];
                                    Ndrattemps::create($attempt);
                                    Order::where('id', $o->id)->update(['ndr_status_date' => $shipment_summary['INTRANSIT_DATE']]);
                                }
                            }
                            Order::where('id', $o->id)->update(['status' => 'out_for_delivery']);
                            self::PushChannelStatus($o,'out_for_delivery',$shipment_summary['INTRANSIT_DATE']);
                            self::CheckAndSendSMS($o);
                            break;
                        case 'DKTAD':
                        case 'TCSOU':
                        case 'EOUS':
                            Order::where('id', $o->id)->update(['status' => 'in_transit']);
                            self::PushChannelStatus($o,'in_transit',$shipment_summary['INTRANSIT_DATE']);
                            break;
                        case 'DDLVD':
                            $delivery_date = date('Y-m-d', strtotime($shipment_summary['INTRANSIT_DATE']));
                            Order::where('id', $o->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            if ($o->order_type == 'cod' && $o->o_type=='forward' && $o->rto_status == 'n') {
                                $data = array(
                                    'seller_id' => $o->seller_id,
                                    'order_id' => $o->id,
                                    'amount' => $o->invoice_amount,
                                    'type' => 'c',
                                    'datetime' => $delivery_date,
                                    'description' => 'Order COD Amount Credited',
                                    'redeem_type' => 'o',
                                );
                                COD_transactions::create($data);
                                Seller::where('id', $o->seller_id)->increment('cod_balance', $data['amount']);
                            }
                            self::PushChannelStatus($o,'delivered',$shipment_summary['INTRANSIT_DATE']);
                            self::CheckAndSendSMS($o);
                            break;
                        // case 'RTD':
                        //     if($o->o_type == "forward")
                        //         @$this->_RTOOrder($o->id);
                        //     $delivery_date = date('Y-m-d', strtotime($shipment_summary['INTRANSIT_DATE']));
                        //     Order::where('awb_number', $awb)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                        //     @$this->pushChannelStatus($o,'delivered');
                        //     break;
                        case 'DUNDL':
                            if ($o->rto_status != 'y') {
                                //Order::where('id', $o->id)->update(['status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['INTRANSIT_STATUS'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['INTRANSIT_DATE']]);
                                $ndrRaisedDate = date('Y-m-d H:i:s');
                                Order::where('id', $o->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['INTRANSIT_STATUS'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['INTRANSIT_DATE']]);
                                $attempt = [
                                    'seller_id' => $o->seller_id,
                                    'order_id' => $o->id,
                                    'raised_date' => date('Y-m-d',strtotime($ndrRaisedDate)),
                                    'raised_time' => date('H:i:s'),
                                    'action_by' => 'Gati',
                                    'reason' => $shipment_summary['INTRANSIT_STATUS'],
                                    'action_status' => 'pending',
                                    'remark' => 'pending',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);
                                self::PushChannelStatus($o,'ndr',$shipment_summary['INTRANSIT_DATE']);
                            }
                            break;
                        // case 'RTON':
                        //     @$this->_RTOOrder($o->id);
                        //     Order::where('awb_number', $awb)->update(['status' => 'rto_initated', 'rto_status' => 'y']);
                        //     @$this->pushChannelStatus($o,'rto_initated');
                        //     break;
                        case 'DDITS':
                            Order::where('id', $o->id)->update(['status' => 'damaged']);
                            self::PushChannelStatus($o,'damaged',$shipment_summary['INTRANSIT_DATE']);
                            break;
                        default:
                            $missStatus = [
                                'order_id' => $o->id,
                                'courier_keyword' => $o->courier_partner,
                                'status' => $shipment_summary['INTRANSIT_STATUS_CODE'],
                                'status_description' => $shipment_summary['REASON_DESC'],
                                'json' => json_encode($tracking_data),
                                'created_at' => date('Y-m-d h:i:s')
                            ];
                            CourierMissStatusCode::create($missStatus);
                            break;
                    }
                    $data = [
                        "awb_number" => $tracking_data['DOCKET_NUMBER'],
                        "status_code" => $shipment_summary['INTRANSIT_STATUS_CODE'],
                        "status" => $shipment_summary['INTRANSIT_STATUS'],
                        "status_description" => $shipment_summary['REASON_DESC'],
                        "remarks" =>  $shipment_summary['REASON_DESC'],
                        "location" =>  $shipment_summary['INTRANSIT_LOCATION'],
                        "updated_date" => $shipment_summary['INTRANSIT_DATE'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                }
            } else {
                $data = [
                    "awb_number" => $tracking_data['DOCKET_NUMBER'],
                    "status_code" => $shipment_summary['INTRANSIT_STATUS_CODE'],
                    "status" => $shipment_summary['INTRANSIT_STATUS'],
                    "status_description" => $shipment_summary['REASON_DESC'],
                    "remarks" =>  $shipment_summary['REASON_DESC'],
                    "location" =>  $shipment_summary['INTRANSIT_LOCATION'],
                    "updated_date" => $shipment_summary['INTRANSIT_DATE'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                OrderTracking::create($data);
            }
        }
        return true;
    }

    // Track Order Movin
    public static function TrackMovinOrder($orderData){
        $returnValue = false;
        $movin = new Movin();
        $trackingData = $movin->shipmentTracking($orderData);
        if($trackingData['status'] != 200)
            return false;
        if(empty($trackingData['data']))
            return false;
        $trackingDetail = $trackingData['data'][$orderData->awb_number];
        if($trackingDetail['current_status'] == "")
            return false;
        $order_tracking = OrderTracking::where('awb_number', $orderData->awb_number)->orderBy('id', 'desc')->first();
        if(!empty($order_tracking)){
            if($order_tracking->status_code != $trackingDetail['current_status']){
                // Handle
                self::_HandleMovinTracking($trackingDetail,$orderData);
                $returnValue = true;
            }
        }
        else{
            // Handle
            self::_HandleMovinTracking($trackingDetail,$orderData);
            $returnValue = true;
        }
        return $returnValue;
    }
    public static function _HandleMovinTracking($trackingData,$order){
        $datetime = date('Y-m-d H:i:s');
        if($trackingData['is_rto_shipment'] == true)
        {
            if($order->rto_status == 'n') {
                self::RTOOrder($order->id);
                self::PushChannelStatus($order,'rto_initiated',$datetime);
            }
        }
        switch($trackingData['current_status']){
            case 'NOTDISPATCHED_UNASSIGNED':
            case 'ATTEMPTED_PICKUP':
                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                self::PushChannelStatus($order,'pickup_scheduled',$datetime);
                break;
            case 'PICKEDUP_FROM_CUSTOMER':
            case 'PICKEDUP_FROM_INTERIMBRANCH':
                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $datetime]);
                self::PushChannelStatus($order,'picked_up',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'INTRANSIT_TO_CUSTOMER':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $datetime != $order->ndr_status_date) {
                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $order->seller_id,
//                            'order_id' => $order->id,
//                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                            'raised_time' => date('H:i:s'),
//                            'action_by' => 'Movin',
//                            'reason' => $order->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $datetime]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                self::PushChannelStatus($order,'out_for_delivery',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'ATTEMPTED_DELIVERY':
                if ($order->rto_status != 'y') {
                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['current_branch'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                    $ndrRaisedDate = date('Y-m-d H:i:s');
                    Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['current_branch'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                    $attempt = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                        'raised_time' => date('H:i:s'),
                        'action_by' => 'Movin',
                        'reason' => $trackingData['current_branch'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                    self::PushChannelStatus($order,'ndr',$datetime);
                }
                break;
            case 'OUTSCANNED_DESTINATION_BRANCH':
            case 'REACHED_INTERIM_BRANCH':
            case 'OUTSCANNED_INTERIM_BRANCH':
            case 'INSCANNED_DESTINATION_BRANCH':
            case 'INSCANNED_ORIGIN_BRANCH':
            case 'REACHED_DESTINATION_BRANCH':
            case 'INSCANNED_INTERIM_BRANCH':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit');
                break;
            case 'DELIVERED_TO_CUSTOMER':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
                if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $datetime ?? date('Y-m-d'),
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($order,'delivered');
                self::CheckAndSendSMS($order);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $trackingData['current_status'],
                    'status_description' => $trackingData['current_branch'],
                    'json' => json_encode($trackingData),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $trackingData['current_status'],
            "status" => $trackingData['current_status'],
            "status_description" => $trackingData['current_branch'],
            "remarks" =>  $trackingData['current_branch'],
            "location" =>  $trackingData['current_branch'],
            "updated_date" => $datetime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }
    public static function _HandleProfessionalTracking($trackingData,$order){
        $datetime = date('Y-m-d H:i:s');
        switch($trackingData['Type']){
            case 'Booking':
                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                self::PushChannelStatus($order,'pickup_scheduled',$datetime);
                self::CheckAndSendSMS($order);
                break;
            case 'Inbound':
            case 'Outbound':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                self::PushChannelStatus($order,'in_transit',$datetime);
                break;
            case 'Attempted':
                if($trackingData['Remarks'] == 'DL' || $trackingData['Remarks'] == 'NR' || $trackingData['Remarks'] == 'NDR'){
                    // NDR
                    if ($order->rto_status != 'y') {
                        //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['current_branch'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                        $ndrRaisedDate = date('Y-m-d H:i:s');
                        Order::where('id', $order->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['Activity'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                        $attempt = [
                            'seller_id' => $order->seller_id,
                            'order_id' => $order->id,
                            'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'Professional',
                            'reason' => $trackingData['Activity'],
                            'action_status' => 'pending',
                            'remark' => 'pending',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        self::PushChannelStatus($order,'ndr',$datetime);
                    }
                }
                else if($trackingData['Remarks'] == 'TD'){
                    // out for delivery
                    if ($order->rto_status != 'y') {
                        if ($order->ndr_status == 'y' && $datetime != $order->ndr_status_date) {
                            //make attempt here
//                            $attempt = [
//                                'seller_id' => $order->seller_id,
//                                'order_id' => $order->id,
//                                'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                'raised_time' => date('H:i:s'),
//                                'action_by' => 'Professional',
//                                'reason' => $order->reason_for_ndr,
//                                'action_status' => 'requested',
//                                'remark' => 'requested',
//                                'u_address_line1' => 'new address line 1',
//                                'u_address_line2' => 'new address line 2',
//                                'updated_mobile' => ''
//                            ];
//                            Ndrattemps::create($attempt);
                            Order::where('id', $order->id)->update(['ndr_status_date' => $datetime]);
                        }
                    }
                    Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                    self::PushChannelStatus($order,'out_for_delivery',$datetime);
                    self::CheckAndSendSMS($order);
                }else if($trackingData['Remarks'] == 'RO'){
                    // RTO
                    if($order->rto_status == 'n')
                        self::RTOOrder($order->id);
                }
                break;
            case 'Delivered':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
                if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $datetime ?? date('Y-m-d'),
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                self::PushChannelStatus($order,'delivered',$datetime);
                self::CheckAndSendSMS($order);
                break;
            default:
                $missStatus = [
                    'order_id' => $order->id,
                    'courier_keyword' => $order->courier_partner,
                    'status' => $trackingData['Type']."-".$trackingData['Activity'],
                    'status_description' => $trackingData['Activity'],
                    'json' => json_encode($trackingData),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $trackingData['Type'],
            "status" => $trackingData['Remarks'],
            "status_description" => $trackingData['Activity'],
            "remarks" =>  $trackingData['Remarks'],
            "location" =>  $trackingData['Remarks'],
            "updated_date" => $datetime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    public static function CustomRTOOrder($orderId){
        $order = Order::find($orderId);
        $seller = Seller::find($order->seller_id);
        if(empty($order)){
            return false;
        }
        if($order->status == 'pending' || $order->status == 'cancelled' || $order->status == 'pickup_requested')
            return true;
        if($order->rto_status != 'y')
        {
            if(!($order->seller_id == 16 && (str_starts_with($order->courier_partner,'dtdc') || $order->courier_partner == 'xpressbees_sfc' || str_starts_with($order->courier_partner,'shree_maruti'))))
            {
                $data = array(
                    'seller_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'amount' => floatval($order->rto_charges ?? $order->shipping_charges),
                    'balance' => floatval($seller->balance) - floatval($order->rto_charges ?? $order->shipping_charges),
                    'type' => 'd',
                    'redeem_type' => 'o',
                    'datetime' => date('Y-m-d H:i:s'),
                    'method' => 'wallet',
                    'description' => 'Order RTO Charge Deducted'
                );
                Transactions::create($data);
                Seller::where('id', $order->seller_id)->decrement('balance', $data['amount']);
                $balance = $seller->balance - $data['amount'];
                if($order->order_type == 'cod'){
                    if($seller->floating_value_flag == 'y')
                        $refundAmount = $order->cod_charges + $order->early_cod_charges;
                    else
                        $refundAmount = intval($order->cod_charges) + intval($order->early_cod_charges);
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $refundAmount ?? 0,
                        'balance' => $balance + $refundAmount,
                        'type' => 'c',
                        'redeem_type' => 'o',
                        'datetime' => date('Y-m-d H:i:s'),
                        'method' => 'wallet',
                        'description' => 'Order RTO COD Charge Refunded'
                    );
                    Transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('balance', $refundAmount);
                }
            }
            //Order::where('id',$orderId)->update();
            Order::where('id', $orderId)->update(['status' => 'rto_initated', 'rto_status' => 'y']);
        }
        return  true;
    }
}
