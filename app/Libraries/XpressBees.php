<?php

namespace App\Libraries;

use App\Models\Basic_informations;
use App\Models\Configuration;
use App\Models\DefaultInvoiceAmount;
use App\Models\Partners;
use App\Models\Product;
use App\Models\Warehouses;
use App\Models\XbeesAwbnumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class XpressBees
{
    protected $xbKey,$businessName,$username,$password,$secret,$headers,$courierPartner;
    function __construct($credentialType = 'three')
    {
        $this->courierPartner = 'xpressbees_sfc';
        $this->xbKey = '';
        $this->businessName = '';
        $this->username = 'agautam@twinnicindia.com';
        $this->password = 'krish@2484';
        $this->secret = '';
        $this->headers = [
            'content-type' => 'application/json',
            'XBKey' => $this->xbKey
        ];
    }
    function GenerateToken(){
        $data = [
            "email" => $this->username,
            "password" => $this->password,
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://ship.xpressbees.com/api/users/franchise_login', $data)->json();
        return $response['data'] ?? false;
    }
    function ShipOrder($orderData,$sellerData){
        if($orderData->o_type != 'forward')
            return false;
        $generatedToken = $this->GenerateToken();
        if(empty($generatedToken))
            return false;
        $payload = $this->_GeneratePayload($orderData,$sellerData);
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "Ship Request - {$orderData->id}",
            'data' => $payload
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$generatedToken,
        ])->post('https://ship.xpressbees.com/api/franchise/shipments', $payload);
        // echo $response;
        $responseData = $response->json();
        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "Ship Response - {$orderData->id}",
            'data' => $responseData
        ]);
        return $responseData['awb_number'] ?? '';
    }

    function CancelOrder($awbNumber){
        $data = array(
            'awb_number' => $awbNumber
        );

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "Cancellation Request for : {$awbNumber}",
            'data' => $data
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->GenerateToken(),
        ])->post('https://ship.xpressbees.com/api/franchise/shipments/cancel_shipment', $data);
        $response = $response->json();

        Logger::write('logs/partners/xbees/xbees-'.date('Y-m-d').'.text', [
            'title' => "Cancellation Response for {$awbNumber}",
            'data' => $response
        ]);
    }
    function GetTracking($awbNumber){
        $data = [
            'awb_number' => $awbNumber
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->GenerateToken(),
        ])->post('https://ship.xpressbees.com/api/franchise/shipments/track_shipment', $data);
        $awb_data = $response->json();
        return $awb_data;
    }
    function _GeneratePayload($orderData,$sellerData){
        $weight = $orderData->weight > $orderData->vol_weight ? $orderData->weight : $orderData->vol_weight;
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        if (strtolower($orderData->order_type) == 'cod') {
            if(intval($orderData->collectable_amount) > 0){
                $collectable_value = $orderData->collectable_amount;
            }
            else
                $collectable_value = $orderData->invoice_amount;
        }
        else
            $collectable_value = 0;

        $RTOAddress = [
            'Address' => preg_replace("/[^A-Za-z0-9\ ]/","",$orderData->p_address_line1)." ".preg_replace("/[^A-Za-z0-9\ ]/","",$orderData->p_address_line2),
            'City' => $orderData->p_city,
            'EmailID' => "",
            'Name' => $orderData->p_warehouse_name,
            'PinCode' => $orderData->p_pincode,
            'State' => $orderData->p_state,
            'PhoneNo' => $orderData->p_contact
        ];
        if($orderData->same_as_rto == 'n'){
            if($orderData->warehouose_id != $orderData->rto_warehouse_id){
                $warehouse = Warehouses::find($orderData->rto_warehouse_id);
                $RTOAddress = [
                    'Address' => preg_replace("/[^A-Za-z0-9\ ]/","",$warehouse->address_line1)." ".preg_replace("/[^A-Za-z0-9\ ]/","",$warehouse->address_line2),
                    'City' => $warehouse->city,
                    'EmailID' => $warehouse->support_email,
                    'Name' => $warehouse->warehouse_name,
                    'PinCode' => $warehouse->pincode,
                    'State' => $warehouse->state,
                    'PhoneNo' => $warehouse->contact_number,
                ];
            }
        }

        $product = Product::where('order_id', $orderData->id)->get();
        $productArray = [];

        foreach ($product as $p) {
            $productArray[] = [
                "product_name" => $p->product_name,
                "product_qty" => $p->product_qty,
                "product_price" => $orderData->invoice_amount,
                "product_tax_per" => "",
                "product_sku" => $p->product_sku,
                "product_hsn" => ''
            ];
        }

        $payload = [
            "id" => $orderData->customer_order_number."-".$orderData->id,
            "unique_order_number" => "yes",
            "payment_method" => $orderData->order_type == 'cod' ? 'COD' : 'prepaid',
            "consigner_name" => $orderData->p_warehouse_name,
            "consigner_phone" => str_replace("\u202c","",str_replace("\u202a","",trim($orderData->p_contact))),
            "consigner_pincode" => $orderData->p_pincode,
            "consigner_city" => $orderData->p_city,
            "consigner_state" => $orderData->p_state,
            "consigner_address" => $orderData->p_address_line1 . " " . $orderData->p_address_line2,
            "consigner_gst_number" => $seller->gst_number,
            "consignee_name" => $orderData->s_customer_name,
            "consignee_phone" => str_replace("\u202c","",str_replace("\u202a","",trim($orderData->s_contact))),
            "consignee_pincode" => $orderData->s_pincode,
            "consignee_city" =>  $orderData->s_city,
            "consignee_state" => $orderData->s_state,
            "consignee_address" => $orderData->s_address_line1 . " " . $orderData->s_address_line2,
            "consignee_gst_number" => "",
            "products" => $productArray,
            "invoice" => [
                [
                    "invoice_number" => '',
                    "invoice_date" =>  date('Y-m-d'),
                    "ebill_number" => $orderData->ewaybill_number,
                    "ebill_expiry_date" => date('Y-m-d')
                ]
            ],
            "weight" => $weight,
            "length" => $orderData->length,
            "height" => $orderData->height,
            "breadth" => $orderData->breadth,
            "courier_id" => "16354",
            "pickup_location" => "customer",
            "shipping_charges" => 0,
            "cod_charges" => 0,
            "discount" => 0,
            "order_amount" => $orderData->invoice_amount + ($defaultInvoiceAmount ?? 0),
            "collectable_amount" => $orderData->order_type == 'cod' ? $collectable_value : 0
        ];
        return $payload;
    }
}
