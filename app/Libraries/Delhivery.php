<?php

namespace App\Libraries;


use App\Models\Basic_informations;
use App\Models\Configuration;
use App\Models\MPS_AWB_Number;
use App\Models\Seller;
use App\Models\Warehouses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class Delhivery
{
    protected $delhiveryToken,$delhiveryClient,$shippingMode,$config,$headers;
    function __construct($credentialType = 'surface')
    {
        if($credentialType == 'surface'){
            $this->delhiveryToken = '3139b9184109955719485ee59c4c2dd2dc19bf9b';
            $this->delhiveryClient = 'TWINNIC FRANCHISE';
            $this->shippingMode = 'Surface';
            $this->config = Configuration::find(1);
            $this->headers = [
                'content-type' => 'application/json',
                'Authorization' => "Token {$this->delhiveryToken}"
            ];
        }
        else if($credentialType == 'air'){
            $this->delhiveryToken = '3139b9184109955719485ee59c4c2dd2dc19bf9b';
            $this->delhiveryClient = 'TWINNIC FRANCHISE';
            $this->shippingMode = 'Express';
            $this->config = Configuration::find(1);
            $this->headers = [
                'content-type' => 'application/json',
                'Authorization' => "Token {$this->delhiveryToken}"
            ];
        }
        else if($credentialType == 'five'){
            $this->delhiveryToken = '8dc254096903ba9defd9288f7a128ec21eaccfa9';
            $this->delhiveryClient = 'MWTWINNIC FRANCHISE';
            $this->shippingMode = 'Surface';
            $this->config = Configuration::find(1);
            $this->headers = [
                'content-type' => 'application/json',
                'Authorization' => "Token {$this->delhiveryToken}"
            ];
        }
        else if($credentialType == 'ten'){
            $this->delhiveryToken = 'cff281f047354da104dde6852b2d5398b037f092';
            $this->delhiveryClient = 'HWTWINNIC FRANCHISE';
            $this->shippingMode = 'Surface';
            $this->config = Configuration::find(1);
            $this->headers = [
                'content-type' => 'application/json',
                'Authorization' => "Token {$this->delhiveryToken}"
            ];
        }
    }
    function GetServiceablePincode()
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(300)->get("https://track.delhivery.com/c/api/pin-codes/json/?token={$this->delhiveryToken}");
        return $response->json();
    }
    function ShipOrder($orderData,$sellerData){
        $payload = $this->GeneratePayload($orderData,$sellerData);
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Delhivery Request Payload',
            'data' => $payload
        ]);
        $response = $this->MakeRequest($payload);
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Delhivery Response Payload',
            'data' => $response
        ]);
        if (!empty($response['success']) && !empty($response['packages'][0]['waybill'])) {
            return $response['packages'][0]['waybill'];
        }
        else{
            return false;
        }
    }

    function GetTracking($awbNumber){
        $response = Http::withHeaders($this->headers)->get("https://track.delhivery.com/api/v1/packages/json?waybill={$awbNumber}&token={$this->delhiveryToken}");
        return $response->json();
    }
    function CancelOrder($orderData){
        $data = [
            'waybill' => $orderData->awb_number,
            'cancellation' => "true"
        ];
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => "Delhivery Cancel Request for - {$orderData->awb_number}",
            'data' => $data
        ]);
        $response = Http::withHeaders($this->headers)->post('https://track.delhivery.com/api/p/edit.json',$data);
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => "Delhivery Cancel Response for - {$orderData->awb_number}",
            'data' => $response->json()
        ]);
        return $response->json();
    }

    function GenerateWarehouse($warehouse){
        $payload = [
            "phone" => $warehouse->contact_number,
            "city" => $warehouse->city,
            "name" => $warehouse->warehouse_code,
            "pin" => $warehouse->pincode,
            "address" => $warehouse->address_line1,
            "country" => $warehouse->country,
            "email" => $warehouse->support_email,
            "registered_name" => $warehouse->warehouse_code,
            "return_address" => $warehouse->address_line1,
            "return_pin" => $warehouse->pincode,
            "return_city" => $warehouse->city,
            "return_state" => $warehouse->state,
            "return_country" => $warehouse->country
        ];
        $response = Http::withHeaders($this->headers)->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);
        return $response->json();
    }
    function GeneratePayload($orderData,$sellerData){
        if (strtolower($orderData->order_type) == 'cod') {
            $pay_type = "COD";
            if(intval($orderData->collectable_amount) > 0){
                $collectable_value = $orderData->collectable_amount;
            }
            else
                $collectable_value = $orderData->invoice_amount;
        } elseif (strtolower($orderData->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
//        $shippingMode = $orderData->courier_partner == "delhivery_air" ? "Express" : "Surface";
        $shippingMode = $this->shippingMode;
        if ($orderData->o_type == 'reverse') {
            $pay_type = "Pickup";
        }
        $seller_name = $sellerData->first_name . ' ' . $sellerData->last_name;
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        $warehouse = Warehouses::where('id', $orderData->warehouse_id)->first();
        //$warehouse = Warehouses::where('id', $o->warehouse_id ?? 0)->first();
        if(empty($warehouse)){
            Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                'title' => "Delhivery Request Payload for - {$orderData->awb_number}",
                'data' => ['message' => 'Please Check the Warehouse First']
            ]);
            return false;
        }
        $weight = $orderData->weight > $orderData->vol_weight ? $orderData->weight : $orderData->vol_weight;

        $payload = [
            "shipments" => array(
                [
                    "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->s_address_line1). " " . preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->s_address_line2),
                    "address_type" => "home",
                    "shipping_mode" => $shippingMode,
                    "phone" => $orderData->s_contact,
                    "payment_mode" => $pay_type,
                    "name" => preg_replace('/[^A-Za-z0-9\ ]/', ' ',$orderData->s_customer_name),
                    "pin" => $orderData->s_pincode,
                    "order" => "{$orderData->id}-3",
                    "consignee_gst_amount" => "100",
                    "integrated_gst_amount" => "100",
                    "ewbn" => $orderData->ewaybill_number,
                    "consignee_gst_tin" => "",
                    "seller_gst_tin" => "",
                    "client_gst_tin" => "",
                    "hsn_code" => $this->config->hsn_number ?? "",
                    "gst_cess_amount" => "0",
                    "client" => $this->delhiveryClient,
                    "tax_value" => "100",
                    "seller_tin" => "Twinnship",
                    "seller_gst_amount" => "100",
                    "seller_inv" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->order_number),
                    "city" => preg_replace('/[^A-Za-z0-9\-]/', '',$orderData->s_city),
                    "commodity_value" => $orderData->invoice_amount,
                    "weight" => $weight,
                    "return_state" => preg_replace('/[^A-Za-z0-9\ ]/', ' ',$orderData->p_state),
                    "document_number" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->order_number),
                    "od_distance" => "450",
                    "sales_tax_form_ack_no" => "1245",
                    "document_type" => "document",
                    "seller_cst" => "1343",
                    "seller_name" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller_name),
                    "fragile_shipment" => "true",
                    "return_city" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->p_city),
                    "return_phone" => $orderData->p_contact,
                    "shipment_height" => $orderData->height,
                    "shipment_width" => $orderData->breadth,
                    "shipment_length" => $orderData->length,
                    "category_of_goods" => "categoryofgoods",
                    "cod_amount" => $collectable_value,
                    "return_country" => "IN",
                    "document_date" => $orderData->inserted,
                    "taxable_amount" => $orderData->invoice_amount,
                    "products_desc" => preg_replace('/[^A-Za-z0-9\ ]/', '', $orderData->product_name),
                    "state" => preg_replace('/[^A-Za-z0-9\ ]/', '', $orderData->s_state),
                    "dangerous_good" => "False",
                    "consignee_tin" => "1245875454",
                    "order_date" => $orderData->inserted,
                    "return_add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->p_city).",".preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->p_state),
                    "total_amount" => $orderData->invoice_amount,
                    "seller_add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->city).",".preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->state),
                    "country" => "IN",
                    "return_pin" => $orderData->p_pincode,
                    "extra_parameters" => [
                        "return_reason" => ""
                    ],
                    "return_name" => $orderData->p_warehouse_name,
                    "supply_sub_type" => "",
                    "plastic_packaging" => "false",
                    "quantity" => $orderData->product_qty
                ]
            ),
            "pickup_location" => [
                "name" => $warehouse->warehouse_code,
                "city" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $seller->p_city),
                "pin" => $orderData->p_pincode,
                "country" => "IN",
                "phone" => $orderData->p_contact,
                "add" => preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->p_address_line1).",". preg_replace('/[^A-Za-z0-9\ ]/', ' ', $orderData->p_address_line2)
            ]
        ];
        return $payload;
    }
    function MakeRequest($payload){
        try{
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
                    "Authorization: Token {$this->delhiveryToken}",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response,true);
        }catch(Exception $e){
            $data = [];
        }
        return $data;
    }

    function MakeRequestNew($payload)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post('https://track.delhivery.com/api/cmu/create.json', [
                    'format' => 'json',
                    'data' => $payload,
                ]);

            $data = $response->json();
        } catch (Exception $e) {
            $data = [];
        }

        return $data;
    }

    function DelhiveryMPS($order) {
        try {
            DB::beginTransaction();

            $seller = Seller::find($order->seller_id);
            $config = Configuration::first();
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
            $warehouse = Warehouses::where('id', $order->warehouse_id)->first();

            // Get waybill number and master id
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://track.delhivery.com/waybill/api/bulk/json/?token='.$this->delhiveryToken.'&count='.$order->number_of_packets,
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
                    "Authorization: Token ".$this->delhiveryToken,
                    'Content-Type: application/json'
                ),
            ));
            $returnAWB = null;
            $response = curl_exec($curl);
            curl_close($curl);
            $json = json_decode($response, true);
            Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                'title' => 'Delhivery MPS Response Payload',
                'data' => $json
            ]);
            if($json['success']) {
                $returnAWB = $json['packages'][0]['waybill'] ?? null;
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $returnAWB;
        } catch(Exception $e) {
            DB::rollBack();
            return null;
        }
    }
}
