<?php

namespace App\Libraries;

use App\Models\DefaultInvoiceAmount;
use App\Models\Order;
use App\Models\Partners;
use Dotenv\Util\Str;
use Illuminate\Support\Facades\Http;
use App\Libraries\Logger;
use Exception;

class Maruti
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

    /**
     * API URL.
     *
     * @var url string
     */
    private $url;

    function __construct(string $userName = 'TwinnshipSOLUTIONS.DPCLIENT', string $password = 'TwinnshipSol@0411', string $clientCode = '2637', string $secKey = '7DNE6isoBzb')
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->clientCode = $clientCode;
        $this->secKey = $secKey;
        $this->url = 'https://customerapi.sevasetu.in/index.php/clientbooking_v2';
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
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Login Request",
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
            ])->post("{$this->url}/login", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Login Response",
                'data' => $res
            ]);
            if ($res['success'] == "1") {
                $this->userData = $res['data'];
            }
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
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
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Request For Order Id: " . $order->id,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
            ])->post("{$this->url}/insertbooking", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Response For Order Id: " . $order->id,
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Create Order Response For Order Id: " . $order->id,
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    function generatePayload(Order $order) {
        try {
            $partnerData = Partners::where('keyword',$order->courier_partner)->first();
            $defaultAmount = DefaultInvoiceAmount::where('seller_id',$order->seller_id)->where('partner_id',$partnerData->id)->first();
            if (strtolower($order->order_type) == 'prepaid')
            {
                $defaultInvoiceAmount = $defaultAmount->amount ?? 0;
            }
            $stateList = $this->getState($order->inserted);
            $stateId = '';
            if (!empty($stateList['state_data'])) {
                foreach ($stateList['state_data'] as $row) {
                    if (strtolower($row['StateName']) == strtolower($order->s_state)) {
                        $stateId = $row['StateID'];
                        break;
                    }
                }
            }

            $payload = [
                "Data" => [
                    [
                        "data" => [
                            "IsDP" => $this->userData['IsDP'] ?? '',
                            "ClientRefID" => $this->userData['ClientRefID'] ?? '',
                            "UserID" => $this->userData['UserID'] ?? '',
                            "ReceiverName" => $order->s_customer_name,
                            "DocumentNoRef" => $order->awb_number,
                            "OrderNo" => $order->order_number,
                            "ToPincode" => $order->s_pincode,
                            "CodBooking" => $order->order_type == 'cod' ? 1 : 0,
                            "TypeID" => "2",
                            "ServiceTypeID" => "1",
                            "TravelBy" => "1",
                            "Length" => $order->length,
                            "Width" => $order->breadth,
                            "Height" => $order->height,
                            "Weight" => $order->weight,
                            "ValueRs" => $order->invoice_amount + ($defaultInvoiceAmount ?? 0),
                            "ReceiverAddress" => $order->s_address_line1,
                            "ReceiverCity" => $order->s_city,
                            "ReceiverState" => $stateId ?? "",
                            "Area" => $order->s_address_line2 ?? "t",
                            "ReceiverMobile" => $order->s_contact,
                            "ReceiverEmail" => "info@Twinnship.in",
                            "Remarks" => "Booking",
                        ]
                    ]
                ]
            ];
            return $payload;
        } catch(Exception $e) {
            return [];
        }
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
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Request",
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
            ])->post("{$this->url}/getstatedata", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Response Request",
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Get State Response Request",
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get orders of given date.
     *
     * @param string $date
     * @return mixed
     */
    function getOrders(string $date)
    {
        try {
            $payload = [
                "data" => [
                    "IsDP" => $this->userData['IsDP'] ?? '',
                    "ClientRefID" => $this->userData['ClientRefID'] ?? '',
                    "bookingdate" => $date
                ]
            ];
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
            ])->post("{$this->url}/getshipmentdetails", $payload);
            return $httpRes->json();
        } catch (Exception $e) {
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
                            "Weight" => $order->weight
                        ]
                    ]
                ]
            ];
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
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
                "Data" => [
                    [
                        "data" => [
                            "ClientRefID" => $this->userData['ClientRefID'] ?? '',
                            "UserID" => $this->userData['UserID'],
                            "DocumentNoRef" => $awb,
                        ]
                    ]
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Request For AWB: ".$awb,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientid' => $this->clientCode
            ])->post("{$this->url}/cancelbooking", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Cancel Order Response For AWB: ".$awb,
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
                "data" => [
                    "reference_no" => $awb,
                ]
            ];
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Request For AWB: ".$awb,
                'data' => $payload
            ]);
            $httpRes = Http::withHeaders([
                'token' => $this->generateToken(json_encode($payload)),
                'clientcode' => $this->clientCode
            ])->post("{$this->url}/client_tracking_all", $payload);
            $res = $httpRes->json();
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Response For AWB: ".$awb,
                'data' => $res
            ]);
            return $res;
        } catch (Exception $e) {
            // Logging
            Logger::write('logs/partners/shree-maruti/shree-maruti-' . date('Y-m-d') . '.text', [
                'title' => "Track Order Response For AWB: ".$awb,
                'data' => $e->getMessage()
            ]);
            return false;
        }
    }
}
