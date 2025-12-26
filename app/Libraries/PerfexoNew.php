<?php

namespace App\Libraries;

use App\Models\Basic_informations;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\Product;
use App\Models\XbeesAwbnumberUnique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PerfexoNew
{
    protected $xbKey,$businessName,$username,$password,$secret;
    const xbKey = 'apv12843wcu';
    const businessName = 'UNIQUEENTERPRISES';
    const username = 'admin@uniqusurfa.com';
    const password = '$uniqusurfa$';
    const secret = '3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0';

    public static function getNextAwbNumber(){

    }
    public static function GenerateAwb($o, $orderType, $courierPartner, $xbKey, $awbType)
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
                self::GetAwbNumbersXbees(self::xbKey, $courierPartner, $orderType, $awbType);
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
    public static function GetAwbNumbersXbees($XBkey, $courier, $type, $service = "FORWARD")
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => self::xbKey
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);
        $awb_data = $response->json();
        self::FetchAllAwbs($awb_data['BatchID'], $courier, $type, $service, self::xbKey);
    }
    public static function FetchAllAwbs($batch, $courier, $type, $service = "FORWARD", $XBkey = '')
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
    public static function ExpressBees($orderId, $getAwbNumber, $businessName, $username, $password, $secret, $XBkey,$sellerData)
    {
        $token = self::GetXpressBeesToken(self::username, self::password, self::secret);
        if(empty($token))
            return false;
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = Configuration::find(1);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $collectible_value = $o->invoice_amount;
        }  else {
            $collectible_value = "0";
        }
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "BusinessAccountName" => self::businessName,
            "OrderNo" => $o->customer_order_number,
            "SubOrderNo" => $o->order_number,
            "OrderType" => $o->order_type,
            "CollectibleAmount" => $collectible_value,
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
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/forward', $payload);
        return $response->json();
    }
    public static function GetXpressBeesToken($username, $password, $secret)
    {
        $data = [
            "username" => $username,
            "password" => $password,
            "secretkey" => $secret
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer xyz'
        ])->post('http://userauthapis.xbees.in/api/auth/generateToken', $data);
        // echo $response;
        $data = $response->json();
        return $data['token'] ?? null;
    }
    public static function ExpressBeesReverse($orderId, $getAwbNumber, $businessName, $username, $password, $secret, $XBkey,$sellerData)
    {
        $token = self::GetXpressBeesToken(self::username, self::password, self::secret);
        $o = Order::find($orderId);
        $config = Configuration::find(1);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        $seller = Basic_informations::where('seller_id', $sellerData->id)->first();
        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "OrderNo" => $o->customer_order_number,
            "BusinessAccountName" => self::businessName,
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
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/reverse', $payload);
        return $response->json();
    }
    public static function CancelOrder($awb, $XBkey)
    {
        $data = array(
            'XBkey' => self::xbKey,
            'AWBNumber' => $awb,
            'RTOReason' => 'Seller Cancellation'
        );
        Logger::write('logs/partners/xbees/xbees-unique'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);
        $response = Http::withHeaders([
            "cache-control" => "no-cache",
            "content-type" => "application/json",
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/RTONotifyShipment', $data);
        $response = $response->json();
        Logger::write('logs/partners/xbees/xbees-unique'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    public static function CancelReverseOrderXpressBees($awb, $XBkey)
    {
        $data = array(
            'XBkey' => self::xbKey,
            'AWBNumber' => $awb,
            'RTOReason' => 'Fraud Cancellation'
        );
        Logger::write('logs/partners/xbees/xbees-unique-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $data
        ]);
        $response = Http::withHeaders([
            "cache-control" => "no-cache",
            "content-type" => "application/json",
            "postman-token" => "3886d97b-364b-2748-c922-314f489a1f12"
        ])->post('https://xbclientapi.xbees.in/POSTShipmentService.svc/ReversePickupCancellation', $data);
        $response = $response->json();
        Logger::write('logs/partners/xbees/xbees-unique'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $response
        ]);
    }
    public static function getTrackingDetails($awbNumber){

    }
}
