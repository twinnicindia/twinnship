<?php

namespace App\Libraries;


use App\Models\Warehouses;
use Illuminate\Support\Facades\Http;

class SMCNew
{
    const apiKey = '08fc70a87f8ae68869704f8176f0db';
    const baseURL = 'https://app.shipsy.in';
    const customerCode = 'TWIN2';

    /*
     * Sends Order Data to SMC
     */
    public static function ShipOrder($orderData, $courierPartner='smc_air'){
        $payload = self::GeneratePayload($orderData, $courierPartner);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Ship Order Request {$orderData->awb_number}",
            'data' => $payload
        ]);
        $generatedUrl = self::baseURL."/api/customer/integration/consignment/upload/softdata/v2";
        $response = Http::withHeaders(['content-type' => 'application/json', 'api-key' => self::apiKey])->post($generatedUrl,$payload);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Ship Order Response for {$orderData->awb_number}",
            'data' => $response->json()
        ]);
        return $response->json();
    }

    /*
     * Cancels Order to SMC
     */
    public static function CancelOrder($awbNumber){
        $generatedUrl = self::baseURL."/api/customer/integration/consignment/cancel";
        $payload = [
            'AWBNo' => [
                "{$awbNumber}"
            ],
            "customerCode" => self::customerCode
        ];
        $response = Http::withHeaders(['content-type' => 'application/json', 'api-key' => self::apiKey])->post($generatedUrl,$payload);
        return $response->json();
    }

    /*
     * Get Tracking Data from SMC
     */
    public static function TrackOrder($awbNumber){
        $generatedUrl = self::baseURL."/api/customer/integration/consignment/track?reference_number={$awbNumber}";
        $response = Http::withHeaders(['content-type' => 'application/json', 'api-key' => self::apiKey])->get($generatedUrl);
        return $response->json();
    }

    /*
     * Get Label Data from SMC
     */
    public static function GetLabel($awbNumber){
        $generatedUrl = self::baseURL."/api/customer/integration/consignment/shippinglabel/stream?reference_number={$awbNumber}";
        return Http::withHeaders(['content-type' => 'application/json', 'api-key' => self::apiKey])->get($generatedUrl);
    }

    /*
     * Create Pickup to SMC
     */

    public static function CreatePickup($orderData){
        $generatedUrl = self::baseURL."/api/customer/integration/pickup/create";
        $payload = self::GeneratePickupPayload($orderData);
        $response = Http::withHeaders(['content-type' => 'application/json', 'api-key' => self::apiKey])->post($generatedUrl,$payload);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Create Pickup for {$orderData->awb_number}",
            'data' => $response->json()
        ]);
        return $response->json();
    }

    public static function GeneratePayload($orderData, $courierPartner){
        $serviceTypeID = in_array($courierPartner, ['smc_air', 'smc_air_2kg']) ? "Air" : "Surface";
        $weight = $orderData->vol_weight > $orderData->weight ? round(($orderData->vol_weight/1000),2) : round(($orderData->weight/1000),2);
        $codAmount = $orderData->order_type == 'prepaid' ? 0 : ($orderData->collectable_amount > 0 ? $orderData->collectable_amount : $orderData->invoice_amount);
        $rtoAddress = Warehouses::find($orderData->warehouse_id);
        if($orderData->same_as_rto == 'n' && !empty($orderData->rto_warehouse_id))
            $rtoAddress = Warehouses::find($orderData->rto_warehouse_id);
        $payload = [
            "action_type" => "single_pickup",
            "consignment_type" => "forward",
            "movement_type" => "forward",
            "eway_bill" => $orderData->ewaybill_number,
            "load_type" => "NON-DOCUMENT",
            "description" => "Common",
            "customer_code" => self::customerCode,
            "service_type_id" => $serviceTypeID,
            "cod_favor_of" => $orderData->s_customer_name,
            "cod_collection_mode" => $orderData->order_type == "cod" ? "Cash" : "Online", // Cash-Online
            "dimension_unit" => "cm",
            "length" => "{$orderData->length}",
            "width" => "{$orderData->breadth}",
            "height" => "{$orderData->height}",
            "weight_unit" => "kg",
            "weight" => "{$weight}",
            "volume" => "10",
            "volume_unit" => "m3",
            "cod_amount" => "{$codAmount}",
            "invoice_amount" => "{$orderData->invoice_amount}",
            "invoice_number" => "{$orderData->id}",
            "invoice_date" => date('Y-m-d'),
            "declared_value" => $orderData->invoice_amount,
            "declared_value_without_tax" => $orderData->invoice_amount,
            "product_code" => "{$orderData->product_name}",
            "num_pieces" => 1,
            "customer_reference_number" => "{$orderData->customer_order_number}",
            "is_risk_surcharge_applicable" => false,
            "courier_partner" => "",
            "courier_account" => "",
            "courier_partner_reference_number" => "",
            "hub_code" => "",
            "hsn_code" => "",
            "tax_details" => [
                [
                    "cgst" => "0.0",
                    "sgst" => "0.0",
                    "igst" => "0.0",
                    "total_tax" => "0.0",
                    "sender_gstin" => "06ABICS4825P1ZQ"
                ]
            ],
            "delivery_time_slot_start" => "",
            "delivery_time_slot_end" => "",
            "scheduled_at" => "",
            "service_time" => 1200,
            "reference_image_url" => "https://shipsy-public-assets.s3.amazonaws.com/shipsyflamingo/logo.png",
            "origin_details" => [
                "address_hub_code" => "",
                "name" => "{$orderData->p_warehouse_name}",
                "phone" => "{$orderData->p_contact}",
                "alternate_phone" => "",
                "address_line_1" => "{$orderData->p_address_line1}",
                "address_line_2" => "{$orderData->p_address_line2}",
                "pincode" => "{$orderData->p_pincode}",
                "district" => "{$orderData->p_city}",
                "city" => "{$orderData->p_city}",
                "state" => "{$orderData->p_state}",
                "country" => "India",
                "latitude" => "",
                "longitude" => ""
            ],
            "destination_details" => [
                "address_hub_code" => "",
                "name" => "{$orderData->s_customer_name}",
                "phone" => "{$orderData->s_contact}",
                "alternate_phone" => "",
                "address_line_1" => "{$orderData->s_address_line1}",
                "address_line_2" => "{$orderData->s_address_line2}",
                "pincode" => "{$orderData->s_pincode}",
                "district" => "{$orderData->s_city}",
                "city" => "{$orderData->s_city}",
                "state" => "{$orderData->s_state}",
                "country" => "India",
                "latitude" => "",
                "longitude" => ""
            ],
            "nodes" => [
                [
                    "courier_partner" => "",
                    "courier_accounts" => "",
                    "declared_value" => "",
                    "mode" => "",
                    "node_type" => ""
                ]
            ],
            "return_details" => [
                "address_hub_code" => "",
                "name" => "{$rtoAddress->warehouse_name}",
                "phone" => "{$rtoAddress->support_phone}",
                "alternate_phone" => "{$rtoAddress->contact_number}",
                "address_line_1" => "{$rtoAddress->address_line1}",
                "address_line_2" => "Opp po",
                "pincode" => "{$rtoAddress->pincode}",
                "district" => "{$rtoAddress->city}",
                "city" => "{$rtoAddress->city}",
                "state" => "{$rtoAddress->state}",
                "country" => "India",
                "latitude" => "",
                "longitude" => ""
            ],
            "pieces_detail" => [
                [
                    "description" => "{$orderData->product_name}",
                    "declared_value" => $orderData->invoice_amount,
                    "volume" => "10",
                    "weight" => "{$weight}",
                    "height" => "{$orderData->height}",
                    "length" => "{$orderData->length}",
                    "width" => "{$orderData->breadth}",
                    "weight_unit" => "kg",
                    "dimension_unit" => "cm",
                    "volume_unit" => "MMQ",
                    "piece_product_code" => "{$orderData->product_sku}",
                    "reference_image_url" => "https://shipsy-public-assets.s3.amazonaws.com/shipsyflamingo/logo.png"
                ]
            ]
        ];
        return $payload;
    }

    public static function GeneratePickupPayload($orderData){
        $pickupDate = date('H') < 11 ? date('d/m/Y') : date('d/m/Y',strtotime('+1 day'));
        $payload = [
            'pickup_type' => 'BUSINESS',
            'customer_code' => self::customerCode,
            'pickup_address' => [
                'pincode' => $orderData->p_pincode,
                'name' => $orderData->p_pincode,
                'phone' => $orderData->p_pincode,
                'address_line_1' => $orderData->p_pincode,
                'address_line_2' => $orderData->p_pincode,
                'city' => $orderData->p_pincode,
                'state' => $orderData->p_pincode,
                'country' => $orderData->p_pincode,
            ],
            'load_type' => 'DOCUMENT',
            'total_items' => '1',
            'total_weight' => round(($orderData->weight/1000),2),
            'pickup_slot' => [
                'start' => "11:00",
                'end' => "23:00",
                'date' => "{$pickupDate}"
            ]
        ];
        return $payload;
    }
}
