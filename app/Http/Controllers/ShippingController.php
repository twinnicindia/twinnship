<?php
namespace App\Http\Controllers;

use App\Libraries\BlueDart;
use App\Libraries\BluedartRest;
use App\Libraries\BucketHelper;
use App\Libraries\CustomBlueDart;
use App\Libraries\CustomBluedartRest;
use App\Libraries\Maruti;
use App\Libraries\MarutiEcom;
use App\Libraries\MyUtility;
use App\Models\Basic_informations;
use App\Models\Bluedart_details;
use App\Models\Configuration;
use App\Models\DefaultInvoiceAmount;
use App\Models\InternationalOrders;
use App\Models\ManifestationIssues;
use App\Models\Order;
use App\Models\Partners;
use App\Models\PendingShipments;
use App\Models\Preferences;
use App\Models\Product;
use App\Models\Rules;
use App\Models\Seller;
use App\Models\ServiceablePincode;
use App\Models\ServiceablePincodeFM;
use App\Models\Transactions;
use App\Models\Warehouses;
use App\Models\XbeesAwbnumber;
use App\Models\XbeesAwbnumberUnique;
use App\Models\ZoneMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Libraries\Logger;
use App\Models\GatiAwbs;
use App\Models\GatiPackageNumber;
use App\Models\MPS_AWB_Number;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShippingController extends Controller{
    protected $info, $utilities, $status, $noOfvalue,$metroCities;
    function __construct()
    {
        $this->utilities = new Utilities();
        $this->info['config'] = Configuration::find(1);
        $this->metroCities=['bangalore','chennai','hyderabad','kolkata','mumbai','new delhi'];
    }

    function shipOrder($orderId){
        $o = Order::find($orderId);
        $sellerDetail=Seller::find($o->seller_id);
        if(empty($sellerDetail) || empty($o)){
            return false;
        }
        $rateCriteria = $this->_findMatchCriteria($orderId);
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
        $preference = $this->_getShippingRate($orderId);
        $returnValue = false;
        if ($preference == false) {
            $courier_partners = [$sellerDetail->courier_priority_1,$sellerDetail->courier_priority_2,$sellerDetail->courier_priority_3,$sellerDetail->courier_priority_4];
            $courier_partners = array_unique($courier_partners);
            foreach ($courier_partners as $courier_partner){
                $partner = Partners::where('keyword', $courier_partner)->where('status', 'y')->first();
                $resp = $this->_ProcessOrder($o,$partner,$sellerDetail,$zone,$rateCriteria);
                if($resp){
                    $returnValue = true;
                    break;
                }
            }
        } else {
            $courier_partners = [$preference->priority1,$sellerDetail->priority2,$sellerDetail->priority3,$sellerDetail->priority4];
            $courier_partners = array_unique($courier_partners);
            foreach ($courier_partners as $courier_partner) {
                $partner = Partners::where('keyword', $courier_partner)->where('status', 'y')->first();
                $resp = $this->_ProcessOrder($o, $partner, $sellerDetail, $zone, $rateCriteria, $total_amount);
                if ($resp) {
                    $returnValue = true;
                    break;
                }
            }
        }
        return $returnValue;
    }
    function _getShippingRate($order)
    {
        $orderDetail = Order::find($order);
        $sellerDetail = Seller::find($orderDetail->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $wareHouse = Warehouses::where('seller_id', $sellerDetail->id)->where('default', 'y')->get();
        if (count($wareHouse) == 0) {
            echo json_encode(array('error' => 'default warehouse not selected'));
            exit;
        }
        $prefs = Preferences::where('seller_id', $sellerDetail->id)->where('status', 'y')->orderBy('priority')->get();
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
                            if ($wareHouse[0]->pincode == $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if ($wareHouse[0]->pincode != $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            if ($this->_matchFromArray($r->match_value, $wareHouse[0]->pincode))
                                $match++;
                        }
                        break;
                    case 'delivery_pincode':
                        if ($r->match_type == 'is') {
                            if ($orderDetail->pincode == $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'is_not') {
                            if ($orderDetail->pincode != $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            if ($this->_matchFromArray($r->match_value, $orderDetail->pincode))
                                $match++;
                        } else if ($r->match_type == 'starts_with') {
                            if ($this->_startsWith($orderDetail->pincode, $r->match_value))
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
                            if ($orderDetail->weight <= $r->match_value * 1000)
                                $match++;
                        } else if ($r->match_type == 'greater_than') {
                            if ($orderDetail->weight > $r->match_value * 1000)
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
                                if ($this->_startsWith($pr->product_name, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'contain') {
                            $found = false;
                            foreach ($products as $pr) {
                                if ($this->_containString($pr->product_name, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            $found = false;
                            foreach ($products as $pr) {
                                if ($this->_matchFromArray($r->match_value, $pr->product_name)) {
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
                                if ($this->_startsWith($pr->product_sku, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'contain') {
                            $found = false;
                            foreach ($products as $pr) {
                                if ($this->_containString($pr->product_sku, $r->match_value)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if ($found)
                                $match++;
                        } else if ($r->match_type == 'any_of') {
                            $found = false;
                            foreach ($products as $pr) {
                                if ($this->_matchFromArray($r->match_value, $pr->product_sku)) {
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
    function _startsWith($string, $startString)
    {
        $string = strtolower($string);
        $startString = strtolower($startString);
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    function _containString($string, $search)
    {
        $string = strtolower($string);
        $search = strtolower($search);
        $result = strpos($string, $search);
        if ($result === false)
            return false;
        else
            return true;
    }

    function _matchFromArray($string, $search)
    {
        $string = strtolower($string);
        $search = strtolower($search);
        $master = explode(',', $string);
        return in_array($search, $master);
    }

    function _checkServicePincode($pincode, $courier_partner)
    {
        $service = ServiceablePincode::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->count();
        return $service;
    }

    function _checkServicePincodeFM($pincode, $courier_partner)
    {
        $service = ServiceablePincodeFM::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->count();
        return $service;
    }

    function _wowExpress($orderId)
    {
        $o = Order::find($orderId);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $vol_weight = ($o->height * $o->length * $o->breadth) / 5;
        $payload = [
            "api_key" => "20681",
            "transaction_id" => "",
            "order_no" => "$o->order_number",
            "consignee_first_name" => $o->s_customer_name,
            "consignee_last_name" => "",
            "consignee_address1" => $o->s_address_line1,
            "consignee_address2" => $o->s_address_line2,
            "destination_city" => $o->s_city,
            "destination_pincode" => $o->s_pincode,
            "state" => $o->s_state,
            "telephone1" => $o->s_contact,
            "telephone2" => "",
            "vendor_name" => $o->p_customer_name,
            "vendor_address" => $o->p_address_line1,
            "vendor_city" => $o->p_city,
            "pickup_pincode" => $o->p_pincode,
            "vendor_phone1" => $o->p_contact,
            "rto_vendor_name" => $o->p_customer_name,
            "rto_address" => $o->p_address_line1,
            "rto_city" => $o->p_city,
            "rto_pincode" => $o->p_pincode,
            "rto_phone" => $o->p_contact,
            "pay_type" => $pay_type,
            "item_description" => $o->product_name,
            "qty" => $qty,
            "collectable_value" => $collectable_value,
            "product_value" => $o->invoice_amount,
            "actual_weight" => $o->weight / 1000,
            "volumetric_weight" => $vol_weight / 1000,
            "length" => "$o->length",
            "breadth" => "$o->breadth",
            "height" => "$o->height",
            "category" => ""
        ];
        // dd($payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wowship.wowexpress.in/index.php/alltracking/create_shipment_v1/doUpload',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function _delhiverySurface($orderId,$delhiveryClient="TwinnshipIN SURFACE",$delhiveryToken="894217b910b9e60d3d12cab20a3c5e206b739c8b")
    {
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        if($o->o_type == 'reverse'){
            $pay_type = "Pickup";
        }
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        $warehouse= Warehouses::where('id',$o->warehouse_id)->first();
        $payload = [
            "shipments" => array(
                [
                    "add" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_address_line1). " " . preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_address_line2),
                    "address_type" => "home",
                    "phone" => $o->s_contact,
                    "payment_mode" => $pay_type,
                    "name" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->b_customer_name),
                    "pin" => $o->s_pincode,
                    "order" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->order_number),
                    "consignee_gst_amount" => "100",
                    "integrated_gst_amount" => "100",
                    "ewbn" => $o->ewaybill_number,
                    "consignee_gst_tin" => "",
                    "seller_gst_tin" => "",
                    "client_gst_tin" => "",
                    "hsn_code" => $config->hsn_number,
                    "gst_cess_amount" => "0",
                    "client" => $delhiveryClient,
                    "tax_value" => "100",
                    "seller_tin" => "Twinnship",
                    "seller_gst_amount" => "100",
                    "seller_inv" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->order_number),
                    "city" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_city),
                    "commodity_value" => $o->invoice_amount,
                    "weight" => $o->weight,
                    "return_state" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_state),
                    "document_number" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->order_number),
                    "od_distance" => "450",
                    "sales_tax_form_ack_no" => "1245",
                    "document_type" => "document",
                    "seller_cst" => "1343",
                    "seller_name" => $seller_name,
                    "fragile_shipment" => "true",
                    "return_city" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_city),
                    "return_phone" => $o->p_contact,
                    "shipment_height" => $o->height,
                    "shipment_width" => $o->breadth,
                    "shipment_length" => $o->length,
                    "category_of_goods" => "categoryofgoods",
                    "cod_amount" => $collectable_value,
                    "return_country" => $o->p_country,
                    "document_date" => $o->inserted,
                    "taxable_amount" => $o->invoice_amount,
                    "products_desc" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->product_name),
                    "state" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_state),
                    "dangerous_good" => "False",
                    "waybill" => "",
                    "consignee_tin" => "1245875454",
                    "order_date" => $o->inserted,
                    "return_add" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_city). " , " . preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_state),
                    "total_amount" => $o->invoice_amount ?? 1,
                    "seller_add" => preg_replace('/[^A-Za-z0-9\-]/', '', $seller->city) ." , " .preg_replace('/[^A-Za-z0-9\-]/', '', $seller->state),
                    "country" => $o->p_country,
                    "return_pin" => $o->p_pincode,
                    "extra_parameters" => [
                        "return_reason" => ""
                    ],
                    "return_name" => $o->p_warehouse_name,
                    "supply_sub_type" => "",
                    "plastic_packaging" => "false",
                    "quantity" => $qty
                ]
            ),
            "pickup_location" => [
                "name" => $warehouse->warehouse_code,
                "city" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_city),
                "pin" => $o->p_pincode,
                "country" => $o->p_country,
                "phone" => $o->p_contact,
                "add" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_address_line1) . " , " . preg_replace('/[^A-Za-z0-9\-]/', '', $o->p_address_line2)
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'format=json&data=' . json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token '.$delhiveryToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function _delhiveryMPS($orderId, $delhiveryToken="18765103684ead7f379ec3af5e585d16241fdb94") {
        try {
            DB::beginTransaction();

            $order = Order::find($orderId);
            $seller = Seller::find($order->seller_id);
            $config = Configuration::first();
            $qty = Product::where('order_id', $orderId)->sum('product_qty');
            if (strtolower($order->order_type) == 'cod') {
                $pay_type = "COD";
                $collectable_value = $order->invoice_amount;
            } elseif (strtolower($order->order_type) == 'prepaid') {
                $pay_type = "Prepaid";
                $collectable_value = "0";
            } else {
                $pay_type = "REVERSE";
                $collectable_value = "0";
            }
            if ($order->o_type == 'reverse') {
                $pay_type = "Pickup";
            }
            $seller_name = $seller->first_name . ' ' . $seller->last_name;
            $seller = Basic_informations::where('seller_id', $seller->id)->first();
            //$warehouse = Warehouses::where('id', $order->warehouse_id)->first();
            $warehouse = Warehouses::where('id', $order->warehouse_id)->first();

            // Get waybill number and master id
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://track.delhivery.com/waybill/api/bulk/json/?token='.$delhiveryToken.'&count='.$order->number_of_packets,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
                CURLOPT_HTTPHEADER => [],
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $waybillNumber = explode(',', trim($response, '"'));

            $shipments = [];
            for($i=0; $i<$order->number_of_packets; $i++) {
                $shipments[] = [
                    "weight" => $order->weight,
                    "mps_amount" => $order->order_type == "cod" ? ($order->invoice_amount) : "0",
                    "mps_children" => $order->number_of_packets,
                    "seller_inv" => $order->order_number,
                    "city" => preg_replace('/[^A-Za-z0-9\-]/', '', $order->s_city),
                    "pin" => $order->s_pincode,
                    "products_desc" =>preg_replace('/[^A-Za-z0-9\-]/', '', $order->product_name),
                    "product_type" => "Heavy",
                    "extra_parameters" => [
                        "encryptedShipmentID" => "DdB6bvvFN"
                    ],
                    "add" => $order->s_address_line1 . " " . $order->s_address_line2,
                    "shipment_type" => "MPS",
                    "hsn_code" => $config->hsn_number,
                    "state" => $order->s_state,
                    "waybill" => $waybillNumber[$i] ?? null,
                    "supplier" => $seller_name,
                    "master_id" => $waybillNumber[0] ?? null,
                    "sst" => "-",
                    "phone" => $order->s_contact,
                    "payment_mode" => $pay_type,
                    "cod_amount" => $order->order_type == "cod" ? $order->invoice_amount : "0",
                    "order_date" => $order->inserted,
                    "name" => $order->s_customer_name,
                    "total_amount" => ($order->invoice_amount),
                    "country" => $order->p_country,
                    "order" => $order->order_number,
                    "ewbn" => $order->ewaybill_number,
                ];
                if($i == 0) {
                    $order->awb_number = $waybillNumber[$i] ?? null;
                    $order->save();
                } else {
                    $mps = new MPS_AWB_Number();
                    $mps->order_id = $order->id;
                    $mps->awb_number = $waybillNumber[$i] ?? null;
                    $mps->inserted = now();
                    $mps->save();
                }
            }
            $payload = [
                "shipments" => $shipments,
                "pickup_location" => [
                    "name" => $warehouse->warehouse_code,
                    "city" => $order->p_city,
                    "pin" => $order->p_pincode,
                    "country" => $order->p_country,
                    "phone" => $order->p_contact,
                    "add" => "$order->p_address_line1 , $order->p_address_line2"
                ]
            ];
            Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                'title' => 'Delhivery MPS Request Payload',
                'data' => $payload
            ]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'format=json&data=' . json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Token $delhiveryToken",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $json = json_decode($response);
            if($json->success == true) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $response;
        } catch(Exception $e) {
            DB::rollBack();
            return "";
        }
    }

    function _dtdcSurface($orderId, $serviceType = "GROUND EXPRESS")
    {
        $o = Order::find($orderId);
		// $apiKey = ($o->seller_id == 16 || $o->seller_id == 150) ? "fefdb6dc8c709b2128fd24490be6df" : "f1f881d18d4b2204af76ff6282c476";
        // $customerCode = ($o->seller_id == 16 || $o->seller_id == 150) ? "GL3980" : "GL2367";
		// $serviceType = ($o->seller_id == 16 || $o->seller_id == 150) ? "B2C SMART EXPRESS" : $serviceType;

        $seller = Seller::find($o->seller_id);
        if(empty($seller)) {
            return false;
        }
        $apiKey = "fefdb6dc8c709b2128fd24490be6df";
            $customerCode = "GL3980";
            $serviceType = $serviceType;
        // if(strtoupper($seller->seller_order_type) == 'NSE') {
        //     $apiKey = "fefdb6dc8c709b2128fd24490be6df";
        //     $customerCode = "GL3980";
        //     $serviceType = "B2C SMART EXPRESS";
        // } else {
        //     $apiKey = "f1f881d18d4b2204af76ff6282c476";
        //     $customerCode = "GL2367";
        //     $serviceType = $serviceType;
        // }

        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $payload = [
            "consignments" => [
                [
                    "customer_code" => $customerCode,
                    "reference_number" => "",
                    "service_type_id" => $serviceType,
                    "load_type" => "NON-DOCUMENT",
                    "description" => "Gifts/Samples",
                    "cod_favor_of" => "",
                    "cod_collection_mode" => strtolower($o->order_type) == 'cod' ? 'Cash' : '',
                    "consignment_type" => ucfirst($o->o_type),
                    "dimension_unit" => "cm",
                    "length" => ceil($o->length),
                    "width" => ceil($o->breadth),
                    "height" => ceil($o->height),
                    "weight_unit" => "kg",
                    "weight" => round(($o->weight / 1000),2),
                    "declared_value" => $o->invoice_amount,
                    "cod_amount" => $collectable_value,
                    "num_pieces" => 1,
                    "customer_reference_number" => "",
                    "commodity_id" => "GIFT",
                    "is_risk_surcharge_applicable" => true,
                    "origin_details" => [
                        "name" => $o->p_warehouse_name,
                        "phone" => $o->p_contact,
                        "alternate_phone" => $o->p_contact,
                        "address_line_1" => $o->p_address_line1,
                        "address_line_2" => $o->p_address_line2,
                        "pincode" => $o->p_pincode,
                        "city" => $o->p_city,
                        "state" => $o->p_state
                    ],
                    "destination_details" => [
                        "name" => strlen($o->s_customer_name) > 5 ? $o->s_customer_name : $o->s_customer_name."*****",
                        "phone" => $o->s_contact,
                        "alternate_phone" => $o->s_contact,
                        "address_line_1" => $o->s_address_line1,
                        "address_line_2" => $o->s_address_line2,
                        "pincode" => $o->s_pincode,
                        "city" => $o->s_city,
                        "state" => $o->s_state,
                    ],
                    "pieces_detail" => [
                        [
                            "description" => $o->product_name,
                            "declared_value" => $o->invoice_amount,
                            "weight" => round(($o->weight / 1000),2),
                            "height" => ceil($o->height),
                            "length" => ceil($o->length),
                            "width" => ceil($o->breadth)
                        ]
                    ]
                ]
            ]
        ];
        //file_put_contents("payload.txt", json_encode($payload));
        // dd($payload);
        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://app.shipsy.in/api/customer/integration/consignment/softdata', $payload);

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'DTDC Response Payload',
            'data' => $payload
        ]);
        // echo $response;
        return $response->json();
    }

    function _getXbeesToken($username,$password,$secret){
        $data = [
            "username" => $username,
            "password" => $password,
            "secretkey" => $secret
        ];

        // $data = [
        //     "username" => "admin@Twinnship.com",
        //     "password" => '$Twinnship$',
        //     "secretkey" => "e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc"
        // ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer xyz'
        ])->post('http://userauthapis.xbees.in/api/auth/generateToken', $data);
        // echo $response;
        $data = $response->json();
        return $data['token'] ?? false;
    }

    function _xpressBees($orderId,$getAwbNumber,$businessName,$username,$password,$secret,$XBkey){
        $token = $this->_getXbeesToken($username,$password,$secret);
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

        //$getAwbNumber = XbeesAwbnumber::where('used','n')->first();
        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "BusinessAccountName" => $businessName,
            "OrderNo" => $o->customer_order_number,
            "SubOrderNo" => $o->order_number,
            "OrderType" => $o->order_type,
            "CollectibleAmount" => $collectable_value,
            "DeclaredValue" => $o->invoice_amount,
            "PickupType" => "Vendor",
            "Quantity" => $qty,
            "ServiceType" => "SD",
            "DropDetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                        "City" => $o->s_city,
                        "EmailID" => $o->b_customer_email,
                        "Name" => $o->b_customer_name,
                        "PinCode" => $o->s_pincode,
                        "State" => $o->s_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => $o->s_contact,
                        "Type" => "Primary",
                        "VirtualNumber" => null
                    ]
                ],
                "IsGenSecurityCode" => null,
                "SecurityCode" => null,
                "IsGeoFencingEnabled" => null,
                "Latitude" => null,
                "Longitude" => null,
                "MaxThresholdRadius" => null,
                "MidPoint" => null,
                "MinThresholdRadius" => null,
                "RediusLocation" => null
            ],
            "PickupDetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "EmailID" => "",
                        "Name" => $o->p_warehouse_name,
                        "PinCode" => $o->p_pincode,
                        "State" => $o->p_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => $o->p_contact,
                        "Type" => "Primary"
                    ]
                ],
                "PickupVendorCode" => "ORUF1THL3Y0SJ",
                "IsGenSecurityCode" => null,
                "SecurityCode" => null,
                "IsGeoFencingEnabled" => null,
                "Latitude" => null,
                "Longitude" => null,
                "MaxThresholdRadius" => null,
                "MidPoint" => null,
                "MinThresholdRadius" => null,
                "RediusLocation" => null
            ],
            "RTODetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "EmailID" => "",
                        "Name" => $o->p_warehouse_name,
                        "PinCode" => $o->p_pincode,
                        "State" => $o->p_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => $o->p_contact,
                        "Type" => "Primary"
                    ]
                ]
            ],
            "Instruction" => "",
            "CustomerPromiseDate" => null,
            "IsCommercialProperty" => null,
            "IsDGShipmentType" => null,
            "IsOpenDelivery" => null,
            "IsSameDayDelivery" => null,
            "ManifestID" => "SGHJDX1554362X",
            "MultiShipmentGroupID" => null,
            "SenderName" => null,
            "IsEssential" => "false",
            "IsSecondaryPacking" => "false",
            "PackageDetails" => [
                "Dimensions" => [
                    "Height" => $o->height,
                    "Length" => $o->length,
                    "Width" => $o->length
                ],
                "Weight" => [
                    "BillableWeight" => $o->weight / 1000,
                    "PhyWeight" => $o->weight / 1000,
                    "VolWeight" => $o->weight / 1000
                ]
            ],
            "GSTMultiSellerInfo" => [
                [
                    "BuyerGSTRegNumber" => $seller->gst_number,
                    "EBNExpiryDate" => null,
                    "EWayBillSrNumber" => $getAwbNumber->awb_number,
                    "InvoiceDate" => date('d-m-Y'),
                    "InvoiceNumber" => "IN$o->id",
                    "InvoiceValue" => null,
                    "IsSellerRegUnderGST" => "Yes",
                    "ProductUniqueID" => null,
                    "SellerAddress" => $seller->street,
                    "SellerGSTRegNumber" => $seller->gst_number,
                    "SellerName" => $seller->company_name,
                    "SellerPincode" => $seller->pincode,
                    "SupplySellerStatePlace" => $seller->state,
                    "HSNDetails" => [
                        [
                            "ProductCategory" => "Retail",
                            "ProductDesc" => $o->product_name,
                            "CGSTAmount" => null ?? 0,
                            "Discount" => null,
                            "GSTTAXRateIGSTN" => null,
                            "GSTTaxRateCGSTN" => null,
                            "GSTTaxRateSGSTN" => null,
                            "GSTTaxTotal" => null,
                            "HSNCode" => $config->hsn_number,
                            "IGSTAmount" => null ?? 0,
                            "ProductQuantity" => null,
                            "SGSTAmount" => null ?? 0,
                            "TaxableValue" => null
                        ]
                    ]
                ]
            ]
        ];
        // dd($payload);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/forward', $payload);
        // echo $response;
        return $response->json();
    }

    function _xpressBeesReverse($orderId,$getAwbNumber,$businessName,$username,$password,$secret,$XBkey){
        $token = $this->_getXbeesToken($username,$password,$secret);
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        // $getAwbNumber = XbeesAwbnumber::where('order_type','reverse')->where('used','n')->first();

        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "OrderNo" => $o->customer_order_number,
            "BusinessAccountName" => $businessName,
            "ProductID" => $o->customer_order_number.''.$o->id,
            "Quantity" => $qty,
            "ProductName" => $o->product_name,
            "Instruction" => "",
            "IsCommercialProperty" => "",
            "CollectibleAmount" => "0",
            "ProductMRP" => $o->invoice_amount,
            "DropDetails" => [
                "Addresses" => [
                    [
                        "Type" => "Primary",
                        "Name" => $o->p_warehouse_name,
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "State" => $o->p_state,
                        "PinCode" => $o->p_pincode,
                        "EmailID" => "",
                        "Landmark" => "",
                    ]
                ],
                "ContactDetails" => [
                    [
                        "Type" => "Primary",
                        "PhoneNo" => $o->p_contact
                    ]
                ],
                "IsGenSecurityCode" => "",
                "SecurityCode" => "",
                "IsGeoFencingEnabled" => "",
                "Longitude" => "",
                "Latitude" => "",
                "RadiusLocation" => "",
                "MidPoint" => "",
                "MinThresholdRadius" => "",
                "MaxThresholdRadius" => ""
            ],
            "PickupDetails" => [
                "Addresses" => [
                    [
                        "Type" => "Primary",
                        "Name" => $o->s_customer_name,
                        "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                        "City" => $o->s_city,
                        "State" => $o->s_state,
                        "PinCode" => $o->s_pincode,
                        "EmailID" => "",
                        "Landmark" => ""
                    ]
                ],
                "ContactDetails" => [
                    [
                        "Type" => "Primary",
                        "VirtualNumber" => "",
                        "PhoneNo" => $o->s_contact
                    ]
                ],
                "IsPickupPriority" => "1",
                "PriorityRemarks" => "High value shipments",
                "PickupSlotsDate" => "",
                "IsGenSecurityCode" => "",
                "SecurityCode" => "",
                "IsGeoFencingEnabled" => "0",
                "Longitude" => "",
                "Latitude" => "",
                "RadiusLocation" => "",
                "MidPoint" => "",
                "MinThresholdRadius" => "",
                "MaxThresholdRadius" => ""
            ],
            "PackageDetails" => [
                "Dimensions" => [
                    "Height" => $o->height,
                    "Length" => $o->length,
                    "Width" => $o->bredth
                ],
                "Weight" => [
                    "BillableWeight" => $o->weight / 1000,
                    "PhyWeight" => $o->weight / 1000,
                    "VolWeight" => $o->weight / 1000
                ]
            ],
            "QCTemplateDetails" => [
                "TemplateId" => null,
                "TemplateCategory" => ""
            ],
            "TextCapture" => [
                [
                    "Label" => "",
                    "Type" => "",
                    "ValueToCheck" => ""
                ]
            ],
            "PickupProductImage" => [
                [
                    "ImageUrl" => "",
                    "TextToShow" => ""
                ]
            ],
            "CaptureImageRule" => [
                "MinImage" => "",
                "MaxImage" => ""
            ],
            "HelpContent" => [
                "Description" => "",
                "URL" => "",
                "IsMandatory" => ""
            ],
            "GSTMultiSellerInfo" => [
                [
                    "InvoiceNumber" => "IN$o->id",
                    "InvoiceDate" => date('d-m-Y'),
                    "InvoiceValue" => $o->invoice_amount,
                    "ProductUniqueID" => "",
                    "IsSellerRegUnderGST" => "",
                    "BuyerGSTRegNumber" => $seller->gst_number,
                    "SellerName" => $seller->company_name,
                    "SellerGSTRegNumber" => $seller->gst_number,
                    "SellerAddress" => $seller->street,
                    "SupplySellerStatePlace" => $seller->state,
                    "SellerPincode" => $seller->pincode,
                    "EBNExpiryDate" => "",
                    "EWayBillSrNumber" => "",
                    "HSNDetails" => [
                        [
                            "HSNCode" => $config->hsn_number,
                            "ProductCategory" => "Retail",
                            "ProductDesc" => $o->product_name,
                            "SGSTAmount" => null,
                            "CGSTAmount" => null,
                            "IGSTAmount" => null,
                            "GSTTaxTotal" => null,
                            "TaxableValue" => null,
                            "Discount" => null,
                            "GSTTaxRateCGSTN" => null,
                            "GSTTaxRateSGSTN" => null,
                            "GSTTAXRateIGSTN" => null
                        ]
                    ]
                ]
            ]
        ];

        // dd($payload);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/reverse', $payload);
        // echo $response;
        return $response->json();
    }

    function _checkServicabilityShadowFax($pickup_pincode,$delivery_pincode){
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76'
        ])->get("https://dale.shadowfax.in/api/v1/serviceability/?pickup_pincode=$pickup_pincode&delivery_pincode=$delivery_pincode&format=json");
        return $response->json();
    }

    function _shadowFax($orderId){
        // $token = $this->_getXbeesToken();
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

        //for generate AWB Number
        $response = $this->_generateAWBShadowFax();
        $awb_number = $response['awb_numbers'][0];
        // dd($awb_number);
        $products=[];
        foreach($product as $p){
            $products[]=[
                "hsn_code" => "",
                "invoice_no" => "SNP678",
                "sku_name" => $p->product_sku,
                "client_sku_id" => "",
                "category" => "",
                "price" => round($product_price),
                "seller_details" => [
                    "seller_name" => $seller_name,
                    "seller_address" => $seller->street,
                    "seller_state" => $seller->state,
                    "gstin_number" => $seller->gst_number
                ],
                "taxes" => [
                    "cgst" => 3,
                    "sgst" => 4,
                    "igst" => 0,
                    "total_tax" => 7
                ],
                "additional_details" => [
                    "requires_extra_care" => "False",
                    "type_extra_care" => "Normal Goods"
                ]
            ];
        }

        $promised_delivery_date=Date('Y-m-d', strtotime('+3 days'))."T00:00:00.000Z";
        $payload = [
            "order_details" => [
                "client_order_id" => $o->customer_order_number,
                "awb_number" => $awb_number,
                "actual_weight" => $o->weight,
                "volumetric_weight" => ($o->height * $o->length * $o->breadth) / 5,
                "product_value" => $o->invoice_amount,
                "payment_mode" => $pay_type,
                "cod_amount" => $collectable_value,
                "promised_delivery_date" => $promised_delivery_date,
                "total_amount" => $o->invoice_amount
            ],
            "customer_details" => [
                "name" => $o->b_customer_name,
                "contact" => $o->b_contact,
                "address_line_1" => $o->b_address_line1,
                "address_line_2" => $o->b_address_line2,
                "city" => $o->b_city,
                "state" => $o->b_state,
                "pincode" => $o->b_pincode,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "pickup_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
                "latitude" => "",
                "longitude" => ""
            ],
            "rts_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
            ],
            "product_details" => $products
        ];
        // echo json_encode($payload);
        // exit;
        //  dd($payload);
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/orders/', $payload);
        // echo $response;
        return $response->json();
    }

    function _shadowFaxReverse($orderId){
        // $token = $this->_getXbeesToken();
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

        //for generate AWB Number
        $response = $this->_generateAWBShadowFaxReverese();
        $awb_number = $response['awb_numbers'][0];

        // dd($awb_number);
        $products=[];
        foreach($product as $p){
            $products[]=[
                "client_sku_id" => $o->id,
                "name" => $p->product_sku,
                "price" => round($product_price),
                "return_reason" => "xyz",
                "brand" => "xyz",
                "category" => "xyz",
                "additional_details" => [
                    "type_extra_care" => "Dangerous Goods",
                    "color" => "xyz",
                    "serial_no" => "ABC.$o->id",
                    "sku_images" => [
                        "",
                        ""
                    ],
                    "requires_extra_care" => false,
                    "quantity" => $p->product_qty,
                    "size" => 8
                ],
                "seller_details" => [
                    "state" => $seller->state,
                    "regd_address" => $seller->street,
                    "regd_name" => $seller_name,
                    "gstin" => $seller->gst_number
                ],
                "taxes" => [
                    "total_tax_amount" => 18,
                    "igst_amount" => 18,
                    "cgst_amount" => 0,
                    "sgst_amount" => 0
                ],
                "hsn_code" => "",
                "invoice_no" => "In.$o->id"
            ];
        }

        $promised_delivery_date=Date('Y-m-d', strtotime('+3 days'))."T00:00:00.000Z";
        // $payload = [
        //     "client_order_number" => $o->customer_order_number,
        //     "request_type" => "pickup",
        //     "client_request_id" => $o->customer_order_number,
        //     "client_id" => $o->customer_order_number,
        //     "destination_pincode" => null,
        //     "price" => $o->invoice_amount,
        //     "address_attributes" => [
        //       "name" => $o->p_warehouse_name,
        //       "phone_number" => $o->p_contact,
        //       "address_line" => $o->p_address_line1.' '.$o->p_address_line2,
        //       "city" => $o->p_city,
        //       "state" => $o->p_state,
        //       "c    ountry" => $o->p_country,
        //       "pincode" => $o->p_pincode,
        //       "created_at" => "",
        //       "updated_at" => "",
        //       "alternate_contact" => ""
        //     ],
        //     "skus_attributes" => $products,
        //     "seller_attributes" => [
        //       "name" => $o->p_warehouse_name,
        //       "phone_number" => $o->p_contact,
        //       "address_line" => $o->p_address_line1,
        //       "city" => $o->p_city,
        //       "state" => $o->p_state,
        //       "pincode" => $o->p_pincode,
        //       "created_at" => "",
        //       "updated_at" => "",
        //       "alternate_contact" => null
        //     ],
        //     "status_last_updated_at" => "",
        //     "pickup_request_state_histories" => null,
        //     "status" => "New",
        //     "scheduled_date" => "",
        //     "date_created" => $o->inserted,
        //     "pickup_type" => "regular",
        //     "slot_start_time" => null,
        //     "slot_end_time" => null,
        //     "total_amount" => $o->invoice_amount,
        //     "address_type" => null,
        //     "invoice_date" => null,
        //     "eway_bill_number" => null,
        //     "awb_number" => $awb_number
        // ];

        $payload = [
            "client_order_number" => $o->customer_order_number,
            "total_amount" => $o->invoice_amount,
            "price" => $o->invoice_amount,
            "eway_bill" => "",
            "address_attributes" => [
                "address_line" => $o->s_address_line1.' '.$o->s_address_line2,
                "city" => $o->s_city,
                "country" => $o->s_country,
                "pincode" => $o->s_pincode,
                "name" => $o->s_customer_name,
                "phone_number" => $o->s_contact,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "seller_attributes" => [
                "name" => $o->p_warehouse_name,
                "address_line" => $o->p_address_line1.' '.$o->p_address_line2,
                "city" => $o->p_city,
                "pincode" =>  $o->p_pincode,
                "phone" =>  $o->p_contact
            ],
            "skus_attributes" => $products
        ];
        // echo json_encode($payload);
        // exit;
        //  dd($payload);
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/requests', $payload);
        // echo $response;
        return $response->json();

    }

    function _generateAWBShadowFax(){
        $payload = [
            'count' => 1
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/generate_marketplace_awb/', $payload);
        return $response->json();
    }

    function _generateAWBShadowFaxReverese(){
        $payload = [
            'count' => 1
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/orders/generate_awb/', $payload);
        return $response->json();
    }

    function _generateAwbUdaan(){
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
        ])->post('https://udaan.com/api/udaan-express/integration/v1/awb-store/create?logisticsPartnerOrgId=ORGZPKZ992460QL8GPWW4JDZGLC67&awbCount=1');
        $data = $response->json();
        return $data['response'][0];
    }

    function _udaanExpress($orderId){
        $o = Order::find($orderId);
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $product_price = $o->invoice_amount / count($product);
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount * 100;
        } else {
            $pay_type = "PPD";
            $collectable_value = "0";
        }
        if (strtolower($o->o_type) == 'forward') {
            $order_type = "FORWARD";
        }else{
            $order_type = "REVERSE";
        }

        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        $awb_number = $this->_generateAwbUdaan();
        // dd($awb_number);

        $products=[];
        foreach($product as $p){
            $products[]=[
                "itemTitle"=> $p->product_name,
                "hsnCode"=> "",
                "unitPrice"=> round($product_price) * 100,
                "unitQty"=> $p->product_qty ?? 1,
                "taxPercentage"=> 0
            ];
        }
        $warehouse = Warehouses::where('id',$o->warehouse_id)->first();
        $payload = [
            "awbNumber" => $awb_number,
            "orderId" => $o->customer_order_number,
            "orderType" => $order_type,
            "orderParty" => "THIRD_PARTY",
            "orderPartyOrgId" => "ORGZPKZ992460QL8GPWW4JDZGLC67",
            "sourceOrgUnitDetails"=> [
                "orgUnitId"=> $warehouse->org_unit_id ?? "",
                "representativePersonName"=> $warehouse->contact_name,
                "unitName"=> $warehouse->warehouse_code,
                "contactNumPrimary"=> $o->p_contact,
                "contactNumSecondary"=> "",
                "gstIn"=> "",
                "address"=> [
                    "addressLine1"=> $o->p_address_line1,
                    "addressLine2"=> isset($o->p_address_line2) ? $o->p_address_line2 : $o->p_address_line1,
                    "addressLine3"=> "",
                    "city"=> $o->p_city,
                    "state"=> $o->p_state,
                    "pincode"=> $o->p_pincode
                ]
            ],
            "billToOrgUnitDetails"=> [
                "orgUnitId"=> "ORGZPKZ992460QL8GPWW4JDZGLC67",
                "representativePersonName"=> "Kaushal Sharma",
                "unitName"=> "Twinnship",
                "contactNumPrimary"=> "+91-9910995659",
                "contactNumSecondary"=> "",
                "gstIn"=> "06ABECS8200N1Z5",
                "address"=> [
                    "addressLine1"=> "House No 544,sector 29",
                    "addressLine2"=> "Faridabad",
                    "addressLine3"=> "",
                    "city"=> "Faridabad",
                    "state"=> "Hariyana",
                    "pincode"=> "121008"
                ]
            ],
            "destinationOrgUnitDetails"=> [
                "representativePersonName"=> $o->b_customer_name,
                "unitName"=> $o->b_customer_name,
                "contactNumPrimary"=> $o->s_contact,
                "contactNumSecondary"=> "",
                "gstIn"=> "",
                "address"=> [
                    "addressLine1"=> $o->s_address_line1,
                    "addressLine2"=> $o->s_address_line2 ?? "",
                    "addressLine3"=> "",
                    "city"=> $o->s_city,
                    "state"=>$o->s_state,
                    "pincode"=> $o->s_pincode
                ]
            ],
            "category" => "Default",
            "collectibleAmount" => $collectable_value,
            "boxDetails"=> [
                "numOfBoxes"=> 1,
                "totalBoxWeight"=> 0,
                "boxDetails"=> []
            ],
            "goodsDetails"=> [
                "goodsDetailsList"=> $products
            ],
            "goodsInvoiceDetails" => [
                "invoiceNumber" => "INV.$orderId",
                "ewayBill" => "",
                "invoiceDocUrls" => ["link"],
                "goodsInvoiceAmount" => $o->invoice_amount * 100,
                "goodsInvoiceTaxAmount" => 0
            ],
            "orderNotes" => ""
        ];
        //echo json_encode($payload); exit;
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
        ])->post('https://udaan.com/api/udaan-express/integration/v1/confirm', $payload);
        // echo $response;
        return $response->json();
    }

    function _ProcessOrder($o,$partner,$sellerDetail,$zone,$rateCriteria){
        if($o->weight > $o->vol_weight)
            $weight=$o->weight;
        else
            $weight=$o->vol_weight;

        // Change weight to 500gm if weight is <= 1500gm for amazon amazon_swa_1kg
        if($partner->keyword == 'amazon_swa_1kg' && $weight <= 1500) {
            $weight = 500;
        }

        $extra=($weight - $partner->weight_initial) > 0 ? $weight - $partner->weight_initial : 0;
        $mul=ceil($extra / $partner->extra_limit);
        //$mul = ceil($o->weight / 500) - 1;
        $plan_id = $sellerDetail->plan_id;

        $seller_id = $sellerDetail->id;
        $partner_rate = DB::select("select *,$rateCriteria + ( extra_charge_".strtolower($zone)." * $mul ) as price from rates where plan_id=$plan_id and partner_id = $partner->id and seller_id = $seller_id limit 1");
        // $partner_rate = Rates::select("$rateCriteria as price", 'cod_charge','cod_maintenance')->where('partner_id', $partner->id)->where('plan_id', $sellerDetail->plan_id)->first();
        $courier_partner = $partner->keyword;
        $shipping_charge = $partner_rate[0]->price;
        if(strtolower($o->o_type) == 'reverse'){
            $shipping_charge  = ($shipping_charge * $sellerDetail->reverse_charge) / 100;
        }
        $shipping_charge += ($shipping_charge * 18) / 100;
        $cod_maintenance = $partner_rate[0]->cod_maintenance;

        if (strtolower($o->order_type) == 'prepaid') {
            $cod_charge = "0";
            $early_cod = "0";
        } else {
            $cod_charge = ($o->invoice_amount * $cod_maintenance) / 100;
            if ($cod_charge < $partner_rate[0]->cod_charge)
                $cod_charge = $partner_rate[0]->cod_charge;
            $cod_charge += ($cod_charge * 18) / 100;
            $early_cod = ($o->invoice_amount * $sellerDetail->early_cod_charge) / 100;
            $early_cod += ($early_cod * 18) / 100;
        }

        $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
        $rto_charge = ($shipping_charge) * $sellerDetail->rto_charge / 100;
        $total_charge = round($shipping_charge + $cod_charge + $early_cod);
        if($courier_partner == 'delhivery_surface')
            $total_charge = $total_charge + 1.18;
        $seller_balance = $sellerDetail->balance;
        if ($total_charge <= $seller_balance) {
            if(strtolower($o->status) == 'pending'){
                if($courier_partner == 'xpressbees_sfc')
						$courier_partner = 'xpressbees_surface';
                if($courier_partner == 'wow_express') {
                    $status = $this->_checkServicePincode($o->s_pincode, $courier_partner);
                    if (intval($status) > 0) {
                        $status =  $this->_wowExpress($o->id);
                        $data = json_decode($status);
                        if ($data == null) {
                            return false;
                        } elseif (isset($data->response[0]->error)) {
                            return false;
                        }
                        $awb_number = $data->response[0]->awbno;
                    } else {
                        return false;
                    }
                }
                elseif($courier_partner == 'delhivery_surface') {
                    $status = $this->_checkServicePincode($o->s_pincode, $courier_partner);
                    if (intval($status) > 0) {
                        $status =  $this->_delhiverySurface($o->id);
                        $data = json_decode($status);
                        if ($data->success == false) {
                            return false;
                        }else{
                            $awb_number = $data->packages[0]->waybill;
                        }
                    } else {
                        return false;
                    }
                }
                elseif($courier_partner == 'dtdc_surface') {
                    $status =  $this->_dtdcSurface($o->id);
                    if ($status['data'][0]['success'] == true) {
                        $awb_number = $status['data'][0]['reference_number'];
                    } else {
                        return false;
                    }
                }
                elseif($courier_partner == 'xpressbees_surface') {
                    if($o->o_type=='forward'){
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('kEVUGEG3450nSssVzZQ','xpressbees_surface','forward','FORWARD');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBees($o->id,$getAwbNumber,"Twinnship","admin@Twinnship.com",'$Twinnship$',"e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc","kEVUGEG3450nSssVzZQ");
                    }
                    else{
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('kEVUGEG3450nSssVzZQ','xpressbees_surface','reverse','REVERSE');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBeesReverse($o->id,$getAwbNumber,"Twinnship","admin@Twinnship.com",'$Twinnship$',"e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc","kEVUGEG3450nSssVzZQ");
                    }
                    if ($status['ReturnCode'] == '100') {
                        $awb_number = $status['AWBNo'];
                        $token_number = $status['TokenNumber'];
                        XbeesAwbnumber::where('id',$getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s')]);
                    } else {
                        return false;
                    }
                }
                elseif($courier_partner == 'xpressbees_surface_3kg' || $courier_partner == 'xpressbees_surface_1kg' || $courier_partner == 'xpressbees_sfc'){
                    if($o->o_type=='forward'){
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface_3kg')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('aSNDKedk3586OIPdSKsIESSK','xpressbees_surface_3kg','forward','FORWARD');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface_3kg')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBees($o->id,$getAwbNumber,"Twinnship SFC 3","admin@shipesfc3.com",'$shipesfc3$',"58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd","aSNDKedk3586OIPdSKsIESSK");
                    }
                    else{
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface_3kg')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('aSNDKedk3586OIPdSKsIESSK','xpressbees_surface_3kg','reverse','REVERSE');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface_3kg')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBeesReverse($o->id,$getAwbNumber,"Twinnship SFC 3","admin@shipesfc3.com",'$shipesfc3$',"58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd","aSNDKedk3586OIPdSKsIESSK");
                    }
                    if ($status['ReturnCode'] == '100') {
                        $awb_number = $status['AWBNo'];
                        $token_number = $status['TokenNumber'];
                        XbeesAwbnumber::where('id',$getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s')]);
                    } else {
                        return false;
                    }
                }
                elseif($courier_partner == 'xpressbees_surface_5kg' || $courier_partner == 'xpressbees_surface_10kg'){
                    if($o->o_type=='forward'){
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface_5kg')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('fsSEKs3587kdPKDAkdrSNsSJ','xpressbees_surface_5kg','forward','FORWARD');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','forward')->where('courier_partner','xpressbees_surface_5kg')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBees($o->id,$getAwbNumber,"Twinnship SFC 5","admin@shipesfc5.com",'$shipesfc5$',"4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4","fsSEKs3587kdPKDAkdrSNsSJ");
                    }
                    else{
                        $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface_5kg')->first();
                        if(empty($getAwbNumber)){
                            $this->_getAwbNumbersXbees('fsSEKs3587kdPKDAkdrSNsSJ','xpressbees_surface_5kg','reverse','REVERSE');
                            $getAwbNumber = XbeesAwbnumber::where('used','n')->where('order_type','reverse')->where('courier_partner','xpressbees_surface_5kg')->first();
                        }
                        if(empty($getAwbNumber)){
                            return false;
                        }
                        $status =  $this->_xpressBeesReverse($o->id,$getAwbNumber,"Twinnship SFC 5","admin@shipesfc5.com",'$shipesfc5$',"4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4","fsSEKs3587kdPKDAkdrSNsSJ");
                    }
                    if ($status['ReturnCode'] == '100') {
                        $awb_number = $status['AWBNo'];
                        $token_number = $status['TokenNumber'];
                        XbeesAwbnumber::where('id',$getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s')]);
                    } else {
                        return false;
                    }
                }
                elseif ($courier_partner == 'shadow_fax') {
                    $servicibility = $this->_checkServicabilityShadowFax($o->p_pincode,$o->s_pincode);
                    if($servicibility['Serviceability'] == true){
                        $data =  $this->_shadowFax($o->id);
                        if($data['message'] == 'Failure'){
                            return false;
                        }else{
                            $awb_number = $data['data']['awb_number'];
                        }
                    }else{
                        return false;
                    }
                }
                elseif($courier_partner == 'udaan' || $courier_partner == 'udaan_1kg' || $courier_partner == 'udaan_2kg' || $courier_partner == 'udaan_3kg' || $courier_partner == 'udaan_10kg'){
                    $data = $this->_udaanExpress($o->id);
                    if($data['responseCode'] == 'UE_1001'){
                        $awb_number = $data['response']['awbNumber'];
                    }else{
                        return false;
                    }
                }
                else {
                    return false;
                }
                $awb = $awb_number;
                if(empty($awb_number)){
                    return false;
                }
                $barcode = @file_get_contents("https://www.Twinnship.in/barcode/test.php?code=$awb");
                @file_put_contents("assets/seller/images/Barcode/$awb.png", $barcode);
                $shipped_data = array(
                    'status' => 'shipped',
                    'courier_partner' => $courier_partner,
                    'awb_number' => $awb ?? "",
                    'shipping_charges' => round($shipping_charge, 2),
                    'cod_charges' => round($cod_charge, 2),
                    'early_cod_charges' => round($early_cod, 2),
                    'rto_charges' => round($rto_charge, 2),
                    'gst_charges' => round($gst_charge, 2),
                    'total_charges' => $total_charge,
                    'zone' => $zone,
                    'awb_assigned_date' => date('Y-m-d H:i:s'),
                    'awb_barcode' => "assets/seller/images/Barcode/$awb.png"
                );
                Order::where('id', $o->id)->update($shipped_data);
                //wallet deduction
                $transaction_check = Transactions::where('seller_id',$sellerDetail->id)->where('order_id',$o->id)->count();
                if($transaction_check == 0){
                    //$sellerDeta = Seller::find($sellerDetail->id);
                    $data = array(
                        'seller_id' => $sellerDetail->id,
                        'order_id' => $o->id,
                        'amount' => $total_charge,
                        'balance' => $sellerDetail->balance - $total_charge,
                        'type' => 'd',
                        'redeem_type' => 'o',
                        'datetime' => date('Y-m-d H:i:s'),
                        'method' => 'wallet',
                        'description' => 'Order Shipping Charge Deducted'
                    );
                    Transactions::create($data);
                    Seller::where('id', $sellerDetail->id)->decrement('balance', $data['amount']);
                    //Session(['MySeller' => Seller::find($sellerDetail->id)]);
                }
            }
        }
        else{
            return false;
        }
        return true;
    }
    function _getAwbNumbersXbees($XBkey,$courier,$type,$service="FORWARD")
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);
        $awb_data = $response->json();
        $this->_FetchAllAwbs($awb_data['BatchID'],$courier,$type,$service,$XBkey);
    }
    function _FetchAllAwbs($batch,$courier,$type,$service="FORWARD",$XBkey='')
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'BatchID' => $batch
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/TrackingService.svc/GetAWBNumberGeneratedSeries', $data);
        $awb_data = $response->json();
        if ($awb_data['ReturnCode'] == 100) {
            $insData = [];
            foreach ($awb_data['AWBNoSeries'] as $awb) {
                $insData[] = [
                    'awb_number' => $awb,
                    'order_type' => strtolower($type),
                    'courier_partner' => strtolower($courier),
                    'batch_number' => $awb_data['BatchID']
                ];
                if (count($insData) == 2500) {
                    XbeesAwbnumber::insert($insData);
                    $insData = [];
                }
            }
            XbeesAwbnumber::insert($insData);
        }
    }
    function _cancelBlueDartOrder($orderData,$customCredentials=null){
        try {
            if(!empty($customCredentials) && $customCredentials['status']){
                $bluedart = new CustomBluedartRest($customCredentials['credentials'],$orderData->courier_partner);
            }
            else{
                if ($orderData->courier_partner == 'bluedart_surface' && $orderData->is_alpha == 'NSE')
                    $bluedart = new BluedartRest('NSE');
                else {
                    if ($orderData->is_alpha == 'NSE')
                        $bluedart = new BluedartRest('NSE');
                    else
                        $bluedart = new BluedartRest();
                }
            }
            return $bluedart->cancelWayBill($orderData->awb_number);
        }
        catch(Exception $e){
            return false;
        }
    }
    function _cancelOrderWowExpress($awb){
        $data = array(
            'api_key' => '20681',
            'airwaybilno' => $awb,
            'action_type' => 'cancel'
        );

        $response = Http::withHeaders([
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'postman-token' => 'f611a94e-976e-e6dc-61a5-206625e54bdb'
        ])->post('https://wowship.wowexpress.in/index.php/api/cancel_pickup',$data);
        $response = $response->json();
    }
    function _cancelOrderDelhiverySurface($awb,$token="5f922aaa22343e75749cd486b10a92c6cf1d75ce"){
        $data = array(
            'waybill' => $awb,
            'cancellation' => 'true'
        );

        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);

        $response = Http::withHeaders([
            "authorization" => "Token $token",
            "cache-control" => "no-cache",
            "content-type" => "application/json",
            "postman-token" => "904fad58-6048-407c-ced2-aeb93deb7051"
        ])->post('https://track.delhivery.com/api/p/edit', $data);
        $response = $response->json();

        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    function _cancelOrderDtdcSurface($awb)
    {
        $order = Order::where('awb_number',$awb)->first();
        if(empty($order))
            return false;
        $data = array(
            'AWBNo' => ["$awb"],
            'customerCode' => 'GL3980'
        );

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Basic ZTA4MjE1MGE3YTQxNWVlZjdkMzE0NjhkMWRkNDY1Og==",
            "Content-Type" => "application/json",
        ])->post('https://app.shipsy.in/api/client/integration/consignment/cancellation', $data);
        $response = $response->json();

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
        return true;
    }
	function _cancelOrderDtdcSurfaceUnique($awb)
    {
		$order = Order::where('awb_number',$awb)->first();
		if(empty($order))
			return false;
		// $apiKey = ($order->seller_id == 16 || $order->seller_id == 150) ? "e082150a7a415eef7d31468d1dd465" : "ZTA4MjE1MGE3YTQxNWVlZjdkMzE0NjhkMWRkNDY1Og==";

        if(strtoupper($order->seller_order_type) == 'NSE') {
            $apiKey = "e082150a7a415eef7d31468d1dd465";
        } else {
            $apiKey = "ZTA4MjE1MGE3YTQxNWVlZjdkMzE0NjhkMWRkNDY1Og==";
        }

        $data = array(
            'AWBNo' => ["$awb"],
            'customerCode' => 'GL3980'
        );

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
        ])->withBasicAuth("e082150a7a415eef7d31468d1dd465","e082150a7a415eef7d31468d1dd465")->post('https://app.shipsy.in/api/client/integration/consignment/cancellation', $data);
        $response = $response->json();

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    function _cancelOrderXpressBees($awb, $XBkey)
    {
        $data = array(
            'XBkey' => $XBkey,
            'AWBNumber' => $awb,
            'RTOReason' => 'Fraud Cancellation'
        );

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);

        $response = Http::withHeaders([
            "cache-control" => "no-cache",
            "content-type" => "application/json",
            "postman-token" => "3886d97b-364b-2748-c922-314f489a1f12"
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/RTONotifyShipment', $data);
        $response = $response->json();

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    function _cancelReverseOrderXpressBees($awb, $XBkey)
    {
        $data = array(
            'XBkey' => $XBkey,
            'AWBNumber' => $awb,
            'RTOReason' => 'Fraud Cancellation'
        );

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);

        $order = Order::where('awb_number',$awb)->first();

        if($order->seller_order_type == 'NSE' && $order->courier_partner == 'xpressbees_sfc'){
            $response = Http::withHeaders([
                "cache-control" => "no-cache",
                "content-type" => "application/json",
                "postman-token" => "3886d97b-364b-2748-c922-314f489a1f12"
            ])->post('https://clientshipupdatesapi.xbees.in/reversecancellation', $data);
        }
        else {
            $response = Http::withHeaders([
                "cache-control" => "no-cache",
                "content-type" => "application/json",
                "postman-token" => "3886d97b-364b-2748-c922-314f489a1f12"
            ])->post('https://xbclientapi.xbees.in/POSTShipmentService.svc/ReversePickupCancellation', $data);
        }
        $response = $response->json();

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    function _cancelOrderShadowFax($awb){
        $data = array(
            'request_id' => $awb,
            'cancel_remarks' => 'Request cancelled by customer'
        );
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v1/clients/orders/cancel/?format=json',$data);
        $response = $response->json();
    }
    function _cancelOrderUdaan($awb){
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
        ])->post("https://udaan.com/api/udaan-express/integration/v1/cancel/$awb");
        $response = $response->json();
    }
	function _findMatchCriteria($orderId){
        $orderDetail = Order::find($orderId);
        $column = '';
        $res=ZoneMapping::where('pincode',$orderDetail->s_pincode)->where('picker_zone','E')->get();
        $ncrArray = ['gurgaon','noida','ghaziabad','faridabad','delhi','new delhi','gurugram'];
        if(in_array(strtolower($orderDetail->s_city),$ncrArray) && in_array(strtolower($orderDetail->p_city),$ncrArray)){
            return 'within_city';
        }
        else if (strtolower($orderDetail->s_city) == strtolower($orderDetail->p_city) && strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_city';
        }
        else if(count($res)==1){
            return 'north_j_k';
        }
        else if (strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_state';
        }
        else if(in_array(strtolower($orderDetail->s_city),$this->metroCities) && in_array(strtolower($orderDetail->p_city),$this->metroCities)){
            return 'metro_to_metro';
        }
        else {
            return 'rest_india';
        }
    }
    function _SendManifestationXpressBees($orderId, $awbNumber, $businessName, $username, $password, $secret, $XBkey,$sellerDetail)
    {
        $token = $this->_getXbeesToken($username, $password, $secret);
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $weight = $o->weight > $o->vol_weight ? $o->weight : $o->vol_weight;
        if($weight > 15000)
            $weight -= 4000;
        else if($weight > 7000)
            $weight -= 3000;
        else if($weight > 5000)
            $weight -= 2000;
        else if($weight > 3000)
            $weight -= 1000;
        else if($weight > 2000)
            $weight -= 1000;
        else
            $weight = 500;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        $payload = [
            "AirWayBillNO" => $awbNumber,
            "BusinessAccountName" => $businessName,
            "OrderNo" => $o->customer_order_number,
            "SubOrderNo" => $o->order_number,
            "OrderType" => $o->order_type,
            "CollectibleAmount" => $collectable_value,
            "DeclaredValue" => ($o->invoice_amount),
            "PickupType" => "Vendor",
            "Quantity" => $qty,
            "ServiceType" => "SD",
            "DropDetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                        "City" => $o->s_city,
                        "EmailID" => $o->b_customer_email,
                        "Name" => $o->b_customer_name,
                        "PinCode" => $o->s_pincode,
                        "State" => $o->s_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => str_replace("\u202c","",str_replace("\u202a","",trim($o->s_contact))),
                        "Type" => "Primary",
                        "VirtualNumber" => str_replace("\u202c","",str_replace("\u202a","",trim($o->s_contact))),
                    ]
                ],
                "IsGenSecurityCode" => null,
                "SecurityCode" => null,
                "IsGeoFencingEnabled" => null,
                "Latitude" => null,
                "Longitude" => null,
                "MaxThresholdRadius" => null,
                "MidPoint" => null,
                "MinThresholdRadius" => null,
                "RediusLocation" => null
            ],
            "PickupDetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "EmailID" => "",
                        "Name" => $o->p_warehouse_name,
                        "PinCode" => $o->p_pincode,
                        "State" => $o->p_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => $o->p_contact,
                        "Type" => "Primary"
                    ]
                ],
                "PickupVendorCode" => "ORUF1THL3Y0SJ",
                "IsGenSecurityCode" => null,
                "SecurityCode" => null,
                "IsGeoFencingEnabled" => null,
                "Latitude" => null,
                "Longitude" => null,
                "MaxThresholdRadius" => null,
                "MidPoint" => null,
                "MinThresholdRadius" => null,
                "RediusLocation" => null
            ],
            "RTODetails" => [
                "Addresses" => [
                    [
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "EmailID" => "",
                        "Name" => $o->p_warehouse_name,
                        "PinCode" => $o->p_pincode,
                        "State" => $o->p_state,
                        "Type" => "Primary"
                    ]
                ],
                "ContactDetails" => [
                    [
                        "PhoneNo" => $o->p_contact,
                        "Type" => "Primary"
                    ]
                ]
            ],
            "Instruction" => "",
            "CustomerPromiseDate" => null,
            "IsCommercialProperty" => null,
            "IsDGShipmentType" => null,
            "IsOpenDelivery" => null,
            "IsSameDayDelivery" => null,
            "ManifestID" => "SGHJDX1554362X",
            "MultiShipmentGroupID" => null,
            "SenderName" => null,
            "IsEssential" => "false",
            "IsSecondaryPacking" => "false",
            "PackageDetails" => [
                "Dimensions" => [
                    "Height" => $o->height,
                    "Length" => $o->length,
                    "Width" => $o->length
                ],
                "Weight" => [
                    "BillableWeight" => $weight / 1000,
                    "PhyWeight" => $weight / 1000,
                    "VolWeight" => $weight / 1000
                ]
            ],
            "GSTMultiSellerInfo" => [
                [
                    "BuyerGSTRegNumber" => $seller->gst_number,
                    "EBNExpiryDate" => null,
                    "EWayBillSrNumber" => $awbNumber,
                    "InvoiceDate" => date('d-m-Y'),
                    "InvoiceNumber" => "IN$o->id",
                    "InvoiceValue" => null,
                    "IsSellerRegUnderGST" => "Yes",
                    "ProductUniqueID" => null,
                    "SellerAddress" => $seller->street,
                    "SellerGSTRegNumber" => $seller->gst_number,
                    "SellerName" => $seller->company_name,
                    "SellerPincode" => $seller->pincode,
                    "SupplySellerStatePlace" => $seller->state,
                    "HSNDetails" => [
                        [
                            "ProductCategory" => "Retail",
                            "ProductDesc" => $o->product_name,
                            "CGSTAmount" => $o->cgst,
                            "Discount" => null,
                            "GSTTAXRateIGSTN" => null,
                            "GSTTaxRateCGSTN" => null,
                            "GSTTaxRateSGSTN" => null,
                            "GSTTaxTotal" => $o->igst == 0 ? ($o->sgst + $o->cgst) : $o->igst,
                            "HSNCode" => $config->hsn_number,
                            "IGSTAmount" => $o->igst,
                            "ProductQuantity" => null,
                            "SGSTAmount" => $o->sgst,
                            "TaxableValue" => null
                        ]
                    ]
                ]
            ]
        ];
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "XpressBees Forward Request Manifestation for AWB : $awbNumber",
            //'requestPayload' => $payload,
            'data' => $payload
        ]);
        // dd($payload);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/forward', $payload);
        // echo $response;
        $responseData = $response->json();
        $this->_addLog($responseData,"XpressBees Forward Manifestation for AWB : $awbNumber");
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "XpressBees Forward Manifestation for AWB : $awbNumber",
            //'requestPayload' => $payload,
            'data' => $responseData
        ]);
        if(!empty($responseData['ReturnCode']) && !empty($responseData['ReturnMessage']) && $responseData['ReturnCode'] == 100 && strtolower($responseData['ReturnMessage']) == "successful"){
            Order::where('id',$orderId)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
            return true;
        }
        else{

            if(!empty($responseData) && $responseData['ReturnMessage'] == 'Drop pincode not serviceable' || str_contains($responseData['ReturnMessage'],"ServiceType not accepted")){
                ServiceablePincode::where('courier_partner','xpressbees_sfc')->where('pincode',$o->s_pincode)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['ReturnMessage']]);
            }
            if(!empty($responseData) && $responseData['ReturnMessage'] == "AirWayBillNO Already exists"){
                Order::where('id',$orderId)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
                return true;
            }
        }
        return false;
        //return $response->json();
    }
    function _SendManiferstationXpressBeesReverse($orderId, $awbNumber, $businessName, $username, $password, $secret, $XBkey,$sellerDetail)
    {
        $token = $this->_getXbeesToken($username, $password, $secret);
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        $qcImage = [];
        if($o->is_qc == 'y'){
            $qcInfo = InternationalOrders::where('order_id',$o->id)->first();
            $image = explode(",",$qcInfo->qc_image);
            foreach ($image as $i){
                $i = str_replace("qc_image/",'',$i);
                $qcImage[] = [ "ImageUrl" => url('get-image')."?image=".$i,"TextToShow" => ""];
            }

            $l = explode(",",$qcInfo->qc_label);
            $v = explode(",",$qcInfo->qc_value_to_check);
            $data = [];
            for($i = 0;$i<count($l);$i++){
                $data[] = [
                    "Label" => $l[$i],
                    "Type" => "text",
                    "ValueToCheck" => $v[$i] ?? ""
                ];
            }
        }
        else{
            $qcImage[] = ["ImageUrl" => "", 'TextToShow' => ""];
        }

        $payload = [
            "AirWayBillNO" => $awbNumber,
            "OrderNo" => $o->customer_order_number,
            "BusinessAccountName" => $businessName,
            "ProductID" => $o->customer_order_number . '' . $o->id,
            "Quantity" => $qty,
            "ProductName" => $o->product_name,
            "Instruction" => "",
            "IsCommercialProperty" => "",
            "CollectibleAmount" => "0",
            "ProductMRP" => $o->invoice_amount,
            "DropDetails" => [
                "Addresses" => [
                    [
                        "Type" => "Primary",
                        "Name" => $o->p_warehouse_name,
                        "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                        "City" => $o->p_city,
                        "State" => $o->p_state,
                        "PinCode" => $o->p_pincode,
                        "EmailID" => "",
                        "Landmark" => "",
                    ]
                ],
                "ContactDetails" => [
                    [
                        "Type" => "Primary",
                        "PhoneNo" => $o->p_contact
                    ]
                ],
                "IsGenSecurityCode" => "",
                "SecurityCode" => "",
                "IsGeoFencingEnabled" => "",
                "Longitude" => "",
                "Latitude" => "",
                "RadiusLocation" => "",
                "MidPoint" => "",
                "MinThresholdRadius" => "",
                "MaxThresholdRadius" => ""
            ],
            "PickupDetails" => [
                "Addresses" => [
                    [
                        "Type" => "Primary",
                        "Name" => $o->s_customer_name,
                        "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                        "City" => $o->s_city,
                        "State" => $o->s_state,
                        "PinCode" => $o->s_pincode,
                        "EmailID" => "",
                        "Landmark" => ""
                    ]
                ],
                "ContactDetails" => [
                    [
                        "Type" => "Primary",
                        "VirtualNumber" => "",
                        "PhoneNo" => $o->s_contact
                    ]
                ],
                "IsPickupPriority" => "1",
                "PriorityRemarks" => "High value shipments",
                "PickupSlotsDate" => "",
                "IsGenSecurityCode" => "",
                "SecurityCode" => "",
                "IsGeoFencingEnabled" => "0",
                "Longitude" => "",
                "Latitude" => "",
                "RadiusLocation" => "",
                "MidPoint" => "",
                "MinThresholdRadius" => "",
                "MaxThresholdRadius" => ""
            ],
            "PackageDetails" => [
                "Dimensions" => [
                    "Height" => $o->height,
                    "Length" => $o->length,
                    "Width" => $o->bredth
                ],
                "Weight" => [
                    "BillableWeight" => $o->weight / 1000,
                    "PhyWeight" => $o->weight / 1000,
                    "VolWeight" => $o->weight / 1000
                ]
            ],
            "QCTemplateDetails" => [
                "TemplateId" => null,
                "TemplateCategory" => ""
            ],
            "TextCapture" =>$o->is_qc == 'y' ? $data : [["Label" => $o->is_qc == 'y' ? $qcInfo->qc_help_description : "", "Type" => "text","ValueToCheck" => $o->is_qc == 'y' ? $qcInfo->qc_value_to_check : ""]
            ],
            "PickupProductImage" => $qcImage,
            "CaptureImageRule" => [
                "MinImage" => "1",
                "MaxImage" => "6"
            ],
            "HelpContent" => [
                "Description" => $o->is_qc == 'y' ?  : "",
                "URL" => "",
                "IsMandatory" => $o->is_qc == 'y' ? "1" : ""
            ],
            "GSTMultiSellerInfo" => [
                [
                    "InvoiceNumber" => "IN$o->id",
                    "InvoiceDate" => date('d-m-Y'),
                    "InvoiceValue" => $o->invoice_amount,
                    "ProductUniqueID" => "",
                    "IsSellerRegUnderGST" => "",
                    "BuyerGSTRegNumber" => $seller->gst_number,
                    "SellerName" => $seller->company_name,
                    "SellerGSTRegNumber" => $seller->gst_number,
                    "SellerAddress" => $seller->street,
                    "SupplySellerStatePlace" => $seller->state,
                    "SellerPincode" => $seller->pincode,
                    "EBNExpiryDate" => "",
                    "EWayBillSrNumber" => "",
                    "HSNDetails" => [
                        [
                            "HSNCode" => $config->hsn_number,
                            "ProductCategory" => "Retail",
                            "ProductDesc" => $o->product_name,
                            "SGSTAmount" => null,
                            "CGSTAmount" => null,
                            "IGSTAmount" => null,
                            "GSTTaxTotal" => null,
                            "TaxableValue" => null,
                            "Discount" => null,
                            "GSTTaxRateCGSTN" => null,
                            "GSTTaxRateSGSTN" => null,
                            "GSTTAXRateIGSTN" => null
                        ]
                    ]
                ]
            ]
        ];

        if($o->seller_order_type == 'NSE'){
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'token' => $token,
                'versionnumber' => 'v1'
            ])->post('http://apishipmentmanifestation.xbees.in/shipmentmanifestation/reverse', $payload);
        }
        else {
            //dd(json_encode($payload));
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'token' => $token,
                'versionnumber' => 'v1'
            ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/reverse', $payload);
        }
        // echo $response;
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "XpressBees Reverse Manifestation Request for AWB : $awbNumber",
            'data' => $payload
        ]);
        $responseData = $response->json();
        $this->_addLog($responseData,"XpressBees Reverse Manifestation for AWB : $awbNumber");
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "XpressBees Reverse Manifestation for AWB : $awbNumber",
            'data' => $responseData
        ]);
        if($responseData['ReturnCode'] == 100 && strtolower($responseData['ReturnMessage']) == "successfull"){
            Order::where('id',$orderId)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
            return true;
        }
        else{
            if($responseData['ReturnMessage'] == 'Drop pincode not serviceable' || str_contains($responseData['ReturnMessage'],"ServiceType not accepted")){
                $serviceAble = ServiceablePincode::where('courier_partner','xpressbees_sfc')->where('pincode',$o->s_pincode)->first();
                //ServiceablePincode::where('courier_partner','xpressbees_sfc')->where('pincode',$o->s_pincode)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['ReturnMessage']]);
                ServiceablePincode::where('id',$serviceAble->id)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['ReturnMessage']]);
            }
        }
        return false;
        //return $response->json();
    }
    function _addLog($response, $text)
    {
        // $date=date('Y-m-d');
        // if(!is_dir('logs/channels')) {
        //     @mkdir('logs/channels');
        // } else {
        //     if(!is_dir('logs/channels/xbees')) {
        //         @mkdir('logs/channels/xbees');
        //     }
        // }
        // $myfile = fopen("logs/channels/xbees/xbees-$date.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, "\n" . $text . " ------- " . json_encode($response));
        // fclose($myfile);
    }

	function _generateAwbForXbeesUnique($o, $orderType, $courierPartner, $xbKey, $awbType)
    {
        $returnValue = ['status' => true, 'data' => []];
        if ($o->suggested_awb != "") {
            $getAwbNumber = XbeesAwbnumberUnique::where('awb_number', $o->suggested_awb)->first();
            if (empty($getAwbNumber)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
            if ($getAwbNumber->used == 'y') {
                $returnValue['status'] = false;
                return $returnValue;
            }
            $returnValue['data'] = $getAwbNumber;
            XbeesAwbnumberUnique::where('id', $getAwbNumber->id)->update(['used' => 'y']);
            return $returnValue;
        } else {
            DB::beginTransaction();
            $getAwbNumber = XbeesAwbnumberUnique::where('used', 'n')->where('assigned', 'n')->where('order_type', $orderType)->where('courier_partner', $courierPartner)->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                $this->_getAwbNumbersXbeesUnique($xbKey, $courierPartner, $orderType, $awbType);
                $getAwbNumber = XbeesAwbnumberUnique::where('used', 'n')->where('assigned', 'n')->where('order_type', $orderType)->where('courier_partner', $courierPartner)->lockForUpdate()->first();
            }
            if (empty($getAwbNumber)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
            $returnValue['data'] = $getAwbNumber;
            XbeesAwbnumberUnique::where('id', $getAwbNumber->id)->update(['used' => 'y']);
            DB::commit();
            return $returnValue;
        }
    }

	function _getAwbNumbersXbeesUnique($XBkey, $courier, $type, $service = "FORWARD")
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);
        $awb_data = $response->json();
        $this->_FetchAllAwbsUnique($awb_data['BatchID'], $courier, $type, $service, $XBkey);
    }

	function _FetchAllAwbsUnique($batch, $courier, $type, $service = "FORWARD", $XBkey = '')
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'BatchID' => $batch
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/TrackingService.svc/GetAWBNumberGeneratedSeries', $data);
        $awb_data = $response->json();
        if ($awb_data['ReturnCode'] == 100) {
            $insData = [];
            foreach ($awb_data['AWBNoSeries'] as $awb) {
                $insData[] = [
                    'awb_number' => $awb,
                    'order_type' => strtolower($type),
                    'courier_partner' => strtolower($courier),
                    'batch_number' => $awb_data['BatchID']
                ];
                if (count($insData) == 2500) {
                    XbeesAwbnumberUnique::insert($insData);
                    $insData = [];
                }
            }
            XbeesAwbnumberUnique::insert($insData);
        }
    }
    function SendManifestationDelhivery($orderId,$delhiveryClient,$delhiveryToken){
        $o = Order::find($orderId);
        if(empty($o))
            return false;
        $sellerData = Seller::find($o->seller_id);
        $config = $this->info['config'];
        $partnerData = Partners::where('keyword',$o->courier_partner)->first();
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        $defaultAmount = DefaultInvoiceAmount::where('seller_id',$sellerData->id)->where('partner_id',$partnerData->id)->first();
        $defaultInvoiceAmount = 0;
        if (strtolower($o->order_type) == 'prepaid')
        {
            $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
        }
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            if(intval($o->collectable_amount) > 0)
                $collectable_value = $o->collectable_amount;
            else
                $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $shippingMode = $o->courier_partner == "delhivery_air" ? "Express" : "Surface";
        if ($o->o_type == 'reverse') {
            $pay_type = "Pickup";
        }
        $seller_name = $sellerData->first_name . ' ' . $sellerData->last_name;
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        $warehouse = Warehouses::where('id', $o->warehouse_id)->first();
        $rtoWarehouse = $warehouse;
        if($o->same_as_rto == 'n'){
            $tempWarehouse = Warehouses::where('id',$o->rto_warehouse_id)->first();
            if(!empty($tempWarehouse))
                $rtoWarehouse = $tempWarehouse;
        }
        //$warehouse = Warehouses::where('id', $o->warehouse_id ?? 0)->first();
        if(empty($warehouse)){
            Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                'title' => "Delhivery Request Payload for - {$o->awb_number}",
                'data' => ['message' => 'Please Check the Warehouse First']
            ]);
            return false;
        }
        $weight = $o->weight > $o->vol_weight ? $o->weight : $o->vol_weight;
        if($weight > 15000)
            $weight -= 4000;
        else if($weight > 12000)
            $weight -= 3000;
        else if($weight > 9000)
            $weight -= 2000;
        else if($weight > 7000)
            $weight -= 1000;
        else if($weight > 5000)
            $weight -= 2000;
        else if($weight > 2000)
            $weight -= 1000;
        else
            $weight = 500;

        if($o->seller_id == 6729){
            $productName = Str::random(10)." ".Str::random(8);
        }
        else if($o->seller_id == 32508 || $o->seller_id == 32054){
            $productName = "Apricot";
        }
        else{
            $productName = preg_replace('/[^A-Za-z0-9\ ]/', '', $o->product_name);
        }

        $payload = [
            "shipments" => array(
                [
                    "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->s_address_line1). " " . preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->s_address_line2),
                    "address_type" => "home",
                    "shipping_mode" => $shippingMode,
                    "phone" => $o->s_contact,
                    "payment_mode" => $pay_type,
                    "name" => preg_replace('/[^A-Za-z0-9\ ]/', ' ',$o->s_customer_name),
                    "pin" => $o->s_pincode,
                    "order" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                    "consignee_gst_amount" => "100",
                    "integrated_gst_amount" => "100",
                    "ewbn" => $o->ewaybill_number,
                    "consignee_gst_tin" => "",
                    "seller_gst_tin" => "",
                    "client_gst_tin" => "",
                    "hsn_code" => $config->hsn_number,
                    "gst_cess_amount" => "0",
                    "client" => $delhiveryClient,
                    "tax_value" => "100",
                    "seller_tin" => "Twinnship",
                    "seller_gst_amount" => "100",
                    "seller_inv" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                    "city" => preg_replace('/[^A-Za-z0-9\-]/', '',$o->s_city),
                    "commodity_value" => $o->invoice_amount + $defaultInvoiceAmount,
                    "weight" => $weight,
                    "return_state" => preg_replace('/[^A-Za-z0-9\ ]/', ' ',$rtoWarehouse->state),
                    "document_number" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                    "od_distance" => "450",
                    "sales_tax_form_ack_no" => "1245",
                    "document_type" => "document",
                    "seller_cst" => "1343",
                    "seller_name" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller_name),
                    "fragile_shipment" => "true",
                    "return_city" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $rtoWarehouse->city),
                    "return_phone" => $rtoWarehouse->contact_number,
                    "shipment_height" => $o->height,
                    "shipment_width" => $o->breadth,
                    "shipment_length" => $o->length,
                    "category_of_goods" => "categoryofgoods",
                    "cod_amount" => $collectable_value,
                    "return_country" => "IN",
                    "document_date" => $o->inserted,
                    "taxable_amount" => $o->invoice_amount + $defaultInvoiceAmount,
                    "products_desc" => $productName,
                    "state" => preg_replace('/[^A-Za-z0-9\ ]/', '', $o->s_state),
                    "dangerous_good" => "False",
                    "waybill" => $o->awb_number,
                    "consignee_tin" => "1245875454",
                    "order_date" => $o->inserted,
                    "return_add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $rtoWarehouse->address_line1).",".preg_replace('/[^A-Za-z0-9\ ]/', ' ', $rtoWarehouse->address_line2),
                    "total_amount" => $o->invoice_amount + $defaultInvoiceAmount,
                    "seller_add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->city).",".preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->state),
                    "country" => "IN",
                    "return_pin" => $rtoWarehouse->pincode,
                    "extra_parameters" => [
                        "return_reason" => ""
                    ],
                    "return_name" => $rtoWarehouse->warehouse_code,
                    "supply_sub_type" => "",
                    "plastic_packaging" => "false",
                    "quantity" => $qty
                ]
            ),
            "pickup_location" => [
                "name" => $warehouse->warehouse_code,
                "city" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->p_city),
                "pin" => $o->p_pincode,
                "country" => "IN",
                "phone" => $o->p_contact,
                "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->p_address_line1).",". preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->p_address_line2)
            ]
        ];
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Delhivery Request Payload',
            'data' => $payload
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'format=json&data=' . json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token $delhiveryToken",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Delhivery Response Payload',
            'data' => $data
        ]);
        if (!empty($data->success) && $data->success != false) {
            Order::where('id',$orderId)->update(['manifest_sent' => 'y']);
            return true;
        }
        else{
            try{
                if(!empty($data)){
                    $message = $data->packages[0]->remarks[0] ?? "";
                    if($message == ""){
                        $message = $data->rmk ?? "";
                        if(str_contains($message,"matching query does not exist")){
                            MyUtility::CreateDelhiveryWarehouse($warehouse,$delhiveryToken);
                        }
                    }
                    if(trim($message) != ""){
                        ManifestationIssues::updateOrCreate(
                            ['order_id' => $o->id],
                            [
                                'order_id' => $o->id,
                                'message' => $message,
                                'created' => date('Y-m-d H:i:s')
                            ]
                        );
                        if(!str_contains($message,"matching query does not exist"))
                            Order::where('id',$o->id)->update(['is_retry' => 1]);
                    }
                }
                if(!empty($data->packages[0]->remarks[0]) && str_contains(strtolower($data->packages[0]->remarks[0]),"is non serviceable pincode")){
                    $serviceAble = ServiceablePincode::where('courier_partner','delhivery_surface')->where('pincode',$o->s_pincode)->first();
//                    ServiceablePincode::where('courier_partner','delhivery_surface')->where('pincode',$o->s_pincode)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $data->packages[0]->remarks[0]]);
                    ServiceablePincode::where('id',$serviceAble->id)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $data->packages[0]->remarks[0]]);
                }
            }catch(Exception $e){}
        }
        return false;
    }

    function _gatiMps($orderId) {
        try {
            DB::beginTransaction();
            $order = Order::find($orderId);
            if (strtolower($order->o_type) == 'forward') {
                $resp = ServiceablePincode::where('pincode', $order->s_pincode)->where('courier_partner', 'gati')->first();
                $awb = GatiAwbs::where('used', 'n')->lockForUpdate()->first();
                $packages = GatiPackageNumber::where('used', 'n')->limit($order->number_of_packets)->lockForUpdate()->get();
                if (empty($resp) || empty($awb) || empty($packages)) {
                    throw new Exception("Pincode is not Serviceable");
                }
                $i=0;
                foreach($packages as $package) {
                    if($i == 0) {
                        $order->awb_number = $awb->awb_number ?? null;
                        $order->gati_package_no = $package->package_number ?? null;
                        $order->manifest_sent = 'n';
                        $order->save();
                    } else {
                        $mps = new MPS_AWB_Number();
                        $mps->order_id = $order->id;
                        $mps->awb_number = $awb->awb_number ?? null;
                        $mps->gati_package_no = $package->package_number ?? null;
                        $mps->inserted = now();
                        $mps->save();
                    }
                    $i++;
                }
                GatiAwbs::where('id', $awb->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
                GatiPackageNumber::whereIn('id', $packages->pluck('id'))->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception("Pincode is not Serviceable");
            }
            DB::commit();
            return [
                'awb_number' => $order->awb_number,
                'package_number' => $order->gati_package_no,
            ];
        } catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    function _gati($orderId) {
        try {
            DB::beginTransaction();
            $order = Order::find($orderId);
            if (strtolower($order->o_type) == 'forward') {
                $resp = ServiceablePincode::where('pincode', $order->s_pincode)->where('courier_partner', 'gati')->first();
                $awb = GatiAwbs::where('used', 'n')->lockForUpdate()->first();
                $package = GatiPackageNumber::where('used', 'n')->lockForUpdate()->first();
                if (empty($resp) || empty($awb) || empty($packages)) {
                    throw new Exception("Pincode is not Serviceable");
                }
                $order->awb_number = $awb->awb_number ?? null;
                $order->gati_package_no = $package->package_number ?? null;
                $order->manifest_sent = 'n';
                $order->save();
                GatiAwbs::where('id', $awb->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
                GatiPackageNumber::where('id', $package->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception("Pincode is not Serviceable");
            }
            DB::commit();
            return [
                'awb_number' => $order->awb_number,
                'package_number' => $order->gati_package_no,
            ];
        } catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    function _cancelGatiOrder($order) {
        try{
            $data = [
                'name' => 'Twinnship Corporation',
                'mailContent' => "Gati AWB Number {$order->awb_number} cancelled by seller at date " .date('Y-m-d H:i:s') . ", please cancel it."
            ];
            $this->utilities->send_email("info.Twinnship@gmail.com","Twinnship Corporation",'OPS Twinnship',"Gati AWB Number {$order->awb_number} cancelled by seller at date " .date('Y-m-d H:i:s') . ", please cancel it.","Twinnship Corporation");
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    function _SendManifestationDTDCSurface($order){
        try {
            $payload = $this->_GenerateDtdcPayload($order);
            Logger::write('logs/partners/dtdc/dtdc-' . date('Y-m-d') . '.text', [
                'title' => "DTDC Request Payload for {$order->awb_number} : ",
                'data' => $payload
            ]);
            $response = Http::withHeaders([
                'api-key' => 'fefdb6dc8c709b2128fd24490be6df',
                'Content-Type' => 'application/json'
            ])->post('https://app.shipsy.in/api/customer/integration/consignment/softdata', $payload);
            Logger::write('logs/partners/dtdc/dtdc-' . date('Y-m-d') . '.text', [
                'title' => "DTDC Response Payload for {$order->awb_number} : ",
                'data' => $response->json()
            ]);
            $responseData = $response->json();
            if (isset($responseData['data'][0]['success'])) {
                if ($responseData['data'][0]['success']){
                    return true;
                }
                else{
                    if(!empty($responseData['data'][0]['reason'])){
                        if(strpos($responseData['data'][0]['message'],'NOT APPLICABLE FOR THIS PINCODE PAIR') !== false || strtolower($responseData['data'][0]['message']) == 'destination pincode is not serviceable'){
                            $serviceAble = ServiceablePincode::where('courier_partner','dtdc_surface')->where('pincode',$order->s_pincode)->first();
                            //ServiceablePincode::where('courier_partner','dtdc_surface')->where('pincode',$order->s_pincode)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['data'][0]['reason']]);
                            ServiceablePincode::where('id',$serviceAble->id)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['data'][0]['reason']]);
                            return false;
                        }
                    }
                }
            } else
                return false;
        }
        catch(Exception $e){
            return false;
        }
        return false;
    }
    function _GenerateDTDCPayload($order){
        $serviceType = "B2C SMART EXPRESS";
        $product = Product::where('order_id', $order->id)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $order->id)->sum('product_qty');
        if (strtolower($order->order_type) == 'cod') {
            $collectable_value = $order->invoice_amount;
        } elseif (strtolower($order->order_type) == 'prepaid') {
            $collectable_value = "0";
        } else {
            $collectable_value = "0";
        }
        $payload = [
            "consignments" => [
                [
                    "customer_code" => "GL3980",
                    "reference_number" => $order->awb_number,
                    "service_type_id" => $serviceType,
                    "load_type" => "NON-DOCUMENT",
                    "description" => "Gifts/Samples",
                    "cod_favor_of" => "",
                    "cod_collection_mode" => strtolower($order->order_type) == 'cod' ? 'Cash' : '',
                    "consignment_type" => ucfirst($order->o_type),
                    "dimension_unit" => "cm",
                    "length" => ceil($order->length),
                    "width" => ceil($order->breadth),
                    "height" => ceil($order->height),
                    "weight_unit" => "kg",
                    "weight" => number_format(($order->weight / 1000),2),
                    "declared_value" => $order->invoice_amount,
                    "cod_amount" => $collectable_value,
                    "num_pieces" => 1,
                    "customer_reference_number" => "",
                    "commodity_id" => "GIFT",
                    "is_risk_surcharge_applicable" => true,
                    "origin_details" => [
                        "name" => $order->p_warehouse_name,
                        "phone" => $order->p_contact,
                        "alternate_phone" => $order->p_contact,
                        "address_line_1" => strlen($order->p_address_line1) < 3 ? $order->p_address_line1." *** " : substr($order->p_address_line1,0,249),
                        "address_line_2" => strlen($order->p_address_line2) < 3 ? $order->p_address_line2." *** " : substr($order->p_address_line2,0,249),
                        "pincode" => $order->p_pincode,
                        "city" => $order->p_city,
                        "state" => strtoupper($order->p_state)
                    ],
                    "destination_details" => [
                        "name" => strlen($order->s_customer_name) > 5 ? $order->s_customer_name : $order->s_customer_name."*****",
                        "phone" => $order->s_contact,
                        "alternate_phone" => $order->s_contact,
                        "address_line_1" => strlen($order->s_address_line1) < 3 ? $order->s_address_line1." *** " : substr($order->s_address_line1,0,249),
                        "address_line_2" => strlen($order->s_address_line2) < 3 ? $order->s_address_line2." *** " : substr($order->s_address_line2,0,249),
                        "pincode" => $order->s_pincode,
                        "city" => $order->s_city,
                        "state" => strtoupper($order->s_state),
                    ],
                    "pieces_detail" => [
                        [
                            "description" => substr($order->product_name,0,250),
                            "declared_value" => $order->invoice_amount,
                            "weight" => number_format(($order->weight / 1000),2),
                            "height" => ceil($order->height),
                            "length" => ceil($order->length),
                            "width" => ceil($order->breadth)
                        ]
                    ]
                ]
            ]
        ];
        //dd($payload);
        return $payload;
    }

    function _generateUdaanPayload($o){
        $orderId = $o->id;
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return false;
        }
        $product = Product::where('order_id', $orderId)->get();
        $product_price = $o->invoice_amount / count($product);
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount * 100;
        } else {
            $pay_type = "PPD";
            $collectable_value = "0";
        }
        if (strtolower($o->o_type) == 'forward') {
            $order_type = "FORWARD";
        }else{
            $order_type = "REVERSE";
        }

        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
        $awb_number = $o->awb_number;
        // dd($awb_number);

        $products=[];
        foreach($product as $p){
            $products[]=[
                "itemTitle"=> $p->product_name,
                "hsnCode"=> "",
                "unitPrice"=> round($product_price) * 100,
                "unitQty"=> $p->product_qty ?? 1,
                "taxPercentage"=> 0
            ];
        }
        $warehouse = Warehouses::where('id',$o->warehouse_id)->first();
        $payload = [
            "awbNumber" => $awb_number,
            "orderId" => $o->customer_order_number,
            "orderType" => $order_type,
            "orderParty" => "THIRD_PARTY",
            "orderPartyOrgId" => "ORGZPKZ992460QL8GPWW4JDZGLC67",
            "sourceOrgUnitDetails"=> [
                "orgUnitId"=> $warehouse->org_unit_id ?? "",
                "representativePersonName"=> $warehouse->contact_name,
                "unitName"=> $warehouse->warehouse_code,
                "contactNumPrimary"=> $o->p_contact,
                "contactNumSecondary"=> "",
                "gstIn"=> "",
                "address"=> [
                    "addressLine1"=> $o->p_address_line1,
                    "addressLine2"=> isset($o->p_address_line2) ? $o->p_address_line2 : $o->p_address_line1,
                    "addressLine3"=> "",
                    "city"=> $o->p_city,
                    "state"=> $o->p_state,
                    "pincode"=> $o->p_pincode
                ]
            ],
            "billToOrgUnitDetails"=> [
                "orgUnitId"=> "ORGZPKZ992460QL8GPWW4JDZGLC67",
                "representativePersonName"=> "Kaushal Sharma",
                "unitName"=> "Twinnship",
                "contactNumPrimary"=> "+91-9910995659",
                "contactNumSecondary"=> "",
                "gstIn"=> "06ABECS8200N1Z5",
                "address"=> [
                    "addressLine1"=> "House No 544,sector 29",
                    "addressLine2"=> "Faridabad",
                    "addressLine3"=> "",
                    "city"=> "Faridabad",
                    "state"=> "Hariyana",
                    "pincode"=> "121008"
                ]
            ],
            "destinationOrgUnitDetails"=> [
                "representativePersonName"=> $o->b_customer_name,
                "unitName"=> $o->b_customer_name,
                "contactNumPrimary"=> $o->s_contact,
                "contactNumSecondary"=> "",
                "gstIn"=> "",
                "address"=> [
                    "addressLine1"=> $o->s_address_line1,
                    "addressLine2"=> $o->s_address_line2 ?? "",
                    "addressLine3"=> "",
                    "city"=> $o->s_city,
                    "state"=>$o->s_state,
                    "pincode"=> $o->s_pincode
                ]
            ],
            "category" => "Default",
            "collectibleAmount" => $collectable_value,
            "boxDetails"=> [
                "numOfBoxes"=> 1,
                "totalBoxWeight"=> 0,
                "boxDetails"=> []
            ],
            "goodsDetails"=> [
                "goodsDetailsList"=> $products
            ],
            "goodsInvoiceDetails" => [
                "invoiceNumber" => "INV.$orderId",
                "ewayBill" => "",
                "invoiceDocUrls" => ["link"],
                "goodsInvoiceAmount" => $o->invoice_amount * 100,
                "goodsInvoiceTaxAmount" => 0
            ],
            "orderNotes" => ""
        ];
        return $payload;
    }

    function _generateWowExpressPayload($o)
    {
        $orderId = $o->id;
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $vol_weight = ($o->height * $o->length * $o->breadth) / 5;
        $payload = [
            "api_key" => "20681",
            "transaction_id" => "",
            "order_no" => "$o->order_number",
            "consignee_first_name" => $o->b_customer_name,
            "consignee_last_name" => "",
            "consignee_address1" => $o->b_address_line1,
            "consignee_address2" => $o->b_address_line2,
            "destination_city" => $o->b_city,
            "destination_pincode" => $o->s_pincode,
            "state" => $o->b_state,
            "telephone1" => $o->b_contact,
            "telephone2" => "",
            "vendor_name" => $o->p_customer_name,
            "vendor_address" => $o->p_address_line1,
            "vendor_city" => $o->p_city,
            "pickup_pincode" => $o->p_pincode,
            "vendor_phone1" => $o->p_contact,
            "rto_vendor_name" => $o->p_customer_name,
            "rto_address" => $o->p_address_line1,
            "rto_city" => $o->p_city,
            "rto_pincode" => $o->p_pincode,
            "rto_phone" => $o->p_contact,
            "pay_type" => $pay_type,
            "item_description" => $o->product_name,
            "qty" => $qty,
            "collectable_value" => $collectable_value,
            "product_value" => $o->invoice_amount,
            "actual_weight" => $o->weight / 1000,
            "volumetric_weight" => $vol_weight / 1000,
            "length" => "$o->length",
            "breadth" => "$o->breadth",
            "height" => "$o->height",
            "category" => ""
        ];
        return $payload;
    }

    function _generateShadowFaxPayload($o) {
        $orderId = $o->id;
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return [];
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

        $awb_number = $o->awb_number;
        // dd($awb_number);
        $products=[];
        foreach($product as $p){
            $products[]=[
                "hsn_code" => "",
                "invoice_no" => "SNP678",
                "sku_name" => $p->product_sku,
                "client_sku_id" => "",
                "category" => "",
                "price" => round($product_price),
                "seller_details" => [
                    "seller_name" => $seller_name,
                    "seller_address" => $seller->street,
                    "seller_state" => $seller->state,
                    "gstin_number" => $seller->gst_number
                ],
                "taxes" => [
                    "cgst" => 3,
                    "sgst" => 4,
                    "igst" => 0,
                    "total_tax" => 7
                ],
                "additional_details" => [
                    "requires_extra_care" => "False",
                    "type_extra_care" => "Normal Goods"
                ]
            ];
        }

        $promised_delivery_date=Date('Y-m-d', strtotime('+3 days'))."T00:00:00.000Z";
        $payload = [
            "order_details" => [
                "client_order_id" => $o->customer_order_number,
                "awb_number" => $awb_number,
                "actual_weight" => $o->weight,
                "volumetric_weight" => ($o->height * $o->length * $o->breadth) / 5,
                "product_value" => $o->invoice_amount,
                "payment_mode" => $pay_type,
                "cod_amount" => $collectable_value,
                "promised_delivery_date" => $promised_delivery_date,
                "total_amount" => $o->invoice_amount
            ],
            "customer_details" => [
                "name" => $o->b_customer_name,
                "contact" => $o->b_contact,
                "address_line_1" => $o->b_address_line1,
                "address_line_2" => $o->b_address_line2,
                "city" => $o->b_city,
                "state" => $o->b_state,
                "pincode" => $o->b_pincode,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "pickup_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
                "latitude" => "",
                "longitude" => ""
            ],
            "rts_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
            ],
            "product_details" => $products
        ];
        return $payload;
    }

    function _generateShadowFaxReversePayload($o) {
        $orderId = $o->id;
        $sellerDetail = Seller::find($o->seller_id);
        if(empty($sellerDetail)){
            return [];
        }
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = $sellerDetail->first_name . ' ' . $sellerDetail->last_name;
        $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

        //for generate AWB Number
        $awb_number = $o->awb_number;

        // dd($awb_number);
        $products=[];
        foreach($product as $p){
            $products[]=[
                "client_sku_id" => $o->id,
                "name" => $p->product_sku,
                "price" => round($product_price),
                "return_reason" => "xyz",
                "brand" => "xyz",
                "category" => "xyz",
                "additional_details" => [
                    "type_extra_care" => "Dangerous Goods",
                    "color" => "xyz",
                    "serial_no" => "ABC.$o->id",
                    "sku_images" => [
                        "",
                        ""
                    ],
                    "requires_extra_care" => false,
                    "quantity" => $p->product_qty,
                    "size" => 8
                ],
                "seller_details" => [
                    "state" => $seller->state,
                    "regd_address" => $seller->street,
                    "regd_name" => $seller_name,
                    "gstin" => $seller->gst_number
                ],
                "taxes" => [
                    "total_tax_amount" => 18,
                    "igst_amount" => 18,
                    "cgst_amount" => 0,
                    "sgst_amount" => 0
                ],
                "hsn_code" => "",
                "invoice_no" => "In.$o->id"
            ];
        }

        $promised_delivery_date=Date('Y-m-d', strtotime('+3 days'))."T00:00:00.000Z";

        $payload = [
            "client_order_number" => $o->customer_order_number,
            "total_amount" => $o->invoice_amount,
            "price" => $o->invoice_amount,
            "eway_bill" => "",
            "address_attributes" => [
                "address_line" => $o->s_address_line1.' '.$o->s_address_line2,
                "city" => $o->s_city,
                "country" => $o->s_country,
                "pincode" => $o->s_pincode,
                "name" => $o->s_customer_name,
                "phone_number" => $o->s_contact,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "seller_attributes" => [
                "name" => $o->p_warehouse_name,
                "address_line" => $o->p_address_line1.' '.$o->p_address_line2,
                "city" => $o->p_city,
                "pincode" =>  $o->p_pincode,
                "phone" =>  $o->p_contact
            ],
            "skus_attributes" => $products
        ];
        return $payload;
    }

    function generatePayloadXpressBees($o)
    {
        try {
            $orderId = $o->id;
            $awbNumber = $o->awb_number;
            $config = $this->info['config'];
            $qty = Product::where('order_id', $orderId)->sum('product_qty');
            if (strtolower($o->order_type) == 'cod') {
                $collectable_value = $o->invoice_amount;
            } elseif (strtolower($o->order_type) == 'prepaid') {
                $collectable_value = "0";
            } else {
                $collectable_value = "0";
            }
            $sellerDetail = Seller::where('id', $o->seller_id)->first();
            $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();
            $businessName = null;
            switch($o->courier_partner) {
                case 'xpressbees_surface':
                    $businessName = 'Twinnship';
                    break;
                case 'xpressbees_sfc':
                case 'xpressbees_surface_1kg':
                case 'xpressbees_surface_3kg':
                    if($o->seller_order_type == 'NSE'){
                        if($o->o_type == 'forward') {
                            $businessName = 'UNIQUEENTERPRISES';
                        }
                    } else {
                        $businessName = 'Twinnship SFC';
                    }
                    break;
                case 'xpressbees_surface_5kg':
                case 'xpressbees_surface_10kg':
                    $businessName = 'Twinnship SFC 5';
                    break;
            }
            $payload = [
                "AirWayBillNO" => $awbNumber,
                "BusinessAccountName" => $businessName,
                "OrderNo" => $o->customer_order_number,
                "SubOrderNo" => $o->order_number,
                "OrderType" => $o->order_type,
                "CollectibleAmount" => $collectable_value,
                "DeclaredValue" => $o->invoice_amount,
                "PickupType" => "Vendor",
                "Quantity" => $qty,
                "ServiceType" => "SD",
                "DropDetails" => [
                    "Addresses" => [
                        [
                            "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                            "City" => $o->s_city,
                            "EmailID" => $o->b_customer_email,
                            "Name" => $o->b_customer_name,
                            "PinCode" => $o->s_pincode,
                            "State" => $o->s_state,
                            "Type" => "Primary"
                        ]
                    ],
                    "ContactDetails" => [
                        [
                            "PhoneNo" => $o->s_contact,
                            "Type" => "Primary",
                            "VirtualNumber" => null
                        ]
                    ],
                    "IsGenSecurityCode" => null,
                    "SecurityCode" => null,
                    "IsGeoFencingEnabled" => null,
                    "Latitude" => null,
                    "Longitude" => null,
                    "MaxThresholdRadius" => null,
                    "MidPoint" => null,
                    "MinThresholdRadius" => null,
                    "RediusLocation" => null
                ],
                "PickupDetails" => [
                    "Addresses" => [
                        [
                            "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                            "City" => $o->p_city,
                            "EmailID" => "",
                            "Name" => $o->p_warehouse_name,
                            "PinCode" => $o->p_pincode,
                            "State" => $o->p_state,
                            "Type" => "Primary"
                        ]
                    ],
                    "ContactDetails" => [
                        [
                            "PhoneNo" => $o->p_contact,
                            "Type" => "Primary"
                        ]
                    ],
                    "PickupVendorCode" => "ORUF1THL3Y0SJ",
                    "IsGenSecurityCode" => null,
                    "SecurityCode" => null,
                    "IsGeoFencingEnabled" => null,
                    "Latitude" => null,
                    "Longitude" => null,
                    "MaxThresholdRadius" => null,
                    "MidPoint" => null,
                    "MinThresholdRadius" => null,
                    "RediusLocation" => null
                ],
                "RTODetails" => [
                    "Addresses" => [
                        [
                            "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                            "City" => $o->p_city,
                            "EmailID" => "",
                            "Name" => $o->p_warehouse_name,
                            "PinCode" => $o->p_pincode,
                            "State" => $o->p_state,
                            "Type" => "Primary"
                        ]
                    ],
                    "ContactDetails" => [
                        [
                            "PhoneNo" => $o->p_contact,
                            "Type" => "Primary"
                        ]
                    ]
                ],
                "Instruction" => "",
                "CustomerPromiseDate" => null,
                "IsCommercialProperty" => null,
                "IsDGShipmentType" => null,
                "IsOpenDelivery" => null,
                "IsSameDayDelivery" => null,
                "ManifestID" => "SGHJDX1554362X",
                "MultiShipmentGroupID" => null,
                "SenderName" => null,
                "IsEssential" => "false",
                "IsSecondaryPacking" => "false",
                "PackageDetails" => [
                    "Dimensions" => [
                        "Height" => $o->height,
                        "Length" => $o->length,
                        "Width" => $o->length
                    ],
                    "Weight" => [
                        "BillableWeight" => $o->weight / 1000,
                        "PhyWeight" => $o->weight / 1000,
                        "VolWeight" => $o->weight / 1000
                    ]
                ],
                "GSTMultiSellerInfo" => [
                    [
                        "BuyerGSTRegNumber" => $seller->gst_number,
                        "EBNExpiryDate" => null,
                        "EWayBillSrNumber" => $awbNumber,
                        "InvoiceDate" => date('d-m-Y'),
                        "InvoiceNumber" => "IN$o->id",
                        "InvoiceValue" => null,
                        "IsSellerRegUnderGST" => "Yes",
                        "ProductUniqueID" => null,
                        "SellerAddress" => $seller->street,
                        "SellerGSTRegNumber" => $seller->gst_number,
                        "SellerName" => $seller->company_name,
                        "SellerPincode" => $seller->pincode,
                        "SupplySellerStatePlace" => $seller->state,
                        "HSNDetails" => [
                            [
                                "ProductCategory" => "Retail",
                                "ProductDesc" => $o->product_name,
                                "CGSTAmount" => null,
                                "Discount" => null,
                                "GSTTAXRateIGSTN" => null,
                                "GSTTaxRateCGSTN" => null,
                                "GSTTaxRateSGSTN" => null,
                                "GSTTaxTotal" => null,
                                "HSNCode" => $config->hsn_number,
                                "IGSTAmount" => null,
                                "ProductQuantity" => null,
                                "SGSTAmount" => null,
                                "TaxableValue" => null
                            ]
                        ]
                    ]
                ]
            ];
            return $payload;
        } catch(Exception $e) {
            return [];
        }
    }

    function generatePayloadXpressBeesReverse($o)
    {
        try {
            $orderId = $o->id;
            $awbNumber = $o->awb_number;
            $config = $this->info['config'];
            $qty = Product::where('order_id', $orderId)->sum('product_qty');
            $sellerDetail = Seller::where('id', $o->seller_id)->first();
            $seller = Basic_informations::where('seller_id', $sellerDetail->id)->first();

            $businessName = null;
            switch($o->courier_partner) {
                case 'xpressbees_sfc':
                    if($o->seller_order_type == 'NSE'){
                        if($o->o_type == 'forward') {
                            $businessName = 'UNIQUEENTERPRISES';
                        }
                    } else {
                        $businessName = 'Twinnship SFC';
                    }
                    break;
                case 'xpressbees_surface':
                    $businessName = 'Twinnship';
                    break;
                case 'xpressbees_surface_1kg':
                    $businessName = 'TwinnshipSFC1';
                    break;
                case 'xpressbees_surface_3kg':
                    $businessName = 'Twinnship SFC 3';
                    break;
                case 'xpressbees_surface_5kg':
                case 'xpressbees_surface_10kg':
                    $businessName = 'Twinnship SFC 5';
                    break;
            }
            $payload = [
                "AirWayBillNO" => $awbNumber,
                "OrderNo" => $o->customer_order_number,
                "BusinessAccountName" => $businessName,
                "ProductID" => $o->customer_order_number . '' . $o->id,
                "Quantity" => $qty,
                "ProductName" => $o->product_name,
                "Instruction" => "",
                "IsCommercialProperty" => "",
                "CollectibleAmount" => "0",
                "ProductMRP" => $o->invoice_amount,
                "DropDetails" => [
                    "Addresses" => [
                        [
                            "Type" => "Primary",
                            "Name" => $o->p_warehouse_name,
                            "Address" => $o->p_address_line1 . " " . $o->p_address_line2,
                            "City" => $o->p_city,
                            "State" => $o->p_state,
                            "PinCode" => $o->p_pincode,
                            "EmailID" => "",
                            "Landmark" => "",
                        ]
                    ],
                    "ContactDetails" => [
                        [
                            "Type" => "Primary",
                            "PhoneNo" => $o->p_contact
                        ]
                    ],
                    "IsGenSecurityCode" => "",
                    "SecurityCode" => "",
                    "IsGeoFencingEnabled" => "",
                    "Longitude" => "",
                    "Latitude" => "",
                    "RadiusLocation" => "",
                    "MidPoint" => "",
                    "MinThresholdRadius" => "",
                    "MaxThresholdRadius" => ""
                ],
                "PickupDetails" => [
                    "Addresses" => [
                        [
                            "Type" => "Primary",
                            "Name" => $o->s_customer_name,
                            "Address" => $o->s_address_line1 . " " . $o->s_address_line2,
                            "City" => $o->s_city,
                            "State" => $o->s_state,
                            "PinCode" => $o->s_pincode,
                            "EmailID" => "",
                            "Landmark" => ""
                        ]
                    ],
                    "ContactDetails" => [
                        [
                            "Type" => "Primary",
                            "VirtualNumber" => "",
                            "PhoneNo" => $o->s_contact
                        ]
                    ],
                    "IsPickupPriority" => "1",
                    "PriorityRemarks" => "High value shipments",
                    "PickupSlotsDate" => "",
                    "IsGenSecurityCode" => "",
                    "SecurityCode" => "",
                    "IsGeoFencingEnabled" => "0",
                    "Longitude" => "",
                    "Latitude" => "",
                    "RadiusLocation" => "",
                    "MidPoint" => "",
                    "MinThresholdRadius" => "",
                    "MaxThresholdRadius" => ""
                ],
                "PackageDetails" => [
                    "Dimensions" => [
                        "Height" => $o->height,
                        "Length" => $o->length,
                        "Width" => $o->bredth
                    ],
                    "Weight" => [
                        "BillableWeight" => $o->weight / 1000,
                        "PhyWeight" => $o->weight / 1000,
                        "VolWeight" => $o->weight / 1000
                    ]
                ],
                "QCTemplateDetails" => [
                    "TemplateId" => null,
                    "TemplateCategory" => ""
                ],
                "TextCapture" => [
                    [
                        "Label" => "",
                        "Type" => "",
                        "ValueToCheck" => ""
                    ]
                ],
                "PickupProductImage" => [
                    [
                        "ImageUrl" => "",
                        "TextToShow" => ""
                    ]
                ],
                "CaptureImageRule" => [
                    "MinImage" => "",
                    "MaxImage" => ""
                ],
                "HelpContent" => [
                    "Description" => "",
                    "URL" => "",
                    "IsMandatory" => ""
                ],
                "GSTMultiSellerInfo" => [
                    [
                        "InvoiceNumber" => "IN$o->id",
                        "InvoiceDate" => date('d-m-Y'),
                        "InvoiceValue" => $o->invoice_amount,
                        "ProductUniqueID" => "",
                        "IsSellerRegUnderGST" => "",
                        "BuyerGSTRegNumber" => $seller->gst_number,
                        "SellerName" => $seller->company_name,
                        "SellerGSTRegNumber" => $seller->gst_number,
                        "SellerAddress" => $seller->street,
                        "SupplySellerStatePlace" => $seller->state,
                        "SellerPincode" => $seller->pincode,
                        "EBNExpiryDate" => "",
                        "EWayBillSrNumber" => "",
                        "HSNDetails" => [
                            [
                                "HSNCode" => $config->hsn_number,
                                "ProductCategory" => "Retail",
                                "ProductDesc" => $o->product_name,
                                "SGSTAmount" => null,
                                "CGSTAmount" => null,
                                "IGSTAmount" => null,
                                "GSTTaxTotal" => null,
                                "TaxableValue" => null,
                                "Discount" => null,
                                "GSTTaxRateCGSTN" => null,
                                "GSTTaxRateSGSTN" => null,
                                "GSTTAXRateIGSTN" => null
                            ]
                        ]
                    ]
                ]
            ];
            return $payload;
        } catch(Exception $e) {
            return [];
        }
    }

    function generatePayloadDelhivery($o) {
        try {
            $orderId = $o->id;
            $sellerData = Seller::find($o->seller_id);
            $config = $this->info['config'];
            $qty = Product::where('order_id', $orderId)->sum('product_qty');
            if (strtolower($o->order_type) == 'cod') {
                $pay_type = "COD";
                $collectable_value = $o->invoice_amount;
            } elseif (strtolower($o->order_type) == 'prepaid') {
                $pay_type = "Prepaid";
                $collectable_value = "0";
            } else {
                $pay_type = "REVERSE";
                $collectable_value = "0";
            }
            if ($o->o_type == 'reverse') {
                $pay_type = "Pickup";
            }
            $seller_name = $sellerData->first_name . ' ' . $sellerData->last_name;
            $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
            $warehouse = Warehouses::where('id', $o->warehouse_id)->first();
            if(empty($warehouse)){
                return [];
            }

            $delhiveryClient = null;
            switch($o->courier_partner) {
                case 'delhivery_surface':
                    $delhiveryClient = 'TwinnshipIN SURFACE';
                    break;
                case 'delhivery_surface_10kg':
                    if($sellerData->is_alpha == 'NSE')
                        $delhiveryClient = 'Twinnship SURFACE';
                    else
                        $delhiveryClient = 'HAMARA BAZAAR SURFACE';
                    break;
                case 'delhivery_surface_20kg':
                    $delhiveryClient = 'TwinnshipHEAVY2 SURFACE';
                    break;
                case 'delhivery_b2b_20kg':
                    $delhiveryClient = 'TwinnshipTECHNOLO B2BC';
                    break;
            }

            if(strtolower($o->shipment_type) == 'mps') {
                $shipments = [];
                for($i=0; $i<$o->number_of_packets; $i++) {
                    $shipments[] = [
                        "weight" => $o->weight,
                        "mps_amount" => $o->order_type == "cod" ? $o->invoice_amount : "0",
                        "mps_children" => $o->number_of_packets,
                        "seller_inv" => $o->order_number,
                        "city" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_city),
                        "pin" => $o->s_pincode,
                        "products_desc" =>preg_replace('/[^A-Za-z0-9\-]/', '', $o->product_name),
                        "product_type" => "Heavy",
                        "extra_parameters" => [
                            "encryptedShipmentID" => "DdB6bvvFN"
                        ],
                        "add" => $o->s_address_line1 . " " . $o->s_address_line2,
                        "shipment_type" => "MPS",
                        "hsn_code" => $config->hsn_number,
                        "state" => $o->s_state,
                        "waybill" => $waybillNumber[$i] ?? null,
                        "supplier" => $seller_name,
                        "master_id" => $waybillNumber[0] ?? null,
                        "sst" => "-",
                        "phone" => $o->s_contact,
                        "payment_mode" => $pay_type,
                        "cod_amount" => $o->order_type == "cod" ? $o->invoice_amount : "0",
                        "order_date" => $o->inserted,
                        "name" => $o->s_customer_name,
                        "total_amount" => $o->invoice_amount,
                        "country" => $o->p_country,
                        "order" => $o->order_number,
                        "ewbn" => ($o->invoice_amount > 50000 ? $o->ewaybill_number : "")
                    ];
                }
                $payload = [
                    "shipments" => $shipments,
                    "pickup_location" => [
                        "name" => $warehouse->warehouse_code,
                        "city" => $o->p_city,
                        "pin" => $o->p_pincode,
                        "country" => $o->p_country,
                        "phone" => $o->p_contact,
                        "add" => "$o->p_address_line1 , $o->p_address_line2"
                    ]
                ];
            } else {
                $payload = [
                    "shipments" => array(
                        [
                            "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->s_address_line1). " " . preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->s_address_line2),
                            "address_type" => "home",
                            "phone" => $o->s_contact,
                            "payment_mode" => $pay_type,
                            "name" => $o->s_customer_name,
                            "pin" => $o->s_pincode,
                            "order" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                            "consignee_gst_amount" => "100",
                            "integrated_gst_amount" => "100",
                            "ewbn" => $o->ewaybill_number,
                            "consignee_gst_tin" => "",
                            "seller_gst_tin" => "",
                            "client_gst_tin" => "",
                            "hsn_code" => $config->hsn_number,
                            "gst_cess_amount" => "0",
                            "client" => $delhiveryClient,
                            "tax_value" => "100",
                            "seller_tin" => "Twinnship",
                            "seller_gst_amount" => "100",
                            "seller_inv" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                            "city" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_city),
                            "commodity_value" => $o->invoice_amount,
                            "weight" => $o->weight,
                            "return_state" => preg_replace('/[^A-Za-z0-9\ ]/', '', $o->p_state),
                            "document_number" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->order_number),
                            "od_distance" => "450",
                            "sales_tax_form_ack_no" => "1245",
                            "document_type" => "document",
                            "seller_cst" => "1343",
                            "seller_name" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->$seller_name),
                            "fragile_shipment" => "true",
                            "return_city" => $o->p_city,
                            "return_phone" => $o->p_contact,
                            "shipment_height" => $o->height,
                            "shipment_width" => $o->breadth,
                            "shipment_length" => $o->length,
                            "category_of_goods" => "categoryofgoods",
                            "cod_amount" => $collectable_value,
                            "return_country" => $o->p_country,
                            "document_date" => $o->inserted,
                            "taxable_amount" => $o->invoice_amount,
                            "products_desc" => preg_replace('/[^A-Za-z0-9\ ]/', '', $o->product_name),
                            "state" => preg_replace('/[^A-Za-z0-9\ ]/', '', $o->s_state),
                            "dangerous_good" => "False",
                            "waybill" => $o->awb_number,
                            "consignee_tin" => "1245875454",
                            "order_date" => $o->inserted,
                            "return_add" => preg_replace('/[^A-Za-z0-9\ ]/', '', $o->p_city).",".preg_replace('/[^A-Za-z0-9\ ]/', '', $o->p_state),
                            "total_amount" => $o->invoice_amount,
                            "seller_add" => "$seller->city".",".preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->state),
                            "country" => $o->p_country,
                            "return_pin" => $o->p_pincode,
                            "extra_parameters" => [
                                "return_reason" => ""
                            ],
                            "return_name" => $o->p_warehouse_name,
                            "supply_sub_type" => "",
                            "plastic_packaging" => "false",
                            "quantity" => $qty
                        ]
                    ),
                    "pickup_location" => [
                        "name" => $warehouse->warehouse_code,
                        "city" => $o->p_city,
                        "pin" => $o->p_pincode,
                        "country" => $o->p_country,
                        "phone" => $o->p_contact,
                        "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->p_address_line1).",". preg_replace('/[^A-Za-z0-9\ ]/', ' ', $o->p_address_line2)
                    ]
                ];
            }
            return $payload;
        } catch(Exception $e) {
            return [];
        }
    }
    function _cancelMarutiOrder($order){
        $maruti = new Maruti();
        $maruti->cancelOrder($order->awb_number);
    }
    function _cancelMarutiEcomOrder($order){
        $maruti = new MarutiEcom();
        $maruti->cancelOrder($order->awb_number);
    }
}
