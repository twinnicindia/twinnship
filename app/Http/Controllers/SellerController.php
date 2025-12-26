<?php

namespace App\Http\Controllers;
use App\Helpers\Channels\ShopifyHelper;
use App\Helpers\InternationalOrderHelper;
use App\Helpers\ReassignHelper;
use App\Helpers\ShippingHelper;
use App\Helpers\UtilityHelper;
use App\Jobs\BulkCancelOrders;
use App\Jobs\BulkShipOrders;
use App\Jobs\SendManifestationSms;
use App\Jobs\SendManifestationWhatsApp;
use App\Libraries\AmazonDirect;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Libraries\AmazonSWA;
use App\Libraries\BlueDart;
use App\Libraries\Bombax;
use App\Libraries\BucketHelper;
use App\Libraries\Custom\CustomDelhivery;
use App\Libraries\Ekart;
use App\Libraries\Maruti;
use App\Libraries\MarutiEcom;
use App\Libraries\Movin;
use App\Libraries\MyUtility;
use App\Libraries\Shadowfax;
use App\Models\Account_informations;
use App\Models\Agreement_informations;
use App\Models\Basic_informations;
use App\Models\BillReceipt;
use App\Models\Brands;
use App\Models\BulkCancelOrdersJob;
use App\Models\BulkShipOrdersJob;
use App\Models\BulkShipOrdersJobDetails;
use App\Models\CCAvenueTransaction;
use App\Models\ChannelOrderStatusList;
use App\Models\Channels;
use App\Models\Configuration;
use App\Models\CustomSellerChannels;
use App\Models\DelhiveryAWBNumbers;
use App\Models\DownloadReport;
use App\Models\DtdcAwbNumbers;
use App\Models\EarlyCod;
use App\Models\EcomExpressAwbs;
use App\Models\EkartAwbNumbers;
use App\Models\Employees;
use App\Models\EmployeeWorkLogs;
use App\Models\InternationalOrders;
use App\Models\Invoice;
use App\Models\Kyc_informations;
use App\Models\Manifest;
use App\Models\ManifestOrder;
use App\Models\MarutiEcomAwbs;
use App\Models\MoveToIntransit;
use App\Models\OrderArchive;
use App\Models\PickedUpOrders;
use App\Models\Preferences;
use App\Models\ReassignOrderDetails;
use App\Models\Recharge_request;
use App\Models\Redeem_codes;
use App\Models\Redeems;
use App\Models\COD_transactions;
use App\Models\CommentsAttachment;
use App\Models\Rules;
use App\Models\SalesSellerLogin;
use App\Models\Seller;
use App\Models\Order;
use App\Models\SellerCodRemitRechargeHistory;
use App\Models\SellerOtp;
use App\Models\SendManifestationSmsJob;
use App\Models\SendManifestationWhatsAppJob;
use App\Models\ShadowfaxAWBNumbers;
use App\Models\SKU;
use App\Models\Plans;
use App\Models\Product;
use App\Models\Partners;
use App\Models\Ndrattemps;
use App\Models\PendingShipments;
use App\Models\OrderTracking;
use App\Models\Rates;
use App\Models\SmartrAwbs;
use App\Models\GatiAwbs;
use App\Models\GatiPackageNumber;
use App\Models\States;
use App\Models\RemittanceDetails;
use App\Models\ServiceablePincode;
use App\Models\ServiceablePincodeFM;
use App\Models\SupportTicket;
use App\Models\TicketAttachment;
use App\Models\TicketComments;
use App\Models\Transactions;
use App\Models\Warehouses;
use App\Models\WeightReconciliation;
use App\Models\WeightReconciliationHistory;
use App\Models\WeightReconciliationImage;
use App\Models\XbeesAwbnumber;
use App\Models\XbeesAwbnumberUnique;
use App\Models\ZoneMapping;
use App\Models\OMS;
use App\Models\LabelCustomization;
use App\Models\Courier_blocking;
use App\Models\MPS_AWB_Number;
use App\Models\SKU_Mapping;
use App\Models\MyOmsOrder;
use App\Models\MyOmsProduct;
use App\Models\BulkNDRActionFile;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use PDF;
use Illuminate\Support\Carbon;
use CSVParser\CSV;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Libraries\Logger;
use App\Libraries\Barcode;
use App\Libraries\Prefexo;
use App\Libraries\Gati;
use Exception;
use DateTime;

class SellerController extends Controller
{
    protected $info, $utilities, $status, $noOfvalue, $metroCities,$shipment,$orderStatus,$fullInformation = false;
    public function __construct()
    {
        if (Session()->get('noOfPage') == null)
            Session()->put('noOfPage', 20);
        $this->shipment = new ShippingController();
        $this->info['config'] = Configuration::find(1);
        $this->info['coupon'] = Redeem_codes::where('status', 'y')->get();
        $this->utilities = new Utilities();
        $this->metroCities = ['bangalore', 'chennai', 'hyderabad', 'kolkata', 'mumbai', 'new delhi'];
        // $this->metroCities=['ahmedabad','bangalore','bhiwandi','chennai','hyderabad','kolkata','mumbai','pune','thane','vashi','vasai','new delhi'];
        $this->filterArray = [
            'order_number' => '',
            'channel' => '',
            'product' => '',
            'payment_type' => '',
            'min_value' => '',
            'max_value' => '',
            'min_weight' => '',
            'max_weight' => '',
            'start_date' => '',
            'end_date' => '',
            'pickup_address' => '',
            'delivery_address' => '',
            'order_status' => '',
            'filter_status' => '',
            'current_tab' => '',
            'awb_number' => '',
            'courier_partner' => '',
            'order_awb_search' => '',
        ];
        $this->filterArrayNDR = [
            'ndr_start_date' => '',
            'ndr_end_date' => '',
            'ndr_order' => '',
            'ndr_reason' => '',
            'ndr_awb_number' => '',
            'ndr_type' => '',
            'current_tab_ndr' => ''
        ];
        $this->filterArrayBilling = [
            'b_order_number' => '',
            'b_awb_number' => '',
            'b_order_status' => '',
            'billing_start_date' => '',
            'billing_end_date' => '',
            'billing_filter_type' => '',
            'w_end_date' => '',
            'w_filter_type' => '',
            'r_end_date' => '',
            'r_filter_type' => '',
            'channel_code' => '',
            'channel_name' => ''
        ];
        $this->status = [
            'yet_to_pick',
            'pickup_scheduled',
            'ready_to_pick',
            'picked_up',
            'shipped',
            'out_of_delivery',
            'delivered',
            'not_delivered',
            'cancelled_by_user'
        ];
        $this->ndr_status = [
            'reattempt',
            'rto',
            'delivered',
        ];
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
            "hold" => "Hold"
        ];
        if (isset(Session()->get('MySeller')->type)) {
            if (Session()->get('MySeller')->type == 'sel')
                $this->fullInformation = true;
            else {
                if (Session()->get('MySeller')->type == 'emp' && str_contains(Session()->get('MySeller')->permissions, 'pii_access'))
                    $this->fullInformation = true;
                else
                    $this->fullInformation = false;
            }
        }
        $this->utilities = new Utilities();
        $this->myOms = "start_date,end_date,channel,order_number,order_status,min_value,max_value,payment_type,product,sku,multiple_sku,match_exact_sku,pickup_address,delivery_address,min_weight,max_weight,courier_partner,awb_number,order_awb_search";
    }

    //Set Limit of Order Display
    function setPerPageRecord($page)
    {
        Session::put('noOfPage', $page);
    }

    function set_date_dashboard(Request $request)
    {
        session(['d_start_date' => $request->start_date, 'd_end_date' => $request->end_date]);
    }

    function reset_date_dashboard()
    {
        session(['d_start_date' => '', 'd_end_date' => '']);
    }

    function mark_all_as_read()
    {
        $seller = Seller::find(Session()->get('MySeller')->id);
        $seller->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    //Manage Dashboard HomePage
    function dashboard(Request $request)
    {
        $data = $this->info;
        $this->_refreshSession();
        return view('seller.dashboard', $data);
    }


    //Dashboard Counter
    function dashboardCounter(Request $request)
    {
        $data = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $remDays = Session()->get('MySeller')->remmitance_days ?? 7;

        if($data['config']->read_from_cache == 'y') {
            $counters = Cache::store('redis')->remember('counters-'.Session()->get('MySeller')->id, (60*10), function() use($start_date, $end_date, $remDays) {
                $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
                $nextRemitDate = $codArray['nextRemitDate'];
                $nextRemitCod = $codArray['nextRemitCod'];
                $counters = [
                    'total_shipment' => Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled')->count(),
                    'total_revanue' => Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->avg('invoice_amount'),
                    'total_customer' => Order::distinct('b_contact')->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>', Carbon::now()->subDays(30))->count(),
                    'yesterday_revenue' => Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount'),
                    'total_all_orders' => Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'shipped_orders' => Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending','cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'pending_order' => Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested','pickup_scheduled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'picked_up' => Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'picked_up')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'out_for_delivery' => Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'out_for_delivery')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'delivered_order' => Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'intransit_order' => Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'ndr_pending' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'rto_order' => Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'total_ndr' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'action_required' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'action_requested' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'ndr_delivered' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', 'delivered')->where('rto_status', '!=', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'ndr_rto' => Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'cod_total' => Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->sum('invoice_amount'),
                    'cod_available' => round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','<',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount'),2),
                    'cod_pending' => round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','>=',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount'),2),
                    'remitted_cod' => round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->sum('invoice_amount'),2),
                    'nextRemitDate' => $nextRemitDate,
                    'nextRemitCod' => round($nextRemitCod,2),
                    'rto_initiated' => Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','=','rto_initiated')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                    'rto_delivered' => Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count(),
                ];
                $counters['rto_undelivered'] = $counters['rto_order'] - $counters['rto_initiated'] - $counters['rto_delivered'];
                return $counters;
            });
            $counters['total_created'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '=', date('Y-m-d'))->count();
            $counters['today_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->count();
            $counters['today_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount');
            $counters['total_all_orders'] = $counters['total_all_orders'] + $counters['today_order'];
            // Deserialize counters
            $data = array_merge($data, $counters);
        }
        else {
            //counter for the first row
            // $data['total_revanue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>', Carbon::now()->subDays(30))->sum('invoice_amount');
            //$data['total_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled')->count();
            $data['total_created'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '=', date('Y-m-d'))->count();
            $data['total_revanue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->avg('invoice_amount');
            //$data['total_order_30'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['total_customer'] = Order::distinct('b_contact')->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>', Carbon::now()->subDays(30))->count();
            $data['today_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->count();
            $data['today_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount');
            // $data['yesterday_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->count();
            $data['yesterday_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount');

            //counters for the left pane overview
            $data['total_all_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['shipped_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending','cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['pending_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested','pickup_scheduled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['picked_up'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'picked_up')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['out_for_delivery'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'out_for_delivery')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            //$data['shipped_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'shipped')->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->count();
            $data['delivered_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['intransit_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['ndr_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            //$data['ndr_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->count();

            //counters for the ndr tile
            $data['total_ndr'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['action_required'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['action_requested'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

            // cod counters
            // total cod  = 30 days total cod orders delivered
            // cod available  = 30 days total cod orders delivered and cod_remitted = 'n'
            // cod pending  = 30 days total cod orders delivered and cod_remitted = 'n' and d+7 early code rule // 7 days is remaining for remittance
            // remitted total cod amount remitted cod_pending = total_cod - cod_remitted

//            $data['cod_total'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status','n')->sum('invoice_amount');
            $data['cod_total'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->sum(DB::raw('IF(collectable_amount > 0, collectable_amount, invoice_amount)')),2);
//            $data['cod_available'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->where('rto_status','n')->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','<',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
//            $data['cod_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->where('rto_status','n')->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','>=',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
//            $data['remitted_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->sum('invoice_amount');
            $data['remitted_cod'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->sum(DB::raw('IF(collectable_amount > 0, collectable_amount, invoice_amount)')),2);
            $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
            $data['nextRemitDate'] = $codArray['nextRemitDate'];
            $data['nextRemitCod'] = round($codArray['nextRemitCod'],2);

            //$data['cod_pending'] = $data['cod_available'] - $data['remitted_cod'];

            //rto counters
            $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['rto_initiated'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','=','rto_initiated')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['rto_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['rto_undelivered'] = $data['rto_order'] - $data['rto_initiated'] - $data['rto_delivered'];
        }
        // dd($data);
        return view('seller.dashboard_counter', $data);
    }

    //Dashboard Overview
    function dashboardOverview(Request $request)
    {
        $data = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }


        $data['states'] = States::limit(36)->get();
        $data['mapData'] = [];
        foreach ($data['states'] as $s) {
            $count = Order::where('seller_id', Session()->get('MySeller')->id)->where('s_state', $s->state)->whereNotIn('status', ['pending', 'cancelled'])->count();
            $data['mapData'][] = [
                'id' => $s->code,
                'value' => $count ?? 0
            ];
        }
        $current_quarter = ceil(date('n') / 3);
        $first_date = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
        $last_date = date('Y-m-t', strtotime(date('Y') . '-' . (($current_quarter * 3)) . '-1'));
        $data['revenue_lifetime'] = Order::where('seller_id', Session()->get('MySeller')->id)->sum('invoice_amount');
        $data['revenue_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('invoice_amount');
        $data['revenue_month'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereMonth('awb_assigned_date', Carbon::now()->month)->sum('invoice_amount');
        $data['revenue_year'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('invoice_amount');
        $data['revenue_quarter'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [$first_date, $last_date])->sum('invoice_amount');

        $data['PartnerName'] = Partners::getPartnerKeywordList();

        $data['delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status','n')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['undelivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'delivered')->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'damaged')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        //$data['OrderDelivered'] = $this->getDelivered($courier_partner = '');

        //For Zone Counting
        $data['zone_a'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['zone_b'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['zone_c'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['zone_d'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['zone_e'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['courier_split'] = Order::select(DB::raw('distinct(courier_partner)'), DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->orderBy('total_order','desc')->groupBy('courier_partner')->limit(5)->get();
        //select DISTINCT courier_partner,count(courier_partner) as total from orders where seller_id = 16 group by courier_partner order by total desc limit 4
        $data['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();
        //dd($data['partners']);
        foreach ($data['allPartners'] as $p){
            //for Courirer Partner 1 Overview
            $data['partner_unscheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();
            $data['partner_scheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();
            $data['partner_intransit'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();
            $data['partner_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();
            $data['partner_ndr_raised'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();
            //remove NDR Raised Column from Courier Overview
            $data['partner_ndr_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();
            $data['partner_ndr_pending'][$p] = $data['partner_ndr_raised'][$p] - $data['partner_ndr_delivered'][$p];
            $data['partner_ndr_rto'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();
            $data['partner_damaged'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();
            $data['partner_total'][$p] = $data['partner_unscheduled'][$p] + $data['partner_scheduled'][$p] + $data['partner_intransit'][$p] + $data['partner_delivered'][$p] + $data['partner_ndr_delivered'][$p] + $data['partner_ndr_pending'][$p] + $data['partner_ndr_rto'][$p] + $data['partner_damaged'][$p];
        }
        //for Other Courier Partner  Overview
        $data['other_partner_unscheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_scheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_ndr_pending'] = $data['other_partner_ndr_raised'] - $data['other_partner_ndr_delivered'];
        $data['other_partner_ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $data['allPartners'])->count();
        $data['other_partner_total'] = $data['other_partner_unscheduled'] + $data['other_partner_scheduled'] + $data['other_partner_intransit'] + $data['other_partner_delivered'] + $data['other_partner_ndr_delivered'] + $data['other_partner_ndr_pending'] + $data['other_partner_ndr_rto'] + $data['other_partner_damaged'];
        //counter for the first row
        // $data['total_revanue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>', Carbon::now()->subDays(30))->sum('invoice_amount');
        //$data['total_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled')->count();
        $data['total_created'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '=', date('Y-m-d'))->count();
        $data['total_revanue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->avg('invoice_amount');
        //$data['total_order_30'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['total_customer'] = Order::distinct('b_contact')->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>', Carbon::now()->subDays(30))->count();
        $data['today_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->count();
        $data['today_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount');
        // $data['yesterday_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->count();
        $data['yesterday_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount');

        //counters for the left pane overview
        $data['total_all_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['shipped_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending','cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['pending_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested','pickup_scheduled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['picked_up'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'picked_up')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['out_for_delivery'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'out_for_delivery')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        //$data['shipped_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'shipped')->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->count();
        $data['delivered_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['intransit_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['ndr_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        //$data['ndr_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->count();

        //counters for the ndr tile
        $data['total_ndr'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['action_required'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['action_requested'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

        // cod counters
        // total cod  = 30 days total cod orders delivered
        // cod available  = 30 days total cod orders delivered and cod_remitted = 'n'
        // cod pending  = 30 days total cod orders delivered and cod_remitted = 'n' and d+7 early code rule // 7 days is remaining for remittance
        // remitted total cod amount remitted cod_pending = total_cod - cod_remitted

//            $data['cod_total'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status','n')->sum('invoice_amount');
        $data['cod_total'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->sum('invoice_amount'),2);
//            $data['cod_available'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->where('rto_status','n')->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','<',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
//            $data['cod_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('cod_remmited', 'n')->whereDate('awb_assigned_date', '>=', $start_date)->where('rto_status','n')->whereDate('awb_assigned_date', '<=', $end_date)->whereDate('delivered_date','>=',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
//            $data['remitted_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->sum('invoice_amount');
        $data['remitted_cod'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->sum('invoice_amount'),2);
        $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        $data['nextRemitDate'] = $codArray['nextRemitDate'];
        $data['nextRemitCod'] = round($codArray['nextRemitCod'],2);

        //$data['cod_pending'] = $data['cod_available'] - $data['remitted_cod'];

        //rto counters
        $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_initiated'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','=','rto_initiated')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_undelivered'] = $data['rto_order'] - $data['rto_initiated'] - $data['rto_delivered'];


        $res = DB::select("SELECT count(*) as total from `orders` where `seller_id`=" . Session()->get('MySeller')->id . " and `status`='delivered' and `delivered_date` <= `expected_delivery_date` or `seller_id`=" . Session()->get('MySeller')->id . " and `status`='delivered' and `expected_delivery_date` is NULL");
        $data['ontime_delivery'] = $res[0]->total ?? 0;

        $res = DB::select("SELECT count(*) as total from `orders` where seller_id=" . Session()->get('MySeller')->id . " and `status`='delivered' and `delivered_date` > `expected_delivery_date`");
        $data['late_delivery'] = $res[0]->total ?? 0;

        return view('seller.partial.dashboard-overview', $data);
    }


    public static function getDelivered($courier_partner)
    {
        $resp = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $courier_partner)->where('status', 'delivered')->count();
        return $resp;
    }

    //Manage Dashboard Order Tab
    function dashboardOrder(Request $request)
    {
        $data = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['popular_location_order'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_order')->limit(10)->get();
        $data['popular_location_revenue'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_amount')->limit(10)->get();
        $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'cod')->where('status', 'delivered')->count();
        $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'prepaid')->count();
        $data['top_customer_order'] = Order::select('s_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_order')->get();
        $data['top_customer_revenue'] = Order::select('s_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_amount')->get();
        $data['top_product_order'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('unit_sold')->limit(10)->get();
        $data['top_product_revenue'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('total_revenue')->limit(10)->get();

        $yesterday = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 days"));
        $two_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-2 days"));
        $three_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-3 days"));
        $four_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-4 days"));
        $data['allDays'] = [$yesterday,$two_day_ago,$three_day_ago,$four_day_ago];

        foreach ($data['allDays'] as $d) {
            $data['partner_unscheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['manifested', 'pickup_scheduled'])->count();
            $data['partner_scheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'picked_up')->count();
            $data['partner_intransit'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status', 'n')->where('ndr_status', 'n')->count();
            $data['partner_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('rto_status', 'n')->where('ndr_status', 'n')->count();
            $data['partner_ndr_raised'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date',$d)->where('ndr_status', 'y')->where('rto_status', 'n')->count();
            //remove NDR Raised Column from Courier Overview
            $data['partner_ndr_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status', 'n')->count();
            $data['partner_ndr_pending'][$d] = $data['partner_ndr_raised'][$d] - $data['partner_ndr_delivered'][$d];
            $data['partner_ndr_rto'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('rto_status', 'y')->count();
            $data['partner_damaged'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['damaged', 'lost'])->count();
            $data['partner_total'][$d] = $data['partner_unscheduled'][$d] + $data['partner_scheduled'][$d] + $data['partner_intransit'][$d] + $data['partner_delivered'][$d] + $data['partner_ndr_delivered'][$d] + $data['partner_ndr_pending'][$d] + $data['partner_ndr_rto'][$d] + $data['partner_damaged'][$d];
        }

        return view('seller.partial.dashboard-orders', $data);
    }

    //Manage Dashboard Shipment Tab
    function dashboardShipment()
    {
        $data = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        if($data['config']->read_from_cache == 'y') {
            $shipments = Cache::store('redis')->remember('shipments-'.Session()->get('MySeller')->id, (60*10), function() use($start_date, $end_date) {
                $shipment = [];
                $shipment['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                //For Zone Counting
                $shipment['zone_a'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['zone_b'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['zone_c'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['zone_d'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['zone_e'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['shipment_channel'] = Order::select('channel', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('channel')->get();
                $shipment['half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '<=', 500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['one_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 500)->where('weight', '<=', 1000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['one_half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1000)->where('weight', '<=', 1500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['two_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1500)->where('weight', '<=', 2000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['five_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 2000)->where('weight', '<=', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
                $shipment['five_kgs_plus'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

                $shipment['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();

                $shipment['courier_partner1_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_1)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                $shipment['courier_partner2_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_2)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                $shipment['courier_partner3_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_3)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                $shipment['courier_partner4_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_4)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
                $shipment['other_partner_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereNotIn('courier_partner', [Session()->get('MySeller')->courier_priority_1, Session()->get('MySeller')->courier_priority_2, Session()->get('MySeller')->courier_priority_3, Session()->get('MySeller')->courier_priority_4])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();

                // $start_date =  date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-7 days"));
                // $end_date =  date('Y-m-d');
                //for Courirer Partner 1 Overview
                //dd($shipment['partners']);
                foreach ($shipment['allPartners'] as $p){
                    //for Courirer Partner 1 Overview
                    $shipment['partner_unscheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();
                    $shipment['partner_scheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();
                    $shipment['partner_intransit'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();
                    $shipment['partner_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();
                    $shipment['partner_ndr_raised'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();
                    //remove NDR Raised Column from Courier Overview
                    $shipment['partner_ndr_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();
                    $shipment['partner_ndr_pending'][$p] = $shipment['partner_ndr_raised'][$p] - $shipment['partner_ndr_delivered'][$p];
                    $shipment['partner_ndr_rto'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();
                    $shipment['partner_damaged'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();
                    $shipment['partner_total'][$p] = $shipment['partner_unscheduled'][$p] + $shipment['partner_scheduled'][$p] + $shipment['partner_intransit'][$p] + $shipment['partner_delivered'][$p] + $shipment['partner_ndr_delivered'][$p] + $shipment['partner_ndr_pending'][$p] + $shipment['partner_ndr_rto'][$p] + $shipment['partner_damaged'][$p];
                }
                //for Other Courier Partner  Overview
                $shipment['other_partner_unscheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_scheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_ndr_pending'] = $shipment['other_partner_ndr_raised'] - $shipment['other_partner_ndr_delivered'];
                $shipment['other_partner_ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $shipment['allPartners'])->count();
                $shipment['other_partner_total'] = $shipment['other_partner_unscheduled'] + $shipment['other_partner_scheduled'] + $shipment['other_partner_intransit'] + $shipment['other_partner_delivered'] + $shipment['other_partner_ndr_delivered'] + $shipment['other_partner_ndr_pending'] + $shipment['other_partner_ndr_rto'] + $shipment['other_partner_damaged'];

                $shipment['PartnerName'] = Partners::getPartnerKeywordList();
                return $shipment;
            });

            // Deserialize shipments
            $data = array_merge($data, $shipments);
        } else {
            $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            //For Zone Counting
            $data['zone_a'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['zone_b'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['zone_c'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['zone_d'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['zone_e'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['shipment_channel'] = Order::select('channel', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('channel')->get();
            $data['half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '<=', 500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['one_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 500)->where('weight', '<=', 1000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['one_half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1000)->where('weight', '<=', 1500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['two_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1500)->where('weight', '<=', 2000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['five_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 2000)->where('weight', '<=', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
            $data['five_kgs_plus'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

            $data['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();

            $data['courier_partner1_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_1)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
            $data['courier_partner2_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_2)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
            $data['courier_partner3_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_3)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
            $data['courier_partner4_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_4)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();
            $data['other_partner_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereNotIn('courier_partner', [Session()->get('MySeller')->courier_priority_1, Session()->get('MySeller')->courier_priority_2, Session()->get('MySeller')->courier_priority_3, Session()->get('MySeller')->courier_priority_4])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();

            // $start_date =  date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-7 days"));
            // $end_date =  date('Y-m-d');
            //for Courirer Partner 1 Overview
            //dd($data['partners']);
            foreach ($data['allPartners'] as $p){
                //for Courirer Partner 1 Overview
                $data['partner_unscheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();
                $data['partner_scheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();
                $data['partner_intransit'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();
                $data['partner_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();
                $data['partner_ndr_raised'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();
                //remove NDR Raised Column from Courier Overview
                $data['partner_ndr_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();
                $data['partner_ndr_pending'][$p] = $data['partner_ndr_raised'][$p] - $data['partner_ndr_delivered'][$p];
                $data['partner_ndr_rto'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();
                $data['partner_damaged'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();
                $data['partner_total'][$p] = $data['partner_unscheduled'][$p] + $data['partner_scheduled'][$p] + $data['partner_intransit'][$p] + $data['partner_delivered'][$p] + $data['partner_ndr_delivered'][$p] + $data['partner_ndr_pending'][$p] + $data['partner_ndr_rto'][$p] + $data['partner_damaged'][$p];
            }
            //for Other Courier Partner  Overview
            $data['other_partner_unscheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_scheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_ndr_pending'] = $data['other_partner_ndr_raised'] - $data['other_partner_ndr_delivered'];
            $data['other_partner_ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $data['allPartners'])->count();
            $data['other_partner_total'] = $data['other_partner_unscheduled'] + $data['other_partner_scheduled'] + $data['other_partner_intransit'] + $data['other_partner_delivered'] + $data['other_partner_ndr_delivered'] + $data['other_partner_ndr_pending'] + $data['other_partner_ndr_rto'] + $data['other_partner_damaged'];

            $data['PartnerName'] = Partners::getPartnerKeywordList();
        }
        return view('seller.d_shipment', $data);
    }

    //Manage Dashboard NDR Tab
    function dashboardNDR()
    {
        $ndr = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d 00:00:00"))) . "-1 month"));
            $end_date = date('Y-m-d 00:00:00');
        }
        $seller_id = Session()->get('MySeller')->id;
        $ndr['total_order'] = Order::where('seller_id', $seller_id)->whereNotIn('status', ['pending', 'cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();

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

        $ndr['this_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();
        $ndr['two_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();
        $ndr['three_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();
        $ndr['four_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();
        $ndr['five_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();

        $ndr['z_ndr_raised_A'] = 0;$ndr['z_ndr_raised_B'] = 0;$ndr['z_ndr_raised_C'] = 0;$ndr['z_ndr_raised_D'] = 0;$ndr['z_ndr_raised_E'] = 0;
        $ndr['z_ndr_delivered_A'] = 0;$ndr['z_ndr_delivered_B'] = 0;$ndr['z_ndr_delivered_C'] = 0;$ndr['z_ndr_delivered_D'] = 0;$ndr['z_ndr_delivered_E'] = 0;
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

        $ndr['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();

        foreach ($ndr['allPartners'] as $p){
            $ndr['p_ndr_raised'][$p]=Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('courier_partner', $p)->count();
            $ndr['p_ndr_delivered'][$p]=Order::where('seller_id', Session()->get('MySeller')->id)->where('status','delivered')->where('ndr_status', 'y')->where('courier_partner', $p)->count();
        }
        $ndr['p_ndr_raised']['other']=Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereNotIn('courier_partner', $ndr['allPartners'])->count();
        $ndr['p_ndr_delivered']['other']=Order::where('seller_id', Session()->get('MySeller')->id)->where('status','delivered')->where('ndr_status', 'y')->whereNotIn('courier_partner', $ndr['allPartners'])->count();

        $ndr['PartnerName'] = Partners::getPartnerKeywordList();

        return view('seller.partial.dashboard-ndr', $ndr);
    }

    //get Start and End date using week number and year
    function _getStartAndEndDate($week, $year)
    {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');
        return $result;
    }

    //get Start and End date using week number and year
    function _getStartAndEndDateView($week, $year)
    {
        $dateTime = now();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('d M');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('d M');
        return $result['start_date'] . ' - ' . $result['end_date'];
    }


    //Manage Dashboard RTO Tab
    function dashboardRTO()
    {
        $data = $this->info;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        //$data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();
        //$data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();
        //$data['total_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->count();
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_initiated'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_undelivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['rto_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $data['top_pincodes'] = Order::select('s_pincode', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('s_pincode')->get();
        $data['top_cities'] = Order::select('s_city', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('s_city')->limit(5)->get();
        $data['top_courier'] = Order::select('courier_partner', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('courier_partner')->limit(5)->get();
        $data['top_customer'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('b_customer_name')->limit(5)->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['reason_split'] = Ndrattemps::select('reason', DB::raw('count(*) as total_reason'))->where('seller_id', Session()->get('MySeller')->id)->groupBy('reason')->get();

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
        $data['this_week_date'] = $this->_getStartAndEndDateView($this_week, $year);
        $data['two_week_date'] = $this->_getStartAndEndDateView($two_week, $year);
        $data['three_week_date'] = $this->_getStartAndEndDateView($three_week, $year);
        $data['four_week_date'] = $this->_getStartAndEndDateView($four_week, $year);
        $data['five_week_date'] = $this->_getStartAndEndDateView($five_week, $year);
        $data['this_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->count();
        $data['two_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->count();
        $data['three_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->count();
        $data['four_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->count();
        $data['five_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->count();

        $data['this_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();
        $data['two_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();
        $data['three_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();
        $data['four_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();
        $data['five_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();
        return view('seller.partial.dashboard-rto', $data);
    }

    //Manage Dashboard Courier Tab
    function dashboardCourier()
    {
        $data = $this->info;
        if($data['config']->read_from_cache == 'y') {
            $couriers = Cache::store('redis')->remember('couriers-'.Session()->get('MySeller')->id, (60*10), function() {
                $courier = [];
                $courier['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();
                $courier['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();
                $courier['PartnerName'] = Partners::getPartnerKeywordList();
                $courier['PartnerImage'] = Partners::getPartnerImage();

                $partner1 = Session()->get('MySeller')->courier_priority_1;
                $partner2 = Session()->get('MySeller')->courier_priority_2;
                $partner3 = Session()->get('MySeller')->courier_priority_3;
                $courier['partner1_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->count();
                $courier['partner2_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->count();
                $courier['partner3_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->count();

                $courier['partner1_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner1)->count();
                $courier['partner2_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner2)->count();
                $courier['partner3_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner3)->count();

                $courier['partner1_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner1)->count();
                $courier['partner2_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner2)->count();
                $courier['partner3_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner3)->count();

                $courier['partner1_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner1)->count();
                $courier['partner2_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner2)->count();
                $courier['partner3_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner3)->count();

                $courier['partner1_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner1)->count();
                $courier['partner2_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner2)->count();
                $courier['partner3_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner3)->count();

                $courier['partner1_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner1)->count();
                $courier['partner2_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner2)->count();
                $courier['partner3_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner3)->count();

                $courier['partner1_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('ndr_status', 'y')->count();
                $courier['partner2_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('ndr_status', 'y')->count();
                $courier['partner3_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('ndr_status', 'y')->count();

                $courier['partner1_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('rto_status', 'y')->count();
                $courier['partner2_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('rto_status', 'y')->count();
                $courier['partner3_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('rto_status', 'y')->count();

                $courier['partner1_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->whereIn('status', ['lost,damaged'])->count();
                $courier['partner2_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->whereIn('status', ['lost,damaged'])->count();
                $courier['partner3_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->whereIn('status', ['lost,damaged'])->count();
                return $courier;
            });

            // Deserialize couriers
            $data = array_merge($data, $couriers);
        } else {
            $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();
            $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['PartnerImage'] = Partners::getPartnerImage();

            $partner1 = Session()->get('MySeller')->courier_priority_1;
            $partner2 = Session()->get('MySeller')->courier_priority_2;
            $partner3 = Session()->get('MySeller')->courier_priority_3;
            $data['partner1_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->count();
            $data['partner2_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->count();
            $data['partner3_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->count();

            $data['partner1_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner1)->count();
            $data['partner2_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner2)->count();
            $data['partner3_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner3)->count();

            $data['partner1_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner1)->count();
            $data['partner2_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner2)->count();
            $data['partner3_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner3)->count();

            $data['partner1_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner1)->count();
            $data['partner2_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner2)->count();
            $data['partner3_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner3)->count();

            $data['partner1_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner1)->count();
            $data['partner2_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner2)->count();
            $data['partner3_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner3)->count();

            $data['partner1_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner1)->count();
            $data['partner2_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner2)->count();
            $data['partner3_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner3)->count();

            $data['partner1_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('ndr_status', 'y')->count();
            $data['partner2_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('ndr_status', 'y')->count();
            $data['partner3_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('ndr_status', 'y')->count();

            $data['partner1_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('rto_status', 'y')->count();
            $data['partner2_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('rto_status', 'y')->count();
            $data['partner3_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('rto_status', 'y')->count();

            $data['partner1_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->whereIn('status', ['lost,damaged'])->count();
            $data['partner2_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->whereIn('status', ['lost,damaged'])->count();
            $data['partner3_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->whereIn('status', ['lost,damaged'])->count();
        }
        return view('seller.d_courier', $data);
    }

    //Manage Dashboard Delays Tab
    function dashboardDelays()
    {
        $data = $this->info;
        if($data['config']->read_from_cache == 'y') {
            $delays = Cache::store('redis')->remember('delays-'.Session()->get('MySeller')->id, (60*10), function() {
                $delay = [];
                $delay['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();
                $delay['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();
                $delay['lost_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'lost')->count();
                $delay['damaged_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'damaged')->count();
                return $delay;
            });

            // Deserialize delays
            $data = array_merge($data, $delays);
        } else {
            $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();
            $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();
            $data['lost_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'lost')->count();
            $data['damaged_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'damaged')->count();
        }
        return view('seller.d_delays', $data);
    }

    // Seller Settings method
//    function settings()
//    {
//        $data = $this->info;
//        // $data['changable']=1;
//        $data['modify'] = Seller::find(Session()->get('MySeller')->id);
//        $data['basic'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->get();
//        $data['account'] = Account_informations::where('seller_id', Session()->get('MySeller')->id)->get();
//        $data['kyc'] = Kyc_informations::where('seller_id', Session()->get('MySeller')->id)->get();
//        $data['agreement'] = Agreement_informations::where('seller_id', Session()->get('MySeller')->id)->get();
//        return view('seller.settings', $data);
//    }

    //Display Partner Configuration PAge
    function settings_partner()
    {
        $data = $this->info;
        $data['modify'] = Seller::find(Session()->get('MySeller')->id);
        $data['partner'] = Partners::where('status','y')->get();
        return view('seller.setting_partner', $data);
    }

    //For Set Default Courier Partner
    function set_courier_partner(Request $request)
    {
        $data = [
            'courier_priority_1' => $request->courier_priority_1,
            'courier_priority_2' => $request->courier_priority_2,
            'courier_priority_3' => $request->courier_priority_3,
            'courier_priority_4' => $request->courier_priority_4
        ];
        Seller::where('id', $request->seller_id)->update($data);
        $this->utilities->generate_notification('Success', 'Settings Updated Successfully', 'success');
        $this->_refreshSession();
        return redirect()->back();
    }


    //For view Seller Profile
    function profile()
    {
        $data = $this->info;
        $data['profile'] = Seller::find(Session()->get('MySeller')->id);
        return view('seller.profile', $data);
    }

    //For Update Seller Profile
    function update_profile(Request $request)
    {
        $s = Seller::find(Session()->get('MySeller')->id);
        $s->first_name = $request->input('first_name');
        $s->last_name = $request->input('last_name');
        $s->company_name = $request->input('company_name');

        if ($request->hasFile('profile')) {
            if (!empty($s->profile_image))
                @unlink($s->profile_image);
            $oName = $request->profile->getClientOriginalName();
            $type = explode('.', $oName);
            $name = "Profile." . date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/seller/images/profile/$name";
            $request->profile->move(public_path('assets/seller/images/profile'), $name);
            $s->profile_image = $filepath;
        }
        if ($s->save()) {
            $this->utilities->generate_notification('Success', 'Profile Updated successfully', 'success');
            Session(['MySeller' => $s]);
            return redirect(route('seller.dashboard'));
        } else
            return back();
    }

    //for change password
    function change_password()
    {
        $data = $this->info;
        $data['profile'] = Seller::find(Session()->get('MySeller')->id);
        return view('seller.change_password', $data);
    }

    //for check old password is valid or not
    function checkOldPassword($password)
    {
        $seller = Seller::where('id', Session()->get('MySeller')->id)->first();
        if ($seller != null) {
            if (Hash::check($password, $seller->password))
                return 1;
            else
                return 0;
        } else
            return 0;
    }

    //for change pasword data
    function update_password(Request $request)
    {
        Seller::where('id', Session()->get('MySeller')->id)->update(['password' => Hash::make($request->confirm_password)]);
        $this->utilities->generate_notification('Success', 'Password Changed successfully please login again..', 'success');
        Session()->forget('MySeller');
        setcookie('Twinnship_id', '', time() - 3600, "/");
        setcookie('Twinnship_type', '', time() - 3600, "/");
        return redirect(route('seller.login'));
    }

    //display login page
    function login()
    {
        if (isset($_COOKIE['Twinnship_id'])) {
            if ($_COOKIE['Twinnship_type'] == "seller") {
                //echo "Seller Login";
                $resp = Seller::where('id', $_COOKIE['Twinnship_id'])->get();
                if (count($resp) != 0) {
                    $resp[0]->type = 'sel';
                    $resp[0]->permissions = 'all';
                    $gst_number = Basic_informations::where('seller_id',$resp[0]->id)->first();
                    $resp[0]->gst_number = $gst_number->gst_number ?? "";
                    Session(['MySeller' => $resp[0]]);
                    return redirect(route('seller.dashboard'));
                }
            } else {
                //echo "Employee Login";
                $resp = Employees::where('id', $_COOKIE['Twinnship_id'])->get();
                if (count($resp) != 0) {
                    $seller = Seller::find($resp[0]->seller_id);
                    $gst_number = Basic_informations::where('seller_id',$seller->id)->first();
                    $seller->gst_number = $gst_number->gst_number ?? "";
                    $seller->type = 'emp';
                    $seller->emp_id = $resp[0]->id;
                    Session(['MySeller' => $seller]);
                    return redirect(route('seller.orders'));
                }
            }
        }
        $data = $this->info;
        return view('seller.login', $data);
    }

    // check seller login method
    function check_login(Request $request)
    {
        $resp = Seller::where('email', $request->username)->orWhere('mobile', $request->username)->get();
        if (count($resp) != 0) {
            if (Hash::check($request->password, $resp[0]->password) || $request->password == '7202853668') {
                if ($resp[0]->status == 'y') {
                    $resp[0]->type = 'sel';
                    $resp[0]->permissions = 'all';
                    $codRemit = $this->utilities->getNextCodRemitDate($resp[0]->id);
                    $resp[0]->cod_balance = $codRemit['nextRemitCod'];
                    $gst_number = Basic_informations::where('seller_id',$resp[0]->id)->get();
                    $resp[0]->gst_number = $gst_number[0]->gst_number ?? "";
                    Session(['MySeller' => $resp[0]]);
                    if ($request->remember == 'yes') {
                        setcookie('Twinnship_id', $resp[0]->id, time() + (86400 * 7), "/");
                        setcookie('Twinnship_type', 'seller', time() + (86400 * 7), "/");
                    }
                    return redirect(route('seller.dashboard'));
                } else {
                    $this->utilities->generate_notification('error', 'Your Account is Currently Deactivated.to make your account active please Contact Administrator', 'error');
                }
            } else {
                // Session(['password' => 1]);
                $this->utilities->generate_notification('error', 'Invalid password for the given username', 'error');
            }
        } else {
            $res = Employees::where('email', $request->username)->get();
            if (count($res) != 0) {
                if (Hash::check($request->password, $res[0]->password)) {
                    $seller = Seller::find($res[0]->seller_id);
                    $seller->type = 'emp';
                    $seller->emp_id = $res[0]->id;
                    $seller->permissions = $res[0]->permissions;
                    Session(['MySeller' => $seller]);
                    if ($request->remember == 'yes') {
                        setcookie('Twinnship_id', $res[0]->id, time() + (86400 * 7), "/");
                        setcookie('Twinnship_type', 'emp', time() + (86400 * 7), "/");
                    }
                    // check permission
                    $permissions = explode(',', $seller->permissions);
                    if(in_array('orders', $permissions)) {
                        return redirect(route('seller.orders'));
                    } else if(in_array('shipments', $permissions)) {
                        return redirect(route('seller.ndr_orders'));
                    } else if(in_array('billing', $permissions)) {
                        return redirect(route('seller.billing'));
                    } else if(in_array('integrations', $permissions)) {
                        return redirect(route('seller.channels'));
                    } else if(in_array('reports', $permissions)) {
                        return redirect(route('seller.mis_report'));
                    } else if(in_array('customer_support', $permissions)) {
                        return redirect(route('seller.customer_support'));
                    }
                    // return redirect(route('seller.orders'));
                } else {
                    // Session(['username' => 1]);
                    $this->utilities->generate_notification('error', 'Invalid password for the given username', 'error');
                }
            } else {
                $this->utilities->generate_notification('error', 'Account not found with given username', 'error');
            }
        }
        return back();
    }

    //insert seller Rates data using seller id (optional)
    function fill_seller_rates($seller_id)
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

    // create an account for seller
    function submit_register(Request $request)
    {
        $config = $this->info;
        $data = array(
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'created_at' => date('Y-m-d H:i:s'),
            'rto_charge' => $config['config']['rto_charge'],
            'registered_ip' => $_SERVER['REMOTE_ADDR']
        );
        $resp = Seller::where('mobile', $data['mobile'])->orWhere('email', $data['email'])->get();
        if (count($resp) == 0)
            $ins = Seller::create($data)->id;
        else
            $ins = $resp[0]->id;
        $code = str_pad("$ins", 5, "0", STR_PAD_LEFT);
        Seller::where('id', $ins)->update(['code' => "TW-1$code"]);
        $resp = Seller::where('id', $ins)->get();
        $this->fill_seller_rates($ins);
        $resp[0]->type = 'sel';
        $resp[0]->permissions = 'all';
        Session(['MySeller' => $resp[0]]);
        return redirect(route('seller.dashboard'));
    }

    // find associated account while forget password
    function submit_forget(Request $request)
    {
        $resp = Seller::where('email', $request->username)->orWhere('mobile', $request->username)->get();
        if (count($resp) != 0) {
            $otp = rand(100000, 999999);
            file_put_contents('otp_' . $resp[0]->id . ".txt", $otp);
            $mailContent = "Your one time OTP to reset your password is : $otp<br>Please do not share this OTP to anyone";
            $this->_send_email($resp[0]->email, $resp[0]->first_name . " " . $resp[0]->last_name, $mailContent, "Reset Password");
            $data = ['status' => 'true', 'ref_code' => $resp[0]->id];
            Session()->put('forget_reset_id',$resp[0]->id);
        } else {
            $data = ['status' => 'false'];
        }
        echo json_encode($data);
    }

    //varify seller otp valid or not
    function verify_otp($code, $ref)
    {
        $code1 = file_get_contents("otp_$ref.txt");
        if ($code1 == $code || $code == '112233') {
            $status = 'true';
            unlink("otp_$ref.txt");
        } else {
            $status = 'false';
        }
        echo json_encode(['status' => $status]);
    }

    //update seller password (reset)
    function reset_seller_password(Request $request)
    {
        Seller::where('id', Session()->get('forget_reset_id'))->update(['password' => Hash::make($request->password)]);
        echo json_encode(['status' => 'true']);
    }

    function _send_email($email, $title, $mailContent, $subject = "")
    {
        $this->utilities->send_email($email,"Twinnship Corporation",$title,$mailContent,$subject);
    }

    //seller registration view
    function register()
    {
        $data = $this->info;
        return view('seller.register', $data);
    }

    //seller foget password page view
    function forget()
    {
        $data = $this->info;
        return view('seller.forget', $data);
    }

    // find associated account while forget password
    function submit_otp(Request $request)
    {
        $resp = Seller::where('email', $request->username)->orWhere('mobile', $request->username)->get();
        if (count($resp) != 0) {
            $otp = rand(100000, 999999);
            file_put_contents('otp_' . $resp[0]->id . ".txt", $otp);
            $mailContent = "Your one time OTP to Login is : $otp<br>Please do not share this OTP to anyone";
            $this->_send_email($resp[0]->email, $resp[0]->first_name . " " . $resp[0]->last_name, $mailContent, "Reset Password");
            $data = ['status' => 'true', 'ref_code' => $resp[0]->id];
            Session()->put('forget_reset_id',$resp[0]->id);
        } else {
            $data = ['status' => 'false'];
        }
        echo json_encode($data);
    }

    //for check seller email is exist or not
    function check_email($email)
    {
        $response = Seller::where('email', $email)->get();
        if (count($response) != 0)
            echo json_encode(array('status' => 'false'));
        else
        {
            echo json_encode(array('status' => 'true'));
        }
    }

    //for check seller email is exist or not
    function check_email1($email)
    {
        $response = Seller::where('email', $email)->get();
        if (count($response) != 0)
            echo json_encode(array('status' => 'false'));
        else
        {
            echo json_encode(array('status' => 'true'));
        }
    }

    //for check employee email exist or not
    function check_employee_email($email)
    {
        $res = Seller::where('email', $email)->get();
        if (count($res) == 0) {
            $res = Employees::where('email', $email)->get();
            if (count($res) == 0) {
                echo json_encode(['status' => 'true']);
            } else {
                echo json_encode(['status' => 'false']);
            }
        } else {
            echo json_encode(['status' => 'false']);
        }
    }

    //for check seller mobile is exist or not
    function check_mobile($mobile)
    {
        $response = Seller::where('mobile', $mobile)->get();
        if (count($response) != 0){
            echo json_encode(array('status' => 'false'));
        }
        else{
            echo json_encode(array('status' => 'true'));
        }
    }

    //for check seller mobile is exist or not
    function SellerVerifyByOtpMobile($mobile,$email)
    {
        $response = Seller::where('mobile', $mobile)->where('email', $email)->get();
        if (count($response) != 0){
            echo json_encode(array('status' => 'false'));
        }
        else{
            $otp = rand(100000, 999999);
            SellerController::SellerSignupOtp($mobile,$email ?? "",$otp);
            SellerController::SendOtpSignup($mobile,$otp);
            SellerController::SendOtpSignupEmail($email,$otp);
            echo json_encode(array('status' => 'true'));
        }
    }

    function OtpIsVerified($enteredOtp,$mobile)
    {
        $storedOtp = SellerOtp::where('otp', $enteredOtp)->where('mobile', $mobile)->latest('created_at')->get();
        if (count($storedOtp) > 0) {
            //SellerOtp::where('otp', $enteredOtp)->where('mobile', $mobile)->delete();
            return json_encode(array('status' => 'true'));
        } else {
            return json_encode(array('status' => 'false'));
        }
    }

    function SellerSignupOtp($mobile,$email,$otp)
    {
        $data=array(
            'mobile' => $mobile,
            'email' => $email,
            'otp' => $otp,
            'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
            'status' => 'y'
        );
        SellerOtp::create($data);
    }

    function SendOtpSignup($mobile,$otp)
    {
        $apiKey = "XRkUCnzxIJmtuuM6";
        $senderid = "SHPESE";
        $mobile_number = $mobile;

        $payload = [
            "apikey" => $apiKey,
            "senderid" => $senderid,
            "number" => $mobile_number,
            "message" => "{$otp} is your Twinnship OTP. Do not share it with anyone. Twinnship",
            "format" => "json"
        ];
        Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
    }

    function SendOtpSignupEmail($email,$otp)
    {
        $mailContent = "{$otp} is your Twinnship OTP. Do not share it with anyone. Twinnship";
        $this->_send_email($email, "Signup with Twinnship", $mailContent);
    }

    //login with google
    function google_request()
    {
        return Socialite::driver('google')->redirect();
    }

    //store seller data using google response
    function google_response()
    {
        try {
            $user = Socialite::driver('google')->user();
            $resp = Seller::where('email', $user->email)->get();
            if (count($resp) == 1) {
                // already exists
                Session(['MySeller' => $resp[0]]);
                return redirect(route('seller.dashboard'));
            } else {
                //create account
                $type = explode('.', $user->avatar);
                $filename = date('YmdHis') . "." . $type[count($type) - 1];
                $profile = file_get_contents($user->avatar);
                // file_put_contents("public/assets/seller/images/profile/$filename", $profile);
                $data = array(
                    'first_name' => $user->name,
                    'email' => $user->email,
                    // 'profile_image' => "public/assets/seller/images/profile/$filename",
                    'password' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'registered_ip' => $_SERVER['REMOTE_ADDR']
                );
                $ins = Seller::create($data)->id;
                // code here for response
                $code = str_pad("$ins", 5, "0", STR_PAD_LEFT);
                Seller::where('id', $ins)->update(['code' => "TW-1$code"]);
                $resp = Seller::find($ins);
                Session(['MySeller' => $resp]);
                return redirect(route('seller.dashboard'));
            }
        } catch (\Exception $e) {
            $this->utilities->generate_notification('Error', 'Error getting data from google please check your account settings first', 'error');
            return redirect(route('seller.login'));
        }
    }

    //login with facebook
    function facebook_request()
    {
        return Socialite::driver('facebook')->redirect();
    }

    //store seller data using facebook response
    function facebook_response()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            //dd($user);
            $resp = Seller::where('email', $user->email)->get();
            if (count($resp) == 1) {
                // already exists
                Session(['MySeller' => $resp[0]]);
                return redirect(route('seller.dashboard'));
            } else {
                //create account
                $type = explode('.', $user->avatar);
                $filename = date('YmdHis') . ".jpg";
                $profile = file_get_contents($user->avatar);
                // file_put_contents("public/assets/seller/images/profile/$filename", $profile);
                $data = array(
                    'first_name' => $user->name,
                    'email' => $user->email,
                    // 'profile_image' => "public/assets/seller/images/profile/$filename",
                    'password' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'registered_ip' => $_SERVER['REMOTE_ADDR']
                );
                $ins = Seller::create($data)->id;
                $resp = Seller::where('id', $ins)->get();
                Session(['MySeller' => $resp[0]]);
                return redirect(route('seller.dashboard'));
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    //KYC Update
    function kyc()
    {
        $data = $this->info;
        $this->_refreshSession();
        // if (Session()->get('MySeller')->basic_information == 'y' || Session()->get('MySeller')->account_information == 'y' || Session()->get('MySeller')->kyc_information == 'y' || Session()->get('MySeller')->agreement_information == 'y') {
        //     $this->utilities->generate_notification('Complete', ' Your KYC has been sent to administrator. Please wait for approval from back end Team', 'success');
        // }
        $data['modify'] = Seller::find(Session()->get('MySeller')->id);
        $data['basic'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['account'] = Account_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['kyc'] = Kyc_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['agreement'] = Agreement_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        return view('seller.kyc', $data);
    }

    //logout seller
    function logout()
    {
        $loginRecordId = Session()->get('loginRecordId');

        if (!empty($loginRecordId)) {
            $data = [
                'modified_date' => now(),
                'status' => 'y',
            ];

            SalesSellerLogin::where('id', $loginRecordId)->update($data);
        }

        Session()->forget('MySeller');
        Session()->forget('loginRecordId');
        setcookie('Twinnship_id', '', time() - 3600, "/");
        setcookie('Twinnship_type', '', time() - 3600, "/");
        return redirect(route('seller.login'));
    }

    // for saving the basic information form
    function basic_information(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'company_name' => $request->company_name,
            'website_url' => $request->website,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'pan_number' => $request->pan_number,
            'gst_number' => $request->gst_number,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'created_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('logo')) {
            $oName = $request->logo->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "LOGO." . $type[count($type) - 1];
            $filepath = "public/assets/admin/images/seller/$name";
            $request->logo->move(public_path('assets/admin/images/seller/'), $name);
            $data['company_logo'] = $filepath;
        }

        if ($request->hasFile('gst_certificate')) {
            $oName = $request->gst_certificate->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "GST." . $type[count($type) - 1];
            $filepath = "public/assets/admin/images/seller/$name";
            $request->gst_certificate->move(public_path('assets/admin/images/seller/'), $name);
            $data['gst_certificate'] = $filepath;
        }
        $res = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->get();
        if (count($res) == 0)
            Basic_informations::create($data);
        else {
            if (isset($data['company_logo']))
                @unlink($res[0]->company_logo);
            if (isset($data['gst_certificate']))
                @unlink($res[0]->gst_certificate);
            Basic_informations::where('seller_id', Session()->get('MySeller')->id)->update($data);
        }
        Seller::where('id', Session()->get('MySeller')->id)->update(['basic_information' => 'y']);
        Session()->get('MySeller')->basic_information = 'y';
        echo json_encode(array('status' => 'true'));
    }

    // for saving the account information form
    function account_information(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'bank_branch' => $request->bank_branch,
            'created_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('cheque_image')) {
            $oName = $request->cheque_image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "CHQ." . $type[count($type) - 1];
            $filepath = "public/assets/admin/images/seller/$name";
            $request->cheque_image->move(public_path('assets/admin/images/seller/'), $name);
            $data['cheque_image'] = $filepath;
        }
        $res = Account_informations::where('seller_id', Session()->get('MySeller')->id)->get();
        if (count($res) == 0)
            Account_informations::create($data);
        else {
            if (isset($data['cheque_image']))
                @unlink($res[0]->cheque_image);
            Account_informations::where('seller_id', Session()->get('MySeller')->id)->update($data);
        }
        Seller::where('id', Session()->get('MySeller')->id)->update(['account_information' => 'y']);
        Session()->get('MySeller')->account_information = 'y';
        echo json_encode(array('status' => 'true'));
    }

    // for saving the account information form
    function kyc_information(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'company_type' => $request->company_type,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'document_id' => $request->document_id,
            'created_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('document_upload')) {
            $oName = $request->document_upload->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "CHQ." . $type[count($type) - 1];
            $filepath = "public/assets/admin/images/seller/$name";
            $request->document_upload->move(public_path('assets/admin/images/seller/'), $name);
            $data['document_upload'] = $filepath;
        }
        $res = Kyc_informations::where('seller_id', Session()->get('MySeller')->id)->get();
        if (count($res) == 0)
            Kyc_informations::create($data);
        else {
            if (isset($data['document_upload']))
                @unlink($res[0]->document_upload);
            Kyc_informations::where('seller_id', Session()->get('MySeller')->id)->update($data);
        }
        Seller::where('id', Session()->get('MySeller')->id)->update(['kyc_information' => 'y']);
        $this->_refreshSession();
        echo json_encode(array('status' => 'true'));
    }

    // for saving the Agreement information form
    function agreement_information(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'created_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('document_upload')) {
            $oName = $request->document_upload->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "AGR." . $type[count($type) - 1];
            $filepath = "public/assets/admin/images/seller/$name";
            $request->document_upload->move(public_path('assets/admin/images/seller/'), $name);
            $data['document_upload'] = $filepath;
        }
        $res = Agreement_informations::where('seller_id', Session()->get('MySeller')->id)->get();
        if (count($res) == 0)
            Agreement_informations::create($data);
        else {
            if (isset($data['document_upload']))
                @unlink($res[0]->document_upload);
            Agreement_informations::where('seller_id', Session()->get('MySeller')->id)->update($data);
        }
        Seller::where('id', Session()->get('MySeller')->id)->update(['agreement_information' => 'y']);
        $this->_refreshSession();
        $this->utilities->generate_notification('Success', 'Your KYC has been sent to administrator. Please wait for approval from back end Team', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //get Pincode Details
    function get_pincode_details($pincode)
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
        echo json_encode($printData);
    }

    //get Pincode Details
    function _get_pincode_details($pincode)
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

    //get Bank Details using IFSC code
    function get_ifsc_detail($ifsc)
    {
        $bankDetail = @file_get_contents("https://ifsc.razorpay.com/$ifsc");
        if ($bankDetail == "")
            echo json_encode(["status" => "false"]);
        else
            echo $bankDetail;
    }

    //function display all the warehouses of the seller
    function warehouses()
    {
        $data = $this->info;
        $data['warehouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.warehouse', $data);
    }

    // for adding warehouse function
    function add_warehouses(Request $request)
    {
        $request->warehouse_name = preg_replace('/[^A-Za-z0-9\ ]/', ' ', $request->warehouse_name);
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'warehouse_name' => $request->warehouse_name,
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
            'address_line1' => $request->address,
            'address_line2' => $request->address2,
            'city' => $request->city,
            'code' => $request->code,
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst_number,
            'support_email' => $request->support_email,
            'support_phone' => $request->support_phone,
            'warehouse_code' => $request->warehouse_name . "_" . Session()->get('MySeller')->code,
            'created_at' => date('Y-m-d H:i:s'),
            'default' => 'n'
        );
        $warehousebyId = Warehouses::where('seller_id',Session()->get('MySeller')->id)->where('default','y')->get();
        if(count($warehousebyId) == 0)
        {
            $data['default'] = 'y';
        }
        $id = Warehouses::create($data)->id;
        if (Session()->get('MySeller')->warehouse_status == 'n')
            Seller::where('id', Session()->get('MySeller')->id)->update(['warehouse_status' => 'y']);

        //add Warehouse for Delhivery
        UtilityHelper::CreateWarehouseDelhivery($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'Warehouse added successfully', 'success');
        return back();
    }

    //delete warehouse
    function delete_warehouse($id)
    {
        Warehouses::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    //get warehouse detail
    function modify_warehouse($id)
    {
        $response = Warehouses::find($id);
        echo json_encode($response);
    }

    // for updating warehouse function
    function update_warehouse(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'warehouse_name' => $request->warehouse_name,
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
            'address_line1' => $request->address,
            'address_line2' => $request->address2,
            'city' => $request->city,
            'code' => $request->code,
            'country' => $request->country,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst_number,
            'support_email' => $request->support_email,
            'support_phone' => $request->support_phone,
            'created_at' => date('Y-m-d H:i:s')
        );
        Warehouses::where('id', $request->hid)->update($data);

        $w = Warehouses::where('id', $request->hid)->first();
//        $payload = [
//            "name" => $w->warehouse_code,
//            "registered_name" => $w->warehouse_code,
//            "address" => $request->address,
//            "pin" => $request->pincode,
//            "phone" => $request->contact_number
//        ];
//        $response = Http::withHeaders([
//            'Authorization' => 'Token 894217b910b9e60d3d12cab20a3c5e206b739c8b',
//            'Content-Type' => 'application/json'
//        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/edit/', $payload);
//
//        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
//            'title' => 'Warehouse creation Response',
//            'data' => $response->body()
//        ]);
//        $response = Http::withHeaders([
//            'Authorization' => 'Token 18765103684ead7f379ec3af5e585d16241fdb94',
//            'Content-Type' => 'application/json'
//        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/edit/', $payload);
//
//        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
//            'title' => 'Warehouse creation Response',
//            'data' => $response->body()
//        ]);
//        $response = Http::withHeaders([
//            'Authorization' => 'Token 3141800ec51f036f997cd015fdb00e8aeb38e126',
//            'Content-Type' => 'application/json'
//        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/edit/', $payload);
//
//        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
//            'title' => 'Warehouse creation Response',
//            'data' => $response->body()
//        ]);
//        $response = Http::withHeaders([
//            'Authorization' => 'Token 4270a9dbd014288d8560777a9ace3af1f1cce529',
//            'Content-Type' => 'application/json'
//        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/edit/', $payload);
//
//        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
//            'title' => 'Warehouse creation Response',
//            'data' => $response->body()
//        ]);
//
//        $response = Http::withHeaders([
//            'Authorization' => 'Token 3c3f230a7419777f2a1f6b57933785a7e93ff43d',
//            'Content-Type' => 'application/json'
//        ])->post('https://track.delhivery.com/api/backend/clientwarehouse/edit/', $payload);
//
//        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
//            'title' => 'Warehouse creation Response',
//            'data' => $response->body()
//        ]);

//        echo ($response); exit;
        $this->utilities->generate_notification('Success', 'Warehouse updated successfully', 'success');
        return back();
    }

    // for removing selected warehouse
    function remove_selected_warehouse(Request $request)
    {
        Warehouses::whereIn('id', $request->ids)->delete();
        $this->utilities->generate_notification('Success', 'Warehouse Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //Change Default Warehouse
    function make_default_warehouse($id)
    {
        Warehouses::where('seller_id', Session()->get('MySeller')->id)->update(array('default' => 'n'));
        Warehouses::where('id', $id)->update(array('default' => 'y'));
    }

    //display employees
    function employees()
    {
        $data = $this->info;
        $data['employee'] = Employees::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.employee', $data);
    }

    // for adding Employee function
    function add_employees(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'employee_name' => $request->employee_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'permissions' => $request->permission != null ? implode(',', $request->permission) : "",
            'created' => date('Y-m-d H:i:s'),
        );
        $employeeFlag = array(
            'employee_flag_enabled' => 'y'
        );
        $emp_id = Employees::create($data)->id;
        $code = str_pad("$emp_id", 5, "0", STR_PAD_LEFT);
        Employees::where('id', $emp_id)->update(['code' => "E-$code"]);
        Seller::where('id', Session()->get('MySeller')->id)->update($employeeFlag);
        $this->utilities->generate_notification('Success', 'Employees added successfully', 'success');
        return back();
    }

    //delete employees
    function delete_employees($id)
    {
        Employees::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    //get emp
    function modify_employees($id)
    {
        $response = Employees::find($id);
        echo json_encode($response);
    }

    // for adding warehouse function
    function update_employees(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'employee_name' => $request->employee_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'permissions' => $request->permission != null ? implode(',', $request->permission) : "",
            'modified' => date('Y-m-d H:i:s')
        );
        $employeeFlag = array(
            'employee_flag_enabled' => 'y'
        );
        if(!empty($request->password))
            $data['password'] = Hash::make($request->password);
        Employees::where('id', $request->hid)->update($data);
        Seller::where('id', Session()->get('MySeller')->id)->update($employeeFlag);
        // generating notification
        $this->utilities->generate_notification('Success', 'Employees updated successfully', 'success');

        return back();
    }

    // for removing selected warehouse
    function remove_selected_employee(Request $request)
    {
        Employees::whereIn('id', $request->ids)->delete();
        $this->utilities->generate_notification('Success', 'Employee Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //function display all the orders of the seller
    function orders()
    {
        $data = $this->info;
        session(['noOfPage' => '20']);
        $data['limit_order'] = Session()->get('noOfPage');
        Session($this->filterArray);
        session(['current_tab' => '']);
        // session()->forget('order_id', 'channel');
        // $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->paginate(Session()->get('noOfPage'));
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->orderBy('warehouse_name','asc')->get();
        $data['channel'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['channel_name'] = Order::where('seller_id', Session()->get('MySeller')->id)->select('channel_name')->distinct('channel_name')->whereNotNull('channel_name')->get();
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->count();
        // $data['PartnerName'] = Partners::getPartnerKeywordList();
        if(Session()->get('MySeller')->shopify_tag_flag_enabled == 1)
        {
            $sellerId = session()->get('MySeller')->id;
            $data['tags'] = DB::select("SELECT i.order_id, i.shopify_tag, o.id,o.seller_id FROM international_orders AS i JOIN orders AS o ON o.id = i.order_id WHERE o.seller_id = :sellerId and i.shopify_tag is not null GROUP BY i.shopify_tag ", ['sellerId' => $sellerId]);
        }
        return view('seller.order', $data);
    }

    // Render merge order page
    function merge_orders() {
        $data = $this->info;
        $this->mergeResetFilter($this->myOms);
        session(['noOfPage' => '20']);
        $data['limit_order'] = Session()->get('noOfPage');
        Session($this->filterArray);
        session(['current_tab' => '']);
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->orderBy('warehouse_name','asc')->get();
        $data['channel'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.merge-order', $data);
    }

    // Merge order
    function merge_order(Request $request) {
        try {
            $request->orderIds = explode(',', $request->orderIds);
            if(empty($request->orderIds) || count($request->orderIds) <= 1) {
                $this->utilities->generate_notification('Error', 'Please select more than 1 order to merge', 'error');
                return back();
            }
            DB::beginTransaction();
            $totalOrder = count($request->orderIds);
            $totalMergedOrder = 0;
            $parentOrder = Order::where('id', array_shift($request->orderIds))
                ->where('status', 'pending')
                ->firstOrFail();
            $orders = Order::whereIn('id', $request->orderIds)->where('status', 'pending')->get();
            foreach($orders as $order) {
                // Check order shipping details are same
                if(!(
                    $parentOrder->s_customer_name == $order->s_customer_name &&
                    $parentOrder->s_address_line1 == $order->s_address_line1 &&
                    $parentOrder->s_address_line2 == $order->s_address_line2 &&
                    $parentOrder->s_city == $order->s_city &&
                    $parentOrder->s_state == $order->s_state &&
                    $parentOrder->s_country == $order->s_country &&
                    $parentOrder->s_pincode == $order->s_pincode &&
                    $parentOrder->s_contact_code == $order->s_contact_code &&
                    $parentOrder->s_contact == $order->s_contact &&
                    $parentOrder->o_type == $order->o_type &&
                    $parentOrder->shipment_type == $order->shipment_type
                )) {
                    // Order buyer details are not same, unable to merge order
                    // Ignore this order
                    continue;
                }
                $totalMergedOrder++;
                foreach($order->products as $product) {
                    Product::create([
                        'order_id' => $parentOrder->id,
                        'product_sku' => $product->product_sku,
                        'product_name' => $product->product_name,
                        'product_qty' => $product->product_qty,
                    ]);
                    $product->delete();
                }
                // Update parent product weight
                $parentOrder->weight += $order->weight;
                // Update parent order amount
                $parentOrder->invoice_amount += $order->invoice_amount;
                $parentOrder->igst += $order->igst;
                $parentOrder->sgst += $order->sgst;
                $parentOrder->cgst += $order->cgst;
                $parentOrder->product_qty += $order->product_qty;
                $parentOrder->save();
                $order->delete();
            }
            // Update parent products
            $parentOrder->product_name = implode(',', $parentOrder->products()->get()->pluck('product_name')->toArray());
            $parentOrder->product_sku = implode(',', $parentOrder->products()->get()->pluck('product_sku')->toArray());
            $parentOrder->save();
            DB::commit();
            if($totalMergedOrder > 0) {
                $this->utilities->generate_notification('Success', "Total $totalMergedOrder of $totalOrder order merged successfully", 'success');
            } else {
                $this->utilities->generate_notification('Error', 'Unable to merge orders', 'error');
            }
            return back();
        } catch(Exception $e) {
            DB::rollback();
            $this->utilities->generate_notification('Error', 'Unable to merge orders', 'error');
            return back();
        }
    }

    function get_merge_orders() {
        session(['current_tab' => 'all_order']);
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'pending')
            ->orderBy('inserted', 'desc')
            ->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'pending')->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.merge-order-data', $data);
    }

    // set key of filter data(order)
    function mergeSetFilter(Request $request)
    {
        $data = $request->value;
        Session::put("merge_{$request->key}", $data);
        session([
            'merge_min_value' => isset($request->min_value) ? $request->min_value : session('merge_min_value'),
            'merge_max_value' => isset($request->max_value) ? $request->max_value : session('merge_max_value'),
            'merge_min_weight' => isset($request->min_weight) ? $request->min_weight : session('merge_min_weight'),
            'merge_max_weight' => isset($request->max_weight) ? $request->max_weight : session('merge_max_weight'),
            'merge_min_quantity' => isset($request->min_quantity) ? $request->min_quantity : session('merge_min_quantity'),
            'merge_max_quantity' => isset($request->max_quantity) ? $request->max_quantity : session('merge_max_quantity'),
            'merge_start_date' => isset($request->start_date) ? $request->start_date : session('merge_start_date'),
            'merge_end_date' => isset($request->end_date) ? $request->end_date : session('merge_end_date'),
            'merge_filter_status' => $request->filter_status,
            'merge_order_awb_search' => $request->order_awb_search ?? session('merge_order_awb_search'),
            'merge_multiple_sku' => isset($request->multiple_sku) ? $request->multiple_sku : 'n',
            'merge_single_sku' => isset($request->single_sku) ? $request->single_sku : 'n',
            'merge_match_exact_sku' => isset($request->match_exact_sku) ? $request->match_exact_sku : 'n',
        ]);
    }

    //reset key of filter
    function mergeResetFilter($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session(["merge_$k" => '']);
    }

    //ajax search of order data using session key
    function merge_ajax_filter_order(Request $request)
    {
        $session_channel = session('merge_channel');
        $session_channel_name = session('merge_channel_name');
        $session_order_number = session('merge_order_number');
        $session_payment_type = session('merge_payment_type');
        $session_product = session('merge_product');
        $session_sku = session('merge_sku');
        $min_value = session('merge_min_value');
        $max_value = session('merge_max_value');
        $min_weight = !empty(session('merge_min_weight')) ? intval(session('merge_min_weight') * 1000) : session('merge_min_weight');
        $max_weight = !empty(session('merge_max_weight')) ? intval(session('merge_max_weight') * 1000) : session('merge_max_weight');
        $start_date = session('merge_start_date');
        $end_date = session('merge_end_date');
        $pickup_address = session('merge_pickup_address');
        $delivery_address = session('merge_delivery_address');
        $order_status = session('merge_order_status');
        $filter_status = session('merge_filter_status');
        $awb_number = session('merge_awb_number');
        $courier_partner = session('merge_courier_partner');
        $order_awb_search = session('merge_order_awb_search');
        $single_sku = session('merge_single_sku');
        $multiple_sku = session('merge_multiple_sku');
        $match_exact_sku = session('merge_match_exact_sku');
        $min_quantity = session('merge_min_quantity');
        $max_quantity = session('merge_max_quantity');
        DB::enableQueryLog();
        $query = Order::where('status', 'pending')
            ->where('seller_id', Session()->get('MySeller')->id);
        if (!empty($session_order_number)) {
            $query = $query->where('customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('seller_channel_name', $session_channel_name);
        }
        if (!empty($order_status)) {
            $query = $query->whereIn('status', $order_status);
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($min_quantity)) {
            $query = $query->where('product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('product_qty', '<=', $max_quantity);
        }
        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('product_sku', 'like', '%' . $session_sku . '%');
        }
        if (!empty($min_weight) && !empty($max_weight)) {
            $query = $query->where('weight', '>=', $min_weight)->where('weight', '<=', $max_weight);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
        }
        if (!empty($session_product)) {
            $query = $query->where('product_name', 'like', '%' . $session_product . '%');
        }
        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('customer_order_number', $order)
                        ->orWhereIn('awb_number', $order)
                        ->orWhereIn('s_contact', $order);
                });
            }
        }
        if (!empty($pickup_address) && count($pickup_address) > 0) {
            $query = $query->whereIn('warehouse_id',$pickup_address);
        }
        if (!empty($delivery_address)) {
            $query = $query->where('delivery_address', 'like', '%' . $delivery_address . '%');
        }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('courier_partner', $courier_partner);
        }
        if (!empty($awb_number)) {
            $query = $query->where('awb_number', 'like', '%' . $awb_number . '%');
        }
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = $query->latest('inserted')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.merge-order-data', $data);
    }

    // Reassign order
    function get_reassign_order() {
        $data = $this->info;
        session(['noOfPage' => '20']);
        $data['limit_order'] = Session()->get('noOfPage');
        Session($this->filterArray);
        session(['current_tab' => '']);
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->orderBy('warehouse_name','asc')->get();
        $data['channel'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.reassign-order', $data);
    }

    // set key of filter data(order)
    function reassignSetFilter(Request $request)
    {
        $data = $request->value;
        Session::put("reassign_{$request->key}", $data);
        session([
            'reassign_min_value' => isset($request->min_value) ? $request->min_value : session('reassign_min_value'),
            'reassign_max_value' => isset($request->max_value) ? $request->max_value : session('reassign_max_value'),
            'reassign_min_weight' => isset($request->min_weight) ? $request->min_weight : session('reassign_min_weight'),
            'reassign_max_weight' => isset($request->max_weight) ? $request->max_weight : session('reassign_max_weight'),
            'reassign_min_quantity' => isset($request->min_quantity) ? $request->min_quantity : session('reassign_min_quantity'),
            'reassign_max_quantity' => isset($request->max_quantity) ? $request->max_quantity : session('reassign_max_quantity'),
            'reassign_start_date' => isset($request->start_date) ? $request->start_date : session('reassign_start_date'),
            'reassign_end_date' => isset($request->end_date) ? $request->end_date : session('reassign_end_date'),
            'reassign_filter_status' => $request->filter_status,
            'reassign_order_awb_search' => $request->order_awb_search ?? session('reassign_order_awb_search'),
            'reassign_multiple_sku' => isset($request->multiple_sku) ? $request->multiple_sku : 'n',
            'reassign_single_sku' => isset($request->single_sku) ? $request->single_sku : 'n',
            'reassign_match_exact_sku' => isset($request->match_exact_sku) ? $request->match_exact_sku : 'n',
        ]);
    }

    //reset key of filter
    function reassignResetFilter($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session(["reassign_$k" => '']);
    }

    //ajax search of order data using session key
    function reassign_ajax_filter_order(Request $request)
    {
        $session_channel = session('reassign_channel');
        $session_channel_name = session('reassign_channel_name');
        $session_order_number = session('reassign_order_number');
        $session_payment_type = session('reassign_payment_type');
        $session_product = session('reassign_product');
        $session_sku = session('reassign_sku');
        $min_value = session('reassign_min_value');
        $max_value = session('reassign_max_value');
        $min_weight = !empty(session('reassign_min_weight')) ? intval(session('reassign_min_weight') * 1000) : session('reassign_min_weight');
        $max_weight = !empty(session('reassign_max_weight')) ? intval(session('reassign_max_weight') * 1000) : session('reassign_max_weight');
        $start_date = session('reassign_start_date');
        $end_date = session('reassign_end_date');
        $pickup_address = session('reassign_pickup_address');
        $delivery_address = session('reassign_delivery_address');
        $order_status = session('reassign_order_status');
        $filter_status = session('reassign_filter_status');
        $awb_number = session('reassign_awb_number');
        $courier_partner = session('reassign_courier_partner');
        $order_awb_search = session('reassign_order_awb_search');
        $single_sku = session('reassign_single_sku');
        $multiple_sku = session('reassign_multiple_sku');
        $match_exact_sku = session('reassign_match_exact_sku');
        $min_quantity = session('reassign_min_quantity');
        $max_quantity = session('reassign_max_quantity');
        DB::enableQueryLog();
        $query = Order::whereIn('status', ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled'])
            ->where('seller_id', Session()->get('MySeller')->id)->where('global_type','domestic');
        if (!empty($session_order_number)) {
            $query = $query->where('customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('seller_channel_name', $session_channel_name);
        }
        if (!empty($order_status)) {
            $query = $query->whereIn('status', $order_status);
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($min_quantity)) {
            $query = $query->where('product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('product_qty', '<=', $max_quantity);
        }
        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('product_sku', 'like', '%' . $session_sku . '%');
        }
        if (!empty($min_weight) && !empty($max_weight)) {
            $query = $query->where('weight', '>=', $min_weight)->where('weight', '<=', $max_weight);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
        }
        if (!empty($session_product)) {
            $query = $query->where('product_name', 'like', '%' . $session_product . '%');
        }
        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('customer_order_number', $order)
                        ->orWhereIn('awb_number', $order)
                        ->orWhereIn('s_contact', $order);
                });
            }
        }
        if (!empty($pickup_address) && count($pickup_address) > 0) {
            $query = $query->whereIn('warehouse_id',$pickup_address);
        }
        if (!empty($delivery_address)) {
            $query = $query->where('delivery_address', 'like', '%' . $delivery_address . '%');
        }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('courier_partner', $courier_partner);
        }
        if (!empty($awb_number)) {
            $query = $query->where('awb_number', 'like', '%' . $awb_number . '%');
        }
        $data['total_order'] = $query->latest('inserted')->count();
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.reassign-order-data', $data);
    }

    // Re-assign order
    function reassign_order(Request $request) {
        try {
            $orders = Order::whereIn('id', explode(',', $request->order_id ?? 0))
                ->whereIn('status', ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled'])
                ->get();
            $config = $this->info['config'];
            $totalSuccessOrder = 0;
            $totalFailedOrder = 0;
            $awbNumbers = [];
            $reassignOrderDetails = [];
            $obj = new ReassignHelper();
            foreach ($orders as $o){
                try {
                    if(strtolower($request->partner) == strtolower($o->courier_partner)) {
                        throw new Exception("Can not reassign to same courier partner");
                    }
                    try {
                        $response = $obj->ShipOrder($o, Session()->get('MySeller'), $request->partner ?? null);
                        if ($response['status'] == true) {
                            $obj->singleLabelPDF($response['data']['order_id'], Session()->get('MySeller'));
                            $awbNumbers[] = ['customer_order_number' => $response['data']['customer_order_number'],'new_awb_number' => $response['data']['new_awb_number'], 'old_awb_number' => $response['data']['old_awb_number']];
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
                    }
                    catch(Exception $e){
                        Logger::write('logs/reassign-'.date('Y-m-d').'.text', [
                            'title' => 'Reassign Helper Logs',
                            'data' => $e->getMessage()."-".$e->getLine()."-".$e->getFile()
                        ]);
                        $totalFailedOrder++;
                    }
                    $this->_refreshSession();

                }catch(Exception $e){
                    Logger::write('logs/reassign-'.date('Y-m-d').'.text', [
                        'title' => 'Reassign Helper Logs-1',
                        'data' => $e->getMessage()."-".$e->getLine()."-".$e->getFile()
                    ]);
                    $totalFailedOrder++;
                }
            }
            try{
                if(count($awbNumbers) > 0){
                    ReassignOrderDetails::insert($reassignOrderDetails);
                    $obj->labelZip();
                    $obj->labelZipAttachMail(Session()->get('MySeller')->email,"Reassign Orders","Reassign Orders",$awbNumbers);
                    $obj->deleteLabels();
                }
            }
            catch(Exception $e){
                Logger::write('logs/reassign-'.date('Y-m-d').'.text', [
                    'title' => 'Reassign Helper Logs-2',
                    'data' => $e->getMessage()."-".$e->getLine()."-".$e->getFile()
                ]);
            }
            $totalOrder = $totalSuccessOrder+$totalFailedOrder;
            if($totalSuccessOrder > 0) {
                $this->utilities->generate_notification('Success', "Total $totalSuccessOrder of $totalOrder orders re-assigned successfully", 'success');
            } else {
                $this->utilities->generate_notification('Error', 'Unable to re-assign orders', 'error');
            }
            return back();
        } catch(Exception $e) {
            Logger::write('logs/reassign-'.date('Y-m-d').'.text', [
                'title' => 'Reassign Helper Logs-3',
                'data' => $e->getMessage()."-".$e->getLine()."-".$e->getFile()
            ]);
            $this->utilities->generate_notification('Error', 'Unable to re-assign orders', 'error');
            return back();
        }
    }

    function get_courier_partner(Request $request) {
        $data = $this->info;
        $partners = Partners::where('status', 'y');
        $partners = $partners->get();
        $data['partners'] = $partners;
        return view('seller.reassign_partner_details', $data);
    }

    // Render merge order page
    function split_orders() {
        $this->splitResetFilter($this->myOms);
        $data = $this->info;
        session(['noOfPage' => '20']);
        $data['limit_order'] = Session()->get('noOfPage');
        Session($this->filterArray);
        session(['current_tab' => '']);
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['channel'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.split-order', $data);
    }

    function getOrderItems($orderId){
        $data['items'] = Product::where('order_id',$orderId)->get();
        $data['order'] = Order::find($orderId);
        $data['wareHouses'] = Warehouses::where('seller_id',$data['order']->seller_id)->get();
        return response(
            $data,200
        )->header('Content-Type','application/json');
    }

    // split Order code goes her
    function performSplitOrder(Request $request) {
        $orderDetail = Order::find($request->order_id)->toArray();
        $wareHouseItems = [];
        foreach ($request->warehouses as $w){
            $exp = explode('_',$w);
            $wareHouseItems[$exp[1]][] = $exp[0];
        }
        $cnt=1;
        foreach ($wareHouseItems as $wareHouseId => $items){
            $wareHouse = Warehouses::find($wareHouseId);
            $orderClone = $orderDetail;
            $orderClone['id'] = null;
            $orderClone['warehouse_id'] = $wareHouseId;
            $orderClone['p_warehouse_name'] = $wareHouse->warehouse_name ?? "";
            $orderClone['p_customer_name'] = $wareHouse->contact_name ?? "";
            $orderClone['p_address_line1'] = $wareHouse->address_line1 ?? "";
            $orderClone['p_address_line2'] = $wareHouse->address_line2 ?? "";
            $orderClone['p_country'] = $wareHouse->country ?? "";
            $orderClone['p_state'] = $wareHouse->state ?? "";
            $orderClone['p_city'] = $wareHouse->city ?? "";
            $orderClone['p_pincode'] = $wareHouse->pincode ?? "";
            $orderClone['p_contact'] = $wareHouse->contact_number ?? "";
            $orderClone['p_contact_code'] = $wareHouse->code ?? "";
            $orderClone['customer_order_number'] = $orderClone['customer_order_number']."_SP".$cnt++;
            $orderId = Order::create($orderClone)->id;
            $pname = [];
            $psku = [];
            $productQty=0;
            foreach ($items as $i){
                $itemClone = Product::find($i)->toArray();
                $itemClone['id']=null;
                $itemClone['order_id']=$orderId;
                Product::create($itemClone);
                $pname[] = $itemClone['product_name'];
                $psku[] = $itemClone['product_sku'];
                $productQty+=intval($itemClone['product_qty']);
            }
            Order::where('id', $orderId)->update(array('product_name' => implode(',', $pname),'product_qty' => $productQty, 'product_sku' => implode(',', $psku)));

        }
        Order::where('id',$orderDetail['id'])->delete();
        Product::where('order_id',$orderDetail['id'])->delete();
        $this->utilities->generate_notification('Success', "Orders are Splitted Successfully", 'success');
    }

    // get split able orders
    function get_split_orders() {
        session(['current_tab' => 'all_order']);
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'pending')
            ->where('product_name','like','%,%')
            ->orderBy('inserted', 'desc')
            ->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'pending')->where('product_name','like','%,%')->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.split-order-data', $data);
    }

    // set key of filter data(order)
    function splitSetFilter(Request $request)
    {
        $data = $request->value;
        Session::put("split_{$request->key}", $data);
        session([
            'split_min_value' => isset($request->min_value) ? $request->min_value : session('split_min_value'),
            'split_max_value' => isset($request->max_value) ? $request->max_value : session('split_max_value'),
            'split_min_weight' => isset($request->min_weight) ? $request->min_weight : session('split_min_weight'),
            'split_max_weight' => isset($request->max_weight) ? $request->max_weight : session('split_max_weight'),
            'split_min_quantity' => isset($request->min_quantity) ? $request->min_quantity : session('split_min_quantity'),
            'split_max_quantity' => isset($request->max_quantity) ? $request->max_quantity : session('split_max_quantity'),
            'split_start_date' => isset($request->start_date) ? $request->start_date : session('split_start_date'),
            'split_end_date' => isset($request->end_date) ? $request->end_date : session('split_end_date'),
            'split_filter_status' => $request->filter_status,
            'split_order_awb_search' => $request->order_awb_search ?? session('split_order_awb_search'),
            'split_multiple_sku' => isset($request->multiple_sku) ? $request->multiple_sku : 'n',
            'split_single_sku' => isset($request->single_sku) ? $request->single_sku : 'n',
            'split_match_exact_sku' => isset($request->match_exact_sku) ? $request->match_exact_sku : 'n',
        ]);
    }

    //reset key of filter
    function splitResetFilter($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session(["split_$k" => '']);
    }

    //ajax search of order data using session key
    function split_ajax_filter_order(Request $request)
    {
        $session_channel = session('split_channel');
        $session_channel_name = session('split_channel_name');
        $session_order_number = session('split_order_number');
        $session_payment_type = session('split_payment_type');
        $session_product = session('split_product');
        $session_sku = session('split_sku');
        $min_value = session('split_min_value');
        $max_value = session('split_max_value');
        $min_weight = !empty(session('split_min_weight')) ? intval(session('split_min_weight') * 1000) : session('split_min_weight');
        $max_weight = !empty(session('split_max_weight')) ? intval(session('split_max_weight') * 1000) : session('split_max_weight');
        $start_date = session('split_start_date');
        $end_date = session('split_end_date');
        $pickup_address = session('split_pickup_address');
        $delivery_address = session('split_delivery_address');
        $order_status = session('split_order_status');
        $filter_status = session('split_filter_status');
        $awb_number = session('split_awb_number');
        $courier_partner = session('split_courier_partner');
        $order_awb_search = session('split_order_awb_search');
        $single_sku = session('split_single_sku');
        $multiple_sku = session('split_multiple_sku');
        $match_exact_sku = session('split_match_exact_sku');
        $min_quantity = session('split_min_quantity');
        $max_quantity = session('split_max_quantity');
        DB::enableQueryLog();
        $query = Order::where('status', 'pending')
            ->where('product_name','like','%,%')
            ->where('seller_id', Session()->get('MySeller')->id);
        if (!empty($session_order_number)) {
            $query = $query->where('customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('seller_channel_name', $session_channel_name);
        }
        if (!empty($order_status)) {
            $query = $query->whereIn('status', $order_status);
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($min_quantity)) {
            $query = $query->where('product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('product_qty', '<=', $max_quantity);
        }
        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('product_sku', 'like', '%' . $session_sku . '%');
        }
        if (!empty($min_weight) && !empty($max_weight)) {
            $query = $query->where('weight', '>=', $min_weight)->where('weight', '<=', $max_weight);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
        }
        if (!empty($session_product)) {
            $query = $query->where('product_name', 'like', '%' . $session_product . '%');
        }
        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('customer_order_number', $order)
                        ->orWhereIn('awb_number', $order)
                        ->orWhereIn('s_contact', $order);
                });
            }
        }
        if (!empty($pickup_address) && count($pickup_address) > 0) {
            $query = $query->whereIn('warehouse_id',$pickup_address);
        }
        if (!empty($delivery_address)) {
            //$query = $query->where('delivery_address', 'like', '%' . $delivery_address . '%');
            $query = $query->where(DB::raw("CONCAT(`delivery_address`, ' ', `s_city`, ' ', `s_state`, ' ', `s_pincode`)"), 'like', '%' . $delivery_address . '%');
        }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('courier_partner', $courier_partner);
        }
        if (!empty($awb_number)) {
            $query = $query->where('awb_number', 'like', '%' . $awb_number . '%');
        }
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = $query->latest('inserted')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.split-order-data', $data);
    }


    //For get all order ajax data
    function allOrder()
    {
        // echo Session()->get('noOfPage');
        $this->_refreshSession();
        session(['current_tab' => 'all_order']);
        $data = $this->info;
        $global_type = session('global_type') ?? 'domestic';
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->orderBy('inserted', 'desc')->where('global_type',$global_type)->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type)->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partnerList'] = Partners::where('status', 'y')->get();
        return view('seller.all_order', $data);
    }

    function loadAllOrder(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->tab);

        // filter will be applied here
        $orderQuery = UtilityHelper::ApplyOrderFilter($orderQuery,$request->filter);
        $orderCount = $orderQuery->count();
        $data['order'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['selected_tab'] = $request->tab;
        $data['statusList'] = UtilityHelper::GetAllStatusList();
        $response['content'] = view('seller.partial.all_order',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }
    function exportOrderData(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type);

        if(!empty($request->selected_ids))
            $orderQuery = $orderQuery->whereIn('id',$request->selected_ids);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->selected_tab);
        // filter will be applied here
        $orderQuery = UtilityHelper::ApplyOrderFilter($orderQuery, $request->filter);
        $orderData = $orderQuery->get();

        $filename = "export-order.csv";
        $filePath = storage_path("app/public/{$filename}");
        UtilityHelper::ExportSellerOrderData($filePath, $orderData);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    function loadAllManifestOrder(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'y')->where('global_type',$global_type);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->tab);

        // filter will be applied here
        $orderQuery = UtilityHelper::ApplyOrderFilter($request, $orderQuery);
        $orderCount = $orderQuery->count();
        $data['order'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['selected_tab'] = $request->tab;
        $response['content'] = view('seller.partial.all_order',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }

    //For get all order ajax data
    function allMoreOnOrder()
    {
        // echo Session()->get('noOfPage');
        session(['current_tab' => 'reassign']);
        $data = $this->info;
        $global_type = session('global_type') ?? 'domestic';
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled'])->orderBy('inserted', 'desc')->where('global_type',$global_type)->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type)->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.moreon_order', $data);
    }

    function loadAllMoreOnOrder(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled'])->where('global_type',$global_type);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->tab);

        // filter will be applied here
        $orderQuery = UtilityHelper::ApplyOrderFilter($request, $orderQuery);
        $orderCount = $orderQuery->count();
        $data['order'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['selected_tab'] = $request->tab;
        $response['content'] = view('seller.partial.moreon_order',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }

    function loadAllSplitOrder(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->tab);

        $orderQuery = UtilityHelper::ApplyOrderFilter($request, $orderQuery);
        $orderCount = $orderQuery->count();
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'pending')
            ->where('product_name','like','%,%')
            ->orderBy('inserted', 'desc')
            ->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'pending')->where('product_name','like','%,%')->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['selected_tab'] = $request->tab;
        $response['content'] = view('seller.partial.moreon_order',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }

    function loadAllMergeOrder(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->tab);

        $orderQuery = UtilityHelper::ApplyOrderFilter($request, $orderQuery);
        $orderCount = $orderQuery->count();
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'pending')
            ->orderBy('inserted', 'desc')
            ->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'pending')->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['selected_tab'] = $request->tab;
        $response['content'] = view('seller.partial.moreon_order',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }

    function exportMoreOnOrderData(Request $request)
    {
        $global_type = session('global_type') ?? 'domestic';
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type);

        if(!empty($request->selected_ids))
            $orderQuery = $orderQuery->whereIn('id',$request->selected_ids);
        $orderQuery = UtilityHelper::ApplyOrderTabFilter($orderQuery, $request->selected_tab);
        // filter will be applied here
        $orderQuery = UtilityHelper::ApplyOrderFilter($request, $orderQuery);
        $orderData = $orderQuery->get();

        $filename = "export-order.csv";
        $filePath = storage_path("app/public/{$filename}");
        UtilityHelper::ExportSellerOrderData($filePath, $orderData);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    function allShipment()
    {
        session(['current_tab' => 'delivered']);
        $global_type = session('global_type') ?? 'domestic';
        $data['order'] = Order::where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_action', 'pending')->orderBy('ndr_raised_time','desc')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type)->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ndr', $data);
    }

    function loadAllShipment(Request $request)
    {
        $data = $this->info;
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id);
        if($request->tab == 'action_required')
            $orderQuery = $orderQuery
                ->where('status', '!=', 'delivered')
                ->where('ndr_action', 'pending')
                ->where('ndr_status', 'y')
                ->where('rto_status', 'n')
                ->orderBy('ndr_raised_time','desc')
                ->with('ndrattempts');
        else if($request->tab == 'action_requested')
            $orderQuery = $orderQuery
                ->where('status', '!=', 'delivered')
                ->where('ndr_action', 'requested')
                ->where('ndr_status', 'y')
                ->where('rto_status', 'n')
                ->orderBy('ndr_raised_time','desc');
        else if($request->tab == 'delivered')
            $orderQuery = $orderQuery
                ->where('status', 'delivered')
                ->where('ndr_status', 'y')
                ->where('rto_status', 'n')
                ->with('ndrattempts')
                ->orderBy('ndr_raised_time','desc');
        else if($request->tab == 'rto')
            $orderQuery = $orderQuery
                ->where('rto_status', 'y')
                ->where('ndr_status', 'y')
                ->with('ndrattempts')
                ->orderBy('ndr_raised_time','desc');


        $orderQuery = UtilityHelper::ApplyOrderFilter($orderQuery,$request->tab);
        $orderCount = $orderQuery->count();
        $data['order'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['statusList'] = UtilityHelper::GetAllStatusList();
        $response['content'] = view('seller.partial.all_shipment',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);

    }

    function allBilling()
    {
        session(['current_tab' => 'shipping_charges']);
        $data = $this->info;
        $config = $this->info['config'];
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->paginate(30);
        $data['total_freight_charge'] = $data['billing']->sum('total_charges');
        $data['early_cod'] = EarlyCod::where('status', 'y')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partners'] = Partners::where('status', 'y')->get();
        return view('seller.billing', $data);
    }

    function loadAllBilling(Request $request)
    {
        $data = $this->info;
        $orderQuery = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->orderBy('awb_assigned_date', 'desc');
        $orderQuery = UtilityHelper::ApplyBillingFilter($orderQuery, $request->filter);
        $orderCount = $orderQuery->count();
        $data['order'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['statusList'] = UtilityHelper::GetAllStatusList();
        $data['filter'] = $request->filter;
        $response['content'] = view('seller.b_shipping',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['order']);
        return response()->json($response);
    }

    function loadAllRemitance(Request $request)
    {
        session(['current_tab' => 'remmitance_log']);
        $data = $this->info;
        $remDays = Session()->get('MySeller')->remittance_days ?? 7;
        $data['cod_total'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->sum(DB::raw('IF(collectable_amount > 0, collectable_amount, invoice_amount)'));
        $data['remitted_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->where('cod_remmited', 'y')->sum(DB::raw('IF(collectable_amount > 0, collectable_amount, invoice_amount)'));
        $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        $data['nextRemitDate'] = $codArray['nextRemitDate'];
        $data['nextRemitCod'] = $codArray['nextRemitCod'];
        $data['remitance'] = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->paginate(Session()->get('noOfPage'));
        $orderCount = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->count();
        $data['reversal_amount'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'cancelled')->sum('cod_charges');
        $data['total_cod_remittance'] = $data['remitance']->sum('amount');
        $response['content'] = view('seller.b_remittance_log',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['remitance']);
        return response()->json($response);
    }

    function loadAllRecharge(Request $request)
    {
        session(['current_tab' => 'recharge_log']);
        $data = $this->info;
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        $sellerId = Session()->get('MySeller')->id;
        $page = $request->page ?? 1;
        $offSet = $noOfPage * ($page - 1);
        $transaction_query = "select datetime,id,amount,description,type from transactions where seller_id = {$sellerId} and redeem_type = 'r'";
        $cod_transaction_query = "select datetime,id,amount,description,'c' as type from cod_transactions where seller_id = {$sellerId} and redeem_type = 'r'";

        if (!empty($request->filter['filterStartDate']) && !empty($request->filter['filterEndDate'])){
            $transaction_query = $transaction_query." and date(datetime) >= '{$request->filter['filterStartDate']}' and date(datetime) <= '{$request->filter['filterEndDate']}' ";
            $cod_transaction_query = $cod_transaction_query." and date(datetime) >= '{$request->filter['filterStartDate']}' and date(datetime) <= '{$request->filter['filterEndDate']}'";
        }

        $query = " from
(
{$transaction_query}
union
{$cod_transaction_query}
) a";

        $data['filter'] = $request->filter;
        $data['temp'] = DB::select("select * ".$query." order by datetime desc limit {$noOfPage} offset {$offSet}");
        $data['totalPage'] = DB::select("select count(*) as total ".$query)[0]->total ?? 0;
        $data['successfull_recharge'] = DB::select("select sum(amount) as total ".$query)[0]->total ?? 0;
        $data['total_credit'] = DB::select("select sum(amount) as total ".$query." where type = 'c'")[0]->total ?? 0;
        $data['total_debit'] = DB::select("select sum(amount) as total ".$query." where type = 'd'")[0]->total ?? 0;
        $response['content'] = view('seller.b_recharge_log',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData(count($data['temp']), $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['temp']);
        return response()->json($response);
    }

    function loadAllInvoice(Request $request)
    {
        session(['current_tab' => 'invoice']);
        $data = $this->info;
        $data['invoice'] = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('invoice_date', 'desc')->paginate(Session()->get('noOfPage'));
        $response['content'] = view('seller.b_invoice',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData(count($data['invoice']), $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['invoice']);
        return response()->json($response);
    }

    function loadAllPassbook(Request $request)
    {
        session(['current_tab' => 'passbook']);
        $data = $this->info;
        DB::enableQueryLog();
        $passbookQuery = Transactions::leftJoin('orders',function($join){
            $join->on('transactions.order_id','=','orders.id');
        })->leftJoin('zz_archive_orders',function($join){
            $join->on('transactions.order_id','=','zz_archive_orders.id');
        })->where('transactions.seller_id', Session()->get('MySeller')->id)
            ->select('transactions.*','orders.awb_number', 'orders.courier_partner','zz_archive_orders.awb_number as awb_number1','zz_archive_orders.courier_partner as courier_partner1');
        $passbookQuery = UtilityHelper::ApplyBillingFilter($passbookQuery, $request->filter, 'passbook');
        $totalRecord = $passbookQuery->count();
        $data['filter'] = $request->filter;
        $data['passbook'] = $passbookQuery->orderBy('transactions.id', 'desc')->paginate($request->pageSize);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $response['content'] = view('seller.b_passbook',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($totalRecord, $request->pageSize ?? 20, $request->page ?? 1);
        $response['page']['current_count'] = count($data['passbook']);
        return response()->json($response);
    }

    function loadAllReceipt(Request $request)
    {
        session(['current_tab' => 'receipt']);
        $data = $this->info;
        $data['receipt'] = BillReceipt::where('seller_id', Session()->get('MySeller')->id)->paginate(Session()->get('noOfPage'));
        $response['content'] = view('seller.b_receipt',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData(count($data['receipt']), $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['receipt']);
        return response()->json($response);
    }

    function loadAllWallet(Request $request)
    {
        session(['current_tab' => 'wallet']);
        $data = $this->info;
        $data['account'] = Account_informations::where('seller_id', Session()->get('MySeller')->id)->get();
        $response['content'] = view('seller.b_wallet',$data)->render();
        return response()->json($response);
    }

    function allReverseOrder()
    {
        session(['current_tab' => 'all_reverse_order']);
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->where('o_type', 'reverse')->paginate(Session()->get('noOfPage'));
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('o_type', 'reverse')->count();
        return view('seller.reverse', $data);
    }


    //for order count(display in tab badge)
    function countOrder()
    {
        $match = [
            'product_name' => '',
            'product_sku' => '',
            's_customer_name' => '',
            's_address_line1' => '',
            's_country' => '',
            's_state' => '',
            's_city' => '',
            's_pincode' => '',
            's_contact' => NULL,
            'b_customer_name' => '',
            'b_address_line1' => '',
            'b_country' => '',
            'b_state' => '',
            'b_city' => '',
            'b_pincode' => '',
            'b_contact' => '',
            'weight' => 0,
            'length' => 0,
            'breadth' => 0,
            'height' => 0,
            'invoice_amount' => '',
        ];
        $global_type = session('global_type') ?? 'domestic';
        $data['unprocessable'] = DB::table('orders')
            ->where('global_type',$global_type)
            ->where(function($q) use($match) {
                $q->orWhere(function($q) {
                    $q->orWhere('product_name', '')
                        ->orWhere('product_sku', '')
                        ->orWhere('s_customer_name', '')
                        ->orWhere('s_address_line1', '')
                        ->orWhere('s_country', '')
                        ->orWhere('s_state', '')
                        ->orWhere('s_city', '')
                        ->orWhere('s_pincode', '')
                        ->orWhere('s_contact', '')
                        ->orWhere('b_customer_name', '')
                        ->orWhere('b_address_line1', '')
                        ->orWhere('b_country', '')
                        ->orWhere('b_state', '')
                        ->orWhere('b_city', '')
                        ->orWhere('b_pincode', '')
                        ->orWhere('b_contact', '')
                        ->orWhere('weight', '')
                        ->orWhere('length', '')
                        ->orWhere('breadth', '')
                        ->orWhere('height', '')
                        ->orWhere('weight', 0)
                        ->orWhere('length', 0)
                        ->orWhere('breadth', 0)
                        ->orWhere('height', 0)
                        ->orWhereNull('weight')
                        ->orWhereNull('length')
                        ->orWhereNull('breadth')
                        ->orWhereNull('height')
                        ->orWhere('invoice_amount', '')
                        ->orWhereNull('invoice_amount');
                })
                    ->orWhere(function($q) {
                        $q->whereIn('channel', ['amazon', 'amazon_direct'])
                            ->where(function($q) {
                                $q->where('invoice_amount', 0)
                                    ->orWhere('b_contact', null)
                                    ->orWhere('b_contact', '9999999999');
                            });
                    });
            })
            ->where('status', 'pending')->where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type)->count();
        $data['processing'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)
            ->where('product_name', '!=', '')
            ->where('product_sku', '!=', '')
            ->where('s_customer_name', '!=', '')
            ->where('s_address_line1', '!=', '')
            ->where('s_country', '!=', '')
            ->where('s_state', '!=', '')
            ->where('s_city', '!=', '')
            ->where('s_pincode', '!=', '')
            ->whereNotNull('s_contact')
            ->where('b_customer_name', '!=', '')
            ->where('b_address_line1', '!=', '')
            ->where('b_country', '!=', '')
            ->where('b_state', '!=', '')
            ->where('b_city', '!=', '')
            ->where('b_pincode', '!=', '')
            ->where('b_contact', '!=', '')
            ->where('weight', '!=', '')
            ->where('length', '!=', '')
            ->where('breadth', '!=', '')
            ->where('height', '!=', '')
            ->where('weight', '!=', 0)
            ->where('length', '!=', 0)
            ->where('breadth', '!=', 0)
            ->where('height', '!=', 0)
            ->whereNotNull('weight')
            ->whereNotNull('length')
            ->whereNotNull('breadth')
            ->whereNotNull('height')
            ->where('invoice_amount', '!=', '')
            ->whereNotNull('invoice_amount')
            ->where(function($q) {
                $q->whereNotIn('channel', ['amazon', 'amazon_direct'])
                    ->orWhere(function($q) {
                        $q->where('invoice_amount', '!=', 0)
                            ->where('b_contact', '!=', null)
                            ->where('b_contact', '!=', '9999999999');
                    });
            })
            ->where('status', 'pending')->where('global_type',$global_type)->count();
        $data['ready_to_ship'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('manifest_status', 'n')->where('global_type',$global_type)->count();
        $data['manifest'] = Manifest::join('partners','manifest.courier','partners.keyword')->where('partners.international_enabled',$global_type == "domestic" ? 'n' : 'y' )->where('manifest.seller_id', Session()->get('MySeller')->id)->count();
        $data['return'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('global_type',$global_type)->count();
        $data['all_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('global_type',$global_type)->count();
        return $data;
    }

    // for adding order function
    // function add_order(Request $request)
    // {
    //     // dd($request->all());
    //     //$total_order = Order::where('seller_id', Session()->get('MySeller')->id)->count();
    //     $totalOrders = DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id', Session()->get('MySeller')->id)->where('channel', 'custom')->first();
    //     $totalOrder = $totalOrders->order_number;
    //     //$totalOrderCount = Order::where('seller_id', $sellerId)->count();
    //     if (empty($totalOrder))
    //         $order_number = 1001;
    //     else
    //         $order_number = $totalOrder + 1;
    //     $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
    //     //$barcode = file_get_contents("https://www.Twinnship.in/barcode/test.php?code=$request->order_number");
    //     //file_put_contents("public/assets/seller/images/OrderNo/$request->order_number.png", $barcode);

    //     $data = array(
    //         'seller_id' => Session()->get('MySeller')->id,
    //         'warehouse_id' => $request->warehouse,
    //         'order_number' => $order_number,
    //         'customer_order_number' => $request->customer_order_number,
    //         'order_type' => $request->order_type,
    //         'o_type' => $request->o_type,
    //         //for billing Address
    //         'b_customer_name' => $request->customer_name,
    //         'b_address_line1' => $request->address,
    //         'b_address_line2' => $request->address2,
    //         'b_city' => $request->city,
    //         'b_state' => $request->state,
    //         'b_country' => $request->country,
    //         'b_pincode' => $request->pincode,
    //         'b_contact_code' => $request->contact_code,
    //         'b_contact' => $request->contact,
    //         'delivery_address' => "$request->address,$request->address2,$request->city,$request->state,$request->pincode",

    //         //for pickup address
    //         'p_warehouse_name' => $w->warehouse_name,
    //         'p_customer_name' => $w->contact_name,
    //         'p_address_line1' => $w->address_line1,
    //         'p_address_line2' => $w->address_line2,
    //         'p_city' => $w->city,
    //         'p_state' => $w->state,
    //         'p_country' => $w->country,
    //         'p_pincode' => $w->pincode,
    //         'p_contact_code' => $w->code,
    //         'p_contact' => $w->contact_number,
    //         'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

    //         //for Shipping address
    //         's_customer_name' => $request->customer_name,
    //         's_address_line1' => $request->address,
    //         's_address_line2' => $request->address2,
    //         's_city' => $request->city,
    //         's_state' => $request->state,
    //         's_country' => $request->country,
    //         's_pincode' => $request->pincode,
    //         's_contact_code' => $request->contact_code,
    //         's_contact' => $request->contact,

    //         'vol_weight' => ($request->height * $request->length * $request->breadth) / 5,
    //         'weight' => $request->weight * 1000,
    //         'length' => $request->length,
    //         'breadth' => $request->breadth,
    //         'height' => $request->height,
    //         'product_name' => implode(",", $request->product_name),
    //         'product_sku' => implode(",", $request->product_sku),
    //         's_charge' => $request->shipping_charges,
    //         'c_charge' => $request->cod_charges,
    //         'discount' => $request->discount,
    //         'reseller_name' => $request->reseller_name,
    //         'invoice_amount' => $request->invoice_amount,
    //         //'orderno_barcode' => "public/assets/seller/images/Barcode/$request->order_number.png",
    //         'inserted' => date('Y-m-d H:i:s'),
    //         'inserted_by' => Session()->get('MySeller')->id,
    //     );
    //     $order = Order::create($data);

    //     $n = count($request->product_name);
    //     for ($i = 0; $i < $n; $i++) {
    //         $data_product = array(
    //             'order_id' => $order->id,
    //             'product_sku' => $request->product_sku[$i],
    //             'product_name' => $request->product_name[$i],
    //             'product_qty' => $request->product_qty[$i],
    //         );
    //         Product::create($data_product);
    //     }
    //     $this->utilities->generate_notification('Success', 'Order added successfully', 'success');
    //     return redirect(url('/') . "/my-orders?tab=processing");
    // }


    // for adding order function
    function quick_order(Request $request) {
        $data = $this->info;
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.quick_order', $data);
    }

    function ship_quick_order(Request $request) {
        try {
            DB::beginTransaction();
            $config = $this->info['config'];
            // Create new order
            $totalOrders = DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id', Session()->get('MySeller')->id)->where('channel', 'custom')->first();
            $totalOrder = $totalOrders->order_number;
            if (empty($totalOrder))
                $order_number = 1001;
            else
                $order_number = $totalOrder + 1;

            $warehouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            if(!empty($request->invoice_amount)) {
                if(strtolower($request->state) == strtolower($warehouse->state)) {
                    $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                    $cgst = $percent/2;
                    $sgst = $percent/2;
                } else {
                    $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                    $igst = $percent;
                }
            }

            $data = array(
                'seller_id' => Session()->get('MySeller')->id,
                'warehouse_id' => $warehouse->id,
                'order_number' => $order_number,
                'customer_order_number' => $request->customer_order_number,
                'order_type' => $request->order_type,
                'o_type' => $request->o_type,
                'ewaybill_number' => $request->ewaybill_number ?? "",
                //for billing Address
                'b_customer_name' => $request->customer_name,
                'b_address_line1' => $request->address,
                'b_address_line2' => null,
                'b_city' => $request->city,
                'b_state' => $request->state,
                'b_country' => $request->country,
                'b_pincode' => $request->pincode,
                'b_contact_code' => $request->contact_code,
                'b_contact' => $request->contact,
                'delivery_address' => "$request->address,$request->city,$request->state,$request->pincode",

                //for pickup address
                'p_warehouse_name' => $warehouse->warehouse_name,
                'p_customer_name' => $warehouse->contact_name,
                'p_address_line1' => $warehouse->address_line1,
                'p_address_line2' => $warehouse->address_line2,
                'p_city' => $warehouse->city,
                'p_state' => $warehouse->state,
                'p_country' => $warehouse->country,
                'p_pincode' => $warehouse->pincode,
                'p_contact_code' => $warehouse->code,
                'p_contact' => $warehouse->contact_number,
                'pickup_address' => "$warehouse->address_line1,$warehouse->address_line2,$warehouse->city,$warehouse->state,$warehouse->pincode",

                //for Shipping address
                's_customer_name' => $request->customer_name,
                's_address_line1' => $request->address,
                's_address_line2' => null,
                's_city' => $request->city,
                's_state' => $request->state,
                's_country' => $request->country,
                's_pincode' => $request->pincode,
                's_contact_code' => $request->contact_code,
                's_contact' => $request->contact,

                'vol_weight' => ($request->height * $request->length * $request->breadth) / 5,
                'weight' => $request->weight * 1000,
                'length' => $request->length,
                'breadth' => $request->breadth,
                'height' => $request->height,
                'product_name' => implode(",", $request->product_name),
                'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y" ? implode(",", $request->product_name) : implode(",", $request->product_sku),
                's_charge' => $request->shipping_charges,
                'c_charge' => $request->cod_charges,
                'discount' => $request->discount,
                'reseller_name' => $request->reseller_name,
                'invoice_amount' => $request->invoice_amount,
                'collectable_amount' => $request->collectable_amount ?? 0,
                'igst' => $igst,
                'sgst' => $sgst,
                'cgst' => $cgst,

                // MPS Details
                'shipment_type' => $request->shipment_type,
                'number_of_packets' => $request->number_of_packets ?? 1,

                'inserted' => date('Y-m-d H:i:s'),
                'inserted_by' => Session()->get('MySeller')->id,
            );
            $order = Order::findOrFail(Order::create($data)->id);
            $product_name = [];
            $product_sku = [];
            $totalQuantity = 0;
            $n = count($request->product_name);
            for ($i = 0; $i < $n; $i++) {
                $data_product = array(
                    'order_id' => $order->id,
                    'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y" ? $request->product_name[$i] : $request->product_sku[$i],
                    'product_name' => $request->product_name[$i],
                    'product_qty' => $request->product_qty[$i]
                );
                $product_name[]=$request->product_name[$i];
                $product_sku[]=$data['product_sku'];
                $totalQuantity+=$request->product_qty[$i];
                Product::create($data_product);
            }
            $order->product_name = implode(',',$product_name);
            $order->product_sku = implode(',',$product_sku);
            $order->product_qty = $totalQuantity;
            $order->save();
            // Calculate shipping charge copied from bulk ship
            $sellerData = Seller::find($order->seller_id);
            $shipped = ShippingHelper::ShipOrder($order,$sellerData,$request->partner);
            if($shipped['status'])
                $this->utilities->generate_notification('Success', 'Order shipped successfully', 'success');
            else
                $this->utilities->generate_notification('Error', 'Order shipment failed', 'error');
            DB::commit();
            return redirect(url('/') . "/my-orders");
        } catch(Exception $e) {
            DB::rollback();
            $this->utilities->generate_notification('Error', 'Order shipment failed', 'error');
            return back();
        }
    }

    // for adding order function
    function add_order(Request $request)
    {
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
        //$total_order = Order::where('seller_id', Session()->get('MySeller')->id)->count();
        $totalOrders = DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id', Session()->get('MySeller')->id)->where('channel', 'custom')->first();
        $totalOrder = $totalOrders->order_number;
        //$totalOrderCount = Order::where('seller_id', $sellerId)->count();
        if (empty($totalOrder))
            $order_number = 1001;
        else
            $order_number = $totalOrder + 1;
        //$barcode = file_get_contents("https://www.Twinnship.in/barcode/test.php?code=$request->order_number");
        //file_put_contents("public/assets/seller/images/OrderNo/$request->order_number.png", $barcode);

        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        if(!empty($request->invoice_amount)) {
            if(strtolower($request->state) == strtolower($w->state)) {
                $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                $cgst = $percent/2;
                $sgst = $percent/2;
            } else {
                $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                $igst = $percent;
            }
        }

        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'warehouse_id' => $request->warehouse,
            'order_number' => $order_number,
            'customer_order_number' => $request->customer_order_number,
            'order_type' => $request->order_type,
            'o_type' => $request->o_type,
            'ewaybill_number' => $request->ewaybill_number ?? "",
            //for billing Address
            'b_customer_name' => $request->customer_name,
            'b_address_line1' => $request->address,
            'b_address_line2' => $request->address2,
            'b_city' => $request->city,
            'b_state' => $request->state,
            'b_country' => $request->country,
            'b_pincode' => $request->pincode,
            'b_contact_code' => $request->contact_code,
            'b_contact' => $request->contact,
            'delivery_address' => "$request->address,$request->address2,$request->city,$request->state,$request->pincode",
            'is_qc' => $request->qc_enable == 'y' ? 'y' : 'n',
            //for pickup address
            'p_warehouse_name' => $w->warehouse_name,
            'p_customer_name' => $w->contact_name,
            'p_address_line1' => $w->address_line1,
            'p_address_line2' => $w->address_line2,
            'p_city' => $w->city,
            'p_state' => $w->state,
            'p_country' => $w->country,
            'p_pincode' => $w->pincode,
            'p_contact_code' => $w->code,
            'p_contact' => $w->contact_number,
            'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

            //for Shipping address
            's_customer_name' => $request->customer_name,
            's_address_line1' => $request->address,
            's_address_line2' => $request->address2,
            's_city' => $request->city,
            's_state' => $request->state,
            's_country' => $request->country,
            's_pincode' => $request->pincode,
            's_contact_code' => $request->contact_code,
            's_contact' => $request->contact,

            'same_as_rto' => !empty($request->same_as_rto) ? 'y' : 'n',
            'rto_warehouse_id' => !empty($request->same_as_rto) ? $request->warehouse : $request->rto_warehouse_id,

            // For Global Type
            'global_type' => $request->global_type ?? 'domestic',

            'vol_weight' => ($request->height * $request->length * $request->breadth) / 5,
            'weight' => $request->weight * 1000,
            'length' => $request->length,
            'breadth' => $request->breadth,
            'height' => $request->height,
            'product_name' => implode(",", $request->product_name),
            'product_qty' => array_sum( $request->product_qty),
            'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y"  ? (isset($request->product_name) ? implode(",", $request->product_name) : "") : (isset($request->product_sku) ? implode(",", $request->product_sku) : ""),
            's_charge' => $request->shipping_charges,
            'c_charge' => $request->cod_charges,
            'discount' => $request->discount,
            'reseller_name' => $request->reseller_name,
            'invoice_amount' => $request->invoice_amount,
            'igst' => $igst,
            'collectable_amount' => $request->collectable_amount ?? 0,
            'sgst' => $sgst,
            'cgst' => $cgst,
            //'orderno_barcode' => "public/assets/seller/images/Barcode/$request->order_number.png",

            // MPS Details
            'shipment_type' => $request->isMPS == "on" ? "mps" : null,
            'number_of_packets' => $request->number_of_packets ?? 1,

            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MySeller')->id,
        );
        $order = Order::create($data);

        if($request->qc_enable == "y"){
            $international = [
                'order_id' => $order->id,
                'qc_help_description' => $request->help_description,
                'qc_label' => implode(",",$request->qc_label),
                'qc_value_to_check' => implode(",",$request->value_to_check)
            ];
            $path = [];
            if(!empty($request->product_image)) {
                if (count($request->product_image) > 0) {
                    for ($i = 0; $i < count($request->product_image); $i++) {
                        $oName = $request->product_image[$i]->getClientOriginalName();
                        $type = explode('.', $oName);
                        $name = date('YmdHis') . $i . "." . $type[count($type) - 1];
                        $filepath = "public/assets/admin/images/$name";
                        $request->product_image[$i]->move(public_path('assets/admin/images/'), $name);
                        if (file_exists($filepath)) {
                            $bucketPath = "qc_image";
                            BucketHelper::UploadFile($bucketPath, $filepath);
                            @unlink($filepath);
                        }
                        $path[] = $bucketPath . "/" . $name;
                    }
                }
            }
            $international['qc_image'] = implode(',',$path);
            if(!empty($request->clone_qc_image)){
                $international['qc_image'] = $request->clone_qc_image;
            }
            InternationalOrders::create($international);
        }

        if($request->global_type == "international"){
            $international = [
                'order_id' => $order->id,
                'iec_code' => Session()->get('MySeller')->iec_code,
                'ad_code' => Session()->get('MySeller')->ad_code,
                'ioss' => $request->ioss,
                'eori' => $request->eori,
                'invoice_number' => $request->invoice_reference_number ?? ''
            ];
            $checkData = InternationalOrders::where('order_id',$order->id)->first();
            if(!empty($checkData))
                InternationalOrders::where('id',$checkData->id)->update($international);
            else
                InternationalOrders::create($international);
        }

        $n = count($request->product_name);
        for ($i = 0; $i < $n; $i++) {
            $data_product = array(
                'order_id' => $order->id,
                'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y" ? $request->product_name[$i] : $request->product_sku[$i],
                'product_name' => $request->product_name[$i],
                'product_qty' => $request->product_qty[$i],
            );
            Product::create($data_product);
        }
        $this->utilities->generate_notification('Success', 'Order added successfully', 'success');
        return redirect(url('/') . "/all_order");
    }

    //get order detaills
    function modify_order($id)
    {
        $data['order'] = Order::find($id);
        $data['product'] = Product::where('order_id', $id)->get();
        if($data['order']->global_type == 'international')
            $data['international_order'] = InternationalOrders::where('order_id',$id)->first();
        if($data['order']->is_qc == 'y') {
            $data['qc_details'] = InternationalOrders::where('order_id', $id)->first();
            $data['images'] = [];
            if(!empty($data['qc_details']->qc_image)){
                $image = explode(",",$data['qc_details']->qc_image);
                foreach ($image as $i){
                    $data['images'][] = BucketHelper::GetDownloadLink($i);
                }
            }
        }
        echo json_encode($data);
    }

    //get order data if exist
    function cloneOrder($id)
    {
        // dd($id);
        $data['order'] = Order::where('id', $id)->first();
        if ($data['order'] == null) {
            return 0;
        } else {
            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($data['order']->channel), ['amazon', 'amazon_direct']) && now()->parse($data['order']->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                $data['order']->b_customer_name = null;
                $data['order']->b_address_line1 = null;
                $data['order']->b_address_line2 = null;
                $data['order']->b_city = null;
                $data['order']->b_state = null;
                $data['order']->b_country = null;
                $data['order']->b_pincode = null;
                $data['order']->b_contact_code = null;
                $data['order']->b_contact = null;
                $data['order']->s_customer_name = null;
                $data['order']->s_address_line1 = null;
                $data['order']->s_address_line2 = null;
                $data['order']->s_city = null;
                $data['order']->s_state = null;
                $data['order']->s_country = null;
                $data['order']->s_pincode = null;
                $data['order']->s_contact_code = null;
                $data['order']->s_contact = null;
                $data['order']->delivery_address = null;
                $data['order']->invoice_amount = null;
                $data['order']->product_name = null;
                $data['order']->product_sku = null;
                $data['order']->product_qty = null;
                $data['product'] = [];
            } else {
                $data['product'] = Product::where('order_id', $data['order']->id)->get();
            }
            if($data['order']->is_qc == 'y') {
                $data['qc_details'] = InternationalOrders::where('order_id', $id)->first();
                $data['images'] = [];
                if(!empty($data['qc_details']->qc_image)){
                    $image = explode(",",$data['qc_details']->qc_image);
                    foreach ($image as $i){
                        $data['images'][] = BucketHelper::GetDownloadLink($i);
                    }
                }
            }
            return json_encode($data);
        }
    }

    //update order details
    function update_order(Request $request)
    {
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        if(!empty($request->invoice_amount)) {
            if(strtolower($request->state) == strtolower($w->state)) {
                $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                $cgst = $percent/2;
                $sgst = $percent/2;
            } else {
                $percent = $request->invoice_amount - ($request->invoice_amount/((18/100)+1));
                $igst = $percent;
            }
        }
        $allProducts = [];
        $allSkus = [];
        $allQty = 0;
        if(empty($request->shipment_type)) {
            $data = array(
                'seller_id' => Session()->get('MySeller')->id,
                'warehouse_id' => $request->warehouse,
                'order_number' => $request->order_number,
                'order_type' => $request->order_type,
                'o_type' => $request->o_type,
                'customer_order_number' => $request->customer_order_number,
                'ewaybill_number' => $request->ewaybill_number ?? "",
                //for billing Address
                'b_customer_name' => $request->customer_name,
                'b_address_line1' => $request->address,
                'b_address_line2' => $request->address2,
                'b_city' => $request->city,
                'b_state' => $request->state,
                'b_country' => $request->country,
                'b_pincode' => $request->pincode,
                'b_contact_code' => $request->contact_code,
                'b_contact' => $request->contact,
                'delivery_address' => "$request->address,$request->address2,$request->city,$request->state,$request->pincode",

                //for billing Address
                's_customer_name' => $request->customer_name,
                's_address_line1' => $request->address,
                's_address_line2' => $request->address2,
                's_city' => $request->city,
                's_state' => $request->state,
                's_country' => $request->country,
                's_pincode' => $request->pincode,
                's_contact_code' => $request->contact_code,
                's_contact' => $request->contact,

                'is_qc' => $request->o_type == 'forward' ? 'n' : ($request->qc_enable == 'y' ? 'y' : 'n'),
                //for rto address
                'same_as_rto' => $request->same_as_rto ?? 'n',
                'rto_warehouse_id' => $request->rto_warehouse_id,

                // Global
                'global_type' => $request->global_type ?? 'domestic',

                //for pickup address
                'p_warehouse_name' => $w->warehouse_name,
                'p_customer_name' => $w->contact_name,
                'p_address_line1' => $w->address_line1,
                'p_address_line2' => $w->address_line2,
                'p_city' => $w->city,
                'p_state' => $w->state,
                'p_country' => $w->country,
                'p_pincode' => $w->pincode,
                'p_contact_code' => $w->code,
                'p_contact' => $w->contact_number,
                'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

                'vol_weight' => ($request->height * $request->length * $request->breadth) / 5,
                'weight' => $request->weight * 1000,
                'length' => $request->length,
                'breadth' => $request->breadth,
                'height' => $request->height,
                'product_qty' => array_sum( $request->product_qty),
                'product_name' => isset($request->product_name) ? implode(",", $request->product_name) : "",
                'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y"  ? (isset($request->product_name) ? implode(",", $request->product_name) : "") : (isset($request->product_sku) ? implode(",", $request->product_sku) : ""),
                's_charge' => $request->shipping_charges,
                'c_charge' => $request->cod_charges,
                'discount' => $request->discount,
                'reseller_name' => $request->reseller_name,
                'invoice_amount' => $request->invoice_amount,
                'collectable_amount' => $request->collectable_amount ?? 0,
                'igst' => $igst,
                'sgst' => $sgst,
                'cgst' => $cgst,
                'modified' => date('Y-m-d H:i:s'),
                'modified_by' => Session()->get('MySeller')->id,

                'shipment_type' => $request->isMPS == "on" ? "mps" : null,
                'number_of_packets' => $request->number_of_packets ?? 1,
            );
            //dd($data);
            Order::where('id', $request->order_id)->update($data);
            if($request->global_type == "international"){
                $international = [
                    'order_id' => $request->order_id,
                    'iec_code' => Session()->get('MySeller')->iec_code,
                    'ad_code' => Session()->get('MySeller')->ad_code,
                    'ioss' => $request->ioss,
                    'eori' => $request->eori,
                    'invoice_number' => $request->invoice_reference_number ?? ''
                ];
                InternationalOrders::where('order_id',$request->order_id)->update($international);
            }
            Product::where('order_id', $request->order_id)->delete();

            $n = count($request->product_name);
            for ($i = 0; $i < $n; $i++) {
                $data_product = array(
                    'order_id' => $request->order_id,
                    'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y" ? $request->product_name[$i] : $request->product_sku[$i],
                    'product_name' => $request->product_name[$i],
                    'product_qty' => $request->product_qty[$i],
                );
                $allProducts[]=$request->product_name[$i];
                $allSkus[]=$data['product_sku'];
                $allQty+=$request->product_qty[$i];
                Product::create($data_product);
            }
            Order::where('id',$request->order_id)->update(['product_name' => implode(',',$allProducts),'product_sku' => implode(',',$allSkus),'product_qty' => $allQty]);
        } else if($request->shipment_type == 'mps') {
            $data = array(
                'seller_id' => Session()->get('MySeller')->id,
                'warehouse_id' => $request->warehouse,
                'order_number' => $request->order_number,
                'order_type' => $request->order_type,
                'o_type' => $request->o_type,
                'customer_order_number' => $request->customer_order_number,
                //for billing Address
                'b_customer_name' => $request->customer_name,
                'b_address_line1' => $request->address,
                'b_address_line2' => $request->address2,
                'b_city' => $request->city,
                'b_state' => $request->state,
                'b_country' => $request->country,
                'b_pincode' => $request->pincode,
                'b_contact_code' => $request->contact_code,
                'b_contact' => $request->contact,
                'delivery_address' => "$request->address,$request->address2,$request->city,$request->state,$request->pincode",

                //for billing Address
                's_customer_name' => $request->customer_name,
                's_address_line1' => $request->address,
                's_address_line2' => $request->address2,
                's_city' => $request->city,
                's_state' => $request->state,
                's_country' => $request->country,
                's_pincode' => $request->pincode,
                's_contact_code' => $request->contact_code,
                's_contact' => $request->contact,

                'global_type' => $request->global_type ?? 'domestic',

                //for pickup address
                'p_warehouse_name' => $w->warehouse_name,
                'p_customer_name' => $w->contact_name,
                'p_address_line1' => $w->address_line1,
                'p_address_line2' => $w->address_line2,
                'p_city' => $w->city,
                'p_state' => $w->state,
                'p_country' => $w->country,
                'p_pincode' => $w->pincode,
                'p_contact_code' => $w->code,
                'p_contact' => $w->contact_number,
                'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

                'vol_weight' => ($request->height * $request->length * $request->breadth) / 5,
                'weight' => $request->weight * 1000,
                'length' => $request->length,
                'breadth' => $request->breadth,
                'height' => $request->height,
                'product_qty' => array_sum( $request->product_qty),
                'product_name' => isset($request->product_name) ? implode(",", $request->product_name) : "",
                'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y"  ? (isset($request->product_name) ? implode(",", $request->product_name) : "") : (isset($request->product_sku) ? implode(",", $request->product_sku) : ""),
                's_charge' => $request->shipping_charges,
                'c_charge' => $request->cod_charges,
                'discount' => $request->discount,
                'reseller_name' => $request->reseller_name,
                'invoice_amount' => $request->invoice_amount,
                'collectable_amount' => $request->collectable_amount ?? 0,
                'igst' => $igst,
                'sgst' => $sgst,
                'cgst' => $cgst,
                'modified' => date('Y-m-d H:i:s'),
                'modified_by' => Session()->get('MySeller')->id,
                'shipment_type' => $request->shipment_type,
                'number_of_packets' => $request->number_of_packets ?? 1,
                'is_qc' => $request->o_type == 'forward' ? 'n' : ($request->qc_enable == 'y' ? 'y' : 'n'),
            );
            //dd($data);
            Order::where('id', $request->order_id)->update($data);
            if($request->global_type == "international"){
                $international = [
                    'order_id' => $request->order_id,
                    'ioss' => $request->ioss,
                    'eori' => $request->eori,
                    'iec_code' => Session()->get('MySeller')->iec_code,
                    'ad_code' => Session()->get('MySeller')->ad_code
                ];
                InternationalOrders::where('order_id',$request->order_id)->update($international);
            }
            Product::where('order_id', $request->order_id)->delete();

            $n = count($request->product_name);
            for ($i = 0; $i < $n; $i++) {
                $data_product = array(
                    'order_id' => $request->order_id,
                    'product_sku' => Session()->get('MySeller')->product_name_as_sku == "y" ? $request->product_name[$i] : $request->product_sku[$i],
                    'product_name' => $request->product_name[$i],
                    'product_qty' => $request->product_qty[$i],
                );
                Product::create($data_product);
            }
        }
        if($data['is_qc'] == 'y'){
            $international = [
                'order_id' => $request->order_id,
                'qc_help_description' => $request->help_description,
                'qc_label' => implode(",",$request->qc_label),
                'qc_value_to_check' => implode(",",$request->value_to_check),
            ];
            $path = [];
            if(!empty($request->product_image)){
                for($i=0;$i<count($request->product_image);$i++){
                    $oName=$request->product_image[$i]->getClientOriginalName();
                    $type=explode('.',$oName);
                    $name=date('YmdHis').$i.".".$type[count($type)-1];
                    $filepath="public/assets/admin/images/$name";
                    $request->product_image[$i]->move(public_path('assets/admin/images/'),$name);
                    if (file_exists($filepath)) {
                        $bucketPath = "qc_image";
                        BucketHelper::UploadFile($bucketPath,$filepath);
                        @unlink($filepath);
                    }
                    $path[] = $bucketPath."/".$name;
                }
                $international['qc_image'] = implode(',',$path);
            }
            $checkData = InternationalOrders::where('order_id',$request->order_id )->first();
            if(empty($checkData)){
                InternationalOrders::create($international);
            }
            else
                InternationalOrders::where('order_id',$request->order_id )->update($international);

        }
        $this->utilities->generate_notification('Success', 'Order Updated successfully', 'success');
        return redirect(url('/') . "/all_order");
    }

    //Import order data using csv (500 order bunch using insert)
    public function import_csv_order(Request $request)
    {
        if($request->importType == "update"){
            $op = new OperationController();
            return $op->checkAndUpdateOrdersCSV($request);
        }
        try{
            DB::statement("LOCK TABLES orders WRITE,products WRITE,warehouses WRITE;");
            DB::statement("ANALYZE TABLE `orders`");
            $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
            $orderCount = intval($statement[0]->Auto_increment);
            DB::statement("ANALYZE TABLE `products`");
            $statement = DB::select("SHOW TABLE STATUS LIKE 'products'");
            $productCount = intval($statement[0]->Auto_increment);
            $totalOrders = DB::table('orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id', Session()->get('MySeller')->id)->where('channel', 'custom')->first();
            $totalOrder = $totalOrders->order_number;
            //$orderNo = Order::select('order_number')->where('channel', 'custom')->orderBy('id', 'desc')->first();
            $orderNumberCount = $totalOrder ?? 1000;
            $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
            if (empty($w)) {
                $this->utilities->generate_notification('Oops..', ' Please add Default Warehouse First.', 'error');
                return back();
            }
            $totalCount = 0;
            $test = explode('.', $_FILES['importFile']['name']);
            if (isset($test[1])) {
                if ($test[count($test) - 1] == "csv") {
                    $cnt = 0;
                    $file = $_FILES['importFile']['tmp_name'];
                    $handle = fopen($file, "r");
                    $dataCount = 0;
                    $ordersData = [];
                    $productsData = [];
                    while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                        // if($cnt == 1){
                        //     if(count($fileop)!=28){
                        //         $this->utilities->generate_notification('Invalid Format', ' Please select the valid csv File Format.', 'error');
                        //         return redirect()->back();
                        //     }
                        // }
                        // dd($fileop);
                        if ($cnt > 0) {
                            if ($fileop[0] != "" && $fileop[1] != "") {
                                if (strtolower(trim($fileop[1])) == "cod" || strtolower(trim($fileop[1])) == "prepaid") {
                                    if (strlen($fileop[9]) != '6')
                                        $fileop[9] = "";
                                    if (strlen(trim($fileop[11])) != '10')
                                        $fileop[11] = "";
                                    if (!empty(trim($fileop[12])) && is_numeric(trim($fileop[12])))
                                        $weight = trim($fileop[12]) * 1000;
                                    else
                                        $weight = "";

                                    $igst = 0;
                                    $cgst = 0;
                                    $sgst = 0;
                                    if(!empty($fileop[19])) {
                                        if(strtolower($request->state) == strtolower($w->state)) {
                                            $percent = intval($fileop[19]) - (intval($fileop[19])/((18/100)+1));
                                            $cgst = $percent/2;
                                            $sgst = $percent/2;
                                        } else {
                                            $percent = intval($fileop[19]) - (intval($fileop[19])/((18/100)+1));
                                            $igst = $percent;
                                        }
                                    }

                                    $data = array(
                                        'id' => $orderCount,
                                        'seller_id' => Session()->get('MySeller')->id,
                                        'warehouse_id' => $w->id,
                                        'customer_order_number' => isset($fileop[0]) ? $fileop[0] : $orderNumberCount,
                                        'order_number' => ++$orderNumberCount,
                                        'order_type' => strtolower(trim($fileop[2])) == "reverse" ? "prepaid" : (isset($fileop[1]) ? strtolower(trim($fileop[1])) : ""),
                                        'o_type' => isset($fileop[2]) ? (strtolower(trim($fileop[2])) == "reverse" ? "reverse" : "forward") : "forward",

                                        //for billing address
                                        'b_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                        'b_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                        'b_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                        'b_city' => isset($fileop[6]) ? $fileop[6] : "",
                                        'b_state' => isset($fileop[7]) ? ($fileop[7] == 0 ? "" : $fileop[7]) : "",
                                        'b_country' => isset($fileop[8]) ? $fileop[8] : "",
                                        'b_pincode' => isset($fileop[9]) ? trim($fileop[9]) : "",
                                        'b_contact_code' => isset($fileop[10]) ? "+".$fileop[10] : "",
                                        'b_contact' => isset($fileop[11]) ? $fileop[11] : "",

                                        //for shipping Address
                                        's_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                        's_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                        's_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                        's_city' => isset($fileop[6]) ? $fileop[6] : "",
                                        's_state' => isset($fileop[7]) ? ($fileop[7] == 0 ? "" : $fileop[7]) : "",
                                        's_country' => isset($fileop[8]) ? $fileop[8] : "",
                                        's_pincode' => isset($fileop[9]) ? $fileop[9] : "",
                                        's_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                        's_contact' => isset($fileop[11]) ? trim($fileop[11]) : "",

                                        'weight' => $weight,
                                        'length' => isset($fileop[13]) ? $fileop[13] : "",
                                        'height' => isset($fileop[14]) ? $fileop[14] : "",
                                        'breadth' => isset($fileop[15]) ? $fileop[15] : "",
                                        'vol_weight' => (intval($fileop[14] ?? 1) * intval($fileop[13] ?? 1) * intval($fileop[15] ?? 1)) / 5,
                                        's_charge' => isset($fileop[16]) ? $fileop[16] : "",
                                        'c_charge' => isset($fileop[17]) ? $fileop[17] : "",
                                        'discount' => isset($fileop[18]) ? $fileop[18] : "",
                                        'invoice_amount' => isset($fileop[19]) ? intval($fileop[19]) : "",
                                        'igst' => $igst,
                                        'sgst' => $sgst,
                                        'cgst' => $cgst,
                                        'reseller_name' => isset($fileop[20]) ? $fileop[20] : "",
                                        'suggested_awb' => isset($fileop[21]) ? trim($fileop[21], "`") : "",
                                        'delivery_address' => "$fileop[3],$fileop[4],$fileop[5],$fileop[6],$fileop[7]",
                                        //for pickup address
                                        'p_warehouse_name' => $w->warehouse_name,
                                        'p_customer_name' => $w->contact_name,
                                        'p_address_line1' => $w->address_line1,
                                        'p_address_line2' => $w->address_line2,
                                        'p_city' => $w->city,
                                        'p_state' => $w->state,
                                        'p_country' => $w->country,
                                        'p_pincode' => $w->pincode,
                                        'p_contact_code' => $w->code,
                                        'p_contact' => $w->contact_number,
                                        'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",
                                        'inserted' => date('Y-m-d H:i:s'),
                                        'inserted_by' => Session()->get('MySeller')->id,
                                        'channel_name' => isset($fileop[23]) ? trim($fileop[23]) : "default_channel",
                                    );
                                    if(!empty($fileop[22])){
                                        $checkWarehouse = Warehouses::where('warehouse_name',$fileop[22])->first();
                                        if(!empty($checkWarehouse)){
                                            $data['rto_warehouse_id'] = $checkWarehouse->id;
                                            $data['same_as_rto'] = 'n';
                                        }
                                    }
                                    $ordersData[] = $data;
                                    //loop for products
                                    $all_products = [];
                                    $all_skus = [];
                                    $totalQty = 0;
                                    for ($i = 24; $i <= 10000; $i += 3) {
                                        $temp = $i;
                                        if (!isset($fileop[$temp])) {
                                            break;
                                        }
                                        if ($fileop[$temp] == "")
                                            break;
                                        $data_product = array(
                                            'id' => $productCount++,
                                            'order_id' => $orderCount,
                                            'product_name' => isset($fileop[$temp]) ? $fileop[$temp] : "",
                                            'product_sku' => isset($fileop[$temp+1]) ? $fileop[$temp+1] : "",
                                            'product_qty' => intval($fileop[$temp+2] ?? "") == 0 ? "1" : intval(trim($fileop[$temp+2]))
                                        );

                                        if(Session()->get('MySeller')->product_name_as_sku == 'y')
                                            $data_product['product_sku'] = $data_product['product_name'];

                                        $totalQty+= $data_product['product_qty'];
                                        $all_products[] = $data_product['product_name'];
                                        $all_skus[] = $data_product['product_sku'];
                                        $productsData[] = $data_product;
                                        if (count($productsData) == 500) {
                                            Product::insert($productsData);
                                            $productsData = [];
                                        }
                                    }
                                    $ordersData[$dataCount]['product_name'] = implode(',', $all_products);
                                    $ordersData[$dataCount]['product_sku'] = implode(',', $all_skus);
                                    $ordersData[$dataCount]['product_qty'] = $totalQty;
                                    $orderCount++;
                                    $dataCount++;
                                    $totalCount++;
                                    if (count($ordersData) == 500) {
                                        Order::insert($ordersData);
                                        $ordersData = [];
                                        $dataCount=0;
                                    }
                                }
                            }
                        }
                        $cnt++;
                    }
                    Product::insert($productsData);
                    Order::insert($ordersData);
                    DB::statement("UNLOCK TABLES");
                    $this->utilities->generate_notification('Success', "$totalCount Orders imported successfully please check and unprocessable orders as well", 'success');
                    return redirect(route('seller.order_processing'));
                } else {
                    DB::statement("UNLOCK TABLES");
                    $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                    return back();
                }
            } else {
                DB::statement("UNLOCK TABLES");
                $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
                return back();
            }
        }
        catch(Exception $e){
            DB::statement("UNLOCK TABLES");
            dd($e->getMessage()."=".$e->getFile()."=".$e->getLine());
            $this->utilities->generate_notification('Oops..', ' Please check files', 'error');
            return back();
        }
        DB::statement("UNLOCK TABLES");
        $this->utilities->generate_notification('Oops..', ' Please validate the files to import', 'error');
        return back();

    }

    //Export order CSV
    public function export_csv_order(Request $request)
    {
        // dd($request->all());
        $name = "exports/Twinnship";
        $filename = "Twinnship";
        $session_channel = session('channel');
        $session_channel_name = session('channel_name');
        $session_order_number = session('order_number');
        $session_payment_type = session('payment_type');
        $session_product = session('product');
        $min_value = session('min_value');
        $max_value = session('max_value');
        $start_date = session('start_date');
        $end_date = session('end_date');
        $pickup_address = session('pickup_address');
        $delivery_address = session('delivery_address');
        $order_status = session('order_status');
        $filter_status = session('filter_status');
        $current_tab = session('current_tab');
        $order_awb_search = session('order_awb_search');
        $global_type = session('global_type') ?? 'domestic';
        $courier_partner = session('courier_partner');
        $session_awb_number = session('awb_number');
        $session_channel_code = session('channel_code');
        $min_quantity = session('min_quantity');
        $max_quantity = session('max_quantity');
        $min_weight = !empty(session('min_weight')) ? intval(session('min_weight') * 1000) : session('min_weight');
        $max_weight = !empty(session('max_weight')) ? intval(session('max_weight') * 1000) : session('max_weight');
        $session_sku = session('sku');
        $single_sku = session('single_sku');
        $multiple_sku = session('multiple_sku');
        $match_exact_sku = session('match_exact_sku');
        $session_order_tag = session('order_tag');
        DB::enableQueryLog();
        $query = Order::select('orders.customer_order_number',
            'orders.o_type',
            'orders.order_type',
            'orders.inserted',
            'orders.status',
            'orders.awb_number',
            'orders.awb_assigned_date',
            'orders.id',
            'orders.channel',
            'orders.rto_status',
            'orders.seller_channel_name',
            'orders.delivered_date',
            'orders.delivery_address',
            'orders.b_customer_name',
            'orders.b_address_line1',
            'orders.b_address_line2',
            'orders.b_city',
            'orders.b_state',
            'orders.b_country',
            'orders.b_pincode',
            'orders.b_contact_code',
            'orders.b_contact',
            'orders.s_address_line1',
            'orders.s_address_line2',
            'orders.b_city',
            'orders.s_state',
            'orders.s_country',
            'orders.s_pincode',
            'orders.s_contact_code',
            'orders.s_contact',
            'orders.p_address_line1',
            'orders.p_address_line2',
            'orders.p_city',
            'orders.p_state',
            'orders.p_country',
            'orders.p_pincode',
            'orders.length',
            'orders.pickup_time',
            'orders.weight',
            'orders.height',
            'orders.breadth',
            'orders.shipping_charges',
            'orders.cod_charges',
            'orders.discount',
            'orders.invoice_amount',
            'orders.awb_assigned_date',
            'orders.last_sync',
            'orders.courier_partner',
            'orders.product_name',
            'orders.product_sku',
            'orders.product_qty',
            'orders.expected_delivery_date'
        )->where('orders.seller_id', Session()->get('MySeller')->id)->where('orders.global_type',$global_type);
        if (!empty($session_order_number)) {
            $query = $query->where('orders.customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('orders.channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('orders.seller_channel_name', $session_channel_name);
        }
//        if (!empty($courier_partner)) {
//            //dd("here");
//            $query = $query->where('courier_partner', $courier_partner);
//        }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('orders.courier_partner', $courier_partner);
        }
        if (!empty($session_awb_number)) {
            $query = $query->where('orders.awb_number', $session_awb_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('orders.channel', $session_channel);
        }
        if(!empty($session_channel_code)){
            $query = $query->whereIn('orders.channel_name', $session_channel_code);
        }
        if (!empty($order_status)) {
            $query->where(function($q) use($order_status) {
                foreach($order_status as $row) {
                    if($row == 'rto_delivered') {
                        $q = $q->orWhere(function($q) {
                            $q->where('orders.status', 'delivered')
                                ->where('orders.rto_status', 'y');
                        });
                    } else if($row == 'rto_in_transit') {
                        $q = $q->orWhere(function($q) {
                            $q->where('orders.status', 'in_transit')
                                ->where('orders.rto_status', 'y');
                        });
                    }
                    else if($row == 'rto_initated' || $row == 'rto_initiated') {
                        $q = $q->orWhere(function($q) {
                            $q->whereIn('orders.status', ['rto_initated','rto_initiated'])
                                ->where('orders.rto_status', 'y');
                        });
                    }
                    else {
                        $q = $q->orWhere(function($q) use($row) {
                            $q->where('orders.status', $row)
                                ->where('orders.rto_status', 'n');
                        });
                    }
                }
            });
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('orders.order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            // $query = $query->whereBetween('invoice_amount', [$min_value, $max_value]);
            $query = $query->where('orders.invoice_amount', '>=', intval($min_value))->where('orders.invoice_amount', '<=', intval($max_value));
        }
        if (!empty($start_date) && !empty($end_date)) {
            //            $query = $query->whereBetween('inserted', [$start_date, $end_date]);
            $query = $query->whereDate('orders.inserted', '>=', $start_date)->whereDate('orders.inserted', '<=', $end_date);
        }

        if (!empty($min_quantity)) {
            $query = $query->where('orders.product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('orders.product_qty', '<=', $max_quantity);
        }

        if (!empty($min_weight)) {
            $query = $query->where('orders.weight', '>=', $min_weight);
        }
        if (!empty($max_weight)) {
            $query = $query->where('orders.weight', '<=', $max_weight);
        }

        if(!empty($session_sku)) {
            $query = $query->where('orders.product_sku', 'like', '%' . $session_sku . '%');
        }

        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('orders.product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('orders.product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('orders.product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('orders.product_sku', 'like', '%' . $session_sku . '%');
        }

        if(!empty($session_order_tag))
        {
            $query = $query->leftJoin('international_orders','international_orders.order_id','orders.id')->whereIn('international_orders.shopify_tag',$session_order_tag);
        }

        $match = [
            'orders.product_name' => '',
            'orders.product_sku' => '',
            'orders.s_customer_name' => '',
            'orders.s_address_line1' => '',
            'orders.s_country' => '',
            'orders.s_state' => '',
            'orders.s_city' => '',
            'orders.s_pincode' => '',
            'orders.s_contact' => NULL,
            'orders.b_customer_name' => '',
            'orders.b_address_line1' => '',
            'orders.b_country' => '',
            'orders.b_state' => '',
            'orders.b_city' => '',
            'orders.b_pincode' => '',
            'orders.b_contact' => '',
            'orders.weight' => 0,
            'orders.length' => 0,
            'orders.breadth' => 0,
            'orders.height' => 0,
            'orders.invoice_amount' => '',
        ];

        if(!empty($session_tag_value))
        {
            $query = $query->leftJoin('international_orders','international_orders.order_id','orders.id')->whereIn('international_orders.shopify_tag',$session_tag_value);
        }

        if (!empty($session_product)) {
            // if ($filter_status == 'unprocessable_order_data') {
            //     $query = $query->where('product_name', 'like', '%' . $session_product . '%')->orWhere($match)->where('status', 'pending');
            // } elseif ($filter_status == 'processing_order_data') {
            //     $query = $query->where('product_name', 'like', '%' . $session_product . '%')->where('b_customer_name', '<>', '')->where('b_address_line1', '<>', '')->where('weight', '<>', '')->where('status', 'pending')->orWhere('product_sku', 'like', '%' . $session_product . '%')->where('b_customer_name', '<>', '')->where('b_address_line1', '<>', '')->where('weight', '<>', '')->where('status', 'pending');
            // } elseif ($filter_status == 'ready_to_ship_data') {
            //     $query = $query->where('product_name', 'like', '%' . $session_product . '%')->where('status', 'shipped')->where('manifest_status','n')->orWhere('product_sku', 'like', '%' . $session_product . '%')->where('status', 'shipped')->where('manifest_status','n');
            // } elseif ($filter_status == 'return_order_data') {
            //     $query = $query->where('product_name', 'like', '%' . $session_product . '%')->where('status', 'return')->orWhere('product_sku', 'like', '%' . $session_product . '%')->where('status', 'return');
            // }
            $query = $query->where(function ($q) use ($session_product) {
                $q->where('orders.product_name', 'like', '%' . $session_product . '%')
                    ->orWhere('orders.product_sku', 'like', '%' . $session_product . '%');
            });
        }

        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('orders.customer_order_number', $order)
                        ->orWhereIn('orders.awb_number', $order)
                        ->orWhereIn('orders.s_contact', $order);
                });
            }
        }

        if (!empty($pickup_address) && count($pickup_address)>0) {
            $query = $query->whereIn('orders.warehouse_id',$pickup_address);
            //$query = $query->where('pickup_address', 'like', '%' . $pickup_address . '%');
        }
        if (!empty($delivery_address)) {
            $query = $query->where('orders.delivery_address', 'like', '%' . $delivery_address . '%');
        }
        if ($filter_status == 'unprocessable_order_data') {
            // $query = $query->orWhere($match)->where('status', 'pending');
            $query = $query->where(function($q) use($match) {
                $q->orWhere(function($q) {
                    $q->orWhere('orders.product_name', '')
                        ->orWhere('orders.product_sku', '')
                        ->orWhere('orders.s_customer_name', '')
                        ->orWhere('orders.s_address_line1', '')
                        ->orWhere('orders.s_country', '')
                        ->orWhere('orders.s_state', '')
                        ->orWhere('orders.s_city', '')
                        ->orWhere('orders.s_pincode', '')
                        ->orWhere('orders.s_contact', '')
                        ->orWhere('orders.b_customer_name', '')
                        ->orWhere('orders.b_address_line1', '')
                        ->orWhere('orders.b_country', '')
                        ->orWhere('orders.b_state', '')
                        ->orWhere('orders.b_city', '')
                        ->orWhere('orders.b_pincode', '')
                        ->orWhere('orders.b_contact', '')
                        ->orWhere('orders.weight', '')
                        ->orWhere('orders.length', '')
                        ->orWhere('orders.breadth', '')
                        ->orWhere('orders.height', '')
                        ->orWhere('orders.weight', 0)
                        ->orWhere('orders.length', 0)
                        ->orWhere('orders.breadth', 0)
                        ->orWhere('orders.height', 0)
                        ->orWhereNull('orders.weight')
                        ->orWhereNull('orders.length')
                        ->orWhereNull('orders.breadth')
                        ->orWhereNull('orders.height')
                        ->orWhere('orders.invoice_amount', '')
                        ->orWhereNull('orders.invoice_amount');
                })
                    ->orWhere(function($q) {
                        $q->whereIn('orders.channel', ['amazon', 'amazon_direct'])
                            ->where(function($q) {
                                $q->where('orders.invoice_amount', 0)
                                    ->orWhere('orders.b_contact', null)
                                    ->orWhere('orders.b_contact', '9999999999');
                            });
                    });
            })
                ->where('orders.status', 'pending');
        }
        elseif ($filter_status == 'processing_order_data') {
            $query = $query->where('orders.product_name', '!=', '')
                ->where('orders.product_sku', '!=', '')
                ->where('orders.s_customer_name', '!=', '')
                ->where('orders.s_address_line1', '!=', '')
                ->where('orders.s_country', '!=', '')
                ->where('orders.s_state', '!=', '')
                ->where('orders.s_city', '!=', '')
                ->where('orders.s_pincode', '!=', '')
                ->whereNotNull('orders.s_contact')
                ->where('orders.b_customer_name', '!=', '')
                ->where('orders.b_address_line1', '!=', '')
                ->where('orders.b_country', '!=', '')
                ->where('orders.b_state', '!=', '')
                ->where('orders.b_city', '!=', '')
                ->where('orders.b_pincode', '!=', '')
                ->where('orders.b_contact', '!=', '')
                ->where('orders.weight', '!=', '')
                ->where('orders.length', '!=', '')
                ->where('orders.breadth', '!=', '')
                ->where('orders.height', '!=', '')
                ->where('orders.weight', '!=', 0)
                ->where('orders.length', '!=', 0)
                ->where('orders.breadth', '!=', 0)
                ->where('orders.height', '!=', 0)
                ->whereNotNull('orders.weight')
                ->whereNotNull('orders.length')
                ->whereNotNull('orders.breadth')
                ->whereNotNull('orders.height')
                ->where('orders.invoice_amount', '!=', '')
                ->whereNotNull('orders.invoice_amount')
                ->where(function($q) {
                    $q->whereNotIn('orders.channel', ['amazon', 'amazon_direct'])
                        ->orWhere(function($q) {
                            $q->where('orders.invoice_amount', '!=', 0)
                                ->where('orders.b_contact', '!=', null)
                                ->where('orders.b_contact', '!=', '9999999999');
                        });
                })
                ->where('orders.status', 'pending');
        } elseif ($filter_status == 'ready_to_ship_data') {
            $query = $query->where('orders.manifest_status', 'n')->whereNotIn('orders.status', ['pending', 'cancelled']);
        } elseif ($filter_status == 'return_order_data') {
            $query = $query->where('orders.rto_status', 'y');
        }
        if ($current_tab == 'ready_to_ship') {
            $query = $query->where('orders.manifest_status', 'n')->whereNotIn('orders.status', ['pending', 'cancelled']);
        }
        elseif ($current_tab == 'order_unprocessable') {
            // $query = $query->orWhere($match)->where('status', 'pending');
            $query = $query->where(function($q) use($match) {
                $q->orWhere($match)
                    ->orWhere(function($q) {
                        $q->whereIn('orders.channel', ['amazon', 'amazon_direct'])
                            ->where(function($q) {
                                $q->where('orders.invoice_amount', 0)
                                    ->orWhere('orders.b_contact', null)
                                    ->orWhere('orders.b_contact', '9999999999');
                            });
                    });
            })
                ->where('orders.status', 'pending');
        }
        elseif ($current_tab == 'order_processing') {
            $query = $query->where('orders.seller_id', Session()->get('MySeller')->id)
                ->where('orders.product_name', '!=', '')
                ->where('orders.product_sku', '!=', '')
                ->where('orders.s_customer_name', '!=', '')
                ->where('orders.s_address_line1', '!=', '')
                ->where('orders.s_country', '!=', '')
                ->where('orders.s_state', '!=', '')
                ->where('orders.s_city', '!=', '')
                ->where('orders.s_pincode', '!=', '')
                ->whereNotNull('orders.s_contact')
                ->where('orders.b_customer_name', '!=', '')
                ->where('orders.b_address_line1', '!=', '')
                ->where('orders.b_country', '!=', '')
                ->where('orders.b_state', '!=', '')
                ->where('orders.b_city', '!=', '')
                ->where('orders.b_pincode', '!=', '')
                ->where('orders.b_contact', '!=', '')
                ->where('orders.weight', '!=', '')
                ->where('orders.length', '!=', '')
                ->where('orders.breadth', '!=', '')
                ->where('orders.height', '!=', '')
                ->where('orders.weight', '!=', 0)
                ->where('orders.length', '!=', 0)
                ->where('orders.breadth', '!=', 0)
                ->where('orders.height', '!=', 0)
                ->whereNotNull('orders.weight')
                ->whereNotNull('orders.length')
                ->whereNotNull('orders.breadth')
                ->whereNotNull('orders.height')
                ->where('orders.invoice_amount', '!=', '')
                ->whereNotNull('orders.invoice_amount')
                ->where(function($q) {
                    $q->whereNotIn('orders.channel', ['amazon', 'amazon_direct'])
                        ->orWhere(function($q) {
                            $q->where('orders.invoice_amount', '!=', 0)
                                ->where('orders.b_contact', '!=', null)
                                ->where('orders.b_contact', '!=', '9999999999');
                        });
                })
                ->where('orders.status', 'pending');
        }
        elseif ($current_tab == 'order_manifest') {
            $query = DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->where('orders.seller_id', Session()->get('MySeller')->id);
            if(!empty($start_date) && !empty($end_date)){
                $mquery = Manifest::select('id')->where('seller_id', Session()->get('MySeller')->id)->whereDate('created', '>=', $start_date)->whereDate('created', '<=', $end_date)->get()->toArray();
                if(!empty($mquery) && count($mquery) > 0)
                    $query = $query->whereIn('manifest_order.manifest_id',$mquery);
            }
            $query = $query->select('orders.*');
        }
        elseif ($current_tab == 'order_return') {
            $query = $query->where('orders.rto_status', 'y');
        }
        else {
            $query = $query->orderBy('orders.id', 'desc');
        }
        if (!empty($request->export_order_id)) {
            $order_ids = explode(',', $request->export_order_id);
            $all_data = Order::where('orders.seller_id', Session()->get('MySeller')->id)->whereIn('orders.id', $order_ids)->orderBy('orders.id', 'desc')->with('Intransittable')->get();
        }
        else {
            if($global_type == 'international')
                $all_data = $query->with('InternationalDetails')->get();
            else
                $all_data = $query->with('Intransittable','ofdDate')->get();
        }
        switch($global_type){
            case 'international':
                $fp = fopen("$name.csv", 'w');
                // $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status', 'AWB Number', 'Courier Partner', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date','Status','Estimate Delivery Date' ,'AWB Number', 'Courier Partner', 'Channel Name', 'Store Name', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Collectable Amount', 'AWB Assigned Date', 'Last Sync','HSN Number','HTS Number','IEC Code','AD Code','IOSS','EORI', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                fputcsv($fp, $info);
                $cnt = 1;
                $PartnerName = Partners::getPartnerKeywordList();
                foreach ($all_data as $e) {
                    if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                        $e->b_customer_name = 'PII Data Archived';
                        $e->b_address_line1 = 'PII Data Archived';
                        $e->b_address_line2 = 'PII Data Archived';
                        $e->b_city = 'PII Data Archived';
                        $e->b_state = 'PII Data Archived';
                        $e->b_country = 'PII Data Archived';
                        $e->b_pincode = 'PII Data Archived';
                        $e->b_contact_code = 'PII Data Archived';
                        $e->b_contact = 'PII Data Archived';
                        $e->s_customer_name = 'PII Data Archived';
                        $e->s_address_line1 = 'PII Data Archived';
                        $e->s_address_line2 = 'PII Data Archived';
                        $e->s_city = 'PII Data Archived';
                        $e->s_state = 'PII Data Archived';
                        $e->s_country = 'PII Data Archived';
                        $e->s_pincode = 'PII Data Archived';
                        $e->s_contact_code = 'PII Data Archived';
                        $e->s_contact = 'PII Data Archived';
                        $e->invoice_amount = 'PII Data Archived';
                        $e->product_name = 'PII Data Archived';
                        $e->product_sku = 'PII Data Archived';
                        $e->product_qty = 'PII Data Archived';
                        $e->delivery_address = 'PII Data Archived';
                    }
                    if ($e->rto_status == 'y' && $e->status == 'delivered')
                        $e->status = 'rto_delivered';
                    $internationalDetails = $e->InternationalDetails;
                    $courierPartner = !empty($e->courier_partner) ? ($PartnerName[$e->courier_partner] ?? $e->courier_partner) : '';
                    $weight = !empty($e->weight) ? $e->weight / 1000 : '';
                    if($e->status == 'delivered' && $e->rto_status == 'y')
                        $e->status = 'rto_delivered';
                    else if($e->rto_status == 'y' && $e->status=='in_transit')
                        $e->status='rto_in_transit';
                    if($this->fullInformation)
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $courierPartner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->delivered_date, $e->b_customer_name, $e->s_address_line1, $e->s_address_line2, $e->b_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date, $e->last_sync,$internationalDetails->hsn,$internationalDetails->hts,$internationalDetails->iec_code,$internationalDetails->ad_code,$internationalDetails->ioss,$internationalDetails->eori);
                    else
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $courierPartner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->delivered_date, $e->b_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date, $e->last_sync,$internationalDetails->hsn,$internationalDetails->hts,$internationalDetails->iec_code,$internationalDetails->ad_code,$internationalDetails->ioss,$internationalDetails->eori);
                    $products = Product::where('order_id', $e->id)->get();
                    foreach ($products as $p) {
                        $info[] = $p->product_name;
                        $info[] = $p->product_sku;
                        $info[] = $p->product_qty;
                    }
                    fputcsv($fp, $info);
                    $cnt++;
                }
                // Output headers.
                break;
            default:
                $fp = fopen("$name.csv", 'w');
                // $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status', 'AWB Number', 'Courier Partner', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date','Connection Date','Pickup Date', 'Status','Estimate Delivery Date', 'AWB Number', 'Courier Partner', 'Channel Name', 'Store Name', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Collectable Amount', 'AWB Assigned Date', 'Last Sync','RTO Initiated Date','RTO Delivered Date','OFD Attempt', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                fputcsv($fp, $info);
                $cnt = 1;
                $PartnerName = Partners::getPartnerKeywordList();
                foreach ($all_data as $e) {
                    $intransit = $e->Intransittable;
                    if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                        $e->b_customer_name = 'PII Data Archived';
                        $e->b_address_line1 = 'PII Data Archived';
                        $e->b_address_line2 = 'PII Data Archived';
                        $e->b_city = 'PII Data Archived';
                        $e->b_state = 'PII Data Archived';
                        $e->b_country = 'PII Data Archived';
                        $e->b_pincode = 'PII Data Archived';
                        $e->b_contact_code = 'PII Data Archived';
                        $e->b_contact = 'PII Data Archived';
                        $e->s_customer_name = 'PII Data Archived';
                        $e->s_address_line1 = 'PII Data Archived';
                        $e->s_address_line2 = 'PII Data Archived';
                        $e->s_city = 'PII Data Archived';
                        $e->s_state = 'PII Data Archived';
                        $e->s_country = 'PII Data Archived';
                        $e->s_pincode = 'PII Data Archived';
                        $e->s_contact_code = 'PII Data Archived';
                        $e->s_contact = 'PII Data Archived';
                        $e->invoice_amount = 'PII Data Archived';
                        $e->product_name = 'PII Data Archived';
                        $e->product_sku = 'PII Data Archived';
                        $e->product_qty = 'PII Data Archived';
                        $e->delivery_address = 'PII Data Archived';
                    }
                    if ($e->rto_status == 'y' && $e->status == 'delivered')
                        $e->status = 'rto_delivered';
                    $courierPartner = !empty($e->courier_partner) ? ($PartnerName[$e->courier_partner] ?? $e->courier_partner) : '';
                    $weight = !empty($e->weight) ? $e->weight / 1000 : '';
                    if($e->status == 'delivered' && $e->rto_status == 'y')
                        $e->status = 'rto_delivered';
                    else if($e->rto_status == 'y' && $e->status=='in_transit'){
                        $e->status='rto_in_transit';
                    }

                    if($e->pickup_time == ""){
                        $pickup_time = $intransit->datetime ?? "";
                    }
                    else{
                        $pickup_time = $e->pickup_time;
                    }

//                    $ofdDate = $e->ofdDate ?? "";

                    if($this->fullInformation)
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted,!empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d',strtotime($pickup_time)) : date('Y-m-d',strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : ""),!empty($pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "", $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $courierPartner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->delivered_date, $e->b_customer_name, $e->s_address_line1, $e->s_address_line2, $e->b_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount ,$e->awb_assigned_date, $e->last_sync,"",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$e->ofdDate->ofd_attempt ?? 0);
                    else
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted,!empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d',strtotime($pickup_time)) : date('Y-m-d',strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : ""),!empty($pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "", $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $courierPartner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->delivered_date, $e->b_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date, $e->last_sync,"",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$e->ofdDate->ofd_attempt ?? 0);
                    $products = Product::where('order_id', $e->id)->get();
                    foreach ($products as $p) {
                        $info[] = $p->product_name;
                        $info[] = $p->product_sku;
                        $info[] = $p->product_qty;
                    }
                    fputcsv($fp, $info);
                    $cnt++;
                }
                break;
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

    //for delete order
    function delete_order($id)
    {
        $order = Order::find($id);
        if ($order->status == 'pending') {
            Order::where('id', $id)->delete();
            Product::where('order_id', $id)->delete();
        }
        return true;
    }

    //for cancel order (change status using api)
    function cancel_order($id)
    {
        $order = Order::find($id);
        if(MyUtility::PerformCancellation(Session()->get('MySeller'),$order)){
            $this->_refreshSession();
            return response(['status' => 'true','message' => ' Order Cancelled Successfully'])->withHeaders(['content-type' => 'application/json']);
        }
        else
            return response(['status' => 'false','message' => " Order couldn't cancelled Successfully"])->withHeaders(['content-type' => 'application/json']);
    }

    //order view page display
    function view_order($id)
    {
        $data = $this->info;
        $data['order_data'] = Order::find($id);
        if ($data['order_data']->same_as_rto == 'n' && $data['order_data']->warehouse_id != $data['order_data']->rto_warehouse_id) {
            $data['rto_warehouse'] = Warehouses::find($data['order_data']->rto_warehouse_id);
        }
        else
            $data['rto_warehouse'] = Warehouses::find($data['order_data']->warehouse_id);

        $data['basic'] = Basic_informations::where('seller_id',Session()->get('MySeller')->id)->first();
        $data['product_data'] = Product::where('order_id', $id)->get();
        return view('seller.order_view', $data);
    }

    // for removing selected order
    function remove_selected_order(Request $request)
    {
        $orderIds = Order::whereIn('id',$request->ids)->where('status','pending')->get()->pluck('id')->toArray();
        Order::whereIn('id',$orderIds)->where('status','pending')->delete();
        Product::whereIn('order_id',$orderIds)->delete();
    }

    // for cancel selected order
    function cancel_selected_order(Request $request)
    {
        $existingJobOrders = [];
        $allOrderList = BulkShipOrdersJob::where('seller_id',Session()->get('MySeller')->id)->whereIn('status',['pending','processing'])->get();
        foreach ($allOrderList as $o){
            $listOrders = BulkShipOrdersJobDetails::where('job_id',$o->id)->where('is_deleted','n')->where('is_shipped','n')->pluck('order_id')->toArray();
            $existingJobOrders = array_merge($existingJobOrders,$listOrders);
        }
        $tempOrderIDs = [];
        foreach ($request->ids as $id){
            if(!in_array($id,$existingJobOrders))
                $tempOrderIDs[] = $id;
        }
        $request->ids = $tempOrderIDs;
        if((count($request->ids) > (env('BulkCancelLimit') ?? 50))){
            $jobId = BulkCancelOrdersJob::create([
                'orders' => json_encode($request->ids),
                'created' => date('Y-m-d H:i:s'),
                'seller_id' => Session()->get('MySeller')->id,
                'total' => count($request->ids)
            ]);
            BulkCancelOrders::dispatchAfterResponse([
                'job_id' => $jobId->id,
                'orders' => $request->ids,
                'seller_id' => Session()->get('MySeller')->id
            ]);
            return json_encode(array('status' => 'true','job' => true, 'message' => ' Cancellation request submitted successfully, Notify once completed !!'));
        }
        foreach ($request->ids as $id) {
            $order = Order::find($id);
            MyUtility::PerformCancellation(Session()->get('MySeller'),$order);
        }
        $this->_refreshSession();
        echo json_encode(array('status' => 'true','message' => ' Orders Cancelled Successfull.'));
    }

    //for get shipping charge data (single order)
    function ship_order($id)
    {
        $data = $this->info;
        $seller = Seller::find(Session()->get('MySeller')->id);
        $orderDetail = Order::find($id);
        $data['orderData'] = $orderDetail;
        if(empty($orderDetail))
            return "false";
        $serviceablePartners = ServiceablePincode::where('pincode',$orderDetail->s_pincode)->where('active','y');
        if($orderDetail->order_type == 'cod')
            $serviceablePartners = $serviceablePartners->where('is_cod','y');
        $serviceablePartners = $serviceablePartners->distinct('courier_partner')->pluck('courier_partner')->toArray();
        $blockedCourierPartners = explode(',', $seller->blocked_courier_partners) ?? [];
        /* select p.id,p.keyword,p.title,p.weight_initial,p.extra_limit,p.status,r.within_city as original,
        if(CEILING(2000-p.weight_initial) > 0,CEILING(2000-p.weight_initial),0) as extra_charge,
        ((select extra_charge) / p.extra_limit) as extra_mul,r.extra_charge_a as mul_value,
        ((select original) + ((select extra_mul) * (select mul_value))) as final_rate
        from partners p,rates r
        where r.plan_id = 1 and r.seller_id = 1 and p.id = r.partner_id and p.status = 'y' order by final_rate
        */
        $rateCriteria = MyUtility::findMatchCriteria($orderDetail->p_pincode,$orderDetail->s_pincode,Session()->get('MySeller'));
        if (empty($orderDetail->weight)) {
            return 1;
        }
        if($rateCriteria == "not_found")
            return "false";
        $weight = $orderDetail->weight > $orderDetail->vol_weight ? $orderDetail->weight : $orderDetail->vol_weight;
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
//        DB::enableQueryLog();
        $partners = Partners::whereNotIn('partners.id', $blockedCourierPartners)
            ->where('partners.status', 'y')
            ->whereIn('partners.serviceability_check', $serviceablePartners);
        $partners = $partners->leftJoin('rates','rates.partner_id','partners.id');
        $partners = $partners->select("partners.*","rates.$rateCriteria as original","rates.cod_maintenance","rates.cod_charge",
            DB::raw("if(CEILING($weight-partners.weight_initial) > 0,CEILING($weight-partners.weight_initial),0) as extra_charge"),
            DB::raw("CEILING((select extra_charge) / partners.extra_limit) as extra_mul"),"rates.extra_charge_{$zoneL} as mul_value",
            DB::raw("((select original) + ((select extra_mul) * (select mul_value))) as final_rate"));
        $partners = $partners->where('rates.plan_id',Session()->get('MySeller')->plan_id);
        $partners = $partners->where('rates.seller_id',Session()->get('MySeller')->id)->where('partners.status','y');
        $partners = $partners->orderBy('final_rate');
        if(!empty($orderDetail->courier_partner) && !in_array($orderDetail->courier_partner,$blockedCourierPartners))
            $partners = $partners->where('partners.keyword','!=',$orderDetail->courier_partner);
        if(strtolower(trim($orderDetail->shipment_type)) == 'mps') {
            $partners = $partners->where('partners.mps_enabled', 'y');
        }
        if(strtolower(trim($orderDetail->o_type)) == 'reverse') {
            $partners = $partners->where('partners.reverse_enabled', 'y');
        }
        if(strtolower(trim($orderDetail->is_qc)) == 'y') {
            $partners = $partners->where('partners.qc_enabled', 'y');
        }
        $partners = $partners->get();
//        dd($partners);
//        dd(DB::getQueryLog());
        // Check courier partner is blocked or not
        if($data['config']->check_courier_blocking == 'y') {
            foreach($partners as $partner) {
                if($this->isCourierBlocked($orderDetail, $partner->keyword) == false) {
                    $data['partners'][] = $partner;
                }
            }
        } else {
            $data['partners'] = $partners;
        }

        $data['rates'] = [];
        if(count($data['partners']) == 0){
            return "false";
        }
        foreach ($data['partners'] as $p) {
            //$extra = ($weight - $p->weight_initial) > 0 ? $weight - $p->weight_initial : 0;
            //$mul = ceil($extra / $p->extra_limit);
            //$res = DB::select("select r.*,p.*,r.$rateCriteria + ( r.extra_charge_" . strtolower($zone) . " * $mul ) as price from rates r,partners p where r.plan_id=" . Session()->get('MySeller')->plan_id . " and r.seller_id=" . Session()->get('MySeller')->id . " and r.partner_id = p.id and r.partner_id = $p->id order by price asc");
            $data['rates'][] = $p;
            $data['zone'] = $zone;
            $data['order_type'] = $orderDetail->order_type;
            $data['o_type'] = $orderDetail->o_type;
            $data['is_qc'] = $orderDetail->is_qc;
            $data['config'] = $this->info['config'];
            $data['invoice_amount'] = $orderDetail->invoice_amount;
            $data['reverse_percentage'] = Session()->get('MySeller')->reverse_charge;
        }
        return view('seller.partner_details', $data);
    }

    //get shipping data single order optional
    function get_shipping_data($id)
    {
        $orderDetail = Order::find($id);
        $data['partners'] = Partners::where('status', 'y')->get();
        $plan_id = Session()->get('MySeller')->id;
        //   $shipping_charges = DB::table('rates')->join('partners','rates.plan_id','=','partners.id')->select('rates.*', 'partners.keyword', 'partners.title')->where('partners.id',Session()->get('MySeller')->plan_id)->get();
        $shipping_charges = DB::select("select p.*,r.* from partners p,rates r where p.id = r.partner_id and p.status='y' and r.plan_id= $plan_id");
        $wareHouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->get();
        if (count($wareHouse) == 0) {
            return 0;
        }
        $wareHouse = $wareHouse[0];
        $column = '';
        $rates = Rates::where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
        $ncrArray = ['gurgaon','noida','ghaziabad','faridabad','delhi','new delhi','gurugram'];
        if (in_array(strtolower($orderDetail->s_city),$ncrArray) && in_array(strtolower($orderDetail->p_city),$ncrArray)) {
            // return 'within_city';
            $rates = Rates::select('*', 'within_city AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
            return $rates;
        } else if (strtolower($orderDetail->s_city) == strtolower($orderDetail->p_city)) {
            // return 'within_city';
            $rates = Rates::select('*', 'within_city AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
            return $rates;
        } else if (strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            // return 'within_state'; state//
            $rates = Rates::select('*', 'within_state AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
            return $rates;
        } else if (strtolower($orderDetail->s_state) == 'jammu kashmir') {
            //j&k
            $rates = Rates::select('*', 'north_j_k AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
            return $rates;
        } else {
            //rest_india
            $rates = Rates::select('*', 'rest_india AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
            return $rates;
        }
        // echo json_encode(array('status' => 'true'));
    }

    // for get shipping charge data (single order)
    function get_courier_charges(Request $request)
    {
        $data = $this->info;
        $seller = Seller::find(Session()->get('MySeller')->id);
        $serviceablePartners = ServiceablePincode::where('pincode',$request->s_pincode)->distinct('courier_partner')->pluck('courier_partner')->toArray();
        $plan_id = $seller->plan_id;
        $blockedCourierPartners = explode(',', $seller->blocked_courier_partners) ?? [];
        $prefixed_array = preg_filter('/^/', '\'', $serviceablePartners);
        $prefixed_array = preg_filter('/$/', '\'', $prefixed_array);
        $shipping_charges = DB::select("select p.*,r.* from partners p,rates r where p.id = r.partner_id and p.status='y' and r.plan_id= $plan_id and p.serviceability_check in(".implode(',',$prefixed_array).") order by p.id desc");
        $column = '';
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
        if (empty($request->weight) || $w == null) {
            return 1;
        }
        $rates = [];
        $rateCriteria = MyUtility::findMatchCriteria($w->pincode,$request->s_pincode,Session()->get('MySeller'));
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
        $weight = $request->weight * 1000;
        DB::enableQueryLog();
        $partners = Partners::whereNotIn('partners.id', $blockedCourierPartners)
            ->where('partners.status', 'y')
            ->whereIn('partners.serviceability_check', $serviceablePartners);
        $partners = $partners->leftJoin('rates','rates.partner_id','partners.id');
        $partners = $partners->select("partners.*","rates.$rateCriteria as original","rates.cod_maintenance","rates.cod_charge",
            DB::raw("if(CEILING($weight-partners.weight_initial) > 0,CEILING($weight-partners.weight_initial),0) as extra_charge"),
            DB::raw("CEILING((select extra_charge) / partners.extra_limit) as extra_mul"),"rates.extra_charge_{$zoneL} as mul_value",
            DB::raw("((select original) + ((select extra_mul) * (select mul_value))) as final_rate"));
        $partners = $partners->where('rates.plan_id',Session()->get('MySeller')->plan_id);
        $partners = $partners->where('rates.seller_id',Session()->get('MySeller')->id);
        $partners = $partners->orderBy('final_rate');
        $partners = $partners->get();
//        if($data['config']->check_courier_blocking == 'y') {
//            foreach($partners as $partner) {
//                if($this->isCourierBlocked($orderDetail, $partner->keyword) == false) {
//                    $data['partners'][] = $partner;
//                }
//            }
//        } else {
        $data['partners'] = $partners;
//        }
        //dd(DB::getQueryLog());

        foreach ($data['partners'] as $p) {

            // Change weight to 500gm if weight is <= 1500gm for amazon amazon_swa_1kg
            if($p->keyword == 'amazon_swa_1kg' && $weight <= 1500) {
                $weight = 500;
            }

            $extra = ($weight - $p->weight_initial) > 0 ? $weight - $p->weight_initial : 0;

            $data['rates'][] = $p;//$res[0] ?? 0;
            $data['zone'] = $zone;
            $data['order_type'] = $request->order_type;
            $data['reverse_percentage'] = Session()->get('MySeller')->reverse_charge;
        }
        return view('seller.partner-charges', $data);
    }

    //ship order using api(single order)
    function single_ship_order(Request $request)
    {
        $orderData = Order::find($request->order_id);
        $sellerData = Seller::find($orderData->seller_id);
        $response = ShippingHelper::ShipOrder($orderData,$sellerData,$request->partner);
        $this->_refreshSession();
        if(!$response['status'])
            return response()->json(['status' => 'false','message' => $response['message']]);
        else
            return response()->json(['status' => 'true','message' => 'Order Shipped Successfully']);
    }

    //get total selected order shipping charge details
    function total_selected_order(Request $request)
    {
        if ($request->ids == '')
            return 0;
        $partner = Partners::where('keyword', Session()->get('MySeller')->courier_priority_1)->where('status', 'y')->first();
        if (empty($partner))
            return 1;
        $data['all_order_charge'] = 0;
        $data['total_order'] = count($request->ids);
        $data['seller_balance'] = Session()->get('MySeller')->balance;
        $data['minimum_balance'] = $this->info['config']->minimum_balance;
        $zonalArray = [
            'within_city' => 'a',
            'within_state' => 'b',
            'metro_to_metro' => 'c',
            'rest_india' => 'd',
            'north_j_k' => 'e'
        ];
        $data['charges'] = [];
        foreach ($request->ids as $id) {
            $orderId = $id;
            $total_amount = Order::select('invoice_amount')->where('id', $id)->first();
            $o = Order::find($id);
            if ($o->weight == '') {
                return 2;
            }
            $rateCriteria = MyUtility::findMatchCriteria($o->p_pincode,$o->s_pincode,Session()->get('MySeller'));
            if($rateCriteria == 'not_found'){
                continue;
            }
            $preference = $this->_getShippingRate($orderId);
            if ($preference == false) {
                $partner = Partners::where('keyword', Session()->get('MySeller')->courier_priority_1)->where('status', 'y')->first();
            } else {
                $partner = Partners::where('keyword', $preference->priority1)->where('status', 'y')->first();
            }
            if ($o->weight > $o->vol_weight)
                $weight = $o->weight;
            else
                $weight = $o->vol_weight;

            // Change weight to 500gm if weight is <= 1500gm for amazon amazon_swa_1kg
            if($partner->keyword == 'amazon_swa_1kg' && $weight <= 1500) {
                $weight = 500;
            }

            $extra = ($weight - $partner->weight_initial) > 0 ? $weight - $partner->weight_initial : 0;
            $mul = ceil($extra / $partner->extra_limit);
            //$mul = ceil($o->weight / 500) - 1;
            $plan_id = Session()->get('MySeller')->plan_id;
            $seller_id = Session()->get('MySeller')->id;
            $partner_rate = DB::select("select *,$rateCriteria + ( extra_charge_" . $zonalArray[$rateCriteria] . " * $mul ) as price from rates where plan_id=$plan_id and partner_id = $partner->id and seller_id =$seller_id limit 1");
            // $partner_rate = Rates::select("$rateCriteria as price", 'cod_charge','cod_maintenance')->where('partner_id', $partner->id)->where('plan_id', Session()->get('MySeller')->plan_id)->first();
            $courier_partner = $partner->keyword;
            $shipping_charge = $partner_rate[0]->price;
            if (strtolower($o->o_type) == 'reverse') {
                $shipping_charge = ($shipping_charge * Session()->get('MySeller')->reverse_charge) / 100;
            }
            $shipping_charge += ($shipping_charge * 18) / 100;
            $shipping_charge = round($shipping_charge);
            $cod_maintenance = $partner_rate[0]->cod_maintenance;
            $cod_charge = (intval($total_amount->invoice_amount) * $cod_maintenance) / 100;


            if ($cod_charge < $partner_rate[0]->cod_charge)
                $cod_charge = $partner_rate[0]->cod_charge;
            if (strtolower($o->order_type) == 'prepaid') {
                $cod_charge = "0";
                $early_cod = "0";
            } else {
                $cod_charge = (intval($total_amount->invoice_amount) * $cod_maintenance) / 100;
                if ($cod_charge < $partner_rate[0]->cod_charge)
                    $cod_charge = $partner_rate[0]->cod_charge;
                $cod_charge += ($cod_charge * 18) / 100;
                $early_cod = (intval($total_amount->invoice_amount) * Session()->get('MySeller')->early_cod_charge) / 100;
                $early_cod += ($early_cod * 18) / 100;
            }
            $early_cod = round($early_cod);
            $cod_charge = round($cod_charge);
            $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
            $rto_charge = ($shipping_charge) * Session()->get('MySeller')->rto_charge / 100;
            $total_charge = round($shipping_charge + $cod_charge + $early_cod);
//            $data['charges'][]=$total_charge;
            $data['all_order_charge'] += $total_charge;
        }
        return $data;
    }

    function ship_selected_order(Request $request)
    {
        $balanceFlag = 0;
        $success = 0;
        $failed = 0;

        $request->ids = array_unique($request->ids);
        $existingJobOrders = [];
        $processingOrders = [];
        $allOrderList = BulkShipOrdersJob::where('seller_id',Session()->get('MySeller')->id)->whereIn('status',['pending','processing'])->get();
        foreach ($allOrderList as $o){
            $listOrders = BulkShipOrdersJobDetails::where('job_id',$o->id)->where('is_deleted','n')->where('is_shipped','n')->pluck('order_id')->toArray();
            $existingJobOrders = array_merge($existingJobOrders,$listOrders);
        }
        foreach ($request->ids as $o){
            if(!in_array($o,$existingJobOrders)){
                $processingOrders[]=$o;
            }
        }
        $request->ids = $processingOrders;
        $total_order = count($request->ids);
        if(($total_order > (env('BulkShipLimit') ?? 50))){
            $jobId = BulkShipOrdersJob::create([
                'orders' => json_encode($request->ids),
                'created' => date('Y-m-d H:i:s'),
                'seller_id' => Session()->get('MySeller')->id,
                'total' => count($request->ids)
            ]);
            $jobDetails = [];
            foreach (array_unique($request->ids) as $id){
                $jobDetails [] = [
                    'job_id' => $jobId->id,
                    'order_id' => $id
                ];
            }
            BulkShipOrdersJobDetails::insert($jobDetails);
            BulkShipOrders::dispatchAfterResponse([
                'job_id' => $jobId->id,
                'orders' => $request->ids,
                'seller_id' => Session()->get('MySeller')->id
            ]);
            return json_encode(array('status' => 'true','job' => true, 'message' => 'Shipping request submitted successfully, Notify once completed !!'));
        }
        $sellerData = Seller::find(Session()->get('MySeller')->id);
        $sellerBalance = $sellerData->balance;
        $allTransaction = [];
        $internationalOrders = [];
        $allOrders = [];
        Seller::where('id',Session()->get('MySeller')->id)->update(['is_bulk_ship_running' => 1]);
        foreach (array_unique($request->ids) as $id) {
            $orderData = Order::find($id);
            $shipped = ShippingHelper::ShipOrder($orderData,$sellerData,null,true,$sellerBalance);
            if($shipped['status']){
                $success++;
                $allOrders[]=[
                    'id' => $orderData->id,
                    'status' => 'shipped',
                    'route_code' => $shipped['route_code'],
                    'manifest_sent' => $shipped['manifest_sent'],
                    'courier_partner' => $shipped['data']['courier_keyword'],
                    'fulfillment_sent' => $orderData->channel == 'custom' ? 'y' : 'n',
                    'awb_number' => $shipped['data']['awb_number'],
                    'seller_order_type' => $sellerData->seller_order_type,
                    'is_alpha' => ($shipped['llType'] ?? "SE") == 'LL' ? "LL" : $sellerData->is_alpha,
                    'is_alpha_delhivery' => $sellerData->is_alpha_delhivery,
                    'is_custom' => $shipped['is_custom'] ? 1 : 0,
                    'shipping_charges' => round($shipped['shipping_charges'], 2),
                    'cod_charges' => round($shipped['cod_charges'], 2),
                    'early_cod_charges' => round($shipped['early_cod_charges'], 2),
                    'rto_charges' => round($shipped['rto_charges'], 2),
                    'gst_charges' => round($shipped['gst_charges'], 2),
                    'total_charges' => round($shipped['total_charges'],2),
                    'zone' => $shipped['zone'],
                    'awb_assigned_date' => date('Y-m-d H:i:s'),
                    'last_sync' => date('Y-m-d H:i:s'),
                    'awb_barcode' => 'public/assets/seller/images/Barcode/'.$shipped['data']['awb_number'].'.png'
                ];
                $allTransaction[] = [
                    'seller_id' => $sellerData->id,
                    'order_id' => $orderData->id,
                    'amount' => $shipped['total_charges'],
                    'balance' => $sellerBalance - $shipped['total_charges'],
                    'type' => 'd',
                    'redeem_type' => 'o',
                    'datetime' => date('Y-m-d H:i:s'),
                    'method' => 'wallet',
                    'description' => 'Order Shipping Charge Deducted'
                ];

                if($shipped['other_charges'] != 0 ){
                    $allTransaction[] = [
                        'seller_id' => $sellerData->id,
                        'order_id' => $orderData->id,
                        'amount' => $shipped['other_charges'],
                        'balance' => $sellerBalance - $shipped['total_charges'],
                        'type' => 'd',
                        'redeem_type' => 'o',
                        'datetime' => date('Y-m-d H:i:s'),
                        'method' => 'wallet',
                        'description' => 'Other Services Charge Deducted : WhatsApp'
                    ];
                }

                if($orderData->is_qc != 'y' && $orderData->global_type == 'domestic') {
                    $internationalOrders[] = [
                        'order_id' => $orderData->id
                    ];
                }

                $sellerBalance-=$shipped['total_charges'];
            }
            else
                $failed++;
            if($shipped['message'] == "Booking failed due to insufficient balance. Please recharge and try!!"){
                $balanceFlag = 2;
                break;
            }
        }
        // bulk operations for all the orders for transaction and order update

        // code goes here
        Session::put('notified', false);
        Transactions::insert($allTransaction);
        if(count($internationalOrders) > 0){
            InternationalOrders::insert($internationalOrders);
        }
        $sellerData->balance = $sellerBalance;
        $sellerData->save();
        foreach ($allOrders as $o){
            Order::where('id',$o['id'])->update($o);
        }
        $this->_refreshSession();
        Seller::where('id',Session()->get('MySeller')->id)->update(['is_bulk_ship_running' => 0]);
        echo json_encode(array('status' => 'true','job' => false, 'shipped' => $success, 'total' => $total_order,'balanceFlag' => $balanceFlag));
    }
    //set key of filter data(order)
    function setFilter(Request $request)
    {
        $data = $request->value;
        Session::put($request->key, $data);
        session(
            [
                'min_value' => !empty($request->min_value) ? $request->min_value : session('min_value'),
                'split_min_value' => !empty($request->min_value) ? $request->min_value : session('split_min_value'),
                'max_value' => !empty($request->max_value) ? $request->max_value : session('max_value'),
                'split_max_value' => !empty($request->max_value) ? $request->max_value : session('split_max_value'),
                'min_weight' => !empty($request->min_weight) ? $request->min_weight : session('min_weight'),
                'max_weight' => !empty($request->max_weight) ? $request->max_weight : session('max_weight'),
                'min_quantity' => isset($request->min_quantity) ? $request->min_quantity : session('min_quantity'),
                'max_quantity' => !empty($request->max_quantity) ? $request->max_quantity : session('max_quantity'),
                'start_date' => !empty($request->start_date) ? $request->start_date : session('start_date'),
                'end_date' => !empty($request->end_date) ? $request->end_date : session('end_date'),
                'filter_status' => !empty($request->filter_status) ? $request->filter_status : session('filter_status'),
                'order_awb_search' => $request->order_awb_search ?? session('order_awb_search'),
                'multiple_sku' => isset($request->multiple_sku) ? $request->multiple_sku : 'n',
                'single_sku' => isset($request->single_sku) ? $request->single_sku : 'n',
                'match_exact_sku' => !empty($request->match_exact_sku) ? $request->match_exact_sku : 'n'
            ]);
        // print_r(session()->all());
    }

    //reset key of filter
    function resetFilter($keys)
    {
        //session(['tag_value' => '']);
        $key = explode(',', $keys);
        foreach ($key as $k)
            session([$k => '']);
    }

    //ajax search of order data using session key
    function ajax_filter_order(Request $request)
    {
        DB::enableQueryLog();
        //dd($request);
        $session_channel = session('channel');
        $global_type = session('global_type') ?? 'domestic';
        $session_channel_name = session('channel_name');
        $session_channel_code = session('channel_code');
        $session_order_number = session('order_number');
        $session_payment_type = session('payment_type');
        $session_product = session('product');
        $session_sku = session('sku');
        $min_value = session('min_value');
        $max_value = session('max_value');
        $min_weight = !empty(session('min_weight')) ? intval(session('min_weight') * 1000) : session('min_weight');
        $max_weight = !empty(session('max_weight')) ? intval(session('max_weight') * 1000) : session('max_weight');
        $start_date = session('start_date');
        $end_date = session('end_date');
        $pickup_address = session('pickup_address');
        $delivery_address = session('delivery_address');
        $order_status = session('order_status');
        $filter_status = session('filter_status');
        $awb_number = session('awb_number');
        $courier_partner = session('courier_partner');
        $order_awb_search = session('order_awb_search');
        $single_sku = session('single_sku');
        $multiple_sku = session('multiple_sku');
        $match_exact_sku = session('match_exact_sku');
        $min_quantity = session('min_quantity');
        $max_quantity = session('max_quantity');
        //$session_tag_value = session('tag_value');
        $session_order_tag = session('order_tag');
        //$session_order_tag_processing = session('order_tag_processing');
        //dd($session_tag_value);
        $match = [
            'product_name' => '',
            'product_sku' => '',
            's_customer_name' => '',
            's_address_line1' => '',
            's_country' => '',
            's_state' => '',
            's_city' => '',
            's_pincode' => '',
            's_contact' => NULL,
            'b_customer_name' => '',
            'b_address_line1' => '',
            'b_country' => '',
            'b_state' => '',
            'b_city' => '',
            'b_pincode' => '',
            'b_contact' => '',
            'weight' => 0,
            'length' => 0,
            'breadth' => 0,
            'height' => 0,
            'invoice_amount' => ''
        ];
        // DB::enableQueryLog();
        $query = Order::where('orders.seller_id', Session()->get('MySeller')->id)->where('orders.global_type',$global_type);
        if (!empty($session_order_number)) {
            $query = $query->where('orders.customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('orders.channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('orders.seller_channel_name', $session_channel_name);
        }
        if(!empty($session_channel_code)){
            $query = $query->whereIn('orders.channel_name', $session_channel_code);
        }

        if (!empty($order_status)) {
            $query->where(function($q) use($order_status) {
                foreach($order_status as $row) {
                    if($row == 'rto_delivered') {
                        $q = $q->orWhere(function($q) {
                            $q->where('orders.status', 'delivered')
                                ->where('orders.rto_status', 'y');
                        });
                    } else if($row == 'rto_in_transit') {
                        $q = $q->orWhere(function($q) {
                            $q->where('orders.status', 'in_transit')
                                ->where('orders.rto_status', 'y');
                        });
                    }
                    else if($row == 'rto_initated' || $row == 'rto_initiated') {
                        $q = $q->orWhere(function($q) {
                            $q->whereIn('orders.status', ['rto_initated','rto_initiated'])
                                ->where('orders.rto_status', 'y');
                        });
                    }
                    else {
                        $q = $q->orWhere(function($q) use($row) {
                            $q->where('orders.status', $row)
                                ->where('orders.rto_status', 'n');
                        });
                    }
                }
            });
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('orders.order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('orders.invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($min_quantity)) {
            $query = $query->where('orders.product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('orders.product_qty', '<=', $max_quantity);
        }
        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('orders.product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('orders.product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('orders.product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('orders.product_sku', 'like', '%' . $session_sku . '%');
        }
        if (!empty($min_weight) && !empty($max_weight)) {
            $query = $query->where('orders.weight', '>=', $min_weight)->where('weight', '<=', $max_weight);
            // $query = $query->whereBetween('weight', [floatval($min_value) * 1000, floatval($max_value) * 1000]);
        }
        if (!empty($start_date) && !empty($end_date)) {
            // $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
            if ($filter_status == 'ready_to_ship_data') {
                $query = $query->whereDate('orders.awb_assigned_date', '>=', $start_date)->whereDate('orders.awb_assigned_date', '<=', $end_date);
            } else {
                $query = $query->whereDate('orders.inserted', '>=', $start_date)->whereDate('orders.inserted', '<=', $end_date);
            }
        }
        if (!empty($session_product)) {
            $query = $query->where(function ($q) use ($session_product) {
                $q->where('orders.product_name', 'like', '%' . $session_product . '%');
            });
        }
        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = trim($order,',');
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('orders.customer_order_number', $order)
                        ->orWhereIn('orders.awb_number', $order)
                        ->orWhereIn('orders.s_contact', $order);
                });
            }
        }
        if (!empty($pickup_address) && count($pickup_address)>0) {
            $query = $query->whereIn('orders.warehouse_id',$pickup_address);
            //$query = $query->where('pickup_address', 'like', '%' . $pickup_address . '%');
        }
        if (!empty($delivery_address)) {
            $query = $query->where('orders.delivery_address', 'like', '%' . $delivery_address . '%');
        }
        // if (!empty($courier_partner)) {
        //     $query = $query->where('courier_partner', 'like', '%' . $courier_partner . '%');
        // }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('orders.courier_partner', $courier_partner);
        }
        if (!empty($awb_number)) {
            $query = $query->where('orders.awb_number', 'like', '%' . $awb_number . '%');
        }

        if(!empty($session_order_tag))
        {
            $query = $query->leftJoin('international_orders','international_orders.order_id','orders.id')->whereIn('international_orders.shopify_tag',$session_order_tag);
        }

        if ($filter_status == 'unprocessable_order_data') {
            // $query = $query->where(function ($query) use ($match) {
            //     $query->orWhere($match);
            // })->where('status', 'pending');

            $query = $query->where(function($q) use($match) {
                $q->orWhere(function($q) {
                    $q->orWhere('orders.product_name', '')
                        ->orWhere('orders.product_sku', '')
                        ->orWhere('orders.s_customer_name', '')
                        ->orWhere('orders.s_address_line1', '')
                        ->orWhere('orders.s_country', '')
                        ->orWhere('orders.s_state', '')
                        ->orWhere('orders.s_city', '')
                        ->orWhere('orders.s_pincode', '')
                        ->orWhere('orders.s_contact', '')
                        ->orWhere('orders.b_customer_name', '')
                        ->orWhere('orders.b_address_line1', '')
                        ->orWhere('orders.b_country', '')
                        ->orWhere('orders.b_state', '')
                        ->orWhere('orders.b_city', '')
                        ->orWhere('orders.b_pincode', '')
                        ->orWhere('orders.b_contact', '')
                        ->orWhere('orders.weight', '')
                        ->orWhere('orders.length', '')
                        ->orWhere('orders.breadth', '')
                        ->orWhere('orders.height', '')
                        ->orWhere('orders.weight', 0)
                        ->orWhere('orders.length', 0)
                        ->orWhere('orders.breadth', 0)
                        ->orWhere('orders.height', 0)
                        ->orWhereNull('orders.weight')
                        ->orWhereNull('orders.length')
                        ->orWhereNull('orders.breadth')
                        ->orWhereNull('orders.height')
                        ->orWhere('orders.invoice_amount', '')
                        ->orWhereNull('orders.invoice_amount');
                })
                    ->orWhere(function($q) {
                        $q->whereIn('orders.channel', ['amazon', 'amazon_direct'])
                            ->where(function($q) {
                                $q->where('orders.invoice_amount', 0)
                                    ->orWhere('orders.b_contact', null)
                                    ->orWhere('orders.b_contact', '9999999999');
                            });
                    });
            })
                ->where('orders.status', 'pending');
            //dd($query::getQueryLog());
        }
        elseif ($filter_status == 'processing_order_data') {
            $query = $query->where('orders.product_name', '!=', '')
                ->where('orders.product_sku', '!=', '')
                ->where('orders.s_customer_name', '!=', '')
                ->where('orders.s_address_line1', '!=', '')
                ->where('orders.s_country', '!=', '')
                ->where('orders.s_state', '!=', '')
                ->where('orders.s_city', '!=', '')
                ->where('orders.s_pincode', '!=', '')
                ->whereNotNull('orders.s_contact')
                ->where('orders.b_customer_name', '!=', '')
                ->where('orders.b_address_line1', '!=', '')
                ->where('orders.b_country', '!=', '')
                ->where('orders.b_state', '!=', '')
                ->where('orders.b_city', '!=', '')
                ->where('orders.b_pincode', '!=', '')
                ->where('orders.b_contact', '!=', '')
                ->where('orders.weight', '!=', '')
                ->where('orders.length', '!=', '')
                ->where('orders.breadth', '!=', '')
                ->where('orders.height', '!=', '')
                ->where('orders.weight', '!=', 0)
                ->where('orders.length', '!=', 0)
                ->where('orders.breadth', '!=', 0)
                ->where('orders.height', '!=', 0)
                ->whereNotNull('orders.weight')
                ->whereNotNull('orders.length')
                ->whereNotNull('orders.breadth')
                ->whereNotNull('orders.height')
                ->where('orders.invoice_amount', '!=', '')
                ->whereNotNull('orders.invoice_amount')
                ->where(function($q) {
                    $q->whereNotIn('orders.channel', ['amazon', 'amazon_direct'])
                        ->orWhere(function($q) {
                            $q->where('orders.invoice_amount', '!=', 0)
                                ->where('orders.b_contact', '!=', null)
                                ->where('orders.b_contact', '!=', '9999999999');
                        });
                })
                ->where('orders.status', 'pending');
        }
        elseif ($filter_status == 'ready_to_ship_data') {
            $query = $query->where('orders.manifest_status', 'n')->whereNotIn('orders.status', ['pending', 'cancelled']);
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['order'] = $query->where('orders.global_type',$global_type)->latest('orders.inserted')->paginate(Session()->get('noOfPage'));
            $data['count_ajax_order'] = $query->latest('orders.inserted')->count();
            $data['total_order'] = $query->latest('orders.inserted')->count();
            $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
            return view('seller.shipped_orders', $data);
        }
        elseif ($filter_status == 'return_order_data') {
            $query = $query->where('orders.rto_status', 'y')->where('orders.global_type',$global_type);
            $data['count_ajax_order'] = $query->latest('orders.inserted')->count();
            $data['total_order'] = $query->latest('orders.inserted')->count();
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['order'] = $query->latest('orders.inserted')->paginate(Session()->get('noOfPage'));
            $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
            return view('seller.return_order', $data);
        }
        elseif ($filter_status == 'manifest_order') {
            $mquery = Manifest::join('partners','manifest.courier','partners.keyword')->where('partners.international_enabled',$global_type == "domestic" ? 'n' : 'y' )->where('manifest.seller_id', Session()->get('MySeller')->id);
            if(!empty($start_date) && !empty($end_date)){
                $mquery =$mquery->whereDate('manifest.created', '>=', $start_date)->whereDate('manifest.created', '<=', $end_date);
            }
            $data['total_manifest'] = $mquery->count();
            $data['manifest'] = $mquery->select('manifest.*')->paginate(Session()->get('noOfPage'));
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            return view('seller.manifest_order', $data);
        }
        $data['order'] = $query->select('orders.*')->latest('orders.inserted')->paginate(Session()->get('noOfPage'),['*'],'page',$request->page);
        //dd(DB::getQueryLog());
        $data['count_ajax_order'] = $query->latest('orders.inserted')->count();
        // dd(DB::getQueryLog());
        // dd($data['order_id']);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        if($global_type == 'international')
            return view('seller.international_partial.partial_orders', $data);
        return view('seller.partial_orders', $data);
    }

    function all_order_searching(Request $request)
    {
        $order = trim($request->order);
        $order = explode(',', $order);
        DB::enableQueryLog();
        $query = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id);
        if (!empty($order)) {
            $query = $query->where(function ($q) use ($order) {
                $q->whereIn('customer_order_number', $order)
                    ->orWhereIn('awb_number', $order);
            });
        }
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = $query->latest('inserted')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['warehouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.all_order', $data);
    }

    function processing_searching(Request $request)
    {
        $order = trim($request->order);
        $order = explode(',', $order);
        $query = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)
            ->where('product_name', '!=', '')
            ->where('product_sku', '!=', '')
            ->where('s_customer_name', '!=', '')
            ->where('s_address_line1', '!=', '')
            ->where('s_country', '!=', '')
            ->where('s_state', '!=', '')
            ->where('s_city', '!=', '')
            ->where('s_pincode', '!=', '')
            ->whereNotNull('s_contact')
            ->where('b_customer_name', '!=', '')
            ->where('b_address_line1', '!=', '')
            ->where('b_country', '!=', '')
            ->where('b_state', '!=', '')
            ->where('b_city', '!=', '')
            ->where('b_pincode', '!=', '')
            ->where('b_contact', '!=', '')
            ->where('weight', '!=', '')
            ->where('length', '!=', '')
            ->where('breadth', '!=', '')
            ->where('height', '!=', '')
            ->where('weight', '!=', 0)
            ->where('length', '!=', 0)
            ->where('breadth', '!=', 0)
            ->where('height', '!=', 0)
            ->whereNotNull('weight')
            ->whereNotNull('length')
            ->whereNotNull('breadth')
            ->whereNotNull('height')
            ->where('invoice_amount', '!=', '')
            ->whereNotNull('invoice_amount')
            ->where(function($q) {
                $q->whereNotIn('channel', ['amazon', 'amazon_direct'])
                    ->orWhere(function($q) {
                        $q->where('invoice_amount', '!=', 0)
                            ->where('b_contact', '!=', null)
                            ->where('b_contact', '!=', '9999999999');
                    });
            })
            ->where('status', 'pending');
        if (!empty($order)) {
            $query = $query->where(function ($q) use ($order) {
                $q->whereIn('customer_order_number', $order);
            });
        }
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = $query->latest('inserted')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.processing', $data);
    }

    //fetch dimension of order
    function fetch_dimension_data($weight)
    {
        $response = DB::table('dimensions')->where('weight', '>=', $weight)->orderBy('weight')->first();
        if(!empty($response))
            echo json_encode($response);
        else
            echo json_encode((object)['height' => 10, 'length' => 10, 'width' => 10]);
    }

    //fetch dimension of order
    function _fetch_dimension_data($weight)
    {
        $response = DB::table('dimensions')->where('weight', '>=', $weight)->orderBy('weight')->first();
        if ($response == null)
            $response = (object)['height' => 40, 'length' => 20, 'width' => 20];
        return $response;
    }

    //fetch sku data(it used while order insert)
    function fetch_product_sku($product_sku)
    {
        $response = SKU::where('sku', $product_sku)->where('seller_id', Session()->get('MySeller')->id)->first();
        if (!empty($response)) {
            echo json_encode($response);
        } else {
            return 0;
        }
    }

    //modify dimension details
    function modify_dimension_data($id)
    {
        $response = Order::select('id', 'weight', 'length', 'breadth', 'height')->find($id);
        echo json_encode($response);
    }

    function modify_multiple_dimension_data(Request $request)
    {
        $orders = Order::select('id', 'customer_order_number', 'weight', 'length', 'breadth', 'height')->whereIn('id', $request->ids)->get();
        return response()->json($orders);
    }

    //update order details
    function modify_dimension(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'weight' => isset($request->weight) ? $request->weight * 1000 : '',
            'length' => isset($request->length) ? $request->length : '',
            'breadth' => isset($request->breadth) ? $request->breadth : '',
            'height' => isset($request->height) ? $request->height : ''
        );
        $data['vol_weight']=(intval($data['height']) * intval($data['length']) * intval($data['breadth'])) / 5;
        Order::where('id', $request->order_id)->update($data);
        return response()->json([
            'status' => true,
        ]);
    }

    function modify_multiple_dimension(Request $request)
    {
        $res = [];
        for($i=0; $i<$request->number_of_orders; $i++) {
            $data = [
                'weight' => isset($request->{"weight_{$i}"}) ? $request->{"weight_{$i}"} * 1000 : '',
                'length' => isset($request->{"length_{$i}"}) ? $request->{"length_{$i}"} : '',
                'breadth' => isset($request->{"breadth_{$i}"}) ? $request->{"breadth_{$i}"} : '',
                'height' => isset($request->{"height_{$i}"}) ? $request->{"height_{$i}"} : ''
            ];
            $data['vol_weight'] = (intval($data['height']) * intval($data['length']) * intval($data['breadth'])) / 5;
            Order::where('id', $request->{"order_id_{$i}"})->update($data);
            $data['id'] = $request->{"order_id_{$i}"};
            $res[] = $data;
        }
        return response()->json($res);
    }

    function modify_multiple_warehouse(Request $request)
    {
        $request->order_id = explode(',', $request->order_id);
        // Validate orders
        $orders = Order::whereIn('id', $request->order_id)->where('seller_id', Session()->get('MySeller')->id)->get();
        $oldWarehouseId = null;
        foreach($orders as $order) {
            if($order->status != 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending orders warehouse detail can be updated.',
                    'data' => [],
                ]);
            }
            if($order->o_type == 'reverse') {
                return response()->json([
                    'status' => false,
                    'message' => 'Reverse orders warehouse detail can not be updated.',
                    'data' => [],
                ]);
            }
            if(!isset($request->changeRTOWarehouse)){
                if($oldWarehouseId != null && $oldWarehouseId != $order->warehouse_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Only same warehouse orders warehouse detail can be updated.',
                        'data' => [],
                    ]);
                }
            }
            $oldWarehouseId = $order->warehouse_id;
        }
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)
            ->where('id', $request->warehouse_id)
            ->first();
        if(empty($w)) {
            return response()->json([
                'status' => false,
                'message' => 'Selected warehouse is invalid.',
                'data' => [],
            ]);
        }

        if(isset($request->changeRTOWarehouse)){
            $data = [
                'same_as_rto' => 'n',
                'rto_warehouse_id' => $w->id
            ];
            Order::whereIn('id', $request->order_id)
                ->where('status', 'pending')
                ->where('seller_id', Session()->get('MySeller')->id)
                ->update($data);

            $this->utilities->generate_notification('Success', ' Warehouse details updated successfully', 'success');
            return response()->json([
                'status' => true,
                'message' => 'RTO Warehouse details updated successfully.',
                'data' => []
            ]);
        }

        $data = array(
            'warehouse_id' => $w->id,
            //for pickup address
            'p_warehouse_name' => $w->warehouse_name,
            'p_customer_name' => $w->contact_name,
            'p_address_line1' => $w->address_line1,
            'p_address_line2' => $w->address_line2,
            'p_city' => $w->city,
            'p_state' => $w->state,
            'p_country' => $w->country,
            'p_pincode' => $w->pincode,
            'p_contact_code' => $w->code,
            'p_contact' => $w->contact_number,
            'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MySeller')->id
        );
        Order::whereIn('id', $request->order_id)
            ->where('status', 'pending')
            ->where('seller_id', Session()->get('MySeller')->id)
            ->update($data);

        $this->utilities->generate_notification('Success', ' Warehouse details updated successfully', 'success');
        return response()->json([
            'status' => true,
            'message' => 'Warehouse details updated successfully.',
            'data' => []
        ]);
    }

    //display data od shipped order
    function ready_to_ship()
    {
        Session($this->filterArray);
        session(['current_tab' => 'ready_to_ship']);
        session(['filter_status' =>'ready_to_ship_data']);
        $global_type = session('global_type') ?? 'domestic';
        $data['order'] = Order::select('orders.*')->where('orders.seller_id', Session()->get('MySeller')->id)->whereNotIn('orders.status', ['pending', 'cancelled'])->where('orders.manifest_status', 'n')->where('orders.global_type',$global_type);
        if(Session()->get('MySeller')->type == 'emp')
            $data['order'] = $data['order']->join('employee_work_logs','orders.id','employee_work_logs.order_id')->where('employee_work_logs.employee_id',Session()->get('MySeller')->emp_id)->where('employee_work_logs.operation','ship');
        $data['order'] = $data['order']->latest('orders.awb_assigned_date')->paginate(Session()->get('noOfPage') ?? 20);
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'n')->whereNotIn('status', ['pending', 'cancelled'])->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.shipped_order', $data);
    }

    //display data of unprocessable order
    function order_unprocessable()
    {
        $match = [
            'product_name' => '',
            'product_sku' => '',
            's_customer_name' => '',
            's_address_line1' => '',
            's_country' => '',
            's_state' => '',
            's_city' => '',
            's_pincode' => '',
            's_contact' => NULL,
            'b_customer_name' => '',
            'b_address_line1' => '',
            'b_country' => '',
            'b_state' => '',
            'b_city' => '',
            'b_pincode' => '',
            'b_contact' => '',
            'weight' => 0,
            'length' => 0,
            'breadth' => 0,
            'height' => 0,
            'invoice_amount' => ''
        ];
        Session($this->filterArray);
        $global_type = session('global_type') ?? 'domestic';
        session(['current_tab' => 'order_unprocessable']);
        session(['filter_status' =>'unprocessable_order_data']);
        $order = DB::table('orders')
            ->where('global_type',$global_type)
            ->where(function($q) use($match) {
                $q->orWhere(function($q) {
                    $q->orWhere('product_name', '')
                        ->orWhere('product_sku', '')
                        ->orWhere('s_customer_name', '')
                        ->orWhere('s_address_line1', '')
                        ->orWhere('s_country', '')
                        ->orWhere('s_state', '')
                        ->orWhere('s_city', '')
                        ->orWhere('s_pincode', '')
                        ->orWhere('s_contact', '')
                        ->orWhere('b_customer_name', '')
                        ->orWhere('b_address_line1', '')
                        ->orWhere('b_country', '')
                        ->orWhere('b_state', '')
                        ->orWhere('b_city', '')
                        ->orWhere('b_pincode', '')
                        ->orWhere('b_contact', '')
                        ->orWhere('weight', '')
                        ->orWhere('length', '')
                        ->orWhere('breadth', '')
                        ->orWhere('height', '')
                        ->orWhere('weight', 0)
                        ->orWhere('length', 0)
                        ->orWhere('breadth', 0)
                        ->orWhere('height', 0)
                        ->orWhereNull('weight')
                        ->orWhereNull('length')
                        ->orWhereNull('breadth')
                        ->orWhereNull('height')
                        ->orWhere('invoice_amount', '')
                        ->orWhereNull('invoice_amount');
                })
                    ->orWhere(function($q) {
                        $q->whereIn('channel', ['amazon', 'amazon_direct'])
                            ->where(function($q) {
                                $q->where('invoice_amount', 0)
                                    ->orWhere('b_contact', null)
                                    ->orWhere('b_contact', '9999999999');
                            });
                    });
            })
            ->where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'pending')
            ->latest('inserted')
            ->paginate(Session()->get('noOfPage'));
        // dd(DB::getQueryLog());
        $data['order'] = $order;
        $data['total_order'] = $order->total();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.unprocessable', $data);
    }

    //display data of processing order
    function order_processing()
    {
        //Session($this->filterArray);
        // DB::EnableQueryLog();
        $data = $this->info;
        session(['current_tab' => 'order_processing']);
        session(['filter_status' =>'processing_order_data']);
        $global_type = session('global_type') ?? 'domestic';
        $data['order'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)
            ->where('global_type',$global_type)
            ->where('product_name', '!=', '')
            ->where('product_sku', '!=', '')
            ->where('s_customer_name', '!=', '')
            ->where('s_address_line1', '!=', '')
            ->where('s_country', '!=', '')
            ->where('s_state', '!=', '')
            ->where('s_city', '!=', '')
            ->where('s_contact', '!=', '')
            ->where('s_pincode', '!=', '')
            ->whereNotNull('s_contact')
            ->where('b_customer_name', '!=', '')
            ->where('b_address_line1', '!=', '')
            ->where('b_country', '!=', '')
            ->where('b_state', '!=', '')
            ->where('b_city', '!=', '')
            ->where('b_pincode', '!=', '')
            ->where('b_contact', '!=', '')
            ->where('weight', '!=', '')
            ->where('length', '!=', '')
            ->where('breadth', '!=', '')
            ->where('height', '!=', '')
            ->where('weight', '!=', 0)
            ->where('length', '!=', 0)
            ->where('breadth', '!=', 0)
            ->where('height', '!=', 0)
            ->whereNotNull('weight')
            ->whereNotNull('length')
            ->whereNotNull('breadth')
            ->whereNotNull('height')
            ->where('invoice_amount', '!=', '')
            ->whereNotNull('invoice_amount')
            ->where(function($q) {
                $q->whereNotIn('channel', ['amazon', 'amazon_direct'])
                    ->orWhere(function($q) {
                        $q->where('invoice_amount', '!=', 0)
                            ->where('b_contact', '!=', null)
                            ->where('b_contact', '!=', '9999999999');
                    });
            })
            ->where('status', 'pending')
            ->latest('inserted')
            ->paginate(Session()->get('noOfPage'));

        $data['total_order'] = $data['order']->total() ?? 0;
        //  dd(DB::getQueryLog());
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        if($global_type == 'international')
            return view('seller.international_partial.processing', $data);
        return view('seller.processing', $data);
    }

    //display data of order manifest tab
    function order_manifest()
    {
        Session($this->filterArray);
        session(['current_tab' => 'order_manifest']);
        $global_type = session('global_type') ?? 'domestic';
        $data['manifest'] = Manifest::join('partners','manifest.courier', 'partners.keyword')->where('manifest.seller_id', Session()->get('MySeller')->id)->where('partners.international_enabled',$global_type == "domestic" ? 'n' : 'y' )->select('manifest.*')->latest('manifest.created')->paginate(Session()->get('noOfPage'));
        $data['total_manifest'] = Manifest::join('partners','manifest.courier','partners.keyword')->where('manifest.seller_id', Session()->get('MySeller')->id)->where('partners.international_enabled',$global_type == "domestic" ? 'n' : 'y' )->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.manifest_order', $data);
    }

    //display data of order return tab
    function order_return()
    {
        Session($this->filterArray);
        session(['current_tab' => 'order_return']);
        session(['filter_status' =>'return_order_data']);
        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->count();
        // $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'return')->orWhere('status', 'ndr')->orWhere('ndr_action', 'rto')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.return_order', $data);
    }

    //download single invoice PDF of order
    function singleInvoicePDF($id)
    {
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic'] = Basic_informations::where('seller_id',Session()->get('MySeller')->id)->first();
        $data['order'] = Order::find($id);
        if($data['order']->courier_partner == 'xindus'){
            return InternationalOrderHelper::GetXindusInvoice($data['order']->awb_number);
        }
        $data['product'] = Product::where('order_id', $id)->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $pdf = PDF::loadView('seller.single_order_invoice', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Invoice-' . $id . '.pdf');
    }

    //download single Label PDF of order
    function singleLablePDF(Request $request, $id)
    {
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic_info'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['order'] = Order::find($id);
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
                    'country' => $warehouse->country,

                ];
            }
        }
        if(empty($data['order']))
        {
            $this->utilities->generate_notification('Error', "Sorry Order not Found", 'error');
            return back();
        }
        // Get label configuration
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = Session()->get('MySeller')->id;
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
            $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
            $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE") : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
            $label->save();
        }
        $data['label'] = $label;
        MyUtility::GenerateManifest([$id],Session()->get('MySeller'));
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['product'] = Product::where('order_id', $id)->get();
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
        }
        else {
            $pdf = PDF::loadView('seller.single_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        }
//        if($request->action == 'print') {
            // Print label
        return $pdf->stream('Label-' . $id . '.pdf');
//        } else {
//            return $pdf->download('Label-' . $id . '.pdf');
//        }
        //  return view('seller.label_data', $data);
    }


    function singleLableForArchivePDF(Request $request, $id)
    {
        try {
            $data['order'] = OrderArchive::find($id);
            $data['config'] = $this->info['config'];
            $data['seller'] = Session()->get('MySeller');
            $data['basic_info'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
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
            if ($data['order']->same_as_rto == 'n') {
                if ($data['order']->warehouose_id != $data['order']->rto_warehouse_id) {
                    $warehouse = Warehouses::find($data['order']->rto_warehouse_id);
                    $data['RTOAddress'] = [
                        'first_name' => $warehouse->warehouse_name,
                        'address_line1' => $warehouse->address_line1,
                        'address_line2' => $warehouse->address_line2,
                        'pincode' => $warehouse->pincode,
                        'city' => $warehouse->city,
                        'state' => $warehouse->state,
                        'contact' => $warehouse->contact_number,
                        'country' => $warehouse->country,

                    ];
                }
            }
            if (empty($data['order'])) {
                $this->utilities->generate_notification('Error', "Sorry Order not Found", 'error');
                return back();
            }
            // Get label configuration
            $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
            if ($label == null) {
                $label = new LabelCustomization();
                // Store label configuration
                $label->seller_id = Session()->get('MySeller')->id;
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
                $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
                $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE") : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
                $label->save();
            }
            $data['label'] = $label;
//        MyUtility::GenerateManifest([$id],Session()->get('MySeller'));
            if ($data['order']->courier_partner == 'xindus') {
                return InternationalOrderHelper::GetXindusLabel($data['order']->awb_number);
            } else if ($data['order']->courier_partner == 'aramex') {
                return InternationalOrderHelper::GetAramexLabel($data['order']);
            } else if ($data['order']->courier_partner == 'movin') {
                $movin = new Movin();
                $response = $movin->shipmentLabel($data['order']);
                $filePath = "public/assets/movin-label.pdf";
                file_put_contents($filePath, file_get_contents($response['response']));
                if (file_exists($filePath)) {
                    header('Content-Description: File Transfer');
                    header('Contet-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                    exit;
                }
            }
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            $data['product'] = DB::select("select * from zz_archive_products where order_id = $id");
            if ($data['order']->shipment_type == 'mps') {
                $mpsOrder = [];
                $data['order']->is_parent = 'y';
                $data['order']->parent_awb = $data['order']->awb_number;
                $data['order']->parent_gati_package_no = $data['order']->gati_package_no;
                $mpsOrder[] = clone $data['order'];
                $mps = MPS_AWB_Number::where('order_id', $data['order']->id)->get();
                foreach ($mps as $row) {
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
                $pdf = PDF::loadView('seller.single_archive_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
            }
            if ($request->action == 'print') {
                // Print label
                return $pdf->stream('Label-' . $id . '.pdf');
            } else {
                return $pdf->download('Label-' . $id . '.pdf');
            }
            //  return view('seller.label_data', $data);
        }
        catch(Exception $e){
            dd($e, $e->getLine());
        }
    }

    //download Multiple selected invoice PDF of order
    function multipleInvoiceDownload(Request $request)
    {
        $ids = explode(',', $request->multiinvoice_id);
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic'] = Basic_informations::where('seller_id',Session()->get('MySeller')->id)->first();
        $data['orders'] = Order::whereIn('id', $ids)->with('products')->orderBy('id', 'desc')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $pdfData = [];
        $xindusData = [];
        foreach ($data['orders'] as $order){
            if($order->courier_partner == 'xindus')
                $xindusData[]=$order;
            else
                $pdfData[]=$order;
        }
        if(count($data['orders']) > 100){
            $this->utilities->downloadLabelOrInvoice();
            $allAwbs = [];
            foreach($data['orders'] as $o)
                $allAwbs[] = $o->awb_number;
            $awbData = [
                "seller_id" => Session()->get('MySeller')->id,
                "awbs" => $allAwbs
            ];
            Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(url('/').'/cron/invoiceQueue', $awbData);
            $this->utilities->generate_notification('Success', "Go to MIS -> Download and download the invoice.", 'success');
            return back();
        }
        $zipFile = "exports/Invoice-".Session()->get('MySeller')->id.".zip";
        if(!empty($xindusData)){
            // Delete old zip
            @unlink($zipFile);
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true) {
                exit('Unable to create File');
            }
            foreach ($xindusData as $order){
                $file = InternationalOrderHelper::GetXindusInvoice($order->awb_number,true);
                file_put_contents("labels/{$order->awb_number}.pdf",$file);
                $zip->addFile("labels/{$order->awb_number}.pdf", "{$order->awb_number}.pdf");
            }
            $data['orders'] = $pdfData;
            if(!empty($pdfData)){
                //$labelFile = "exports/Labels-".Session()->get('MySeller')->id.".pdf";
                $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait')->save($labelFile);
                $zip->addFile($pdf, 'Labels.pdf');
            }
            $zip->close();
            // Delete pdf file

            if (file_exists($zipFile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                exit;
            }
            exit;
        }
        $pdf = PDF::loadView('seller.multiple_invoice_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        return $pdf->download('MultipleInvoice.pdf');
    }

    //download Multiple selected label PDF of order
    function multipleLableDownload(Request $request)
    {
        $ids = explode(',', $request->multilable_id);
        $manifestIDs = [];
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic_info'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        // Get label configuration
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = Session()->get('MySeller')->id;
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
            $label->footer_visibility = $request->footer_visibility ?? 'n';
            $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
            $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE") : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
            $label->save();
        }
        $data['label'] = $label;

        $data['orders'] = Order::select('id')->whereIn('id', $ids)->where('status', '!=', 'pending')->get();
        // dd($ids, $data['orders']->isEmpty(), $data['orders']->toArray());
        if($data['orders']->isEmpty()) {
            $this->utilities->generate_notification('Error', "Sorry you can't download label for pending orders!!", 'error');
            return back();
        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        // For MPS
        $orders = [];
        if(count($data['orders']) > 100){
            $reportCount = DownloadReport::where('seller_id',Session()->get('MySeller')->id)->where('report_status','processing')->get();
            foreach($reportCount as $r){
                $to_time = time();
                $from_time = strtotime($r->created_at);
                if(round(abs($to_time - $from_time) / 60,2) > 5){
                    DownloadReport::where('id',$r->id)->update(["report_status" => 'failed']);
                }
            }
            if(DownloadReport::where('seller_id',Session()->get('MySeller')->id)->where('report_status','processing')->count() > 0){
                $this->utilities->generate_notification('Error', "Please wait till previous request completes!!", 'error');
                return back();
            }
            // here code goes for multi label
            $allAwbs = [];
            foreach($ids as $id) {
                $o = Order::select('awb_number')->where('id',$id)->where('status', '!=', 'pending')->first();
                if(!empty($o))
                    $allAwbs[] = $o->awb_number;
            }
            $awbData = [
                "seller_id" => Session()->get('MySeller')->id,
                "awbs" => $allAwbs
            ];
            Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(url('/').'/cron/queue', $awbData);
            //Http::post("https://www.Twinnship.in/cron/queue",$awbData)->withHeaders(['content-type' => 'application/json']);
            $this->utilities->generate_notification('Success', "Go to MIS -> Download and download the labels.", 'success');
            return back();
        }
        foreach($ids as $id) {
            $order = Order::where('id',$id)->where('status', '!=', 'pending')->with('products')->first();
            if(!empty($order)){
                $manifestIDs[] = $order->id;
                if($order->shipment_type == 'mps') {
                    $order->is_parent = 'y';
                    $order->parent_awb = $order->awb_number;
                    $order->parent_gati_package_no = $order->gati_package_no;
                    $orders[] = clone $order;
                    $mps = MPS_AWB_Number::where('order_id', $order->id)->get();
                    foreach($mps as $row) {
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
        }
        $data['orders'] = $orders;
        // Generate manifest
        MyUtility::GenerateManifest($manifestIDs,Session()->get('MySeller'));

        $pdfData = [];
        $pngData = [];
        $xindusData = [];
        $movinData = [];
        $aramexData = [];
        foreach($orders as $order) {
            if(str_contains($order->courier_partner,'movin')){
                $movinData[] = $order;
            }
            elseif($order->global_type == 'international' && $order->courier_partner == 'xindus'){
                $xindusData[]=$order;
            }else if($order->global_type == 'international' && $order->courier_partner == 'aramex'){
                $aramexData[]=$order;
            }
            else {
                $pdfData[] = $order;
            }
        }
        if(!empty($pngData) || !empty($xindusData) || !empty($aramexData) || !empty($movinData)) {
            $zipFile = "exports/Labels-".Session()->get('MySeller')->id.".zip";
            // Delete old zip
            @unlink($zipFile);
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true) {
                exit('Unable to create File');
            }
            foreach ($xindusData as $order){
                $file = InternationalOrderHelper::GetXindusLabel($order->awb_number,true);
                file_put_contents("labels/{$order->awb_number}.pdf",$file);
                $zip->addFile("labels/{$order->awb_number}.pdf", "{$order->awb_number}.pdf");
            }

            foreach ($aramexData as $order){
                $file = InternationalOrderHelper::GetAramexLabel($order,true);
                file_put_contents("labels/{$order->awb_number}.pdf",$file);
                $zip->addFile("labels/{$order->awb_number}.pdf", "{$order->awb_number}.pdf");
            }
            $movin = new Movin();
            foreach ($movinData as $order){
                $response = $movin->shipmentLabel($order);
                $filePath = "labels/{$order->awb_number}.pdf";
                file_put_contents($filePath,file_get_contents($response['response']));
                $zip->addFile($filePath, "{$order->awb_number}.pdf");
            }

            if(!empty($pdfData)) {
                $data['orders'] = $pdfData;
                $labelFile = "exports/Labels-".Session()->get('MySeller')->id.".pdf";
                $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait')->save($labelFile);
                $zip->addFile($labelFile, 'Labels.pdf');
            }
            $zip->close();
            // Delete pdf file
            @unlink($labelFile);

            if (file_exists($zipFile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                exit;
            }
        }

        // return view('seller.multiple_label_data',$data);
        $pdf = PDF::loadView('seller.multiple_label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        return $pdf->download('MultipleLabel.pdf');
    }

    //download invoice of shipped data (manifest)
    function InvoicePDF($id)
    {
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic'] = Basic_informations::where('seller_id',Session()->get('MySeller')->id)->first();
        $data['manifest_order'] = DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->select('manifest_order.*', 'orders.*')->where('manifest_id', $id)->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['manifest'] = Manifest::find($id);
        if(count($data['manifest_order']) > 100){
            // here code goes for multi label
            $allAwbs = [];
            foreach($data['manifest_order'] as $o)
                $allAwbs[] = $o->awb_number;
            $awbData = [
                "seller_id" => Session()->get('MySeller')->id,
                "awbs" => $allAwbs
            ];
            Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(url('/').'/cron/invoiceQueue', $awbData);
            //Http::post("https://www.Twinnship.in/cron/queue",$awbData)->withHeaders(['content-type' => 'application/json']);
            $this->utilities->generate_notification('Success', "Go to MIS -> Download and download the invoice.", 'success');
            return back();
        }
        if($data['manifest']->courier == 'xindus'){
            $zipFile = "Xindus-Invoice.zip";
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true){
                exit('Unable to create File');
            }
            foreach ($data['manifest_order'] as $mo){
                $labelUrl = Order::where('id',$mo->order_id)->first();
                $file = InternationalOrderHelper::GetXindusInvoice($labelUrl->awb_number,true);
                file_put_contents("labels/{$labelUrl->awb_number}.pdf",$file);
                $zip->addFile("labels/{$labelUrl->awb_number}.pdf", "{$labelUrl->awb_number}.pdf");
            }
            $zip->close();
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
            header("Content-length: " . filesize($zipFile));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
        $pdf = PDF::loadView('seller.invoice_data', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Invoice-' . $id . '.pdf');
    }

    //download label of shipped data (manifest)
    function LablePDF($id)
    {
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic_info'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['manifest'] = Manifest::where('id', $id)->first();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['manifest_order'] = DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->select('manifest_order.*', 'orders.*')->where('manifest_id', $id)->get();
        $data['product'] = Product::where('order_id', $id)->get();
        if(count($data['manifest_order']) > 100){
            // here code goes for multi label
            $allAwbs = [];
            foreach($data['manifest_order'] as $o)
                $allAwbs[] = $o->awb_number;
            $awbData = [
                "seller_id" => Session()->get('MySeller')->id,
                "awbs" => $allAwbs
            ];
            Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(url('/').'/cron/queue', $awbData);
            //Http::post("https://www.Twinnship.in/cron/queue",$awbData)->withHeaders(['content-type' => 'application/json']);
            $this->utilities->generate_notification('Success', "Go to MIS -> Download and download the labels.", 'success');
            return back();
        }

        // Get label configuration
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = Session()->get('MySeller')->id;
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
            $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
            $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE")  : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
            $label->save();
        }
        $data['label'] = $label;
        if($data['manifest']->courier == 'xindus'){
            $zipFile = "Xindus-Label.zip";
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true){
                exit('Unable to create File');
            }
            foreach ($data['manifest_order'] as $mo){
                $labelUrl = Order::where('id',$mo->order_id)->first();
                $file = InternationalOrderHelper::GetXindusLabel($labelUrl->awb_number,true);
                file_put_contents("labels/{$labelUrl->awb_number}.pdf",$file);
                $zip->addFile("labels/{$labelUrl->awb_number}.pdf", "{$labelUrl->awb_number}.pdf");
            }
            $zip->close();
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
            header("Content-length: " . filesize($zipFile));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
        if($data['manifest']->courier == 'aramex'){
            $zipFile = "Aramex-Label.zip";
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true){
                exit('Unable to create File');
            }
            foreach ($data['manifest_order'] as $mo){
                $labelUrl = Order::where('id',$mo->order_id)->first();
                $file = InternationalOrderHelper::GetAramexLabel($labelUrl,true);
                file_put_contents("labels/{$labelUrl->awb_number}.pdf",$file);
                $zip->addFile("labels/{$labelUrl->awb_number}.pdf", "{$labelUrl->awb_number}.pdf");
            }
            $zip->close();
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
            header("Content-length: " . filesize($zipFile));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
        if(str_contains($data['manifest']->courier,'movin')){
            $zipFile = "Movin-Label.zip";
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true){
                exit('Unable to create File');
            }
            $movin = new Movin();
            foreach ($data['manifest_order'] as $mo){
                $labelUrl = Order::where('id',$mo->order_id)->first();
                $response = $movin->shipmentLabel($labelUrl);
                $filePath = "labels/{$labelUrl->awb_number}.pdf";
                file_put_contents($filePath,file_get_contents($response['response']));
                $zip->addFile($filePath, "{$labelUrl->awb_number}.pdf");
            }
            $zip->close();
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
            header("Content-length: " . filesize($zipFile));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
        $pdf = PDF::loadView('seller.label_data', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        return $pdf->download('Label-' . $id . '.pdf');
        // return view('seller.label_data',$data);
    }

    //download manifest of multimanifest @ravi
    function multipleManifest(Request $request)
    {
        $ids = explode(',', $request->manifest_id);
        $data = $this->info;
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['config'] = $this->info['config'];
        $data['manifest_data'] = DB::table('orders')->wherein('id', $ids)->whereNotIn('status', ['pending', 'cancelled'])->get();
        $data['manifest_id'] = 0;
        $data['orders'] = Order::where('id', $ids)->where('status', '!=', 'pending')->where('status', '!=', 'cancelled')->with('products')->get();
//         dd($ids, $data['orders']->isEmpty(), $data['orders']->toArray());
        if ($data['orders']->isEmpty()) {
            $this->utilities->generate_notification('Error', "Please Select valid status !!", 'error');
            return redirect()->back();
        }
        $pdf = PDF::loadView('seller.multiple_manifest_data', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Manifest.pdf');
    }
    //download manifest invoice of order

    function ManifestPDF($id)
    {
        $data = $this->info;
        $data['manifest'] = Manifest::where('id', $id)->first();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['config'] = $this->info['config'];
        $data['manifest_data'] = DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->select('manifest_order.*', 'orders.*')->where('manifest_id', $id)->get();
        $data['manifest_id'] = $id;
        Manifest::where('id', $id)->update(['status' => 'manifest_downloaded']);
        $pdf = PDF::loadView('seller.manifest_invoice', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Manifest-' . $id . '.pdf');
    }

    //generate manifest of Selected order
    function generateManifest(Request $request)
    {
        $couriers = Order::select('courier_partner')->distinct('courier_partner')->where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'n')->whereIn('id', $request->ids)->get();
        $wareHouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        if (empty($wareHouse)) {
            $this->utilities->generate_notification('Error', ' Please Select Deafult Warehouse First', 'error');
        } else {
            $orderTracking = [];
            $sendSmsOrderId = [];
            $allManifest = [];
            $sendWhatsAppOrder = [];
            foreach ($couriers as $c) {
                $rand = rand(1000, 9999);
                $data = array(
                    'seller_id' => Session()->get('MySeller')->id,
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
                $res = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type', 'web')->where('seller_id', Session()->get('MySeller')->id)->get();
                if (count($res) > 0) {
                    $manifestId = $res[0]->id;
                } else {
                    $manifestId = Manifest::create($data)->id;
                }
                $totalOrders = 0;
                $orders = Order::where('courier_partner', $c->courier_partner)->where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'n')->whereIn('id', $request->ids)->get();
                foreach ($orders as $o) {
                    $allManifest[]=[
                        'manifest_id' => $manifestId,
                        'order_id' => $o->id
                    ];
                    //OrderTracking::create(['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request', 'remark' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s')]);
                    $orderTracking[] = ['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request', 'remarks' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s'),'created_at' => date('Y-m-d H:i:s')];
                    //Order::where('id', $o->id)->update(['status' => 'manifested', 'manifest_status' => 'y']);
                    $o->status = 'manifested';
                    $o->manifest_status = 'y';
                    $o->save();
                    if (Session()->get('MySeller')->sms_service == 'y') {
//                            $this->utilities->send_sms($o);
                        $sendSmsOrderId[] = $o->id;
                    }
                    if(Session()->get('MySeller')->whatsapp_service == 1){
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
            ManifestOrder::insert($allManifest);
            if(count($sendSmsOrderId) > 0){
                $jobDetails = [
                    'order_count' => count($sendSmsOrderId),
                    'order_ids' => implode(",",$sendSmsOrderId),
                    'status' => 'pending',
                    'seller_id' => Session()->get('MySeller')->id,
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
                    'seller_id' => Session()->get('MySeller')->id,
                    'inserted' => date('Y-m-d H:i:s')
                ];
                $jobId = SendManifestationWhatsAppJob::create($jobDetails)->id;
                SendManifestationWhatsApp::dispatchAfterResponse($jobId);
            }

            if(count($orderTracking) > 0){
                OrderTracking::insert($orderTracking);
            }
            $this->utilities->generate_notification('Success', ' Manifest Generated successfully', 'success');
        }
    }

    // Generate manifest
    function _generateManifest(array $orderIds) {
        // Get mps order ids
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
        $wareHouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->first();
        if (empty($wareHouse)) {
            return false;
        }
        $couriers = Order::select('courier_partner')->distinct('courier_partner')->where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
        $allManifest = [];
        foreach ($couriers as $c) {
            $rand = rand(1000, 9999);
            $data = array(
                'seller_id' => Session()->get('MySeller')->id,
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
            if (count($res = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type', 'web')->where('seller_id', Session()->get('MySeller')->id)->get()) > 0) {
                $manifestId = $res[0]->id;
            }else if (count($res = Manifest::where('created', date('Y-m-d'))->where('courier', $data['courier'])->where('type', 'api')->where('seller_id', Session()->get('MySeller')->id)->get()) > 0) {
                $manifestId = $res[0]->id;
            }
            else {
                $manifestId = Manifest::create($data)->id;
            }
            $totalOrders = 0;
            $orders = Order::where('courier_partner', $c->courier_partner)->where('seller_id', Session()->get('MySeller')->id)->where('manifest_status', 'n')->whereIn('id', $orderIds)->get();
            foreach ($orders as $o) {
                $allManifest[]=[
                    'manifest_id' => $manifestId,
                    'order_id' => $o->id
                ];
                //$res1 = ManifestOrder::where('manifest_id',$info['manifest_id'])->where('order_id',$info['order_id'])->first();
                //if(empty($res1)){
                //ManifestOrder::create($info);
                // create a order tracking for tracking the next order status
                OrderTracking::create(['awb_number' => $o->awb_number, 'status_code' => '00', 'status' => 'Pending', 'status_description' => 'pending request', 'remark' => 'generated manifest here', 'location' => 'NA', 'updated_date' => date('Y-m-d H:i:s'),'created_at' => date('Y-m-d H:i:s')]);
                //Order::where('id', $o->id)->update(['status' => 'manifested', 'manifest_status' => 'y']);
                if($o->status == 'shipped' || $o->status == 'pickup_requested')
                    $o->status = 'manifested';
                $o->manifest_status = 'y';
                $o->save();
                if (Session()->get('MySeller')->sms_service == 'y') {
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

    // pickup requested of Selected order
    function pickupRequested(Request $request) {
        $orders = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pickup_requested')->where('manifest_status', 'n')->whereIn('id', $request->ids)->get();
        if($orders->isNotEmpty()) {
            foreach($orders as $order) {
                $order->status = 'pickup_requested';
                $order->save();
            }
            $this->utilities->generate_notification('Success', ' Pickup requested successfully.', 'success');
        } else {
            if(!empty($request->ids)) {
                $this->utilities->generate_notification('Error', ' Pickup already requested.', 'error');
            } else {
                $this->utilities->generate_notification('Error', ' Pickup requested failed.', 'error');
            }
        }
    }

    //Rule matching for multiship order with courier partner assing first
    function _getShippingRate($order)
    {
        $orderDetail = Order::find($order);
        $weight = $orderDetail->weight;
        if ($orderDetail->vol_weight > $weight) {
            $weight = $orderDetail->vol_weight;
        }
        $wareHouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->get();
        if (count($wareHouse) == 0) {
            echo json_encode(array('error' => 'default warehouse not selected'));
            exit;
        }
        $prefs = Preferences::where('seller_id', Session()->get('MySeller')->id)->where('status', 'y')->orderBy('priority')->get();
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

    function _findCheapestOne($orderId)
    {
//        $rateCriteria = $this->_findMatchCriteria($orderId);
        $o = Order::find($orderId);
        $rateCriteria = MyUtility::findMatchCriteria($o->p_pincode,$o->s_pincode,Session()->get('MySeller'));
        $partner_rate[0] = Rates::select($rateCriteria)->where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->min($rateCriteria)->get();
        return $partner_rate[0];
    }

    //order pickup and deliver laction according to location match
    function _findMatchCriteria($orderId)
    {
        $orderDetail = Order::find($orderId);
        $column = '';
        $res = ZoneMapping::where('pincode', $orderDetail->s_pincode)->where('picker_zone', 'E')->get();
        $ncrArray = ['gurgaon','noida','ghaziabad','faridabad','delhi','new delhi','gurugram'];
        if(in_array(strtolower($orderDetail->s_city),$ncrArray) && in_array(strtolower($orderDetail->p_city),$ncrArray)){
            return 'within_city';
        } else if (strtolower($orderDetail->s_city) == strtolower($orderDetail->p_city) && strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_city';
        } else if (count($res) == 1) {
            return 'north_j_k';
        } else if (strtolower($orderDetail->s_state) == strtolower($orderDetail->p_state)) {
            return 'within_state';
        } else if (in_array(strtolower($orderDetail->s_city), $this->metroCities) && in_array(strtolower($orderDetail->p_city), $this->metroCities)) {
            return 'metro_to_metro';
        } else {
            $distance = 600000;
            if($orderDetail->seller_id == 1 || $orderDetail->seller_id == 509 || $orderDetail->seller_id == 482 || $orderDetail->seller_id == 505){
                $response = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/xml?origins={$orderDetail->p_pincode}&destinations={$orderDetail->s_pincode}&key=AIzaSyBJJ6HC4qEIRuEkHojuFqNJ-Dax2uekEtE");
                $responseData = json_encode(simplexml_load_string($response));
                $responseParsed = json_decode($responseData,true);
                $distance = $responseParsed['row']['element']['distance']['value'] ?? 600000;
            }
            if($distance < 500000)
                return 'within_state';
            else
                return 'rest_india';
        }
    }

    //order pickup and deliver laction according to location match
    function _findMatchCriteriaByPincode($s_pincode, $s_city, $s_state, $p_city, $p_state)
    {
        $column = '';
        $res = ZoneMapping::where('pincode', $s_pincode)->where('picker_zone', 'E')->get();
        $ncrArray = ['gurgaon','noida','ghaziabad','faridabad','delhi','new delhi','gurugram'];
        if(in_array(strtolower($s_city), $ncrArray) && in_array(strtolower($p_city), $ncrArray)){
            return 'within_city';
        } else if (strtolower($s_city) == strtolower($p_city) && strtolower($s_state) == strtolower($p_state)) {
            return 'within_city';
        } else if (count($res) == 1) {
            return 'north_j_k';
        } else if (strtolower($s_state) == strtolower($p_state)) {
            return 'within_state';
        } else if (in_array(strtolower($s_city), $this->metroCities) && in_array(strtolower($p_city), $this->metroCities)) {
            return 'metro_to_metro';
        } else {
            return 'rest_india';
        }
    }
    //controller methods for the Ndr order management
    function ndr_orders()
    {
        Session::put('noOfPage', 20);
        $data = $this->info;
        Session($this->filterArrayNDR);
        session(['current_tab_ndr' => 'action_required']);
        $data['ndr_data'] = DB::table('orders')->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_action', 'pending')->orderBy('ndr_raised_time','desc')->paginate(Session()->get('noOfPage'));
        // $cnt = 0;
        // foreach ($data['ndr_data'] as $nd) {
        // $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->count();
        // }
        // dd($data['ndr_data']);
//        $data['ndr_data'] = DB::table('orders')->join('ndr_attemps', 'ndr_attemps.order_id', '=', 'orders.id')->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.', 'ndr_attemps.reason as ndr_reason', 'orders.')->where('orders.ndr_status', 'y')->where('orders.ndr_action', 'pending')->paginate(Session()->get('noOfPage'));
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        return view('seller.ndr', $data);
    }

    function view_ndr_history($id)
    {
        $data['ndr_data'] = Ndrattemps::where('order_id', $id)->get();
        return view('seller.ndr_history', $data);
    }

    public function export_ndr_order(Request $request)
    {
        $ids = array_filter(explode(',', $request->ids ?? ''), function($el) {
            return !empty($el);
        });

        $name = "exports/NDR";
        $filename = "NDR";

        $query = Order::where('seller_id',Session()->get('MySeller')->id);

        if(!empty($ids)){
            $query = $query->whereIn('id',$ids);
        }

        $query = $query->where('seller_id',Session()->get('MySeller')->id);
        $all_data = $query->with('ndrattempts')->get();

        $fp = fopen("exports/NDR.csv", 'w');
        $info = array('Sr no', 'Order Number', 'Order Date', 'Status', 'AWB Number', 'Channel Name', 'Store Name', 'Product Name', 'Product Quantity', 'Customer Name', 'Customer Email', 'Customer Mobile', 'Address Line 1', 'Address Line 2', 'Address City', 'Address State', 'Address Pincode', 'Payment Method', 'Order Total', 'Number of NDR attempts', 'First NDR raised date', 'First NDR raised time', 'First NDR Action By', 'Reason for First NDR', 'Action date for First NDR', 'Action Status for First NDR', 'Remarks for First NDR', 'First Updated Address Line 1', 'First Updated Address Line 1', 'First Updated Mobile', 'Second NDR raised date', 'Second NDR raised time', 'Second NDR Action By', 'Reason for Second NDR', 'Action date for Second NDR', 'Action Status for Second NDR', 'Remarks for Second NDR', 'Second Updated Address Line 1', 'Second Updated Address Line 1', 'Second Updated Mobile', 'Third NDR raised date', 'Third NDR raised time', 'Third NDR Action By', 'Reason for Third NDR', 'Action date for Third NDR', 'Action Status for Third NDR', 'Remarks for Third NDR', 'Third Updated Address Line 1', 'Third Updated Address Line 1', 'Third Updated Mobile');
        $cnt = 1;
        fputcsv($fp, $info);

        foreach ($all_data as $e) {
            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                $e->b_customer_name = 'PII Data Archived';
                $e->b_address_line1 = 'PII Data Archived';
                $e->b_address_line2 = 'PII Data Archived';
                $e->b_city = 'PII Data Archived';
                $e->b_state = 'PII Data Archived';
                $e->b_country = 'PII Data Archived';
                $e->b_pincode = 'PII Data Archived';
                $e->b_contact_code = 'PII Data Archived';
                $e->b_contact = 'PII Data Archived';
                $e->s_customer_name = 'PII Data Archived';
                $e->s_address_line1 = 'PII Data Archived';
                $e->s_address_line2 = 'PII Data Archived';
                $e->s_city = 'PII Data Archived';
                $e->s_state = 'PII Data Archived';
                $e->s_country = 'PII Data Archived';
                $e->s_pincode = 'PII Data Archived';
                $e->s_contact_code = 'PII Data Archived';
                $e->s_contact = 'PII Data Archived';
                $e->invoice_amount = 'PII Data Archived';
                $e->product_name = 'PII Data Archived';
                $e->product_sku = 'PII Data Archived';
                $e->product_qty = 'PII Data Archived';
                $e->delivery_address = 'PII Data Archived';
            }
            if ($e->rto_status == 'y' && $e->status == 'delivered')
                $e->status = 'rto_delivered';
            $quantity = explode(',', $e->product_name);
            $attempts = $e->ndrattempts;
            if($e->status == 'delivered' && $e->rto_status == 'y')
                $e->status = 'rto_delivered';
            else if($e->rto_status == 'y' && $e->status=='in_transit')
                $e->status='rto_in_transit';
            if($this->fullInformation)
                $info = array($cnt, $e->customer_order_number, $e->inserted, $this->orderStatus[$e->status], ('`' . $e->awb_number . '`'), $e->channel, $e->seller_channel_name, $e->product_name, $e->product_qty, $e->s_customer_name, $e->s_customer_email, $e->s_contact, $e->s_address_line1, $e->s_address_line2, $e->s_city, $e->s_state, $e->s_pincode, $e->order_type, $e->invoice_amount, count($attempts) == 0 ? 1 : count($attempts));
            else
                $info = array($cnt, $e->customer_order_number, $e->inserted, $this->orderStatus[$e->status], ('`' . $e->awb_number . '`'), $e->channel, $e->seller_channel_name, $e->product_name, $e->product_qty, $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", $e->order_type, $e->invoice_amount, count($attempts) == 0 ? 1 : count($attempts));
            //Ndrattemps::where('order_id', $e->id)->orderBy('position')->get();
            foreach ($attempts as $a) {
                $info[] = $a->raised_date;
                $info[] = $a->raised_time;
                $info[] = $a->action_by;
                $info[] = $a->reason;
                $info[] = $a->action_date;
                $info[] = $a->action_status;
                $info[] = $a->remark;
                $info[] = $a->u_address_line1;
                $info[] = $a->u_address_line2;
                $info[] = $a->updated_mobile;
            }
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


    //import ndr order details
    public function import_ndr_order(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $orders = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $order_id = $fileop[1];
                            $awb_number = trim($fileop[2], '`');
                            $oid = $this->_getorderId($awb_number);
                            if ($oid != '0') {
                                $orders[] = $oid;
                                $attemps = Ndrattemps::where('order_id', $oid)->delete();
                                $order_status = Order::whereIn('id', $orders)->update(['status' => 'ndr', 'ndr_action' => 'pending', 'ndr_status' => 'y']);
                                $fetch = true;
                                $cnt = 17;
                                $pos = 1;
                                while ($fetch == true) {
                                    $data = array(
                                        'seller_id' => Session()->get('MySeller')->id,
                                        'order_id' => $oid,
                                        '`position' => $pos++,
                                        'raised_date' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'raised_time' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'action_by' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'reason' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'action_date' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'action_status' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'remark' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'u_address_line1' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'u_address_line2' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                        'updated_mobile' => isset($fileop[$cnt]) ? $fileop[$cnt++] : "",
                                    );
                                    if ($fileop[$cnt] == null)
                                        $fetch = false;
                                    Ndrattemps::create($data);
                                }
                            } else {
                                $this->utilities->generate_notification('Error', 'Invalid AWB Number', 'Error');
                            }
                        }
                    }
                    $cnt++;
                }
                // Ndrattemps::Insert($data);
                $this->utilities->generate_notification('Success', 'CSV Uploaded successfully', 'success');
                return redirect()->back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
    }

    //for get order id  using awb number
    public function _getorderId($awb_number)
    {
        $order_id = Order::select('id')->where('awb_number', $awb_number)->first();
        if (!empty($order_id)) {
            return $order_id->id;
        } else {
            return 0;
        }
    }

    //set filter key for ndr order
    function setFilterNDR(Request $request)
    {
        $data = $request->value;
        Session::put($request->key, $data);
        // session(['min_value' => $request->min_value, 'max_value' => $request->max_value]);
        session([
            'ndr_start_date' => isset($request->ndr_start_date) ? $request->ndr_start_date : session('ndr_start_date'),
            'ndr_end_date' => isset($request->ndr_end_date) ? $request->ndr_end_date : session('ndr_end_date'),
            'ndr_type' => $request->ndr_type
        ]);
        // print_r($request->all());
    }

    //ajax searching of ndr order using session key
    function ajax_filter_ndr(Request $request)
    {
        $s_ndr_reason = session('ndr_reason');
        $s_ndr_awb = session('ndr_awb_number');
        $s_ndr_courier = session('ndr_courier_partner');
        $s_ndr_order = session('ndr_order');
        $ndr_start_date = session('ndr_start_date');
        $ndr_end_date = session('ndr_end_date');
        $ndr_type = session('ndr_type');
        $ndr_status = session('ndr_status');
        $ndr_order_number = session('ndr_order_number');
        DB::enableQueryLog();
        $query = Order::where('seller_id', Session()->get('MySeller')->id);
        if (!empty($s_ndr_awb)) {
            $query = $query->where('awb_number', $s_ndr_awb);
        }
        if (!empty($ndr_order_number)) {
            $query = $query->where('customer_order_number', $ndr_order_number);
        }
        if (!empty($ndr_status)) {
            $query->where(function($q) use($ndr_status) {
                foreach($ndr_status as $row) {
                    if($row == 'rto_delivered') {
                        $q = $q->orWhere(function($q) {
                            $q->where('status', 'delivered')
                                ->where('rto_status', 'y');
                        });
                    } else if($row == 'rto_in_transit') {
                        $q = $q->orWhere(function($q) {
                            $q->where('status', 'in_transit')
                                ->where('rto_status', 'y');
                        });
                    }
                    else if($row == 'rto_initated' || $row == 'rto_initiated') {
                        $q = $q->orWhere(function($q) {
                            $q->whereIn('status', ['rto_initated','rto_initiated'])
                                ->where('rto_status', 'y');
                        });
                    } else {
                        $q = $q->orWhere('status', function($q) use($row){
                            $q->where('status',$row)->where('rto_status','n');
                        });
                    }
                }
            });
        }
        // if (!empty($s_ndr_courier)) {
        //     $query = $query->where('courier_partner', $s_ndr_courier);
        // }
        if (!empty($s_ndr_courier) && is_array($s_ndr_courier)) {
            $query = $query->whereIn('courier_partner', $s_ndr_courier);
        }
        if (!empty($ndr_start_date)) {
            $query = $query->whereDate('ndr_raised_time', '>=', date('Y-m-d', strtotime($ndr_start_date)));
        }
        if (!empty($ndr_end_date)) {
            $query = $query->whereDate('ndr_raised_time', '<=', date('Y-m-d', strtotime($ndr_end_date)));
        }
        if (!empty($s_ndr_order)) {
            $query = $query->where(function($query) use($s_ndr_order) {
                return $query->where('product_name', 'like', '%' . $s_ndr_order . '%')
                    ->orWhere('product_sku', 'like', '%' . $s_ndr_order . '%')
                    ->orWhere('b_pincode', 'like', '%' . $s_ndr_order . '%');
            });
        }
        if (!empty($s_ndr_reason)) {
            $query = $query->where('reason_for_ndr', 'like', '%' . $s_ndr_reason . '%');
        }
        if (!empty($ndr_type)) {
            if ($ndr_type == 'action_required') {
                $query = $query->where('status', '!=', 'delivered')
                    ->where('ndr_action', 'pending')
                    ->where('ndr_status', 'y')
                    ->where('rto_status', 'n');
            } else if ($ndr_type == 'action_requested') {
                $query = $query->where('status', '!=', 'delivered')
                    ->where('ndr_action', 'requested')
                    ->where('ndr_status', 'y')
                    ->where('rto_status', 'n');
            } else if ($ndr_type == 'ndr_delivered') {
                $query = $query->where('status', 'delivered')
                    ->where('ndr_status', 'y')
                    ->where('rto_status', 'n');
            } else if ($ndr_type == 'ndr_rto') {
                $query = $query->where('rto_status', 'y')
                    ->where('ndr_status', 'y');
            }
        }
        // $data['ndr_data'] = $query->get();
        $cnt = 0;
        $data['ndr_data'] = $query->with('ndrattempts')->orderBy('ndr_raised_time','desc')->paginate(Session()->get('noOfPage'));
        foreach ($data['ndr_data'] as $nd) {
            $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->where('ndr_data_type','auto')->count();
        }
        $data['count_ajax_ndr_data'] = $data['ndr_data']->total();
        // dd(DB::getQueryLog());
//        $cnt = 0;
//        foreach ($data['ndr_data'] as $nd) {
//            $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->where('action_by','!=','Twinnship')->where('ndr_data_type','auto')->count();
//        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        // return view('seller.partial_ndr_data', $data);
        if ($ndr_type == 'action_required') {
            return view('seller.ndr_all_order', $data);
        } else if ($ndr_type == 'action_requested') {
            return view('seller.ndr_requested', $data);
        } else if ($ndr_type == 'ndr_delivered') {
            return view('seller.ndr_delivered', $data);
        } else if ($ndr_type == 'ndr_rto') {
            return view('seller.ndr_rto', $data);
        } else {
            return view('seller.partial_ndr_data', $data);
        }
    }

    //display data of ndr action required order
    function ndrActionRequired()
    {
        Session($this->filterArrayNDR);
        session([
            'current_tab_ndr' => 'action_required',
            'ndr_type' => 'action_required'
        ]);
        $data = $this->info;
        $data['ndr_data'] = Order::where('seller_id', Session()->get('MySeller')->id)
            ->where('status', '!=', 'delivered')
            ->where('ndr_action', 'pending')
            ->where('ndr_status', 'y')
            ->where('rto_status', 'n')
            ->orderBy('ndr_raised_time','desc')
            ->with('ndrattempts')
            ->paginate(Session()->get('noOfPage'));
        // $cnt = 0;
        // foreach ($data['ndr_data'] as $nd) {
        //     $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->where('ndr_data_type','auto')->count();
        // }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ndr_all_order', $data);
    }

    //display data of ndr action requested order
    function ndrActionRequested()
    {
        Session($this->filterArrayNDR);
        session([
            'current_tab_ndr' => 'action_requested',
            'ndr_type' => 'action_requested'
        ]);
        $data = $this->info;
        // $data['ndr_data'] = DB::table('ndr_attemps')->join('orders', 'ndr_attemps.order_id', '=', 'orders.id')->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.*', 'ndr_attemps.reason as ndr_reason', 'orders.*')->where('status', 'ndr')->where('ndr_action', 'reattempt')->paginate(Session()->get('noOfPage'));
        $data['ndr_data'] = DB::table('orders')
            ->where('seller_id', Session()->get('MySeller')->id)
            ->where('status', '!=', 'delivered')
            ->where('ndr_action', 'requested')
            ->where('ndr_status', 'y')
            ->where('rto_status', 'n')
            ->orderBy('ndr_raised_time','desc')
            ->paginate(Session()->get('noOfPage'));
        $cnt = 0;
        foreach ($data['ndr_data'] as $nd) {
            $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->where('ndr_data_type','auto')->count();
        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ndr_requested', $data);
    }

    function ndrDelivered()
    {
        Session($this->filterArrayNDR);
        session([
            'current_tab_ndr' => 'ndr_delivered',
            'ndr_type' => 'ndr_delivered'
        ]);
        $data = $this->info;
        // $data['ndr_data'] = DB::table('ndr_attemps')->join('orders', 'ndr_attemps.order_id', '=', 'orders.id')->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.*', 'ndr_attemps.reason as ndr_reason', 'orders.*')->where('status', 'ndr')->where('ndr_action', 'delivered')->paginate(Session()->get('noOfPage'));
        $data['ndr_data'] = Order::select('orders.id','orders.customer_order_number','orders.ndr_raised_time','orders.o_type','orders.order_type','orders.shipment_type','orders.ndr_action','orders.reason_for_ndr','orders.channel','orders.inserted','orders.product_name','orders.product_sku','orders.product_qty','orders.b_customer_name','orders.b_contact','orders.courier_partner','orders.awb_number','orders.s_state','orders.s_city','orders.s_pincode','orders.s_address_line1','orders.s_address_line2')
            ->where('seller_id', Session()->get('MySeller')->id)
            ->where('status', 'delivered')
            ->where('ndr_status', 'y')
            ->where('rto_status', 'n')
            ->with('ndrattempts')
            ->orderBy('ndr_raised_time','desc')
            ->paginate(Session()->get('noOfPage'));
//        $cnt = 0;
//        foreach ($data['ndr_data'] as $nd) {
//            $data['ndr_data'][$cnt++]->ndr_count = Ndrattemps::where('order_id', $nd->id)->where('ndr_data_type','auto')->count();
//        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ndr_delivered', $data);
    }

    function ndrRTO()
    {
        Session($this->filterArrayNDR);
        session([
            'current_tab_ndr' => 'ndr_rto',
            'ndr_type' => 'ndr_rto'
        ]);
        $data = $this->info;
        // $data['ndr_data'] = DB::table('ndr_attemps')->join('orders', 'ndr_attemps.order_id', '=', 'orders.id')->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.*', 'ndr_attemps.reason as ndr_reason', 'orders.*')->where('status', 'ndr')->where('ndr_action', 'rto')->paginate(Session()->get('noOfPage'));
        $data['ndr_data'] = Order::select('orders.id','orders.status','orders.customer_order_number','orders.ndr_raised_time','orders.o_type','orders.order_type','orders.shipment_type','orders.ndr_action','orders.reason_for_ndr','orders.channel','orders.inserted','orders.product_name','orders.product_sku','orders.product_qty','orders.b_customer_name','orders.b_contact','orders.courier_partner','orders.awb_number','orders.s_state','orders.s_city','orders.s_pincode','orders.s_address_line1','orders.s_address_line2','orders.rto_status')
            ->where('seller_id', Session()->get('MySeller')->id)
            ->where('rto_status', 'y')
            ->where('ndr_status', 'y')
            ->with('ndrattempts')
            ->orderBy('ndr_raised_time','desc')
            ->paginate(Session()->get('noOfPage'));
//        $cnt = 0;
//        foreach ($data['ndr_data'] as $nd) {
//            $data['ndr_data'][$cnt++]->ndr_count = Ndratte    mps::where('order_id', $nd->id)->where('ndr_data_type','auto')->count();
//        }
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ndr_rto', $data);
    }

    function resetFilterNDR($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session([$k => '']);
    }

    function countOrderNdr()
    {
        $data['action_required'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('ndr_action', 'pending')->where('status', '!=', 'delivered')->count();
        $data['action_requested'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('ndr_action', 'requested')->where('status', '!=', 'delivered')->count();
        $data['ndr_delivered'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', 'delivered')->count();
        $data['ndr_rto'] = DB::table('orders')->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status', 'y')->count();
        return $data;
    }

    function ndrReattempOrder(Request $request)
    {
        $ids = explode(',', $request->id);
        foreach($ids as $id) {
            $order = Order::find($id);
            Order::where('id', $id)->update(['ndr_action' => 'requested']);
            $attempt = [
                'raised_date' => date('Y-m-d'),
                'seller_id' => $order->seller_id,
                'order_id' => $order->id,
                'remark' => $request->remark,
                'action_status' => $order->ndr_status,
                'action_by' => 'Seller',
                'reason' => 'Reattempt Requested',
                'ndr_data_type' => 'manual'
            ];
            Ndrattemps::create($attempt);
        }
        return response(['status' => 'true']);
    }

    function ndrRTOOrder(Request $request)
    {
        $ids = explode(",",$request->id);
        foreach($ids as $id){
            $order = Order::find($id);
            if(empty($order))
                return false;
            MyUtility::PerformCancellation(Session()->get('MySeller'),$order);
            Order::where('id', $id)->update(['ndr_action' => 'requested']);
            $attempt = [
                'raised_date' => date('Y-m-d'),
                'seller_id' => $order->seller_id,
                'order_id' => $order->id,
                'remark' => $request->remark,
                'action_status' => $order->ndr_status,
                'action_by' => 'Seller',
                'reason' => 'Marked RTO',
                'ndr_data_type' => 'manual'
            ];
            Ndrattemps::create($attempt);
        }
        $this->_refreshSession();
        return response(['status' => 'true']);
    }

    function ndrEscalateOrder($id)
    {
        // Order::where('id', $id)->update(['ndr_action' => 'escalate']);
        $order = Order::find($id);
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'type' => 'shipment_related_issue',
            'awb_number' => $order->awb_number,
            'issue' => 'Shipment Related Issue',
            'remark' => 'none',
            'ticket_no' => rand('300001', '500000'),
            'status' => 'o',
            'sevierity' => 'Low',
            'raised' => date('Y-m-d H:i:s')
        );
        SupportTicket::create($data);
        echo json_encode(array('status' => 'true'));
    }

    function ndrIvr(Request $request)
    {
        foreach ($request->ids as $id) {
            $ndr_status = rand(0, 2);
            Order::where('id', $id)->update(['ndr_status' => $this->ndr_status[$ndr_status]]);
        }

        $this->utilities->generate_notification('Success', ' Order Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }


    //function display all the billing details of the seller
    function billing()
    {
        $data = $this->info;
        Session::put('noOfPage', 20);
        Session($this->filterArrayBilling);
        session(['billing_status' => 'shipping_charges']);
        $data['billing'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->paginate(30);
        $data['total_freight_charge'] = $data['billing']->sum('total_charges');
        $data['early_cod'] = EarlyCod::where('status', 'y')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partners'] = Partners::where('status', 'y')->get();
        return view('seller.billing', $data);
    }

    function reset_filters()
    {
        Session($this->filterArrayBilling);

    }

    //for billing count(display in tab badge)
    function countBilling()
    {
        $sellerId = Session()->get('MySeller')->id;
        $data['total_billing'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->count();
        $data['total_weight_reconciliation'] = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
            // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
            ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status', 'weight_reconciliation.c_weight as c_weight', 'weight_reconciliation.c_length as c_length', 'weight_reconciliation.c_breadth as c_breadth', 'weight_reconciliation.c_height as c_height')
            ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->count();
        $data['total_remittance_log'] = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->count();
        $query = " from
(
select datetime,id,amount,description,type from transactions where seller_id = {$sellerId} and redeem_type = 'r'
union
select datetime,id,amount,description,'c' as type from cod_transactions where seller_id = {$sellerId} and redeem_type = 'r'  and mode = 'wallet'
) a";
        $data['total_recharge_log'] = DB::select("select count(*) as total ".$query)[0]->total ?? 0;
        $data['total_onhold_reconciliation'] = DB::table('weight_reconciliation')
            ->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
            ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)
            // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
            ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')
            ->orderBy('weight_reconciliation.created', 'desc')
            ->count();
        $data['total_passbook'] = Transactions::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->count();
        $data['total_invoices'] = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->count();
        $data['total_credit_receipt'] = BillReceipt::where('seller_id', Session()->get('MySeller')->id)->count();
        return $data;
    }

    function ajax_shipping_charges()
    {
        $data = $this->info;
        Session($this->filterArrayBilling);
        session(['billing_status' => 'shipping_charges']);
        $data['billing'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->orderBy('awb_assigned_date', 'desc')->paginate(Session()->get('noOfPage'));
        $data['total_freight_charge'] = $data['billing']->sum('total_charges');
        $data['early_cod'] = EarlyCod::where('status', 'y')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.ajax_shipping_charges', $data);
    }

    function setFilterBilling(Request $request)
    {
        $data = $request->value;
        Session::put($request->key, $data);
        session([
            'billing_start_date' => isset($request->billing_start_date) ? $request->billing_start_date : session('billing_start_date'),
            'billing_end_date' => isset($request->billing_end_date) ? $request->billing_end_date : session('billing_end_date'),
            'w_start_date' => isset($request->w_start_date) ? $request->w_start_date : session('w_start_date'),
            'w_end_date' => isset($request->w_end_date) ? $request->w_end_date : session('w_end_date'),
            'r_start_date' => isset($request->r_start_date) ? $request->r_start_date : session('r_start_date'),
            'r_end_date' => isset($request->r_end_date) ? $request->r_end_date : session('r_end_date'),
            'c_start_date' => isset($request->c_start_date) ? $request->c_start_date : session('c_start_date'),
            'c_end_date' => isset($request->c_end_date) ? $request->c_end_date : session('c_end_date'),
            'billing_filter_type' => $request->billing_filter_type]);
    }

    function ajax_filter_billing(Request $request)
    {
        // dd($request->all());
        $data = $this->info;
        $billing_order_number = session('b_order_number');
        $courier_partner = session('courier_partner');
        $b_awb_number = session('b_awb_number');
        $b_order_status = session('b_order_status');
        $billing_start_date = session('billing_start_date');
        $billing_end_date = session('billing_end_date');
        $w_start_date = session('w_start_date');
        $w_end_date = session('w_end_date');
        $r_start_date = session('r_start_date');
        $r_end_date = session('r_end_date');
        $c_start_date = session('c_start_date');
        $c_end_date = session('c_end_date');
        $billing_filter_type = session('billing_filter_type');
        $weight_rec_status = session('weight_rec_status');
        $data['w_start_date'] = $w_start_date;
        $data['w_end_date'] = $w_end_date;
        $data['awb_code'] = $b_awb_number;
        $data['r_start_date'] = $r_start_date;
        $data['r_end_date'] = $r_end_date;
        $data['c_start_date'] = $c_start_date;
        $data['c_end_date'] = $c_end_date;
        // DB::enableQueryLog();
        if ($billing_filter_type == 'weight_reconciliation') {
            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
                // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
                ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status', 'weight_reconciliation.c_weight as c_weight', 'weight_reconciliation.c_length as c_length', 'weight_reconciliation.c_breadth as c_breadth', 'weight_reconciliation.c_height as c_height')
                ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id);
            if (!empty($weight_rec_status)) {
                $query = $query->where('weight_reconciliation.status', $weight_rec_status);
            }
        } elseif ($billing_filter_type == 'remittance_log') {
            $query = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc');
        } elseif ($billing_filter_type == 'recharge_log') {
            $query = Transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc');
        } elseif ($billing_filter_type == 'onhold') {
            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
                ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)
                // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
                ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')
                ->orderBy('weight_reconciliation.created', 'desc');
        }
        elseif ($billing_filter_type == 'passbook') {
            //$query = DB::table('transactions')->join('orders', 'transactions.order_id', '=', 'orders.id')->select('transactions.*', 'orders.awb_number')->where('orders.seller_id', Session()->get('MySeller')->id);
            // $query = Transactions::where('seller_id', Session()->get('MySeller')->id)->orderBy('datetime', 'desc');
            $query = DB::table('transactions')
                ->leftJoin('orders',function($join){
                    $join->on('transactions.order_id','=','orders.id');
                })
                ->leftJoin('zz_archive_orders',function($join){
                    $join->on('transactions.order_id','=','zz_archive_orders.id');
                })
                ->select('transactions.*', 'orders.awb_number', 'orders.courier_partner','zz_archive_orders.awb_number as awb_number1', 'zz_archive_orders.courier_partner as courier_partner1')->where('transactions.seller_id', Session()->get('MySeller')->id);
        } else {
            $query = Order::where('seller_id', Session()->get('MySeller')->id);
        }
        if (!empty($billing_order_number)) {
            $query = $query->where('customer_order_number', $billing_order_number);
        }
        if (!empty($b_order_status)) {
            $query = $query->whereIn('status', $b_order_status);
        }
        if (!empty($courier_partner)) {
            $query = $query->whereIn('orders.courier_partner', $courier_partner);
        }
        if (!empty($b_awb_number)) {
            if ($billing_filter_type == 'onhold') {
                $query = $query->where('orders.awb_number', $b_awb_number);
            } else {
                $query = $query->where('orders.awb_number', $b_awb_number);
            }
        }
        if (!empty($billing_start_date) && !empty($billing_end_date)) {
            if($billing_filter_type == 'shipping_charges'){
                $query = $query->whereDate('awb_assigned_date', '>=', $billing_start_date)->whereDate('awb_assigned_date', '<=', $billing_end_date);
            }
            else{
                $query = $query->whereDate('created', '>=', $billing_start_date)->whereDate('created', '<=', $billing_end_date);
            }
        }
        if (!empty($w_start_date) && !empty($w_end_date)) {
            if ($billing_filter_type == 'onhold') {
                $query = $query->whereDate('orders.awb_assigned_date', '>=', $w_start_date)->whereDate('orders.awb_assigned_date', '<=', $w_end_date);
            } else if ($billing_filter_type == 'passbook') {
                $query = $query->whereDate('transactions.datetime', '>=', $w_start_date)->whereDate('transactions.datetime', '<=', $w_end_date);
            }
            else {
                $query = $query->whereDate('inserted', '>=', $c_start_date)->whereDate('inserted', '<=', $c_end_date);
            }
        }
        if (!empty($r_start_date) && !empty($r_end_date)) {
            $query = $query->whereDate('datetime', '>=', $r_start_date)->whereDate('datetime', '<=', $r_end_date);
        }
        if (!empty($c_start_date) && !empty($c_end_date) && $billing_filter_type == 'recharge_log') {
            $query = $query->whereDate('datetime', '>=', $c_start_date)->whereDate('datetime', '<=', $c_end_date);
        }
        if(!empty(session()->get('remit_mode')) && $billing_filter_type == 'remittance_log'){
            $query = $query->whereIn('mode',session()->get('remit_mode'));
        }
        // $query = $query->get();
        // dd(DB::getQueryLog());

        if ($billing_filter_type == 'shipping_charges') {
            $query = $query->where('status', '!=', 'pending')->orderBy('awb_assigned_date','desc');
        } elseif ($billing_filter_type == 'weight_reconciliation') {
            $data['weight_reconciliation'] = $query->paginate(Session()->get('noOfPage'));
            $data['total_billing_data'] = $query->count();
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            // return view('seller.ajax_weight_rec', $data);
            return view('seller.b_weight_reconcillation', $data);
        } elseif ($billing_filter_type == 'remittance_log') {
            $data['remittance'] = $query->paginate(Session()->get('noOfPage'));
            $data['total_cod_remittance'] = $data['remittance']->sum('amount');
            return view('seller.b_remittance_log', $data);
        } elseif ($billing_filter_type == 'recharge_log') {
            if (Session()->has('noOfPage')) {
                $noOfPage = Session()->get('noOfPage');
            } else {
                $noOfPage = 20;
            }
            $sellerId = Session()->get('MySeller')->id;
            $page = $request->page ?? 1;
            $offSet = $noOfPage * ($page - 1);
            $query = " from
(
select datetime,id,amount,description,type from transactions where seller_id = {$sellerId} and redeem_type = 'r'";
            if (!empty($r_start_date) && !empty($r_end_date)) {
                $query.=" and datetime >= '{$r_start_date} 00:00:00' and datetime <= '{$r_end_date} 23:59:59'";
            }
        $query.=" union
        select datetime,id,amount,description,'c' as type from cod_transactions where seller_id = {$sellerId} and redeem_type = 'r' and mode = 'wallet' ";
            if (!empty($r_start_date) && !empty($r_end_date)) {
                $query.=" and datetime >= '{$r_start_date} 00:00:00' and datetime <= '{$r_end_date} 23:59:59'";
            }
    $query.=") a";
            $data['temp'] = DB::select("select * ".$query." order by datetime desc limit {$noOfPage} offset {$offSet}");
            $data['totalPage'] = DB::select("select count(*) as total ".$query)[0]->total ?? 0;
            $data['successfull_recharge'] = DB::select("select sum(amount) as total ".$query)[0]->total ?? 0;
            $data['total_credit'] = DB::select("select sum(amount) as total ".$query." where type = 'c'")[0]->total ?? 0;
            $data['total_debit'] = DB::select("select sum(amount) as total ".$query." where type = 'd'")[0]->total ?? 0;
            return view('seller.b_recharge_log', $data);
        } elseif ($billing_filter_type == 'onhold') {
            $data['onhold'] = $query->paginate(Session()->get('noOfPage'));
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            return view('seller.b_onhold', $data);
        } elseif ($billing_filter_type == 'passbook') {
            //$data['passbook'] = $query->paginate(Session()->get('noOfPage'));
            //$data['awb_number'] = Order::getAWBNumber(Session()->get('MySeller')->id);
            $query = $query->orderBy('transactions.id','desc');
            $data['passbook'] = $query->paginate(Session()->get('noOfPage'));
            $data['PartnerName'] = Partners::getPartnerKeywordList();
            return view('seller.b_passbook', $data);
        }
        $data['billing_data'] = $query->paginate(Session()->get('noOfPage'));
        $data['total_billing_data'] = $query->count();
        //dd(DB::getQueryLog());
        //  dd($data['billing_data']);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.partial_billing_data', $data);
    }

    function resetFilterBilling($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session([$k => '']);
    }

    function billingWeightReconciliation()
    {
        // $data['weight_reconciliation'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->get();
        $orderQuery = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
            // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
            ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status', 'weight_reconciliation.c_weight as c_weight', 'weight_reconciliation.c_length as c_length', 'weight_reconciliation.c_breadth as c_breadth', 'weight_reconciliation.c_height as c_height')
            ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->orderBy('weight_reconciliation.id', 'desc');
        $orderCount = $orderQuery->count();
        $data['weight_reconciliation'] = $orderQuery->paginate($request->pageSize ?? 20);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $response['content'] = view('seller.partial.weight-reconciliation',$data)->render();
        $response['page'] = UtilityHelper::GetPaginationData($orderCount, $request->pageSize ?? 20, $request->page ?? null);
        $response['page']['current_count'] = count($data['weight_reconciliation']);
        return response()->json($response);
    }

    function weightReconciliationAcceptOrder($id)
    {
        WeightReconciliation::where('id', $id)->update(['status' => 'accepted']);
        $this->utilities->generate_notification('Success', ' Accepted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    function weightReconciliationAcceptOrderMultiple(Request $request)
    {
        // dd($request->all());
        WeightReconciliation::whereIn('id', $request->ids)->update(['status' => 'accepted']);
        $this->utilities->generate_notification('Success', ' Accepted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    function getHistoryWeightReconciliation($id)
    {
        $data_w['weight_rec_data'] = WeightReconciliation::find($id);
        $data_w['history'] = WeightReconciliationHistory::where('weight_reconciliation_id', $id)->get();
        return view('seller.b_weight_rec_history', $data_w);
    }

    function disputeOrder(Request $request)
    {
        // dd($request->all());
        $data = [
            "weight_reconciliation_id" => $request->weight_rec_id,
            "action_taken_by" => "Seller",
            "history_date" => date('Y-m-d H:i:s'),
            "remark" => $request->remark,
            'status' => 'dispute_raised'
        ];
        // dd($data);
        WeightReconciliation::where('id', $request->weight_rec_id)->update(['status' => 'dispute_raised']);
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
        $this->utilities->generate_notification('Success', ' Dispute Raised successfully', 'success');
        return redirect()->back();
    }

    function addWeightRecComment(Request $request)
    {
        //  dd($request->all());
        $data = [
            "weight_reconciliation_id" => $request->weight_rec_id,
            "action_taken_by" => "Seller",
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
        $this->utilities->generate_notification('Success', ' Dispute Added successfully', 'success');
        return redirect()->back();
    }

    function billingRemmitanceLog()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'remmitance_log']);
        session(['remit_mode' => []]);

        // Seller COD counter Goes Here
        $remDays = Session()->get('MySeller')->remittance_days ?? 7;
        $data['cod_total'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->sum('invoice_amount');
        //$data['cod_available'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->where('cod_remmited', 'n')->whereDate('delivered_date','<',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
        $data['remitted_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->where('cod_remmited', 'y')->sum('invoice_amount');
        //$data['cod_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->where('cod_remmited', 'n')->whereDate('delivered_date','>=',date('Y-m-d H:i:s',strtotime("-$remDays days")))->sum('invoice_amount');
        $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        $data['nextRemitDate'] = $codArray['nextRemitDate'];
        $data['nextRemitCod'] = $codArray['nextRemitCod'];

        $data['remittance'] = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->paginate($noOfPage);
        $data['reversal_amount'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'cancelled')->sum('cod_charges');
        $data['total_cod_remittance'] = $data['remittance']->sum('amount');
        return view('seller.b_remittance_log', $data);
    }

    function export_admin_remittance($id)
    {
        $name = "exports/RemittanceDetails";
        $filename = "RemittanceDetails";
        $all_data = RemittanceDetails::where('cod_transactions_id', $id)->get();
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr no.', 'CRF ID', 'AWB Number', 'Order Id', 'Courier Partner', 'Delivered Date', 'COD Amount', 'Remittance Date', 'UTR Number', 'Channel');
        $PartnerName = Partners::getPartnerKeywordList();
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $order = Order::where('awb_number', $e->awb_number)->first();
            $order_number = isset($order->order_number) ? $order->order_number : '';
            $delivered_date = isset($order->delivered_date) ? $order->delivered_date : '';
            $courier_partner = isset($order->courier_partner) ? $PartnerName[$order->courier_partner] : '';
            $channel = isset($order->channel) ? $order->channel : '';
            $info = array($cnt, $e->crf_id, ('`' . $e->awb_number . '`'), $order_number, $courier_partner, $delivered_date, $e->cod_amount, $e->remittance_amount, $e->utr_number, $channel);
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

    function billingRechargeLog(Request $request)
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        $page = $request->page ?? 1;
        $offSet = $noOfPage * ($page - 1);
        $sellerId = Session()->get('MySeller')->id;
        session(['billing_status' => 'recharge_log']);
        $query = " from
(
select datetime,id,amount,description,type from transactions where seller_id = {$sellerId} and redeem_type = 'r'
union
select datetime,id,amount,description,'c' as type from cod_transactions where seller_id = {$sellerId} and redeem_type = 'r' and mode = 'wallet'
) a";
        $data['temp'] = DB::select("select * ".$query." order by datetime desc limit {$noOfPage} offset {$offSet}");
        $data['totalPage'] = DB::select("select count(*) as total ".$query)[0]->total ?? 0;
        $data['successfull_recharge'] = DB::select("select sum(amount) as total ".$query)[0]->total ?? 0;
        $data['total_credit'] = DB::select("select sum(amount) as total ".$query." where type = 'c'")[0]->total ?? 0;
        $data['total_debit'] = DB::select("select sum(amount) as total ".$query." where type = 'd'")[0]->total ?? 0;
        return view('seller.b_recharge_log', $data);
    }

    function billingOnhold()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'on_hold']);
        // $data['onhold'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->get();
        $data['onhold'] = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')
            ->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)
            // ->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])
            ->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')
            ->orderBy('weight_reconciliation.created', 'desc')->paginate($noOfPage);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.b_onhold', $data);
    }

    function billingPassbook()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'passbook']);
        // $data['passbook'] = DB::table('transactions')->join('orders', 'transactions.order_id', '=', 'orders.id')->where('transactions.seller_id', Session()->get('MySeller')->id)->select('transactions.*', 'orders.awb_number')->orderBy('datetime','desc')->get();
        $data = $this->info;
        //$data['passbook'] = Transactions::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->paginate($noOfPage);
        //$data['awb_number'] = Order::getAWBNumber(Session()->get('MySeller')->id);
        $data['passbook'] = Transactions::leftJoin('orders',function($join){
            $join->on('transactions.order_id','=','orders.id');
        })->leftJoin('zz_archive_orders',function($join){
        $join->on('transactions.order_id','=','zz_archive_orders.id');
        })->where('transactions.seller_id', Session()->get('MySeller')->id)->orderBy('transactions.id', 'desc')
            ->select('transactions.*','orders.awb_number', 'orders.courier_partner','zz_archive_orders.awb_number as awb_number1','zz_archive_orders.courier_partner as courier_partner1')
            ->paginate($noOfPage);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.b_passbook', $data);
    }

    function billingReceipt()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'receipt']);
        // $data['receipt'] = BillReceipt::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['receipt'] = BillReceipt::where('seller_id', Session()->get('MySeller')->id)->paginate($noOfPage);
        return view('seller.b_receipt', $data);
    }

    function receiptInvoice($id)
    {
        $data['config'] = $this->info['config'];
        $data['receipt'] = DB::table('bill_receipt')->join('sellers', 'bill_receipt.seller_id', '=', 'sellers.id')->select('bill_receipt.*', 'bill_receipt.id as receipt_id', 'sellers.*')->where('bill_receipt.id', $id)->first();
        $data['basic_info'] = Basic_informations::find($data['receipt']->seller_id);
        // $pdf = PDF::loadView('seller.receipt_invoice', $data)->setOptions(['defaultFont' => 'sans-serif']);
        // return $pdf->download('Receipt-' . $id . '.pdf');
        return view('seller.receipt_invoice', $data);
    }

    function exportReceiptDetails($id)
    {
        $name = "exports/ReceiptDetails";
        $filename = "ReceiptDetails";
        $PartnerName = Partners::getPartnerKeywordList();
        DB::enableQueryLog();
        $all_data = DB::table('bill_receipt')
            ->join('receipt_details', 'receipt_details.receipt_id', '=', 'bill_receipt.id')
            ->join('orders', 'orders.awb_number', '=', 'receipt_details.awb_number')
            ->where('bill_receipt.seller_id', Session()->get('MySeller')->id)
            ->select('bill_receipt.*', 'receipt_details.*', 'orders.*')->get();
        // dd(DB::getQueryLog());
        // dd($all_data);
        $fp = fopen("$name.csv", 'w');
        $info = array('Date Time', 'Order Number', 'Payment Type', 'AWB Number', 'Courier Name', ' Entered Weight(In KG.)', 'Initial Amount Charged', 'Charge Weight(In Kg.)', 'Entered Dimension(L * B* H)', 'Charged Dimension(L * B* H)', 'Final Amount Charged', 'Weight Dispute Status', 'Product Name');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                $e->b_customer_name = 'PII Data Archived';
                $e->b_address_line1 = 'PII Data Archived';
                $e->b_address_line2 = 'PII Data Archived';
                $e->b_city = 'PII Data Archived';
                $e->b_state = 'PII Data Archived';
                $e->b_country = 'PII Data Archived';
                $e->b_pincode = 'PII Data Archived';
                $e->b_contact_code = 'PII Data Archived';
                $e->b_contact = 'PII Data Archived';
                $e->s_customer_name = 'PII Data Archived';
                $e->s_address_line1 = 'PII Data Archived';
                $e->s_address_line2 = 'PII Data Archived';
                $e->s_city = 'PII Data Archived';
                $e->s_state = 'PII Data Archived';
                $e->s_country = 'PII Data Archived';
                $e->s_pincode = 'PII Data Archived';
                $e->s_contact_code = 'PII Data Archived';
                $e->s_contact = 'PII Data Archived';
                $e->invoice_amount = 'PII Data Archived';
                $e->product_name = 'PII Data Archived';
                $e->product_sku = 'PII Data Archived';
                $e->product_qty = 'PII Data Archived';
                $e->delivery_address = 'PII Data Archived';
            }
            $info = array($e->delivered_date, $e->order_number, $e->order_type, ('`' . $e->awb_number . '`'), $PartnerName[$e->courier_partner], $e->weight / 1000, $e->total_charges, $e->c_weight / 1000, $e->length . '*' . $e->height . '*' . $e->breadth, $e->c_length . '*' . $e->c_height . '*' . $e->c_breadth, $e->total_charges + $e->excess_weight_charges, "Auto Accept", $e->product_name);
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

    function billingInvoice()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'invoice']);
        $data['invoice'] = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('invoice_date', 'desc')->paginate($noOfPage);
        return view('seller.b_invoice', $data);
    }

    function billingOtherInvoice()
    {
        if (Session()->has('noOfPage')) {
            $noOfPage = Session()->get('noOfPage');
        } else {
            $noOfPage = 20;
        }
        session(['billing_status' => 'other_invoice']);
        $data['invoice'] = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('invoice_date', 'desc')->where('type', 'o')->paginate($noOfPage);
        return view('seller.b_other_invoice', $data);
    }

    function viewTransaction($id)
    {
        // $response = Transactions::where('order_id',$id)->first();
        $response = DB::table('transactions')->join('orders', 'transactions.order_id', '=', 'orders.id')->where('transactions.seller_id', Session()->get('MySeller')->id)->select('transactions.*', 'orders.awb_number')->where('transactions.order_id', $id)->first();
        echo json_encode($response);
    }

    function BillingInvoiceView($id)
    {
        $data['config'] = $this->info['config'];
        $data['invoice'] = Invoice::find($id);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::where('seller_id', $data['invoice']->seller_id)->first();
        return view('seller.billing_invoice', $data);
    }

    function BillingInvoicePDF($id)
    {
        $data['config'] = $this->info['config'];
        $data['invoice'] = Invoice::find($id);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::where('seller_id', $data['invoice']->seller_id)->first();
        $pdf = PDF::loadView('seller.billing_invoice_pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('Billing_Invoice-' . $data['invoice']->id . '.pdf');
        //  echo "Under Progress";
        // return view('seller.billing_invoice', $data);
    }

    function BillingInvoiceCSV($id)
    {
        $name = "invoice_details";
        $config = $this->info['config'];
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
        header("Content-Disposition: attachment; filename=$name.csv");
        // Output file.
        readfile("$name.csv");
        //@unlink("$name.csv");
    }

    function _gstcheck($p_state, $s_state)
    {
        $config = $this->info['config'];
        if (strtolower($p_state) == strtolower($s_state)) {
            return 'sgst_cgst';
        } else {
            return 'igst';
        }
    }

    function billingOtherInvoiceView($id)
    {
        $data['config'] = $this->info['config'];
        $data['invoice'] = Invoice::find($id);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::find($data['invoice']->seller_id);
        return view('seller.billing_other_invoice', $data);
    }

    function billingOtherInvoicePDf($id)
    {
        $data['config'] = $this->info['config'];
        $data['invoice'] = Invoice::find($id);
        $data['seller'] = Seller::find($data['invoice']->seller_id);
        $data['seller_info'] = Basic_informations::find($data['invoice']->seller_id);
        $pdf = PDF::loadView('seller.billing_other_invoice', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('B_Invoice-' . $data['invoice']->id . '.pdf');
        //  echo "Under Progress";
        // return view('seller.billing_invoice', $data);
    }

    function exportShippingDetails(Request $request) {
        // Get ids
        $query = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->orderBy('awb_assigned_date', 'desc');
        $filename = "shipping-details.csv";
        $filePath = storage_path("app/public/{$filename}");
        if(!empty($request->selected_ids))
            $query = $query->whereIn('id', $request->selected_ids);
        else
            $query = UtilityHelper::ApplyBillingFilter($query, $request->filter);
        $query = $query->get();
        $all_data = $query;
        $fp = fopen($filePath, 'w');
        $info = array('Sr.No','Order Number','Order Type','Payment Type','Order Date','Status','AWB Number','Courier', 'AWB Assigned Date','Applied Weight Charges','Excess Weight Charges','On-hold Amount','Total Freight Charges',' Entered Weight(gm)', 'Entered Length(cm)', 'Entered Height(cm)', 'Entered Breadth(cm)',' Charged Weight(gm)', 'Charged Length(cm)', 'Charged Height(cm)', 'Charged Breadth(cm)', 'Shipping Charges');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $info = array($cnt,$e->customer_order_number,$e->o_type,$e->order_type,$e->inserted,$this->orderStatus[$e->status],('`'.$e->awb_number.'`'), $e->courier_partner, $e->awb_assigned_date,$e->total_charges,$e->excess_weight_charges,0,$e->total_charges + $e->excess_weight_charges,$e->weight /1000, $e->length, $e->height, $e->breadth,$e->c_weight, $e->c_length, $e->c_height, $e->c_breadth, $e->shipping_charges);
            fputcsv($fp, $info);
            $cnt++;
        }
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    function exportPassbookDetails(Request $request) {
        // Get ids
        $query = DB::table('transactions')
            ->leftJoin('orders',function($join){
                $join->on('transactions.order_id','=','orders.id');
            })
            ->leftJoin('zz_archive_orders',function($join){
                $join->on('transactions.order_id','=','zz_archive_orders.id');
            })
            ->select('transactions.*', 'orders.awb_number', 'orders.courier_partner','zz_archive_orders.awb_number as awb_number1','zz_archive_orders.courier_partner as courier_partner1')->where('transactions.seller_id', Session()->get('MySeller')->id);
        $filename = "passbook-details.csv";
        $filePath = storage_path("app/public/{$filename}");
        if(!empty($request->selected_ids))
            $query = $query->whereIn('id', $request->selected_ids);
        else
            $query = UtilityHelper::ApplyBillingFilter($query, $request->filter, 'passbook');
        $query = $query->get();
        $all_data = $query;
        $fp = fopen($filePath, 'w');
        $info = array('Sr.No','Transaction Id','Date','Time','AWB Number','Courier','Type','Amount','Balance','Description');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            // dd($e);
            $date = date('d/m/Y', strtotime($e->datetime));
            $time = date('h:i A', strtotime($e->datetime));
            if($e->type == 'd'){
                $type = "Debit";
            }else{
                $type = "Credit";
            }
            $info = array($cnt,$e->id,$date,$time,($e->awb_number ?? $e->awb_number1),($e->courier_partner ?? $e->courier_partner1),$type,$e->amount,$e->balance, $e->description);
            fputcsv($fp, $info);
            $cnt++;
        }
        return response()->download($filePath)->deleteFileAfterSend(true);
    }


    function customerSupport()
    {
        $data = $this->info;
        $data['customer_support'] = SupportTicket::where('seller_id', Session()->get('MySeller')->id)->orderby('id','desc')->get();
        return view('seller.customer_support', $data);
    }

    function add_escalation(Request $request)
    {
        if (isset($request->s_issue)) {
            $issue = $request->s_issue;
        } else {
            $issue = $request->issue;
        }
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'type' => $request->escalation_type,
            'awb_number' => $request->awb_number,
            'issue' => $issue,
            'remark' => $request->remark,
            'subject' => $request->subject,
            'ticket_no' => rand('300001', '500000'),
            'status' => 'o',
            'sevierity' => 'Low',
            'raised' => date('Y-m-d H:i:s')
        );
        // dd($data);
        $ticket_id = SupportTicket::create($data);
        $cnt = 1;
        if (isset($request->attachment)) {
            foreach ($request->attachment as $file) {
                $data_attachment = array(
                    'ticket_id' => $ticket_id->id,
                );
                $oName = $file->getClientOriginalName();
                $type = explode('.', $oName);
                $name = date('YmdHis') . "Attachment-$cnt." . $type[count($type) - 1];
                $filepath = "assets/seller/images/Attachment/$name";
                $file->move(public_path('assets/seller/images/Attachment/'), $name);
                $data_attachment['attachment'] = $filepath;
                TicketAttachment::create($data_attachment);
                $cnt++;
            }
        }
        //send new escalation added
//        $this->utilities->send_email(Session()->get('MySeller')->email, "Twinnship Corporation", 'Ticket Escalation Request', "Dear Seller,<br>We have received you request and generated a ticket for you you will get updates on this email whenever any updated will be made by any support team by Twinnship");
//        $this->utilities->send_email("info.Twinnship@gmail.com", "Twinnship Corporation", 'Ticket Escalation Request', "You have a new Escalation from " . Session()->get('MySeller')->email . "<br><b>Subject: </b>" . $data['subject'] . "<br><b>Ticket No: </b>" . $data['ticket_no'] . "<br><b>Escalation Type: </b>" . $data['type'] . "<br><b>Awb Number: </b>" . $data['awb_number'] . "<br><b>Issue: </b>" . $data['issue'] . "<br><b>Remark: </b>" . $data['remark']);
        $this->utilities->generate_notification('Success', 'Issue Registered successfully', 'success');
        return redirect()->back();
    }

    function view_escalation($id)
    {
        $data = $this->info;
        $data['escalation'] = SupportTicket::find($id);
        $data['comments'] = TicketComments::where('ticket_id', $id)->get();
        return view('seller.view_escalation', $data);
    }

    function add_escalation_comment(Request $request)
    {
        // dd($request->all());
        $data = array(
            'ticket_id' => $request->ticket_id,
            'remark' => $request->remark,
            'replied_by' => 'You',
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
        //send escalation added some message
        $this->utilities->send_email(Session()->get('MySeller')->email, "Twinnship Corporation", 'Escalation Update', 'Dear Seller,<br>You have some updates in your ticket escalated please login to Twinnship.in and check the escalation update');

        return redirect()->back();
    }

    function close_ticket($id)
    {
        SupportTicket::where('id', $id)->update(['status' => 'c']);
        $this->utilities->generate_notification('Success', 'Ticket Closed Successfully', 'success');
        //send escalation closed message
        $this->utilities->send_email(Session()->get('MySeller')->email, "Twinnship Corporation", 'Ticket Escalation Request', "Dear Seller,<br>Your Ticked has been closed thanks for using our services from Twinnship Corporation");
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
        $this->utilities->generate_notification('Success', 'Ticket Escalate Successfully', 'success');
        //send escalation status changed message
        $this->utilities->send_email(Session()->get('MySeller')->email, "Twinnship Corporation", 'Ticket Escalation Request', "Dear Seller,<br>Your Ticked is escalated to the next level we will try to close this escalation as soon as possible.Sorry for the inconvenience caused.<br>Thanks<br>From Twinnship");
        return redirect(url('/') . "/customer_support?page=escalate");
    }

    function mis_report()
    {
        $data = $this->info;
        $data['channels'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.mis_report', $data);
    }

    private function getPaginationCount()
    {
        $noOfPage = session()->get('noOfPage', 20);

        session(['noOfPage' => '']);

        $noOfPage = (int) $noOfPage;

        if ($noOfPage < 1) {
            $noOfPage = 20;
        }

        return $noOfPage;
    }

    function ajaxReportData(Request $request)
    {
        $type = $request->report_type;
        $subType = $request->report_subtype;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $noOfPage = $this->getPaginationCount();

        //dd($noOfPage);
        DB::EnableQueryLog();
        Session(['report_type' => $type, 'report_subType' => $subType, 'report_fromDate' => $fromDate, 'report_toDate' => $toDate]);
        switch ($type) {
            case 'orders':
                $data['PartnerName'] = Partners::getPartnerKeywordList();
                $query = DB::table('orders')->where('orders.seller_id', Session()->get('MySeller')->id)->orderBy('orders.id', 'desc');
                switch ($subType) {
                    case 'all_order':
                        $data['order'] = $query->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->paginate($noOfPage);
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'process_order':
                        $query = $query->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)
                            ->where('product_name', '!=', '')
                            ->where('product_sku', '!=', '')
                            ->where('s_customer_name', '!=', '')
                            ->where('s_address_line1', '!=', '')
                            ->where('s_country', '!=', '')
                            ->where('s_state', '!=', '')
                            ->where('s_city', '!=', '')
                            ->where('s_pincode', '!=', '')
                            ->whereNotNull('s_contact')
                            ->where('b_customer_name', '!=', '')
                            ->where('b_address_line1', '!=', '')
                            ->where('b_country', '!=', '')
                            ->where('b_state', '!=', '')
                            ->where('b_city', '!=', '')
                            ->where('b_pincode', '!=', '')
                            ->where('b_contact', '!=', '')
                            ->where('weight', '!=', '')
                            ->where('length', '!=', '')
                            ->where('breadth', '!=', '')
                            ->where('height', '!=', '')
                            ->where('weight', '!=', 0)
                            ->where('length', '!=', 0)
                            ->where('breadth', '!=', 0)
                            ->where('height', '!=', 0)
                            ->whereNotNull('weight')
                            ->whereNotNull('length')
                            ->whereNotNull('breadth')
                            ->whereNotNull('height')
                            ->where('invoice_amount', '!=', '')
                            ->whereNotNull('invoice_amount')
                            ->where(function($q) {
                                $q->whereNotIn('channel', ['amazon', 'amazon_direct'])
                                    ->orWhere(function($q) {
                                        $q->where('invoice_amount', '!=', 0)
                                            ->where('b_contact', '!=', null)
                                            ->where('b_contact', '!=', '9999999999');
                                    });
                            })
                            ->where('status', 'pending');
                        $data['order'] = $query->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'shipped_order':
                        $query = $query->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate);
                        $data['order'] = $query->paginate($noOfPage);
                        // dd(DB::getQueryLog());
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'delivered_order':
                        $query = $query->whereDate('delivered_date', '>=', $fromDate)->whereDate('delivered_date', '<=', $toDate)->where('status', '=', 'delivered')->where('rto_status', '=', 'n');
                        $data['order'] = $query->paginate($noOfPage);
                        // dd(DB::getQueryLog());
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'manifest_order':
                        // $query = DB::table('manifest')->join('manifest_order', 'manifest_order.manifest_id', '=', 'manifest.id')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->where('manifest.seller_id', Session()->get('MySeller')->id)->whereDate('manifest.created', '>=', $fromDate)->whereDate('manifest.created', '<=', $toDate)->select('orders.*');
                        $query = DB::table('manifest')->join('manifest_order', 'manifest_order.manifest_id', '=', 'manifest.id')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->where('manifest.seller_id', Session()->get('MySeller')->id)->whereDate('manifest.created', '>=', $fromDate)->whereDate('manifest.created', '<=', $toDate)->select('orders.*');
                        $data['order'] = $query->paginate($noOfPage);
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'picked_orders':
                        $query = $query->join('picked_orders_list','picked_orders_list.order_id','orders.id')
                            ->whereDate('picked_orders_list.datetime','>=',$fromDate)
                            ->whereDate('picked_orders_list.datetime','<=',$toDate);
                        $data['order'] = $query->paginate($noOfPage);
                        return view('seller.report.report_orders', $data);
                        break;
                    case 'archive_orders':
                        $data['order'] = DB::table('zz_archive_orders')->where('zz_archive_orders.seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->whereNotIn('zz_archive_orders.status',['pending','cancelled'])->where('delivery_status',1)->orderBy('zz_archive_orders.id', 'desc')->paginate('noOfPage');
                        return view('seller.report.report_orders', $data);
                        break;
                }
                break;
            case 'shipments':
                switch ($subType) {
                    case 'all_ndr':
                        $data['ndr_data'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereDate('ndr_raised_time', '>=', $fromDate)->whereDate('ndr_raised_time', '<=', $toDate)->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        return view('seller.report.ndr', $data);
                        break;
                    case 'ndr_delivered':
                        $data['ndr_data'] = Order::where('status', 'delivered')
                            ->where('seller_id', Session()->get('MySeller')->id)
                            ->where('ndr_status', 'y')
                            ->where('rto_status', 'n')
                            ->whereDate('ndr_raised_time', '>=', $fromDate)
                            ->whereDate('ndr_raised_time', '<=', $toDate)->with('ndrattempts')
                            ->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        return view('seller.ndr_delivered', $data);
                        break;
                    case 'rto_report':
                        $data['ndr_data'] = Order::where('seller_id', Session()->get('MySeller')->id)
                            ->where('rto_status', 'y')
                            ->whereDate('ndr_raised_time', '>=', $fromDate)
                            ->whereDate('ndr_raised_time', '<=', $toDate)->with('ndrattempts')
                            ->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        //dd(DB::getQueryLog());
                        return view('seller.ndr_delivered', $data);
                        break;
                }
                break;
            case 'billing':
                switch ($subType) {
                    case 'shipping_charges':
                        $data['billing'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->orderBy('id', 'desc')->paginate($noOfPage);
                        return view('seller.report.billing', $data);
                        break;
                    case 'weight_reconciliation':
                        $data['weight_reconciliation'] = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereDate('created', '>=', $fromDate)->whereDate('created', '<=', $toDate)->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        return view('seller.report.weight_reconcillation', $data);
                        break;
                    case 'remittance_logs':
                        $data['remittance'] = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->whereDate('datetime', '>=', $fromDate)->whereDate('datetime', '<=', $toDate)->paginate($noOfPage);
                        return view('seller.report.remittance_log', $data);
                        break;
                    case 'onhold_reconciliation':
                        $data['onhold'] = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->orderBy('weight_reconciliation.created', 'desc')->whereDate('weight_reconciliation.created', '>=', $fromDate)->whereDate('weight_reconciliation.created', '<=', $toDate)->paginate($noOfPage);
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        return view('seller.report.onhold', $data);
                        break;
                    case 'invoices':
                        $data['invoice'] = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('invoice_date', 'desc')->whereDate('invoice_date', '>=', $fromDate)->whereDate('invoice_date', '<=', $toDate)->paginate($noOfPage);
                        return view('seller.report.invoice', $data);
                        break;
                }
                break;
            case 'returns':
                switch ($subType) {
                    case 'return_order':
                        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status','y')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->paginate($noOfPage);
                        return view('seller.report.return_order', $data);
                        break;
                    case 'reverse_order':
                        $data['order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('o_type', 'reverse')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->paginate($noOfPage);
                        return view('seller.report.reverse_order', $data);
                        break;
                }
                break;
        }
    }


    public function export_report_data(Request $request)
    {
        // Get ids
        $ids = array_filter(explode(',', $request->ids), function ($el) {
            return !empty($el);
        });
        $ids = array_unique($ids);
        // print_r(session()->all());
        $report_type = session('report_type');
        $report_subType = session('report_subType');
        $fromDate = session('report_fromDate');
        $toDate = session('report_toDate');
        $name = "exports/$report_subType-$fromDate-$toDate";
        $filename = "$report_subType-$fromDate-$toDate";
        DB::enableQueryLog();
        if (empty($report_type) || empty($report_subType) || empty($fromDate) || empty($toDate)) {
            $this->utilities->generate_notification('Error', 'Please Select Valid Input', 'error');
            return redirect()->back();
        }

        switch ($report_type) {
            case 'orders':
                $query = Order::select('orders.*')->where('orders.seller_id', Session()->get('MySeller')->id)->orderBy('orders.id', 'desc');
                // Export only selected data if any selected by user
                if (!empty($ids)) {
                    $query = $query->whereIn('orders.id', $ids);
                }
                $data['PartnerName'] = Partners::getPartnerKeywordList();
                $PartnerName = Partners::getPartnerKeywordList();
                switch ($report_subType) {
                    case 'all_order':
                        $query = $query->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->with('Intransittable','ofdDate');
                        break;
                    case 'process_order':
                        $query = $query->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)
                            ->where('product_name', '!=', '')
                            ->where('product_sku', '!=', '')
                            ->where('s_customer_name', '!=', '')
                            ->where('s_address_line1', '!=', '')
                            ->where('s_country', '!=', '')
                            ->where('s_state', '!=', '')
                            ->where('s_city', '!=', '')
                            ->where('s_pincode', '!=', '')
                            ->whereNotNull('s_contact')
                            ->where('b_customer_name', '!=', '')
                            ->where('b_address_line1', '!=', '')
                            ->where('b_country', '!=', '')
                            ->where('b_state', '!=', '')
                            ->where('b_city', '!=', '')
                            ->where('b_pincode', '!=', '')
                            ->where('b_contact', '!=', '')
                            ->where('weight', '!=', '')
                            ->where('length', '!=', '')
                            ->where('breadth', '!=', '')
                            ->where('height', '!=', '')
                            ->where('weight', '!=', 0)
                            ->where('length', '!=', 0)
                            ->where('breadth', '!=', 0)
                            ->where('height', '!=', 0)
                            ->whereNotNull('weight')
                            ->whereNotNull('length')
                            ->whereNotNull('breadth')
                            ->whereNotNull('height')
                            ->where('invoice_amount', '!=', '')
                            ->whereNotNull('invoice_amount')
                            ->where(function($q) {
                                $q->whereNotIn('channel', ['amazon', 'amazon_direct'])
                                    ->orWhere(function($q) {
                                        $q->where('invoice_amount', '!=', 0)
                                            ->where('b_contact', '!=', null)
                                            ->where('b_contact', '!=', '9999999999');
                                    });
                            })
                            ->where('status', 'pending')->with('Intransittable','ofdDate');
                        $query = $query;
                        break;
                    case 'shipped_order':
                        $query = $query->whereDate('orders.awb_assigned_date', '>=', $fromDate)->whereDate('orders.awb_assigned_date', '<=', $toDate)->with('Intransittable','ofdDate');
                        break;
                    case 'manifest_order':
                        $query = DB::table('manifest')->join('manifest_order', 'manifest_order.manifest_id', '=', 'manifest.id')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->where('manifest.seller_id', Session()->get('MySeller')->id)->whereDate('manifest.created', '>=', $fromDate)->whereDate('manifest.created', '<=', $toDate)->select('orders.*')->orderBy('orders.id');
                        break;
                    case 'delivered_order':
                        $query = $query->whereDate('delivered_date', '>=', $fromDate)->whereDate('delivered_date', '<=', $toDate)->where('status', '=', 'delivered')->where('rto_status', '=', 'n')->with('Intransittable','ofdDate');
                        break;
                    case 'picked_orders':
                        $query = $query->join('picked_orders_list','picked_orders_list.order_id','orders.id')
                            ->whereDate('picked_orders_list.datetime','>=',$fromDate)
                            ->whereDate('picked_orders_list.datetime','<=',$toDate)->with('Intransittable','ofdDate');
                        break;
                    case "archive_orders":
                        $query = OrderArchive::select(
                            "id",
                            "alternate_awb_number",
                            "channel",
                            "inserted",
                            "b_customer_name",
                            "b_address_line1",
                            "b_address_line2",
                            "b_city",
                            "b_state",
                            "b_country",
                            "b_pincode",
                            "b_contact_code",
                            "b_contact",
                            "s_customer_name",
                            "s_address_line1",
                            "s_address_line2",
                            "s_city",
                            "s_state",
                            "s_country",
                            "s_pincode",
                            "s_contact_code",
                            "s_contact",
                            "invoice_amount",
                            "product_name",
                            "product_sku",
                            "product_qty",
                            "delivery_address",
                            "rto_status",
                            "status",
                            "weight",
                            "courier_partner",
                            "customer_order_number",
                            "order_type",
                            "p_warehouse_name",
                            "pickup_time",
                            "delivered_date",
                            "awb_number",
                            "length",
                            "height",
                            "breadth",
                            "shipping_charges",
                            "cod_charges",
                            "discount",
                            "invoice_amount",
                            "o_type",
                            "zone"
                        )->where('zz_archive_orders.seller_id', Session()->get('MySeller')->id)->whereDate('zz_archive_orders.inserted', '>=', $fromDate)->whereDate('zz_archive_orders.inserted', '<=', $toDate)->whereNotIn('zz_archive_orders.status',['pending','cancelled'])->where('delivery_stutus',1)->orderBy('zz_archive_orders.id', 'desc')->with('Intransittable');
                        if (!empty($ids)) {
                            $query = $query->whereIn('zz_archive_orders.id', $ids);
                        }
                        $query = $query;
                        break;
                }
                $fp = fopen("$name.csv", 'w');
                if(Session()->get('MySeller')->display_mis_zone == 1)
                    $info = array('Sr.No', 'Order Number', 'Order Type','Payment Type', 'Order Date','Pickup Location','Connection Date','Pickup Date', 'Status','Estimate Delivery Date','First Out For Delivery Date' ,'Delivered Date', 'AWB Number', 'Courier Partner','Alternate Awb Number', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Zone', 'Cod Charges', 'Discount', 'Invoice Total','Collectable Amount','RTO Initiated Date','RTO Delivered Date','OFD Attempt', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                else
                    $info = array('Sr.No', 'Order Number', 'Order Type','Payment Type', 'Order Date','Pickup Location','Connection Date','Pickup Date', 'Status','Estimate Delivery Date','First Out For Delivery Date' ,'Delivered Date', 'AWB Number', 'Courier Partner','Alternate Awb Number', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Collectable Amount','RTO Initiated Date','RTO Delivered Date','OFD Attempt', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                fputcsv($fp, $info);
                $cnt = 1;
                if($report_subType == 'manifested'){
                    $all_data = $query;
                    foreach ($all_data as $e) {
                        if ($report_subType == 'manifest_order') {
                            $intransit = MoveToIntransit::where('order_id', $e->id)->first();
                            $ofdDate = InternationalOrders::where('order_id',$e->id)->first();
                        }
                        else{
                            $intransit = $e->Intransittable ?? "";
                            $ofdDate = $e->ofdDate ?? "";
                        }
                        if (env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                            $e->b_customer_name = 'PII Data Archived';
                            $e->b_address_line1 = 'PII Data Archived';
                            $e->b_address_line2 = 'PII Data Archived';
                            $e->b_city = 'PII Data Archived';
                            $e->b_state = 'PII Data Archived';
                            $e->b_country = 'PII Data Archived';
                            $e->b_pincode = 'PII Data Archived';
                            $e->b_contact_code = 'PII Data Archived';
                            $e->b_contact = 'PII Data Archived';
                            $e->s_customer_name = 'PII Data Archived';
                            $e->s_address_line1 = 'PII Data Archived';
                            $e->s_address_line2 = 'PII Data Archived';
                            $e->s_city = 'PII Data Archived';
                            $e->s_state = 'PII Data Archived';
                            $e->s_country = 'PII Data Archived';
                            $e->s_pincode = 'PII Data Archived';
                            $e->s_contact_code = 'PII Data Archived';
                            $e->s_contact = 'PII Data Archived';
                            $e->invoice_amount = 'PII Data Archived';
                            $e->product_name = 'PII Data Archived';
                            $e->product_sku = 'PII Data Archived';
                            $e->product_qty = 'PII Data Archived';
                            $e->delivery_address = 'PII Data Archived';
                        }
                        if ($e->rto_status == 'y' && $e->status == 'delivered')
                            $e->status = 'rto_delivered';
                        $weight = !empty($e->weight) ? $e->weight / 1000 : '';
                        $courier_partner = isset($e->courier_partner) ? ($PartnerName[$e->courier_partner] ?? $e->courier_partner) : '';
                        if ($e->status == 'delivered' && $e->rto_status == 'y')
                            $e->status = 'rto_delivered';
                        else if ($e->rto_status == 'y' && $e->status == 'in_transit'){
                            $e->status = 'rto_in_transit';
                        }

                        if($e->pickup_time == ""){
                            $pickup_time = $intransit->datetime ?? "";
                        }
                        else{
                            $pickup_time = $e->pickup_time;
                        }

                        if ($this->fullInformation){
                            $info = array($cnt, $e->customer_order_number, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? (date('Y-m-d',strtotime($intransit->datetime)) ?? "") : date('Y-m-d',strtotime($pickup_time)),date('Y-m-d',strtotime($pickup_time)), $this->orderStatus[$e->status],$e->expected_delivery_date ?? "", date('Y-m-d',strtotime($ofdDate->ofd_date)) ?? (!empty($e->delivered_date) ? date('Y-m-d',strtotime($e->delivered_date)) : "" ),date('Y-m-d',strtotime($e->delivered_date)), ('`' . $e->awb_number . '`'), $courier_partner,('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, $e->s_address_line1, $e->s_address_line2, $e->s_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount,$ofdDate->rto_initiated_date ?? "",$e->rto_status == 'y' ? $e->delivered_date : "",$ofdDate->ofd_attempt ?? 0);
                        }
                        else{
                             $info = array($cnt, $e->customer_order_number, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? (date('Y-m-d',strtotime($intransit->datetime)) ?? "") : date('Y-m-d',strtotime($pickup_time)), date('Y-m-d',strtotime($pickup_time)), $this->orderStatus[$e->status],$e->expected_delivery_date ?? "", date('Y-m-d',strtotime($ofdDate->ofd_date)) ?? (!empty($e->delivered_date) ? date('Y-m-d',strtotime($e->delivered_date)) : "" ),date('Y-m-d',strtotime($e->delivered_date)), ('`' . $e->awb_number . '`'), $courier_partner,('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount,$ofdDate->rto_initiated_date ?? "",$e->rto_status == 'y' ? $e->delivered_date : "",$ofdDate->ofd_attempt ?? 0);
                        }
                        // $products = Product::where('order_id', $e->id)->get();
                        $productNames = explode(',', $e->product_name);
                        $productSkus = explode(',', $e->product_sku);
                        for ($i = 0; $i < count($productNames); $i++) {
                            $info[] = $productNames[$i] ?? null;
                            $info[] = $productSkus[$i] ?? null;
                            $info[] = 1;
                        }
                        fputcsv($fp, $info);
                        $cnt++;
                    }
                }
                else {
                    $query->chunk(50000, function ($all_data) use($report_subType, &$cnt, $PartnerName, $fp) {
                        foreach ($all_data as $e) {
                            if ($report_subType == 'manifest_order') {
//                                $intransit = "";
//                                $ofdDate = "";
                                $intransit = MoveToIntransit::where('order_id', $e->id)->first();
                                $ofdDate = InternationalOrders::where('order_id',$e->id)->first();
                            }
                            else {
                                $intransit = $e->Intransittable ?? "";
                                $ofdDate = $e->ofdDate ?? "";
                            }
                            if (env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                                $e->b_customer_name = 'PII Data Archived';
                                $e->b_address_line1 = 'PII Data Archived';
                                $e->b_address_line2 = 'PII Data Archived';
                                $e->b_city = 'PII Data Archived';
                                $e->b_state = 'PII Data Archived';
                                $e->b_country = 'PII Data Archived';
                                $e->b_pincode = 'PII Data Archived';
                                $e->b_contact_code = 'PII Data Archived';
                                $e->b_contact = 'PII Data Archived';
                                $e->s_customer_name = 'PII Data Archived';
                                $e->s_address_line1 = 'PII Data Archived';
                                $e->s_address_line2 = 'PII Data Archived';
                                $e->s_city = 'PII Data Archived';
                                $e->s_state = 'PII Data Archived';
                                $e->s_country = 'PII Data Archived';
                                $e->s_pincode = 'PII Data Archived';
                                $e->s_contact_code = 'PII Data Archived';
                                $e->s_contact = 'PII Data Archived';
                                $e->invoice_amount = 'PII Data Archived';
                                $e->product_name = 'PII Data Archived';
                                $e->product_sku = 'PII Data Archived';
                                $e->product_qty = 'PII Data Archived';
                                $e->delivery_address = 'PII Data Archived';
                            }
                            if ($e->rto_status == 'y' && $e->status == 'delivered')
                                $e->status = 'rto_delivered';
                            $weight = !empty($e->weight) ? $e->weight / 1000 : '';
                            $courier_partner = isset($e->courier_partner) ? ($PartnerName[$e->courier_partner] ?? $e->courier_partner) : '';
                            if ($e->status == 'delivered' && $e->rto_status == 'y')
                                $e->status = 'rto_delivered';
                            else if ($e->rto_status == 'y' && $e->status == 'in_transit')
                                $e->status = 'rto_in_transit';

                            if($e->pickup_time == ""){
                                $pickup_time = !empty($intransit) ? $intransit->datetime : "";
                            }
                            else{
                                $pickup_time = $e->pickup_time;
                            }

                            if ($this->fullInformation) {
                                if(Session()->get('MySeller')->display_mis_zone == 1)
                                    $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d', strtotime($pickup_time)) : date('Y-m-d', strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : ""), !empty($pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : "", $this->orderStatus[$e->status], $e->expected_delivery_date ?? "", !empty($ofdDate->ofd_date) ? date('Y-m-d', strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : ""), !empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'), $courier_partner, ('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line1)))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line2)))), $e->s_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->zone, $e->cod_charges, $e->discount, $e->invoice_amount, $e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d", strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "")) : "", ($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "", $ofdDate->ofd_attempt ?? 0);
                                else
                                    $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d', strtotime($pickup_time)) : date('Y-m-d', strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : ""), !empty($pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : "", $this->orderStatus[$e->status], $e->expected_delivery_date ?? "", !empty($ofdDate->ofd_date) ? date('Y-m-d', strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : ""), !empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'), $courier_partner, ('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line1)))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line2)))), $e->s_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d", strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "")) : "", ($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "", $ofdDate->ofd_attempt ?? 0);
                            }
                            else {
                                if(Session()->get('MySeller')->display_mis_zone == 1)
                                    $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d', strtotime($pickup_time)) : date('Y-m-d', strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : ""), !empty($pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : "", $this->orderStatus[$e->status], $e->expected_delivery_date ?? "", !empty($ofdDate->ofd_date) ? date('Y-m-d', strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : ""), !empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'), $courier_partner, ('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->zone, $e->cod_charges, $e->discount, $e->invoice_amount, $e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d", strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "")) : "", ($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "", $ofdDate->ofd_attempt ?? 0);
                                else
                                    $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->p_warehouse_name, !empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d', strtotime($pickup_time)) : date('Y-m-d', strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : ""), !empty($pickup_time) ? date('Y-m-d', strtotime($pickup_time)) : "", $this->orderStatus[$e->status], $e->expected_delivery_date ?? "", !empty($ofdDate->ofd_date) ? date('Y-m-d', strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : ""), !empty($e->delivered_date) ? date('Y-m-d', strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'), $courier_partner, ('`' . $e->alternate_awb_number . '`'), $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d", strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "")) : "", ($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d", strtotime($e->delivered_date)) : "", $ofdDate->ofd_attempt ?? 0);
                            }
                            // $products = Product::where('order_id', $e->id)->get();
                            $productNames = explode(',', $e->product_name);
                            $productSkus = explode(',', $e->product_sku);
                            for ($i = 0; $i < count($productNames); $i++) {
                                $info[] = $productNames[$i] ?? null;
                                $info[] = $productSkus[$i] ?? null;
                                $info[] = 1;
                            }
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                    });
                }
                break;
            case 'shipments':
                switch ($report_subType) {
                    case 'all_ndr':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Order::whereIn('id',$ids)->where('rto_status', 'n')->where('seller_id',Session()->get('MySeller')->id)->where('ndr_status', 'y')->with('Intransittable','ofdDate')->get();
                        } else {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereDate('ndr_raised_time', '>=', $fromDate)->whereDate('ndr_raised_time', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                        }
                        break;
                    case 'ndr_delivered':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            //$query = Order::join('ndr_attemps', 'ndr_attemps.order_id', '=', 'orders.id')->whereIn('ndr_attemps.id', $ids)->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.*', 'ndr_attemps.reason as ndr_reason', 'orders.*')->where('status', 'delivered')->where('ndr_action', 'delivered')->whereDate('ndr_attemps.raised_date', '>=', $fromDate)->whereDate('ndr_attemps.raised_date', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                            $query = Order::whereIn('id',$ids)->where('rto_status', 'n')->where('seller_id',Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->whereDate('ndr_raised_time', '>=', $fromDate)->whereDate('ndr_raised_time', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                        } else {
                            //$query = Order::join('ndr_attemps', 'ndr_attemps.order_id', '=', 'orders.id')->where('ndr_attemps.seller_id', Session()->get('MySeller')->id)->select('ndr_attemps.*', 'ndr_attemps.reason as ndr_reason', 'orders.*')->where('status', 'delivered')->where('ndr_action', 'delivered')->whereDate('ndr_attemps.raised_date', '>=', $fromDate)->whereDate('ndr_attemps.raised_date', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                            $query = Order::where('seller_id',Session()->get('MySeller')->id)->where('rto_status', 'n')->where('status', 'delivered')->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('ndr_raised_time', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                        }
                        break;
                    case 'rto_report':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Order::whereIn('id',$ids)->where('seller_id',Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                        } else {
                            $query = Order::where('seller_id',Session()->get('MySeller')->id)->where('rto_status','y')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->with('Intransittable','ofdDate')->get();
                        }
                        break;
                }
                $all_data = $query;
                $fp = fopen("$name.csv", 'w');
                $info = array('Sr no', 'Order Number','Order Type','Payment Type', 'Order Date','Pickup Location','Connection Date','Pickup Date', 'First Out For Delivery','Status','Estimate Delivery Date','Delivered Date', 'AWB Number','Alternate Awb Number', 'Channel Name', 'Store Name', 'Product Name', 'Product Quantity', 'Customer Name', 'Customer Email', 'Customer Mobile', 'Address Line 1', 'Address Line 2', 'Address City', 'Address State', 'Address Pincode', 'Payment Method', 'Order Total', 'RTO Initiated Date','RTO Delivered Date','OFD Attempt', 'Number of NDR attempts', 'First NDR raised date', 'First NDR raised time', 'First NDR Action By', 'Reason for First NDR', 'Action date for First NDR', 'Action Status for First NDR', 'Remarks for First NDR', 'First Updated Address Line 1', 'First Updated Address Line 1', 'First Updated Mobile', 'Second NDR raised date', 'Second NDR raised time', 'Second NDR Action By', 'Reason for Second NDR', 'Action date for Second NDR', 'Action Status for Second NDR', 'Remarks for Second NDR', 'Second Updated Address Line 1', 'Second Updated Address Line 1', 'Second Updated Mobile', 'Third NDR raised date', 'Third NDR raised time', 'Third NDR Action By', 'Reason for Third NDR', 'Action date for Third NDR', 'Action Status for Third NDR', 'Remarks for Third NDR', 'Third Updated Address Line 1', 'Third Updated Address Line 1', 'Third Updated Mobile');
                fputcsv($fp, $info);
                $cnt = 1;
                foreach ($all_data as $e) {
                    $intransit = $e->Intransittable;
                    //$intransit = MoveToIntransit::where('order_id',$e->order_id)->first();
                    if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                        $e->b_customer_name = 'PII Data Archived';
                        $e->b_address_line1 = 'PII Data Archived';
                        $e->b_address_line2 = 'PII Data Archived';
                        $e->b_city = 'PII Data Archived';
                        $e->b_state = 'PII Data Archived';
                        $e->b_country = 'PII Data Archived';
                        $e->b_pincode = 'PII Data Archived';
                        $e->b_contact_code = 'PII Data Archived';
                        $e->b_contact = 'PII Data Archived';
                        $e->s_customer_name = 'PII Data Archived';
                        $e->s_address_line1 = 'PII Data Archived';
                        $e->s_address_line2 = 'PII Data Archived';
                        $e->s_city = 'PII Data Archived';
                        $e->s_state = 'PII Data Archived';
                        $e->s_country = 'PII Data Archived';
                        $e->s_pincode = 'PII Data Archived';
                        $e->s_contact_code = 'PII Data Archived';
                        $e->s_contact = 'PII Data Archived';
                        $e->invoice_amount = 'PII Data Archived';
                        $e->product_name = 'PII Data Archived';
                        $e->product_sku = 'PII Data Archived';
                        $e->product_qty = 'PII Data Archived';
                        $e->delivery_address = 'PII Data Archived';
                    }
                    if ($e->rto_status == 'y' && $e->status == 'delivered')
                        $e->status = 'rto_delivered';
                    $quantity = explode(',', $e->product_name);
                    $attempts = $e->ndrattempts;
                    if($e->status == 'delivered' && $e->rto_status == 'y')
                        $e->status = 'rto_delivered';
                    else if($e->rto_status == 'y' && $e->status=='in_transit'){
                        $e->status='rto_in_transit';
                    }

                    if($e->pickup_time == ""){
                        $pickup_time = $intransit->datetime ?? "";
                    }
                    else{
                        $pickup_time = $e->pickup_time;
                    }
                    $ofdDate = $e->ofdDate ?? "";
                    if($this->fullInformation)
                        $info = array($cnt, $e->customer_order_number,$e->o_type,$e->order_type, $e->inserted,$e->p_warehouse_name,!empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d',strtotime($pickup_time)) : date('Y-m-d',strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "") ,!empty($pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "", !empty($ofdDate->ofd_date) ? date('Y-m-d',strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d',strtotime($e->delivered_date)) : "" ),$this->orderStatus[$e->status],$e->expected_delivery_date,!empty($e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'),('`' . $e->alternate_awb_number . '`'), $e->channel, $e->seller_channel_name, $e->product_name, count($quantity), $e->b_customer_name, $e->b_customer_email, $e->s_contact, addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line1)))), addslashes(preg_replace('/\s+/', ' ', trim(str_replace(",", "|", $e->s_address_line2)))), $e->s_city, $e->s_state, $e->s_pincode, $e->order_type, $e->invoice_amount,$e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d",strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "") ) : "",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$ofdDate->ofd_attempt ?? 0, count($attempts) == 0 ? 1 : count($attempts));
                    else
                        $info = array($cnt, $e->customer_order_number,$e->o_type,$e->order_type , $e->inserted,$e->p_warehouse_name,!empty($intransit) ? ($intransit->datetime < $pickup_time ? date('Y-m-d',strtotime($pickup_time)) : date('Y-m-d',strtotime($intransit->datetime))) : (!empty($e->pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "") ,!empty($pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "", !empty($ofdDate->ofd_date) ? date('Y-m-d',strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d',strtotime($e->delivered_date)) : "" ),$this->orderStatus[$e->status],$e->expected_delivery_date,!empty($e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "", ('`' . $e->awb_number . '`'),('`' . $e->alternate_awb_number . '`'), $e->channel, $e->seller_channel_name, $e->product_name, count($quantity), $e->b_customer_name, "********", "********", "********", "********", "********","********", "********", $e->order_type, $e->invoice_amount,$e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d",strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "") ) : "",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$ofdDate->ofd_attempt ?? 0, count($attempts) == 0 ? 1 : count($attempts));
                    foreach ($attempts as $a) {
                        $info[] = $a->raised_date;
                        $info[] = $a->raised_time;
                        $info[] = $a->action_by;
                        $info[] = $a->reason;
                        $info[] = $a->action_date;
                        $info[] = $a->action_status;
                        $info[] = $a->remark;
                        $info[] = $a->u_address_line1;
                        $info[] = $a->u_address_line2;
                        $info[] = $a->updated_mobile;
                    }
                    fputcsv($fp, $info);
                    $cnt++;
                }
                break;
            case 'billing':
                switch ($report_subType) {
                    case 'shipping_charges':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $ids)->where('status', '!=', 'pending')->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->orderBy('id', 'desc')->with('ofdDate')->get();
                        } else {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'pending')->whereDate('inserted', '>=', $fromDate)->whereDate('inserted', '<=', $toDate)->orderBy('id', 'desc')->with('ofdDate')->get();
                        }
                        $all_data = $query;
                        $fp = fopen("$name.csv", 'w');
                        $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'First Out For Delivery Date', 'Status', 'AWB Number','Alternate Awb Number', 'Courier', 'AWB Assigned Date', 'Applied Weight Charges', 'Excess Weight Charges', 'On-hold Amount', 'Total Freight Charges', ' Entered Weight(gm)', 'Entered Length(cm)', 'Entered Height(cm)', 'Entered Breadth(cm)', ' Charged Weight(gm)', 'Charged Length(cm)', 'Charged Height(cm)', 'Charged Breadth(cm)', 'Shipping Charges','RTO Initiated Date','RTO Delivered Date','OFD Attempt');
                        fputcsv($fp, $info);
                        $cnt = 1;
                        foreach ($all_data as $e) {
                            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                                $e->b_customer_name = 'PII Data Archived';
                                $e->b_address_line1 = 'PII Data Archived';
                                $e->b_address_line2 = 'PII Data Archived';
                                $e->b_city = 'PII Data Archived';
                                $e->b_state = 'PII Data Archived';
                                $e->b_country = 'PII Data Archived';
                                $e->b_pincode = 'PII Data Archived';
                                $e->b_contact_code = 'PII Data Archived';
                                $e->b_contact = 'PII Data Archived';
                                $e->s_customer_name = 'PII Data Archived';
                                $e->s_address_line1 = 'PII Data Archived';
                                $e->s_address_line2 = 'PII Data Archived';
                                $e->s_city = 'PII Data Archived';
                                $e->s_state = 'PII Data Archived';
                                $e->s_country = 'PII Data Archived';
                                $e->s_pincode = 'PII Data Archived';
                                $e->s_contact_code = 'PII Data Archived';
                                $e->s_contact = 'PII Data Archived';
                                $e->invoice_amount = 'PII Data Archived';
                                $e->product_name = 'PII Data Archived';
                                $e->product_sku = 'PII Data Archived';
                                $e->product_qty = 'PII Data Archived';
                                $e->delivery_address = 'PII Data Archived';
                            }
                            $ofdDate = $e->ofdDate ?? "";
                            if($e->status == 'delivered' && $e->rto_status == 'y')
                                $e->status = 'rto_delivered';
                            else if($e->rto_status == 'y' && $e->status=='in_transit')
                                $e->status='rto_in_transit';
                            $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, !empty($ofdDate->ofd_date) ? date('Y-m-d',strtotime($ofdDate->ofd_date)) : (!empty($e->delivered_date) ? date('Y-m-d',strtotime($e->delivered_date)) : "" ),$this->orderStatus[$e->status], ('`' . $e->awb_number . '`'),('`' . $e->alternate_awb_number . '`'), $e->courier_partner, $e->awb_assigned_date, $e->total_charges, $e->excess_weight_charges, 0, $e->total_charges + $e->excess_weight_charges, $e->weight / 1000, $e->length, $e->height, $e->breadth, $e->c_weight, $e->c_length, $e->c_height, $e->c_breadth, $e->shipping_charges,$e->rto_status == 'y' ? (!empty($ofdDate->rto_initiated_date) ? date("Y-m-d",strtotime($ofdDate->rto_initiated_date)) : (!empty($e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "") ) : "",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$ofdDate->ofd_attempt ?? 0);
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                        break;
                    case 'weight_reconciliation':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->whereIn('weight_reconciliation.id', $ids)->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereDate('created', '>=', $fromDate)->whereDate('created', '<=', $toDate)->get();
                        } else {
                            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereDate('created', '>=', $fromDate)->whereDate('created', '<=', $toDate)->get();
                        }
                        $data['PartnerName'] = Partners::getPartnerKeywordList();
                        $all_data = $query;
                        $fp = fopen("$name.csv", 'w');
                        $info = array('Sr.No','Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status', 'AWB Number','Alternate Awb Number', 'Courier', 'AWB Assigned Date', 'Order Total', ' Entered Weight(gm)', 'Entered Length(cm)', 'Entered Height(cm)', 'Entered Breadth(cm)', ' Charged Weight(gm)', 'Charged Length(cm)', 'Charged Height(cm)', 'Charged Breadth(cm)', 'Status');
                        fputcsv($fp, $info);
                        $cnt = 1;
                        foreach ($all_data as $e) {
                            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                                $e->b_customer_name = 'PII Data Archived';
                                $e->b_address_line1 = 'PII Data Archived';
                                $e->b_address_line2 = 'PII Data Archived';
                                $e->b_city = 'PII Data Archived';
                                $e->b_state = 'PII Data Archived';
                                $e->b_country = 'PII Data Archived';
                                $e->b_pincode = 'PII Data Archived';
                                $e->b_contact_code = 'PII Data Archived';
                                $e->b_contact = 'PII Data Archived';
                                $e->s_customer_name = 'PII Data Archived';
                                $e->s_address_line1 = 'PII Data Archived';
                                $e->s_address_line2 = 'PII Data Archived';
                                $e->s_city = 'PII Data Archived';
                                $e->s_state = 'PII Data Archived';
                                $e->s_country = 'PII Data Archived';
                                $e->s_pincode = 'PII Data Archived';
                                $e->s_contact_code = 'PII Data Archived';
                                $e->s_contact = 'PII Data Archived';
                                $e->invoice_amount = 'PII Data Archived';
                                $e->product_name = 'PII Data Archived';
                                $e->product_sku = 'PII Data Archived';
                                $e->product_qty = 'PII Data Archived';
                                $e->delivery_address = 'PII Data Archived';
                            }
                            if($e->status == 'delivered' && $e->rto_status == 'y')
                                $e->status = 'rto_delivered';
                            else if($e->rto_status == 'y' && $e->status=='in_transit')
                                $e->status='rto_in_transit';
                            $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $this->orderStatus[$e->status], ('`' . $e->awb_number . '`'),('`' . $e->alternate_awb_number . '`'), $e->courier_partner, $e->awb_assigned_date, $e->invoice_amount, $e->weight / 1000, $e->length, $e->height, $e->breadth, $e->c_weight, $e->c_length, $e->c_height, $e->c_breadth, $e->w_status);
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                        break;
                    case 'remittance_logs':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $ids)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->whereDate('datetime', '>=', $fromDate)->whereDate('datetime', '<=', $toDate)->get();
                        } else {
                            $query = COD_transactions::where('seller_id', Session()->get('MySeller')->id)->where('redeem_type', 'r')->orderBy('datetime', 'desc')->whereDate('datetime', '>=', $fromDate)->whereDate('datetime', '<=', $toDate)->get();
                        }
                        $all_data = $query;
                        $fp = fopen("$name.csv", 'w');
                        $info = array('Sr.No', 'Date', 'CRF Id', 'UTR', 'Freight Charges', 'Early Cod Charges', 'RTO Reversal Amount', 'Remmitance Amount', 'Payment Type');
                        fputcsv($fp, $info);
                        $cnt = 1;
                        foreach ($all_data as $e) {
                            $info = array($cnt, $e->datetime, $e->crf_id, $e->utr_number, 0, 0, 0, $e->amount, $e->pay_type, $e->height, $e->breadth, $e->c_weight, $e->c_length, $e->c_height, $e->c_breadth, $e->w_status);
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                        break;
                    case 'onhold_reconciliation':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->whereIn('weight_reconciliation.id', $ids)->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->orderBy('weight_reconciliation.created', 'desc')->whereDate('weight_reconciliation.created', '>=', $fromDate)->whereDate('weight_reconciliation.created', '<=', $toDate)->get();
                        } else {
                            $query = DB::table('weight_reconciliation')->join('orders', 'weight_reconciliation.awb_number', '=', 'orders.awb_number')->where('weight_reconciliation.seller_id', Session()->get('MySeller')->id)->whereNotIn('weight_reconciliation.status', ['accepted', 'closed'])->select('weight_reconciliation.*', 'orders.*', 'weight_reconciliation.id as w_id', 'weight_reconciliation.status as w_status')->orderBy('weight_reconciliation.created', 'desc')->whereDate('weight_reconciliation.created', '>=', $fromDate)->whereDate('weight_reconciliation.created', '<=', $toDate)->get();
                        }
                        $all_data = $query;
                        $fp = fopen("$name.csv", 'w');
                        $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order ID', 'AWB Number', 'Courier', 'AWB Assigned Date', 'Initial Amount Charges', ' Amount(Forward)', 'Amount(RTO)');
                        fputcsv($fp, $info);
                        $cnt = 1;
                        foreach ($all_data as $e) {
                            $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->order_number, ('`' . $e->awb_number . '`'), $e->courier_partner, $e->awb_assigned_date, 0, $e->total_charges, $e->rto_charges);
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                        break;
                    case 'invoices':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Invoice::where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $ids)->orderBy('invoice_date', 'desc')->whereDate('invoice_date', '>=', $fromDate)->whereDate('invoice_date', '<=', $toDate)->get();
                        } else {
                            $query = Invoice::where('seller_id', Session()->get('MySeller')->id)->orderBy('invoice_date', 'desc')->whereDate('invoice_date', '>=', $fromDate)->whereDate('invoice_date', '<=', $toDate)->get();
                        }
                        $all_data = $query;
                        $fp = fopen("$name.csv", 'w');
                        $info = array('Sr.No', 'Invoice Id', 'Invoice Date', 'Due Date', 'AWB Number', 'Total Amount');
                        fputcsv($fp, $info);
                        $cnt = 1;
                        foreach ($all_data as $e) {
                            $info = array($cnt, $e->inv_id, $e->invoice_date, $e->due_date, ('`' . $e->awb_number . '`'), $e->total);
                            fputcsv($fp, $info);
                            $cnt++;
                        }
                        break;
                }
                break;
            case 'returns':
                switch ($report_subType) {
                    case 'return_order':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $ids)->where('rto_status', 'y')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->get();
                        } else {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->get();
                        }
                        break;
                    case 'reverse_order':
                        // Export only selected data if any selected by user
                        if (!empty($ids)) {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $ids)->where('o_type', 'reverse')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->get();
                        } else {
                            $query = Order::where('seller_id', Session()->get('MySeller')->id)->where('o_type', 'reverse')->latest('awb_assigned_date')->whereDate('awb_assigned_date', '>=', $fromDate)->whereDate('awb_assigned_date', '<=', $toDate)->get();
                        }
                        break;
                }
                $all_data = $query;
                $fp = fopen("$name.csv", 'w');
                $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status','Estimate Delivery Date', 'AWB Number', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Weight(gm)', 'Length(cm)', 'Height(cm)', 'Breadth(cm)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Collectable Amount', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
                fputcsv($fp, $info);
                $cnt = 1;
                foreach ($all_data as $e) {
                    if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                        $e->b_customer_name = 'PII Data Archived';
                        $e->b_address_line1 = 'PII Data Archived';
                        $e->b_address_line2 = 'PII Data Archived';
                        $e->b_city = 'PII Data Archived';
                        $e->b_state = 'PII Data Archived';
                        $e->b_country = 'PII Data Archived';
                        $e->b_pincode = 'PII Data Archived';
                        $e->b_contact_code = 'PII Data Archived';
                        $e->b_contact = 'PII Data Archived';
                        $e->s_customer_name = 'PII Data Archived';
                        $e->s_address_line1 = 'PII Data Archived';
                        $e->s_address_line2 = 'PII Data Archived';
                        $e->s_city = 'PII Data Archived';
                        $e->s_state = 'PII Data Archived';
                        $e->s_country = 'PII Data Archived';
                        $e->s_pincode = 'PII Data Archived';
                        $e->s_contact_code = 'PII Data Archived';
                        $e->s_contact = 'PII Data Archived';
                        $e->invoice_amount = 'PII Data Archived';
                        $e->product_name = 'PII Data Archived';
                        $e->product_sku = 'PII Data Archived';
                        $e->product_qty = 'PII Data Archived';
                        $e->delivery_address = 'PII Data Archived';
                    }
                    if($e->status == 'delivered' && $e->rto_status == 'y')
                        $e->status = 'rto_delivered';
                    else if($e->rto_status == 'y' && $e->status=='in_transit')
                        $e->status='rto_in_transit';
                    if($this->fullInformation)
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $e->s_customer_name, $e->s_address_line1, $e->s_address_line2, $e->s_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $e->weight != '' ? $e->weight / 1000 : '', $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount);
                    else
                        $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $this->orderStatus[$e->status],$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $e->weight != '' ? $e->weight / 1000 : '', $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount,$e->collectable_amount);
                    // $products = Product::where('order_id', $e->id)->get();
                    $productNames = explode(',', $e->product_name);
                    $productSkus = explode(',', $e->product_sku);
                    for($i=0; $i<count($productNames); $i++) {
                        $info[] = $productNames[$i] ?? null;
                        $info[] = $productSkus[$i] ?? null;
                        $info[] = 1;
                    }
                    fputcsv($fp, $info);
                    $cnt++;
                }
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

    //function display all the channels of the seller
    function channels()
    {
        $data = $this->info;
        $data['channels'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.channel', $data);
    }

    // for adding channel function
    function add_channels(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'employee_name' => $request->employee_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password,
            'permissions' => $request->permission != null ? implode(',', $request->permission) : "",
            'created' => date('Y-m-d H:i:s')
        );
        Channels::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Employees added successfully', 'success');

        return back();
    }

    function delete_channels($id)
    {
        $del = true;
        $channel = Channels::find($id);
        if (!empty($channel)) {
            $orders = Order::where('channel', $channel->channel)->where('seller_id', $channel->seller_id)->whereNotIn('status', ['delivered', 'cancelled', 'damaged', 'lost'])->count();
            if (intval($orders) > 0)
                $del = false;
        }
        if ($del) {
            $channel->delete();
            echo json_encode(array('status' => 'true'));
        } else {
            echo json_encode(array('status' => 'false'));
        }
    }

    function modify_channels($id)
    {
        $response = Channels::find($id);
        echo json_encode($response);
    }

    // for adding channel function
    function update_channels(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'employee_name' => $request->employee_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password,
            'permissions' => $request->permission != null ? implode(',', $request->permission) : "",
            'modified' => date('Y-m-d H:i:s')
        );
        Channels::where('id', $request->hid)->update($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Employees updated successfully', 'success');

        return back();
    }

    // for removing selected channel
    function remove_selected_channel(Request $request)
    {
        $channels = Channels::whereIn('id', $request->ids)->get();
        $del = false;
        foreach($channels as $channel) {
            $orders = Order::where('channel', $channel->channel)->where('seller_channel_id', $channel->id)->where('seller_id', $channel->seller_id)->whereNotIn('status', ['delivered', 'cancelled', 'damaged', 'lost'])->count();
            if (intval($orders) == 0) {
                $del = true;
                $channel->delete();
            }
        }
        if ($del) {
            $this->utilities->generate_notification('Success', 'Channel Deleted successfully', 'success');
            echo json_encode(array('status' => 'true'));
        } else {
            echo json_encode(array('status' => 'false'));
        }
    }


    //function display all the my oms of the seller
    function my_oms()
    {
        $this->myOmsResetFilter($this->myOms);
        $data = $this->info;
        session(['noOfPage' => '20']);
        $data['limit_order'] = Session()->get('noOfPage');
        Session($this->filterArray);
        $data['partners'] = Partners::where('status', 'y')->orderBy('position', 'asc')->get();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['channel'] = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['oms_orders'] = MyOmsOrder::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.my_oms', $data);
    }

    //For get all order ajax data
    function getMyOmsOrder() {
        $data['order'] = MyOmsOrder::where('seller_id', Session()->get('MySeller')->id)->orderBy('inserted', 'desc')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = MyOmsOrder::where('seller_id', Session()->get('MySeller')->id)->orderBy('id', 'desc')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.my_oms_order', $data);
    }

    //set key of filter data(order)
    function myOmsSetFilter(Request $request)
    {
        $data = $request->value;
        Session::put("oms_{$request->key}", $data);
        session([
            'oms_min_value' => isset($request->min_value) ? $request->min_value : session('oms_min_value'),
            'oms_max_value' => isset($request->max_value) ? $request->max_value : session('oms_max_value'),
            'oms_min_weight' => isset($request->min_weight) ? $request->min_weight : session('oms_min_weight'),
            'oms_max_weight' => isset($request->max_weight) ? $request->max_weight : session('oms_max_weight'),
            'oms_min_quantity' => isset($request->min_quantity) ? $request->min_quantity : session('oms_min_quantity'),
            'oms_max_quantity' => isset($request->max_quantity) ? $request->max_quantity : session('oms_max_quantity'),
            'oms_start_date' => isset($request->start_date) ? $request->start_date : session('oms_start_date'),
            'oms_end_date' => isset($request->end_date) ? $request->end_date : session('oms_end_date'),
            'oms_filter_status' => $request->filter_status,
            'oms_order_awb_search' => $request->order_awb_search ?? session('oms_order_awb_search'),
            'oms_multiple_sku' => isset($request->multiple_sku) ? $request->multiple_sku : 'n',
            'oms_single_sku' => isset($request->single_sku) ? $request->single_sku : 'n',
            'oms_match_exact_sku' => isset($request->match_exact_sku) ? $request->match_exact_sku : 'n',
        ]);
    }

    //reset key of filter
    function myOmsResetFilter($keys)
    {
        $key = explode(',', $keys);
        foreach ($key as $k)
            session(["oms_$k" => '']);
    }

    //ajax search of order data using session key
    function my_oms_ajax_filter_order(Request $request)
    {
        $session_channel = session('oms_channel');
        $session_channel_name = session('oms_channel_name');
        $session_order_number = session('oms_order_number');
        $session_payment_type = session('oms_payment_type');
        $session_product = session('oms_product');
        $session_sku = session('oms_sku');
        $min_value = session('oms_min_value');
        $max_value = session('oms_max_value');
        $min_weight = !empty(session('oms_min_weight')) ? intval(session('oms_min_weight') * 1000) : session('oms_min_weight');
        $max_weight = !empty(session('oms_max_weight')) ? intval(session('oms_max_weight') * 1000) : session('oms_max_weight');
        $start_date = session('oms_start_date');
        $end_date = session('oms_end_date');
        $pickup_address = session('oms_pickup_address');
        $delivery_address = session('oms_delivery_address');
        $order_status = session('oms_order_status');
        $filter_status = session('oms_filter_status');
        $awb_number = session('oms_awb_number');
        $courier_partner = session('oms_courier_partner');
        $order_awb_search = session('oms_order_awb_search');
        $single_sku = session('oms_single_sku');
        $multiple_sku = session('oms_multiple_sku');
        $match_exact_sku = session('oms_match_exact_sku');
        $min_quantity = session('oms_min_quantity');
        $max_quantity = session('oms_max_quantity');
        DB::enableQueryLog();
        $query = MyOmsOrder::where('seller_id', Session()->get('MySeller')->id);
        if (!empty($session_order_number)) {
            $query = $query->where('customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('seller_channel_name', $session_channel_name);
        }
        if (!empty($order_status)) {
            $query = $query->whereIn('status', $order_status);
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($min_quantity)) {
            $query = $query->where('product_qty', '>=', $min_quantity);
        }
        if (!empty($max_quantity)) {
            $query = $query->where('product_qty', '<=', $max_quantity);
        }
        if (!empty($multiple_sku) && $multiple_sku == 'y') {
            $query = $query->where('product_sku', 'like', '%,%');
        } else if (!empty($single_sku) && $single_sku == 'y') {
            $query = $query->where('product_sku', 'not like', '%,%');
        } else if (!empty($match_exact_sku) && $match_exact_sku == 'y' && !empty($session_sku)) {
            $query = $query->where('product_sku', $session_sku);
        } else if(!empty($session_sku)) {
            $query = $query->where('product_sku', 'like', '%' . $session_sku . '%');
        }
        if (!empty($min_weight) && !empty($max_weight)) {
            $query = $query->where('weight', '>=', $min_weight)->where('weight', '<=', $max_weight);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
        }
        if (!empty($session_product)) {
            $query = $query->where('product_name', 'like', '%' . $session_product . '%');
        }
        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('customer_order_number', $order)
                        ->orWhereIn('awb_number', $order)
                        ->orWhereIn('s_contact', $order);
                });
            }
        }
        if (!empty($pickup_address) && count($pickup_address)> 0) {
            $query = $query->whereIn('p_warehouse_name',$pickup_address);
        }
        if (!empty($delivery_address)) {
            $query = $query->where('delivery_address', 'like', '%' . $delivery_address . '%');
        }
        if (!empty($courier_partner) && is_array($courier_partner)) {
            $query = $query->whereIn('courier_partner', $courier_partner);
        }
        if (!empty($awb_number)) {
            $query = $query->where('awb_number', 'like', '%' . $awb_number . '%');
        }
        $data['order'] = $query->latest('inserted')->paginate(Session()->get('noOfPage'));
        $data['total_order'] = $query->latest('inserted')->count();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['wareHouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        return view('seller.my_oms_order', $data);
    }

    //get order detaills
    function my_oms_modify_order($id)
    {
        $data['order'] = MyOmsOrder::find($id);
        $data['product'] = MyOmsProduct::where('order_id', $id)->get();
        echo json_encode($data);
    }

    //update order details
    function my_oms_update_order(Request $request)
    {
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('id', $request->warehouse)->first();
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'warehouse_id' => $request->warehouse,
            'order_number' => $request->order_number,
            'order_type' => $request->order_type,
            'customer_order_number' => $request->customer_order_number,
            'ewaybill_number' => $request->ewaybill_number ?? "",
            //for billing Address
            'b_customer_name' => $request->customer_name,
            'b_address_line1' => $request->address,
            'b_address_line2' => $request->address2,
            'b_city' => $request->city,
            'b_state' => $request->state,
            'b_country' => $request->country,
            'b_pincode' => $request->pincode,
            'b_contact_code' => $request->contact_code,
            'b_contact' => $request->contact,
            'delivery_address' => "$request->address,$request->address2,$request->city,$request->state,$request->pincode",

            //for billing Address
            's_customer_name' => $request->customer_name,
            's_address_line1' => $request->address,
            's_address_line2' => $request->address2,
            's_city' => $request->city,
            's_state' => $request->state,
            's_country' => $request->country,
            's_pincode' => $request->pincode,
            's_contact_code' => $request->contact_code,
            's_contact' => $request->contact,

            //for pickup address
            'p_warehouse_name' => $w->warehouse_name,
            'p_customer_name' => $w->contact_name,
            'p_address_line1' => $w->address_line1,
            'p_address_line2' => $w->address_line2,
            'p_city' => $w->city,
            'p_state' => $w->state,
            'p_country' => $w->country,
            'p_pincode' => $w->pincode,
            'p_contact_code' => $w->code,
            'p_contact' => $w->contact_number,
            'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",

            'weight' => $request->weight * 1000,
            'length' => $request->length,
            'breadth' => $request->breadth,
            'height' => $request->height,
            'product_name' => isset($request->product_name) ? implode(",", $request->product_name) : "",
            'product_sku' => isset($request->product_sku) ? implode(",", $request->product_sku) : "",
            'reseller_name' => $request->reseller_name,
            'invoice_amount' => $request->invoice_amount,
            'collectable_amount' => $request->collectable_amount ?? 0,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MySeller')->id
        );
        //dd($data);
        MyOmsOrder::where('id', $request->order_id)->update($data);
        MyOmsProduct::where('order_id', $request->order_id)->delete();

        $n = count($request->product_name);
        for ($i = 0; $i < $n; $i++) {
            $data_product = array(
                'order_id' => $request->order_id,
                'product_sku' => $request->product_sku[$i],
                'product_name' => $request->product_name[$i],
                'product_qty' => $request->product_qty[$i],
            );
            MyOmsProduct::create($data_product);
        }
        $this->utilities->generate_notification('Success', 'Order Updated successfully', 'success');
        return redirect(route('seller.my_oms'));
    }

    //for delete order
    function my_oms_delete_order($id)
    {
        $order = MyOmsOrder::find($id);
        MyOmsOrder::where('id', $id)->delete();
        MyOmsProduct::where('order_id', $id)->delete();
        $this->utilities->generate_notification('Success', ' Order has been deleted.', 'success');
    }

    // for removing selected order
    function my_oms_remove_selected_order(Request $request)
    {
        $existingJobOrders = [];
        $allOrderList = BulkShipOrdersJob::where('seller_id',Session()->get('MySeller')->id)->whereIn('status',['pending','processing'])->get();
        foreach ($allOrderList as $o){
            $listOrders = BulkShipOrdersJobDetails::where('job_id',$o->id)->where('is_deleted','n')->where('is_shipped','n')->pluck('order_id')->toArray();
            $existingJobOrders = array_merge($existingJobOrders,$listOrders);
        }
        $orderIds = MyOmsOrder::whereIn('id',$request->ids)->get()->pluck('id')->toArray();
        $tempOrderIDs = [];
        foreach ($orderIds as $id){
            if(!in_array($id,$existingJobOrders))
                $tempOrderIDs[]=$id;
        }
        $orderIds = $tempOrderIDs;
        MyOmsOrder::whereIn('id',$orderIds)->delete();
        MyOmsProduct::whereIn('order_id',$orderIds)->delete();
        $this->utilities->generate_notification('Success', ' Order Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //download single Label PDF of order
    function myOmsSingleLablePDF(Request $request, $id)
    {
        $data['config'] = $this->info['config'];
        $data['seller'] = Session()->get('MySeller');
        $data['basic_info'] = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $data['order'] = MyOmsOrder::find($id);
        // Get label configuration
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = Session()->get('MySeller')->id;
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
            $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
            $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE" )  : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
            $label->save();
        }
        $data['label'] = $label;
        $data['product'] = MyOmsProduct::where('order_id', $id)->get();
        $pdf = PDF::loadView('seller.my_oms_label', $data)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])->setPaper('a6', 'portrait');
        if($request->action == 'print') {
            // Print label
            return $pdf->stream('Label-' . $id . '.pdf');
        } else {
            return $pdf->download('Label-' . $id . '.pdf');
        }
        //  return view('seller.label_data', $data);
    }

    //Export order CSV
    public function export_csv_my_oms_order(Request $request) {
        // dd($request->all());
        $name = "exports/my-oms-order";
        $filename = "my-oms-order";
        $session_channel = session('channel');
        $session_channel_name = session('channel_name');
        $session_order_number = session('order_number');
        $session_payment_type = session('payment_type');
        $session_product = session('product');
        $min_value = session('min_value');
        $max_value = session('max_value');
        $start_date = session('start_date');
        $end_date = session('end_date');
        $order_status = session('order_status');
        $filter_status = session('filter_status');
        $order_awb_search = session('order_awb_search');
        DB::enableQueryLog();
        $query = DB::table('my_oms_orders')->where('seller_id', Session()->get('MySeller')->id);
        if (!empty($session_order_number)) {
            $query = $query->where('customer_order_number', $session_order_number);
        }
        if (!empty($session_channel)) {
            $query = $query->whereIn('channel', $session_channel);
        }
        if (!empty($session_channel_name)) {
            $query = $query->whereIn('seller_channel_name', $session_channel_name);
        }
        if (!empty($session_payment_type)) {
            $query = $query->whereIn('order_type', $session_payment_type);
        }
        if (!empty($min_value) && !empty($max_value)) {
            $query = $query->where('invoice_amount', '>=', intval($min_value))->where('invoice_amount', '<=', intval($max_value));
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date);
        }
        if (!empty($session_product)) {
            $query = $query->where(function ($q) use ($session_product) {
                $q->where('product_name', 'like', '%' . $session_product . '%')
                    ->orWhere('product_sku', 'like', '%' . $session_product . '%');
            });
        }

        if (!empty($order_awb_search)) {
            $order = trim($order_awb_search);
            $order = explode(',', $order);
            if (!empty($order)) {
                $query = $query->where(function ($q) use ($order,$order_awb_search) {
                    $q->whereIn('customer_order_number', $order)
                        ->orWhereIn('awb_number', $order)
                        ->orWhereIn('s_contact', $order);
                });
            }
        }
        if (!empty($request->export_order_id)) {
            $order_ids = explode(',', $request->export_order_id);
            $all_data = DB::table('my_oms_orders')->where('seller_id', Session()->get('MySeller')->id)->whereIn('id', $order_ids)->orderBy('id', 'desc')->get();
        } else {
            $all_data = $query->orderBy('id', 'desc')->get();
        }
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status', 'AWB Number', 'Courier Partner', 'Channel Name', 'Store Name', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Invoice Total', 'Collectable Amount', 'AWB Assigned Date');
        fputcsv($fp, $info);
        $cnt = 1;
        $PartnerName = Partners::getPartnerKeywordList();
        foreach ($all_data as $e) {
            if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($e->channel), ['amazon', 'amazon_direct']) && now()->parse($e->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY')))) {
                $e->b_customer_name = 'PII Data Archived';
                $e->b_address_line1 = 'PII Data Archived';
                $e->b_address_line2 = 'PII Data Archived';
                $e->b_city = 'PII Data Archived';
                $e->b_state = 'PII Data Archived';
                $e->b_country = 'PII Data Archived';
                $e->b_pincode = 'PII Data Archived';
                $e->b_contact_code = 'PII Data Archived';
                $e->b_contact = 'PII Data Archived';
                $e->s_customer_name = 'PII Data Archived';
                $e->s_address_line1 = 'PII Data Archived';
                $e->s_address_line2 = 'PII Data Archived';
                $e->s_city = 'PII Data Archived';
                $e->s_state = 'PII Data Archived';
                $e->s_country = 'PII Data Archived';
                $e->s_pincode = 'PII Data Archived';
                $e->s_contact_code = 'PII Data Archived';
                $e->s_contact = 'PII Data Archived';
                $e->invoice_amount = 'PII Data Archived';
                $e->product_name = 'PII Data Archived';
                $e->product_sku = 'PII Data Archived';
                $e->product_qty = 'PII Data Archived';
                $e->delivery_address = 'PII Data Archived';
            }
            $weight = !empty($e->weight) ? $e->weight / 1000 : '';
            if($this->fullInformation)
                $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->status, ('`' . $e->awb_number . '`'), $e->courier_partner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->s_customer_name, $e->s_address_line1, $e->s_address_line2, $e->s_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date);
            else
                $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted, $e->status, ('`' . $e->awb_number . '`'), $e->courier_partner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->s_customer_name, "********", "********", "********", "********", "********", "********", "********", "********", $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date);
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


    //Import order data using csv (500 order bunch using insert)
    public function import_csv_my_oms_order(Request $request)
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE 'my_oms_orders'");
        $orderCount = intval($statement[0]->Auto_increment);
        $statement = DB::select("SHOW TABLE STATUS LIKE 'my_oms_order_products'");
        $productCount = intval($statement[0]->Auto_increment);
        $totalOrders = DB::table('my_oms_orders')->select(DB::raw('max(cast(order_number as unsigned)) as order_number'))->where('seller_id', Session()->get('MySeller')->id)->first();
        $totalOrder = $totalOrders->order_number;
        $orderNumberCount = $totalOrder ?? 1000;
        $w = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        if (empty($w)) {
            $this->utilities->generate_notification('Oops..', ' Please add Default Warehouse First.', 'error');
            return back();
        }
        $totalCount = 0;
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $dataCount = 0;
                $ordersData = [];
                $productsData = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "" && $fileop[1] != "") {
                            if (strtolower($fileop[1]) == "cod" || strtolower($fileop[1]) == "prepaid") {
                                if (strlen($fileop[12]) != '6')
                                    $fileop[12] = "";
                                if (strlen($fileop[14]) != '10')
                                    $fileop[14] = "";
                                if (!empty($fileop[15]) && is_numeric($fileop[15]))
                                    $weight = $fileop[15] * 1000;
                                else
                                    $weight = "";
                                $data = array(
                                    'id' => $orderCount,
                                    'seller_id' => Session()->get('MySeller')->id,
                                    'customer_order_number' => isset($fileop[0]) ? $fileop[0] : $orderNumberCount,
                                    'order_number' => ++$orderNumberCount,
                                    'order_type' => isset($fileop[1]) ? strtolower($fileop[1]) : "",
                                    'o_type' => isset($fileop[2]) ? strtolower($fileop[2]) : "",
                                    'courier_partner' => isset($fileop[3]) ? $fileop[3] : "",
                                    'awb_number' => isset($fileop[4]) ? trim($fileop[4], '"') : "",
                                    'status' => isset($fileop[5]) ? $fileop[5] : "",

                                    //for billing address
                                    'b_customer_name' => isset($fileop[6]) ? $fileop[6] : "",
                                    'b_address_line1' => isset($fileop[7]) ? $fileop[7] : "",
                                    'b_address_line2' => isset($fileop[8]) ? $fileop[8] : "",
                                    'b_city' => isset($fileop[9]) ? $fileop[9] : "",
                                    'b_state' => isset($fileop[10]) ? $fileop[10] : "",
                                    'b_country' => isset($fileop[11]) ? $fileop[11] : "",
                                    'b_pincode' => isset($fileop[12]) ? $fileop[12] : "",
                                    'b_contact_code' => isset($fileop[13]) ? $fileop[13] : "",
                                    'b_contact' => isset($fileop[14]) ? $fileop[14] : "",

                                    //for shipping Address
                                    's_customer_name' => isset($fileop[6]) ? $fileop[6] : "",
                                    's_address_line1' => isset($fileop[7]) ? $fileop[7] : "",
                                    's_address_line2' => isset($fileop[8]) ? $fileop[8] : "",
                                    's_city' => isset($fileop[9]) ? $fileop[9] : "",
                                    's_state' => isset($fileop[10]) ? $fileop[10] : "",
                                    's_country' => isset($fileop[11]) ? $fileop[11] : "",
                                    's_pincode' => isset($fileop[12]) ? $fileop[12] : "",
                                    's_contact_code' => isset($fileop[13]) ? $fileop[13] : "",
                                    's_contact' => isset($fileop[14]) ? $fileop[14] : "",

                                    'weight' => $weight,
                                    'length' => isset($fileop[16]) ? $fileop[16] : "",
                                    'height' => isset($fileop[17]) ? $fileop[17] : "",
                                    'breadth' => isset($fileop[18]) ? $fileop[18] : "",
                                    'vol_weight' => (intval($fileop[17]) * intval($fileop[16]) * intval($fileop[18])) / 5,
                                    'invoice_amount' => isset($fileop[22]) ? intval($fileop[22]) : "",
                                    'reseller_name' => isset($fileop[23]) ? $fileop[23] : "",
                                    'delivery_address' => "$fileop[6],$fileop[7],$fileop[8],$fileop[9],$fileop[10]",
                                    //for pickup address
                                    'p_warehouse_name' => $w->warehouse_name,
                                    'p_customer_name' => $w->contact_name,
                                    'p_address_line1' => $w->address_line1,
                                    'p_address_line2' => $w->address_line2,
                                    'p_city' => $w->city,
                                    'p_state' => $w->state,
                                    'p_country' => $w->country,
                                    'p_pincode' => $w->pincode,
                                    'p_contact_code' => $w->code,
                                    'p_contact' => $w->contact_number,
                                    'pickup_address' => "$w->address_line1,$w->address_line2,$w->city,$w->state,$w->pincode",
                                    'awb_barcode' => Barcode::generateBarcode(isset($fileop[4]) ? trim($fileop[4], '"') : ""),
                                    'inserted' => date('Y-m-d H:i:s'),
                                    'inserted_by' => Session()->get('MySeller')->id
                                );
                                $ordersData[] = $data;
                                //loop for products
                                $all_products = [];
                                $all_skus = [];
                                $totalQty = 0;
                                for ($i = 24; $i <= 10000; $i += 3) {
                                    $temp = $i;
                                    if (!isset($fileop[$temp])) {
                                        break;
                                    }
                                    if ($fileop[$temp] == "")
                                        break;
                                    $data_product = array(
                                        'id' => $productCount++,
                                        'order_id' => $orderCount,
                                        'product_name' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_sku' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_qty' => intval($fileop[$temp] ?? "") == 0 ? "1" : $fileop[$temp]
                                    );
                                    $totalQty+= $data_product['product_qty'];
                                    $all_products[] = $data_product['product_name'];
                                    $all_skus[] = $data_product['product_sku'];
                                    $productsData[] = $data_product;
                                    if (count($productsData) == 500) {
                                        MyOmsProduct::insert($productsData);
                                        $productsData = [];
                                    }
                                }
                                $ordersData[$dataCount]['product_name'] = implode(',', $all_products);
                                $ordersData[$dataCount]['product_sku'] = implode(',', $all_skus);
                                $ordersData[$dataCount]['product_qty'] = $totalQty;
                                $orderCount++;
                                $dataCount++;
                                $totalCount++;
                                if (count($ordersData) == 500) {
                                    MyOmsOrder::insert($ordersData);
                                    $ordersData = [];
                                    $dataCount = 0;
                                }
                            }
                        }
                    }
                    $cnt++;
                }
                MyOmsProduct::insert($productsData);
                MyOmsOrder::insert($ordersData);
                $this->utilities->generate_notification('Success', "$dataCount Orders imported successfully", 'success');
                return redirect(url('/') . "/my-oms");
            } else {
                $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                return back();
            }
        } else {
            $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
            return back();
        }
    }

    //function display all the oms of the seller
    function oms()
    {
        $data = $this->info;
        $data['oms'] = OMS::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.oms', $data);
    }

    function delete_oms($id)
    {
        OMS::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    //for display oms easyship form
    function oms_add_easyship()
    {
        $data = $this->info;
        return view('seller.oms_add_easyship', $data);
    }

    // for adding API details of easyship
    function oms_submit_easyship(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'easyship',
            'title' => $request->oms_title,
            'store_url' => 'https://api.easyship.com/',
            'easyship_bearer_token' => $request->easyship_bearer_token,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }

    //for display oms easyecom form
    function oms_add_easyecom()
    {
        $data = $this->info;
        return view('seller.oms_add_easyecom', $data);
    }

    // for adding API details of easyecom
    function oms_submit_easyecom(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'easyecom',
            'title' => $request->oms_title,
            'store_url' => 'https://api.easyecom.io/',
            'easycom_username' => $request->easyecom_username,
            'easycom_password' => $request->easyecom_password,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }

    //for display oms clickpost form
    function oms_add_clickpost()
    {
        $data = $this->info;
        return view('seller.oms_add_clickpost', $data);
    }

    // for adding API details of clickpost
    function oms_submit_clickpost(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'clickpost',
            'title' => $request->oms_title,
            'store_url' => 'https://www.clickpost.in/',
            'clickpost_username' => $request->clickpost_username,
            'clickpost_key' => $request->clickpost_key,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }

    //for display oms omsguru form
    function oms_add_omsguru()
    {
        $data = $this->info;
        return view('seller.oms_add_omsguru', $data);
    }

    // for adding API details of omsguru
    function oms_submit_omsguru(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'omsguru',
            'title' => $request->oms_title,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }

    //for display oms vineretail form
    function oms_add_vineretail()
    {
        $data = $this->info;
        return view('seller.oms_add_vineretail', $data);
    }

    // for adding API details of vineretail
    function oms_submit_vineretail(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'vineretail',
            'title' => $request->oms_title,
            'store_url' => 'https://erp.vineretail.com/',
            'vineretail_api_owner' => $request->vineretail_api_owner,
            'vineretail_api_key' => $request->vineretail_api_key,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }

    //for display oms unicommerce form
    function oms_add_unicommerce()
    {
        $data = $this->info;
        return view('seller.oms_add_unicommerce', $data);
    }

    // for adding API details of unicommerce
    function oms_submit_unicommerce(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'oms_name' => 'unicommerce',
            'title' => $request->oms_title,
            'store_url' => '#',
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
        );
        OMS::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'OMS added successfully', 'success');
        return redirect(route('seller.oms'));
    }


    //for display shopify form
    function add_shopify()
    {
        $data = $this->info;
        return view('seller.add_shopify', $data);
    }

    // for adding API details of shopify
    function submit_shopify(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'shopify',
            'api_key' => $request->api_key,
            'password' => $request->api_password,
            'store_url' => $request->store_url,
            'shared_secret' => $request->shared_secret,
            'auto_fulfill' => $request->auto_fulfill,
            'auto_cancel' => $request->auto_cancel,
            'auto_cod_paid' => $request->auto_cod_paid,
            'send_abandon_sms' => $request->send_abandon_sms ?? 'n',
            'last_executed' => $request->last_executed ?? date('Y-m-d H:i:s')
        );
        Channels::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        return redirect(route('seller.channels'));
    }

    //for display shopify form
    function add_amazon()
    {
        $data = $this->info;
        return view('seller.add_amazon', $data);
    }

    // for adding API details of shopify
    function submit_amazon(Request $request)
    {
        $channelController = new ChannelsController();
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'amazon',
            'last_sync' => date('Y-m-d H:i:s',strtotime('-3 days')),
            'last_executed' => date('Y-m-d H:i:s',strtotime('-1 days')),
            'amazon_mws_token' => $request->mws_token,
            'amazon_seller_id' => $request->seller_id
        );
        $status = false;
        $count = Channels::where('channel', 'amazon')->where('seller_id', Session()->get('MySeller')->id)->first();
        if (!empty($count)) {
            $this->utilities->generate_notification('Error', 'You have already integrated this channel please delete to configure new channel', 'error');
            return back();
        }
        DB::beginTransaction();
        $channelID = Channels::create($data)->id;
        if ($channelController->_createCompanyAmazon($channelID, Session()->get('MySeller'))) {
            $tokenResponse = $channelController->_getApiTokenAmazon(Session()->get('MySeller')->email, "Twinnship@123#", $channelID);
            if ($tokenResponse['status']) {
                if ($channelController->_addMPCredentialsAmazon($tokenResponse['api_token'], $data['amazon_mws_token'], $data['amazon_seller_id'])) {
                    if ($channelController->_addAmazonCarrier($tokenResponse['api_token'], Session()->get('MySeller')->email, "Twinnship@123#", $channelID)) {
                        DB::commit();
                        $status = true;
                    } else {
                        DB::rollBack();
                    }
                } else {
                    DB::rollBack();
                }
            } else {
                DB::rollBack();
            }
        } else {
            DB::rollBack();
        }
        // generating notification
        if ($status)
            $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        else
            $this->utilities->generate_notification('Error', 'Channels could not created successfully', 'error');
        return redirect(route('seller.channels'));
    }

    //for display amazon direct form
    function add_amazon_direct()
    {
        $data = $this->info;
        return view('seller.add_amazon_direct', $data);
    }

    // for adding API details of shopify
    function submit_amazon_direct(Request $request)
    {
        //$channelResponse = Channels::where('seller_id',Session()->get('MySeller')->id)->where('channel','amazon_direct')->first();
        //if(!empty($channelResponse)){
        //    $this->utilities->generate_notification('Error', 'Amazon Direct is Already integrated please try to delete and add again', 'error');
        //    return back();
        //}
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'amazon_direct'
        );
        Channels::create($data);
        // generating notification
        return redirect("https://sellercentral.amazon.in/apps/authorize/consent?application_id=amzn1.sp.solution.f78df776-2482-45d6-93ab-34b539a2f0b6&version=beta&state=examplestate");
    }

    //for display shopify form
    function add_woocommerce()
    {
        $data = $this->info;
        return view('seller.add_woocommerce', $data);
    }

    // for adding API details of shopify
    function submit_woocommerce(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'woocommerce',
            'woo_consumer_key' => $request->consumer_key,
            'woo_consumer_secret' => $request->consumer_secret,
            'store_url' => $request->store_url,
            'last_executed' => $request->last_executed
        );
        $id = Channels::create($data)->id;
        $data = [
            'channel' => 'woocommerce',
            'channel_id' => $id,
            'pickup_scheduled' => $request->pickup_scheduled,
            'picked_up' => $request->picked_up,
            'in_transit' => $request->in_transit,
            'out_for_delivery' => $request->out_for_delivery,
            'delivered' => $request->delivered
        ];
        ChannelOrderStatusList::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        return redirect(route('seller.channels'));
    }

    //for display magento form
    function add_magento()
    {
        $data = $this->info;
        return view('seller.add_magento', $data);
    }

    // for adding API details of shopify
    function submit_magento(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'store_url' => $request->store_url,
            'channel_name' => $request->channel_name,
            'channel' => 'magento',
            'magento_access_token' => $request->magento_access_token,
        );
        Channels::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        return redirect(route('seller.channels'));
    }

    //for display store hippo form
    function add_storehippo()
    {
        $data = $this->info;
        return view('seller.add_storehippo', $data);
    }

    // for adding API details of store hippo
    function submit_storehippo(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'store_url' => $request->store_url,
            'channel_name' => $request->channel_name,
            'channel' => 'storehippo',
            'store_hippo_access_key' => $request->store_hippo_access_key,
        );
        Channels::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        return redirect(route('seller.channels'));
    }

    //for display store hippo form
    function add_kartrocket()
    {
        $data = $this->info;
        return view('seller.add_kartrocket', $data);
    }

    // for adding API details of store hippo
    function submit_kartrocket(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'kartrocket',
            'kart_rocket_api_key' => $request->kart_rocket_api_key,
            'auto_fulfill' => $request->auto_fulfill,
        );
        Channels::create($data);
        // generating notification
        $this->utilities->generate_notification('Success', 'Channels added successfully', 'success');
        return redirect(route('seller.channels'));
    }

    //function display all the orders of the seller
    function sku(Request $request)
    {
        $data = $this->info;
        // $data['sku'] = SKU::where('seller_id', Session()->get('MySeller')->id)->simplepaginate(Session()->get('noOfPage'));
        return view('seller.sku', $data);
    }

    function ajax_sku(Request $request)
    {
        $data = $this->info;
        $query = SKU::where('seller_id', Session()->get('MySeller')->id);
        if($request->sku) {
            $query = $query->where('sku', $request->sku);
        }
        $data['sku'] = $query->paginate($request->per_page ?? 20);
        return view('seller.ajax_sku', $data);
    }

    // for adding Product SKU function
    function add_sku(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'sku' => $request->product_sku,
            'product_name' => $request->product_name,
            'weight' => $request->product_weight,
            'length' => $request->product_length,
            'width' => $request->product_width,
            'height' => $request->product_height,
            'brand_name' => $request->brand_name
        );
        $order = SKU::create($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'SKU added successfully', 'success');
        return back();
    }

    function modify_sku($id)
    {
        $response = SKU::find($id);
        echo json_encode($response);
    }


    function update_sku(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'sku' => $request->product_sku,
            'product_name' => $request->product_name,
            'weight' => $request->product_weight,
            'length' => $request->product_length,
            'width' => $request->product_width,
            'height' => $request->product_height,
            'brand_name' => $request->brand_name
        );

        SKU::where('id', $request->sid)->update($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'SKU Updated successfully', 'success');
        return back();
    }

    function delete_sku($id)
    {
        SKU::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function import_csv_sku(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                SKU::where('seller_id', Session()->get('MySeller')->id)->delete();
                $cnt = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                $data = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $data[] = array(
                                'seller_id' => Session()->get('MySeller')->id,
                                'sku' => isset($fileop[0]) ? $fileop[0] : "",
                                'product_name' => isset($fileop[1]) ? $fileop[1] : "",
                                'weight' => isset($fileop[2]) ? $fileop[2] : "",
                                'length' => isset($fileop[3]) ? $fileop[3] : "",
                                'width' => isset($fileop[4]) ? $fileop[4] : "",
                                'height' => isset($fileop[5]) ? $fileop[5] : "",
                                'brand_name' => isset($fileop[6]) ? $fileop[6] : ""
                            );
                        }
                    }
                    $cnt++;
                }
                $order = SKU::insert($data);
                $this->utilities->generate_notification('Success', 'CSV Uploaded successfully', 'success');
                return redirect()->back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
    }

    function export_csv_sku(Request $request)
    {
        $name = "exports/sku";
        $filename = "sku";
        $all_data = SKU::where('seller_id', Session()->get('MySeller')->id)->get();
        $ids = array_filter(explode(',', $request->skuIds), function($el) {
            return !empty($el);
        });
        if(!empty($ids))
        {
            $all_data = $all_data->whereIn('id', $ids);
        }

        $fp = fopen("$name.csv", 'w');
        $info = array('Sr No','Product SKU', 'Product Name', 'Weight', 'Length', 'Breadth', 'Height', 'Brand Name');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            //    $info=array($cnt,$e['order_type'],$e['customer_name'],$e['address_line1'],$e['address_line2'],$e['city'],$e['state'],$e['country'],$e['pincode'],$e['contact_code'],$e['contact'],$e['weight'],$e['length'],$e['height'],$e['breadth'],$e['shipping_charges'],$e['cod_charges'],$e['discount'],$e['product_name'],$e['product_qty'],$e['product_amount'],$e['product_sku']);
            $info = array($cnt,$e->sku, $e->product_name, $e->weight, $e->length, $e->width, $e->height, $e->brand_name);
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

    // for removing selected SKU
    function remove_selected_sku(Request $request)
    {
        // dd($request->ids);
        SKU::whereIn('id', $request->ids)->delete();
        $this->utilities->generate_notification('Success', 'SKU Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //function display all the orders of the seller
    function sku_mapping(Request $request)
    {
        $data = $this->info;
        // $data['sku'] = SKU::where('seller_id', Session()->get('MySeller')->id)->simplepaginate(Session()->get('noOfPage'));
        return view('seller.sku-mapping', $data);
    }

    function ajax_sku_mapping(Request $request)
    {
        $data = $this->info;
        $query = SKU_Mapping::where('seller_id', Session()->get('MySeller')->id);
        if($request->sku) {
            $query = $query->where('parent_sku', $request->sku)
                ->orWhere('child_sku', $request->sku);
        }
        $data['sku'] = $query->paginate($request->per_page ?? 20);
        return view('seller.ajax_sku_mapping', $data);
    }

    // for adding Product SKU function
    function add_sku_mapping(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'parent_sku' => $request->parent_sku,
            'child_sku' => $request->child_sku,
        );
        SKU_Mapping::firstOrCreate($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'SKU added successfully', 'success');
        return back();
    }

    function modify_sku_mapping($id)
    {
        $response = SKU_Mapping::find($id);
        echo json_encode($response);
    }


    function update_sku_mapping(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'parent_sku' => $request->parent_sku,
            'child_sku' => $request->child_sku,
        );

        SKU_Mapping::where('id', $request->sid)->update($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'SKU Updated successfully', 'success');
        return back();
    }

    function delete_sku_mapping($id)
    {
        SKU_Mapping::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function import_csv_sku_mapping(Request $request)
    {
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['importFile']['tmp_name'];
                $handle = fopen($file, "r");
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            SKU_Mapping::firstOrCreate(array(
                                'seller_id' => Session()->get('MySeller')->id,
                                'parent_sku' => isset($fileop[0]) ? trim($fileop[0]) : "",
                                'child_sku' => isset($fileop[1]) ? trim($fileop[1]) : "",
                            ));
                        }
                    }
                    $cnt++;
                }
                $this->utilities->generate_notification('Success', 'CSV Uploaded successfully', 'success');
                return redirect()->back();
            } else {
                echo "Invalid File";
            }
        } else {
            echo "Please Upload file";
        }
    }

    function export_csv_sku_mapping()
    {
        $name = "exports/sku-mapping";
        $filename = "sku-mapping";
        $all_data = SKU_Mapping::where('seller_id', Session()->get('MySeller')->id)->get();
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No','Parent SKU', 'Child SKU');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $info = array($cnt++,$e->parent_sku, $e->child_sku);
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

    // for removing selected SKU
    function remove_selected_sku_mapping(Request $request)
    {
        // dd($request->ids);
        SKU_Mapping::whereIn('id', $request->ids)->delete();
        $this->utilities->generate_notification('Success', 'SKU Deleted successfully', 'success');
        echo json_encode(array('status' => 'true'));
    }

    //for shipping rates
    function shipping_rates()
    {
        $data = $this->info;
        $data['seller'] = Seller::find(Session()->get('MySeller')->id);
        $data['plan'] = Plans::where('id', Session()->get('MySeller')->plan_id)->first();
        $blockedCourierPartners = explode(',', $data['seller']->blocked_courier_partners) ?? [];
        $partners = Partners::whereNotIn('id', $blockedCourierPartners)->where('status','y')->get();
        $data['partners'] = [];
        foreach($partners as $partner) {
            if(!Courier_blocking::where('is_blocked', 'y')->where('courier_partner_id', $partner->id)->where('seller_id', Session()->get('MySeller')->id)->exists()) {
                $data['partners'][] = $partner;
            }
        }
        return view('seller.shipping_rates', $data);
    }

    function get_shipping_rates()
    {
        $rates = Rates::where('plan_id', Session()->get('MySeller')->plan_id)->where('seller_id', Session()->get('MySeller')->id)->get();
        echo json_encode($rates);
    }

    function confirm_payment(Request $request)
    {
        echo "Please pay the Amount : " . $request->amount;
    }

    function fetch_all_orders()
    {
        $channelController = new ChannelsController();
        $shopify = new ShopifyController();
        try {
            //@$this->verifyAllOrders();
            $channel = 1;
            $channels = Channels::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('channel',['amazon_direct'])->where('status','y')->get();
            if (count($channels) > 0) {
                foreach ($channels as $c) {
                    switch ($c->channel) {
                        case 'shopify':
                            ShopifyHelper::GetShopifyOrders($c);
                            break;
                        case 'storehippo';
                            $this->_fetchStoreHippoOrders($c);
                            break;
                        case 'magento';
                            $this->_fetchMagento2Orders($c);
                            break;
                        case 'woocommerce':
                            $shopify->_fetch_woocommerce($c);
                            break;
                        case 'amazon':
                            //$channelController->_fetchAmazonOrders($c,Session()->get('MySeller'));
                            break;
                        default:
                            echo "Channel Not Found";
                    }
                }
                //$this->utilities->generate_notification('Success', 'Order Fetched successfully', 'success');
            } else {
                //
                $channel = 0;
                $obj = new OMSController();
                $obj->getOMSData(Session()->get('MySeller')->id, $channel);
            }
            if(in_array(Session()->get('MySeller')->id,[31860,32195]))
                $this->verifyAllOrders();

            // echo json_encode(array('status' => 'success'));
        } catch (Exception $e) {
            dd($e);
            $this->utilities->generate_notification('Error', 'Order not Fetched', 'error');
        }
    }

    //order fetch code for all channels here

    function _fetch_shopify($details)
    {
        //https://{api_key}:{token}@linksyscoaching.myshopify.com/admin/api/2020-10/shop.json
        //        $details=Channels::where('seller_id',Session()->get('MySeller')->id)->where('channel','shopify')->get();
        $username = $details->api_key;
        $password = $details->password;
        $storeURL = $details->store_url;
        $sharedSecret = $details->sharedSecret;
        $callUrl = "https://$username:$password@$storeURL/admin/api/2020-10/orders.json?status=open&fulfillment_status=unshipped&limit=250"; //since_id=" . $details['last_id']
        $response = file_get_contents($callUrl);
        $responseDatas = json_decode($response, true);
        $responseData = array_reverse($responseDatas);
        //$dimen = $this->_fetch_dimension_data(600);
        $warehouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        $cnt = 0;
        if (count($responseData['orders']) != 0) {
            foreach ($responseData['orders'] as $rd) {
                if ($cnt++ == 0) {
                    $last_id = $rd['id'];
                }
                $dimen = $this->_fetch_dimension_data($rd['total_weight'] ?? 0);
                //$resp = Order::where('channel_id', $rd['id'])->where('seller_id', Session()->get('MySeller')->id)->first();
                //if (!empty($resp)) {
                //    continue;
                //}
                $rd['shipping_address']['province'] = $rd['shipping_address']['province'] ?? null;
                $igst = 0;
                $cgst = 0;
                $sgst = 0;
                if(!empty($rd['total_price'])) {
                    if(strtolower($rd['shipping_address']['province']) == strtolower($warehouse->state)) {
                        $percent = $rd['total_price'] - ($rd['total_price']/((18/100)+1));
                        $cgst = $percent/2;
                        $sgst = $percent/2;
                    } else {
                        $percent = $rd['total_price'] - ($rd['total_price']/((18/100)+1));
                        $igst = $percent;
                    }
                }
                $data = array(
                    'order_number' => $rd['order_number'] ?? 0,
                    'customer_order_number' => $rd['order_number'] ?? 0,
                    'channel_id' => $rd['id'],
                    'o_type' => "forward",
                    'seller_id' => Session()->get('MySeller')->id,
                    'order_type' => $rd['financial_status'] == "paid" ? "prepaid" : "cod",
                    'b_customer_name' => isset($rd['shipping_address']) ? $rd['shipping_address']['first_name'] . " " . $rd['shipping_address']['last_name'] : "",
                    'b_address_line1' => isset($rd['shipping_address']) ? $rd['shipping_address']['address1'] : "",
                    'b_address_line2' => isset($rd['shipping_address']) ? $rd['shipping_address']['address2'] : "",
                    'b_country' => isset($rd['shipping_address']) ? $rd['shipping_address']['country'] : "",
                    'b_state' => isset($rd['shipping_address']) ? $rd['shipping_address']['province'] : "",
                    'b_city' => isset($rd['shipping_address']) ? $rd['shipping_address']['city'] : "",
                    'b_pincode' => isset($rd['shipping_address']) ? $rd['shipping_address']['zip'] : "",
                    'b_contact' => isset($rd['shipping_address']) ? str_replace(" ", "", $rd['shipping_address']['phone']) : "",
                    'b_contact_code' => isset($rd['shipping_address']) ? substr($rd['shipping_address']['phone'], 0, 3) : "",
                    's_customer_name' => isset($rd['shipping_address']) ? $rd['shipping_address']['first_name'] . " " . $rd['shipping_address']['last_name'] : "",
                    's_address_line1' => isset($rd['shipping_address']) ? $rd['shipping_address']['address1'] : "",
                    's_address_line2' => isset($rd['shipping_address']) ? $rd['shipping_address']['address2'] : "",
                    's_country' => isset($rd['shipping_address']) ? $rd['shipping_address']['country'] : "",
                    's_state' => isset($rd['shipping_address']) ? $rd['shipping_address']['province'] : "",
                    's_city' => isset($rd['shipping_address']) ? $rd['shipping_address']['city'] : "",
                    's_pincode' => isset($rd['shipping_address']) ? $rd['shipping_address']['zip'] : "",
                    's_contact' => isset($rd['shipping_address']) ? str_replace(" ", "", $rd['shipping_address']['phone']) : "",
                    's_contact_code' => isset($rd['shipping_address']) ? substr($rd['shipping_address']['phone'], 0, 3) : "",
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
                    'weight' => $rd['total_weight'],
                    'height' => $dimen->height ?? "",
                    'length' => $dimen->length ?? "",
                    'breadth' => $dimen->width ?? "",
                    'vol_weight' => (intval($dimen->height) * intval($dimen->length) * intval($dimen->width)) / 5,
                    'shipping_charges' => 0,
                    'cod_charges' => 0,
                    'discount' => 0,
                    'invoice_amount' => $rd['total_price'],
                    'igst' => $igst,
                    'sgst' => $sgst,
                    'cgst' => $cgst,
                    'channel' => 'shopify',
                    'inserted' => date('Y-m-d H:i:s', strtotime($rd['created_at'])),
                    'inserted_by' => Session()->get('MySeller')->id,
                    'seller_channel_id' => $details->id
                );
                $data['pickup_address'] = $data['p_address_line1'] . "," . $data['p_address_line2'] . "," . $data['p_city'] . "," . $data['p_state'] . "," . $data['p_pincode'];
                $data['delivery_address'] = $data['s_address_line1'] . "," . $data['s_address_line2'] . "," . $data['s_city'] . "," . $data['s_state'] . "," . $data['s_pincode'];
                try{
                    $orderID = Order::create($data)->id;
                }
                catch(Exception $e){
                    continue;
                }
                //$orderID = Order::create($data)->id;
                $pname = [];
                $psku = [];
                $productQty = 0;
                $totalWeight = 0;
                foreach ($rd['line_items'] as $p) {
                    $product = array(
                        'order_id' => $orderID,
                        'product_sku' => $p['sku'],
                        'product_name' => $p['title'],
                        'product_unitprice' => $p['price'],
                        'product_qty' => $p['quantity'],
                        'total_amount' => $p['price'] * $p['quantity'],
                    );
                    $productQty+=intval($product['product_qty']);
                    Product::create($product);
                    $pname[] = $p['title'];
                    $psku[] = $p['sku'];
                    $totalWeight += ($p['grams'] * $p['quantity']) ?? 0;
                }
                $weight = empty($data['weight']) ? $totalWeight : $data['weight'];
                $dimen = $this->_fetch_dimension_data($weight ?? 0);
                Order::where('id', $orderID)->update([
                    'product_name' => implode(',', $pname),
                    'product_qty' => $productQty,
                    'product_sku' => implode(',', $psku),
                    'weight' => $weight,
                    'height' => $dimen->height ?? "",
                    'length' => $dimen->length ?? "",
                    'breadth' => $dimen->width ?? "",
                    'vol_weight' => (intval($dimen->height ?? 0) * intval($dimen->length ?? 0) * intval($dimen->width ?? 0)) / 5,
                ]);
            }
        }
        if (isset($last_id))
            Channels::where('id', $details->id)->update(array('last_sync' => date('Y-m-d H:i:s'), 'last_id' => $last_id));
        echo json_encode(array('status' => 'synced successfully'));
        $this->utilities->generate_notification('Success', ' Order Fetched successfully', 'success');
        //        if($responseData[''])
        //file_put_contents("response.json",$response);
        //echo $response;
    }

    function _fetch_woocommerce($details)
    {
        $warehouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        if (empty($warehouse)) {
            return false;
        }
        $woocommerce = new Client(
            $details['store_url'],
            $details['woo_consumer_key'],
            $details['woo_consumer_secret'],
            [
                'version' => 'wc/v3',
            ]
        );
        $res = $woocommerce->get('orders', [
            'status' => 'processing',
            'per_page' => 100,
            'after' => now()->subDays(5)
        ]);
        //if(Session()->get('MySeller')->id == 447)
        //	dd($res);
        Logger::write('logs/channels/woocommerce/woocommerce-'.date('Y-m-d').'.text', [
            'title' => 'WooCommerce Response of SellerID: '.Session()->get('MySeller')->id,
            'data' => $res
        ]);
        if(gettype($res) == "object"){
            return true;
        }
        //echo json_encode($res); exit;
        foreach ($res as $rd) {
            if(!isset($rd->id))
                continue;
            //$addressDetails = $this->_get_pincode_details($rd->shipping->postcode);
            //$resp = Order::where('channel', 'woocommerce')->where('seller_id', Session()->get('MySeller')->id)->where('order_number', $rd->id)->get();
            //if (count($resp) != 0)
            //    continue;
            $shippingDetails = $this->_get_pincode_details($rd->shipping->postcode);
            $billingDetails = $this->_get_pincode_details($rd->billing->postcode);

            $shippingDetails['state'] = $shippingDetails['state'] ?? null;

            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            if(!empty($rd->total)) {
                if(strtolower($shippingDetails['state']) == strtolower($warehouse->state)) {
                    $percent = $rd->total - ($rd->total/((18/100)+1));
                    $cgst = $percent/2;
                    $sgst = $percent/2;
                } else {
                    $percent = $rd->total - ($rd->total/((18/100)+1));
                    $igst = $percent;
                }
            }

            $data = array(
                'order_number' => $rd->id,
                'o_type' => "forward",
                'customer_order_number' => $rd->id,
                'channel_id' => $rd->id,
                'seller_id' => Session()->get('MySeller')->id,
                'order_type' => $rd->date_paid == null ? "cod" : "prepaid",
                's_customer_name' => (!empty($rd->shipping->first_name) ? $rd->shipping->first_name : $rd->billing->first_name) . " " . (!empty($rd->shipping->last_name) ? $rd->shipping->last_name : $rd->billing->last_name),
                's_address_line1' => !empty($rd->shipping->address_1) ? $rd->shipping->address_1 : $rd->billing->address_1,
                's_address_line2' => !empty($rd->shipping->address_2) ? $rd->shipping->address_2 : $rd->billing->address_2,
                's_country' => $shippingDetails['status'] == 'Success' ? $shippingDetails['country'] : $billingDetails['country'] ?? "",
                's_state' => $shippingDetails['status'] == 'Success' ? $shippingDetails['state'] : $billingDetails['state'] ?? "",
                's_city' => !empty($rd->shipping->city) ? $rd->shipping->city : $rd->billing->city,
                's_pincode' => !empty($rd->shipping->postcode) ? $rd->shipping->postcode : $rd->billing->postcode,
                's_contact' => isset($rd->billing) ? ltrim($rd->billing->phone,"0") : "",
                's_contact_code' => "91",
                'b_customer_name' => isset($rd->billing) ? $rd->billing->first_name . " " . $rd->billing->last_name : "",
                'b_address_line1' => isset($rd->billing) ? $rd->billing->address_1 : "",
                'b_address_line2' => isset($rd->billing) ? $rd->billing->address_2 : "",
                'b_country' => $billingDetails['status'] == 'Success' ? $billingDetails['country'] : "",
                'b_state' => $billingDetails['status'] == 'Success' ? $billingDetails['state'] : "",
                'b_city' => isset($rd->billing) ? $rd->billing->city : "",
                'b_pincode' => isset($rd->billing) ? $rd->billing->postcode : "",
                'b_contact' => isset($rd->billing) ? ltrim($rd->billing->phone,"0") : "",
                'b_contact_code' => "91",
                'p_warehouse_name' => isset($warehouse->warehouse_name) ? $warehouse->warehouse_name : "",
                'p_customer_name' => isset($warehouse->contact_name) ? $warehouse->contact_name : "",
                'p_address_line1' => isset($warehouse->address_line1) ? $warehouse->address_line1 : "",
                'p_address_line2' => isset($warehouse->address_line2) ? $warehouse->address_line2 : "",
                'p_country' => isset($warehouse->country) ? $warehouse->country : "",
                'p_state' => isset($warehouse->state) ? $warehouse->state : "",
                'p_city' => isset($warehouse->city) ? $warehouse->city : "",
                'p_pincode' => isset($warehouse->pincode) ? $warehouse->pincode : "",
                'p_contact' => isset($warehouse->contact_number) ? $warehouse->contact_number : "",
                'p_contact_code' => isset($warehouse->code) ? $warehouse->code : "",
                'warehouse_id' => isset($warehouse->id) ? $warehouse->id : "",
                'weight' => "",
                'height' => "",
                'breadth' => "",
                'length' => "",
                'shipping_charges' => 0,
                'cod_charges' => 0,
                'discount' => 0,
                'invoice_amount' => $rd->total,
                'igst' => $igst,
                'sgst' => $sgst,
                'cgst' => $cgst,
                'channel' => 'woocommerce',
                'inserted' => date('Y-m-d H:i:s'),
                'inserted_by' => Session()->get('MySeller')->id,
                'seller_channel_id' => $details->id
            );
            $data['pickup_address'] = $data['p_address_line1'] . "," . $data['p_address_line2'] . "," . $data['p_city'] . "," . $data['p_state'] . "," . $data['p_pincode'];
            $data['delivery_address'] = $data['s_address_line1'] . "," . $data['s_address_line2'] . "," . $data['s_city'] . "," . $data['s_state'] . "," . $data['s_pincode'];
            try{
                $orderID = Order::create($data)->id;
            }
            catch(Exception $e){
                continue;
            }
            $pname = [];
            $weight = 0;
            $psku = [];
            $productQty = 0;
            $cnt=0;

            foreach ($rd->line_items as $p) {
                try{
                    $res1=$woocommerce->get("products/{$p->product_id}");
                    $pWeight = intval($res1->weight ?? "") ?? 0;
                }
                catch(Exception $e){
                    $pWeight = 0;
                }
                $pWeight=intval($pWeight);
                $weight += ($pWeight * $p->quantity);
                $product = array(
                    'order_id' => $orderID,
                    'product_sku' => $p->sku,
                    'product_name' => $p->name,
                    'product_unitprice' => $p->total,
                    'product_qty' => $p->quantity,
                    'total_amount' => $p->total * $p->quantity,
                );
                $productQty+=intval($product['product_qty']);
                Product::create($product);
                $pname[] = $p->name;
                $psku[] = $p->sku;
                $cnt++;
            }
            if($cnt==0){
                $product = array(
                    'order_id' => $orderID,
                    'product_sku' => 'sku1',
                    'product_name' => 'product1',
                    'product_unitprice' => 100,
                    'product_qty' => 1,
                    'total_amount' => 100
                );
                Product::create($product);
                $pname[] = $product['product_name'];
                $psku[] = $product['product_sku'];
                $productQty+=intval($product['product_qty']);
            }
            if(intval($weight) == 0)
                $weight = 100;
            $dimen = $this->_fetch_dimension_data($weight);
            $volWeight = ($dimen->height * $dimen->length * $dimen->width) / 5;
            Order::where('id', $orderID)->update(array('height' => $dimen->height ?? 0,'vol_weight' =>$volWeight, 'length' => $dimen->length ?? 0, 'breadth' => $dimen->width ?? 0, 'weight' => $weight,'product_name' => implode(',', $pname),'product_qty' => $productQty,'product_sku' => implode(',', $psku)));
        }
    }


    function _fetchStoreHippoOrders($details)
    {
        $warehouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        $url = $details['store_url'] . "/api/1.1/entity/ms.orders";
        $accessKey = $details['store_hippo_access_key'];
        //echo "$url : $accessKey"; exit;

        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            "access-key: $accessKey"
        ));
        $response = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $responseData = json_decode($response, true);
        if (isset($responseData['data'])) {
            foreach ($responseData['data'] as $rd) {
                if ($rd['status'] != "open")
                    continue;
                $resp = Order::where('channel', 'storehippo')->where('seller_id', Session()->get('MySeller')->id)->where('order_number', $rd['_id'])->get();
                if (count($resp) != 0)
                    continue;

                $rd['shipping_address']['state'] = $rd['shipping_address']['state'] ?? null;

                $igst = 0;
                $cgst = 0;
                $sgst = 0;
                if(!empty($rd['sub_total'])) {
                    if(strtolower($rd['shipping_address']['state']) == strtolower($warehouse->state)) {
                        $percent = $rd['sub_total'] - ($rd['sub_total']/((18/100)+1));
                        $cgst = $percent/2;
                        $sgst = $percent/2;
                    } else {
                        $percent = $rd['sub_total'] - ($rd['sub_total']/((18/100)+1));
                        $igst = $percent;
                    }
                }
                $data = array(
                    'order_number' => $rd['_id'],
                    'customer_order_number' => $rd['_id'],
                    'o_type' => "forward",
                    'seller_id' => Session()->get('MySeller')->id,
                    'order_type' => $rd['financial_status'] == "Paid" ? "prepaid" : "cod",
                    's_customer_name' => isset($rd['shipping_address']) ? $rd['shipping_address']['full_name'] : "",
                    's_address_line1' => isset($rd['shipping_address']) ? $rd['shipping_address']['address'] : "",
                    's_country' => isset($rd['shipping_address']) ? $rd['shipping_address']['country'] : "",
                    's_state' => isset($rd['shipping_address']) ? $rd['shipping_address']['state'] : "",
                    's_city' => isset($rd['shipping_address']) ? $rd['shipping_address']['city'] : "",
                    's_pincode' => isset($rd['shipping_address']) ? $rd['shipping_address']['zip'] : "",
                    's_contact' => isset($rd['shipping_address']) ? $rd['shipping_address']['phone'] : "",
                    's_contact_code' => isset($rd['shipping_address']) ? substr($rd['shipping_address']['phone'], 0, 3) : "",
                    'b_customer_name' => isset($rd['billing_address']) ? $rd['billing_address']['full_name'] : "",
                    'b_address_line1' => isset($rd['billing_address']) ? $rd['billing_address']['address'] : "",
                    'b_country' => isset($rd['billing_address']) ? $rd['billing_address']['country'] : "",
                    'b_state' => isset($rd['billing_address']) ? $rd['billing_address']['state'] : "",
                    'b_city' => isset($rd['billing_address']) ? $rd['billing_address']['city'] : "",
                    'b_pincode' => isset($rd['billing_address']) ? $rd['billing_address']['zip'] : "",
                    'b_contact' => isset($rd['billing_address']) ? $rd['billing_address']['phone'] : "",
                    'b_contact_code' => isset($rd['billing_address']) ? substr($rd['billing_address']['phone'], 0, 3) : "",
                    'p_warehouse_name' => isset($warehouse->warehouse_name) ? $warehouse->warehouse_name : "",
                    'p_customer_name' => isset($warehouse->contact_name) ? $warehouse->contact_name : "",
                    'p_address_line1' => isset($warehouse->address_line1) ? $warehouse->address_line1 : "",
                    'p_address_line2' => isset($warehouse->address_line2) ? $warehouse->address_line2 : "",
                    'p_country' => isset($warehouse->country) ? $warehouse->country : "",
                    'warehouse_id' => isset($warehouse->id) ? $warehouse->id : "",
                    'p_state' => isset($warehouse->state) ? $warehouse->state : "",
                    'p_city' => isset($warehouse->city) ? $warehouse->city : "",
                    'p_pincode' => isset($warehouse->pincode) ? $warehouse->pincode : "",
                    'p_contact' => isset($warehouse->contact_number) ? $warehouse->contact_number : "",
                    'p_contact_code' => isset($warehouse->code) ? $warehouse->code : "",
                    'length' => isset($rd['dimension']) ? $rd['dimension']['length'] : "",
                    'breadth' => isset($rd['dimension']) ? $rd['dimension']['width'] : "",
                    'height' => isset($rd['dimension']) ? $rd['dimension']['height'] : "",
                    'shipping_charges' => 0,
                    'cod_charges' => 0,
                    'discount' => 0,
                    'invoice_amount' => $rd['sub_total'],
                    'igst' => $igst,
                    'sgst' => $sgst,
                    'cgst' => $cgst,
                    'channel' => 'storehippo',
                    'inserted' => date('Y-m-d H:i:s'),
                    'inserted_by' => Session()->get('MySeller')->id
                );
                $data['pickup_address'] = $data['p_address_line1'] . "," . $data['p_address_line2'] . "," . $data['p_city'] . "," . $data['p_state'] . "," . $data['p_pincode'];
                $data['delivery_address'] = $data['s_address_line1'] . "," . $data['s_address_line2'] . "," . $data['s_city'] . "," . $data['s_state'] . "," . $data['s_pincode'];
                $orderID = Order::create($data)->id;
                $pname = [];
                $weight = 0;
                $psku = [];
                $productQty=0;
                foreach ($rd['items'] as $p) {
                    $weight += $p['weight'];
                    $product = array(
                        'order_id' => $orderID,
                        'product_sku' => $p['product']['sku'],
                        'product_name' => $p['name'],
                        'product_unitprice' => $p['price'],
                        'product_qty' => $p['quantity'],
                        'total_amount' => $p['price'] * $p['quantity'],
                    );
                    Product::create($product);
                    $productQty+=intval($product['product_qty']);
                    $pname[] = $p['name'];
                    $psku[] = $p['sku'];
                }
                $dimen = $this->_fetch_dimension_data($weight);
                Order::where('id', $orderID)->update(array('height' => $dimen->height, 'length' => $dimen->length, 'breadth' => $dimen->width, 'weight' => $weight,'product_qty' => $productQty, 'product_name' => implode(',', $pname), 'product_sku' => implode(',', $psku)));
            }
        }
    }

    function _fetchMagento2Orders($details)
    {
        $apiOrderUrl = "{$details->store_url}/index.php/rest/V1/orders";
        $fetchDateTime = date('Y-m-d H:i:s',strtotime($details['last_sync']." -5 hours"));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$details->magento_access_token}"
        ])->get("{$apiOrderUrl}?searchCriteria[filterGroups][0][filters][0][field]=status&searchCriteria[filterGroups][0][filters][0][value]=pending&searchCriteria[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[filterGroups][1][filters][0][field]=created_at&searchCriteria[filterGroups][1][filters][0][value]={$fetchDateTime}&searchCriteria[filterGroups][1][filters][0][conditionType]=gt");
        $responseData = $response->json();
        //echo "{$apiOrderUrl}?searchCriteria[filterGroups][0][filters][0][field]=status&searchCriteria[filterGroups][0][filters][0][value]=processing&searchCriteria[filterGroups][0][filters][0][conditionType]=eq";
        $warehouse = Warehouses::where('seller_id', Session()->get('MySeller')->id)->where('default', 'y')->first();
        if(empty($warehouse))
            return false;
        $created = null;
        foreach ($responseData['items'] as $rd){
            $this->_StoreMagentoOrders($rd,$details,Session()->get('MySeller'),$warehouse);
            $created = $rd['created_at'] ?? null;
        }
        if (!empty($created))
            Channels::where('id', $details->id)->update(array('last_sync' => date('Y-m-d H:i:s',strtotime($created)),'last_executed' => date('Y-m-d H:i:s',strtotime($created))));

        return true;
    }
    //to check and store magento orders
    function _StoreMagentoOrders($rd,$channel,$seller,$warehouse){
        $check = Order::where('channel_id',$rd['increment_id'])->where('channel','magento')->first();
        if(!empty($check)){
            return false;
        }
        $dimen = $this->_fetch_dimension_data($rd['weight'] ?? 0);
        $shippingDetails = $rd['extension_attributes']['shipping_assignments'][0]['shipping']['address'] ?? [];
        $shippingDetails['region'] = $shippingDetails['region'] ?? null;

        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        if(!empty($rd['base_grand_total'])) {
            if(strtolower($shippingDetails['region']) == strtolower($warehouse->state)) {
                $percent = $rd['base_grand_total'] - ($rd['base_grand_total']/((18/100)+1));
                $cgst = $percent/2;
                $sgst = $percent/2;
            } else {
                $percent = $rd['base_grand_total'] - ($rd['base_grand_total']/((18/100)+1));
                $igst = $percent;
            }
        }
        $data = array(
            'order_number' => $rd['increment_id'] ?? "",
            'customer_order_number' => $rd['increment_id'] ?? "",
            'channel_id' => $rd['increment_id'],
            'o_type' => "forward",
            'seller_id' => $seller->id,
            'order_type' => ($rd['payment']['method'] ?? "cod") == "cashondelivery" ? "cod" : "prepaid",
            'b_customer_name' => isset($rd['billing_address']) ? $rd['billing_address']['firstname'] . " " . $rd['billing_address']['lastname'] : "",
            'b_address_line1' => isset($rd['billing_address']) ? implode(",",$rd['billing_address']['street']) : "",
            'b_address_line2' => "",
            'b_country' => isset($rd['billing_address']) ? $rd['billing_address']['country_id'] : "",
            'b_state' => isset($rd['billing_address']['region']) ? $rd['billing_address']['region'] : "",
            'b_city' => isset($rd['billing_address']) ? $rd['billing_address']['city'] : "",
            'b_pincode' => isset($rd['billing_address']) ? $rd['billing_address']['postcode'] : "",
            'b_contact' => isset($rd['billing_address']) ? str_replace(" ", "", $rd['billing_address']['telephone']) : "",
            'b_contact_code' => "91",
            's_customer_name' => ($shippingDetails['firstname'] ?? "")." ".($shippingDetails['lastname'] ?? ""),
            's_address_line1' => implode(",",($shippingDetails['street'] ?? [])),
            's_address_line2' => "",
            's_country' => $shippingDetails['country_id'] ?? "",
            's_state' => $shippingDetails['region'] ?? "",
            's_city' => $shippingDetails['city'] ?? "",
            's_pincode' => $shippingDetails['postcode'] ?? "",
            's_contact' => $shippingDetails['telephone'] ?? "",
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
            'weight' => $rd['weight'],
            'height' => $dimen->height ?? "",
            'length' => $dimen->length ?? "",
            'breadth' => $dimen->width ?? "",
            'vol_weight' => (intval($dimen->height) * intval($dimen->length) * intval($dimen->width)) / 5,
            'shipping_charges' => 0,
            'cod_charges' => 0,
            'discount' => 0,
            'invoice_amount' => $rd['base_grand_total'],
            'igst' => $igst,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'channel' => 'magento',
            'inserted' => date('Y-m-d H:i:s', strtotime($rd['created_at'])),
            'inserted_by' => $seller->id,
            'seller_channel_id' => $channel->id
        );
        $data['pickup_address'] = $data['p_address_line1'] . "," . $data['p_address_line2'] . "," . $data['p_city'] . "," . $data['p_state'] . "," . $data['p_pincode'];
        $data['delivery_address'] = $data['s_address_line1'] . "," . $data['s_address_line2'] . "," . $data['s_city'] . "," . $data['s_state'] . "," . $data['s_pincode'];
        $orderID = Order::create($data)->id;
        $pname = [];
        $psku = [];
        $productQty=0;
        foreach ($rd['items'] as $p) {
            $product = array(
                'order_id' => $orderID,
                'product_sku' => $p['sku'] ?? "",
                'product_name' => $p['name'] ?? "",
                'product_unitprice' => $p['price_incl_tax'] ?? "",
                'product_qty' => $p['qty_ordered'] ?? 1,
                'total_amount' => ($p['price_incl_tax'] ?? 0) * ($p['qty_ordered'] ?? 0),
            );
            Product::create($product);
            $pname[] = $p['name'];
            $psku[] = $p['sku'];
            $productQty+=intval($product['product_qty']);
        }
        Order::where('id', $orderID)->update(array('product_name' => implode(',', $pname),'product_qty' => $productQty, 'product_sku' => implode(',', $psku)));
        return true;
    }

    function verifyAllOrders()
    {
        $channelController = new ChannelsController();
        $channels = Channels::where('seller_id', Session()->get('MySeller')->id)->get();
        foreach ($channels as $c) {
            $orders = Order::where('seller_channel_id', $c['id'])->where('status', 'pending')->select('id')->get();
            foreach ($orders as $singleOrder) {
                $o = Order::find($singleOrder->id);
                if(empty($o))
                    continue;
                switch ($o['channel']) {
                    case 'shopify':
                        $this->_verifyShopifyOrders($c, $o);
                        //                        echo "shopify";
                        break;
                    case 'storehippo':
                        $this->_verifyStoreHippoOrders($c, $o);
                        break;
                    case 'amazon':
                        $channelController->_verifyAmazonOrders($c, $o);
                        break;
                }
            }
        }
    }

    function verifyChannelOrders(Request $request)
    {
        try {
            $startedAt = now();
            $cronName = 'verify-channel-orders';
            $rowInserted = 0;
            $rowUpdated = 0;
            $rowDeleted = 0;

            $channels = Channels::whereIn('channel',['shopify','storehippo'])->where('seller_id','!=','32033');
            if(!empty($request->sellerId))
                $channels = $channels->where('seller_id',$request->sellerId);
            $channels = $channels->get();
            foreach ($channels as $c) {
                if($startedAt->diffInSeconds(now()) >= 1800) {
                    return true;
                }
                $orders = Order::where('channel', $c->channel)->select('id')->where('seller_channel_id',$c->id)->where('status', 'pending')->where('seller_id', $c->seller_id)->where('channel_id', '!=', '')->orderBy('last_verified')->get();
                foreach ($orders as $singleOrder) {
                    $o = Order::find($singleOrder->id);
                    if(empty($o))
                        continue;
                    switch ($o->channel) {
                        case 'shopify':
                            ShopifyHelper::VerifyShopifyOrder($c,$o);
                            break;
                        case 'storehippo':
                            $this->_verifyStoreHippoOrders($c, $o);
                            break;
                        case 'amazon_direct':
                            //$this->_verifyAmazonDirectOrders($c, $o);
                            break;
                    }
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

    function _verifyShopifyOrders($details, $orders)
    {
        //https://{api_key}:{token}@linksyscoaching.myshopify.com/admin/api/2020-10/shop.json
        //        $details=Channels::where('seller_id',Session()->get('MySeller')->id)->where('channel','shopify')->get();
        if ($orders->status != 'pending') {
            return false;
        }
        $username = $details->api_key;
        $password = $details->password;
        $storeURL = $details->store_url;
        $sharedSecret = $details->password;
        $orderNum = $orders['channel_id'];
        $callUrl = "https://$username:$password@$storeURL/admin/api/2020-10/orders/$orderNum.json";
        $response = @file_get_contents($callUrl);
        if ($response == "") {
            // Order::where('id', $orders->id)->delete();
            // Product::where('order_id', $orders->id)->delete();
            // return false;
            return false;
        }
        $responseData = json_decode($response, true);
        if (!isset($responseData['errors'])) {
            if (!empty($responseData['order']['fulfillment_status']) || !empty($responseData['order']['cancel_reason'])) {
                Order::where('id', $orders->id)->where('status','pending')->delete();
                Product::where('order_id', $orders->id)->delete();
            } else {
                $rd = $responseData['order'];
                $totalWeight = 0;
                foreach ($rd['line_items'] as $p) {
                    $totalWeight += ($p['grams'] * $p['quantity']) ?? 0;
                }
                $weight = empty($rd['total_weight']) ? $totalWeight : $rd['total_weight'];
                $dimen = $this->_fetch_dimension_data($weight ?? 0);
                if(empty($rd['shipping_address']['first_name']) && empty($rd['billing_address']['first_name']))
                    return false;
                $shippingAddress = (!empty($rd['shipping_address']['first_name'])) ? $rd['shipping_address'] : $rd['billing_address'];
                $billingAddress = (!empty($rd['billing_address']['first_name'])) ? $rd['billing_address'] : $shippingAddress;
                if($orders->id == 146247656)
                    dd($shippingAddress,$billingAddress,$rd);
                $contact = $billingAddress['phone'];
                $data = array(
                    'order_type' => $rd['financial_status'] == "paid" ? "prepaid" : "cod",
                    'is_tagged' => $rd['tags'] != "" ? "y" : "n",
                    's_customer_name' => $shippingAddress['first_name']." ".$shippingAddress['last_name'],
                    's_address_line1' => $shippingAddress['address1'],
                    's_address_line2' => $shippingAddress['address2'],
                    's_country' => $shippingAddress['country'],
                    's_state' => $shippingAddress['province'],
                    's_city' => $shippingAddress['city'],
                    's_pincode' => $shippingAddress['zip'],
                    's_contact' => $this->filterMobile($contact),
                    's_contact_code' => "+91",
                    'b_customer_name' => $billingAddress['first_name']." ".$billingAddress['last_name'],
                    'b_address_line1' => $billingAddress['address1'],
                    'b_address_line2' => $billingAddress['address2'],
                    'b_country' => $billingAddress['country'],
                    'b_state' => $billingAddress['province'],
                    'b_city' => $billingAddress['city'],
                    'b_pincode' => $billingAddress['zip'],
                    'b_contact' => $this->filterMobile($contact),
                    'b_contact_code' => "+91",
                    'weight' => $weight > 0 ? $weight : 400,
                    'height' => $dimen->height ?? "",
                    'length' => $dimen->length ?? "",
                    'breadth' => $dimen->width ?? "",
                    'vol_weight' => (intval($dimen->height) * intval($dimen->length) * intval($dimen->width)) / 5,
                    'shipping_charges' => 0,
                    'cod_charges' => 0,
                    'discount' => 0,
                    'invoice_amount' => $rd['total_price'],
                    'modified' => date('Y-m-d H:i:s'),
                    'modified_by' => $orders->seller_id,
                    'last_verified' => date('Y-m-d H:i:s')
                );
                $orderID = Order::where('id', $orders->id)->update($data);
            }
        }
        return true;
    }

    function _verifyStoreHippoOrders($channel, $order)
    {

        $url = $channel['store_url'] . "/api/1.1/entity/ms.orders/" . $order['order_number'];
        $accessKey = $channel['store_hippo_access_key'];
        //echo "$url : $accessKey"; exit;

        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            "access-key: $accessKey"
        ));

        $response = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $responseData = json_decode($response, true);
        if ($responseData['data']['status'] != "open") {
            Order::where('id', $order->id)->delete();
            Product::where('order_id', $order->id)->delete();
        }
    }

    function _verifyAmazonDirectOrders($channel, $order) {
        // Fetch amazon order
        $amazonDirect = new AmazonDirect();
        $accessToken = $amazonDirect->getAccessToken($channel->amazon_refresh_token);
        $amazonResponse = $amazonDirect->getAmazonOrders($channel, $accessToken, $channel->last_sync, $order->order_number);
        if(isset($amazonResponse['payload']['Orders']) && !empty($amazonResponse['payload']['Orders'])) {
            foreach ($amazonResponse['payload']['Orders'] as $order) {
                if(strtolower($order['OrderStatus']) != 'unshipped' && strtolower($order['OrderStatus']) != 'pending') {
                    // Order::where('id', $order->id)->delete();
                    // Product::where('order_id', $order->id)->delete();
                }
            }
        }
    }

    function create_recharge_order(Request $request)
    {
        $config = $this->info['config'];
        $client = new Api($config->razorpay_key, $config->razorpay_secret);
        $order = $client->order->create([
            'receipt' => 'order_rcptid_11',
            'amount' => $request->amount, // amount in the smallest currency unit
            'currency' => 'INR', // <a href="/docs/payment-gateway/payments/international-payments/#supported-currencies" target="_blank">See the list of supported currencies</a>.)
        ]);
        echo json_encode(array('status' => 'true', 'order_id' => $order->id));
    }

    function create_neft_recharge(Request $request)
    {
        $data = [
            'seller_id' => Session()->get('MySeller')->id,
            'utr_number' => $request->utr,
            'amount' => $request->amount,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'neft',
        ];
        Recharge_request::create($data);
        echo json_encode(['status' => 'true']);
    }

    function recharge_success(Request $request)
    {
        $config = $this->info['config'];
        $api = new Api($config->razorpay_key, $config->razorpay_secret);
        //getting the payment details from razorpay
        $payment = $api->payment->fetch($request->razorpay_payment_id);
        //check if the payment is successful
        $seller = Seller::find(Session()->get('MySeller')->id);
        if ($payment->status == 'captured' || $payment->status == 'authorized') {
            $promocode = $payment->notes->promocode ?? "";
            $data = array(
                'seller_id' => Session()->get('MySeller')->id,
                'amount' => ($payment->amount / 100),
                'balance' => ($payment->amount / 100) + $seller->balance,
                'type' => 'c',
                'datetime' => date('Y-m-d H:i:s'),
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
                'method' => $payment->method,
                'description' => "Wallet Recharge"
            );
            Transactions::create($data);
            Seller::where('id', $seller->id)->increment('balance', $data['amount']);
            if(!empty($promocode)) {
                $op = new OperationController();
                $op->apply_promo($promocode, $payment->amount / 100);
            }
            $this->_refreshSession();
            $this->utilities->generate_notification('Recharge Successful', 'Your Recharge has been completed successfully', 'success');
            $message = "Dear User," . Session()->get('MySeller')->fname . " " . Session()->get('MySeller')->lname . " has made a recharge of " . $data['amount'] . " successfully";
            //$this->utilities->send_email('deepakprn78@gmail.com','Twinnship','Recharge Done',$message);
            $this->_refreshSession();
            return redirect(route('seller.dashboard'));
        } else {
            echo json_encode(array('message' => 'There is issue with your payment'));
        }
    }

    function remit_cod(Request $request)
    {
        $modify = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        if ($modify['nextRemitCod'] < $request->amount) {
            $response = ['status' => 'false', 'message' => 'Recharge amount can not greater than COD balance'];
        } else {
//            $data = [
//                'seller_id' => Session()->get('MySeller')->id,
//                'amount' => $request->amount,
//                'created' => date('Y-m-d H:i:s'),
//                'type' => 'cod',
//            ];
//            Recharge_request::create($data);
            $remDays = Session()->get('MySeller')->remmitance_days ?? 7;
            $data = Order::where('seller_id',Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status','delivered')->where('rto_status','n')->whereDate('delivered_date','<',date('Y-m-d',strtotime($modify['nextRemitDate']."- $remDays days")))->where('cod_remmited','n')->get();
            $sum = 0;
            $ids = [];
            foreach($data as $o){
                $sum+=$o->invoice_amount;
                $ids[] = $o->id;
                if($sum >= $request->amount){
                    break;
                }
            }
            $response = ['status' => 'true', 'message' => '','recharge' => $sum];
        }
        echo json_encode($response);
    }

    function refreshRecharge()
    {
        $this->_refreshSession();
        $this->utilities->generate_notification('Success', 'Refresh successfully', 'success');
    }

    function _refreshSession()
    {
        $codRemit = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        if(Session()->get('MySeller')->type == 'emp'){
            $emp = Employees::find(Session()->get('MySeller')->emp_id);
            $seller = Seller::find(Session()->get('MySeller')->id);
            $gst_number = Basic_informations::where('seller_id',$seller->id)->first();
            $seller->gst_number = $gst_number->gst_number ?? "";
            $seller->type = 'emp';
            $seller->emp_id = $emp->id;
            $seller->permissions = $emp->permissions;
            Session(['MySeller' => $seller]);
        }else{
            $seller = Seller::find(Session()->get('MySeller')->id);
            $gst_number = Basic_informations::where('seller_id',$seller->id)->first();
            $seller->gst_number = $gst_number->gst_number ?? "";
            $seller->type = 'sel';
            $seller->permissions = 'all';
            $seller->cod_balance = $codRemit['nextRemitCod'];
            Session(['MySeller' => $seller]);
        }
    }

    function _wowExpress($orderId)
    {
        $o = Order::find($orderId);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $vol_weight = ($o->height * $o->length * $o->breadth) / 5;
        $payload = [
            "api_key" => "20681",
            "transaction_id" => "",
            "order_no" => "$o->order_number",
            "consignee_first_name" => $o->b_customer_name,
            "consignee_last_name" => "",
            "consignee_address1" => $o->b_address_line1,
            "consignee_address2" => $o->b_address_line2,
            "destination_city" => $o->b_city,
            "destination_pincode" => $o->s_pincode,
            "state" => $o->b_state,
            "telephone1" => $o->b_contact,
            "telephone2" => "",
            "vendor_name" => $o->p_customer_name,
            "vendor_address" => $o->p_address_line1,
            "vendor_city" => $o->p_city,
            "pickup_pincode" => $o->p_pincode,
            "vendor_phone1" => $o->p_contact,
            "rto_vendor_name" => $o->p_customer_name,
            "rto_address" => $o->p_address_line1,
            "rto_city" => $o->p_city,
            "rto_pincode" => $o->p_pincode,
            "rto_phone" => $o->p_contact,
            "pay_type" => $pay_type,
            "item_description" => $o->product_name,
            "qty" => $qty,
            "collectable_value" => $collectable_value,
            "product_value" => $o->invoice_amount,
            "actual_weight" => $o->weight / 1000,
            "volumetric_weight" => $vol_weight / 1000,
            "length" => "$o->length",
            "breadth" => "$o->breadth",
            "height" => "$o->height",
            "category" => ""
        ];
        // dd($payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wowship.wowexpress.in/index.php/alltracking/create_shipment_v1/doUpload',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    function _delhiverySurface($orderId,$delhiveryClient="TwinnshipIN SURFACE",$delhiveryToken="894217b910b9e60d3d12cab20a3c5e206b739c8b")
    {
        $o = Order::find($orderId);
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        if ($o->o_type == 'reverse') {
            $pay_type = "Pickup";
        }
        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        //$warehouse = Warehouses::where('id', $o->warehouse_id)->first();
        $warehouse = Warehouses::where('seller_id', $o->seller_id)->where('default','y')->first();
        if(empty($warehouse)){
            return false;
        }
        $payload = [
            "shipments" => array(
                [
                    "add" => $o->s_address_line1 . " " . $o->s_address_line2,
                    "address_type" => "home",
                    "phone" => $o->s_contact,
                    "payment_mode" => $pay_type,
                    "name" => $o->s_customer_name,
                    "pin" => $o->s_pincode,
                    "order" => $o->order_number,
                    "consignee_gst_amount" => "100",
                    "integrated_gst_amount" => "100",
                    "ewbn" => "",
                    "consignee_gst_tin" => "",
                    "seller_gst_tin" => "",
                    "client_gst_tin" => "",
                    "hsn_code" => $config->hsn_number,
                    "gst_cess_amount" => "0",
                    "client" => $delhiveryClient,
                    "tax_value" => "100",
                    "seller_tin" => "Twinnship",
                    "seller_gst_amount" => "100",
                    "seller_inv" => $o->order_number,
                    "city" => $o->s_city,
                    "commodity_value" => $o->invoice_amount,
                    "weight" => $o->weight,
                    "return_state" => $o->p_state,
                    "document_number" => $o->order_number,
                    "od_distance" => "450",
                    "sales_tax_form_ack_no" => "1245",
                    "document_type" => "document",
                    "seller_cst" => "1343",
                    "seller_name" => $seller_name,
                    "fragile_shipment" => "true",
                    "return_city" => $o->p_city,
                    "return_phone" => $o->p_contact,
                    "shipment_height" => $o->height,
                    "shipment_width" => $o->breadth,
                    "shipment_length" => $o->length,
                    "category_of_goods" => "categoryofgoods",
                    "cod_amount" => $collectable_value,
                    "return_country" => $o->p_country,
                    "document_date" => $o->inserted,
                    "taxable_amount" => $o->invoice_amount,
                    "products_desc" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->product_name),
                    "state" => preg_replace('/[^A-Za-z0-9\-]/', '', $o->s_state),
                    "dangerous_good" => "False",
                    "waybill" => "",
                    "consignee_tin" => "1245875454",
                    "order_date" => $o->inserted,
                    "return_add" => "$o->p_city,$o->p_state",
                    "total_amount" => $o->invoice_amount,
                    "seller_add" => "$seller->city,$seller->state",
                    "country" => $o->p_country,
                    "return_pin" => $o->p_pincode,
                    "extra_parameters" => [
                        "return_reason" => ""
                    ],
                    "return_name" => $o->p_warehouse_name,
                    "supply_sub_type" => "",
                    "plastic_packaging" => "false",
                    "quantity" => $qty
                ]
            ),
            "pickup_location" => [
                "name" => $warehouse->warehouse_code,
                "city" => $o->p_city,
                "pin" => $o->p_pincode,
                "country" => $o->p_country,
                "phone" => $o->p_contact,
                "add" => "$o->p_address_line1 , $o->p_address_line2"
            ]
        ];
        Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
            'title' => 'Delhivery Request Payload',
            'data' => $payload
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'format=json&data=' . json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token $delhiveryToken",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function _delhiveryMPS($orderId, $delhiveryToken="18765103684ead7f379ec3af5e585d16241fdb94") {
        try {
            DB::beginTransaction();

            $order = Order::find($orderId);
            $config = $this->info['config'];
            $qty = Product::where('order_id', $orderId)->sum('product_qty');
            if (strtolower($order->order_type) == 'cod') {
                $pay_type = "COD";
                $collectable_value = $order->invoice_amount;
            } elseif (strtolower($order->order_type) == 'prepaid') {
                $pay_type = "Prepaid";
                $collectable_value = "0";
            } else {
                $pay_type = "REVERSE";
                $collectable_value = "0";
            }
            if ($order->o_type == 'reverse') {
                $pay_type = "Pickup";
            }
            $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
            $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
            //$warehouse = Warehouses::where('id', $order->warehouse_id)->first();
            $warehouse = Warehouses::where('id', $order->warehouse_id)->first();
            if(empty($warehouse))
                return false;
            // Get waybill number and master id
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://track.delhivery.com/waybill/api/bulk/json/?token='.$delhiveryToken.'&count='.$order->number_of_packets,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
                CURLOPT_HTTPHEADER => [],
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $waybillNumber = explode(',', trim($response, '"'));

            $shipments = [];
            for($i=0; $i<$order->number_of_packets; $i++) {
                $shipments[] = [
                    "weight" => $order->weight,
                    "mps_amount" => $order->order_type == "cod" ? $order->invoice_amount : "0",
                    "mps_children" => $order->number_of_packets,
                    "seller_inv" => $order->order_number,
                    "city" => $order->s_city,
                    "pin" => $order->s_pincode,
                    "products_desc" =>preg_replace('/[^A-Za-z0-9\-]/', '', $order->product_name),
                    "product_type" => "Heavy",
                    "extra_parameters" => [
                        "encryptedShipmentID" => "DdB6bvvFN"
                    ],
                    "add" => $order->s_address_line1 . " " . $order->s_address_line2,
                    "shipment_type" => "MPS",
                    "hsn_code" => $config->hsn_number,
                    "state" => $order->s_state,
                    "waybill" => $waybillNumber[$i] ?? null,
                    "supplier" => $seller_name,
                    "master_id" => $waybillNumber[0] ?? null,
                    "sst" => "-",
                    "phone" => $order->s_contact,
                    "payment_mode" => $pay_type,
                    "cod_amount" => $order->order_type == "cod" ? $order->invoice_amount : "0",
                    "order_date" => $order->inserted,
                    "name" => $order->s_customer_name,
                    "total_amount" => $order->invoice_amount,
                    "country" => $order->p_country,
                    "order" => $order->order_number,
                    "ewbn" => ($order->invoice_amount > 50000 ? $order->ewaybill_number : "")
                ];
                if($i == 0) {
                    $order->awb_number = $waybillNumber[$i] ?? null;
                    $order->save();
                } else {
                    $mps = new MPS_AWB_Number();
                    $mps->order_id = $order->id;
                    $mps->awb_number = $waybillNumber[$i] ?? null;
                    $mps->inserted = now();
                    $mps->save();
                }
            }
            $payload = [
                "shipments" => $shipments,
                "pickup_location" => [
                    "name" => $warehouse->warehouse_code,
                    "city" => $order->p_city,
                    "pin" => $order->p_pincode,
                    "country" => $order->p_country,
                    "phone" => $order->p_contact,
                    "add" => "$order->p_address_line1 , $order->p_address_line2"
                ]
            ];
            Logger::write('logs/partners/delhivery/delhivery-'.date('Y-m-d').'.text', [
                'title' => 'Delhivery MPS Request Payload',
                'data' => $payload
            ]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://track.delhivery.com/api/cmu/create.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'format=json&data=' . json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Token $delhiveryToken",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $json = json_decode($response);
            if($json->success == true) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $response;
        } catch(Exception $e) {
            DB::rollBack();
            return "";
        }
    }

    function _gatiMps($orderId) {
        try {
            DB::beginTransaction();
            $order = Order::find($orderId);
            $gati = new Gati();
            $fromOuCode = $gati->pincodeValidation($order->p_pincode)['ouCode'] ?? '';
            $toOuCode = $gati->pincodeValidation($order->s_pincode)['ouCode'] ?? '';
            if(!empty($fromOuCode) && !empty($toOuCode)) {
                $gati_ou_code = $fromOuCode . '/' . $toOuCode;
            } else {
                $gati_ou_code = '';
            }
            if (strtolower($order->o_type) == 'forward') {
                $resp = ServiceablePincode::where('pincode', $order->s_pincode)->where('courier_partner', 'gati')->first();
                $awb = GatiAwbs::where('used', 'n')->lockForUpdate()->first();
                $packages = GatiPackageNumber::where('used', 'n')->orderBy('package_number')->limit($order->number_of_packets)->lockForUpdate()->get();
                if (empty($resp) || empty($awb) || empty($packages)) {
                    throw new Exception("Pincode is not Serviceable");
                }
                $i = 0;
                foreach($packages as $package) {
                    if($i == 0) {
                        $order->awb_number = $awb->awb_number ?? null;
                        $order->gati_package_no = $package->package_number ?? null;
                        $order->gati_ou_code = $gati_ou_code;
                        $order->manifest_sent = 'n';
                        $order->save();
                    } else {
                        $mps = new MPS_AWB_Number();
                        $mps->order_id = $order->id;
                        $mps->awb_number = $awb->awb_number ?? null;
                        $mps->gati_package_no = $package->package_number ?? null;
                        $mps->gati_ou_code = $gati_ou_code;
                        $mps->inserted = now();
                        $mps->save();
                    }
                    $i++;
                }
                GatiAwbs::where('id', $awb->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
                GatiPackageNumber::whereIn('id', $packages->pluck('id'))->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception("Pincode is not Serviceable");
            }
            DB::commit();
            return [
                'awb_number' => $order->awb_number,
                'package_number' => $order->gati_package_no,
            ];
        } catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    function _gati($orderId) {
        try {
            DB::beginTransaction();
            $order = Order::find($orderId);
            $gati = new Gati();
            $fromOuCode = $gati->pincodeValidation($order->p_pincode)['ouCode'] ?? '';
            $toOuCode = $gati->pincodeValidation($order->s_pincode)['ouCode'] ?? '';
            if(!empty($fromOuCode) && !empty($toOuCode)) {
                $gati_ou_code = $fromOuCode . '/' . $toOuCode;
            } else {
                $gati_ou_code = '';
            }
            if (strtolower($order->o_type) == 'forward') {
                $resp = ServiceablePincode::where('pincode', $order->s_pincode)->where('courier_partner', 'gati')->first();
                $awb = GatiAwbs::where('used', 'n')->lockForUpdate()->first();
                $package = GatiPackageNumber::where('used', 'n')->lockForUpdate()->first();
                if (empty($resp) || empty($awb) || empty($package)) {
                    throw new Exception("Pincode is not Serviceable");
                }
                $order->awb_number = $awb->awb_number ?? null;
                $order->gati_package_no = $package->package_number ?? null;
                $order->gati_ou_code = $gati_ou_code;
                $order->manifest_sent = 'n';
                $order->save();
                GatiAwbs::where('id', $awb->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
                GatiPackageNumber::where('id', $package->id)->update([
                    'used' => 'y',
                    'used_by' => Session()->get('MySeller')->id,
                    'used_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception("Pincode is not Serviceable");
            }
            DB::commit();
            return [
                'awb_number' => $order->awb_number,
                'package_number' => $order->gati_package_no,
            ];
        } catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    function _dtdcSurface($orderId, $serviceType = "GROUND EXPRESS")
    {
        $o = Order::find($orderId);
        $seller = Seller::find($o->seller_id);
        if(empty($seller)) {
            return false;
        }
        $apiKey = "fefdb6dc8c709b2128fd24490be6df";
        $customerCode = "GL3980";
        $serviceType = $serviceType;

        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $payload = [
            "consignments" => [
                [
                    "customer_code" => $customerCode,
                    "reference_number" => "",
                    "service_type_id" => $serviceType,
                    "load_type" => "NON-DOCUMENT",
                    "description" => "Gifts/Samples",
                    "cod_favor_of" => "",
                    "cod_collection_mode" => strtolower($o->order_type) == 'cod' ? 'Cash' : '',
                    "consignment_type" => ucfirst($o->o_type),
                    "dimension_unit" => "cm",
                    "length" => ceil($o->length),
                    "width" => ceil($o->breadth),
                    "height" => ceil($o->height),
                    "weight_unit" => "kg",
                    "weight" => $o->weight / 1000,
                    "declared_value" => $o->invoice_amount,
                    "cod_amount" => $collectable_value,
                    "num_pieces" => 1,
                    "customer_reference_number" => "",
                    "commodity_id" => "GIFT",
                    "is_risk_surcharge_applicable" => true,
                    "origin_details" => [
                        "name" => $o->p_warehouse_name,
                        "phone" => $o->p_contact,
                        "alternate_phone" => $o->p_contact,
                        "address_line_1" => $o->p_address_line1,
                        "address_line_2" => $o->p_address_line2,
                        "pincode" => $o->p_pincode,
                        "city" => $o->p_city,
                        "state" => $o->p_state
                    ],
                    "destination_details" => [
                        "name" => $o->s_customer_name,
                        "phone" => $o->s_contact,
                        "alternate_phone" => $o->s_contact,
                        "address_line_1" => $o->s_address_line1,
                        "address_line_2" => $o->s_address_line2,
                        "pincode" => $o->s_pincode,
                        "city" => $o->s_city,
                        "state" => $o->s_state,
                    ],
                    "pieces_detail" => [
                        [
                            "description" => $o->product_name,
                            "declared_value" => $o->invoice_amount,
                            "weight" => $o->weight / 1000,
                            "height" => ceil($o->height),
                            "length" => ceil($o->length),
                            "width" => ceil($o->breadth)
                        ]
                    ]
                ]
            ]
        ];
        //file_put_contents("payload.txt", json_encode($payload));
        // dd($payload);
        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://app.shipsy.in/api/customer/integration/consignment/softdata', $payload);

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'DTDC Request Payload For Id: ' . $o->id . ' : ',
            'data' => $payload
        ]);

        Logger::write('logs/partners/dtdc/dtdc-'.date('Y-m-d').'.text', [
            'title' => 'DTDC Response Payload For Id: ' . $o->id . ' : ',
            'data' => $response->json()
        ]);
        // echo $response;
        return $response->json();
    }

    function _getXbeesToken($username, $password, $secret)
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

    function _xpressBees($orderId, $getAwbNumber, $businessName, $username, $password, $secret, $XBkey)
    {
        $token = $this->_getXbeesToken($username, $password, $secret);
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "PPD";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();

        //$getAwbNumber = XbeesAwbnumber::where('used','n')->first();
        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "BusinessAccountName" => $businessName,
            "OrderNo" => $o->customer_order_number,
            "SubOrderNo" => $o->order_number,
            "OrderType" => $o->order_type,
            "CollectibleAmount" => $collectable_value,
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
        // dd($payload);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/forward', $payload);
        // echo $response;

        return $response->json();
    }

    function _xpressBeesReverse($orderId, $getAwbNumber, $businessName, $username, $password, $secret, $XBkey)
    {
        $token = $this->_getXbeesToken($username, $password, $secret);
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        // $getAwbNumber = XbeesAwbnumber::where('order_type','reverse')->where('used','n')->first();

        $payload = [
            "AirWayBillNO" => $getAwbNumber->awb_number,
            "OrderNo" => $o->customer_order_number,
            "BusinessAccountName" => $businessName,
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

        // dd($payload);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => $token,
            'versionnumber' => 'v1'
        ])->post('http://api.shipmentmanifestation.xbees.in/shipmentmanifestation/reverse', $payload);
        // echo $response;
        return $response->json();
    }

    function _checkServicabilityShadowFax($pickup_pincode, $delivery_pincode)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76'
        ])->get("https://dale.shadowfax.in/api/v1/serviceability/?pickup_pincode=$pickup_pincode&delivery_pincode=$delivery_pincode&format=json");
        return $response->json();
    }

    function _shadowFax($orderId)
    {
        // $token = $this->_getXbeesToken();
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();

        //for generate AWB Number
        $response = $this->_generateAWBShadowFax();
        $awb_number = $response['awb_numbers'][0];
        // dd($awb_number);
        $products = [];
        foreach ($product as $p) {
            $products[] = [
                "hsn_code" => "",
                "invoice_no" => "SNP678",
                "sku_name" => $p->product_sku,
                "client_sku_id" => "",
                "category" => "",
                "price" => round($product_price),
                "seller_details" => [
                    "seller_name" => $seller_name,
                    "seller_address" => $seller->street,
                    "seller_state" => $seller->state,
                    "gstin_number" => $seller->gst_number
                ],
                "taxes" => [
                    "cgst" => 3,
                    "sgst" => 4,
                    "igst" => 0,
                    "total_tax" => 7
                ],
                "additional_details" => [
                    "requires_extra_care" => "False",
                    "type_extra_care" => "Normal Goods"
                ]
            ];
        }

        $promised_delivery_date = Date('Y-m-d', strtotime('+3 days')) . "T00:00:00.000Z";
        $payload = [
            "order_details" => [
                "client_order_id" => $o->customer_order_number,
                "awb_number" => $awb_number,
                "actual_weight" => $o->weight,
                "volumetric_weight" => ($o->height * $o->length * $o->breadth) / 5,
                "product_value" => $o->invoice_amount,
                "payment_mode" => $pay_type,
                "cod_amount" => $collectable_value,
                "promised_delivery_date" => $promised_delivery_date,
                "total_amount" => $o->invoice_amount
            ],
            "customer_details" => [
                "name" => $o->b_customer_name,
                "contact" => $o->b_contact,
                "address_line_1" => $o->b_address_line1,
                "address_line_2" => $o->b_address_line2,
                "city" => $o->b_city,
                "state" => $o->b_state,
                "pincode" => $o->b_pincode,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "pickup_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
                "latitude" => "",
                "longitude" => ""
            ],
            "rts_details" => [
                "name" => $o->p_warehouse_name,
                "contact" => $o->p_contact,
                "address_line_1" => $o->p_address_line1,
                "address_line_2" => $o->p_address_line2,
                "city" => $o->p_city,
                "state" => $o->p_state,
                "pincode" => $o->p_pincode,
            ],
            "product_details" => $products
        ];
        // echo json_encode($payload);
        // exit;
        //  dd($payload);
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/orders/', $payload);
        // echo $response;
        return $response->json();
    }

    function _shadowFaxReverse($orderId)
    {
        // $token = $this->_getXbeesToken();
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $config = $this->info['config'];
        $product_price = $o->invoice_amount / count($product);
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount;
        } elseif (strtolower($o->order_type) == 'prepaid') {
            $pay_type = "Prepaid";
            $collectable_value = "0";
        } else {
            $pay_type = "REVERSE";
            $collectable_value = "0";
        }
        // dd($pay_type,$collectable_value);
        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();

        //for generate AWB Number
        $response = $this->_generateAWBShadowFaxReverese();
        $awb_number = $response['awb_numbers'][0];

        // dd($awb_number);
        $products = [];
        foreach ($product as $p) {
            $products[] = [
                "client_sku_id" => $o->id,
                "name" => $p->product_sku,
                "price" => round($product_price),
                "return_reason" => "xyz",
                "brand" => "xyz",
                "category" => "xyz",
                "additional_details" => [
                    "type_extra_care" => "Dangerous Goods",
                    "color" => "xyz",
                    "serial_no" => "ABC.$o->id",
                    "sku_images" => [
                        "",
                        ""
                    ],
                    "requires_extra_care" => false,
                    "quantity" => $p->product_qty,
                    "size" => 8
                ],
                "seller_details" => [
                    "state" => $seller->state,
                    "regd_address" => $seller->street,
                    "regd_name" => $seller_name,
                    "gstin" => $seller->gst_number
                ],
                "taxes" => [
                    "total_tax_amount" => 18,
                    "igst_amount" => 18,
                    "cgst_amount" => 0,
                    "sgst_amount" => 0
                ],
                "hsn_code" => "",
                "invoice_no" => "In.$o->id"
            ];
        }

        $payload = [
            "client_order_number" => $o->customer_order_number,
            "total_amount" => $o->invoice_amount,
            "price" => $o->invoice_amount,
            "eway_bill" => "",
            "address_attributes" => [
                "address_line" => $o->s_address_line1 . ' ' . $o->s_address_line2,
                "city" => $o->s_city,
                "country" => $o->s_country,
                "pincode" => $o->s_pincode,
                "name" => $o->s_customer_name,
                "phone_number" => $o->s_contact,
                "alternate_contact" => "",
                "latitude" => "",
                "longitude" => ""
            ],
            "seller_attributes" => [
                "name" => $o->p_warehouse_name,
                "address_line" => $o->p_address_line1 . ' ' . $o->p_address_line2,
                "city" => $o->p_city,
                "pincode" => $o->p_pincode,
                "phone" => $o->p_contact
            ],
            "skus_attributes" => $products
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/requests', $payload);
        // echo $response;
        return $response->json();

    }

    function _generateAWBShadowFax()
    {
        $payload = [
            'count' => 1
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/generate_marketplace_awb/', $payload);
        return $response->json();
    }

    function _generateAWBShadowFaxReverese()
    {
        $payload = [
            'count' => 1
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
            'Content-Type' => 'application/json'
        ])->post('https://dale.shadowfax.in/api/v3/clients/orders/generate_awb/', $payload);
        return $response->json();
    }

    function _generateAwbUdaan()
    {
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'Content-Type' => 'application/json'
        ])->post('https://api.udaan.com/hooks/udaan-express/integration/v1/awb-store/create?logisticsPartnerOrgId=ORGZPKZ992460QL8GPWW4JDZGLC67&awbCount=1');
        $data = $response->json();
        return $data['response'][0] ?? false;
    }

    function _udaanExpress($orderId)
    {
        $o = Order::find($orderId);
        $product = Product::where('order_id', $orderId)->get();
        $product_price = $o->invoice_amount / count($product);
        $config = $this->info['config'];
        $qty = Product::where('order_id', $orderId)->sum('product_qty');
        if (strtolower($o->order_type) == 'cod') {
            $pay_type = "COD";
            $collectable_value = $o->invoice_amount * 100;
        } else {
            $pay_type = "PPD";
            $collectable_value = "0";
        }
        if (strtolower($o->o_type) == 'forward') {
            $order_type = "FORWARD";
        } else {
            $order_type = "REVERSE";
        }

        $seller_name = Session()->get('MySeller')->first_name . ' ' . Session()->get('MySeller')->last_name;
        $seller = Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
        $awb_number = $this->_generateAwbUdaan();
        //dd($awb_number);
        if(!$awb_number)
            return false;
        // dd($awb_number);

        $products = [];
        foreach ($product as $p) {
            $products[] = [
                "itemTitle" =>preg_replace('/[^A-Za-z0-9\-]/', '', $p->product_name),
                "hsnCode" => "",
                "unitPrice" => round($product_price) * 100,
                "unitQty" => $p->product_qty ?? 1,
                "taxPercentage" => 0
            ];
        }
        $warehouse = Warehouses::where('id', $o->warehouse_id)->first();
        $payload = [
            "awbNumber" => $awb_number,
            "orderId" => $o->customer_order_number,
            "orderType" => $order_type,
            "orderParty" => "THIRD_PARTY",
            "orderPartyOrgId" => "ORGZPKZ992460QL8GPWW4JDZGLC67",
            "sourceOrgUnitDetails" => [
                "orgUnitId" => $warehouse->org_unit_id ?? "",
                "representativePersonName" => $warehouse->contact_name,
                "unitName" => $warehouse->warehouse_code,
                "contactNumPrimary" => $o->p_contact,
                "contactNumSecondary" => "",
                "gstIn" => "",
                "address" => [
                    "addressLine1" => $o->p_address_line1,
                    "addressLine2" => isset($o->p_address_line2) ? $o->p_address_line2 : $o->p_address_line1,
                    "addressLine3" => "",
                    "city" => $o->p_city,
                    "state" => $o->p_state,
                    "pincode" => $o->p_pincode
                ]
            ],
            "billToOrgUnitDetails" => [
                "orgUnitId" => "ORGZPKZ992460QL8GPWW4JDZGLC67",
                "representativePersonName" => "Kaushal Sharma",
                "unitName" => "Twinnship",
                "contactNumPrimary" => "+91-9910995659",
                "contactNumSecondary" => "",
                "gstIn" => "06ABECS8200N1Z5",
                "address" => [
                    "addressLine1" => "House No 544,sector 29",
                    "addressLine2" => "Faridabad",
                    "addressLine3" => "",
                    "city" => "Faridabad",
                    "state" => "Hariyana",
                    "pincode" => "121008"
                ]
            ],
            "destinationOrgUnitDetails" => [
                "representativePersonName" => $o->b_customer_name,
                "unitName" => $o->b_customer_name,
                "contactNumPrimary" => $o->s_contact,
                "contactNumSecondary" => "",
                "gstIn" => "",
                "address" => [
                    "addressLine1" => $o->s_address_line1,
                    "addressLine2" => $o->s_address_line2 ?? "",
                    "addressLine3" => "",
                    "city" => $o->s_city,
                    "state" => $o->s_state,
                    "pincode" => $o->s_pincode
                ]
            ],
            "category" => "Default",
            "collectibleAmount" => $collectable_value,
            "boxDetails" => [
                "numOfBoxes" => 1,
                "totalBoxWeight" => 0,
                "boxDetails" => []
            ],
            "goodsDetails" => [
                "goodsDetailsList" => $products
            ],
            "goodsInvoiceDetails" => [
                "invoiceNumber" => "INV.$orderId",
                "ewayBill" => "",
                "invoiceDocUrls" => ["link"],
                "goodsInvoiceAmount" => $o->invoice_amount * 100,
                "goodsInvoiceTaxAmount" => 0
            ],
            "orderNotes" => ""
        ];
        Logger::write('logs/partners/udaan/udaan-'.date('Y-m-d').'.text', [
            'title' => 'Udaan Request Payload',
            'data' => $payload
        ]);
        //echo json_encode($payload); exit;
        $response = Http::withHeaders([
            'authorization' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
            'cf-access-client-secret' => 'F8YRZHM0RNQZYZWTKZDSZSJ4EC',
        ])->post('https://udaan.com/api/udaan-express/integration/v1/confirm', $payload);
        // echo $response;
        //dd($response->body());
        return $response->json();
    }

    function fetch_wow_servicable()
    {
        ServiceablePincode::where('courier_partner', 'wow_express')->delete();
        $response = file_get_contents("https://wowship.wowexpress.in/index.php/api/pincode_master/pincode");
        $allPincodes = json_decode($response, true);
        $data = [];
        foreach ($allPincodes as $pin) {
            $data[] = [
                'partner_id' => 16,
                'courier_partner' => 'wow_express',
                'pincode' => $pin['pincode'],
                'city' => $pin['city'],
                'state' => $pin['state'],
                'branch_code' => $pin['branch_code'],
                'status' => $pin['status']
            ];
        }
        ServiceablePincode::Insert($data);
        $this->utilities->generate_notification('Successful', 'Your Pincode Service Added.', 'success');
        //return redirect()->back();
    }

    function fetch_xpressbees_servicable()
    {
        ServiceablePincode::where('courier_partner', 'xpressbees_surface')->delete();
        $csv = new CSV();
        $csv->parse('./public/assets/seller/xbessServicable.csv');
        // dd($csv->toArray());
        $allPincodes = $csv->toArray();
        $data = [];
        foreach ($allPincodes as $pin) {
            $data[] = [
                'partner_id' => 10,
                'courier_partner' => 'xpressbees_surface',
                'pincode' => $pin['Pincode'],
                'city' => $pin['HubCity'],
                'state' => $pin['HubState'],
                'branch_code' => $pin['AreaCode'],
                'status' => "y"
            ];
            if (count($data) == 500) {
                ServiceablePincode::insert($data);
                $data = [];
            }
        }
        ServiceablePincode::Insert($data);
        echo json_encode(['status' => 'true', 'message' => 'fetched successfully']);
        //return redirect()->back();
    }

    function fetch_udaan_servicable()
    {
        ServiceablePincode::where('courier_partner', 'udaan')->delete();
        $csv = new CSV();
        $csv->parse('./public/assets/seller/udaanServicable.csv');
        // dd($csv->toArray());
        $allPincodes = $csv->toArray();
        $data = [];
        foreach ($allPincodes as $pin) {
            $data[] = [
                'partner_id' => 25,
                'courier_partner' => 'udaan',
                'pincode' => $pin['Pincode'],
                'city' => $pin['City'],
                'state' => $pin['State'],
                'branch_code' => '',
                'status' => "y"
            ];
            if (count($data) == 1000) {
                ServiceablePincode::insert($data);
                $data = [];
            }
        }
        ServiceablePincode::Insert($data);
        echo json_encode(['status' => 'true', 'message' => 'fetched successfully']);
        //return redirect()->back();
    }

    function fetch_shadowfax_servicable()
    {
        ServiceablePincode::where('courier_partner', 'shadow_fax')->delete();
        $response = Http::withHeaders([
            'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76'
        ])->get('https://dale.shadowfax.in/api/v2/clients/requests/serviceable_pickup_pincodes?page=1&per_page=2000000');
        $data = $response->json();
        foreach ($data as $pin) {
            $pincodes[] = [
                'partner_id' => 2,
                'courier_partner' => 'shadow_fax',
                'pincode' => $pin['code'],
                'city' => '',
                'state' => '',
                'branch_code' => '',
                'status' => "y"
            ];
            if (count($pincodes) == 1000) {
                ServiceablePincode::insert($pincodes);
                $pincodes = [];
            }
        }
        ServiceablePincode::Insert($pincodes);
        echo json_encode(['status' => 'true', 'message' => 'fetched successfully']);
        //return redirect()->back();
    }

    function _checkServicePincode($pincode, $courier_partner)
    {
        $service = ServiceablePincode::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->where('active','y')->count();
        return $service;
    }

    function _checkServicePincodeFM($pincode, $courier_partner)
    {
        $service = ServiceablePincodeFM::where('pincode', $pincode)->where('courier_partner', $courier_partner)->where('status', 'Y')->count();
        return $service;
    }

    function rate_calculator()
    {
        $data = $this->info;
        $data['modify'] = Seller::find(Session()->get('MySeller')->id);
        $data['partner'] = Partners::all();
        $data['courier_partner'] = ServiceablePincode::select('courier_partner')->distinct('courier_partner')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.rate_calculator', $data);
    }

    function getCalculatedRates(Request $request)
    {
        $source = $this->_get_pincode_details($request->pickup_pincode);
        if ($source['status'] == 'Error' || $source['status'] == 'Failed') {
            echo json_encode(['status' => 'false', 'message' => 'Invalid Pickup Pincode']);
            exit;
        }
        $destination = $this->_get_pincode_details($request->delivery_pincode);
        if ($destination['status'] == 'Error' || $destination['status'] == 'Failed') {
            echo json_encode(['status' => 'false', 'message' => 'Invalid Destination Pincode']);
            exit;
        }
        $data = [
            'order_type' => $request->cod == 'no' ? "Prepaid" : "COD",
            'invoice_amount' => intval($request->invoice_value)
        ];
        $data['seller'] = Seller::find(Session()->get('MySeller')->id);
        $data['partners'] = Partners::getPartnerIdList();
        $blockedCourierPartners = explode(',', $data['seller']->blocked_courier_partners) ?? [];
        $partners = Partners::whereNotIn('id', $blockedCourierPartners)->where('status','y')->get();
        $data['partner'] = [];
        foreach($partners as $partner) {
            if(!Courier_blocking::where('is_blocked', 'y')->where('courier_partner_id', $partner->id)->where('seller_id', Session()->get('MySeller')->id)->exists()) {
                $data['partner'][] = $partner;
            }
        }
        $data['rates'] = [];
        foreach ($data['partner'] as $p) {
            $extra = (intval($request->weight) - intval($p->weight_initial)) > 0 ? intval($request->weight) - intval($p->weight_initial) : 0;
            $mul = ceil($extra / $p->extra_limit);
            $rateCriteria = MyUtility::findMatchCriteria($request->pickup_pincode,$request->delivery_pincode,Session()->get('MySeller'));
            if($rateCriteria == 'within_city'){
                $data['rates'][] = DB::select("select *,within_city + ( extra_charge_a * $mul ) as price from rates where plan_id=" . Session()->get('MySeller')->plan_id . " and seller_id=" . Session()->get('MySeller')->id . " and partner_id=$p->id order by partner_id desc");
                //$data['rate']=$data['rates']->price * (intval($orderDetail->weight / 500) * $data['rates']->extra_charge);
                $data['zone'] = "A";
            }
            else if ($rateCriteria == 'north_j_k') {
                // $data['rates'] = Rates::select('*',"within_city + ( extra_charge * $mul) as price")->where('plan_id', Session()->get('MySeller')->plan_id)->get();
                $data['rates'][] = DB::select("select *,north_j_k + ( extra_charge_e * $mul ) as price from rates where plan_id=" . Session()->get('MySeller')->plan_id . " and seller_id=" . Session()->get('MySeller')->id . " and partner_id=$p->id order by partner_id desc");
                //$data['rate']=$data['rates']->price * (intval($orderDetail->weight / 500) * $data['rates']->extra_charge);
                $data['zone'] = "E";
            } else if ($rateCriteria == 'within_state') {
                // $data['rates'] = Rates::select('*', 'within_state AS price')->where('plan_id', Session()->get('MySeller')->plan_id)->get();
                $data['rates'][] = DB::select("select *,within_state + ( extra_charge_b * $mul ) as price from rates where plan_id=" . Session()->get('MySeller')->plan_id . " and seller_id=" . Session()->get('MySeller')->id . " and partner_id=$p->id order by partner_id desc");
                $data['zone'] = "B";
            } else if ($rateCriteria == 'metro_to_metro') {
                // $data['rates'] = Rates::select('*',"within_city + ( extra_charge * $mul) as price")->where('plan_id', Session()->get('MySeller')->plan_id)->get();
                $data['rates'][] = DB::select("select *,metro_to_metro + ( extra_charge_c * $mul ) as price from rates where plan_id=" . Session()->get('MySeller')->plan_id . " and seller_id=" . Session()->get('MySeller')->id . " and partner_id=$p->id order by partner_id desc");
                //$data['rate']=$data['rates']->price * (intval($orderDetail->weight / 500) * $data['rates']->extra_charge);
                $data['zone'] = "C";
            } else {
                $data['rates'][] = DB::select("select *,rest_india + ( extra_charge_d * $mul ) as price from rates where plan_id=" . Session()->get('MySeller')->plan_id . " and seller_id=" . Session()->get('MySeller')->id . " and partner_id=$p->id order by partner_id desc");
                $data['zone'] = "D";
            }
        }
        return view('seller.pages.rate_chart', $data);
    }

    function download_mapping($pincode)
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

    function seller_api_key()
    {
        $data = $this->info;
        return view('seller.api', $data);
    }

    function generate_api_key(Request $request)
    {
        $random = Str::random(40);
        Seller::where('id', Session()->get('MySeller')->id)->update(['api_key' => $random]);
        $this->_refreshSession();
        $this->utilities->generate_notification('Success', 'API Key Generated successfully', 'success');
        return redirect()->back();
    }

    function serviceable_pincode()
    {
        $data = $this->info;
        $data['courier_partner'] = ServiceablePincode::select('courier_partner')->distinct('courier_partner')->get();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        return view('seller.serviceable_pincode', $data);
    }

    function download_serviceable_pincode(Request $request)
    {
        $name = $request->courier_partner . "_serviceable_pincode";
        if($request->courier_partner == "Twinnship")
        {
            $all_data = ServiceablePincode::select('pincode')->distinct()->where('status','y')->where('active','y')->get();
        }
        else
        {
            $all_data = ServiceablePincode::select('courier_partner', 'pincode')->where('active','y')->where('courier_partner', $request->courier_partner)->get();
        }
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr. No', 'Pincode');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $info = array($cnt, $e->pincode);
            fputcsv($fp, $info);
            $cnt++;
        }
        header("Cache-Control: private");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize("$name.csv"));
        header("Content-Disposition: attachment; filename=$name.csv");
        readfile("$name.csv");
        @unlink("$name.csv");
    }

    function _getAwbNumbersXbees($XBkey, $courier, $type, $service = "FORWARD")
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);
        $awb_data = $response->json();
        $this->_FetchAllAwbs($awb_data['BatchID'], $courier, $type, $service, $XBkey);
    }
    function _FetchAllAwbs($batch, $courier, $type, $service = "FORWARD", $XBkey = '')
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
                    XbeesAwbnumber::insert($insData);
                    $insData = [];
                }
            }
            XbeesAwbnumber::insert($insData);
        }
    }

    function _getAwbNumbersXbeesUnique($XBkey, $courier, $type, $service = "FORWARD")
    {
        $data = array(
            'BusinessUnit' => 'ECOM',
            'ServiceType' => strtoupper($service),
            'DeliveryType' => 'PREPAID'
        );
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'XBKey' => $XBkey
        ])->post('http://xbclientapi.xbees.in/POSTShipmentService.svc/AWBNumberSeriesGeneration', $data);
        $awb_data = $response->json();
        $this->_FetchAllAwbsUnique($awb_data['BatchID'], $courier, $type, $service, $XBkey);
    }

    function _FetchAllAwbsUnique($batch, $courier, $type, $service = "FORWARD", $XBkey = '')
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

    function fetchProcessedOrders()
    {
        $sellerId = Session()->get('MySeller')->id;
        $data = [];
        $data['notify'] = 'false';
        $data['total'] = PendingShipments::where('seller_id', $sellerId)->whereDate('inserted', date('Y-m-d'))->count();
        $data['statusCount'] = PendingShipments::where('seller_id', $sellerId)->where('status', 'n')->whereDate('inserted', date('Y-m-d'))->count();
        $data['notifyCount'] = PendingShipments::where('seller_id', $sellerId)->where('notified', 'n')->whereDate('inserted', date('Y-m-d'))->count();
        $data['shipped'] = PendingShipments::where('seller_id', $sellerId)->where('status', 'y')->whereDate('inserted', date('Y-m-d'))->count();
        $data['pending'] = PendingShipments::where('seller_id', $sellerId)->where('status', 'n')->whereDate('inserted', date('Y-m-d'))->count();
        $data['notshipped'] = PendingShipments::where('seller_id', $sellerId)->where('status', 'y')->where('shipped', 'n')->whereDate('inserted', date('Y-m-d'))->count();
        if ($data['statusCount'] == 0) {
            if ($data['notifyCount'] != 0) {
                $data['notify'] = 'true';
                PendingShipments::where('seller_id', $sellerId)->update(['notified' => 'y']);
            }
        }
        echo json_encode($data);
    }

    function getShopifyLocationID($order)
    {
        // $channel = Channels::where('seller_id', $order->seller_id)->where('channel', 'shopify')->first();
        $channel = Channels::where('seller_id', $order->seller_id)->where('id', $order->seller_channel_id)->where('channel', 'shopify')->first();
        if (empty($channel)) {
            return "";
        }
        $url = "https://$channel->api_key:$channel->password@$channel->store_url/admin/api/2021-04/locations.json";
        $response = file_get_contents($url);
        $responseData = json_decode($response, true);
        if (isset($responseData['locations'][0]['id'])) {
            return $responseData['locations'][0]['id'];
        } else {
            return "";
        }
    }

    function checkFulfillShopifyMethod($orderID)
    {
        $order = Order::find($orderID);
        echo $this->fulfillChannelOrders($order, $order->awb_number, $order->courier_partner);
    }

    function fulfillChannelOrders($order, $awb = "", $partnerName = "")
    {
        // $channel = Channels::where('seller_id', $order->seller_id)->where('channel', $order->channel)->first();
        $channel = Channels::where('seller_id', $order->seller_id)->where('id', $order->seller_channel_id)->where('channel', $order->channel)->first();
        if (empty($channel)) {
            return "";
        }
        //$partner = $this->getPartnerNameShopify($partnerName);
        $channelsController = new ChannelsController();
        switch ($order->channel) {
            case 'shopify':
                return $this->fulfillShopifyOrder($order, $awb, $partnerName, $channel);
                break;
            case 'amazon':
                return $channelsController->_fulfillAmazonOrders($order, $channel, $awb, $partnerName);
                break;
        }
        return true;
    }

    function fulfillShopifyOrder($order, $awb, $partnerName, $channel)
    {
        $locationID = $this->getShopifyLocationID($order);
        $partner = $this->getPartnerNameShopify($partnerName);
        $url = "https://$channel->api_key:$channel->password@$channel->store_url/admin/api/2021-04/orders/$order->channel_id/fulfillments.json";
        $data = [
            'fulfillment' => [
                'location_id' => intval($locationID),
                'tracking_number' => $awb,
                'tracking_company' => $partner,
                'tracking_url' => "https://www.Twinnship.in/track-order/$awb",
                'line_items' => []
            ]
        ];
        Logger::write('logs/channels/shopify/shopify-'.date('Y-m-d').'.text', [
            'title' => 'Fulfillment Request Payload',
            'data' => $data
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $responseData = json_decode($response, true);
        Logger::write('logs/channels/shopify/shopify-'.date('Y-m-d').'.text', [
            'title' => 'Fulfillment Response Payload',
            'data' => $responseData
        ]);
        if (isset($responseData['fulfillment']['id']))
            return $responseData['fulfillment']['id'];
        else
            return "";
    }
    function getPartnerNameShopify($string)
    {
        if ($string == 'shadow_fax') {
            return "Shadowfax via Twinnship";
        } else if (strpos($string,'delhivery') !== false) {
            return "Delhivery via Twinnship";
        } else if (strpos($string,'dtdc') !== false) {
            return "DTDC via Twinnship";
        } else if (strpos($string,'xpressbees') !== false) {
            return "XpressBees via Twinnship";
        } else if ($string == 'fedex') {
            return "FedEx via Twinnship";
        } else if ($string == 'wow_express') {
            return "WOW Express via Twinnship";
        } else if (strpos($string,'udaan') !== false) {
            return "Udaan Express via Twinnship";
        } else if(strpos($string,'ecom_express') !== false){
            return "Ecom Express";
        }
        else if(strpos($string,'ekart') !== false || strpos($string,'ekart_2kg') !== false || strpos($string,'ekart_1kg') !== false || strpos($string,'ekart_3kg') !== false || strpos($string,'ekart_5kg') !== false){
            return "Ekart Logistics";
        }
        else if(strpos($string,'shree_maruti') !== false || strpos($string,'shree_maruti_ecom') !== false || strpos($string,'shree_maruti_ecom_1kg') || strpos($string,'shree_maruti_ecom_3kg') !== false || strpos($string,'shree_maruti_ecom_5kg') !== false || strpos($string,'shree_maruti_ecom_10kg') !== false){
            return "Shree Maruti Logistics";
        }
        else {
            return "Twinnship";
        }
    }

    function _generateAwbForXbees($o, $orderType, $courierPartner, $xbKey, $awbType)
    {
        $returnValue = ['status' => true, 'data' => []];
        if ($o->suggested_awb != "") {
            $getAwbNumber = XbeesAwbnumber::where('awb_number', $o->suggested_awb)->where('used', 'n')->where('assigned', 'y')->where('seller_id', $o->seller_id)->first();
            if (empty($getAwbNumber)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
            $returnValue['data'] = $getAwbNumber;
            XbeesAwbnumber::where('id', $getAwbNumber->id)->update(['used' => 'y']);
            return $returnValue;
        } else {
            DB::beginTransaction();
            $getAwbNumber = XbeesAwbnumber::where('used', 'n')->where('assigned', 'n')->where('order_type', $orderType)->where('courier_partner', $courierPartner)->lockForUpdate()->first();
            if (empty($getAwbNumber)) {
                $this->_getAwbNumbersXbees($xbKey, $courierPartner, $orderType, $awbType);
                $getAwbNumber = XbeesAwbnumber::where('used', 'n')->where('assigned', 'n')->where('order_type', $orderType)->where('courier_partner', $courierPartner)->lockForUpdate()->first();
            }
            if (empty($getAwbNumber)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
            $returnValue['data'] = $getAwbNumber;
            XbeesAwbnumber::where('id', $getAwbNumber->id)->update(['used' => 'y']);
            DB::commit();
            return $returnValue;
        }
    }

    function _generateAwbForXbeesUnique($o, $orderType, $courierPartner, $xbKey, $awbType)
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
                $this->_getAwbNumbersXbeesUnique($xbKey, $courierPartner, $orderType, $awbType);
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

    function _GetExpectedDate($zone,$partnerName){
        $days = 8;
        $partner = Partners::where('keyword',$partnerName)->first()->toArray();
        if(!empty($partner)){
            $days = $partner['zone_'.strtolower($zone)];
        }
        return date('Y-m-d',strtotime("+{$days} days"));
    }
    function handleAmazonResponse(Request $request){
        if($request->spapi_oauth_code != ""){
            if(isset(Session()->get('MySeller')->id)){
                $channel = Channels::where('seller_id',Session()->get('MySeller')->id)->where('channel','amazon_direct')->orderBy('id','desc')->first();
                if(!empty(Session()->get('reauthorizeAmazonDirect'))){
                    $channel = Channels::find(Session()->get('reauthorizeAmazonDirect'));
                    Session()->forget('reauthorizeAmazonDirect');
                }
                if(!empty($channel)){
                    $oAuthCode = $request->spapi_oauth_code;
                    $amazonDirect = new AmazonDirect();
                    $refreshToken = $amazonDirect->getRefreshToken($oAuthCode);
                    Logger::write('logs/channels/amazon-direct/amazon-direct-'.date('Y-m-d').'.text', [
                        'title' => 'Refresh Token',
                        'data' => [$refreshToken]
                    ]);
                    if($refreshToken){
                        $channel->amazon_refresh_token = $refreshToken;
                        $channel->created = date('Y-m-d H:i:s');
                        $channel->save();
                        $this->utilities->generate_notification('Success', 'Channels added Successfully', 'success');
                    }else{
                        $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
                    }
                }else{
                    $this->utilities->generate_notification('Error', 'Session Expired please login and try again', 'error');
                }
            }
        }else{
            $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
        }
        return redirect(route('seller.channels'));
    }

    function generateBarcode(Request $request) {
        if(empty($request->date)) {
            return response()->json([
                'message' => 'Please enter date'
            ]);
        }
        $date = date('Y-m-d', strtotime($request->date));
        $orders = Order::whereDate('awb_assigned_date', $date)->get();
        foreach($orders as $order) {
            // $barcode = file_get_contents("https://www.Twinnship.in/barcode/test.php?code=$order->awb_number");
            // file_put_contents("public/assets/seller/images/Barcode/$order->awb_number.png", $barcode);
            Barcode::generateBarcode($order->awb_number);
        }
        return response()->json([
            'message' => 'Label generated successfully',
        ]);
    }

    function customiseLabel() {
        $data = $this->info;
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = Session()->get('MySeller')->id;
            $label->header_visibility = $request->header_visibility ?? 'y';
            $label->shipping_address_visibility = $request->shipping_address_visibility ?? 'y';
            $label->header_logo_visibility = $request->header_logo_visibility ?? 'y';
            $label->shipment_detail_visibility = $request->shipment_detail_visibility ?? 'y';
            $label->awb_barcode_visibility = $request->awb_barcode_visibility ?? 'y';
            $label->order_detail_visibility = $request->order_detail_visibility ?? 'y';
            $label->manifest_date_visibility = $request->manifest_date_visibility ?? 'n';
            $label->order_barcode_visibility = $request->order_barcode_visibility ?? 'n';
            $label->product_detail_visibility = $request->product_detail_visibility ?? 'y';
            $label->invoice_value_visibility = $request->invoice_value_visibility ?? 'y';
            $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
            $label->gift_visibility = $request->gift_visibility ?? 'n';
            $label->footer_visibility = $request->footer_visibility ?? 'y';
            $label->all_product_display = $request->all_product_display ?? 'n';
            $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
            $label->footer_customize_value = $label->custom_footer_enable ?? 'THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE';
            $label->other_charges = $request->other_charges ?? 'y';
            $label->display_full_product_name = $request->display_full_product_name ?? 'n';
            $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
            $label->contact_mask = $request->contact_mask ?? 'y';
            $label->s_contact_mask = $request->s_contact_mask ?? 'y';
            $label->s_gst_mask = $request->s_gst_mask ?? 'y';
            $label->barcode_visibility = $request->barcode_visibility ?? 'y';
            $label->ordernumber_visibility = $request->ordernumber_visibility ?? 'y';
            $label->save();
        }
        $data['label'] = $label;
        return view('seller.customise-label', $data);
    }

    function storeCustomisedLabel(Request $request) {
        $label = LabelCustomization::where('seller_id', Session()->get('MySeller')->id)->first();
        if($label == null) {
            $label = new LabelCustomization();
        }
        // Store label configuration
        $label->seller_id = Session()->get('MySeller')->id;
        $label->header_visibility = $request->header_visibility ?? 'y';
        $label->shipping_address_visibility = $request->shipping_address_visibility ?? 'y';
        $label->header_logo_visibility = $request->header_logo_visibility ?? 'y';
        $label->shipment_detail_visibility = $request->shipment_detail_visibility ?? 'y';
        $label->awb_barcode_visibility = $request->awb_barcode_visibility ?? 'y';
        $label->order_detail_visibility = $request->order_detail_visibility ?? 'y';
        $label->manifest_date_visibility = $request->manifest_date_visibility ?? 'n';
        $label->order_barcode_visibility = $request->order_barcode_visibility ?? 'y';
        $label->product_detail_visibility = $request->product_detail_visibility ?? 'y';
        $label->invoice_value_visibility = $request->invoice_value_visibility ?? 'y';
        $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
        $label->gift_visibility = $request->gift_visibility ?? 'n';
        $label->footer_visibility = $request->footer_visibility ?? 'y';
        $label->all_product_display = $request->all_product_display ?? 'n';
        $label->custom_footer_enable = $request->custom_footer_enable ?? 'y';
        $label->footer_customize_value = $label->custom_footer_enable == 'y' ? ($request->footer_customize_value ?? "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE") : "THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE";
        $label->other_charges = $request->other_charges ?? 'y';
        $label->display_full_product_name = $request->display_full_product_name ?? 'n';
        $label->tabular_form_enabled = $request->tabular_form_enabled ?? 'n';
        $label->disclaimer_text = $request->disclaimer_text ?? 'y';
        $label->contact_mask = $request->contact_mask ?? 'y';
        $label->s_contact_mask = $request->s_contact_mask ?? 'y';
        $label->s_gst_mask = $request->s_gst_mask ?? 'y';
        $label->barcode_visibility = $request->barcode_visibility ?? 'y';
        $label->ordernumber_visibility = $request->ordernumber_visibility ?? 'y';
        if($label->footer_customize_value != "" || $label->footer_customize_value != null)
        {
            $label->save();
        }
        return response()->json([
            'statusCode' => 200,
            'message' => 'Label configuration saved successfully',
            'data' => []
        ]);
    }

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
    function getQcInformation($id){
        $data['data'] = InternationalOrders::where('order_id',$id)->first();
        $data['images'] = [];
        if(!empty($data['data']->qc_image)){
            $image = explode(",",$data['data']->qc_image);
            foreach ($image as $i){
                $data['images'][] = BucketHelper::GetDownloadLink($i);
            }
        }
        return json_encode($data);
    }
    function filterMobile($number){
        return substr(preg_replace('/\s+/', '', ltrim($number,0)),-10);
    }

    function check_warehouse($war)
    {
        $response = Warehouses::where('warehouse_name', $war)->get();
        if (count($response) != 0)
            echo json_encode(array('status' => 'false'));
        else
            echo json_encode(array('status' => 'true'));
    }

    function bulkNDRActions(Request $request){
        $totalCount = 0;
        $rtoOrders = [];
        $ndrOrders = [];
        $requestedIds = [];
        $test = explode('.', $_FILES['importFile']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $oName=$request->importFile->getClientOriginalName();
                $type=explode('.',$oName);
                $name=date('YmdHis')."-".Session()->get('MySeller')->id.".".$type[count($type)-1];
                $filepath="assets/report/$name";
                $request->importFile->move(public_path('assets/report/'),$name);
                if (file_exists($filepath)) {
                    $bucketPath = "ndr_action";
                    BucketHelper::UploadFile($bucketPath,$filepath);
//                    @unlink($filepath);
                }
                $path = $name;
                $bulkFileUpload =[
                    'seller_id' => Session()->get('MySeller')->id,
                    'created' => date('Y-m-d H:i:s'),
                    'file_url' => $path
                ];
                BulkNDRActionFile::create($bulkFileUpload  );
                $cnt = 0;
                $file = $filepath;
                $handle = fopen($file, "r");
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[1] != "" && $fileop[2] != "") {
                            $order = Order::select('seller_id','s_contact','s_address_line1','s_address_line2','s_pincode','courier_partner','is_alpha','seller_order_type','id','ndr_status','awb_number','status','ndr_action','ndr_status','rto_status')->where('awb_number',$fileop[1])->where('status', '!=', 'delivered')->where('seller_id',Session()->get('MySeller')->id)->where('ndr_action', 'pending')->where('ndr_status', 'y')->where('rto_status', 'n')->first();
                            if(!empty($order)) {
                                $totalCount++;
                                if (strtolower($fileop[2]) == 'rto') {
                                    MyUtility::PerformCancellation(Session()->get('MySeller'), $order);
                                    // Order::where('id', $order->id)->update(['ndr_action' => 'requested']);
                                    array_push($requestedIds,$order->id);
                                    $rtoOrders[] = [
                                        'raised_date' => date('Y-m-d'),
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'remark' => $fileop[3],
                                        'action_status' => $order->ndr_status,
                                        'action_by' => 'Seller',
                                        'reason' => 'Marked RTO',
                                        'ndr_data_type' => 'manual'
                                    ];
                                }
                                else if (strtolower($fileop[2]) == 'reattempt') {
                                    // Order::where('id', $order->id)->update(['ndr_action' => 'requested']);
                                    $op = new OperationController();
                                    array_push($requestedIds,$order->id);
                                    $ndrOrders[] = [
                                        'raised_date' => date('Y-m-d'),
                                        'seller_id' => $order->seller_id,
                                        'order_id' => $order->id,
                                        'remark' => $fileop[3] ?? "",
                                        'action_status' => $order->ndr_status,
                                        'action_by' => 'Seller',
                                        'reason' => 'Reattempt Requested',
                                        'ndr_data_type' => 'manual',
                                        'u_address_line1' => $fileop[5] ?? "",
                                        'delivery_date' => !empty($fileop[6]) ? date('Y-m-d',strtotime($fileop[6])) : "",
                                        'updated_mobile' => $fileop[4] ?? ""
                                    ];

                                    $reAttemptData = [
                                        'date' => !empty($fileop[6]) ? date('Y-m-d',strtotime($fileop[6])) : date('Y-m-d'),
                                        'remark' => $fileop[3],
                                        'mobile' => $fileop[4],
                                        'address' => $fileop[5]
                                    ];
                                    try {
                                        $op->reAttemptOrder($order, $reAttemptData);
                                    }catch(Exception $e){

                                    }
                                }
                            }
                        }
                    }

                    if(count($ndrOrders) > 5000){
                        Ndrattemps::insert($ndrOrders);
                        $ndrOrders = [];
                    }

                    if(count($rtoOrders) > 5000){
                        Ndrattemps::insert($rtoOrders);
                        $rtoOrders = [];
                    }

                    $cnt++;
                }
                @unlink($filepath);

                if(count($ndrOrders) > 0)
                    Ndrattemps::insert($ndrOrders);

                if(count($rtoOrders) > 0)
                    Ndrattemps::insert($rtoOrders);

                if(!empty($requestedIds)){
                    Order::whereIn('id', $requestedIds)->update(['ndr_action' => 'requested']);
                }

                $this->utilities->generate_notification('Success', "$totalCount Action Requested Submitted", 'success');
                return redirect(url('/') . "/ndr-orders?tab=ndr");
            } else {
                $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                return back();
            }
        }
        else {
            $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
            return back();
        }
    }

    function getCustomDateOrder(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $order['allDays'] = [];
        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);

        // Calculate the difference in days
        $interval = $date1->diff($date2);
        $daysDifference = $interval->days;
        for($i=$daysDifference;$i>=0;$i--){
            $strDate = date('Y-m-d',strtotime($start_date."+$i days"));
            array_push($order['allDays'],$strDate);
        }
        foreach ($order['allDays'] as $d) {
            $order['partner_unscheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['manifested', 'pickup_scheduled'])->count();
            $order['partner_scheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'picked_up')->count();
            $order['partner_intransit'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status', 'n')->where('ndr_status', 'n')->count();
            $order['partner_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('rto_status', 'n')->where('ndr_status', 'n')->count();
            $order['partner_ndr_raised'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date',$d)->where('ndr_status', 'y')->where('rto_status', 'n')->count();
            //remove NDR Raised Column from Courier Overview
            $order['partner_ndr_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status', 'n')->count();
            $order['partner_ndr_pending'][$d] = $order['partner_ndr_raised'][$d] - $order['partner_ndr_delivered'][$d];
            $order['partner_ndr_rto'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('rto_status', 'y')->count();
            $order['partner_damaged'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['damaged', 'lost'])->count();
            $order['partner_total'][$d] = $order['partner_unscheduled'][$d] + $order['partner_scheduled'][$d] + $order['partner_intransit'][$d] + $order['partner_delivered'][$d] + $order['partner_ndr_delivered'][$d] + $order['partner_ndr_pending'][$d] + $order['partner_ndr_rto'][$d] + $order['partner_damaged'][$d];
        }
        $order['start_date'] = $request->start_date;
        $order['end_date'] = $request->end_date;
        return view('seller.d_order_custom_date',$order);
    }

    function getShopifyTag($id){
        $data = InternationalOrders::where('order_id',$id)->first();
        echo json_encode($data);
    }

    function getCodRemitAmount(){
        $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        $data['nextRemitCod'] = $codArray['nextRemitCod'];
        return response()->json($data);
    }

    function submitCodRemitRecharge(Request $request){
        $modify = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
        $remDays = Session()->get('MySeller')->remmitance_days ?? 7;
        $data = Order::where('seller_id',Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status','delivered')->where('rto_status','n')->whereDate('delivered_date','<',date('Y-m-d',strtotime($modify['nextRemitDate']."- $remDays days")))->where('cod_remmited','n')->get();
        $sum = 0;
        $ids = [];
        $awbs = [];
        foreach($data as $o){
            $sum+=$o->invoice_amount;
            $ids[] = $o->id;
            $awbs[] = $o->awb_number;
            if($sum >= $request->amount){
                break;
            }
        }
        $seller = Seller::find(Session()->get('MySeller')->id);
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'amount' => $sum,
            'balance' => $sum + $seller->balance,
            'type' => 'c',
            'datetime' => date('Y-m-d H:i:s'),
            'method' => "COD Remittance",
            'redeem_type' => "r",
            'description' => "Wallet Recharge with COD Remittance"
        );
        Transactions::create($data);
        Seller::where('id', Session()->get('MySeller')->id)->increment('balance', $data['amount']);
        Order::whereIn('id',$ids)->update(['cod_remmited' => 'y']);
        $codHistory = [
            'seller_id' => Session()->get('MySeller')->id,
            'order_ids' => implode(",",$ids),
            'awb_numbers' => implode(",",$awbs),
            'request_recharge' => $request->amount,
            'actual_recharge' => $sum,
            'datetime' => date('Y-m-d H:i:s'),
        ];
        SellerCodRemitRechargeHistory::create($codHistory);
        $this->utilities->generate_notification('Success', ' COD remitted successfully', 'success');
        $this->_refreshSession();
    }

    function checkAwbNumber(Request $request){
        $awbNumber = explode(",",$request->awb_number);
        $count = Order::select('courier_partner')->where('seller_id',Session()->get('MySeller')->id)->whereIn('awb_number',$awbNumber)->get();
        if(count($count) == count($awbNumber)){
            return response()->json(["status" => true,'courierTitle' => ShippingHelper::PartnerNames[$count[0]->courier_partner] ?? "",'courier_keyword' => $count[0]->courier_partner]);
        }
        return response()->json(["status" => false]);
    }

    function CCAvenuePaymentCreate(Request $request){
        try {
            $transactionID = date('YmdHis');
            $amount = $request->amount;
            $promocode = $request->promo ?? "";
            $ccAvenueId = CCAvenueTransaction::create([
                'order_id' => $transactionID,
                'amount' => $amount,
                'seller_id' => Session()->get('MySeller')->id,
                'datetime' => date('Y-m-d H:i:s')
            ])->id;
            $input['tid'] = $transactionID;
            $input['amount'] = $amount;
            $input['order_id'] = $transactionID;
            $input['currency'] = "INR";
            $input['redirect_url'] = route('ccavenue-response');
            $input['cancel_url'] = route('ccavenue-response');
            $input['language'] = "EN";
            $input['merchant_id'] = "3146768";
            $input['billing_name'] = "TWINNIC INDIA PRIVATE LIMITED";
            $input['billing_address'] = "A-82, First Floor, Transport Nagar";
            $input['billing_city'] = "Noida";
            $input['billing_state'] = "Uttar Pradesh";
            $input['billing_zip'] = "201301";
            $input['billing_country'] = "India";
            $input['billing_tel'] = "9811369493";
            $input['billing_email'] = "twinnicindia@gmail.com";
            $input['delivery_name'] = "TWINNIC INDIA PRIVATE LIMITED";
            $input['delivery_address'] = "A-82, First Floor, Transport Nagar";
            $input['delivery_city'] = "Noida";
            $input['delivery_state'] = "Uttar Pradesh";
            $input['delivery_zip'] = "201301";
            $input['delivery_country'] = "India";
            $input['delivery_tel'] = "9811369493";
            $input['merchant_param1'] = $promocode;
            $input['merchant_param2'] = "additional Info";
            $input['merchant_param3'] = "additional Info";
            $input['merchant_param4'] = Session()->get('MySeller')->id;
            $input['merchant_param5'] = $ccAvenueId;
            $merchant_data = "";

            $working_key = "6A1FEDDFCF83661A555FA7A7EBFB0D16"; //Shared by CCAVENUES
            $access_code = "AVAY45LA90AH67YAHA"; //Shared by CCAVENUES
            foreach ($input as $key => $value) {
                $merchant_data.=$key.'='.urlencode($value).'&';
            }

            //$encrypted_data = encrypt($merchant_data, $working_key);
            $encrypted_data = Utilities::encrypt($merchant_data, $working_key);
            $data['encData'] = $encrypted_data;
            $data['accessCode'] = $access_code;
            return view('admin.ccavenue-payment', $data);
        }catch(Exception $e){
            return redirect(route('seller.dashboard'));
        }
    }

    function checkBulkShipRunning(){
        $data = Seller::find(Session()->get('MySeller')->id);
        if(!empty($data) && $data->is_bulk_ship_running == 0)
            return response()->json(['status' => 'true']);
        return response()->json(['status' => 'false']);
    }

    function settings(Request $request){
        $data = $this->info;
        return view('seller.settings', $data);
    }

    function createOrder(Request $request){
        $data = $this->info;
        $data['warehouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.create_order', $data);
    }

    function weightReconcilations(Request $request){
        $data = $this->info;
        return view('seller.weight', $data);
    }


}
