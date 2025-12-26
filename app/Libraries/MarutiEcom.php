<?php

namespace App\Libraries;

use App\Models\DefaultInvoiceAmount;
use App\Models\MarutiEcomAwbs;
use App\Models\Order;
use App\Models\Partners;
use App\Models\ZoneMapping;
use Dotenv\Util\Str;
use Illuminate\Support\Facades\Http;
use App\Libraries\Logger;
use Exception;

class MarutiEcom
{
    /**
     * User username.
     *
     * @var string
     */
    private $userName;

    /**
     * User password.
     *
     * @var string
     */
    private $password;

    /**
     * User client code.
     *
     * @var string
     */
    private $clientCode;

    /**
     * User secret key.
     *
     * @var string
     */
    private $secKey;

    /**
     * User details.
     *
     * @var string
     */
    private $userData;
    private $token;
    private $allStates;

    /**
     * API URL.
     *
     * @var url string
     */
    private $url;

    function __construct(string $userName = 'TwinnshipSOLUTIONS.DPCLIENT', string $password = 'SHipe@2022', string $clientCode = '2637', string $secKey = 'UvbhteHco8p')
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->clientCode = $clientCode;
        $this->secKey = $secKey; // PfCcGW6rZVK beta key, UvbhteHco8p live key
        // Beta url
        $this->url = 'https://customerapi.sevasetu.in/index.php/clientbooking_ecom';
        $this->allStates = $this->getAllStates();
        // Live url
        // $this->url = 'https://customerapi.sevasetu.in/index.php/clientbooking_ecom';
        // Get user data
        $this->getLogin();
    }

    /**
     * Login
     *
     * @return mixed
     */
    function getLogin()
    {
        try {
            $payload = [
                "data" => [
                    "login_username" => $this->userName,
                    "login_password" => $this->password
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Login Request",
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => $this->userName,
                'password' => $this->password,
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/login", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Login Response",
                'data' => $res
            ]);
            if ($res['success'] == "1") {
                $this->userData = $res['data'];
                $this->token = $res['AuthToken'];
            }
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Login Response",
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate token
     *
     * @param string $payload
     * @return mixed
     */
    function generateToken(string $payload)
    {
        $client_code = $this->clientCode;
        $project = 'shreemaruticourier'; // Static string
        $encrypt = '14daf8a3b6244969d9ac951de4871eed'; // Static string
        $sec_key = $this->secKey; // From maruti courier
        $request_json = $payload;
        if (!empty($client_code) && !empty($project) && !empty($encrypt) && !empty($sec_key)) {
            $hash = $client_code . '|' . $project . '|' . $encrypt . '|' . $request_json;
            return hash_pbkdf2('sha256', $hash, $sec_key, 100000, 60, false);
        }
        return '';
    }

    /**
     * Create new order.
     *
     * @param Order $order
     * @return mixed
     */
    function createOrder(Order $order)
    {
        try {

            $payload = $this->generatePayload($order);

            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Request For Order Id: " . $order->id,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => 'shreemaruticourier',
                'password' => '14daf8a3b6244969d9ac951de4871eed',
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/insertbooking", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Response For Order Id: " . $order->id,
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Response For Order Id: " . $order->id,
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    function generatePayload($order){
        $partnerData = Partners::where('keyword',$order->courier_partner)->first();
        $defaultAmount = DefaultInvoiceAmount::where('seller_id',$order->seller_id)->where('partner_id',$partnerData->id)->first();
        if (strtolower($order->order_type) == 'prepaid')
        {
            $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
        }
        $pickupStateId = '';
        $deliveryStateId = '';
        $pickupDetail = ZoneMapping::where('pincode',$order->p_pincode)->first();
        $deliveryDetail = ZoneMapping::where('pincode',$order->s_pincode)->first();
        foreach ($this->allStates as $row) {
            if (strtolower($row['StateName']) == strtolower($pickupDetail->state)) {
                $pickupStateId = $row['StateID'];
            }
            if (strtolower($row['StateName']) == strtolower($deliveryDetail->state)) {
                $deliveryStateId = $row['StateID'];
            }
        }
        $weight = $order->weight > $order->vol_weight ? $order->weight : $order->vol_weight;
        if($order->seller_id == 1138){
            if($weight > 10000)
                $weight -= 5000;
            else if($weight > 5000)
                $weight -= 3000;
            else if($weight > 3000)
                $weight = 1000;
            else
                $weight = 500;
        }
        $payload = [
                "Data" => [
                    [
                        "data" => [
                            'ClientRefID' => $this->userData['ClientRefID'] ?? '',
                            'IsDP' => $this->userData['IsDP'] ?? '',
                            'OrderNo' => (string)$order->id,
                            'DocumentNo' => $order->awb_number,
                            'ServiceTypeID' => '1',
                            'TravelBy' => '1',
                            'IsCodOrder' => strtolower($order->order_type) == 'cod' ? 1 : 0,
                            'ProductSKU' => in_array($order->seller_id,[32508,32054]) ? "Apricot" : $order->product_sku,
                            'Quantity' => $order->product_qty,
                            'ActualWeight' => $weight,
                            'Width' => $order->breadth,
                            'Length' => $order->length,
                            'Height' => $order->height,
                            'OrderAmount' => $order->invoice_amount + ($defaultInvoiceAmount ?? 0),
                            'EwayNumber' => $order->invoice_amount > 50000 ? $order->ewaybill_number : '',
                            'PickupPincode' => $order->p_pincode,
                            'SenderName' => $order->p_customer_name,
                            'SenderAddress' => $order->p_address_line1,
                            'SenderCity' => $order->p_city,
                            'SenderState' => $pickupStateId ?? '',
                            'SenderArea' => strlen(($order->p_address_line2 ?? "")) > 100 ?  (substr(($order->p_address_line2 ?? ""),0,99)) : ($order->p_address_line2 ?? ""),
                            'SenderPincode' => $order->p_pincode,
                            'SenderMobile' => $order->p_contact,
                            'SenderEmail' => 'info@Twinnship.in',
                            'ReceiverName' => substr($order->s_customer_name, 0, 50),
                            'ReceiverAddress' => substr($order->s_address_line1,0,255),
                            'ReceiverCity' => $order->s_city,
                            'ReceiverState' => $deliveryStateId ?? '',
                            'ReceiverArea' => strlen(($order->s_address_line2 ?? "")) > 100 ?  (substr(($order->s_address_line2 ?? ""),0,99)) : ($order->s_address_line2 ?? ""),
                            'ReceiverPincode' => $order->s_pincode,
                            'ReceiverMobile' => $order->s_contact,
                            'ReceiverEmail' => 'info@Twinnship.in',
                            'Remarks' => 'DoPickup',
                            'UserID' => $this->userData['UserID'] ?? ''

                        ]
                    ]
                ]
            ];
            return $payload;
    }
    /**
     * Get state list.
     *
     * @param string $date
     * @return mixed
     */
    function getState(string $date)
    {
        try {
            $payload = [
                "data" => [
                    "date" => $date
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Request",
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => 'shreemaruticourier',
                'password' => '14daf8a3b6244969d9ac951de4871eed',
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/getstatedata", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Response Request",
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Response Request",
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get rate
     *
     * @param Order $order
     * @return mixed
     */
    function getRate(Order $order)
    {
        try {
            $payload = [
                "Data" => [
                    [
                        "data" => [
                            "IsDP" => $this->userData['IsDP'] ?? '',
                            "ClientRefID" => $this->userData['ClientRefID'] ?? '',
                            "FromPincode" => $order->p_pincode,
                            "ToPincode" => $order->s_pincode,
                            "DocType" => "1",
                            "Weight" => (float) $order->weight
                        ]
                    ]
                ]
            ];
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => 'shreemaruticourier',
                'password' => '14daf8a3b6244969d9ac951de4871eed',
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/ratecalculator", $payload);
            return $httpRes->json();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Cancel order.
     *
     * @param string $awb
     * @return mixed
     */
    function cancelOrder(string $awb)
    {
        try {
            $payload = [
                'Data' => [
                    [
                        'data' => [
                            'ClientRefID' => $this->userData['ClientRefID'] ?? '',
                            'IsDP' => $this->userData['IsDP'] ?? '',
                            'DocumentNo' => $awb,
                            'CancelType' => '1',
                            'CancelRemark' => 'Customer has cancelled the order',
                            'UserID' => $this->userData['UserID']
                        ]
                    ]
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Request For AWB: " . $awb,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => 'shreemaruticourier',
                'password' => '14daf8a3b6244969d9ac951de4871eed',
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/cancelbooking", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Response For AWB: " . $awb,
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Track order.
     *
     * @param string $awb
     * @return mixed
     */
    function trackOrder(string $awb)
    {
        try {
            $payload = [
                'Data' => [
                    [
                        "data" => [
                            'ClientRefID' => $this->userData['ClientRefID'] ?? $this->clientCode,
                            'IsDP' => $this->userData['IsDP'] ?? '1',
                            'DocumentNo' => $awb,
                        ]
                    ]
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Request For AWB: " . $awb,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'username' => 'shreemaruticourier',
                'password' => '14daf8a3b6244969d9ac951de4871eed',
                'clientid' => $this->clientCode,
                'Content-Type' => 'application/json',
            ])->post("{$this->url}/tracking", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Response For AWB: " . $awb,
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Response For AWB: " . $awb,
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    function reAttempt($data){
        try {
            $payload = [
                "Data" => [
                    [
                        "data" => [
                            "DocumentNo" => $data['awbNumber'],
                            "ReattemptDate" => $data['reattemptDate'],
                            "AlternateNo" => $data['alternateNo'],
                            "NewAddress" => $data['newAddress'],
                            "Remark" => $data['remark'],
                        ]
                    ]
                ]
            ];

            // Logging
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Request For AWB: " . $data['awbNumber'],
                'data' => $payload
            ]);

            $httpResponse = Http::withHeaders(['token' => $this->token, 'Clientid' => $this->userData['ClientRefID']])->post("{$this->url}/reattempt_request", $payload)->json();
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $data['awbNumber'],
                'data' => $httpResponse
            ]);
        }catch (Exception $e){
            Logger::write('logs/partners/shree-maruti-ecom/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $data['awbNumber'],
                'data' => $e->getMessage()
            ]);
        }
    }
    function getAllStates(){
        $allStates = [
            [
                "StateID" => "35",
                "StateCode" => "35",
                "StateName" => "ANDAMAN AND NICOBAR",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "17",
                "StateCode" => "37",
                "StateName" => "ANDHRA PRADESH",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "27",
                "StateCode" => "12",
                "StateName" => "ARUNACHAL PRADESH",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "18",
                "StateCode" => "18",
                "StateName" => "ASSAM",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "21",
                "StateCode" => "10",
                "StateName" => "BIHAR",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "25",
                "StateCode" => "04",
                "StateName" => "CHANDIGARH",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "6",
                "StateCode" => "22",
                "StateName" => "CHHATTISGARH",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "33",
                "StateCode" => "26",
                "StateName" => "DADRA AND NAGAR HAVELI",
                "ZoneName" => "WEST2"
            ],
            [
                "StateID" => "32",
                "StateCode" => "26",
                "StateName" => "DAMAN AND DIU",
                "ZoneName" => "WEST2"
            ],
            [
                "StateID" => "9",
                "StateCode" => "07",
                "StateName" => "DELHI",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "3",
                "StateCode" => "30",
                "StateName" => "GOA",
                "ZoneName" => "WEST1"
            ],
            [
                "StateID" => "1",
                "StateCode" => "24",
                "StateName" => "GUJARAT",
                "ZoneName" => "WEST2"
            ],
            [
                "StateID" => "10",
                "StateCode" => "06",
                "StateName" => "HARYANA",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "24",
                "StateCode" => "02",
                "StateName" => "HIMACHAL PRADESH",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "8",
                "StateCode" => "01",
                "StateName" => "JAMMU & KASHMIR",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "19",
                "StateCode" => "20",
                "StateName" => "JHARKHAND",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "13",
                "StateCode" => "29",
                "StateName" => "KARNATAKA",
                "ZoneName" => "SOUTH1"
            ],
            [
                "StateID" => "14",
                "StateCode" => "32",
                "StateName" => "KERALA",
                "ZoneName" => "SOUTH1"
            ],
            [
                "StateID" => "34",
                "StateCode" => "31",
                "StateName" => "LAKSHADWEEP",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "5",
                "StateCode" => "23",
                "StateName" => "MADHYA PRADESH",
                "ZoneName" => "WEST1"
            ],
            [
                "StateID" => "2",
                "StateCode" => "27",
                "StateName" => "MAHARASHTRA",
                "ZoneName" => "WEST1"
            ],
            [
                "StateID" => "23",
                "StateCode" => "14",
                "StateName" => "MANIPUR",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "31",
                "StateCode" => "17",
                "StateName" => "MEGHALAYA",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "29",
                "StateCode" => "15",
                "StateName" => "MIZORAM",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "28",
                "StateCode" => "13",
                "StateName" => "NAGALAND",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "20",
                "StateCode" => "21",
                "StateName" => "ORISSA",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "16",
                "StateCode" => "34",
                "StateName" => "PONDICHERRY",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "11",
                "StateCode" => "03",
                "StateName" => "PUNJAB",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "4",
                "StateCode" => "08",
                "StateName" => "RAJASTHAN",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "26",
                "StateCode" => "11",
                "StateName" => "SIKKIM",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "15",
                "StateCode" => "33",
                "StateName" => "TAMILNADU",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "36",
                "StateCode" => "36",
                "StateName" => "TELANGANA",
                "ZoneName" => "SOUTH2"
            ],
            [
                "StateID" => "30",
                "StateCode" => "16",
                "StateName" => "TRIPURA",
                "ZoneName" => "EAST"
            ],
            [
                "StateID" => "7",
                "StateCode" => "09",
                "StateName" => "UTTAR PRADESH",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "12",
                "StateCode" => "05",
                "StateName" => "UTTARAKHAND",
                "ZoneName" => "NORTH"
            ],
            [
                "StateID" => "22",
                "StateCode" => "19",
                "StateName" => "WEST BENGAL",
                "ZoneName" => "EAST"
            ]
        ];
        return $allStates;
    }
}
