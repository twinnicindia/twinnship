<?php

namespace App\Libraries;


use App\Libraries\Logger;
use App\Models\Basic_informations;
use App\Models\DefaultInvoiceAmount;
use App\Models\Order;
use App\Models\Partners;
use App\Models\Product;
use App\Models\ServiceablePincode;
use Illuminate\Support\Facades\Http;
use Exception;

class Shadowfax
{
    protected $accessToken, $endpoint, $headers, $orderService;
    function __construct($orderService = 'surface')
    {
        if($orderService == 'surface')
            $this->orderService = 'regular';
        else
            $this->orderService = 'ndd';
        $this->accessToken = 'c877db05c77ef1c2fe6da05ee024930cce5a908d';
        $this->endpoint = 'https://dale.shadowfax.in/api/';
        $this->headers = ['Authorization' => "Token {$this->accessToken}"];
    }

    // Check Serviceability per Pincode
    function checkServiceabilityBetweenPincode($source,$destination){
        $data = [
            'pickup_pincode' => $source,
            'delivery_pincode' => $destination,
            'format' => 'json'
        ];
        $url = $this->endpoint."v1/serviceability/";
        $response = Http::withHeaders($this->headers)->get($url,$data);
        return $response->json();
    }

    // Get All Serviceable Pincode
    // List of supported service type seller_pickup,customer_delivery,customer_pickup,seller_delivery,warehouse_pickup,warehouse_return
    function getAllServiceablePincodes(){
        $data = [
            'service' => 'customer_delivery',
            'page' => 1,
            'count' => 30000
        ];
        $url = $this->endpoint."v1/clients/serviceability/?service=customer_delivery&page=1&count=30000";
        $response = Http::withHeaders($this->headers)->timeout(40)->get($url);
        return $response->json();
    }
    function getAllServiceablePincodesFM(){
        $data = [
            'service' => 'shipper_pickup',
            'page' => 1,
            'count' => 30000
        ];
        $url = $this->endpoint."v1/clients/serviceability/?service=shipper_pickup&page=1&count=30000";
        $response = Http::withHeaders($this->headers)->timeout(40)->get($url);
        return $response->json();
    }

    // Generate AWB Numbers From Shadowfax
    function generateAwbNumbers($count){
        $data = [
            'count' => $count
        ];
        $url = $this->endpoint."v3/clients/generate_marketplace_awb/";
        $response = Http::withHeaders($this->headers)->post($url,$data)->json();
        Logger::write('logs/partners/shadowfax/shadowfax-' . date('Y-m-d') . '.text', [
            'title' => "Generate AWB Response",
            'data' => $response
        ]);
        return $response;
    }

    // Generate AWB Numbers From Shadowfax
    function generateAwbNumbersReverse($count){
        $data = [
            'count' => $count
        ];
        $url = $this->endpoint."v3/clients/orders/generate_awb/";
        $response = Http::withHeaders($this->headers)->post($url,$data);
        return $response->json();
    }

    // Order Details Update
    function updateOrderDetail($orderData){
        $data = [
            'awb_number' => $orderData->awb_number,
            'customer_details' => [
                'contact' => $orderData->s_contact,
                'alternate_contact' => $orderData->b_contact,
                'customer_address' => $orderData->s_address_line1
            ],
            'order_details' => [
                'cod_amount' => $orderData->order_type == 'prepaid' ? 0 : $orderData->invoice_amount,
                'eway_bill_number' => $orderData->ewaybill_number
            ]
        ];
        $url = $this->endpoint."v1/clients/order_update/";
        $response = Http::withHeaders($this->headers)->post($url,$data);
        return $response->json();
    }

    // Create Order of Shadowfax
    function manifestOrder($order,$sellerData){
        $url = $this->endpoint."v3/clients/orders/";
        $data = $this->_GenerateManifestPayload($order,$sellerData);
        Logger::write('logs/partners/shadowfax/shadowfax-' . date('Y-m-d') . '.text', [
            'title' => "Shadowfax Request Payload {$order->awb_number} : ",
            'data' => $data
        ]);
        $response = Http::withHeaders($this->headers)->post($url,$data);
        Logger::write('logs/partners/shadowfax/shadowfax-' . date('Y-m-d') . '.text', [
            'title' => "Shadowfax Response Payload {$order->awb_number} : ",
            'data' => $response->json()
        ]);
        return $response->json();
    }

    // Mark Order as RTO
    function markOrderAsRTO($awbNumber){
        $url = $this->endpoint."v3/clients/rto_rts_update/";
        $data = [
            'awb_numbers' => [
                $awbNumber
            ]
        ];
        $response = Http::withHeaders($this->headers)->post($url,$data);
        return $response->json();
    }

    // ReAttempt Order
    function reAttemptOrder($awbNumber){
        $url = $this->endpoint."v3/clients/ndr_update/";
        $data = [
            'awb_numbers' => [
                $awbNumber
            ]
        ];
        $response = Http::withHeaders($this->headers)->post($url,$data);
        return $response->json();
    }

    // Cancel Order
    function cancelOrder($awbNumber,$remark){
        $url = $this->endpoint."v3/clients/orders/cancel/?format=json";
        $data = [
            'request_id' => $awbNumber,
            'cancel_remarks' => $remark
        ];
        Logger::write('logs/partners/shadowfax/shadowfax-' . date('Y-m-d') . '.text', [
            'title' => "Cancel Request for {$awbNumber} : ",
            'data' => $data
        ]);
        $response = Http::withHeaders($this->headers)->post($url,$data);
        Logger::write('logs/partners/shadowfax/shadowfax-' . date('Y-m-d') . '.text', [
            'title' => "Cancel Response for {$awbNumber} : ",
            'data' => $response->json()
        ]);
        return $response->json();
    }

    function reverseManifestationOrder($order,$sellerData){
        $url = $this->endpoint."v3/clients/requests";
        $payload = $this->_GenerateReverseManifestPayload($order,$sellerData);
        $response = Http::withHeaders($this->headers)->post($url,$payload);
        // echo $response;
        return $response->json();
    }

    // Support method for generation of payload
    function _GenerateManifestPayload($order,$sellerData){
        $partnerData = Partners::where('keyword',$order->courier_partner)->first();
        $defaultAmount = DefaultInvoiceAmount::where('seller_id',$sellerData->id ?? 0)->where('partner_id',$partnerData->id ?? 0)->first();
        $defaultInvoiceAmount = 0;
        if (strtolower($order->order_type) == 'prepaid')
        {
            $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
        }
        $products = [];
        foreach ($order->products as $p) {
            $products[] = [
                "hsn_code" => "",
                "invoice_no" => "SNP678",
                "sku_name" => $p->product_sku,
                "client_sku_id" => "",
                "category" => "",
                "price" => round($p->product_unitprice),
                "seller_details" => [
                    "seller_name" => $sellerData->first_name." ".$sellerData->last_name,
                    "seller_address" => $order->p_address_line1,
                    "seller_state" => $sellerData->state,
                    "gstin_number" => $sellerData->gst_number
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

        if (strtolower($order->order_type) == 'cod') {
            $pay_type = "COD";
            if(intval($order->collectable_amount) > 0)
                $collectable_value = $order->collectable_amount;
            else
                $collectable_value = $order->invoice_amount;
        } elseif (strtolower($order->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $promised_delivery_date = Date('Y-m-d', strtotime('+4 days')) . "T00:00:00.000Z";
        $weight = $order->weight > $order->vol_weight ? $order->weight : $order->vol_weight;
        if($weight > 5000)
            $weight -= 2000;
        else if($weight > 2000)
            $weight -= 1000;
        else
            $weight = 500;
        $payload = [
            "order_type" => 'marketplace',
            "order_details" => [
                "client_order_id" => $order->customer_order_number,
                "actual_weight" => $weight,
                "volumetric_weight" => $weight,
                "product_value" => $order->invoice_amount,
                "payment_mode" => $pay_type,
                "cod_amount" => $collectable_value,
                "promised_delivery_date" => $promised_delivery_date,
                "total_amount" => $order->invoice_amount + $defaultInvoiceAmount,
                "order_service" => $this->orderService
            ],
            "customer_details" => [
                "name" => $order->s_customer_name,
                "contact" => $order->s_contact,
                "address_line_1" => $order->s_address_line1,
                "address_line_2" => $order->s_address_line2,
                "city" => $order->s_city,
                "state" => $order->s_state,
                "pincode" => $order->s_pincode,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "pickup_details" => [
                "name" => $order->p_warehouse_name,
                "contact" => $order->p_contact,
                "address_line_1" => $order->p_address_line1,
                "address_line_2" => $order->p_address_line2,
                "city" => $order->p_city,
                "state" => $order->p_state,
                "pincode" => $order->p_pincode,
                "latitude" => "",
                "longitude" => ""
            ],
            "rts_details" => [
                "name" => $order->p_warehouse_name,
                "contact" => $order->p_contact,
                "address_line_1" => $order->p_address_line1,
                "address_line_2" => $order->p_address_line2,
                "city" => $order->p_city,
                "state" => $order->p_state,
                "pincode" => $order->p_pincode
            ],
            "product_details" => $products
        ];
        return $payload;
    }

    // Support method for generating reverse payload
    function _GenerateReverseManifestPayload($order,$sellerData){
        $product = Product::where('order_id', $order->id)->get();
        $product_price = $order->invoice_amount / count($product);
        $seller_name = $sellerData->first_name . ' ' . $sellerData->last_name;
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        // dd($awb_number);
        $products = [];
        foreach ($product as $p) {
            $products[] = [
                "client_sku_id" => $order->id,
                "name" => $p->product_sku,
                "price" => round($product_price),
                "return_reason" => "xyz",
                "brand" => "xyz",
                "category" => "xyz",
                "additional_details" => [
                    "type_extra_care" => "Dangerous Goods",
                    "color" => "xyz",
                    "serial_no" => "ABC.$order->id",
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
                "invoice_no" => "In.$order->id"
            ];
        }

        $payload = [
            "client_order_number" => $order->customer_order_number,
            "total_amount" => $order->invoice_amount,
            "price" => $order->invoice_amount,
            "eway_bill" => "",
            "address_attributes" => [
                "address_line" => $order->s_address_line1 . ' ' . $order->s_address_line2,
                "city" => $order->s_city,
                "country" => $order->s_country,
                "pincode" => $order->s_pincode,
                "name" => $order->s_customer_name,
                "phone_number" => $order->s_contact,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "seller_attributes" => [
                "name" => $order->p_warehouse_name,
                "address_line" => $order->p_address_line1 . ' ' . $order->p_address_line2,
                "city" => $order->p_city,
                "pincode" => $order->p_pincode,
                "phone" => $order->p_contact
            ],
            "skus_attributes" => $products
        ];
        return $payload;
    }

    function GetTracking($orderData)
    {
        $response = Http::withHeaders([
            'Authorization' => "Token {$this->accessToken}"
        ])->get("https://dale.shadowfax.in/api/v3/clients/orders/{$orderData->awb_number}/track/?format=json");
        return $response->json();
    }
}
