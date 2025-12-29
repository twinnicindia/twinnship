<?php

namespace App\Http\Controllers;

use App\Helper\Channels\ShopifyHelper;
use App\Helper\ReassignHelper;
use App\Helpers\TrackingHelper;
use App\Jobs\GenerateInvoice;
use App\Jobs\GenerateLabels;
use App\Libraries\AmazonDirect;
use App\Libraries\BlueDart;
use App\Libraries\BluedartRest;
use App\Libraries\Delhivery;
use App\Libraries\Dtdc;
use App\Libraries\Ekart;
use App\Libraries\Movin;
use App\Libraries\PickNDel;
use App\Libraries\Professional;
use App\Libraries\Shadowfax;
use App\Libraries\Smartr;
use App\Libraries\SMCNew;
use App\Libraries\XpressBees;
use App\Models\Channel_orders_log;
use App\Models\ChannelOrderStatusList;
use App\Models\COD_transactions;
use App\Models\Channels;
use App\Models\CourierUnorganisedTracking;
use App\Models\Employees;
use App\Models\EmployeeWorkLogs;
use App\Models\InternationalOrders;
use App\Models\InvalidContact;
use App\Models\Invoice;
use App\Models\Invoice_orders;
use App\Models\Manifest;
use App\Models\ManifestationIssues;
use App\Models\ManifestOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReassignOrderDetails;
use App\Models\Seller;
use App\Models\SellerInfoLogs;
use App\Models\ServiceablePincodeFM;
use App\Models\Warehouses;
use Carbon\Carbon;
use App\Models\Partners;
use App\Models\ServiceablePincode;
use App\Models\SKU;
use App\Models\Transactions;
use App\Models\WeightReconciliation;
use App\Models\OrderTracking;
use App\Models\States;
use App\Models\Ndrattemps;
use App\Models\Basic_informations;
use App\Models\Configuration;
use App\Models\LabelCustomization;
use App\Models\MPS_AWB_Number;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use App\Models\PendingShipments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Libraries\Logger;
use Automattic\WooCommerce\Client;
use App\Libraries\Barcode;
use App\Libraries\Gati;
use App\Libraries\MyUtility;
use App\Libraries\Prefexo;
use App\Libraries\MarutiEcom;
use Exception;
use App\Exports\FulfilAmazonFeedFlatFile;
use App\Imports\AmazonReportFileImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Jobs\GenerateLabel;
use App\Models\DownloadReport;
use App\Models\SellerRateChangeDetails;
use App\Models\Rates;
use Throwable;
use ZipArchive;
use PDF;

class CroneController extends Controller
{
    protected $partnerNames,$amazonPartnerNames,$utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
        $this->partnerNames=[
            'amazon_swa' => 'AmazonSwa',
            'amazon_swa_10kg' => 'AmazonSwa',
            'amazon_swa_1kg' => 'AmazonSwa',
            'amazon_swa_3kg' => 'AmazonSwa',
            'amazon_swa_5kg' => 'AmazonSwa',
            'bluedart' => 'Bluedart',
            'bluedart_surface' => 'Bluedart',
            'shadow_fax' => 'Shadowfax',
            'delhivery_surface' => 'Delhivery',
            'delhivery_surface_1kg' => 'Delhivery',
            'delhivery_surface_10kg' => 'Delhivery',
            'delhivery_surface_20kg' => 'Delhivery',
            'delhivery_surface_2kg' => 'Delhivery',
            'delhivery_surface_5kg' => 'Delhivery',
            'delhivery_lite' => 'Delhivery',
            'dtdc_surface' => 'DTDC',
            'dtdc_10kg' => 'DTDC',
            'dtdc_2kg' => 'DTDC',
            'ekart' => 'Ekart Logistics',
            'ekart_2kg' => 'Ekart Logistics',
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
            'xpressbees_sfc'  => 'XpressBees'
        ];
        $this->amazonPartnerNames = [
            'amazon_swa' => 'Other',
            'amazon_swa_10kg' => 'Other',
            'amazon_swa_1kg' => 'Other',
            'amazon_swa_3kg' => 'Other',
            'amazon_swa_5kg' => 'Other',
            'bluedart' => 'BlueDart',
            'bluedart_surface' => 'BlueDart',
            'delhivery_surface' => 'Delhivery',
            'delhivery_surface_1kg' => 'Delhivery',
            'delhivery_surface_10kg' => 'Delhivery',
            'delhivery_surface_20kg' => 'Delhivery',
            'delhivery_surface_2kg' => 'Delhivery',
            'delhivery_surface_5kg' => 'Delhivery',
            'delhivery_lite' => 'Delhivery',
            'dtdc_10kg' => 'DTDC',
            'dtdc_6kg' => 'DTDC',
            'dtdc_1kg' => 'DTDC',
            'dtdc_2kg' => 'DTDC',
            'dtdc_3kg' => 'DTDC',
            'dtdc_5kg' => 'DTDC',
            'dtdc_express' => 'DTDC',
            'dtdc_surface' => 'DTDC',
            'ecom_express' => 'Ecom Express',
            'ecom_express_rvp' => 'Ecom Express',
            'ecom_express_3kg' => 'Ecom Express',
            'ecom_express_3kg_rvp' => 'Ecom Express',
            'fedex' => 'FedEx',
            'shadow_fax' => 'Other',
            'smartr' => 'Smartrlogistics',
            'udaan' => 'Other',
            'udaan_10kg' => 'Other',
            'udaan_1kg' => 'Other',
            'udaan_2kg' => 'Other',
            'udaan_3kg' => 'Other',
            'wow_express' => 'Other',
            'bombax' => 'Bombax',
            'shree_maruti' => 'Shree Maruti Courier',
            'shree_maruti_ecom' => 'Shree Maruti Courier',
            'smc_new' => 'Shree Maruti Courier',
            'shree_maruti_ecom_1kg' => 'Shree Maruti Courier',
            'shree_maruti_ecom_3kg' => 'Shree Maruti Courier',
            'shree_maruti_ecom_5kg' => 'Shree Maruti Courier',
            'shree_maruti_ecom_10kg' => 'Shree Maruti Courier',
            'xpressbees_sfc' => 'Xpressbees',
            'xpressbees_surface' => 'Xpressbees',
            'xpressbees_surface_10kg' => 'Xpressbees',
            'xpressbees_surface_1kg' => 'Xpressbees',
            'xpressbees_surface_3kg' => 'Xpressbees',
            'xpressbees_surface_5kg' => 'Xpressbees',
            'tpc_surface' => 'The Professional Couriers',
            'tpc_1kg' => 'The Professional Couriers',
            'pick_del' => 'Pick & Del',
        ];
    }

    function generate_invoices(){
        try {
            if(date('d') != 16 && date('d') != 1){
                return true;
            }
            $startedAt = now();
            $cronName = 'generate-invoices';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $sellers=Seller::get();
            $all_sellers=[];
            $current_year = date("y");
            $next_year =  $current_year + 1;
            $invoiceDate = date('Y-m-d',strtotime(date('Y-m-d')." -1 days"));
            foreach ($sellers as $s){
                $all_sellers[]=$s->id;
                $orders=Order::select('id','total_charges','awb_assigned_date')->where('status','delivered')->where('invoice_status','n')->where('seller_id',$s->id)->get();
                //generate invoice here
                if(count($orders)!=0){
                    $invoiceNumber = Invoice::max('invoice_number') + 1;
                    $invoiceData=[
                        'seller_id' => $s->id,
                        'inv_id' => "TW/$current_year"."$next_year/$invoiceNumber",
                        'invoice_date' => $invoiceDate,
                        'due_date' => date('Y-m-d',strtotime(date('Y-m-d')." +7 days")),
                        'status' => 'Paid',
                        'type' => 'f',
                        'invoice_number' => $invoiceNumber
                    ];
                    // dd($invoiceData);
                    //print_r($invoiceData);
                    $invoice_id=Invoice::create($invoiceData)->id;
                    $total_charges=0;
                    $all_orders=[];
                    foreach ($orders as $o){
                        //insert record in invoice_orders and update invoice_status of order
                        $invoiceOrders=[
                            'invoice_id' => $invoice_id ?? 0,
                            'order_id' => $o->id
                        ];
                        //print_r($invoiceOrders);
                        Invoice_orders::create($invoiceOrders);
                        $all_orders[]=$o->id;
                        $total_charges+=$o->total_charges;
                        $rowInserted++;
                    }
                    Order::whereIn('id',$all_orders)->update(['invoice_status'=>'y']);
                    $invoice_amount=($total_charges * 100)/ 118;
                    $charge=$total_charges - $invoice_amount;
                    $rowUpdated += Invoice::where('id',$invoice_id)->update(['gst_amount' => $charge,'invoice_amount' => $invoice_amount,'total' => $total_charges]);
                    // echo $invoice_amount." = ".$charge;
                }
            }
            Seller::whereIn('id',$all_sellers)->update(['invoice_date' => date('Y-m-d',strtotime(date('Y-m-d')." +15 days"))]);
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    function GenerateSellerInvoice($sellerId){
        try {
            $startedAt = now();
            $cronName = 'generate-invoices';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $sellers=Seller::where('id', $sellerId)->get();
            $all_sellers=[];
            $current_year = date("y");
            $next_year =  $current_year + 1;
            $invoiceDate = date('Y-m-d',strtotime(date('Y-m-d')." -1 days"));
            foreach ($sellers as $s){
                $all_sellers[]=$s->id;
                $orders=Order::select('id','total_charges','awb_assigned_date')->where('status','delivered')->where('invoice_status','n')->where('seller_id',$s->id)->get();
                //generate invoice here
                if(count($orders)!=0){
                    $invoiceNumber = Invoice::max('invoice_number') + 1;
                    $invoiceData=[
                        'seller_id' => $s->id,
                        'inv_id' => "TW/$current_year"."$next_year/$invoiceNumber",
                        'invoice_date' => $invoiceDate,
                        'due_date' => date('Y-m-d',strtotime(date('Y-m-d')." +7 days")),
                        'status' => 'Paid',
                        'type' => 'f',
                        'invoice_number' => $invoiceNumber
                    ];
                    // dd($invoiceData);
                    //print_r($invoiceData);
                    $invoice_id=Invoice::create($invoiceData)->id;
                    $total_charges=0;
                    $all_orders=[];
                    foreach ($orders as $o){
                        //insert record in invoice_orders and update invoice_status of order
                        $invoiceOrders=[
                            'invoice_id' => $invoice_id ?? 0,
                            'order_id' => $o->id
                        ];
                        //print_r($invoiceOrders);
                        Invoice_orders::create($invoiceOrders);
                        $all_orders[]=$o->id;
                        $total_charges+=$o->total_charges;
                        $rowInserted++;
                    }
                    Order::whereIn('id',$all_orders)->update(['invoice_status'=>'y']);
                    $invoice_amount=($total_charges * 100)/ 118;
                    $charge=$total_charges - $invoice_amount;
                    $rowUpdated += Invoice::where('id',$invoice_id)->update(['gst_amount' => $charge,'invoice_amount' => $invoice_amount,'total' => $total_charges]);
                }
            }
            Seller::whereIn('id',$all_sellers)->update(['invoice_date' => date('Y-m-d',strtotime(date('Y-m-d')." +15 days"))]);
            $notification=array(
                'notification' => array(
                    'type' => 'success',
                    'title' => 'Generated',
                    'message' => 'Invoice Generated successfully',
                ),
            );
            Session($notification);
            return back();
        } catch(Exception $e) {
            $notification=array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Failed',
                    'message' => 'Invoice Generation Failed',
                ),
            );
            Session($notification);
            return back();
        }
    }

    // function generate_invoices(){
    //     $sellers=Seller::where('invoice_date',null)->orWhere('invoice_date',date('Y-m-d'))->get();
    //     $all_sellers=[];
    //     $current_year = date("y");
    //     $next_year =  $current_year + 1;
    //     foreach ($sellers as $s){
    //         $all_sellers[]=$s->id;
    //         $orders=Order::where('status','delivered')->where('invoice_status','n')->where('seller_id',$s->id)->get();
    //         //generate invoice here
    //         if(count($orders)!=0){
    //             $rand = rand(111111, 555555);
    //             $invoiceData=[
    //                 'seller_id' => $s->id,
    //                 'inv_id' => "SEF/$current_year-$next_year/$rand",
    //                 'invoice_date' => date('Y-m-d'),
    //                 'due_date' => date('Y-m-d',strtotime(date('Y-m-d')." +7 days")),
    //                 'status' => 'Paid',
    //                 'type' => 'f',
    //             ];
    //             //print_r($invoiceData);
    //             $invoice_id=Invoice::create($invoiceData)->id;
    //             $total_charges=0;
    //             $all_orders=[];
    //             foreach ($orders as $o){
    //                 //insert record in invoice_orders and update invoice_status of order
    //                 $invoiceOrders=[
    //                     'invoice_id' => $invoice_id ?? 0,
    //                     'order_id' => $o->id
    //                 ];
    //                 //print_r($invoiceOrders);
    //                 Invoice_orders::create($invoiceOrders);
    //                 $all_orders[]=$o->id;
    //                 $total_charges+=$o->total_charges;
    //             }
    //             Order::whereIn('id',$all_orders)->update(['invoice_status'=>'y']);
    //             $invoice_amount=($total_charges * 100)/(100 + 18);
    //             $charge=$total_charges - $invoice_amount;
    //             Invoice::where('id',$invoice_id)->update(['gst_amount' => $charge,'invoice_amount' => $invoice_amount,'total' => $total_charges]);
    //             // echo $invoice_amount." = ".$charge;
    //         }
    //     }
    //     Seller::whereIn('id',$all_sellers)->update(['invoice_date' => date('Y-m-d',strtotime(date('Y-m-d')." +15 days"))]);
    //     echo json_encode(['message' => 'Invoice Generated Successfully']);
    // }

    function SendEmployeeReportMail(Request $request){
        $allSellers = Seller::where('employee_flag_enabled','y');
        $date = date('Y-m-d',strtotime('- 1 day'));
        // $date = '2022-08-01';
        if($request->seller != '')
            $allSellers = $allSellers->where('id',$request->seller);
        $allSellers = $allSellers->get();
        foreach($allSellers as $seller)
        {
            $mailContent = '';
            $allEmployees = Employees::where('seller_id',$seller->id)->get();
            foreach($allEmployees as $e){
                $mailContent .= "<div><h3 style='text-align:center;'>$e->employee_name</h3>";
                //DB::enableQueryLog();
                $logs = EmployeeWorkLogs::join('orders','orders.id','=','employee_work_logs.order_id')->join('employees','employees.id','=','employee_work_logs.employee_id')->select('employees.employee_name as employee','orders.awb_number','orders.customer_order_number','orders.courier_partner','employee_work_logs.*')->where('orders.seller_id',$seller->id)->where('employee_work_logs.employee_id',$e->id)->whereDate('employee_work_logs.inserted',$date)->get();
                //dd(DB::getQueryLog());
                $totalShipped = EmployeeWorkLogs::where('employee_id',$e->id)->where('operation','ship')->whereDate('inserted',$date)->count();
                $totalCancelled = EmployeeWorkLogs::where('employee_id',$e->id)->where('operation','cancel')->count();
                $mailContent .= "<p>Total Order Shipped : {$totalShipped}</p><p>Total Order Cancelled : {$totalCancelled}</p>";
                $mailContent .= "<table border='1'><tr><th>Sr.No</th><th>Order Number</th><th>AWB Number</th><th>Courier Partner</th><th>Operation</th><th>TimeStamp</th></tr>";
                $cnt=1;
                foreach($logs as $l){
                    $mailContent .= "<tr><td>{$cnt}</td><td>{$l->customer_order_number}</td><td>{$l->awb_number}</td><td>{$l->courier_partner}</td><td>{$l->operation}</td><td>{$l->inserted}</td></tr>";
                    $cnt++;
                }
                $mailContent .="</table></div>";
            }
            $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
            $email = "info.twinnship@gmail.com";
            $email1 = $seller->email;
            $subject = "Employee Work Report";
            $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
            $this->utilities->send_email($email1,"Twinnship Corporation",$subject,$mailContent,$subject);
        }
    }
    function getServicablePincodes($partner){
        switch ($partner){
            case 'delhivery_surface':
                $this->_getDelhiveryPincodes();
                break;
            case 'wow_express':
                $this->_getWowPincodes();
                break;
            case 'xpressbees_sfc':
                $this->_getXpressBeesPincodes();
                break;
            case 'shadow_fax';
                $this->_getShadowFaxPincodes();
        }
    }
    function _getShadowFaxPincodes(){
        $shadowFax = new Shadowfax();
        $pincodes = $shadowFax->getAllServiceablePincodes();
        $allPincodes = [];
        if(!empty($pincodes))
            ServiceablePincode::where('partner_id',2)->delete();
        foreach ($pincodes as $pincode){
            $allPincodes[]=[
                'partner_id' => 2,
                'courier_partner' => 'shadow_fax',
                'pincode' => $pincode['code'],
                'inserted' => date('Y-m-d H:i:s')
            ];
            if(count($allPincodes) == 10000){
                ServiceablePincode::insert($allPincodes);
                $allPincodes = [];
            }
        }
        ServiceablePincode::insert($allPincodes);
    }
    function _getXpressBeesPincodes(){
        file_put_contents("logs/service.txt",date('Y-m-d H:i:s')." as Indian Time serviceability called");
        $partner=Partners::where('keyword','xpressbees_sfc')->first();
        $token = MyUtility::GetXbeesToken("admin@Twinnship.com",'$Twinnship$',"e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc");
        $data=[
            'BusinessUnit' => 'eComm',
            'BusinessFlow' => 'Forward',
            'BusinessService' => 'Delivery'
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' =>  $token
        ])->post("https://xbmasterapi.xbees.in/expose/get/serviceabilitypincode/details",$data);
        $responseData = $response->json();
        if($responseData['ReturnCode'] == 100){
            if(count($responseData['ServicablePincodeDetails']) > 0){
                ServiceablePincode::where('courier_partner','like','%xpressbees%')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner','like','%xpressbees%')->where('active','n')->pluck('pincode')->toArray();
                foreach ($responseData['ServicablePincodeDetails'] as $p){
                    if(!in_array($p['pincode'],$disabledPincode)){
                        $pincodes[]=[
                            'partner_id' => $partner->id,
                            'courier_partner' => $partner->keyword,
                            'pincode' => $p['pincode'],
                            'city' => $p['cityname'],
                            'state' => $p['statename'],
                            'branch_code' => $p['HubName'],
                            'status' => 'y',
                            'inserted' => date('Y-m-d H:i:s')
                        ];
                    }
                    if(count($pincodes)==700)
                    {
                        ServiceablePincode::insert($pincodes);
                        $pincodes=[];
                    }
                }
                ServiceablePincode::insert($pincodes);
            }
        }
    }
    function _getDelhiveryPincodes(){
        $partner=Partners::where('keyword','delhivery_surface')->first();
        $url = 'https://track.delhivery.com/c/api/pin-codes/json';
        $data = array('key1' => 'value1', 'key2' => 'value2');
        $options = array(
            'http' => array(
                'header'  => "authorization: Token 894217b910b9e60d3d12cab20a3c5e206b739c8b\r\ncontent-type: application/json\r\n",
                'method'  => 'GET',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ exit; }
        $allPincodes=json_decode($result,true);
        ServiceablePincode::where('partner_id',$partner->id)->where('active','y')->delete();
        $disabledPincode = ServiceablePincode::where('courier_partner','like','delhivery_surface')->where('active','n')->pluck('pincode')->toArray();
        $pincodes=[];
        foreach ($allPincodes['delivery_codes'] as $p){
            if(!in_array($p['postal_code']['pin'],$disabledPincode)) {
                $pincodes[] = [
                    'partner_id' => $partner->id,
                    'courier_partner' => $partner->keyword,
                    'pincode' => $p['postal_code']['pin'],
                    'city' => $p['postal_code']['district'],
                    'state' => $p['postal_code']['state_code'],
                    'branch_code' => $p['postal_code']['sort_code'],
                    'status' => 'y',
                    'inserted' => date('Y-m-d H:i:s')
                ];
            }
            if(count($pincodes)==700)
            {
                ServiceablePincode::insert($pincodes);
                $pincodes=[];
            }
        }
        ServiceablePincode::insert($pincodes);
    }

    function _getWowPincodes()
    {
        $partner=Partners::where('keyword','wow_express')->first();
        ServiceablePincode::where('partner_id',$partner->id)->delete();
        $response = file_get_contents("https://wowship.wowexpress.in/index.php/api/pincode_master/pincode");
        $allPincodes = json_decode($response, true);
        $data = [];
        foreach ($allPincodes as $pin) {
            $data[] = [
                'partner_id' => $partner->id,
                'courier_partner' => $partner->keyword,
                'pincode' => $pin['pincode'],
                'city' => $pin['city'],
                'state' => $pin['state'],
                'branch_code' => $pin['branch_code'],
                'status' => $pin['status'],
                'inserted' => date('Y-m-d H:i:s')
            ];
            if(count($data)==700){
                ServiceablePincode::Insert($data);
                $data=[];
            }
        }
        ServiceablePincode::Insert($data);
        //$this->utilities->generate_notification('Successful', 'Your Pincode Service Added.', 'success');
        //return redirect()->back();
    }

    function updateCodAomunt(){
        $orders = Order::where('order_type','cod')->where('status','delivered')->get();
        // dd($orders);
        foreach($orders as $order){
            $data = array(
                'seller_id' => $order->seller_id,
                'order_id' => $order->id,
                'amount' => $order->invoice_amount,
                'type' => 'c',
                'datetime' => date('Y-m-d H:i:s',strtotime($order->delivered_date)),
                'description' => 'Order COD Amount Credited',
                'redeem_type' => 'o',
                'remitted_by' => 'seller'
            );
            // dd($data);
            COD_transactions::create($data);
            Seller::where('id', $order->seller_id)->increment('cod_balance', $data['amount']);
        }
    }

    function auto_accept_weight_reconciliation(){
        try {
            $startedAt = now();
            $cronName = 'auto-accept-weight-reconciliation';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $weight_recon  = WeightReconciliation::where('status', 'pending')->get();
            foreach($weight_recon as $w){
                $sellerData = Seller::find($w->seller_id);
                if(empty($sellerData)){
                    continue;
                }
                $date = date('Y-m-d', strtotime($w->created));
                $now = time(); // or your date as well
                $your_date = strtotime($date);
                $datediff = $now - $your_date;
                $difference= round($datediff / (60 * 60 * 24)) - 1;
                $remaining_days = $sellerData->reconciliation_days - $difference;
                // dd($remmaining_days);
                if($remaining_days < 1){
                    //dd('Auto Accept');
                    $rowUpdated += WeightReconciliation::where('id', $w->id)->update([
                        'status' => 'auto_accepted'
                    ]);
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function update_status_xbees(){
        // $order = Order::select('id','awb_number', 'courier_partner')->whereDate('delivered_date','0000-00-00 00:00:00')->get();
        $order = Order::select('id','awb_number', 'courier_partner','seller_id','order_type','invoice_amount')->where('status','manifested')->where('courier_partner','like','xpressbees%')->get();
        dd($order);
        $cnt = 0;
        foreach($order as $o){
            $order_tracking = OrderTracking::where('awb_number', $o->awb_number)->orderBy('id', 'desc')->first();
            if(!empty($order_tracking)){
                if($order_tracking->status_code == 'IT'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'in_transit','pickup_done' => 'y', 'pickup_schedule' => 'y']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'PUD'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'picked_up','pickup_done' => 'y', 'pickup_schedule' => 'y']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'OFD'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'out_for_delivery']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'DLVD'){
                    $delivery_date = date('Y-m-d H:i:s', strtotime($order_tracking->updated_date));
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                    if($o->order_type == 'cod'){
                        $data = array(
                            'seller_id' => $o->seller_id,
                            'order_id' => $o->id,
                            'amount' => $o->invoice_amount,
                            'type' => 'c',
                            'datetime' => $delivery_date,
                            'description' => 'Order COD Amount Credited',
                            'redeem_type' => 'o',
                        );
                        COD_transactions::create($data);
                        Seller::where('id', $o->seller_id)->increment('cod_balance', $data['amount']);
                    }
                    $cnt++;
                }
            }
            //   dd($order_tracking);
        }
        return response()->json(['message' => "$cnt order updated"]);
    }

    function update_status_delhivery(){
        // $order = Order::select('id','awb_number', 'courier_partner')->whereDate('delivered_date','0000-00-00 00:00:00')->get();
        $order = Order::select('id','awb_number', 'courier_partner','seller_id','order_type','invoice_amount')->where('status','manifested')->where('courier_partner','delhivery_surface')->get();
        dd($order);
        $cnt = 0;
        foreach($order as $o){
            $order_tracking = OrderTracking::where('awb_number', $o->awb_number)->orderBy('id', 'desc')->first();
            if(!empty($order_tracking)){
                if($order_tracking->status_code == 'X-PIOM'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'in_transit','pickup_done' => 'y', 'pickup_schedule' => 'y']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'X-PROM'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'picked_up','pickup_done' => 'y', 'pickup_schedule' => 'y']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'X-DDD3FD'){
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'out_for_delivery']);
                    $cnt++;
                }
                if($order_tracking->status_code == 'EOD-38'|| $order_tracking->status_code == 'EOD-37' || $order_tracking->status_code == 'EOD-135'){
                    $delivery_date = date('Y-m-d H:i:s', strtotime($order_tracking->updated_date));
                    Order::where('awb_number', $order_tracking->awb_number)->update(['status' => 'delivered', 'delivered_date' => $delivery_date]);
                    if($o->order_type == 'cod'){
                        $data = array(
                            'seller_id' => $o->seller_id,
                            'order_id' => $o->id,
                            'amount' => $o->invoice_amount,
                            'type' => 'c',
                            'datetime' => $delivery_date,
                            'description' => 'Order COD Amount Credited',
                            'redeem_type' => 'o',
                        );
                        COD_transactions::create($data);
                        Seller::where('id', $o->seller_id)->increment('cod_balance', $data['amount']);
                    }
                    $cnt++;
                }
            }
            //   dd($order_tracking);
        }
        return response()->json(['message' => "$cnt order updated"]);
    }
    function shipPendingOrders(){
        $shipments = new ShippingController();
        $orders = PendingShipments::where('status','n')->get();
        foreach ($orders as $o){
            $result = $shipments->shipOrder($o->order_id);
            if($result){
                PendingShipments::where('id',$o->id)->update(['shipped' => 'y','status' => 'y','last_tried' => date('Y-m-d H:i:s')]);
            }else{
                PendingShipments::where('id',$o->id)->update(['status' => 'y','last_tried' => date('Y-m-d H:i:s')]);
            }
        }
        echo json_encode(['status' => 'true','message' => 'Orders shipped successfully']);
    }
    function fetchChannelOrdersJob(Request $request){
        try {
            $startedAt = now();
            $cronName = 'fetch-channel-orders-job';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $startedAt = now();
            $channelController = new ShopifyController();
            // $channels = Channels::orderBy('last_executed')->whereIn('channel',['shopify','storehippo','woocommerce'])->where('seller_id',291)->get();
            $channels = Channels::orderBy('last_sync')->whereIn('channel',['shopify','storehippo','woocommerce'])->where('status','y') ;
            if($request->sellerId != ""){
                $sellerIDs = explode(",", $request->sellerId);
                $channels = $channels->whereIn('seller_id',$sellerIDs);
            }
            $channels = $channels->get();
            if(count($channels) > 0){
                foreach ($channels as $c) {
                    if($startedAt->diffInSeconds(now()) >= 1440) {
                        return true;
                    }
                    switch ($c->channel) {
                        case 'shopify':
                            ShopifyHelper::GetShopifyOrders($c);
                            break;
                        case 'storehippo';
                            $channelController->_fetchStoreHippoOrders($c);
                            break;
                        case 'woocommerce':
                            $channelController->_fetch_woocommerce($c);
                            break;
                        default:
                            echo "Channel Not Found";
                    }
                    Channels::where('id',$c->id)->update(['last_sync' => date('Y-m-d H:i:s')]);
                }
                //$this->utilities->generate_notification('Success', 'Order Fetched successfully', 'success');
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage()."-".$e->getFile()."-".$e->getLine(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()
            ]);
        }
    }
    function fetchAmazonOrdersJob(Request $request){
        try {
            $startedAt = now();
            $cronName = 'fetch-amazon-orders-job';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;
            //ini_set('max_execution_time', 1760);
            $startedAt = now();
            $lastExecuted = Carbon::now()->subMinute(30)->format('Y-m-d H:i:s');
            // dd($lastExecuted);
            $amazonController = new ChannelsController();
            // Exclude seller id 270
            // $channels = Channels::whereNotIn('seller_id', [270])->orderBy('last_executed')->where('last_executed','<=',$lastExecuted)->where('channel','amazon')->get();
            $channels = Channels::orderBy('last_executed')->where('last_executed','<=',$lastExecuted)->where('channel','amazon')->where('status','y');
            if($request->seller_id != "")
                $channels = $channels->where('seller_id',$request->seller_id);
            $channels = $channels->get();
            //dd($channels);
            if(count($channels) > 0){
                foreach ($channels as $c) {
                    if($startedAt->diffInSeconds(now()) >= 1760) {
                        return true;
                    }
                    $seller = Seller::find($c->seller_id);
                    if(!empty($seller))
                        $amazonController->_fetchAmazonOrders($c,$seller);
                    $rowUpdated += Channels::where('id',$c->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
                }
                //$this->utilities->generate_notification('Success', 'Order Fetched successfully', 'success');
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    function fetchAmazonOrdersCustom($sellerId,$from,$to){
        $amazonController = new ChannelsController();
        $seller = Seller::find($sellerId);
        $channel = Channels::where('seller_id',$sellerId)->where('channel','amazon')->first();
        if(!empty($seller) && !empty($channel))
            $amazonController->_fetchAmazonOrders($channel,$seller,$from,$to);
    }

    // send manifestation to Xpressbees,Ecom Express and Smartr , Delhivery Surface
    function sendManifestation(Request $request){
        try {
            $startedAt = now();
            $cronName = 'send-manifestation';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;
            $orderList = Order::where('manifest_sent','n')->whereNotIn('courier_partner',['shree_maruti'])->whereNotIn('seller_id',[1])->whereNotIn('status',['pending','cancelled'])->where('is_retry',0)->select('id')->orderBy('awb_assigned_date');
            if($request->awb_number != ""){
                $allAwbs = explode(',',$request->awb_number);
                $orderList = $orderList->whereIn('awb_number',$allAwbs);
            }
            if($request->courier_partner != "")
                $orderList = $orderList->where('courier_partner','like',"%{$request->courier_partner}%");
            if($request->seller_id != "")
                $orderList = $orderList->where('seller_id',$request->seller_id);
            $orderList = $orderList->get();
            foreach ($orderList as $singleOrder){
                if($startedAt->diffInSeconds(now()) >= 880) {
                    return true;
                }
                $o = Order::find($singleOrder->id);
                $sellerDetail = Seller::find($o->seller_id);
                if(empty($sellerDetail))
                    continue;
                if(empty($o))
                    continue;
                switch ($o->courier_partner){
                    case 'ecom_express':
                    case 'ecom_express_rvp':
                        $ecom = new EcomExpressController();
                        if($ecom->_ManifestEcomExpressOrder($o)){
                            $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                        }else{
                            Order::where('id',$o->id)->update(['is_retry' => 1]);
                        }
                        break;
                    case 'ecom_express_3kg':
                    case 'ecom_express_3kg_rvp':
                        $ecom = new EcomExpress3kgController();
                        if($ecom->_ManifestEcomExpressOrder($o)){
                            $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                        }
                        else{
                            Order::where('id',$o->id)->update(['is_retry' => 1]);
                        }
                        break;
                    case 'xpressbees_surface':
                        if($o->o_type == 'forward'){
                            $client = new XpressBees('air');
                            $responseData = $client->ShipOrder($o,$sellerDetail);
                            if(!empty($responseData['ReturnCode']) && !empty($responseData['ReturnMessage']) && $responseData['ReturnCode'] == 100 && strtolower($responseData['ReturnMessage']) == "successfull"){
                                Order::where('id',$o->id)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
                            }
                            else{
                                ManifestationIssues::updateOrCreate(
                                    ['order_id' => $o->id],
                                    [
                                        'order_id' => $o->id,
                                        'message' => $responseData['ReturnMessage'] ?? "",
                                        'created' => date('Y-m-d H:i:s')
                                    ]
                                );
                                if(!empty($responseData) && $responseData['ReturnMessage'] == 'Drop pincode not serviceable' || str_contains($responseData['ReturnMessage'],"ServiceType not accepted")){
                                    ServiceablePincode::where('courier_partner','xpressbees_sfc')->where('pincode',$o->s_pincode)->update(['active' => 'n','modified' => date('Y-m-d H:i:s'),'remark' => $responseData['ReturnMessage']]);
                                }
                                if(!empty($responseData) && $responseData['ReturnMessage'] == "AirWayBillNO Already exists"){
                                    Order::where('id',$o->id)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
                                }
                                Order::where('id',$o->id)->update(['is_retry' => 1]);
                            }
                        }else{
                            $client = new XpressBees('air');
                            $responseData = $client->ShipReverseOrder($o,$sellerDetail);
                            if(!empty($responseData['ReturnCode']) && !empty($responseData['ReturnMessage']) && $responseData['ReturnCode'] == 100 && strtolower($responseData['ReturnMessage']) == "successful"){
                                Order::where('id',$o->id)->update(['xb_token_number' => $responseData['TokenNumber'],'manifest_sent' => 'y']);
                            }
                            else{
                                ManifestationIssues::updateOrCreate(
                                    ['order_id' => $o->id],
                                    [
                                        'order_id' => $o->id,
                                        'message' => $responseData['ReturnMessage'] ?? "",
                                        'created' => date('Y-m-d H:i:s')
                                    ]
                                );
                                Order::where('id',$o->id)->update(['is_retry' => 1]);
                            }
                        }
                        break;
                    case 'ekart':
                    case 'ekart_2kg':
                    case 'ekart_1kg':
                    case 'ekart_3kg':
                    case 'ekart_5kg':
                        $shipping = new Ekart();
                        if($shipping->shipOrder($o))
                            Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                        else
                            Order::where('id',$o->id)->update(['is_retry' => 1]);
                        break;
                    case 'shadow_fax':
                        $shadowFax = new Shadowfax();
                        $res = $shadowFax->manifestOrder($o,$sellerDetail);
                        if(!empty($res) && $res['message'] == 'Success'){
                            Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                        }
                        else{
                            // code to store message
                            ManifestationIssues::updateOrCreate(
                                ['order_id' => $o->id],
                                [
                                    'order_id' => $o->id,
                                    'message' => $res['errors'][0] ?? "",
                                    'created' => date('Y-m-d H:i:s')
                                ]
                            );
                            Order::where('id',$o->id)->update(['is_retry' => 1]);
                        }
                        break;
                    case 'xpressbees_sfc':
                    case 'smc_new':
                        $responseData = SMCNew::ShipOrder($o);
                        if(!empty($responseData['success']) && $responseData['success']){
                            $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                            // success register pickup here
                            SMCNew::CreatePickup($o);
                        }
                        break;
                    case "bluedart":
                        if($o->is_alpha == 'NSE'){
//                            if($o->seller_id == 188)
                                $blueDart = new BluedartRest('NSE','bluedart');
//                            else
//                                $blueDart = new BlueDart('NSE','bluedart');
                            $response = $blueDart->shipOrder($o);
                            if($response) {
                                $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                            }
                        }
                        else{
//                            if($o->seller_id == 188)
                                $blueDart = new BluedartRest('SE','bluedart');
//                            else
//                                $blueDart = new BlueDart('SE','bluedart');
                            $response = $blueDart->shipOrder($o);
                            if($response) {
                                $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                            }
                        }
                        break;
                    case "bluedart_surface":
                        if($o->is_alpha == 'NSE'){
//                            if($o->seller_id == 188)
                                $blueDart = new BluedartRest('NSE');
//                            else
//                                $blueDart = new BlueDart('NSE');
                            $response = $blueDart->shipOrder($o);
                            if($response) {
                                $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                            }
                        }
                        else{
//                            if($o->seller_id == 188)
                                $blueDart = new BluedartRest('SE');
//                            else
//                                $blueDart = new BlueDart('SE');
                            $response = $blueDart->shipOrder($o);
                            if($response) {
                                $rowUpdated += Order::where('id',$o->id)->update(['manifest_sent' => 'y']);
                            }
                        }
                        break;
                    default:
                        break;
                }
                $data = Order::find($o->id);
                if($data->manifest_sent == 'y'){
                    // put message sent code here
                    $this->utilities->send_sms($o);
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage()."-".$e->getFile()."-".$e->getLine(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()
            ]);
        }
    }

    function fulfillPendingOrders(Request $request){
        try {
            $startedAt = now();
            $cronName = 'fulfill-pending-orders';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;
            DB::enableQueryLog();
            //ini_set('max_execution_time', 1440);
            $startedAt = now();
            $orders = Order::where('fulfillment_sent', 'n')->where('manifest_sent', 'y')->whereNotIn('status',['pending','cancelled'])->whereIn('channel',['shopify','woocommerce'])->select('id')->orderBy('awb_assigned_date','desc');
            if(!empty($request->seller_id)){
                $orders=$orders->where('seller_id',$request->seller_id);
            }
            if(!empty($request->channel)){
                $orders=$orders->where('channel',$request->channel);
            }
            if(!empty($request->order_id))
                $orders=$orders->where('id',$request->order_id);
            if(!empty($request->awb_number)){
                $allAwbs = explode(',',$request->awb_number);
                $orders=$orders->whereIn('awb_number',$allAwbs);
            }

            $orders = $orders->get();
            //$orders = Order::where('id', 33916597)->get();
            //dd(DB::getQueryLog());
            foreach ($orders as $singleOrder){
                $o = Order::find($singleOrder->id);
                if($startedAt->diffInSeconds(now()) >= 1440) {
                    return true;
                }
                try{
                    $fulfillmentId = $this->fulfillChannelOrders($o,$o->awb_number,$o->courier_partner);
                    if(!empty($fulfillmentId)) {
                        $rowUpdated += Order::where('id',$o->id)->update(['fulfillment_id' => $fulfillmentId, 'fulfillment_sent' => 'y']);
                    }
                }
                catch(Exception $e){
                    continue;
                }

            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage()."-".$e->getFile()."-".$e->getLine(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()
            ]);
        }
    }

    // Only fulfill smartr orders
    function fulfillAmazonDirectOrders(Request $request){
        try {
            $startedAt = now();
            $cronName = 'fulfill-amazon-direct-orders';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            //ini_set('max_execution_time', 1440);
            $startedAt = now();
            $orders = Order::where('fulfillment_sent', 'n')->select('id')->where('manifest_sent','y')->whereNotIn('status',['pending','cancelled','delivered'])->where('channel','amazon_direct')->where('courier_partner', 'smartr')->orderBy('id','desc');
            if(!empty($request->sellerId)){
                $orders=$orders->where('seller_id',$request->sellerId);
            }
            if(!empty($request->awbNumber)){
                $orders=$orders->where('awb_number',$request->awbNumber);
            }
            $orders = $orders->limit(300)->get();
            //$orders = Order::where('id', 33916597)->get();
            foreach ($orders as $singleOrder){
                $o = Order::find($singleOrder->id);
                if($startedAt->diffInSeconds(now()) >= 1440) {
                    throw new Exception('Time limit exceeded');
                }
                $fulfillmentId = $this->fulfillChannelOrders($o,$o->awb_number,$o->courier_partner);
                if(!empty($fulfillmentId)) {
                    $rowUpdated += Order::where('id',$o->id)->update(['fulfillment_id' => $fulfillmentId, 'fulfillment_sent' => 'y']);
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully',
                'fulfillmentId' => $fulfillmentId ?? 0
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function fulfillChannelOrders($order, $awb = "", $partnerName = "")
    {
        // $channel = Channels::where('seller_id', $order->seller_id)->where('channel', $order->channel)->first();
        $channel = Channels::where('seller_id', $order->seller_id)->where('id', $order->seller_channel_id)->first();
        if($awb == "SF7065416411471SIA")
            dd($channel);
        if (empty($channel)) {
            return false;
        }
        //$partner = $this->getPartnerNameShopify($partnerName);
        switch ($order->channel) {
            case 'shopify':
                return ShopifyHelper::FulfillShopifyOrder($channel,$order);
//                $shopify = new ShopifyController();
//                return $shopify->fulfillShopifyOrder($order, $awb, $partnerName, $channel);
                break;
            case 'amazon':
                $channelsController = new ChannelsController();
                return $channelsController->_fulfillAmazonOrders($order, $channel, $awb, $partnerName);
                break;
            case 'amazon_direct':
                Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                    'title' => "Creating Amazon Direct Feed For Seller ID: ".$order->seller_id,
                    'data' => []
                ]);
                return $this->_fulfillAmazonDirectOrders($order, $channel);
                break;
            case 'woocommerce':
                Logger::write('logs/channels/woocommerce/woocommerce-'.date('Y-m-d').'.text', [
                    'title' => "WooCommerce Status Push Fulfillment: ".$order->seller_id,
                    'data' => []
                ]);
                TrackingHelper::CreateWooCommerceOrderNote($order);
                TrackingHelper::PushWooCommerceStatus($order, 'shipped');

                break;
        }
        return true;
    }

    function _fulfillAmazonDirectOrders($order, $channel) {
        // Only fulfill smartr orders
        // if(strtolower($order->courier_partner) != 'smartr') {
        //     return;
        // }
        $amazonDirect = new AmazonDirect();
        $partnerName = $this->amazonPartnerNames[$order->courier_partner] ?? "Others";
        $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
        $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken);
        if(!isset($feedDocument['payload']))
            return;
        $date=gmdate('c');
        $payload = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>A3DIIY2B8PMH51</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderFulfillment>
            <AmazonOrderID>$order->channel_id</AmazonOrderID>
            <FulfillmentDate>$date</FulfillmentDate>
            <FulfillmentData>
                <CarrierName>$partnerName</CarrierName>
                <ShippingMethod>Standard</ShippingMethod>
                <ShipperTrackingNumber>$order->awb_number</ShipperTrackingNumber>
            </FulfillmentData>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>
EOD;
        $uploadDocument = $amazonDirect->uploadAmazonFeedDocument($accessToken, $feedDocument['payload'], $payload);
        if($uploadDocument->getStatusCode() == 200) {
            $feed = $amazonDirect->createAmazonFeed($accessToken, 'POST_ORDER_FULFILLMENT_DATA', ['A21TJRUUN4KGV'], $feedDocument['payload']['feedDocumentId']);
            // dd($feed->ok(), $feed->status(), $feed, $feed->json(), $feedDocument['payload']);
            if($feed->ok() || $feed->status() == 201 || $feed->status() == 202) {
                // Logs
                $this->_addLog([], "Feed Created Successfully");
                Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                    'title' => "Feed Created Successfully",
                    'data' => $feed->json()
                ]);
                return $feed->json()['payload']['feedId'];
            } else {
                // Logs
                $this->_addLog([], "Feed Not Created");
                Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                    'title' => "Feed Not Created",
                    'data' => $feed->json()
                ]);
                return false;
            }
        }
        return true;
    }

    // Fulfuill amazon direct orders using flat file
    function fulfillAmazonDirectOrdersFlatFile(Request $request) {
        try {
            $startedAt = now();
            $cronName = 'fulfill-amazon-direct-orders-flat-file';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            // Get all amazon direct sellers
            $channels = Channels::where('channel', 'amazon_direct')
                ->where('status', 'y');
            // For specific seller id
            if($request->filled('sellerId')) {
                $channels = $channels->where('seller_id', $request->sellerId);
            }
            $channels = $channels->orderBy('updated')->get();
            foreach ($channels as $channel) {
                // Generate flat file
                $feedData = [];
                $orderIds = [];
                $orders = Order::select('id','channel_id','courier_partner','awb_number')->where('fulfillment_sent', 'n')
                    ->where('manifest_sent', 'y')
                    ->whereNotIn('status', ['pending','cancelled'])
                    ->where('seller_channel_id', $channel->id)
                    ->whereNotIn('courier_partner', ['smartr']) // Ignore smartr order, we have another cron for that
                    ->where('seller_id', $channel->seller_id);
                // For specific order ids
                if($request->filled('orderId')) {
                    $ids = array_map('trim', explode(',', trim($request->orderId)));
                    $orders = $orders->whereIn('id', $ids);
                }
                $orders = $orders->orderBy('id', 'desc')
                    ->limit(250)
                    ->get();
                foreach($orders as $order) {
                    $partnerName = $this->amazonPartnerNames[$order->courier_partner] ?? 'Other';
                    $awbNumber = in_array($order->courier_partner,['shree_maruti_ecom','shree_maruti_ecom_1kg','shree_maruti_ecom_3kg','shree_maruti_ecom_5kg','shree_maruti_ecom_10kg']) ? "999".$order->awb_number : $order->awb_number;
                    //$awbNumber = $order->awb_number;
                    $feedData[] = [
                        $order->channel_id,
                        '',
                        '',
                        gmdate('c'),
                        $partnerName,
                        '',
                        $awbNumber,
                        'Air' // Standard
                    ];
                    $orderIds[] = $order->id;
                }
                if(empty($feedData)) {
                    // No order for fulfillment for this seller
                    continue;
                }

                $amazonDirect = new AmazonDirect();
                $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
                $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken, 'text/plain; charset=UTF-8');
                if(empty($feedDocument['payload'])) {
                    // Feed document not created
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => "Feed document not created for seller id: " . $channel->seller_id,
                        'data' => []
                    ]);
                    // Unable to create feed document for this seller
                    continue;
                }
                // Generate xls file for feeds
                $payload = Excel::raw(new FulfilAmazonFeedFlatFile($feedData), \Maatwebsite\Excel\Excel::CSV);
                // Upload flat file document
                $uploadDocument = $amazonDirect->uploadAmazonFeedDocument($accessToken, $feedDocument['payload'], $payload, 'text/plain; charset=UTF-8');
                if($uploadDocument->getStatusCode() == 200) {
                    // Create feed
                    $feed = $amazonDirect->createAmazonFeed($accessToken, 'POST_FLAT_FILE_FULFILLMENT_DATA', ['A21TJRUUN4KGV'], $feedDocument['payload']['feedDocumentId']);
                    if($feed->ok() || $feed->status() == 201 || $feed->status() == 202) {
                        // Logs
                        $responseData = $feed->json();
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Feed C  reated Successfully for seller id: " . $channel->seller_id,
                            'data' => $feed->json()
                        ]);
                        // Feed created update the feed id
                        Order::whereIn('id', $orderIds)
                            ->update([
                                'fulfillment_id' => $responseData['payload']['feedId'] ?? null,
                                'fulfillment_sent' => 'y'
                            ]);
                        $rowUpdated++;
                    } else {
                        // Logs
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Feed Not Created for seller id: " . $channel->seller_id,
                            'data' => $feed->json() ?? []
                        ]);
                    }
                } else {
                    // Document not uploaded
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => "Feed document not uploded for seller id: " . $channel->seller_id,
                        'data' => $uploadDocument->json()
                    ]);
                }
                Channels::where('id',$channel->id)->update(['updated' => date('Y-m-d H:i:s')]);
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            // Logger
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Cron failed for seller id: " . $channel->seller_id,
                'data' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function checkAndFixManifest(){
        $manifest = Manifest::whereDate('created','>',date('Y-m-d',strtotime('-3 days')))->get();
        foreach ($manifest as $m){
            $orderCount = ManifestOrder::where('manifest_id',$m->id)->distinct('order_id')->count();
            Manifest::where('id',$m->id)->update(['number_of_order' => $orderCount]);
        }
    }
    function populateAmazonWeight(){
        try {
            $startedAt = now();
            $cronName = 'populate-amazon-weight';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $date = date('Y-m-d',strtotime('-2 days'));
            $startedAt = now();
            //DB::enableQueryLog(); 3159
            $orders = Order::where(function ($query){
                $query->whereIn('channel',['amazon','amazon_direct','woocommerce'])
                    ->whereIn('seller_id',[65,70,91,637,797,891,2781,4006,656,3586,3628,3593,17782,3159])
                    ->orWhere(function ($query){
                        return $query->where('seller_id','7688')
                            ->where('channel','shopify');
                    });
            })->where('status','pending')
                ->where('weight_updated','n')
                ->whereDate('inserted','>=',$date)
                ->select('id','product_qty','seller_id','channel')
                //->select('seller_id','channel')
                //->distinct()
                ->orderBy('inserted','desc')->get();
            //dd(DB::getQueryLog());
            // $orders=Order::whereIn('channel',['amazon','amazon_direct'])->where('status','pending')->whereIn('seller_id',[891])->where('weight_updated','n')->select('id','product_qty','seller_id')->get();
            foreach ($orders as $o){
                if($startedAt->diffInSeconds(now()) >= 1740) {
                    return true;
                }
                $products = Product::where('order_id',$o->id)->get();
                $weight = 0;
                $height = 0;
                $length = 0;
                $width = 0;
                if($o->product_qty == 1){
                    $sku = SKU::where('sku',$products[0]->product_sku)->where('seller_id',$o->seller_id)->first();
                    if(!empty($sku)){
                        $weight = ($sku->weight * 1000);
                        $height = $sku->height;
                        $length = $sku->length;
                        $width = $sku->width;
                    }
                }
                else{
                    //run multi quantity task
                    foreach ($products as $p){
                        $sku = SKU::where('sku',$p->product_sku)->where('seller_id',$o->seller_id)->first();
                        if(!empty($sku))
                            $weight+=($sku->weight * 1000) * $p->product_qty;
                    }
                    $dimen = $this->_FetchDefaultDimensionData($weight);
                    $height = $dimen->height;
                    $length = $dimen->length;
                    $width = $dimen->width;
                }
                //update the details of the orders
                if($weight == 0)
                    continue;
                $volWeight = (intval($height) * intval($length) * intval($width)) / 5;
                $rowUpdated += Order::where('id',$o->id)->update([
                    'weight' => $weight,
                    'height' => $height ?? 10,
                    'length' => $length ?? 10,
                    'breadth' => $width ?? 10,
                    'vol_weight' => $volWeight,
                    'weight_updated' => 'y'
                ]);
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage()." | ".$e->getLine(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()." | ".$e->getLine()
            ]);
        }
    }
    function _FetchDefaultDimensionData($weight)
    {
        $response = DB::table('dimensions')->where('weight', '>=', $weight)->orderBy('weight')->first();
        if ($response == null)
            $response = (object)['height' => 40, 'length' => 20, 'width' => 20];
        return $response;
    }
    function fetchAmazonDirectOrders(Request $request){
        try {
            $startedAt = now();
            $cronName = 'fetch-amazon-direct-orders';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $sellerId = $request->seller;
            $amazonDirect = new AmazonDirect();
            $channels = Channels::where('channel','amazon_direct')->where('status','y');
            if(!empty($sellerId))
                $channels = $channels->where('seller_id',$sellerId);
            $channels = $channels->orderBy('last_executed')->get();
            foreach ($channels as $c){
                $accessToken = $amazonDirect->getAccessToken($c->amazon_refresh_token);
                $warehouse = Warehouses::where('seller_id',$c->seller_id)->where('default','y')->first();
                if(empty($warehouse))
                    continue;
                $this->fetchAndStoreAmazonDirectOrders($amazonDirect,$c,$accessToken,$warehouse);
                Channels::where('id',$c->id)->update(['last_executed' => date('Y-m-d H:i:s')]);
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    function fetchAndStoreAmazonDirectOrders($amazonDirect,$c,$accessToken,$warehouse,$nextToken=""){
        $amazonResponse = $amazonDirect->getAmazonOrders($c,$accessToken,date('Y-m-d H:i:s',strtotime($c->last_sync)),"",$nextToken);
        $lastSync=$c->last_sync;
        if(!empty($amazonResponse['payload']['Orders'])){
            foreach ($amazonResponse['payload']['Orders'] as $o){
                $logResponse = [
                    'channel' => 'amazon_direct',
                    'channel_id' => $o['AmazonOrderId'],
                    'order_response' => json_encode($o),
                    'seller_id' => $c->seller_id,
                    'inserted' => date('Y-m-d H:i:s')
                ];
                if(strtolower($o['OrderStatus']) == 'unshipped'){
                    $orderItems = $amazonDirect->getOrderItems($accessToken,$o['AmazonOrderId']);
                    if(!$orderItems){
                        $logResponse['item_fetched']='n';
                        try{
                            Channel_orders_log::create($logResponse);
                        }
                        catch(Exception $e){}
                        continue;
                    }
                    $logResponse['item_fetched']='y';
                    $logResponse['item_response']=json_encode($orderItems) ?? "";
                    $addressDetail = $amazonDirect->getAddressOrder($accessToken,$o['AmazonOrderId']);
                    if(!$addressDetail){
                        $logResponse['address_fetched']='n';
                        try{
                            Channel_orders_log::create($logResponse);
                        }
                        catch(Exception $e){}
                        continue;
                    }
                    $logResponse['address_fetched']='y';
                    $logResponse['address_response']=json_encode($addressDetail) ?? "";
                    try{
                        Channel_orders_log::create($logResponse);
                    }
                    catch(Exception $e){}
                    $this->_CreateAmazonDirectOrder($o,$c,$warehouse,$orderItems['payload'],$addressDetail['payload']);
                    $lastSync = date('Y-m-d H:i:s',strtotime($o['PurchaseDate']));
                }
                sleep(2);
            }
            Channels::where('id',$c->id)->update(['last_sync' => $lastSync]);
        }
        //$this->_addLog([],"Amazon Direct Fetch -- Seller ID: ".$c->seller_id." Date: ".date('Y-m-d H:i:s')." ----");
        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
            'title' => "Amazon Direct Fetch For Seller ID: ".$c->seller_id,
            'data' => []
        ]);
        if(isset($amazonResponse['payload']))
        {
            $this->_addLog($amazonResponse['payload']['Orders'],"Total Orders Fetched : ".count($amazonResponse['payload']['Orders']));
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Total Orders Fetched : ".count($amazonResponse['payload']['Orders']),
                'data' => $amazonResponse['payload']['Orders']
            ]);
        }
        if(isset($amazonResponse['payload']['NextToken'])){
            $this->fetchAndStoreAmazonDirectOrders($amazonDirect,$c,$accessToken,$warehouse,$amazonResponse['payload']['NextToken']);
        }
    }

    function fetchAmazonDirectOrdersCustom($sellerId,$orderIds){
        $amazonDirect = new AmazonDirect();
        $c = Channels::where('channel','amazon_direct')->where('seller_id',$sellerId)->first();
        if(empty($c))
            dd("Channel not found for Amazon Direct for this seller");
        //
        $accessToken = $amazonDirect->getAccessToken($c->amazon_refresh_token);
        //dd($accessToken);
        $warehouse = Warehouses::where('seller_id',$c->seller_id)->where('default','y')->first();
        if(empty($warehouse))
            dd("Default warehouse not found for the seller");
        $amazonResponse = $amazonDirect->getAmazonOrders($c,$accessToken,"",$orderIds);
        print_r($amazonResponse);
        $numOrder = 0;
        if(!empty($amazonResponse['payload']['Orders'])){
            foreach ($amazonResponse['payload']['Orders'] as $o){
                if($o['OrderStatus'] == 'Unshipped'){
                    $orderItems = $amazonDirect->getOrderItems($accessToken,$o['AmazonOrderId']);
                    if(!$orderItems)
                        continue;
                    $addressDetail = $amazonDirect->getAddressOrder($accessToken,$o['AmazonOrderId']);
                    if(!$addressDetail)
                        continue;
                    if($this->_CreateAmazonDirectOrder($o,$c,$warehouse,$orderItems['payload'],$addressDetail['payload']))
                        $numOrder++;
                    echo ($o['AmazonOrderId'])."<bR>";
                    $lastSync = date('Y-m-d H:i:s',strtotime($o['PurchaseDate']));
                }
            }
            //Channels::where('id',$c->id)->update(['last_sync' => $lastSync]);
        }
        // Logs
        $this->_addLog([],"Amazon Direct Fetch -- Seller ID: ".$c->seller_id." Date: ".date('Y-m-d H:i:s')." ----");
        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
            'title' => "Amazon Direct Fetch For Seller ID: ".$c->seller_id,
            'data' => []
        ]);
        if(!empty($amazonResponse['payload']['Orders']))
        {
            $this->_addLog($amazonResponse['payload']['Orders'],"Total Orders Fetched : ".count($amazonResponse['payload']['Orders']));
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Total Orders Fetched : ".count($amazonResponse['payload']['Orders']),
                'data' => $amazonResponse['payload']['Orders']
            ]);
        }
        echo "$numOrder fetched successfully";
    }
    function fetchAmazonDirectOrdersCustomDate($sellerId,$startDateTime,$endDateTime){
        $amazonDirect = new AmazonDirect();
        $c = Channels::where('channel','amazon_direct')->where('seller_id',$sellerId)->first();
        if(empty($c))
            dd("Channel not found for Amazon Direct for this seller");
        //
        $accessToken = $amazonDirect->getAccessToken($c->amazon_refresh_token);
        $warehouse = Warehouses::where('seller_id',$c->seller_id)->where('default','y')->first();
        if(empty($warehouse))
            dd("Default warehouse not found for the seller");
        $amazonResponse = $amazonDirect->getAmazonOrders($c,$accessToken,"","","",urldecode($startDateTime),urldecode($endDateTime));
        $numOrder = 0;
        if(!empty($amazonResponse['payload']['Orders'])){
            foreach ($amazonResponse['payload']['Orders'] as $o){
                if($o['OrderStatus'] == 'Unshipped'){
                    $orderItems = $amazonDirect->getOrderItems($accessToken,$o['AmazonOrderId']);
                    if(!$orderItems)
                        continue;
                    $addressDetail = $amazonDirect->getAddressOrder($accessToken,$o['AmazonOrderId']);
                    if(!$addressDetail)
                        continue;
                    if($this->_CreateAmazonDirectOrder($o,$c,$warehouse,$orderItems['payload'],$addressDetail['payload']))
                        $numOrder++;
                    print_r($o);
                    $lastSync = date('Y-m-d H:i:s',strtotime($o['PurchaseDate']));
                }
            }
            //Channels::where('id',$c->id)->update(['last_sync' => $lastSync]);
        }
        // Logs
        $this->_addLog([],"Amazon Direct Fetch -- Seller ID: ".$c->seller_id." Date: ".date('Y-m-d H:i:s')." ----");
        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
            'title' => "Amazon Direct Fetch For Seller ID: ".$c->seller_id,
            'data' => []
        ]);
        if(!empty($amazonResponse['payload']['Orders']))
        {
            $this->_addLog($amazonResponse['payload']['Orders'],"Total Orders Fetched : ".count($amazonResponse['payload']['Orders']));
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Total Orders Fetched : ".count($amazonResponse['payload']['Orders']),
                'data' => $amazonResponse['payload']['Orders']
            ]);
        }
        echo "$numOrder fetched successfully";
    }
    function fetchAmazonDirectOrdersMinor($limit){
        $orders = Channel_orders_log::where('item_fetched','n')->orWhere('address_fetched','n')->orderBy('id','desc')->limit($limit)->get();
        //dd($orders);
        $amazonDirect = new AmazonDirect();
        $orderCount=0;
        $orderNos = "";
        foreach ($orders as $o){
            $c = Channels::where('channel','amazon_direct')->where('seller_id',$o->seller_id)->first();
            if(empty($c))
                continue;
            $accessToken = $amazonDirect->getAccessToken($c->amazon_refresh_token);
            $warehouse = Warehouses::where('seller_id',$c->seller_id)->where('default','y')->first();
            if(empty($warehouse))
                continue;
            if(empty($o->order_response)){
                $amazonResponse = $amazonDirect->getAmazonOrders($c,$accessToken,"",$o->channel_id);
                if(!isset($amazonResponse['payload']['Orders'][0]))
                    continue;
                $orderDetail = $amazonResponse['payload']['Orders'][0];
            }
            else
            {
                $orderDetail=json_decode($o->order_response,true);
            }
            if($orderDetail['OrderStatus'] == 'Unshipped'){
                if($o->item_fetched == 'y')
                    $itemDetails = json_decode($o->item_response,true);
                else{
                    $itemDetails = $amazonDirect->getOrderItems($accessToken,$orderDetail['AmazonOrderId']);
                    if(!$itemDetails)
                        continue;
                    $o->item_fetched = 'y';
                    $o->item_response = json_encode($itemDetails);
                }
                if($o->address_fetched == 'y')
                    $addressDetail = json_decode($o->address_response,true);
                else{
                    $addressDetail = $amazonDirect->getAddressOrder($accessToken,$orderDetail['AmazonOrderId']);
                    if(!$addressDetail)
                        continue;
                    $o->address_fetched = 'y';
                    $o->address_response = json_encode($addressDetail);
                }
                if($this->_CreateAmazonDirectOrder($orderDetail,$c,$warehouse,$itemDetails['payload'],$addressDetail['payload'])){
                    $o->save();
                    $orderCount++;
                    $orderNos .= $orderDetail['AmazonOrderId'].",";
                }
            }
            sleep(2);
        }
        return $orderCount." orders fetched successfully<br>".$orderNos;
    }
    function _CreateAmazonDirectOrder($rd,$channel,$warehouse,$orderItems,$addressDetails){
        // if($addressDetails['ShippingAddress']['Phone'] ?? "" == "9999999999" || $rd['OrderTotal']['Amount'] ?? 0 == 0)
        //     return false;
        if(($rd['OrderTotal']['Amount'] ?? 0) == 0) {
            return false;
        }
        $data = array(
            'order_number' => $rd['AmazonOrderId'] ?? 0,
            'customer_order_number' => $rd['AmazonOrderId'] ?? 0,
            'channel_id' => $rd['AmazonOrderId'] ?? null,
            'o_type' => "forward",
            'seller_id' => $channel->seller_id,
            // 'order_type' => $rd['PaymentMethod'] == "Other" ? "prepaid" : "cod",
            'order_type' => @$rd['PaymentMethod'] != "Other" ? "cod" : "prepaid",
            'b_customer_name' => $addressDetails['ShippingAddress']['Name'] ?? "",
            'b_address_line1' => $addressDetails['ShippingAddress']['AddressLine1'] ?? "",
            'b_address_line2' => $addressDetails['ShippingAddress']['AddressLine2'] ?? "",
            'b_country' => $addressDetails['ShippingAddress']['CountryCode'] ?? "",
            'b_state' => $addressDetails['ShippingAddress']['StateOrRegion'] ?? "",
            'b_city' => $addressDetails['ShippingAddress']['City'] ?? "",
            'b_pincode' => $addressDetails['ShippingAddress']['PostalCode'] ?? "",
            'b_contact' => $addressDetails['ShippingAddress']['Phone'] ?? "",
            'b_contact_code' => "91",
            's_customer_name' => $addressDetails['ShippingAddress']['Name'] ?? "",
            's_address_line1' => $addressDetails['ShippingAddress']['AddressLine1'] ?? "",
            's_address_line2' => $addressDetails['ShippingAddress']['AddressLine2'] ?? "",
            's_country' => $addressDetails['ShippingAddress']['CountryCode'] ?? "",
            's_state' => $addressDetails['ShippingAddress']['StateOrRegion'] ?? "",
            's_city' => $addressDetails['ShippingAddress']['City'] ?? "",
            's_pincode' => $addressDetails['ShippingAddress']['PostalCode'] ?? "",
            's_contact' => $addressDetails['ShippingAddress']['Phone'] ?? "",
            's_contact_code' => "91",
            'p_warehouse_name' => isset($warehouse->warehouse_name) ? $warehouse->warehouse_name : "",
            'p_customer_name' => isset($warehouse->contact_name) ? $warehouse->contact_name : "",
            'p_address_line1' => isset($warehouse->address_line1) ? $warehouse->address_line1 : "",
            'p_address_line2' => isset($warehouse->address_line2) ? $warehouse->address_line2 : "",
            'p_country' => isset($warehouse->country) ? $warehouse->country : "",
            'p_state' => isset($warehouse->state) ? $warehouse->state : "",
            'p_city' => isset($warehouse->city) ? $warehouse->city : "",
            'warehouse_id' => isset($warehouse->id) ? $warehouse->id : "",
            'p_pincode' => isset($warehouse->pincode) ? $warehouse->pincode : "",
            'p_contact' => isset($warehouse->contact_number) ? $warehouse->contact_number : "",
            'p_contact_code' => isset($warehouse->code) ? $warehouse->code : "",
            'weight' => 100,
            'height' => 10,
            'length' => 10,
            'breadth' => 10,
            'vol_weight' => 200,
            'shipping_charges' => 0,
            'cod_charges' => 0,
            'discount' => 0,
            // 'invoice_amount' => $rd['OrderTotal']['Amount'],
            'invoice_amount' => $rd['OrderTotal']['Amount'] ?? 0,
            'channel' => 'amazon_direct',
            'inserted' => date('Y-m-d H:i:s', strtotime($rd['PurchaseDate'])),
            'inserted_by' => $channel->seller_id,
            'seller_channel_id' => $channel->id,
            'seller_channel_name' => $channel->channel_name
        );
        try{
            $orderID = Order::create($data)->id;
        }
        catch(Exception $e){
            return false;
        }
        $pname = [];
        $psku = [];
        $productQty = 0;
        foreach ($orderItems['OrderItems'] as $p) {
            $product = array(
                'order_id' => $orderID,
                'product_sku' => $p['SellerSKU'],
                'product_name' => $p['Title'],
                'product_unitprice' => $p['ItemPrice']['Amount'] ?? 0,
                'product_qty' => $p['QuantityOrdered'],
                'item_id' => $p['OrderItemId'] ?? "",
                'total_amount' => ($p['ItemPrice']['Amount'] ?? 0) * ($p['QuantityOrdered'] ?? 1),
            );
            $productQty+=intval($product['product_qty'] ?? 1);
            Product::create($product);
            $pname[] = $p['Title'];
            $psku[] = $p['SellerSKU'];
        }
        Order::where('id', $orderID)->update(array('product_name' => implode(',', $pname),'product_qty' => $productQty,'product_sku' => implode(',', $psku)));
        return true;
    }

    function _addLog($response, $text) {
        // $date=date('Y-m-d');
        // $myfile = fopen("logs/{$date}amazon.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, "\n".date('Y-m-d H:i:s')."----". $text." ------- ".json_encode($response));
        // fclose($myfile);
    }

    function fetchAmazonFeeds(Request $request) {
        $amazonDirect = new AmazonDirect();
        // $channels = Channels::where('channel','amazon_direct')->get();
        $channels = Channels::where('seller_id', $request->sellerId)->where('channel','amazon_direct')->get();
        foreach ($channels as $channel){
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $amazonResponse = $amazonDirect->getAmazonFeeds($accessToken, $request->feedType); // feed type POST_ORDER_FULFILLMENT_DATA, POST_FLAT_FILE_FULFILLMENT_DATA
            dd($amazonResponse);
        }
    }

    function fetchAmazonFeed(Request $request) {
        $amazonDirect = new AmazonDirect();
        // $channels = Channels::where('channel','amazon_direct')->get();
        $channels = Channels::where('seller_id', $request->sellerId)->where('channel','amazon_direct')->get();
        foreach ($channels as $channel) {
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $amazonResponse = $amazonDirect->getAmazonFeed($accessToken, $request->feedId);
            dd($amazonResponse);
        }
    }

    function fetchAmazonFeedDocument(Request $request) {
        $amazonDirect = new AmazonDirect();
        // $channels = Channels::where('channel','amazon_direct')->where('seller_id', 65)->get();
        $channels = Channels::where('seller_id', $request->sellerId)->where('channel','amazon_direct')->get();
        foreach ($channels as $channel) {
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $feedDocument = $amazonDirect->getAmazonFeedDocument($accessToken, $request->feedDocumentId);
            if(!empty($feedDocument['payload'])) {
                $document = $amazonDirect->downloadAmazonFeedDocument($accessToken, $feedDocument['payload']);
                dd($document);
            } else {
                dd($feedDocument);
            }
        }
    }

    function createAmazonFeed() {
        $amazonDirect = new AmazonDirect();
        $channels = Channels::where('channel', 'amazon_direct')->get();
        foreach ($channels as $channel) {
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken);
            $payload = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>A3U56SAUJE9H7N</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderFulfillment>
            <AmazonOrderID>171-1226504-5023531</AmazonOrderID>
            <FulfillmentDate>2022-01-03</FulfillmentDate>
            <FulfillmentData>
                <CarrierName>Xpressbees</CarrierName>
                <ShippingMethod>prepaid</ShippingMethod>
                <ShipperTrackingNumber>14355221262025</ShipperTrackingNumber>
            </FulfillmentData>
            <Item>
                <AmazonOrderItemCode>08792105762075</AmazonOrderItemCode>
                <Quantity>1</Quantity>
            </Item>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>
EOD;
            $uploadDocument = $amazonDirect->uploadAmazonFeedDocument($accessToken, $feedDocument['payload'], $payload);
            if($uploadDocument->getStatusCode() == 200) {
                $feed = $amazonDirect->createAmazonFeed($accessToken, 'POST_ORDER_FULFILLMENT_DATA', ['A21TJRUUN4KGV'], $feedDocument['payload']['feedDocumentId']);
                dd($feed);
            }
        }
    }

    function createAmazonFeedFile() {
        $data = [
            ['408-4622258-5538760', '', '', gmdate('c'), '', 'Other', 'SHEP0000102342', 'Standard'],
            ['402-7730143-6633104', '', '', gmdate('c'), '', 'Other', 'SHEP0000102340', 'Standard'],
        ];
        // Test excel file
        // return Excel::download(new FulfilAmazonFeedFlatFile($data), 'test.xls');
        return response(Excel::raw(new FulfilAmazonFeedFlatFile($data), \Maatwebsite\Excel\Excel::CSV));

        $amazonDirect = new AmazonDirect();
        $channels = Channels::where('channel', 'amazon_direct')->where('seller_id', 660)->get();
        foreach ($channels as $channel) {
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken, 'application/vnd.ms-excel; charset=utf-8'); // for xls file
            // $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8'); // for xlsx file
            $payload = Excel::raw(new FulfilAmazonFeedFlatFile($data), \Maatwebsite\Excel\Excel::XLS);
            $uploadDocument = $amazonDirect->uploadAmazonFeedDocument($accessToken, $feedDocument['payload'], $payload, 'application/vnd.ms-excel; charset=utf-8');
            if($uploadDocument->getStatusCode() == 200) {
                $feed = $amazonDirect->createAmazonFeed($accessToken, 'POST_FLAT_FILE_FULFILLMENT_DATA', ['A21TJRUUN4KGV'], $feedDocument['payload']['feedDocumentId']);
                dd($feed->body());
            } else {
                dd($uploadDocument->getStatusCode(), $uploadDocument->body());
            }
        }
    }

    function cancelAmazonFeed(Request $request) {
        $amazonDirect = new AmazonDirect();
        $channels = Channels::where('channel','amazon_direct')->get();
        foreach ($channels as $channel){
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $amazonResponse = $amazonDirect->cancelAmazonFeed($accessToken, $request->feedId);
            dd($amazonResponse);
        }
    }

    /**
     * Load data into cache.
     *
     * Load all dashboard counter and cache for 20 hours.
     */
    function populateCache() {
        try {
            $startedAt = now();
            $cronName = 'populate-cache';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
            $sellers = Seller::where('verified','y')->get();
            foreach($sellers as $seller) {
                $remDay = $seller->remmitance_days ?? 7;
                // Dashboard counters
                $counters = Cache::store('redis')->remember('counters-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller, $remDay) {
                    $codArray = $this->utilities->getNextCodRemitDate($seller->id);
                    $counters = [
                        'total_shipment' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled')->count(),
                        'total_created' => Order::where('seller_id', $seller->id)->whereDate('inserted', '=', date('Y-m-d'))->count(),
                        'total_revanue' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->avg('invoice_amount'),
                        'total_customer' => Order::distinct('b_contact')->where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>', Carbon::now()->subDays(30))->count(),
                        'today_order' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->count(),
                        'today_revenue' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount'),
                        'yesterday_revenue' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount'),
                        'total_all_orders' => Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'shipped_orders' => Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending','cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'pending_order' => Order::where('seller_id', $seller->id)->whereIn('status', ['manifested','pickup_scheduled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'picked_up' => Order::where('seller_id', $seller->id)->where('status', 'picked_up')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'out_for_delivery' => Order::where('seller_id', $seller->id)->where('status', 'out_for_delivery')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'delivered_order' => Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'intransit_order' => Order::where('seller_id', $seller->id)->where('status', 'in_transit')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'ndr_pending' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'total_ndr' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'action_required' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'action_requested' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'ndr_delivered' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('status', 'delivered')->where('rto_status', '!=', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'ndr_rto' => Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'cod_total' => round(Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->sum('invoice_amount'),2),
                        'cod_available' => round(Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','<',date('Y-m-d H:i:s',strtotime("-$remDay days")))->sum('invoice_amount'),2),
                        'cod_pending' => round(Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','>=',date('Y-m-d H:i:s',strtotime("-$remDay days")))->sum('invoice_amount'),2),
                        'remitted_cod' => round(Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->sum('invoice_amount'),2),
                        'nextRemitDate' => $codArray['nextRemitDate'],
                        'nextRemitCod' => round($codArray['nextRemitCod'],2),
                        'rto_order' => Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'rto_initiated' => Order::where('seller_id', $seller->id)->where('rto_status', 'y')->where('status','=','rto_initiated')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                        'rto_delivered' => Order::where('seller_id', $seller->id)->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    ];
                    //$counters['cod_pending'] = $counters['cod_available'] - $counters['remitted_cod'];
                    $counters['rto_undelivered'] = $counters['rto_order'] - $counters['rto_initiated'] - $counters['rto_delivered'];
                    return $counters;
                });

                // Dashboard overview counters
                $overview = Cache::store('redis')->remember('overview-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller) {
                    $overview = [];
                    $overview['states'] = States::limit(36)->get();
                    $overview['mapData'] = [];
                    foreach ($overview['states'] as $s) {
                        $count = Order::where('seller_id', $seller->id)->where('s_state', $s->state)->whereNotIn('status', ['pending', 'cancelled'])->count();
                        $overview['mapData'][] = [
                            'id' => $s->code,
                            'value' => $count ?? 0
                        ];
                    }
                    $current_quarter = ceil(date('n') / 3);
                    $first_date = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
                    $last_date = date('Y-m-t', strtotime(date('Y') . '-' . (($current_quarter * 3)) . '-1'));
                    $overview['revenue_lifetime'] = Order::where('seller_id', $seller->id)->sum('invoice_amount');
                    $overview['revenue_week'] = Order::where('seller_id', $seller->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('invoice_amount');
                    $overview['revenue_month'] = Order::where('seller_id', $seller->id)->whereMonth('awb_assigned_date', Carbon::now()->month)->sum('invoice_amount');
                    $overview['revenue_year'] = Order::where('seller_id', $seller->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('invoice_amount');
                    $overview['revenue_quarter'] = Order::where('seller_id', $seller->id)->whereBetween('awb_assigned_date', [$first_date, $last_date])->sum('invoice_amount');

                    $overview['PartnerName'] = Partners::getPartnerKeywordList();

                    $overview['delivered'] = Order::where('seller_id', $seller->id)->where('rto_status','n')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['undelivered'] = Order::where('seller_id', $seller->id)->where('status', '!=', 'delivered')->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['intransit'] = Order::where('seller_id', $seller->id)->where('status', 'in_transit')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['rto'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['damaged'] = Order::where('seller_id', $seller->id)->where('status', 'damaged')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    //$overview['OrderDelivered'] = $this->getDelivered($courier_partner = '');

                    //For Zone Counting
                    $overview['zone_a'] = Order::where('seller_id', $seller->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['zone_b'] = Order::where('seller_id', $seller->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['zone_c'] = Order::where('seller_id', $seller->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['zone_d'] = Order::where('seller_id', $seller->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['zone_e'] = Order::where('seller_id', $seller->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $overview['courier_split'] = Order::select(DB::raw('distinct(courier_partner)'), DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->orderBy('total_order','desc')->groupBy('courier_partner')->limit(5)->get();
                    //select DISTINCT courier_partner,count(courier_partner) as total from orders where seller_id = 16 group by courier_partner order by total desc limit 4
                    $overview['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',$seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();
                    //dd($overview['partners']);
                    foreach ($overview['allPartners'] as $p){
                        //for Courirer Partner 1 Overview
                        $overview['partner_unscheduled'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();
                        $overview['partner_scheduled'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();
                        $overview['partner_intransit'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();
                        $overview['partner_delivered'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();
                        $overview['partner_ndr_raised'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();
                        //remove NDR Raised Column from Courier Overview
                        $overview['partner_ndr_delivered'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();
                        $overview['partner_ndr_pending'][$p] = $overview['partner_ndr_raised'][$p] - $overview['partner_ndr_delivered'][$p];
                        $overview['partner_ndr_rto'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();
                        $overview['partner_damaged'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();
                        $overview['partner_total'][$p] = $overview['partner_unscheduled'][$p] + $overview['partner_scheduled'][$p] + $overview['partner_intransit'][$p] + $overview['partner_delivered'][$p] + $overview['partner_ndr_delivered'][$p] + $overview['partner_ndr_pending'][$p] + $overview['partner_ndr_rto'][$p] + $overview['partner_damaged'][$p];
                    }
                    //for Other Courier Partner  Overview
                    $overview['other_partner_unscheduled'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_scheduled'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_intransit'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_delivered'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_ndr_raised'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_ndr_delivered'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_ndr_pending'] = $overview['other_partner_ndr_raised'] - $overview['other_partner_ndr_delivered'];
                    $overview['other_partner_ndr_rto'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_damaged'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $overview['allPartners'])->count();
                    $overview['other_partner_total'] = $overview['other_partner_unscheduled'] + $overview['other_partner_scheduled'] + $overview['other_partner_intransit'] + $overview['other_partner_delivered'] + $overview['other_partner_ndr_delivered'] + $overview['other_partner_ndr_pending'] + $overview['other_partner_ndr_rto'] + $overview['other_partner_damaged'];
                    $res = DB::select("SELECT count(*) as total from `orders` where `seller_id`=" . $seller->id . " and `status`='delivered' and `delivered_date` <= `expected_delivery_date` or `seller_id`=" . $seller->id . " and `status`='delivered' and `expected_delivery_date` is NULL");
                    $overview['ontime_delivery'] = $res[0]->total ?? 0;
                    $res = DB::select("SELECT count(*) as total from `orders` where seller_id=" . $seller->id . " and `status`='delivered' and `delivered_date` > `expected_delivery_date`");
                    $overview['late_delivery'] = $res[0]->total ?? 0;
                    return $overview;
                });

                // Order counter
                $orders = Cache::store('redis')->remember('orders-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller) {
                    $order = [];
                    $order['popular_location_order'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_order')->limit(10)->get();
                    $order['popular_location_revenue'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_amount')->limit(10)->get();
                    $order['cod_order'] = Order::where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'cod')->where('status', 'delivered')->count();
                    $order['prepaid_order'] = Order::where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'prepaid')->count();
                    $order['top_customer_order'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_order')->get();
                    $order['top_customer_revenue'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_amount')->get();
                    $order['top_product_order'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('unit_sold')->limit(10)->get();
                    $order['top_product_revenue'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', $seller->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('total_revenue')->limit(10)->get();

                    $yesterday = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 days"));
                    $two_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-2 days"));
                    $three_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-3 days"));
                    $four_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-4 days"));
                    $order['allDays'] = [$yesterday,$two_day_ago,$three_day_ago,$four_day_ago];

                    foreach ($order['allDays'] as $d) {
                        $order['partner_unscheduled'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['manifested', 'pickup_scheduled'])->count();
                        $order['partner_scheduled'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->where('status', 'picked_up')->count();
                        $order['partner_intransit'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status', 'n')->where('ndr_status', 'n')->count();
                        $order['partner_delivered'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('rto_status', 'n')->where('ndr_status', 'n')->count();
                        $order['partner_ndr_raised'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date',$d)->where('ndr_status', 'y')->where('rto_status', 'n')->count();
                        //remove NDR Raised Column from Courier Overview
                        $order['partner_ndr_delivered'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status', 'n')->count();
                        $order['partner_ndr_pending'][$d] = $order['partner_ndr_raised'][$d] - $order['partner_ndr_delivered'][$d];
                        $order['partner_ndr_rto'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->where('rto_status', 'y')->count();
                        $order['partner_damaged'][$d] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['damaged', 'lost'])->count();
                        $order['partner_total'][$d] = $order['partner_unscheduled'][$d] + $order['partner_scheduled'][$d] + $order['partner_intransit'][$d] + $order['partner_delivered'][$d] + $order['partner_ndr_delivered'][$d] + $order['partner_ndr_pending'][$d] + $order['partner_ndr_rto'][$d] + $order['partner_damaged'][$d];
                    }
                    return $order;
                });

                // Shipment counters
                $shipments = Cache::store('redis')->remember('shipments-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller) {
                    $shipment = [];
                    $shipment['cod_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['prepaid_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    //For Zone Counting
                    $shipment['zone_a'] = Order::where('seller_id', $seller->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['zone_b'] = Order::where('seller_id', $seller->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['zone_c'] = Order::where('seller_id', $seller->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['zone_d'] = Order::where('seller_id', $seller->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['zone_e'] = Order::where('seller_id', $seller->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['shipment_channel'] = Order::select('channel', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('channel')->get();
                    $shipment['half_kgs'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '<=', 500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['one_kgs'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 500)->where('weight', '<=', 1000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['one_half_kgs'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1000)->where('weight', '<=', 1500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['two_kgs'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1500)->where('weight', '<=', 2000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['five_kgs'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 2000)->where('weight', '<=', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $shipment['five_kgs_plus'] = Order::where('seller_id', $seller->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

                    $shipment['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',$seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();

                    $shipment['courier_partner1_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('courier_partner', $seller->courier_priority_1)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                    $shipment['courier_partner2_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('courier_partner', $seller->courier_priority_2)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                    $shipment['courier_partner3_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('courier_partner', $seller->courier_priority_3)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                    $shipment['courier_partner4_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('courier_partner', $seller->courier_priority_4)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                    $shipment['other_partner_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->whereNotIn('courier_partner', [$seller->courier_priority_1, $seller->courier_priority_2, $seller->courier_priority_3, $seller->courier_priority_4])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();

                    // $start_date =  date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-7 days"));
                    // $end_date =  date('Y-m-d');
                    //for Courirer Partner 1 Overview
                    //dd($shipment['partners']);
                    foreach ($shipment['allPartners'] as $p){
                        //for Courirer Partner 1 Overview
                        $shipment['partner_unscheduled'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();
                        $shipment['partner_scheduled'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();
                        $shipment['partner_intransit'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();
                        $shipment['partner_delivered'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();
                        $shipment['partner_ndr_raised'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();
                        //remove NDR Raised Column from Courier Overview
                        $shipment['partner_ndr_delivered'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();
                        $shipment['partner_ndr_pending'][$p] = $shipment['partner_ndr_raised'][$p] - $shipment['partner_ndr_delivered'][$p];
                        $shipment['partner_ndr_rto'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();
                        $shipment['partner_damaged'][$p] = Order::where('seller_id', $seller->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();
                        $shipment['partner_total'][$p] = $shipment['partner_unscheduled'][$p] + $shipment['partner_scheduled'][$p] + $shipment['partner_intransit'][$p] + $shipment['partner_delivered'][$p] + $shipment['partner_ndr_delivered'][$p] + $shipment['partner_ndr_pending'][$p] + $shipment['partner_ndr_rto'][$p] + $shipment['partner_damaged'][$p];
                    }
                    //for Other Courier Partner  Overview
                    $shipment['other_partner_unscheduled'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_scheduled'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_intransit'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_delivered'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_ndr_raised'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_ndr_delivered'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_ndr_pending'] = $shipment['other_partner_ndr_raised'] - $shipment['other_partner_ndr_delivered'];
                    $shipment['other_partner_ndr_rto'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_damaged'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                    $shipment['other_partner_total'] = $shipment['other_partner_unscheduled'] + $shipment['other_partner_scheduled'] + $shipment['other_partner_intransit'] + $shipment['other_partner_delivered'] + $shipment['other_partner_ndr_delivered'] + $shipment['other_partner_ndr_pending'] + $shipment['other_partner_ndr_rto'] + $shipment['other_partner_damaged'];

                    $shipment['PartnerName'] = Partners::getPartnerKeywordList();
                    return $shipment;
                });

                // NDR counters
                $ndrs = Cache::store('redis')->remember('ndrs-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller) {
                    $ndr = [];
                    $seller_id = $seller->id;
                    $ndr['total_order'] = Order::where('seller_id', $seller_id)->whereNotIn('status', ['pending', 'cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $ndr['total_ndr'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    //$ndr['ndr_pending'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->where('ndr_action', 'pending')->where('status','!=','delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $ndr['action_required'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $ndr['action_requested'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $ndr['ndr_delivered'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->where('rto_status', 'n')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $ndr['ndr_rto'] = Order::where('seller_id', $seller_id)->where('ndr_status', 'y')->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

                    $ndr['reason_split'] = Ndrattemps::select('reason', DB::raw('count(*) as total_reason'))->where('seller_id', $seller_id)->groupBy('reason')->get();
                    $ndrCounts = Order::select("ndr_action",'rto_status','status')->where('seller_id',$seller_id)->where('ndr_status','y')->where('awb_assigned_date','>=',$start_date)->where('awb_assigned_date','<=',$end_date)->get();
                    //dd($ndrCounts);
                    $ndr['total_ndr'] = 0;$ndr['action_required'] = 0;$ndr['action_requested'] = 0;$ndr['ndr_delivered'] = 0;$ndr['ndr_rto'] = 0;$ndr['attempt1_total'] = 0;$ndr['attempt1_pending'] = 0;$ndr['attempt1_delivered'] = 0;$ndr['attempt1_rto'] = 0;$ndr['attempt1_lost'] = 0;$ndr['attempt2_total'] = 0;$ndr['attempt2_pending'] = 0;$ndr['attempt2_delivered'] = 0;$ndr['attempt2_rto'] = 0;$ndr['attempt2_lost'] = 0;$ndr['attempt3_total'] = 0;$ndr['attempt3_pending'] = 0;$ndr['attempt3_delivered'] = 0;$ndr['attempt3_rto'] = 0;$ndr['attempt3_lost'] = 0;
                    foreach ($ndrCounts as $n){
                        $n->ndr_count = count($n->ndrattempts);
                        // Total NDR
                        $ndr['total_ndr'] += 1;
                        $ndr['action_required'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'pending') ? 1 : 0;
                        $ndr['action_requested'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'requested') ? 1 : 0;
                        $ndr['ndr_delivered'] += ($n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
                        $ndr['ndr_rto'] += ($n->rto_status == 'y') ? 1 : 0;

                        // Attempt 1
                        $ndr['attempt1_total'] += $n->ndr_count <= 1 ? 1 : 0;
                        $ndr['attempt1_pending'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
                        $ndr['attempt1_delivered'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
                        $ndr['attempt1_rto'] += ($n->ndr_count <= 1 && $n->rto_status == 'y') ? 1 : 0;
                        $ndr['attempt1_lost'] += ($n->ndr_count <= 1 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

                        // Attempt 2
                        $ndr['attempt2_total'] += $n->ndr_count == 2 ? 1 : 0;
                        $ndr['attempt2_pending'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
                        $ndr['attempt2_delivered'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
                        $ndr['attempt2_rto'] += ($n->ndr_count == 2 && $n->rto_status == 'y') ? 1 : 0;
                        $ndr['attempt2_lost'] += ($n->ndr_count == 2 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

                        // Attempt 3
                        $ndr['attempt3_total'] += $n->ndr_count == 3 ? 1 : 0;
                        $ndr['attempt3_pending'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
                        $ndr['attempt3_delivered'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
                        $ndr['attempt3_rto'] += ($n->ndr_count == 3 && $n->rto_status == 'y') ? 1 : 0;
                        $ndr['attempt3_lost'] += ($n->ndr_count == 3 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;
                    }

                    $year = date('Y');
                    $this_week = date('W');
                    $two_week = date('W') - 1;
                    $three_week = date('W') - 2;
                    $four_week = date('W') - 3;
                    $five_week = date('W') - 4;
                    $thisdate = $this->_getStartAndEndDate($this_week, $year);
                    $two_date = $this->_getStartAndEndDate($two_week, $year);
                    $three_date = $this->_getStartAndEndDate($three_week, $year);
                    $four_date = $this->_getStartAndEndDate($four_week, $year);
                    $five_date = $this->_getStartAndEndDate($five_week, $year);
                    $ndr['this_week_date'] = $this->_getStartAndEndDateView($this_week, $year);
                    $ndr['two_week_date'] = $this->_getStartAndEndDateView($two_week, $year);
                    $ndr['three_week_date'] = $this->_getStartAndEndDateView($three_week, $year);
                    $ndr['four_week_date'] = $this->_getStartAndEndDateView($four_week, $year);
                    $ndr['five_week_date'] = $this->_getStartAndEndDateView($five_week, $year);
                    $ndr['this_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();
                    $ndr['two_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();
                    $ndr['three_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();
                    $ndr['four_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();
                    $ndr['five_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();

                    $ndr['z_ndr_raised_A'] = 0;$ndr['z_ndr_raised_B'] = 0;$ndr['z_ndr_raised_C'] = 0;$ndr['z_ndr_raised_D'] = 0;$ndr['z_ndr_raised_E'] = 0;$ndr['z_ndr_delivered_A'] = 0;$ndr['z_ndr_delivered_B'] = 0;$ndr['z_ndr_delivered_C'] = 0;$ndr['z_ndr_delivered_D'] = 0;$ndr['z_ndr_delivered_E'] = 0;
                    $zoneCount = DB::select("select zone,ndr_status,rto_status,status from orders where seller_id = $seller_id;");
                    foreach ($zoneCount as $zone){
                        //zone ndr raised count
                        $ndr['z_ndr_raised_A'] += ($zone->zone == 'A' && $zone->ndr_status == 'y') ? 1:0;
                        $ndr['z_ndr_raised_B'] += ($zone->zone == 'B' && $zone->ndr_status == 'y') ? 1:0;
                        $ndr['z_ndr_raised_C'] += ($zone->zone == 'C' && $zone->ndr_status == 'y') ? 1:0;
                        $ndr['z_ndr_raised_D'] += ($zone->zone == 'D' && $zone->ndr_status == 'y') ? 1:0;
                        $ndr['z_ndr_raised_E'] += ($zone->zone == 'E' && $zone->ndr_status == 'y') ? 1:0;

                        //Zone ndr delivered count
                        $ndr['z_ndr_delivered_A'] += ($zone->zone == 'A' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
                        $ndr['z_ndr_delivered_B'] += ($zone->zone == 'B' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
                        $ndr['z_ndr_delivered_C'] += ($zone->zone == 'C' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
                        $ndr['z_ndr_delivered_D'] += ($zone->zone == 'D' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
                        $ndr['z_ndr_delivered_E'] += ($zone->zone == 'E' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;

                    }

                    $ndr['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',$seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();

                    foreach ($ndr['allPartners'] as $p){
                        $ndr['p_ndr_raised'][$p]=Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->where('courier_partner', $p)->count();
                        $ndr['p_ndr_delivered'][$p]=Order::where('seller_id', $seller->id)->where('status','delivered')->where('ndr_status', 'y')->where('courier_partner', $p)->count();
                    }
                    $ndr['p_ndr_raised']['other']=Order::where('seller_id', $seller->id)->where('ndr_status', 'y')->whereNotIn('courier_partner', $ndr['allPartners'])->count();
                    $ndr['p_ndr_delivered']['other']=Order::where('seller_id', $seller->id)->where('status','delivered')->where('ndr_status', 'y')->whereNotIn('courier_partner', $ndr['allPartners'])->count();

                    $ndr['PartnerName'] = Partners::getPartnerKeywordList();
                    return $ndr;
                });

                // RTO counters
                $rtos = Cache::store('redis')->remember('rtos-'.$seller->id, (60*60)*20, function() use($start_date, $end_date, $seller) {
                    $rto = [];
                    //$rto['total_rto'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->count();
                    $rto['total_order'] = Order::where('seller_id', $seller->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $rto['rto_initiated'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $rto['rto_undelivered'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $rto['rto_delivered'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->where('status','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                    $rto['top_pincodes'] = Order::select('s_pincode', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('rto_status', 'y')->groupBy('s_pincode')->get();
                    $rto['top_cities'] = Order::select('s_city', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('rto_status', 'y')->groupBy('s_city')->limit(5)->get();
                    $rto['top_courier'] = Order::select('courier_partner', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('rto_status', 'y')->groupBy('courier_partner')->limit(5)->get();
                    $rto['top_customer'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('rto_status', 'y')->groupBy('b_customer_name')->limit(5)->get();
                    $rto['PartnerName'] = Partners::getPartnerKeywordList();
                    $rto['reason_split'] = Ndrattemps::select('reason', DB::raw('count(*) as total_reason'))->where('seller_id', $seller->id)->groupBy('reason')->get();

                    $year = date('Y');
                    $this_week = date('W');
                    $two_week = date('W') - 1;
                    $three_week = date('W') - 2;
                    $four_week = date('W') - 3;
                    $five_week = date('W') - 4;
                    $thisdate = $this->_getStartAndEndDate($this_week, $year);
                    $two_date = $this->_getStartAndEndDate($two_week, $year);
                    $three_date = $this->_getStartAndEndDate($three_week, $year);
                    $four_date = $this->_getStartAndEndDate($four_week, $year);
                    $five_date = $this->_getStartAndEndDate($five_week, $year);
                    $rto['this_week_date'] = $this->_getStartAndEndDateView($this_week, $year);
                    $rto['two_week_date'] = $this->_getStartAndEndDateView($two_week, $year);
                    $rto['three_week_date'] = $this->_getStartAndEndDateView($three_week, $year);
                    $rto['four_week_date'] = $this->_getStartAndEndDateView($four_week, $year);
                    $rto['five_week_date'] = $this->_getStartAndEndDateView($five_week, $year);
                    $rto['this_week'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->count();
                    $rto['two_week'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->count();
                    $rto['three_week'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->count();
                    $rto['four_week'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->count();
                    $rto['five_week'] = Order::where('seller_id', $seller->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->count();

                    $rto['this_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();
                    $rto['two_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();
                    $rto['three_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();
                    $rto['four_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();
                    $rto['five_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', $seller->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();
                    return $rto;
                });

                // Courier counters
                $couriers = Cache::store('redis')->remember('couriers-'.$seller->id, (60*60)*20, function() use($seller) {
                    $courier = [];
                    $courier['cod_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->count();
                    $courier['prepaid_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->count();
                    $courier['PartnerName'] = Partners::getPartnerKeywordList();
                    $courier['PartnerImage'] = Partners::getPartnerImage();

                    $partner1 = $seller->courier_priority_1;
                    $partner2 = $seller->courier_priority_2;
                    $partner3 = $seller->courier_priority_3;
                    $courier['partner1_shipment'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner1)->count();
                    $courier['partner2_shipment'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner2)->count();
                    $courier['partner3_shipment'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner3)->count();

                    $courier['partner1_cod'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('courier_partner', $partner1)->count();
                    $courier['partner2_cod'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('courier_partner', $partner2)->count();
                    $courier['partner3_cod'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->where('courier_partner', $partner3)->count();

                    $courier['partner1_prepaid'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->where('courier_partner', $partner1)->count();
                    $courier['partner2_prepaid'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->where('courier_partner', $partner2)->count();
                    $courier['partner3_prepaid'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->where('courier_partner', $partner3)->count();

                    $courier['partner1_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('courier_partner', $partner1)->count();
                    $courier['partner2_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('courier_partner', $partner2)->count();
                    $courier['partner3_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('courier_partner', $partner3)->count();

                    $courier['partner1_1st_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner1)->count();
                    $courier['partner2_1st_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner2)->count();
                    $courier['partner3_1st_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner3)->count();

                    $courier['partner1_ndr_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner1)->count();
                    $courier['partner2_ndr_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner2)->count();
                    $courier['partner3_ndr_delivered'] = Order::where('seller_id', $seller->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner3)->count();

                    $courier['partner1_ndr_raised'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner1)->where('ndr_status', 'y')->count();
                    $courier['partner2_ndr_raised'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner2)->where('ndr_status', 'y')->count();
                    $courier['partner3_ndr_raised'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner3)->where('ndr_status', 'y')->count();

                    $courier['partner1_rto'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner1)->where('rto_status', 'y')->count();
                    $courier['partner2_rto'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner2)->where('rto_status', 'y')->count();
                    $courier['partner3_rto'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner3)->where('rto_status', 'y')->count();

                    $courier['partner1_lost'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner1)->whereIn('status', ['lost,damaged'])->count();
                    $courier['partner2_lost'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner2)->whereIn('status', ['lost,damaged'])->count();
                    $courier['partner3_lost'] = Order::where('seller_id', $seller->id)->where('courier_partner', $partner3)->whereIn('status', ['lost,damaged'])->count();
                    return $courier;
                });

                // Delays counters
                $delays = Cache::store('redis')->remember('delays-'.$seller->id, (60*60)*20, function() use($seller) {
                    $delay = [];
                    $delay['cod_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'cod')->count();
                    $delay['prepaid_order'] = Order::where('seller_id', $seller->id)->where('order_type', 'prepaid')->count();
                    $delay['lost_order'] = Order::where('seller_id', $seller->id)->where('status', 'lost')->count();
                    $delay['damaged_order'] = Order::where('seller_id', $seller->id)->where('status', 'damaged')->count();
                    return $delay;
                });
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Load data into cache.
     *
     * Load all dashboard counter and cache for 20 hours.
     */
    function populateOrderTrackingCache() {
        try {
            $startedAt = now();
            $cronName = 'populate-order-tracking-cache';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $orders = Order::select('id', 'awb_number', 'status', 'rto_status', 'courier_partner')
                ->whereNotIn('status', ['pending', 'cancelled', 'delivered'])
                ->where('manifest_status', 'y')
                ->whereNotNull('awb_number')
                ->get();
            foreach($orders as $order) {
                $orderTracking = OrderTracking::where('awb_number', $order->awb_number)->orderBy('id', 'desc')->get()->toArray();
                $cachePayload = [];
                if(count($orderTracking) > 0) {
                    $cachePayload['OrderId'] = $order->id;
                    $cachePayload['AWBNumber'] = $order->awb_number;
                    $cachePayload['CourierPartner'] = $this->partnerNames[$order->courier_partner] ?? "Twinnship";
                    $cachePayload['CurrentStatus'] = $orderTracking[0]['status'];
                    $cachePayload['StatusCode'] = $order->status ?? "manifested";
                    if($cachePayload['StatusCode'] == 'delivered' && $order->rto_status == 'y') {
                        $cachePayload['StatusCode'] = 'rto_delivered';
                    }
                    if($cachePayload['StatusCode'] == 'in_transit' && $order->rto_status == 'y') {
                        $cachePayload['StatusCode'] = 'rto_in_transit';
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
                    $cachePayload['OrderHistory'][0]['StatusCode'] = $cachePayload['StatusCode'];
                } else {
                    $cachePayload['OrderId'] = $order->id;
                    $cachePayload['AWBNumber'] = $order->awb_number;
                    $cachePayload['CourierPartner'] = $this->partnerNames[$order->courier_partner] ?? "Twinnship";
                    $cachePayload['CurrentStatus'] = 'Pending';
                    $cachePayload['StatusCode'] = $order->status ?? null;
                    $cachePayload['OrderHistory'] = [];
                }

                // Cache::store('redis')->forget('api-tracking-'.$order->awb_number);
                // Cache::store('redis')->forget('api-tracking-'.$order->id);
                Cache::store('redis')->put('api-tracking-'.$order->awb_number, $cachePayload, (60*60)*22);
                Cache::store('redis')->put('api-tracking-'.$order->id, $cachePayload, (60*60)*22);
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // get Start and End date using week number and year
    function _getStartAndEndDate($week, $year)
    {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');
        return $result;
    }

    // get Start and End date using week number and year
    function _getStartAndEndDateView($week, $year) {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('d M');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('d M');
        return $result['start_date'] . ' - ' . $result['end_date'];
    }

    function createWarehouseForHeavyDelhivery() {
        $allWarehouses = Warehouses::get();
        // dd($allWarehouses);
        foreach ($allWarehouses as $w){
            $payload = [
                "phone" => $w->contact_number,
                "city" => $w->city,
                "name" => $w->warehouse_code,
                "pin" => $w->pincode,
                "address" => $w->address_line1,
                "country" => $w->country,
                "email" => $w->support_email,
                "registered_name" => $w->warehouse_code,
                "return_address" => $w->address_line1,
                "return_pin" => $w->pincode,
                "return_city" => $w->city,
                "return_state" => $w->state,
                "return_country" => $w->country
            ];
            // dd($payload);

            // $response = Http::withHeaders([
            //     'Authorization' => 'Token 5a396ff5c4555d639a7149cac4beb0bb0af8db94',
            //     'Content-Type' => 'application/json'
            // ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Token 3141800ec51f036f997cd015fdb00e8aeb38e126',
                'Content-Type' => 'application/json'
            ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload);
        }
        echo "Created Successfully";
    }

    function archiveLogs() {
        try {
            // $warehouses = Warehouses::where('seller_id', '!=', 1)->get();
            // // dd($warehouses);
            // foreach($warehouses as $row) {
            //     $gati = new Gati();
            //     $gati->createWarehouse($row);
            // }
            // dd("ok");

            // // 529551061 – 529561060
            // $packages = [];
            // $cnt = 0;
            // for($i = 529551061; $i <= 529561060; $i++) {
            //     $packages[] = [
            //         'courier_partner' => 'gati',
            //         'package_number' => $i,
            //     ];
            //     if($cnt == 10000) {
            //         DB::table('gati_package_numbers')->insert($packages);
            //         $cnt = 0;
            //         $packages = [];
            //     }
            //     $cnt++;
            // }
            // dd(DB::table('gati_package_numbers')->insert($packages));

            // //22090374001001 – 22090374051000
            // $awbs = [];
            // $cnt = 0;
            // for($i = 22090374001001; $i <= 22090374051000; $i++) {
            //     $awbs[] = [
            //         'courier_partner' => 'shree_maruti',
            //         'awb_number' => $i,
            //         'assigned' => 'y',
            //         'seller_id' => 150
            //     ];
            //     if($cnt == 10000) {
            //         DB::table('maruti_awbs')->insert($awbs);
            //         $cnt = 0;
            //         $awbs = [];
            //     }
            //     $cnt++;
            // }
            // dd(DB::table('maruti_awbs')->insert($awbs));
            $startedAt = now();
            $cronName = 'archive-logs';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;
            Logger::archiveLogs('archives/');
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function archiveBarcodes() {
        try {
            $startedAt = now();
            $cronName = 'archive-barcodes';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;
            Logger::archiveBarcodes('archives/');
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fetch all serviceable pincodes from courier partner
     *
     * @return mixed
     */
    function getServiceablePincodeSmartr()
    {
        try {
            $startedAt = now();
            $cronName = 'fetch-smartr-serviceability';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $smartr = new Smartr();
            $allData = [];
            $pincodes = $smartr->getServiceablePincodes();
            DB::beginTransaction();
            if(!empty($pincodes['status']) && $pincodes['status'] == 'Success' && count($pincodes['data']) > 0) {
                $rowDeleted = ServiceablePincode::where('courier_partner','smartr')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner','smartr')->where('active','n')->pluck('pincode')->toArray();
                Logger::write('logs/crones/smartr/smartr-'.date('Y-m-d').'.text', [
                    'title' => "Fetched Serviceable Pincodes: ".count($pincodes['data']),
                    'data' => []
                ]);
                foreach ($pincodes['data'] as $p){
                    if(!in_array($p['pincode'],$disabledPincode)) {
                        $allData[] = [
                            'partner_id' => 52,
                            'courier_partner' => 'smartr',
                            'pincode' => $p['pincode'],
                            'city' => $p['city_name'],
                            'state' => $p['state_name'],
                            'branch_code' => $p['service_center'],
                            'status' => 'Y',
                            'inserted' => date('Y-m-d H:i:s')
                        ];
                    }
                    if(count($allData)==700)
                    {
                        ServiceablePincode::insert($allData);
                        $allData=[];
                    }
                }
                ServiceablePincode::insert($allData);
                DB::commit();
//                $collection = collect($pincodes['data']);
//                $collection = $collection->where('inbound', true)
//                    ->where('outbound', true)
//                    ->where('is_active', true);
//                $collection = $collection->map(function($item) {
//                    return [
//                        'partner_id' => 52,
//                        'courier_partner' => 'smartr',
//                        'pincode' => $item['pincode'],
//                        'city' => $item['city_name'],
//                        'state' => $item['state_name'],
//                        'branch_code' => $item['service_center'],
//                        'status' => 'Y',
//                        'inserted' => date('Y-m-d H:i:s')
//                    ];
//                });
//                $chunks = $collection->chunk(1000);
//                foreach($chunks as $chunk) {
//                    ServiceablePincode::insert($chunk->toArray());
//                }
//                $rowInserted = $collection->count();
//                DB::commit();
                Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
                return response()->json([
                    'status' => true,
                    'message' => 'Cron executed successfully'
                ]);
            }
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, json_encode($pincodes), 0, 0, 0, $startedAt, now());
            DB::rollback();
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), 0, 0, 0, $startedAt, now());
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function syncTransaction()
    {
        try {
            // 218429
            // Wallet deduction
            $orders = Order::where('awb_assigned_date', '>=', now()->subDays(4))->get();
            // dd(count($orders));
            $cnt = 0;
            $amount = 0;
            foreach($orders as $order) {
                $transaction_check = Transactions::where('seller_id', $order->seller_id)->where('order_id', $order->id)->count();
                if ($transaction_check == 0) {
                    $seller = Seller::find($order->seller_id);
                    if(!($seller->id == 16 && str_starts_with($order->courier_partner, 'dtdc'))) {
                        $data = array(
                            'seller_id' => $seller->id,
                            'order_id' => $order->id,
                            'amount' => $order->total_charges,
                            'balance' => $seller->balance - $order->total_charges,
                            'type' => 'd',
                            'redeem_type' => 'o',
                            'datetime' => $order->awb_assigned_date,
                            'method' => 'wallet',
                            'description' => 'Order Shipping Charge Deducted'
                        );
                        Transactions::create($data);
                        Seller::where('id', $seller->id)->decrement('balance', $data['amount']);
                        $cnt++;
                        $amount += $data['amount'];
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully',
                'data' => [
                    'totalDebit' => $cnt,
                    'totalAmount' => $amount
                ]
            ]);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
    function fetchDelhiveryServiceablePincodes(){
        try {
            DB::beginTransaction();
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->get("https://track.delhivery.com/c/api/pin-codes/json/?token=894217b910b9e60d3d12cab20a3c5e206b739c8b");
            $responseData = $response->json();
            if(count($responseData['delivery_codes']) > 0) {
                ServiceablePincode::where('courier_partner', 'delhivery_surface')->where('active', 'y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'delhivery_surface')->where('active', 'n')->pluck('pincode')->toArray();
                $allData = [];
                foreach ($responseData['delivery_codes'] as $dc) {
                    $pincode = $dc['postal_code']['pin'];
                    if(strlen($pincode) == 6){
                        if(!in_array($pincode,$disabledPincode)){
                            $allData[] = [
                                'partner_id' => 4,
                                'courier_partner' => 'delhivery_surface',
                                'pincode' => $pincode,
                                'city' => $dc['postal_code']['inc'] ?? "",
                                'state' => $dc['postal_code']['state_code'] ?? "",
                                'branch_code' => $dc['sort_code']['inc'] ?? "",
                                'status' => 'Y',
                                'inserted' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                    if (count($allData) == 1000) {
                        ServiceablePincode::insert($allData);
                        $allData = [];
                    }
                }
                ServiceablePincode::insert($allData);
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Cron executed successfully'
                ]);
            }
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        return true;
    }
    function SendMailForNotUpdatingStatus(){
        $allOrders = DB::select("SELECT seller_id,awb_number,awb_assigned_date as shipped_date,status,last_sync as last_status_sync,last_executed as last_job_run,TIMESTAMPDIFF(HOUR,last_executed, last_sync) AS `pending_hours` from orders where last_executed is not null and TIMESTAMPDIFF(HOUR,last_executed, last_sync) > 24 order by pending_hours desc");
        $mailContent = "<table border='1'><tr><th>Sr No</th><th>Seller Id</th><th>AWB Number</th><th>Shipped Date</th><th>Order Status</th><th>Last Status Sync</th><th>Last Job Run</th><th>Pending Status Hours</th></tr>";
        $cnt=1;
        foreach($allOrders as $o){
            $mailContent.="<tr><td>{$cnt}</td><td>{$o->seller_id}</td><td>{$o->awb_number}</td><td>{$o->shipped_date}</td><td>{$o->status}</td><td>{$o->last_status_sync}</td><td>{$o->last_job_run}</td><td>{$o->pending_hours} Hours</td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        //$mailContent = "<h1>Hello</h1>";
        //dd($mailContent);
        //$utility = new Utilities();
        //$utility->send_email('info.Twinnship@gmail.com','Twinnship Corporation','Order In-Scan Pending for 24 Hours',$mailContent,"Order In-Scan Pending for 24 Hours");
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "Order In-Scan Pending for 24 Hours";
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
    }
    function SendMailForManifestOrders(){
        $allOrders = DB::select("SELECT seller_id,awb_number,awb_assigned_date as shipped_date,status,last_sync as last_status_sync,last_executed as last_job_run,TIMESTAMPDIFF(HOUR,last_executed, last_sync) AS `pending_hours`,manifest_status,manifest_sent from orders where last_executed is not null and status = 'manifested' and TIMESTAMPDIFF(HOUR,last_executed, last_sync) > 168 order by pending_hours desc");
        $mailContent = "<table border='1'><tr><th>Sr No</th><th>Seller Id</th><th>AWB Number</th><th>Shipped Date</th><th>Order Status</th><th>Last Status Sync</th><th>Last Job Run</th><th>Pending Status Hours</th><th>Manifest Status</th><th>Manifest Sent</th></tr>";
        $cnt=1;
        foreach($allOrders as $o){
            $days = floor($o->pending_hours/24);
            $mailContent.="<tr><td>{$cnt}</td><td>{$o->seller_id}</td><td>{$o->awb_number}</td><td>{$o->shipped_date}</td><td>{$o->status}</td><td>{$o->last_status_sync}</td><td>{$o->last_job_run}</td><td>{$days} Days</td><td>{$o->manifest_status}</td><td>{$o->manifest_sent}</td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "Manifested Orders for more than 7 Days";
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
    }
    function SendMailForNotFulfilledOrders(){
        $today = date('Y-m-d')." 00:00:00";
        $last = date('Y-m-d',strtotime("-1 day"))." 00:00:00";
        $allOrders = DB::select("select seller_id,channel,courier_partner,awb_number,fulfillment_sent,manifest_sent,status from orders where awb_assigned_date > '$last' and awb_assigned_date < '$today'  and fulfillment_sent = 'n' and status not in ('pending','cancelled') and manifest_sent = 'y'");
        $mailContent = "<table border='1'><tr><th>Sr No</th><th>Seller Id</th><th>AWB Number</th><th>Courier Partner</th><th>Channel</th><th>Manifest Sent</th><th>Status</th></tr>";
        $cnt=1;
        foreach($allOrders as $o){
            $mailContent.="<tr><td>{$cnt}</td><td>{$o->seller_id}</td><td>{$o->awb_number}</td><td>{$o->courier_partner}</td><td>{$o->channel}</td><td>{$o->manifest_sent}</td><td>{$o->status}</td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "Not Fulfilled Orders for $last";
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);;
    }
    function SendMailForBlockedPincodes(){
        $date = date('Y-m-d 00:00:00',strtotime('-1 day'));
        $allPincodes = ServiceablePincode::where('active','n')->where('modified','>=',$date)->orderBy('courier_partner')->get();
        $mailContent = "<table border='1'><tr><th>Sr No</th><th>Courier Partner</th><th>Pincode</th><th>Modified</th><th>Remark</th></tr>";
        $cnt=1;
        foreach($allPincodes as $o){
            $mailContent.="<tr><td>{$cnt}</td><td>{$o->courier_partner}</td><td>{$o->pincode}</td><td>{$o->modified}</td><td>{$o->remark}</td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "Pincodes Blocked for Date : ".date('d/m/Y',strtotime($date));
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
    }
    function SendMailForJobStatus(){
        $logs = DB::table('cron_jobs')->get();
        $mailContent = "<table border='1'><tr><th>Sr.No</th><th>Job Name</th><th>Last Status</th><th>Started At</th><th>Finished At</th></tr>";
        $cnt=1;
        foreach($logs as $row){
            $mailContent.="<tr><td>{$cnt}</td><td>{$row->job_name}</td><td>{$row->last_status}</td><td>{$row->started_at}</td><td>{$row->finished_at}</td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "Cron job status logs";
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
    }

    // Send awb threshold email
    function awbThreshold() {
        $advanceAwbs = [
            'shadowfax_awb_numbers' => 'Shadowfax',
            'delhivery_awb_numbers' => 'Delhivery',
            'ecom_express_awbs' => 'Ecom Express',
            'ekart_awb_numbers' => 'Ekart',
            'gati_awbs' => 'Gati',
            'maruti_awbs_ecom' => 'SMC Ecom',
            'smartr_awbs' => 'Smartr',
            'xbees_awb_numbers' => 'Xpressbees',
            'xbees_awb_numbers_unique' => 'Xpressbees Unique',
            'dtdc_ll_awb_numbers' => 'DTDC (LL)',
            'dtdc_se_awb_numbers' => 'DTDC SE',
            'bluedart_awb_numbers as bluedart_awb_numbers_cod' => 'BlueDart(COD)',
            'bluedart_awb_numbers as bluedart_awb_numbers_prepaid' => 'BlueDart(Prepaid)',
            'bluedart_awb_numbers as bluedart_plus_awb_numbers_cod' => 'BlueDart+(COD)',
            'bluedart_awb_numbers as bluedart_plus_awb_numbers_prepaid' => 'BlueDart+(Prepaid)',
            'bluedart_nse_awb_numbers as bluedart_nse_awb_numbers_cod' => 'BlueDart(NSE)(COD)',
            'bluedart_nse_awb_numbers as bluedart_nse_awb_numbers_prepaid' => 'BlueDart(NSE)(Prepaid)',
            'bluedart_nse_awb_numbers as bluedart_plus_nse_awb_numbers_cod' => 'BlueDart+(NSE)(COD)',
            'bluedart_nse_awb_numbers as bluedart_plus_nse_awb_numbers_prepaid' => 'BlueDart+(NSE)(Prepaid)',
            'professional_awb_numbers' => 'The Professional Courier'
        ];
        $thresholds = collect([]);
        foreach($advanceAwbs as $table => $partner) {
            // Get awb numbers thresholds
            if (strpos($table, 'as ') !== false) {
                // Extract the value after "as" and store it in the new array
                $parts = explode('as ', $table);
                $asKey = trim(end($parts));
                //BlueDart(COD)
                if($asKey == "bluedart_awb_numbers_cod"){
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                    ];
                }
                //BlueDart(Prepaid)
                else if($asKey == "bluedart_awb_numbers_prepaid"){
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                    ];
                }

                //BlueDart+(COD)
                else if($asKey == "bluedart_plus_awb_numbers_cod"){
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                    ];
                }

                //BlueDart+(Prepaid)
                else if($asKey == "bluedart_plus_awb_numbers_prepaid"){
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                    ];
                }

                //BlueDart(NSE)(COD)
                elseif ($asKey == "bluedart_nse_awb_numbers_cod")
                {
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart')->where('awb_type',"=",'cod')->count(),
                    ];
                }

                //BlueDart(NSE)(Prepaid)
                elseif ($asKey == "bluedart_nse_awb_numbers_prepaid")
                {
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart')->where('awb_type',"=",'prepaid')->count(),
                    ];
                }

                //BlueDart+(NSE)(COD)
                elseif ($asKey == "bluedart_plus_nse_awb_numbers_cod")
                {
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'cod')->count(),
                    ];
                }

                //BlueDart+(NSE)(Prepaid)
                elseif ($asKey == "bluedart_plus_nse_awb_numbers_prepaid")
                {
                    $data = [
                        'courier_partner' => $partner,
                        'available_awb' => DB::table($table)->where('used', 'n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'used_awb' => DB::table($table)->where('used', 'y')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'total_awb' => DB::table($table)->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                        'remaining_awb' => DB::table($table)->where('used','n')->where('courier_keyword','bluedart_surface')->where('awb_type',"=",'prepaid')->count(),
                    ];
                }
            }
            else
            {
                $data = [
                    'courier_partner' => $partner,
                    'available_awb' => DB::table($table)->where('used', 'n')->count(),
                    'used_awb' => DB::table($table)->where('used', 'y')->count(),
                    'total_awb' => DB::table($table)->count(),
                    'remaining_awb' => DB::table($table)->where('used','n')->count(),
                ];
            }
            // Calculate in %
            if(!empty($data['total_awb'])) {
                $data['available_awb_in_pr'] = round($data['available_awb'] * 100 / $data['total_awb'], 2);
                $data['used_awb_in_pr'] = round($data['used_awb'] * 100 / $data['total_awb'], 2);
                $data['remaining_awb_in_pr'] = $data['remaining_awb'];
            } else {
                $data['available_awb_in_pr'] = 0;
                $data['used_awb_in_pr'] = 0;
                $data['remaining_awb_in_pr'] = 0;
            }
            $thresholds->push($data);
        }
        $mailContent = "<table border='1'><th>Sr.No</th><th>Courier Partner</th><th>Used Awbs</th><th>Available Awbs</th><th>Total Awbs</th><th>Remaining Awbs</th></tr>";
        $cnt=1;
        foreach($thresholds as $row){
            $mailContent .= "<tr><td>{$cnt}</td><td>" . $row['courier_partner'] . "</td><td><strong style='". ($row['used_awb_in_pr'] < 90 ? 'color:green' : 'color:red') . "'>" . $row['used_awb_in_pr'] . " %</strong></td><td><strong style='" . ($row['available_awb_in_pr'] > 10 ? 'color:green' : 'color:red') . "'>" . $row['available_awb_in_pr'] . " %</strong></td><td><strong>" . number_format($row['total_awb']) . "</strong></td><td><strong style='" . ($row['available_awb_in_pr'] > 10 ? 'color:green' : 'color:red') . "'>" . $row['remaining_awb_in_pr'] . " </strong></td></tr>";
            $cnt++;
        }
        $mailContent.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $mailContent);
        $email = "info.Twinnship@gmail.com";
        $subject = "AWB Threshold Status";
        // echo $mailContent;
        // exit();
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);
        $email = "tech@Twinnship.in";
        $subject = "AWB Threshold Status";
        // echo $mailContent;
        // exit();
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$mailContent,$subject);

    }
    function updateEkartManifestationDetails(){
        $orders = Order::where('courier_partner','ekart')->where('awb_assigned_date','<','2022-06-16 00:00:00')->whereNotIn('status',['pending','cancelled'])->get();
        $ekart = new Ekart();
        foreach($orders as $o){
            $ekart->updateOrder($o);
        }
    }

    function updateLostStatus() {
        try {
            DB::enableQueryLog();
            $orders = Order::with('seller')
                ->with('tracking')
                ->with(['courier' => function($q) {
                    $q->where('status', 'y');
                }])
                ->whereHas('courier', function($q) {
                    $q->where('status', 'y')
                        ->where('liability_amount', '>', 0);
                })
                ->whereHas('seller')
                ->whereNotIn('status', ['pending', 'delivered', 'cancelled', 'lost', 'damaged'])
                ->whereNotIn('courier_partner', ['xpressbees_sfc', 'dtdc_surface', 'dtdc_express', 'dtdc_10kg', 'dtdc_2kg', 'dtdc_3kg', 'dtdc_5kg', 'dtdc_6kg','dtdc_1kg'])
                ->where('rto_status', 'n')
                ->whereDate('awb_assigned_date', '>=', '2022-04-01')
                ->whereDate('awb_assigned_date', '<=', now()->subDays(80))
                ->whereIn('seller_id', [16])
                ->get();
            dd(DB::getQueryLog(), $orders->count());
            // select * from `orders` where exists (select * from `partners` where `orders`.`courier_partner` = `partners`.`keyword` and `status` = 'y' and `liability_amount` > 0) and exists (select * from `sellers` where `orders`.`seller_id` = `sellers`.`id`) and `status` not in ('pending', 'delivered', 'cancelled', 'lost', 'damaged') and `courier_partner` not in ('xpressbees_sfc', 'dtdc_surface', 'dtdc_express', 'dtdc_10kg', 'dtdc_2kg', 'dtdc_3kg', 'dtdc_5kg') and `rto_status` = 'n' and date(`awb_assigned_date`) >= '2022-04-01' and date(`awb_assigned_date`) >= '2022-03-30' and `seller_id` in (16)
            foreach($orders as $order) {
                // Update status to lost
                DB::beginTransaction();
                $order->status = 'lost';
                $order->save();
                $tracking = new OrderTracking();
                $tracking->status = 'lost';
                $tracking->status_code = 'lost';
                $tracking->status_description = 'Shipment lost by courier';
                $tracking->remarks = 'Order status updated by Twinnship';
                $tracking->location = 'NA';
                $tracking->updated_date = now();
                $order->tracking()->save($tracking);

                // Refund amount in wallet
                $amount = ($order->invoice_amount < $order->courier->liability_amount ? $order->invoice_amount : $order->courier->liability_amount);
                $seller = Seller::find($order->seller_id);
                $data = [
                    'seller_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'balance' => $seller->balance + $amount,
                    'type' => 'c',
                    'redeem_type' => 'o',
                    'datetime' => date('Y-m-d H:i:s'),
                    'method' => 'wallet',
                    'description' => 'Order Lost Reversal'
                ];
                $resp = Transactions::where('seller_id', $data['seller_id'])
                    ->where('order_id', $data['order_id'])
                    ->where('type', $data['type'])
                    ->where('amount', $data['amount'])
                    ->count();
                if (intval($resp) == 0) {
                    Transactions::create($data);
                    Seller::where('id', $order->seller_id)->increment('balance', $data['amount']);
                }
                DB::commit();
            }
        } catch(Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    // Do not live this code order cancellation code is not latest code please update it.
    function updateCancelledStatus() {
        try {
            DB::enableQueryLog();
            $orders = Order::with('seller')
                ->with('tracking')
                ->whereHas('seller')
                ->whereIn('status', ['shipped', 'manifested', 'pickup_requested'])
                ->whereNotIn('courier_partner', ['xpressbees_sfc', 'dtdc_surface', 'dtdc_express', 'dtdc_10kg', 'dtdc_2kg', 'dtdc_3kg', 'dtdc_5kg', 'dtdc_6kg','dtdc_1kg'])
                ->whereDate('awb_assigned_date', '<=', now()->subDays(10))
                ->whereIn('seller_id', [16])
                ->get();
            dd(DB::getQueryLog(), $orders->count());
            // select * from `orders` where exists (select * from `sellers` where `orders`.`seller_id` = `sellers`.`id`) and `status` in ('shipped', 'manifested', 'pickup_requested') and `courier_partner` not in ('xpressbees_sfc', 'dtdc_surface', 'dtdc_express', 'dtdc_10kg', 'dtdc_2kg', 'dtdc_3kg', 'dtdc_5kg') and date(`awb_assigned_date`) >= '2022-06-08' and `seller_id` in (16)
            foreach($orders as $order) {
                dd($order->toArray());
                // Canecel order and refund amount
                DB::beginTransaction();
                $order_type = strtolower($order->o_type);
                $awb  = $order->awb_number;
                switch ($order->courier_partner) {
                    case 'wow_express':
                        $this->shipment->_cancelOrderWowExpress($awb);
                        break;
                    case 'delhivery_surface':
                        $this->shipment->_cancelOrderDelhiverySurface($awb,"894217b910b9e60d3d12cab20a3c5e206b739c8b");
                        break;
                    case 'delhivery_surface_10kg':
                        $this->shipment->_cancelOrderDelhiverySurface($awb,"3141800ec51f036f997cd015fdb00e8aeb38e126");
                        break;
                    case 'delhivery_surface_20kg':
                        $this->shipment->_cancelOrderDelhiverySurface($awb,"18765103684ead7f379ec3af5e585d16241fdb94");
                        break;
                    case 'dtdc_surface':
                        $this->shipment->_cancelOrderDtdcSurface($awb);
                        break;
                    case 'xpressbees_sfc':
                        if($order->shipping_partner == 'prefexo') {
                            $prefexo = new Prefexo();
                            $prefexo->cancelOrder($awb);
                        } else {
                            if ($order_type == 'forward') {
                                $this->shipment->_cancelOrderXpressBees($awb, "SsNLds3552adLSIpksnPSKsK");
                            } else {
                                $this->shipment->_cancelReverseOrderXpressBees($awb, "SsNLds3552adLSIpksnPSKsK");
                            }
                        }
                        break;
                    case 'xpressbees_surface':
                        if ($order_type == 'forward')
                            $this->shipment->_cancelOrderXpressBees($awb, "kEVUGEG3450nSssVzZQ");
                        else
                            $this->shipment->_cancelReverseOrderXpressBees($awb, "kEVUGEG3450nSssVzZQ");
                        break;
                    case 'xpressbees_surface_1kg':
                        if ($order_type == 'forward')
                            $this->shipment->_cancelOrderXpressBees($awb, "JuJDsd3585sdfnuemsjsqISk");
                        else
                            $this->shipment->_cancelReverseOrderXpressBees($awb, "JuJDsd3585sdfnuemsjsqISk");
                        break;
                    case 'xpressbees_surface_3kg':
                        if ($order_type == 'forward')
                            $this->shipment->_cancelOrderXpressBees($awb, "aSNDKedk3586OIPdSKsIESSK");
                        else
                            $this->shipment->_cancelReverseOrderXpressBees($awb, "aSNDKedk3586OIPdSKsIESSK");
                        break;
                    case 'xpressbees_surface_5kg':
                        if ($order_type == 'forward')
                            $this->shipment->_cancelOrderXpressBees($awb, "fsSEKs3587kdPKDAkdrSNsSJ");
                        else
                            $this->shipment->_cancelReverseOrderXpressBees($awb, "fsSEKs3587kdPKDAkdrSNsSJ");
                        break;
                    case 'xpressbees_surface_10kg':
                        if ($order_type == 'forward')
                            $this->shipment->_cancelOrderXpressBees($awb, "ndkPSKD3588ndKSILSKsoeSd");
                        else
                            $this->shipment->_cancelReverseOrderXpressBees($awb, "ndkPSKD3588ndKSILSKsoeSd");
                        break;
                    case 'shadow_fax':
                        $this->shipment->_cancelOrderShadowFax($awb);
                        break;
                    case 'ecom_express':
                        $ecom = new EcomExpressController();
                        $ecom->_CancelEcomExpressOrder($awb);
                        break;
                    case 'ecom_express_3kg':
                        $ecom = new EcomExpress3kgController();
                        $ecom->_CancelEcomExpressOrder($awb);
                        break;
                    case 'udaan':
                    case 'udaan_1kg':
                    case 'udaan_2kg':
                    case 'udaan_3kg':
                    case 'udaan_10kg':
                        $this->shipment->_cancelOrderUdaan($awb);
                        break;
                    case 'bluedart':
                        $this->shipment->_cancelBlueDartOrder($order);
                        break;
                    case 'shree_maruti':
                        $this->_cancelMarutiOrder($order);
                        break;
                    default:
                        echo "Courier Partner not Cancel this Order";
                }
                $order->status = 'cancelled';
                $order->save();
                if($order->awb_number) {
                    OrderTracking::create([
                        'awb_number' => $order->awb_number,
                        'status_code' => 'CAN',
                        'status' => 'CANCELLED',
                        'status_description' => 'ORDER CANCELLED BY SELLER',
                        'remarks' => 'ORDER CANCELLED BY SELLER THROUGH API',
                        'location' => 'NA',
                        'updated_date' => date('Y-m-d H:i:s')
                    ]);
                }
                $seller = Seller::find($order->seller_id);
                $data = [
                    'seller_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'amount' => $order->shipping_charges,
                    'balance' => $seller->balance + $order->shipping_charges,
                    'type' => 'c',
                    'redeem_type' => 'o',
                    'datetime' => date('Y-m-d H:i:s'),
                    'method' => 'wallet',
                    'description' => 'Cancel Order Charge Reversal'
                ];
                Transactions::create($data);
                Seller::where('id', $order->seller_id)->increment('balance', $data['amount']);
                DB::commit();
            }
        } catch(Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    // Create amazon direct order report
    function createAmazonDirectOrderReport(Request $request) {
        try {
            $startedAt = now();
            $cronName = 'create-amazon-direct-orders-report';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            // Get all amazon direct sellers
            $channels = Channels::where('channel', 'amazon_direct')
                ->whereNull('amazon_report_id')
                ->where('status', 'y');
            // For specific seller id
            if($request->filled('sellerId')) {
                $channels = $channels->where('seller_id', $request->sellerId);
            }
            $channels = $channels->get();
            foreach ($channels as $channel) {
                $amazonDirect = new AmazonDirect();
                $fromDate = empty($channel->last_sync) ? now()->subDays(2) : date('Y-m-d H:i:s',strtotime($channel->last_sync." -5 hours"));
                $unshippedReport = $amazonDirect->createReport($channel->amazon_refresh_token, $fromDate, 'GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_SHIPPING');
                if(empty($unshippedReport)) {
                    // Report not created
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => "Unshipped report not created for seller id: " . $channel->seller_id,
                        'data' => null
                    ]);
                    // Unable to create report for this seller
                    continue;
                }
                $shippingReport = $amazonDirect->createReport($channel->amazon_refresh_token, $fromDate, 'GET_FLAT_FILE_ORDER_REPORT_DATA_SHIPPING');
                if(empty($shippingReport)) {
                    // Report not created
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => "Shipping report not created for seller id: " . $channel->seller_id,
                        'data' => null
                    ]);
                    // Unable to create report for this seller
                    continue;
                }
                // Store amazon report id
                $channel->amazon_report_id = "{$unshippedReport},{$shippingReport}";
                $channel->save();
                // Report not created
                Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                    'title' => "Report created for seller id: " . $channel->seller_id,
                    'data' => $channel->amazon_report_id
                ]);
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            // Logger
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Cron failed for seller id: " . $channel->seller_id,
                'data' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Fetch amazon report
    function fetchAmazonDirectOrderReport(Request $request) {
        try {
            $startedAt = now();
            $cronName = 'fetch-amazon-direct-orders-report-file';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            // Get all amazon direct sellers
            $channels = Channels::where('channel', 'amazon_direct')
                ->whereNotNull('amazon_report_id')
                ->where('status','y');
            // For specific seller id
            if($request->filled('sellerId')) {
                $channels = $channels->where('seller_id', $request->sellerId);
            }
            $channels = $channels->get();
            foreach ($channels as $channel) {
                try{
                    $amazonDirect = new AmazonDirect();
                    $unshippedReport = $amazonDirect->checkReportStatus($channel->amazon_refresh_token, explode(',', $channel->amazon_report_id)[0] ?? '');
                    $shippingReport = $amazonDirect->checkReportStatus($channel->amazon_refresh_token, explode(',', $channel->amazon_report_id)[1] ?? '');
                    if($unshippedReport === false || $shippingReport === false) {
                        // report not processed
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Report not processed for seller id: " . $channel->seller_id,
                            'data' => null
                        ]);

                        // Remove amazon report id
                        $channel->amazon_report_id = null;
                        $channel->save();
                    } else if($unshippedReport !== true && $unshippedReport !== false && !is_null($unshippedReport) && $shippingReport !== true && $shippingReport !== false && !is_null($shippingReport)) {
                        if($channel->id == 3257)
                            dd($unshippedReport, $shippingReport);
                        // Import orders
                        $unshippedReportUrl = $amazonDirect->getReportInformation($channel->amazon_refresh_token, $unshippedReport);
                        $shippingReportUrl = $amazonDirect->getReportInformation($channel->amazon_refresh_token, $shippingReport);
                        // Import
                        $temp = tmpfile();
                        fwrite($temp, addslashes(@file_get_contents($unshippedReportUrl)));
                        $unshippedReportData = Excel::toArray(new AmazonReportFileImport, stream_get_meta_data($temp)['uri'], null, \Maatwebsite\Excel\Excel::CSV);
                        // this removes the file
                        fclose($temp);

                        // Logger
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Unshipped report data for seller id: " . $channel->seller_id,
                            'data' => $unshippedReportData
                        ]);

                        $temp = tmpfile();
                        fwrite($temp, addslashes(@file_get_contents($shippingReportUrl)));
                        $shippingReportData = Excel::toArray(new AmazonReportFileImport, stream_get_meta_data($temp)['uri'], null, \Maatwebsite\Excel\Excel::CSV);
                        // this removes the file
                        fclose($temp);

                        // Logger
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Shipping report data for seller id: " . $channel->seller_id,
                            'data' => $shippingReportData
                        ]);

                        // Compare both report and save data
                        $reportData = $this->generateReportData($unshippedReportData, $shippingReportData);
                        // Save data
                        MyUtility::createAmazonDirectOrderFromReport($reportData, $channel);

                        // Remove amazon report id
                        $channel->amazon_report_id = null;
                        $channel->save();

                        // Report imported
                        Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                            'title' => "Report imported for seller id: " . $channel->seller_id,
                            'data' => $reportData
                        ]);
                    }
                }
                catch(Exception $e){
                    Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
                    // Logger
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => "Cron failed for seller id: " . $channel->seller_id,
                        'data' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()
                    ]);
                    continue;
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $rowInserted, $rowUpdated, $rowDeleted, $startedAt, now());
            // Logger
            Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                'title' => "Cron failed for seller id: " . $channel->seller_id,
                'data' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function generateReportData($unshippedReportData, $shippingReportData) {
        $unshippedReport = collect($unshippedReportData[0] ?? []);
        $shippingReport = collect($shippingReportData[0] ?? []);
        $tmpData = $unshippedReport->map(function($item) use($shippingReport) {
            $product = collect($item)->only([
                'order_id',
                'payment_method',
                'recipient_name',
                'ship_address_1',
                'ship_address_2',
                'ship_country',
                'ship_state',
                'ship_city',
                'ship_postal_code',
                'ship_phone_number',
                'purchase_date',
            ])->all();
            // Get data from shipping report file
            $shippingReport = $shippingReport->firstWhere('order_id', $item['order_id']);
            // Get details from shipping file
            $product['ship_phone_number'] = $shippingReport['ship_phone_number'] ?? $item['buyer_phone_number'];
            return $product;
        })->unique('order_id')->values()->all();
        $reportData = collect();
        foreach($tmpData as $row) {
            $data = $row;
            $products = $unshippedReport->where('order_id', $row['order_id'])->map(function($item) use($row, $shippingReport) {
                $product = collect($item)->only([
                    'order_item_id',
                    'sku',
                    'product_name',
                    'item_price',
                    'shipping_price',
                    'quantity_purchased'
                ])->all();
                // Get data from shipping report file
                $shippingReportData = $shippingReport->where('order_id', $row['order_id'])
                    ->where('order_item_id', $item['order_item_id'])
                    ->first();
                // Get details from shipping file
                $product['item_price'] = $shippingReportData['item_price'] ?? 499;
                $product['shipping_price'] = $shippingReportData['shipping_price'] ?? 0;
                $product['quantity_purchased'] = $item['quantity_purchased'] ?? 1;
                return $product;
            });
            $data['product_sku'] = implode(',', $products->pluck('sku')->all());
            $data['product_name'] = implode(',', $products->pluck('product_name')->all());
            $data['product_qty'] = $products->sum('quantity_purchased');
            $data['invoice_amount'] = $products->reduce(function($carry, $item) {
                return $carry + ($item['item_price'] + $item['shipping_price']);
            }, 0);
            $data['products'] = $products->values()->all();
            $reportData->push($data);
        }
        return $reportData;
    }

    function importAmazonDirectOrder(Request $request) {
        if(!$request->hasFile('file')) {
            return response('please upload file.');
        }

        // For specific seller id
        if(!$request->filled('sellerId')) {
            return response('please enter seller id.');
        }
        // Get all amazon direct sellers
        $channel = Channels::where('channel', 'amazon_direct')
            ->where('status','y')
            ->where('seller_id', $request->sellerId)
            ->first();

        $temp = tmpfile();
        fwrite($temp, addslashes(file_get_contents($request->file->getRealPath())));
        $unshippedReportData = Excel::toArray(new AmazonReportFileImport, stream_get_meta_data($temp)['uri'], null, \Maatwebsite\Excel\Excel::CSV);
        // this removes the file
        fclose($temp);

        $unshippedReport = collect($unshippedReportData[0] ?? []);
        $tmpData = $unshippedReport->map(function($item) {
            $product = collect($item)->only([
                'order_id',
                'payment_method',
                'recipient_name',
                'ship_address_1',
                'ship_address_2',
                'ship_country',
                'ship_state',
                'ship_city',
                'ship_postal_code',
                'ship_phone_number',
                'purchase_date',
            ])->all();
            if(!isset($product['ship_phone_number'])) {
                $product['ship_phone_number'] = $item['buyer_phone_number'] ?? null;
            }
            return $product;
        })->unique('order_id')->values()->all();
        $reportData = collect();
        foreach($tmpData as $row) {
            $data = $row;
            $products = $unshippedReport->where('order_id', $row['order_id'])->map(function($item) {
                $product = collect($item)->only([
                    'order_item_id',
                    'sku',
                    'product_name',
                    'item_price',
                    'shipping_price',
                    'quantity_purchased'
                ])->all();
                if(!isset($product['item_price'])) {
                    $product['item_price'] = 499;
                }
                if(!isset($product['shipping_price'])) {
                    $product['shipping_price'] = 0;
                }
                if(!isset($product['quantity_purchased'])) {
                    $product['quantity_purchased'] = 1;
                }
                return $product;
            });
            $data['product_sku'] = implode(',', $products->pluck('sku')->all());
            $data['product_name'] = implode(',', $products->pluck('product_name')->all());
            $data['product_qty'] = $products->sum('quantity_purchased');
            $data['invoice_amount'] = $products->reduce(function($carry, $item) {
                return $carry + ($item['item_price'] + $item['shipping_price']);
            }, 0);
            $data['products'] = $products->values()->all();
            $reportData->push($data);
        }
        MyUtility::createAmazonDirectOrderFromReport($reportData, $channel);
        return response([
            'status' => true
        ]);
    }

    function uploadBackupFiles()
    {
        try {
            $backupDir = '/backup/weekly';
            $backups = [];
            $thisWeek = date('Y-m-d', strtotime("this week"));
            foreach (array_diff(scandir($backupDir), ['.', '..']) as $file) {
                $path = rtrim($backupDir, '/') . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path) && $file != $thisWeek) {
                    $backups[$path] = $this->getFiles($path);
                }
            }
            dd($backups);

            $archiveDirName = '/archives/backup';
            $this->createArchive($archiveDirName, $backups);
            foreach (array_diff(scandir($archiveDirName), ['.', '..']) as $file) {
                $path = rtrim($archiveDirName, '/').DIRECTORY_SEPARATOR.$file;
                $fileName = basename($file);
                if(!Storage::disk('s3')->exists("archive/backup/{$fileName}") && Storage::disk('s3')->putFileAs("archive/backup", new File($path), $fileName)) {
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    private function getFiles($rootDir, $files = [])
    {
        try {
            foreach (array_diff(scandir($rootDir), ['.', '..']) as $file) {
                $path = rtrim($rootDir, '/') . DIRECTORY_SEPARATOR . $file;
                $files[] = $path;
                if (is_dir($path)) {
                    $files = $this->getFiles($path, $files);
                }
            }
            return $files;
        } catch (Exception $e) {
            return [];
        }
    }

    private function createArchive(string $archiveFileName, array $files)
    {
        try {
            // Create zip file
            if (!empty($files)) {
                foreach ($files as $key => $file) {
                    $zip = new ZipArchive();
                    $baseDir = rtrim($archiveFileName, '/').DIRECTORY_SEPARATOR.basename($key);
                    if ($zip->open($baseDir.'.zip', ZipArchive::CREATE) === TRUE) {
                        // Add files to archive
                        foreach ($file as $path) {
                            // dd(rtrim($archiveFileName, '/').DIRECTORY_SEPARATOR.basename($key));
                            if(is_file($path)) {
                                $zip->addFile($path);
                            }
                            if(is_dir($path)) {
                                $zip->addEmptyDir($path);
                            }
                        }
                        // All files are added, so close the zip file.
                        $zip->close();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function generateLable(Request $request)
    {
        try {
            set_time_limit(4000);
            // $orders = Order::whereNotIn('channel', ['custom', 'api', 'oms_guru', 'easyecom', 'unicommerce'])->whereNotIn('status', ['cancelled', 'delivered', 'lost', 'damaged'])->whereNull('seller_channel_id')->get();
            // $orders = Order::whereNotIn('channel', ['custom', 'api', 'oms_guru', 'easyecom', 'unicommerce'])->whereNull('seller_channel_name')->limit(20000)->get();
            // // dd($orders);
            // foreach($orders as $order) {
            //     $channel = Channels::where('seller_id', $order->seller_id)->where('channel', $order->channel)->first();
            //     if(!empty($channel)) {
            //         // $order->seller_channel_id = $channel->id;
            //         $order->seller_channel_name = $channel->channel_name;
            //         $order->save();
            //     }
            // }
            dd("ok");

            $tmp = DB::select("SELECT awb_number FROM `orders` WHERE seller_id = 485 and courier_partner like 'ekart%' and date(awb_assigned_date) = '2022-08-13' and status in ('manifested', 'shipped') order by id limit ?, ?", [$request->from, $request->to]);
            $awb = [];
            foreach($tmp as $row) {
                $awb[] = $row->awb_number;
            }
            $request->awb_number = implode(',', $awb);
            $request->seller_id = 485;

            // if(!$request->filled('awb_number')) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Please enter awb numbers'
            //     ]);
            // }
            // if(!$request->filled('seller_id')) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Please enter seller id'
            //     ]);
            // }

            $request->awb_number = explode(',', $request->awb_number);
            $data = [];
            $data['config'] = Configuration::first();
            $data['seller'] = Seller::where('id', $request->seller_id)->first();
            $data['basic_info'] = Basic_informations::where('seller_id', $request->seller_id)->first();

            // Get label configuration
            $label = LabelCustomization::where('seller_id', $request->seller_id)->first();
            if ($label == null) {
                $label = new LabelCustomization();
                // Store label configuration
                $label->seller_id = $request->seller_id;
                $label->header_visibility = 'y';
                $label->shipping_address_visibility = 'y';
                $label->header_logo_visibility = 'y';
                $label->shipment_detail_visibility = 'y';
                $label->awb_barcode_visibility = 'y';
                $label->order_detail_visibility = 'y';
                $label->order_barcode_visibility = 'y';
                $label->product_detail_visibility = 'y';
                $label->invoice_value_visibility = 'y';
                $label->tabular_form_enabled = 'n';
                $label->gift_visibility = 'n';
                $label->footer_visibility = 'y';
                $label->all_product_display = 'n';
                $label->save();
            }
            $data['label'] = $label;

            $data['orders'] = Order::whereIn('awb_number', $request->awb_number)->where('status', '!=', 'pending')->with('products')->get();
            if ($data['orders']->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order data not found'
                ]);
            }
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            // For MPS
            $orders = [];
            $ids = [];
            foreach ($data['orders'] as $order) {
                $ids[] = $order->id;
                if ($order->shipment_type == 'mps') {
                    $order->is_parent = 'y';
                    $order->parent_awb = $order->awb_number;
                    $order->parent_gati_package_no = $order->gati_package_no;
                    $orders[] = clone $order;
                    $mps = MPS_AWB_Number::where('order_id', $order->id)->get();
                    foreach ($mps as $row) {
                        $order->awb_number = $row->awb_number;
                        $order->awb_barcode = $row->awb_barcode;
                        $order->gati_ou_code = $row->gati_ou_code;
                        $order->gati_package_no = $row->gati_package_no;
                        $order->is_parent = 'n';
                        $orders[] = clone $order;
                    }
                } else {
                    $orders[] = clone $order;
                }
            }
            $data['orders'] = $orders;
            // Generate manifest
            $this->_generateManifest($ids, $request->seller_id);

            $pdfData = [];
            $pngData = [];
            foreach ($orders as $order) {
                if ($order->courier_partner == 'amazon_swa' || $order->courier_partner == 'amazon_swa_1kg' || $order->courier_partner == 'amazon_swa_3kg' || $order->courier_partner == 'amazon_swa_5kg' || $order->courier_partner == 'amazon_swa_10kg') {
                    $pngData[] = $order;
                } else {
                    $pdfData[] = $order;
                }
            }

            if (!empty($pngData)) {
                $zipFile = "exports/Labels-" . $request->seller_id . ".zip";
                // Delete old zip
                @unlink($zipFile);
                $zip = new \ZipArchive();
                if ($zip->open($zipFile, \ZipArchive::CREATE) !== true) {
                    return false;
                }
                foreach ($pngData as $order) {
                    if (!empty($order->amazon_label)) {
                        $zip->addFile($order->amazon_label, basename($order->amazon_label));
                    }
                }

                if (!empty($pdfData)) {
                    $data['orders'] = $pdfData;
                    $labelFile = "exports/Labels-" . $request->seller_id . ".pdf";
                    $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait')->save($labelFile);
                    $zip->addFile($labelFile, 'Labels.pdf');
                }
                $zip->close();
                $file = "assets/report/{$this->data['job_id']}.zip";
                if (file_exists($zipFile)) {
                    return response()->download($zipFile);
                }
            } else {
                $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
                return $pdf->download();

                return response()->json([
                    'status' => true,
                    'message' => 'Label generated'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Label not generated'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Generate manifest
    function _generateManifest(array $orderIds, $sellerId)
    {
        $seller = Seller::where('id', $sellerId)->first();
        // Get mps order ids
        $tmpOrderId = [];
        foreach ($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->whereIn('status', ['shipped', 'pickup_requested'])->first();
            if ($order == null) {
                continue;
            }
            if ($order->shipment_type == 'mps') {
                $childOrders = Order::where('parent_id', $order->parent_id)
                    ->where('shipment_type', 'mps')
                    ->get();
                foreach ($childOrders as $childOrder) {
                    $tmpOrderId[] = $childOrder->id;
                }
            } else {
                $tmpOrderId[] = $order->id;
            }
        }
        if (empty($tmpOrderId)) {
            return false;
        } else {
            $orderIds = $tmpOrderId;
        }
        $wareHouse = Warehouses::where('seller_id', $sellerId)->where('default', 'y')->first();
        if (empty($wareHouse)) {
            return false;
        }
        $couriers = Order::select('courier_partner')->distinct('courier_partner')->where('seller_id', $sellerId)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
        $allManifest = [];
        foreach ($couriers as $c) {
            $rand = rand(1000, 9999);
            $data = array(
                'seller_id' => $sellerId,
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
            if (count($res = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type', 'web')->where('seller_id', $sellerId)->get()) > 0) {
                $manifestId = $res[0]->id;
            } else {
                $manifestId = Manifest::create($data)->id;
            }
            $totalOrders = 0;
            $orders = Order::where('courier_partner', $c->courier_partner)->where('seller_id', $sellerId)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
            foreach ($orders as $o) {
                $allManifest[] = [
                    'manifest_id' => $manifestId,
                    'order_id' => $o->id
                ];
                // create a order tracking for tracking the next order status
                OrderTracking::create(['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request', 'remark' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s'),'created_at' => date('Y-m-d H:i:s')]);
                $o->status = 'manifested';
                $o->manifest_status = 'y';
                $o->save();
                if ($seller->sms_service == 'y') {
                    $this->utilities->send_sms($o);
                }
                $totalOrders++;
            }
            if (count($res) > 0)
                Manifest::where('id', $manifestId)->increment('number_of_order', $totalOrders);
            else
                Manifest::where('id', $manifestId)->update(array('number_of_order' => $totalOrders));
        }
        ManifestOrder::insert($allManifest);
        return true;
    }

    function queue(Request $request) {
        if(!$request->filled('seller_id')) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Seller id is required.'
            ]);
        }
        if(!$request->filled('awbs')) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'AWB number is required.'
            ]);
        }
        $allAwbs = [];
        foreach ($request->awbs as $awb){
            $allAwbs[]=$awb;
            if(count($allAwbs) == 1000){
                // create job
                $job = DownloadReport::create([
                    'seller_id' => $request->seller_id,
                    'report_name' => 'Download Label',
                    'report_type' => 'Generate Label',
                    'report_status' => 'pending',
                    'payload' => json_encode([
                        'awbs' => $allAwbs,
                        'seller_id' => $request->seller_id,
                    ]),
                ]);
                // Dispatch job
                GenerateLabel::dispatchAfterResponse([
                    'job_id' => $job->id
                ]);
                $allAwbs = [];
            }
        }
        if(count($allAwbs) > 0){
            $job = DownloadReport::create([
                'seller_id' => $request->seller_id,
                'report_name' => 'Download Label',
                'report_type' => 'Generate Label',
                'report_status' => 'pending',
                'payload' => json_encode([
                    'awbs' => $allAwbs,
                    'seller_id' => $request->seller_id,
                ]),
            ]);
            // Dispatch job
            GenerateLabel::dispatchAfterResponse([
                'job_id' => $job->id
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'job_id' => $job->id
            ],
            'message' => 'Your label will be generated soon.'
        ]);
    }
    function cancelOrderService(Request $request){
        $orderData = Order::where('awb_number',$request->awb_number)->first();
        if(empty($orderData))
            return response()->json(['status' => 'false','message' => 'Invalid Awb Number']);
        $sellerData = Seller::find($orderData->seller_id);
        if(empty($sellerData))
            return response()->json(['status' => 'false','message' => 'Seller Not Found']);
        if($orderData->status == 'pending' || $orderData->status == 'delivered')
            return response()->json(['status' => 'false','message' => 'Delivered or Pending orders can not be cancelled']);
        if($sellerData->id == 3488)
            MyUtility::PerformCancellation($sellerData,$orderData,'web',false);
        else
            MyUtility::PerformCancellation($sellerData,$orderData);
        return response()->json(['status' => 'true','message' => 'Order Cancelled Successfully']);
    }

    function reassignOrders(){
        $sellers = Seller::where('auto_reassign_enabled','y')->get();
        if(!empty($sellers)) {
            foreach ($sellers as $s) {
                try {
                    $orders = Order::where('awb_number', "!=", "")->where('manifest_sent','n')->whereDate('awb_assigned_date',">=",date('Y-m-d',strtotime("-2 days")))->whereDate('awb_assigned_date',"<=",date('Y-m-d',strtotime("-1 days")))->whereIn('status', ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled'])->where('seller_id',$s->id)->get();
                    $totalSuccessOrder = 0;
                    $totalFailedOrder = 0;
                    $awbNumbers = [];
                    $reassignOrderDetails = [];
                    $obj = new ReassignHelper();
                    if(!empty($orders)) {
                        foreach ($orders as $o) {
                            try {
                                try {
                                    $response = $obj->ShipOrder($o, $s);
                                    if ($response['status'] == true) {
                                        $obj->singleLabelPDF($response['data']['order_id'], $s);
                                        $awbNumbers[] = ['customer_order_number' => $response['data']['customer_order_number'], 'new_awb_number' => $response['data']['new_awb_number'], 'old_awb_number' => $response['data']['old_awb_number']];
                                        $reassignOrderDetails[] = [
                                            'order_id' => $response['data']['order_id'],
                                            'old_awb_number' => $response['data']['old_awb_number'],
                                            'new_awb_number' => $response['data']['new_awb_number'],
                                            'courier_partner' => $response['data']['courier_keyword'],
                                            'seller_id' => Session()->get('MySeller')->id,
                                            'inserted' => date('Y-m-d H:i:s')
                                        ];
                                        $totalSuccessOrder++;
                                    }
                                } catch (Exception $e) {
                                    Logger::write('logs/reassign-' . date('Y-m-d') . '.text', [
                                        'title' => 'Reassign Helper Logs',
                                        'data' => $e->getMessage()
                                    ]);
                                    $totalFailedOrder++;
                                }
                                $this->_refreshSession();

                            } catch (Exception $e) {
                                Logger::write('logs/reassign-' . date('Y-m-d') . '.text', [
                                    'title' => 'Reassign Helper Logs-1',
                                    'data' => $e->getMessage()
                                ]);
                                $totalFailedOrder++;
                            }
                        }
                    }
                    try {
                        if (count($awbNumbers) > 0) {
                            ReassignOrderDetails::insert($reassignOrderDetails);
                            $obj->labelZip();
                            $obj->labelZipAttachMail($s->email, "Reassign Orders", "Reassign Orders", $awbNumbers);
                            $obj->deleteLabels();
                        }
                    } catch (Exception $e) {
                        Logger::write('logs/cron/reassign-' . date('Y-m-d') . '.text', [
                            'title' => 'Reassign Helper Logs-2',
                            'data' => $e->getMessage()
                        ]);
                    }
                    $totalOrder = $totalSuccessOrder + $totalFailedOrder;
                    if ($totalSuccessOrder > 0) {
                        $output = ['status'=> 'success', 'message' => "Total $totalSuccessOrder of $totalOrder orders re-assigned successfully"];
                    } else {
                        $output = ['status' => 'Error', 'message' => 'Unable to re-assign orders'];
                    }
                    return response()->json($output);
                } catch (Exception $e) {
                    Logger::write('logs/cron/reassign-' . date('Y-m-d') . '.text', [
                        'title' => 'Reassign Helper Logs-3',
                        'data' => $e->getMessage()
                    ]);
                    return response()->json(['status' => 'Error', 'message' => 'Unable to re-assign orders']);
                }
            }
        }
    }

    function invoiceQueue(Request $request) {
        if(!$request->filled('seller_id')) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Seller id is required.'
            ]);
        }
        if(!$request->filled('awbs')) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'AWB number is required.'
            ]);
        }
        $job = DownloadReport::create([
            'seller_id' => $request->seller_id,
            'report_name' => 'Download Invoice',
            'report_type' => 'Generate Invoice',
            'report_status' => 'pending',
            'payload' => json_encode([
                'awbs' => $request->awbs,
                'seller_id' => $request->seller_id,
            ]),
        ]);

//        // Dispatch job
        GenerateInvoice::dispatchAfterResponse([
            'job_id' => $job->id
        ]);
        return response()->json([
            'status' => true,
            'data' => [
                'job_id' => $job->id
            ],
            'message' => 'Your Invoice will be generated soon.'
        ]);
    }

    //for new seller send mail
    function onboardingNewSeller()
    {
        $sellerInfo = Seller::whereDate('created_at',date('Y-m-d',strtotime('-1 days')))->get();
        $Content = "<table border='1'><th>Sr.No</th><th>Seller Name</th><th>Company Name</th><th>Seller Details</th><th>KYC Status</th><th>Status</th></tr>";
        $cnt=1;
        foreach ($sellerInfo as $s)
        {
            $Content .= "<tr>
                <td>{$cnt}</td>
                <td>{$s->first_name}</td>
                <td>{$s->company_name}</td>
                <td>
                    <b>Email :- </b> {$s->email}<br>
                    <b>Contact :- </b> {$s->mobile}
                </td>
                <td style='color: " . ($s->verified == 'y' ? '#28a745' : 'red') . "; font-weight: bold'>" . ($s->verified == 'y' ? 'Verified' : 'Not Verified') . "</td>
                <td style='color: #28a745; font-weight: bold'>" . ($s->status ? 'Active' : 'Not Active') . "</td>
            </tr>";
            $cnt++;
        }
        $Content.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $Content);
        $email = "sales@Twinnship.in";
        $subject = "Twinnship new onboarding ";
        $sellerInfo = null;
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$Content,$subject);
    }

    function compareRateCard(Request $request){
        $columsToCompare = [
            'within_city',
            'within_state',
            'metro_to_metro',
            'rest_india',
            'north_j_k',
            'cod_charge',
            'cod_maintenance',
            'extra_charge_a',
            'extra_charge_b',
            'extra_charge_c',
            'extra_charge_d',
            'extra_charge_e',
        ];
        $rateCardIds = SellerRateChangeDetails::select('seller_rate_change_id','seller_id')->where('inserted','>=',date('Y-m-d 00:00:00',strtotime('-1 day')))->distinct()->get();
        $Content = "<table border='1'>
                        <th>Sr.No</th>
                        <th>Seller</th>
                        <th>Courier Partner</th>
                        <th>Within City</th>
                        <th>Within State</th>
                        <th>Metro to Metro</th>
                        <th>Rest of India</th>
                        <th>North East & J.K</th>
                        <th>COD Charges</th>
                        <th>COD Maintenance(%)</th>
                        <th>Extra Charge Zone - A</th>
                        <th>Extra Charge Zone - B</th>
                        <th>Extra Charge Zone - C</th>
                        <th>Extra Charge Zone - D</th>
                        <th>Extra Charge Zone - E</th>
                        </tr>";
        $cnt=1;
        foreach ($rateCardIds as $rateCardId){
            $oldRateCard = SellerRateChangeDetails::where('seller_rate_change_id',$rateCardId->seller_rate_change_id)->get();
            foreach ($oldRateCard as $oldRate){
                $newRate = Rates::where('seller_id',$oldRate->seller_id)->where('partner_id',$oldRate->partner_id)->where('plan_id',$oldRate->plan_id)->first();
                if(empty($newRate))
                    continue;
                foreach ($columsToCompare as $c){
                    if($oldRate->{$c} != $newRate->{$c}){
                        $Content .= "<tr>
                            <td>{$cnt}</td>
                            <td>
                                {$oldRate->sellers->code}
                            </td>
                            <td>
                                {$oldRate->partners->keyword}
                            </td>";
                        if($oldRate->within_city != $newRate->within_city){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->within_city == $newRate->within_city ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->within_city}</span><br>
                                    <span style='color: " . ($oldRate->within_city == $newRate->within_city ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->within_city}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->within_city}</span><br>
                                    <span><b>NEW :- </b> {$newRate->within_city}</span>
                                </td>";
                        }
                        if($oldRate->within_state != $newRate->within_state){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->within_state == $newRate->within_state ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->within_state}</span><br>
                                    <span style='color: " . ($oldRate->within_state == $newRate->within_state ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->within_state}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->within_state}</span><br>
                                    <span><b>NEW :- </b> {$newRate->within_state}</span>
                                </td>";
                        }
                        if($oldRate->metro_to_metro != $newRate->metro_to_metro){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->metro_to_metro == $newRate->metro_to_metro ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->metro_to_metro}</span><br>
                                    <span style='color: " . ($oldRate->metro_to_metro == $newRate->metro_to_metro ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->metro_to_metro}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->metro_to_metro}</span><br>
                                    <span><b>NEW :- </b> {$newRate->metro_to_metro}</span>
                                </td>";
                        }
                        if($oldRate->rest_india != $newRate->rest_india){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->rest_india == $newRate->rest_india ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->rest_india}</span><br>
                                    <span style='color: " . ($oldRate->rest_india == $newRate->rest_india ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->rest_india}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->rest_india}</span><br>
                                    <span><b>NEW :- </b> {$newRate->rest_india}</span>
                                </td>";
                        }
                        if($oldRate->north_j_k != $newRate->north_j_k){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->north_j_k == $newRate->north_j_k ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->north_j_k}</span><br>
                                    <span style='color: " . ($oldRate->north_j_k == $newRate->north_j_k ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->north_j_k}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->north_j_k}</span><br>
                                    <span><b>NEW :- </b> {$newRate->north_j_k}</span>
                                </td>";
                        }
                        if($oldRate->cod_charge != $newRate->cod_charge){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->cod_charge == $newRate->cod_charge ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->cod_charge}</span><br>
                                    <span style='color: " . ($oldRate->cod_charge == $newRate->cod_charge ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->cod_charge}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->cod_charge}</span><br>
                                    <span><b>NEW :- </b> {$newRate->cod_charge}</span>
                                </td>";
                        }
                        if($oldRate->cod_maintenance != $newRate->cod_maintenance){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->cod_maintenance == $newRate->cod_maintenance ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->cod_maintenance}</span><br>
                                    <span style='color: " . ($oldRate->cod_maintenance == $newRate->cod_maintenance ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->cod_maintenance}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->cod_maintenance}</span><br>
                                    <span><b>NEW :- </b> {$newRate->cod_maintenance}</span>
                                </td>";
                        }
                        if($oldRate->extra_charge_a != $newRate->extra_charge_a){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->extra_charge_a == $newRate->extra_charge_a ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->extra_charge_a}</span><br>
                                    <span style='color: " . ($oldRate->extra_charge_a == $newRate->extra_charge_a ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->extra_charge_a}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->extra_charge_a}</span><br>
                                    <span><b>NEW :- </b> {$newRate->extra_charge_a}</span>
                                </td>";
                        }
                        if($oldRate->extra_charge_b != $newRate->extra_charge_b){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->extra_charge_b == $newRate->extra_charge_b ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->extra_charge_b}</span><br>
                                    <span style='color: " . ($oldRate->extra_charge_b == $newRate->extra_charge_b ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->extra_charge_b}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->extra_charge_b}</span><br>
                                    <span><b>NEW :- </b> {$newRate->extra_charge_b}</span>
                                </td>";
                        }
                        if($oldRate->extra_charge_c != $newRate->extra_charge_c){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->extra_charge_c == $newRate->extra_charge_c ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->extra_charge_c}</span><br>
                                    <span style='color: " . ($oldRate->extra_charge_c == $newRate->extra_charge_c ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->extra_charge_c}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->extra_charge_c}</span><br>
                                    <span><b>NEW :- </b> {$newRate->extra_charge_c}</span>
                                </td>";
                        }
                        if($oldRate->extra_charge_d != $newRate->extra_charge_d){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->extra_charge_d == $newRate->extra_charge_d ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->extra_charge_d}</span><br>
                                    <span style='color: " . ($oldRate->extra_charge_d == $newRate->extra_charge_d ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->extra_charge_d}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->extra_charge_d}</span><br>
                                    <span><b>NEW :- </b> {$newRate->extra_charge_d}</span>
                                </td>";
                        }
                        if($oldRate->extra_charge_e != $newRate->extra_charge_e){
                            $Content.="<td>
                                    <span style='color: " . ($oldRate->extra_charge_e == $newRate->extra_charge_e ? 'green' : 'red') . ";'><b>OLD :- </b> {$oldRate->extra_charge_e}</span><br>
                                    <span style='color: " . ($oldRate->extra_charge_e == $newRate->extra_charge_e ? 'red' : 'green') . ";'><b>NEW :- </b> {$newRate->extra_charge_e}</span>
                                </td>";
                        }
                        else{
                            $Content.="<td>
                                    <span><b>OLD :- </b> {$oldRate->extra_charge_e}</span><br>
                                    <span><b>NEW :- </b> {$newRate->extra_charge_e}</span>
                                </td>";
                        }";
                        </tr>";
                        $cnt++;
                    }
                }
            }
        }
        $Content.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $Content);
        $email = "Leadership@Twinnship.in";
        $subject = "Seller Rate Change Details ";
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$Content,$subject);
        echo $Content;
    }

    //for seller configuration tracking
    function sellerConfigurationTrackingSendMail()
    {
        $data['sellerInfo'] = SellerInfoLogs::whereDate('created_at',date('Y-m-d',strtotime('-1 days')))->get();
        $Content = view('admin.selle-configuration-change',$data);
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $Content);
        $email = "Leadership@Twinnship.in";
        $subject = "Twinnship Configuration Tracking ";
        echo $Content;
        //$this->utilities->send_email($email,"Twinnship Corporation",$subject,$Content,$subject);
    }

    function pickupConnectionDateMissing(){
        try {
            $data = DB::select("select o.id,o.awb_number,o.awb_assigned_date,o.courier_partner,o.status,o.rto_status,o.pickup_time,io.datetime from orders o left join intransit_orders_list io on io.order_id = o.id where io.datetime is null and o.awb_assigned_date >= '2023-11-25 00:00:00' and o.status not in('cancelled','manifested','pickup_requested','pickup_scheduled','shipped','pending') and (o.pickup_time is null and io.datetime is null)");
            $count = count($data);
            $message = "<h5>Total Count $count</h5><br><table border='1'><th>AWB Number</th><th>AWB Assigned Date</th><th>Courier Partner</th><th>Status</th><th>Pickup Time</th><th>Connection Time</th></tr>";
            foreach ($data as $d) {
                $message .= "<tr>
            <td>" . $d->awb_number . "</td>
            <td>" . $d->awb_assigned_date . "</td>
            <td>" . $d->courier_partner . "</td>
            <td>" . $d->status . "</td>
            <td>" . $d->pickup_time . "</td>
            <td>" . $d->datetime . "</td>
        </tr>";
            }
            $message .= "</table>";
            $obj = new Utilities();
            $obj->send_email("tech@Twinnship.in", "Twinnship Corporation", "Missing Pickup and Connection Date", $message, 'Missing Pickup and Connection Date');
        }catch (Exception $e){

        }
        echo "success";
    }

    function zeroCharges(){
        try {
            //$data = Order::where('shipping_charges', 0)->orWhereNull('shipping_charges')->whereNotNull('awb_number')->get();
            //$data = DB::select("select o.id,o.awb_number,o.awb_assigned_date,o.courier_partner,o.status,o.rto_status,o.pickup_time,io.datetime from orders o left join intransit_orders_list io on io.order_id = o.id where io.datetime is null and o.awb_assigned_date >= '2023-11-25 00:00:00' and o.status not in('cancelled','manifested','pickup_requested','pickup_scheduled','shipped','pending') and (o.pickup_time is null and io.datetime is null)");
            $data = DB::select("select orders.id,orders.seller_id,orders.awb_number,orders.awb_assigned_date,transactions.amount,transactions.description,orders.status from orders join transactions on transactions.order_id = orders.id and transactions.type = 'd' and transactions.description = 'Order Shipping Charge Deducted' where (orders.shipping_charges = 0) and orders.awb_number is not null and transactions.amount = 0 order by orders.seller_id");
            $count = count($data);
            $message = "<h5>Total Count $count</h5><br><table border='1'><th>Seller ID</th><th>AWB Number</th><th>AWB Assigned Date</th><th>Courier Partner</th><th>Status</th><th>Shipping Charges</th></tr>";
            foreach ($data as $d) {
                $message .= "<tr>
            <td>" . $d->seller_id . "</td>
            <td>" . $d->awb_number . "</td>
            <td>" . $d->awb_assigned_date . "</td>
            <td>" . $d->courier_partner . "</td>
            <td>" . $d->status . "</td>
            <td>" . $d->amount . "</td>
        </tr>";
            }
            $message .= "</table>";
            $obj = new Utilities();
            $obj->send_email("tech@Twinnship.in", "Twinnship Corporation", "Zero Charges", $message, 'Zero Charges');
        }catch(Exception $e){

        }
        echo "success";
    }

    function firstOfdDateMissing(){
        try {
            $data = DB::select("select o.id,o.awb_number,o.awb_assigned_date,o.courier_partner,o.status,o.rto_status,io.ofd_date,o.delivered_date from orders o join international_orders io on io.order_id = o.id where (o.status in('ndr','delivered','out_for_delivery') or rto_status = 'y') and io.ofd_date is null and o.delivered_date is null and o.awb_assigned_date >= '2023-11-25 00:00:00'");
            $count = count($data);
            $message = "<h5>Total Count $count</h5><br><table border='1'><th>AWB Number</th><th>AWB Assigned Date</th><th>Courier Partner</th><th>Status</th><th>RTO Status</th><th>OFD Date</th></tr>";
            foreach ($data as $d){
                $message.="<tr>
                <td>".$d->awb_number."</td>
                <td>".$d->awb_assigned_date."</td>
                <td>".$d->courier_partner."</td>
                <td>".$d->status."</td>
                <td>".$d->rto_status."</td>
                <td>".$d->ofd_date."</td>
            </tr>";
            }
            $message.="</table>";
            $obj = new Utilities();
            $obj->send_email("tech@Twinnship.in", "Twinnship Corporation", "Missing First OFD", $message, 'Missing First OFD');
        }catch(Exception $e){

        }
        echo "success";
    }

    function rtoInitiatedDateMissing(){
        try {
            $data = DB::select("select o.id,o.awb_number,o.awb_assigned_date,o.courier_partner,o.status,o.rto_status,io.ofd_date,o.delivered_date from orders o join international_orders io on io.order_id = o.id where (o.status in('ndr','delivered','out_for_delivery') or rto_status = 'y') and io.ofd_date is null and o.delivered_date is null and o.awb_assigned_date >= '2023-11-25 00:00:00'");
            $count = count($data);
            $message = "<h5>Total Count $count</h5><br><table border='1'><th>AWB Number</th><th>AWB Assigned Date</th><th>Courier Partner</th><th>Status</th><th>RTO Status</th><th>OFD Date</th></tr>";
            foreach ($data as $d){
                $message.="<tr>
                <td>".$d->awb_number."</td>
                <td>".$d->awb_assigned_date."</td>
                <td>".$d->courier_partner."</td>
                <td>".$d->status."</td>
                <td>".$d->rto_status."</td>
                <td>".$d->ofd_date."</td>
            </tr>";
            }
            $message.="</table>";
            $obj = new Utilities();
            $obj->send_email("tech@Twinnship.in", "Twinnship Corporation", "Missing First OFD", $message, 'Missing First OFD');
        }catch(Exception $e){

        }
        echo "success";
    }

    function rtoDeliveredDateMissing(){
        try {
            $data = DB::select("select id,awb_number,awb_assigned_date,courier_partner,status,rto_status,delivered_date from orders where rto_status = 'y' and status = 'delivered' and awb_assigned_date >= '2023-11-25 00:00:00' and delivered_date is null");
            $count = count($data);
            $message = "<h5>Total Count $count</h5><br><table border='1'><th>AWB Number</th><th>AWB Assigned Date</th><th>Courier Partner</th><th>Status</th><th>RTO Status</th><th>Delivered Date</th></tr>";
            foreach ($data as $d){
                $message.="<tr>
                <td>".$d->awb_number."</td>
                <td>".$d->awb_assigned_date."</td>
                <td>".$d->courier_partner."</td>
                <td>".$d->status."</td>
                <td>".$d->rto_status."</td>
                <td>".$d->delivered_date."</td>
            </tr>";
            }
            $message.="</table>";
            echo $message;
            $obj = new Utilities();
            $obj->send_email("tech@Twinnship.in", "Twinnship Corporation", "Missing RTO Delivered Date", $message, 'Missing RTO Delivered Date');
        }catch (Exception $e){
            echo "success";
        }
        echo "success";
    }

    function sellerInvalidContactSendMail()
    {
        $contactInfo = InvalidContact::whereDate('date',date('Y-m-d',strtotime('-0 days')))->get();
        $Content = "<table border='2'><th>Sr.No</th><th>AWB Number</th><th>Seller</th><th>Contact</th><th>Date</th></tr>";
        $cnt=1;
        foreach ($contactInfo as $s)
        {
            $Content .= "<tr>
                <td>{$cnt}</td>
                <td>{$s->awb_number}</td>
                <td>{$s->seller_id}</td>
                <td>{$s->contact}</td>
                <td>{$s->date}</td>
            </tr>";
            $cnt++;
        }
        $Content.="</table>";
        $data = array('name' => 'Twinnship Corporation', 'mailContent' => $Content);
        $email = "Tech@Twinnship.in";
        $subject = "Invalid Contact For Order ";
        echo $Content;
        $this->utilities->send_email($email,"Twinnship Corporation",$subject,$Content,$subject);
    }
    function SendReAssignMailForSeller(Request $request){
        try{
            $config = Configuration::find(1);
            if($config->send_reassignment_email == 0)
                return response()->json(['status' => true,'message' => 'Email is disabled']);
            $startDateTime = date('Y-m-d H:i:s',strtotime('-1 hour'));
            $allSellerIds = DB::select("select seller_id,count(seller_id) as total_count from orders where manifest_sent = 'n' and status not in('pending','cancelled') and is_retry = 1 and awb_assigned_date >= '{$startDateTime}' and seller_id not in(1) and is_custom = 0 group by seller_id having total_count > 0");
            $allSellers = [];
            $objSubject = "Re-Assignment for the Hour";
            $OpsMailContent = "<b>Please Reassign Mentioned AWBs Below: </b> <br><table border='1'><tr><td>Sr.No</td><td>Seller Id</td><td>AWB Number</td><td>Status</td><td>Reason</td></tr>";
            foreach ($allSellerIds as $data){
                $mailAwb = [];
                $sellerMailContent = "<b>Please Reassign Mentioned AWBs Below: </b> <br><table border='1'><tr><td>Sr.No</td><td>AWB Number</td><td>Status</td><td>Reason</td></tr>";
                $sellerData = Seller::find($data->seller_id);
                $orderData = DB::select("select o.id,o.awb_number,o.seller_id,o.status,o.awb_number,mi.message from orders o left join manifestation_issues mi on mi.order_id = o.id where o.seller_id = {$data->seller_id} and o.manifest_sent = 'n' and o.status not in('pending','cancelled') and o.is_retry = 1 and o.awb_assigned_date >= '{$startDateTime}' and o.is_custom = 0");
                $opsCnt = 1;
                foreach ($orderData as $s){
                    $sellerMailContent.="<tr><td>{$opsCnt}</td><td>{$s->awb_number}</td><td>{$s->status}</td><td>{$s->message}</td></tr>";
                    $OpsMailContent.="<tr><td>{$opsCnt}</td><td>{$s->seller_id}</td><td>{$s->awb_number}</td><td>{$s->status}</td><td>{$s->message}</td></tr>";
                    $mailAwb[]=$s->awb_number;
                    $opsCnt++;
                }
                Seller::find($sellerData->id)->notify(new \App\Notifications\ReAssignNotification($mailAwb));
                $subject = "Seller ID : {$sellerData->code}";
                $allSellers[] = [
                    'seller_id' => $sellerData->id,
                    'awbs' => $mailAwb
                ];
                $sellerMailContent.="</table>";
                if($config->send_reassignment_email == 0)
                    $this->utilities->send_email($sellerData->email,"Twinnship Corporation",$subject,$sellerMailContent,$subject);
            }
            $OpsMailContent.="</table>";
            if($config->send_reassignment_email == 0)
                $this->utilities->send_email("ops@Twinnship.in","Twinnship Corporation",$subject,$OpsMailContent,$objSubject);
            return response()->json($allSellers);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]);
        }

    }
    function SendReAssignMailForSellerDaily(Request $request){
        $config = Configuration::find(1);
        try{
            $startDateTime = $request->start ?? date('Y-m-d 00:00:00',strtotime('-1 day'));
            $endDateTime = $request->end ?? date('Y-m-d 23:59:59',strtotime('-1 day'));
            $allOrders = DB::select("select o.id,o.seller_id,o.awb_number,o.status,mi.message,o.p_pincode,o.s_pincode,o.awb_assigned_date,o.o_type,o.courier_partner from orders o left join manifestation_issues mi on o.id = mi.order_id where o.manifest_sent = 'n' and o.status not in('pending','cancelled') and o.is_retry = 1 and o.awb_assigned_date >= '{$startDateTime}' and o.awb_assigned_date <= '{$endDateTime}' and o.seller_id not in(1) and o.is_custom = 0 order by o.seller_id");
            $OpsMailContent = "<b>Please Reassign Mentioned AWBs Below: </b> <br><table border='1'><tr><td>Sr.No</td><td>Seller Id</td><td>AWB Number</td><td>Courier Partner</td><td>Status</td><td>Reason</td><td>P Pincode</td><td>S Pincode</td><td>AWB Assigned Date</td></tr>";
            $opsSubject = "Reassignment Automated Mail for : ".date('d M Y',strtotime($startDateTime));
            $opsCnt = 1;
            foreach ($allOrders as $data){
                $OpsMailContent.="<tr><td>{$opsCnt}</td><td>{$data->seller_id}</td><td>{$data->awb_number}</td><td>{$data->courier_partner}</td><td>{$data->status}</td><td>{$data->message}</td><td>{$data->p_pincode}</td><td>{$data->s_pincode}</td><td>{$data->awb_assigned_date}</td></tr>";
                $opsCnt++;
            }
            $OpsMailContent.="</table>";
            if($config->send_reassignment_email == 0)
                $this->utilities->send_email("ravishankar.maurya@Twinnship.in","Twinnship Corporation",$opsSubject,$OpsMailContent,$opsSubject);
        }catch(Exception $e){
            dd($e->getMessage()."-".$e->getFile()."-".$e->getLine());
        }
        return response()->json($allOrders);
    }

    function populateBluedartPickupDate(){
        $orders = Order::select('id')->whereNull('pickup_time')->whereIn('status',['picked_up','in_transit','ndr','out_for_delivery','delivered'])->whereIn('courier_partner',['bluedart','bluedart_surface'])->where('awb_assigned_date','>=','2023-11-01 00:00:00')->get();//;where('awb_number',$awb)->first();
        foreach ($orders as $o){
            $order = Order::find($o->id);
            if($order->courier_partner == 'bluedart_surface')
                $blueDart = new BlueDart('NSE');
            else{
                if($order->is_alpha == 'NSE')
                    $blueDart = new BlueDart('NSE');
                else
                    $blueDart = new BlueDart('SE');
            }
            if(!empty($order)) {
                $res = $blueDart->trackOrder([
                    'awb' => $order->awb_number,
                    'numbers' => $order->awb_number
                ]);
                $trackingData = (array)@$res->Shipment ?? [];
                $scans = (array)@$trackingData['Scans'] ?? [];
                $scanDetail = (array)@$scans['ScanDetail'] ?? [];
                if(!empty($scanDetail)) {
                    $newArray = array_reverse($scanDetail);
                    foreach ($newArray as $t) {
                        $code = $t->ScanCode."-".$t->ScanGroupType;
                        if ($code = '015-S') {
                            Order::where('id', $order->id)->update(['pickup_time' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
                            break;
                        }
                    }
                }
            }
        }
    }

    function populateBDAllDates(Request $request){
        $awb = explode(",",$request->awb_number) ?? [];
        if(!empty($awb)){
            $orderData = Order::select('id','awb_number','is_alpha','courier_partner')->whereIn('awb_number',$awb)->get();
            foreach ($orderData as $order) {
                try {
                    if ($order->courier_partner == 'bluedart_surface')
                        $blueDart = new BluedartRest('NSE');
                    else {
                        if ($order->is_alpha == 'NSE')
                            $blueDart = new BluedartRest('NSE');
                        else
                            $blueDart = new BluedartRest('SE');
                    }
                    if (!empty($order)) {
                        $res = $blueDart->trackOrder([
                            'awb' => $order->awb_number,
                            'numbers' => $order->awb_number
                        ]);
                        $trackingData = (array)@$res->Shipment ?? [];
                        $scans = (array)@$trackingData['Scans'] ?? [];
                        $scanDetail = (array)@$scans['ScanDetail'] ?? [];
                        if (!empty($scanDetail)) {
                            $newArray = array_reverse($scanDetail);
                            foreach ($newArray as $t) {
                                if ($t->ScanCode == '015' && $t->ScanGroupType == 'S') {
                                    Order::where('id', $order->id)->update(['pickup_time' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
                                    break;
                                }
                            }
                            foreach ($newArray as $t) {
                                if ($t->ScanCode == '001' && $t->ScanGroupType == 'S') {
                                    \App\Models\MoveToIntransit::where('order_id', $order->id)->update(['datetime' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
                                    break;
                                }
                            }
                            foreach ($newArray as $t) {
                                if ($t->ScanCode == '104' && $t->ScanGroupType == 'T') {
                                    InternationalOrders::where('order_id', $order->id)->update(['rto_initiated_date' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
                                    break;
                                }
                            }
                            foreach ($newArray as $t) {
                                if ($t->ScanCode == '019' && $t->ScanGroupType == 'S') {
                                    InternationalOrders::where('order_id', $order->id)->update(['ofd_date' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
                                    break;
                                }
                            }
//                foreach ($newArray as $t) {
//                    if ($t->ScanCode == '188' && $t->ScanGroupType == 'T') {
//                        Order::where('id', $order->id)->update(['delivered_date' => date('Y-m-d H:i:s', strtotime($t->ScanDate . " " . $t->ScanTime))]);
//                        break;
//                    }
//                }
                        }
                    }
                }catch (Exception $e){
                    echo $order->awb_number." ".$e->getMessage()." ".$e->getFile()."<br>";
                    continue;
                }
            }
        }
    }
    function fetchAllServiceablePincodeShadowFax(Request $request){
        $shadowFax = new Shadowfax();
        $allPincodes = $shadowFax->getAllServiceablePincodes();
        $serviceableArray = [];
        $counter = 0;
        if(count($allPincodes) > 0){
            ServiceablePincode::where('courier_partner', 'shadowfax')->delete();
            foreach ($allPincodes as $pincode){
                $counter++;
                $serviceableArray[]= [
                    'partner_id' => 11,
                    'courier_partner' => 'shadowfax',
                    'pincode' => $pincode['code'],
                    'active' => 'y',
                    'is_cod' => 'y',
                    'status' => 'y'
                ];
                if(count($serviceableArray) == 500){
                    ServiceablePincode::insert($serviceableArray);
                    $serviceableArray = [];
                }
            }
            ServiceablePincode::insert($serviceableArray);
        }
        return response()->json(['status' => true, 'message' => "{$counter} pincodes imported successfully"]);
    }
    function fetchAllServiceablePincodeFMShadowFax(Request $request){
        $shadowFax = new Shadowfax();
        $allPincodes = $shadowFax->getAllServiceablePincodesFM();
        $serviceableArray = [];
        $counter = 0;
        if(count($allPincodes) > 0){
            ServiceablePincodeFM::where('courier_partner', 'shadowfax')->delete();
            foreach ($allPincodes as $pincode){
                $counter++;
                $serviceableArray[]= [
                    'partner_id' => 11,
                    'courier_partner' => 'shadowfax',
                    'pincode' => $pincode['code'],
                    'status' => 'y'
                ];
                if(count($serviceableArray) == 500){
                    ServiceablePincodeFM::insert($serviceableArray);
                    $serviceableArray = [];
                }
            }
            ServiceablePincodeFM::insert($serviceableArray);
        }
        return response()->json(['status' => true, 'message' => "{$counter} pincodes imported successfully"]);
    }
    function fetchAllServiceablePincodeDelhivery(Request $request){
        $delhiveryClient = new Delhivery('surface');
        $allPincodes = $delhiveryClient->GetServiceablePincode();
        $serviceableArray = [];
        $counter = 0;
        if(count($allPincodes) > 0){
            ServiceablePincode::where('courier_partner', 'delhivery_surface')->delete();
            foreach ($allPincodes['delivery_codes'] as $p){
                $counter++;
                $pincodes[] = [
                    'partner_id' => 1,
                    'courier_partner' => 'delhivery_surface',
                    'pincode' => $p['postal_code']['pin'],
                    'status' => 'y',
                    'active' => strtolower($p['postal_code']['pre_paid']),
                    'is_cod' => strtolower($p['postal_code']['cash']),
                    'inserted' => date('Y-m-d H:i:s')
                ];
                if(count($pincodes)==700)
                {
                    ServiceablePincode::insert($pincodes);
                    $pincodes=[];
                }
            }
            ServiceablePincode::insert($pincodes);
        }
        return response()->json(['status' => true, 'message' => "{$counter} pincode imported successfully"]);
    }
}
