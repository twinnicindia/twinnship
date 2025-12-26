<?php

namespace App\Libraries;


use App\Models\Warehouses;
use Illuminate\Support\Facades\Http;

class SMCMaruti
{
    const AUTH_EMAIL = 'ajay.k@Twinnship.in';
    const AUTH_PASSWORD = 'Ship@123$';
    const AUTH_VENDOR_TYPE = 'SELLER';

    public static function getAuthToken()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://apis.delcaper.com/auth/login', [
            "email" => self::AUTH_EMAIL,
            "password" => self::AUTH_PASSWORD,
            "vendorType" => self::AUTH_VENDOR_TYPE
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['data']['accessToken'])) {
                return $responseData['data']['accessToken'];
            }
        }
        return null;
    }

    /*
     * Sends Order Data to SMC
     */

    public static function ShipOrder($orderData){
        $token = SMCMaruti::getAuthToken();
        $payload = self::GeneratePayload($orderData);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Ship Order Request Payload {$orderData->awb_number}",
            'data' => $payload
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])
            ->post('https://apis.delcaper.com/fulfillment/public/seller/order/ecomm/push-order',$payload);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Ship Order Response for {$orderData->awb_number}",
            'data' => $response->json()
        ]);
        return $response->json();
    }


    public static function createManifest($order)
    {
        $token = SMCMaruti::getAuthToken();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])
            ->post('https://apis.delcaper.com/fulfillment/public/seller/order/create-manifest', [
            'awbNumber' => [
                $order->awb_number
            ]
        ]);
        Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
            'title' => "Create Manifest Response for {$order->awb_number}",
            'data' => $response->json()
        ]);

        if ($response->successful()) {
            // Handle successful response
            $data = $response->json();
            return $data; // Return response data as JSON
        } else {
            // Handle error
            return response()->json([
                'error' => $response->body()
            ], $response->status());
        }
    }



    /*
     * Cancels Order to SMC
     */
    // public static function CancelOrder($orderId){
    //     $response = Http::withHeaders([

    //     ])
    //         ->put('https://apis.delcaper.com/fulfillment/public/seller/order/cancel-order', [
    //             "orderId" => $orderId,
    //             "cancelReason" => "Order Cancel by seller"
    //         ]);
    //     Logger::write('logs/partners/smc-new/smc-new-'.date('Y-m-d').'.text', [
    //         'title' => "Cancel Order Response for {$orderId}",
    //         'data' => $response->json()
    //     ]);
    //     return $response->json();
    // }

    public static function CancelOrder($orderId){
        $token = SMCMaruti::getAuthToken();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])
                ->put('https://apis.delcaper.com/fulfillment/public/seller/order/cancel-order', [
                    "orderId" => $orderId,
                    "cancelReason" => "Order Cancel by seller"
                ]);

            $responseData = $response->json();

            // Log the response
            Logger::write('logs/partners/smc-new/smc-new-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Response for {$orderId}",
                'data' => $responseData
            ]);

            return $responseData;
        } catch (\Exception $e) {
            // Log the error
            Logger::write('logs/partners/smc-new/smc-new-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Error for {$orderId}",
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    /*
     * Get Tracking Data from SMC
     */
    public static function TrackOrder($awbNumber){
        $response = Http::withHeaders([

        ])
            ->get('https://apis.delcaper.com/fulfillment/public/seller/order/order-tracking/' .$awbNumber);
        return $response->json();
    }

    /*
     * Get Label Data from SMC
     */
    public static function GetLabel($awbNumber){
        $response = Http::withHeaders([

        ])
            ->get('https://apis.delcaper.com/fulfillment/public/seller/order/download/label-invoice?cAwbNumber=' .$awbNumber);
        return $response->json();

    }



    public static function GeneratePayload($orderData){
        $weight = $orderData->vol_weight > $orderData->weight ? $orderData->vol_weight : $orderData->weight;
//        $calculatedWeight = $orderData->vol_weight > $orderData->weight ? $orderData->vol_weight : $orderData->weight;
//        $weight = round(max($calculatedWeight / 1000, 0.001), 2);
        $codAmount = $orderData->order_type == 'cod' ? 0 : $orderData->invoice_amount;
        $rtoAddress = Warehouses::find($orderData->warehouse_id);
       if($orderData->same_as_rto == 'n')
            $rtoAddress = Warehouses::find($orderData->rto_warehouse_id);
        $payload = [
            "orderId" => "{$orderData->id}",
            "orderSubtype" => "FORWARD",
            "orderCreatedAt" => $orderData->inserted,
            "currency" => "INR",
            "amount" => $orderData->invoice_amount,
            "weight" => intval($weight),
            "lineItems" => [
                [
                    "name" => $orderData->product_name,
                    "price" => $orderData->invoice_amount,
                    "weight" => intval($weight),
                    "quantity" => $orderData->product_qty,
                    "sku" => $orderData->sku,
                    "unitPrice" => 1
                ]
            ],
            "paymentType" => $orderData->order_type == "cod" ? "COD" : "ONLINE",
            "paymentStatus" => "PENDING",
            "subTotal" => $orderData->invoice_amount,
            "remarks" => "",
            "shippingAddress" => [
                "name" => "{$orderData->s_customer_name}",
                "email" => "",
                "phone" => "{$orderData->s_contact}",
                "address1" => "{$orderData->s_address_line1}",
                "address2" => "{$orderData->s_address_line2}",
                "city" => "{$orderData->s_city}",
                "state" => "{$orderData->s_state}",
                "country" => "India",
                "zip" => "{$orderData->s_pincode}",
                "latitude" => "",
                "longitude" => ""
            ],
            "billingAddress" => [
                "name" => "{$orderData->s_customer_name}",
                "email" => "",
                "phone" => "{$orderData->s_contact}",
                "address1" => "{$orderData->s_address_line1}",
                "address2" => "{$orderData->s_address_line2}",
                "city" => "{$orderData->s_city}",
                "state" => "{$orderData->s_state}",
                "country" => "India",
                "zip" => "{$orderData->s_pincode}",
                "latitude" => "",
                "longitude" => ""
            ],
            "pickupAddress" => [
                "name" => "{$orderData->p_customer_name}",
                "email" => "",
                "phone" => "{$orderData->p_contact}",
                "address1" => "{$orderData->p_address_line1}",
                "address2" => "{$orderData->p_address_line2}",
                "city" => "{$orderData->p_city}",
                "state" => "{$orderData->p_state}",
                "country" => "India",
                "zip" => "{$orderData->p_pincode}",
                "latitude" => "",
                "longitude" => ""
            ],
            "returnAddress" => [
                "name" => "{$rtoAddress->warehouse_name}",
                "email" => "",
                "phone" => "{$rtoAddress->contact_number}",
                "address1" => "{$rtoAddress->address_line1}",
                "address2" => "{$rtoAddress->address_line2}",
                "city" => "{$rtoAddress->city}",
                "state" => "{$rtoAddress->state}",
                "country" => "{$rtoAddress->country}",
                "zip" => "{$rtoAddress->pincode}",
                "latitude" => "",
                "longitude" => ""
            ],
            "gst" => $orderData->gst,
            "deliveryPromise" => "SURFACE",
            "discountUnit" => "",
            "discount" => intval($orderData->discount),
            "length" => (float)$orderData->length,
            "width" => (float)$orderData->breadth,
            "height" => (float)$orderData->height
        ];
        return $payload;
    }

}
