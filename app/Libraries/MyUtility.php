<?php

namespace App\Libraries;

use App\Helpers\ShippingHelper;
use App\Helpers\TrackingHelper;
use App\Http\Controllers\EcomExpress3kgController;
use App\Http\Controllers\EcomExpressController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Utilities;
use App\Jobs\SendManifestationSms;
use App\Jobs\SendManifestationWhatsApp;
use App\Libraries\Custom\CustomDelhivery;
use App\Libraries\Custom\CustomDtdc;
use App\Libraries\Custom\CustomXpressBees;
use App\Models\Channels;
use App\Models\Configuration;
use App\Models\DelhiveryAWBNumbers;
use App\Models\DtdcAwbNumbers;
use App\Models\DtdcLLAwbNumbers;
use App\Models\DtdcSEAwbNumbers;
use App\Models\EcomExpressAwbs;
use App\Models\EkartAwbNumbers;
use App\Models\EkartSmallAwbNumbers;
use App\Models\EmployeeWorkLogs;
use App\Models\InternationalOrders;
use App\Models\Manifest;
use App\Models\ManifestOrder;
use App\Models\MarutiEcomAwbs;
use App\Models\MovinAWBNumbers;
use App\Models\PickDelAwbNumbers;
use App\Models\PincodeDistance;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Models\ProfessionalAwbNumbers;
use App\Models\Rates;
use App\Models\Seller;
use App\Models\SendManifestationSmsJob;
use App\Models\SendManifestationWhatsAppJob;
use App\Models\ShadowfaxAWBNumbers;
use App\Models\SMCMarutiAWB;
use App\Models\SMCNewAWB;
use App\Models\Transactions;
use App\Models\ZoneMapping;
use App\Models\SKU;
use App\Models\Warehouses;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use DateTime;

class MyUtility
{
    const northZoneArray = ['CHANDIGARH', 'HARYANA', 'HIMACHAL PRADESH', 'PUNJAB', 'RAJASTHAN', 'UTTAR PRADESH', 'UTTARAKHAND', 'DELHI'];
    const westZoneArray = ['MADHYA PRADESH', 'GOA', 'MAHARASHTRA', 'GUJARAT'];
    const eastZoneArray = ['BIHAR', 'JHARKHAND', 'CHATTISGARH', 'ODISHA', 'WEST BENGAL'];
    const southZoneArray = ['ANDHRA PRADESH', 'TELANGANA', 'KARNATAKA', 'KERALA', 'TAMIL NADU', 'ANDAMAN & NICOBAR ISLANDS'];
    const northEastZone = ['ARUNACHAL PRADESH', 'ASSAM', 'MANIPUR', 'MEGHALAYA', 'MIZORAM', 'NAGALAND', 'SIKKIM', 'TRIPURA'];
    const ncrArray = ['gurgaon', 'noida', 'ghaziabad', 'faridabad', 'delhi', 'new delhi', 'gurugram'];
    const metroCities = ['bangalore', 'chennai', 'hyderabad', 'kolkata', 'mumbai', 'new delhi', 'delhi', 'pune', 'gurugram', 'gurgaon', 'noida', 'ghaziabad', 'faridabad'];
    const b2bZone = [
        'north' => [
            'city' => [
                'delhi',
                'new delhi',
                'gurgaon',
                'gurugram',
                'faridabad',
                'ghaziabad',
                'noida',
            ],
            'excludedCity' => [],
            'state' => [
                'uttar pradesh',
                'uttarpradesh',
                'uttarakhand',
                'punjab',
                'haryana',
                'himachal pradesh',
                'himachalpradesh',
                'jammu and kashmir',
                'jammu & kashmir',
                'rajasthan'
            ]
        ],
        'west' => [
            'city' => [
                'mumbai',
            ],
            'excludedCity' => [
                'goa',
            ],
            'state' => [
                'gujarat',
                'daman & diu',
                'daman and diu',
                'diu & daman',
                'diu and daman',
                'dadra & nagar haveli',
                'dadra and nagar haveli',
                'madhya pradesh',
                'madhyapradesh',
                'chhattisgarh',
                'maharashtra',
            ]
        ],
        'south' => [
            'city' => [
                'chennai',
                'puducherry',
            ],
            'excludedCity' => [],
            'state' => [
                'karnataka',
                'andhra pradesh',
                'andhrapradesh',
                'telangana',
                'tamil nadu',
                'tamilnadu',
                'kerala'
            ]
        ],
        'east' => [
            'city' => [],
            'excludedCity' => [],
            'state' => [
                'west bengal',
                'westbengal',
                'sikkim',
                'bihar',
                'orissa',
                'jharkhand'
            ]
        ],
        'northEast' => [
            'city' => [
                'guwahati'
            ],
            'excludedCity' => [],
            'state' => [
                'meghalaya',
                'arunachal pradesh',
                'arunachalpradesh',
                'mizoram',
                'tripura',
                'manipur',
                'nagaland',
                'assam',
            ]
        ]
    ];
    //get Start and End date using week number and year
    public static function getStartAndEndDate($week, $year)
    {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');
        return $result;
    }

    //get Start and End date using week number and year
    public static function getStartAndEndDateView($week, $year)
    {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('d M');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('d M');
        return $result['start_date'] . ' - ' . $result['end_date'];
    }

    //insert seller Rates data using seller id (optional)
    public static function fill_seller_rates($seller_id)
    {
        Rates::where('seller_id', $seller_id)->delete();
        $allData = Rates::where('seller_id', 0)->get();
        $rateData = [];
        foreach ($allData as $a) {
            $rateData[] = [
                'partner_id' => $a->partner_id,
                'plan_id' => $a->plan_id,
                'within_city' => $a->within_city,
                'within_state' => $a->within_state,
                'metro_to_metro' => $a->metro_to_metro,
                'rest_india' => $a->rest_india,
                'north_j_k' => $a->north_j_k,
                'cod_charge' => $a->cod_charge,
                'cod_maintenance' => $a->cod_maintenance,
                'extra_charge_a' => $a->extra_charge_a,
                'extra_charge_b' => $a->extra_charge_b,
                'extra_charge_c' => $a->extra_charge_c,
                'extra_charge_d' => $a->extra_charge_d,
                'extra_charge_e' => $a->extra_charge_e,
                'seller_id' => $seller_id
            ];
            if (count($rateData) == 500) {
                Rates::insert($rateData);
                $rateData = [];
            }
        }
        Rates::insert($rateData);
    }
    public static function gstcheck($p_state, $s_state)
    {
        if (strtolower($p_state) == strtolower($s_state)) {
            return 'sgst_cgst';
        } else {
            return 'igst';
        }
    }
    public static function findMatchCriteria($fromPincode, $toPincode, $sellerDetail)
    {
        $fromDetail = self::findPincodeDetails($fromPincode);
        $toDetail = self::findPincodeDetails($toPincode);
        if ($toDetail['status'] == 'Failed') {
            return 'not_found';
        }
        return self::getOrderZoneByDistanceDefault($toPincode, $fromPincode, $toDetail['city'], $fromDetail['city'], $toDetail['state'], $fromDetail['state']);
    }
    public static function getOrderZoneByDefault($s_pincode, $s_city, $p_city, $s_state, $p_state)
    {
        $res = ZoneMapping::where('pincode', $s_pincode)->where('picker_zone', 'E')->get();
        if (in_array(strtolower($s_city), self::ncrArray) && in_array(strtolower($p_city), self::ncrArray)) {
            return 'within_city';
        } else if (strtolower($s_city) == strtolower($p_city) && strtolower($s_state) == strtolower($p_state)) {
            return 'within_city';
        } else if (count($res) == 1) {
            return 'north_j_k';
        } else if (strtolower($s_state) == strtolower($p_state)) {
            return 'within_state';
        } else if (in_array(strtolower($s_city), self::metroCities) && in_array(strtolower($p_city), self::metroCities)) {
            return 'metro_to_metro';
        } else {
            return 'rest_india';
        }
    }
    public static function getOrderZoneByDistance($s_pincode, $p_pincode, $s_city, $p_city, $s_state, $p_state)
    {
        $res = ZoneMapping::where('pincode', $s_pincode)->where('picker_zone', 'E')->get();
        if (in_array(strtolower($s_city), self::ncrArray) && in_array(strtolower($p_city), self::ncrArray)) {
            return 'within_city';
        } else if (strtolower($s_city) == strtolower($p_city) && strtolower($s_state) == strtolower($p_state)) {
            return 'within_city';
        } else if (count($res) == 1) {
            return 'north_j_k';
        } else if (strtolower($s_state) == strtolower($p_state)) {
            return 'within_state';
        } else if (in_array(strtolower($s_city), self::metroCities) && in_array(strtolower($p_city), self::metroCities)) {
            return 'metro_to_metro';
        } else {
            $distance = 600000;
            $query = PincodeDistance::where('pincode1',$p_pincode)->where('pincode2',$s_pincode)->orWhere('pincode1',$s_pincode)->where('pincode2',$p_pincode)->first();
            if(!empty($query)){
                $distance = $query->distance;
            }else{
                $responseData = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json?origins={$s_pincode}&destinations={$p_pincode}&key=AIzaSyAHkwl-CEfw1Hyt2Oe6NOFIKCip9QqweP8")->json();
                $distance = $responseData['rows'][0]['elements'][0]['distance']['value'] ?? 600000;
                PincodeDistance::create([
                    'pincode1' => $p_pincode,
                    'pincode2' => $s_pincode,
                    'payload' => $responseData,
                    'distance' => $distance,
                    'inserted' => date('Y-m-d H:i:s')
                ]);
            }
            if ($distance < 500000)
                return 'within_state';
            else
                return 'rest_india';
        }
    }
    public static function GetDistanceFromAPI($source, $destination)
    {
        $responseData = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json?origins={$source}&destinations={$destination}&key=AIzaSyAHkwl-CEfw1Hyt2Oe6NOFIKCip9QqweP8")->json();
        $distance = $responseData['rows'][0]['elements'][0]['distance']['value'] ?? 0;
        if($distance > 0){
            PincodeDistance::create([
                'pincode1' => $source,
                'pincode2' => $destination,
                'payload' => json_encode($responseData),
                'distance' => $distance,
                'inserted' => date('Y-m-d H:i:s')
            ]);
        }
        return $distance;
    }
    public static function getOrderZoneByDistanceDefault($s_pincode, $p_pincode, $s_city, $p_city, $s_state, $p_state)
    {
        $query = PincodeDistance::where('pincode1',$p_pincode)->where('pincode2',$s_pincode)->orWhere('pincode1',$s_pincode)->where('pincode2',$p_pincode)->first();
        if(!empty($query))
            $distance = $query->distance;
        else
            $distance = self::GetDistanceFromAPI($p_pincode, $s_pincode);

        if($distance == 0)
            return self::getOrderZoneByDefault($s_pincode, $s_city, $p_city, $s_state, $p_state);

        $distanceKilometer = $distance / 1000;
        if($distanceKilometer < 100 || (in_array(strtolower($s_city), self::ncrArray) && in_array(strtolower($p_city), self::ncrArray)))
            return 'within_city';
        else if($distanceKilometer < 400)
            return 'within_state';
        else if (in_array(strtolower($s_city), self::metroCities) && in_array(strtolower($p_city), self::metroCities))
            return 'metro_to_metro';
        else if($distanceKilometer < 2200)
            return 'rest_india';
        else
            return 'north_j_k';
    }
    public static function getOrderZoneBySL($s_city, $p_city, $s_state, $p_state)
    {
        $zoneOrigin = self::_findStateZone($p_state);
        $zoneDestination = self::_findStateZone($s_state);
        if (in_array(strtolower($s_city), self::ncrArray) && in_array(strtolower($p_city), self::ncrArray)) {
            return 'within_city';
        } else if (strtolower($s_city) == strtolower($p_city) && strtolower($s_state) == strtolower($p_state)) {
            return 'within_city';
        } else if ($zoneOrigin == $zoneDestination) {
            return 'within_state';
        } else if ($zoneDestination == 'special' || $zoneDestination == 'north_east_zone') {
            return 'north_j_k';
        } else if (in_array(strtolower($s_city), self::metroCities) && in_array(strtolower($p_city), self::metroCities)) {
            return 'metro_to_metro';
        } else {
            return 'rest_india';
        }
    }
    public static function _findStateZone($state)
    {
        $state = strtoupper($state);
        if (in_array($state, self::northZoneArray)) {
            return 'north_zone';
        } else if (in_array($state, self::southZoneArray)) {
            return 'south_zone';
        } else if (in_array($state, self::eastZoneArray)) {
            return 'east_zone';
        } else if (in_array($state, self::westZoneArray)) {
            return 'west_zone';
        } else if (in_array($state, self::northEastZone)) {
            return 'north_east_zone';
        } else {
            return 'special';
        }
    }
    public static function findPincodeDetails($pincode)
    {
        $response = ZoneMapping::where('pincode', $pincode)->first();
        $printData = array(
            'status' => $response == null ? "Failed" : "Success"
        );
        if ($printData['status'] == "Success") {
            $printData['city'] = $response->city;
            $printData['state'] = $response->state;
            $printData['country'] = 'India';
        }
        return $printData;
    }
    public static function saveSKUMapping($sellerId, $sku, $productName, $weight = 500, $length = 10, $width = 10, $height = 10)
    {
        $data = [
            'seller_id' => $sellerId,
            'sku' => $sku,
            'product_name' => $productName,
            'weight' => round(($weight / 1000), 2),
            'length' => $length ?? 10,
            'width' => $width ?? 10,
            'height' => $height ?? 10
        ];
        try {
            SKU::create($data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    public static function getShadowFaxAwbNumber($orderData){
        $client = new Shadowfax();
        $sellerData = Seller::find($orderData->seller_id);
        $responseData = $client->manifestOrder($orderData, $sellerData);
        if(!empty($responseData['message']) && strtolower($responseData['message']) == 'success' && !empty($responseData['data']['awb_number'])){
            return $responseData['data']['awb_number'];
        }
        else
            return null;
    }
    public static function GetShreeMarutiSMCAWBNumber($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = MarutiEcomAwbs::where('used','n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        MarutiEcomAwbs::where('id',$getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }
    public static function GetSMCNewAWB($orderData, $courierPartner){
        try{
            $responseData = SMCNew::ShipOrder($orderData, $courierPartner);
            if(!empty($responseData['success']) && $responseData['success']){
                SMCNew::CreatePickup($orderData);
            }
            return $responseData['reference_number'];
        }
        catch(Exception $e){
            Logger::write("logs/partners/smc-new/smc-new-".date('Y-m-d').'.text', [
                'title' => "SMC New Exception",
                'data' => ['exception' => $e->getMessage()." - ". $e->getFile()." - ". $e->getLine()]
            ]);
            return false;
        }
    }
    public static function GetSMCMarutiAWB($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = SMCMarutiAWB::where('used','n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        SMCMarutiAWB::where('id',$getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }
    public static function GetMovinAwbNumber($mode = 'air'){
        DB::beginTransaction();
        $getAwbNumber = MovinAWBNumbers::where('used','n')->where('mode',$mode)->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        MovinAWBNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y']);
        DB::commit();
        return $getAwbNumber;
    }
    public static function GenerateShadowfaxAwbNumber($count,$flow){
        $shadowfax = new Shadowfax();
        if($flow == 'forward')
            $awbNumbers = $shadowfax->generateAwbNumbers($count);
        else
            $awbNumbers = $shadowfax->generateAwbNumbersReverse($count);
        if($awbNumbers['message'] == 'success'){
            foreach ($awbNumbers['awb_numbers'] as $awb_number){
                $allAwbs = [
                    'awb_number' => $awb_number,
                    'flow' => $flow,
                    'used' => 'n',
                    'inserted' => date('Y-m-d H:i:s')
                ];
                try{
                    ShadowfaxAWBNumbers::create($allAwbs);
                }catch(Exception $e){}
            }
            return true;
        }
        return false;
    }
    public static function getDelhiveryAWBNumber($businessName, $accessToken, $courier_partner,$sellerOrderType='SE',$sellerId=0)
    {
        DB::beginTransaction();
        $getAwbNumber = DelhiveryAWBNumbers::where('used', 'n')->where('seller_type',$sellerOrderType)->where('courier_partner', $courier_partner)->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            self::GenerateDelhiveryAWBNumbers($businessName, $accessToken, $courier_partner,$sellerOrderType);
            $getAwbNumber = DelhiveryAWBNumbers::where('used', 'n')->where('seller_type',$sellerOrderType)->where('courier_partner', $courier_partner)->lockForUpdate()->first();
        }
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        DelhiveryAWBNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }
    public static function GenerateDelhiveryAWBNumbers($businessName, $accessToken, $courier_partner,$sellerOrderType)
    {
        $response = Http::get("https://track.delhivery.com/waybill/api/bulk/json/?token={$accessToken}&count=10000");
        $res = trim($response->body(), '"');
        $allAWB = explode(",", $res);
        $fetchedAWB = [];
        foreach ($allAWB as $awb) {
            $fetchedAWB[] = [
                'courier_partner' => $courier_partner,
                'awb_number' => $awb,
                'seller_type' => $sellerOrderType
            ];
            if (count($fetchedAWB) == 1000) {
                DelhiveryAWBNumbers::insert($fetchedAWB);
                $fetchedAWB = [];
            }
        }
        DelhiveryAWBNumbers::insert($fetchedAWB);
    }
    public static function getEkartAwbNumber($order=null,$sellerId=0)
    {
        if($order != null && $order->suggested_awb != "") {
            DB::beginTransaction();
            $getAwbNumber = EkartAwbNumbers::where('awb_number', $order->suggested_awb)->where('used', 'n')->where('assigned', 'y')->where('seller_id', $order->seller_id)->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                DB::rollBack();
                return false;
            }
            EkartAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'used_by' => $sellerId]);
            DB::commit();
        } else {
            DB::beginTransaction();
            $getAwbNumber = EkartAwbNumbers::where('used', 'n')->where('assigned', 'n')->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                self::GenerateEkartPincodes();
                $getAwbNumber = EkartAwbNumbers::where('used', 'n')->where('assigned', 'n')->lockForUpdate()->first();
            }
            if (empty($getAwbNumber)) {
                DB::rollBack() ;
                return false;
            }
            EkartAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'used_by' => $sellerId]);
            DB::commit();
        }
        return $getAwbNumber;
    }
    public static function GenerateEkartPincodes()
    {
        $ekart = new Ekart();
        $ekart->GenerateNextAWBs();
        return true;
    }
    public static function getEkartSmallAwbNumber($order=null,$sellerId=0)
    {
        if($order != null && $order->suggested_awb != "") {
            DB::beginTransaction();
            $getAwbNumber = EkartSmallAwbNumbers::where('awb_number', $order->suggested_awb)->where('used', 'n')->where('assigned', 'y')->where('seller_id', $order->seller_id)->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                DB::rollBack();
                return false;
            }
            EkartSmallAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'used_by' => $sellerId]);
            DB::commit();
        } else {
            DB::beginTransaction();
            $getAwbNumber = EkartSmallAwbNumbers::where('used', 'n')->where('assigned', 'n')->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                self::GenerateEkartAwb();
                $getAwbNumber = EkartSmallAwbNumbers::where('used', 'n')->where('assigned', 'n')->lockForUpdate()->first();
            }
            if (empty($getAwbNumber)) {
                DB::rollBack() ;
                return false;
            }
            EkartSmallAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'used_by' => $sellerId]);
            DB::commit();
        }
        return $getAwbNumber;
    }
    public static function GenerateEkartAwb()
    {
        $ekart = new EkartSmall();
        $ekart->GenerateNextAWBn();
        return true;
    }
    public static function getOrderZoneB2B($s_city, $p_city, $s_state, $p_state)
    {
        $s_city = strtolower($s_city);
        $s_state = strtolower($s_state);
        $p_city = strtolower($p_city);
        $p_state = strtolower($p_state);
        $zones = [
            'north:north' => 'within_city', // Zone A
            'north:west' => 'within_state', //  Zone B
            'north:south' => 'metro_to_metro', // Zone C
            'north:east' => 'rest_india', // Zone D
            'north:northEast' => 'north_j_k', // Zone E
            'west:north' => 'within_state', //  Zone B
            'west:west' => 'within_city', // Zone A
            'west:south' => 'metro_to_metro', // Zone C
            'west:east' => 'rest_india', // Zone D
            'west:northEast' => 'north_j_k', // Zone E
            'south:north' => 'metro_to_metro', // Zone C
            'south:west' => 'within_state', //  Zone B
            'south:south' => 'within_city', // Zone A
            'south:east' => 'rest_india', // Zone D
            'south:northEast' => 'north_j_k', // Zone E
            'east:north' => 'rest_india', // Zone D
            'east:west' => 'rest_india', // Zone D
            'east:south' => 'rest_india', // Zone D
            'east:east' => 'metro_to_metro', // Zone C
            'east:northEast' => 'north_j_k', // Zone E
            'northEast:north' => 'north_j_k', // Zone E
            'northEast:west' => 'north_j_k', // Zone E
            'northEast:south' => 'north_j_k', // Zone E
            'northEast:east' => 'north_j_k', // Zone E
            'northEast:northEast' => 'north_j_k', // Zone E
        ];
        foreach (self::b2bZone as $s_zone => $s_data) {
            if (
                (in_array($s_city, $s_data['city']) || in_array($s_state, $s_data['state'])) &&
                !in_array($s_city, $s_data['excludedCity'])
            ) {
                foreach (self::b2bZone as $p_zone => $p_data) {
                    if (
                        (in_array($p_city, $p_data['city']) || in_array($p_state, $p_data['state'])) &&
                        !in_array($p_city, $p_data['excludedCity'])
                    ) {
                        return $zones["{$s_zone}:{$p_zone}"];
                    }
                }
            }
        }
        return 'north_j_k';
    }

    public static function getDtdcAWBNumber(){
        DB::beginTransaction();
        $getAwbNumber = DtdcAwbNumbers::where('used', 'n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        DtdcAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y']);
        DB::commit();
        return $getAwbNumber;
    }
    public static function getDtdcSEAWBNumber($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = DtdcSEAwbNumbers::where('used', 'n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        DtdcSEAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }

    public static function getDtdcLLAWBNumber($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = DtdcLLAwbNumbers::where('used', 'n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        DtdcLLAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }

    public static function createAmazonDirectOrderFromReport($reportData, Channels $channel) {
        $totalOrders = count($reportData);
        $successfullyImported = 0;

        $warehouse = Warehouses::where('seller_id', $channel->seller_id)->where('default', 'y')->first();
        if(empty($warehouse)) {
            return [
                'status' => false,
                'message' => 'Default warehouse not found.',
                'data' => [
                    'totalOrders' => $totalOrders,
                    'successfullyImported' => $successfullyImported,
                    'failed' => $totalOrders - $successfullyImported,
                ]
            ];
        }

        $lastSync = $channel->last_sync;
        foreach($reportData as $report) {
            try {
                if(now()->parse($report['purchase_date'])->gt(now()->parse($lastSync))) {
                    $lastSync = $report['purchase_date'];
                }

                // Ignore orders where mobile number is not available or payment mode is cod
                if(in_array($report['ship_phone_number'], ['9999999999', '0000000000']) || strtoupper($report['payment_method'] ?? "prepaid") == 'COD') {
                    throw new Exception('Invalid order');
                }
                if(strlen($report['ship_phone_number']) != 10 || in_array(substr($report['ship_phone_number'],0,1),["1","2","3","4","5"])){
                    $report['ship_phone_number'] = null;
                }
                $report['ship_phone_number'] = str_replace(' ','',$report['ship_phone_number']);
                $report['ship_phone_number'] = substr($report['ship_phone_number'],-10);
                $order = Order::create([
                    'order_number' => $report['order_id'] ?? null,
                    'customer_order_number' => $report['order_id'] ?? null,
                    'channel_id' => $report['order_id'] ?? null,
                    'o_type' => "forward",
                    'seller_id' => $channel->seller_id,
                    'order_type' => strtoupper($report['payment_method'] ?? 'prepaid') == 'COD' ? 'cod' : 'prepaid',
                    'b_customer_name' => $report['recipient_name'] ?? null,
                    'b_address_line1' => $report['ship_address_1'] ?? null,
                    'b_address_line2' => $report['ship_address_2'] ?? null,
                    'b_country' => $report['ship_country'] ?? null,
                    'b_state' => $report['ship_state'] ?? null,
                    'b_city' => $report['ship_city'] ?? null,
                    'b_pincode' => $report['ship_postal_code'] ?? null,
                    'b_contact' => $report['ship_phone_number'] ?? null,
                    'b_contact_code' => '91',
                    's_customer_name' => $report['recipient_name'] ?? null,
                    's_address_line1' => $report['ship_address_1'] ?? null,
                    's_address_line2' => $report['ship_address_2'] ?? null,
                    's_country' => $report['ship_country'] ?? null,
                    's_state' => $report['ship_state'] ?? null,
                    's_city' => $report['ship_city'] ?? null,
                    's_pincode' => $report['ship_postal_code'] ?? null,
                    's_contact' => $report['ship_phone_number'] ?? null,
                    's_contact_code' => '91',
                    'delivery_address' => ($report['ship_address_1'] ?? null)." ".($report['ship_address_2'] ?? null). " ". ($report['ship_city'] ?? null)." ".($report['ship_state'] ?? null)." ".($report['ship_postal_code'] ?? null),
                    'p_warehouse_name' => $warehouse->warehouse_name,
                    'p_customer_name' => $warehouse->contact_name,
                    'p_address_line1' => $warehouse->address_line1,
                    'p_address_line2' => $warehouse->address_line2,
                    'p_country' => $warehouse->country,
                    'p_state' => $warehouse->state,
                    'p_city' => $warehouse->city,
                    'pickup_address' => $warehouse->address_line1 . "," . $warehouse->address_line2 . "," . $warehouse->city . "," . $warehouse->state. "," . $warehouse->pincode,
                    'warehouse_id' => $warehouse->id,
                    'p_pincode' => $warehouse->pincode,
                    'p_contact' => $warehouse->contact_number,
                    'p_contact_code' => $warehouse->code,
                    'weight' => 100, // In gram
                    'height' => 10, // In cm
                    'length' => 10,
                    'breadth' => 10,
                    'vol_weight' => 200,
                    'shipping_charges' => 0,
                    'cod_charges' => 0,
                    'discount' => 0,
                    'invoice_amount' => round($report['invoice_amount'], 2),
                    'channel' => 'amazon_direct',
                    'inserted' => date('Y-m-d H:i:s', strtotime($report['purchase_date'])),
                    'inserted_by' => $channel->seller_id,
                    'seller_channel_id' => $channel->id,
                    'seller_channel_name' => $channel->channel_name,
                    'product_sku' => $report['product_sku'],
                    'product_name' => $report['product_name'],
                    'product_qty' => $report['product_qty'],
                    'imported' => now(),
                ]);
                foreach($report['products'] as $product) {
                    Product::create([
                        'order_id' => $order->id,
                        'product_sku' => $product['sku'],
                        'product_name' => $product['product_name'],
                        'product_unitprice' => round($product['item_price'] ?? 0, 2),
                        'product_qty' => $product['quantity_purchased'],
                        'item_id' => $product['order_item_id'] ?? null,
                        'total_amount' => round($product['item_price'] ?? 0, 2),
                    ]);
                }
                $successfullyImported++;
            } catch(Exception $e) {
                continue;
            }
        }
        // Update last sync
        $channel->last_sync = $lastSync;
        $channel->save();
        if($successfullyImported > 0) {
            return [
                'status' => true,
                'message' => "Total {$successfullyImported} out of {$totalOrders} imported successfully.",
                'data' => [
                    'totalOrders' => $totalOrders,
                    'successfullyImported' => $successfullyImported,
                    'failed' => $totalOrders - $successfullyImported,
                ]
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Orders not imported.',
                'data' => [
                    'totalOrders' => $totalOrders,
                    'successfullyImported' => $successfullyImported,
                    'failed' => $totalOrders - $successfullyImported,
                ]
            ];
        }
    }

    public static function PerformCancellation($sellerData,$order,$source = 'web',$checkSeller=true){
        TrackingHelper::PerformTracking($order);
        self::CheckAndCreateLog($sellerData,$order,'cancel');
        $order = Order::find($order->id);
        if($order->status == 'out_for_delivery'){
            return false;
        }
        if (strtolower($order->status) == 'pending') {
            $order->status = 'cancelled';
            $order->modified = date('Y-m-d H:i:s');
            $order->modified_by = $order->seller_id;
            $order->save();
            return true;
        } elseif (in_array(strtolower($order->status), ['shipped', 'manifested','pickup_requested', 'pickup_scheduled'])) {
            $oldStatus = $order->status;
            $order->status = 'cancelled';
            $order->modified = date('Y-m-d H:i:s');
            $order->modified_by = $order->seller_id;
            $order->save();
            // here amount will be credited for the seller
            self::refundSellerForCancelledOrder($order,$oldStatus,$source);
            // cancel order from courier partner
            self::CancelOrderFromCourier($order);
            // Remove From Manifestation
            self::RemoveFromManifestation($order);
            return true;
        } elseif (strtolower($order->status) == 'delivered') {
            return false;
        } elseif (strtolower($order->status) == 'cancelled') {
            self::CancelOrderFromCourier($order);
            return true;
        } else {
            // deduct RTO charges
            self::DeductRTOCharge($order);
            // mark RTO from courier
            self::CancelOrderFromCourier($order);
            // Remove From Manifestation
            self::RemoveFromManifestation($order);
        }
        return true;
    }
    public static function CancelOrderFromCourier($order){
        $returnValue = true;
        $awb = $order->awb_number;
        $order_type = $order->o_type;
        $sellerDetail = Seller::find($order->seller_id);
        $shipping = new ShippingController();
        switch ($order->courier_partner) {
            case 'ekart':
            case 'ekart_2kg':
            case 'ekart_1kg':
            case 'ekart_3kg':
            case 'ekart_5kg':
                $ekart = new Ekart();
                $ekart->cancelOrder($awb);
                break;
            case 'delhivery_surface':
            case 'delhivery_surface_2kg':
                $delhiveryClient = new Delhivery('surface');
                $delhiveryClient->CancelOrder($order);
            case 'delhivery_air':
                $delhiveryClient = new Delhivery('air');
                $delhiveryClient->CancelOrder($order);
                break;
            case 'delhivery_surface_5kg':
                $delhiveryClient = new Delhivery('five');
                $delhiveryClient->CancelOrder($order);
                break;
            case 'delhivery_surface_10kg':
                $delhiveryClient = new Delhivery('ten');
                $delhiveryClient->CancelOrder($order);
                break;
            case 'shadowfax':
                $shadowfax = new Shadowfax();
                $shadowfax->cancelOrder($awb,"Seller Cancellation");
                break;
            case 'bluedart':
            case 'bluedart_surface':
                $customCredentials = ShippingHelper::CheckSellerCustomChannel($order->courier_partner,$sellerDetail->id);
                $shipping->_cancelBlueDartOrder($order,$customCredentials);
                break;
            case 'smc_new':
            case 'smc_2kg':
            case 'smc_5kg':
            case 'smc_air':
            case 'smc_air_2kg':
                $responseData = SMCNew::CancelOrder($order->awb_number);
                if(!empty($responseData['status']) && $responseData['status'] == "OK")
                    return true;
                else
                    return false;
            case 'xpressbees_surface_3kg':
            case 'xpressbees_sfc':
            case 'xpressbees_surface_1kg':
                $obj = new XpressBees('three');
                $obj->CancelOrder($order->awb_number);
                $returnValue = true;
                break;
            default:
                $returnValue = false;
                break;
        }
        return $returnValue;
    }
    public static function refundSellerForCancelledOrder($order,$oldStatus,$orderSource){
        if ($order->awb_number) {
            OrderTracking::create([
                'awb_number' => $order->awb_number,
                'status_code' => 'CAN',
                'status' => 'CANCELLED',
                'status_description' => 'ORDER CANCELLED BY SELLER',
                'remarks' => 'ORDER CANCELLED BY SELLER',
                'location' => 'NA',
                'updated_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        $seller = Seller::find($order->seller_id);
        $data = [
            'seller_id' => $order->seller_id,
            'order_id' => $order->id,
            'amount' => $oldStatus == 'shipped' ? $order->total_charges : ($order->total_charges - $order->other_charges),
            'balance' => $oldStatus == 'shipped' ? ($seller->balance + $order->total_charges) : ($seller->balance + $order->total_charges - $order->other_charges),
            'type' => 'c',
            'redeem_type' => 'o',
            'datetime' => date('Y-m-d H:i:s'),
            'method' => 'wallet',
            'description' => 'Order Cancel Charge Reversal'
        ];
        $resp = Transactions::where('seller_id', $data['seller_id'])->where('order_id', $data['order_id'])->where('type', $data['type'])->where('amount', $data['amount'])->count();
        if (intval($resp) == 0) {
            Transactions::create($data);
            Seller::where('id', $order->seller_id)->increment('balance', $data['amount']);
        }
        // store cancellation details
        $updateData = [
            'order_id' => $order->id,
            'cancel_source' => $orderSource,
            'cancel_datetime' => date('Y-m-d H:i:s'),
            'cancel_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
        ];
        $otherDetails = InternationalOrders::where('order_id',$order->id)->first();
        if(empty($otherDetails)){
            // create
            InternationalOrders::create($updateData);
        }
        else{
            //update
            InternationalOrders::where('id',$otherDetails->id)->update($updateData);
        }
    }
    public static function DeductRTOCharge($order){
        TrackingHelper::RTOOrder($order->id);
    }
    public static function CheckAndCreateLog($sellerSession,$order,$flag='ship'){
        if(empty($sellerSession))
            return false;
        if(($sellerSession->employee_flag_enabled ?? 'n') == 'y' && ($sellerSession->type ?? 'seller') == 'emp'){
            EmployeeWorkLogs::create([
                'order_id' => $order->id,
                'employee_id' => $sellerSession->emp_id ?? 0,
                'operation' => $flag,
                'inserted' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }
    public static function GetXbeesToken($username, $password, $secret)
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
        return $data['token'] ?? '';
    }
    public static function GetPartnerCategory($weight){
        if($weight <=500)
            return ['half_kg'];
        else if($weight > 500 && $weight <= 1000)
            return ['half_kg','one_kg'];
        else if($weight > 1000 && $weight <= 2500)
            return ['one_kg','two_kg'];
        else if($weight > 2500 && $weight <= 3000)
            return ['one_kg','two_kg','three_kg'];
        else if($weight > 3000 && $weight <= 5000)
            return ['one_kg','three_kg','five_kg'];
        else if($weight > 5000 && $weight <= 10000)
            return ['five_kg','six_kg','ten_kg'];
        else if($weight > 10000 && $weight <= 20000)
            return ['ten_kg','twenty_kg'];
        else if($weight > 20000)
            return ['twenty_kg'];
        else
            return ['half_kg'];
    }
    public static function PushWebHookStatusForCustomOrder($order,$status){
        $seller = Seller::find($order->seller_id);
        if(empty($seller))
            return false;
        if($seller->webhook_enabled == 'n')
            return false;
        $payload = self::GenerateWebhookPayload($order,$seller,$status);
        Logger::write("logs/webhook-push-".date('Y-m-d').'.text', [
            'title' => "Webhook Request for {$seller->code}-{$order->awb_number}",
            'data' => [
                'url' => $seller->webhook_url,
                'data' => $payload
            ]
        ]);
        $response = Http::withHeaders(['Api-Key' => $seller->webhook_api_key,'Content-Type' => 'application/json'])->post($seller->webhook_url,$payload);
        Logger::write("logs/webhook-push-".date('Y-m-d').'.text', [
            'title' => "Webhook Response for {$seller->code}-{$order->awb_number}",
            'data' => $response->json()
        ]);
        return true;
    }
    public static function GenerateWebhookPayload($order,$seller,$status){
        if($order->rto_status == 'y' && $status == 'delivered')
            $status = 'rto_delivered';
        if($order->rto_status == 'y' && $status == 'in_transit')
            $status = 'rto_in_transit';
        $orderTracking = OrderTracking::where('awb_number',$order->awb_number)->orderBy('id','desc')->first();
        $payload = [
            'awb' => $order->awb_number,
            'datetime' => date('Y-m-d H:i:s'),
            'status' => $status,
            'reason_code' => $status,
            'reason_code_number' => $status,
            'location' => $orderTracking->location ?? "NA",
            'employee' => '',
            'status_update_number' => '',
            'order_number' => $order->customer_order_number,
            'city' => $orderTracking->location ?? "NA",
            'ref_awb' => $order->alternate_awb_number ?? "NA",
            'remarks' => $orderTracking->remark ?? "NA",
            'product_type' => strtoupper($order->order_type),
            'edd' => $order->expected_delivery_date ?? date('Y-m-d H:i:s',strtotime("+5 days"))
        ];
        return $payload;
    }
    public static function GenerateManifest(array $orderIds,$sellerData) {
        // Get mps order ids
        $utilities = new Utilities();
        $tmpOrderId = [];
        foreach($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->whereNotIn('status', ['pending', 'cancelled'])->first();
            if($order == null) {
                continue;
            }
            if($order->shipment_type == 'mps') {
                $childOrders = Order::where('parent_id', $order->parent_id)
                    ->where('shipment_type', 'mps')
                    ->get();
                foreach($childOrders as $childOrder) {
                    $tmpOrderId[] = $childOrder->id;
                }
            } else {
                $tmpOrderId[] = $order->id;
            }
        }
        if(empty($tmpOrderId)) {
            return false;
        } else {
            $orderIds = $tmpOrderId;
        }
        $wareHouse = Warehouses::where('seller_id', $sellerData->id)->orderBy('default')->first();
        if (empty($wareHouse)) {
            foreach ($orderIds as $o){
                Order::where('id',$o)->whereIn('status',['shipped','pickup_requested'])->update(['manifest_status' => 'y','status' => 'manifested']);
            }
            return false;
        }
        $couriers = Order::select('courier_partner')->distinct('courier_partner')->where('seller_id', $sellerData->id)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
        $allManifest = [];
        $orderTracking = [];
        $setStatusManifestedIds = [];
        $manifestedOrderID = [];
        $sendSmsOrder = [];
        $sendWhatsAppOrder = [];
        foreach ($couriers as $c) {
            $rand = rand(1000, 9999);
            $data = array(
                'seller_id' => $sellerData->id,
                'courier' => $c->courier_partner,
                'status' => 'manifest_generated',
                'warehouse_name' => $wareHouse->warehouse_name,
                'warehouse_contact' => $wareHouse->contact_number,
                'warehouse_gst_no' => $wareHouse->gst_number,
                'warehouse_address' => $wareHouse->address_line1 . "," . $wareHouse->address_line2 . "," . $wareHouse->city . "," . $wareHouse->state . " - " . $wareHouse->pincode,
                'p_ref_no' => "TST$rand",
                'type' => "web",
                'created' => date('Y-m-d'),
                'created_time' => date('H:i:s')
            );
            if (count($res = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type', 'web')->where('seller_id', $sellerData->id)->get()) > 0) {
                $manifestId = $res[0]->id;
            }
            else {
                $manifestId = Manifest::create($data)->id;
            }
            $totalOrders = 0;
            $orders = Order::where('courier_partner', $c->courier_partner)->where('seller_id', $sellerData->id)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
            foreach ($orders as $o) {
                $allManifest[]=[
                    'manifest_id' => $manifestId,
                    'order_id' => $o->id
                ];
                //$res1 = ManifestOrder::where('manifest_id',$info['manifest_id'])->where('order_id',$info['order_id'])->first();
                //if(empty($res1)){
                //ManifestOrder::create($info);
                // create a order tracking for tracking the next order status
                $orderTracking[] =  ['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request','remarks' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s'),'created_at' => date('Y-m-d H:i:s')];
                //OrderTracking::create(['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request', 'remark' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s')]);
                //Order::where('id', $o->id)->update(['status' => 'manifested', 'manifest_status' => 'y']);
                if($o->status == 'shipped' || $o->status == 'pickup_requested') {
                    $setStatusManifestedIds[] = $o->id;
//                    $o->status = 'manifested';
                }

//                $o->manifest_status = 'y';
//                $o->save();
                $manifestedOrderID[] = $o->id;
                if ($sellerData->sms_service == 'y') {
//                    $utilities->send_sms($o);
                    $sendSmsOrder[] = $o->id;
                }
                if($sellerData->whatsapp_service == 1){
                    $sendWhatsAppOrder[] = $o->id;
                }
                $totalOrders++;

                if(count($orderTracking) == 500){
                    OrderTracking::insert($orderTracking);
                    $orderTracking = [];
                }
            }
            if (count($res) > 0)
                Manifest::where('id', $manifestId)->increment('number_of_order', $totalOrders);
            else
                Manifest::where('id', $manifestId)->update(array('number_of_order' => $totalOrders));
        }

        if(count($orderTracking) > 0){
            OrderTracking::insert($orderTracking);
        }

        if(count($setStatusManifestedIds) > 0)
            Order::whereIn('id',$setStatusManifestedIds)->update(['status' => 'manifested']);

        if(count($manifestedOrderID)> 0)
            Order::whereIn('id',$manifestedOrderID)->update(['manifest_status' => 'y']);

        if(count($sendSmsOrder) > 0){
            $jobDetails = [
                'order_count' => count($sendSmsOrder),
                'order_ids' => implode(",",$sendSmsOrder),
                'status' => 'pending',
                'seller_id' => $sellerData->id,
                'inserted' => date('Y-m-d H:i:s')
            ];
            $jobId = SendManifestationSmsJob::create($jobDetails)->id;
            SendManifestationSms::dispatchAfterResponse($jobId);
        }

        if(count($sendWhatsAppOrder) > 0){
            $jobDetails = [
                'order_count' => count($sendWhatsAppOrder),
                'order_ids' => implode(",",$sendWhatsAppOrder),
                'status' => 'pending',
                'seller_id' => $sellerData->id,
                'inserted' => date('Y-m-d H:i:s')
            ];
            $jobId = SendManifestationWhatsAppJob::create($jobDetails)->id;
            SendManifestationWhatsApp::dispatchAfterResponse($jobId);
        }

        ManifestOrder::insert($allManifest);
        return true;
    }
    public static function GenerateManifestOrderAPI($orderId, $sellerId) {
        $courier = Order::select('courier_partner')->where('seller_id', $sellerId)->whereNotIn('status',['pending','cancelled','delivered'])->where('manifest_status', 'n')->where('id', $orderId)->first();
        if(!empty($courier)){
            $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
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
    public static function GenerateManifestOrderWeb($orderId, $sellerId) {
        $courier = Order::select('courier_partner')->where('seller_id', $sellerId)->whereNotIn('status',['pending','cancelled','delivered'])->where('manifest_status', 'n')->where('id', $orderId)->first();
        if(!empty($courier)){
            $wareHouse = Warehouses::where('seller_id', $sellerId)->orderBy('default', 'desc')->first();
            if(empty($wareHouse)) {
                Order::where('id',$orderId)->update(['manifest_status' => 'y','status' => 'manifested']);
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
                    'type' =>  "web",
                    'created' => date('Y-m-d'),
                    'created_time' => date('H:i:s')
                ];
                if(count($manifest = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type','web')->where('seller_id', $sellerId)->get()) > 0) {
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
    public static function ReverseCancellation($orderData,$status){
        OrderTracking::create([
            'awb_number' => $orderData->awb_number,
            'status_code' => 'CAN-REV',
            'status' => $status,
            'status_description' => 'Order Cancellation Reversal',
            'remarks' => 'Order Cancellation Reversal',
            'location' => 'NA',
            'updated_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $seller = Seller::find($orderData->seller_id);
        if(!($orderData->seller_id == 16 && (str_starts_with($orderData->courier_partner,'dtdc') || $orderData->courier_partner == 'xpressbees_sfc')))
        {
            $data = [
                'seller_id' => $orderData->seller_id,
                'order_id' => $orderData->id,
                'amount' => $orderData->total_charges,
                'balance' => $seller->balance - $orderData->total_charges,
                'type' => 'd',
                'redeem_type' => 'o',
                'datetime' => date('Y-m-d H:i:s'),
                'method' => 'wallet',
                'description' => 'Order Cancel Charge Reversal'
            ];
            Transactions::create($data);
            Seller::where('id', $orderData->seller_id)->decrement('balance', $data['amount']);
        }
        $orderData->status = $status;
        $orderData->save();
        return true;
    }
    public static function GenerateArchivePassword(){
        $year = date('Y');
        $dayName = date('D');
        $hour = date('H');
        $minute = date('i');
        return "SHE-ARC-{$year}-{$dayName}-{$hour}-{$minute}";
    }
    public static function RemoveFromManifestation($orderData){
        $manifestOrder = ManifestOrder::where('order_id',$orderData->id)->first();
        if(empty($manifestOrder))
            return true;
        $manifest = Manifest::find($manifestOrder->manifest_id);
        if(empty($manifest)) {
            $manifestOrder->delete();
            return true;
        }
        if($manifest->number_of_order == 1){
            $manifest->delete();
            $manifestOrder->delete();
        }else{
            $manifestOrder->delete();
            $manifest->number_of_order -= 1;
            $manifest->save();
        }
        return true;
    }
    public static function GenerateEcomExpressAwbNumber($courierPartner,$orderType,$sellerId=0){
        DB::beginTransaction();
        $awb = EcomExpressAwbs::where('used', 'n')->where('courier_partner', $courierPartner)->where('order_type', $orderType)->lockForUpdate()->first();
        if(empty($awb)){
            if($courierPartner == 'ecom_express_3kg'){
                $client = new EcomExpress3kgController();
            }else{
                $client = new EcomExpressController();
            }
            $client->fetchAirwayBillNumbers($orderType);
        }
        $awb = EcomExpressAwbs::where('used', 'n')->where('courier_partner', $courierPartner)->where('order_type', $orderType)->lockForUpdate()->first();
        if(empty($awb))
        {
            DB::rollBack();
            return null;
        }
        EcomExpressAwbs::where('id', $awb->id)->update(['used' => 'y', 'used_by' => $sellerId, 'used_time' => date('Y-m-d H:i:s')]);
        DB::commit();
        return $awb;
    }

    public static function getProfessionalAWBNumber($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = ProfessionalAwbNumbers::where('used', 'n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        ProfessionalAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }
    public static function CreateDelhiveryWarehouse($warehouse,$accessKey){
        try{
            $payload = [
                'phone' => $warehouse->contact_number,
                'city' => $warehouse->city,
                'name' => $warehouse->warehouse_code,
                'pin' => $warehouse->pincode,
                'address' => preg_replace('/[^A-Za-z0-9\ \,\-]/', ' ', $warehouse->address_line1),
                'country' => "India",
                'email' => $warehouse->support_email,
                'registered_name' => $warehouse->warehouse_code,
                'return_address' => preg_replace('/[^A-Za-z0-9\ \,\-]/', ' ', $warehouse->address_line1),
                'return_pin' => $warehouse->pincode,
                'return_city' => $warehouse->city,
                'return_state' => $warehouse->state,
                'return_country' => "India"
            ];
            $response = Http::withHeaders([
                'Authorization' => "Token {$accessKey}",
                'Content-Type' => 'application/json'
            ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);
        }
        catch(Exception $e){}
    }
    public static function isValidDateTime($dateTimeString, $format = 'Y-m-d H:i:s') {
        $dateTime = DateTime::createFromFormat($format, $dateTimeString);
        return $dateTime && $dateTime->format($format) === $dateTimeString;
    }
    public static function GetProfessionalId(){
        $configData = Configuration::find(1);
        $sendId = $configData->professional_webhook_id;
        $configData->professional_webhook_id++;
        $configData->save();
        return $sendId ?? 10000;
    }

    public static function GetMasterPassword(){
        return "Twin@".date('M#Y')."!Ship";
    }

    public static function getPickDelAWBNumber($sellerId=0){
        DB::beginTransaction();
        $getAwbNumber = PickDelAwbNumbers::where('used', 'n')->lockForUpdate()->first();
        if (empty($getAwbNumber)) {
            DB::rollBack();
            return false;
        }
        PickDelAwbNumbers::where('id', $getAwbNumber->id)->update(['used' => 'y','used_time' => date('Y-m-d H:i:s'),'seller_id' => $sellerId]);
        DB::commit();
        return $getAwbNumber;
    }

    public static function CheckPickNDelNCRCity($p_city,$s_city){
        if(in_array(strtolower($p_city),self::ncrArray) && in_array(strtolower($s_city),self::ncrArray)){
            return true;
        }
        elseif (strtolower($p_city) == strtolower($s_city))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
