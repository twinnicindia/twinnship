<?php

namespace App\Http\Controllers;

use App\Helper\ONDCHelper;
use App\Helper\ShippingHelper;
use App\Helper\TrackingHelper;
use App\Libraries\BucketHelper;
use App\Libraries\Shadowfax;
use App\Models\Basic_informations;
use App\Models\BluedartWebHookResponse;
use App\Models\COD_transactions;
use App\Models\Configuration;
use App\Models\DownloadOrderReportModel;
use App\Models\DtdcAwbNumbers;
use App\Models\EcomExpressAwbs;
use App\Models\EkartAwbNumbers;
use App\Models\InternationalOrders;
use App\Models\LabelCustomization;
use App\Models\Manifest;
use App\Models\ManifestOrder;
use App\Models\MPS_AWB_Number;
use App\Models\Ndrattemps;
use App\Models\Order;
use App\Models\Preferences;
use App\Models\Product;
use App\Models\Rules;
use App\Models\Seller;
use App\Models\ShadowfaxAWBNumbers;
use App\Models\Warehouses;
use App\Models\OrderTracking;
use App\Models\Transactions;
use App\Models\Partners;
use App\Models\Rates;
use App\Models\ServiceablePincode;
use App\Models\ServiceablePincodeFM;
use App\Models\XbeesAwbnumber;
use App\Models\XbeesAwbnumberUnique;
use App\Models\ZoneMapping;
use App\Models\SmartrAwbs;
use App\Models\GatiAwbs;
use App\Models\Courier_blocking;
use App\Models\ZZArchiveOrder;
use Illuminate\Http\Request;
use Validator\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Libraries\Logger;
use App\Libraries\BlueDart;
use App\Libraries\AmazonSWA;
use App\Libraries\Prefexo;
use App\Libraries\Bombax;
use App\Libraries\Ekart;
use App\Libraries\Gati;
use App\Libraries\Maruti;
use App\Libraries\MyUtility;
use Illuminate\Support\Facades\Validator as LaravelValidator;
use Exception;
use PDF;

class ApiController extends Controller {
    protected $metroCities,$partners,$shipment,$orderStatus,$partnerNames,$info;

    public function __construct() {
        $this->info['config'] = Configuration::find(1);
        $this->shipment=new ShippingController();
        $this->metroCities = ['bangalore', 'chennai', 'hyderabad', 'kolkata', 'mumbai', 'new delhi', 'delhi', 'pune', 'gurugram', 'gurgaon'];
        $this->orderStatus = [
            "pending" => "Pending",
            "shipped" => "Shipped",
            "manifested" => "Manifested",
            "pickup_scheduled" => "Pickup Scheduled",
            "picked_up" => "Picked Up",
            "cancelled" => "Cancelled",
            "in_transit" => "In Transit",
            "out_for_delivery" => "Out for Delivery",
            "rto_initated" => "RTO Initiated",
            "rto_delivered" => "RTO Delivered",
            "rto_in_transit" => "RTO In Transit",
            "delivered" => "Delivered",
            "ndr" => "NDR",
            "lost" => "Lost",
            "damaged" => "Damaged",
            "hold" => "Hold"
        ];
        $this->partners=[
            'shadow_fax' => 'Shadowfax',
            'delhivery_surface' => 'Delhivery',
            'delhivery_surface_2kg' => 'Delhivery',
            'delhivery_surface_5kg' => 'Delhivery',
            'delhivery_surface_air' => 'Delhivery',
            'dtdc_surface' => 'DTDC',
            'dtdc_1kg' => 'DTDC',
            'dtdc_2kg' => 'DTDC',
            'dtdc_3kg' => 'DTDC',
            'dtdc_5kg' => 'DTDC',
            'dtdc_6kg' => 'DTDC',
            'dtdc_10kg' => 'DTDC',
            'dtdc_express' => 'DTDC',
            'amazon_swa' => 'AmazonSwa',
            'amazon_swa_10kg' => 'AmazonSwa',
            'amazon_swa_1kg' => 'AmazonSwa',
            'amazon_swa_3kg' => 'AmazonSwa',
            'amazon_swa_5kg' => 'AmazonSwa',
            'xpressbees_surface' => 'XPRESSBEES',
            'fedex' => 'FedEx',
            'wow_express' => 'WowExpress',
            'udaan' => 'Udaan',
            'udaan_1kg' => 'Udaan',
            'udaan_2kg' => 'Udaan',
            'udaan_3kg' => 'Udaan',
            'udaan_10kg' => 'Udaan',
            'xpressbees_surface_1kg' => 'XPRESSBEES',
            'xpressbees_surface_3kg' => 'XPRESSBEES',
            'xpressbees_surface_5kg' => 'XPRESSBEES',
            'xpressbees_surface_10kg' => 'XPRESSBEES',
            'xpressbees_sfc'  => 'XPRESSBEES',
            'smartr' => 'Smartrlogistics'
        ];
        $this->partnerNames = [
            'amazon_swa' => 'AmazonSwa',
            'amazon_swa_10kg' => 'AmazonSwa',
            'amazon_swa_1kg' => 'AmazonSwa',
            'amazon_swa_3kg' => 'AmazonSwa',
            'amazon_swa_5kg' => 'AmazonSwa',
            'bluedart' => 'Bluedart',
            'bluedart_surface' => 'Bluedart',
            'shadow_fax' => 'Shadowfax',
            'delhivery_surface' => 'Delhivery',
            'delhivery_surface_10kg' => 'Delhivery',
            'delhivery_surface_20kg' => 'Delhivery',
            'delhivery_b2b_20kg' => 'Delhivery',
            'delhivery_surface_2kg' => 'Delhivery',
            'delhivery_surface_5kg' => 'Delhivery',
            'delhivery_air' => 'Delhivery',
            'dtdc_surface' => 'DTDC',
            'dtdc_10kg' => 'DTDC',
            'dtdc_2kg' => 'DTDC',
            'dtdc_3kg' => 'DTDC',
            'dtdc_5kg' => 'DTDC',
            'dtdc_6kg' => 'DTDC',
            'dtdc_1kg' => 'DTDC',
            'dtdc_express' => 'DTDC',
            'ecom_express' => 'Ecom Express',
            'ecom_express_rvp' => 'Ecom Express',
            'ecom_express_3kg' => 'Ecom Express',
            'ecom_express_3kg_rvp' => 'Ecom Express',
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
            'smartr' => 'Smartrlogistics'
        ];
    }

    // Create order api
    function createOrder(Request $request) {
        try{
            $validator = new Validator();
            $sellerId = null;
            //Set validation rules
            $validator->rules([
                'ApiKey' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_api_key' => function($apiKey) use(&$sellerId) {
                            $seller = Seller::where('api_key', $apiKey)->first();
                            if(!empty($seller)) {
                                $sellerId = $seller->id;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'OrderDetails' => 'required|not_null|array'
            ]);

            //Set error messages
            $validator->messages([
                'ApiKey' => [
                    'rules' => [
                        'valid_api_key' => 'Invalid api key.'
                    ]
                ],
            ]);

            Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
                'title' => 'Create Order Request Payload',
                'data' => $request->all()
            ]);

            if($validator->validate($request->all())) {
                //Set validation rules
                $validator->rules([
                    'PaymentType' => 'in:prepaid,cod|required',
                    'OrderType,PaymentType, CustomerName' => 'required|not_null',
                    'Addresses.BilingAddress.AddressLine2, Addresses.ShippingAddress.AddressLine2, Addresses.PickupAddress.AddressLine2' => 'required',
                    'Addresses.BilingAddress.AddressLine1, Addresses.BilingAddress.City, Addresses.BilingAddress.State, Addresses.BilingAddress.Country, Addresses.BilingAddress.Pincode, Addresses.BilingAddress.ContactCode, Addresses.BilingAddress.Contact, Addresses.ShippingAddress.AddressLine1, Addresses.ShippingAddress.City, Addresses.ShippingAddress.State, Addresses.ShippingAddress.Country, Addresses.ShippingAddress.Pincode, Addresses.ShippingAddress.ContactCode, Addresses.ShippingAddress.Contact, Addresses.PickupAddress.WarehouseName, Addresses.PickupAddress.ContactName, Addresses.PickupAddress.AddressLine1, Addresses.PickupAddress.City, Addresses.PickupAddress.State, Addresses.PickupAddress.Country, Addresses.PickupAddress.Pincode, Addresses.PickupAddress.ContactCode, Addresses.PickupAddress.Contact' => 'required|not_null',
                    'Weight, Length, Breadth, Height, InvoiceAmount' => 'required|not_null',
                ]);

                //Set error messages
                $validator->messages([
                    'PaymentType' =>[
                        'required' => 'Please provide Order Type',
                        'in' => 'Invalid Order Type must be prepaid,cod'
                    ],
                    'Addresses.BilingAddress.AddressLine1' => [
                        'required' => 'Please add Addressline1 in Billing Address',
                        'not_null' => 'Addressline1 in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.AddressLine2' => [
                        'required' => 'Please add Addressline2 in Billing Address',
                    ],
                    'Addresses.BilingAddress.City' => [
                        'required' => 'Please add City in Billing Address',
                        'not_null' => 'City in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.State' => [
                        'required' => 'Please add State in Billing Address',
                        'not_null' => 'State in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.Country' => [
                        'required' => 'Please add Country in Billing Address',
                        'not_null' => 'Country in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.Pincode' => [
                        'required' => 'Please add Pincode in Billing Address',
                        'not_null' => 'Pincode in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.ContactCode' => [
                        'required' => 'Please add ContactCode in Billing Address',
                        'not_null' => 'ContactCode in Billing Address should not be null.'
                    ],
                    'Addresses.BilingAddress.Contact' => [
                        'required' => 'Please add Contact in Billing Address',
                        'not_null' => 'Contact in Billing Address should not be null.'
                    ],

                    'Addresses.ShippingAddress.AddressLine1' => [
                        'required' => 'Please add Addressline1 in Shipping Address',
                        'not_null' => 'Addressline1 in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.AddressLine2' => [
                        'required' => 'Please add Addressline2 in Shipping Address',
                    ],
                    'Addresses.ShippingAddress.City' => [
                        'required' => 'Please add City in Shipping Address',
                        'not_null' => 'City in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.State' => [
                        'required' => 'Please add State in Shipping Address',
                        'not_null' => 'State in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.Country' => [
                        'required' => 'Please add Country in Shipping Address',
                        'not_null' => 'Country in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.Pincode' => [
                        'required' => 'Please add Pincode in Shipping Address',
                        'not_null' => 'Pincode in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.ContactCode' => [
                        'required' => 'Please add ContactCode in Shipping Address',
                        'not_null' => 'ContactCode in Shipping Address should not be null.'
                    ],
                    'Addresses.ShippingAddress.Contact' => [
                        'required' => 'Please add Contact in Shipping Address',
                        'not_null' => 'Contact in Shipping Address should not be null.'
                    ],

                    'Addresses.PickupAddress.WarehouseName' => [
                        'required' => 'Please add WarehouseName in Pickup Address',
                        'not_null' => 'WarehouseName in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.ContactName' => [
                        'required' => 'Please add ContactName in Pickup Address',
                        'not_null' => 'ContactName in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.Addressline1' => [
                        'required' => 'Please add Addressline1 in Pickup Address',
                        'not_null' => 'Addressline1 in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.AddressLine2' => [
                        'required' => 'Please add Addressline2 in Pickup Address',
                    ],
                    'Addresses.PickupAddress.City' => [
                        'required' => 'Please add City in Pickup Address',
                        'not_null' => 'City in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.State' => [
                        'required' => 'Please add State in Pickup Address',
                        'not_null' => 'State in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.Country' => [
                        'required' => 'Please add Country in Pickup Address',
                        'not_null' => 'Country in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.Pincode' => [
                        'required' => 'Please add Pincode in Pickup Address',
                        'not_null' => 'Pincode in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.ContactCode' => [
                        'required' => 'Please add ContactCode in Pickup Address',
                        'not_null' => 'ContactCode in Pickup Address should not be null.'
                    ],
                    'Addresses.PickupAddress.Contact' => [
                        'required' => 'Please add Contact in Pickup Address',
                        'not_null' => 'Contact in Pickup Address should not be null.'
                    ],
                ]);
                $orderId = [];
                foreach($request->OrderDetails as $orderDetails) {
                    if($orderDetails['InvoiceAmount'] > 50000 && empty($orderDetails['EwayBill'])) {
                        $res[] = [
                            'order_id' => null,
                            'status' => false,
                            'message' => [
                                'EwayBill' => 'EwayBill number is required if invoice amount is more than 50000'
                            ]
                        ];
                        continue;
                    }
                    if($validator->validate($orderDetails)) {
                        $totalOrders=DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id',$sellerId)->where('channel','custom')->first();
                        $totalOrder = $totalOrders->order_number;
                        //$totalOrder=Order::where('seller_id',$sellerId)->where('channel','custom')->max('order_number');
                        if(empty($totalOrder))
                            $orderNumber = 1001;
                        else
                            $orderNumber = $totalOrder + 1;
                        if($request->warehouseCode!= "")
                            $wareHouse = Warehouses::where('seller_id', $sellerId)->where('warehouse_code',$request->warehouseCode)->first();
                        else
                            $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
                        if(empty($wareHouse)){
                            $res[] = [
                                'order_id' => null,
                                'status' => false,
                                'message' => "Please create default warehouse first"
                            ];
                            continue;
                        }
                        $billingAddress = $orderDetails['Addresses']['BilingAddress'];
                        $shippingAddress = $orderDetails['Addresses']['ShippingAddress'];

                        $igst = 0;
                        $cgst = 0;
                        $sgst = 0;
                        if(!empty($orderDetails['InvoiceAmount'])) {
                            if(strtolower($shippingAddress['State']) == strtolower($wareHouse->state)) {
                                $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                                $cgst = $percent/2;
                                $sgst = $percent/2;
                            } else {
                                $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                                $igst = $percent;
                            }
                        }

                        $orderData = [
                            'seller_id' => $sellerId,
                            'warehouse_id' => $wareHouse->id,
                            'order_number' => $orderNumber,
                            'customer_order_number' => $orderDetails['OrderNumber'] ?? $orderNumber,
                            'order_type' => strtolower($orderDetails['PaymentType']) == "cod" ? "cod" : "prepaid",
                            'o_type' => strtolower($orderDetails['OrderType']),
                            'channel' => 'api',

                            // Billing Address
                            'b_customer_name' => $orderDetails['CustomerName'],
                            'b_address_line1' => $billingAddress['AddressLine1'],
                            'b_address_line2' => $billingAddress['AddressLine2'],
                            'b_city' => $billingAddress['City'],
                            'b_state' => $billingAddress['State'],
                            'b_country' => $billingAddress['Country'],
                            'b_pincode' => $billingAddress['Pincode'],
                            'b_contact_code' => $billingAddress['ContactCode'],
                            'b_contact' => $billingAddress['Contact'],
                            'delivery_address' => $shippingAddress['AddressLine1'] . ',' . $shippingAddress['AddressLine2'] . ',' . $shippingAddress['City'] . ',' . $shippingAddress['State'] . ',' . $shippingAddress['Pincode'],

                            // Pickup address
                            'p_warehouse_name' => $wareHouse['warehouse_name'],
                            'p_customer_name' => $wareHouse['contact_name'],
                            'p_address_line1' => $wareHouse['address_line1'],
                            'p_address_line2' => $wareHouse['address_line2'],
                            'p_city' => $wareHouse['city'],
                            'p_state' => $wareHouse['state'],
                            'p_country' => $wareHouse['country'],
                            'p_pincode' => $wareHouse['pincode'],
                            'p_contact_code' => $wareHouse['code'],
                            'p_contact' => $wareHouse['support_phone'],
                            'pickup_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                            // Shipping address
                            's_customer_name' => $orderDetails['CustomerName'],
                            's_address_line1' => $shippingAddress['AddressLine1'],
                            's_address_line2' => $shippingAddress['AddressLine2'],
                            's_city' => $shippingAddress['City'],
                            's_state' => $shippingAddress['State'],
                            's_country' => $shippingAddress['Country'],
                            's_pincode' => $shippingAddress['Pincode'],
                            's_contact_code' => $shippingAddress['ContactCode'],
                            's_contact' => $shippingAddress['Contact'],

                            'weight' => $orderDetails['Weight'] * 1000,
                            'length' => $orderDetails['Length'],
                            'breadth' => $orderDetails['Breadth'],
                            'height' => $orderDetails['Height'],
                            'vol_weight' => ($orderDetails['Height'] * $orderDetails['Length'] * $orderDetails['Breadth']) / 5,
                            's_charge' => $orderDetails['ShippingCharge'] ?? null,
                            'c_charge' => $orderDetails['CodCharge'] ?? null,
                            'discount' => $orderDetails['Discount'] ?? null,
                            'invoice_amount' => $orderDetails['InvoiceAmount'],
                            'igst' => $igst,
                            'sgst' => $sgst,
                            'cgst' => $cgst,
                            'ewaybill_number' => $orderDetails['EwayBill'] ?? null,

                            // MPS Details
                            // 'shipment_type' => $orderDetails['shipmentType'] ?? 'single',
                            // 'number_of_packets' => $orderDetails['numberOfPackets'] ?? 1,
                            'collectable_amount' => $orderDetails['CollectableAmount'] ?? 0 ,
                            'inserted' => date('Y-m-d H:i:s'),
                            'inserted_by' => $sellerId,
                        ];

                        $order = Order::create($orderData);
                        $productName = [];
                        $productSKU = [];
                        foreach($orderDetails['ProductDetails'] as $productDetails) {
                            $productData = [
                                'order_id' => $order->id,
                                'product_sku' => $productDetails['SKU'],
                                'product_name' => $productDetails['Name'],
                                'product_qty' => $productDetails['QTY'],
                                'total_amount' => $productDetails['Amount'] ?? null
                            ];
                            $productName[] = $productDetails['Name'];
                            $productSKU[] = $productDetails['SKU'];
                            Product::create($productData);
                        }
                        Order::where('id', $order->id)->update(['product_name' => implode(',', $productName), 'product_sku' => implode(',', $productSKU)]);
                        $res[] = [
                            'order_id' => $order->id,
                            'status' => true,
                            'message' => 'Order created successfully.'
                        ];
                    } else {
                        $res[] = [
                            'order_id' => null,
                            'status' => false,
                            'message' => $validator->errors()
                        ];
                    }
                }
            } else {
                $res['message'] = $validator->errors();
            }

            Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
                'title' => 'Create Order Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        }
        catch(Exception $e){
            return response()->json(['status' => false,'message' => $e->getMessage()]);
        }
    }

    // Create Order With Pickup
    function createOrderWithPickup(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderDetails' => 'required|not_null|array'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order with Pickup Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            //Set validation rules
            $validator->rules([
                'PaymentType, OrderType, CustomerName' => 'required|not_null',
                'Addresses.BilingAddress.AddressLine2, Addresses.ShippingAddress.AddressLine2, Addresses.PickupAddress.AddressLine2' => 'required',
                'Addresses.BilingAddress.AddressLine1, Addresses.BilingAddress.City, Addresses.BilingAddress.State, Addresses.BilingAddress.Country, Addresses.BilingAddress.Pincode, Addresses.BilingAddress.ContactCode, Addresses.BilingAddress.Contact, Addresses.ShippingAddress.AddressLine1, Addresses.ShippingAddress.City, Addresses.ShippingAddress.State, Addresses.ShippingAddress.Country, Addresses.ShippingAddress.Pincode, Addresses.ShippingAddress.ContactCode, Addresses.ShippingAddress.Contact, Addresses.PickupAddress.WarehouseName, Addresses.PickupAddress.ContactName, Addresses.PickupAddress.AddressLine1, Addresses.PickupAddress.City, Addresses.PickupAddress.State, Addresses.PickupAddress.Country, Addresses.PickupAddress.Pincode, Addresses.PickupAddress.ContactCode, Addresses.PickupAddress.Contact' => 'required|not_null',
                'Weight, Length, Breadth, Height, InvoiceAmount' => 'required|not_null',
            ]);

            //Set error messages
            $validator->messages([
                'Addresses.BilingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Billing Address',
                    'not_null' => 'Addressline1 in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Billing Address',
                ],
                'Addresses.BilingAddress.City' => [
                    'required' => 'Please add City in Billing Address',
                    'not_null' => 'City in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.State' => [
                    'required' => 'Please add State in Billing Address',
                    'not_null' => 'State in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Country' => [
                    'required' => 'Please add Country in Billing Address',
                    'not_null' => 'Country in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Billing Address',
                    'not_null' => 'Pincode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Billing Address',
                    'not_null' => 'ContactCode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Contact' => [
                    'required' => 'Please add Contact in Billing Address',
                    'not_null' => 'Contact in Billing Address should not be null.'
                ],

                'Addresses.ShippingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Shipping Address',
                    'not_null' => 'Addressline1 in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Shipping Address',
                ],
                'Addresses.ShippingAddress.City' => [
                    'required' => 'Please add City in Shipping Address',
                    'not_null' => 'City in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.State' => [
                    'required' => 'Please add State in Shipping Address',
                    'not_null' => 'State in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Country' => [
                    'required' => 'Please add Country in Shipping Address',
                    'not_null' => 'Country in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Shipping Address',
                    'not_null' => 'Pincode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Shipping Address',
                    'not_null' => 'ContactCode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Contact' => [
                    'required' => 'Please add Contact in Shipping Address',
                    'not_null' => 'Contact in Shipping Address should not be null.'
                ],

                'Addresses.PickupAddress.WarehouseName' => [
                    'required' => 'Please add WarehouseName in Pickup Address',
                    'not_null' => 'WarehouseName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactName' => [
                    'required' => 'Please add ContactName in Pickup Address',
                    'not_null' => 'ContactName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Addressline1' => [
                    'required' => 'Please add Addressline1 in Pickup Address',
                    'not_null' => 'Addressline1 in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Pickup Address',
                ],
                'Addresses.PickupAddress.City' => [
                    'required' => 'Please add City in Pickup Address',
                    'not_null' => 'City in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.State' => [
                    'required' => 'Please add State in Pickup Address',
                    'not_null' => 'State in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Country' => [
                    'required' => 'Please add Country in Pickup Address',
                    'not_null' => 'Country in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Pincode' => [
                    'required' => 'Please add Pincode in Pickup Address',
                    'not_null' => 'Pincode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Pickup Address',
                    'not_null' => 'ContactCode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Contact' => [
                    'required' => 'Please add Contact in Pickup Address',
                    'not_null' => 'Contact in Pickup Address should not be null.'
                ],
            ]);
            $orderId = [];
            $sellerData = Seller::find($sellerId);
            foreach($request->OrderDetails as $orderDetails) {
                if($orderDetails['InvoiceAmount'] > 50000 && empty($orderDetails['EwayBill'])) {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => [
                            'EwayBill' => 'EwayBill number is required if invoice amount is more than 50000'
                        ]
                    ];
                    continue;
                }
                if($validator->validate($orderDetails)) {
                    $totalOrders=DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id',$sellerId)->where('channel','custom')->first();
                    $totalOrder = $totalOrders->order_number;
                    //$totalOrder=Order::where('seller_id',$sellerId)->where('channel','custom')->max('order_number');
                    if(empty($totalOrder))
                        $orderNumber = 1001;
                    else
                        $orderNumber = $totalOrder + 1;
                    // check warehouse exists or not
                    $pickupAddress = $orderDetails['Addresses']['PickupAddress'];
                    $wareHouse = Warehouses::firstOrCreate([
                        'seller_id' => $sellerId,
                        'address_line1' => $pickupAddress['AddressLine1'],
                        'city' => $pickupAddress['City'],
                        'state' => $pickupAddress['State'],
                        'pincode' => $pickupAddress['Pincode'],
                        'support_phone' => $pickupAddress['Contact']
                    ], [
                        'seller_id' => $sellerId,
                        'warehouse_name' => $pickupAddress['WarehouseName'],
                        'warehouse_code' => $pickupAddress['WarehouseName'].$sellerData->code,
                        'contact_name' => $pickupAddress['ContactName'],
                        'contact_number' => $pickupAddress['Contact'] ?? $sellerData->mobile,
                        'address_line1' => $pickupAddress['AddressLine1'],
                        'city' => $pickupAddress['City'],
                        'state' => $pickupAddress['State'],
                        'country' => $pickupAddress['Country'],
                        'pincode' => $pickupAddress['Pincode'],
                        'gst_number' =>  null,
                        'support_phone' => $pickupAddress['Contact'] ?? $sellerData->mobile,
                    ]);
                    if($wareHouse->wasRecentlyCreated)
                        @$this->createWarehouseAtCourier($wareHouse);
                    if(empty($wareHouse)){
                        $res[] = [
                            'order_id' => null,
                            'status' => false,
                            'message' => "Please create default warehouse first"
                        ];
                        continue;
                    }
                    $billingAddress = $orderDetails['Addresses']['BilingAddress'];
                    $shippingAddress = $orderDetails['Addresses']['ShippingAddress'];

                    $igst = 0;
                    $cgst = 0;
                    $sgst = 0;
                    if(!empty($orderDetails['InvoiceAmount'])) {
                        if(strtolower($shippingAddress['State']) == strtolower($wareHouse->state)) {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $cgst = $percent/2;
                            $sgst = $percent/2;
                        } else {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $igst = $percent;
                        }
                    }

                    $orderData = [
                        'seller_id' => $sellerId,
                        'warehouse_id' => $wareHouse->id,
                        'order_number' => $orderNumber,
                        'customer_order_number' => $orderDetails['OrderNumber'] ?? $orderNumber,
                        'order_type' => strtolower($orderDetails['PaymentType']),
                        'o_type' => strtolower($orderDetails['OrderType']),
                        'channel' => 'api',

                        // Billing Address
                        'b_customer_name' => $orderDetails['CustomerName'],
                        'b_address_line1' => $billingAddress['AddressLine1'],
                        'b_address_line2' => $billingAddress['AddressLine2'],
                        'b_city' => $billingAddress['City'],
                        'b_state' => $billingAddress['State'],
                        'b_country' => $billingAddress['Country'],
                        'b_pincode' => $billingAddress['Pincode'],
                        'b_contact_code' => $billingAddress['ContactCode'],
                        'b_contact' => $billingAddress['Contact'],
                        'delivery_address' => $shippingAddress['AddressLine1'] . ',' . $shippingAddress['AddressLine2'] . ',' . $shippingAddress['City'] . ',' . $shippingAddress['State'] . ',' . $shippingAddress['Pincode'],

                        // Pickup address
                        'p_warehouse_name' => $wareHouse['warehouse_name'],
                        'p_customer_name' => $wareHouse['contact_name'],
                        'p_address_line1' => $wareHouse['address_line1'],
                        'p_address_line2' => $wareHouse['address_line2'],
                        'p_city' => $wareHouse['city'],
                        'p_state' => $wareHouse['state'],
                        'p_country' => $wareHouse['country'],
                        'p_pincode' => $wareHouse['pincode'],
                        'p_contact_code' => $wareHouse['code'],
                        'p_contact' => $wareHouse['support_phone'],
                        'pickup_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                        // Shipping address
                        's_customer_name' => $orderDetails['CustomerName'],
                        's_address_line1' => $shippingAddress['AddressLine1'],
                        's_address_line2' => $shippingAddress['AddressLine2'],
                        's_city' => $shippingAddress['City'],
                        's_state' => $shippingAddress['State'],
                        's_country' => $shippingAddress['Country'],
                        's_pincode' => $shippingAddress['Pincode'],
                        's_contact_code' => $shippingAddress['ContactCode'],
                        's_contact' => $shippingAddress['Contact'],

                        'weight' => $orderDetails['Weight'] * 1000,
                        'length' => $orderDetails['Length'],
                        'breadth' => $orderDetails['Breadth'],
                        'height' => $orderDetails['Height'],
                        'vol_weight' => ($orderDetails['Height'] * $orderDetails['Length'] * $orderDetails['Breadth']) / 5,
                        's_charge' => $orderDetails['ShippingCharge'] ?? null,
                        'c_charge' => $orderDetails['CodCharge'] ?? null,
                        'discount' => $orderDetails['Discount'] ?? null,
                        'invoice_amount' => $orderDetails['InvoiceAmount'],
                        'igst' => $igst,
                        'sgst' => $sgst,
                        'cgst' => $cgst,
                        'ewaybill_number' => $orderDetails['EwayBill'] ?? null,

                        // MPS Details
                        // 'shipment_type' => $orderDetails['shipmentType'] ?? 'single',
                        // 'number_of_packets' => $orderDetails['numberOfPackets'] ?? 1,

                        'inserted' => date('Y-m-d H:i:s'),
                        'inserted_by' => $sellerId,
                    ];
                    $existing = Order::where('customer_order_number',$orderData['customer_order_number'])->where('seller_id',$sellerData->id)->where('status','!=','cancelled')->first();
                    if(empty($existing)){
                        $order = Order::create($orderData);
                        // delete if existing product exists
                        Product::where('order_id',$order->id)->delete();
                        $productName = [];
                        $productSKU = [];
                        foreach($orderDetails['ProductDetails'] as $productDetails) {
                            $productData = [
                                'order_id' => $order->id,
                                'product_sku' => $productDetails['SKU'],
                                'product_name' => $productDetails['Name'],
                                'product_qty' => $productDetails['QTY'],
                                'total_amount' => $productDetails['Amount'] ?? null
                            ];
                            $productName[] = $productDetails['Name'];
                            $productSKU[] = $productDetails['SKU'];
                            Product::create($productData);
                        }
                        Order::where('id', $order->id)->update(['product_name' => implode(',', $productName), 'product_sku' => implode(',', $productSKU)]);
                    }
                    else
                        $order = $existing;
                    $res[] = [
                        'order_id' => $order->id,
                        'status' => true,
                        'message' => 'Order created successfully.'
                    ];
                } else {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => $validator->errors()
                    ];
                }
            }
        } else {
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order with Pickup Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }
    // Create reverse order api
    function createReverseOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderDetails' => 'required|not_null|array'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            //Set validation rules
            $validator->rules([
                'PaymentType, OrderType, CustomerName' => 'required|not_null',
                'Addresses.BilingAddress.AddressLine2, Addresses.ShippingAddress.AddressLine2, Addresses.PickupAddress.AddressLine2' => 'required',
                'Addresses.BilingAddress.AddressLine1, Addresses.BilingAddress.City, Addresses.BilingAddress.State, Addresses.BilingAddress.Country, Addresses.BilingAddress.Pincode, Addresses.BilingAddress.ContactCode, Addresses.BilingAddress.Contact, Addresses.ShippingAddress.AddressLine1, Addresses.ShippingAddress.City, Addresses.ShippingAddress.State, Addresses.ShippingAddress.Country, Addresses.ShippingAddress.Pincode, Addresses.ShippingAddress.ContactCode, Addresses.ShippingAddress.Contact, Addresses.PickupAddress.WarehouseName, Addresses.PickupAddress.ContactName, Addresses.PickupAddress.AddressLine1, Addresses.PickupAddress.City, Addresses.PickupAddress.State, Addresses.PickupAddress.Country, Addresses.PickupAddress.Pincode, Addresses.PickupAddress.ContactCode, Addresses.PickupAddress.Contact' => 'required|not_null',
                'Weight, Length, Breadth, Height, InvoiceAmount' => 'required|not_null',
            ]);

            //Set error messages
            $validator->messages([
                'Addresses.BilingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Billing Address',
                    'not_null' => 'Addressline1 in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Billing Address',
                ],
                'Addresses.BilingAddress.City' => [
                    'required' => 'Please add City in Billing Address',
                    'not_null' => 'City in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.State' => [
                    'required' => 'Please add State in Billing Address',
                    'not_null' => 'State in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Country' => [
                    'required' => 'Please add Country in Billing Address',
                    'not_null' => 'Country in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Billing Address',
                    'not_null' => 'Pincode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Billing Address',
                    'not_null' => 'ContactCode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Contact' => [
                    'required' => 'Please add Contact in Billing Address',
                    'not_null' => 'Contact in Billing Address should not be null.'
                ],

                'Addresses.ShippingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Shipping Address',
                    'not_null' => 'Addressline1 in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Shipping Address',
                ],
                'Addresses.ShippingAddress.City' => [
                    'required' => 'Please add City in Shipping Address',
                    'not_null' => 'City in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.State' => [
                    'required' => 'Please add State in Shipping Address',
                    'not_null' => 'State in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Country' => [
                    'required' => 'Please add Country in Shipping Address',
                    'not_null' => 'Country in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Shipping Address',
                    'not_null' => 'Pincode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Shipping Address',
                    'not_null' => 'ContactCode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Contact' => [
                    'required' => 'Please add Contact in Shipping Address',
                    'not_null' => 'Contact in Shipping Address should not be null.'
                ],

                'Addresses.PickupAddress.WarehouseName' => [
                    'required' => 'Please add WarehouseName in Pickup Address',
                    'not_null' => 'WarehouseName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactName' => [
                    'required' => 'Please add ContactName in Pickup Address',
                    'not_null' => 'ContactName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Addressline1' => [
                    'required' => 'Please add Addressline1 in Pickup Address',
                    'not_null' => 'Addressline1 in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Pickup Address',
                ],
                'Addresses.PickupAddress.City' => [
                    'required' => 'Please add City in Pickup Address',
                    'not_null' => 'City in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.State' => [
                    'required' => 'Please add State in Pickup Address',
                    'not_null' => 'State in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Country' => [
                    'required' => 'Please add Country in Pickup Address',
                    'not_null' => 'Country in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Pincode' => [
                    'required' => 'Please add Pincode in Pickup Address',
                    'not_null' => 'Pincode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Pickup Address',
                    'not_null' => 'ContactCode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Contact' => [
                    'required' => 'Please add Contact in Pickup Address',
                    'not_null' => 'Contact in Pickup Address should not be null.'
                ],
            ]);
            $orderId = [];
            foreach($request->OrderDetails as $orderDetails) {
                if($orderDetails['InvoiceAmount'] > 50000 && empty($orderDetails['EwayBill'])) {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => [
                            'EwayBill' => 'EwayBill number is required if invoice amount is more than 50000'
                        ]
                    ];
                    continue;
                }
                if($validator->validate($orderDetails)) {
                    $totalOrders=DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id',$sellerId)->where('channel','custom')->first();
                    $totalOrder = $totalOrders->order_number;
                    //$totalOrder=Order::where('seller_id',$sellerId)->where('channel','custom')->max('order_number');
                    if(empty($totalOrder))
                        $orderNumber = 1001;
                    else
                        $orderNumber = $totalOrder + 1;
                    if($request->warehouseCode!= "")
                        $wareHouse = Warehouses::where('seller_id', $sellerId)->where('warehouse_code',$request->warehouseCode)->first();
                    else
                        $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
                    if(empty($wareHouse)){
                        $res[] = [
                            'order_id' => null,
                            'status' => false,
                            'message' => "Please create default warehouse first"
                        ];
                        continue;
                    }
                    $billingAddress = $orderDetails['Addresses']['BilingAddress'];
                    $shippingAddress = $orderDetails['Addresses']['ShippingAddress'];

                    $igst = 0;
                    $cgst = 0;
                    $sgst = 0;
                    if(!empty($orderDetails['InvoiceAmount'])) {
                        if(strtolower($shippingAddress['State']) == strtolower($wareHouse->state)) {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $cgst = $percent/2;
                            $sgst = $percent/2;
                        } else {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $igst = $percent;
                        }
                    }

                    $orderData = [
                        'seller_id' => $sellerId,
                        'warehouse_id' => $wareHouse->id,
                        'order_number' => $orderNumber,
                        'customer_order_number' => $orderDetails['OrderNumber'] ?? $orderNumber,
                        'order_type' => "prepaid",
                        'o_type' => strtolower($orderDetails['OrderType']),
                        'channel' => 'api',

                        //Billing Address
                        'b_customer_name' => $orderDetails['CustomerName'],
                        'b_address_line1' => $billingAddress['AddressLine1'],
                        'b_address_line2' => $billingAddress['AddressLine2'],
                        'b_city' => $billingAddress['City'],
                        'b_state' => $billingAddress['State'],
                        'b_country' => $billingAddress['Country'],
                        'b_pincode' => $billingAddress['Pincode'],
                        'b_contact_code' => $billingAddress['ContactCode'],
                        'b_contact' => $billingAddress['Contact'],
                        'delivery_address' => $billingAddress['AddressLine1'] . ',' . $billingAddress['AddressLine2'] . ',' . $billingAddress['City'] . ',' . $billingAddress['State'] . ',' . $billingAddress['Pincode'],

                        // 's_warehouse_name' => $wareHouse['warehouse_name'],
                        // 's_customer_name' => $wareHouse['contact_name'],
                        // 's_address_line1' => $wareHouse['address_line1'],
                        // 's_address_line2' => $wareHouse['address_line2'],
                        // 's_city' => $wareHouse['city'],
                        // 's_state' => $wareHouse['state'],
                        // 's_country' => $wareHouse['country'],
                        // 's_pincode' => $wareHouse['pincode'],
                        // 's_contact_code' => $wareHouse['code'],
                        // 's_contact' => $wareHouse['support_phone'],
                        // 'delivery_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                        // 'p_customer_name' => $orderDetails['CustomerName'],
                        // 'p_address_line1' => $shippingAddress['AddressLine1'],
                        // 'p_address_line2' => $shippingAddress['AddressLine2'],
                        // 'p_city' => $shippingAddress['City'],
                        // 'p_state' => $shippingAddress['State'],
                        // 'p_country' => $shippingAddress['Country'],
                        // 'p_pincode' => $shippingAddress['Pincode'],
                        // 'p_contact_code' => $shippingAddress['ContactCode'],
                        // 'p_contact' => $shippingAddress['Contact'],

                        // Pickup address
                        'p_warehouse_name' => $wareHouse['warehouse_name'],
                        'p_customer_name' => $wareHouse['contact_name'],
                        'p_address_line1' => $wareHouse['address_line1'],
                        'p_address_line2' => $wareHouse['address_line2'],
                        'p_city' => $wareHouse['city'],
                        'p_state' => $wareHouse['state'],
                        'p_country' => $wareHouse['country'],
                        'p_pincode' => $wareHouse['pincode'],
                        'p_contact_code' => $wareHouse['code'],
                        'p_contact' => $wareHouse['support_phone'],
                        'pickup_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                        // Shipping address
                        's_customer_name' => $orderDetails['CustomerName'],
                        's_address_line1' => $shippingAddress['AddressLine1'],
                        's_address_line2' => $shippingAddress['AddressLine2'],
                        's_city' => $shippingAddress['City'],
                        's_state' => $shippingAddress['State'],
                        's_country' => $shippingAddress['Country'],
                        's_pincode' => $shippingAddress['Pincode'],
                        's_contact_code' => $shippingAddress['ContactCode'],
                        's_contact' => $shippingAddress['Contact'],

                        'weight' => $orderDetails['Weight'] * 1000,
                        'length' => $orderDetails['Length'],
                        'breadth' => $orderDetails['Breadth'],
                        'height' => $orderDetails['Height'],
                        'vol_weight' => ($orderDetails['Height'] * $orderDetails['Length'] * $orderDetails['Breadth']) / 5,
                        's_charge' => $orderDetails['ShippingCharge'] ?? null,
                        'c_charge' => $orderDetails['CodCharge'] ?? null,
                        'discount' => $orderDetails['Discount'] ?? null,
                        'invoice_amount' => $orderDetails['InvoiceAmount'],
                        'igst' => $igst,
                        'sgst' => $sgst,
                        'cgst' => $cgst,
                        'ewaybill_number' => $orderDetails['EwayBill'] ?? null,

                        // MPS Details
                        // 'shipment_type' => $orderDetails['shipmentType'] ?? 'single',
                        // 'number_of_packets' => $orderDetails['numberOfPackets'] ?? 1,

                        'inserted' => date('Y-m-d H:i:s'),
                        'inserted_by' => $sellerId,
                    ];
                    $order = Order::create($orderData);
                    $productName = [];
                    $productSKU = [];
                    foreach($orderDetails['ProductDetails'] as $productDetails) {
                        $productData = [
                            'order_id' => $order->id,
                            'product_sku' => $productDetails['SKU'],
                            'product_name' => $productDetails['Name'],
                            'product_qty' => $productDetails['QTY'],
                            'total_amount' => $productDetails['Amount'] ?? null
                        ];
                        $productName[] = $productDetails['Name'];
                        $productSKU[] = $productDetails['SKU'];
                        Product::create($productData);
                    }
                    Order::where('id', $order->id)->update(['product_name' => implode(',', $productName), 'product_sku' => implode(',', $productSKU)]);
                    $res[] = [
                        'order_id' => $order->id,
                        'status' => true,
                        'message' => 'Order created successfully.'
                    ];
                } else {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => $validator->errors()
                    ];
                }
            }
        } else {
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }

    function createReverseOrderWithQc(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderDetails' => 'required|not_null|array'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            //Set validation rules
            $validator->rules([
                'PaymentType, OrderType, CustomerName' => 'required|not_null',
                'Addresses.BilingAddress.AddressLine2, Addresses.ShippingAddress.AddressLine2, Addresses.PickupAddress.AddressLine2' => 'required',
                'Addresses.BilingAddress.AddressLine1, Addresses.BilingAddress.City, Addresses.BilingAddress.State, Addresses.BilingAddress.Country, Addresses.BilingAddress.Pincode, Addresses.BilingAddress.ContactCode, Addresses.BilingAddress.Contact, Addresses.ShippingAddress.AddressLine1, Addresses.ShippingAddress.City, Addresses.ShippingAddress.State, Addresses.ShippingAddress.Country, Addresses.ShippingAddress.Pincode, Addresses.ShippingAddress.ContactCode, Addresses.ShippingAddress.Contact, Addresses.PickupAddress.WarehouseName, Addresses.PickupAddress.ContactName, Addresses.PickupAddress.AddressLine1, Addresses.PickupAddress.City, Addresses.PickupAddress.State, Addresses.PickupAddress.Country, Addresses.PickupAddress.Pincode, Addresses.PickupAddress.ContactCode, Addresses.PickupAddress.Contact' => 'required|not_null',
                'Weight, Length, Breadth, Height, InvoiceAmount' => 'required|not_null',
                'QCInformation.Image, QCInformation.Description' => 'required|not_null',
            ]);

            //Set error messages
            $validator->messages([
                'Addresses.BilingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Billing Address',
                    'not_null' => 'Addressline1 in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Billing Address',
                ],
                'Addresses.BilingAddress.City' => [
                    'required' => 'Please add City in Billing Address',
                    'not_null' => 'City in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.State' => [
                    'required' => 'Please add State in Billing Address',
                    'not_null' => 'State in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Country' => [
                    'required' => 'Please add Country in Billing Address',
                    'not_null' => 'Country in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Billing Address',
                    'not_null' => 'Pincode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Billing Address',
                    'not_null' => 'ContactCode in Billing Address should not be null.'
                ],
                'Addresses.BilingAddress.Contact' => [
                    'required' => 'Please add Contact in Billing Address',
                    'not_null' => 'Contact in Billing Address should not be null.'
                ],

                'Addresses.ShippingAddress.AddressLine1' => [
                    'required' => 'Please add Addressline1 in Shipping Address',
                    'not_null' => 'Addressline1 in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Shipping Address',
                ],
                'Addresses.ShippingAddress.City' => [
                    'required' => 'Please add City in Shipping Address',
                    'not_null' => 'City in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.State' => [
                    'required' => 'Please add State in Shipping Address',
                    'not_null' => 'State in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Country' => [
                    'required' => 'Please add Country in Shipping Address',
                    'not_null' => 'Country in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Pincode' => [
                    'required' => 'Please add Pincode in Shipping Address',
                    'not_null' => 'Pincode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Shipping Address',
                    'not_null' => 'ContactCode in Shipping Address should not be null.'
                ],
                'Addresses.ShippingAddress.Contact' => [
                    'required' => 'Please add Contact in Shipping Address',
                    'not_null' => 'Contact in Shipping Address should not be null.'
                ],

                'Addresses.PickupAddress.WarehouseName' => [
                    'required' => 'Please add WarehouseName in Pickup Address',
                    'not_null' => 'WarehouseName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactName' => [
                    'required' => 'Please add ContactName in Pickup Address',
                    'not_null' => 'ContactName in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Addressline1' => [
                    'required' => 'Please add Addressline1 in Pickup Address',
                    'not_null' => 'Addressline1 in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.AddressLine2' => [
                    'required' => 'Please add Addressline2 in Pickup Address',
                ],
                'Addresses.PickupAddress.City' => [
                    'required' => 'Please add City in Pickup Address',
                    'not_null' => 'City in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.State' => [
                    'required' => 'Please add State in Pickup Address',
                    'not_null' => 'State in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Country' => [
                    'required' => 'Please add Country in Pickup Address',
                    'not_null' => 'Country in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Pincode' => [
                    'required' => 'Please add Pincode in Pickup Address',
                    'not_null' => 'Pincode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.ContactCode' => [
                    'required' => 'Please add ContactCode in Pickup Address',
                    'not_null' => 'ContactCode in Pickup Address should not be null.'
                ],
                'Addresses.PickupAddress.Contact' => [
                    'required' => 'Please add Contact in Pickup Address',
                    'not_null' => 'Contact in Pickup Address should not be null.'
                ],
                'QCInformation.Image' => [
                    'required' => 'Please add image',
                    'not_null' => 'Image should not be null.'
                ],
                'QCInformation.Description' => [
                    'required' => 'Please add Description',
                    'not_null' => 'Description should not be null.'
                ],
            ]);
            $orderId = [];
            foreach($request->OrderDetails as $orderDetails) {
                if($orderDetails['InvoiceAmount'] > 50000 && empty($orderDetails['EwayBill'])) {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => [
                            'EwayBill' => 'EwayBill number is required if invoice amount is more than 50000'
                        ]
                    ];
                    continue;
                }
                if($validator->validate($orderDetails)) {
                    $totalOrders=DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id',$sellerId)->where('channel','custom')->first();
                    $totalOrder = $totalOrders->order_number;
                    //$totalOrder=Order::where('seller_id',$sellerId)->where('channel','custom')->max('order_number');
                    if(empty($totalOrder))
                        $orderNumber = 1001;
                    else
                        $orderNumber = $totalOrder + 1;
                    if($request->warehouseCode!= "")
                        $wareHouse = Warehouses::where('seller_id', $sellerId)->where('warehouse_code',$request->warehouseCode)->first();
                    else
                        $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
                    if(empty($wareHouse)){
                        $res[] = [
                            'order_id' => null,
                            'status' => false,
                            'message' => "Please create default warehouse first"
                        ];
                        continue;
                    }
                    $billingAddress = $orderDetails['Addresses']['BilingAddress'];
                    $shippingAddress = $orderDetails['Addresses']['ShippingAddress'];

                    $igst = 0;
                    $cgst = 0;
                    $sgst = 0;
                    if(!empty($orderDetails['InvoiceAmount'])) {
                        if(strtolower($shippingAddress['State']) == strtolower($wareHouse->state)) {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $cgst = $percent/2;
                            $sgst = $percent/2;
                        } else {
                            $percent = $orderDetails['InvoiceAmount'] - ($orderDetails['InvoiceAmount']/((18/100)+1));
                            $igst = $percent;
                        }
                    }

                    $orderData = [
                        'seller_id' => $sellerId,
                        'warehouse_id' => $wareHouse->id,
                        'order_number' => $orderNumber,
                        'customer_order_number' => $orderDetails['OrderNumber'] ?? $orderNumber,
                        'order_type' => "prepaid",
                        'o_type' => "reverse",
                        'channel' => 'api',

                        //Billing Address
                        'b_customer_name' => $orderDetails['CustomerName'],
                        'b_address_line1' => $billingAddress['AddressLine1'],
                        'b_address_line2' => $billingAddress['AddressLine2'],
                        'b_city' => $billingAddress['City'],
                        'b_state' => $billingAddress['State'],
                        'b_country' => $billingAddress['Country'],
                        'b_pincode' => $billingAddress['Pincode'],
                        'b_contact_code' => $billingAddress['ContactCode'],
                        'b_contact' => $billingAddress['Contact'],
                        'delivery_address' => $billingAddress['AddressLine1'] . ',' . $billingAddress['AddressLine2'] . ',' . $billingAddress['City'] . ',' . $billingAddress['State'] . ',' . $billingAddress['Pincode'],

                        // 's_warehouse_name' => $wareHouse['warehouse_name'],
                        // 's_customer_name' => $wareHouse['contact_name'],
                        // 's_address_line1' => $wareHouse['address_line1'],
                        // 's_address_line2' => $wareHouse['address_line2'],
                        // 's_city' => $wareHouse['city'],
                        // 's_state' => $wareHouse['state'],
                        // 's_country' => $wareHouse['country'],
                        // 's_pincode' => $wareHouse['pincode'],
                        // 's_contact_code' => $wareHouse['code'],
                        // 's_contact' => $wareHouse['support_phone'],
                        // 'delivery_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                        // 'p_customer_name' => $orderDetails['CustomerName'],
                        // 'p_address_line1' => $shippingAddress['AddressLine1'],
                        // 'p_address_line2' => $shippingAddress['AddressLine2'],
                        // 'p_city' => $shippingAddress['City'],
                        // 'p_state' => $shippingAddress['State'],
                        // 'p_country' => $shippingAddress['Country'],
                        // 'p_pincode' => $shippingAddress['Pincode'],
                        // 'p_contact_code' => $shippingAddress['ContactCode'],
                        // 'p_contact' => $shippingAddress['Contact'],

                        // Pickup address
                        'p_warehouse_name' => $wareHouse['warehouse_name'],
                        'p_customer_name' => $wareHouse['contact_name'],
                        'p_address_line1' => $wareHouse['address_line1'],
                        'p_address_line2' => $wareHouse['address_line2'],
                        'p_city' => $wareHouse['city'],
                        'p_state' => $wareHouse['state'],
                        'p_country' => $wareHouse['country'],
                        'p_pincode' => $wareHouse['pincode'],
                        'p_contact_code' => $wareHouse['code'],
                        'p_contact' => $wareHouse['support_phone'],
                        'pickup_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                        // Shipping address
                        's_customer_name' => $orderDetails['CustomerName'],
                        's_address_line1' => $shippingAddress['AddressLine1'],
                        's_address_line2' => $shippingAddress['AddressLine2'],
                        's_city' => $shippingAddress['City'],
                        's_state' => $shippingAddress['State'],
                        's_country' => $shippingAddress['Country'],
                        's_pincode' => $shippingAddress['Pincode'],
                        's_contact_code' => $shippingAddress['ContactCode'],
                        's_contact' => $shippingAddress['Contact'],

                        'weight' => $orderDetails['Weight'] * 1000,
                        'length' => $orderDetails['Length'],
                        'breadth' => $orderDetails['Breadth'],
                        'height' => $orderDetails['Height'],
                        'vol_weight' => ($orderDetails['Height'] * $orderDetails['Length'] * $orderDetails['Breadth']) / 5,
                        's_charge' => $orderDetails['ShippingCharge'] ?? null,
                        'c_charge' => $orderDetails['CodCharge'] ?? null,
                        'discount' => $orderDetails['Discount'] ?? null,
                        'invoice_amount' => $orderDetails['InvoiceAmount'],
                        'igst' => $igst,
                        'sgst' => $sgst,
                        'cgst' => $cgst,
                        'ewaybill_number' => $orderDetails['EwayBill'] ?? null,
                        'is_qc' => "y",

                        // MPS Details
                        // 'shipment_type' => $orderDetails['shipmentType'] ?? 'single',
                        // 'number_of_packets' => $orderDetails['numberOfPackets'] ?? 1,

                        'inserted' => date('Y-m-d H:i:s'),
                        'inserted_by' => $sellerId,
                    ];
                    $order = Order::create($orderData);
                    $productName = [];
                    $productSKU = [];
                    foreach($orderDetails['ProductDetails'] as $productDetails) {
                        $productData = [
                            'order_id' => $order->id,
                            'product_sku' => $productDetails['SKU'],
                            'product_name' => $productDetails['Name'],
                            'product_qty' => $productDetails['QTY'],
                        ];
                        $productName[] = $productDetails['Name'];
                        $productSKU[] = $productDetails['SKU'];
                        Product::create($productData);
                    }
                    Order::where('id', $order->id)->update(['product_name' => implode(',', $productName), 'product_sku' => implode(',', $productSKU)]);
                    if($orderData['is_qc'] == "y"){
                        $international = [
                            'order_id' => $order->id,
                            'qc_help_description' => $orderDetails['QCInformation']['Description'],
                            'qc_label' => implode(",",array_column($orderDetails['QCInformation']['Label'],"Label")),
                            'qc_value_to_check' => implode(",",array_column($orderDetails['QCInformation']['Label'],"ValueToCheck"))
                        ];
                        $path = [];
                        if(count($orderDetails['QCInformation']['Image']) > 0){
                            for($i=0;$i<count($orderDetails['QCInformation']['Image']);$i++){
                                $imageData = @file_get_contents($orderDetails['QCInformation']['Image'][$i]);
                                $localImagePath = date('YmdHis').$i;

                                if ($imageData !== false) {
                                    if (file_put_contents($localImagePath,$imageData ) !== false) {
                                        $bucketPath = "qc_image";
                                        BucketHelper::UploadFile($bucketPath,$localImagePath);
                                        @unlink($localImagePath);
                                        $path[] = $bucketPath."/".$localImagePath;
                                    }
                                }
                            }
                        }

                        $international['qc_image'] = implode(',',$path) ?? "";
                        InternationalOrders::create($international);
                    }
                    $res[] = [
                        'order_id' => $order->id,
                        'status' => true,
                        'message' => 'Order created successfully.'
                    ];
                } else {
                    $res[] = [
                        'order_id' => null,
                        'status' => false,
                        'message' => $validator->errors()
                    ];
                }
            }
        } else {
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/create-order-'.date('Y-m-d').'.text', [
            'title' => 'Create Order Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }

    // Update order api
    function updateOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        $orderStatus = [
            'shipped' => 'Shipped',
            'pickup_scheduled' => 'Pickup Scheduled',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'rto_initated' => 'RTO Initiated',
            'rto_delivered' => 'RTO Delivered',
            'delivered' => 'Delivered',
            'ndr' => 'NDR',
            'lost' => 'Lost',
            'damaged' => 'Damaged'
        ];

        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null',
            'OrderStatus' => [
                'required' => true,
                'not_null' => true,
                'in' => array_keys($orderStatus)
            ],
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/update-order-'.date('Y-m-d').'.text', [
            'title' => 'Update Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $order = Order::find($request->OrderID);
            if(!empty($order)) {
                if(strtolower($order->status) == $request->OrderStatus) {
                    $res['status'] = true;
                    $res['message'] = 'Order already '.$order->status;
                } else if(strtolower($request->OrderStatus) == 'pickup_scheduled') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->pickup_time = date('Y-m-d H:i:s');
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'PUC',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Pickup scheduled successfully.';
                } else if(strtolower($request->OrderStatus) == 'picked_up') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->pickup_done = 'y';
                    $order->pickup_schedule = 'y';
                    $order->pickup_time = date('Y-m-d H:i:s');
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'PUD',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order picked up successfully.';
                } else if(strtolower($request->OrderStatus) == 'in_transit') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'IT',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order in transit successfully.';
                } else if(strtolower($request->OrderStatus) == 'out_for_delivery') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'OFD',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order out for delivery successfully.';
                } else if(strtolower($order->status) == 'rto_initated') {
                    //Chnage order status to rto
                    $seller = Seller::find($order->seller_id);
                    $data = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->rto_charges,
                        'balance' => $seller->balance - $order->rto_charges,
                        'type' => 'd',
                        'redeem_type' => 'o',
                        'datetime' => date('Y-m-d H:i:s'),
                        'method' => 'wallet',
                        'description' => 'Order RTO Charge Deducted'
                    ];
                    Transactions::create($data);
                    Seller::where('id', $order->seller_id)->decrement('balance', $data['amount']);
                    Order::where('id', $order->id)->update(['status' => 'rto_initated', 'rto_status' => 'y']);
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'RTO',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'RTO initiated for order.';
                } else if(strtolower($request->OrderStatus) == 'delivered') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->delivered_date = date('Y-m-d H:i:s');
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'DVD',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order delivered successfully.';
                } else if(strtolower($request->OrderStatus) == 'ndr') {
                    // //Update order status
                    // $order->status = $request->OrderStatus;
                    // $order->ndr_status = 'y';
                    // $order->ndr_action = 'pending';
                    // $order->save();
                    // //Insert order tracking
                    // OrderTracking::create([
                    //     'awb_number' => $order->awb_number,
                    //     'status_code' => 'LOS',
                    //     'status' => strtoupper($request->OrderStatus),
                    //     'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                    //     'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                    //     'location' => 'NA',
                    //     'updated_date' => date('Y-m-d H:i:s')
                    // ]);
                    // $res['status'] = true;
                    // $res['message'] = 'Order ndr successfully.';

                    // if ($order->rto_status != 'y') {
                    //     if ($order->ndr_status == 'y' && $shipment_summary['StatusDate'] != $order->ndr_status_date) {
                    //         //make attempt here
                    //         $attempt = [
                    //             'seller_id' => $order->seller_id,
                    //             'order_id' => $order->id,
                    //             'raised_date' => date('Y-m-d'),
                    //             'raised_time' => date('H:i:s'),
                    //             'action_by' => 'XpressBees',
                    //             'reason' => $shipment_summary['Status'],
                    //             'action_status' => 'requested',
                    //             'remark' => 'requested',
                    //             'u_address_line1' => 'new address line 1',
                    //             'u_address_line2' => 'new address line 2',
                    //             'updated_mobile' => 'newmobile'
                    //         ];
                    //         Ndrattemps::create($attempt);
                    //     }
                    // }
                    // Order::where('awb_number', $order->awb)->update(['status' => 'out_for_delivery']);
                    $res['message'] = 'Pending';
                } else if(strtolower($request->OrderStatus) == 'lost') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'LOS',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order lost successfully.';
                } else if(strtolower($request->OrderStatus) == 'damaged') {
                    //Update order status
                    $order->status = $request->OrderStatus;
                    $order->save();
                    //Insert order tracking
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'ODD',
                        'status' => strtoupper($request->OrderStatus),
                        'status_description' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER',
                        'remarks' => 'ORDER '.strtoupper($request->OrderStatus).' BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $res['status'] = true;
                    $res['message'] = 'Order damaged successfully.';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Order not found.';
            }
        } else {
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/update-order-'.date('Y-m-d').'.text', [
            'title' => 'Update Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    // NDR order api
    function ndrOrder(Request $request) {
        //Set validation rules
        $validator = LaravelValidator::make($request->all(), [
            'ApiKey' => 'required|exists:sellers,api_key',
            'awbNumber' => 'required|exists:orders,awb_number',
            'action' => 'required|in:reattempt,rto',
            'reattemptDate' => 'required_if:action,reattempt',
            'comments' => 'nullable'
        ]);

        Logger::write('logs/api/ndr-order-'.date('Y-m-d').'.text', [
            'title' => 'NDR Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ]);
        }

        $order = Order::where('awb_number', $request->awbNumber)->first();
        $seller = Seller::find($order->seller_id);
        if(strtolower($request->action) == 'rto') {
            $order_type = strtolower($order->o_type);
            if(strtolower($order->status) == 'pending') {
                $order->status = 'cancelled';
                $order->save();
                if($order->status == 'cancelled') {
                    $res['status'] = true;
                    $res['message'] = 'Order cancelled successfully.';
                    $res['data'] = [];
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Unable to cancel order.';
                    $res['data'] = [];
                }
            } else if(in_array(strtolower($order->status), ['shipped', 'manifested','pickup_requested', 'pickup_scheduled'])) {
                MyUtility::PerformCancellation($seller,$order,'api');
                $res['status'] = true;
                $res['message'] = 'Order Cancelled Successfully.';
                $res['data'] = [];
            } else if(strtolower($order->status) == 'delivered') {
                $res['status'] = true;
                $res['message'] = 'Order is delivered, delivered order can not be cancelled';
                $res['data'] = [];
            } else if(strtolower($order->status) == 'cancelled') {
                $res['status'] = true;
                $res['message'] = 'Order already cancelled';
                $res['data'] = [];
            } else {
                MyUtility::PerformCancellation($seller,$order,'api');
                $res['status'] = true;
                $res['message'] = 'RTO initiated for order.';
                $res['data'] = [
                    "awbNumber" => $request->awbNumber,
                    "action" => $request->action,
                    "reattemptDate" => null,
                    "comments" => $request->comments
                ];
            }
        } else {
            switch ($order->courier_partner) {
                case 'shadow_fax':
                    if ($order->status == 'cancelled' || $order->ndr_status == 'y') {
                        $this->_ndrUpdateShadowFax($order->awb_number);
                    }
                    Order::where('id', $order->id)->update(['ndr_action' => 'requested']);
                    break;
            }
            $res['status'] = true;
            $res['message'] = "Reattempt request sent to courier partner";
            $res['data'] = [
                "awbNumber" => $request->awbNumber,
                "action" => $request->action,
                "reattemptDate" => $request->reattemptDate,
                "comments" => $request->comments
            ];
        }

        Logger::write('logs/api/ndr-order-'.date('Y-m-d').'.text', [
            'title' => 'NDR Order Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }

    function _ndrUpdateShadowFax($awb)
    {
        $data = array(
            "awb_numbers" => ["$awb"]
        );
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v1/clients/ndr_update/', $data);
        $response = $response->json();
    }

    //Track order api
    function trackOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'AWBNumber' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            // Cache api order tracking data for 20 hours
            //Cache::store('redis')->forget('api-tracking-'.$request->AWBNumber);
            $res = Cache::store('redis')->remember('api-tracking-'.$request->AWBNumber, (60*60)*20, function() use($request) {
                $order = Order::where('awb_number', $request->AWBNumber)->select('id', 'awb_number', 'status', 'rto_status', 'courier_partner')->first();
                if($order == null) {
                    $res['OrderId'] = null;
                    $res['AWBNumber'] = $request->AWBNumber;
                    $res['CourierPartner'] = null;
                    $res['CurrentStatus'] = 'NA';
                    $res['StatusCode'] = null;
                    $res['OrderHistory'] = [];
                    return $res;
                }
                $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
                if(count($orderTracking) > 0) {
                    $res['OrderId'] = $order->id;
                    $res['AWBNumber'] = $order->awb_number;
                    $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                    $res['CurrentStatus'] = $orderTracking[0]['status'];
                    $res['StatusCode'] = $order->status ?? "manifested";
                    if($res['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                        $res['StatusCode'] = 'rto_delivered';
                    }
                    if($res['StatusCode'] == 'in_transit' && $order->rto_status == 'y') {
                        $res['StatusCode'] = 'rto_in_transit';
                    }
                    foreach($orderTracking as $orderHistory) {
                        $res['OrderHistory'][] = [
                            'status_code' => $orderHistory['status_code'],
                            'status' => $orderHistory['status'],
                            'status_description' => $orderHistory['status_description'],
                            'remarks' => $orderHistory['remarks'],
                            'location' => $orderHistory['location'],
                            'updated_date' => date('Y-m-d H:i:s', strtotime($orderHistory['updated_date']))
                            // 'updated_date' => $orderHistory['created_at']
                        ];
                    }
                    $res['OrderHistory'][0]['StatusCode'] = $res['StatusCode'];
                } else {
                    $res['OrderId'] = $order->id;
                    $res['AWBNumber'] = $order->awb_number;
                    $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                    $res['CurrentStatus'] = 'Pending';
                    $res['StatusCode'] = $order->status ?? null;
                    $res['OrderHistory'] = [];
                }
                return $res;
            });
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    // Track multiple order api
    // function trackBulkOrder(Request $request) {
    //     $validator = new Validator();
    //     $sellerId = null;
    //     //Set validation rules
    //     $validator->rules([
    //         'ApiKey' => [
    //             'required' => true,
    //             'not_null' => true,
    //             'rules' => [
    //                 'valid_api_key' => function($apiKey) use(&$sellerId) {
    //                     $seller = Seller::where('api_key', $apiKey)->first();
    //                     if(!empty($seller)) {
    //                         $sellerId = $seller->id;
    //                         return true;
    //                     } else {
    //                         return false;
    //                     }
    //                 }
    //             ]
    //         ],
    //         'AWBNumber' => 'required|not_null|string'
    //     ]);

    //     //Set error messages
    //     $validator->messages([
    //         'ApiKey' => [
    //             'rules' => [
    //                 'valid_api_key' => 'Invalid api key.'
    //             ]
    //         ],
    //     ]);

    //     if($validator->validate($request->all())) {
    //         $awbNumbers = array_map('trim', explode(',', $request->AWBNumber));
    //         $orders = Order::whereIn('awb_number', $awbNumbers)->select('awb_number','status','rto_status','courier_partner')->get();
    //         if($orders->isEmpty()){
    //             $res['status'] = false;
    //             $res['message'] = 'Order history not found.';
    //             return response()->json($res);
    //         }
    //         foreach($orders as $order) {
    //             $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
    //             if(count($orderTracking) > 0) {
    //                 $tmpRes['AWBNumber'] = $orderTracking[0]['awb_number'];
    //                 $tmpRes['CourierPartner'] = $this->partnerNames[$order->courier_partner] ?? "Twinnship";
    //                 $tmpRes['CurrentStatus'] = $orderTracking[0]['status'];
    //                 $tmpRes['StatusCode'] = $order->status ?? 0;
    //                 if($tmpRes['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
    //                     $tmpRes['StatusCode'] = 'rto_delivered';
    //                 }
    //                 foreach($orderTracking as $orderHistory) {
    //                     $tmpRes['OrderHistory'][] = [
    //                         'status_code' => $orderHistory['status_code'],
    //                         'status' => $orderHistory['status'],
    //                         'status_description' => $orderHistory['status_description'],
    //                         'remarks' => $orderHistory['remarks'],
    //                         'location' => $orderHistory['location'],
    //                         'updated_date' => $orderHistory['updated_date']
    //                     ];
    //                 }
    //             } else {
    //                 $tmpRes['AWBNumber'] = $order->awb_number;
    //                 $tmpRes['CourierPartner'] = $this->partnerNames[$order->courier_partner] ?? "Twinnship";
    //                 $tmpRes['CurrentStatus'] = 'Order history not found.';
    //                 $tmpRes['StatusCode'] = $order->status ?? null;
    //                 $tmpRes['OrderHistory'] = [];
    //             }
    //             $res[] = $tmpRes;
    //         }
    //     } else {
    //         $res['status'] = false;
    //         $res['message'] = $validator->errors();
    //     }
    //     return response()->json($res);
    // }

    // Track multiple order api
    function trackBulkOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'AWBNumber' => 'required|not_null|string'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $awbNumbers = array_map('trim', explode(',', $request->AWBNumber));

            // Cache api order tracking data for 20 hours
            foreach($awbNumbers as $awbNumber) {
                $orderTracking = Cache::store('redis')->remember('api-tracking-'.$awbNumber, (60*60)*20, function() use($awbNumber) {
                    $order = Order::where('awb_number', $awbNumber)->select('id', 'awb_number', 'status', 'rto_status', 'courier_partner')->first();
                    if($order == null) {
                        $res['OrderId'] = null;
                        $res['AWBNumber'] = $awbNumber;
                        $res['CourierPartner'] = null;
                        $res['CurrentStatus'] = 'NA';
                        $res['StatusCode'] = null;
                        $res['OrderHistory'] = [];
                        return $res;
                    }
                    $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
                    if(count($orderTracking) > 0) {
                        $res['OrderId'] = $order->id;
                        $res['AWBNumber'] = $order->awb_number;
                        $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                        $res['CurrentStatus'] = $orderTracking[0]['status'];
                        $res['StatusCode'] = $order->status ?? 0;
                        if($res['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                            $res['StatusCode'] = 'rto_delivered';
                        }
                        foreach($orderTracking as $orderHistory) {
                            $res['OrderHistory'][] = [
                                'status_code' => $orderHistory['status_code'],
                                'status' => $orderHistory['status'],
                                'status_description' => $orderHistory['status_description'],
                                'remarks' => $orderHistory['remarks'],
                                'location' => $orderHistory['location'],
                                'updated_date' => date('Y-m-d H:i:s', strtotime($orderHistory['updated_date']))
                                // 'updated_date' => $orderHistory['created_at']
                            ];
                        }
                    } else {
                        $res['OrderId'] = $order->id;
                        $res['AWBNumber'] = $order->awb_number;
                        $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                        $res['CurrentStatus'] = 'Pending';
                        $res['StatusCode'] = $order->status ?? null;
                        $res['OrderHistory'] = [];
                    }
                    return $res;
                });
                $res[] = $orderTracking;
            }
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    function authenticateUser(Request $request){
        //file_put_contents('request.txt',$request->);
        $res['code'] = 200;
        $res['message'] = null;
        $sellerDetail = Seller::where('api_key',$request->token)->first();
        if(empty($sellerDetail)){
            $res['code']=400;
            $res['message']="Credentials not Found";
            return response()->json($res, $res['code']);
        }
        if($sellerDetail->api_key == $request->token && $request->username == $sellerDetail->email){
            Seller::where('id',$sellerDetail->id)->update(['easyecom_token' => $request->eeApiToken]);
            $res['message']="Successful";
            return response()->json($res, $res['code']);
        }
        else{
            $res['code']=400;
            $res['message']="This user is not allowed for the resource";
            return response()->json($res, $res['code']);
        }
    }

    // Track order by order id api
    function trackOrderById(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $res = Cache::store('redis')->remember('api-tracking-'.$request->OrderID, (60*60)*20, function() use($request) {
                $order = Order::where('id', $request->OrderID)->select('id', 'awb_number', 'status', 'rto_status', 'courier_partner')->first();
                if($order == null) {
                    $res['OrderId'] = $request->OrderID;
                    $res['AWBNumber'] = null;
                    $res['CourierPartner'] = null;
                    $res['CurrentStatus'] = 'NA';
                    $res['StatusCode'] = null;
                    $res['OrderHistory'] = [];
                    return $res;
                }
                $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
                if(count($orderTracking) > 0) {
                    $res['OrderId'] = $order->id;
                    $res['AWBNumber'] = $order->awb_number;
                    $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                    $res['CurrentStatus'] = $orderTracking[0]['status'];
                    $res['StatusCode'] = $order->status ?? 0;
                    if($res['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                        $res['StatusCode'] = 'rto_delivered';
                    }
                    foreach($orderTracking as $orderHistory) {
                        $res['OrderHistory'][] = [
                            'status_code' => $orderHistory['status_code'],
                            'status' => $orderHistory['status'],
                            'status_description' => $orderHistory['status_description'],
                            'remarks' => $orderHistory['remarks'],
                            'location' => $orderHistory['location'],
                            // 'updated_date' => $orderHistory['updated_date']
                            'updated_date' => $orderHistory['created_at']
                        ];
                    }
                } else {
                    $res['OrderId'] = $order->id;
                    $res['AWBNumber'] = $order->awb_number;
                    $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                    $res['CurrentStatus'] = 'Pending';
                    $res['StatusCode'] = $order->status ?? null;
                    $res['OrderHistory'] = [];
                }
                return $res;
            });
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    // Track multiple order by order id api
    function trackBulkOrderById(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null|string'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $orderIds = array_map('trim', explode(',', $request->OrderID));

            // Cache api order tracking data for 20 hours
            foreach($orderIds as $orderId) {
                $orderTracking = Cache::store('redis')->remember('api-tracking-'.$orderId, (60*60)*20, function() use($orderId) {
                    $order = Order::where('id', $orderId)->select('id', 'awb_number', 'status', 'rto_status', 'courier_partner')->first();
                    if($order == null) {
                        $res['OrderId'] = $orderId;
                        $res['AWBNumber'] = null;
                        $res['CourierPartner'] = null;
                        $res['CurrentStatus'] = 'NA';
                        $res['StatusCode'] = null;
                        $res['OrderHistory'] = [];
                        return $res;
                    }
                    $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
                    if(count($orderTracking) > 0) {
                        $res['OrderId'] = $order->id;
                        $res['AWBNumber'] = $order->awb_number;
                        $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                        $res['CurrentStatus'] = $orderTracking[0]['status'];
                        $res['StatusCode'] = $order->status ?? 0;
                        if($res['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                            $res['StatusCode'] = 'rto_delivered';
                        }
                        foreach($orderTracking as $orderHistory) {
                            $res['OrderHistory'][] = [
                                'status_code' => $orderHistory['status_code'],
                                'status' => $orderHistory['status'],
                                'status_description' => $orderHistory['status_description'],
                                'remarks' => $orderHistory['remarks'],
                                'location' => $orderHistory['location'],
                                'updated_date' => date('Y-m-d H:i:s', strtotime($orderHistory['updated_date']))
                                // 'updated_date' => $orderHistory['created_at']
                            ];
                        }
                    } else {
                        $res['OrderId'] = $order->id;
                        $res['AWBNumber'] = $order->awb_number;
                        $res['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
                        $res['CurrentStatus'] = 'Pending';
                        $res['StatusCode'] = $order->status ?? null;
                        $res['OrderHistory'] = [];
                    }
                    return $res;
                });
                $res[] = $orderTracking;
            }
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/track-order-'.date('Y-m-d').'.text', [
            'title' => 'Track Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    //Cancel order api
    function cancelOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        $seller  = null;
        $res['status'] = false;
        $res['message'] = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId,&$seller) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/cancel-order-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $order = Order::find($request->OrderID);
            if($seller->id != $order->seller_id){
                $res['status'] = false;
                $res['message'] = "Order doesn't belongs to this seller";
                return response()->json($res);
            }
            if(!empty($order)){
                $order_type = strtolower($order->o_type);
                if(strtolower($order->status) == 'pending') {
                    $order->status = 'cancelled';
                    $order->save();
                    if($order->status == 'cancelled') {
                        $res['status'] = true;
                        $res['message'] = 'Order cancelled successfully.';
                    } else {
                        $res['status'] = false;
                        $res['message'] = 'Unable to cancel order.';
                    }
                } else if(in_array(strtolower($order->status), ['shipped', 'manifested','pickup_requested', 'pickup_scheduled'])) {
                    MyUtility::PerformCancellation($seller,$order,'api');
                    $res['status'] = true;
                    $res['message'] = 'Order Cancelled Successfully.';
                } else if(strtolower($order->status) == 'delivered') {
                    $res['status'] = true;
                    $res['message'] = 'Order is delivered, delivered order can not be cancelled';
                } else if(strtolower($order->status) == 'cancelled') {
                    $res['status'] = true;
                    $res['message'] = 'Order already cancelled';
                } else {
                    MyUtility::PerformCancellation($seller,$order,'api');
                    $res['status'] = true;
                    $res['message'] = 'RTO initiated for order.';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Order not found.';
            }
            $this->updateOrderCache($order->awb_number);
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/cancel-order-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }
    function cancelOrderByAwb(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        $seller = null;
        $res['status'] = false;
        $res['message'] = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId,&$seller) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'AwbNumber' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/cancel-order-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $order = Order::where('awb_number',$request->AwbNumber)->first();
            if(!empty($order)){
                $order_type = strtolower($order->o_type);
                if(strtolower($order->status) == 'pending') {
                    $order->status = 'cancelled';
                    $order->save();
                    if($order->status == 'cancelled') {
                        $res['status'] = true;
                        $res['message'] = 'Order cancelled successfully.';
                    } else {
                        $res['status'] = false;
                        $res['message'] = 'Unable to cancel order.';
                    }
                } else if(in_array(strtolower($order->status), ['shipped', 'manifested','pickup_requested', 'pickup_scheduled'])) {
                    MyUtility::PerformCancellation($seller,$order,'api');
                    $res['status'] = true;
                    $res['message'] = 'Order Cancelled Successfully.';
                } else if(strtolower($order->status) == 'delivered') {
                    $res['status'] = true;
                    $res['message'] = 'Order is delivered, delivered order can not be cancelled';
                } else if(strtolower($order->status) == 'cancelled') {
                    $res['status'] = true;
                    $res['message'] = 'Order already cancelled';
                } else {
                    MyUtility::PerformCancellation($seller,$order,'api');
                    $res['status'] = true;
                    $res['message'] = 'RTO initiated for order.';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Order not found.';
            }
            $this->updateOrderCache($order->awb_number);
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/cancel-order-'.date('Y-m-d').'.text', [
            'title' => 'Cancel Order Response Payload',
            'data' => $res
        ]);
        return response()->json($res);
    }

    function _cancelMarutiOrder($order) {
        $maruti = new Maruti();
        $maruti->cancelOrder($order->awb_number);
    }

    //Ship order api
    function shipOrder(Request $request) {
        try {
            $validator = new Validator();
            $sellerData = null;
            $orderData = null;
            //Set validation rules
            $seller = Seller::where('api_key', $request->ApiKey)->first();
            if($seller->cheapest_enabled == 'y')
                return $this->shipOrderCheapest($request);
            $validator->rules([
                'ApiKey' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_api_key' => function($apiKey) use(&$sellerData) {
                            $seller = Seller::where('api_key', $apiKey)->first();
                            if(!empty($seller)) {
                                $sellerData = $seller;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'OrderID' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_order_id' => function($OrderID) use(&$orderData) {
                            $order = Order::where('id', $OrderID)->first();
                            if($order != null && $order->shipment_type != 'mps') {
                                $orderData = $order;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
            ]);

            //Set error messages
            $validator->messages([
                'ApiKey' => [
                    'rules' => [
                        'valid_api_key' => 'Invalid api key.'
                    ]
                ],
            ]);

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Request Payload',
                'data' => $request->all()
            ]);

            if($validator->validate($request->all())) {
                if($sellerData->id != $orderData->seller_id){
                    $res['status'] = false;
                    $res['message'] = "Order doesn't belongs to this seller";
                }else{
                    if($orderData->status == 'pending'){
                        $res = ShippingHelper::ShipOrder($orderData,$sellerData);
                    }else{
                        $res = [
                            'status' => true,
                            'message' => "Order Shipped Successful",
                            'total_charges' => $orderData->total_charges,
                            'shipping_charges' => $orderData->shipping_charges,
                            'cod_charges' => $orderData->cod_charges,
                            'data' => [
                                'awb_number' => $orderData->awb_number,
                                'courier' => ShippingHelper::PartnerNames[$orderData->courier_partner] ?? "Twinnship",
                                'courier_keyword' => $orderData->courier_partner
                            ]
                        ];
                    }
                    if($sellerData->auto_manifest_enabled == 'y')
                        $this->_manifestOrder($orderData->id, $sellerData->id);
                }
            } else {
                $res['status'] = false;
                $res['message'] = $validator->errors();
            }

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        } catch(\Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Order shipment failed.';

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);

            return response()->json($res);
        }
    }

    //Ship order api
    function shipOrderCourier(Request $request) {
        try {
            $validator = new Validator();
            $sellerData = null;
            $orderData = null;
            //Set validation rules
            $seller = Seller::where('api_key', $request->ApiKey)->first();
            $validator->rules([
                'ApiKey' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_api_key' => function($apiKey) use(&$sellerData) {
                            $seller = Seller::where('api_key', $apiKey)->first();
                            if(!empty($seller)) {
                                $sellerData = $seller;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'OrderID' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_order_id' => function($OrderID) use(&$orderData) {
                            $order = Order::where('id', $OrderID)->first();
                            if($order != null && $order->shipment_type != 'mps') {
                                $orderData = $order;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'CourierPartner' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_order_id' => function($CourierPartner) use(&$orderData) {
                            $partnerData = Partners::where('keyword', $CourierPartner)->first();
                            if($partnerData != null) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
            ]);

            //Set error messages
            $validator->messages([
                'ApiKey' => [
                    'rules' => [
                        'valid_api_key' => 'Invalid api key.'
                    ]
                ],
            ]);

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Courier Request Payload',
                'data' => $request->all()
            ]);

            if($validator->validate($request->all())) {
                if($sellerData->id != $orderData->seller_id){
                    $res['status'] = false;
                    $res['message'] = "Order doesn't belongs to this seller";
                }else{
                    if($orderData->status == 'pending'){
                        $res = ShippingHelper::ShipOrder($orderData,$sellerData,$request->CourierPartner);
                    }else{
                        $res = [
                            'status' => true,
                            'message' => "Order Shipped Successful",
                            'total_charges' => $orderData->total_charges,
                            'shipping_charges' => $orderData->shipping_charges,
                            'cod_charges' => $orderData->cod_charges,
                            'data' => [
                                'awb_number' => $orderData->awb_number,
                                'courier' => ShippingHelper::PartnerNames[$orderData->courier_partner] ?? "Twinnship",
                                'courier_keyword' => $orderData->courier_partner
                            ]
                        ];
                    }
                    if($sellerData->auto_manifest_enabled == 'y')
                        $this->_manifestOrder($orderData->id, $sellerData->id);
                }
            } else {
                $res['status'] = false;
                $res['message'] = $validator->errors();
            }

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Courier Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        } catch(\Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Order shipment failed.';

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Courier Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        }
    }

    // Get All Courier Partner List
    function getAllCourierPartner(Request $request){
        $res = [
            'status' => false,
            'message' => ''
        ];
        $apiKey = $request->ApiKey;
        $sellerData = Seller::where('api_key',$apiKey)->whereNotNull('api_key')->first();
        if(empty($sellerData)){
            $res['message'] = "API key is Invalid";
            return response($res)->withHeaders(['content-type' => 'application/json']);
        }
        else{
            return response()->json(Partners::where('status','y')->select('title','keyword')->orderBy('title')->get());
        }
    }

    //Ship order api
    function shipBulkOrder(Request $request) {
        try {
            $validator = new Validator();
            $sellerData = null;
            $orderData = null;
            $sellerData = Seller::where('api_key', $request->ApiKey)->first();
            //Set validation rules
            if(empty($sellerData))
            {
                return response()->json(['status' => false,'message' => 'Invalid API Key']);
            }
            if(empty($request->OrderID))
            {
                return response()->json(['status' => false,'message' => 'Please Enter Order ID']);
            }
            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Request Payload',
                'data' => $request->all()
            ]);
            $orderList = explode(",",$request->OrderID);
            foreach ($orderList as $o){
                $orderData = Order::where('id',$o)->where('seller_id',$sellerData->id)->first();
                $res[$o] = [];
                if(empty(!empty($orderData))){
                    $res[$o]['status'] = false;
                    $res[$o]['message'] = "Order doesn't belongs to this seller";
                }
                else{
                    $res[$o] = ShippingHelper::ShipOrder($orderData,$sellerData);
                    if($sellerData->auto_manifest_enabled == 'y')
                        $this->_manifestOrder($orderData->id, $sellerData->id);
                }
            }
            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        } catch(\Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Order shipment failed.';

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);

            return response()->json($res);
        }
    }

    // Ship Order Bulk Courier Partner
    //Ship order api
    function shipBulkOrderCourier(Request $request) {
        try {
            $validator = new Validator();
            $sellerData = null;
            $orderData = null;
            $sellerData = Seller::where('api_key', $request->ApiKey)->first();
            //Set validation rules
            if(empty($sellerData))
            {
                return response()->json(['status' => false,'message' => 'Invalid API Key']);
            }
            if(empty($request->OrderID))
            {
                return response()->json(['status' => false,'message' => 'Please Enter Order ID']);
            }
            if(empty($request->CourierPartner))
            {
                return response()->json(['status' => false,'message' => 'Please Enter Courier Partner']);
            }
            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Request Payload',
                'data' => $request->all()
            ]);
            $orderList = explode(",",$request->OrderID);
            foreach ($orderList as $o){
                $orderData = Order::where('id',$o)->where('seller_id',$sellerData->id)->first();
                $res[$o] = [];
                if(empty(!empty($orderData))){
                    $res[$o]['status'] = false;
                    $res[$o]['message'] = "Order doesn't belongs to this seller";
                }
                else{
                    $res[$o] = ShippingHelper::ShipOrder($orderData,$sellerData,$request->CourierPartner);
                    if($sellerData->auto_manifest_enabled == 'y')
                        $this->_manifestOrder($orderData->id, $sellerData->id);
                }
            }
            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);
            return response()->json($res);
        } catch(\Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Order shipment failed.';

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);

            return response()->json($res);
        }
    }

    //Ship order api
    function shipOrderCheapest(Request $request) {
        try {
            $validator = new Validator();
            $sellerData = null;
            $orderData = null;
            //Set validation rules
            $seller = Seller::where('api_key', $request->ApiKey)->first();
            $validator->rules([
                'ApiKey' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_api_key' => function($apiKey) use(&$sellerData) {
                            $seller = Seller::where('api_key', $apiKey)->first();
                            if(!empty($seller)) {
                                $sellerData = $seller;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'OrderID' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_order_id' => function($OrderID) use(&$orderData) {
                            $order = Order::where('id', $OrderID)->first();
                            if($order != null && $order->shipment_type != 'mps') {
                                $orderData = $order;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
            ]);

            //Set error messages
            $validator->messages([
                'ApiKey' => [
                    'rules' => [
                        'valid_api_key' => 'Invalid api key.'
                    ]
                ],
            ]);

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Request Payload',
                'data' => $request->all()
            ]);

            if($validator->validate($request->all())) {
                if($sellerData->id != $orderData->seller_id){
                    $res['status'] = false;
                    $res['message'] = "Order doesn't belongs to this seller";
                    return response()->json($res);
                }
                $serviceablePartners = ServiceablePincode::where('pincode',$orderData->s_pincode)->where('active','y')->distinct('courier_partner')->pluck('courier_partner')->toArray();
                $blockedCourierPartners = explode(',', $sellerData->blocked_courier_partners) ?? [];
                $rateCriteria = MyUtility::findMatchCriteria($orderData->p_pincode,$orderData->s_pincode,$sellerData);
                $weight = $orderData->weight > $orderData->vol_weight ? $orderData->weight : $orderData->vol_weight;
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
                $zoneL = strtolower($zone);
                //DB::enableQueryLog();
                $partners = Partners::whereNotIn('partners.id', $blockedCourierPartners)
                    ->where('partners.status', 'y')
                    ->whereIn('partners.keyword', [$sellerData->courier_priority_1,$sellerData->courier_priority_2,$sellerData->courier_priority_3,$sellerData->courier_priority_4])
                    ->whereIn('partners.serviceability_check', $serviceablePartners);
                $partners = $partners->leftJoin('rates','rates.partner_id','partners.id');
                $partners = $partners->select("partners.*","rates.$rateCriteria as original","rates.cod_maintenance","rates.cod_charge",
                    DB::raw("if(CEILING($weight-partners.weight_initial) > 0,CEILING($weight-partners.weight_initial),0) as extra_charge"),
                    DB::raw("CEILING((select extra_charge) / partners.extra_limit) as extra_mul"),"rates.extra_charge_{$zoneL} as mul_value",
                    DB::raw("((select original) + ((select extra_mul) * (select mul_value))) as final_rate"));
                $partners = $partners->where('rates.plan_id',$sellerData->plan_id);
                $partners = $partners->where('rates.seller_id',$sellerData->id);
                $partners = $partners->orderBy('final_rate');
                $partners = $partners->get();
                //dd(DB::getQueryLog());
                // Check courier partner is blocked or not
                foreach ($partners as $p){
                    $res = ShippingHelper::ShipOrder($orderData,$sellerData,$p->keyword);
                    if($res['status'])
                        break;
                }
                if($sellerData->auto_manifest_enabled == 'y')
                    $this->_manifestOrder($orderData->id, $sellerData->id);
            } else {
                $res['status'] = false;
                $res['message'] = $validator->errors();
            }
            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);

            return response()->json($res);
        } catch(\Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Order shipment failed.';

            Logger::write('logs/api/ship-order-'.date('Y-m-d').'.text', [
                'title' => 'Ship Order Response Payload',
                'data' => $res
            ]);

            return response()->json($res);
        }
    }

    //Generate manifest
    function generateManifest(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);
        $res['status'] = false;
        $res['message'] = null;

        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Generate Manifest Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $orderStatus = Order::find($request->OrderID);
            if(empty($orderStatus)){
                $res['status'] = false;
                $res['message'] = "Order not Found";
            }
            else{
                if($orderStatus->status == 'pending' || $orderStatus->status == 'cancelled' || $orderStatus->status == 'delivered')
                {
                    $res['status'] = false;
                    $res['message'] = "Order can not be manifested";
                }
                else{
                    return $this->_manifestOrder($request->OrderID, $sellerId);
                }
            }
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Gemerate Manifest Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    // Generate bulk manifest
    function generateBulkManifest(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            $sellerId = $seller->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'OrderID' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);
        $res['status'] = false;
        $res['message'] = null;


        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Gemerate Manifest Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
            if(empty($wareHouse)) {
                $res['status'] = false;
                $res['message'] = 'Please Select Deafult Warehouse First.';
            } else {
                $orderIds = array_map('trim', explode(',', $request->OrderID));
                $couriers = Order::select('id', 'courier_partner')->where('seller_id', $sellerId)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
                if($couriers->isNotEmpty()) {
                    foreach($couriers as $courier) {
                        $rand = rand(1000, 9999);
                        $data = [
                            'seller_id' => $sellerId,
                            'courier' => $courier->courier_partner,
                            'status' => 'manifest_generated',
                            'warehouse_name' => $wareHouse->warehouse_name,
                            'warehouse_contact' => $wareHouse->contact_number,
                            'warehouse_gst_no' => $wareHouse->gst_number,
                            'warehouse_address' => $wareHouse->address_line1 . "," . $wareHouse->address_line2 . "," . $wareHouse->city . "," . $wareHouse->state . " - " . $wareHouse->pincode,
                            'p_ref_no' =>  "TST$rand",
                            'type' =>  "api",
                            'created' => date('Y-m-d'),
                            'created_time' => date('H:i:s')
                        ];
                        if(count($manifest = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type','api')->where('seller_id', $sellerId)->get()) > 0) {
                            $manifestId = $manifest[0]->id;
                        } else {
                            $manifestId = Manifest::create($data)->id;
                        }
                        $orders = Order::where('courier_partner', $courier->courier_partner)->where('seller_id', $sellerId)->where('manifest_status', 'n')->where('id', $courier->id)->get();
                        if(count($manifest) > 0) {
                            Manifest::where('id', $manifestId)->increment('number_of_order', count($orders));
                        } else {
                            Manifest::where('id', $manifestId)->update(array('number_of_order' => count($orders)));
                        }
                        foreach($orders as $order) {
                            $info = array(
                                'manifest_id' => $manifestId,
                                'order_id' => $order->id
                            );
                            ManifestOrder::create($info);
                            Order::where('id', $order->id)->update(['status' => 'manifested','manifest_status' => 'y']);
                            OrderTracking::create([
                                'awb_number' => $order->awb_number,
                                'status_code' => '00',
                                'status' => 'Pending',
                                'status_description' => 'pending request',
                                'remark' => 'generated manifest here',
                                'location' => 'NA',
                                'updated_date' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    $res['status'] = true;
                    $res['message'] = 'Manifest Generated successfully.';
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Courier Partner not atteched with order.';
                }
            }
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Gemerate Manifest Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    // Generate Manifest From Order IDs List
    function generateBulkManifestFromOrderIdsList(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'OrderID' => 'required|not_null'
        ]);

        //Set error messages
        $validator->messages([
            'OrderID' => [
                'rules' => [
                    'order_id' => 'Please Pass Order IDs List'
                ]
            ],
        ]);
        $res['status'] = false;
        $res['message'] = null;


        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Generate Manifest Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
            if(empty($wareHouse)) {
                $res['status'] = false;
                $res['message'] = 'Please Select Deafult Warehouse First.';
            } else {
                $orderIds = array_map('trim', explode(',', $request->OrderID));
                $couriers = Order::select('id', 'courier_partner')->where('seller_id', $sellerId)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
                if($couriers->isNotEmpty()) {
                    foreach($couriers as $courier) {
                        $rand = rand(1000, 9999);
                        $data = [
                            'seller_id' => $sellerId,
                            'courier' => $courier->courier_partner,
                            'status' => 'manifest_generated',
                            'warehouse_name' => $wareHouse->warehouse_name,
                            'warehouse_contact' => $wareHouse->contact_number,
                            'warehouse_gst_no' => $wareHouse->gst_number,
                            'warehouse_address' => $wareHouse->address_line1 . "," . $wareHouse->address_line2 . "," . $wareHouse->city . "," . $wareHouse->state . " - " . $wareHouse->pincode,
                            'p_ref_no' =>  "TST$rand",
                            'type' =>  "api",
                            'created' => date('Y-m-d'),
                            'created_time' => date('H:i:s')
                        ];
                        if(count($manifest = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type','api')->where('seller_id', $sellerId)->get()) > 0) {
                            $manifestId = $manifest[0]->id;
                        } else {
                            $manifestId = Manifest::create($data)->id;
                        }
                        $orders = Order::where('courier_partner', $courier->courier_partner)->where('seller_id', $sellerId)->where('manifest_status', 'n')->where('id', $courier->id)->get();
                        if(count($manifest) > 0) {
                            Manifest::where('id', $manifestId)->increment('number_of_order', count($orders));
                        } else {
                            Manifest::where('id', $manifestId)->update(array('number_of_order' => count($orders)));
                        }
                        foreach($orders as $order) {
                            $info = array(
                                'manifest_id' => $manifestId,
                                'order_id' => $order->id
                            );
                            ManifestOrder::create($info);
                            Order::where('id', $order->id)->update(['status' => 'manifested','manifest_status' => 'y']);
                            OrderTracking::create([
                                'awb_number' => $order->awb_number,
                                'status_code' => '00',
                                'status' => 'Pending',
                                'status_description' => 'pending request',
                                'remark' => 'generated manifest here',
                                'location' => 'NA',
                                'updated_date' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    $res['status'] = true;
                    $res['message'] = 'Manifest Generated successfully.';
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Courier Partner not atteched with order.';
                }
            }
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/generate-manifest-'.date('Y-m-d').'.text', [
            'title' => 'Gemerate Manifest Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    //Check Servicable Pincode
    function serviceablePincode(Request $request) {
        $validator = new Validator();
        //Set validation rules
        $validator->rules([
            'ApiKey' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) {
                        $seller = Seller::where('api_key', $apiKey)->first();
                        if(!empty($seller)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ]
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/api/check-serviceable-'.date('Y-m-d').'.text', [
            'title' => 'Check Serviceable Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $res['status'] = true;
            $res['message'] = 'Serviceable Pincodes.';
            $res['data'] = ServiceablePincode::select(DB::raw('distinct pincode'))->get()->pluck('pincode')->toArray();
            // $res['data'] = ServiceablePincode::distinct('pincode')->select('pincode')->get();
        } else {
            $res['status'] = false;
            $res['message'] = $validator->errors();
        }

        Logger::write('logs/api/check-serviceable-'.date('Y-m-d').'.text', [
            'title' => 'Check Serviceable Response Payload',
            'data' => $res
        ]);

        return response()->json($res);
    }

    //Create order EasyEcom
    function createEasyEcomOrder(Request $request) {
        try {
            $validator = new Validator();
            $sellerId = null;
            $sellerData = null;
            //Set validation rules
            $validator->rules([
                'credentials.token' => [
                    'required' => true,
                    'not_null' => true,
                    'rules' => [
                        'valid_api_key' => function($apiKey) use(&$sellerId, &$sellerData) {
                            $sellerData = Seller::where('api_key', $apiKey)->first();
                            if(!empty($sellerData)) {
                                $sellerId = $sellerData->id;
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ]
                ],
                'order_data' => 'required|not_null|array',
                'order_data.invoice_id,order_data.order_id,order_data.reference_code,order_data.company_name,order_data.warehouse_id,order_data.pickup_address,order_data.pickup_city,order_data.pickup_state,order_data.pickup_pin_code,order_data.pickup_country,order_data.marketplace,order_data.marketplace_id,order_data.order_date,order_data.Package Weight,order_data.Package Height,order_data.Package Length,order_data.Package Width,order_data.order_status,order_data.payment_mode,order_data.payment_mode_id,order_data.customer_name,order_data.contact_num,order_data.address_line_1,order_data.city,order_data.pin_code,order_data.state,order_data.country' => 'required|not_null',
                'order_data.order_quantity' => 'required',
                'order_data.total_amount' => 'required',
            ]);

            //Set error messages
            $validator->messages([
                'ApiKey' => [
                    'rules' => [
                        'valid_api_key' => 'Invalid api key.'
                    ]
                ],
            ]);

            Logger::write('logs/oms/easyecom/easyecom-'.date('Y-m-d').'.text', [
                'title' => 'Easyecom Request Payload',
                'data' => $request->all()
            ]);

            if($validator->validate($request->all())) {
                if(empty($request->order_data['Package Weight']) && (empty($request->order_data['Package Height']) || empty($request->order_data['Package Length']) || empty($request->order_data['Package Width']))) {
                    $res = [
                        "code" => 400,
                        "message" => "Order Dimensions or Weight not Found",
                        "tracking_number" => null,
                        "courier_name" => null,
                        "label_url" => null
                    ];
                    return response()->json($res, $res['code']);
                }
                $channelId = Order::where('seller_id', $sellerId)
                    ->where('channel','easyecom')
                    ->where('channel_id', $request->order_data['invoice_id'])
                    ->first();
                if(!empty($channelId)) {
                    $res = [
                        "code" => 200,
                        "message" => "Order Shipped Successfully",
                        "tracking_number" => $channelId->awb_number ?? "",
                        "courier_name" => ShippingHelper::GetPartnerName($channelId->courier_partner),
                        "label_url" => null
                    ];
                    return response()->json($res, $res['code']);
                } else {
                    $channelId = $request->order_data['invoice_id'];
                }
                $orderNumber = $request->order_data['reference_code'];
                // $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();

                // Create warehouse if not exists at Twinnship
                $wareHouse = Warehouses::firstOrCreate([
                    'seller_id' => $sellerId,
                    'easyecom_warehouse_id' => $request->order_data['assigned_warehouse_id'],
                ], [
                    'warehouse_name' => $request->order_data['company_name'] . ' - ' . $request->order_data['assigned_warehouse_id'],
                    'warehouse_code' => $request->order_data['company_name'] . ' - ' . $request->order_data['assigned_warehouse_id'] . "_" . $sellerData->code,
                    'contact_name' => $request->order_data['company_name'],
                    'contact_number' => $request->order_data['warehouse_contact'] ?? $sellerData->mobile,
                    'address_line1' => $request->order_data['pickup_address'],
                    'city' => $request->order_data['pickup_city'],
                    'state' => $request->order_data['pickup_state'],
                    'country' => $request->order_data['pickup_country'],
                    'pincode' => $request->order_data['pickup_pin_code'],
                    'gst_number' => $request->order_data['assigned_company_gst'] ?? null,
                    'support_phone' => $request->order_data['warehouse_contact'] ?? $sellerData->mobile,
                ]);
                if($wareHouse->wasRecentlyCreated)
                  @$this->createWarehouseAtCourier($wareHouse);
                if($wareHouse == null) {
                    $res = [
                        "code" => 400,
                        "message" => "Please Select Default Warehouse First.",
                        "tracking_number" => null,
                        "courier_name" => null,
                        "label_url" => null
                    ];
                    return response()->json($res, $res['code']);
                }

                $igst = 0;
                $cgst = 0;
                $sgst = 0;
                if(!empty($request->order_data['total_amount'])) {
                    if(strtolower($request->order_data['state']) == strtolower($wareHouse->state)) {
                        $percent = $request->order_data['total_amount'] - ($request->order_data['total_amount']/((18/100)+1));
                        $cgst = $percent/2;
                        $sgst = $percent/2;
                    } else {
                        $percent = $request->order_data['total_amount'] - ($request->order_data['total_amount']/((18/100)+1));
                        $igst = $percent;
                    }
                }

                $orderData = [
                    'seller_id' => $sellerId,
                    'warehouse_id' => $wareHouse->id,
                    'order_number' => $orderNumber,
                    'customer_order_number' => $orderNumber,
                    'channel' => 'easyecom',
                    'channel_id' => $channelId,
                    'reference_code' => $request->order_data['reference_code'],
                    'marketplace' => $request->order_data['marketplace'],
                    'marketplace_id' => $request->order_data['marketplace_id'],
                    'order_type' => (strtolower($request->order_data['payment_mode']) == 'cod' ? 'cod' : 'prepaid'),
                    'o_type' => 'forward',

                    //Billing Address
                    'b_customer_name' => $request->order_data['customer_name'],
                    'b_address_line1' => $request->order_data['billing_address_1'],
                    'b_address_line2' => $request->order_data['billing_address_1'],
                    'b_city' => $request->order_data['billing_city'],
                    'b_state' => $request->order_data['billing_state'],
                    'b_country' => $request->order_data['billing_country'],
                    'b_pincode' => $request->order_data['billing_pin_code'],
                    'b_contact_code' => '+91',
                    'b_contact' => $request->order_data['billing_mobile'],

                    //Pickup address
                    'p_warehouse_name' => $wareHouse['warehouse_name'],
                    'p_customer_name' => $wareHouse['contact_name'],
                    'p_address_line1' => $wareHouse['address_line1'],
                    'p_address_line2' => $wareHouse['address_line2'],
                    'p_city' => $wareHouse['city'],
                    'p_state' => $wareHouse['state'],
                    'p_country' => $wareHouse['country'],
                    'p_pincode' => $wareHouse['pincode'],
                    'p_contact_code' => $wareHouse['code'],
                    'p_contact' => $wareHouse['support_phone'],
                    'pickup_address' => $wareHouse['address_line1'] . ',' . $wareHouse['address_line2'] . ',' . $wareHouse['city'] . ',' . $wareHouse['state'] . ',' . $wareHouse['pincode'],

                    //Shipping address
                    's_customer_name' => $request->order_data['customer_name'],
                    's_address_line1' => $request->order_data['address_line_1'],
                    's_address_line2' => $request->order_data['address_line_2'],
                    's_city' => $request->order_data['city'],
                    's_state' => $request->order_data['state'],
                    's_country' => $request->order_data['country'],
                    's_pincode' => $request->order_data['pin_code'],
                    's_contact_code' => '+91',
                    's_contact' => $request->order_data['contact_num'],

                    'delivery_address' => $request->order_data['address_line_1'] . ',' . $request->order_data['address_line_2'] . ',' . $request->order_data['city'] . ',' . $request->order_data['state'] . ',' . $request->order_data['pin_code'],

                    'weight' => $request->order_data['Package Weight'],
                    'length' => $request->order_data['Package Length'],
                    'breadth' => $request->order_data['Package Width'],
                    'height' => $request->order_data['Package Height'],
                    'vol_weight' => ($request->order_data['Package Height'] * $request->order_data['Package Length'] * $request->order_data['Package Width']) / 5,
                    's_charge' => 0,
                    'c_charge' => 0,
                    'discount' => 0,
                    'status' => 'pending',
                    'igst' => $igst,
                    'sgst' => $sgst,
                    'cgst' => $cgst,
                    'invoice_amount' => $request->order_data['total_amount'],
                    'inserted' => date('Y-m-d H:i:s'),
                    'inserted_by' => $sellerId,
                ];
                //dd($orderData);
                $order = Order::create($orderData);
                $productName = [];
                $productSKU = [];
                $itemCount = 0;
                foreach($request->order_data['order_items'] as $productDetails) {
                    $productData = [
                        'order_id' => $order->id,
                        'product_sku' => $productDetails['sku'],
                        'product_name' => $productDetails['productName'],
                        'product_qty' => $productDetails['item_quantity'],
                    ];
                    $itemCount += $productDetails['item_quantity'] ?? 1;
                    $productName[] = $productDetails['productName'];
                    $productSKU[] = $productDetails['sku'];
                    Product::create($productData);
                }
                $order->product_name = implode(',', $productName);
                $order->product_sku = implode(',', $productSKU);
                $order->product_qty = $itemCount;
                $order->save();
                $shippedOrder = ShippingHelper::ShipOrder($order,$sellerData);
                if(isset($shippedOrder['status']) && $shippedOrder['status'] == true) {
                    @$this->_manifestOrder($order->id, $sellerId);
                    $res = [
                        "code" => 200,
                        "message" => "Successful",
                        "tracking_number" => $shippedOrder['data']['awb_number'],
                        "courier_name" => ShippingHelper::GetPartnerName($shippedOrder['data']['courier_keyword']),
                        "label_url" => null
                    ];
                } else {
                    // Delete order data
                    Product::where('order_id', $order->id)->delete();
                    $order->delete();
                    $res = [
                        "code" => 400,
                        "message" => $shippedOrder['message'],
                        "tracking_number" => null,
                        "courier_name" => null,
                        "label_url" => null
                    ];
                }
            } else {
                $res = [
                    "code" => 400,
                    "message" => $validator->errors(),
                    "tracking_number" => null,
                    "courier_name" => null,
                    "label_url" => null
                ];
            }

            Logger::write('logs/oms/easyecom/easyecom-'.date('Y-m-d').'.text', [
                'title' => 'Easyecom Response Payload',
                'data' => $res
            ]);

            return response()->json($res, $res['code']);
        } catch(Exception $e) {
            // Delete order data
            if(!empty($order) && $order->status == 'pending') {
                Product::where('order_id', $order->id)->delete();
                $order->delete();
            }
            $res = [
                "code" => 500,
                "message" => $e->getMessage(),
                "tracking_number" => null,
                "courier_name" => null,
                "label_url" => null
            ];
            Logger::write('logs/oms/easyecom/easyecom-'.date('Y-m-d').'.text', [
                'title' => 'Easyecom Response Payload',
                'data' => $res
            ]);
            return response()->json($res, $res['code']);
        }
    }


    // for adding warehouse function
    function createWarehouseAtCourier(Warehouses $wareHouse,$flag = 0)
    {
        try {
            //add Warehouse for Delhivery
            $payload = [
                "phone" => $wareHouse->contact_number,
                "city" => $wareHouse->city,
                "name" => $wareHouse->warehouse_code,
                "pin" => $wareHouse->pincode,
                "address" => $wareHouse->address_line1,
                "country" => $wareHouse->country,
                "email" => $wareHouse->support_email,
                "registered_name" => $wareHouse->warehouse_code,
                "return_address" => $wareHouse->address_line1,
                "return_pin" => $wareHouse->pincode,
                "return_city" => $wareHouse->city,
                "return_state" => $wareHouse->state,
                "return_country" => $wareHouse->country
            ];
            // dd($payload);
            if($flag == 0){
                $response = Http::withHeaders([
                    'Authorization' => 'Token 894217b910b9e60d3d12cab20a3c5e206b739c8b',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
                $response = Http::withHeaders([
                    'Authorization' => 'Token 18765103684ead7f379ec3af5e585d16241fdb94',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);
                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
                $response = Http::withHeaders([
                    'Authorization' => 'Token 3141800ec51f036f997cd015fdb00e8aeb38e126',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
            }
            if($flag == 1){
                $response = Http::withHeaders([
                    'Authorization' => 'Token be6d002daeb8bf53fc5e6dd25bf33a4d03a45891',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
            }
            if($flag == 2){
                $response = Http::withHeaders([
                    'Authorization' => 'Token 9c6bb4a5969f73ce2bfe937a10140ce843f8096f',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
            }

            if($flag == 3){
                $response = Http::withHeaders([
                    'Authorization' => 'Token 3c3f230a7419777f2a1f6b57933785a7e93ff43d',
                    'Content-Type' => 'application/json'
                ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

                Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                    'title' => 'Warehouse creation Response',
                    'data' => $response->body()
                ]);
            }

            /*
            $payload_udaan = [
                "orgUnitId" => "",
                "addressLine1" => $wareHouse->address_line1,
                "addressLine2" => "",
                "addressLine3" => "",
                "city" => $wareHouse->city,
                "state" => $wareHouse->state,
                "pincode" => $wareHouse->pincode,
                "unitName" => $wareHouse->warehouse_code,
                "representativeName" => $wareHouse->contact_name,
                "mobileNumber" => $wareHouse->contact_number,
                "gstin" => ""
            ];
            $response_uddan = Http::withHeaders([
                'Authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
                'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
                'Content-Type' => 'application/json'
            ])->post('https://udaan.com/api/udaan-express/integration/v1/address/ORGZPKZ992460QL8GPWW4JDZGLC67', $payload_udaan);

            $warehouse_udaan = $response_uddan->json();
            Logger::write('logs/partners/udaan/udaan'.date('Y-m-d').'.text', [
                'title' => 'Warehouse creation Response',
                'data' => $warehouse_udaan
            ]);
            if(!empty($warehouse_udaan)){
                // dd($warehouse_udaan);
                if ($warehouse_udaan['responseCode'] == 'UE_1001') {
                    $org_unit_id = $warehouse_udaan['response']['orgUnitId'];
                    Warehouses::where('id', $wareHouse->id)->update(['org_unit_id' => $org_unit_id]);
                }
            }
            $gati = new Gati();
            $wareHouse = Warehouses::find($wareHouse->id);
            $gati->createWarehouse($wareHouse); */
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //Cancel order EasyEcom
    function cancelEasyEcomOrder(Request $request) {
        $validator = new Validator();
        $sellerId = null;
        //Set validation rules
        $validator->rules([
            'credentials.token' => [
                'required' => true,
                'not_null' => true,
                'rules' => [
                    'valid_api_key' => function($apiKey) use(&$sellerId, &$sellerData) {
                        $sellerData = Seller::where('api_key', $apiKey)->first();
                        if(!empty($sellerData)) {
                            $sellerId = $sellerData->id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ],
            'awb_details' => 'required|not_null|array',
            'awb_details.awb,awb_details.courier' => 'required|not_null',
        ]);

        //Set error messages
        $validator->messages([
            'ApiKey' => [
                'rules' => [
                    'valid_api_key' => 'Invalid api key.'
                ]
            ],
        ]);

        Logger::write('logs/oms/easyecom/easyecom-'.date('Y-m-d').'.text', [
            'title' => 'Easyecom Cancel Order Request Payload',
            'data' => $request->all()
        ]);

        if($validator->validate($request->all())) {
            $order = Order::where('awb_number', $request->awb_details['awb'])->first();
            if($sellerData->id != $order->seller_id){
                $res['status'] = false;
                $res['message'] = "Order doesn't belongs to this seller";
                return response()->json($res);
            }
            if(!empty($order)){
                $order_type = strtolower($order->o_type);
                if(strtolower($order->status) == 'pending') {
                    $order->status = 'cancelled';
                    $order->save();
                    $res = [
                        "code" => 200,
                        "message" => "Order cancelled successfully",
                    ];
                } else if(in_array(strtolower($order->status), ['shipped', 'manifested','pickup_requested','pickup_scheduled'])) {
                    MyUtility::PerformCancellation($sellerData,$order,'api');
                    $res = [
                        "code" => 200,
                        "message" => "Order Cancelled Successfully",
                    ];
                } else if(strtolower($order->status) == 'delivered') {
                    $res = [
                        "code" => 400,
                        "message" => "Order is delivered, delivered order can not be cancelled",
                    ];
                    return response()->json($res, $res['code']);
                } else if(strtolower($order->status) == 'cancelled') {
                    $res = [
                        "code" => 400,
                        "message" => "Order already cancelled",
                    ];
                    return response()->json($res, $res['code']);
                } else {
                    MyUtility::PerformCancellation($sellerData,$order,'api');
                    $res = [
                        "code" => 200,
                        "message" => "RTO initiated for order.",
                    ];
                    return response()->json($res, $res['code']);
                }
            } else {
                $res = [
                    "code" => 400,
                    "message" => "Order not found",
                ];
            }
        } else {
            $res = [
                "code" => 400,
                "message" => $validator->errors(),
            ];
        }

        Logger::write('logs/oms/easyecom/easyecom-'.date('Y-m-d').'.text', [
            'title' => 'Easyecom Cancel Order Response Payload',
            'data' => $res
        ]);

        return response()->json($res, $res['code']);
    }

    //Manifest order
    function _manifestOrder($orderId, $sellerId) {
        $courier = Order::select('courier_partner')->where('seller_id', $sellerId)->whereNotIn('status',['pending','cancelled','delivered'])->where('manifest_status', 'n')->where('id', $orderId)->first();
        if(!empty($courier)){
            $wareHouse = Warehouses::where('seller_id', $sellerId)->orderBy('default', 'desc')->first();
            if(empty($wareHouse)) {
                $res['status'] = false;
                $res['message'] = 'Please Select Default Warehouse First.';
            } else {
                $rand = rand(1000, 9999);
                $data = [
                    'seller_id' => $sellerId,
                    'courier' => $courier->courier_partner,
                    'status' => 'manifest_generated',
                    'warehouse_name' => $wareHouse->warehouse_name,
                    'warehouse_contact' => $wareHouse->contact_number,
                    'warehouse_gst_no' => $wareHouse->gst_number,
                    'warehouse_address' => $wareHouse->address_line1 . "," . $wareHouse->address_line2 . "," . $wareHouse->city . "," . $wareHouse->state . " - " . $wareHouse->pincode,
                    'p_ref_no' =>  "TST$rand",
                    'type' =>  "api",
                    'created' => date('Y-m-d'),
                    'created_time' => date('H:i:s')
                ];
                if(count($manifest = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type','api')->where('seller_id', $sellerId)->get()) > 0) {
                    $manifestId = $manifest[0]->id;
                } else {
                    $manifestId = Manifest::create($data)->id;
                }
                $orders = Order::where('courier_partner', $courier->courier_partner)->where('seller_id', $sellerId)->where('manifest_status', 'n')->where('id', $orderId)->get();
                if(count($manifest) > 0) {
                    Manifest::where('id', $manifestId)->increment('number_of_order', count($orders));
                } else {
                    Manifest::where('id', $manifestId)->update(array('number_of_order' => count($orders)));
                }
                foreach($orders as $order) {
                    $info = array(
                        'manifest_id' => $manifestId,
                        'order_id' => $order->id
                    );
                    ManifestOrder::create($info);
                    Order::where('id', $order->id)->update(['status' => 'manifested','manifest_status' => 'y']);
                    $orderTracking = [
                        'awb_number' => $order->awb_number,
                        'status_code' => '00',
                        'status' => 'Pending',
                        'status_description' => 'pending request',
                        'remark' => 'generated manifest here',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($orderTracking);
                }
                $res['status'] = true;
                $res['message'] = 'Manifest Generated successfully.';
            }
        }else{
            $res['status'] = false;
            $res['message'] = 'Courier Partner not atteched with this order.';
        }
        return $res;
    }

    function _checkAndReturn($partners,$partner){
        $match = false;
        foreach($partners as $p){
            if($p->keyword == $partner)
                $match = true;
        }
        return $match;
    }

    function _matchRules($orderDetail,$sellerData)
    {
        $weight = $orderDetail->weight;
        if($orderDetail->vol_weight > $weight){
            $weight = $orderDetail->vol_weight;
        }
        $wareHouse = Warehouses::where('seller_id', $sellerData->id)->where('default', 'y')->get();
        if (count($wareHouse) == 0) {
            echo json_encode(array('error' => 'default warehouse not selected'));
            exit;
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
                            if ($weight <= $r->match_value)
                                $match++;
                        } else if ($r->match_type == 'greater_than') {
                            if ($weight > $r->match_value)
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

    function createWarehouse(Request $request){
        $res['code'] = 200;
        $res['message'] = null;
        if($request->warehouse_name=="" || $request->contact_person == "" || $request->contact_number == "" || $request->gst == "" || $request->wareHouseAddress == "" || $request->pincode == "" || $request->city == "" || $request->state == "" || $request->country == "" || $request->support_email == "" ||  $request->support_phone == ""){
            $res['code']=400;
            $res['message']="Please send all the required details";
            return response()->json($res, $res['code']);
        }
        $sellerDetail = Seller::where('api_key',$request->token)->first();
        if(empty($sellerDetail)){
            $res['code']=400;
            $res['message']="Credentials not Found";
            return response()->json($res, $res['code']);
        }
        if($sellerDetail->api_key == $request->token){
            $detail = $this->_createWareHouses($request,$sellerDetail);
            $res['message']="Successfully added warehouse";
            $res['warehouseCode']=$detail->warehouse_code;
            return response()->json($res, $res['code']);
        }
        else{
            $res['code']=400;
            $res['message']="This user is not allowed for the resource";
            return response()->json($res, $res['code']);
        }
    }

    function _createWareHouses($request,$sellerDetail){
        $code= $request->warehouse_name."_".$sellerDetail->code;
        $warehouse = Warehouses::where('seller_id',$sellerDetail->id)->where('warehouse_code',$code)->first();
        if(!empty($warehouse)){
            return  $warehouse;
        }
        $data = array(
            'seller_id' => $sellerDetail->id,
            'warehouse_name' => $request->warehouse_name,
            'contact_name' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'address_line1' => $request->wareHouseAddress,
            'city' => $request->city,
            'code' => "91",
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst,
            'support_email' => $request->support_email,
            'support_phone' => $request->support_phone,
            'warehouse_code' => $code,
            'created_at' => date('Y-m-d H:i:s')
        );
        $id = Warehouses::create($data)->id;

        //add Warehouse for Delhivery
        $payload= [
            "phone" => $request->contact_number,
            "city" => $request->city,
            "name" => $data['warehouse_code'],
            "pin" => $request->pincode,
            "address" => $request->wareHouseAddress,
            "country" => $request->country,
            "email" => $request->support_email,
            "registered_name" => $data['warehouse_code'],
            "return_address" => $request->wareHouseAddress,
            "return_pin" => $request->pincode,
            "return_city" => $request->city,
            "return_state" => $request->state,
            "return_country" => $request->country
        ];
        // dd($payload);
        $response = Http::withHeaders([
            'Authorization' => 'Token 894217b910b9e60d3d12cab20a3c5e206b739c8b',
            'Content-Type' => 'application/json'
        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

        $payload_udaan = [
            "orgUnitId" => "",
            "addressLine1" => $request->wareHouseAddress,
            "addressLine2" => "",
            "addressLine3" => "",
            "city" => $request->city,
            "state" => $request->state,
            "pincode" => $request->pincode,
            "unitName" =>  $data['warehouse_code'],
            "representativeName" => $request->contact_person,
            "mobileNumber" => $request->contact_number,
            "gstin" => ""
        ];
        $response_uddan = Http::withHeaders([
            'Authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'Content-Type' => 'application/json'
        ])->post('https://udaan.com/api/udaan-express/integration/v1/address/ORGZPKZ992460QL8GPWW4JDZGLC67', $payload_udaan);

        $warehouse_udaan = $response_uddan->json();
        // dd($warehouse_udaan);
        if($warehouse_udaan['responseCode'] == 'UE_1001'){
            $org_unit_id = $warehouse_udaan['response']['orgUnitId'];
            Warehouses::where('id',$id)->update(['org_unit_id' => $org_unit_id]);
        }
        $gati = new Gati();
        $wareHouse = Warehouses::find($id);
        $gati->createWarehouse($wareHouse);
        return Warehouses::find($id);
    }

    function _addLog($response,$text){
        // $myfile = fopen("logs/xpressbees.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, "\n".date('Y-m-d H:i:s')."----". $text." ------- ".json_encode($response));
        // fclose($myfile);
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

    // Check courier is blocked or not
    function isCourierBlocked(Order $order, string $courier_keyword) {
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
            $orderZone = $this->getOrderZone($order);
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

    // Get order zone
    function getOrderZone(Order $order) {
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
        } else if (in_array(strtolower($order->s_city), $this->metroCities) && in_array(strtolower($order->p_city), $this->metroCities)) {
            return 'c';
        } else {
            return 'd';
        }
    }
    //download single Label PDF of order
    function downloadAwbLabel(Request $request)
    {
        $res = [
            'status' => false,
            'message' => ''
        ];
        $awb = $request->awb_number;
        $apiKey = $request->api_key;
        $sellerData = Seller::where('api_key',$apiKey)->whereNotNull('api_key')->first();
        if(empty($sellerData)){
            $res['message'] = "API key is Invalid";
            return response($res)->withHeaders(['content-type' => 'application/json']);
        }
        $data['config'] = $this->info['config'];
        $data['seller'] = $sellerData;
        $data['basic_info'] = Basic_informations::where('seller_id', $sellerData->id)->first();
        $data['order'] = Order::where('awb_number',$awb)->whereNotNull('awb_number')->first();
        if(empty($data['order'])){
            $res['message'] = "Order Not Found";
            return response($res)->withHeaders(['content-type' => 'application/json']);
        }
        // Get label configuration
        $label = LabelCustomization::where('seller_id', $sellerData->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = $sellerData->id;
            $label->header_visibility = $request->header_visibility ?? 'y';
            $label->shipping_address_visibility = $request->shipping_address_visibility ?? 'y';
            $label->header_logo_visibility = $request->header_logo_visibility ?? 'y';
            $label->shipment_detail_visibility = $request->shipment_detail_visibility ?? 'y';
            $label->awb_barcode_visibility = $request->awb_barcode_visibility ?? 'y';
            $label->order_detail_visibility = $request->order_detail_visibility ?? 'y';
            $label->order_barcode_visibility = $request->order_barcode_visibility ?? 'y';
            $label->product_detail_visibility = $request->product_detail_visibility ?? 'y';
            $label->invoice_value_visibility = $request->invoice_value_visibility ?? 'y';
            $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
            $label->gift_visibility = $request->gift_visibility ?? 'n';
            $label->footer_visibility = $request->footer_visibility ?? 'y';
            $label->all_product_display = $request->all_product_display ?? 'n';
            $label->save();
        }
        $data['label'] = $label;
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['product'] = Product::where('order_id', $data['order']->id)->get();
        $data['RTOAddress'] = [
            'first_name' => $data['order']->p_warehouse_name,
            'address_line1' => $data['order']->p_address_line1,
            'address_line2' => $data['order']->p_address_line2,
            'pincode' => $data['order']->p_pincode,
            'city' => $data['order']->p_city,
            'state' => $data['order']->p_state,
            'contact' => $data['order']->p_contact_number,
            'country' => $data['order']->p_country,
        ];
        if($data['order']->same_as_rto == 'n'){
            if($data['order']->warehouose_id != $data['order']->rto_warehouse_id){
                $warehouse = Warehouses::find($data['order']->rto_warehouse_id);
                $data['RTOAddress'] = [
                    'first_name' => $warehouse->warehouse_name,
                    'address_line1' => $warehouse->address_line1,
                    'address_line2' => $warehouse->address_line2,
                    'pincode' => $warehouse->pincode,
                    'city' => $warehouse->city,
                    'state' => $warehouse->state,
                    'contact' => $warehouse->contact_number,
                    'country' => $warehouse->country
                ];
            }
        }
        if($data['order']->shipment_type == 'mps') {
            $mpsOrder = [];
            $data['order']->is_parent = 'y';
            $data['order']->parent_awb = $data['order']->awb_number;
            $data['order']->parent_gati_package_no = $data['order']->gati_package_no;
            $mpsOrder[] = clone $data['order'];
            $mps = MPS_AWB_Number::where('order_id', $data['order']->id)->get();
            foreach($mps as $row) {
                $data['order']->awb_number = $row->awb_number;
                $data['order']->awb_barcode = $row->awb_barcode;
                $data['order']->gati_ou_code = $row->gati_ou_code;
                $data['order']->gati_package_no = $row->gati_package_no;
                $data['order']->is_parent = 'n';
                $mpsOrder[] = clone $data['order'];
            }
            $data['orders'] = $mpsOrder;
            $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        } else {
            $pdf = PDF::loadView('seller.single_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        }
        return $pdf->download('Label-' . $awb . '.pdf');
        //  return view('seller.label_data', $data);
    }
    //download single Label PDF of order
    function downloadAwbLabelEncoded(Request $request)
    {
        $res = [
            'status' => false,
            'message' => ''
        ];
        $awb = $request->awb_number;
        $apiKey = $request->api_key;
        $sellerData = Seller::where('api_key',$apiKey)->whereNotNull('api_key')->first();
        if(empty($sellerData)){
            $res['message'] = "API key is Invalid";
            return response($res)->withHeaders(['content-type' => 'application/json']);
        }
        $data['config'] = $this->info['config'];
        $data['seller'] = $sellerData;
        $data['basic_info'] = Basic_informations::where('seller_id', $sellerData->id)->first();
        $data['order'] = Order::where('awb_number',$awb)->whereNotNull('awb_number')->first();
        if(empty($data['order'])){
            $res['message'] = "Order Not Found";
            return response($res)->withHeaders(['content-type' => 'application/json']);
        }
        // Get label configuration
        $label = LabelCustomization::where('seller_id', $sellerData->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = $sellerData->id;
            $label->header_visibility = $request->header_visibility ?? 'y';
            $label->shipping_address_visibility = $request->shipping_address_visibility ?? 'y';
            $label->header_logo_visibility = $request->header_logo_visibility ?? 'y';
            $label->shipment_detail_visibility = $request->shipment_detail_visibility ?? 'y';
            $label->awb_barcode_visibility = $request->awb_barcode_visibility ?? 'y';
            $label->order_detail_visibility = $request->order_detail_visibility ?? 'y';
            $label->order_barcode_visibility = $request->order_barcode_visibility ?? 'y';
            $label->product_detail_visibility = $request->product_detail_visibility ?? 'y';
            $label->invoice_value_visibility = $request->invoice_value_visibility ?? 'y';
            $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
            $label->gift_visibility = $request->gift_visibility ?? 'n';
            $label->footer_visibility = $request->footer_visibility ?? 'y';
            $label->all_product_display = $request->all_product_display ?? 'n';
            $label->save();
        }
        $data['label'] = $label;
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['product'] = Product::where('order_id', $data['order']->id)->get();
        $data['RTOAddress'] = [
            'first_name' => $data['order']->p_warehouse_name,
            'address_line1' => $data['order']->p_address_line1,
            'address_line2' => $data['order']->p_address_line2,
            'pincode' => $data['order']->p_pincode,
            'city' => $data['order']->p_city,
            'state' => $data['order']->p_state,
            'contact' => $data['order']->p_contact_number,
            'country' => $data['order']->p_country,
        ];
        if($data['order']->same_as_rto == 'n'){
            if($data['order']->warehouose_id != $data['order']->rto_warehouse_id){
                $warehouse = Warehouses::find($data['order']->rto_warehouse_id);
                $data['RTOAddress'] = [
                    'first_name' => $warehouse->warehouse_name,
                    'address_line1' => $warehouse->address_line1,
                    'address_line2' => $warehouse->address_line2,
                    'pincode' => $warehouse->pincode,
                    'city' => $warehouse->city,
                    'state' => $warehouse->state,
                    'contact' => $warehouse->contact_number,
                    'country' => $warehouse->country
                ];
            }
        }
        if($data['order']->shipment_type == 'mps') {
            $mpsOrder = [];
            $data['order']->is_parent = 'y';
            $data['order']->parent_awb = $data['order']->awb_number;
            $data['order']->parent_gati_package_no = $data['order']->gati_package_no;
            $mpsOrder[] = clone $data['order'];
            $mps = MPS_AWB_Number::where('order_id', $data['order']->id)->get();
            foreach($mps as $row) {
                $data['order']->awb_number = $row->awb_number;
                $data['order']->awb_barcode = $row->awb_barcode;
                $data['order']->gati_ou_code = $row->gati_ou_code;
                $data['order']->gati_package_no = $row->gati_package_no;
                $data['order']->is_parent = 'n';
                $mpsOrder[] = clone $data['order'];
            }
            $data['orders'] = $mpsOrder;
            $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        } else {
            $pdf = PDF::loadView('seller.single_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        }
        $string = base64_encode($pdf->download('Label-' . $awb . '.pdf'));
        return response()->json(['status' => true,'type' => 'pdf','data' => $string]);
        //  return view('seller.label_data', $data);
    }
    function updateOrderCache($awb){
        $order = Order::where('awb_number',$awb)->first();
        if(empty($order))
            return false;
        $orderTracking = OrderTracking::where('awb_number', $awb)->orderBy('id', 'desc')->get()->toArray();
        $cachePayload = [];
        if(count($orderTracking) > 0) {
            $cachePayload['OrderId'] = $order->id;
            $cachePayload['AWBNumber'] = $order->awb_number;
            $cachePayload['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
            $cachePayload['CurrentStatus'] = $orderTracking[0]['status'];
            $cachePayload['StatusCode'] = $order->status ?? 0;
            if($cachePayload['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                $cachePayload['StatusCode'] = 'rto_delivered';
            }
            foreach($orderTracking as $orderHistory) {
                $cachePayload['OrderHistory'][] = [
                    'status_code' => $orderHistory['status_code'],
                    'status' => $orderHistory['status'],
                    'status_description' => $orderHistory['status_description'],
                    'remarks' => $orderHistory['remarks'],
                    'location' => $orderHistory['location'],
                    'updated_date' => $orderHistory['updated_date']
                ];
            }
        } else {
            $cachePayload['OrderId'] = $order->id;
            $cachePayload['AWBNumber'] = $order->awb_number;
            $cachePayload['CourierPartner'] = ShippingHelper::GetPartnerName($order->courier_partner);
            $cachePayload['CurrentStatus'] = 'Pending';
            $cachePayload['StatusCode'] = $order->status ?? null;
            $cachePayload['OrderHistory'] = [];
        }

        // Cache::store('redis')->forget('api-tracking-'.$order->awb_number);
        // Cache::store('redis')->forget('api-tracking-'.$order->id);
        Cache::store('redis')->put('api-tracking-'.$order->awb_number, $cachePayload, (60*60)*22);
        Cache::store('redis')->put('api-tracking-'.$order->id, $cachePayload, (60*60)*22);
    }
    function getOrderIdFromNumber(Request $request){
        if(empty($request->order_numbers) || empty($request->channel) || empty($request->api_key)){
            return response()->json(['status' => 'false','message' => 'Please Provide All Required Fields']);
        }
        $seller = Seller::where('api_key', $request->api_key)->first();
        if(empty($seller)){
            return response()->json(['status' => 'false','message' => 'Invalid API Key Provided']);
        }
        $orderNumbers = explode(",",$request->order_numbers);
        $response = ['status' =>  'true','message' => 'Data Found','data' => []];
        foreach ($orderNumbers as $o){
            $response['data'][$o] = Order::select('id')->where('seller_id',$seller->id)->where('customer_order_number',$o)->where('channel',$request->channel)->first();
        }
        return response()->json($response);
    }
    function getRoutingCode(Request $request,$awb){
        $apiKey = $request->api_key;
        $seller = Seller::where('api_key',$apiKey)->first();
        if(empty($seller))
            return response()->json(['status' => false,'message'=>'Invalid API key Provided']);
        $order=Order::where('seller_id',$seller->id)->where('awb_number',$awb)->select('awb_number','route_code')->first();
        if(empty($order))
            return response()->json(['status' => false,'message'=> 'No Data found for the provided AWB']);
        else
            return response()->json(['status' => true,'message'=> 'Data Found','data' => $order]);
    }
    function downloadZoneMapping($pincode)
    {
        $source = ZoneMapping::where('pincode', $pincode)->first();
        if ($source == null) {
            echo json_encode(['error' => 'invalid pincode provided']);
            exit;
        }
        $name = "Zone-$pincode";
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No', 'City', 'State', 'Has COD', 'Has DG', 'Has Prepaid', 'Has Reverse', 'Shipping Zone', 'Pincode');
        fputcsv($fp, $info);
        $detail = ZoneMapping::where('pincode', $pincode)->first();
        //echo $detail->picker_zone; exit;
        if ($detail->picker_zone != "E") {
            $datas = ZoneMapping::where('city', $source->city)->where('pincode', '!=', $source->pincode)->get();
            $cnt = 1;
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "A", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            $datas = ZoneMapping::where('picker_zone', 'E')->where('city', '!=', $source->city)->where('pincode', '!=', $source->pincode)->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "E", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            $datas = ZoneMapping::where('state', $source->state)->where('city', '!=', $source->city)->where('pincode', '!=', $source->pincode)->where('picker_zone', '!=', 'E')->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "B", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            if (in_array(strtolower($source->city), $this->metroCities)) {
                $datas = ZoneMapping::where('city', '!=', $source->city)->where('pincode', '!=', $source->pincode)->whereIn('city', $this->metroCities)->where('picker_zone', '!=', 'E')->where('city', '!=', $source->city)->where('state', '!=', $source->state)->get();
                foreach ($datas as $e) {
                    $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "C", $e->pincode);
                    fputcsv($fp, $info);
                    $cnt++;
                }
            }
            if (in_array(strtolower($source->city), $this->metroCities))
                $datas = ZoneMapping::where('state', '!=', $source->state)->where('pincode', '!=', $source->pincode)->where('picker_zone', '!=', 'E')->whereNotIn('city', $this->metroCities)->get();
            else
                $datas = ZoneMapping::where('state', '!=', $source->state)->where('pincode', '!=', $source->pincode)->where('picker_zone', '!=', 'E')->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "D", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
        } else {
            $datas = ZoneMapping::where('city', $source->city)->where('pincode', '!=', $source->pincode)->get();
            $cnt = 1;
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "A", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            $datas = ZoneMapping::where('state', $source->state)->where('city', '!=', $source->city)->where('pincode', '!=', $source->pincode)->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "B", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            $datas = ZoneMapping::where('picker_zone', 'E')->where('city', '!=', $source->city)->where('state', '!=', $source->state)->where('pincode', '!=', $source->pincode)->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "E", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
            $datas = ZoneMapping::where('state', '!=', $source->state)->where('pincode', '!=', $source->pincode)->where('picker_zone', '!=', 'E')->get();
            foreach ($datas as $e) {
                $info = array($cnt, $e->city, $e->state, $e->has_cod, $e->has_dg, $e->has_prepaid, $e->has_reverse, "D", $e->pincode);
                fputcsv($fp, $info);
                $cnt++;
            }
        }
        //fputcsv($fp, $info);
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$name.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function checkPincodeServiceable(Request $request)
    {
        $sellerKey = Seller::where('api_key',$request->apiKey)->get();
        if(count($sellerKey) == 0)
            return response()->json(['error' => 'Unauthorized'], 401);
        $response = [
            'status' => true,
            'message' => "Success",
            'data' => []
        ];
        $checkCourierFM = [
            'ekart',
            'dtdc_surface',
            'bluedart',
            'bluedart_surface',
            'movin',
            'shree_maruti_ecom'
        ];
        $serviceability_check = Partners::select('serviceability_check')->distinct()->get();

        $pincode = [];
        foreach ($serviceability_check as $s){
            if(in_array($s->serviceability_check,$checkCourierFM)){
                $FM = ServiceablePincodeFM::where('courier_partner',$s->serviceability_check)->where('pincode',$request->sourcePincode)->get();
                $LM = ServiceablePincode::where('courier_partner',$s->serviceability_check)->where('pincode',$request->destinationPincode)->get();
                if(count($FM) > 0 && count($LM) > 0)
                    $pincode[] = ['Partner' => ShippingHelper::PartnerNames[$s->serviceability_check] ?? $s->serviceability_check];
            }
            else{
                $LM = ServiceablePincode::where('courier_partner',$s->serviceability_check)->where('pincode',$request->destinationPincode)->get();
                if(count($LM) > 0)
                    $pincode[] = ['Partner' => ShippingHelper::PartnerNames[$s->serviceability_check] ?? $s->serviceability_check];
            }
        }
        $response['data'] = $pincode;
        return response()->json($response);
    }
}
