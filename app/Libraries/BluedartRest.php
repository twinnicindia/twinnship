<?php

namespace App\Libraries;


use App\Models\BluedartJWTToken;
use App\Models\DefaultInvoiceAmount;
use App\Models\ManifestationIssues;
use App\Models\Order;
use App\Models\Partners;
use App\Models\Seller;
use App\Models\ServiceablePincodeFM;
use App\Models\Warehouses;
use DateTime;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Str;
use SimpleXMLElement;

class BluedartRest
{
    private $loginId;
    private $licenceKey;
    private $TlicenceKey;
    private $customerCode;
    private $area;
    private $apiType;
    private $version;
    private $sellerType;
    private $courierPartner;
    private $appKey;
    private $appSecret;
    private $jwtToken;
    private $packtype;
    private $productCode;
    function __construct($type = 'SE',$courierPartner='bluedart')
    {

        $this->sellerType = $type;
        $this->loginId = 'NDA43765';
        $this->licenceKey = 'rngfjlul8jgpwuievulkimszj7sfhtpu';
        $this->TlicenceKey = 'oorfstv7nnjnnl7hhqltshtnlqrijskt';
        $this->customerCode = '994545';
        $this->area = 'NDA';
        $this->apiType = 'S';
        $this->version = '1.3';
        $this->appKey = 'fcJbzXXRgSXvOpvM3P4YZAVUqEfXvoVL';
        $this->appSecret = '0sqaGSnBJhkhWIR7';
        $this->courierPartner = $courierPartner;
        $this->jwtToken = self::generateToken();
        $this->productCode = 'D';
        if ($courierPartner == 'bluedart_10kg')
            $this->productCode = 'A';
        if ($courierPartner == 'bluedart_10kg_surface')
            $this->productCode = 'E';
        if ($courierPartner == 'bluedart' || $courierPartner == 'bluedart_10kg')
            $this->packtype = '';
        else
            $this->packtype = 'L';
    }

    function generateToken(){
        $token = BluedartJWTToken::whereDate('inserted',date('Y-m-d'))->where('is_alpha',$this->sellerType)->first();
        if(empty($token)){
            $response = Http::withHeaders(['ClientID' => $this->appKey,'clientSecret' => $this->appSecret])->get("https://apigateway.bluedart.com/in/transportation/token/v1/login")->json();
            BluedartJWTToken::create([
                'token' => $response['JWTToken'],
                'inserted' => date('Y-m-d H:i:s'),
                'is_alpha' => $this->sellerType,
            ]);
            return $response['JWTToken'];
        }
        else{
            return $token->token;
        }
    }

    function pickupRegister($wayBill,$order,$orderWeight){
        $pickupDate = date('Y-m-d 16:00:00',strtotime("+1 days"));
        if(date('H') < 11)
            $pickupDate = date('Y-m-d 16:00:00');
        $dateTime = new DateTime($pickupDate);
        $timestampInMilliseconds = $dateTime->getTimestamp() * 1000;
        $payload = [
            'request' => [
                'AreaCode' => $this->area,//'GGN',
                'AWBNo' => [$wayBill['GenerateWayBillResult']['AWBNo']],
                'ContactPersonName' => $order->p_warehouse_name,
                'CustomerAddress1' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->p_address_line1),
                'CustomerAddress2' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->p_address_line2),
                'CustomerAddress3' => '',
                'CustomerCode' => $this->customerCode,
                'CustomerName' => $order->p_warehouse_name,
                'CustomerPincode' => $order->p_pincode,
                'CustomerTelephoneNumber' => $order->p_contact,
                'DoxNDox' => '',
                'EmailID' => 'agautam@twinnicindia.com',
                'IsForcePickup' => true,
                'IsPartialPickup' => true,
                'IsToPayCustomer' => true,
                'IsReversePickup' => false,
                'MobileTelNo' => $order->p_contact,
                'NumberofPieces' => 1,
                'OfficeCloseTime' => '1800',
//                'SubProducts' => ['E-Tailing'],
                'ProductCode' => $this->productCode,
                'PackType' => $this->packtype,
                'RegisterPickup' => true,
                'ReferenceNo' => $order->order_number,
                'Remarks' => 'Remark Test',
                'RouteCode' => '99',
                'ShipmentPickupDate' => "/Date($timestampInMilliseconds)/",
                'ShipmentPickupTime' => '1600',
                'VolumeWeight' => number_format((float)(($orderWeight)/1000), 2, '.', '') ?? "0.5",
                'WeightofShipment' => number_format((float)(($orderWeight)/1000), 2, '.', '') ?? "0.5",
                'isToPayShipper' => $order->order_type == 'cod' ? true : false
            ],
            'profile' => [
                'Api_type' => $this->apiType,
                'LicenceKey' => $this->licenceKey,
                'LoginID' => $this->loginId,
            ]
        ];
        try {
            $pickup = Http::withHeaders(['JWTToken' => $this->jwtToken])->post('https://apigateway.bluedart.com/in/transportation/pickup/v1/RegisterPickup', $payload)->json();
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Generate Pickup Request",
                'data' => $payload
            ]);
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Generate Pickup Response",
                'data' => $pickup
            ]);
            if (!empty($pickup)) {
                if ($pickup['RegisterPickupResult']['IsError'] == false && !empty($pickup['RegisterPickupResult']['TokenNumber'])) {
                    // Store token number and shipment pickup date
                    $order->bluedart_details()->create([
                        'pickup_token_number' => $pickup['RegisterPickupResult']['TokenNumber'],
                        'shipment_pickup_date' => date('H') < 11 ? now()->toDateString() : now()->addDays(1)->toDateString()
                    ]);
                    return $wayBill['GenerateWayBillResult']['AWBNo'];
                }
                return $wayBill['GenerateWayBillResult']['AWBNo'];
            } else
                return false;
        }catch (Exception $e){
            Logger::write('logs/partners/bluedart/bluedart-rest-exception-' . date('Y-m-d') . '.text', [
                'title' => "Generate Pickup Exception",
                'data' => ['line' => $e->getLine(),'message' => $e->getMessage(),'file' => $e->getFile()]
            ]);
            return false;
        }
    }

    function trackOrder(array $payload = []){
        $queryString = [
            'handler' => 'tnt',
            'action' => 'custawbquery',
            'loginid' => $this->loginId,
            'awb' => '',
            'numbers' => '',
            'format' => 'xml',
            'lickey' => $this->TlicenceKey,
            'verno' => $this->version,
            'scan' => '1'
        ];
        $payload = array_merge($queryString, $payload);
        $payload['awb'] = 'awb';
        $res = Http::withHeaders(['JWTToken' => $this->jwtToken])->get("https://apigateway.bluedart.com/in/transportation/tracking/v1/shipment", $payload);
        $res = new SimpleXMLElement($res->body());
        Logger::write('logs/partners/bluedart/bluedart-rest-order-tracking-'.date('Y-m-d').'.text', [
            'title' => "Order Tracking Request",
            'data' => $payload
        ]);
        Logger::write('logs/partners/bluedart/bluedart-rest-order-tracking-'.date('Y-m-d').'.text', [
            'title' => "Order Tracking Response",
            'data' => $res
        ]);
        return $res;
    }

    function generateWaybill($order) {
        if (strtolower($order->order_type) == 'prepaid')
        {
            $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
        }

        $this->area = self::getOriginCodeByPincode($order->p_pincode);

        if($order->seller_id == 6729){
            $productName = Str::random(10)." ".Str::random(8);
        }else{
            $productName = $order->product_name;
        }
        $RTOAddress = [
            'ReturnAddress1' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->p_address_line1),
            'ReturnAddress2' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->p_address_line2),
            'ReturnAddress3' => "",
            'ReturnAddressinfo' => $order->p_pincode,
            'ReturnContact' => $order->p_contact,
            'ReturnEmailID' => $order->b_customer_email,
            'ReturnMobile' => $order->p_contact,
            'ReturnPincode' => $order->p_pincode,
            'ReturnTelephone' => $order->p_contact,
        ];

        if($order->same_as_rto == 'n'){
            if($order->warehouose_id != $order->rto_warehouse_id){
                $warehouse = Warehouses::find($order->rto_warehouse_id);
                $RTOAddress = [
                    'ReturnAddress1' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $warehouse->address_line1),
                    'ReturnAddress2' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $warehouse->address_line2),
                    'ReturnAddress3' => "",
                    'ReturnAddressinfo' => $warehouse->pincode,
                    'ReturnContact' => $warehouse->contact_number,
                    'ReturnEmailID' => $warehouse->support_email,
                    'ReturnMobile' => $warehouse->contact_number,
                    'ReturnPincode' => $warehouse->pincode,
                    'ReturnTelephone' => $warehouse->contact_number,
                ];
            }
        }
        try {
            $pickupDate = now()->addDays(1)->toDateString();
            if(date('H') < 11)
                $pickupDate = now()->toDateString();

//            $pickupDateTimeStamp = strtotime($pickupDate);
            $dateTime = new DateTime($pickupDate);
            $pickupDateTimeStamp = $dateTime->getTimestamp() * 1000;
            $orderWeight = $order->weight;
            if($order->vol_weight > $order->weight) {
                $orderWeight = $order->vol_weight;
            }
            if($orderWeight > 5000)
                $orderWeight -= 2000;
            else if($orderWeight > 2000)
                $orderWeight -= 1000;
            else
                $orderWeight = 500;
            $payload = [
                'Request' =>[
                    'Consignee' => [
                        'ConsigneeAddress1' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->s_address_line1),
                        'ConsigneeAddress2' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $order->s_address_line2),
                        'ConsigneeAddress3'=> '',
                        'ConsigneeAttention'=> '',
                        'ConsigneeMobile'=> $order->s_contact,
                        'ConsigneeName'=> $order->s_customer_name,
                        'ConsigneePincode'=> $order->s_pincode,
                        'ConsigneeTelephone'=> $order->s_contact,
                    ],

                    'Returnadds' => [
                        'ReturnAddress1' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $RTOAddress['ReturnAddress1']),
                        'ReturnAddress2' => preg_replace("/[^a-zA-Z0-9\s\-,]/", "", $RTOAddress['ReturnAddress2']),
                        'ReturnAddress3'=> '',
                        'ReturnAddressinfo  '=> $RTOAddress['ReturnAddressinfo'],
                        'ReturnContact'=> $RTOAddress['ReturnContact'],
                        'ReturnEmailID'=> $RTOAddress['ReturnEmailID'],
                        'ReturnMobile'=> $RTOAddress['ReturnMobile'],
                        'ReturnPincode'=> $RTOAddress['ReturnPincode'],
                        'ReturnTelephone'=> $RTOAddress['ReturnTelephone'],
                    ],
                    'Services' => [
                        'AWBNo' => (!empty($order->awb_number)) ? $order->awb_number : "",
                        'ActualWeight' => ($orderWeight)/1000,
                        'CollectableAmount' => $order->order_type == 'cod' ? (intval($order->collectable_amount) > 0 ? $order->collectable_amount : $order->invoice_amount) : 0,
                        'Commodity' => [
                            'CommodityDetail1' => $productName,
                            'CommodityDetail2'  => '',
                            'CommodityDetail3' => ''
                        ],
                        'CreditReferenceNo' => $order->id."-".rand(1,100),
                        'DeclaredValue' => $order->invoice_amount + ($defaultInvoiceAmount ?? 0),
                        'Dimensions' => [
                            [
                                'Length' => $order->length,
                                'Breadth' => $order->breadth,
                                'Height' => $order->height,
                                'Count' => 1,
                            ]
                        ],
                        'InvoiceNo' => '',
                        'PackType' => $this->packtype,
                        'PickupDate' => "/Date($pickupDateTimeStamp)/",
                        'PickupTime' => '1600',
                        'PieceCount' => 1,
                        'RegisterPickup' => true,
                        'ProductCode' => $this->productCode,
                        'ProductType' => 2,
                        'SpecialInstruction' => '1',
                        'SubProductCode' => '' //$order->order_type == 'cod' ? 'C' : 'P', // For Prepaid P, For COD C
                    ],
                    'Shipper' => [
                        'CustomerAddress1' => $order->p_address_line1,
                        'CustomerAddress2' => $order->p_address_line1,
                        'CustomerAddress3' => '',
                        'CustomerCode' => $this->customerCode,
                        'CustomerEmailID' => '',
                        'CustomerMobile' => $order->p_contact,
                        'CustomerName' => $order->p_customer_name,
                        'CustomerPincode' => $order->p_pincode,
                        'CustomerTelep`hone' => $order->p_contact,
                        'IsToPayCustomer' => true,
                        'OriginArea' => $this->area,
                        'Sender' => $order->p_customer_name,
                        'VendorCode' => str_pad($order->warehouse_id,9,0,STR_PAD_LEFT) ?? ''
                    ]
                ],
                "Profile" => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                ]
            ];
            Logger::write('logs/partners/bluedart/bluedart-rest-'.date('Y-m-d').'.text', [
                'title' => "Generate Waybill Request",
                'data' => $payload
            ]);
            $jwtToken = $this->generateToken();
            $response = Http::withHeaders(['JWTToken' => $jwtToken,'Content-Type' => 'application/json'])->post('https://apigateway.bluedart.com/in/transportation/waybill/v1/GenerateWayBill',$payload)->json();
            if(array_key_exists('GenerateWayBillResult',$response))
                $response['GenerateWayBillResult']['AWBPrintContent'] = "";
            Logger::write('logs/partners/bluedart/bluedart-rest-'.date('Y-m-d').'.text', [
                'title' => "Generate Waybill Response",
                'data' => $response
            ]);
            return $response;
        } catch(Exception $e) {
            Logger::write('logs/partners/bluedart/bluedart-rest-exception-' . date('Y-m-d') . '.text', [
                'title' => "Generate Waybill Exception",
                'data' => ['line' => $e->getLine(),'message' => $e->getMessage(),'file' => $e->getFile()]
            ]);
            return null;
        }
    }

    function shipOrder($order){
        $orderWeight = $order->weight;
        if($order->vol_weight > $order->weight) {
            $orderWeight = $order->vol_weight;
        }
        $wayBill = $this->generateWaybill($order);
        if(empty($wayBill))
            return false;
        if(array_key_exists('GenerateWayBillResult',$wayBill) && !empty($wayBill['GenerateWayBillResult']['AWBNo'] && !$wayBill['GenerateWayBillResult']['IsError'])) {
            // Store BlueDart lable pdf
            $pdf = @file_put_contents("public/assets/seller/label/bluedart/{$wayBill['GenerateWayBillResult']['AWBNo']}.pdf", $wayBill['GenerateWayBillResult']['AWBPrintContent']);
            if($pdf != null && $pdf !== false) {
                $order->bluedart_label = "public/assets/seller/label/bluedart/{$wayBill['GenerateWayBillResult']['AWBNo']}.pdf";
                $order->save();
            }

            $order->bluedart_details()->create([
                'pickup_token_number' => $wayBill['GenerateWayBillResult']['TokenNumber'],
                'shipment_pickup_date' => date('H') < 11 ? now()->toDateString() : now()->addDays(1)->toDateString()
            ]);

            // Store bluedart route code
            if($wayBill['GenerateWayBillResult']['DestinationArea'] && $wayBill['GenerateWayBillResult']['DestinationLocation']) {
                if(empty($order->route_code)) {
                    $order->route_code = $wayBill['GenerateWayBillResult']['DestinationArea'] . '/' . $wayBill['GenerateWayBillResult']['DestinationLocation'];
                    $order->save();
                }
            }
            return $wayBill['GenerateWayBillResult']['AWBNo'];
        }
        else{
            $responseMessage = $wayBill['error-response'][0]['Status'][0]['StatusInformation'] ?? "";
            if(str_contains($responseMessage,"is already exists")){
                return $wayBill['GenerateWayBillResult']['AWBNo'];
            }
            $message = $wayBill['error-response'][0]['Status'][0]['StatusInformation'] ?? "";//$wayBill['GenerateWayBillResult']['Status']['WayBillGenerationStatus']['StatusInformation'] ?? "";
            if(!str_contains(strtolower($message),"unable to process") && $message != ""){
                ManifestationIssues::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'order_id' => $order->id,
                        'message' => $message,
                        'created' => date('Y-m-d H:i:s')
                    ]
                );
                Order::where('id',$order->id)->update(['is_retry' => 1]);
            }
        }
    }

    function getOriginCodeByPincode($pincode){
        return "NDA";
//        $res = ServiceablePincodeFM::where('pincode',$pincode)->where('courier_partner','bluedart')->first();
//        if (!empty($res) && strtoupper($res->region == 'NORTH'))
//            return 'NDA';
//        return $res->origin_code ?? "GGN";
    }

    function cancelOrder($tokenNumber,$pickupDate){
        try {
            $dateTime = new DateTime($pickupDate);
            $pickupDateTimeStamp = $dateTime->getTimestamp() * 1000;
            $payload = [
                'request' => [
                    'TokenNumber' => str_replace('DEMO', '', $tokenNumber),
                    'PickupRegistrationDate' => '/Date('.$pickupDateTimeStamp.')/',
                ],
                'profile' => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId
                ]
            ];
            echo json_encode($payload);
            $cancelPickup = Http::withHeaders(['JWTToken' => $this->jwtToken])->post('https://apigateway.bluedart.com/in/transportation/cancel-pickup/v1/CancelPickup', $payload)->json();
            // Logging
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Pickup Request",
                'data' => $payload
            ]);
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Pickup Response",
                'data' => $cancelPickup
            ]);
            if ($cancelPickup['CancelPickupResult']['IsError'] == false) {
                return true;
            }
        }catch (Exception $e){
            return true;
        }
        return true;
    }

    function cancelWayBill($awb_number){
        try {
            $payload = [
                "Request" => [
                    "AWBNo" => $awb_number
                ],
                "Profile" => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId
                ]
            ];
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Cancel WayBill Request - $awb_number",
                'data' => $payload
            ]);
            $response = Http::withHeaders(['JWTToken' => $this->jwtToken])->post('https://apigateway.bluedart.com/in/transportation/waybill/v1/CancelWaybill', $payload)->json();
            Logger::write('logs/partners/bluedart/bluedart-rest-' . date('Y-m-d') . '.text', [
                'title' => "Cancel WayBill Response - $awb_number",
                'data' => $response
            ]);
            if (!empty($response) && $response['CancelWaybillResult']['IsError'] == false)
                return true;
            else
                return false;
        }catch(Exception $e){
            Logger::write('logs/api/bluedart-rest-exception-'.date('Y-m-d').'.text', [
                'title' => "Cancel WayBill Exception - $awb_number : ",
                'data' => ['error' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]
            ]);
            return false;
        }
    }

    function reAttempt($data){
        try {
            $dateTime = new DateTime( $data['date']);
            $pickupDateTimeStamp = $dateTime->getTimestamp() * 1000;
            $payload = [
                'data' => [
                    'altreq' => [
                        'AWBNo' => $data['AwbNumber'],
                        'PreferDate' => $pickupDateTimeStamp,
                        'AltInstRequestType' => 'DT',
                        'TimeSlot' => "",
                        'MobileNo' => $data['mobile'],
                        'PreferTime' => ""
                    ]
                ],
                "profile" => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId
                ]
            ];
            $response = Http::withHeaders(['JWTToken' => $this->jwtToken])->post('https://apigateway.bluedart.com/in/transportation/cust-instruction-update/v1/CustALTInstructionUpdate', $payload)->json();
            Logger::write('logs/partners/bluedart/bluedart-reattempt-'.date('Y-m-d').'.text', [
                'title' => "NDR ReAttempt Request",
                'data' => $payload
            ]);
            Logger::write('logs/partners/bluedart/bluedart-reattempt-'.date('Y-m-d').'.text', [
                'title' => "NDR ReAttempt Response",
                'data' => $response->json()
            ]);
        }catch(Exception $e){
            Logger::write('logs/api/bluedart-rest-exception-reattempt-'.date('Y-m-d').'.text', [
                'title' => "NDR Exception - ".$data['AwbNumber'].": ",
                'data' => ['error' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]
            ]);
        }
        return true;
    }
}
