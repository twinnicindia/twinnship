<?php

namespace App\Http\Controllers;

use App\Helpers\TrackingHelper;
use App\Libraries\MyUtility;
use App\Libraries\SMCNew;
use App\Models\BluedartWebHookResponse;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\COD_transactions;
use App\Models\Configuration;
use App\Models\CourierMissStatusCode;
use App\Models\Coverage;
use App\Models\CourierUnorganisedTracking;
use App\Models\DtdcAwbNumbers;
use App\Models\DtdcLLAwbNumbers;
use App\Models\DTDCPushLogData;
use App\Models\DtdcSEAwbNumbers;
use App\Models\PickNDelWebHookResponse;
use App\Models\ProfessionalAwbNumbers;
use App\Models\MarutiEcomAwbs;
use App\Models\Features;
use App\Models\Logistics;
use App\Models\Ndrattemps;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\Slider;
use App\Models\SMCNewAWB;
use App\Models\Socials;
use App\Models\Stats;
use App\Models\Steps;
use App\Models\Testimonial;
use App\Models\Why_choose;
use App\Models\OrderTracking;
use App\Models\Partners;
use App\Models\RecommendationEngine;
use App\Models\Seller;
use App\Models\XbeesAwbnumber;
use App\Models\ZZExceptionLogs;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Logger;

class WebController extends Controller
{
    protected $info, $utilities,$easyEcomStatus;
    public function __construct()
    {
        $this->info['config'] = Configuration::find(1);
        $this->info['links'] = Socials::where('status', 'y')->get();
        $this->utilities = new Utilities();
        $this->easyEcomStatus=[
            "pending" => 2,
            "shipped" => 2,
            "manifested" => 2,
            "pickup_scheduled" => 18,
            "picked_up" => 19,
            "cancelled" => 6,
            "in_transit" => 2,
            "out_for_delivery" => 2,
            "rto_initated" => 17,
            "rto_delivered" => 9,
            "delivered" => 3,
            "ndr" => 16,
            "lost" => 2,
            "damaged" => 2
        ];
    }

    function index()
    {
        $data = $this->info;
        $data['slider'] = Slider::where('status', 'y')->get();
        $data['feature'] = Features::where('status', 'y')->get();
        $data['why'] = Why_choose::where('status', 'y')->get();
        $data['logistics'] = Logistics::where('status', 'y')->orderBy('position')->get();
        $data['channel'] = Channel_partners::where('status', 'y')->orderBy('position')->get();
        $data['brands'] = Brands::where('status', 'y')->orderBy('position')->get();
        $data['coverage'] = Coverage::where('status', 'y')->get();
        $data['testimonial'] = Testimonial::where('status', 'y')->get();
        $data['links'] = Socials::where('status', 'y')->get();
        $data['steps'] = Steps::where('status', 'y')->get();
        $data['stats'] = Stats::where('status', 'y')->get();
        return view('web.home', $data);
    }
    function about()
    {
        // echo $this->utilities->send_email('deepakprn78@gmail.com','Twinnship','Recharge Done',"This is the test message"); exit;
        $data = $this->info;
        return view('web.about', $data);
    }
    function send_test_email(){
        $utilities=new Utilities();
        $utilities->send_email('dnsdeepak78@gmail.com',env('appTitle'),'Deepak Prajapati','<h1>Hello</h1><p>Please have a look for this email I have tried very hard to send it</p>',"This is the Subject");
    }
    function privacy()
    {
        $data = $this->info;
        return view('web.privacy', $data);
    }
    function terms()
    {
        $data = $this->info;
        return view('web.terms', $data);
    }
    function export_xml()
    {
        $xml = simplexml_load_file('books.xml') or die('Error Reading Response');
        print_r($xml);
        foreach ($xml->book as $b)
            echo $b->title;
    }

    function pricing()
    {
        $data = $this->info;
        return view('web.pricing', $data);
    }

    function table_pricing()
    {
        $data = $this->info;
        return view('web.table_pricing', $data);
    }

    function order_track()
    {
        $data = $this->info;
        return view('web.track_order', $data);
    }

    function ndr_management()
    {
        $data = $this->info;
        return view('web.ndr_management', $data);
    }

    function postpaid()
    {
        $data = $this->info;
        return view('web.postpaid', $data);
    }

    function early_cod()
    {
        $data = $this->info;
        return view('web.early_cod', $data);
    }
    function recommendation_engine()
    {
        $data = $this->info;
        $data['recon_engine'] = RecommendationEngine::where('status','y')->get();
        return view('web.recommendation_engine', $data);
    }

    function tracking($awb)
    {
        $data = $this->info;
        //$statusList = ['delivered','pending','cancelled'];
        $data['order'] = Order::where('awb_number', $awb)->first();
        if (empty($data['order'])) {
            // $this->utilities->generate_notification('error', "AWB Number not Found in our System", 'error');
            return back();
        } else {
            $courier  = $data['order']->courier_partner;
            TrackingHelper::PerformTracking($data['order']);
            $data['order_tracking'] = OrderTracking::where('awb_number', $awb)->get();
            $data['partner'] = Partners::where('keyword', $courier)->first();
            return view('web.tracking', $data);
        }
    }
    function tracking_clone($awb)
    {
        $data = $this->info;
        $data['order'] = Order::where('awb_number', $awb)->first();
        if (empty($data['order'])) {
            // $this->utilities->generate_notification('error', "AWB Number not Found in our System", 'error');
            return back();
        } else {
            $courier  = $data['order']->courier_partner;
            TrackingHelper::PerformTracking($data['order']);
            $data['order_tracking'] = OrderTracking::where('awb_number', $awb)->get();
            $data['partner'] = Partners::where('keyword', $courier)->first();
            return view('web.tracking', $data);
        }
    }

    function single_order_track(Request $request)
    {
        // dd($request->all());
        $track = Order::where('awb_number', $request->awb_number)->count();
        if ($track > 0) {
            return  redirect()->route('web.track_order', $request->awb_number);
        } else {
            $this->utilities->generate_notification('Error', "AWB Number not Found in our System", 'error');
            return redirect()->back();
        }
        // dd($track);
    }

    function trackOrderCustom(Request $request)
    {
        try {
            $startedAt = now();
            $cronName = 'track-order-custom';
            $totalExecuted = 0;
            $totalSucceeded = 0;
            $totalSkipped = 0;
            $startedAt = now();
            $seven_days = \Carbon\Carbon::now()->subDays(70)->format("Y-m-d H:i:s");
            //$toBeSkipped = 'ekart','ekart_1kg','ekart_2kg','ekart_3kg','ekart_5kg','amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','dtdc_express','dtdc_surface','dtdc_1kg','dtdc_2kg','dtdc_3kg','dtdc_5kg','dtdc_6kg','dtdc_10kg';
            $query = Order::where('manifest_status','y')->whereNotIn('status',['pending','cancelled','delivered','shipped','pickup_requested','lost','damaged'])->where('awb_assigned_date','>=',$seven_days)->whereNotIn('courier_partner',['amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','shree_maruti_ecom','shree_maruti_ecom_1kg','shree_maruti_ecom_3kg','shree_maruti_ecom_5kg','shree_maruti_ecom_10kg']);
            if(!empty($request->sellerId))
                $query = $query->where('seller_id',$request->sellerId);
            if(!empty($request->awbNumbers))
                $query = $query->whereIn('awb_number',explode(',',$request->awbNumbers));
            if(!empty($request->courierPartner))
                $query = $query->where('courier_partner',$request->courierPartner);
            $orders = $query->orderBy('last_sync')->limit(1200)->get();
//            $query = "select * from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped','pickup_requested','lost','damaged') and `awb_assigned_date` >= '$seven_days' and courier_partner not in('ekart','ekart_1kg','ekart_2kg','ekart_3kg','ekart_5kg','amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','shree_maruti_ecom','shree_maruti_ecom_1kg','shree_maruti_ecom_3kg','shree_maruti_ecom_5kg','shree_maruti_ecom_10kg') order by last_sync limit 1200";
//            $orders=DB::select($query);
            // $orders=DB::select("select * from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped') and awb_number = '2804227350'");
            $orderIds = [];
            foreach ($orders as $o) {
                $totalExecuted++;
                if($startedAt->diffInSeconds(now()) >= 1140) {
                    throw new Exception('Time limit exceeded');
                }
                try{
                    $orderIds[] = $o->id;
                    $isSucceeded = TrackingHelper::PerformTracking($o);
                    if($isSucceeded){
                        $totalSucceeded++;
                    }
                    else
                        $totalSkipped++;
                }catch(Exception $e){
                    ZZExceptionLogs::create(['order_id' => $o->id,'awb_number' => $o->awb_number,'seller_id' =>$o->seller_id,'courier_partner' => $o->courier_partner,'exception_message' => $e->getMessage()." - ".$e->getLine(),'inserted' => date('Y-m-d H:i:s')]);
                    continue;
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage()."-".$e->getFile()."-".$e->getLine(), $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    function trackingSecondJob()
    {
        try {
            $startedAt = now();
            $cronName = 'track-order-second-job';
            $totalExecuted = 0;
            $totalSucceeded = 0;
            $totalSkipped = 0;
            $startedAt = now();
            $datetime = \Carbon\Carbon::now()->subHours(1)->format("Y-m-d H:i:s");
            $seven_days = \Carbon\Carbon::now()->subDays(70)->format("Y-m-d H:i:s");
//            $orders=DB::select("select id,awb_number,courier_partner,rto_status,ndr_status,seller_id,o_type,order_type,invoice_amount,ndr_status_date,reason_for_ndr,expected_delivery_date,alternate_awb_number,seller_order_type,is_alpha from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped','pickup_requested','lost','damaged') and `awb_assigned_date` >= '$seven_days' and ((courier_partner not in('ekart','ekart_1kg','ekart_2kg','ekart_3kg','ekart_5kg','amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','shree_maruti_ecom','shree_maruti_ecom_1kg','shree_maruti_ecom_3kg','shree_maruti_ecom_5kg','shree_maruti_ecom_10kg','dtdc','dtdc_1kg','dtdc_2kg','dtdc_5kg','dtdc_6kg','smartr') and is_custom = 0) or is_custom=1 ) order by last_sync limit 1200 offset 1200");
            $orders=DB::select("select id from `orders` where `manifest_status`='y' and manifest_sent = 'y' and `status` not in('pending','cancelled','delivered','shipped','pickup_requested','lost','damaged') and `awb_assigned_date` >= '$seven_days' and ((courier_partner not in('ekart','ekart_1kg','ekart_2kg','ekart_3kg','ekart_5kg','amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','shree_maruti_ecom','shree_maruti_ecom_1kg','shree_maruti_ecom_3kg','shree_maruti_ecom_5kg','shree_maruti_ecom_10kg','dtdc','dtdc_1kg','dtdc_2kg','dtdc_5kg','dtdc_6kg','smartr','bluedart','bluedart_surface') and is_custom = 0) or is_custom=1 ) order by last_sync limit 1200 offset 1200");
            $orderIds = [];
            foreach ($orders as $singleOrder) {
                $o = Order::find($singleOrder->id);
                $totalExecuted++;
                if($startedAt->diffInSeconds(now()) >= 1140) {
                    throw new Exception('Time limit exceeded');
                }
                try{
                    if(empty($o))
                        continue;
                    $orderIds[] = $o->id;
                    $isSucceeded = TrackingHelper::PerformTracking($o);
                    if($isSucceeded){
                        $totalSucceeded++;
                    }
                    else
                        $totalSkipped++;
                }catch(Exception $e){
                    //  dd($e->getMessage(), $e->getLine(), $e, $o);
                    ZZExceptionLogs::create(['order_id' => $o->id,'awb_number' => $o->awb_number,'seller_id' =>$o->seller_id,'courier_partner' => $o->courier_partner,'exception_message' => $e->getMessage()." - ".$e->getLine(),'inserted' => date('Y-m-d H:i:s')]);
                    continue;
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function track_order_for_sync()
    {
        try {
            $startedAt = now();
            $cronName = 'track-order';
            $totalExecuted = 0;
            $totalSucceeded = 0;
            $totalSkipped = 0;
            $startedAt = now();
            $seven_days = \Carbon\Carbon::now()->subDays(70)->format("Y-m-d H:i:s");
            $query = "select * from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped','pickup_requested','lost','damaged') and `awb_assigned_date` >= '$seven_days' order by last_sync limit 900";
            $orders=DB::select($query);
            // $orders=DB::select("select * from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped') and awb_number = '2804227350'");
            $orderIds = [];
            foreach ($orders as $o) {
                $totalExecuted++;
                if($startedAt->diffInSeconds(now()) >= 1140) {
                    throw new Exception('Time limit exceeded');
                }
                try{
                    $orderIds[] = $o->id;
                    $isSucceeded = TrackingHelper::PerformTracking($o);
                    if($isSucceeded){
                        $totalSucceeded++;
                    }
                    else
                        $totalSkipped++;
                }catch(Exception $e){
                    ZZExceptionLogs::create(['order_id' => $o->id,'awb_number' => $o->awb_number,'seller_id' =>$o->seller_id,'courier_partner' => $o->courier_partner,'exception_message' => $e->getMessage()." - ".$e->getLine(),'inserted' => date('Y-m-d H:i:s')]);
                    continue;
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function storeXbeesAwbNumber()
    {
        $data = @file_get_contents("http://localhost/Twinnship/assets/awbs.json");
        $response = json_decode($data);
        $awbNumbers = [];
        foreach ($response->AWBNoSeries as $a) {
            $awbNumbers[] = [
                'awb_number' => $a
            ];
            if (count($awbNumbers) == 2500) {
                XbeesAwbnumber::insert($awbNumbers);
                $awbNumbers = [];
            }
        }
        XbeesAwbnumber::insert($awbNumbers);
    }


    //Generate AWB Number for Forward Order
    function getAwbNumbersXbees()
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => 'FORWARD',
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => 'kEVUGEG3450nSssVzZQ'
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);

        $awb_data = $response->json();
        $this->_FetchAllAwbs($awb_data['BatchID']);
    }

    function _FetchAllAwbs($batch)
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => 'FORWARD',
            'BatchID' => $batch
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => 'kEVUGEG3450nSssVzZQ'
        ])->post('http://xbclientapi.xbees.in/TrackingService.svc/GetAWBNumberGeneratedSeries', $data);

        $awb_data = $response->json();
        if ($awb_data['ReturnCode'] == 100) {
            $insData = [];
            foreach ($awb_data['AWBNoSeries'] as $awb) {
                $insData[] = [
                    'awb_number' => $awb,
                    'batch_number' => $awb_data['BatchID']
                ];
                if (count($insData) == 2500) {
                    XbeesAwbnumber::insert($insData);
                    $insData = [];
                }
            }
            XbeesAwbnumber::insert($insData);
        }
    }


    //Generate AWB Number for Reverse Order
    function getAwbNumbersXbeesReverse()
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => 'REVERSE',
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => 'kEVUGEG3450nSssVzZQ'
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);

        $awb_data = $response->json();
        $this->_FetchAllAwbsReverse($awb_data['BatchID']);
    }

    function _FetchAllAwbsReverse($batch)
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => 'REVERSE',
            'BatchID' => $batch
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => 'kEVUGEG3450nSssVzZQ'
        ])->post('http://xbclientapi.xbees.in/TrackingService.svc/GetAWBNumberGeneratedSeries', $data);

        $awb_data = $response->json();
        if ($awb_data['ReturnCode'] == 100) {
            $insData = [];
            foreach ($awb_data['AWBNoSeries'] as $awb) {
                $insData[] = [
                    'order_type' => 'reverse',
                    'awb_number' => $awb,
                    'batch_number' => $awb_data['BatchID']
                ];
                if (count($insData) == 2500) {
                    XbeesAwbnumber::insert($insData);
                    $insData = [];
                }
            }
            XbeesAwbnumber::insert($insData);
        }
    }


    function newsletter(Request $request)
    {
        $data = Newsletter::where('email', $request->email)->count();
        if ($data > 0) {
            $this->utilities->generate_notification('Success', "You already Subscribed Twinnship", 'success');
        } else {
            Newsletter::create(['email' => $request->email]);
            $this->utilities->generate_notification('Success', "You Successfully Subscribed Twinnship", 'success');
        }
        return back();
    }

    function test_sms()
    {
        $o = Order::find('276048');
        $seller = Seller::select('sms_service')->where('id', $o->seller_id)->first();
        if (!empty($seller)) {
            if ($seller->sms_service == 'y') {
                $this->utilities->send_sms($o);
            }
        }
    }
    function testPush(Request $request){
        $order=Order::where('awb_number',$request->awb)->first();
        TrackingHelper::PushChannelStatus($order,$request->status);
    }

    // Track delhivery order webhook
    function trackDelhiveryOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/delhivery/delhivery-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all(),
            ]);
            // Auth
            if($request->header('Authorization') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            $data = $request->all();
            if (!empty($data['Shipment'])) {
                $order = Order::where('awb_number', $data['Shipment']['AWB'])->firstOrFail();
                if(strtolower($order->status) == 'delivered' || strtolower($order->status) == 'cancelled' || strtolower($order->status) == 'shipped' || strtolower($order->status) == 'pickup_requested') {
                    return response()->json([
                        'status' => true,
                        'message' => 'Tracking updated successfully.'
                    ]);
                }
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                $status = $data['Shipment']['Status']['Status'];
                $statusCode = $data['Shipment']['NSLCode'];
                $statusDateTime = $data['Shipment']['Status']['StatusDateTime'];
                $scanType = $data['Shipment']['Status']['StatusType'];
                $location = $data['Shipment']['Status']['StatusLocation'];
                $instruction = $data['Shipment']['Status']['Instructions'];
                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $statusCode) {
                        if($statusCode == 'X-PROM' || $statusCode == 'X-UNEX' || $statusCode == 'EOD-77')
                        {
                            Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => date('Y-m-d H:i:s',strtotime($statusDateTime))]);
                            TrackingHelper::PushChannelStatus($order,'picked_up');
                            TrackingHelper::CheckAndSendSMS($order);
                        }
                        else if(in_array($statusCode,['FMEOD-110','FMEOD-905','DTUP-214','FMEOD-109','PNP-101','ST-116','FMEOD-108','X-UCI','X-PNP','DTUP-205','FMEOD-152','FMPUR-101','DTUP-219','DTUP-210','FMEOD-103','X-ASP','X-DDD3FP'])){
                            if($order->status == 'manifested') {
                                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                                TrackingHelper::PushChannelStatus($order, 'pickup_scheduled');
                                TrackingHelper::CheckAndSendSMS($order);
                            }
                        }
                        else if(in_array($statusCode,['LT-100']))
                        {
                            Order::where('id', $order->id)->update(['status' => 'lost']);
                            TrackingHelper::PushChannelStatus($order,'lost');
                        }
                        else if(in_array($statusCode,['DLYMPS-101','EOD-43','EOD-3','EOD-148','EOD-6','DLYDC-107','DLYB2B-101','ST-117','EOD-73','ST-108','DLYB2B-108','ST-107','EOD-149','X-SC','ST-120','EOD-104','EOD-69','DLYRPC-417','X-PDASS','ST-118','ST-NTL','DLYLH-146','ST-NT']))
                        {
                            // Handle NDR Code Here
                            if($order->ndr_status != "y") {
                                //Order::where('id', $order->id)->update(['ndr_raised_time' => date('Y-m-d H:i:s'), 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['Instructions']]);
                                $ndrRaisedDate = date('Y-m-d H:i:s',strtotime($statusDateTime));
                                Order::where('id', $order->id)->update(['ndr_raised_time' => $ndrRaisedDate, 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $instruction]);
                                $attempt = [
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                                    'raised_time' => date('H:i:s'),
                                    'action_by' => 'Delhivery',
                                    'reason' => $instruction,
                                    'action_status' => 'pending',
                                    'remark' => 'pending',
                                    'u_address_line1' => 'new address line 1',
                                    'u_address_line2' => 'new address line 2',
                                    'updated_mobile' => ''
                                ];
                                Ndrattemps::create($attempt);
                            }
                            if($order->o_type == 'forward' && $scanType == 'RT') {
                                TrackingHelper::RTOOrder($order->id);
                                TrackingHelper::PushChannelStatus($order,'rto_initiated',$statusDateTime);
                            }
                            else
                                TrackingHelper::PushChannelStatus($order,'ndr',$statusDateTime);
                        }
                        else if($order->o_type == 'forward' && ($statusCode == 'EOD-6O'))
                        {
                            TrackingHelper::RTOOrder($order->id);
                            TrackingHelper::PushChannelStatus($order,'rto_initiated',$statusDateTime);
                        }
                        else if($statusCode == 'X-DDD3FD')
                        {
                            if ($order->rto_status != 'y') {
                                if ($order->ndr_status == 'y' && $statusDateTime != $order->ndr_status_date) {
                                    //make attempt here
//                                    $attempt = [
//                                        'seller_id' => $order->seller_id,
//                                        'order_id' => $order->id,
//                                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                        'raised_time' => date('H:i:s'),
//                                        'action_by' => 'Delhivery',
//                                        'reason' => $order->reason_for_ndr,
//                                        'action_status' => 'requested',
//                                        'remark' => 'requested',
//                                        'u_address_line1' => 'new address line 1',
//                                        'u_address_line2' => 'new address line 2',
//                                        'updated_mobile' => ''
//                                    ];
//                                    Ndrattemps::create($attempt);
                                    Order::where('id', $order->id)->update(['ndr_status_date' => $statusDateTime]);
                                }
                            }
                            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                            TrackingHelper::PushChannelStatus($order,'out_for_delivery');
                        }
                        else if($statusCode == 'DLYRG-120' || $statusCode == 'DTUP-209' || $statusCode == 'DTUP-ZL' || $statusCode == 'DLYRG-132' || $statusCode == 'S-TAT2' || $statusCode == 'DTUP-207' || $statusCode == 'DLYLH-115' || $statusCode == 'DLYLH-106' || $statusCode == 'PNP-102' || $statusCode == 'DLYLH-133' || $statusCode == 'DLYLH-104' || $statusCode == 'DLYRG-125' || $statusCode == 'X-PIOM' || $statusCode == 'X-PPOM' || $statusCode == 'X-DLL2F' || $statusCode == 'X-DBL1F' || $statusCode == 'CS-CSL' || $statusCode == 'X-IBD3F' || $statusCode == 'X-DBL2F' || $statusCode == 'DLYLH-105' || $statusCode == 'DOFF-128' || $statusCode == 'X-ILL1F' || $statusCode == 'X-DWS' || $statusCode == 'CS-101' || $statusCode == 'X-ILL2F' || $statusCode == 'DLYLH-126' || $statusCode == 'ST-114' || $statusCode == 'ST-115'  || $statusCode == 'DLYSHRTBAG-115' || $statusCode == 'S-MAR' || $statusCode == 'X-OLL2F' || $statusCode == 'DLYLH-152' || $statusCode == 'DTUP-204' || $statusCode == 'DLYDC-101' || $statusCode == 'EOD-86' || $statusCode == 'CS-104' || $statusCode == 'DLYMR-118' || $statusCode == 'DLYHD-007' || $statusCode == 'DLYRG-135' || $statusCode == 'DLYDG-120' || $statusCode == 'DLYDC-105' || $statusCode == 'DLYSOR-101' || $statusCode == 'S-MDIN')
                        {
                            if($order->status != 'in_transit'){
                                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                                TrackingHelper::PushChannelStatus($order,'in_transit');
                            }
                        }
                        else if($order->o_type == 'forward' && (in_array($statusCode,['RT-108','RT-113','RT-109','ST-102','RD-PD22','DTUP-235','RT-114','RT-101','RD-PD24','X-DDD3FD','EOD-148','ST-118','RD-PD23']))){
                            if($scanType == 'RT') {
                                TrackingHelper::RTOOrder($order->id);
                                TrackingHelper::PushChannelStatus($order,'lost',date('Y-m-d H:i:s',strtotime($statusDateTime)));
                            }
                        }
                        else if($statusCode == 'RD-AC' || $statusCode == 'RT-110')
                        {
                            if($order->o_type == 'forward') {
                                TrackingHelper::RTOOrder($order->id);
                                TrackingHelper::PushChannelStatus($order, 'rto_initiated', $statusDateTime);
                            }
                            if($scanType == 'DL'){
                                // mark shipment as rto delivered
                                $delivery_date = date('Y-m-d H:i:s',strtotime($statusDateTime));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                TrackingHelper::PushChannelStatus($order,'delivered');
                            }
                        }
                        else if($statusCode == 'EOD-38' || $statusCode ==  'EOD-135' || $statusCode == 'EOD-37' || $statusCode == 'EOD-36' || $statusCode == 'ED-100')
                        {
                            if($order->status == 'delivered')
                                return true;
                            $delivery_date = date('Y-m-d', strtotime($statusDateTime));
                            Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                            TrackingHelper::PushChannelStatus($order,'delivered');
                            TrackingHelper::CheckAndSendSMS($order);
                            if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                                $data = array(
                                    'seller_id' => $order->seller_id,
                                    'order_id' => $order->id,
                                    'amount' => $order->invoice_amount,
                                    'type' => 'c',
                                    'datetime' => $delivery_date,
                                    'description' => 'Order COD Amount Credited',
                                    'redeem_type' => 'o',
                                );
                                $resp = COD_transactions::where('seller_id',$order->seller_id)->where('order_id',$order->id)->first();
                                if(empty($resp)){
                                    COD_transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                                }
                            }
                        }
                        $data = [
                            "awb_number" => $order->awb_number,
                            "status_code" => $statusCode,
                            "status" => $status,
                            "status_description" => $instruction,
                            "remarks" =>  $instruction,
                            "location" =>  $location,
                            "updated_date" => $statusDateTime,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                    }
                }
                else {
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $statusCode,
                        "status" => $status,
                        "status_description" => $instruction,
                        "remarks" =>  $instruction,
                        "location" =>  $location,
                        "updated_date" => $statusDateTime,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                    if($scanType == 'RT'){
                        TrackingHelper::RTOOrder($order->id);
                        TrackingHelper::PushChannelStatus($order,'rto_initiated',null);
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/delhivery/delhivery-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Track DTDC Order Status WebHook
    function trackSmartrOrdersWebHook(Request $request){
        if($request->header('authorization') != "44592f55-7163-496d-b72a-daf4c38fc462")
            return response()->json(['status' => false,'message' => 'Unauthorized'],401);
        try{
            Logger::write('logs/partners/smartr/smartr-webhook-'.date('Y-m-d').'.text', [
                    'title' => 'Webhook Request Payload:',
                    'data' => $request->all()
                ]);
            $orderData = Order::where('awb_number',$request->awb_number)->first();
            $awbNumber = $request->awb_number;
            if(empty($orderData)){
                $awbNumber = str_replace("XSE-","",$request->ref_awb);
                $orderData = Order::where('awb_number',$awbNumber)->first();
            }
            if(empty($orderData))
                return response()->json(['status' => false,'awb' => $request->awb_number,'reason' => 'AWB does not exists','status_update_number' => rand(1000000,9999999)]);
            if($orderData->status == 'delivered' || $orderData->status == 'shipped' || $orderData->status == 'cancelled')
                return response()->json(['status' => true,'awb' => $request->awb_number,'status_update_number' => rand(1000000,9999999)]);
            SmartrWebhooksJobs::dispatchAfterResponse($request,$orderData,$awbNumber);
            return response()->json(['status' => true,'awb' => $request->awb_number,'status_update_number' => rand(1000000,9999999)]);
        }
        catch(Exception $e){
            Logger::write('logs/partners/smartr/smartr-webhook.text', [
                'title' => 'Check out error with this payload:',
                'data' => ['error' => $e->getMessage()." - ".$e->getFile()." - ".$e->getLine()],
            ]);
            return response()->json(['status' => 500,'message' => 'Something went wrong']);
        }
    }

//    function _HandleSmartrTracking($request,$orderData){
//        $datetime = date('Y-m-d H:i:s',strtotime($request->event_datetime));
//        $sellerData = Seller::find($orderData->seller_id);
//        switch($request->status_code){
//            case 'MAN':
//            case 'OFP':
//            case 'PKF':
//                // manifested
//                Order::where('id', $orderData->id)->update(['status' => 'pickup_scheduled']);
//                TrackingHelper::PushChannelStatus($orderData,'pickup_scheduled');
//                TrackingHelper::CheckAndSendSMS($orderData);
//                break;
//            case 'CAN':
//                MyUtility::PerformCancellation($sellerData,$orderData);
//                // cancelled
//                break;
//            case 'PKD':
//            case 'IND':
//                // picked up
//                Order::where('id', $orderData->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $datetime]);
//                TrackingHelper::PushChannelStatus($orderData,'picked_up');
//                TrackingHelper::CheckAndSendSMS($orderData);
//                break;
//            case 'DPD':
//            case 'ARD':
//            case 'RDC':
//            case 'ABD':
//                // In transit
//                Order::where('id', $orderData->id)->update(['status' => 'in_transit']);
//                TrackingHelper::PushChannelStatus($orderData,'in_transit');
//                TrackingHelper::CheckAndSendSMS($orderData);
//                break;
//            case 'OFD':
//                // out for delivery
//                if ($orderData->rto_status != 'y') {
//                    if ($orderData->ndr_status == 'y' && $datetime != $orderData->ndr_status_date) {
//                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $orderData->seller_id,
//                            'order_id' => $orderData->id,
//                            'raised_date' => date('Y-m-d', strtotime($orderData->ndr_status_date)),
//                            'raised_time' => date('H:i:s'),
//                            'action_by' => 'Smartr',
//                            'reason' => $orderData->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
//                        Order::where('id', $orderData->id)->update(['ndr_status_date' => $datetime]);
//                    }
//                }
//                $checkOfdExist = InternationalOrders::where('order_id',$orderData->id)->first();
//                if(empty($checkOfdExist)) {
//                    $ofdDate = [
//                        'order_id' => $orderData->id,
//                        'ofd_date' => $datetime
//                    ];
//                    InternationalOrders::create($ofdDate);
//                }
//                else{
//                    if(empty($checkOfdExist->ofd_date)){
//                        $ofdDate = [
//                            'ofd_date' => $datetime
//                        ];
//                        InternationalOrders::where('id',$checkOfdExist->id)->update($ofdDate);
//                    }
//                }
//                Order::where('id', $orderData->id)->update(['status' => 'out_for_delivery']);
//                TrackingHelper::PushChannelStatus($orderData,'out_for_delivery');
//                TrackingHelper::CheckAndSendSMS($orderData);
//                break;
//            case 'SUD':
//                // ndr
//                if ($orderData->rto_status != 'y') {
//                    //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $tracking_data['status'], 'ndr_action' => 'pending', 'ndr_status_date' => $shipment_summary['event_time']]);
//                    Order::where('id', $orderData->id)->update(['ndr_raised_time'=> $datetime,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $request->remarks, 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
//                    $attempt = [
//                        'seller_id' => $orderData->seller_id,
//                        'order_id' => $orderData->id,
//                        'raised_date' => date('Y-m-d', strtotime($datetime)),
//                        'raised_time' => date('H:i:s'),
//                        'action_by' => 'Bombax',
//                        'reason' => $request->remarks,
//                        'action_status' => 'pending',
//                        'remark' => 'pending',
//                        'u_address_line1' => 'new address line 1',
//                        'u_address_line2' => 'new address line 2',
//                        'updated_mobile' => ''
//                    ];
//                    Ndrattemps::create($attempt);
//                    TrackingHelper::PushChannelStatus($orderData, 'ndr');
//                }
//                break;
//            case 'DDL':
//                // delivered
//                Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => $datetime]);
//                if ($orderData->order_type == 'cod') {
//                    $data = array(
//                        'seller_id' => $orderData->seller_id,
//                        'order_id' => $orderData->id,
//                        'amount' => $orderData->invoice_amount,
//                        'type' => 'c',
//                        'datetime' => $datetime,
//                        'description' => 'Order COD Amount Credited',
//                        'redeem_type' => 'o',
//                    );
//                    COD_transactions::create($data);
//                    Seller::where('id', $orderData->seller_id)->increment('cod_balance', $data['amount']);
//                }
//                $checkOfdExist = InternationalOrders::where('order_id',$orderData->id)->first();
//                if(empty($checkOfdExist)) {
//                    $ofdDate = [
//                        'order_id' => $orderData->id,
//                        'ofd_date' => $datetime
//                    ];
//                    InternationalOrders::create($ofdDate);
//                }
//                else{
//                    if(empty($checkOfdExist->ofd_date)){
//                        $ofdDate = [
//                            'ofd_date' => $datetime
//                        ];
//                        InternationalOrders::where('id',$checkOfdExist->id)->update($ofdDate);
//                    }
//                }
//                TrackingHelper::PushChannelStatus($orderData, 'delivered');
//                TrackingHelper::CheckAndSendSMS($orderData);
//                break;
//            case 'RTL':
//                // rto initiated
//                break;
//            case 'RTS':
//                // rto delivered
//                if($orderData->o_type == "forward")
//                    TrackingHelper::RTOOrder($orderData->id);
//                $delivery_date = $datetime;
//                Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
//                $checkOfdExist = InternationalOrders::where('order_id',$orderData->id)->first();
//                if(empty($checkOfdExist)) {
//                    $ofdDate = [
//                        'order_id' => $orderData->id,
//                        'ofd_date' => $datetime
//                    ];
//                    InternationalOrders::create($ofdDate);
//                }
//                else{
//                    if(empty($checkOfdExist->ofd_date)){
//                        $ofdDate = [
//                            'ofd_date' => $datetime
//                        ];
//                        InternationalOrders::where('id',$checkOfdExist->id)->update($ofdDate);
//                    }
//                }
//                TrackingHelper::PushChannelStatus($orderData,'delivered');
//                break;
//            case 'LST':
//            case 'DMG':
//            case 'DSD':
//                // damaged - lost - destroyed
//                Order::where('id', $orderData->id)->update(['status' => 'lost']);
//                break;
//        }
//        $data = [
//            "awb_number" => $orderData->awb_number,
//            "status_code" => $request->status_code,
//            "status" => $request->status_code,
//            "status_description" => $request->status_description,
//            "remarks" =>  $request->remarks,
//            "location" =>  $request->location,
//            "updated_date" => $datetime
//        ];
//        OrderTracking::create($data);
//        return true;
//    }


    //Track DTDC Order Status
    function trackDTDCOrdersWebHook(Request $request){
        if($request->header('authentication') != "a811aa1c-e398-4b12-a103-932e206143ce")
            return response()->json(['status' => false,'message' => 'Unauthorized'],401);
        $responseData = $request->all();
//        $awbNumber = $responseData['shipment']['strShipmentNo'];
//        $order = Order::where('awb_number',$awbNumber)->first();
//        if(empty($order))
//            return response()->json(['status' => false,'message' => 'Invalid AWB Number'],500);
        DTDCWebhookJobs::dispatchAfterResponse($request);
        return response()->json(['status' => true,'message' => 'Status Updated Successfully'],200);
    }

    //Track DTDC Order Status
//    function trackDTDCOrdersWebHook(Request $request){
//        if($request->header('authentication') != "a811aa1c-e398-4b12-a103-932e206143ce")
//            return response()->json(['status' => false,'message' => 'Unauthorized'],401);
//        $responseData = $request->all();
//        try{
//            DTDCPushLogData::create(['awb' => $responseData['shipment']['strShipmentNo'],'request' => json_encode($responseData)]);
//            Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                'title' => 'Webhook Request Payload:',
//                'data' => $responseData
//            ]);
//            if(count($responseData) != 2){
//                Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                    'title' => 'Webhook Response Payload:',
//                    'data' => ['status' => 500,'message' => 'Inappropriate Data']
//                ]);
//                return response()->json(['status' => 500,'message' => 'Inappropriate Data']);
//            }
//            $awbNumber = $responseData['shipment']['strShipmentNo'];
//            $order_tracking = OrderTracking::where('awb_number', $awbNumber)->orderBy('id', 'desc')->first();
//            $order = Order::where('awb_number',$awbNumber)->first();
//            if(empty($order))
//            {
//                Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                    'title' => 'Webhook Response Payload:',
//                    'data' => ['status' => 500,'message' => 'Invalid AWB Number']
//                ]);
//                return response()->json(['status' => 500,'message' => 'Invalid AWB Number']);
//            }
//            if(strtolower($order->status) == 'delivered' || strtolower($order->status) == 'cancelled' || strtolower($order->status) == 'shipped' || strtolower($order->status) == 'pickup_requested' || $order->status == 'pending')
//            {
//                Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                    'title' => 'Webhook Response Payload:',
//                    'data' => ['status' => 200,'message' => 'Order status changed successfully','status_description' => 'delivered/cancelled/shipped/pickup_requested']
//                ]);
//                return response()->json(['status' => 200,'message' => 'Order status changed successfully']);
//            }
//            $shipment_summary = $responseData['shipmentStatus'];
//            if(empty($shipment_summary))
//            {
//                Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                    'title' => 'Webhook Response Payload:',
//                    'data' => ['status' => 500,'message' => 'Invalid Shipment Data']
//                ]);
//                return response()->json(['status' => 500,'message' => 'Invalid Shipment Data']);
//            }
//            if(!empty($shipment_summary[0]))
//                $shipment_summary = $shipment_summary[0];
//            $statusDate = $shipment_summary['strActionDate'] ?? date("dmY");
//            $statusTime = $shipment_summary['strActionTime'] ?? date("Hi");
//            try{
//                $statusDateTime = substr($statusDate,-4)."-".substr($statusDate,2,2)."-".substr($statusDate,0,2)." ".substr($statusTime,0,2).":".substr($statusTime,2,2).":00";
//            }catch(Exception $e){
//                $statusDateTime = date('Y-m-d H:i:s');
//            }
//            $returnValue = false;
//            if ($order_tracking != null) {
//                if ($order_tracking->status_code != $shipment_summary['strAction']) {
//                    switch ($shipment_summary['strAction']) {
//                        case 'OUTDLV':
//                            if ($order->rto_status != 'y') {
//                                if ($order->ndr_status == 'y' && $statusDateTime != $order->ndr_status_date) {
//                                    $attempt = [
//                                        'seller_id' => $order->seller_id,
//                                        'order_id' => $order->id,
//                                        'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                        'raised_time' => date('H:i:s'),
//                                        'action_by' => 'DTDC',
//                                        'reason' => $shipment_summary['strRemarks'],
//                                        'action_status' => 'requested',
//                                        'remark' => 'requested',
//                                        'u_address_line1' => 'new address line 1',
//                                        'u_address_line2' => 'new address line 2',
//                                        'updated_mobile' => ''
//                                    ];
//                                    Ndrattemps::create($attempt);
//                                    Order::where('awb_number', $order->awb_number)->update(['ndr_status_date' => $statusDateTime]);
//                                }
//                            }
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'out_for_delivery']);
//                            TrackingHelper::PushChannelStatus($order,'out_for_delivery');
//                            TrackingHelper::CheckAndSendSMS($order);
//                            break;
//                        case 'OBMN':
//                        case 'CDOUT':
//                        case 'CDIN':
//                        case 'IBMN':
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'in_transit']);
//                            TrackingHelper::PushChannelStatus($order,'in_transit');
//                            break;
//                        case 'PCUP':
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'picked_up']);
//                            TrackingHelper::PushChannelStatus($order,'picked_up');
//                            break;
//                        case 'RTO':
//                            TrackingHelper::RTOOrder($order->id);
//                            TrackingHelper::PushChannelStatus($order,'rto_initated');
//                            break;
//                        case 'RTOCDOUT':
//                        case 'RTOIBMN':
//                            TrackingHelper::RTOOrder($order->id);
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'in_transit']);
//                            break;
//                        case 'RTODLV':
//                            TrackingHelper::RTOOrder($order->id);
//                            $delivery_date = date('Y-m-d H:i:s', strtotime($statusDateTime));
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
//                            TrackingHelper::PushChannelStatus($order,'delivered');
//                            break;
//                        case 'NONDLV':
//                            if ($order->rto_status != 'y') {
//                                //dd($shipment_summary);
//                                Order::where('awb_number', $order->awb_number)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $shipment_summary['strRemarks'], 'ndr_action' => 'pending', 'ndr_status_date' => date('Y-m-d H:i:s', strtotime($statusDateTime))]);
//                                TrackingHelper::PushChannelStatus($order,'ndr');
//                            }
//                            break;
//                        case 'DLV':
//                            $delivery_date = date('Y-m-d H:i:s', strtotime($statusDateTime));
//                            Order::where('awb_number', $order->awb_number)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
//                            if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
//                                $data = array(
//                                    'seller_id' => $order->seller_id,
//                                    'order_id' => $order->id,
//                                    'amount' => $order->invoice_amount,
//                                    'type' => 'c',
//                                    'datetime' => $delivery_date,
//                                    'description' => 'Order COD Amount Credited',
//                                    'redeem_type' => 'o',
//                                );
//                                COD_transactions::create($data);
//                                Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
//                            }
//                            TrackingHelper::PushChannelStatus($order,'delivered');
//                            TrackingHelper::CheckAndSendSMS($order);
//                            break;
//                    }
//                    $data = [
//                        "awb_number" =>   $order->awb_number,
//                        "status_code" => $shipment_summary['strAction'],
//                        "status" => $shipment_summary['strActionDesc'],
//                        "status_description" => $shipment_summary['strActionDesc'],
//                        "remarks" =>  $shipment_summary['strRemarks'],
//                        "location" =>  $shipment_summary['strRemarks'],
//                        "updated_date" => date('Y-m-d H:i:s', strtotime($statusDateTime)),
//                    ];
//                    OrderTracking::create($data);
//                    $returnValue = true;
//                }
//            }
//            else {
//                $data = [
//                    "awb_number" =>   $order->awb_number,
//                    "status_code" => $shipment_summary['strAction'],
//                    "status" => $shipment_summary['strActionDesc'],
//                    "status_description" => $shipment_summary['strActionDesc'],
//                    "remarks" =>  $shipment_summary['strRemarks'],
//                    "location" =>  $shipment_summary['strRemarks'],
//                    "updated_date" => date('Y-m-d H:i:s', strtotime($statusDateTime)),
//                ];
//                OrderTracking::create($data);
//                $returnValue = true;
//            }
//            $updateArray = [
//                'last_sync' => date('Y-m-d H:i:s')
//            ];
//            if($returnValue){
//                $updateArray['last_executed'] = date('Y-m-d H:i:s');
//            }
//            Order::where('id',$order->id)->update($updateArray);
//            Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                'title' => 'Webhook Response Payload:',
//                'data' => ['status' => 200,'message' => 'Status saved Successfully','status_description' => 'All Good']
//            ]);
//            return response()->json(['status' => 200,'message' => 'Status saved Successfully']);
//        }
//        catch(Exception $e){
//            Logger::write('logs/partners/dtdc/dtdc-webhook-'.date('Y-m-d').'.text', [
//                'title' => 'Webhook Response Payload:',
//                'data' => ['status' => 500,'message' => 'Something went wrong','error' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]
//            ]);
//            return response()->json(['status' => 500,'message' => 'Something went wrong']);
//        }
//    }

    // Track DTDC Order Status WebHook
    function trackDTDCOrdersWebHookStaging(Request $request){
        $responseData = $request->all();
        if($request->header('authentication') != "33d560d9-0224-4417-bd1e-b29c5aae95ba")
            return response()->json(['status' => false,'message' => 'Unauthorized'],401);
        try{
            DTDCPushLogData::create(['awb' => $responseData['shipment']['strShipmentNo'],'request' => json_encode($responseData)]);
            Logger::write('logs/partners/dtdc/dtdc-webhook-staging-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $responseData
            ]);
            if(count($responseData) != 2){
                return response()->json(['status' => 500,'message' => 'Inappropriate Data']);
            }
            $awbNumber = $responseData['shipment']['strShipmentNo'];
            $order = Order::where('awb_number',$awbNumber)->first();
            if(empty($order))
            {
                return response()->json(['status' => 500,'message' => 'Invalid AWB Number']);
            }
            return response()->json(['status' => 200,'message' => 'Status saved Successfully']);
        }
        catch(Exception $e){
            Logger::write('logs/partners/dtdc/dtdc-webhook-error.text', [
                'title' => 'Check out error with this payload:',
                'data' => ['error' => $e->getMessage()." - ".$e->getFile()." - ".$e->getLine()],
            ]);
            return response()->json(['status' => 500,'message' => 'Something went wrong']);
        }
    }

    // Track amazon swa order webhook
    function trackAmazonSwaOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/amazon-swa/amazon-swa-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all(),
            ]);
            // Auth
            if($request->header('Authorization') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            $data = $request->all();
            if (!empty($data['detail'])) {
                $order = Order::where('awb_number', $data['detail']['trackingId'])->firstOrFail();
                if(strtolower($order->status) == 'delivered') {
                    throw new Exception('Order is delivered, order status can not be changed.');
                }
                if(strtolower($order->status) == 'shipped' || strtolower($order->status) == 'pickup_requested' || strtolower($order->status == 'pending'))
                {
                    throw new Exception('Label is not printed');
                }
                if(strtolower($order->status) == 'cancelled') {
                    throw new Exception('Order is cancelled, order status can not be changed.');
                }
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                $status = $data['detail']['status'];
                $statusCode = $data['detail']['eventCode'];
                $statusDateTime = $data['detail']['eventTime'];
                $location = 'N/A';
                $instruction = 'N/A';

                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $statusCode) {
                        $this->HandleAmazonSWATracking($data,$order,$statusCode,$statusDateTime,$location);
                    }
                } else {
                    $this->HandleAmazonSWATracking($data,$order,$statusCode,$statusDateTime,$location);
                }
                $order->last_sync = date('Y-m-d H:i:s');
                $order->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/amazon-swa/amazon-swa-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function HandleAmazonSWATracking($data,$order,$statusCode,$statusDateTime,$location){
        if(!empty($data['detail']['alternateLegTrackingId']) && $data['detail']['alternateLegTrackingId'] != ""){
            Order::where('id', $order->id)->update(['alternate_awb_number' => $data['detail']['alternateLegTrackingId']]);
            TrackingHelper::RTOOrder($order->id);
            TrackingHelper::PushChannelStatus($order,'rto_initiated',date('Y-m-d', strtotime($statusDateTime)));
        }
        switch($statusCode){
            case 'ReadyForReceive':
                Order::where('id', $order->id)->update(['status' => 'pickup_scheduled']);
                TrackingHelper::PushChannelStatus($order,'pickup_scheduled',date('Y-m-d', strtotime($statusDateTime)));
                break;
            case 'PickupDone':
                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => date('Y-m-d H:i:s',strtotime($statusDateTime))]);
                TrackingHelper::PushChannelStatus($order,'picked_up',date('Y-m-d', strtotime($statusDateTime)));
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'Rejected':
                if ($order->rto_status != 'y') {
                    //dd($shipment_summary);
                    if($order->ndr_status != 'y'){
                        Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $statusCode, 'ndr_action' => 'pending', 'ndr_status_date' => date('Y-m-d H:i:s', strtotime($statusDateTime))]);
                        TrackingHelper::PushChannelStatus($order,'ndr',date('Y-m-d', strtotime($statusDateTime)));
                    }else{
                        Order::where('id', $order->id)->update(['status' => 'ndr', 'reason_for_ndr' => $statusCode, 'ndr_action' => 'pending', 'ndr_status_date' => date('Y-m-d H:i:s', strtotime($statusDateTime))]);
                        TrackingHelper::PushChannelStatus($order,'ndr',date('Y-m-d', strtotime($statusDateTime)));
                    }
                }
                break;
            case 'Undeliverable':
                // Changes done by ajay sir -- discussed
                break;
            case 'Delivered':
                $delivery_date = date('Y-m-d', strtotime($statusDateTime));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                if ($order->order_type == 'cod' && $order->o_type=='forward' && $order->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $delivery_date,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                TrackingHelper::PushChannelStatus($order,'delivered',date('Y-m-d', strtotime($statusDateTime)));
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'Departed':
            case 'ArrivedAtCarrierFacility':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                TrackingHelper::PushChannelStatus($order,'in_transit',date('Y-m-d', strtotime($statusDateTime)));
                break;
            case 'Lost':
            case 'Destroyed':
                Order::where('id', $order->id)->update(['status' => 'lost']);
                TrackingHelper::PushChannelStatus($order,'lost',date('Y-m-d', strtotime($statusDateTime)));
                break;
            case 'OutForDelivery':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $statusDateTime != $order->ndr_status_date) {
                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $order->seller_id,
//                            'order_id' => $order->id,
//                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                            'raised_time' => date('H:i:s'),
//                            'action_by' => 'XpressBees',
//                            'reason' => $order->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => date('Y-m-d', strtotime($statusDateTime))]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                TrackingHelper::PushChannelStatus($order,'out_for_delivery',date('Y-m-d', strtotime($statusDateTime)));
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'DeliveryAttempted':
                if ($order->rto_status != 'y' && $order->ndr_status != 'y') {
                    Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $statusCode, 'ndr_action' => 'pending', 'ndr_status_date' => $statusDateTime]);
                    TrackingHelper::PushChannelStatus($order,'ndr',date('Y-m-d', strtotime($statusDateTime)));
                }
                break;
            case 'PickupCancelled':
                Order::where('id', $order->id)->update(['status' => 'cancelled']);
                TrackingHelper::PushChannelStatus($order,'cancelled',date('Y-m-d', strtotime($statusDateTime)));
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $statusCode,
            "status" => $statusCode,
            "status_description" => $statusCode,
            "remarks" =>  $statusCode,
            "location" =>  $location,
            "updated_date" => $statusDateTime,
            "updated_by" => $order->courier_partner,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    // Track ekart order webhook
    function trackEkartOrderHook(Request $request)
    {
        // Auth
        if($request->header('Authorization') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
            return response()->json([
                'status' => false,
                'message' => 'Invalid access token'
            ], 401);
        }
        $data = $request->all();
        $orderData = Order::where('awb_number',$data['vendor_tracking_id'])->firstOrFail();
        if(!empty($orderData)){
            EkartWebhookJobs::dispatchAfterResponse($request,$orderData);
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid AWB Number'
            ], 200);
        }
    }

    function insertDtdcAdvanceAwbs(Request $request){
        if($request->password == MyUtility::GenerateArchivePassword()){
            $start = $request->start;
            $end = $request->end;
            $prefix = $request->prefix;
            $s = ltrim($start,$prefix);
            $e = ltrim($end,$prefix);
            $allAwbs = [];
            for($i=$s;$i<=$e;$i++){
                $allAwbs[] = [
                    'awb_number' => $prefix.$i,
                    'created' => date('Y-m-d H:i:s')
                ];
                if(count($allAwbs) == 5000)
                {
                    DtdcAwbNumbers::insert($allAwbs);
                    $allAwbs = [];
                }
            }
            DtdcAwbNumbers::insert($allAwbs);
            return response()->json(['status'=> true,'message' => 'AWB imported Successfully']);
        }
        else{
            return response()->json(['status'=> false,'message' => 'Invalid Password']);
        }
    }

    function insertDtdcSEAdvanceAwbs(Request $request){
        $start = $request->start;
        $end = $request->end;
        $prefix = $request->prefix;
        $s = ltrim($start,$prefix);
        $e = ltrim($end,$prefix);
        $allAwbs = [];
        for($i=$s;$i<=$e;$i++){
            $allAwbs[] = [
                'awb_number' => $prefix.$i,
                'created' => date('Y-m-d H:i:s')
            ];
            if(count($allAwbs) == 5000)
            {
                DtdcSEAwbNumbers::insert($allAwbs);
                $allAwbs = [];
            }
        }
        DtdcSEAwbNumbers::insert($allAwbs);
    }
    function insertProfessionalAdvanceAwbs(Request $request){
        $start = $request->start;
        $end = $request->end;
        $prefix = $request->prefix;
        $s = ltrim($start,$prefix);
        $e = ltrim($end,$prefix);
        $allAwbs = [];
        for($i=$s;$i<=$e;$i++){
            $allAwbs[] = [
                'awb_number' => $prefix.$i,
                'created' => date('Y-m-d H:i:s'),
                'used' => 'n'
            ];
            if(count($allAwbs) == 5000)
            {
                ProfessionalAwbNumbers::insert($allAwbs);
                $allAwbs = [];
            }
        }
        ProfessionalAwbNumbers::insert($allAwbs);
    }
    function insertDtdcLLAdvanceAwbs(Request $request){
        $start = $request->start;
        $end = $request->end;
        $prefix = $request->prefix;
        $s = ltrim($start,$prefix);
        $e = ltrim($end,$prefix);
        $allAwbs = [];
        for($i=$s;$i<=$e;$i++){
            $allAwbs[] = [
                'awb_number' => $prefix.$i,
                'created' => date('Y-m-d H:i:s')
            ];
            if(count($allAwbs) == 5000)
            {
                DtdcLLAwbNumbers::insert($allAwbs);
                $allAwbs = [];
            }
        }
        DtdcLLAwbNumbers::insert($allAwbs);
    }

    // Generate Shree Maruti Ecom AWBs Generation
    function insertMarutiEcomAdvanceAwbs(Request $request){
        $start = $request->start;
        $end = $request->end;
        $prefix = $request->prefix;
        $s = ltrim($start,$prefix);
        $e = ltrim($end,$prefix);
        $allAwbs = [];
        for($i=$s;$i<=$e;$i++){
            $allAwbs[] = [
                'courier_partner' => 'shree_maruti_ecom',
                'awb_number' => $prefix.$i,
                'generated' => date('Y-m-d H:i:s')
            ];
            if(count($allAwbs) == 10000)
            {
                MarutiEcomAwbs::insert($allAwbs);
                $allAwbs = [];
            }
        }
        MarutiEcomAwbs::insert($allAwbs);
    }

    // Generate Shree Maruti Ecom AWBs Generation
    function insertMarutiEcomNewAdvanceAwbs(Request $request){
        $start = $request->start;
        $end = $request->end;
        $prefix = $request->prefix;
        $s = ltrim($start,$prefix);
        $e = ltrim($end,$prefix);
        $allAwbs = [];
        for($i=$s;$i<=$e;$i++){
            $allAwbs[] = [
                'courier_partner' => 'smc_new',
                'awb_number' => $prefix.$i,
                'generated' => date('Y-m-d H:i:s')
            ];
            if(count($allAwbs) == 10000)
            {
                SMCNewAWB::insert($allAwbs);
                $allAwbs = [];
            }
        }
        SMCNewAWB::insert($allAwbs);
    }

    // Track pidge order webhook
    function trackPidgeOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/pidge/pidge-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all(),
            ]);
            // Auth
            if($request->header('Authorization') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
                Logger::write('logs/partners/pidge/pidge-webhook-'.date('Y-m-d').'.text', [
                    'title' => 'Webhook Response Payload:',
                    'data' => 'Invalid access token',
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            $data = $request->all();
            if (!empty($data['PBID'])) {
                $order = Order::where('awb_number', $data['PBID'])->firstOrFail();
                if(strtolower($order->status) == 'delivered') {
                    throw new Exception('Order is delivered, order status can not be changed.');
                }

                if(strtolower($order->status) == 'pending' || strtolower($order->status) == 'shipped' || strtolower($order->status) == 'pickup_requested' ){
                    return response()->json([
                        'status' => true,
                        'message' => 'Tracking updated successfully'
                    ]);
                }

                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                $statusCode = $data['status'];
                $statusDateTime = $data['timestamp'] ?? date('Y-m-d H:i:s');
                $location = 'N/A';
                $expectedDeliveryDate = null;

                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $statusCode) {
                        switch ($statusCode) {
                            case 3:
                                Order::where('id', $order->id)->update(['expected_delivery_date' => $expectedDeliveryDate, 'status' => 'pickup_scheduled','pickup_schedule' => 'y']);
                                TrackingHelper::PushChannelStatus($order,'pickup_scheduled');
                                break;
                            case 4:
                                Order::where('id', $order->id)->update(['expected_delivery_date' => $expectedDeliveryDate, 'status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $statusDateTime]);
                                TrackingHelper::PushChannelStatus($order,'picked_up');
                                TrackingHelper::CheckAndSendSMS($order);
                                break;
                            case 10:
                            case 20:
                                if ($order->rto_status != 'y') {
                                    if ($order->ndr_status == 'y' && $statusDateTime != $order->ndr_status_date) {
                                        //make attempt here
//                                        $attempt = [
//                                            'seller_id' => $order->seller_id,
//                                            'order_id' => $order->id,
//                                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
//                                            'raised_time' => date('H:i:s'),
//                                            'action_by' => 'Ekart',
//                                            'reason' => $order->reason_for_ndr,
//                                            'action_status' => 'requested',
//                                            'remark' => 'requested',
//                                            'u_address_line1' => 'new address line 1',
//                                            'u_address_line2' => 'new address line 2',
//                                            'updated_mobile' => ''
//                                        ];
//                                        Ndrattemps::create($attempt);
                                        Order::where('id', $order->id)->update(['ndr_status_date' => $statusDateTime]);
                                    }
                                }
                                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                                TrackingHelper::PushChannelStatus($order,'out_for_delivery');
                                TrackingHelper::CheckAndSendSMS($order);
                                break;
                            case 5:
                                Order::where('id', $order->id)->update(['status' => 'in_transit','expected_delivery_date' => $expectedDeliveryDate]);
                                TrackingHelper::PushChannelStatus($order,'in_transit');
                                break;
                            case 11:
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $statusDateTime]);
                                if ($order->order_type == 'cod') {
                                    $data = array(
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'amount' => $order->invoice_amount,
                                        'type' => 'c',
                                        'datetime' => $statusDateTime,
                                        'description' => 'Order COD Amount Credited',
                                        'redeem_type' => 'o',
                                    );
                                    COD_transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                                }
                                TrackingHelper::PushChannelStatus($order,'delivered');
                                TrackingHelper::CheckAndSendSMS($order);
                                break;
                            case 19:
                                TrackingHelper::RTOOrder($order->id);
                                Order::where('id', $order->id)->update(['status' => 'rto_initated', 'rto_status' => 'y']);
                                TrackingHelper::PushChannelStatus($order, 'rto_initated');
                                break;
                            case 22:
                                TrackingHelper::RTOOrder($order->id);
                                // mark shipment as rto delivered
                                $delivery_date = date('Y-m-d H:i:s', strtotime($statusDateTime));
                                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                                TrackingHelper::PushChannelStatus($order,'delivered');
                                break;
                            case 40:
                                Order::where('id', $order->id)->update(['status' => 'lost']);
                                TrackingHelper::PushChannelStatus($order, 'lost');
                                break;
                            case 41:
                                Order::where('id', $order->id)->update(['status' => 'damaged']);
                                TrackingHelper::PushChannelStatus($order, 'damaged');
                                break;
                        }
                        $data = [
                            "awb_number" => $order->awb_number,
                            "status_code" => $statusCode,
                            "status" => $statusCode,
                            "status_description" => $statusCode,
                            "remarks" =>  $statusCode,
                            "location" =>  $location,
                            "updated_date" => $statusDateTime,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        OrderTracking::create($data);
                    }
                } else {
                    $data = [
                        "awb_number" => $order->awb_number,
                        "status_code" => $statusCode,
                        "status" => $statusCode,
                        "status_description" => $statusCode,
                        "remarks" =>  $statusCode,
                        "location" =>  $location,
                        "updated_date" => $statusDateTime,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    OrderTracking::create($data);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/pidge/pidge-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function exportSelectedPincodesWithCourier(){
        $allPincodes = DB::select("select courier_partner,pincode from serviceable_pincode where courier_partner in('shadow_fax','delhivery_surface','dtdc_surface','bluedart','smartr','bombax','shree_maruti','gati','ekart') order by courier_partner");
        $name = "exports/serviceable-pincodes";
        $filename = "serviceable-pincodes";
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No', 'Courier Partner', 'Pincode');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach($allPincodes as $p){
            $info = array($cnt++, $p->courier_partner, $p->pincode);
            fputcsv($fp, $info);
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$filename.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");

    }

    function TrackMarutiEcomOrderWebhook(Request $request){
        $response = [
            'status' => 200,
            'message' => 'Order updated successfully',
            'data' => [
                'success' => true
            ]
        ];
        try{
            Logger::write('logs/partners/shree-maruti-ecom/maruti-ecom-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all()
            ]);
            // Auth
            if($request->header('Token') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
                Logger::write('logs/partners/shree-maruti-ecom/maruti-ecom-webhook-'.date('Y-m-d').'.text', [
                    'title' => 'Webhook Response Payload:',
                    'data' => [
                        'status' => false,
                        'message' => 'Invalid access token'
                    ]
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            $data = $request->all();
            $orderData = Order::where('awb_number',$data['cAwbNumber'])->first();
            if(empty($orderData))
                return response()->json($response);
            if($orderData->status == 'cancelled' || $orderData->status == 'delivered' || $orderData->status == 'shipped' || $orderData->status == 'pickup_requested'){
                return response()->json($response);
            }
            $order_tracking = OrderTracking::where('awb_number', $orderData->awb_number)->orderBy('id', 'desc')->first();
            if(!empty($order_tracking)){
                if($order_tracking->status_code != $data['statusCode']){
                    $this->_HandleMarutiEcomOrder($orderData,$data);
                }
            }else{
                $this->_HandleMarutiEcomOrder($orderData,$data);
            }
            $orderData->last_sync = date('Y-m-d H:i:s');
            $orderData->save();
            Logger::write('logs/partners/shree-maruti-ecom/maruti-ecom-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $response
            ]);
            return response()->json($response);
        }
        catch(Exception $e){
            Logger::write('logs/partners/shree-maruti-ecom/maruti-ecom-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload Error',
                'data' => $e->getMessage()." - ".$e->getFile(). " - ".$e->getLine()
            ]);
            return response()->json(['status' => 500,'message' => $e->getMessage()." - ".$e->getLine(),'data' => ['status' => false]]);
        }
    }
    function _HandleMarutiEcomOrder($orderData,$currentData){
        $sellerData = Seller::find($orderData->seller_id);
        $updateDateTime = $currentData['updateDate'];
        switch ($currentData['statusCode']) {
            case 'FM-CAN':
                MyUtility::PerformCancellation($sellerData,$orderData);
                break;
            case 'FM-PKR':
            case 'FM-OFP':
            case 'FM-TRP':
            case 'FM-PNR':
                // pickup scheduled
                Order::where('id', $orderData->id)->update(['status' => 'pickup_scheduled']);
                TrackingHelper::PushChannelStatus($orderData, 'pickup_scheduled',$updateDateTime);
                break;
            case 'FM-PBH':
            case 'FM-PKU':
                // picked up
                Order::where('id', $orderData->id)->update(['status' => 'picked_up','pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $updateDateTime]);
                TrackingHelper::PushChannelStatus($orderData, 'picked_up',$updateDateTime);
                break;
            case 'OUT-SCAN':
            case 'IN-SCAN':
                // In Transit
                Order::where('id', $orderData->id)->update(['status' => 'in_transit']);
                TrackingHelper::PushChannelStatus($orderData, 'in_transit',$updateDateTime);
                TrackingHelper::CheckAndSendSMS($orderData);
                break;
            case 'LM-OFD':
                // out for delivery
                $updateArray = [];
                if ($orderData->rto_status != 'y') {
                    if ($orderData->ndr_status == 'y' && $updateDateTime != $orderData->ndr_status_date) {
                        //make attempt here
                        $attempt = [
                            'seller_id' => $orderData->seller_id,
                            'order_id' => $orderData->id,
                            'raised_date' => $updateDateTime,
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'Shree Maruti',
                            'reason' => $orderData->reason_for_ndr,
                            'action_status' => 'requested',
                            'remark' => 'requested',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        $updateArray['ndr_status_date'] = $updateDateTime;
                        //Order::where('id', $orderData->id)->update(['ndr_status_date' => $updateDateTime]);
                    }
                }
                $updateArray['status'] = 'out_for_delivery';
                Order::where('id', $orderData->id)->update($updateArray);
                TrackingHelper::PushChannelStatus($orderData,'out_for_delivery',$updateDateTime);
                TrackingHelper::CheckAndSendSMS($orderData);
                break;
            case 'LM-DEL':
                // delivered
                Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => date('Y-m-d H:i:s')]);
                if ($orderData->order_type == 'cod') {
                    $data = array(
                        'seller_id' => $orderData->seller_id,
                        'order_id' => $orderData->id,
                        'amount' => $orderData->invoice_amount,
                        'type' => 'c',
                        'datetime' => $updateDateTime,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $orderData->seller_id)->increment('cod_balance', $data['amount']);
                }
                TrackingHelper::PushChannelStatus($orderData,'delivered',$updateDateTime);
                TrackingHelper::CheckAndSendSMS($orderData);
                break;
            case 'LM-RTORE':
            case 'LM-RTI':
            case 'LM-RTN':
                // rto initiated
                TrackingHelper::RTOOrder($orderData->id);
                TrackingHelper::PushChannelStatus($orderData,'rto_initiated',$updateDateTime);
                break;
            case 'LM-NDR':
                // NDR
                if ($orderData->rto_status != 'y') {
                    Order::where('id', $orderData->id)->update(['ndr_raised_time'=> $updateDateTime,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $currentData['reason'], 'ndr_action' => 'pending', 'ndr_status_date' => $updateDateTime]);
                    TrackingHelper::PushChannelStatus($orderData,'ndr',$updateDateTime);
                }
                break;
            case 'LM-RTO':
                // RTO Delivered
                if($orderData->o_type == "forward")
                    TrackingHelper::RTOOrder($orderData->id);
                Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => $updateDateTime]);
                TrackingHelper::PushChannelStatus($orderData,'delivered',$updateDateTime);
                break;
            case 'DESTROY':
                // damaged
                Order::where('id', $orderData->id)->update(['status' => 'damaged']);
                TrackingHelper::PushChannelStatus($orderData, 'damaged',$updateDateTime);
                break;
            default:
                $missStatus = [
                    'order_id' => $orderData->id,
                    'courier_keyword' => $orderData->courier_partner,
                    'status' => $currentData['statusCode'],
                    'status_description' => $currentData['orderStatus'],
                    'json' => json_encode($currentData),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $currentData['cAwbNumber'],
            "status_code" => $currentData['statusCode'],
            "status" => $currentData['orderStatus'],
            "status_description" => $currentData['orderStatus'],
            "remarks" =>  $currentData['reason'],
            "location" =>  $currentData['reason'],
            "updated_date" => $updateDateTime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }
    function markOrderAsRTO(Request $request){
        if(empty($request->awb_number))
            return response()->json(['status' => false,'message' => 'Please pass awb numbers']);
        $awbList = explode(',',$request->awb_number);
        $orders = Order::whereIn('awb_number',$awbList)->select('id','awb_number')->get();
        foreach ($orders as $o){
            TrackingHelper::RTOOrder($o->id,true);
            Order::where('id',$o->id)->update(['status' => $request->status ?? 'rto_initiated']);
            TrackingHelper::PushChannelStatus($o,'rto_initiated',date('Y-m-d H:i:s'));
        }
        return response()->json(['status' => true,'message' => 'All Orders marked as RTO Successfully']);
    }
    function ReverseCancellation(Request $request){
        $awbList = explode(',',$request->awb_numbers);
        $orderList = Order::whereIn('awb_number',$awbList)->get();
        foreach ($orderList as $o){
            MyUtility::ReverseCancellation($o,$request->status);
        }
    }

    // Track ShadowFax Staging order webhook
    function trackShadowFaxStagingOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/shadowfax/shadowfax-staging-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all(),
            ]);
            // Auth
            if($request->header('Authorization') != '33d560d902244417bd1eb29c5aae95ba') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/shadowfax/shadowfax-staging-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Track ShadowFax Production order webhook
    function trackShadowFaxOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/shadowfax/shadowfax-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all(),
            ]);
            // Auth
            if($request->header('Authorization') != '87huyo78372sjoujhnHhjkgKJdIunbKP08m') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/shadowfax/shadowfax-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Track Professional Production order webhook
    function trackProfessionalOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/professional/professional-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $request->all()
            ]);
            // Auth Check for the API key
            if($request->api_key != '02303067-610d-4b77-a7f0-2ea730420be8') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access key'
                ], 401);
            }
            $responseID = null;
            $awbNumber = null;
            if(!empty($request[0]['POD_NO'])){
                $trackingData = $request[0];
                $awbNumber = $trackingData['POD_NO'];
                $responseID = $trackingData['id'];
                $orderData = Order::where('awb_number',$trackingData['POD_NO'])->first();
                if(empty($orderData))
                    return response()->json([
                        'status' => false,
                        'message' => 'POD Number not found'
                    ],500);
                if(!in_array($orderData->status,['pending','shipped','pickup_requested','delivered','cancelled'])){
                    $orderTracking = OrderTracking::where('awb_number',$orderData->awb_number)->orderBy('id','desc')->first();
                    if(empty($orderTracking) || ($orderTracking->status != $trackingData['Type'] || $orderTracking->status != $trackingData['Status_code'])){
                        $this->HandleProfessionalTracking($orderTracking,$trackingData);
                    }
                }
                $this->HandleProfessionalTracking($orderData,$trackingData);
            }
            return response()->json([
                'POD_NO' => $awbNumber,
                'status' => 'success',
                'error' => '0',
                'ID' => $responseID
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/professional/professional-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'POD_NO' => $request->POD_NO,
                'status' => $e->getMessage(),
                'error' => '1',
                'ID' => $responseID
            ]);
        }
    }

    function HandleProfessionalTracking($orderData,$trackingData){
        try{
            $statusDateTime = date('Y-m-d H:i:s',strtotime($trackingData['Sys_Dt']." ".$trackingData['Sys_Tm']));
        }catch(Exception $e){
            $statusDateTime = date('Y-m-d H:i:s');
        }
        switch($trackingData['Type']){
            case 'Booking':
                Order::where('id', $orderData->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $statusDateTime]);
                TrackingHelper::PushChannelStatus($orderData,'picked_up',$statusDateTime);
                TrackingHelper::CheckAndSendSMS($orderData);
                break;
            case 'Inbound':
            case 'Outbound':
                Order::where('id', $orderData->id)->update(['status' => 'in_transit']);
                TrackingHelper::PushChannelStatus($orderData,'in_transit',$statusDateTime);
                break;
            case 'Attempted':
                if($trackingData['Remarks'] == 'DL' || $trackingData['Remarks'] == 'NR' || $trackingData['Remarks'] == 'NDR'){
                    // NDR
                    if ($orderData->rto_status != 'y') {
                        //Order::where('id', $order->id)->update(['ndr_raised_time'=> date('Y-m-d H:i:s'),'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['current_branch'], 'ndr_action' => 'pending', 'ndr_status_date' => $datetime]);
                        $ndrRaisedDate = $statusDateTime;
                        Order::where('id', $orderData->id)->update(['ndr_raised_time'=> $ndrRaisedDate,'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['Activity'], 'ndr_action' => 'pending', 'ndr_status_date' => $statusDateTime]);
                        $attempt = [
                            'seller_id' => $orderData->seller_id,
                            'order_id' => $orderData->id,
                            'raised_date' => date('Y-m-d', strtotime($ndrRaisedDate)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'Professional',
                            'reason' => $trackingData['Activity'],
                            'action_status' => 'pending',
                            'remark' => 'pending',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        TrackingHelper::PushChannelStatus($orderData,'ndr',$statusDateTime);
                    }
                }
                else if($trackingData['Remarks'] == 'TD'){
                    // out for delivery
                    if ($orderData->rto_status != 'y') {
                        if ($orderData->ndr_status == 'y' && $statusDateTime != $orderData->ndr_status_date) {
                            //make attempt here
                            $attempt = [
                                'seller_id' => $orderData->seller_id,
                                'order_id' => $orderData->id,
                                'raised_date' => date('Y-m-d', strtotime($orderData->ndr_status_date)),
                                'raised_time' => date('H:i:s'),
                                'action_by' => 'Professional',
                                'reason' => $orderData->reason_for_ndr,
                                'action_status' => 'requested',
                                'remark' => 'requested',
                                'u_address_line1' => 'new address line 1',
                                'u_address_line2' => 'new address line 2',
                                'updated_mobile' => ''
                            ];
                            Ndrattemps::create($attempt);
                            Order::where('id', $orderData->id)->update(['ndr_status_date' => $statusDateTime]);
                        }
                    }
                    Order::where('id', $orderData->id)->update(['status' => 'out_for_delivery']);
                    TrackingHelper::PushChannelStatus($orderData,'out_for_delivery',$statusDateTime);
                    TrackingHelper::CheckAndSendSMS($orderData);
                }else if($trackingData['Remarks'] == 'RO'){
                    // RTO
                    if($orderData->rto_status == 'n')
                        TrackingHelper::RTOOrder($orderData->id);
                }
                break;
            case 'Delivered':
                Order::where('id', $orderData->id)->update(['status' => 'delivered', 'delivered_date' => $statusDateTime]);
                if ($orderData->order_type == 'cod' && $orderData->o_type=='forward' && $orderData->rto_status == 'n') {
                    $data = array(
                        'seller_id' => $orderData->seller_id,
                        'order_id' => $orderData->id,
                        'amount' => $orderData->invoice_amount,
                        'type' => 'c',
                        'datetime' => $statusDateTime ?? date('Y-m-d'),
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $orderData->seller_id)->increment('cod_balance', $data['amount']);
                }
                TrackingHelper::PushChannelStatus($orderData,'delivered',$statusDateTime);
                TrackingHelper::CheckAndSendSMS($orderData);
                break;
            default:
                $missStatus = [
                    'order_id' => $orderData->id,
                    'courier_keyword' => $orderData->courier_partner,
                    'status' => $trackingData['Type']."-".$trackingData['Activity'],
                    'status_description' => $trackingData['Activity'],
                    'json' => json_encode($trackingData),
                    'created_at' => date('Y-m-d h:i:s')
                ];
                CourierMissStatusCode::create($missStatus);
                break;
        }
        $data = [
            "awb_number" => $orderData->awb_number,
            "status_code" => $trackingData['Type'],
            "status" => $trackingData['Type'],
            "status_description" => $trackingData['Activity'],
            "remarks" =>  $trackingData['Remarks'],
            "location" =>  $trackingData['Remarks'],
            "updated_date" => date('Y-m-d',strtotime($trackingData['Sys_Dt'])),
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    function pickndelWebhook(Request $request)
    {
        try {
            // Auth
            if($request->header('Authentication') != '49ed9b22-da0c-4716-b893-cf66b8acb168') {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            $data = $request->all();
            PickNDelWebHookResponse::create(['awb_number' => $data['AWB'], 'request' => json_encode($request->all()), 'inserted' => date('Y-m-d H:i:s')]);
            if (!empty($data['OrderStatus'])) {
                $order = Order::where('awb_number', $data['AWB'])->firstOrFail();
                if(strtolower($order->status) == 'delivered') {
                    throw new Exception('Order is delivered, order status can not be changed.');
                }
                if(strtolower($order->status) == 'shipped' || strtolower($order->status) == 'pickup_requested' || strtolower($order->status == 'pending'))
                {
                    throw new Exception('Label is not printed');
                }
                if(strtolower($order->status) == 'cancelled') {
                    throw new Exception('Order is cancelled, order status can not be changed.');
                }
                $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                $statusCode = $data['OrderStatus'];
                $statusDateTime = $data['Time'] ?? $data['OrderDate'];
                $location = 'N/A';

                if ($order_tracking != null) {
                    if ($order_tracking->status_code != $statusCode) {
                        $this->HandlePickNDelTracking($data,$order,$statusCode,$statusDateTime,$location);
                    }
                } else {
                    $this->HandlePickNDelTracking($data,$order,$statusCode,$statusDateTime,$location);
                }
                $order->last_sync = date('Y-m-d H:i:s');
                $order->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Tracking updated successfully'
            ]);
        } catch(Exception $e) {
            Logger::write('logs/partners/pickndel/pickndel-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function HandlePickNDelTracking($data,$order,$statusCode,$statusDateTime,$location)
    {
        $edd = $data['ExpectedDeliveryDate'] ?? null;
        if(!empty($edd)) {
            try{
                Order::where('awb_number', $data["AWB"])->update(['expected_delivery_date' => date('Y-m-d', strtotime($edd))]);
            }catch (Exception $e){}
        }
        switch ($statusCode) {
            case 'NEW':
            case 'RAP':
            case 'ARP':
            case 'OFP':
            case 'ARV':
            case 'CANT':
            case 'CER':
            case 'CIDR':
            case 'CIWA':
            case 'CLOC':
            case 'CNSP':
            case 'CNSA':
            case 'CPNM':
            case 'CSHI':
            case 'CPOS':
            case 'CPDOC':
            case 'CSNPP':
            case 'CCNAP':
            case 'CPA3D':
            case 'CCRTH':
            case 'CCROC':
            case 'CTAFC':
            case 'CLSV':
            case 'CCTZ':
            case 'CQCF':
            case 'CCLD':
            case 'PNR':
                Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y','pickup_time' => $statusDateTime]);
                TrackingHelper::PushChannelStatus($order,'pickup_scheduled');
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'ANT':
            case 'CLJ':
            case 'CNA':
            case 'ER':
            case 'IDR':
            case 'IWA':
            case 'LOC':
            case 'NSP':
            case 'NSA':
            case 'PNM':
            case 'RTA':
            case 'SHI':
            case 'POS':
            case 'CROC':
            case 'TAFC':
            case 'LSV':
            case 'CTZ':
            case 'CNR':
            case 'CAN':
            case 'CBD':
            case 'CDD':
            case 'PEN':
            case 'OSA':
            case 'PANT':
            case 'PCNA':
            case 'PH':
            case 'PL':
            case 'PNA':
            case 'PM':
            case 'CNC':
            case 'R3D':
            case 'PAWC':
            case 'LFV':
            case 'PFL':
            case 'RSC':
            case 'PCNR':
            case 'PPNM':
            case 'PHL':
            case 'PCOD':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $statusDateTime != $order->ndr_status_date) {
                        //make attempt here
                        $attempt = [
                            'seller_id' => $order->seller_id,
                            'order_id' => $order->id,
                            'raised_date' => date('Y-m-d', strtotime($order->ndr_status_date)),
                            'raised_time' => date('H:i:s'),
                            'action_by' => 'PickNDel',
                            'reason' => $data['Reason'],
                            'action_status' => 'requested',
                            'remark' => 'requested',
                            'u_address_line1' => 'new address line 1',
                            'u_address_line2' => 'new address line 2',
                            'updated_mobile' => ''
                        ];
                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $statusDateTime]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                TrackingHelper::PushChannelStatus($order,'out_for_delivery');
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'CFD':
                TrackingHelper::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery','rto_status' => 'y']);
                TrackingHelper::PushChannelStatus($order,'out_for_delivery',$statusDateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'PCN':
                Order::where('id', $order->id)->update(['status' => 'cancelled']);
                TrackingHelper::PushChannelStatus($order,'cancelled',$statusDateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'OFD':
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                TrackingHelper::PushChannelStatus($order,'out_for_delivery',$statusDateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case 'ITR':
            case 'PCK':
            case 'DTH':
            case 'RCH':
            case 'RAH':
            case 'RAD':
            case 'ARD':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                TrackingHelper::PushChannelStatus($order,'in_transit');
                break;
            case 'DLD':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $statusDateTime]);
                if ($order->order_type == 'cod') {
                    $codEntry = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => $statusDateTime,
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($codEntry);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $codEntry['amount']);
                }
                TrackingHelper::PushChannelStatus($order,'delivered');
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case "CBH":
            case "CRD":
                TrackingHelper::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'in_transit', 'rto_status' => 'y']);
                TrackingHelper::PushChannelStatus($order, 'in_transit');
                break;
            case "RTO":
                TrackingHelper::RTOOrder($order->id);
                // mark shipment as rto delivered
                $delivery_date = date('Y-m-d H:i:s', strtotime($statusDateTime));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                TrackingHelper::PushChannelStatus($order,'delivered');
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $statusCode,
            "status" => $data['Message'],
            "status_description" => $data['Message'],
            "remarks" =>  $data['Message'],
            "location" =>  $data['ReportingCity'],
            "updated_date" => $data["Time"] ?? $data["OrderDate"],
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
    }

    function bluedartwebhookStaging(Request $request)
    {
        $apiKey = $request->header('Authentication');
        if ($apiKey == "oF57gcJA194al4fi6sDLCd0NRs6Ya8DTQrAHeB2D5lwU4zv9d9UyjZrjZUo1U7rB") {
            $getRequest = $request->all();
//            foreach ($getRequest as $t){
//                $trackingData = $t['request']['statustracking'][0]['Shipment']['Scans']['ScanDetail'][0] ?? [];
//                $statusCode = $trackingData['ScanCode'] . '-' . $trackingData['ScanGroupType'];
//                echo $statusCode;
//                exit;
//            }
            return response()->json(['status' => true,'message'=> 'Status updated successfully','data' => "Success"]);
        }
        else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    function bluedartwebhook(Request $request)
    {
        $apiKey = $request->header('Authentication');
        if($apiKey != "694IeSeo1B17BKe06vYf1oxvszJMRXXmvqScmrjY0gHTm3XaUDU0tziOMbiu7hqa")
            return response()->json(['error' => 'Unauthorized'], 401);
        try {
            $tracking = $request->all();
            BluedartWebHookResponse::create(['awb_number' => $tracking['statustracking'][0]['Shipment']['WaybillNo'],'request' => json_encode($request->all()),'inserted' => date('Y-m-d H:i:s')]);
            $trackingData = $tracking['statustracking'][0]['Shipment']['Scans']['ScanDetail'][0] ?? [];
            if(!empty($trackingData['ScanCode']) && !empty($trackingData['ScanGroupType'])) {
                $statusCode = $trackingData['ScanCode'] . '-' . $trackingData['ScanGroupType'];
                $order = Order::where('awb_number', $tracking['statustracking'][0]['Shipment']['WaybillNo'])->whereIn('courier_partner', ['bluedart', 'bluedart_surface'])->whereNotIn('status', ['cancelled', 'shipped', 'pickup_requested'])->first();
                if(empty($order) && !empty($tracking['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation'])){
                    $order = Order::where('alternate_awb_number', preg_replace('/[^0-9]/','',$tracking['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']))->whereIn('courier_partner', ['bluedart', 'bluedart_surface'])->whereNotIn('status', ['cancelled', 'shipped', 'pickup_requested'])->first();
                }
                if (!empty($order)) {
                    Order::where('id',$order->id)->update(['last_sync' => date('Y-m-d H:i:s')]);
                    $sellerData = Seller::find($order->seller_id);
                    if(empty($order->alternate_awb_number) && !empty($tracking['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']) && $statusCode == '074-RT'){
                        $altAwb = preg_replace('/[^0-9]/','',$tracking['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']);
                        $order->alternate_awb_number = $altAwb;
                        $order->save();
//                        Order::where('id',$order->id)->whereNull('alternate_awb_number')->update(['alternate_awb_number' => $altAwb]);
                    }
                    if(!empty($tracking['statustracking'][0]['Shipment']['PickUpDate']) && !empty($tracking['statustracking'][0]['Shipment']['PickUpTime'])){
                        $pickupTime = date('Y-m-d', strtotime($tracking['statustracking'][0]['Shipment']['PickUpDate'])) . " " . date('H:i:s', strtotime($tracking['statustracking'][0]['Shipment']['PickUpTime']));
                        Order::where('id',$order->id)->whereNotIn('status',['manifested','shipped','pickup_scheduled'])->whereNull('pickup_time')->update(['pickup_time' => $pickupTime]);
                    }
                    $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                    if ($order_tracking != null) {
                        $datetime = date('Y-m-d', strtotime($trackingData['ScanDate']));
                        $trackingDateTime = $order_tracking->updated_date != "" ? date('Y-m-d', strtotime($order_tracking->updated_date)) : date('Y-m-d');
                        if ($datetime >= $trackingDateTime){
                            if ($order_tracking->status_code != $statusCode) {
                                self::HandleBluedartTracking($order,$sellerData,$trackingData);
                                Order::where('id',$order->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
                            }
                        }
                        else{
                            $ignoreTracking = [
                                'courier' => 'bluedart',
                                'awb_number' => $order->awb_number,
                                'courier_status' => $statusCode." - .(".$trackingData['Scan'].")",
                                'Twinnship_status' => $order->status,
                                'received_date' => date('Y-m-d H:i:s'),
                                'courier_scan_date' => date('Y-m-d', strtotime($trackingData['ScanDate'])) . " " . date('H:i:s', strtotime($trackingData['ScanTime'])),
                                'inserted' => date('Y-m-d H:i:s')
                            ];

                            $this->handleBluedartUnorganizedTracking($ignoreTracking);
                        }
                    } else {
                        self::HandleBluedartTracking($order,$sellerData,$trackingData);
                        Order::where('id',$order->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
                    }

                }
            }
            return response()->json(['status' => true, 'message' => 'Status updated successfully', 'data' => "Success"]);
        }catch(Exception $e){
            Logger::write('logs/api/bluedart-webhook-exception-se-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => ['status' => 500,'message' => 'Something went wrong','error' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]
            ]);
            return response()->json(['status' => 500,'message' => 'Something went wrong']);
        }
    }

    function bluedartWebhookNSE(Request $request){
        $apiKey = $request->header('Authentication');
        if($apiKey != "694IeSeo1B17BKe06vYf1oxvszJMRXXmvqScmrjY0gHTm3XaUDU0tziOMbiu7hqa")
            return response()->json(['error' => 'Unauthorized'], 401);
        $getRequest = $request->all();
        $responseData = [];
        foreach ($getRequest as $tracking) {
            try {
                $singleResponse = ['id' => $tracking['id'],'status' => false];
                BluedartWebHookResponse::create(['awb_number' => $tracking['awb_number'], 'request' => json_encode($tracking['request']), 'inserted' => date('Y-m-d H:i:s'),'is_alpha' => 'nse']);
                $trackingData = $tracking['request']['statustracking'][0]['Shipment']['Scans']['ScanDetail'][0] ?? [];
                if (!empty($trackingData['ScanCode']) && !empty($trackingData['ScanGroupType'])) {
                    $statusCode = $trackingData['ScanCode'] . '-' . $trackingData['ScanGroupType'];
                    $order = Order::where('awb_number', $tracking['awb_number'])->whereIn('courier_partner', ['bluedart', 'bluedart_surface'])->whereNotIn('status', ['cancelled', 'shipped', 'pickup_requested'])->first();
                    if (empty($order) && !empty($tracking['request']['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation'])) {
                        $order = Order::where('alternate_awb_number', preg_replace('/[^0-9]/','',$tracking['request']['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']))->whereIn('courier_partner', ['bluedart', 'bluedart_surface'])->whereNotIn('status', ['cancelled', 'shipped', 'pickup_requested'])->first();
                    }
                    if (!empty($order)) {
                        Order::where('id',$order->id)->update(['last_sync' => date('Y-m-d H:i:s')]);
                        $sellerData = Seller::find($order->seller_id);
                        if(empty($order->alternate_awb_number) && !empty($tracking['request']['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']) && $statusCode == '074-RT'){
                            $altAwb = preg_replace('/[^0-9]/','',$tracking['request']['statustracking'][0]['Shipment']['Scans']['DeliveryDetails']['Relation']);
                            $order->alternate_awb_number = $altAwb;
                            $order->save();
                        }
                        if(!empty($tracking['request']['statustracking'][0]['Shipment']['PickUpDate']) && !empty($tracking['request']['statustracking'][0]['Shipment']['PickUpTime'])){
                            $pickupTime = date('Y-m-d', strtotime($tracking['request']['statustracking'][0]['Shipment']['PickUpDate'])) . " " . date('H:i:s', strtotime($tracking['request']['statustracking'][0]['Shipment']['PickUpTime']));
                            Order::where('id',$order->id)->whereNotIn('status',['manifested','shipped','pickup_scheduled'])->whereNull('pickup_time')->update(['pickup_time' => $pickupTime]);
                        }
                        $order_tracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->first();
                        if (!empty($order_tracking)) {
                            $datetime = date('Y-m-d', strtotime($trackingData['ScanDate']));
                            $trackingDateTime = $order_tracking->updated_date != "" ? date('Y-m-d', strtotime($order_tracking->updated_date)) : date('Y-m-d');
                            if($datetime >= $trackingDateTime){
                                if ($order_tracking->status_code != $statusCode ) {
                                    self::HandleBluedartTracking($order,$sellerData,$trackingData);
                                    Order::where('id',$order->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
                                }
                            }
                            else{
                                $ignoreTracking = [
                                    'courier' => 'bluedart',
                                    'awb_number' => $order->awb_number,
                                    'courier_status' => $statusCode." - .(".$trackingData['Scan'].")",
                                    'Twinnship_status' => $order->status,
                                    'received_date' => date('Y-m-d H:i:s'),
                                    'courier_scan_date' => date('Y-m-d', strtotime($trackingData['ScanDate'])) . " " . date('H:i:s', strtotime($trackingData['ScanTime'])),
                                    'inserted' => date('Y-m-d H:i:s')
                                ];

                                $this->handleBluedartUnorganizedTracking($ignoreTracking);
                            }
                        }
                        else {
                            self::HandleBluedartTracking($order,$sellerData,$trackingData);
                            Order::where('id',$order->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
                        }
                    }
                }
                $singleResponse['status'] = true;
                $responseData[]=$singleResponse;
            }
            catch(Exception $e){
                Logger::write('logs/api/bluedart-webhook-exception'.date('Y-m-d').'.text', [
                    'title' => 'Webhook Response Payload:',
                    'data' => ['status' => 500,'message' => 'Something went wrong','error' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]
                ]);
                continue;
            }
        }
        return response()->json(['status' => true, 'message' => 'Status updated successfully', 'data' => $responseData]);
    }

    function HandleBluedartTracking($order,$sellerData,$trackingData){
        $dateTime = date('Y-m-d', strtotime($trackingData['ScanDate'])) . " " . date('H:i:s', strtotime($trackingData['ScanTime']));
        $statusCode = $trackingData['ScanCode'] . '-' . $trackingData['ScanGroupType'];
        switch ($statusCode) {
            case '025-RT':
            case '130-RT':
                if(!empty($order->alternate_awb_number))
                    TrackingHelper::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'damaged']);
                TrackingHelper::PushChannelStatus($order, 'damaged', $dateTime);
                break;
            case '500-S':
            case '500-T':
            case '501-S':
            case '501-T':
            case '502-S':
            case '502-T':
            case '503-T':
            case '504-S':
            case '504-T':
            case '505-T':
            case '506-T':
            case '507-T':
            case '508-T':
            case '509-T':
            case '510-T':
            case '511-T':
            case '512-T':
            case '513-T':
            case '514-T':
            case '531-T':
            case '532-T':
            case '533-T':
            case '534-T':
            case '535-T':
            case '536-T':
            case '537-T':
            case '539-T':
            case '540-T':
            case '541-T':
            case '542-T':
            case '543-T':
            case '544-T':
            case '555-T':
            case '590-T':
            case '591-T':
            case '592-T':
            case '593-T':
            case '594-T':
            case '595-T':
            case '353-T':
            case '351-T':
            case '596-T':
            case '098-T':
            case '352-T':
            case '505-S':
            case '506-S':
            case '561-T':
            case '562-T':
            case '563-T':
            case '564-T':
            case '565-T':
            case '030-S':
            case '566-T':
                if($order->status == 'manifested') {
                    Order::where('id', $order->id)->update(['status' => 'pickup_scheduled', 'pickup_schedule' => 'y']);
                    TrackingHelper::PushChannelStatus($order, 'pickup_scheduled', $dateTime);
                }
                break;
            case '015-S':
            case '001-S':
                if($order->status == 'manifested' || $order->status == 'pickup_scheduled') {
                    Order::where('id', $order->id)->update(['status' => 'picked_up', 'pickup_done' => 'y', 'pickup_schedule' => 'y', 'pickup_time' => $dateTime]);
                }
                TrackingHelper::PushChannelStatus($order, 'picked_up', $dateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case '002-RT':
            case '035-RT':
                if(!empty($order->alternate_awb_number))
                    TrackingHelper::RTOOrder($order->id);
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                TrackingHelper::PushChannelStatus($order, 'out_for_delivery', $dateTime);
                break;
            case '033-T':
            case '174-T':
            case '065-T':
            case '221-T':
            case '003-S':
            case '004-S':
            case '005-S':
            case '007-S':
            case '007-T':
            case '010-S':
            case '011-S':
            case '012-S':
            case '017-T':
            case '020-S':
            case '021-S':
            case '022-S':
            case '023-S':
            case '024-T':
            case '027-T':
            case '029-T':
            case '032-T':
            case '035-T':
            case '036-T':
            case '037-T':
            case '045-T':
            case '046-T':
            case '048-T':
            case '049-T':
            case '050-T':
            case '052-T':
            case '054-T':
            case '055-T':
            case '057-T':
            case '058-T':
            case '059-T':
            case '068-T':
            case '073-T':
            case '077-T':
            case '078-T':
            case '095-T':
            case '100-S':
            case '100-T':
            case '101-T':
            case '103-T':
            case '132-T':
            case '133-T':
            case '135-T':
            case '136-T':
            case '143-T':
            case '147-T':
            case '154-T':
            case '178-T':
            case '186-T':
            case '206-T':
            case '207-T':
            case '210-T':
            case '014-S':
            case '030-T':
            case '301-T':
            case '312-T':
            case '303-T':
            case '308-T':
            case '309-T':
            case '313-T':
            case '305-T':
            case '311-T':
            case '314-T':
            case '306-T':
            case '307-T':
            case '166-T':
            case '027-S':
            case '302-T':
            case '304-T':
            case '026-T':
            case '008-S':
            case '026-S':
            case '185-T':
            case '140-T':
            case '181-T':
            case '219-T':
            case '189-T':
            case '224-T':
            case '220-T':
            case '191-T':
            case '192-T':
            case '127-S':
            case '193-T':
            case '037-S':
            case '137-S':
            case '124-T':
            case '200-T':
                Order::where('id', $order->id)->update(['status' => 'in_transit']);
                TrackingHelper::PushChannelStatus($order, 'in_transit', $dateTime);
                break;
            case '000-T':
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $dateTime]);
                if ($order->order_type == 'cod') {
                    $data = array(
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'amount' => $order->invoice_amount,
                        'type' => 'c',
                        'datetime' => date('Y-m-d H:i:s'),
                        'description' => 'Order COD Amount Credited',
                        'redeem_type' => 'o',
                    );
                    COD_transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
                }
                TrackingHelper::PushChannelStatus($order, 'delivered', $dateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case '105-T':
            case '188-RT':
            case "000-RT":
            case '105-RT':
                if ($order->o_type == "forward") {
                    if(!empty($order->alternate_awb_number))
                        TrackingHelper::RTOOrder($order->id);
                }
                $delivery_date = date('Y-m-d', strtotime($trackingData['ScanDate']));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                TrackingHelper::PushChannelStatus($order, 'delivered', $dateTime);
                break;
            case '188-T':
                TrackingHelper::RTOOrder($order->id);
                $delivery_date = date('Y-m-d', strtotime($trackingData['ScanDate']));
                Order::where('id', $order->id)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                TrackingHelper::PushChannelStatus($order, 'delivered', $dateTime);
                break;
            case '001-T':
            case '002-T':
            case '003-T':
            case '004-T':
            case '005-T':
            case '008-T':
            case '009-T':
            case '010-T':
            case '011-T':
            case '012-T':
            case '013-T':
            case '014-T':
            case '015-T':
            case '019-T':
            case '020-T':
            case '034-T':
            case '044-T':
            case '062-T':
            case '066-T':
            case '067-T':
            case '071-T':
            case '076-T':
            case '080-T':
            case '096-T':
            case '097-T':
            case '099-T':
            case '106-T':
            case '107-T':
            case '110-T':
            case '111-T':
            case '129-T':
            case '137-T':
            case '139-T':
            case '142-T':
            case '145-T':
            case '146-T':
            case '148-T':
            case '150-T':
            case '151-T':
            case '152-T':
            case '175-T':
            case '201-T':
            case '202-T':
            case '203-T':
            case '204-T':
            case '205-T':
            case '208-T':
            case '211-T':
            case '212-T':
            case '213-T':
            case '214-T':
            case '215-T':
            case '006-S':
            case '070-T':
            case '315-T':
            case '217-T':
            case '218-T':
            case '187-T':
            case '106-S':
            case '179-T':
            case '180-T':
            case '182-T':
            case '183-T':
            case '777-T':
            case '223-T':
            case '222-T':
            case '316-T':
            case '121-T':
            case '120-T':
            case '042-T':
            case '056-T':
            case '190-T':
            case '029-S':
            case '024-S':
            case '025-S':
                if ($order->rto_status != 'y') {
                    Order::where('id', $order->id)->update(['ndr_raised_time' => $dateTime, 'status' => 'ndr', 'ndr_status' => 'y', 'reason_for_ndr' => $trackingData['Scan'], 'ndr_action' => 'pending', 'ndr_status_date' => $trackingData['ScanDate']]);
                    $attempt = [
                        'seller_id' => $order->seller_id,
                        'order_id' => $order->id,
                        'raised_date' => $dateTime,
                        'raised_time' => $dateTime,
                        'action_by' => 'bluedart',
                        'reason' => $trackingData['Scan'],
                        'action_status' => 'pending',
                        'remark' => 'pending',
                        'u_address_line1' => 'new address line 1',
                        'u_address_line2' => 'new address line 2',
                        'updated_mobile' => ''
                    ];
                    Ndrattemps::create($attempt);
                    TrackingHelper::PushChannelStatus($order, 'ndr', $dateTime);
                }
                break;
            case '003-RT':
            case '004-RT':
            case '008-RT':
            case '009-RT':
            case '015-RT':
            case '019-RT':
            case '020-RT':
            case '034-RT':
            case '044-RT':
            case '060-RT':
            case '062-RT':
            case '066-RT':
            case '067-RT':
            case '071-RT':
            case '076-RT':
            case '080-RT':
            case '096-RT':
            case '097-RT':
            case '099-RT':
            case '106-RT':
            case '107-RT':
            case '110-RT':
            case '111-RT':
            case '137-RT':
            case '139-RT':
            case '142-RT':
            case '145-RT':
            case '146-RT':
            case '148-RT':
            case '150-RT':
            case '151-RT':
            case '152-RT':
            case '175-RT':
            case '201-RT':
            case '202-RT':
            case '203-RT':
            case '204-RT':
            case '205-RT':
            case '208-RT':
            case '211-RT':
            case '212-RT':
            case '213-RT':
            case '214-RT':
            case '215-RT':
            case '070-RT':
            case '310-RT':
            case '315-RT':
            case '217-RT':
            case '218-RT':
            case '187-RT':
            case '179-RT':
            case '180-RT':
            case '182-RT':
            case '183-RT':
            case '777-RT':
            case '222-RT':
            case '316-RT':
            case '120-RT':
            case '042-RT':
            case '056-RT':
            case '190-RT':
            case '029-RS':
            case '024-RS':
            case '025-RS':
            case '129-RT':
            case '006-RS':
            case '106-RS':
            case '121-RT':
                if ($order->o_type == 'forward') {
                    if(!empty($order->alternate_awb_number))
                        TrackingHelper::RTOOrder($order->id);
                    Order::where('id', $order->id)->update(['status' => 'in_transit']);
                    TrackingHelper::PushChannelStatus($order, 'in_transit', $dateTime);
                }
                break;
            case '021-T':
                Order::where('id', $order->id)->update(['status' => 'lost']);
                TrackingHelper::PushChannelStatus($order, 'lost', $dateTime);
                break;
            case '021-RT':
                if ($order->o_type == "forward") {
                    if(!empty($order->alternate_awb_number))
                        TrackingHelper::RTOOrder($order->id);
                }
                Order::where('id', $order->id)->update(['status' => 'lost']);
                TrackingHelper::PushChannelStatus($order, 'lost', $dateTime);
                break;
            case '016-S':
            case '017-S':
            case '018-S':
            case '019-S':
            case '016-T':
            case '060-T':
            case '123-T':
            case '104-T':
            case '074-RT':
            case '104-RT':
            case '123-RT':
            case '223-RT':
            case '351-RT': //RTO Pickup Scheduled
            case '353-RT': //RTO Pickup Scheduled
            case '098-RT': //RTO Pickup Scheduled
            case '352-RT': //RTO Pickup Scheduled
            case '209-T':
            case '209-RT':
                if(!empty($order->alternate_awb_number)) {
                    TrackingHelper::RTOOrder($order->id);
                    TrackingHelper::PushChannelStatus($order, 'rto_initiated', $dateTime);
                }
                break;
            case '025-T':
            case '130-T':
                Order::where('id', $order->id)->update(['status' => 'damaged']);
                TrackingHelper::PushChannelStatus($order, 'damaged', $dateTime);
                break;
            case '002-S':
                if ($order->rto_status != 'y') {
                    if ($order->ndr_status == 'y' && $dateTime != $order->ndr_status_date) {
                        //make attempt here
//                        $attempt = [
//                            'seller_id' => $order->seller_id,
//                            'order_id' => $order->id,
//                            'raised_date' => $dateTime,
//                            'raised_time' => $dateTime,
//                            'action_by' => 'bluedart',
//                            'reason' => $order->reason_for_ndr,
//                            'action_status' => 'requested',
//                            'remark' => 'requested',
//                            'u_address_line1' => 'new address line 1',
//                            'u_address_line2' => 'new address line 2',
//                            'updated_mobile' => ''
//                        ];
//                        Ndrattemps::create($attempt);
                        Order::where('id', $order->id)->update(['ndr_status_date' => $dateTime]);
                    }
                }
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);
                TrackingHelper::PushChannelStatus($order, 'out_for_delivery', $dateTime);
                TrackingHelper::CheckAndSendSMS($order);
                break;
            case '001-RS':
            case '001-RT':
            case '002-RS':
            case '003-RS':
            case '004-RS':
            case '005-RS':
            case '005-RT':
            case '007-RS':
            case '007-RT':
            case '010-RS':
            case '010-RT':
            case '011-RS':
            case '011-RT':
            case '012-RS':
            case '012-RT':
            case '013-RT':
            case '014-RT':
            case '015-RS':
            case '016-RT':
            case '017-RT':
            case '020-RS':
            case '021-RS':
            case '022-RS':
            case '023-RS':
            case '024-RT':
            case '027-RT':
            case '029-RT':
            case '032-RT':
            case '033-RT':
//            case '035-RT':
            case '036-RT':
            case '037-RT':
            case '045-RT':
            case '046-RT':
            case '048-RT':
            case '049-RT':
            case '050-RT':
            case '052-RT':
            case '054-RT':
            case '055-RT':
            case '057-RT':
            case '058-RT':
            case '059-RT':
            case '068-RT':
            case '073-RT':
            case '077-RT':
            case '078-RT':
            case '095-RT':
            case '100-RS':
            case '100-RT':
            case '101-RT':
            case '103-RT':
            case '132-RT':
            case '133-RT':
            case '135-RT':
            case '136-RT':
            case '143-RT':
            case '147-RT':
            case '154-RT':
            case '174-RT':
            case '178-RT':
            case '186-RT':
            case '206-RT':
            case '207-RT':
            case '210-RT':
            case '014-RS':
            case '065-RT':
            case '030-RT':
            case '301-RT':
            case '312-RT':
            case '303-RT':
            case '308-RT':
            case '309-RT':
            case '313-RT':
            case '305-RT':
            case '311-RT':
            case '314-RT':
            case '306-RT':
            case '307-RT':
            case '166-RT':
            case '027-RS':
            case '302-RT':
            case '304-RT':
            case '026-RT':
            case '008-RS':
            case '026-RS':
            case '185-RT':
            case '140-RT':
            case '181-RT':
            case '219-RT':
            case '189-RT':
            case '224-RT':
            case '221-RT':
            case '220-RT':
            case '191-RT':
            case '192-RT':
            case '127-RS':
            case '193-RT':
            case '037-RS':
            case '137-RS':
            case '124-RT':
            case '200-RT':
                if ($order->o_type == "forward") {
                    if(!empty($order->alternate_awb_number))
                        TrackingHelper::RTOOrder($order->id);
                    Order::where('id', $order->id)->update(['status' => 'in_transit']);
                    TrackingHelper::PushChannelStatus($order, 'in_transit', $dateTime);
                }
                break;
        }
        $data = [
            "awb_number" => $order->awb_number,
            "status_code" => $statusCode,
            "status" => $trackingData['Scan'],
            "status_description" => $trackingData['Scan'],
            "remarks" => $trackingData['Scan'],
            "location" => $trackingData['ScannedLocation'],
            "updated_date" => $dateTime,
            'created_at' => date('Y-m-d H:i:s')
        ];
        OrderTracking::create($data);
        return true;
    }


    function handleBluedartUnorganizedTracking($trackingData){
        CourierUnorganisedTracking::create($trackingData);
    }
}
