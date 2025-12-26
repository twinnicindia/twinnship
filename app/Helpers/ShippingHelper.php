<?php

namespace App\Helpers;


use App\Libraries\AmazonSWA;
use App\Libraries\BluedartRest;
use App\Libraries\Custom\CustomXpressBees;
use App\Libraries\Delhivery;
use App\Libraries\Logger;
use App\Libraries\MyUtility;
use App\Libraries\Shadowfax;
use App\Libraries\XpressBees;
use App\Models\BluedartAwbNumbers;
use App\Models\BluedartNSEAwbNumbers;
use App\Models\Configuration;
use App\Models\Courier_blocking;
use App\Models\CustomSellerChannels;
use App\Models\InternationalOrders;
use App\Models\Order;
use App\Models\Partners;
use App\Models\Preferences;
use App\Models\Product;
use App\Models\Rules;
use App\Models\Seller;
use App\Models\ServiceablePincode;
use App\Models\ServiceablePincodeFM;
use App\Models\SmartrAwbs;
use App\Models\Transactions;
use App\Models\Warehouses;
use App\Models\ZoneMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class ShippingHelper
{
    const MetroCities=['ahmedabad','bangalore','bhiwandi','chennai','hyderabad','kolkata','mumbai','pune','thane','vashi','vasai','new delhi'];
    const OrderStatus = [
        "pending" => "Pending",
        "shipped" => "Shipped",
        "manifested" => "Manifested",
        "pickup_scheduled" => "Pickup Scheduled",
        "picked_up" => "Picked Up",
        "cancelled" => "Cancelled",
        "in_transit" => "In Transit",
        "out_for_delivery" => "Out for Delivery",
        "rto_initated" => "RTO Initiated",
        "rto_initiated" => "RTO Initiated",
        "rto_delivered" => "RTO Delivered",
        "rto_in_transit" => "RTO In Transit",
        "delivered" => "Delivered",
        "ndr" => "NDR",
        "lost" => "Lost",
        "damaged" => "Damaged"
    ];
    const CustomCredentials = [
        'bluedart' => [
            10271 => 'y',
            11300 => 'y',
            11230 => 'y',
            34634 => 'y',
            32151 => 'y'
        ],
        'bluedart_surface' => [
            10271 => 'y',
            11300 => 'y',
            34634 => 'y',
            32151 => 'y'
        ],
        'dtdc_surface' => [
            11300 => 'y',
            16 => 'y',
        ],
        'delhivery_surface' => [
            12889 => 'y'
        ],
        'xpressbees_sfc' => [
            16 => 'y',
        ],
//        'dtdc_1kg' => [
//            1 => 'y'
//        ],
//        'dtdc_2kg' => [
//            1 => 'y'
//        ],
//        'dtdc_3kg' => [
//            1 => 'y'
//        ],
//        'dtdc_5kg' => [
//            1 => 'y'
//        ],
//        'dtdc_6kg' => [
//            1 => 'y'
//        ],
//        'dtdc_10kg' => [
//            1 => 'y'
//        ]
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
        'smc_maruti' => 'Shree Maruti Courier',
        'shree_maruti_ecom_1kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_3kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_5kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_10kg' => 'Shree Maruti Courier',
        'tpc_surface' => 'The Professional Courier',
        'tpc_1kg' => 'The Professional Courier 1KG',
        'pick_del' => 'Pick & Del',
    ];

    public static function ShipOrder($orderData,$sellerData,$selectedCourierPartner=null,$isBulkShip=false,$sellerBalance = 0) {
        $res['status'] = false;
        $res['message'] = ' Pincode is not Serviceable';
        try{
            $configuration = Configuration::find(1);
//            if($orderData->global_type == 'international'){
//                $res = InternationalOrderHelper::ShipOrder($orderData,$sellerData,$selectedCourierPartner);
//                return $res;
//            }
            $rateCriteria = MyUtility::findMatchCriteria($orderData->p_pincode,$orderData->s_pincode,$sellerData);
            if($rateCriteria == "not_found"){
                $res['status'] = false;
                $res['message'] = "Pincode is not serviceable";
                return $res;
            }
            if($orderData->invoice_amount >= 50000 && empty($orderData->ewaybill_number)){
                $res['status'] = false;
                $res['message'] = "Ewaybill Number is required for this shipment";
                return $res;
            }
            $contactNumber = trim(preg_replace("/[^0-9]/", "", $orderData->s_contact));
            if(strlen($contactNumber) != 10 || preg_match('/^[6789]/', $contactNumber) == false){
                $res['status'] = false;
                $res['message'] = "Mobile Number is not valid";
                return $res;
            }
//            $filteredName = preg_replace("/[^a-zA-Z\s]/", "", $orderData->s_customer_name);
//            $filteredName = trim($filteredName);
//            if(strlen($filteredName) < 1){
//                $res['status'] = false;
//                $res['message'] = "Invalid Customer Name";
//                return $res;
//            }
            $filteredCity = trim(preg_replace("/[^a-zA-Z\s]/", "", $orderData->s_customer_name));
            if(strlen($filteredCity) < 1){
                $res['status'] = false;
                $res['message'] = "Invalid Customer Name";
                return $res;
            }
            $filteredAddress = preg_replace("/[^a-zA-Z0-9\s\-,]/", "", ($orderData->s_address_line1." ".$orderData->s_address_line2));
            $filteredAddress = trim($filteredAddress);
            if(strlen($filteredAddress) < 3){
                $res['status'] = false;
                $res['message'] = "Invalid Shipping Address";
                return $res;
            }
            $zone = self::GetZoneNameByKeyword($rateCriteria);
            $partners=[];
            if($selectedCourierPartner == null){
                $preferences = self::MatchRules($orderData,$sellerData);
                if($preferences == false) {
                    $partnersData = [
                        Partners::where('keyword', $sellerData->courier_priority_1)->where('status', 'y')->first(),
                        Partners::where('keyword', $sellerData->courier_priority_2)->where('status', 'y')->first(),
                        Partners::where('keyword', $sellerData->courier_priority_3)->where('status', 'y')->first(),
                        Partners::where('keyword', $sellerData->courier_priority_4)->where('status', 'y')->first(),
                    ];
                }else{
                    $partnersData = [
                        Partners::where('keyword', $preferences->priority1)->where('status', 'y')->first(),
                        Partners::where('keyword', $preferences->priority2)->where('status', 'y')->first(),
                        Partners::where('keyword', $preferences->priority3)->where('status', 'y')->first(),
                        Partners::where('keyword', $preferences->priority4)->where('status', 'y')->first(),
                    ];
                }
            }
            else{
                $partnersData = [
                    Partners::where('keyword', $selectedCourierPartner)->where('status', 'y')->first()
                ];
            }
            $blockedCourierPartners = explode(',', $sellerData->blocked_courier_partners) ?? [];
            foreach($partnersData as $p){
                if(!empty($p) && !in_array($p->id, $blockedCourierPartners))
                {
                    if(!self::CheckAndReturn($partners,$p->keyword)){
                        if(strtolower(trim($orderData->shipment_type)) == 'mps') {
                            if($p->mps_enabled == 'y') {
                                if(strtolower(trim($orderData->o_type)) == 'reverse') {
                                    if($p->reverse_enabled == 'y') {
                                        $partners[]=$p;
                                    }
                                } else {
                                    $partners[]=$p;
                                }
                            }
                        } else if(strtolower(trim($orderData->o_type)) == 'reverse') {
                            if($p->reverse_enabled == 'y') {
                                if($orderData->is_qc == 'y'){
                                    if($p->qc_enabled == 'y')
                                        $partners[]=$p;
                                }
                                else
                                    $partners[]=$p;
                            }
                        } else {
                            $partners[]=$p;
                        }
                    }
                }
            }
            // Check courier partner is blocked or not
            // will be skipped
            $tmp = [];
            if($configuration->check_courier_blocking == 'y') {
                foreach($partners as $partner) {
                    if(self::IsCourierBlocked($orderData, $partner->keyword) == false) {
                        $tmp[] = $partner;
                    }
                }
            } else {
                $tmp = $partners;
            }
            if(count($tmp)==0){
                $res['status'] = false;
                $res['message'] = "Pincode is not Serviceable";
                return $res;
            }
            $partners = $tmp;
            // comment ends here
            $awb_number = null;
            $routeCode = '';
            $manifestSent = 'y';
            $llType = 'SE';
            $token_number = '';
            $isCustom = false;
            $courier_partner = null;
            $errorMessage = null;
            foreach($partners as $partner) {
                if(empty($partner)) {
                    continue;
                }
                else
                {
                    if($orderData->weight > $orderData->vol_weight)
                        $weight=$orderData->weight;
                    else
                        $weight=$orderData->vol_weight;

                    // Change weight to 500gm if weight is <= 1500gm for amazon amazon_swa_1kg
//                    if($partner->keyword == 'amazon_swa_1kg' && $weight <= 1500) {
//                        $weight = 500;
//                    }

                    $extra=($weight - $partner->weight_initial) > 0 ? $weight - $partner->weight_initial : 0;
                    $mul=ceil($extra / $partner->extra_limit);
                    $plan_id = $sellerData->plan_id;
                    $seller_id = $sellerData->id;
                    $partner_rate = DB::select("select *,$rateCriteria + ( extra_charge_".strtolower($zone)." * $mul ) as price from rates where plan_id=$plan_id and partner_id = $partner->id and seller_id = $seller_id limit 1");
                    // $partner_rate = Rates::select("$rateCriteria as price", 'cod_charge','cod_maintenance')->where('partner_id', $partner->id)->where('plan_id', $sellerData->plan_id)->first();
                    $courier_partner = $partner->keyword;
                    if(empty($partner_rate))
                        continue;
                    $shipping_charge = $partner_rate[0]->price;
                    if(strtolower($orderData->o_type) == 'reverse'){
                        $shipping_charge  = ($shipping_charge * $sellerData->reverse_charge) / 100;
                        if($orderData->is_qc == "y")
                            $shipping_charge+= ($shipping_charge * $configuration->qc_charges ) / 100;
                    }
                    $shipping_charge += ($shipping_charge * 18) / 100;
                    $cod_maintenance = $partner_rate[0]->cod_maintenance;
                    if (strtolower($orderData->order_type) == 'prepaid') {
                        $cod_charge = "0";
                        $early_cod = "0";
                    } else {
                        $invoiceAmount = !empty($orderData->collectable_amount) ? $orderData->collectable_amount : $orderData->invoice_amount;
                        $cod_charge = ($invoiceAmount * $cod_maintenance) / 100;
                        if ($cod_charge < $partner_rate[0]->cod_charge)
                            $cod_charge = $partner_rate[0]->cod_charge;
                        $cod_charge += ($cod_charge * 18) / 100;
                        $early_cod = ($invoiceAmount * $sellerData->early_cod_charge) / 100;
                        $early_cod += ($early_cod * 18) / 100;
                    }
                    $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
                    $rto_charge = ($shipping_charge) * $sellerData->rto_charge / 100;

                    $other_charges = 0;
                    if($sellerData->whatsapp_service == 1){
                        $other_charges = $sellerData->whatsapp_charges + round(($sellerData->whatsapp_charges * 18) / 100,2);
                    }
                    $sms_charge = 0;
                    if($sellerData->sms_service == 'y'){
                        $sms_charge = $sellerData->sms_charges;
                    }

                    if($sellerData->floating_value_flag == 'y')
                        $total_charge = $shipping_charge + $cod_charge + $early_cod;
                    else
                        $total_charge = round($shipping_charge + $cod_charge + $early_cod);
                    $total_charge = $total_charge + $other_charges + $sms_charge;
                    $seller_balance = ($sellerBalance!=0) ? ($sellerBalance-$configuration->minimum_balance) : ($sellerData->balance - $configuration->minimum_balance);
                    //dd($total_charge);
                    if($total_charge <= $seller_balance) {
                        if(strtolower($orderData->status) == 'pending') {
                            if(intval($orderData->invoice_amount) <= 0){
                                $errorMessage = 'Invoice amount must be non zero!!';
                                continue;
                            }
                            $errorMessage = " Pincode is not Serviceable";
                            if($partner->keyword == 'ekart' || $partner->keyword == 'ekart_1kg' || $partner->keyword == 'ekart_2kg')
                            {
                                if($orderData->length > 75 || $orderData->breadth > 75 || $orderData->height > 75)
                                {
                                    $errorMessage = 'Order not shipped!';
                                    continue;
                                }
                                $status = self::CheckServicePincode($orderData->s_pincode, 'ekart',$orderData->order_type);
                                $statusFM = self::CheckServicePincodeFM($orderData->p_pincode, 'ekart');
                                if ($status > 0 && $statusFM > 0) {
                                    $generatedAWB = MyUtility::getEkartAwbNumber(null,$sellerData->id);
                                    if(!$generatedAWB){
                                        $errorMessage = 'Order not shipped!';
                                        continue;
                                    }
                                    if($orderData->o_type == "forward"){
                                        // forward order
                                        $prefix = $orderData->order_type == 'prepaid' ? "P" : "C";
                                        $awb_number = "SHE".$prefix.$generatedAWB->number;
                                        $courier_partner = $partner->keyword;
                                        $manifestSent = 'n';
                                        break;
                                    }
                                    else{
                                        // reverse
                                        $errorMessage = 'Reverse Order is not Enabled for this Courier Partner';
                                        continue;
                                    }
                                } else {
                                    $errorMessage = 'Pincode is not Serviceable!';
                                    continue;
                                }
                            }
                            else if($partner->keyword == 'ekart_250gm')
                            {
                                if($orderData->length > 75 || $orderData->breadth > 75 || $orderData->height > 75)
                                {
                                    $errorMessage = 'Order not shipped!';
                                    continue;
                                }
                                $status = self::CheckServicePincode($orderData->s_pincode, 'ekart_250gm',$orderData->order_type);
                                $statusFM = self::CheckServicePincodeFM($orderData->p_pincode, 'ekart_250gm');
                                if ($status > 0 && $statusFM > 0) {
                                    $generatedAWB = MyUtility::getEkartSmallAwbNumber(null,$sellerData->id);
                                    if(!$generatedAWB){
                                        $errorMessage = 'Order not shipped!';
                                        continue;
                                    }
                                    if($orderData->o_type == "forward"){
                                        // forward order
                                        $prefix = $orderData->order_type == 'prepaid' ? "P" : "C";
                                        $awb_number = "SHT".$prefix.$generatedAWB->number;
                                        $courier_partner = $partner->keyword;
                                        $manifestSent = 'n';
                                        break;
                                    }
                                    else{
                                        // reverse
                                        $errorMessage = 'Reverse Order is not Enabled for this Courier Partner';
                                        continue;
                                    }
                                } else {
                                    $errorMessage = 'Pincode is not Serviceable!';
                                    continue;
                                }
                            }
                            elseif ($courier_partner == 'delhivery_surface' || $courier_partner == 'delhivery_surface_2kg') {
                                if (UtilityHelper::CheckPincodeServiceability($orderData->p_pincode, $orderData->s_pincode, $orderData->order_type, $partner->serviceability_check)) {
                                    //$credentials = self::CheckSellerCustomChannel('delhivery_surface',$sellerData->id);
//                                    if($credentials['status']){
//                                        $customClient = new CustomDelhivery($credentials['credentials']);
//                                        $response = $customClient->ShipOrder($orderData,$sellerData);
//                                        if(!empty($response))
//                                        {
//                                            $awb_number = $response;
//                                            $isCustom = true;
//                                            break;
//                                        }
//                                        continue;
//                                    }
//                                    else{
                                        $delhiveryClient = new Delhivery('surface');
                                        $response = $delhiveryClient->ShipOrder($orderData,$sellerData);
                                        if(!empty($response))
                                        {
                                            $awb_number = $response;
                                            break;
                                        }
//                                    }
                                }
                            }
                            elseif ($courier_partner == 'delhivery_air') {
                                if (UtilityHelper::CheckPincodeServiceability($orderData->p_pincode, $orderData->s_pincode, $orderData->order_type, $partner->serviceability_check)) {
                                    $delhiveryClient = new Delhivery('air');
                                    $response = $delhiveryClient->ShipOrder($orderData,$sellerData);
                                    if(!empty($response))
                                    {
                                        $awb_number = $response;
                                        break;
                                    }
                                }
                            }
                            elseif ($courier_partner == 'delhivery_surface_5kg') {
                                if (UtilityHelper::CheckPincodeServiceability($orderData->p_pincode, $orderData->s_pincode, $orderData->order_type, $partner->serviceability_check)) {
                                    $delhiveryClient = new Delhivery('five');
                                    $response = $delhiveryClient->ShipOrder($orderData,$sellerData);
                                    if(!empty($response))
                                    {
                                        $awb_number = $response;
                                        break;
                                    }
                                }
                            }
                            elseif ($courier_partner == 'delhivery_surface_10kg') {
                                if (UtilityHelper::CheckPincodeServiceability($orderData->p_pincode, $orderData->s_pincode, $orderData->order_type, $partner->serviceability_check)) {
                                    $delhiveryClient = new Delhivery('ten');
                                    if(strtolower($orderData->shipment_type) == "mps"){
                                        $response = $delhiveryClient->DelhiveryMPS($orderData);
                                    }
                                    else{
                                        $response = $delhiveryClient->ShipOrder($orderData,$sellerData);
                                    }
                                    if(!empty($response))
                                    {
                                        $awb_number = $response;
                                        break;
                                    }
                                }
                            }
                            elseif ($courier_partner == 'xpressbees_surface_3kg' || $courier_partner == 'xpressbees_sfc' || $courier_partner == 'xpressbees_surface_1kg' || $courier_partner == 'xpressbees_5kg' || $courier_partner == 'xpressbees_2kg' || $courier_partner == 'xpressbees_10kg' || $courier_partner == 'xpressbees_20kg') {
                                $client = new XpressBees('one');
                                if ($orderData->o_type == 'forward') {
                                    $awbData = $client->ShipOrder($orderData, $sellerData);
                                    if(!empty($awbData)){
                                        $awb_number = $awbData;
                                        break;
                                    }else{
                                        continue;
                                    }
                                }
                                else {
                                    $errorMessage = ' Pincode is not serviceable';
                                    continue;
                                }
                            }
                            elseif ($courier_partner == 'shadowfax') {
                                $serviceability = self::CheckServicabilityShadowFax($orderData->p_pincode, $orderData->s_pincode);
                                if ($serviceability['Serviceability']) {
                                    if (strtolower($orderData->o_type) == 'forward') {
                                        $generatedAWB = MyUtility::getShadowFaxAwbNumber($orderData);
                                        if(empty($generatedAWB)){
                                            continue;
                                        }
                                        $awb_number = $generatedAWB;
                                        break;
                                    } else {
                                        $shadowfax = new Shadowfax();
                                        $response = $shadowfax->reverseManifestationOrder($orderData,$sellerData);
                                        if($response['message'] == 'Success'){
                                            $awb_number = $response['awb_number'];
                                            break;
                                        }
                                    }
                                }
                            }
                            elseif ($courier_partner == 'ecom_express' || $courier_partner == 'ecom_express_rvp') {
                                if (strtolower($orderData->o_type) == 'forward') {
                                    $orderType = $orderData->order_type == 'prepaid' ? 'ppd' : 'cod';
                                    $resp = self::CheckServicePincode($orderData->s_pincode,$courier_partner);
                                    if ($resp == 0) {
                                        continue;
                                    }
                                    $generatedAWB = MyUtility::GenerateEcomExpressAwbNumber('ecom_express',$orderType);
                                    if(!empty($generatedAWB)){
                                        $awb_number = $generatedAWB->awb_number;
                                        $manifestSent = 'n';
                                        break;
                                    }
                                    else{
                                        continue;
                                    }
                                } else {
                                    continue;
                                }
                            }
                            elseif ($courier_partner == 'smartr') {
                                if (strtolower($orderData->o_type) == 'forward') {
                                    $resp = self::CheckServicePincode($orderData->s_pincode,'smartr');
                                    $respFM = self::CheckServicePincode($orderData->p_pincode,'smartr');
                                    if ($resp == 0 || $respFM == 0) {
                                        continue;
                                    }
                                    DB::beginTransaction();
                                    $awb = SmartrAwbs::where('used', 'n')->lockForUpdate()->first();
                                    $manifestSent = 'n';
                                    SmartrAwbs::where('id', $awb->id)->update(['used' => 'y', 'used_by' => $seller_id, 'used_time' => date('Y-m-d H:i:s')]);
                                    DB::commit();
                                    $awb_number = $awb->awb_number;
                                    $resPincode = ServiceablePincode::where('courier_partner','smartr')->where('pincode',$orderData->s_pincode)->first();
                                    $routeCode = $resPincode->branch_code;
                                    break;
                                } else {
                                    continue;
                                }
                            }
                            else if($courier_partner == 'amazon_swa' || $courier_partner == 'amazon_swa_1kg' || $courier_partner == 'amazon_swa_3kg' || $courier_partner == 'amazon_swa_5kg' || $courier_partner == 'amazon_swa_10kg') {
                                if (strtolower($orderData->o_type) == 'forward') {
                                    $orderData->courier_partner = $courier_partner;
                                    $amazonSwa = new AmazonSWA();
                                    $response = $amazonSwa->shipOrder($orderData);
                                    if(!$response){
                                        continue;
                                    }
                                    $awb_number = $response;
                                    break;
                                } else {
                                    //reverse flow goes here
                                    continue;
                                }
                            }
                            elseif (in_array($courier_partner, ['smc_new', 'smc_2kg', 'smc_5kg', 'smc_air', 'smc_air_2kg'])) {
                                if (UtilityHelper::CheckPincodeServiceability($orderData->p_pincode, $orderData->s_pincode, $orderData->order_type, $partner->serviceability_check)) {
                                    // New Code
                                    $getAwbNumber = MyUtility::GetSMCNewAWB($orderData, $courier_partner);
                                    if(empty($getAwbNumber))
                                        continue;
                                    $awb_number = $getAwbNumber;
                                    break;
                                }
                            }
                            else if(in_array($courier_partner, ['bluedart', 'bluedart_surface', 'bluedart_10kg', 'bluedart_10kg_surface'])) {
                                $bluedart = new BluedartRest('SE', $courier_partner);
                                $resp = $bluedart->shipOrder($orderData);
                                if(!empty($resp)){
                                    $clusterCode = ServiceablePincode::where('pincode',$orderData->s_pincode)->where('courier_partner','bluedart')->first();
                                    $routeCode = $clusterCode->branch_code."/".$clusterCode->cluster_code;
                                    $awb_number = $resp;
                                    break;
                                }
                            }
                            else{
                                $errorMessage = 'Order not shipped!';
                            }

                        }else{
                            $res['status'] = false;
                            $errorMessage = "Your Order Status is already ".self::OrderStatus[$orderData->status]." so you can not Shipped this order";
                            continue;
                        }
                    }
                    else {
                        $errorMessage = 'Booking failed due to insufficient balance. Please recharge and try!!';
                        $balanceFlag = true;
                        break;
                    }
                }
            }
            if(!empty($awb_number)) {
                //if(in_array($orderData->seller_id,[14366,15682,188,3488,6960,17558]))
//                if(!in_array($orderData->seller_id,[1]))
//                    SendManifestationFulfillment::dispatchAfterResponse($orderData->id);
                if(!$isBulkShip){
                    $shipping_partner = $shipping_partner ?? null;
                    $barcode = @file_get_contents('https://twinnship.com/barcode/test.php?code='.$awb_number);
                    @file_put_contents('public/assets/seller/images/Barcode/'.$awb_number.'.png', $barcode);
                    $shipped_data = array(
                        'status' => 'shipped',
                        'courier_partner' => $courier_partner,
                        'shipping_partner' => $shipping_partner ?? '',
                        'manifest_sent' => $manifestSent,
                        'awb_number' => $awb_number,
                        'xb_token_number' => $token_number ?? "",
                        'seller_order_type' => $sellerData->seller_order_type,
                        'is_alpha' => $llType == 'LL' ? "LL" : $sellerData->is_alpha,
                        'is_alpha_delhivery' => $sellerData->is_alpha_delhivery,
                        'is_custom' => $isCustom ? 1 : 0,
                        'shipping_charges' => round($shipping_charge, 2),
                        'cod_charges' => round($cod_charge, 2),
                        'early_cod_charges' => round($early_cod, 2),
                        'rto_charges' => round($rto_charge, 2),
                        'gst_charges' => round($gst_charge, 2),
                        'total_charges' => $total_charge,
                        'zone' => $zone,
                        'awb_assigned_date' => date('Y-m-d H:i:s'),
                        'last_sync' => date('Y-m-d H:i:s'),
                        'awb_barcode' => 'public/assets/seller/images/Barcode/'.$awb_number.'.png',
                        'other_charges' => $other_charges
                    );
                    if(!$isCustom){
                        $shipped_data['route_code'] = $routeCode;
                    }
                    if($orderData->channel != 'custom')
                        $shipped_data['fulfillment_sent'] = 'n';
                    $updateResponse = Order::where('id', $orderData->id)->update($shipped_data);
                    MyUtility::CheckAndCreateLog(Session()->get('MySeller'),$orderData);

                    //wallet deduction
                    $transaction_check = Transactions::where('seller_id',$sellerData->id)->where('order_id', $orderData->id)->count();
                    if($transaction_check == 0) {
                        $seller = Seller::find($sellerData->id);
                        $data = array(
                            'seller_id' => $sellerData->id,
                            'order_id' => $orderData->id,
                            'amount' => $total_charge,
                            'balance' => $seller->balance - $total_charge,
                            'type' => 'd',
                            'redeem_type' => 'o',
                            'datetime' => date('Y-m-d H:i:s'),
                            'method' => 'wallet',
                            'description' => 'Order Shipping Charge Deducted'
                        );
                        Transactions::create($data);
                        Seller::where('id', $sellerData->id)->decrement('balance', $data['amount']);
                        if($other_charges != 0 ) {
                            $otherChargesData = array(
                                'seller_id' => $sellerData->id,
                                'order_id' => $orderData->id,
                                'amount' => $other_charges,
                                'balance' => $seller->balance - $total_charge,
                                'type' => 'd',
                                'redeem_type' => 'o',
                                'datetime' => date('Y-m-d H:i:s'),
                                'method' => 'wallet',
                                'description' => 'Other Services Charge Deducted : WhatsApp'
                            );
                            Transactions::create($otherChargesData);
                        }
                        if($sms_charge != 0 ) {
                            $smsChargesData = array(
                                'seller_id' => $sellerData->id,
                                'order_id' => $orderData->id,
                                'amount' => $sms_charge,
                                'balance' => $seller->balance - $total_charge,
                                'type' => 'd',
                                'redeem_type' => 'o',
                                'datetime' => date('Y-m-d H:i:s'),
                                'method' => 'wallet',
                                'description' => 'Other Services Charge Deducted : SMS'
                            );
                            Transactions::create($smsChargesData);
                        }

                        //Seller::where('id', $sellerData->id)->decrement('balance', $otherChargesData['amount']);
                    }

                    if($orderData->is_qc != 'y' && $orderData->global_type == 'domestic') {
                        InternationalOrders::create(['order_id' => $orderData->id]);
                    }
                    $details = [
                        'awb_number' => $awb_number,
                        'courier' => self::PartnerNames[$courier_partner] ?? 'Twinnship',
                        'courier_keyword' => $courier_partner,
                        'route_code' => $routeCode ?? ""
                    ];
                    $res['status'] = true;
                    $res['total_charges'] = $shipped_data['total_charges'];
                    $res['other_charges'] = $other_charges;
                    $res['shipping_charges'] = $shipped_data['shipping_charges'];
                    $res['cod_charges'] = $shipped_data['cod_charges'];
                    $res['data'] = $details;
                    $res['message'] = 'Order Shipped Successful';
                }
                else{
                    $res['status'] = true;
                    $res['data'] = [
                        'awb_number' => $awb_number,
                        'courier' => self::PartnerNames[$courier_partner] ?? 'Twinnship',
                        'courier_keyword' => $courier_partner
                    ];
                    $res['shipping_charges'] = $shipping_charge;
                    $res['manifest_sent'] = $manifestSent;
                    $res['route_code'] = $routeCode;
                    $res['total_charges'] = $total_charge;
                    $res['other_charges'] = $other_charges;
                    $res['cod_charges'] = $cod_charge;
                    $res['gst_charges'] = $gst_charge;
                    $res['zone'] = $zone;
                    $res['llType'] = $llType;
                    $res['is_custom'] = $isCustom;
                    $res['rto_charges'] = $rto_charge;
                    $res['early_cod_charges'] = $early_cod;
                    $res['balance'] = $balanceFlag ?? false;
                    $res['message'] = $errorMessage;
                }
            }
            else {
                $res['status'] = false;
                $res['balance'] = $balanceFlag ?? false;
                $res['message'] = $errorMessage;
            }
            return $res;
        }
        catch(Exception $e){
            Logger::write('logs/shipping-helper-'.date('Y-m-d').'.text', [
                'title' => 'Shipping Helper Exception',
                'data' => ['message' => $e->getMessage(),$e->getFile(),$e->getLine()]
            ]);
            return $res;
        }
    }
    public static function MatchRules($orderDetail,$sellerData)
    {
        $weight = $orderDetail->weight;
        if($orderDetail->vol_weight > $weight){
            $weight = $orderDetail->vol_weight;
        }
        $wareHouse = Warehouses::where('seller_id', $sellerData->id)->where('default', 'y')->get();
        if (count($wareHouse) == 0) {
            return false;
        }
        $prefs = Preferences::where('seller_id', $sellerData->id)->where('status', 'y')->orderBy('priority')->get();
        $ptnr = false;
        $matchStatus = false;
        foreach ($prefs as $p) {
            $match = 0;
            $rules = Rules::where('preferences_id', $p->id)->get();
            foreach ($rules as $r) {
                switch ($r->criteria) {
                    case 'order_amount':
                        if ($r->match_type == 'less_than') {
                            if ($orderDetail->invoice_amount <= $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'greater_than') {
                            if ($orderDetail->invoice_amount > $r->match_value)
                                $match++;
                        }
                        break;
                    case 'payment_type':
                        if ($r->match_type == "is") {
                            if (strtolower($orderDetail->order_type) == strtolower($r->match_value))
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if (strtolower($orderDetail->order_type) != strtolower($r->match_value))
                                $match++;
                        }
                        break;
                    case 'pickup_pincode':
                        if ($r->match_type == 'is') {
                            if ($orderDetail->p_pincode == $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if ($orderDetail->p_pincode != $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            if (self::MatchFromArray($r->match_value, $orderDetail->p_pincode))
                                $match++;
                        }
                        break;
                    case 'delivery_pincode':
                        if ($r->match_type == 'is') {
                            if ($orderDetail->s_pincode == $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if ($orderDetail->s_pincode != $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            if (self::MatchFromArray($r->match_value, $orderDetail->s_pincode))
                                $match++;
                        } else if ($r->match_type == 'starts_with') {
                            if (self::StartsWith($orderDetail->s_pincode, $r->match_value))
                                $match++;
                        }
                        break;
                    case 'zone':
                        if ($r->match_type == 'is') {
                            if ($orderDetail->zone == $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if ($orderDetail->zone != $r->match_value)
                                $match++;
                        }
                        break;
                    case 'weight':
                        if ($r->match_type == 'less_than') {
                            if ($weight <= $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'greater_than') {
                            if ($weight > $r->match_value)
                                $match++;
                        }
                        break;
                    case 'order_type':
                        if($r->match_value == 'reverse' && $orderDetail->o_type == 'reverse'){
                            $match++;
                        }else if($r->match_value == 'reverse_qc' && $orderDetail->is_qc == 'y' && $orderDetail->o_type == 'reverse'){
                            $match++;
                        }
                        break;
                    case 'product_name':
                        $products = Product::where('order_id', $orderDetail->id)->get();
                        if ($r->match_type == 'is') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (strtolower($r->match_value) == strtolower($pr->product_name)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            $found = true;
                            foreach ($products as $pr) {
                                if (strtolower($r->match_value) == strtolower($pr->product_name)) {
                                    $found = false;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'starts_with') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::StartsWith($pr->product_name, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'contain') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::ContainString($pr->product_name, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::MatchFromArray($r->match_value, $pr->product_name)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        }
                        break;
                    case 'product_sku':
                        $products = Product::where('order_id', $orderDetail->id)->get();
                        if ($r->match_type == 'is') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (strtolower($r->match_value) == strtolower($pr->product_sku)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            $found = true;
                            foreach ($products as $pr) {
                                if (strtolower($r->match_value) == strtolower($pr->product_sku)) {
                                    $found = false;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'starts_with') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::StartsWith($pr->product_sku, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'contain') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::ContainString($pr->product_sku, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            $found = false;
                            foreach ($products as $pr) {
                                if (self::MatchFromArray($r->match_value, $pr->product_sku)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        }
                        break;
                    default:
                        echo "";
                }
            }
            if ($p->match_type == 'all') {
                if ($match == count($rules)) {
                    $matchStatus = true;
                    $ptnr = $p;
                    break;
                }
            } else if ($p->match_type == 'any') {
                if ($match > 0) {
                    $matchStatus = true;
                    $ptnr = $p;
                    break;
                }
            }
        }
        if ($matchStatus) {
            return $ptnr;
        } else {
            return false;
        }
    }
    public static function CheckAndReturn($partners,$partner){
        $match = false;
        foreach($partners as $p){
            if($p->keyword == $partner)
                $match = true;
        }
        return $match;
    }
    public static function IsCourierBlocked(Order $order, string $courier_keyword) {
        // Get courier partner details
        $courier = Partners::where('keyword', $courier_keyword)->where('status', 'y')->first();
        if($courier == null) {
            return true;
        }

        // Get courier blocking details for seller and courier partner
        $blocking = Courier_blocking::where('seller_id', $order->seller_id)
            ->where('courier_partner_id', $courier->id)
            ->where('is_approved', 'y')
            ->first();
        if($blocking == null) {
            return false;
        }

        // Check courier is blocked or not
        if($blocking->is_blocked == 'y') {
            return true;
        }
        $orderType = strtolower($order->order_type);
        $orderZone = strtolower($order->zone);
        if(empty($orderZone)) {
            $orderZone = self::GetOrderZone($order);
        }
        // Check zone wise blocking
        if($blocking->{'zone_'.$orderZone} == 'y') {
            // Check payment type blocking
            if(($orderType == 'cod' && $blocking->cod == 'y') || ($orderType == 'prepaid' && $blocking->prepaid == 'y')) {
                return true;
            }
            // All payment types are blocked
            if($blocking->cod == 'y' && $blocking->prepaid == 'y') {
                return true;
            }
        }
        return false;
    }
    public static function MatchFromArray($string, $search)
    {
        $string = strtolower($string);
        $search = strtolower($search);
        $master = explode(',', $string);
        return in_array($search, $master);
    }
    public static function StartsWith($string, $startString)
    {
        $string = strtolower($string);
        $startString = strtolower($startString);
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
    public static function ContainString($string, $search)
    {
        $string = strtolower($string);
        $search = strtolower($search);
        $result = strpos($string, $search);
        if ($result === false)
            return false;
        else
            return true;
    }
    public static function GetOrderZone(Order $order) {
        $zoneE = ZoneMapping::where('pincode', $order->s_pincode)->where('picker_zone', 'E')->first();
        $ncrRegion = ['gurgaon', 'noida', 'ghaziabad', 'faridabad', 'delhi', 'new delhi', 'gurugram'];
        if(in_array(strtolower($order->s_city), $ncrRegion) && in_array(strtolower($order->p_city), $ncrRegion)){
            return 'a';
        } else if (strtolower($order->s_city) == strtolower($order->p_city) && strtolower($order->s_state) == strtolower($order->p_state)) {
            return 'a';
        } else if ($zoneE != null) {
            return 'e';
        } else if (strtolower($order->s_state) == strtolower($order->p_state)) {
            return 'b';
        } else if (in_array(strtolower($order->s_city), self::MetroCities) && in_array(strtolower($order->p_city), self::MetroCities)) {
            return 'c';
        } else {
            return 'd';
        }
    }
    public static function CheckCityNCRRegion($city){
        $ncrRegion = ['gurgaon', 'gurugram'];
        if(in_array(strtolower($city),$ncrRegion)){
            return true;
        }
        return false;
    }
    public static function CheckServicePincode($pincode, $courier_partner,$orderType = 'prepaid')
    {
        $count = ServiceablePincode::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->where('active','y');
        if($orderType == 'cod')
            $count = $count->where('is_cod','y');
        $count = $count->count();
        return intval($count);
    }
    public static function CheckServicePincodeFM($pincode, $courier_partner)
    {
        $count = ServiceablePincodeFM::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->count();
        return intval($count);
    }
    public static function CheckServicabilityShadowFax($pickup_pincode, $delivery_pincode)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76'
        ])->get("https://dale.shadowfax.in/api/v1/serviceability/?pickup_pincode=$pickup_pincode&delivery_pincode=$delivery_pincode&format=json");
        return $response->json();
    }

    public static function GetPartnerName($keyword){
        return self::PartnerNames[$keyword] ?? 'Other';
    }
    public static function GetZoneNameByKeyword($rateCriteria){
        if ($rateCriteria == 'within_city') {
            $zone = "A";
        } else if ($rateCriteria == 'within_state') {
            $zone = "B";
        } else if ($rateCriteria == 'rest_india') {
            $zone = "D";
        } else if ($rateCriteria == 'metro_to_metro') {
            $zone = "C";
        } else {
            $zone = "E";
        }
        return $zone ?? "E";
    }

    public static function getBlueDartAwbNumber($courier_keyword,$orderType,$sellerId = 0){
        $retryLimit = 5;
        $retryCount = 0;
        $success = false;
        do {
            try {
                DB::beginTransaction();

                $getAwbNumber = BluedartAwbNumbers::where('used', 'n')
                    ->where('courier_keyword', $courier_keyword)
                    ->where('awb_type', $orderType)
                    ->lockForUpdate()
                    ->first();

                if (!$getAwbNumber) {
                    DB::rollBack();
                    return false;
                }

                BluedartAwbNumbers::where('id', $getAwbNumber->id)
                    ->update([
                        'used' => 'y',
                        'used_time' => now(),
                        'used_by' => $sellerId
                    ]);

                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Logger::write('logs/shipping-helper-'.date('Y-m-d').'.text', [
                    'title' => 'Shipping Helper Deadlock',
                    'data' => ['message' => $e->getMessage(),$e->getFile(),$e->getLine()]
                ]);
                // Check if the exception is due to a deadlock
                if (str_contains($e->getMessage(), 'SQLSTATE[40001]')) {
                    $retryCount++;
                    if ($retryCount >= $retryLimit) {
                        return false; // If we exceeded retry limit, throw the exception
                    }
                    usleep(500000); // Wait for 0.5 seconds before retrying
                } else {
                    return false; // If it's not a deadlock, rethrow the exception
                }
            }
        } while (!$success && $retryCount < $retryLimit);
        return $getAwbNumber;
    }

    public static function getBlueDartNSEAwbNumber($courier_keyword,$orderType,$sellerId = 0){
        $retryLimit = 5;
        $retryCount = 0;
        $success = false;
        do {
            try {
                DB::beginTransaction();

                $getAwbNumber = BluedartNSEAwbNumbers::where('used', 'n')
                    ->where('courier_keyword', $courier_keyword)
                    ->where('awb_type', $orderType)
                    ->lockForUpdate()
                    ->first();

                if (!$getAwbNumber) {
                    DB::rollBack();
                    return false;
                }

                BluedartNSEAwbNumbers::where('id', $getAwbNumber->id)
                    ->update([
                        'used' => 'y',
                        'used_time' => now(),
                        'used_by' => $sellerId
                    ]);

                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Logger::write('logs/shipping-helper-'.date('Y-m-d').'.text', [
                    'title' => 'Shipping Helper Deadlock',
                    'data' => ['message' => $e->getMessage(),$e->getFile(),$e->getLine()]
                ]);
                // Check if the exception is due to a deadlock
                if (str_contains($e->getMessage(), 'SQLSTATE[40001]')) {
                    $retryCount++;
                    if ($retryCount >= $retryLimit) {
                        return false; // If we exceeded retry limit, throw the exception
                    }
                    usleep(500000); // Wait for 0.5 seconds before retrying
                } else {
                    return false; // If it's not a deadlock, rethrow the exception
                }
            }
        } while (!$success && $retryCount < $retryLimit);
        return $getAwbNumber;
    }
    public static function CheckSellerCustomChannel($partner,$sellerId){
        $resp = [
            'status' => false,
            'credentials' => null
        ];
        try{
            if(!empty(self::CustomCredentials[$partner][$sellerId])){
                $resp['credentials'] = CustomSellerChannels::where('seller_id',$sellerId)->where('courier_partner',$partner)->where('status','y')->first();
                if(!empty($resp['credentials']))
                    $resp['status'] = true;
                else
                    $resp['status'] = false;
            }else{
                $resp['status'] = false;
            }
        }
        catch(Exception $e){
            $resp['status'] = false;
        }
        return $resp;
    }
}
