<?php

namespace App\Libraries;

use App\Libraries\Logger;
use App\Models\DefaultInvoiceAmount;
use App\Models\ManifestationIssues;
use App\Models\Order;
use App\Models\Partners;
use App\Models\Seller;
use App\Models\ServiceablePincodeFM;
use App\Models\Warehouses;
use Illuminate\Support\Str;
use SoapClient;
use SoapHeader;
use DOMDocument;
use SimpleXMLElement;
use Illuminate\Support\Facades\Http;
use Exception;

class DebugSoapClient extends SoapClient {
    public $sendRequest = true;
    public $printRequest = false;
    public $formatXML = false;

    public function __doRequest($request, $location, $action, $version, $one_way=0) {
        if($this->printRequest) {
            if(!$this->formatXML) {
                $out = $request;
            } else {
                $doc = new DOMDocument;
                $doc->preserveWhiteSpace = false;
                $doc->loadxml($request);
                $doc->formatOutput = true;
                $out = $doc->savexml();
            }
            // echo $out;
            dd($out);
        }

        if($this->sendRequest) {
            return parent::__doRequest($request, $location, $action, $version, $one_way);
        } else {
            return '';
        }
    }
}

class BlueDart {
    private $loginId;
    private $licenceKey;
    private $TlicenceKey;
    private $customerCode;
    private $area;
    private $apiType;
    private $version;
    private $sellerType;
    private $courierPartner;

    public function __construct($type = 'SE',$courierPartner='bluedart_surface') {
        $this->sellerType = $type;
        $this->loginId = 'GGN65746';
        $this->licenceKey = 'toukylffqolghwerlljnjlrt7mlngjn7';
        $this->TlicenceKey = 'yem7tspsmpjjhmseejooghsvnspwuplt';
        $this->customerCode = '702166'; //Ajay Sir Said
        $this->area = 'GGN';
        $this->apiType = 'S';
        $this->version = '1.3';
        $this->courierPartner = $courierPartner;

        if($type == 'NSE'){
            $this->loginId = 'GGN52390';
            $this->licenceKey = 'rjenfvp7ttsmjsugljssmnmogjjjvsmu';
            $this->customerCode = '701761';
            $this->TlicenceKey = 'oppqlrgqoxpt0lq5npf1rqqp2rnvel3t';
            $this->area = 'GGN';
            $this->apiType = 'S';
            $this->version = '1.3';
        }

        // $this->loginId = 'GGN65746';
        // $this->licenceKey = 'toukylffqolghwerlljnjlrt7mlngjn7';
        // $this->customerCode = '65746';
        // $this->area = 'GGN';
        // $this->apiType = 'S';
        // $this->version = '1.3';
    }

    public function pincodeServicable(string $pincode) {
        $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Finder/ServiceFinderQuery.svc?wsdl', [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2
        ]);
        $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Finder/ServiceFinderQuery.svc");
        $soap->sendRequest = true;
        $soap->formatXML = true;
        $soap->printRequest = false;

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IServiceFinderQuery/GetServicesforPincode',
            true
        );
        $soap->__setSoapHeaders($actionHeader);
        $params = [
            [
                'pinCode' => $pincode,
                'profile' => [
                    'Api_type' => $this->apiType,
                    'Area' => $this->area,
                    'Customercode' => $this->customerCode,//$this->customerCode,
                    'IsAdmin' => '',
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                    'Password' => '',
                    'Version' => $this->version
                ]
            ]
        ];
        $res = $soap->__soapCall('GetServicesforPincode', $params);
        return $res;
    }

    public function pickupRegister(array $payload) {
        try {
            $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Pickup/PickupRegistrationService.svc?wsdl', [
                'trace' => 1,
                'style' => SOAP_DOCUMENT,
                'use' => SOAP_LITERAL,
                'soap_version' => SOAP_1_2
            ]);

            $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Pickup/PickupRegistrationService.svc");
            $actionHeader = new SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'Action',
                'http://tempuri.org/IPickupRegistration/RegisterPickup',
                true
            );
            $soap->__setSoapHeaders($actionHeader);
            // Sample payload
            // $payload = [
            //     'AreaCode' => 'GGN',
            //     'ContactPersonName' => 'test1',
            //     'CustomerAddress1' => 'test2',
            //     'CustomerAddress2' => 'test3',
            //     'CustomerAddress3' => 'test4',
            //     'CustomerCode' => '830874',
            //     'CustomerName' => 'test',
            //     'CustomerPincode' => '394221',
            //     'CustomerTelephoneNumber' => '12345678',
            //     'DoxNDox' => '1',
            //     'EmailID' => 'abc@gmail.com',
            //     'MobileTelNo' => '9967327037',
            //     'NumberofPieces' => '1',
            //     'OfficeCloseTime' => '16:00',
            //     'ProductCode' => 'A',
            //     'ReferenceNo' => '1234567',
            //     'Remarks' => 'TEST',
            //     'RouteCode' => '99',
            //     'ShipmentPickupDate' => '2021-12-16',
            //     'ShipmentPickupTime' => '1600',
            //     'VolumeWeight' => '1',
            //     'WeightofShipment' => '1',
            //     'isToPayShipper' => 'true',
            // ];
            $params = [
                [
                    'request' => $payload,
                    'profile' => [
                        'Api_type' => $this->apiType,
                        'LicenceKey' => $this->licenceKey,
                        'LoginID' => $this->loginId,
                        'Version' => $this->version
                    ],
                ]
            ];

            $res = $soap->__soapCall('RegisterPickup', $params);
            return $res;
        }catch (Exception $e){
            Logger::write('logs/partners/bluedart/bluedart-ex-'.date('Y-m-d').'.text', [
                'title' => "Generate Waybill Exception",
                'data' => ['awb' => $payload['AWBNo']],
                'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()
            ]);
            return null;
        }
    }

    public function canclePickup(array $payload) {
        $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Pickup/PickupRegistrationService.svc?wsdl', [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2
        ]);
        $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/Pickup/PickupRegistrationService.svc");
        $soap->sendRequest = true;
        $soap->formatXML = true;
        $soap->printRequest = false;

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IPickupRegistration/CancelPickup',
            true
        );
        $soap->__setSoapHeaders($actionHeader);

        // Sample payload
        // $payload = [
        //     'TokenNumber' => '52954DEMO',
        //     'ShipmentPickupDate' => '2021-12-16',
        // ];
        $params = [
            [
                'request' => $payload,
                'profile' => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                    'Version' => $this->version
                ],
            ]
        ];

        $res = $soap->__soapCall('CancelPickup', $params);
        return $res;
    }

    public function generateWayBill(array $payload) {
        $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl', [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2
        ]);
        $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc");
        $soap->sendRequest = true;
        $soap->formatXML = true;
        $soap->printRequest = false;

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IWayBillGeneration/GenerateWayBill',
            true
        );
        $soap->__setSoapHeaders($actionHeader);

        // $payload = [
        //     'Consignee' => [
        //         'ConsigneeAddress1' => 'A',
        //         'ConsigneeAddress2' => 'A',
        //         'ConsigneeAddress3'=> 'A',
        //         'ConsigneeAttention'=> 'A',
        //         'ConsigneeMobile'=> '1234567890',
        //         'ConsigneeName'=> 'A',
        //         'ConsigneePincode'=> '110001',
        //         'ConsigneeTelephone'=> '1234567890',
        //     ],
        //     'Services' => [
        //         'ActualWeight' => '1',
        //         'CollectableAmount' => '0',
        //         'Commodity' => [
        //             'CommodityDetail1' => 'PRETTYSECRETS Dark Blue 	Allure',
        //             'CommodityDetail2'  => ' Aultra Boost Mutltiway Push Up ',
        //             'CommodityDetail3' => 'Bra'
        //         ],
        //         'CreditReferenceNo' => '105',
        //         'DeclaredValue' => '1000',
        //         'Dimensions' => [
        //             'Dimension' => [
        //                 'Breadth' => '1',
        //                 'Count' => '1',
        //                 'Height' => '1',
        //                 'Length' => '1'
        //             ]
        //         ],
        //         'InvoiceNo' => '',
        //         'PackType' => '',
        //         'PickupDate' => '2021-12-20',
        //         'PickupTime' => '1800',
        //         'PieceCount' => '1',
        //         'ProductCode' => 'A',
        //         'ProductType' => 'Dutiables',
        //         'SpecialInstruction' => '1',
        //         'SubProductCode' => ''
        //     ],
        //     'Shipper' => [
        //         'CustomerAddress1' => '1',
        //         'CustomerAddress2' => '1',
        //         'CustomerAddress3' => '1',
        //         'CustomerCode' => '830874',
        //         'CustomerEmailID' => 'a@b.com',
        //         'CustomerMobile' => '1234567890',
        //         'CustomerName' => '1',
        //         'CustomerPincode' => '110001',
        //         'CustomerTelephone' => '1234567890',
        //         'IsToPayCustomer' => '',
        //         'OriginArea' => 'GGN',
        //         'Sender' => '1',
        //         'VendorCode' => ''
        //     ],
        // ];

        // dd($payload);

        $params = [
            [
                'Request' => $payload,
                'Profile' => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                    'Version' => $this->version
                ]
            ]
        ];

        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Generate Waybill Request",
            'data' => $params
        ]);
        try{
            $res = $soap->__soapCall('GenerateWayBill', $params);
        }catch(Exception $e){
            if(!empty($payload['Services']['AWBNo'])){
                Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
                    'title' => "Generate Waybill Exception",
                    'data' => ['awb' => $payload['Services']['AWBNo']]
                ]);
            }
            $res = null;
        }
        return $res;
    }

    public function updateWayBill(array $payload) {
        $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl', [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2
        ]);
        $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc");

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IWayBillGeneration/UpdateEwayBill',
            true
        );
        $soap->__setSoapHeaders($actionHeader);
        // $payload = [
        //     'Consignee' => [
        //         'ConsigneeAddress1' => 'A',
        //         'ConsigneeAddress2' => 'A',
        //         'ConsigneeAddress3'=> 'A',
        //         'ConsigneeAttention'=> 'A',
        //         'ConsigneeMobile'=> '1234567890',
        //         'ConsigneeName'=> 'A',
        //         'ConsigneePincode'=> '110001',
        //         'ConsigneeTelephone'=> '1234567890',
        //     ],
        //     'Services' => [
        //         'ActualWeight' => '1',
        //         'CollectableAmount' => '0',
        //         'Commodity' => [
        //             'CommodityDetail1' => 'PRETTYSECRETS Dark Blue 	Allure',
        //             'CommodityDetail2'  => ' Aultra Boost Mutltiway Push Up ',
        //             'CommodityDetail3' => 'Bra'
        //         ],
        //         'CreditReferenceNo' => '105',
        //         'DeclaredValue' => '1000',
        //         'Dimensions' => [
        //             'Dimension' => [
        //                 'Breadth' => '1',
        //                 'Count' => '1',
        //                 'Height' => '1',
        //                 'Length' => '1'
        //             ]
        //         ],
        //         'InvoiceNo' => '',
        //         'PackType' => '',
        //         'PickupDate' => '2021-12-20',
        //         'PickupTime' => '1800',
        //         'PieceCount' => '1',
        //         'ProductCode' => 'A',
        //         'ProductType' => 'Dutiables',
        //         'SpecialInstruction' => '1',
        //         'SubProductCode' => ''
        //     ],
        //     'Shipper' => [
        //         'CustomerAddress1' => '1',
        //         'CustomerAddress2' => '1',
        //         'CustomerAddress3' => '1',
        //         'CustomerCode' => '830874',
        //         'CustomerEmailID' => 'a@b.com',
        //         'CustomerMobile' => '1234567890',
        //         'CustomerName' => '1',
        //         'CustomerPincode' => '110001',
        //         'CustomerTelephone' => '1234567890',
        //         'IsToPayCustomer' => '',
        //         'OriginArea' => 'GGN',
        //         'Sender' => '1',
        //         'VendorCode' => ''
        //     ],
        // ];
        $params = [
            [
                'Request' => $payload,
                'Profile' => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                    'Version' => $this->version
                ]
            ]
        ];

        $res = $soap->__soapCall('UpdateEwayBill', $params);
        return $res;
    }

    public function cancelWayBill(array $payload) {
        $soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl', [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2
        ]);
        $soap->__setLocation("https://netconnect.bluedart.com/Ver1.10/ShippingAPI/WayBill/WayBillGeneration.svc");

        $actionHeader = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            'http://tempuri.org/IWayBillGeneration/CancelWaybill',
            true
        );
        $soap->__setSoapHeaders($actionHeader);

        // $payload = [
        //     'AWBNo' => '58112284355',
        // ];

        $params = [
            [
                'Request' => $payload,
                'Profile' => [
                    'Api_type' => $this->apiType,
                    'LicenceKey' => $this->licenceKey,
                    'LoginID' => $this->loginId,
                    'Version' => $this->version
                ]
            ]
        ];

        $res = $soap->__soapCall('CancelWaybill', $params);
        return $res;
    }

    public function shipOrder($order) {
        $sellerData = Seller::find($order->seller_id)->first();
        $packType = '';
        if($sellerData->is_alpha == 'SE' && $this->courierPartner == 'bluedart')
            $packType = '';
        else if($sellerData->is_alpha == 'SE' && $this->courierPartner == 'bluedart_surface')
            $packType = 'L';
        else if($this->courierPartner == 'bluedart' && $sellerData->is_alpha == 'NSE')
            $packType = '';
        else if($this->courierPartner == 'bluedart_surface' && $sellerData->is_alpha == 'NSE')
            $packType = 'L';
        $orderWeight = $order->weight;
        if($order->vol_weight > $order->weight) {
            $orderWeight = $order->vol_weight;
        }
        $payload = $this->generatePayload($order);
        // Logging
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Generate Waybill Payload",
            'data' => $payload
        ]);
        $wayBill = $this->generateWayBill($payload);
        // Logging
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Generate Waybill Response",
            'data' => $wayBill
        ]);
        if(empty($wayBill))
            return false;
        if(!empty($wayBill->GenerateWayBillResult->AWBNo && !$wayBill->GenerateWayBillResult->IsError)) {
            // Store BlueDart lable pdf
            $pdf = @file_put_contents("public/assets/seller/label/bluedart/{$wayBill->GenerateWayBillResult->AWBNo}.pdf", $wayBill->GenerateWayBillResult->AWBPrintContent);
            if($pdf != null && $pdf !== false) {
                $order->bluedart_label = "public/assets/seller/label/bluedart/{$wayBill->GenerateWayBillResult->AWBNo}.pdf";
                $order->save();
            }
            // Store bluedart route code
            if($wayBill->GenerateWayBillResult->DestinationArea && $wayBill->GenerateWayBillResult->DestinationLocation) {
                if(empty($order->route_code)) {
                    $order->route_code = $wayBill->GenerateWayBillResult->DestinationArea . '/' . $wayBill->GenerateWayBillResult->DestinationLocation;
                    $order->save();
                }
            }
            return $this->PickupRegisterNew($wayBill,$order,$packType,$orderWeight);
            // dd($wayBill, $pickup);
        }
        else{
            if(!empty($wayBill->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation)){
                if(str_contains($wayBill->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation,"is already exists")){
                    return $this->PickupRegisterNew($wayBill,$order,$packType,$orderWeight);
                }
            }
            $message = $wayBill->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation ?? "";
            if(!str_contains(strtolower($message),"unable to process")){
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
        // dd($wayBill);
        return false;
    }

    function generatePayload($order) {
        $partnerData = Partners::where('keyword',$order->courier_partner)->first();
        $defaultAmount = DefaultInvoiceAmount::where('seller_id',$order->seller_id)->where('partner_id',$partnerData->id)->first();
        if (strtolower($order->order_type) == 'prepaid')
        {
            $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
        }

        //if($order->seller_id == 6596 || $order->seller_id == 1){
            $this->area = self::getOriginCodeByPincode($order->p_pincode);
        //}

//        // #SE-626 : Bluedart Code update for Suwasthi - START
//        if(strtolower($order->s_city) == 'bangalore')
//            $this->area = 'BLR';
//        else if(strtolower($order->s_city) == 'kolkata')
//            $this->area = 'CCU';
//        else if(strtolower($order->s_city) == 'bhiwandi' || strtolower($order->s_city) == 'mumbai')
//            $this->area = 'BOM';
//        // #SE-626 : Bluedart Code update for Suwasthi END

        if($order->seller_id == 6729){
            $productName = Str::random(10)." ".Str::random(8);
        }else{
            $productName = $order->product_name;
        }
        $sellerData = Seller::find($order->seller_id)->first();
        $RTOAddress = [
            'ReturnAddress1' => $order->p_address_line1,
            'ReturnAddress2' => $order->p_address_line2,
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
                    'ReturnAddress1' => $warehouse->address_line1,
                    'ReturnAddress2' => $warehouse->address_line2,
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
            $packType = '';
            if($sellerData->is_alpha == 'SE' && $this->courierPartner == 'bluedart')
                $packType = '';
            else if($sellerData->is_alpha == 'SE' && $this->courierPartner == 'bluedart_surface')
                $packType = 'L';
            else if($this->courierPartner == 'bluedart' && $sellerData->is_alpha == 'NSE')
                $packType = '';
            else if($this->courierPartner == 'bluedart_surface' && $sellerData->is_alpha == 'NSE')
                $packType = 'L';
            $pickupDate = now()->addDays(1)->toDateString();
            if(date('H') < 11)
                $pickupDate = now()->toDateString();
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
                'Consignee' => [
                    'ConsigneeAddress1' => $order->s_address_line1,
                    'ConsigneeAddress2' => $order->s_address_line2,
                    'ConsigneeAddress3'=> '',
                    'ConsigneeAttention'=> '',
                    'ConsigneeMobile'=> $order->s_contact,
                    'ConsigneeName'=> $order->s_customer_name,
                    'ConsigneePincode'=> $order->s_pincode,
                    'ConsigneeTelephone'=> $order->s_contact,
                ],

                'Returnadds' => [
                    'ReturnAddress1' => $RTOAddress['ReturnAddress1'],
                    'ReturnAddress2' => $RTOAddress['ReturnAddress2'],
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
                        'Dimension' => [
                            'Length' => $order->length,
                            'Breadth' => $order->breadth,
                            'Height' => $order->height,
                            'Count' => 1,
                        ]
                    ],
                    'InvoiceNo' => '',
                    'PackType' => $packType,
                    'PickupDate' => $pickupDate,
                    'PickupTime' => '1600',
                    'PieceCount' => 1,
                    'ProductCode' => 'A',
                    'ProductType' => 'Dutiables',
                    'SpecialInstruction' => '1',
                    'SubProductCode' => $order->order_type == 'cod' ? 'C' : 'P', // For Prepaid P, For COD C
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
                    'IsToPayCustomer' => false,
                    'OriginArea' => $this->area,
                    'Sender' => $order->p_customer_name,
                    'VendorCode' => str_pad($order->warehouse->id,9,0,STR_PAD_LEFT) ?? ''
                ],
            ];
            return $payload;
        } catch(Exception $e) {
            return [];
        }
    }

    function PickupRegisterNew($wayBill,$order,$packType,$orderWeight){
        $payload = [
            'AreaCode' => $this->area,//'GGN',
            'AWBNo' => [$wayBill->GenerateWayBillResult->AWBNo],
            'ContactPersonName' => $order->p_warehouse_name,
            'CustomerAddress1' => $order->p_address_line1,
            'CustomerAddress2' => $order->p_address_line2,
            'CustomerAddress3' => '',
            'CustomerCode' => $this->customerCode,
            'CustomerName' => $order->p_warehouse_name,
            'CustomerPincode' => $order->p_pincode,
            'CustomerTelephoneNumber' => $order->p_contact,
            'DoxNDox' => '2',
            'EmailID' => 'info@Twinnship.in',
            'IsForcePickup' => false,
            'IsReversePickup' => false,
            'MobileTelNo' => $order->p_contact,
            'NumberofPieces' => 1,
            'OfficeCloseTime' => '18:00',
            'ProductCode' => 'A',
            'PackType' => $packType,
            'SubProducts' => ['E-Tailing'],
            'ReferenceNo' => $order->order_number,
            'Remarks' => 'Remark Test',
            'RouteCode' => '99',
            'ShipmentPickupDate' => date('H') < 11 ? now()->toDateString() : now()->addDays(1)->toDateString(),
            'ShipmentPickupTime' => '1600',
            'VolumeWeight' => number_format((float)(($orderWeight)/1000), 2, '.', ''),
            'WeightofShipment' => number_format((float)(($orderWeight)/1000), 2, '.', ''),
            'isToPayShipper' => $order->order_type == 'cod' ? true : false,
        ];
        $pickup = $this->pickupRegister($payload);
        // Logging
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Generate Pickup Request",
            'data' => $payload
        ]);
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Generate Pickup Response",
            'data' => $pickup
        ]);
        if(!empty($pickup)) {
            if ($pickup->RegisterPickupResult->IsError == false && !empty($pickup->RegisterPickupResult->TokenNumber)) {
                // Store token number and shipment pickup date
                $order->bluedart_details()->create([
                    'pickup_token_number' => $pickup->RegisterPickupResult->TokenNumber,
                    'shipment_pickup_date' => date('H') < 11 ? now()->toDateString() : now()->addDays(1)->toDateString()
                ]);
                return $wayBill->GenerateWayBillResult->AWBNo;
            }
            return true;
        }
        else
            return false;
    }
    public function cancelOrder(string $tokenNumber, string $pickupDate) {
        $payload = [
            'TokenNumber' => str_replace('DEMO', '', $tokenNumber),
            'PickupRegistrationDate' => $pickupDate,
        ];
        $cancelPickup = $this->canclePickup($payload);
        // Logging
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Cancel Pickup Request",
            'data' => $payload
        ]);
        Logger::write('logs/partners/bluedart/bluedart-'.date('Y-m-d').'.text', [
            'title' => "Cancel Pickup Response",
            'data' => $cancelPickup
        ]);
        if($cancelPickup->CancelPickupResult->IsError == false) {
            return true;
        }
        return true;
    }

    public function trackOrder(array $payload = []) {
        $queryString = [
            'handler' => 'tnt',
            'action' => 'custawbquery',
            'loginid' => $this->loginId,
            'awb' => '',
            'numbers' => '',
            'format' => 'xml',
            'lickey' => $this->TlicenceKey,
            'verno' => $this->version,
            'scan' => '1',
        ];
        $payload = array_merge($queryString, $payload);
        $payload['awb'] = 'awb';
        $res = Http::get('https://api.bluedart.com/servlet/RoutingServlet', $payload);
        $res = new SimpleXMLElement($res->body());
        // Logging
        Logger::write('logs/partners/bluedart/bluedart-order-tracking-'.date('Y-m-d').'.text', [
            'title' => "Order Tracking Request",
            'data' => $payload
        ]);
        Logger::write('logs/partners/bluedart/bluedart-order-tracking-'.date('Y-m-d').'.text', [
            'title' => "Order Tracking Response",
            'data' => $res
        ]);
        return $res;
    }

    function getOriginCode($city){
        $code = "GGN";
        switch (strtolower($city)){
            case "kolkata":
                $code = "CCU";
                break;
            case "bhiwandi":
            case "mumbai":
                $code = "BOM";
                break;
            case "banglore":
                $code = "BLR";
                break;
            case "gurgaon":
            case "tauru":
            case "noida":
            case "greater noida":
                $code = "GGN";
                break;
            case "chennai":
                $code = "MAA";
                break;
            case "indore":
                $code = "IND";
                break;
            case "nasik":
                $code = "NSK";
                break;
            default:
                $code = "GGN";
                break;
        }
        return $code;
    }
    function getOriginCodeByPincode($pincode){
        $res = ServiceablePincodeFM::where('pincode',$pincode)->where('courier_partner','bluedart')->first();
        return $res->origin_code ?? "GGN";
    }
}
