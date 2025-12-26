<?php

namespace App\Http\Controllers;

use App\Helpers\InvoiceHelper;
use App\Imports\ZoneImports;
use App\Libraries\AmazonDirect;
use App\Libraries\AmazonSWA;
use App\Libraries\BucketHelper;
use App\Libraries\Ekart;
use App\Libraries\Smartr;
use App\Libraries\Gati;
use App\Libraries\Maruti;
use App\Libraries\MarutiEcom;
use App\Libraries\Bombax;
use App\Libraries\BlueDart;
use App\Models\Admin;
use App\Models\Admin_rights;
use App\Models\ArchivedJobLogs;
use App\Models\BillReceipt;
use App\Models\BluedartAwbNumbers;
use App\Models\BluedartNSEAwbNumbers;
use App\Models\Configuration;
use App\Models\DownloadOrderReportModel;
use App\Models\EarlyCod;
use App\Models\Generated_awb;
use App\Models\Master;
use App\Models\MovinAWBNumbers;
use App\Models\Order;
use App\Models\OrderArchive;
use App\Models\OrderSMSLogs;
use App\Models\OrderWhatsAppMessageLogs;
use App\Models\Preferences;
use App\Models\Rates;
use App\Models\RatesCardRequest;
use App\Models\RateCardRequestData;
use App\Models\Recharge_request;
use App\Models\Seller;
use App\Models\Admin_employee;
use App\Models\Basic_informations;
use App\Models\COD_transactions;
use App\Models\SellerCODRemittance;
use App\Models\SellerCODRemittanceLog;
use App\Models\SellerRateChangeDetails;
use App\Models\SellerRateChanges;
use App\Models\SKU;
use App\Models\SmartrAwbs;
use App\Models\GatiAwbs;
use App\Models\Transactions;
use App\Models\WeightReconciliation;
use App\Models\SupportTicket;
use App\Models\TicketComments;
use App\Models\CommentsAttachment;
use App\Models\Invoice;
use App\Models\Partners;
use App\Models\Product;
use App\Models\ReceiptDetail;
use App\Models\RemittanceDetails;
use App\Models\ServiceablePincode;
use App\Models\ServiceablePincodeFM;
use App\Models\Courier_blocking;
use App\Models\WeightReconciliationHistory;
use App\Models\WeightReconciliationImage;
use App\Models\XbeesAwbnumber;
use App\Models\EkartAwbNumbers;
use App\Models\XbeesAwbnumberUnique;
use App\Models\ZoneMapping;
use App\Models\ZZArchiveOrder;
use App\Notifications\DisputeNotification;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Exception;
use App\Libraries\FileUploadJob;
use App\Libraries\MyUtility;
use App\Models\Channels;
use App\Models\CourierCODRemittance;
use App\Models\CourierCODRemittanceLog;
use App\Models\FileUploadJobModel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public $orderStatus, $utilities;

    function __construct()
    {
        $this->utilities = new Utilities();
        $this->orderStatus = [
            "pending" => "Pending",
            "shipped" => "Shipped",
            "pickup_requested" => "Pickup Requested",
            "manifested" => "Manifested",
            "pickup_scheduled" => "Pickup Scheduled",
            "picked_up" => "Picked Up",
            "cancelled" => "Cancelled",
            "in_transit" => "In Transit",
            "out_for_delivery" => "Out for Delivery",
            "rto_initated" => "RTO Initiated",
            "rto_initiated" => "RTO Initiated",
            "rto_delivered" => "RTO Delivered",
            "rto_in_transit" => "RTO In Transit",
            "delivered" => "Delivered",
            "ndr" => "NDR",
            "lost" => "Lost",
            "damaged" => "Damaged",
            "hold" => "Hold",
            "rto_out_for_delivery" => "RTO Out For Deliverey"
        ];
    }
    function index()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['admin'] = Admin::all();
        return view('admin.admin', $data);
    }

    function sellerKeys()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::where('status','y')->where('verified','y')->paginate(15);
        return view('admin.generatedKeys', $data);
    }

    function insert(Request $request)
    {
        $data = array(
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => $request->password,
            'type' => $request->type,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
        }
        Admin::create($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Admin added successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function delete($id)
    {
        Admin::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function modify($id)
    {
        $response = Admin::find($id);
        echo json_encode($response);
    }

    function status(Request $request)
    {
        $data = array(
            'status' => $request->status
        );
        Admin::where('id', $request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }

    function update(Request $request)
    {
        $data = array(
            'id' => $request->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => $request->password,
            'type' => $request->type,
            'modified' => date('Y-m-d H:i:s')
        );

        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
        }
        Admin::where('id', $request->id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Admin updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function get_admin_rights($admin)
    {
        $modify = Admin::find($admin);
        $rights = DB::select("select * from admin_rights where admin_id=$admin");
        $all = array();
        foreach ($rights as $r)
            $all[] = $r->master_id . "_" . $r->ins . "_" . $r->del . "_" . $r->modi;
        $modify['rights'] = implode('^', $all);
        echo json_encode($modify);
    }

    function save_rights(Request $request)
    {
        $admin = $request->admin;
        $rights = $request->rights;
        DB::select("delete from admin_rights where admin_id=$admin");
        foreach ($rights as $r) {
            $info = explode('_', $r);
            $ins = array(
                'admin_id' => $admin,
                'master_id' => $info[0],
                'ins' => $info[1],
                'del' => $info[2],
                'modi' => $info[3]
            );
            Admin_rights::create($ins);
        }
        echo 'yes';
    }

    public static function defaultConfiguration()
    {
        return (object) [
            'title' => 'Default Title',
            'email' => 'default@example.com',
            'mobile' => '0000000000',
            'address' => 'Default Address',
            'meta_keyword' => 'Default Keywords',
            'meta_description' => 'Default Meta Description',
            'logo' => 'default_logo.png',
            'favicon' => 'default_favicon.ico',
            'copyright' => 'Default © 2024',
            'analytics_code' => '',
            'login_message' => 'Default Login Message',
            'register_message' => 'Default Register Message',
            'forget_message' => 'Default Forget Message',
            'logistic_partner' => 'Default Logistic Partner',
            'channel_partner' => 'Default Channel Partner',
            'brands' => 'Default Brands',
            'press_coverage' => 'Default Press Coverage',
            'testimonial_image' => 'default_testimonial.png',
            'account_details' => 'Default Account Details',
            'about' => 'Default About Text',
            'working_hour' => '9 AM - 5 PM',
            'agreement' => 'Default Agreement Text',
            'stats_title' => 'Default Stats Title',
            'associates_title' => 'Default Associates Title',
            'steps_title' => 'Default Steps Title',
            'signup_title' => 'Default Signup Title',
            'ease_title' => 'Default Ease Title',
            'logistics_title' => 'Default Logistics Title',
            'brand_title' => 'Default Brand Title',
            'press_title' => 'Default Press Title',
            'channel_title' => 'Default Channel Title',
            'subscribe_title' => 'Default Subscribe Title',
            'e_cod_title' => 'Default E-COD Title',
            'e_cod_features' => 'Default E-COD Features',
            'reconciliation_days' => 7,
            'rto_charge' => 0,
            'reverse_charge' => 0,
            'account_holder' => 'Default Account Holder',
            'account_number' => '0000000000',
            'ifsc_code' => 'DEFAULT000',
            'bank_name' => 'Default Bank',
            'bank_branch' => 'Default Branch',
            'gstin' => '00AAAAA0000A1Z5',
            'cin_number' => 'U12345AA1234ABC1234',
            'irn_number' => 'Default IRN Number',
            'pan_number' => 'AAAAA0000A',
            'signature_image' => 'default_signature.png',
            'gst_percent' => 18,
            'invoice_generate_days' => 30,
            'hsn_number' => '0000',
            'sac_number' => '0000',
            'razorpay_key' => 'rzp_test_default',
            'razorpay_secret' => 'default_secret',
            'payment_qrcode' => 'default_qrcode.png',
            'ekart_awb' => '000000',
            'last_report_date' => '2024-01-01',
            'qc_charges' => 0,
            'bulkship_limit' => 100,
            'mis_download_limit' => 50,
            'send_reassignment_email' => false,
            'minimum_balance' => 0,
        ];
    }

    function configuration()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['admin'] = Admin::all();
        $data['config'] = Configuration::find(1) ?? $this->defaultConfiguration();
        return view('admin.configuration', $data);
    }

    function save_configuration(Request $request)
    {
        // dd($request->all());
        $data = array(
            'title' => $request->title,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'meta_keyword' => $request->meta_keyword,
            'analytics_code' => $request->analytics_code,
            'minimum_balance' => $request->minimum_balance,
            'login_message' => $request->login_message,
            'register_message' => $request->register_message,
            'forget_message' => $request->forget_message,
            'meta_description' => $request->meta_description,
            'working_hour' => $request->working_hour,
            'logistic_partner' => $request->logistic_partner,
            'channel_partner' => $request->channel_partner,
            'brands' => $request->brands,
            'press_coverage' => $request->press_coverage,
            'about' => $request->about,
            'account_details' => $request->account_details,
            'stats_title' => $request->stats_title,
            'associates_title' => $request->associates_title,
            'steps_title' => $request->steps_title,
            'signup_title' => $request->signup_title,
            'ease_title' => $request->ease_title,
            'logistics_title' => $request->logistics_title,
            'brand_title' => $request->brand_title,
            'press_title' => $request->press_title,
            'channel_title' => $request->channel_title,
            'subscribe_title' => $request->subscribe_title,
            'copyright' => $request->copyright,
            'e_cod_title' => $request->early_cod_title,
            'e_cod_features' => $request->early_cod_features,
            'gst_percent' => $request->gst_charge,
            'rto_charge' => $request->rto_charge,
            'reverse_charge' => $request->reverse_charge,
            'reconciliation_days' => $request->reconciliation_days,
            'invoice_generate_days' => $request->invoice_generate_days,
            'account_holder' => $request->account_holder,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'gstin' => $request->gstin,
            'cin_number' => $request->cin_number,
            'pan_number' => $request->pan_number,
            'irn_number' => $request->irn_number,
            'sac_number' => $request->sac_number,
            'hsn_number' => $request->hsn_number,
            'razorpay_key' => $request->razorpay_key,
            'razorpay_secret' => $request->razorpay_secret,
            'mis_download_limit' => $request->mis_download_limit,
            'qc_charges' => $request->qc_charges,
            'bulkship_limit' => $request->bulkship_limit,
        );
        if ($request->hasFile('logo')) {
            $oName = $request->logo->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "LOGO." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->logo->move(public_path('assets/admin/images/'), $name);
            $data['logo'] = $filepath;
        }
        if ($request->hasFile('favicon')) {
            $oName = $request->favicon->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "FAV." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->favicon->move(public_path('assets/admin/images/'), $name);
            $data['favicon'] = $filepath;
        }
        if ($request->hasFile('testimonial_image')) {
            $oName = $request->testimonial_image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "TST." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->testimonial_image->move(public_path('assets/admin/images/'), $name);
            $data['testimonial_image'] = $filepath;
        }
        if ($request->hasFile('agreement')) {
            $oName = $request->agreement->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "AGR." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->agreement->move(public_path('assets/admin/images/'), $name);
            $data['agreement'] = $filepath;
        }
        if ($request->hasFile('signature_image')) {
            $oName = $request->signature_image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "SGN." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->signature_image->move(public_path('assets/admin/images/'), $name);
            $data['signature_image'] = $filepath;
        }
        if ($request->hasFile('payment_qrcode')) {
            $oName = $request->payment_qrcode->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "QRCode." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->payment_qrcode->move(public_path('assets/admin/images/'), $name);
            $data['payment_qrcode'] = $filepath;
        }
        //dd($data);
        Configuration::updateOrCreate(['id' => 1],$data);
        //creating notification
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Configuration saved successfully',
            ),
        );
        Session($notification);
        return back();
    }


    //Dashboard Method
    function dashboard() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['admin'] = Admin::all();
        $data['config'] = Configuration::first();

        $data['total_seller'] = Seller::count();
        $data['total_order'] = Order::where('seller_id',"!=",1)->where('is_custom',0)->where('status','!=','cancelled')->count();
        $data['today_created_order'] = Order::where('seller_id',"!=",1)->where('is_custom',0)->where('status','!=','cancelled')->whereDate('inserted', Carbon::today())->count();
        $data['today_shipped_order'] = Order::where('seller_id',"!=",1)->where('is_custom',0)->where('status','!=','cancelled')->whereDate('awb_assigned_date', Carbon::today())->count();
        $data['today_invoice_value'] = number_format(Order::where("seller_id","!=",1)->where('is_custom',0)->where('status','!=','cancelled')->whereDate('inserted', Carbon::today())->sum('invoice_amount'),2);
        $data['today_shipped_invoice_value'] = number_format(Order::where('seller_id',"!=",1)->where('is_custom',0)->where('status','!=','cancelled')->whereDate('awb_assigned_date', Carbon::today())->sum('invoice_amount'),2);
        $data['today_freight_charges'] = number_format(Order::where('seller_id',"!=",1)->where('is_custom',0)->where('status','!=','cancelled')->whereDate('awb_assigned_date', Carbon::today())->sum('total_charges'),2);
        return view('admin.dashboard', $data);
    }

    function orderReport(Request $request) {
        $config = Configuration::first();
        if($request->report == 'all-order') {
            $firstDay = date('Y-m-d', strtotime("last week"));
            $currentDay = date('Y-m-d');
            $day = $firstDay;
            $week = [];
            while($day <= $currentDay) {
                $week[] = $day;
                $day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
            }
            $orders = [];
            foreach($week as $date) {
                $orders[] = Order::selectRaw("orders.status,date(awb_assigned_date) as date, count(*) as total_orders")
                    ->whereDate('awb_assigned_date', $date)
                    ->where('orders.status','!=','cancelled')
                    ->where('is_custom',0)
                    ->first();
            }
            $dataSet = [];
            foreach($orders as $order) {
                $sellers = Order::selectRaw("orders.status,CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as total_orders")
                    ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                    ->whereDate('awb_assigned_date', $order->date)
                    ->where('orders.status','!=','cancelled')
                    ->where('is_custom',0)
                    ->groupBy('seller_id')
                    ->orderBy('total_orders', 'desc')
                    ->limit(10)
                    ->get();
                $tooltip = [];
                foreach($sellers as $seller) {
                    $tooltip[] = "{$seller->name}: {$seller->total_orders}";
                }
                $dataSet[] = [
                    'x' => $order->date.' : '.$order->total_orders,
                    'value' => $order->total_orders,
                    'tooltip' => implode('\n', $tooltip)
                ];
            }
        }
        else {
            $orders = Order::selectRaw("orders.status,seller_id, CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as today_total_orders, (select count(*) from orders oy where date(oy.awb_assigned_date) = ? and oy.seller_id = orders.seller_id) as yesterday_total_orders", [Carbon::yesterday()])
                ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                ->whereDate('awb_assigned_date', Carbon::today())
                ->where('is_custom',0)
                ->where('orders.status','!=','cancelled')
                ->groupBy('seller_id')
                ->orderBy('today_total_orders', 'desc')
                ->limit(10)
                ->get();
            $dataSet = [];
            foreach($orders as $order) {
                $dataSet[] = [
                    $order->name,
                    $order->today_total_orders,
                    $order->yesterday_total_orders,
                ];
            }
        }
        return response()->json($dataSet);
    }

    function exportOrderReport(Request $request) {
        if(!$request->filled('report')) {
            return;
        }

        $fileName = $request->report;

        $config = Configuration::first();
        if($request->report == 'all-order') {
            $firstDay = date('Y-m-d', strtotime("last week"));
            $currentDay = date('Y-m-d');
            $day = $firstDay;
            $week = [];
            while($day <= $currentDay) {
                $week[] = $day;
                $day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
            }
            $orders = [];
            foreach($week as $date) {
                $orders[] = Order::selectRaw("status,date(awb_assigned_date) as date,status, count(*) as total_orders")
                    ->whereDate('awb_assigned_date', $date)
                    ->where('seller_id',"!=", 1)
                    ->where('is_custom',0)
                    ->where('status','!=','cancelled')
                    ->first();
            }
            $dataSet = [];
            foreach($orders as $order) {
                $dataSet['headers'][] = $order->date;
                $sellers = Order::selectRaw("sellers.id,orders.status, CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as total_orders")
                    ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                    ->whereDate('awb_assigned_date', $order->date)
                    ->where('seller_id','!=', 1)
                    ->where('is_custom',0)
                    ->where('orders.status','!=','cancelled')
                    ->groupBy('seller_id')
                    ->orderBy('total_orders', 'desc')
                    ->limit(20)
                    ->get();
                foreach($sellers as $seller) {
                    $dataSet['data'][$seller->id]['seller'] = $seller->name;
                    $dataSet['data'][$seller->id]['data'][$order->date] = $seller->total_orders;
                }
            }

            $contents = [];
            $header = array('Seller Name', ...$dataSet['headers']);
            foreach($dataSet['data'] as $data) {
                $content = [];
                foreach($header as $col) {
                    if($col == "Seller Name") {
                        $content[$col] = $data['seller'];
                    } else {
                        $content[$col] = @$data['data'][$col] ?? 0;
                    }
                }
                $contents[] = $content;
            }

            $fp = fopen("$fileName.csv", 'w');
            fputcsv($fp, $header);
            foreach($contents as $content) {
                $content = array(
                    ...array_values($content)
                );
                fputcsv($fp, $content);
            }
        }
        else if($request->report == 'seller-custom-order'){
            $firstDay = date('Y-m-d', strtotime("last week"));
            $currentDay = date('Y-m-d');
            $day = $firstDay;
            $week = [];
            while($day <= $currentDay) {
                $week[] = $day;
                $day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
            }
            $orders = [];
            foreach($week as $date) {
                $orders[] = Order::selectRaw("status,date(awb_assigned_date) as date,status, count(*) as total_orders")
                    ->whereDate('awb_assigned_date', $date)
                    ->where('seller_id',"!=", 1)
                    ->where('is_custom',1)
                    ->where('status','!=','cancelled')
                    ->first();
            }
            $dataSet = [];
            foreach($orders as $order) {
                $dataSet['headers'][] = $order->date;
                $sellers = Order::selectRaw("sellers.id,is_custom,orders.status, CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as total_orders")
                    ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                    ->whereDate('awb_assigned_date', $order->date)
                    ->where('seller_id','!=', 1)
                    ->where('is_custom',1)
                    ->where('orders.status','!=','cancelled')
                    ->groupBy('seller_id')
                    ->orderBy('total_orders', 'desc')
                    ->limit(20)
                    ->get();
                foreach($sellers as $seller) {
                    $dataSet['data'][$seller->id]['seller'] = $seller->name;
                    $dataSet['data'][$seller->id]['data'][$order->date] = $seller->total_orders;
                }
            }

            $contents = [];
            $header = array('Seller Name', ...$dataSet['headers']);
            foreach($dataSet['data'] as $data) {
                $content = [];
                foreach($header as $col) {
                    if($col == "Seller Name") {
                        $content[$col] = $data['seller'];
                    } else {
                        $content[$col] = @$data['data'][$col] ?? 0;
                    }
                }
                $contents[] = $content;
            }

            $fp = fopen("$fileName.csv", 'w');
            fputcsv($fp, $header);
            foreach($contents as $content) {
                $content = array(
                    ...array_values($content)
                );
                fputcsv($fp, $content);
            }
        }
        else if($request->report == 'custom-seller-order'){
            $orders = Order::selectRaw("seller_id,orders.status, CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as today_total_orders, (select count(*) from orders oy where date(oy.awb_assigned_date) = ? and oy.seller_id = orders.seller_id) as yesterday_total_orders", [Carbon::yesterday()])
                ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                ->whereDate('awb_assigned_date', Carbon::today())
                ->where('seller_id', "!=",1)
                ->where('is_custom',1)
                ->where('orders.status','!=','cancelled')
                ->groupBy('seller_id')
                ->orderBy('today_total_orders', 'desc')
                ->limit(20)
                ->get();

            $fp = fopen("$fileName.csv", 'w');
            $header = array('Seller Name', 'Todays Order', 'Yesterdays Order');
            fputcsv($fp, $header);
            foreach($orders as $order) {
                $content = array(
                    $order->name,
                    $order->today_total_orders,
                    $order->yesterday_total_orders,
                );
                fputcsv($fp, $content);
            }
        }
        else {
            $orders = Order::selectRaw("seller_id,orders.status, CONCAT(sellers.company_name, ' (', sellers.code, ')') as name, count(*) as today_total_orders, (select count(*) from orders oy where date(oy.awb_assigned_date) = ? and oy.seller_id = orders.seller_id) as yesterday_total_orders", [Carbon::yesterday()])
                ->join('sellers', 'sellers.id', '=', 'orders.seller_id')
                ->whereDate('awb_assigned_date', Carbon::today())
                ->where('seller_id', "!=",1)
                ->where('is_custom',0)
                ->where('orders.status','!=','cancelled')
                ->groupBy('seller_id')
                ->orderBy('today_total_orders', 'desc')
                ->limit(20)
                ->get();

            $fp = fopen("$fileName.csv", 'w');
            $header = array('Seller Name', 'Todays Order', 'Yesterdays Order');
            fputcsv($fp, $header);
            foreach($orders as $order) {
                $content = array(
                    $order->name,
                    $order->today_total_orders,
                    $order->yesterday_total_orders,
                );
                fputcsv($fp, $content);
            }
        }

        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$fileName.csv"));
        header("Content-Disposition: attachment; filename=$fileName.csv");
        readfile("$fileName.csv");
        @unlink("$fileName.csv");
    }

    function profile()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['admin'] = Admin::all();
        return view('admin.profile', $data);
    }

    function save_profile(Request $request)
    {
        $data = array(
            'id' => $request->hid,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'modified' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
        }
        Admin::where('id', $request->hid)->update($data);
        $admin = Admin::find($request->hid);
        Session()->put(['MyAdmin' => $admin]);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Profile updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function change_password(Request $request)
    {
        if ($request->oldPassword == "") {
            echo json_encode(array('status' => 'false', 'message' => 'Please Enter Old Password'));
        } else if ($request->newPassword == "") {
            echo json_encode(array('status' => 'false', 'message' => 'Please Enter New Password'));
        } else if ($request->confirmNewPassword == "") {
            echo json_encode(array('status' => 'false', 'message' => 'Please Enter Confirm New Password'));
        } else if ($request->oldPassword != Session()->get('MyAdmin')->password) {
            echo json_encode(array('status' => 'false', 'message' => 'Invalid Old Password'));
        } else if ($request->newPassword != $request->confirmNewPassword) {
            echo json_encode(array('status' => 'false', 'message' => 'Both Passwords are not same'));
        } else {
            $data = array(
                'password' => $request->newPassword
            );
            Admin::where('id', Session()->get('MyAdmin')->id)->update($data);
            $admin = Admin::find(Session()->get('MyAdmin')->id);
            Session()->put(['MyAdmin' => $admin]);
            echo json_encode(array('status' => 'true', 'message' => 'Password saved Successfully'));
        }
    }

    function recharge_request()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['recharge'] = DB::select("select s.first_name,s.last_name,s.code,r.* from sellers s,recharge_request r where r.seller_id=s.id and r.status='n'");
        return view('admin.recharge_request', $data);
    }

    function approve_neft(Request $request)
    {
        $id = $request->id;
        if ($request->status == 'y') {
            $resp = Recharge_request::find($id);
            $seller = Seller::select('balance')->where('id', $resp->seller_id)->first();
            if ($resp->type == 'neft') {
                $data = array(
                    'seller_id' => $resp->seller_id,
                    'amount' => $resp->amount,
                    'type' => 'c',
                    'datetime' => date('Y-m-d H:i:s'),
                    'method' => 'NEFT',
                    'balance' => $seller->balance + $resp->amount,
                    'utr_number' => $resp->utr_number,
                    'description' => "Wallet Recharge using NEFT",
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                );
                Transactions::create($data);
                Recharge_request::where('id', $id)->update(['status' => 'y']);
                Seller::where('id', $resp->seller_id)->increment('balance', $data['amount']);
            } else {
                $data = array(
                    'seller_id' => $resp->seller_id,
                    'amount' => $resp->amount,
                    'balance' => $resp->amount + $seller->balance,
                    'type' => 'c',
                    'datetime' => date('Y-m-d H:i:s'),
                    'description' => 'COD Remittance',
                    'method' => 'COD Remittance',
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                );
                Transactions::create($data);
                $data = array(
                    'seller_id' => $resp->seller_id,
                    'amount' => $resp->amount,
                    'type' => 'd',

                    'datetime' => date('Y-m-d H:i:s'),
                    'description' => 'COD Remittance',
                    'remitted_by' => 'seller'
                );
                COD_transactions::create($data);
                Recharge_request::where('id', $id)->update(['status' => 'y']);
                Seller::where('id', $resp->seller_id)->increment('balance', $data['amount']);
                Seller::where('id', $resp->seller_id)->decrement('cod_balance', $data['amount']);
                Seller::where('id', $resp->seller_id)->update(['last_remitted' => $data['amount']]);
            }
        } else {
            Recharge_request::where('id', $id)->delete();
        }
        echo json_encode(['status' => 'true']);
    }

    function early_cod()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['early_cod'] = EarlyCod::all();
        return view('admin.early_cod', $data);
    }

    function early_cod_insert(Request $request)
    {
        $data = array(
            'title' => $request->title,
            'rate' => $request->rate,
            'number_of_days' => $request->number_of_day,
            'status' => 'y',
        );
        if ($request->hasFile('icon')) {
            $oName = $request->icon->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->icon->move(public_path('assets/admin/images/'), $name);
            $data['icon'] = $filepath;
        }
        EarlyCod::create($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Early Cod Plan added successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function early_cod_modify($id)
    {
        $response = EarlyCod::find($id);
        echo json_encode($response);
    }

    function early_cod_delete($id)
    {
        EarlyCod::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function early_cod_status(Request $request)
    {
        $data = array(
            'status' => $request->status
        );
        EarlyCod::where('id', $request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }

    function early_cod_update(Request $request)
    {
        $data = array(
            'title' => $request->title,
            'rate' => $request->rate,
            'number_of_days' => $request->number_of_day,
        );
        if ($request->hasFile('icon')) {
            $oName = $request->icon->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->icon->move(public_path('assets/admin/images/'), $name);
            $data['icon'] = $filepath;
        }
        EarlyCod::where('id', $request->id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Early COD Plan updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function credit_receipt()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['credit_receipt'] = BillReceipt::all();
        $data['awb_number'] = Order::select('awb_number')->where('awb_number', '!=', null)->where('status', 'delivered')->get();
        return view('admin.credit_receipt', $data);
    }

    function credit_receipt_insert(Request $request)
    {
        $number = rand(10000, 500000);
        $data = array(
            'awb_number' => $request->awb_number,
            'total' => '',
            'note_reason' => $request->reason,
            'gstin' => $request->gstin,
            'note_number' => "CN/CM/$number",
            'note_date' => date('Y-m-d H:i:s')
        );
        $receipt_id = BillReceipt::create($data)->id;
        $total_amount = 0;

        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $data = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $data = array(
                                'receipt_id' => $receipt_id,
                                'awb_number' => isset($fileop[0]) ? trim($fileop[0], '`') : "",
                                'amount' => isset($fileop[1]) ? $fileop[1] : "",
                            );
                            $total_amount += $fileop[1];
                            $seller_id = Order::select('seller_id')->where('awb_number', trim($fileop[0], '`'))->first();
                        }
                        $order = ReceiptDetail::create($data);
                    }
                    $cnt++;
                }
                BillReceipt::where('id', $receipt_id)->update(['total' => $total_amount, 'seller_id' => $seller_id->seller_id]);
                $notification = array(
                    'notification' => array(
                        'type' => 'success',
                        'title' => 'Success',
                        'message' => 'Receipt added successfully',
                    ),
                );
                Session($notification);
                return back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
    }

    function credit_receipt_delete($id)
    {
        BillReceipt::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function zone_mapping()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['zone_mapping'] = ZoneMapping::simplePaginate(10);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['awb_number'] = Order::select('awb_number')->where('awb_number', '!=', null)->where('status', 'delivered')->get();
        return view('admin.zone_mapping', $data);
    }

    function weightReconciliation(Request $request)
    {
        $weight_reconciliation = WeightReconciliation::query();
        if($request->filled('seller_id')) {
            if(is_array($request->seller_id)) {
                $weight_reconciliation = $weight_reconciliation->whereIn('seller_id', $request->seller_id);
            } else {
                $weight_reconciliation = $weight_reconciliation->where('seller_id', $request->seller_id);
            }
        }
        if($request->filled('from_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '<=', $request->to_date);
        }
        $weight_reconciliation = $weight_reconciliation->latest('id')->paginate(10);
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['weight_reconciliation'] = $weight_reconciliation;
        $data['sellers'] = Seller::get();
        return view('admin.weight_reconciliation', $data);
    }

    function exportWeightReconciliation(Request $request)
    {
        $weight_reconciliation = WeightReconciliation::query();
        if($request->filled('seller_id')) {
            if(is_array($request->seller_id)) {
                $weight_reconciliation = $weight_reconciliation->whereIn('seller_id', $request->seller_id);
            } else {
                $weight_reconciliation = $weight_reconciliation->where('seller_id', $request->seller_id);
            }
        }
        if($request->filled('from_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '<=', $request->to_date);
        }
        $data = $weight_reconciliation->latest('id')->get();
        $name = "exports/weight-reconciliation";
        $filename = "weight-reconciliation";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'AWB Number',
            'Applied Weight(KG)',
            'Applied Length(CM)',
            'Applied Breadth(CM)',
            'Applied Height(CM)',
            'Charged Weight(KG)',
            'Charged Length(CM)',
            'Charged Breadth(CM)',
            'Charged Height(CM)',
            'Charged Amount',
            'Date',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                "`{$e->awb_number}`",
                $e->e_weight,
                $e->e_length,
                $e->e_breadth,
                $e->e_height,
                $e->c_weight,
                $e->c_length,
                $e->c_breadth,
                $e->c_height,
                $e->charged_amount,
                $e->created,
            );
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

    function weightReconciliationLogs(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['logs'] = FileUploadJobModel::whereIn('job_name', ['weight_reconciliation_upload', 'settled_weight_reconciliation_upload'])->latest()->get();
        return view('admin.weight_reconciliation_logs', $data);
    }

    function weightReconciliationError()
    {
        $job = FileUploadJob::getLastJob('weight_reconciliation_upload');
        if(empty($job)) {
            return response()->json([
                'status' => true,
                'message' => 'No error found',
                'data' => [],
            ]);
        }
        $job->logs = FileUploadJob::getJobLog($job->id, 'fail');
        return response()->json([
            'status' => true,
            'message' => $job->status == 'success' ? 'Total ' . $job->success . ' weight reconciliation uploaded successfully' : $job->remark,
            'data' => $job,
        ]);
    }

    function exportWeightReconciliationError(Request $request)
    {
        $data = FileUploadJob::getJobLog($request->job_id, $request->status ?? null);
        $name = "exports/WeightReco_Error";
        $filename = "WeightReco_Error";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'AWB Number',
            'Charged Weight(KG)',
            'Charged Length(CM)',
            'Charged Breadth(CM)',
            'Charged Height(CM)',
            'Remark',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                "`{$e->awb_number}`",
                $e->weight,
                $e->length,
                $e->breadth,
                $e->height,
                $e->remark,
            );
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

    function weightReconciliationDelete($id)
    {
        WeightReconciliation::where('id', $id)->delete();
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Weight Reconciliation Delete Successfully',
            ),
        );
        Session($notification);
        echo json_encode(array('status' => 'true'));
    }

    function importCsvWeigthReconciliation(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $totalRecords = 0;
                $success = 0;
                $alreadyUploaded = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $uploadedAt = date('Y-m-d H:i:s');
                // Create job
                $job = FileUploadJob::createJob('weight_reconciliation_upload');
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    DB::beginTransaction();
                    try {
                        if ($cnt > 0) {
                            if ($fileop[0] != "") {
                                $totalRecords++;
                                $awbNumber = trim($fileop[0], '`');
                                $weight = is_numeric($fileop[1] ?? null) ? $fileop[1] : 0;
                                $length = is_numeric($fileop[2] ?? null) ? $fileop[2] : 0;
                                $breadth = is_numeric($fileop[3] ?? null) ? $fileop[3] : 0;
                                $height = is_numeric($fileop[4] ?? null) ? $fileop[4] : 0;
                                $order = Order::where('awb_number', $awbNumber)->first();
                                if(empty($order))
                                    $order = ZZArchiveOrder::where('awb_number', $awbNumber)->first();
                                if (empty($order)) {
                                    $notification = array(
                                        'notification' => array(
                                            'type' => 'Error',
                                            'title' => 'Error',
                                            'message' => 'Order Not Found.',
                                        ),
                                    );
                                    Session($notification);
                                    // Create job log
                                    FileUploadJob::createJobLog([
                                        'job_id' => $job->id,
                                        'awb_number' => $awbNumber,
                                        'weight' => $weight,
                                        'length' => $length,
                                        'breadth' => $breadth,
                                        'height' => $height,
                                        'status' => 'fail',
                                        'remark' => 'Order Not Found.',
                                    ]);
                                    DB::commit();
                                    continue;
                                } else {
                                    if ($order->weight_disputed == 'y') {
                                        // Create job log
                                        FileUploadJob::createJobLog([
                                            'job_id' => $job->id,
                                            'awb_number' => $awbNumber,
                                            'weight' => $weight,
                                            'length' => $length,
                                            'breadth' => $breadth,
                                            'height' => $height,
                                            'status' => 'fail',
                                            'remark' => 'AWB is already uploaded.',
                                        ]);
                                        DB::commit();
                                        $alreadyUploaded++;
                                        continue;
                                    }
                                    $isError = 'n';
                                    $weightDisputed = 'y';
                                    $errorMessage = null;

                                    if(empty($weight) || empty($length) || empty($breadth) || empty($height)) {
                                        $weightDisputed = 'n';
                                        $isError = 'y';
                                        $errorMessage = 'Invalid Dimensions';
                                    }

                                    // Create job log
                                    FileUploadJob::createJobLog([
                                        'job_id' => $job->id,
                                        'awb_number' => $awbNumber,
                                        'weight' => $weight,
                                        'length' => $length,
                                        'breadth' => $breadth,
                                        'height' => $height,
                                        'status' => $isError == 'y' ? 'fail' : 'success',
                                        'remark' => $errorMessage,
                                    ]);

                                    // Calculate charges
                                    $vol_weigth = ($length * $breadth * $height) / 5;
                                    if ($weight > ($vol_weigth / 1000)) {
                                        $vol_weigth = $weight * 1000;
                                    }
                                    $actualWeight = $order->weight > $order->vol_weight ? $order->weight : $order->vol_weight;
                                    if($vol_weigth > $actualWeight){
                                        $total_vol_charge = $this->_calculateTotalCharges($order->awb_number, $vol_weigth);
                                        $extra_charge = $total_vol_charge - $order->total_charges;
                                        if($order->rto_status == 'y')
                                            $extra_charge*=2;
                                        $data = array(
                                            'seller_id' => $order->seller_id,
                                            'awb_number' => $order->awb_number,
                                            'e_weight' => isset($order->weight) ? $order->weight / 1000 : "",
                                            'e_length' => isset($order->length) ? $order->length : "",
                                            'e_breadth' => isset($order->breadth) ? $order->breadth : "",
                                            'e_height' => isset($order->height) ? $order->height : "",
                                            'applied_amount' => isset($order->total_charges) ? $order->total_charges : "",
                                            'c_weight' => $weight,
                                            'c_length' => $length,
                                            'c_breadth' => $breadth,
                                            'c_height' => $height,
                                            'charged_amount' => $total_vol_charge,
                                            'status' => 'pending',
                                            'action_taken_by' => env('appTitle'),
                                            'created' => $uploadedAt,
                                            'is_error' => $isError,
                                            'error_message' => $errorMessage,
                                            'uploaded_at' => $uploadedAt
                                        );
                                        WeightReconciliation::updateOrCreate([
                                            'seller_id' => $order->seller_id,
                                            'awb_number' => $order->awb_number,
                                        ], $data);
                                        $od = Order::where('id', $order->id)->update([
                                            'weight_disputed' => $weightDisputed
                                        ]);
                                        $oz = ZZArchiveOrder::where('id', $order->id)->update([
                                            'weight_disputed' => $weightDisputed
                                        ]);
                                        if($isError == 'n') {
                                            $success++;
                                        }
                                        // Deduct charges if no any error and weight disputed is y
                                        if($order->rto_status == 'n')
                                            $description = 'Charges Deducted for Weight Reconciliation for Forward Shipment';
                                        else
                                            $description = 'Charges Deducted for Weight Reconciliation for Forward and RTO Shipment';
                                        if ($isError == 'n' && $weightDisputed == 'y' && $total_vol_charge > $order->total_charges) {
                                            $seller = Seller::where('id', $order->seller_id)->first();
                                            $data = array(
                                                'seller_id' => $order->seller_id,
                                                'order_id' => $order->id,
                                                'amount' => $extra_charge,
                                                'balance' => $seller->balance - $extra_charge,
                                                'type' => 'd',
                                                'redeem_type' => 'o',
                                                'datetime' => date('Y-m-d H:i:s'),
                                                'method' => 'wallet',
                                                'description' => $description
                                            );
                                            Transactions::create($data);
                                            Seller::where('id', $order->seller_id)->decrement('balance', $data['amount']);

                                            // Notify user
                                            $notify = [
                                                'awb_number' => $order->awb_number,
                                                'amount' => $extra_charge
                                            ];
                                            Seller::find($order->seller_id)->notify(new DisputeNotification($notify));
                                        }
                                    }
                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        continue;
                    }
                    $cnt++;
                }
                if($success == $totalRecords) {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $totalRecords . ' weight reconciliation updated successfully';
                } else if($success == 0 && $alreadyUploaded == 0) {
                    $type = 'error';
                    $title = 'Error';
                    $message = 'Weight reconciliation not updated';
                } else {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $success . ' out of ' . $totalRecords . ' weight reconciliation updated and '.$alreadyUploaded.' already uploaded';
                }
                FileUploadJob::updateJob($job->id, [
                    'status' => $type == 'success' ? 'success' : 'fail',
                    'remark' => $message,
                    'total_records' => $totalRecords,
                    'success' => $success,
                    'failed' => ($totalRecords - $success),
                    'already_uploaded' => $alreadyUploaded,
                ]);
                $notification = array(
                    'notification' => array(
                        'type' => $type,
                        'title' => $title,
                        'message' => $message,
                    ),
                );
                Session($notification);
               return back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
        return false;
    }

    function weightDispute()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['weight_dispute'] = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
            ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
            ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status', 'weight_reconciliation.c_weight as c_weight', 'weight_reconciliation.c_length as c_length', 'weight_reconciliation.c_breadth as c_breadth', 'weight_reconciliation.c_height as c_height')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('admin.weight_dispute', $data);
    }

    function settlementWeightReconciliation(Request $request)
    {
        $weight_reconciliation = WeightReconciliation::whereNotNull('settled_amount');
        if($request->filled('seller_id')) {
            if(is_array($request->seller_id)) {
                $weight_reconciliation = $weight_reconciliation->whereIn('seller_id', $request->seller_id);
            } else {
                $weight_reconciliation = $weight_reconciliation->where('seller_id', $request->seller_id);
            }
        }
        if($request->filled('from_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '<=', $request->to_date);
        }
        $weight_reconciliation = $weight_reconciliation->latest('id')->paginate(10);
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['settlement_weight_reconciliation'] = $weight_reconciliation;
        $data['sellers'] = Seller::get();
        return view('admin.settlement_weight_reconciliation', $data);
    }

    function exportSettledWeightReconciliation(Request $request)
    {
        $weight_reconciliation = WeightReconciliation::whereNotNull('settled_amount');
        if($request->filled('seller_id')) {
            if(is_array($request->seller_id)) {
                $weight_reconciliation = $weight_reconciliation->whereIn('seller_id', $request->seller_id);
            } else {
                $weight_reconciliation = $weight_reconciliation->where('seller_id', $request->seller_id);
            }
        }
        if($request->filled('from_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $weight_reconciliation = $weight_reconciliation->whereDate('created', '<=', $request->to_date);
        }
        $data = $weight_reconciliation->latest('id')->get();
        $name = "exports/settled-weight-reconciliation";
        $filename = "settled-weight-reconciliation";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'AWB Number',
            'Applied Weight(KG)',
            'Applied Length(CM)',
            'Applied Breadth(CM)',
            'Applied Height(CM)',
            'Charged Weight(KG)',
            'Charged Length(CM)',
            'Charged Breadth(CM)',
            'Charged Height(CM)',
            'Charged Amount',
            'Settled Weight(KG)',
            'Settled Length(CM)',
            'Settled Breadth(CM)',
            'Settled Height(CM)',
            'Settled Amount',
            'Date',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                "`{$e->awb_number}`",
                $e->e_weight,
                $e->e_length,
                $e->e_breadth,
                $e->e_height,
                $e->c_weight,
                $e->c_length,
                $e->c_breadth,
                $e->c_height,
                $e->charged_amount,
                $e->s_weight,
                $e->s_length,
                $e->s_breadth,
                $e->s_height,
                $e->settled_amount,
                $e->created,
            );
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

    function settlementWeightReconciliationDelete($id)
    {
        WeightReconciliation::where('id', $id)->update([
            's_weight' => null,
            's_length' => null,
            's_breadth' => null,
            's_height' => null,
            'settled_amount' => null,
        ]);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Settlement Weight Reconciliation Delete Successfully',
            ),
        );
        Session($notification);
        echo json_encode(array('status' => 'true'));
    }

    function importCsvSettlementWeigthReconciliation(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $total = 0;
                $success = 0;
                $alreadyUploaded = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $uploadedAt = date('Y-m-d H:i:s');
                // Create job
                $job = FileUploadJob::createJob('settled_weight_reconciliation_upload');
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    DB::beginTransaction();
                    try {
                        if ($cnt > 0) {
                            if ($fileop[0] != "") {
                                $total++;
                                $awbNumber = trim($fileop[0], '`');
                                $weight = is_numeric($fileop[1] ?? null) ? $fileop[1] : 0;
                                $length = is_numeric($fileop[2] ?? null) ? $fileop[2] : 0;
                                $breadth = is_numeric($fileop[3] ?? null) ? $fileop[3] : 0;
                                $height = is_numeric($fileop[4] ?? null) ? $fileop[4] : 0;

                                $order = Order::where('awb_number', $awbNumber)->first();
                                if (empty($order)) {
                                    $notification = array(
                                        'notification' => array(
                                            'type' => 'Error',
                                            'title' => 'Error',
                                            'message' => 'Order Not Found.',
                                        ),
                                    );
                                    Session($notification);
                                    // Create job log
                                    FileUploadJob::createJobLog([
                                        'job_id' => $job->id,
                                        'awb_number' => $awbNumber,
                                        'weight' => $weight,
                                        'length' => $length,
                                        'breadth' => $breadth,
                                        'height' => $height,
                                        'status' => 'fail',
                                        'remark' => 'Order Not Found.',
                                    ]);
                                    DB::commit();
                                    continue;
                                } else {
                                    if ($order->weight_disputed != 'y') {
                                        // Create job log
                                        FileUploadJob::createJobLog([
                                            'job_id' => $job->id,
                                            'awb_number' => $awbNumber,
                                            'weight' => $weight,
                                            'length' => $length,
                                            'breadth' => $breadth,
                                            'height' => $height,
                                            'status' => 'fail',
                                            'remark' => 'AWB weight is not disputed.',
                                        ]);
                                        DB::commit();
                                        continue;
                                    }
                                    if ($order->settled_weight_disputed == 'y') {
                                        // Create job log
                                        FileUploadJob::createJobLog([
                                            'job_id' => $job->id,
                                            'awb_number' => $awbNumber,
                                            'weight' => $weight,
                                            'length' => $length,
                                            'breadth' => $breadth,
                                            'height' => $height,
                                            'status' => 'fail',
                                            'remark' => 'AWB is already uploaded.',
                                        ]);
                                        $alreadyUploaded++;
                                        DB::commit();
                                        continue;
                                    }

                                    $isError = 'n';
                                    $settledWeightDisputed = 'y';
                                    $errorMessage = null;

                                    // Get charged weight
                                    $reconciliation = WeightReconciliation::where('seller_id', $order->seller_id)
                                        ->where('awb_number', $order->awb_number)
                                        ->where('is_error', 'n')
                                        ->first();
                                    if(empty($reconciliation)) {
                                        $settledWeightDisputed = 'n';
                                        $isError = 'y';
                                        $errorMessage = 'Charged Dimensions Not Found';
                                    }

                                    if(empty($weight) || empty($length) || empty($breadth) || empty($height)) {
                                        $settledWeightDisputed = 'n';
                                        $isError = 'y';
                                        $errorMessage = 'Invalid Dimensions';
                                    }

                                    // Create job log
                                    FileUploadJob::createJobLog([
                                        'job_id' => $job->id,
                                        'awb_number' => $awbNumber,
                                        'weight' => $weight,
                                        'length' => $length,
                                        'breadth' => $breadth,
                                        'height' => $height,
                                        'status' => $isError == 'y' ? 'fail' : 'success',
                                        'remark' => $errorMessage,
                                    ]);

                                    // Calculate charges
                                    $vol_weigth = ($length * $breadth * $height) / 5;
                                    if ($weight > ($vol_weigth / 1000)) {
                                        $vol_weigth = $weight * 1000;
                                    }
                                    $actualWeight = $order->weight > $order->vol_weight ? $order->weight : $order->vol_weight;
                                    $total_vol_charge = $this->_calculateTotalCharges($order->awb_number, $vol_weigth);
                                    if($vol_weigth > $actualWeight){
                                        if(!empty($reconciliation)) {
                                            $charged_amount = $reconciliation->charged_amount;
                                            $new_charge = $charged_amount - $total_vol_charge;
                                        }
                                        else {
                                            $charged_amount = 0;
                                            $new_charge = 0;
                                        }
                                        $data = array(
                                            's_weight' => $weight,
                                            's_length' => $length,
                                            's_breadth' => $breadth,
                                            's_height' => $height,
                                            'settled_amount' => $total_vol_charge,
                                            'created' => $uploadedAt,
                                            'is_error' => $isError,
                                            'error_message' => $errorMessage,
                                            'uploaded_at' => $uploadedAt
                                        );
                                        WeightReconciliation::updateOrCreate([
                                            'seller_id' => $order->seller_id,
                                            'awb_number' => $order->awb_number,
                                        ], $data);
                                        Order::where('id', $order->id)->update([
                                            'settled_weight_disputed' => $settledWeightDisputed
                                        ]);
                                        if($isError == 'n') {
                                            $success++;
                                        }
                                        // Deduct charges if no any error and selleted weight disputed is y
                                        if ($isError == 'n' && $settledWeightDisputed == 'y' && $total_vol_charge < $charged_amount) {
                                            $seller = Seller::where('id', $order->seller_id)->first();
                                            $data = array(
                                                'seller_id' => $order->seller_id,
                                                'order_id' => $order->id,
                                                'amount' => $new_charge,
                                                'balance' => $seller->balance + $new_charge,
                                                'type' => 'c',
                                                'redeem_type' => 'o',
                                                'datetime' => date('Y-m-d H:i:s'),
                                                'method' => 'wallet',
                                                'description' => 'Charges Credited for Settlement Of Weight Reconciliation'
                                            );
                                            Transactions::create($data);
                                            Seller::where('id', $order->seller_id)->increment('balance', $data['amount']);

                                            $data = array(
                                                'status' => 'accepted',
                                                'action_taken_by' => env('appTitle'),
                                            );
                                            WeightReconciliation::updateOrCreate([
                                                'seller_id' => $order->seller_id,
                                                'awb_number' => $order->awb_number,
                                            ], $data);

                                            // Notify user
                                            $notify = [
                                                'awb_number' => $order->awb_number,
                                                'amount' => $new_charge
                                            ];
                                            Seller::find($order->seller_id)->notify(new DisputeNotification($notify));
                                        }
                                    }

                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        continue;
                    }
                    $cnt++;
                }
                if($success == $total) {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $total . ' settled weight reconciliation updated successfully';
                } else if($success == 0 && $alreadyUploaded == 0) {
                    $type = 'error';
                    $title = 'Error';
                    $message = 'Settled weight reconciliation not updated';
                } else {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $success . ' out of ' . $total . ' settled weight reconciliation updated and '.$alreadyUploaded.' already uploaded';
                }
                FileUploadJob::updateJob($job->id, [
                    'status' => $type == 'success' ? 'success' : 'fail',
                    'remark' => $message,
                    'total_records' => $total,
                    'success' => $success,
                    'failed' => ($total - $success),
                    'already_uploaded' => $alreadyUploaded,
                ]);
                $notification = array(
                    'notification' => array(
                        'type' => $type,
                        'title' => $title,
                        'message' => $message,
                    ),
                );
                Session($notification);
               return back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
        return false;
    }

    function settledWeightReconciliationError()
    {
        $job = FileUploadJob::getLastJob('settled_weight_reconciliation_upload');
        if(empty($job)) {
            return response()->json([
                'status' => true,
                'message' => 'No error found',
                'data' => [],
            ]);
        }
        $job->logs = FileUploadJob::getJobLog($job->id, 'fail');
        return response()->json([
            'status' => true,
            'message' => $job->status == 'success' ? 'Total ' . $job->success . ' settled weight reconciliation uploaded successfully' : $job->remark,
            'data' => $job,
        ]);
    }

    function exportSettledWeightReconciliationError(Request $request)
    {
        $data = FileUploadJob::getJobLog($request->job_id, $request->status ?? null);
        $name = "exports/SettledWeightReco_Error";
        $filename = "SettledWeightReco_Error";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'AWB Number',
            'Settled Weight(KG)',
            'Settled Length(CM)',
            'Settled Breadth(CM)',
            'Settled Height(CM)',
            'Remark',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                "`{$e->awb_number}`",
                $e->weight,
                $e->length,
                $e->breadth,
                $e->height,
                $e->remark,
            );
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

    function getHistoryWeightReconciliation($id)
    {
        $data_w['weight_rec_data'] = WeightReconciliation::find($id);
        $data_w['history'] = WeightReconciliationHistory::where('weight_reconciliation_id', $id)->get();
        return view('admin.b_weight_rec_history', $data_w);
    }

    function addWeightRecComment(Request $request)
    {
        //  dd($request->all());
        $data = [
            "weight_reconciliation_id" => $request->weight_rec_id,
            "action_taken_by" => "Twinnship",
            "history_date" => date('Y-m-d H:i:s'),
            "remark" => $request->remark,
            'status' => 'dispute_raised'
        ];
        // dd($data);
        $weight_history_id = WeightReconciliationHistory::create($data);
        $cnt = 1;
        if (isset($request->dispute_images)) {
            foreach ($request->dispute_images as $file) {
                $data_attachment = array(
                    'weight_reconciliation_history_id' => $weight_history_id->id,
                );
                $oName = $file->getClientOriginalName();
                $type = explode('.', $oName);
                $name = date('YmdHis') . "Images-$cnt." . $type[count($type) - 1];
                $filepath = "assets/seller/images/Weight/$name";
                $file->move(public_path('assets/seller/images/Weight/'), $name);
                $data_attachment['image'] = $filepath;
                WeightReconciliationImage::create($data_attachment);
                $cnt++;
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => ' Dispute Added successfully',
            ),
        );
        Session($notification);
        return redirect()->back();
    }

    function close_weight_dispute(Request $request)
    {
        $closing_type = $request->closing_type;
        if ($closing_type == 'seller') {
            $data = [
                "weight_reconciliation_id" => $request->weight_rec_id,
                "action_taken_by" => "Twinnship",
                "history_date" => date('Y-m-d H:i:s'),
                "remark" => $request->remark,
                'status' => 'closed'
            ];

            WeightReconciliationHistory::create($data);
            $weight_recon = WeightReconciliation::where('id', $request->weight_rec_id)->first();
            $order = Order::where('awb_number', $weight_recon->awb_number)->first();
            $seller = Seller::where('id', $order->seller_id)->first();
            $amount = $weight_recon->charged_amount - $order->total_charges;
            // dd($order);
            $data = array(
                'seller_id' => $order->seller_id,
                'order_id' => $order->id,
                'amount' => $amount,
                'balance' => $seller->balance + $amount,
                'type' => 'c',
                'redeem_type' => 'o',
                'datetime' => date('Y-m-d H:i:s'),
                'method' => 'wallet',
                'description' => 'Charges Reverted for Weight Dispute'
            );
            //  dd($data);
            $resp = Transactions::where('seller_id', $data['seller_id'])->where('order_id', $data['order_id'])->where('amount', $data['amount'])->where('type', $data['type'])->count();
            if (intval($resp) == 0) {
                Transactions::create($data);
                Seller::where('id', $order->seller_id)->increment('balance', $data['amount']);
                WeightReconciliation::where('id', $request->weight_rec_id)->update(['status' => 'closed']);
            }

            return redirect()->back();
        } else {
            $data = [
                "weight_reconciliation_id" => $request->weight_rec_id,
                "action_taken_by" => "Twinnship",
                "history_date" => date('Y-m-d H:i:s'),
                "remark" => 'Dispule closed inline to seller comment',
                'status' => 'closed'
            ];
            // dd($data);
            WeightReconciliationHistory::create($data);
            WeightReconciliation::where('id', $request->weight_rec_id)->update(['status' => 'closed']);
            return redirect()->back();
        }
    }


    function _calculateTotalCharges($awb_number, $vol_weigth)
    {
        $o = Order::where('awb_number', $awb_number)->first();
        if(empty($o))
            $o = ZZArchiveOrder::where('awb_number',$awb_number)->first();
        $o->invoice_amount = intval(str_replace(',', '', $o->invoice_amount));
        $seller = Seller::where('id', $o->seller_id)->first();
        $rateCriteria = $this->_findMatchCriteria($o);
        $partner = Partners::where('keyword', $o->courier_partner)->first();
        $zone = $o->zone;
        $extra = ($vol_weigth - $partner->weight_initial) > 0 ? $vol_weigth - $partner->weight_initial : 0;
        $mul = ceil($extra / $partner->extra_limit);
        $plan_id = $seller->plan_id;
        $seller_id = $o->seller_id;
        $partner_rate = DB::select("select *,$rateCriteria + ( extra_charge_" . strtolower($zone) . " * $mul ) as price from rates where plan_id=$plan_id and partner_id = $partner->id and seller_id = $seller_id limit 1");
        $courier_partner = $partner->keyword;
        $shipping_charge = $partner_rate[0]->price;
        $shipping_charge += ($shipping_charge * 18) / 100;
        $cod_maintenance = $partner_rate[0]->cod_maintenance;
        $cod_charge = ($o->invoice_amount * $cod_maintenance) / 100;
        if ($cod_charge < $partner_rate[0]->cod_charge)
            $cod_charge = $partner_rate[0]->cod_charge;
        if (strtolower($o->order_type) == 'prepaid') {
            $cod_charge = "0";
            $early_cod = "0";
        } else {
            $cod_charge = ($o->invoice_amount * $cod_maintenance) / 100;
            if ($cod_charge < $partner_rate[0]->cod_charge)
                $cod_charge = $partner_rate[0]->cod_charge;
            $cod_charge += ($cod_charge * 18) / 100;
            $early_cod = ($o->invoice_amount * $seller->early_cod_charge) / 100;
            $early_cod += ($early_cod * 18) / 100;
        }
        $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
        $rto_charge = ($shipping_charge + $cod_charge + $early_cod) * $seller->rto_charge / 100;
        $total_charge = round($shipping_charge + $cod_charge + $early_cod);
        return $total_charge;
    }

    function _findMatchCriteria($orderDetail)
    {
        switch(strtolower($orderDetail->zone)){
            case 'a':
                return 'within_city';
                break;
            case 'b':
                return 'within_state';
                break;
            case 'c':
                return 'metro_to_metro';
                break;
            case 'd':
                return 'rest_india';
                break;
            case 'e':
                return 'north_j_k';
                break;
        }
        $column = '';
        $res = ZoneMapping::where('pincode', $orderDetail->s_pincode)->where('picker_zone', 'E')->get();
        $metroCities = ['bangalore', 'chennai', 'hyderabad', 'kolkata', 'mumbai', 'new delhi'];
        if (strtolower($orderDetail->s_city) == strtolower($orderDetail->p_city) && strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_city';
        } else if (count($res) == 1) {
            return 'north_j_k';
        } else if (strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_state';
        } else if (in_array(strtolower($orderDetail->s_city), $metroCities) && in_array(strtolower($orderDetail->p_city), $metroCities)) {
            return 'metro_to_metro';
        } else {
            return 'rest_india';
        }
    }

    function codRemittance(Request $request)
    {
        $cod_remittance = COD_transactions::where('remitted_by', 'admin');
        if($request->filled('seller_id')) {
            if(is_array($request->seller_id)) {
                $cod_remittance = $cod_remittance->whereIn('seller_id', $request->seller_id);
            } else {
                $cod_remittance = $cod_remittance->where('seller_id', $request->seller_id);
            }
        }
        if($request->filled('from_date')) {
            $cod_remittance = $cod_remittance->whereDate('datetime', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $cod_remittance = $cod_remittance->whereDate('datetime', '<=', $request->to_date);
        }
        $cod_remittance = $cod_remittance->latest('id')->paginate(10);
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['cod_remittance'] = $cod_remittance;
        $data['sellers'] = Seller::all();
        return view('admin.cod_remittance', $data);
    }

    function codRemittanceByDate(Request $request)
    {
        $cod_remittance = COD_transactions::select('cod_transactions.id','account_informations.account_holder_name', 'account_informations.account_number', 'account_informations.ifsc_code', DB::raw('sum(amount) as total_amount'), 'cod_transactions.datetime')
            ->join('account_informations', 'cod_transactions.seller_id', '=', 'account_informations.seller_id')
            ->where('cod_transactions.remitted_by', 'admin');
        if($request->filled('from_date'))
            $cod_remittance = $cod_remittance->whereDate('cod_transactions.datetime', '=', $request->from_date);
        else
            $cod_remittance = $cod_remittance->whereDate('cod_transactions.datetime', '=', date('Y-m-d'));
        $cod_remittance = $cod_remittance->groupBy('cod_transactions.seller_id')->latest('id')->paginate(10);
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['cod_remittance'] = $cod_remittance;
        $data['config'] = Configuration::find(1);
        return view('admin.cod_remittance_bank_logs', $data);
    }

    function ExportCodRemittanceByDate(Request $request)
    {
        $config = Configuration::find(1);
        $cod_remittance = COD_transactions::select('cod_transactions.id','account_informations.account_holder_name', 'account_informations.account_number', 'account_informations.ifsc_code', DB::raw('sum(amount) as total_amount'), 'cod_transactions.datetime')
            ->join('account_informations', 'cod_transactions.seller_id', '=', 'account_informations.seller_id')
            ->where('cod_transactions.remitted_by', 'admin');
        if($request->filled('hidden_date'))
            $cod_remittance = $cod_remittance->whereDate('cod_transactions.datetime', '=', $request->hidden_date);
        else
            $cod_remittance = $cod_remittance->whereDate('cod_transactions.datetime', '=', date('Y-m-d'));
        $cod_remittance = $cod_remittance->groupBy('cod_transactions.seller_id')->latest('id')->paginate(10);
        $name = "exports/Bank_cod_remittance";
        $filename = "Bank_cod_remittance";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'Sr.no',
            'PYMT_PROD_TYPE_CODE',
            'PYMT_MODE',
            'DEBIT_ACC_NO',
            'BNF_NAME',
            'BENE_ACC_NO',
            'BENE_IFSC',
            'AMOUNT',
            'DEBIT_NARR',
            'CREDIT_NARR',
            'MOBILE_NUM',
            'EMAIL_ID',
            'REMARK',
            'PYMT_DATE',
            'REF_NO',
            'ADDL_INFO1',
            'ADDL_INFO2',
            'ADDL_INFO3',
            'ADDL_INFO4',
            'ADDL_INFO5'
        );
        fputcsv($fp, $info);
        $cnt=1;
        foreach ($cod_remittance as $e) {
            $info = array(
                $cnt,
                "PAB_VENDOR",
                "FT",
                $config->account_number,
                $e->account_holder_name,
                $e->account_number,
                $e->ifsc_code,
                $e->total_amount,
                "",
                "",
                "",
                "",
                "",
                date('d-m-Y',strtotime($e->datetime)),
                "",
                "",
                "",
                "",
                "",
                ""
            );
            fputcsv($fp, $info);
            $cnt++;
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

    function codRemittanceLogs(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['logs'] = FileUploadJobModel::where('job_name', 'cod_remittance_upload')->latest()->get();
        return view('admin.cod_remittance_logs', $data);
    }

    function codRemittanceError()
    {
        $job = FileUploadJob::getLastJob('cod_remittance_upload');
        if(empty($job)) {
            return response()->json([
                'status' => true,
                'message' => 'No error found',
                'data' => [],
            ]);
        }
        $job->logs = FileUploadJob::getJobLog($job->id);
        return response()->json([
            'status' => true,
            'message' => $job->remark,
            'data' => $job,
        ]);
    }

    function exportCodRemittanceError(Request $request)
    {
        $data = FileUploadJob::getJobLog($request->job_id, $request->status ?? null);
        $name = "exports/cod_remittance";
        $filename = "cod_remittance";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'Sr.no',
            'CRF Id',
            'AWB Number',
            'COD Amount',
            'Remitted Amount',
            'UTR Number',
            'Remark',
        );
        fputcsv($fp, $info);
        $cnt=1;
        foreach ($data as $e) {
            $info = array(
                $cnt,
                $e->crf_id,
                "`{$e->awb_number}`",
                $e->cod_amount,
                $e->remittance_amount,
                $e->utr_number,
                $e->remark,
            );
            fputcsv($fp, $info);
            $cnt++;
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

    function importCsvCodRemittance(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $totalRecords = 0;
                $success = 0;
                $alreadyUploaded = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $crf_id = '';
                $codTransIds = [];
                $redeemAmount = [];
                $skipSellers = [];
                // Create job
                $job = FileUploadJob::createJob('cod_remittance_upload');
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $totalRecords++;
                            $awbNumber = isset($fileop[2]) ? trim($fileop[2], '`') : "";
                            $orderDetail = Order::where('awb_number', trim($fileop[2], '`'))->first();
                            if(empty($orderDetail)){
                                // Create job log
                                FileUploadJob::createJobLog([
                                    'job_id' => $job->id,
                                    'awb_number' => $awbNumber,
                                    'cod_transactions_id' => null,
                                    'crf_id' => isset($fileop[1]) ? $fileop[1] : "",
                                    'cod_amount' => isset($fileop[3]) ? $fileop[3] : "",
                                    'remittance_amount' => isset($fileop[4]) ? $fileop[4] : "",
                                    'utr_number' => isset($fileop[5]) ? $fileop[5] : "",
                                    'status' => 'fail',
                                    'remark' => 'Order Not Found.',
                                ]);
                                continue;
                            }
                            $sellerDetail = Seller::find($orderDetail->seller_id);
                            if($sellerDetail->is_bulk_ship_running == 1 || in_array($sellerDetail->id,$skipSellers)){
                                if(!in_array($sellerDetail->id,$skipSellers))
                                    $skipSellers[]=$sellerDetail->id;
                                FileUploadJob::createJobLog([
                                    'job_id' => $job->id,
                                    'awb_number' => $awbNumber,
                                    'cod_transactions_id' => null,
                                    'crf_id' => isset($fileop[1]) ? $fileop[1] : "",
                                    'cod_amount' => isset($fileop[3]) ? $fileop[3] : "",
                                    'remittance_amount' => isset($fileop[4]) ? $fileop[4] : "",
                                    'utr_number' => isset($fileop[5]) ? $fileop[5] : "",
                                    'status' => 'fail',
                                    'remark' => 'Bulk ship running for seller',
                                ]);
                                continue;
                            }
                            if($orderDetail->cod_remmited == 'y'){
                                // Create job log
                                FileUploadJob::createJobLog([
                                    'job_id' => $job->id,
                                    'awb_number' => $awbNumber,
                                    'cod_transactions_id' => $codTransIds[$orderDetail->seller_id] ?? null,
                                    'crf_id' => isset($fileop[1]) ? $fileop[1] : "",
                                    'cod_amount' => isset($fileop[3]) ? $fileop[3] : "",
                                    'remittance_amount' => isset($fileop[4]) ? $fileop[4] : "",
                                    'utr_number' => isset($fileop[5]) ? $fileop[5] : "",
                                    'status' => 'fail',
                                    'remark' => 'Awb number already uploaded.',
                                ]);
                                $alreadyUploaded++;
                                continue;
                            }
                            if(!isset($codTransIds[$orderDetail->seller_id])){
                                $data = array(
                                    'datetime' => date('Y-m-d H:i:s'),
                                    'seller_id' => $orderDetail->seller_id,
                                    'description' => 'COD Remitted to '.($request->cod_mode == 'wallet' ? "Wallet" : "Bank"),
                                    'mode' => $request->cod_mode,
                                    'type' => 'd',
                                    'remitted_by' => 'admin'
                                );
                                $codTransIds[$orderDetail->seller_id] = COD_transactions::create($data)->id;
                            }
                            if(!isset($redeemAmount[$orderDetail->seller_id])) {
                                $redeemAmount[$orderDetail->seller_id] = [
                                    'UTR' => $fileop[5] ?? "",
                                    'total' => 0,
                                    'seller_id' => $orderDetail->seller_id
                                ];
                            }
                            $data_remmitance = array(
                                'cod_transactions_id' => $codTransIds[$orderDetail->seller_id],
                                'crf_id' => isset($fileop[1]) ? $fileop[1] : "",
                                'awb_number' => isset($fileop[2]) ? trim($fileop[2], '`') : "",
                                'cod_amount' => isset($fileop[3]) ? $fileop[3] : "",
                                'remittance_amount' => isset($fileop[4]) ? $fileop[4] : "",
                                'utr_number' => isset($fileop[5]) ? $fileop[5] : "",
                                'mode' => $request->cod_mode,
                            );
                            RemittanceDetails::create($data_remmitance);
                            if($request->cod_mode == "wallet"){
                                // code to add balance
                                $sellerDetail = Seller::find($orderDetail->seller_id);
                                $data = array(
                                    'seller_id' => $orderDetail->seller_id,
                                    'order_id' => $orderDetail->id,
                                    'amount' => $data_remmitance['remittance_amount'],
                                    'balance' => $sellerDetail->balance + $data_remmitance['remittance_amount'],
                                    'type' => 'c',
                                    'redeem_type' => 'w',
                                    'datetime' => date('Y-m-d H:i:s'),
                                    'method' => 'wallet',
                                    'description' => 'COD Remitted to wallet'
                                );
                                Transactions::create($data);
                                Seller::where('id', $sellerDetail->id)->increment('balance', $data['amount']);
                            }
                            Order::where('awb_number',$data_remmitance['awb_number'])->update(['cod_remmited' => 'y']);
                            $crf_id = $fileop[1];
                            $redeemAmount[$orderDetail->seller_id]['total']+=floatval($fileop[4]);
                            // Create job log
                            FileUploadJob::createJobLog([
                                'job_id' => $job->id,
                                'awb_number' => $awbNumber,
                                'cod_transactions_id' => $codTransIds[$orderDetail->seller_id],
                                'crf_id' => isset($fileop[1]) ? $fileop[1] : "",
                                'cod_amount' => isset($fileop[3]) ? $fileop[3] : "",
                                'remittance_amount' => isset($fileop[4]) ? $fileop[4] : "",
                                'utr_number' => isset($fileop[5]) ? $fileop[5] : "",
                                'status' => 'success',
                                'remark' => 'Data uploaded successfully.',
                            ]);
                            $success++;
                        }
                    }
                    $cnt++;
                }
                foreach ($redeemAmount as $total)
                {
                    $sellerId = $total['seller_id'];
                    COD_transactions::where('id', $codTransIds[$sellerId])->update(['amount' => $total['total'], 'utr_number' => $total['UTR'], 'crf_id' => $crf_id, 'seller_id' => $total['seller_id']]);
                    Seller::where('id', $sellerId)->decrement('cod_balance', $total['total']);
                }
                if($success == $totalRecords) {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Cod remittance updated successfully';
                } else if($success == 0 && $alreadyUploaded == 0) {
                    $type = 'error';
                    $title = 'Error';
                    $message = 'Cod remittance not updated';
                } else {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $success . ' out of ' . $totalRecords . ' cod remittance and '.$alreadyUploaded.' already uploaded';
                }
                FileUploadJob::updateJob($job->id, [
                    'status' => $type == 'success' ? 'success' : 'fail',
                    'remark' => $message,
                    'total_records' => $totalRecords,
                    'success' => $success,
                    'failed' => ($totalRecords - $success),
                    'already_uploaded' => $alreadyUploaded,
                ]);
                $notification = array(
                    'notification' => array(
                        'type' => $type,
                        'title' => $title,
                        'message' => $message,
                    ),
                );
                Session($notification);
                return back();
            } else {
                $notification = array(
                    'notification' => array(
                        'type' => 'error',
                        'title' => 'Error',
                        'message' => 'Invalid File Uploaded',
                    ),
                );
                Session($notification);
            }
        } else {
            $notification = array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Please Upload file',
                ),
            );
            Session($notification);
        }
        return back();
    }

    function customerSupport(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['sellers'] = Seller::all();
        $customer_support = DB::table('support_ticket')
            ->join('sellers', 'support_ticket.seller_id', '=', 'sellers.id')
            ->select('sellers.code','sellers.email','support_ticket.*');
        if($request->filled('from_date')) {
            $customer_support = $customer_support->whereDate('raised', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $customer_support = $customer_support->whereDate('raised', '<=', $request->to_date);
        }
        if($request->filled('seller')) {
            $customer_support = $customer_support->whereIn('seller_id', $request->seller);
        }
        $customer_support = $customer_support->get();
        $data['customer_support'] = $customer_support;
        return view('admin.customer_support', $data);
    }

    function export_escalation(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $name = "exports/escalation-$from_date-$to_date";
        $filename = "escalation-$from_date-$to_date";
        $customer_support = DB::table('support_ticket')
            ->join('sellers', 'support_ticket.seller_id', '=', 'sellers.id')
            ->select('sellers.code','sellers.email','support_ticket.*');
        if($request->filled('from_date')) {
            $customer_support = $customer_support->whereDate('raised', '>=', $request->from_date);
        }
        if($request->filled('to_date')) {
            $customer_support = $customer_support->whereDate('raised', '<=', $request->to_date);
        }
        if($request->filled('seller')) {
            $customer_support = $customer_support->whereIn('seller_id', $request->seller);
        }
        $customer_support = $customer_support->get();
        $fp = fopen("$name.csv", 'w');
        $fileContent = '';
        $info = array('code', 'email', 'ticket_no', 'type', 'subject', 'description', 'issue', 'awb_number', 'raised', 'last_replied', 'status', 'escalate_reason', 'sevierity');
        fputcsv($fp, $info);
        foreach ($customer_support as $e) {
            $info = array($e->code, $e->email, $e->ticket_no, $e->type, $e->subject, $e->description, $e->issue, $e->awb_number, $e->raised, $e->last_replied, $e->status, $e->escalate_reason, $e->sevierity);
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

    function view_escalation($id)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['escalation'] = SupportTicket::find($id);
        $data['comments'] = TicketComments::where('ticket_id', $id)->get();
        return view('admin.view_ticket', $data);
    }

    function escalateTicket(Request $request)
    {
        $old_sevierity = SupportTicket::select('sevierity')->where('id', $request->ticket_id)->first()->sevierity;
        if ($old_sevierity == 'Low') {
            $new_sevierity = 'Medium';
        } elseif ($old_sevierity == 'Medium') {
            $new_sevierity = 'High';
        } else {
            $new_sevierity = 'Critical';
        }
        $data = [
            'escalate_reason' => $request->escalate_reason,
            'sevierity' => $new_sevierity
        ];
        SupportTicket::where('id', $request->ticket_id)->update($data);
        //$this->utilities->generate_notification('Success', 'Ticket Escalate Successfully', 'success');
        return back();
    }

    function close_ticket($id)
    {
        SupportTicket::where('id', $id)->update(['status' => 'c']);
        //$this->utilities->generate_notification('Success', 'Ticket Closed Successfully', 'success');
    }

    function add_ticket_comment(Request $request)
    {
        $data['config'] = Configuration::find(1);
        $data = array(
            'ticket_id' => $request->ticket_id,
            'remark' => $request->remark,
            'replied_by' => $data['config']->title,
            'replied' => date('Y-m-d H:i:s')
        );
        $ticket_comment_id = TicketComments::create($data);
        $cnt = 1;
        if (isset($request->comment_attachment)) {
            foreach ($request->comment_attachment as $file) {
                $data_attachment = array(
                    'ticket_comment_id' => $ticket_comment_id->id,
                );
                $oName = $file->getClientOriginalName();
                $type = explode('.', $oName);
                $name = date('YmdHis') . "C_Attachment-$cnt." . $type[count($type) - 1];
                $filepath = "assets/seller/images/Attachment/$name";
                $file->move(public_path('assets/seller/images/Attachment/'), $name);
                $data_attachment['attachment'] = $filepath;
                CommentsAttachment::create($data_attachment);
                $cnt++;
            }
        }
        if (isset($request->ticket_status)) {
            SupportTicket::where('id', $request->ticket_id)->update(['status' => $request->ticket_status]);
            $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
            $data['customer_support'] = SupportTicket::all();
            return view('admin.customer_support', $data);
        }
        return redirect()->back();
    }

    function openReconciliation()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['open_reconciliation'] = SupportTicket::where('status', 'o')->get();
        return view('admin.open_reconciliation', $data);
    }

    function view_open_reconciliation($id)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['escalation'] = SupportTicket::find($id);
        $data['comments'] = TicketComments::where('ticket_id', $id)->get();
        return view('admin.view_open_reconciliation', $data);
    }


    function employee()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['employee'] = Admin_employee::all();
        $data['sellers'] = Seller::all();
        return view('admin.employee', $data);
    }

    function employee_insert(Request $request)
    {
        $data = array(
            'admin_id' => Session()->get('MyAdmin')->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'seller_ids' => $request->seller_ids != '' ? implode(',', $request->seller_ids) : '',
            'status' => 'y',
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/employee/images/$name";
            $request->image->move(public_path('assets/admin/employee/images/'), $name);
            $data['image'] = $filepath;
        }
        Admin_employee::create($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Employee added successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function employee_delete($id)
    {
        Admin_employee::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function employee_modify($id)
    {
        $response = Admin_employee::find($id);
        echo json_encode($response);
    }

    function employee_status(Request $request)
    {
        $data = array(
            'status' => $request->status
        );
        Admin_employee::where('id', $request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }

    function employee_update(Request $request)
    {
        $data = array(
            'id' => $request->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'seller_ids' => $request->seller_ids != '' ? implode(',', $request->seller_ids) : ''
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/employee/images/$name";
            $request->image->move(public_path('assets/admin/employee/images/'), $name);
            $data['image'] = $filepath;
        }
        Admin_employee::where('id', $request->id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Employee updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function add_seller_balance(Request $request)
    {
        $seller = Seller::find($request->seller_id);
        $data = array(
            'seller_id' => $request->seller_id,
            'amount' => $request->amount,
            'balance' => $seller->balance + $request->amount,
            'type' => 'c',
            'datetime' => date('Y-m-d H:i:s'),
            'description' => $request->description,
            'method' => 'wallet',
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );
        // dd($data);
        Transactions::create($data);
        Seller::where('id', $request->seller_id)->increment('balance', $data['amount']);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Seller Recharge Successfull.',
            ),
        );
        Session($notification);
        return back();
    }

    function deduct_seller_balance(Request $request)
    {
        $seller = Seller::find($request->seller_id);
        $data = array(
            'seller_id' => $request->seller_id,
            'amount' => $request->amount,
            'balance' => $seller->balance - $request->amount,
            'type' => 'd',
            'datetime' => date('Y-m-d H:i:s'),
            'description' => $request->description,
            'method' => 'wallet',
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );
        // dd($data);
        Transactions::create($data);
        Seller::where('id', $request->seller_id)->decrement('balance', $data['amount']);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Seller Balance Deducted Successful',
            ),
        );
        Session($notification);
        return back();
    }

    function servicable_pincode(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        if (!empty($request->pincode)) {
            if(isset($request->fm)){
                $data['pincodes'] = ServiceablePincodeFM::where('pincode', $request->pincode)->get();
                $data['fm'] = 1;
            }
            else
                $data['pincodes'] = ServiceablePincode::where('pincode', $request->pincode)->get();
        } else {
            $data['pincodes'] = [];
        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('admin.servicable_pincode', $data);
    }

    function order_report()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::where('status','y')->get();
        $data['partners'] = Partners::where('status','y')->get();
        return view('admin.order_report', $data);
    }

    function get_order_report(Request $request) {
        $from_date = $request->start_date;
        $to_date = $request->end_date;
        if (!empty($request->seller)) {
            if(is_array($request->seller)) {
                $request->seller = array_filter($request->seller, function($e) {
                    return !empty($e);
                });
            }
        }
        $query = DB::table('orders')
            ->join('sellers', 'orders.seller_id', '=', 'sellers.id')
            ->select(
                'orders.*',
                'sellers.company_name',
                'sellers.code'
            )
            ->where('orders.awb_number', '!=', '');

        if (!empty($request->awb_number)) {
            $order = array_map('trim', explode(',', $request->awb_number));
            if (!empty($order)) {
                $query = $query->whereIn('orders.awb_number', $order)
                    ->orWhereIn('orders.id', $order);
            }
        }
        else {

            if ($request->order_status == 'delivered')
                $query = $query->whereDate('orders.delivered_date', '>=', $from_date)->whereDate('orders.delivered_date', '<=', $to_date)->orderBy('orders.delivered_date', 'desc');
            else if ($request->order_status == 'rto')
                $query = $query->whereDate('orders.delivered_date', '>=', $from_date)->whereDate('orders.delivered_date', '<=', $to_date)->orderBy('orders.delivered_date', 'desc');
            else if ($from_date && $to_date)
                $query = $query->whereDate('orders.awb_assigned_date', '>=', $from_date)->whereDate('orders.awb_assigned_date', '<=', $to_date)->orderBy('orders.awb_assigned_date', 'desc');
            if (is_array($request->seller) && !empty($request->seller))
                $query = $query->whereIn('orders.seller_id', $request->seller);
            if (is_array($request->courier_partner) && !empty($request->courier_partner))
                $query = $query->whereIn('orders.courier_partner', $request->courier_partner);
            if ($request->order_type && $request->order_type != "0") {
                $query = $query->where('orders.order_type', $request->order_type);
            }
            if ($request->order_status && $request->order_status != "0") {
                if ($request->order_status == 'delivered')
                    $query = $query->where('orders.status', 'delivered')->where('orders.rto_status', 'n');
                else if ($request->order_status == 'ndr')
                    $query = $query->where('orders.ndr_status', 'y');
                else if ($request->order_status == 'rto')
                    $query = $query->where('orders.status', 'delivered')->where('orders.rto_status', 'y');
                else if ($request->order_status == 'shipped')
                    $query = $query->where('orders.status', '!=', 'pending')->where('orders.status', '!=', 'cancelled');
                else
                    $query = $query->where('orders.status', $request->order_status);
            }
        }
        $all_data= $query->get();
        return view('admin.order_report_data', ['all_data' => $all_data]);
    }

    function get_order(Request $request) {
        $order = DB::table('orders')
            ->where('orders.id', $request->id)
            ->first();
        return response()->json($order);
    }

    function export_order_report(Request $request)
    {
//        $counter = DownloadOrderReportModel::where('status','processing')->count();

//        if($counter == 0){
//            $order_report = [
//                'report_name' => $request->orderRadio == 'ArchiveOrder' ? "Archive-OrderReport-$request->start_date/$request->end_date" : "OrderReport-$request->start_date/$request->end_date" ,
//                'payload' => json_encode([
//                    'table' => $request->orderRadio,
//                    'from_date' => $request->start_date,
//                    'to_date' => $request->end_date,
//                    'seller' => $request->seller,
//                    'order_type' => $request->order_type,
//                    'order_status' => $request->order_status,
//                    'courier_partner' => $request->courier_partner,
//                    'awb_number' => $request->awb_number,
//                ]),
//                'status' => "processing",
//                'created_at' => date('Y-m-d H:i:s'),
//            ];
//            $id =  DownloadOrderReportModel::create($order_report)->id;
//
//            //dispatchAfterResponse((new DownloadOrderReport($id))->onQueue('high'));
//            DownloadOrderReport::dispatchAfterResponse($id);
//            $notification = array(
//                'notification' => array(
//                    'type' => 'success',
//                    'title' => 'Success',
//                    'message' => 'Your report will be generated soon.',
//                ),
//            );
//            Session($notification);
//            return back();
//        }
//        else{
//            $notification = array(
//                'notification' => array(
//                    'type' => 'error',
//                    'title' => 'Failed',
//                    'message' => 'Please wait until the previous report generated',
//                ),
//            );
//            Session($notification);
//            return back();
//        }
        try {
            $orderRadio = "order";//$request->orderRadio;
            if ($orderRadio == "ArchiveOrder") {
                $from_date = $request->start_date ?? '';
                $to_date = $request->end_date ?? '';
                $name = "exports/OrderReport-$from_date-$to_date";
                $filename = "OrderReport-$from_date-$to_date";
                if (!empty($request->seller)) {
                    if (is_array($request->seller)) {
                        $request->seller = array_filter($request->seller, function ($e) {
                            return !empty($e);
                        });
                    }
                }
                $query = ZZArchiveOrder::leftJoin("weight_reconciliation", 'zz_archive_orders.awb_number', '=', 'weight_reconciliation.awb_number')->join('sellers', 'zz_archive_orders.seller_id', '=', 'sellers.id')
                    ->select(
                        'sellers.code',
                        'sellers.company_name',
                        'zz_archive_orders.rto_status',
                        'zz_archive_orders.status',
                        'zz_archive_orders.order_type',
                        'zz_archive_orders.awb_number',
                        'zz_archive_orders.p_state',
                        'zz_archive_orders.p_city',
                        'zz_archive_orders.p_pincode',
                        'zz_archive_orders.pickup_time',
                        'zz_archive_orders.awb_assigned_date',
                        'zz_archive_orders.delivered_date',
                        'zz_archive_orders.customer_order_number',
                        'zz_archive_orders.courier_partner',
                        'zz_archive_orders.order_type',
                        'zz_archive_orders.s_pincode',
                        'zz_archive_orders.s_state',
                        'zz_archive_orders.s_city',
                        'zz_archive_orders.zone',
                        'zz_archive_orders.invoice_amount',
                        'zz_archive_orders.weight',
                        'zz_archive_orders.length',
                        'zz_archive_orders.breadth',
                        'zz_archive_orders.height',
                        'zz_archive_orders.product_name',
                        'zz_archive_orders.shipping_charges',
                        'zz_archive_orders.rto_charges',
                        'zz_archive_orders.cod_charges',
                        'zz_archive_orders.total_charges',
                        'zz_archive_orders.last_sync',
                        'zz_archive_orders.rto_status',
                        'zz_archive_orders.inserted'
                    );
                if (!empty($from_date) && !empty($to_date)) {
                    if ($request->order_status == 'delivered')
                        $query = $query->whereDate('zz_archive_orders.delivered_date', '>=', $from_date)->whereDate('zz_archive_orders.delivered_date', '<=', $to_date)->orderBy('zz_archive_orders.delivered_date', 'desc');
                    else if ($request->order_status == 'rto')
                        $query = $query->whereDate('zz_archive_orders.delivered_date', '>=', $from_date)->whereDate('zz_archive_orders.delivered_date', '<=', $to_date)->orderBy('zz_archive_orders.delivered_date', 'desc');
                    else {
                        $query = $query->whereDate('zz_archive_orders.awb_assigned_date', '>=', $from_date)->whereDate('zz_archive_orders.awb_assigned_date', '<=', $to_date)->orderBy('zz_archive_orders.awb_assigned_date', 'desc');
                    }
                }
                if (is_array($request->seller) && !empty($request->seller))
                    $query = $query->whereIn('zz_archive_orders.seller_id', $request->seller);
                if (is_array($request->courier_partner) && !empty($request->courier_partner))
                    $query = $query->whereIn('zz_archive_orders.courier_partner', $request->courier_partner);
                if ($request->order_type != "0") {
                    $query = $query->where('zz_archive_orders.order_type', $request->order_type);
                }
                if ($request->order_status != "0") {
                    if ($request->order_status == 'delivered')
                        $query = $query->where('zz_archive_orders.status', 'delivered')->where('zz_archive_orders.rto_status', 'n');
                    else if ($request->order_status == 'ndr')
                        $query = $query->where('zz_archive_orders.ndr_status', 'y');
                    else if ($request->order_status == 'rto')
                        $query = $query->where('zz_archive_orders.status', 'delivered')->where('zz_archive_orders.rto_status', 'y');
                    else if ($request->order_status == 'shipped')
                        $query = $query->where('zz_archive_orders.status', '!=', 'pending')->where('zz_archive_orders.status', '!=', 'cancelled');
                    else
                        $query = $query->where('zz_archive_orders.status', $request->order_status);
                }
                if ($request->awb_number) {
                    $order = array_map('trim', explode(',', $request->awb_number));
                    if (!empty($order)) {
                        $query = $query->whereIn('zz_archive_orders.awb_number', $order)
                            ->orWhereIn('zz_archive_orders.id', $order);
                    }
                }
            } else {
                $from_date = $request->start_date ?? '';
                $to_date = $request->end_date ?? '';
                $name = "exports/OrderReport-$from_date-$to_date";
                $filename = "OrderReport-$from_date-$to_date";
                if (!empty($request->seller)) {
                    if (is_array($request->seller)) {
                        $request->seller = array_filter($request->seller, function ($e) {
                            return !empty($e);
                        });
                    }
                }
                // $all_data = Order::where('awb_number','!=','')->whereDate('awb_assigned_date', '>=', $from_date)->whereDate('awb_assigned_date', '<=', $to_date)->orderBy('awb_assigned_date','desc')->get();
                $query = Order::leftJoin("weight_reconciliation", 'orders.awb_number', '=', 'weight_reconciliation.awb_number')->join('sellers', 'orders.seller_id', '=', 'sellers.id')
                    ->select(
                        'sellers.code',
                        'sellers.company_name',
                        'orders.rto_status',
                        'orders.status',
                        'orders.order_type',
                        'orders.awb_number',
                        'orders.p_state',
                        'orders.p_city',
                        'orders.p_pincode',
                        'orders.pickup_time',
                        'orders.awb_assigned_date',
                        'orders.delivered_date',
                        'orders.customer_order_number',
                        'orders.courier_partner',
                        'orders.order_type',
                        'orders.s_pincode',
                        'orders.s_state',
                        'orders.s_city',
                        'orders.s_customer_name',
                        'orders.s_contact',
                        'orders.s_address_line1',
                        'orders.s_address_line2',
                        'orders.zone',
                        'orders.invoice_amount',
                        'orders.weight',
                        'orders.length',
                        'orders.breadth',
                        'orders.height',
                        'orders.product_name',
                        'orders.shipping_charges',
                        'orders.rto_charges',
                        'orders.cod_charges',
                        'orders.total_charges',
                        'orders.last_sync',
                        'orders.rto_status',
                        'weight_reconciliation.charged_amount'
                    )
                    ->where('orders.awb_number', '!=', '');


                if (!empty($from_date) && !empty($to_date)) {
                    if ($request->order_status == 'delivered')
                        $query = $query->whereDate('orders.delivered_date', '>=', $from_date)->whereDate('orders.delivered_date', '<=', $to_date)->orderBy('orders.delivered_date', 'desc');
                    else if ($request->order_status == 'rto')
                        $query = $query->whereDate('orders.delivered_date', '>=', $from_date)->whereDate('orders.delivered_date', '<=', $to_date)->orderBy('orders.delivered_date', 'desc');
                    else
                        $query = $query->whereDate('orders.awb_assigned_date', '>=', $from_date)->whereDate('orders.awb_assigned_date', '<=', $to_date)->orderBy('orders.awb_assigned_date', 'desc');
                }
                if (is_array($request->seller) && !empty($request->seller))
                    $query = $query->whereIn('orders.seller_id', $request->seller);
                if (is_array($request->courier_partner) && !empty($request->courier_partner))
                    $query = $query->whereIn('orders.courier_partner', $request->courier_partner);
                if ($request->order_type != "0") {
                    $query = $query->where('orders.order_type', $request->order_type);
                }
                if ($request->order_status != "0") {
                    if ($request->order_status == 'delivered')
                        $query = $query->where('orders.status', 'delivered')->where('orders.rto_status', 'n');
                    else if ($request->order_status == 'ndr')
                        $query = $query->where('orders.ndr_status', 'y');
                    else if ($request->order_status == 'rto')
                        $query = $query->where('orders.status', 'delivered')->where('orders.rto_status', 'y');
                    else if ($request->order_status == 'shipped')
                        $query = $query->where('orders.status', '!=', 'pending')->where('orders.status', '!=', 'cancelled');
                    else
                        $query = $query->where('orders.status', $request->order_status);
                }
                if ($request->awb_number) {
                    $order = array_map('trim', explode(',', $request->awb_number));
                    if (!empty($order)) {
                        $query = $query->whereIn('orders.awb_number', $order)
                            ->orWhereIn('orders.id', $order);
                    }
                }
            }
            //dd($all_data);
            $fp = fopen("$name.csv", 'w');
            $fileContent = '';
            // $info = array('AWB Number','Courier Partner','Weight (Kg)','Length (CM)','Breadth (CM)','Height (CM)','Shipping Charges','COD Charges','RTO Charges','Total Charges','Zone','Status');
            $info = array('Pickup State', 'Pickup City', 'Pickup Pincode', 'Seller', 'Seller Code', 'Pickup Date', 'Shipping Date', 'Delivered Date', 'Order Id', 'AWB Number', 'Courier Partner', 'Payment Type', 'Customer Name', 'Customer Mobile', 'Ship Address', 'Ship Pincode', 'Ship State', 'Ship City', 'Zone', 'Invoice Amount', 'Billing Weight (Kg)', 'Dimension(L * H * H)(CM)', 'Product Name', 'Current Status', 'Collectable Value', 'Freight Charges', 'RTO Charges', 'COD Charges', 'Total Charges', 'AWB Assigned Date', 'Last Sync', 'Final Charge', 'Number of NDR attempts', 'First NDR raised date', 'First NDR raised time', 'First NDR Action By', 'Reason for First NDR', 'Action date for First NDR', 'Action Status for First NDR', 'Remarks for First NDR', 'First Updated Address Line 1', 'First Updated Address Line 1', 'First Updated Mobile', 'Second NDR raised date', 'Second NDR raised time', 'Second NDR Action By', 'Reason for Second NDR', 'Action date for Second NDR', 'Action Status for Second NDR', 'Remarks for Second NDR', 'Second Updated Address Line 1', 'Second Updated Address Line 1', 'Second Updated Mobile', 'Third NDR raised date', 'Third NDR raised time', 'Third NDR Action By', 'Reason for Third NDR', 'Action date for Third NDR', 'Action Status for Third NDR', 'Remarks for Third NDR', 'Third Updated Address Line 1', 'Third Updated Address Line 1', 'Third Updated Mobile');
            fputcsv($fp, $info);
            $fileContent.=implode(',',$info)."\r";
            $cnt = 1;
            $orderStatus = $this->orderStatus;
            $PartnerName = Partners::getPartnerKeywordList();
            $query->with('ndrattempts')->chunk(90000, function ($all_data) use (&$fileContent,$fp, $orderStatus, $PartnerName) {
                try {
                    foreach ($all_data as $e) {
                        try {
                            $awbNumbers = $e->awb_number;
                            $finalCharge = 0;
                            if (strtolower($e->o_type) == 'forward') {
                                if ($e->status == 'delivered' && $e->rto_status == 'n') {
                                    //Delivered, prepaid with wt. Dispute :  Forward + Wt. Dispute
                                    if (strtolower($e->order_type) == 'prepaid') {
                                        if ($e->weight_disputed == 'y')
                                            $finalCharge = round($e->shipping_charges) + round($e->charged_amount);
                                        else
                                            $finalCharge = round($e->shipping_charges);
                                    } //Delivered, COD with wt. Dispute  : Forward + COD + Wt. Dispute
                                    elseif (strtolower($e->order_type) == 'cod') {
                                        if ($e->weight_disputed == 'y')
                                            $finalCharge = round($e->shipping_charges) + round($e->cod_charges) + round($e->charged_amount);
                                        else
                                            $finalCharge = round($e->shipping_charges) + round($e->cod_charges);
                                    }
                                } elseif ($e->status == 'delivered' && $e->rto_status == 'y') {
                                    //RTO prepaid with wt. Dispute : Forward + RTO + Wt. Dispute
                                    if (strtolower($e->order_type) == 'prepaid') {
                                        if ($e->weight_disputed == 'y')
                                            $finalCharge = round($e->shipping_charges) + round($e->rto_charges) + round($e->charged_amount);
                                        else
                                            $finalCharge = round($e->shipping_charges) + round($e->rto_charges);
                                    } //RTO COD with wt. Dispute  : Forward + RTO - COD + Wt. Dispute
                                    elseif (strtolower($e->order_type) == 'cod') {
                                        if ($e->weight_disputed == 'y')
                                            $finalCharge = (round($e->shipping_charges) + round($e->rto_charges) + round($e->charged_amount)) - round($e->cod_charges);
                                        else
                                            $finalCharge = (round($e->shipping_charges) + round($e->rto_charges)) - round($e->cod_charges);
                                    }
                                } else {
                                    if (strtolower($e->order_type) == 'prepaid')
                                        $finalCharge = round($e->shipping_charges);
                                    elseif (strtolower($e->order_type) == 'cod') {
                                        $finalCharge = round($e->shipping_charges) + round($e->cod_charges);
                                    }
                                }
                            }
                            if ($e->rto_status == 'y' && $e->status == 'delivered')
                                $e->status = 'rto_delivered';
                            // $qty = Product::where('order_id', $e->id)->sum('product_qty');
                            if (strtolower($e->order_type) == 'cod')
                                $collectable_value = $e->invoice_amount;
                            else
                                $collectable_value = "0";
                            $e->product_name = str_replace(",", "|", $e->product_name);
                            $attempts = $e->ndrattempts;
                            $weight = $e->weight;

                            if (is_int($e->weight) || is_float($e->weight) || !empty($e->weight))
                                $weight = $e->weight / 1000;

                            $info = array(addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->p_state)))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->p_city)))), preg_replace('/\s+/', ' ', trim($e->p_pincode)), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->company_name)))), preg_replace('/\s+/', ' ', trim($e->code)), $e->pickup_time, $e->awb_assigned_date, $e->delivered_date, addslashes(preg_replace('/\s+/', ' ', preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->customer_order_number))))), ('`' . $e->awb_number . '`'), $PartnerName[$e->courier_partner] ?? "NA", $e->order_type, $e->s_customer_name, $e->s_contact, addslashes(preg_replace('/\s+/', ' ', trim(str_replace(".", "", str_replace(",", " ", $e->s_address_line1))))) ." ".addslashes(preg_replace('/\s+/', ' ', trim(str_replace(".", "", str_replace(",", " ", $e->s_address_line2))))), preg_replace('/\s+/', ' ', trim($e->s_pincode)), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(".", "", str_replace(",", "|", $e->s_state))))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_city)))), $e->zone, $e->invoice_amount, $weight, ($e->length . ' * ' . $e->breadth . ' * ' . $e->height), preg_replace('/\s+/', ' ', trim($e->product_name)), addslashes($orderStatus[$e->status]), $collectable_value, $e->shipping_charges, $e->rto_charges, $e->cod_charges, $e->total_charges, $e->awb_assigned_date, $e->last_sync, count($e->ndrattempts) + 1);

                            foreach ($attempts as $a) {
                                try {
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->raised_date));
                                    $info[] = $a->raised_time;
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->action_by));
                                    $info[] = str_replace(",", "|", $a->reason);
                                    $info[] = $a->action_date;
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->action_status));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->remark))));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->u_address_line1))));
                                    $info[] = addslashes(str_replace(",", "|", $a->u_address_line2));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->updated_mobile))));
                                }catch(\Exception $e){
                                    dd($e->getMessage(),$e->getLine(),$awbNumbers);
                                }
                            }
                            fputcsv($fp, $info);
                        }catch(\Exception $e){
                            dd($e->getMessage(),$e->getLine(),$awbNumbers);
                        }
                    }
                }catch(\Exception $e){
                    dd($e->getMessage(),$e->getLine(),$awbNumbers);
                }
            });
//            $zipFile = "exports/Order-Report-testing.zip";
//            $zip = new \ZipArchive();
//            if ($zip->open($zipFile, \ZipArchive::CREATE) == true) {
//                $zip->addFromString("$name.csv",$fileContent);
//            }
//            $zip->close();
//            $file = "assets/report/Order-Report-testing.zip";
//            if (file_exists($zipFile)) {
//                try {
//                    copy($zipFile, $file);
//                    @unlink($zipFile);
//                    @unlink($file);
//                }catch(\Exception $e){
//                    dd($e->getMessage(),$e->getLine());
//                }
//            }
        }catch(\Exception $e){
            dd($e->getMessage(),$e->getLine());
        }
//
        //fwrite($fp, $fileContent);

        //fclose($file);
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$filename.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function export_order_manually()
    {
        $name = "exports/OrderReport";
        $filename = "OrderReport";
        $all_data = DB::table('orders')->join('sellers', 'orders.seller_id', '=', 'sellers.id')->select('orders.*', 'sellers.first_name', 'sellers.last_name', 'sellers.code')->where('orders.awb_number', '!=', '')->whereIn('awb_number', ['14345021202118',
            '14345021202261',
            '14345021202070',
            '14345021202123',
            '14345021202084',
            '14345021202126',
            '14345021202260',
            '14345021202105',
            '14345021202081',
            '14345021202538',
            '14345021202539',
            '14345021202275',
            '14345021201760'])->get();
        $fp = fopen("$name.csv", 'w');
        // $info = array('AWB Number','Courier Partner','Weight (Kg)','Length (CM)','Breadth (CM)','Height (CM)','Shipping Charges','COD Charges','RTO Charges','Total Charges','Zone','Status');
        $info = array('Pickup State', 'Pickup City', 'Pickup Pincode', 'Seller Name', 'Seller Code', 'Pickup Date', 'Shipping Date', 'Delivered Date', 'Order Id', 'AWB Number', 'Courier Partner', 'Payment Type', 'Ship Pincode', 'Ship State', 'Ship City', 'Zone', ' Billing Weight (Kg)', 'Dimension(L * H * H)(CM)', 'Prouct Name', 'Product Quantity', 'Current Status', 'Collectable Value', 'Freight Charges', 'RTO Charges', 'COD Charges', 'Total Charges');
        fputcsv($fp, $info);
        $cnt = 1;
        $orderStatus = $this->orderStatus;
        $PartnerName = Partners::getPartnerKeywordList();
        foreach ($all_data as $e) {
            if($e->rto_status == 'y' && $e->status=='delivered')
                $e->status='rto_delivered';
            $qty = Product::where('order_id', $e->id)->sum('product_qty');
            if (strtolower($e->order_type) == 'cod')
                $collectable_value = $e->invoice_amount;
            else
                $collectable_value = "0";
            $info = array($e->p_state, $e->p_city, $e->p_pincode, ($e->first_name . ' ' . $e->last_name), $e->code, $e->pickup_time, $e->awb_assigned_date, $e->delivered_date, $e->customer_order_number, ('`' . $e->awb_number . '`'), $PartnerName[$e->courier_partner], $e->order_type, $e->s_pincode, $e->s_state, $e->s_city, $e->zone, ($e->weight / 1000), ($e->length . ' * ' . $e->breadth . ' * ' . $e->height), $e->product_name, $qty, $orderStatus[$e->status] ?? "Not Found", $collectable_value, $e->shipping_charges, $e->rto_charges, $e->cod_charges, $e->total_charges);
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

    function f_seller_invoice(Request $request)
    {
        // dd($request->all());
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::all();
        session(['f_seller_id' => $request->seller_id, 'f_from_date' => $request->from_date, 'f_to_date' => $request->to_date]);
        if (!empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['invoice'] = DB::table('invoice')->join('sellers', 'invoice.seller_id', '=', 'sellers.id')->where('invoice.seller_id', $request->seller_id)->whereDate('invoice.invoice_date', '>=', $request->from_date)->whereDate('invoice.invoice_date', '<=', $request->to_date)->select('invoice.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('invoice.invoice_date', 'desc')->get();
        } elseif (empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['invoice'] = DB::table('invoice')->join('sellers', 'invoice.seller_id', '=', 'sellers.id')->whereDate('invoice.invoice_date', '>=', $request->from_date)->whereDate('invoice.invoice_date', '<=', $request->to_date)->select('invoice.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('invoice.invoice_date', 'desc')->get();
        } else {
            $data['display'] = 'd-none';
            $data['invoice'] = [];
        }
        return view('admin.f_seller_invoice', $data);
    }

    function BillingInvoiceView($id)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['invoice'] = Invoice::find($id);
        $data['config'] = Configuration::find(1);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::where('seller_id', $data['invoice']->seller_id)->first();
        return view('admin.billing_invoice', $data);
    }

    function BillingInvoicePDF($id)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['invoice'] = Invoice::find($id);
        $data['config'] = Configuration::find(1);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::where('seller_id', $data['invoice']->seller_id)->first();
        $pdf = PDF::loadView('admin.billing_invoice_pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Billing_Invoice-' . $data['invoice']->id . '.pdf');
        // return view('seller.billing_invoice', $data);
    }

    function BillingInvoiceCSV($id)
    {
        $name = "invoice_details";
        $config = Configuration::find(1);
        $all_data = DB::table('invoice_orders')->join('orders', 'invoice_orders.order_id', '=', 'orders.id')->select('invoice_orders.*', 'orders.*')->where('invoice_id', $id)->get();
        $PartnerName = Partners::getPartnerKeywordList();
        $fp = fopen("$name.csv", 'w');
        $info = array('AWB Number', 'Courier Partner', 'Billed Weight (In KG)', 'Charged Weight (In KG)', 'SGST (In %)', 'CGST (In %)', 'IGST (In %)', 'Freight Charges', 'COD Charges', 'RTO Charges', 'Billing Amount', 'Shipping Address Line 1', 'Shipping Address Line 2', 'Shipping City', 'Shipping State', 'Shipping Pincode', 'Shipping Country', 'Shipping Method', 'Description');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $gst = $this->_gstcheck($e->s_state, $e->p_state);
            if ($gst == 'igst') {
                $sgst = "";
                $cgst = "";
                $igst = $config->gst_percent;
            } else {
                $sgst = $config->gst_percent / 2;
                $cgst = $config->gst_percent / 2;
                $igst = "";
            }
            $info = array(('`' . $e->awb_number . '`'), $PartnerName[$e->courier_partner], $e->weight / 1000, $e->weight / 1000, $sgst, $cgst, $igst, $e->shipping_charges, $e->cod_charges, $e->rto_status == 'y' ? $e->rto_status : '0', $e->total_charges, $e->s_address_line1, $e->s_address_line2, $e->s_city, $e->s_state, $e->s_pincode, $e->s_country, $e->order_type, $e->rto_status != 'y' ? 'Forward charges' : 'RTO charges');
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename={$name}.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function _gstcheck($p_state, $s_state)
    {
        if (strtolower($p_state) == strtolower($s_state)) {
            return 'sgst_cgst';
        } else {
            return 'igst';
        }
    }

    function export_billing_invoice_data()
    {
        $seller_id = session('f_seller_id');
        $from_date = session('f_from_date');
        $to_date = session('f_to_date');
        $name = "exports/SellerInvoice$from_date-$to_date";
        $filename = "SellerInvoice$from_date-$to_date";
        $config = Configuration::find(1);
        if (!empty($seller_id) && !empty($from_date) && !empty($to_date)) {
            $all_data = DB::table('invoice')->join('sellers', 'invoice.seller_id', '=', 'sellers.id')->where('invoice.seller_id', $seller_id)->whereDate('invoice.invoice_date', '>=', $from_date)->whereDate('invoice.invoice_date', '<=', $to_date)->select('invoice.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('invoice.invoice_date', 'desc')->get();
        } elseif (empty($seller_id) && !empty($from_date) && !empty($to_date)) {
            $all_data = DB::table('invoice')->join('sellers', 'invoice.seller_id', '=', 'sellers.id')->whereDate('invoice.invoice_date', '>=', $from_date)->whereDate('invoice.invoice_date', '<=', $to_date)->select('invoice.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('invoice.invoice_date', 'desc')->get();
        }
        // dd($all_data);
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.no', 'Seller Code', 'Seller Name', 'Invoice Id', 'Invoice Date', 'Due Date', 'Total Amount');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $key => $e) {

            $info = array(++$key, $e->code, $e->first_name . ' ' . $e->last_name, $e->inv_id, $e->invoice_date, $e->due_date, $e->total);
            fputcsv($fp, $info);
            $cnt++;
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

    function f_seller_recharge(Request $request)
    {
        // dd($request->all());
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::all();
        session(['r_seller_id' => $request->seller_id, 'r_from_date' => $request->from_date, 'r_to_date' => $request->to_date]);
        if (!empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['recharge'] = DB::table('transactions')->join('sellers', 'sellers.id', '=', 'transactions.seller_id')->where('transactions.redeem_type', 'r')->where('transactions.seller_id', $request->seller_id)->whereDate('transactions.datetime', '>=', $request->from_date)->whereDate('transactions.datetime', '<=', $request->to_date)->select('transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('transactions.datetime', 'desc')->get();
        } elseif (empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['recharge'] = DB::table('transactions')->join('sellers', 'sellers.id', '=', 'transactions.seller_id')->where('transactions.redeem_type', 'r')->whereDate('transactions.datetime', '>=', $request->from_date)->whereDate('transactions.datetime', '<=', $request->to_date)->select('transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('transactions.datetime', 'desc')->get();
        } else {
            $data['display'] = 'd-none';
            $data['recharge'] = [];
        }
        return view('admin.f_seller_recharge', $data);
    }

    function export_recharge_data()
    {
        $seller_id = session('r_seller_id');
        $from_date = session('r_from_date');
        $to_date = session('r_to_date');
        $name = "exports/SellerRecharge$from_date-$to_date";
        $filename = "SellerRecharge$from_date-$to_date";

        $selected_ids = request()->query('selected_ids', null);

        if (!empty($selected_ids)) {
            $all_data = DB::table('transactions')
                ->join('sellers', 'sellers.id', '=', 'transactions.seller_id')
                ->whereIn('transactions.id', explode(',', $selected_ids))
                ->select('transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')
                ->orderBy('transactions.datetime', 'desc')
                ->get();
        } else {
            if (!empty($seller_id) && !empty($from_date) && !empty($to_date)) {
                $all_data = DB::table('transactions')
                    ->join('sellers', 'sellers.id', '=', 'transactions.seller_id')
                    ->where('transactions.redeem_type', 'r')
                    ->where('transactions.seller_id', $seller_id)
                    ->whereDate('transactions.datetime', '>=', $from_date)
                    ->whereDate('transactions.datetime', '<=', $to_date)
                    ->select('transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')
                    ->orderBy('transactions.datetime', 'desc')
                    ->get();
            } else {
                $all_data = DB::table('transactions')
                    ->join('sellers', 'sellers.id', '=', 'transactions.seller_id')
                    ->where('transactions.redeem_type', 'r')
                    ->whereDate('transactions.datetime', '>=', $from_date)
                    ->whereDate('transactions.datetime', '<=', $to_date)
                    ->select('transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')
                    ->orderBy('transactions.datetime', 'desc')
                    ->get();
            }
        }

        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.no', 'Seller Code', 'Seller Name', 'Transaction Id', 'Transaction Date', 'Amount', 'Description');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $key => $e) {
            $info = array(++$key, $e->code, $e->first_name . ' ' . $e->last_name, $e->id, $e->datetime, $e->amount, $e->description);
            fputcsv($fp, $info);
            $cnt++;
        }

        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$filename.csv");
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function f_seller_remittance(Request $request)
    {
        // dd($request->all());
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::all();
        session(['rm_seller_id' => $request->seller_id, 'rm_from_date' => $request->from_date, 'rm_to_date' => $request->to_date]);
        if (!empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['remittance'] = DB::table('cod_transactions')->join('sellers', 'sellers.id', '=', 'cod_transactions.seller_id')->where('cod_transactions.redeem_type', 'r')->where('cod_transactions.seller_id', $request->seller_id)->whereDate('cod_transactions.datetime', '>=', $request->from_date)->whereDate('cod_transactions.datetime', '<=', $request->to_date)->select('cod_transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('cod_transactions.datetime', 'desc')->get();
        } elseif (empty($request->seller_id) && !empty($request->from_date) && !empty($request->to_date)) {
            $data['display'] = '';
            $data['remittance'] = DB::table('cod_transactions')->join('sellers', 'sellers.id', '=', 'cod_transactions.seller_id')->where('cod_transactions.redeem_type', 'r')->whereDate('cod_transactions.datetime', '>=', $request->from_date)->whereDate('cod_transactions.datetime', '<=', $request->to_date)->select('cod_transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('cod_transactions.datetime', 'desc')->get();
        } else {
            $data['display'] = 'd-none';
            $data['remittance'] = [];
        }
        return view('admin.f_seller_remittance', $data);
    }

    function f_seller_remittance_export($id)
    {
        $name = "exports/Remmitance Details";
        $filename = "Remmitance Details";
        $all_data = RemittanceDetails::where('cod_transactions_id', $id)->get();
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.no', 'CRF Id', 'AWB Number', 'COD Amount', 'Remittance Amount', 'UTR Number');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $key => $e) {
            $info = array(++$key, $e->crf_id, ('`' . $e->awb_number . '`'), $e->cod_amount, $e->remittance_amount, $e->utr_number);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$name.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function export_remittance_report()
    {
        $seller_id = session('rm_seller_id');
        $from_date = session('rm_from_date');
        $to_date = session('rm_to_date');
        $name = "SellerRemittance$from_date-$to_date";
        if (!empty($seller_id) && !empty($from_date) && !empty($to_date)) {
            $all_data = DB::table('cod_transactions')->join('sellers', 'sellers.id', '=', 'cod_transactions.seller_id')->where('cod_transactions.redeem_type', 'r')->where('cod_transactions.seller_id', $seller_id)->whereDate('cod_transactions.datetime', '>=', $from_date)->whereDate('cod_transactions.datetime', '<=', $to_date)->select('cod_transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('cod_transactions.datetime', 'desc')->get();
        } else {
            $all_data = DB::table('cod_transactions')->join('sellers', 'sellers.id', '=', 'cod_transactions.seller_id')->where('cod_transactions.redeem_type', 'r')->whereDate('cod_transactions.datetime', '>=', $from_date)->whereDate('cod_transactions.datetime', '<=', $to_date)->select('cod_transactions.*', 'sellers.code', 'sellers.first_name', 'sellers.last_name')->orderBy('cod_transactions.datetime', 'desc')->get();
        }
        // dd($all_data);
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.no', 'Seller Code', 'Seller Name', 'Transaction Id', 'Transaction Date', 'Amount', 'Description');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $key => $e) {
            $info = array(++$key, $e->code, $e->first_name . ' ' . $e->last_name, $e->id, $e->datetime, $e->amount, $e->description);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$name.csv");
        // Output file.
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function generateAwb()
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['sellers'] = Seller::where('status', 'y')->where('verified', 'y')->get();
        $data['master'] = Master::all();
        $data['partners'] = Partners::where('keyword', 'like', '%xpressbees%')->orWhere('keyword', 'like', '%ekart%')->get();
        $data['awbs'] = DB::table('generated_awb')->join('sellers', 'sellers.id', '=', 'generated_awb.seller_id')->select('sellers.code as seller_code', 'generated_awb.*')->get();
        return view('admin.generate_awb', $data);
    }

    function generateSellerAwb(Request $request)
    {
        $generated = [
            'seller_id' => $request->seller,
            'date' => date('Y-m-d'),
            'no_of_awb' => $request->no_awb,
            'partner_id' => $request->partner,
            'inserted' => date('Y-m-d H:i:s')
        ];
        $generatedId = Generated_awb::create($generated)->id;
        if(in_array($request->partner, ['ekart', 'ekart_2kg'])) {
            $awbs = EkartAwbNumbers::where('courier_partner', $request->partner)->where('used', 'n')->where('assigned', 'n')->limit($request->no_awb)->get();
            $generatedAwbs = [];
            foreach ($awbs as $a) {
                $generatedAwbs[] = $a->awb_number;
            }
            EkartAwbNumbers::whereIn('awb_number', $generatedAwbs)->update(['assigned' => 'y', 'seller_id' => $request->seller, 'generated_id' => $generatedId, 'generated' => date('Y-m-d H:i:s')]);
        } else {
            $awbs = XbeesAwbnumber::where('courier_partner', $request->partner)->where('used', 'n')->where('assigned', 'n')->limit($request->no_awb)->get();
            $generatedAwbs = [];
            foreach ($awbs as $a) {
                $generatedAwbs[] = $a->awb_number;
            }
            XbeesAwbnumber::whereIn('awb_number', $generatedAwbs)->update(['assigned' => 'y', 'seller_id' => $request->seller, 'generated_id' => $generatedId, 'generated' => date('Y-m-d H:i:s')]);
        }
        $fp = fopen("GeneratedAwb.csv", 'w');
        $info = array('Sr.no', 'AWB Number');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($awbs as $a) {
            fputcsv($fp, [$cnt++, "`".$a->awb_number."`"]);
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("GeneratedAwb.csv"));
        header("Content-Disposition: attachment; filename=GeneratedAwb.csv");
        // Output file.
        readfile("GeneratedAwb.csv");
        @unlink("GeneratedAwb.csv");
    }
    function downloadGeneratedAwb($generatedID){
        $tmp = Generated_awb::where('id', $generatedID)->first();
        $data['awbs'] = [];
        if(!empty($tmp)) {
            if(in_array($tmp->partner_id, ['ekart', 'ekart_2kg'])) {
                $data['awbs'] = EkartAwbNumbers::where('generated_id', $generatedID)->get();
            } else {
                $data['awbs'] = XbeesAwbnumber::where('generated_id', $generatedID)->get();
            }
        }

        $pdf = PDF::loadView('admin.pdf.generated_awb', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Generated-' . $generatedID . '.pdf');

//        return view('admin.pdf.generated_awb',$data);
    }
    function importAwbNumbers()
    {
        $file = "GeneratedAwb.csv";
        $handle = fopen($file, "r");
        $cnt = 0;
        while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
            if ($cnt > 0) {
                if ($fileop[0] != "") {
                    echo trim($fileop[1],"`") . "<bR>";
                }
            }
            $cnt++;
        }
    }
    function fillAllWeightAndDimension(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::where('status','y')->get();
        return view('admin.fillWeight',$data);
    }
    function populateWeight(Request $request){
        $values = ['','0'];
        //DB::enableQueryLog();
        $orders = Order::whereIn('weight',$values)->where('status','pending');
        if($request->seller != "0")
            $orders = $orders->where('seller_id',$request->seller);
        $orders = $orders->orWhereNull('weight')->where('status','pending');
        if($request->channel != "0")
            $orders = $orders->where('channel',$request->channel);
        $orders = $orders->get();
        //dd($orders);
        foreach ($orders as $o){
            $product = Product::where('order_id',$o->id)->get();
            $dimension = [
                'weight' => 0,
                'length' => 0,
                'width' => 0,
                'height' => 0
            ];
            if(count($product)==1){
                $sku = SKU::where('seller_id',$o->seller_id)->where('sku',$product[0]->product_sku)->first();
                if(!empty($sku)){
                    $dimension['weight'] = $sku->weight;
                    $dimension['length'] = $sku->length;
                    $dimension['width'] = $sku->width;
                    $dimension['height'] = $sku->height;
                }
            }
            else{
                $totalWeight = 0;
                foreach ($product as $p){
                    $sku = SKU::where('seller_id',$o->seller_id)->where('sku',$p->product_sku)->first();
                    if(!empty($sku)){
                        $totalWeight+=$sku->weight;
                    }
                }
                $dimension['weight'] = $totalWeight;
            }
            if($dimension['weight'] > 0)
            {
                if (count($product) == 1) {
                    $updateData = [
                        'weight' => $dimension['weight'] * 1000,
                        'length' => $dimension['length'],
                        'breadth' => $dimension['width'],
                        'height' => $dimension['height'],
                        'vol_weight' => ($dimension['length'] * $dimension['width'] * $dimension['height'] / 5),
                    ];
                } else {
                    $detail = $this->_fetch_dimension_data($dimension['weight'] * 1000);
                    $updateData = [
                        'weight' => $dimension['weight'] * 1000,
                        'length' => $detail->length,
                        'breadth' => $detail->width,
                        'height' => $detail->height,
                        'vol_weight' => (intval($detail->height) * intval($detail->length) * intval($detail->width)) / 5
                    ];
                }
                Order::where('id', $o->id)->update($updateData);
            }
        }
    }
    function _fetch_dimension_data($weight)
    {
        $response =  DB::table('dimensions')->where('weight', '>=', $weight)->orderBy('weight')->first();
        if($response == null)
            $response = (object) ['height' => 40,'length' => 20,'width' => 20];
        return $response;
    }
    function importServiceability(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability',$data);
    }
    function importServiceabilityCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    $allPincodes[]=[
                        'partner_id' => 35,
                        'courier_partner' => 'dtdc_express',
                        'pincode' => $fileop[0],
                        'city' => $fileop[1],
                        'state' => $fileop[2],
                        'branch_code' => '',
                        'status' => 'Y',
                    ];
                    if(count($allPincodes) == 1000){
                        ServiceablePincode::insert($allPincodes);
                        $allPincodes=[];
                    }
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for Amazon SWA
    function importServiceabilitySWA(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_swa',$data);
    }
    function importServiceabilitySWACsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                ServiceablePincode::where('courier_partner','amazon_swa')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    $allPincodes[]=[
                        'partner_id' => 38,
                        'courier_partner' => 'amazon_swa',
                        'pincode' => $fileop[0],
                        'city' => '',
                        'state' => '',
                        'branch_code' => '',
                        'status' => 'Y',
                    ];
                    if(count($allPincodes) == 1000){
                        ServiceablePincode::insert($allPincodes);
                        $allPincodes=[];
                    }
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }
    function importServiceabilityMarutiEcom(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_maruti_ecom',$data);
    }
    function importServiceabilityCsvMarutiEcom(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','shree_maruti_ecom')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'shree_maruti_ecom')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 61,
                                'courier_partner' => 'shree_maruti_ecom',
                                'pincode' => $fileop[0],
                                'city' => $fileop[1],
                                'state' => $fileop[2],
                                'branch_code' => '',
                                'is_cod' => ($fileop[6] ?? "") == "Yes" ? 'y' : 'n',
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    function importServiceabilityMarutiNew(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_maruti_new',$data);
    }
    function importServiceabilityCsvMarutiNew(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','smc_new')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'smc_new')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 150,
                                'courier_partner' => 'smc_new',
                                'pincode' => $fileop[0],
                                'city' => $fileop[2],
                                'state' => $fileop[3],
                                'branch_code' => '',
                                'is_cod' => 'y',
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    function importServiceabilityMarutiEcomFM(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_maruti_ecom_fm',$data);
    }
    function importServiceabilityCsvMarutiEcomFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','shree_maruti_ecom')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 61,
                            'courier_partner' => 'shree_maruti_ecom',
                            'pincode' => $fileop[0],
                            'city' => $fileop[1],
                            'state' => $fileop[2],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for xbees
    function importServiceabilityXbeesFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_xbees_fm',$data);
    }

    function importServiceabilityXbeesCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','xpressbees_sfc')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 34,
                            'courier_partner' => 'xpressbees_sfc',
                            'pincode' => $fileop[1],
                            'city' => $fileop[4],
                            'state' => $fileop[5],
                            'branch_code' => $fileop[2],
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for Amazon SWA
    function importServiceabilityXbees() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_xbees',$data);
    }


    function importServiceabilityXbeesCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','xpressbees_sfc')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'xpressbees_sfc')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                    'partner_id' => 34,
                                    'courier_partner' => 'xpressbees_sfc',
                                    'pincode' => $fileop[0],
                                    'city' => $fileop[7],
                                    'state' => $fileop[8],
                                    'branch_code' => $fileop[1],
                                    'status' => 'Y',
                                    'inserted' => now()
                                ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);
    }

// Import Serviceability for Amazon SWA
    function importBluedartOriginCodes() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.utility.import-bluedart-origin-codes',$data);
    }

    function submitBluedartOriginCodes(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        $notFound = [];
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!empty($fileop[0]) && !empty($fileop[1])){
                            $response = ServiceablePincodeFM::where('pincode',$fileop[0])->where('courier_partner','bluedart')->update(['origin_code' => $fileop[1]]);
                            $response1 = ServiceablePincodeFM::where('pincode',$fileop[0])->where('courier_partner','bluedart_surface')->update(['origin_code' => $fileop[1]]);
                            if(!$response && !$response1)
                                $notFound[]=$fileop[0];
                        }
                    }
                    $cnt++;
                }
            }
        }
        return response()->json(['status' => true,'pincodes' => $notFound,'count' => count($notFound)]);
    }

    // Import Serviceability for Ekart
    function importServiceabilityEkart() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ekart',$data);
    }

    function importServiceabilityEkartCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','ekart')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'ekart')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                    'partner_id' => 56,
                                    'courier_partner' => 'ekart',
                                    'is_cod' => ($fileop[2] ?? "") == "TRUE" ? 'y' : 'n',
                                    'pincode' => $fileop[0],
                                    'status' => 'Y',
                                    'inserted' => now()
                                ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for Ekart
    function importServiceabilityEkartFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ekart_fm',$data);
    }

    function importServiceabilityEkartCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','ekart')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                                    'partner_id' => 56,
                                    'courier_partner' => 'ekart',
                                    'pincode' => $fileop[0],
                                    'city' => $fileop[1],
                                    'state' => $fileop[1],
                                    'branch_code' => $fileop[1],
                                    'status' => 'Y',
                                    'inserted' => now()
                                ];
                                if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }


    // Import Serviceability for DTDC Surface
    // Import Serviceability for Amazon SWA
    function importServiceabilityDtdc(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_dtdc',$data);
    }

    function importServiceabilityDtdcCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','dtdc_surface')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'dtdc_surface')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        // For dtdc express
                        if(!in_array($fileop[0],$disabledPincode)){
                            $allPincodes[]=[
                                'partner_id' => 7,
                                'courier_partner' => 'dtdc_surface',
                                'pincode' => $fileop[0],
                                'city' => $fileop[6],
                                'state' => $fileop[5],
                                'branch_code' => $fileop[2],
                                'status' => 'Y',
                                'inserted' => now(),
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000){
                            ServiceablePincode::insert($allPincodes);
                            // ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes=[];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
                // ServiceablePincodeFM::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);
    }

    //Import Serviceability for dtdc

    function importServiceabilityDtdcFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_dtdc_fm',$data);
    }

    function importServiceabilityDtdcCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','dtdc_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 7,
                            'courier_partner' => 'dtdc_surface',
                            'pincode' => $fileop[1],
                            'city' => $fileop[4],
                            'state' => $fileop[5],
                            'branch_code' => $fileop[3],
                            'status' => 'Y',
                            'inserted' => now(),
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for BluedartAir

    function importServiceabilityBluedartFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_bluedart_fm',$data);
    }

    function importServiceabilityBluedartCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','bluedart')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 1,
                            'courier_partner' => 'bluedart',
                            'pincode' => $fileop[0],
                            'origin_code' => $fileop[1],
                            'city' => $fileop[2],
                            'state' => $fileop[4],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for BluedartSurface
    function importServiceabilityBluedartSurfaceFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_bluedart_surface',$data);
    }

    function importServiceabilityBluedartSurfaceCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','bluedart_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 107,
                            'courier_partner' => 'bluedart_surface',
                            'pincode' => $fileop[0],
                            'origin_code' => $fileop[1],
                            'city' => $fileop[2],
                            'state' => $fileop[4],
                            'region' => $fileop[5],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for bluedart
    function importServiceabilityBlueDart(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_bluedart',$data);
    }

       function importServiceabilityBlueDartCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                $cnt = 0;
                ServiceablePincode::where('courier_partner','bluedart')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0){
                        $allPincodes[] = [
                            'partner_id' => 51,
                            'courier_partner' => 'bluedart',
                            'pincode' => $fileop[0],
                            'is_cod' => ($fileop[6] ?? "") == "Yes" ? 'y' : 'n',
                            'city' => $fileop[2],
                            'state' => $fileop[4],
                            'cluster_code' => $fileop[9],
                            'region' => $fileop[5],
                            'branch_code' => $fileop[1]."/".$fileop[3],
                            'status' => 'Y',
                            'inserted' => now(),
                        ];
                        if (count($allPincodes) == 1000) {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for Delhivery
    function importServiceabilityDelhivery(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_delhivery',$data);
    }

    function importServiceabilityDelhiveryCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','delhivery_surface')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'delhivery_surface')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode)){
                            $allPincodes[]=[
                                'partner_id' => 04,
                                'courier_partner' => 'delhivery_surface',
                                'pincode' => $fileop[0],
                                'city' => $fileop[6] ?? "",
                                'state' => $fileop[7] ?? "",
                                'branch_code' => $fileop[5] ?? "",
                                'status' => 'Y',
                                'is_cod' => ($fileop[4] ?? "") == "Y" ? 'y' : 'n',
                                'inserted' => now()

                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }

                        if(count($allPincodes) == 1000){
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes=[];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for Delhivery Fm
    function importServiceabilityDelhiveryFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_delhivery_fm',$data);
    }

    function importServiceabilityDelhiveryCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','delhivery_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 04,
                            'courier_partner' => 'delhivery_surface',
                            'pincode' => $fileop[0],
                            'city' => $fileop[6],
                            'state' => $fileop[7],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    function importServiceabilityPick(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_pick',$data);
    }

    function importServiceabilityPickCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','pick_del')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'pick_del')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode)){
                            $allPincodes[]=[
                                'partner_id' => 149,
                                'courier_partner' => 'pick_del',
                                'pincode' => $fileop[1],
                                'city' => $fileop[2] ?? "",
                                'state' => "",
                                'branch_code' => "",
                                'status' => 'Y',
                                //'is_cod' => ($fileop[4] ?? "") == "Y" ? 'y' : 'n',
                                'inserted' => now()

                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }

                        if(count($allPincodes) == 1000){
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes=[];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

// Import Serviceability for Delhivery Fm
    function importServiceabilityPickFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_pick_fm',$data);
    }

    function importServiceabilityPickCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','pick_del')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 149,
                            'courier_partner' => 'pick_del',
                            'pincode' => $fileop[1],
                            'city' => $fileop[2],
                            'state' => "",
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }



    // Import Serviceability for Smartr
    function importServiceabilitySmartr(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_smartr',$data);
    }

    function importServiceabilitySmartrCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','smartr')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'smartr')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode)){
                            $allPincodes[]=[
                                'partner_id' => 52,
                                'courier_partner' => 'smartr',
                                'pincode' => $fileop[1],
                                'city' => $fileop[2] ?? "",
                                'state' => $fileop[7] ?? "",
                                'branch_code' => $fileop[3] ?? "",
                                'status' => str_contains(($fileop[9] ?? ""),"ACP") ? "Y" : "N",
                                'is_cod' => str_contains(($fileop[9] ?? ""),"ACC") ? "Y" : "N",
                                'cluster_code' => $fileop[12]
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }

                        if(count($allPincodes) == 1000){
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes=[];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import AWBs for Smartr
    function importAWBSmartr(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_awbs_smartr',$data);
    }

    function importAWBSmartrCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allAwbs[]=[
                            'courier_partner' => 'smartr',
                            'awb_number' => $fileop[0]
                        ];
                        if(count($allAwbs) == 1000){
                            SmartrAwbs::insert($allAwbs);
                            $allAwbs=[];
                        }
                    }
                    $cnt++;
                }
                SmartrAwbs::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    // Import AWBs for Smartr
    function importAWBXBeesUnique(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_awbs_xbees_unique',$data);
    }

    function importAWBXBeesUniqueCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allAwbs[]=[
                            'courier_partner' => 'xpressbees_sfc',
                            'order_type' => 'forward',
                            'used' => 'n',
                            'awb_number' => trim($fileop[0]),
                            'batch_number' => "5sCsB"
                        ];
                        if(count($allAwbs) == 10000){
                            XbeesAwbnumberUnique::insert($allAwbs);
                            $allAwbs=[];
                        }
                    }
                    $cnt++;
                }
                XbeesAwbnumberUnique::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    // Import AWBs for Gati
    function importAWBGati(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_awbs_gati',$data);
    }

    function importAWBGatiCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allAwbs[]=[
                            'courier_partner' => 'gati',
                            'awb_number' => $fileop[0]
                        ];
                        if(count($allAwbs) == 1000){
                            GatiAwbs::insert($allAwbs);
                            $allAwbs=[];
                        }
                    }
                    $cnt++;
                }
                GatiAwbs::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    // Import AWBs for Gati
    function importAWBMovin(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['total'] = MovinAWBNumbers::count();
        $data['used'] = MovinAWBNumbers::where('used','y')->count();
        return view('admin.import_awb_movin',$data);
    }

    function importAWBMovinCSV(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allAwbs[]=[
                            'mode' => $request->mode,
                            'awb_number' => $fileop[0],
                            'inserted' => date('Y-m-d H:i:s')
                        ];
                        if(count($allAwbs) == 1000){
                            MovinAWBNumbers::insert($allAwbs);
                            $allAwbs=[];
                        }
                    }
                    $cnt++;
                }
                MovinAWBNumbers::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    // Import Serviceability for movin
    function importServiceabilityMovin() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_movin',$data);
    }

    function importServiceabilityMovinCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','movin')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'movin')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if($fileop[3] == 'BOTH'){
                            $pincodes = explode('|',$fileop[8]);
                            foreach ($pincodes as $p){
                                if(!in_array($p,$disabledPincode)) {
                                    $allPincodes[] = [
                                        'partner_id' => 93,
                                        'courier_partner' => 'movin',
                                        'pincode' => $p,
                                        'city' => '',
                                        'state' => '',
                                        'branch_code' => '',
                                        'status' => 'Y',
                                        'inserted' => now()
                                    ];
                                }
                                if(count($allPincodes) > 1000){
                                    ServiceablePincode::insert($allPincodes);
                                    $allPincodes = [];
                                }
                            }
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Serviceability Imported Successfully'
            ),
        );
        Session($notification);
        return back();
    }

    // Import Serviceability for movin fm
    function importServiceabilityMovinFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_movin_fm',$data);
    }

    function importServiceabilityMovinCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincodeFM::where('courier_partner','movin')->delete();
                $allPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if($fileop[3] == 'BOTH' || $fileop[3] == 'PICKUP'){
                            $pincodes = explode('|',$fileop[8]);
                            foreach ($pincodes as $p){
                                $allPincodes[] = [
                                    'partner_id' => 93,
                                    'courier_partner' => 'movin',
                                    'pincode' => $p,
                                    'city' => '',
                                    'state' => '',
                                    'branch_code' => '',
                                    'status' => 'Y',
                                    'inserted' => now()
                                ];
                                if(count($allPincodes) > 1000){
                                    ServiceablePincodeFM::insert($allPincodes);
                                    $allPincodes = [];
                                }
                            }
                        }
                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Serviceability Imported Successfully'
            ),
        );
        Session($notification);
        return back();
    }

    /**
     * Render block courier page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blockCourier(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['courier_partners'] = Partners::all();
        $data['sellers'] = Seller::all();
        return view('admin.block-courier', $data);
    }

    /**
     * Sotre block courier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBlockCourier(Request $request) {
        try {
            // Validate users input
            $validator = Validator::make($request->all(), [
                'seller_id' => 'required|exists:sellers,id',
            ]);

            if($validator->stopOnFirstFailure()->fails()) {
                // Generating notification
                $notification = array(
                    'notification' => array(
                        'type' => 'error',
                        'title' => 'Error',
                        'message' => $validator->errors()->first(),
                    ),
                );
                Session($notification);
                return back();
            }

            $partners = Partners::all();
            foreach($partners as $partner) {
                if($request->get("courier_{$partner->id}")) {
                    $courier = Courier_blocking::firstOrNew([
                        'seller_id' => $request->seller_id,
                        'courier_partner_id' => $partner->id,
                    ]);
                    $courier->is_blocked = $request->get("is_blocked_{$partner->id}") ?? 'n';
                    $courier->zone_a = $request->get("zone_a_{$partner->id}") ?? 'n';
                    $courier->zone_b = $request->get("zone_b_{$partner->id}") ?? 'n';
                    $courier->zone_c = $request->get("zone_c_{$partner->id}") ?? 'n';
                    $courier->zone_d = $request->get("zone_d_{$partner->id}") ?? 'n';
                    $courier->zone_e = $request->get("zone_e_{$partner->id}") ?? 'n';
                    $courier->cod = $request->get("cod_{$partner->id}") ?? 'n';
                    $courier->prepaid = $request->get("prepaid_{$partner->id}") ?? 'n';
                    $courier->remark = 'Approved by admin';
                    $courier->is_approved = 'y';
                    $courier->save();
                    if($courier->is_blocked == 'y'){
                        Seller::where('courier_priority_1',$partner->keyword)->where('id',$request->seller_id)->update(['courier_priority_1' => null]);
                        Seller::where('courier_priority_2',$partner->keyword)->where('id',$request->seller_id)->update(['courier_priority_2' => null]);
                        Seller::where('courier_priority_3',$partner->keyword)->where('id',$request->seller_id)->update(['courier_priority_3' => null]);
                        Seller::where('courier_priority_4',$partner->keyword)->where('id',$request->seller_id)->update(['courier_priority_4' => null]);
                        Preferences::where('priority1',$partner->keyword)->where('seller_id',$request->seller_id)->update(['priority1' => null]);
                        Preferences::where('priority2',$partner->keyword)->where('seller_id',$request->seller_id)->update(['priority2' => null]);
                        Preferences::where('priority3',$partner->keyword)->where('seller_id',$request->seller_id)->update(['priority3' => null]);
                        Preferences::where('priority4',$partner->keyword)->where('seller_id',$request->seller_id)->update(['priority4' => null]);
                    }
                }
            }
            // Generating notification
            $notification = array(
                'notification' => array(
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Courier blocking details stored successfully',
                ),
            );
            Session($notification);
            return back();
        } catch(Exception $e) {
            //dd($e->getLine(),$e->getMessage(),$e->getFile());
            // Generating notification
            $notification = array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Internal server error',
                ),
            );
            Session($notification);
            return back();
        }
    }

    /**
     * Get blocked courier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBlockedCourier(Request $request) {
        $sellers = Courier_blocking::with('seller')
            ->with('partner');
        if($request->filled('sellerId')) {
            $sellers = $sellers->where('seller_id', $request->sellerId);
        }
        if($request->filled('isApproved')) {
            $sellers = $sellers->where('is_approved', $request->isApproved);
        }
        $sellers = $sellers->orderBy('id', 'desc');
        if($request->filled('itemPerPage')) {
            $sellers = $sellers->groupBy('seller_id')->paginate($request->itemPerPage);
            return response()->json([
                'statusCode' => $sellers->isNotEmpty() ? 200 : 204,
                'message' => $sellers->isNotEmpty() ? 'Data found' : 'Data not found',
                'data' => $sellers->items(),
                'pagination' => [
                    'currentPage' => $sellers->currentPage(),
                    'currentPageItemCount' => $sellers->count(),
                    'itemPerPage' => $sellers->perPage(),
                    'totalPage' => $sellers->lastPage(),
                    'totalItemCount' => $sellers->total(),
                    'firstItem' => $sellers->firstItem()
                ]
            ]);
        } else {
            $sellers = $sellers->get();
            return response()->json([
                'statusCode' => $sellers->isNotEmpty() ? 200 : 204,
                'message' => $sellers->isNotEmpty() ? 'Data found' : 'Data not found',
                'data' => $sellers,
            ]);
        }
    }

    function importCsvOrders(Request $request){
        $awbs = [];
        $allAwbs = [];
        $test = explode('.', $_FILES['awb_numbers']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['awb_numbers']['tmp_name'];
                $handle = fopen($file, "r");
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $awbs[]=str_replace('`',"",$fileop[0]);
                            $allAwbs[]= str_replace('`','',$fileop[0]);
                        }
                    }
                    $cnt++;
                }
            }
            else{
                $notification = array(
                    'notification' => array(
                        'type' => 'error',
                        'title' => 'Error',
                        'message' => 'Invalid File Uploaded',
                    ),
                );
                Session($notification);
                return back();
            }
        }
        else{
            $notification = array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No File Uploaded',
                ),
            );
            Session($notification);
            return back();
        }
        if(count($awbs) > 0){
            if(count($awbs) <= 5000){
                $PartnerName = Partners::getPartnerKeywordList();
                $name = "exports/Order Details";
                $filename = "Order Details";
                $query = Order::leftJoin("weight_reconciliation", 'orders.awb_number', '=', 'weight_reconciliation.awb_number')->join('sellers', 'orders.seller_id', '=', 'sellers.id')
                    ->select(
                        'sellers.code',
                        'sellers.company_name',
                        'orders.rto_status',
                        'orders.id',
                        'orders.status',
                        'orders.o_type',
                        'orders.awb_number',
                        'orders.p_state',
                        'orders.p_city',
                        'orders.p_pincode',
                        'orders.pickup_time',
                        'orders.awb_assigned_date',
                        'orders.delivered_date',
                        'orders.customer_order_number',
                        'orders.courier_partner',
                        'orders.order_type',
                        'orders.s_pincode',
                        'orders.s_state',
                        'orders.s_city',
                        'orders.zone',
                        'orders.invoice_amount',
                        'orders.weight',
                        'orders.length',
                        'orders.breadth',
                        'orders.height',
                        'orders.product_name',
                        'orders.product_sku',
                        'orders.shipping_charges',
                        'orders.rto_charges',
                        'orders.cod_charges',
                        'orders.total_charges',
                        'orders.last_sync',
                        'orders.rto_status',
                        'orders.ndr_status',
                        'orders.weight_disputed',
                        'orders.s_customer_name',
                        'orders.s_contact',
                        'orders.s_address_line1',
                        'orders.s_address_line2',
                        'orders.s_pincode',
                        'orders.s_city',
                        'orders.s_state',
                        'orders.awb_number',
                        'weight_reconciliation.charged_amount'
                    )
                    ->where('orders.awb_number', '!=', '')
                    ->whereIn('orders.awb_number',$awbs)
                    ->with('Intransittable');
                $all_data = $query->get();
                $fp = fopen("$name.csv", 'w');
                $info = array('Pickup State', 'Pickup City', 'Pickup Pincode', 'Seller', 'Seller Code','Order Type', 'Pickup Date', 'Shipping Date', 'Delivered Date', 'Order Id', 'AWB Number', 'Courier Partner', 'Payment Type', 'Customer Name','Customer Mobile','Ship Address','Ship Pincode', 'Ship State', 'Ship City', 'Zone', 'Invoice Amount', 'Billing Weight (Kg)', 'Dimension(L * H * H)(CM)', 'Product Name','Product SKU', 'Current Status','Rto Status','Ndr Status', 'Collectable Value', 'Freight Charges', 'RTO Charges', 'COD Charges', 'Total Charges', 'AWB Assigned Date', 'Last Sync', 'Final Charge', 'Number of NDR attempts', 'First NDR raised date', 'First NDR raised time', 'First NDR Action By', 'Reason for First NDR', 'Action date for First NDR', 'Action Status for First NDR', 'Remarks for First NDR', 'First Updated Address Line 1', 'First Updated Address Line 1', 'First Updated Mobile', 'Second NDR raised date', 'Second NDR raised time', 'Second NDR Action By', 'Reason for Second NDR', 'Action date for Second NDR', 'Action Status for Second NDR', 'Remarks for Second NDR', 'Second Updated Address Line 1', 'Second Updated Address Line 1', 'Second Updated Mobile', 'Third NDR raised date', 'Third NDR raised time', 'Third NDR Action By', 'Reason for Third NDR', 'Action date for Third NDR', 'Action Status for Third NDR', 'Remarks for Third NDR', 'Third Updated Address Line 1', 'Third Updated Address Line 1', 'Third Updated Mobile');
                fputcsv($fp, $info);
                $cnt = 1;
                foreach ($all_data as $e) {
                    try {
                        $awbNumbers = $e->awb_number;
                        if(!is_numeric($e->rto_charges))
                            $e->rto_charges = 0;
                        if(!is_numeric($e->shipping_charges))
                            $e->shipping_charges = 0;
                        if(!is_numeric($e->charged_amount))
                            $e->charged_amount = 0;
                        if(!is_numeric($e->cod_charges))
                            $e->cod_charges = 0;
                        if($e->rto_status == 'n')
                            $finalCharge = floatval($e->total_charges) + floatval($e->charged_amount ?? 0);
                        else
                            $finalCharge = floatval($e->total_charges) - floatval($e->cod_charges) + floatval($e->rto_charges) + floatval($e->charged_amount ?? 0);
                        if ($e->rto_status == 'y' && $e->status == 'delivered')
                            $e->status = 'rto_delivered';
                        if ($e->rto_status == 'y' && $e->status == 'in_transit')
                            $e->status = 'rto_in_transit';
                        if($e->rto_status == 'y' && $e->status=='out_for_delivery')
                            $e->status='rto_out_for_delivery';
                        if (strtolower($e->order_type) == 'cod')
                            $collectable_value = $e->invoice_amount;
                        else
                            $collectable_value = "0";
                        $weight = $e->weight;
                        if (is_numeric($weight))
                            $weight = $e->weight / 1000;

                        $intransit = $e->Intransittable ?? "";
                        if(empty($e->pickup_time)){
                            $pickup_time = $intransit->datetime ?? "";
                        }
                        else{
                            if(!empty($e->Intransittable) && ($e->pickup_time > $e->Intransittable->datetime)){
                                $pickup_time = $e->Intransittable->datetime;
                            }
                            else {
                                $pickup_time = $e->pickup_time;
                            }
                        }

                        $e->product_name = trim(str_replace("??", "", str_replace("/", "-", str_replace(",", "|", $e->product_name))));
                        $e->product_sku = trim(str_replace("??", "", str_replace("/", "-", str_replace(",", "|", $e->product_sku))));
                        $attempts = $e->ndrattempts ?? [];
                        $info = array(addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->p_state)))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->p_city)))), preg_replace('/\s+/', ' ', trim($e->p_pincode)), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->company_name)))), preg_replace('/\s+/', ' ', trim($e->code)),$e->o_type, $pickup_time, $e->awb_assigned_date, $e->delivered_date, addslashes(preg_replace('/\s+/', ' ', preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->customer_order_number))))), ('`' . $e->awb_number . '`'), $PartnerName[$e->courier_partner] ?? "NA", $e->order_type,addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_customer_name)))),addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_contact)))),addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line1)))) ." ".addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line2)))), preg_replace('/\s+/', ' ', trim($e->s_pincode)), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(".", "", str_replace(",", "|", $e->s_state))))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_city)))), $e->zone, $e->invoice_amount, $weight, ($e->length . ' * ' . $e->breadth . ' * ' . $e->height), preg_replace('/\s+/', ' ', trim($e->product_name)), preg_replace('/\s+/', ' ', trim($e->product_sku)), addslashes($this->orderStatus[$e->status]),$e->rto_status,$e->ndr_status, $collectable_value, $e->shipping_charges, $e->rto_charges, $e->cod_charges, $e->total_charges, $e->awb_assigned_date, $e->last_sync, round($finalCharge,2), count($e->ndrattempts));

                        if(count($attempts) > 0){
                            foreach ($attempts as $a) {
                                try {
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->raised_date));
                                    $info[] = $a->raised_time;
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->action_by));
                                    $info[] = str_replace(",", "|", $a->reason);
                                    $info[] = $a->action_date;
                                    $info[] = preg_replace('/\s+/', ' ', trim($a->action_status));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->remark))));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->u_address_line1))));
                                    $info[] = addslashes(str_replace(",", "|", $a->u_address_line2));
                                    $info[] = addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $a->updated_mobile))));
                                } catch (Exception $e) {
                                    continue;
                                }
                            }
                        }
                        fputcsv($fp,$info);
                    } catch (Exception $e) {
                        dd($e);
                        continue;
                    }
                    $cnt++;
                }
                // Output headers.
                header("Cache-Control: no-cache");
                header("Content-Type: text/csv");
                header("Content-Length: " . filesize("$name.csv"));
                header("Content-Disposition: attachment; filename=$filename.csv");
                // Output file.
                readfile("$name.csv");
                @unlink("$name.csv");
            }
            else{
                $order_report = [
                    'report_name' => $request->modalOrderRadio == 'ArchiveOrder' ? "Archive-OrderReport-Selected-Awb" : "OrderReport-Selected-Awb" ,
                    'payload' => json_encode([
                        'table' => $request->modalOrderRadio,
                        'from_date' => "",
                        'to_date' => "",
                        'seller' => "",
                        'order_type' => "0",
                        'order_status' => "0",
                        'courier_partner' => null,
                        'awb_number' => implode(",",$awbs),
                    ]),
                    'status' => "processing",
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $id =  DownloadOrderReportModel::create($order_report)->id;

                //dispatchAfterResponse((new DownloadOrderReport($id))->onQueue('high'));
                DownloadOrderReport::dispatchAfterResponse($id);
                $notification = array(
                    'notification' => array(
                        'type' => 'success',
                        'title' => 'Success',
                        'message' => 'Your report will be generated soon.',
                    ),
                );
                Session($notification);
                return back();
            }
        }
        else{
            return back();
        }
    }

    function cronJobs() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['cronJobs'] = DB::table('cron_jobs')->get();
        return view('admin.cron-jobs', $data);
    }

    function exportCronJobs()
    {
        $data = DB::table('cron_jobs')->get();
        $name = 'cron-jobs.csv';
        $fp = fopen($name, 'w');
        $info = array('Sr.no', 'Job Name', 'Last Status', 'Started At', 'Finished At');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($data as $key => $e) {
            $info = array(++$key, $e->job_name, $e->last_status, $e->started_at, $e->finished_at);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize($name));
        header("Content-Disposition: attachment; filename=$name");
        // Output file.
        readfile($name);
        unlink($name);
    }

    function cronLogData($slug) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['cronLogs'] = DB::table('cron_logs')->where('cron_name', $slug)->latest('date')->limit(100)->get();
        return view('admin.cron-log-data', $data);
    }

    function exportCronLogData($slug)
    {
        // dd($all_data);
        $data = DB::table('cron_logs')->where('cron_name', $slug)->latest('date')->limit(100)->get();
        $name = 'cron-logs.csv';
        $fp = fopen($name, 'w');
        $info = array('Sr.no', 'Cron Name', 'Status', 'Success', 'Errors', 'Row Inserted', 'Row Updated', 'Row Deleted', 'Started At', 'Finished At');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($data as $key => $e) {
            $info = array(++$key, $e->cron_name, $e->status, $e->success, $e->errors, $e->row_inserted, $e->row_updated, $e->row_deleted, $e->started_at, $e->finished_at);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize($name));
        header("Content-Disposition: attachment; filename=$name");
        // Output file.
        readfile($name);
        unlink($name);
    }

    function asyncCronJobs() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['cronJobs'] = DB::table('cron_jobs')->get();
        return view('admin.async-cron-job', $data);
    }

    function awbThreshold() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        // Cache for 5 minutes
        $thresholds = Cache::store('redis')->remember('Twinnship:admin:awb_threshold', (60*5), function() {
            $advanceAwbs = [
                'bombax_awbs' => 'Bombax',
                'delhivery_awb_numbers' => 'Delhivery',
                'ecom_express_awbs' => 'Ecom Express',
                'ekart_awb_numbers' => 'Ekart',
                'gati_awbs' => 'Gati',
                'maruti_awbs' => 'Shree Maruti',
                'smartr_awbs' => 'Smartr',
                'xbees_awb_numbers' => 'Xpressbees',
                'xbees_awb_numbers_unique' => 'Xpressbees Unique',
                'dtdc_awb_numbers' => 'DTDC',
            ];
            $thresholds = collect([]);
            foreach($advanceAwbs as $table => $partner) {
                $data = [
                    'courier_partner' => $partner,
                    'available_awb' => DB::table($table)->where('used', 'n')->count(),
                    'used_awb' => DB::table($table)->where('used', 'y')->count(),
                    'total_awb' => DB::table($table)->count(),
                ];
                // Calculate in %
                if(!empty($data['total_awb'])) {
                    $data['available_awb_in_pr'] = round($data['available_awb'] * 100 / $data['total_awb'], 2);
                    $data['used_awb_in_pr'] = round($data['used_awb'] * 100 / $data['total_awb'], 2);
                } else {
                    $data['available_awb_in_pr'] = 0;
                    $data['used_awb_in_pr'] = 0;
                }
                $thresholds->push($data);
            }
            return $thresholds->sortBy('available_awb_in_pr');
        });
        $data['thresholds'] = $thresholds;
        return view('admin.awb-thresholds', $data);
    }

    function importAmazonDirectOrderReport() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::where('status', 'y')->get();
        return view('admin.import-amazon-direct-order-report', $data);
    }

    function importAmazonDirectOrderReportFile(Request $request) {
        try {
            if(!$request->hasFile('importFile')) {
                throw new Exception('Please upload file.');
            }

            // For specific seller id
            if(!$request->filled('sellerId')) {
                throw new Exception('Please select seller.');
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
                    return $carry + (($item['item_price'] * $item['quantity_purchased']) + $item['shipping_price']);
                }, 0);
                $data['products'] = $products->values()->all();
                $reportData->push($data);
            }

            // Import report file.
            $res = MyUtility::createAmazonDirectOrderFromReport($reportData, $channel);
            if($res['status'] == false) {
                throw new Exception($res['message']);
            }

            $notification = array(
                'notification' => array(
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => $res['message'],
                ),
            );
            Session($notification);
            return back();
        } catch(Exception $e) {
            $notification = array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $e->getMessage()
                ),
            );
            Session($notification);
            return back();
        }
    }

    function fulfillAmazonDirectOrder() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::where('status', 'y')->get();
        return view('admin.fulfill-amazon-direct-order-flat-file', $data);
    }

    function fulfillAmazonDirectOrderFlatFile(Request $request) {
        try {
            if(!$request->hasFile('importFile')) {
                throw new Exception('Please upload file.');
            }

            // For specific seller id
            if(!$request->filled('sellerId')) {
                throw new Exception('Please select seller.');
            }

            // Get all amazon direct sellers
            $channel = Channels::where('channel', 'amazon_direct')
                ->where('status','y')
                ->where('seller_id', $request->sellerId)
                ->first();

            // Read feed flat file
            $temp = tmpfile();
            fwrite($temp, file_get_contents($request->importFile->getRealPath()));
            $feedFlatFile = Excel::toArray(new AmazonFeedFlatFileImport, stream_get_meta_data($temp)['uri'], null, \Maatwebsite\Excel\Excel::CSV);
            // this removes the file
            fclose($temp);
            if(empty($feedFlatFile)) {
                throw new Exception('Feed file is empty, please upload data.');
            }
            $awbNumbers = collect($feedFlatFile ?? [])->pluck('tracking_number')->toArray();

            $amazonDirect = new AmazonDirect();
            $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
            $feedDocument = $amazonDirect->createAmazonFeedDocument($accessToken, 'text/plain; charset=UTF-8');
            if(empty($feedDocument['payload'])) {
                // Unable to create feed document for this seller
                throw new Exception('Unable to create feed document for this seller.');
            }

            // File for feeds
            $payload = file_get_contents($request->importFile->getRealPath());
            // Upload flat file document
            $uploadDocument = $amazonDirect->uploadAmazonFeedDocument($accessToken, $feedDocument['payload'], $payload, 'text/plain; charset=UTF-8');
            if($uploadDocument->getStatusCode() == 200) {
                // Create feed
                $feed = $amazonDirect->createAmazonFeed($accessToken, 'POST_FLAT_FILE_FULFILLMENT_DATA', ['A21TJRUUN4KGV'], $feedDocument['payload']['feedDocumentId']);
                if($feed->ok() || $feed->status() == 201 || $feed->status() == 202) {
                    // Feed created update the feed id
                    Order::whereIn('awb_number', $awbNumbers)
                        ->update([
                            'fulfillment_id' => $feed->json()['payload']['feedId'] ?? null,
                            'fulfillment_sent' => 'y'
                        ]);
                } else {
                    throw new Exception('Feed Not Created.');
                }
            } else {
                // Document not uploaded
                throw new Exception('Feed document not uploded.');
            }

            $notification = array(
                'notification' => array(
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Feed uploaded successfully.',
                ),
            );
            Session($notification);
            return back();
        } catch(Exception $e) {
            $notification = array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $e->getMessage()
                ),
            );
            Session($notification);
            return back();
        }
    }

    function pendingManifestOrder(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        if($request->filled('seller')) {
            $manifest = Order::with('seller')
                ->whereNotNull('awb_number')
                ->where('manifest_sent', 'n')
                ->whereNotIn('status', ['cancelled', 'delivered'])
                ->whereRaw('awb_assigned_date <= now() - INTERVAL 30 MINUTE');
            if($request->filled('seller')) {
                $manifest = $manifest->where('seller_id', $request->seller);
            }
            if($request->filled('q')) {
                $manifest = $manifest->where('order_number', $request->q)
                    ->orWhere('awb_number', $request->q)
                    ->orWhere('status', $request->q)
                    ->orWhere('courier_partner', 'like', "%{$request->q}%");
            }
            $manifest = $manifest->orderBy('awb_assigned_date', 'desc')
                ->paginate(10)
                ->appends($request->query());
            $data['manifest'] = $manifest;
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            return view('admin.pending-manifest-seller-wise', $data);
        } else {
            $manifest = Order::with('seller')
                ->selectRaw('seller_id, count(*) total_orders')
                ->whereNotNull('awb_number')
                ->where('manifest_sent', 'n')
                ->whereNotIn('status', ['cancelled', 'delivered'])
                ->whereRaw('awb_assigned_date <= now() - INTERVAL 30 MINUTE');
            if($request->filled('seller_id')) {
                $manifest = $manifest->whereIn('seller_id', $request->seller_id);
            }
            $manifest = $manifest->groupBy('seller_id')
                ->orderBy('total_orders', 'desc')
                ->paginate(10)
                ->appends($request->query());
            $data['manifest'] = $manifest;
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['sellers'] = Seller::where('status', 'y')->get();
            return view('admin.pending-manifest', $data);
        }
    }

    function exportPendingManifestOrder(Request $request) {
        $manifest = Order::with('seller')
            ->whereNotNull('awb_number')
            ->where('manifest_sent', 'n')
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->whereRaw('awb_assigned_date <= now() - INTERVAL 30 MINUTE');
        if($request->filled('seller')) {
            $manifest = $manifest->where('seller_id', $request->seller);
        }
        if($request->filled('q')) {
            $manifest = $manifest->where('order_number', $request->q)
                ->orWhere('awb_number', $request->q)
                ->orWhere('status', $request->q)
                ->orWhere('courier_partner', 'like', "%{$request->q}%");
        }
        $manifest = $manifest->orderBy('awb_assigned_date', 'desc')
            ->get();
        $partnerName = Partners::getPartnerKeywordList();
        $name = 'manifest-report-of-seller-'.$request->seller.'.csv';
        $fp = fopen($name, 'w');
        $info = array('Sr.no', 'Seller Id', 'Seller Code', 'Order Number', 'Awb Number', 'Courier Partner', 'Awb Assigned Date', 'Status', 'Manifest Status');
        fputcsv($fp, $info);
        foreach ($manifest as $key => $e) {
            $info = array(++$key, $e->seller_id, $e->seller->code, $e->order_number, $e->awb_number, $partnerName[$e->courier_partner], $e->awb_assigned_date, $e->status, $e->manifest_sent);
            fputcsv($fp, $info);
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize($name));
        header("Content-Disposition: attachment; filename=$name");
        // Output file.
        readfile($name);
        unlink($name);
    }

    function pendingPickupOrder(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        if($request->filled('seller')) {
            $pickup = Order::with('seller')
                ->whereIn('status', ['shipped', 'manifested', 'pickup_requested', 'pickup_scheduled'])
                ->whereRaw('awb_assigned_date >= now() - INTERVAL 10 DAY')
                ->whereRaw('awb_assigned_date <= now() - INTERVAL 2 DAY');
            if($request->filled('seller')) {
                $pickup = $pickup->where('seller_id', $request->seller);
            }
            if($request->filled('q')) {
                $pickup = $pickup->where('order_number', $request->q)
                    ->orWhere('awb_number', $request->q)
                    ->orWhere('status', $request->q)
                    ->orWhere('courier_partner', 'like', "%{$request->q}%");
            }
            $pickup = $pickup->orderBy('awb_assigned_date', 'desc')
                ->paginate(10)
                ->appends($request->query());
            $data['pickup'] = $pickup;
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            return view('admin.pending-pickup-seller-wise', $data);
        } else {
            $pickup = Order::with('seller')
                ->selectRaw('seller_id, count(*) total_orders')
                ->whereIn('status', ['shipped', 'manifested', 'pickup_requested', 'pickup_scheduled'])
                ->whereRaw('awb_assigned_date >= now() - INTERVAL 10 DAY')
                ->whereRaw('awb_assigned_date <= now() - INTERVAL 2 DAY');
            if($request->filled('seller_id')) {
                $pickup = $pickup->whereIn('seller_id', $request->seller_id);
            }
            $pickup = $pickup->groupBy('seller_id')
                ->orderBy('total_orders', 'desc')
                ->paginate(10)
                ->appends($request->query());
            $data['pickup'] = $pickup;
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['sellers'] = Seller::where('status', 'y')->get();
            return view('admin.pending-pickup', $data);
        }
    }

    function exportPendingPickupOrder(Request $request) {
        $pickup = Order::with('seller')
            ->whereIn('status', ['shipped', 'manifested', 'pickup_requested', 'pickup_scheduled'])
            ->whereRaw('awb_assigned_date >= now() - INTERVAL 10 DAY')
            ->whereRaw('awb_assigned_date <= now() - INTERVAL 2 DAY');
        if($request->filled('seller')) {
            $pickup = $pickup->where('seller_id', $request->seller);
        }
        if($request->filled('q')) {
            $pickup = $pickup->where('order_number', $request->q)
                ->orWhere('awb_number', $request->q)
                ->orWhere('status', $request->q)
                ->orWhere('courier_partner', 'like', "%{$request->q}%");
        }
        $pickup = $pickup->orderBy('awb_assigned_date', 'desc')
            ->get();
        $partnerName = Partners::getPartnerKeywordList();
        $name = 'pickup-report-of-seller-'.$request->seller.'.csv';
        $fp = fopen($name, 'w');
        $info = array('Sr.no', 'Seller Id', 'Seller Code', 'Order Number', 'Awb Number', 'Courier Partner', 'Awb Assigned Date', 'Status');
        fputcsv($fp, $info);
        foreach ($pickup as $key => $e) {
            $info = array(++$key, $e->seller_id, $e->seller->code, $e->order_number, $e->awb_number, $partnerName[$e->courier_partner], $e->awb_assigned_date, $e->status);
            fputcsv($fp, $info);
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize($name));
        header("Content-Disposition: attachment; filename=$name");
        // Output file.
        readfile($name);
        unlink($name);
    }

    function zoneMapping(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $zoneMapping = ZoneMapping::query();
        if($request->filled('q')) {
            $zoneMapping = $zoneMapping->where('pincode', 'like', "%{$request->q}%");
        }
        $zoneMapping = $zoneMapping->paginate(10)->appends($request->query());
        $data['zoneMapping'] = $zoneMapping;
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('admin.zone-mapping', $data);
    }

    function addZoneMapping(Request $request) {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|numeric|unique:zone_mapping',
        ]);

        if($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        }

        $data = $validator->validated();
        $zoneMapping = ZoneMapping::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Zone mapping added successfully.',
            'data' => $zoneMapping,
        ]);
    }

    function getPincodeDetail(Request $request)
    {
        try {
            $response = Http::get("https://api.postalpincode.in/pincode/{$request->pincode}");
            if ($response->successful()) {
                $data = $response->json()[0] ?? [];
                if ($data['Status'] == 'Success') {
                    return response()->json([
                        'status' => true,
                        'data' => $data['PostOffice'][0]
                    ]);
                }
            }
            throw new Exception('Pincode not found.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null
            ]);
        }
    }

    function generateOrderRequestPayload(Request $request) {
        $data['requestPayloads'] = [];
        if($request->filled('awb_number')) {
            $awbNumber = array_map('trim', explode(',', $request->awb_number));
            $requestPayloads = [];
            foreach($awbNumber as $row) {
                $order = Order::where('awb_number', $row)->first();
                $payload = null;
                switch($order->courier_partner) {
                    case 'xpressbees_sfc':
                    case 'xpressbees_surface':
                    case 'xpressbees_surface_1kg':
                    case 'xpressbees_surface_3kg':
                    case 'xpressbees_surface_5kg':
                    case 'xpressbees_surface_10kg':
                        if($order->o_type == 'forward') {
                            $payload = (new ShippingController())->generatePayloadXpressBees($order);
                        } else {
                            $payload = (new ShippingController())->generatePayloadXpressBeesReverse($order);
                        }
                        break;
                    case 'delhivery_surface':
                    case 'delhivery_surface_10kg':
                    case 'delhivery_surface_20kg':
                        $payload = (new ShippingController())->generatePayloadDelhivery($order);
                        break;
                    case 'dtdc_surface':
                    case 'dtdc_1kg':
                    case 'dtdc_2kg':
                    case 'dtdc_3kg':
                    case 'dtdc_5kg':
                    case 'dtdc_6kg':
                    case 'dtdc_10kg':
                        $payload = (new ShippingController())->_GenerateDTDCPayload($order);
                        break;
                    case 'udaan':
                    case 'udaan_1kg':
                    case 'udaan_2kg':
                    case 'udaan_3kg':
                    case 'udaan_10kg':
                        $payload = (new ShippingController())->_generateUdaanPayload($order);
                        break;
                    case 'wow_express':
                        $payload = (new ShippingController())->_generateWowExpressPayload($order);
                        break;
                    case 'shadow_fax':
                        if($order->o_type == 'forward') {
                            $payload = (new ShippingController())->_generateShadowFaxPayload($order);
                        } else {
                            $payload = (new ShippingController())->_generateShadowFaxReversePayload($order);
                        }
                        break;
                    case 'ecom_express':
                    case 'ecom_express_rvp':
                        $payload = (new EcomExpressController())->generatePayload($order);
                        break;
                    case 'ecom_express_3kg':
                    case 'ecom_express_3kg_rvp':
                        $payload = (new EcomExpress3kgController())->generatePayload($order);
                        break;
                    case 'amazon_swa':
                    case 'amazon_swa_1kg':
                    case 'amazon_swa_3kg':
                    case 'amazon_swa_5kg':
                    case 'amazon_swa_10kg':
                        $payload = (new AmazonSWA())->GeneratePayload($order);
                        break;
                    case 'ekart':
                    case 'ekart_2kg':
                    case 'ekart_1kg':
                        if($order->o_type == 'forward') {
                            $payload = (new Ekart())->_GenerateForwardPayload($order);
                        } else {
                            $payload = (new Ekart())->_GenerateReversePayload($order);
                        }
                        break;
                    case 'smartr':
                        $payload = (new Smartr())->_GenerateShipmentPayload($order);
                        break;
                    case 'gati':
                        $payload = (new Gati())->generatePayload($order);
                        break;
                    case 'shree_maruti':
                        $payload = (new Maruti())->generatePayload($order);
                        break;
                    case 'shree_maruti_ecom':
                    case 'shree_maruti_ecom_1kg':
                    case 'shree_maruti_ecom_3kg':
                    case 'shree_maruti_ecom_5kg':
                    case 'shree_maruti_ecom_10kg':
                        $payload = (new MarutiEcom())->generatePayload($order);
                        break;
                    case 'bombax':
                        $payload = (new Bombax())->generatePayload($order);
                        break;
                    case 'bluedart':
                        $payload = (new BlueDart())->generatePayload($order);
                        break;
                }
                $requestPayloads[$order->awb_number] = json_encode($payload);
            }
            $data['requestPayloads'] = $requestPayloads;
        }
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('admin.generate-order-request-payload', $data);
    }

    function courier_cod_remittance(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['logs'] = FileUploadJobModel::whereIn('job_name', ['courier_cod_remittance_upload'])->latest()->get();
        $data['partners'] = Partners::where('parent_id', 0)->get();
        return view('admin.courier-cod-remittance', $data);
    }

    function upload_courier_cod_remittance(Request $request) {
        $fileData = [];
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1]) && $test[count($test) - 1] == "csv") {
            $cnt = 0;
            $file = $_FILES['importFile']['tmp_name'];
            $handle = fopen($file, "r");
            while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                if ($cnt > 0) {
                    $fileData[] = $fileop;
                }
                $cnt++;
            }
        }

        $total = count($fileData);
        $success = 0;
        $alreadyUploaded = 0;
        // Create job
        $job = FileUploadJob::createJob('courier_cod_remittance_upload');
        foreach($fileData as $row) {
            if(in_array(strtolower($request->courier), ['ekart', 'ekart_2kg'])) {
                if(empty($row[3])) {
                    $total--;
                    continue;
                }
                $order = Order::where('awb_number', trim($row[3]))
                    ->whereIn('courier_partner', ['ekart', 'ekart_2kg'])
                    ->first();
                $data = [
                    'order_id' => $order->id ?? null,
                    'seller_id' => $order->seller_id ?? null,
                    'awb_number' => $order->awb_number ?? trim($row[3]),
                    'courier_partner' => $order->courier_partner ?? null,
                    'awb_assigned_date' => $order->awb_assigned_date ?? null,
                    'customer_order_number' => $order->customer_order_number ?? null,
                    'delivery_date' => $row[4] ?? null,
                    'due_date_of_remittance' => $row[6] ?? null,
                    'actual_date_of_remittance' => $row[7] ?? null,
                    'invoice_date' => $row[12] ?? null,
                    'bank_name' => $row[8] ?? null,
                    'bank_reference_no' => $row[9] ?? null,
                    'transaction_mode' => $row[13] ?? null,
                    'cod_amount' => $row[5] ?? 0.0,
                ];
            } else if(in_array(strtolower($request->courier), ['ecom_express','ecom_express_rvp', 'ecom_express_3kg', 'ecom_express_3kg_rvp'])) {
                if(empty($row[1])) {
                    $total--;
                    continue;
                }
                $order = Order::where('awb_number', trim($row[1]))
                    ->whereIn('courier_partner', ['ecom_express','ecom_express_rvp', 'ecom_express_3kg', 'ecom_express_3kg_rvp'])
                    ->first();
                $data = [
                    'order_id' => $order->id ?? null,
                    'seller_id' => $order->seller_id ?? null,
                    'awb_number' => $order->awb_number ?? trim($row[1]),
                    'courier_partner' => $order->courier_partner ?? null,
                    'awb_assigned_date' => $order->awb_assigned_date ?? null,
                    'customer_order_number' => $order->customer_order_number ?? null,
                    'delivery_date' => $row[12] ?? null,
                    'due_date_of_remittance' => null,
                    'actual_date_of_remittance' => null,
                    'invoice_date' => null,
                    'bank_name' => $row[14] ?? null,
                    'bank_reference_no' => $row[15] ?? null,
                    'transaction_mode' => $row[17] ?? null,
                    'cod_amount' => $row[8] ?? 0.0,
                ];
            } else if(in_array(strtolower($request->courier), ['xpressbees_sfc', 'xpressbees_surface', 'xpressbees_surface_1kg', 'xpressbees_surface_3kg', 'xpressbees_surface_5kg', 'xpressbees_surface_10kg'])) {
                if(empty($row[2])) {
                    $total--;
                    continue;
                }
                $order = Order::where('awb_number', trim(trim($row[2]), "'"))
                    ->whereIn('courier_partner', ['xpressbees_sfc', 'xpressbees_surface', 'xpressbees_surface_1kg', 'xpressbees_surface_3kg', 'xpressbees_surface_5kg', 'xpressbees_surface_10kg'])
                    ->first();
                $data = [
                    'order_id' => $order->id ?? null,
                    'seller_id' => $order->seller_id ?? null,
                    'awb_number' => $order->awb_number ?? trim(trim($row[2]), "'"),
                    'courier_partner' => $order->courier_partner ?? null,
                    'awb_assigned_date' => $order->awb_assigned_date ?? null,
                    'customer_order_number' => $order->customer_order_number ?? null,
                    'delivery_date' => !empty($row[7]) ? @now()->createFromFormat('d/m/Y', $row[7])->format('Y-m-d') ?? null : null,
                    'due_date_of_remittance' => null,
                    'actual_date_of_remittance' => !empty($row[9]) ? @now()->createFromFormat('d/m/Y', $row[9])->format('Y-m-d') ?? null : null,
                    'invoice_date' => null,
                    'bank_name' => null,
                    'bank_reference_no' => null,
                    'transaction_mode' => null,
                    'cod_amount' => $row[8] ?? 0.0,
                ];
            } else if(in_array(strtolower($request->courier), ['delhivery_surface', 'delhivery_surface_2kg', 'delhivery_surface_5kg', 'delhivery_surface_10kg', 'delhivery_surface_20kg'])) {
                if(empty($row[0])) {
                    $total--;
                    continue;
                }
                $order = Order::where('awb_number', trim(trim(trim($row[0]), '="'), '"'))
                    ->whereIn('courier_partner', ['delhivery_surface', 'delhivery_surface_2kg', 'delhivery_surface_5kg', 'delhivery_surface_10kg', 'delhivery_surface_20kg'])
                    ->first();
                $data = [
                    'order_id' => $order->id ?? null,
                    'seller_id' => $order->seller_id ?? null,
                    'awb_number' => $order->awb_number ?? trim(trim(trim($row[0]), '="'), '"'),
                    'courier_partner' => $order->courier_partner ?? null,
                    'awb_assigned_date' => $order->awb_assigned_date ?? null,
                    'customer_order_number' => $order->customer_order_number ?? null,
                    'delivery_date' => !empty($row[13]) ? @now()->parse($row[13]) ?? null : null,
                    'due_date_of_remittance' => null,
                    'actual_date_of_remittance' => null,
                    'invoice_date' => null,
                    'bank_name' => null,
                    'bank_reference_no' => $row[2] ?? null,
                    'transaction_mode' => null,
                    'cod_amount' => $row[14] ?? 0.0,
                ];
            } else if(in_array(strtolower($request->courier), ['smartr'])) {
                if(empty($row[3])) {
                    $total--;
                    continue;
                }
                $order = Order::where('awb_number', trim(trim($row[3]), 'XSE-'))
                    ->whereIn('courier_partner', ['smartr'])
                    ->first();
                $data = [
                    'order_id' => $order->id ?? null,
                    'seller_id' => $order->seller_id ?? null,
                    'awb_number' => $order->awb_number ?? trim(trim($row[3]), 'XSE-'),
                    'courier_partner' => $order->courier_partner ?? null,
                    'awb_assigned_date' => $order->awb_assigned_date ?? null,
                    'customer_order_number' => $order->customer_order_number ?? null,
                    'delivery_date' => !empty($row[7]) ? @now()->createFromFormat('d/m/Y', $row[7])->format('Y-m-d') ?? null : null,
                    'due_date_of_remittance' => null,
                    'actual_date_of_remittance' => null,
                    'invoice_date' => null,
                    'bank_name' => null,
                    'bank_reference_no' => $row[2] ?? null,
                    'transaction_mode' => null,
                    'cod_amount' => $row[8] ?? 0.0,
                ];
            }
            $payload = $data;
            $payload['job_id'] = $job->id;
            $payload['status'] = 'success';
            $payload['remark'] = null;
            if(empty($order)) {
                $payload['status'] = 'fail';
                $payload['remark'] = 'Awb details not found.';
                CourierCODRemittanceLog::create($payload);
                continue;
            }
            if(CourierCODRemittance::where([
                'order_id' => $data['order_id'],
                'seller_id' => $data['seller_id'],
                'customer_order_number' => $data['customer_order_number'],
                'awb_number' => $data['awb_number'],
                'courier_partner' => $data['courier_partner'],
                'awb_assigned_date' => $data['awb_assigned_date'],
            ])->exists()) {
                $alreadyUploaded++;
                $payload['status'] = 'fail';
                $payload['remark'] = 'COD Remittance already uploaded.';
                CourierCODRemittanceLog::create($payload);
                continue;
            }
            CourierCODRemittance::create($data);
            CourierCODRemittanceLog::create($payload);
            $success++;
        }

        if($total == 0) {
            Session([
                'notification' => [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Please upload valid file, uploaded file is empty.',
                ],
            ]);
            $status = 'error';
            $message = 'Please upload valid file, uploaded file is empty.';
        } else if($total == $alreadyUploaded) {
            Session([
                'notification' => [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'File already uploaded.',
                ],
            ]);
            $status = 'error';
            $message = 'File already uploaded.';
        } else if($success == 0) {
            Session([
                'notification' => [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Unable to import cod remittance.',
                ],
            ]);
            $status = 'error';
            $message = 'Unable to import cod remittance.';
        } else {
            Session([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => "Total {$success} out of {$total} cod remittance imported successfully.",
                ],
            ]);
            $status = 'success';
            $message = "Total {$success} out of {$total} cod remittance imported successfully.";
        }
        FileUploadJob::updateJob($job->id, [
            'status' => $status == 'success' ? 'success' : 'fail',
            'remark' => "{$request->courier} : {$message}",
            'total_records' => $total,
            'success' => $success,
            'failed' => ($total - $success),
            'already_uploaded' => $alreadyUploaded,
        ]);
        return back();
    }

    function exportCourierCodRemittanceLog(Request $request)
    {
        $data = CourierCODRemittanceLog::where('job_id', $request->job_id);
        if($request->filled('status')) {
            $data = $data->where('status', $request->status);
        }
        $data = $data->orderBy('id')->get();
        $name = "exports/courier-cod-remittance";
        $filename = "courier-cod-remittance";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'order_id',
            'seller_id',
            'awb_number',
            'courier_partner',
            'awb_assigned_date',
            'customer_order_number',
            'delivery_date',
            'due_date_of_remittance',
            'actual_date_of_remittance',
            'invoice_date',
            'bank_name',
            'bank_reference_no',
            'transaction_mode',
            'cod_amount',
            'remark',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                $e->order_id,
                $e->seller_id,
                $e->awb_number,
                $e->courier_partner,
                $e->awb_assigned_date,
                $e->customer_order_number,
                $e->delivery_date,
                $e->due_date_of_remittance,
                $e->actual_date_of_remittance,
                $e->invoice_date,
                $e->bank_name,
                $e->bank_reference_no,
                $e->transaction_mode,
                $e->cod_amount,
                $e->remark,
            );
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

    function sellerCODRemittance(Request $request) {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        if($request->filled('q')) {

            $data['remitted'] = SellerCODRemittance::whereIn('awb_number', explode(",",$request->q))->orderBy('id', 'desc')->paginate(20);
        }
        else
            $data['remitted'] = SellerCODRemittance::orderBy('id','desc')->paginate(20);
        $data['partners'] = Partners::where('parent_id', 0)->get();
        $data['sellers'] = Seller::where('verified', 'y')->get();
        return view('admin.seller-cod-remittance', $data);
    }

    function uploadSellerCODRemmitance(Request $request) {
        $fileData = [];
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1]) && $test[count($test) - 1] == "csv") {
            $cnt = 0;
            $file = $_FILES['importFile']['tmp_name'];
            $handle = fopen($file, "r");
            while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                if ($cnt > 0) {
                    if(strlen(trim($fileop[5])) > 0)
                        $fileData[] = $fileop;
                }
                $cnt++;
            }
        }

        $total = count($fileData);
        $success = 0;
        $alreadyUploaded = 0;
        foreach($fileData as $row) {
            if(empty($row[5])) {
                $total--;
                continue;
            }
            $data = [
                'order_id' => "",
                'seller_id' => null,
                'seller_code' => $row[1] ?? "",
                'seller_name' => $row[0] ?? "",
                'awb_number' => trim($row[5]) ?? "",
                'courier_partner' => trim($row[6]) ?? null,
                'awb_assigned_date' => null,
                'customer_order_number' => $row[4] ?? "",
                'delivery_date' => date('Y-m-d H:i:s',strtotime($row[2])) ?? null,
                'due_date_of_remittance' => date('Y-m-d H:i:s',strtotime($row[3])) ?? null,
                'actual_date_of_remittance' => date('Y-m-d H:i:s',strtotime($row[8])),
                'invoice_date' => null,
                'bank_name' => null,
                'bank_reference_no' => $row[9] ?? null,
                'transaction_mode' => null,
                'cod_amount' => $row[11] ?? 0.0,
                'deduction_amount' => $row[10] ?? 0,
                'remark' => $row[12] ?? null,
                'additional_remark' => $row[13] ?? null,
                'invoice_amount' => $row[7] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $payload = $data;
            if(SellerCODRemittance::where([
                'awb_number' => $data['awb_number']
            ])->exists()) {
                $alreadyUploaded++;
                $payload['status'] = 'fail';
                $payload['remark'] = 'COD Remittance already uploaded.';
//                SellerCODRemittanceLog::create($payload);
                continue;
            }
            SellerCODRemittance::create($data);
//            SellerCODRemittanceLog::create($payload);
            $success++;
        }

        if($total == 0) {
            Session([
                'notification' => [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Please upload valid file, uploaded file is empty.',
                ],
            ]);
        } else if($success == 0) {
            Session([
                'notification' => [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Unable to import cod remittance.',
                ],
            ]);
        } else {
            Session([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => "Total {$success} out of {$total} cod remittance imported successfully.",
                ],
            ]);
        }
        return back();
    }

    function exportSellerCodRemittanceLog(Request $request)
    {
        $data = SellerCODRemittanceLog::where('job_id', $request->job_id);
        if($request->filled('status')) {
            $data = $data->where('status', $request->status);
        }
        $data = $data->orderBy('id')->get();
        $name = "exports/courier-cod-remittance";
        $filename = "courier-cod-remittance";
        $fp = fopen("$name.csv", 'w');
        $info = array(
            'order_id',
            'seller_id',
            'awb_number',
            'courier_partner',
            'awb_assigned_date',
            'customer_order_number',
            'delivery_date',
            'due_date_of_remittance',
            'actual_date_of_remittance',
            'invoice_date',
            'bank_name',
            'bank_reference_no',
            'transaction_mode',
            'cod_amount',
            'remark',
        );
        fputcsv($fp, $info);
        foreach ($data as $e) {
            $info = array(
                $e->order_id,
                $e->seller_id,
                $e->awb_number,
                $e->courier_partner,
                $e->awb_assigned_date,
                $e->customer_order_number,
                $e->delivery_date,
                $e->due_date_of_remittance,
                $e->actual_date_of_remittance,
                $e->invoice_date,
                $e->bank_name,
                $e->bank_reference_no,
                $e->transaction_mode,
                $e->cod_amount,
                $e->remark,
            );
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

    function getDownloadOrderReport(){
        $data['report'] = DownloadOrderReportModel::orderBy('id','desc')->paginate(20);
        return view('admin.partial.order_download_report',$data);
    }

    // Import Serviceability for bluedartSurface
    function importServiceabilityBlueDartSurface(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_bluedart_surface',$data);
    }

     function importServiceabilityBlueDartSurfaceCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                $cnt = 0;
                ServiceablePincode::where('courier_partner','bluedart_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0){
                        $allPincodes[] = [
                            'partner_id' => 107,
                            'courier_partner' => 'bluedart_surface',
                            'pincode' => $fileop[0],
                            'is_cod' => ($fileop[6] ?? "") == "Yes" ? 'y' : 'n',
                            'city' => $fileop[2],
                            'state' => $fileop[4],
                            'cluster_code' => $fileop[9],
                            'branch_code' => $fileop[1]."/".$fileop[3],
                            'status' => 'Y',
                            'inserted' => now(),
                        ];
                        if (count($allPincodes) == 1000) {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }

     // Import Serviceability for EcomExpress
    function importServiceabilityEcom() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom',$data);
    }

    function importServiceabilityEcomCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','ecom_express')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'ecom_express')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 37,
                                'courier_partner' => 'ecom_express',
//                                'is_cod' => ($fileop[2] ?? "") == "TRUE" ? 'y' : 'n',
                                'pincode' => $fileop[0],
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for Ecom FM
    function importServiceabilityEcomFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_fm',$data);
    }

    function importServiceabilityEcomCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','ecom_express')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 37,
                            'courier_partner' => 'ecom_express',
                            'pincode' => $fileop[0],
                            'city' => $fileop[1],
                            'state' => $fileop[1],
                            'branch_code' => $fileop[1],
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for EcomExpressROS
    function importServiceabilityEcomRos() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_ros',$data);
    }

    function importServiceabilityEcomRosCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','ecom_express_rvp')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'ecom_express_rvp')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 121,
                                'courier_partner' => 'ecom_express_rvp',
                                //'is_cod' => ($fileop[2] ?? "") == "TRUE" ? 'y' : 'n',
                                'pincode' => $fileop[0],
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for Ecom FMROS
    function importServiceabilityEcomRosFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_ros_fm',$data);
    }

    function importServiceabilityEcomRosCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','ecom_express_rvp')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 121,
                            'courier_partner' => 'ecom_express_rvp',
                            'pincode' => $fileop[0],
                            'city' => $fileop[1],
                            'state' => $fileop[1],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

     // Import Serviceability for EcomExpress3Kg
    function importServiceabilityEcomThree() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_three',$data);
    }

    function importServiceabilityEcomThreeCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','ecom_express_3kg')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'ecom_express_3kg')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 47,
                                'courier_partner' => 'ecom_express_3kg',
                                //  'is_cod' => ($fileop[2] ?? "") == "TRUE" ? 'y' : 'n',
                                'pincode' => $fileop[3],
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for EcomExpressROS3Kg
    function importServiceabilityEcomRosThree() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_ros_three',$data);
    }

    function importServiceabilityEcomRosThreeCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','ecom_express_3kg_rvp')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'ecom_express_3kg_rvp')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                'partner_id' => 128,
                                'courier_partner' => 'ecom_express_3kg_rvp',
                              //  'is_cod' => ($fileop[2] ?? "") == "TRUE" ? 'y' : 'n',
                                'pincode' => $fileop[0],
                                'status' => 'Y',
                                'inserted' => now()
                            ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);

    }

    // Import Serviceability for Ecom FMROS3Kg
    function importServiceabilityEcomRosThreeFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_ecom_ros_three_fm',$data);
    }

    function importServiceabilityEcomRosThreeCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','ecom_express_3kg_rvp')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 128,
                            'courier_partner' => 'ecom_express_3kg_rvp',
                            'pincode' => $fileop[0],
                            'city' => $fileop[1],
                            'state' => $fileop[1],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }


    // Import AWBs for Bluedart
    function importAWBBluedart(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_awbs_bluedart',$data);
    }

    function importAWBBluedartCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(strlen(trim($fileop[0])) > 0) {
                            $allAwbs[] = [
                                'courier_keyword' => $request->courier_keyword,
                                'awb_type' => $request->awb_type,
                                'used' => 'n',
                                'awb_number' => trim($fileop[0]),
                                'batch_number' => $request->batch_number ?? "",
                                'inserted' => date('Y-m-d H:i:s'),
                                'inserted_by' => Session()->get('MyAdmin')->id
                            ];
                        }
                        if(count($allAwbs) == 5000){
                            BluedartAwbNumbers::insert($allAwbs);
                            $allAwbs=[];
                        }

                    }
                    $cnt++;
                }
                BluedartAwbNumbers::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    // Import AWBs for Bluedart
    function importAWBNSEBluedart(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_awbs_nse_bluedart',$data);
    }

    function importAWBNSEBluedartCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allAwbs = [];
                $cnt=0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(strlen(trim($fileop[0])) > 0) {
                            $allAwbs[] = [
                                'courier_keyword' => $request->courier_keyword,
                                'awb_type' => $request->awb_type,
                                'used' => 'n',
                                'awb_number' => trim($fileop[0]),
                                'batch_number' => $request->batch_number ?? "",
                                'inserted' => date('Y-m-d H:i:s'),
                                'inserted_by' => Session()->get('MyAdmin')->id
                            ];
                        }
                        if (count($allAwbs) == 5000) {
                            BluedartNSEAwbNumbers::insert($allAwbs);
                            $allAwbs = [];
                        }
                    }
                    $cnt++;
                }
                BluedartNSEAwbNumbers::insert($allAwbs);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'AWBs Imported successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function archiveData(Request $request){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['recent'] = ArchivedJobLogs::orderBy('id','desc')->limit(20)->get();
        return view('admin.archive-data',$data);
    }
    function runArchival(Request $request){
        $date = date('Y-m-d 00:00:00',strtotime($request->date));
        $password = MyUtility::GenerateArchivePassword();
        switch($request->type){
            case 'orders':
                //$linkToExecute = "http://localhost/TwinnshipDev/archive/orders-archive?date={$date}&password={$password}";
                $linkToExecute = "https://www.Twinnship.in/archive/orders-archive?date={$date}&password={$password}";
                $response = Http::get($linkToExecute)->json();
                return response()->json(['status' => true,'message' => 'Successful','response' => $response]);
                break;
            case 'others':
                //$linkToExecute = "http://localhost/TwinnshipDev/archive/others-archive?date={$date}&password={$password}";
                $linkToExecute = "https://www.Twinnship.in/archive/others-archive?date={$date}&password={$password}";
                $response = Http::get($linkToExecute)->json();
                return response()->json(['status' => true,'message' => 'Successful','response' => $response]);
                break;
            case 'pending_orders':
                //$linkToExecute = "http://localhost/TwinnshipDev/archive/pending-orders-archive?date={$date}&password={$password}";
                $linkToExecute = "https://www.Twinnship.in/archive/pending-orders-archive?date={$date}&password={$password}";
                $response = Http::get($linkToExecute)->json();
                return response()->json(['status' => true,'message' => 'Successful','response' => $response]);
                break;
            default:
                return response()->json(['status' => false,'message' => 'Please Try with proper Input']);
                break;
        }
    }

    function getMessageCounter(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['sellers'] = Seller::where('verified','y')->get();
        return view('admin.message-counter',$data);
    }

    function submitMessageCounter(Request $request){
        $data['seller_name'] = $request->seller_name;
        $data['seller_id'] = $request->seller;
        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;
        $data['message_type'] = $request->message_type;
        if($request->message_type == 'whatsapp'){
            $data['counter'] = OrderWhatsAppMessageLogs::whereDate('sent_datetime','>=',$request->start_date)->whereDate('sent_datetime','<=',$request->end_date)->where('seller_id',$request->seller)->count();
        }
        else{
            $data['counter'] = OrderSMSLogs::whereDate('sent_datetime','>=',$request->start_date)->whereDate('sent_datetime','<=',$request->end_date)->where('seller_id',$request->seller)->count();
        }
        return view('admin.partial.message-counter',$data);
    }

    function markFailedOrderJob($id){
        DownloadOrderReportModel::where('id',$id)->update(['status' => 'falied']);
        return redirect()->back();
    }


    function getSellerRemitDetails($id){
        $data = SellerCODRemittance::find($id);
         return ($data);
    }

    // Import Serviceability for Professional Fm
    function importServiceabilityProfessionalFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_professional_fm',$data);
    }

    function importServiceabilityProfessionalCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','tpc_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 135,
                            'courier_partner' => 'tpc_surface',
                            'pincode' => $fileop[0],
                            'city' => '',
                            'state' => '',
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }


// Import Serviceability for Professiional
    function importServiceabilityProfessional(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_professional',$data);
    }

    function importServiceabilityProfessionalCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                $cnt = 0;
                ServiceablePincode::where('courier_partner','tpc_surface')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0){
                        $allPincodes[] = [
                            'partner_id' => 135,
                            'courier_partner' => 'tpc_surface',
                             'pincode' => $fileop[0],
                            'is_cod' => 'n',
                            'city' => $fileop[2],
                            'state' => $fileop[3],
                            'branch_code' => $fileop[1],
                            'status' => 'Y',
                            'inserted' => now(),
                        ];
                        if (count($allPincodes) == 1000) {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }


    // Import Serviceability for Delhivery Heavey
    function importServiceabilityDelhiveryHeavey(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_delhiveryheavey',$data);
    }

    function importServiceabilityDelhiveryHeaveyCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $allPincodes = [];
                $cnt = 0;
                ServiceablePincode::where('courier_partner','delhivery_surface_10kg')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0){
                        $allPincodes[] = [
                            'partner_id' => 45,
                            'courier_partner' => 'delhivery_surface_10kg',
                            'pincode' => $fileop[0],
                            'city' => $fileop[3],
                            'state' => $fileop[4],
                            'branch_code' => $fileop[2],
                            'status' => 'Y',
                            'inserted' => now(),
                        ];
                        if (count($allPincodes) == 1000) {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
    }

    function deleteArchiveOrder(){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.archive_order_delete',$data);
    }

    function submitDeleteArchiveOrder(Request $request){
        if(!empty($request->awbs)) {
            $awb = explode(',', $request->awbs);
            $set = [];
            foreach ($awb as $a) {
                $set[] = $a;
                if (count($set) == 200) {
                    ZZArchiveOrder::whereIn('awb_number', $set)->update(['delivery_stutus' => 0]);
                    $set = [];
                }
            }
            if (count($set) > 0) {
                ZZArchiveOrder::whereIn('awb_number', $set)->update(['delivery_stutus' => 0]);
            }
        }
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Order Deleted Successfully',
            ),
        );
        Session($notification);
        return back();
    }

    public function ApprovalRequestBySales(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['rates_card'] = RatesCardRequest::orderBy('id','desc')->get();
        return view('admin.rate_card_approval', $data);
    }

    public function FetchSellerRates($id)
    {
        $data['ratesData'] = RateCardRequestData::leftJoin('partners','partners.id','rates_card_request_data.partner_id')->where('request_id', $id)->select('rates_card_request_data.*','partners.keyword','partners.title')->get();
        return view('admin.seller_rates_modal', $data);
    }

    public function RejectStatus($id)
    {
        $data=array(
            'status' => "rejected"
        );
        RatesCardRequest::where('id',$id)->update($data);
        return response()->json(['status' => 'true']);
    }

    public function ApproveStatus($id)
    {
        $rateRequest = RatesCardRequest::find($id);
        $allRates = RateCardRequestData::where('request_id',$id)->get();
        $this->DumpOldSellerRates($rateRequest->seller_id,$rateRequest->plan_id);
        Rates::where('plan_id', $rateRequest->plan_id)->where('seller_id', $rateRequest->seller_id)->delete();
        Courier_blocking::where('seller_id',$rateRequest->seller_id)->delete();
        $insertData = [];
        foreach ($allRates as $r){
            if(!empty($r->within_city) && !empty($r->within_state) && !empty($r->metro_to_metro) && !empty($r->rest_india) && !empty($r->north_j_k))
            {
                $insertData[]= [
                    'plan_id' => $rateRequest->plan_id,
                    'seller_id' => $rateRequest->seller_id,
                    'partner_id' => $r->partner_id,
                    'within_city' => $r->within_city,
                    'within_state' => $r->within_state,
                    'metro_to_metro' => $r->metro_to_metro,
                    'rest_india' => $r->rest_india,
                    'north_j_k' => $r->north_j_k,
                    'cod_charge' => $r->cod_charge,
                    'cod_maintenance' => $r->cod_maintenance,
                    'extra_charge_a' => $r->extra_charge_a,
                    'extra_charge_b' => $r->extra_charge_b,
                    'extra_charge_c' => $r->extra_charge_c,
                    'extra_charge_d' => $r->extra_charge_d,
                    'extra_charge_e' => $r->extra_charge_e
                ];
            }
            else{
                // Block the Courier Partner Here
                Courier_blocking::create([
                    'seller_id' => $rateRequest->seller_id,
                    'courier_partner_id' => $r->partner_id,
                    'is_blocked' => 'y',
                    'is_approved' => 'y',
                    'zone_a' => 'y',
                    'zone_b' => 'y',
                    'zone_c' => 'y',
                    'zone_d' => 'y',
                    'zone_e' => 'y',
                    'cod' => 'y',
                    'prepaid' => 'y',
                    'remark' => 'Blocked Via Rate Card Accept',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        // code here
        $data=array(
            'status' => "approved"
        );
        RatesCardRequest::where('id',$id)->update($data);
        Rates::insert($insertData);
        return response()->json(['status' => 'true']);
    }
    function DumpOldSellerRates($sellerId,$planId){
        $sellerRate = SellerRateChanges::create([
            'seller_id' => $sellerId,
            'modified' => date('Y-m-d H:i:s'),
            'modified_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
        ]);
        $rates = [];
        $oldRates = Rates::where('seller_id',$sellerId)->where('plan_id',$planId)->get();
        foreach ($oldRates as $o){
            $rates []= [
                'plan_id' => $planId,
                'seller_rate_change_id' => $sellerRate->id,
                'seller_id' => $sellerId,
                'partner_id' => $o->partner_id,
                'within_city' => $o->within_city,
                'within_state' => $o->within_state,
                'metro_to_metro' => $o->metro_to_metro,
                'rest_india' => $o->rest_india,
                'north_j_k' => $o->north_j_k,
                'cod_charge' => $o->cod_charge,
                'cod_maintenance' => $o->cod_maintenance,
                'extra_charge_a' => $o->extra_charge_a,
                'extra_charge_b' => $o->extra_charge_b,
                'extra_charge_c' => $o->extra_charge_c,
                'extra_charge_d' => $o->extra_charge_d,
                'extra_charge_e' => $o->extra_charge_e,
                'inserted' => date('Y-m-d H:i:s')
            ];
        }
        SellerRateChangeDetails::insert($rates);
        return true;
    }
     public function importZone(Request $request)
    {
        $utilities = new Utilities();
        try {
            if (!$request->hasfile('excel')) {

                $utilities->generate_notification('Error', 'Please upload excel file', 'error');
                return back();
            }
            Excel::import(new ZoneImports, $request->file('excel')->store('temp'));
            // Generating notification
            return back();
        } catch (Exception $e) {
            // Generating notification
            $utilities->generate_notification('Error', $e->getMessage(), 'error');
            return back();
        }
    }
    function export_pincode($partner){
        $name = "exports/LM Pincode";
        $filename = "LM Pincode";
        $all_data = ServiceablePincode::where('courier_partner',$partner)->where('active','y')->get();
        //dd($all_data);
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No','Pincode','Courier');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            //dd($e);
            $info = array($cnt,$e->pincode,$e->courier_partner);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$filename.csv");
        // Output file.
        readfile("$name.csv");
    }
    function export_fm_pincode($partner){
        $name = "exports/FM Pincode";
        $filename = "FM Pincode";
        $all_data = ServiceablePincodeFM::where('courier_partner',$partner)->get();
        //dd($all_data);
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No','Pincode','Courier');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            //dd($e);
            $info = array($cnt,$e->pincode,$e->courier_partner);
            fputcsv($fp, $info);
            $cnt++;
        }
        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$filename.csv");
        // Output file.
        readfile("$name.csv");
    }


    function importServiceabilityShadowfaxFM() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_shadowfax_fm',$data);
    }

    function importServiceabilityShadowfaxCsvFM(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                $allPincodes = [];
                ServiceablePincodeFM::where('courier_partner','shadowfax')->delete();
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        $allPincodes[]= [
                            'partner_id' => 11,
                            'courier_partner' => 'shadowfax',
                            'pincode' => $fileop[0],
                            'city' => $fileop[1],
                            'state' => $fileop[2],
                            'branch_code' => '',
                            'status' => 'Y',
                            'inserted' => now()
                        ];
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincodeFM::insert($allPincodes);
                            $allPincodes = [];
                        }

                    }
                    $cnt++;
                }
                ServiceablePincodeFM::insert($allPincodes);
            }
        }
    }

    // Import Serviceability for Amazon SWA
    function importServiceabilityShadowfax() {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.import_serviceability_shadowfax',$data);
    }


    function importServiceabilityShadowfaxCsv(Request $request){
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if (strtolower($test[count($test) - 1]) == "csv") {
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $cnt=0;
                ServiceablePincode::where('courier_partner','shadowfax')->where('active','y')->delete();
                $disabledPincode = ServiceablePincode::where('courier_partner', 'shadowfax')->where('active', 'n')->pluck('pincode')->toArray();
                $allPincodes = [];
                $alreadyBlockedPincodes = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if($cnt > 0)
                    {
                        if(!in_array($fileop[0],$disabledPincode))
                        {
                            $allPincodes[]= [
                                    'partner_id' => 11,
                                    'courier_partner' => 'shadowfax',
                                    'pincode' => $fileop[0],
                                    'city' => $fileop[1],
                                    'state' => $fileop[2],
                                    'branch_code' => '',
                                    'status' => 'Y',
                                    'inserted' => now()
                                ];
                        }else{
                            // Already Blocked Pincodes will be listed here
                            $alreadyBlockedPincodes [] = $fileop[0];
                        }
                        if(count($allPincodes) == 1000)
                        {
                            ServiceablePincode::insert($allPincodes);
                            $allPincodes = [];
                        }
                    }
                    $cnt++;
                }
                ServiceablePincode::insert($allPincodes);
            }
        }
        return response()->json(['status' => true,'pincodes' => $alreadyBlockedPincodes]);
    }

    function weightReconciliationRevoke(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.weight_reconciliation_revoke', $data);
    }
    function importCsvWeightReconciliationRevoke(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $totalRecords = 0;
                $success = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    DB::beginTransaction();
                    try {
                        if ($cnt > 0) {
                            if ($fileop[0] != "") {
                                $totalRecords++;
                                $awbNumber = trim($fileop[0], '`');
                                $order = Order::where('awb_number', $awbNumber)->first();
                                if(empty($order))
                                    $order = ZZArchiveOrder::where('awb_number', $awbNumber)->first();
                                if (empty($order)) {
                                    $notification = [
                                        'notification' => [
                                            'type' => 'Error',
                                            'title' => 'Error',
                                            'message' => 'Order Not Found.',
                                        ],
                                    ];
                                    Session($notification);
                                    // Create job log
                                } else {
                                    $existingRecord = WeightReconciliation::where('awb_number', $awbNumber)->first();
                                    if(empty($existingRecord))
                                        continue;
                                    $extraChargeAmount = $existingRecord->charged_amount - $order->total_charges;
                                    // Credit Seller Balance Back
                                    $seller = Seller::where('id', $order->seller_id)->first();
                                    $data = array(
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'amount' => $extraChargeAmount,
                                        'balance' => $seller->balance + $extraChargeAmount,
                                        'type' => 'c',
                                        'redeem_type' => 'o',
                                        'datetime' => date('Y-m-d H:i:s'),
                                        'method' => 'wallet',
                                        'description' => 'Weight Reconciliation Revoke'
                                    );
                                    Transactions::create($data);
                                    Seller::where('id', $order->seller_id)->increment('balance', $extraChargeAmount);

                                    // mark order as fresh
                                    Order::where('id', $order->id)->update(['weight_disputed' => 'n']);
                                    ZZArchiveOrder::where('id', $order->id)->update(['weight_disputed' => 'n']);
                                    WeightReconciliation::where('id', $existingRecord->id)->delete();
                                    $success++;
                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        continue;
                    }
                    $cnt++;
                }
                if($success == $totalRecords) {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $totalRecords . ' weight reconciliation updated successfully';
                } else if($success == 0) {
                    $type = 'error';
                    $title = 'Error';
                    $message = 'Weight reconciliation not updated';
                } else {
                    $type = 'success';
                    $title = 'Success';
                    $message = 'Total ' . $success . ' out of ' . $totalRecords . ' weight reconciliation revoked and '.($totalRecords-$success).' failed';
                }
                $notification = array(
                    'notification' => array(
                        'type' => $type,
                        'title' => $title,
                        'message' => $message,
                    ),
                );
                Session($notification);
                return back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
        return false;
    }

    function getNDRData(Request $request){
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['orders'] = Order::join('sellers', 'orders.seller_id', '=', 'sellers.id')
            ->select(
                'orders.*',
                'sellers.company_name',
                'sellers.code'
            )
            ->where('orders.awb_number', '!=', '')->where('orders.ndr_status','y')->where('orders.ndr_action', 'requested')->whereNotIn('orders.status', ['delivered', 'rto_delivered'])->with('sellerNdrAction')->orderBY('awb_assigned_date', 'desc')->get();
        return view('admin.seller_ndr_action', $data);
    }
    function generateSellerInvoice(Request $request)
    {
        $response = false;
        try{
            foreach ($request->sellers as $seller){
                $response = InvoiceHelper::GenerateInvoice($request->seller_id, $request->from_date, $request->to_date, $request->invoice_date);
            }
        }catch(Exception $e){
            $response = false;
        }
        if ($response)
            $this->utilities->generate_notification('Success', 'Invoice Generated Successfully!!', 'success');
        else
            $this->utilities->generate_notification('Error', 'Something went wrong please connect with technical team!!', 'error');
        return redirect()->back();
    }

    public function remittance_admin(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master'] = Master::all();
        $data['sellers'] = Seller::all();
        session(['rm_seller_id' => $request->seller_id]);

        $data['cod_total'] = 0;
        $data['remitted_cod'] = 0;
        $data['nextRemitDate'] = '-';
        $data['nextRemitCod'] = 0;

        if (!empty($request->seller_id)) {
            $data['seller']=Seller::where('id', $request->seller_id)->first();
            $data['cod_total'] = round(
                Order::where('seller_id', $request->seller_id)
                    ->where('order_type', 'cod')
                    ->where('status', 'delivered')
                    ->where('rto_status', 'n')
                    ->sum('invoice_amount'),
                2
            );
            $data['remitted_cod'] = round(
                Order::where('seller_id', $request->seller_id)
                    ->where('order_type', 'cod')
                    ->where('rto_status', 'n')
                    ->where('status', 'delivered')
                    ->where('cod_remmited', 'y')
                    ->sum('invoice_amount'),
                2
            );
            $codArray = $this->utilities->getNextCodRemitDate($request->seller_id);
            $data['nextRemitDate'] = $codArray['nextRemitDate'];
            $data['nextRemitCod'] = round($codArray['nextRemitCod'], 2);
            $data['display'] = 'd-none';
        }
        return view('admin.remittance', $data);
    }


}
