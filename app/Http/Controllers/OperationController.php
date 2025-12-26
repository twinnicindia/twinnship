<?php

namespace App\Http\Controllers;

use App\Helper\InternationalOrderHelper;
use App\Helpers\UtilityHelper;
use App\Imports\AmazonFeedFlatFileImport;
use App\Imports\AmazonReportFileImport;
use App\Jobs\BulkShipOrders;
use App\Libraries\AmazonDirect;
use App\Libraries\AmazonSWA;
use App\Libraries\Aramex;
use App\Libraries\BucketHelper;
use App\Libraries\Channels\Flipkart;
use App\Libraries\Channels\Shopify;
use App\Libraries\Ekart;
use App\Libraries\Logger;
use App\Libraries\MarutiEcom;
use App\Libraries\Smartr;
use App\Libraries\Gati;
use App\Libraries\Maruti;
use App\Libraries\Bombax;
use App\Libraries\BlueDart;
use App\Libraries\Xindus;
use App\Models\Admin;
use App\Models\Admin_rights;
use App\Models\BillReceipt;
use App\Models\Bluedart_details;
use App\Models\BrandedTracking;
use App\Models\BulkCancelOrdersJob;
use App\Models\BulkShipOrdersJob;
use App\Models\CCAvenueTransaction;
use App\Models\Configuration;
use App\Models\DownloadOrderReportModel;
use App\Models\DownloadReport;
use App\Models\EarlyCod;
use App\Models\Generated_awb;
use App\Models\InternationalOrders;
use App\Models\Master;
use App\Models\Ndrattemps;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\PickedUpOrders;
use App\Models\Recharge_request;
use App\Models\Redeem_codes;
use App\Models\Redeems;
use App\Models\Seller;
use App\Models\Admin_employee;
use App\Models\Basic_informations;
use App\Models\COD_transactions;
use App\Models\SKU;
use App\Models\SmartrAwbs;
use App\Models\GatiAwbs;
use App\Models\States;
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
use App\Models\Warehouses;
use App\Models\WeightReconciliationHistory;
use App\Models\WeightReconciliationImage;
use App\Models\XbeesAwbnumber;
use App\Models\ZoneMapping;
use App\Models\SettlementWeightReconciliation;
use App\Models\ZZQueryExecutionLogs;
use App\Notifications\DisputeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Exception;
use App\Libraries\FileUploadJob;
use App\Libraries\MyUtility;
use App\Models\Channels;
use App\Models\FileUploadJobModel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class OperationController extends Controller
{
    protected $info, $utilities, $status, $noOfvalue, $metroCities,$shipment,$filterArrayBilling;
    public function __construct()
    {
        if (Session()->get('noOfPage') == null)
            Session()->put('noOfPage', 20);
        $this->shipment = new ShippingController();
        $this->info['config'] = Configuration::find(1);
        $this->info['coupon'] = Redeem_codes::where('status', 'y')->get();
        $this->utilities = new Utilities();
        $this->metroCities = ['bangalore', 'chennai', 'hyderabad', 'kolkata', 'mumbai', 'new delhi'];
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
            "rto_delivered" => "RTO Delivered",
            "rto_in_transit" => "RTO In Transit",
            "delivered" => "Delivered",
            "ndr" => "NDR",
            "lost" => "Lost",
            "damaged" => "Damaged"
        ];
    }
    function reports()
    {
        $data = $this->info;
        $data['reports'] = DownloadReport::where('seller_id',Session()->get('MySeller')->id)->orderBy('id','desc')->paginate(20);
        return view('seller.report.report_status', $data);
    }
    function pickupReports(Request $request){
        $data = $this->info;
        if(!empty($request->from_date) && !empty($request->to_date)){
            $data['reports'] = PickedUpOrders::join('orders','orders.id','picked_orders_list.order_id')
                ->select('picked_orders_list.*','orders.awb_number','orders.status','orders.awb_assigned_date')
                ->whereDate('picked_orders_list.datetime','>=',$request->from_date)
                ->whereDate('picked_orders_list.datetime','<=',$request->to_date)
                ->where('orders.seller_id',Session()->get('MySeller')->id);
            if(!empty($request->export)){
                $name = "exports/Twinnship";
                $filename = "picked_orders";
                $fp = fopen("$name.csv", 'w');
                $info = array('Sr.No', 'AWB Number', 'Shipped Date', 'Picked Date', 'Current Status');
                fputcsv($fp, $info);
                $cnt = 1;
                $orders = $data['reports']->get();
                foreach($orders as $o){
                    $info = [$cnt++,"`{$o->awb_number}`",$o->awb_assigned_date,$o->datetime,$o->status];
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
                exit;
            }
            $data['reports'] = $data['reports']->paginate(20);
        }
        return view('seller.report.pickup_report', $data);
    }
    function Weight()
    {
        $data = $this->info;
        Session::put('noOfPage', 20);
        Session($this->filterArrayBilling);
        session(['billing_status' => 'weight_reconciliation']);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partners'] = Partners::where('status', 'y')->get();
        return view('seller.weight', $data);
    }

    function Weights()
    {
        $data = $this->info;
        Session::put('noOfPage', 20);
        Session($this->filterArrayBilling);
        session(['billing_status' => 'weight_reconciliation']);
        $data['PartnerName'] = Partners::getPartnerKeywordList();
        $data['partners'] = Partners::where('status', 'y')->get();
        return view('reseller.weight', $data);
    }
    function updateDeliveryAddress(Request $request){
        $data = [
            's_address_line1' => $request->address1,
            's_address_line2' => $request->address2,
            's_pincode' => $request->pincode,
            's_city' => $request->city,
            's_state' => $request->state,
            's_country' => $request->country,
            'delivery_address' => $request->address1.','.$request->address2.','.$request->city.','.$request->state.','.$request->pincode
        ];
        Order::where('id',$request->id)->update($data);
        return response()->json(['status' => 'true','message' => 'Address Updated Successfully']);
    }
    function checkAndUpdateOrdersCSV(Request $request){
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
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "" && $fileop[1] != "") {

                            if (strtolower($fileop[1]) == "cod" || strtolower($fileop[1]) == "prepaid") {
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
                                    'seller_id' => Session()->get('MySeller')->id,
                                    'warehouse_id' => $w->id,
                                    'customer_order_number' => $fileop[0],
                                    'order_type' => strtolower(trim($fileop[2])) == "reverse" ? "prepaid" : (isset($fileop[1]) ? strtolower(trim($fileop[1])) : ""),
                                    'o_type' => isset($fileop[2]) ? strtolower($fileop[2]) : "",

                                    //for billing address
                                    'b_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    'b_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    'b_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    'b_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    'b_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    'b_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    'b_pincode' => isset($fileop[9]) ? trim($fileop[9]) : "",
                                    'b_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                    'b_contact' => isset($fileop[11]) ? $fileop[11] : "",

                                    //for shipping Address
                                    's_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    's_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    's_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    's_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    's_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    's_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    's_pincode' => isset($fileop[9]) ? $fileop[9] : "",
                                    's_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                    's_contact' => isset($fileop[11]) ? trim($fileop[11]) : "",

                                    'weight' => $weight,
                                    'length' => isset($fileop[13]) ? $fileop[13] : "",
                                    'height' => isset($fileop[14]) ? $fileop[14] : "",
                                    'breadth' => isset($fileop[15]) ? $fileop[15] : "",
                                    'vol_weight' => (intval($fileop[14]) * intval($fileop[13]) * intval($fileop[15])) / 5,
                                    's_charge' => isset($fileop[16]) ? $fileop[16] : "",
                                    'c_charge' => isset($fileop[17]) ? $fileop[17] : "",
                                    'discount' => isset($fileop[18]) ? $fileop[18] : "",
                                    'invoice_amount' => isset($fileop[19]) ? intval($fileop[19]) : "",
                                    'igst' => $igst,
                                    'sgst' => $sgst,
                                    'cgst' => $cgst,
                                    'reseller_name' => isset($fileop[20]) ? $fileop[20] : "",
                                    'suggested_awb' => isset($fileop[21]) ? trim($fileop[21], "`") : "",
                                    'channel_name' => isset($fileop[23]) ? trim($fileop[23], "`") : "",
                                    'channel_code' => isset($fileop[23]) ? trim($fileop[23], "`") : "",
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
                                    'inserted_by' => Session()->get('MySeller')->id
                                );
                                if(!empty($fileop[22])){
                                    $checkWarehouse = Warehouses::where('warehouse_name',$fileop[22])->first();
                                    if(!empty($checkWarehouse)){
                                        $data['rto_warehouse_id'] = $checkWarehouse->id;
                                        $data['same_as_rto'] = 'n';
                                    }
                                }
                                $orderRow = Order::where('customer_order_number',$data['customer_order_number'])->where('seller_id',Session()->get('MySeller')->id)->where('status','pending')->where('channel','custom')->first();
                                if(empty($orderRow))
                                    continue;
                                //loop for products
                                $all_products = [];
                                $all_skus = [];
                                $totalQty = 0;
                                Product::where('order_id',$orderRow->id)->delete();
                                for ($i = 24; $i <= 10000; $i += 3) {
                                    $temp = $i;
                                    if (!isset($fileop[$temp])) {
                                        break;
                                    }
                                    if ($fileop[$temp] == "")
                                        break;
                                    $data_product = array(
                                        'order_id' => $orderRow->id,
                                        'product_name' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_sku' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_qty' => intval($fileop[$temp] ?? "") == 0 ? "1" : intval(trim($fileop[$temp]))
                                    );

                                    if(Session()->get('MySeller')->product_name_as_sku == 'y')
                                        $data_product['product_sku'] = $data_product['product_name'];

                                    $totalQty+= $data_product['product_qty'];
                                    $all_products[] = $data_product['product_name'];
                                    $all_skus[] = $data_product['product_sku'];
                                    Product::create($data_product);
                                }
                                $data['product_name'] = implode(',', $all_products);
                                $data['product_sku'] = implode(',', $all_skus);
                                $data['product_qty'] = $totalQty;
                                Order::where('id',$orderRow->id)->update($data);
                                $totalCount++;
                            }
                        }
                    }
                    $cnt++;
                }
                $this->utilities->generate_notification('Success', "$totalCount Orders updated successfully", 'success');
                return redirect(url('/') . "/my-orders?tab=processing");
            } else {
                $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                return back();
            }
        } else {
            $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
            return back();
        }
    }

    public function import_csv_international_order(Request $request)
    {
        if($request->importType == "update"){
            $this->checkAndUpdateInternationalOrdersCSV($request);
        }
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
                $internationalCount = [];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "" && $fileop[1] != "") {
                            if (strtolower($fileop[1]) == "cod" || strtolower($fileop[1]) == "prepaid") {
                                if (strlen($fileop[9]) != '5')
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
                                    'seller_id' => Session()->get('MySeller')->id,
                                    'warehouse_id' => $w->id,
                                    'customer_order_number' => isset($fileop[0]) ? $fileop[0] : $orderNumberCount,
                                    'order_number' => ++$orderNumberCount,
                                    'order_type' => isset($fileop[1]) ? strtolower($fileop[1]) : "",
                                    'o_type' => isset($fileop[2]) ? strtolower($fileop[2]) : "",

                                    //for billing address
                                    'b_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    'b_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    'b_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    'b_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    'b_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    'b_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    'b_pincode' => isset($fileop[9]) ? trim($fileop[9]) : "",
                                    'b_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                    'b_contact' => isset($fileop[11]) ? $fileop[11] : "",

                                    //for shipping Address
                                    's_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    's_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    's_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    's_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    's_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    's_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    's_pincode' => isset($fileop[9]) ? $fileop[9] : "",
                                    's_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                    's_contact' => isset($fileop[11]) ? trim($fileop[11]) : "",

                                    'weight' => $weight,
                                    'length' => isset($fileop[13]) ? $fileop[13] : "",
                                    'height' => isset($fileop[14]) ? $fileop[14] : "",
                                    'breadth' => isset($fileop[15]) ? $fileop[15] : "",
                                    'vol_weight' => (intval($fileop[14]) * intval($fileop[13]) * intval($fileop[15])) / 5,
                                    's_charge' => isset($fileop[16]) ? $fileop[16] : "",
                                    'c_charge' => isset($fileop[17]) ? $fileop[17] : "",
                                    'discount' => isset($fileop[18]) ? $fileop[18] : "",
                                    'invoice_amount' => isset($fileop[19]) ? intval($fileop[19]) : "",
                                    'channel_name' => isset($fileop[23]) ? trim($fileop[23]) : "default_channel",
                                    'igst' => $igst,
                                    'sgst' => $sgst,
                                    'cgst' => $cgst,
                                    'reseller_name' => "",
                                    'suggested_awb' => "",
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
                                    'global_type' => 'international'
                                );
                                $orderId  = Order::create($data)->id;

                                $inData = [
                                    'order_id' => $orderId,
                                    'iec_code' => Session()->get('MySeller')->iec_code,
                                    'ad_code' => Session()->get('MySeller')->ad_code,
                                    'ioss' => $fileop[20],
                                    'eori' => $fileop[21],
                                    'invoice_number' => $fileop[22]
                                ];

                                InternationalOrders::create($inData);
                                //loop for products
                                $totalQty = 0;
                                $dataProductName = [];
                                $dataProductSku = [];
                                for ($i = 23; $i <= 10000; $i += 5) {
                                    $temp = $i;
                                    if (!isset($fileop[$temp])) {
                                        break;
                                    }
                                    if ($fileop[$temp] == "")
                                        break;
                                    $data_product = array(
                                        'order_id' => $orderId,
                                        'product_name' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_sku' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_qty' => intval($fileop[$temp] ?? "") == 0 ? "1" : intval(trim($fileop[$temp])),
                                        'hsn_number' => isset($fileop[++$temp]) ? $fileop[$temp] : "",
                                        'hts_number' => isset($fileop[++$temp]) ? $fileop[$temp] : "",
                                    );
                                    $totalQty+= $data_product['product_qty'];
                                    $dataProductName[] = $data_product['product_name'];
                                    $dataProductSku[] = $data_product['product_sku'];
                                    Product::create($data_product);
                                }
                                $updateOrder = [
                                    'id' => $orderId,
                                    'product_name' => implode(',',$dataProductName),
                                    'product_sku' => implode(',',$dataProductSku),
                                    'product_qty' => $totalQty,
                                ];

                                Order::where('id',$orderId)->update($updateOrder);
                                $dataCount++;
                            }
                        }
                    }
                    $cnt++;
                }
                $this->utilities->generate_notification('Success', "$dataCount Orders imported successfully please check and unprocessable orders as well", 'success');
                return redirect(url('/') . "/my-orders?tab=processing");
            } else {
                $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                return back();
            }
        } else {
            $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
            return back();
        }
    }

    function checkAndUpdateInternationalOrdersCSV(Request $request){
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
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "" && $fileop[1] != "") {

                            if (strtolower($fileop[1]) == "cod" || strtolower($fileop[1]) == "prepaid") {
                                if (strlen($fileop[9]) != '5')
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
                                    'seller_id' => Session()->get('MySeller')->id,
                                    'warehouse_id' => $w->id,
                                    'customer_order_number' => $fileop[0],
                                    'order_type' => isset($fileop[1]) ? strtolower($fileop[1]) : "",
                                    'o_type' => isset($fileop[2]) ? strtolower($fileop[2]) : "",

                                    //for billing address
                                    'b_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    'b_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    'b_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    'b_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    'b_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    'b_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    'b_pincode' => isset($fileop[9]) ? trim($fileop[9]) : "",
                                    'b_contact_code' => isset($fileop[10]) ? $fileop[10] : "",
                                    'b_contact' => isset($fileop[11]) ? $fileop[11] : "",

                                    //for shipping Address
                                    's_customer_name' => isset($fileop[3]) ? $fileop[3] : "",
                                    's_address_line1' => isset($fileop[4]) ? $fileop[4] : "",
                                    's_address_line2' => isset($fileop[5]) ? $fileop[5] : "",
                                    's_city' => isset($fileop[6]) ? $fileop[6] : "",
                                    's_state' => isset($fileop[7]) ? $fileop[7] : "",
                                    's_country' => isset($fileop[8]) ? $fileop[8] : "",
                                    's_pincode' => isset($fileop[9]) ? $fileop[9] : "",
                                    's_contact_code' => isset($fileop[10]) ? "+".$fileop[10] : "",
                                    's_contact' => isset($fileop[11]) ? trim($fileop[11]) : "",

                                    'weight' => $weight,
                                    'length' => isset($fileop[13]) ? $fileop[13] : "",
                                    'height' => isset($fileop[14]) ? $fileop[14] : "",
                                    'breadth' => isset($fileop[15]) ? $fileop[15] : "",
                                    'vol_weight' => (intval($fileop[14]) * intval($fileop[13]) * intval($fileop[15])) / 5,
                                    's_charge' => isset($fileop[16]) ? $fileop[16] : "",
                                    'c_charge' => isset($fileop[17]) ? $fileop[17] : "",
                                    'discount' => isset($fileop[18]) ? $fileop[18] : "",
                                    'invoice_amount' => isset($fileop[19]) ? intval($fileop[19]) : "",
                                    'igst' => $igst,
                                    'sgst' => $sgst,
                                    'cgst' => $cgst,
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
                                    'inserted_by' => Session()->get('MySeller')->id
                                );
                                $orderRow = Order::where('customer_order_number',$data['customer_order_number'])->where('seller_id',Session()->get('MySeller')->id)->where('status','pending')->where('channel','custom')->where('global_type','international')->first();
                                if(empty($orderRow))
                                    continue;

                                $internationalData = [
                                    'iec_code' => Session()->get('MySeller')->iec_code,
                                    'ad_code' => Session()->get('MySeller')->ad_code,
                                    'ioss' => $fileop[20],
                                    'eori' => $fileop[21],
                                    'invoice_number' => $fileop[22]
                                ];
                                //loop for products
                                $all_products = [];
                                $all_skus = [];
                                $totalQty = 0;
                                Product::where('order_id',$orderRow->id)->delete();
                                for ($i = 23; $i <= 10000; $i += 5) {
                                    $temp = $i;
                                    if (!isset($fileop[$temp])) {
                                        break;
                                    }
                                    if ($fileop[$temp] == "")
                                        break;
                                    $data_product = array(
                                        'order_id' => $orderRow->id,
                                        'product_name' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_sku' => isset($fileop[$temp]) ? $fileop[$temp++] : "",
                                        'product_qty' => intval($fileop[$temp] ?? "") == 0 ? "1" : intval(trim($fileop[$temp])),
                                        'hsn_number' => isset($fileop[++$temp]) ? $fileop[$temp] : "",
                                        'hts_number' => isset($fileop[++$temp]) ? $fileop[$temp] : "",
                                    );
                                    $totalQty+= $data_product['product_qty'];
                                    $all_products[] = $data_product['product_name'];
                                    $all_skus[] = $data_product['product_sku'];
                                    Product::create($data_product);
                                }
                                $data['product_name'] = implode(',', $all_products);
                                $data['product_sku'] = implode(',', $all_skus);
                                $data['product_qty'] = $totalQty;
                                Order::where('id',$orderRow->id)->update($data);
                                InternationalOrders::where('order_id', $orderRow->id)->update($internationalData);
                                $totalCount++;
                            }
                        }
                    }
                    $cnt++;
                }
                $this->utilities->generate_notification('Success', "$totalCount Orders updated successfully", 'success');
                return redirect(url('/') . "/my-orders?tab=processing");
            } else {
                $this->utilities->generate_notification('Oops..', ' Invalid File.', 'error');
                return back();
            }
        } else {
            $this->utilities->generate_notification('Oops..', ' Please Upload File', 'error');
            return back();
        }
    }

    function queryUtility(Request $request){
        $data['allHeaders'] = [];
        $data['results'] = [];
        if(!empty($request->q)){
            try{
                $data['query'] = $request->q;
                // here comes the fetch query
                if(!str_starts_with(strtolower($request->q),'select') && !str_starts_with(strtolower($request->q),'show')){
                    if($request->rights == 'admin' && !str_starts_with(trim(strtolower($request->q)),"delete")){
                        DB::statement($request->q);
                        $queryData = [
                            'admin_id' => Session()->get('MyAdmin')->id,
                            'query' => $request->q,
                            'executed' => date('Y-m-d H:i:s'),
                            'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                        ];
                        ZZQueryExecutionLogs::create($queryData);
                        return response()->json(['status' => 'true','message' => 'Query Executed Successfully']);
                    }
                    else{
                        return response()->json(['status' => 'false', 'message' => 'You can not run delete or drop query here']);
                    }
                    return response()->json(['status' => 'false','message' => 'You can run only select queries here']);
                }
                $data['results'] = DB::select($request->q);
                foreach($data['results'][0] as $key => $value){
                    $data['allHeaders'][] = $key;
                }
            }
            catch(Exception $e){
                return response()->json(['status' => 'false','message' => $e->getMessage()]);
            }

            if(($request->post('export'))){
                $name = "exports/report";
                $filename = "reports";
                $fp = fopen("$name.csv", 'w');
                $info = $data['allHeaders'];
                fputcsv($fp, $info);
                $cnt = 1;
                $d = [];
                foreach ($data['results'] as $r) {
                    foreach ($data['allHeaders'] as $h){
                        $d[] = $r->$h;
                    }
                    fputcsv($fp,$d);
                    $d = [];
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
        }
        return view('admin/utility/query-utility',$data);
    }

    function queryUtilityNew(Request $request){
        $data['allHeaders'] = [];
        $data['results'] = [];
        if(!empty($request->q)){
            try{
                $data['query'] = $request->q;
                // here comes the fetch query
                if(!str_starts_with(strtolower($request->q),'select')){
                    return response()->json(['status' => 'false','message' => 'You can run only select queries here']);
                }
                $data['results'] = DB::select($request->q);
                foreach($data['results'][0] as $key => $value){
                    $data['allHeaders'][] = $key;
                }
            }
            catch(Exception $e){
                return response()->json(['status' => 'false','message' => $e->getMessage()]);
            }
            if(($request->post('export'))){
                $name = "exports/report";
                $filename = "reports";
                $fp = fopen("$name.csv", 'w');
                $info = $data['allHeaders'];
                fputcsv($fp, $info);
                $cnt = 1;
                $d = [];
                foreach ($data['results'] as $r) {
                    foreach ($data['allHeaders'] as $h){
                        $d[] = $r->{$h};
                    }
                    fputcsv($fp,$d);
                    $d = [];
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
        }
        return view('admin/utility/query-utility',$data);
    }
    function updateInvoiceAmount(Request $request){
        $data = [
            'invoice_amount' => $request->invoice_amount
        ];
        Order::where('id',$request->id)->update($data);
        return response()->json(['status' => 'true','message' => 'Amount Updated Successfully']);
    }
    function getBluedartPickupToken($awb){
        $response = Bluedart_details::join('orders','orders.id','bluedart_details.order_id')->select('bluedart_details.*')->where('orders.awb_number',$awb)->first();
        return response()->json(['pickup_token_number' => $response->pickup_token_number ?? "NA"]);
    }
    function downloadReportFile($report){
        $reportData = DownloadReport::find($report);
        if(empty($reportData))
            return false;
        else
            return BucketHelper::DownloadFile($reportData->bucket_url);
    }

    function downloadOrderReportFile($report){
        $reportData = DownloadOrderReportModel::find($report);
        if(empty($reportData))
            return false;
        else
            return BucketHelper::DownloadFile($reportData->report_download_url);
    }

    function reAuthorizeAmazonDirect($channel){
        Session()->put('reauthorizeAmazonDirect', $channel);
        return redirect("https://sellercentral.amazon.in/apps/authorize/consent?application_id=amzn1.sp.solution.f78df776-2482-45d6-93ab-34b539a2f0b6&version=beta&state=examplestate");
    }

    function shipOrder($id){
        $data['partners'] = Partners::where('international_enabled','y')->where('status','y')->get();
        $data['order'] = Order::find($id);
        $cnt = 0;
        foreach ($data['partners'] as $p){
            if($p->keyword == 'xindus'){
                $xindus = new Xindus();
                $data['partners'][$cnt]->rate = $xindus->GetRate($data['order']->weight,$data['order']->vol_weight,$data['order']->seller_id);
            }
            else if($p->keyword == 'aramex'){
                $data['partners'][$cnt]->rate = Aramex::GetRate($data['order']->weight,$data['order']->vol_weight,$data['order']->s_country,$data['order']->seller_id);
            }
            $cnt++;
        }
        return view('seller.international_partial.partner_details',$data);
    }
    function shipInternationalOrder(Request $request){
        $orderData = Order::find($request->order_id);
        $sellerData = Seller::find($orderData->seller_id);
        try{
            $response = InternationalOrderHelper::ShipOrder($orderData,$sellerData,$request->partner);
            if(!empty($response['status']))
                return response()->json(['status' => 'true','message' => 'Order Shipped Successfully']);
            else
                return response()->json(['status' => 'false','message' => ' Pincode is not Serviceable']);
        }catch(Exception $e){
            return response()->json(['status' => 'false','message' => ' Pincode is not Serviceable']);
        }

    }
    function generateMagentoAdminAccessToken(Request $request){
        $storeUrl = rtrim($request->url,"/");
        $url = $storeUrl."/rest/V1/integration/admin/token";
        $data = [
            'username' => $request->username,
            'password' => $request->password
        ];
        $res = Http::post($url,$data);
        $accessToken = $res->json();
        if(!empty($accessToken))
            return response()->json(['status' => true,'accessToken' => $accessToken]);
        else
            return response()->json(['status' => false]);
    }
    function receiveShopifyFulfillment(Request $request){
        Logger::write('logs/api/shopify-request-'.date('Y-m-d').'.text', [
            'title' => 'Shopify Fulfillment Request:',
            'data' => $request->all()
        ]);
    }
    function testAPIPush(Request $request){
        $payload = [
            "ApiKey" => "SRDmdVIjrCLea1tRiYqVYnXoGwYbBb2sOFHYhOjN",
            "OrderDetails" => [
                [
                    "PaymentType" => "PREPAID",
                    "OrderType" => "Forward",
                    "CustomerName" => "Sachin khurana",
                    "OrderNumber" => "406-1905976-1333967~D8rDD2357",
                    "Addresses" => [
                        "BilingAddress" => [
                            "AddressLine1" => "476-B, Second Floor, Sector - 39, Near Bakhtawar Chowk",
                            "AddressLine2" => "Sector - 39, Near Bakhtawar Chowk",
                            "City" => "Gurugram",
                            "State" => "Haryana",
                            "Country" => "India",
                            "Pincode" => "122022",
                            "ContactCode" => "91",
                            "Contact" => "9399262217"
                        ],
                        "ShippingAddress" => [
                            "AddressLine1" => "A-2/57 first floor sector 3 Rohini RohiniNo Address Provided",
                            "AddressLine2" => null,
                            "City" => "NEW DELHI",
                            "State" => "Delhi",
                            "Country" => "India",
                            "Pincode" => "110085",
                            "ContactCode" => "91",
                            "Contact" => "7827925256"
                        ],
                        "PickupAddress" => [
                            "WarehouseName" => "UNIQUE ENETRPRISES",
                            "ContactName" => "UNIQUE ENETRPRISES",
                            "AddressLine1" => "Unique Enterprises,SHELAR FARMS, TIN SHEDTAKALI GAON ROADTEST WH add4NASIK,Maharashtra Unique Enterprises,SHELAR FARMS, TIN SHEDTAKALI GAON ROADTEST WH add4NASIK,Maharashtra",
                            "AddressLine2" => null,
                            "City" => "NASIK",
                            "State" => "Maharashtra",
                            "Country" => "India",
                            "Pincode" => "422101",
                            "ContactCode" => "91",
                            "Contact" => "9967369333"
                        ]
                    ],
                    "Weight" => "1.1",
                    "Length" => "10.000",
                    "Breadth" => "10.000",
                    "Height" => "10.000",
                    "ProductDetails" => [
                        [
                            "Name" => "Cheetos Masala Balls, 32g",
                            "SKU" => "B016KNUF7I",
                            "QTY" => "10.000"
                        ],
                        [
                            "Name" => "Cheetos Cheez Puffs, 32g",
                            "SKU" => "B016KNU8S4",
                            "QTY" => "10.000"
                        ]
                    ],
                    "InvoiceAmount" => "382.00",
                    "ShippingCharge" => "190.00000",
                    "Discount" => "8.00000"
                ]
            ]
        ];
        $responseData = Http::post("https://www.twinnship.com/api/order-create-with-pickup",$payload)->json();
        if(!empty($responseData[0]['order_id'])){
            $shipPayload = [
                'ApiKey' => 'SRDmdVIjrCLea1tRiYqVYnXoGwYbBb2sOFHYhOjN',
                'OrderID' => $responseData[0]['order_id']
            ];
            $shipResponse = Http::post("https://www.twinnship.com/api/order-ship",$shipPayload)->json();
            dd($responseData,$shipResponse);
        }
    }
    function checkShippedOrderNotification(){
        if(empty(Session()->get('MySeller'))){
            return response()->json(['status' => false,'message' => '']);
        }
        $notifications = BulkShipOrdersJob::where('seller_id',Session()->get('MySeller')->id)->where('status','completed')->where('is_notified','n')->limit(1)->orderBy('id')->get();
        if(count($notifications) > 0){
            BulkShipOrdersJob::where('id',$notifications[0]->id)->update(['is_notified' => 'y']);
            return response()->json(['status' => true, 'data' => $notifications,'notification' => " {$notifications[0]->shipped} out of {$notifications[0]->total} orders shipped successfully!!"]);
        }else{
            $notifications = BulkCancelOrdersJob::where('seller_id',Session()->get('MySeller')->id)->where('status','completed')->where('is_notified','n')->limit(1)->orderBy('id')->get();
            if(count($notifications) > 0){
                BulkCancelOrdersJob::where('id',$notifications[0]->id)->update(['is_notified' => 'y']);
                return response()->json(['status' => true, 'data' => $notifications,'notification' => " {$notifications[0]->cancelled} out of {$notifications[0]->total} orders cancelled successfully!!"]);
            }
        }
        return response()->json(['status' => false,'message' => '']);
    }
    // Track DTDC Order Status WebHook
    function trackSmartrStagingOrdersWebHook(Request $request){
        if($request->header('authorization') != "55d17408-3200-497b-aca0-edc60542b80f")
            return response()->json(['status' => false,'message' => 'Unauthorized'],401);
        $responseData = $request->all();
        try{
            Logger::write('logs/partners/smartr/smartr-webhook-staging-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Request Payload:',
                'data' => $responseData
            ]);
            return response()->json(['status' => 200,'message' => 'Status saved Successfully']);
        }
        catch(Exception $e){
            Logger::write('logs/partners/smartr/smartr-webhook-error.text', [
                'title' => 'Check out error with this payload:',
                'data' => ['error' => $e->getMessage()." - ".$e->getFile()." - ".$e->getLine()],
            ]);
            return response()->json(['status' => 500,'message' => 'Something went wrong']);
        }
    }


    // Track delhivery order webhook
    function trackDelhiveryStagingOrderHook(Request $request)
    {
        try {
            Logger::write('logs/partners/delhivery/delhivery-staging-webhook-'.date('Y-m-d').'.text', [
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
            Logger::write('logs/partners/delhivery/delhivery-staging-webhook-'.date('Y-m-d').'.text', [
                'title' => 'Webhook Response Payload:',
                'data' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    function removeDuplicateOrders(Request $request){
        try{
            $allOrders = Order::select('id','customer_order_number','channel')->where('channel','custom')->where('seller_id',$request->seller_id)->where('inserted','>',date('Y-m-d 00:00:00'))->where('status','pending')->orderBy('id','desc')->get();
            $findOrder = Order::where('seller_id',$request->seller_id)->select('customer_order_number')->distinct()->where('channel','custom')->where('status','!=','cancelled')->where(function ($query){
                return $query->where('inserted','<',date('Y-m-d 00:00:00'))
                    ->orWhere('inserted','>',date('Y-m-d 00:00:00'))->where('status','!=','pending');
            })->pluck('customer_order_number')->toArray();
            $deleteIds = [];
            foreach ($allOrders as $o){
                if(in_array($o->customer_order_number,$findOrder))
                    $deleteIds[] = $o->id;
            }
            Order::whereIn('id',$deleteIds)->delete();
            return response()->json(['status' => true,'message' => count($deleteIds).' duplicate orders removed successfully']);
        }catch(Exception $e){
            return response()->json(['status' => false,'message' => 'Something went wrong please try again']);
        }
    }
    //for display shopify form
    function addShopifyNew()
    {
        $data = $this->info;
        return view('seller.channels.add-shopify-new', $data);
    }

    // for adding API details of shopify
    function submitShopifyNew(Request $request)
    {
        $scopes = [
            'read_all_orders',
            'read_assigned_fulfillment_orders',
            'write_assigned_fulfillment_orders',
            'read_cart_transforms',
            'write_cart_transforms',
            'read_customers',
            'write_customers',
            'read_draft_orders',
            'write_draft_orders',
            'read_fulfillments',
            'write_fulfillments',
            'write_fulfillments',
            'read_merchant_managed_fulfillment_orders',
            'write_merchant_managed_fulfillment_orders',
            'read_orders',
            'write_orders',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts',
            'read_third_party_fulfillment_orders',
            'write_third_party_fulfillment_orders',
        ];
        $url = "https://accounts.shopify.com/oauth/authorize?client_id=c017075f404c3625696bdb3eea5efd72&response_type=code&scopes=".implode(',',$scopes)."&redirect_uri=".urlencode("https://www.twinnship.com/auth/shopify-redirect");
//        $data = [
//            'seller_id' => Session()->get('MySeller')->id,
//            'channel_name' => $request->channel_name,
//            'channel' => 'shopify',
//            'store_url' => $request->store_url,
//            'auto_fulfill' => $request->auto_fulfill,
//            'auto_cancel' => $request->auto_cancel,
//            'auto_cod_paid' => $request->auto_cod_paid,
//            'send_abandon_sms' => $request->send_abandon_sms ?? 'n',
//            'last_executed' => $request->last_executed ?? date('Y-m-d H:i:s')
//        ];
//        Channels::create($data);
        return redirect($url);
    }
    function shopifyRedirect(Request $request){
        $shared_secret = "ee403f4f99b7671debb20fea1d434c25";
        $params = $_GET; // Retrieve all request parameters
        $hmac = $_GET['hmac']; // Retrieve HMAC request parameter
        $params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
        ksort($params); // Sort params lexographically

        // Compute SHA256 digest
        $computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

        // Use hmac data to check that the response is from Shopify or not
        if (hash_equals($hmac, $computed_hmac)) {
            // Get Access Key
            $data = [
                "client_id" => 'c017075f404c3625696bdb3eea5efd72', // Your API key
                "client_secret" => 'ee403f4f99b7671debb20fea1d434c25', // Your app credentials (secret key)
                "code" => $params['code'] // Grab the access key from the URL
            ];
            $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
            $response = Http::post("$access_token_url",$data);
            if(!empty($response)){
                $accessToken = $response['access_token'];
                $channelData = Channels::where('store_url',$params['shop'])->where('channel','shopify')->where('seller_id',Session()->get('MySeller')->id)->first();
                if(empty($channelData)){
                    $channelData = Channels::create(
                        [
                            'seller_id' => Session()->get('MySeller')->id,
                            'channel_name' => "Shopify - ".Session()->get('MySeller')->id." - ".rand(1,10),
                            'channel' => 'shopify',
                            'store_url' => $params['shop'],
                            'auto_fulfill' => 'y',
                            'auto_cancel' => 'y',
                            'auto_cod_paid' => 'y',
                            'send_abandon_sms' => 'n',
                            'last_executed' => date('Y-m-d H:i:s',strtotime("-3 days"))
                        ]
                    );
                }
                $channelData->password = $accessToken;
                $channelData->save();
                $this->utilities->generate_notification('Success', 'Channels added Successfully', 'success');
            }else{
                $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
            }
        } else {
            $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
        }
        return redirect(route('seller.channels'));
    }

    function dashboardOrderTop()
    {
        $data = $this->info;

        $yesterday = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 days"));
        $two_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-2 days"));
        $three_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-3 days"));
        $four_day_ago = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-4 days"));
        $data['allDays'] = [$yesterday,$two_day_ago,$three_day_ago,$four_day_ago];

        foreach ($data['allDays'] as $d) {
            $data['partner_unscheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['manifested', 'pickup_scheduled'])->count();//93.6
            $data['partner_scheduled'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'picked_up')->count();//37.36
            $data['partner_intransit'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status', 'n')->where('ndr_status', 'n')->count();//1235.12
            $data['partner_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('rto_status', 'n')->where('ndr_status', 'n')->count();//1230.43
            $data['partner_ndr_raised'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date',$d)->where('ndr_status', 'y')->where('rto_status', 'n')->count();//272.88
            //remove NDR Raised Column from Courier Overview
            $data['partner_ndr_delivered'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status', 'n')->count();//474.02
            $data['partner_ndr_pending'][$d] = $data['partner_ndr_raised'][$d] - $data['partner_ndr_delivered'][$d];
            $data['partner_ndr_rto'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->where('rto_status', 'y')->count();//55.02
            $data['partner_damaged'][$d] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', $d)->whereIn('status', ['damaged', 'lost'])->count();//2.26
            $data['partner_total'][$d] = $data['partner_unscheduled'][$d] + $data['partner_scheduled'][$d] + $data['partner_intransit'][$d] + $data['partner_delivered'][$d] + $data['partner_ndr_delivered'][$d] + $data['partner_ndr_pending'][$d] + $data['partner_ndr_rto'][$d] + $data['partner_damaged'][$d];
        }

        return view('seller.Dashboard.o_ordertop', $data);
    }

    function dashboardPrepaidOrder()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'cod')->where('status', 'delivered')->count();//648.08
        $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->where('order_type', 'prepaid')->count();//310.76

        return view('seller.Dashboard.o_prepaid', $data);
    }
    function dashboardbuyerOrder()
    {
        $data = $this->info;
        return view('seller.Dashboard.o_buyer', $data);
    }
    function dashboardLocationOrder()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['popular_location_order'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_order')->limit(10)->get();//391.49
        $data['popular_location_revenue'] = Order::select('s_state', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('s_state')->latest('total_amount')->limit(10)->get();//372.88

        return view('seller.Dashboard.o_location', $data);
    }
    function dashboardCustomerOrder()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['top_customer_order'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_order')->get();//437.59
        $data['top_customer_revenue'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'), DB::raw('sum(invoice_amount) as total_amount'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('b_customer_name')->limit(10)->latest('total_amount')->get();//446.61

        return view('seller.Dashboard.o_customer', $data);
    }

    function dashboardProductOrder()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['top_product_order'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('unit_sold')->limit(10)->get();//541.99
        $data['top_product_revenue'] = DB::table('orders')->join('products', 'products.order_id', '=', 'orders.id')->select('products.product_name', DB::raw('sum(products.product_qty) as unit_sold'), DB::raw('sum(orders.invoice_amount) as total_revenue'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '>=', $start_date)->whereDate('inserted', '<=', $end_date)->groupBy('products.product_name')->latest('total_revenue')->limit(10)->get();//522.12

        return view('seller.Dashboard.o_product', $data);
    }

    function dashboardShipmentZoneWiseData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        //For Zone Counting
        $data['courier_partner1_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_1)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();//25.43
        $data['courier_partner2_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_2)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();//45.32
        $data['courier_partner3_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_3)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();//30.18
        $data['courier_partner4_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', Session()->get('MySeller')->courier_priority_4)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();//63.46
        $data['other_partner_zone'] = Order::select('zone', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereNotIn('courier_partner', [Session()->get('MySeller')->courier_priority_1, Session()->get('MySeller')->courier_priority_2, Session()->get('MySeller')->courier_priority_3, Session()->get('MySeller')->courier_priority_4])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('zone')->get();//262.17
        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.71

        return view('seller.Dashboard.s_zone', $data);
    }
    function dashboardShipmentChannelData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['shipment_channel'] = Order::select('channel', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('channel')->get();//345.65

        return view('seller.Dashboard.s_channel', $data);
    }

    function dashboardShipmentWeightData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '<=', 500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//376.3
        $data['one_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 500)->where('weight', '<=', 1000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//207.71
        $data['one_half_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1000)->where('weight', '<=', 1500)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//203.67
        $data['two_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 1500)->where('weight', '<=', 2000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//213.88
        $data['five_kgs'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 2000)->where('weight', '<=', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//205.72
        $data['five_kgs_plus'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->where('weight', '>', 5000)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//193.76

        return view('seller.Dashboard.s_weight', $data);
    }
    function dashboardShipmentZoneData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['zone_a'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//220.96
        $data['zone_b'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//216.73
        $data['zone_c'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//193.73
        $data['zone_d'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//196.59
        $data['zone_e'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//191.46

        return view('seller.Dashboard.s_shipmentzone', $data);
    }

    function dashboardCourierTabData()
    {
        $data = $this->info;

        $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();//221.04
        $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();//182.03
        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.59
        $data['PartnerImage'] = Partners::getPartnerImage();//0.45

        $partner1 = Session()->get('MySeller')->courier_priority_1;
        $partner2 = Session()->get('MySeller')->courier_priority_2;
        $partner3 = Session()->get('MySeller')->courier_priority_3;
        $data['partner1_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->count();//56.84
        $data['partner2_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->count();//0.93
        $data['partner3_shipment'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->count();//18.38

        $data['partner1_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner1)->count();//142.72
        $data['partner2_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner2)->count();//0.86
        $data['partner3_cod'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('courier_partner', $partner3)->count();//0.98

        $data['partner1_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner1)->count();//144.25
        $data['partner2_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner2)->count();//1.53
        $data['partner3_prepaid'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->where('courier_partner', $partner3)->count();//1.5

        $data['partner1_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner1)->count();//332.78
        $data['partner2_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner2)->count();//1.25
        $data['partner3_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('courier_partner', $partner3)->count();//1.16

        $data['partner1_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner1)->count();//412.3
        $data['partner2_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner2)->count();//1.07
        $data['partner3_1st_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', '!=', 'y')->where('courier_partner', $partner3)->count();//1.01

        $data['partner1_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner1)->count();//299.33
        $data['partner2_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner2)->count();//1.0
        $data['partner3_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('ndr_status', 'y')->where('courier_partner', $partner3)->count();//101.38

        $data['partner1_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('ndr_status', 'y')->count();//0.88
        $data['partner2_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('ndr_status', 'y')->count();//1.02
        $data['partner3_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('ndr_status', 'y')->count();//74.16

        $data['partner1_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->where('rto_status', 'y')->count();//0.77
        $data['partner2_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->where('rto_status', 'y')->count();//0.72
        $data['partner3_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->where('rto_status', 'y')->count();//0.3

        $data['partner1_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner1)->whereIn('status', ['lost,damaged'])->count();//0.6
        $data['partner2_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner2)->whereIn('status', ['lost,damaged'])->count();//0.27
        $data['partner3_lost'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $partner3)->whereIn('status', ['lost,damaged'])->count();//0.26

        return view('seller.Dashboard.c_courier', $data);
    }

    function dashboardDelayTabData()
    {
        $data = $this->info;

        $data['cod_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->count();//174.95
        $data['prepaid_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'prepaid')->count();//185.96
        $data['lost_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'lost')->count();//19.83
        $data['damaged_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'damaged')->count();//0.99

        return view('seller.Dashboard.d_delays', $data);
    }
    function dashboardNdrTopTabData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending', 'cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();
        $ndrCounts = Order::select("ndr_action",'rto_status','status')->where('seller_id',Session()->get('MySeller')->id)->where('ndr_status','y')->where('awb_assigned_date','>=',$start_date)->where('awb_assigned_date','<=',$end_date)->get();
        $data['total_ndr'] = 0;$data['action_required'] = 0;$data['action_requested'] = 0;$data['ndr_delivered'] = 0;$data['ndr_rto'] = 0;$data['attempt1_total'] = 0;$data['attempt1_pending'] = 0;$data['attempt1_delivered'] = 0;$data['attempt1_rto'] = 0;$data['attempt1_lost'] = 0;$data['attempt2_total'] = 0;$data['attempt2_pending'] = 0;$data['attempt2_delivered'] = 0;$data['attempt2_rto'] = 0;$data['attempt2_lost'] = 0;$data['attempt3_total'] = 0;$data['attempt3_pending'] = 0;$data['attempt3_delivered'] = 0;$data['attempt3_rto'] = 0;$data['attempt3_lost'] = 0;
        foreach ($ndrCounts as $n){
            $n->ndr_count = count($n->ndrattempts);
            $data['total_ndr'] += 1;
            $data['action_required'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'pending') ? 1 : 0;
            $data['action_requested'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'requested') ? 1 : 0;
            $data['ndr_delivered'] += ($n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
            $data['ndr_rto'] += ($n->rto_status == 'y') ? 1 : 0;
        }

        return view('seller.Dashboard.n_ndrtop', $data);
    }
    function dashboardMiddleTabData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $ndrCounts = Order::select("ndr_action",'rto_status','status')->where('seller_id',Session()->get('MySeller')->id)->where('ndr_status','y')->where('awb_assigned_date','>=',$start_date)->where('awb_assigned_date','<=',$end_date)->get();//368.69
        $data['total_ndr'] = 0;$data['action_required'] = 0;$data['action_requested'] = 0;$data['ndr_delivered'] = 0;$data['ndr_rto'] = 0;$data['attempt1_total'] = 0;$data['attempt1_pending'] = 0;$data['attempt1_delivered'] = 0;$data['attempt1_rto'] = 0;$data['attempt1_lost'] = 0;$data['attempt2_total'] = 0;$data['attempt2_pending'] = 0;$data['attempt2_delivered'] = 0;$data['attempt2_rto'] = 0;$data['attempt2_lost'] = 0;$data['attempt3_total'] = 0;$data['attempt3_pending'] = 0;$data['attempt3_delivered'] = 0;$data['attempt3_rto'] = 0;$data['attempt3_lost'] = 0;
        foreach ($ndrCounts as $n){
            // Attempt 1
            $data['attempt1_total'] += $n->ndr_count <= 1 ? 1 : 0;
            $data['attempt1_pending'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
            $data['attempt1_delivered'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
            $data['attempt1_rto'] += ($n->ndr_count <= 1 && $n->rto_status == 'y') ? 1 : 0;
            $data['attempt1_lost'] += ($n->ndr_count <= 1 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

            // Attempt 2
            $data['attempt2_total'] += $n->ndr_count == 2 ? 1 : 0;
            $data['attempt2_pending'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
            $data['attempt2_delivered'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
            $data['attempt2_rto'] += ($n->ndr_count == 2 && $n->rto_status == 'y') ? 1 : 0;
            $data['attempt2_lost'] += ($n->ndr_count == 2 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

            // Attempt 3
            $data['attempt3_total'] += $n->ndr_count == 3 ? 1 : 0;
            $data['attempt3_pending'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
            $data['attempt3_delivered'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
            $data['attempt3_rto'] += ($n->ndr_count == 3 && $n->rto_status == 'y') ? 1 : 0;
            $data['attempt3_lost'] += ($n->ndr_count == 3 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;
        }

        return view('seller.Dashboard.n_ndrmiddle', $data);
    }

    function dashboardNdrSplitData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $seller_id = Session()->get('MySeller')->id;
        $data['reason_split'] = Ndrattemps::select('reason', DB::raw('count(*) as total_reason'))->where('seller_id', $seller_id)->groupBy('reason')->get();//309.97

        return view('seller.Dashboard.n_ndrsplit', $data);
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

    function dashboardNdrStatusTabData()
    {
        $data = $this->info;

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

        $data['this_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();//356.84
        $data['two_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();//191.49
        $data['three_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();//191.27
        $data['four_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();//196.95
        $data['five_week'] = Order::select('ndr_action', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->groupBy('ndr_action')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();//184.86

        return view('seller.Dashboard.n_ndrstatus', $data);
    }

    function dashboardNdrAttemptTabData()
    {
        $data = $this->info;

        $seller_id = Session()->get('MySeller')->id;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $ndrCounts = Order::select("ndr_action",'rto_status','status')->where('seller_id',Session()->get('MySeller')->id)->where('ndr_status','y')->where('awb_assigned_date','>=',$start_date)->where('awb_assigned_date','<=',$end_date)->get();
        $data['total_ndr'] = 0;$data['action_required'] = 0;$data['action_requested'] = 0;$data['ndr_delivered'] = 0;$data['ndr_rto'] = 0;$data['attempt1_total'] = 0;$data['attempt1_pending'] = 0;$data['attempt1_delivered'] = 0;$data['attempt1_rto'] = 0;$data['attempt1_lost'] = 0;$data['attempt2_total'] = 0;$data['attempt2_pending'] = 0;$data['attempt2_delivered'] = 0;$data['attempt2_rto'] = 0;$data['attempt2_lost'] = 0;$data['attempt3_total'] = 0;$data['attempt3_pending'] = 0;$data['attempt3_delivered'] = 0;$data['attempt3_rto'] = 0;$data['attempt3_lost'] = 0;
        foreach ($ndrCounts as $n){
            $data['attempt1_total'] += $n->ndr_count <= 1 ? 1 : 0;
            $data['attempt1_pending'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
            $data['attempt1_delivered'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
            $data['attempt1_rto'] += ($n->ndr_count <= 1 && $n->rto_status == 'y') ? 1 : 0;
            $data['attempt1_lost'] += ($n->ndr_count <= 1 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;
        }

        return view('seller.Dashboard.n_ndrattempt', $data);
    }

    function dashboardNdrSuccessbyZoneTabData()
    {
        $data = $this->info;

        $seller_id = Session()->get('MySeller')->id;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['z_ndr_raised_A'] = 0;$data['z_ndr_raised_B'] = 0;$data['z_ndr_raised_C'] = 0;$data['z_ndr_raised_D'] = 0;$data['z_ndr_raised_E'] = 0;
        $data['z_ndr_delivered_A'] = 0;$data['z_ndr_delivered_B'] = 0;$data['z_ndr_delivered_C'] = 0;$data['z_ndr_delivered_D'] = 0;$data['z_ndr_delivered_E'] = 0;
        $zoneCount = DB::select("select zone,ndr_status,rto_status,status from orders where seller_id = $seller_id;");//400.58
        foreach ($zoneCount as $zone){
            //zone ndr raised count
            $data['z_ndr_raised_A'] += ($zone->zone == 'A' && $zone->ndr_status == 'y') ? 1:0;
            $data['z_ndr_raised_B'] += ($zone->zone == 'B' && $zone->ndr_status == 'y') ? 1:0;
            $data['z_ndr_raised_C'] += ($zone->zone == 'C' && $zone->ndr_status == 'y') ? 1:0;
            $data['z_ndr_raised_D'] += ($zone->zone == 'D' && $zone->ndr_status == 'y') ? 1:0;
            $data['z_ndr_raised_E'] += ($zone->zone == 'E' && $zone->ndr_status == 'y') ? 1:0;

            //Zone ndr delivered count
            $data['z_ndr_delivered_A'] += ($zone->zone == 'A' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
            $data['z_ndr_delivered_B'] += ($zone->zone == 'B' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
            $data['z_ndr_delivered_C'] += ($zone->zone == 'C' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
            $data['z_ndr_delivered_D'] += ($zone->zone == 'D' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;
            $data['z_ndr_delivered_E'] += ($zone->zone == 'E' && $zone->ndr_status == 'y' && $zone->rto_status == 'n' && $zone->status == 'delivered') ? 1:0;

        }

        return view('seller.Dashboard.n_ndrsuccessbyzone', $data);
    }
    function dashboardNdrSuccessbyCourierTabData()
    {
        $data = $this->info;

        $seller_id = Session()->get('MySeller')->id;
        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray();//224.19

        foreach ($data['allPartners'] as $p){
            $data['p_ndr_raised'][$p]=Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('courier_partner', $p)->count();//93.29
            $data['p_ndr_delivered'][$p]=Order::where('seller_id', Session()->get('MySeller')->id)->where('status','delivered')->where('ndr_status', 'y')->where('courier_partner', $p)->count();//311.85
        }
        $data['p_ndr_raised']['other']=Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereNotIn('courier_partner', $data['allPartners'])->count();//182.98
        $data['p_ndr_delivered']['other']=Order::where('seller_id', Session()->get('MySeller')->id)->where('status','delivered')->where('ndr_status', 'y')->whereNotIn('courier_partner', $data['allPartners'])->count();//375.51

        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.75

        return view('seller.Dashboard.n_ndrsuccessbycourier', $data);
    }




    function reAttemptOrder($order,$reAttemptdata){
        switch ($order->courier_partner) {
            case 'xpressbees_surface':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $order->s_contact,
                    'customer_address' => $order->s_address_line1." ".$order->s_address_line2." ".$order->s_city." ".$order->s_state,
                    'pincode' => $order->s_pincode,
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    $this->xpressbeesReAttempt('admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0',$data);
                }
                else{
                    $this->xpressbeesReAttempt('admin@Twinnship.com','$Twinnship$','e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc',$data);
                }
                break;
            case 'ekart':
            case 'ekart_1kg':
            case 'ekart_2kg':
            case 'ekart_3kg':
            case 'ekart_5kg':
                break;
            case 'xpressbees_sfc':
            case 'xpressbees_surface_3kg':
            case 'xpressbees_surface_1kg':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $order->s_contact,
                    'customer_address' => $order->s_address_line1." ".$order->s_address_line2." ".$order->s_city." ".$order->s_state,
                    'pincode' => $order->s_pincode,
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    if($order->o_type == 'forward') {
                        if ($order->courier_partner == "xpressbees_surface_1kg")
                            $this->xpressbeesReAttempt(  'admin@alpahgroom.com', '$alpahgroom$','b2bfc4fb61c228bf91589bf8ec9feed1fdefb83dae516a7cbde4d6102c7db6be', $data);
                        else
                            $this->xpressbeesReAttempt(  'admin@uniqusurfa.com', '$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0', $data);
                    }
                }
                else {
                    $this->xpressbeesReAttempt( 'admin@shipesfc3.com', '$shipesfc3$','58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd', $data);
                }
                break;
            case 'xpressbees_surface_5kg':
            case 'xpressbees_surface_10kg':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $order->s_contact,
                    'customer_address' => $order->s_address_line1." ".$order->s_address_line2." ".$order->s_city." ".$order->s_state,
                    'pincode' => $order->s_pincode,
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    $this->xpressbeesReAttempt('admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0',$data);
                }
                else {
                    $this->xpressbeesReAttempt( 'admin@shipesfc5.com', '$shipesfc5$', '4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4', $data);
                }
                break;
            case 'wow_express':
                break;
            case 'delhivery_surface':
            case 'delhivery_air':
            case 'delhivery_surface_2kg':
            case 'delhivery_surface_5kg':
                if($order->is_alpha == 'NSE') {
                    $this->delhiveryReAttempt($order->awb_number, "be6d002daeb8bf53fc5e6dd25bf33a4d03a45891");
                }
                else {
                    $this->delhiveryReAttempt($order->awb_number, "894217b910b9e60d3d12cab20a3c5e206b739c8b");
                }
                break;
            case 'delhivery_surface_10kg':
                if($order->is_alpha == 'NSE')
                    $this->delhiveryReAttempt($order->awb_number,"9c6bb4a5969f73ce2bfe937a10140ce843f8096f");
                else
                    $this->delhiveryReAttempt($order->awb_number,"3141800ec51f036f997cd015fdb00e8aeb38e126");
                break;
            case 'delhivery_surface_20kg':
                if($order->is_alpha == 'NSE')
                    $this->delhiveryReAttempt($order->awb_number,"9c6bb4a5969f73ce2bfe937a10140ce843f8096f");
                else
                    $this->delhiveryReAttempt($order->awb_number,"18765103684ead7f379ec3af5e585d16241fdb94");
                break;
            case 'delhivery_b2b_20kg':
                break;
            case 'delhivery_lite':
                $this->delhiveryReAttempt($order->awb_number,"3c3f230a7419777f2a1f6b57933785a7e93ff43d");
                break;
            case 'shadowfax':
                $this->_ndrUpdateShadowFax($order->awb_number);
                break;
            case 'udaan':
            case 'udaan_1kg':
            case 'udaan_2kg':
            case 'udaan_3kg':
            case 'udaan_10kg':
                break;
            case 'dtdc_express':
            case 'dtdc_surface':
            case 'dtdc_2kg':
            case 'dtdc_3kg':
            case 'dtdc_5kg':
            case 'dtdc_6kg':
            case 'dtdc_1kg':
            case 'dtdc_10kg':
                break;
            case 'ecom_express':
            case 'ecom_express_rvp':
                $data = [
                    'awb_number' => $order->awb_number,
                    'remark' => $reAttemptdata['remark'],
                    'date' => $reAttemptdata['date'],
                ];
                $ecom = new EcomExpressController();
                $ecom->reAttempt($data);
                break;
            case 'ecom_express_3kg':
            case 'ecom_express_3kg_rvp':
                $data = [
                    'awb_number' => $order->awb_number,
                    'remark' => $reAttemptdata['remark'],
                    'date' => $reAttemptdata['date'],
                ];
                $ecom = new EcomExpress3kgController();
                $ecom->reAttempt($data);
                break;
            case 'bluedart':
            case 'bluedart_surface':
                break;
            case 'amazon_swa':
            case 'amazon_swa_1kg':
            case 'amazon_swa_3kg':
            case 'amazon_swa_5kg':
            case 'amazon_swa_10kg':
                break;
            case 'smartr':
                break;
            case 'bombax':
                break;
            case 'shree_maruti':
                break;
            case 'shree_maruti_ecom':
            case 'shree_maruti_ecom_1kg':
            case 'shree_maruti_ecom_3kg':
            case 'shree_maruti_ecom_5kg':
            case 'shree_maruti_ecom_10kg':
                $data = [
                    'awbNumber' => $order->awb_number,
                    'reattemptDate' => $reAttemptdata['date'],
                    'alternateNo' => $reAttemptdata['mobile'],
                    'newAddress' => $reAttemptdata['address'],
                    'remark' => $reAttemptdata['remark']
                ];
                $m = new MarutiEcom();
                $m->reAttempt($data);
                break;
            case 'gati':
                break;
            case 'movin':
            case 'movin_a':
                break;
            default:
                break;
        }
    }

    function ndrWithWhatsApp($order,$reAttemptdata){
        switch ($order->courier_partner) {
            case 'xpressbees_surface':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $reAttemptdata['mobile'],
                    'customer_address' => $reAttemptdata['address']." ".$reAttemptdata['city']." ".$reAttemptdata['state'],
                    'pincode' => $reAttemptdata['pincode'],
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    $this->xpressbeesReAttempt('admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0',$data);
                }
                else{
                    $this->xpressbeesReAttempt('admin@Twinnship.com','$Twinnship$','e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc',$data);
                }
                break;
            case 'ekart':
            case 'ekart_1kg':
            case 'ekart_2kg':
            case 'ekart_3kg':
            case 'ekart_5kg':
                break;
            case 'xpressbees_sfc':
            case 'xpressbees_surface_3kg':
            case 'xpressbees_surface_1kg':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $reAttemptdata['mobile'],
                    'customer_address' => $reAttemptdata['address']." ".$reAttemptdata['city']." ".$reAttemptdata['state'],
                    'pincode' => $reAttemptdata['pincode'],
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    if($order->o_type == 'forward') {
                        if ($order->courier_partner == "xpressbees_surface_1kg")
                            $this->xpressbeesReAttempt(  'admin@alpahgroom.com', '$alpahgroom$','b2bfc4fb61c228bf91589bf8ec9feed1fdefb83dae516a7cbde4d6102c7db6be', $data);
                        else
                            $this->xpressbeesReAttempt(  'admin@uniqusurfa.com', '$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0', $data);
                    }
                }
                else {
                    $this->xpressbeesReAttempt( 'admin@shipesfc3.com', '$shipesfc3$','58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd', $data);
                }
                break;
            case 'xpressbees_surface_5kg':
            case 'xpressbees_surface_10kg':
                $data = [
                    'awb_number' => $order->awb_number,
                    'differed_delivery_date' => $reAttemptdata['date'],
                    'customer_number' => $reAttemptdata['mobile'],
                    'customer_address' => $reAttemptdata['address']." ".$reAttemptdata['city']." ".$reAttemptdata['state'],
                    'pincode' => $reAttemptdata['pincode'],
                    'remark' => $reAttemptdata['remark']
                ];
                if($order->seller_order_type == 'NSE'){
                    $this->xpressbeesReAttempt('admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0',$data);
                }
                else {
                    $this->xpressbeesReAttempt( 'admin@shipesfc5.com', '$shipesfc5$', '4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4', $data);
                }
                break;
            case 'wow_express':
                break;
            case 'delhivery_surface':
            case 'delhivery_air':
            case 'delhivery_surface_2kg':
            case 'delhivery_surface_5kg':
                if($order->is_alpha == 'NSE') {
                    $this->delhiveryReAttempt($order->awb_number, "be6d002daeb8bf53fc5e6dd25bf33a4d03a45891");
                }
                else {
                    $this->delhiveryReAttempt($order->awb_number, "894217b910b9e60d3d12cab20a3c5e206b739c8b");
                }
                break;
            case 'delhivery_surface_10kg':
                if($order->is_alpha == 'NSE')
                    $this->delhiveryReAttempt($order->awb_number,"9c6bb4a5969f73ce2bfe937a10140ce843f8096f");
                else
                    $this->delhiveryReAttempt($order->awb_number,"3141800ec51f036f997cd015fdb00e8aeb38e126");
                break;
            case 'delhivery_surface_20kg':
                if($order->is_alpha == 'NSE')
                    $this->delhiveryReAttempt($order->awb_number,"9c6bb4a5969f73ce2bfe937a10140ce843f8096f");
                else
                    $this->delhiveryReAttempt($order->awb_number,"18765103684ead7f379ec3af5e585d16241fdb94");
                break;
            case 'delhivery_b2b_20kg':
                break;
            case 'delhivery_lite':
                $this->delhiveryReAttempt($order->awb_number,"3c3f230a7419777f2a1f6b57933785a7e93ff43d");
                break;
            case 'shadowfax':
                $this->_ndrUpdateShadowFax($order->awb_number);
                break;
            case 'udaan':
            case 'udaan_1kg':
            case 'udaan_2kg':
            case 'udaan_3kg':
            case 'udaan_10kg':
                break;
            case 'dtdc_express':
            case 'dtdc_surface':
            case 'dtdc_2kg':
            case 'dtdc_3kg':
            case 'dtdc_5kg':
            case 'dtdc_6kg':
            case 'dtdc_1kg':
            case 'dtdc_10kg':
                break;
            case 'ecom_express':
            case 'ecom_express_rvp':
                $data = [
                    'awb_number' => $order->awb_number,
                    'remark' => $reAttemptdata['remark'],
                    'date' => $reAttemptdata['date'],
                ];
                $ecom = new EcomExpressController();
                $ecom->reAttempt($data);
                break;
            case 'ecom_express_3kg':
            case 'ecom_express_3kg_rvp':
                $data = [
                    'awb_number' => $order->awb_number,
                    'remark' => $reAttemptdata['remark'],
                    'date' => $reAttemptdata['date'],
                ];
                $ecom = new EcomExpress3kgController();
                $ecom->reAttempt($data);
                break;
            case 'bluedart':
            case 'bluedart_surface':
                break;
            case 'amazon_swa':
            case 'amazon_swa_1kg':
            case 'amazon_swa_3kg':
            case 'amazon_swa_5kg':
            case 'amazon_swa_10kg':
                break;
            case 'smartr':
                break;
            case 'bombax':
                break;
            case 'shree_maruti':
                break;
            case 'shree_maruti_ecom':
            case 'shree_maruti_ecom_1kg':
            case 'shree_maruti_ecom_3kg':
            case 'shree_maruti_ecom_5kg':
            case 'shree_maruti_ecom_10kg':
                $data = [
                    'awbNumber' => $order->awb_number,
                    'reattemptDate' => $reAttemptdata['date'],
                    'alternateNo' => $reAttemptdata['mobile'],
                    'newAddress' => $reAttemptdata['address'].",".$reAttemptdata['city'].",".$reAttemptdata['state']."".$reAttemptdata['pincode'],
                    'remark' => $reAttemptdata['remark']
                ];
                $m = new MarutiEcom();
                $m->reAttempt($data);
                break;
            case 'gati':
                break;
            case 'movin':
            case 'movin_a':
                break;
            default:
                break;
        }
    }

    function delhiveryReAttempt($awb_number,$token){
        try {
            Logger::write('logs/partners/delhivery/delhivery-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Request For AWB: " . $awb_number,
                'data' => ['waybill' => $awb_number, 'act' => 'RE-ATTEMPT']
            ]);
            $response = Http::withHeaders(['Authorization' => "Token $token"])->post("https://track.delhivery.com/api/p/update", ['waybill' => $awb_number, 'act' => 'RE-ATTEMPT'])->json();
            Logger::write('logs/partners/delhivery/delhivery-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $awb_number,
                'data' => $response
            ]);
        }catch(Exception $e){
            Logger::write('logs/partners/delhivery/delhivery-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $awb_number,
                'data' => $e->getMessage()
            ]);
        }
        return true;
    }
    function xpressbeesReAttempt($username,$password,$secret,$data){
        try {
            $s = new ShippingController();
            $token = $s->_getXbeesToken($username, $password, $secret);
            $payload = [
                "ShippingID" => $data['awb_number'],
                "DeferredDeliveryDate" => $data['differed_delivery_date'],
                "PrimaryCustomerMobileNumber" => $data['customer_number'],
                "PrimaryCustomerAddress" => $data['customer_address'],
                "CustomerPincode" => $data['pincode'],
                "Comments" => $data['remark'],
                "LastModifiedBy" => $username,
                "IsMSSQL" => true
            ];
            Logger::write('logs/partners/xbees/xbees-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Request For AWB: " . $data['awb_number'],
                'data' => $payload
            ]);
            $httpResponse = Http::withHeaders(['token' => $token, 'versionnumber' => "v1"])->post("http://clientshipupdatesapi.xbees.in/client/UpdateNDRDeferredDeliveryDate", $payload)->json();
            Logger::write('logs/partners/xbees/xbees-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $data['awb_number'],
                'data' => $httpResponse
            ]);
        }catch(Exception $e){
            Logger::write('logs/partners/xbees/xbees-' . date('Y-m-d') . '.text', [
                'title' => "ReAttempt Response For AWB: " . $data['awb_number'],
                'data' => $e->getMessage()
            ]);
        }
    }

    function _ndrUpdateShadowFax($awb)
    {
        try {
            $data = array(
                "awb_numbers" => ["$awb"]
            );
            $response = Http::withHeaders([
                'Authorization' => 'Token 3ec26f6ffc8a87b9a8cddab2d0e58728ed6f3b76',
                'Content-Type' => 'application/json'
            ])->post('https://dale.shadowfax.in/api/v1/clients/ndr_update/', $data);
            $response = $response->json();
        }catch (Exception $e){

        }
    }

    function brandTrack()
    {
        $data = $this->info;
        $data['brand_tracking']= BrandedTracking::where('seller_id',Session()->get('MySeller')->id)->first();
        return view('seller.brand-tracking', $data);
    }

    function submitBrandTrack(Request $request){
        $checkData = BrandedTracking::where('seller_id',Session()->get('MySeller')->id)->first();
        $data=array(
            'offer_title' => $request->offer_title,
            'product_title1' => $request->product_title1,
            'product_title2' => $request->product_title2,
            'product_title3' => $request->product_title3,
            'product_title4' => $request->product_title4,
            'product_amount1' => $request->product_amount1,
            'product_amount2' => $request->product_amount2,
            'product_amount3' => $request->product_amount3,
            'product_amount4' => $request->product_amount4,
            'link' => $request->link,
            'link1' => $request->link1,
            'link2' => $request->link2,
            'link3' => $request->link3,
            'seller_id' => Session()->get('MySeller')->id,
            'status' => 'y',
        );
        if ($request->hasFile('brand_logo')) {
            $oName = $request->brand_logo->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->brand_logo->move(public_path('assets/admin/images/'), $name);
            $data['brand_logo'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('banner1')) {
            $oName = $request->banner1->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->banner1->move(public_path('assets/admin/images/'), $name);
            $data['banner1'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('banner2')) {
            $oName = $request->banner2->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->banner2->move(public_path('assets/admin/images/'), $name);
            $data['banner2'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_image1')) {
            $oName = $request->product_image1->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_image1->move(public_path('assets/admin/images/'), $name);
            $data['product_image1'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_image2')) {
            $oName = $request->product_image2->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_image2->move(public_path('assets/admin/images/'), $name);
            $data['product_image2'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_image3')) {
            $oName = $request->product_image3->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_image3->move(public_path('assets/admin/images/'), $name);
            $data['product_image3'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_image4')) {
            $oName = $request->product_image4->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_image4->move(public_path('assets/admin/images/'), $name);
            $data['product_image4'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_back_image1')) {
            $oName = $request->product_back_image1->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_back_image1->move(public_path('assets/admin/images/'), $name);
            $data['product_back_image1'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_back_image2')) {
            $oName = $request->product_back_image2->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_back_image2->move(public_path('assets/admin/images/'), $name);
            $data['product_back_image2'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_back_image3')) {
            $oName = $request->product_back_image3->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_back_image3->move(public_path('assets/admin/images/'), $name);
            $data['product_back_image3'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('product_back_image4')) {
            $oName = $request->product_back_image4->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->product_back_image4->move(public_path('assets/admin/images/'), $name);
            $data['product_back_image4'] = $filepath;
            $apiToExecute = "https://twinnship.com/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }

        if(!empty($checkData))
            BrandedTracking::where('id',$request->id)->update($data);
        else
            BrandedTracking::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'BrandedTracking updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function shopifyAppOnInstallation(Request $request){
        try{
            if(empty($request->code)){
                // redirect on shopify
                if(!empty($request->embedded)){
//                    dd($request->all(),$request->header());
                    $channel = Channels::where('store_url',$request->shop)->where('channel','shopify')->first();
                    if(!empty($channel)){
                        $sellerData = Seller::find($channel->seller_id);
                        if(!empty($sellerData)) {
                            $sellerData->type = 'sel';
                            $sellerData->permissions = 'all';
                            $codRemit = $this->utilities->getNextCodRemitDate($sellerData->id);
                            $sellerData->cod_balance = $codRemit['nextRemitCod'];
                            $gst_number = Basic_informations::where('seller_id', $sellerData->id)->get();
                            $sellerData->gst_number = $gst_number[0]->gst_number ?? "";
                            if (!empty($channel->woo_consumer_key)) {
                                $channel->woo_consumer_key = $_GET['session'];
                                $channel->save();
//                                if($channel->woo_consumer_key == $_GET['session']) {
                                    Session(['MySeller' => $sellerData]);
                                    return redirect(route('seller.dashboard'));
//                                }
//                                else{
//                                    return response()->json(
//                                        ['status' => false,'message' => "Invalid Credential"]
//                                    );
//                                }
                            } else {
                                $channel->woo_consumer_key = $_GET['session'];
                                $channel->save();
                                Session(['MySeller' => $sellerData]);
                                return redirect(route('seller.dashboard'));
                            }
                        }
                        else{
                            return response()->json(
                                ['status' => false,'message' => "Seller not found"]
                            );
                        }
                    }
                    return response()->json(
                        ['status' => false,'message' => "Store not found"]
                    );
                }
                else{
                    $scopes = [
                        'read_all_orders',
                        'read_assigned_fulfillment_orders',
                        'write_assigned_fulfillment_orders',
                        'read_cart_transforms',
                        'write_cart_transforms',
                        'read_customers',
                        'write_customers',
                        'read_draft_orders',
                        'write_draft_orders',
                        'read_fulfillments',
                        'write_fulfillments',
                        'write_fulfillments',
                        'read_merchant_managed_fulfillment_orders',
                        'write_merchant_managed_fulfillment_orders',
                        'read_orders',
                        'write_orders',
                        'unauthenticated_read_checkouts',
                        'unauthenticated_write_checkouts',
                        'read_third_party_fulfillment_orders',
                        'write_third_party_fulfillment_orders'
                    ];
                    $url = "https://{$_GET['shop']}/admin/oauth/authorize?client_id=c017075f404c3625696bdb3eea5efd72&scopes=".implode(',',$scopes)."&redirect_uri=".urlencode("https://www.twinnship.com/shopify/shopify-app");
                    return redirect($url);
                }

            }
            $shared_secret = "ee403f4f99b7671debb20fea1d434c25";
            $params = $_GET; // Retrieve all request parameters
            $hmac = $_GET['hmac']; // Retrieve HMAC request parameter
            $params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
            ksort($params); // Sort params lexographically

            // Compute SHA256 digest
            $computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);
            // Use hmac data to check that the response is from Shopify or not
            if (hash_equals($hmac, $computed_hmac)) {
                // Get Access Key
                $data = [
                    "client_id" => 'c017075f404c3625696bdb3eea5efd72', // Your API key
                    "client_secret" => 'ee403f4f99b7671debb20fea1d434c25', // Your app credentials (secret key)
                    "code" => $params['code'] // Grab the access key from the URL
                ];
                $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
                $response = Http::post("$access_token_url",$data)->json();
                // static data put in response remove when Live
                if(!empty($response)){
                    $accessToken = $response['access_token'];
                    // Get Seller Details
                    $sellerDetails = Shopify::GetStoreDetails($accessToken,$params['shop']);
                    $sellerData = $this->CheckAndCreateSellerShopify($sellerDetails['shop']);
                    $this->CheckAndCreateWarehouse($sellerDetails['shop'],$sellerData);
                    $this->CheckAndCreateChannel($sellerData,$accessToken,$params['shop']);
                    $seller = Seller::find($sellerData->id);
                    $seller->gst_number = "NANANANANANANAN";
                    $seller->type = 'sel';
                    $seller->permissions = 'all';
                    Session(['MySeller' => $seller]);
                    //$this->utilities->generate_notification('Success', 'Channels added Successfully', 'success');
                }else{
                    $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
                }
            } else {
                $this->utilities->generate_notification('Error', 'Consent not accepted please try to delete and add channel again', 'error');
            }
            return redirect(route('seller.orders'));
        }catch(Exception $e){
            dd($e->getMessage()." - ".$e->getFile()." - ".$e->getLine());
        }
    }
    function CheckAndCreateSellerShopify($sellerDetails){
        $existing = Seller::where('email',$sellerDetails['email'])->first();
        if(!empty($existing)){
            $sellerData = $existing;
        }else{
            $name = explode(" ",$sellerDetails['shop_owner']);
            $data = [
                'code' => '',
                'first_name' => $name[0],
                'last_name' => $name[1] ?? $name[0],
                'email' => $sellerDetails['email'],
                'mobile' => "9999999999",
                'company_name' => $sellerDetails['name'],
                'password' => Hash::make("Twinnship@123#"),
                'balance' => 0,
                'basic_information' => 'y',
                'account_information' => 'y',
                'kyc_information' => 'y',
                'agreement_information' => 'y',
                'warehouse_status' => 'y',
                'created_at' => date('Y-m-d H:i:s'),
                'registered_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
                'status' => 'y',
                'verified' => 'y',
                'created_by' => 'Shopify',
                'plan_id' => 1,
                'gst_certificate_status' => 'y',
                'cheque_status' => 'y',
                'document_status' => 'y',
                'agreement_status' => 'y',
                'rto_charge' => 100,
                'reverse_charge' => 100
            ];
            $sellerData = Seller::create($data);
            $code = str_pad($sellerData->id, 5, "0", STR_PAD_LEFT);
            $sellerData->code = "TW-1{$code}";
            $sellerData->save();
        }
        return $sellerData;
    }
    function CheckAndCreateWarehouse($sellerDetails,$sellerData){
        $existing = Warehouses::where('seller_id',$sellerData->id)->where('default','y')->first();
        if(empty($existing)){
            $warehouse = [
                'seller_id' => $sellerData->id,
                'warehouse_name' => "DefaultWarehouse",
                'contact_name' => $sellerDetails['shop_owner'],
                'contact_number' => "9999999999",
                'address_line1' => $sellerDetails['address1'],
                'address_line2' => $sellerDetails['address2'],
                'city' => $sellerDetails['city'],
                'code' => "+91",
                'state' => $sellerDetails['province'],
                'country' => $sellerDetails['country'],
                'pincode' => $sellerDetails['zip'],
                'gst_number' => "NANANANANANANAN",
                'support_email' => $sellerDetails['email'],
                'support_phone' => "9999999999",
                'warehouse_code' => "DefaultWarehouse_" .$sellerData->code,
                'default' => 'y',
                'created_at' => date('Y-m-d H:i:s')
            ];
            Warehouses::create($warehouse)->id;
            return true;
        }else{
            return true;
        }
    }
    function CheckAndCreateChannel($sellerData,$accessToken,$storeUrl){
        $channel = Channels::where('seller_id',$sellerData->id)->where('store_url',$storeUrl)->first();
        if(empty($channel)){
            $data = [
                'seller_id' => $sellerData->id,
                'channel_name' => "Shopify Channel",
                'channel' => "shopify",
                'password' => $accessToken,
                'store_url' => $storeUrl,
                'last_sync' => date('Y-m-d H:i:s',strtotime('-2 days')),
                'last_executed' => date('Y-m-d H:i:s',strtotime('-2 days')),
                'status' => 'y',
                'created' => date('Y-m-d H:i:s')
            ];
            Channels::create($data);
        }
        return true;
    }
    function revertCODRemittance(Request $request){
        $awbList = explode(",",$request->awb_numbers);
        foreach ($awbList as $a){
            $orderData = Order::where('awb_number',$a)->first();
            if(empty($orderData) || strtolower($orderData->order_type) != 'cod')
                continue;
            $orderData->cod_remmited = 'n';
            $orderData->save();
            Seller::where('id',$orderData->seller_id)->increment('cod_balance',$orderData->invoice_amount);
        }
        return response()->json(['status' => true,'message' => 'Data reverted successfully']);
    }

    function dashboard1()
    {
        $data = $this->info;
        return view('seller.dashboard1', $data);
    }

    function dashboardTop()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['total_created'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('inserted', '=', date('Y-m-d'))->count(); //278.41
        $data['total_revanue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->avg('invoice_amount'); //489.51
        $data['total_customer'] = Order::distinct('b_contact')->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>', Carbon::now()->subDays(30))->count(); //362.22
        $data['today_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->count(); //337.64
        $data['today_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::today())->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount'); //343.49
        $data['yesterday_revenue'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', Carbon::now()->addDay(-1))->where('status','!=','pending')->where('status','!=','cancelled')->sum('invoice_amount'); //330.08

        return view('seller.Dashboard.d_top', $data);
    }

    function dashboardOrderShipment()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['total_all_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//328.96
        $data['shipped_orders'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status', ['pending','cancelled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//349.47
        $data['pending_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereIn('status', ['manifested','pickup_scheduled'])->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//86.8
        $data['picked_up'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'picked_up')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//21.44
        $data['out_for_delivery'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'out_for_delivery')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//26.41
        $data['delivered_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//896.31
        $data['intransit_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->where('rto_status','n')->where('ndr_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//423.69
        $data['ndr_pending'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//592.68
        $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//130.7

        return view('seller.Dashboard.d_ordershipment', $data);
    }
    function dashboardNdrData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['total_ndr'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//339.15
        $data['action_required'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'pending')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//571.98
        $data['action_requested'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status','n')->where('status', '!=', 'delivered')->where('ndr_action', 'requested')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//380.42
        $data['ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('status', 'delivered')->where('rto_status','n')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//754.43
        $data['ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('ndr_status', 'y')->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//238.71

        return view('seller.Dashboard.d_ndr', $data);
    }

    function dashboardCodData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['cod_total'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('status', 'delivered')->where('rto_status','n')->sum('invoice_amount'),2);//996.83
        $data['remitted_cod'] = round(Order::where('seller_id', Session()->get('MySeller')->id)->where('order_type', 'cod')->where('rto_status','n')->where('status', 'delivered')->where('cod_remmited', 'y')->sum('invoice_amount'),2);//655.73
        $codArray = $this->utilities->getNextCodRemitDate(Session()->get('MySeller')->id);//0.66
        $data['nextRemitDate'] = $codArray['nextRemitDate'];
        $data['nextRemitCod'] = round($codArray['nextRemitCod'],2);

        return view('seller.Dashboard.d_cod', $data);
    }

    function dashboardRtoData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['rto_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//135.5
        $data['rto_initiated'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','=','rto_initiated')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//91.44
        $data['rto_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//614.35
        $data['rto_undelivered'] = $data['rto_order'] - $data['rto_initiated'] - $data['rto_delivered'];

        return view('seller.Dashboard.d_rto', $data);
    }
    function dashboardCourierSplitDatas()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['states'] = States::limit(36)->get(); //0.31
        $data['mapData'] = [];
        foreach ($data['states'] as $s) {
            $count = Order::where('seller_id', Session()->get('MySeller')->id)->where('s_state', $s->state)->whereNotIn('status', ['pending', 'cancelled'])->count();//352.2
            $data['mapData'][] = [
                'id' => $s->code,
                'value' => $count ?? 0
            ];
        } // each and every time time change
        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.7
        $data['courier_split'] = Order::select(DB::raw('distinct(courier_partner)'), DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', '!=', 'pending')->groupBy('courier_partner')->get();//218.74


        return view('seller.Dashboard.d_couriersplit', $data);
    }
    function dashboardOverallData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status','n')->where('status', 'delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//1083.19
        $data['undelivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', '!=', 'delivered')->where('ndr_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//347.13
        $data['intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'in_transit')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//78.46
        $data['rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//211.0
        $data['damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('status', 'damaged')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//2.05

        return view('seller.Dashboard.d_overall', $data);
    }

    function dashboardDeliveredData()
    {
        $data = $this->info;

        $res = DB::select("SELECT count(*) as total from `orders` where `seller_id`=" . Session()->get('MySeller')->id . " and `status`='delivered' and `delivered_date` <= `expected_delivery_date` or `seller_id`=" . Session()->get('MySeller')->id . " and `status`='delivered' and `expected_delivery_date` is NULL");//618.71
        $data['ontime_delivery'] = $res[0]->total ?? 0;
        $res = DB::select("SELECT count(*) as total from `orders` where seller_id=" . Session()->get('MySeller')->id . " and `status`='delivered' and `delivered_date` > `expected_delivery_date`");//676.11
        $data['late_delivery'] = $res[0]->total ?? 0;

        return view('seller.Dashboard.d_delivered', $data);
    }

    function dashboardStateData()
    {
        $data = $this->info;

        $data['states'] = States::limit(36)->get(); //0.32
        $data['mapData'] = [];
        foreach ($data['states'] as $s) {
            $count = Order::where('seller_id', Session()->get('MySeller')->id)->where('s_state', $s->state)->whereNotIn('status', ['pending', 'cancelled'])->count();//348.98
            $data['mapData'][] = [
                'id' => $s->code,
                'value' => $count ?? 0
            ];
        } //each loop time change


        return view('seller.Dashboard.d_statesplit', $data);
    }

//    public function dashboardStateData1()
//    {
//        $data = $this->info;
//
//        if (!empty(session('d_start_date'))) {
//            $start_date = session('d_start_date');
//            $end_date = session('d_end_date');
//        } else {
//            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
//            $end_date = date('Y-m-d');
//        }
//
//        $sellerId = Session()->get('MySeller')->id;
//        $startDate = $start_date;
//        $endDate = $end_date;
//
//        $response = Http::withHeaders([
//            'Content-Type' => 'application/json',
//        ])
//            ->post('https://www.twinnship.com/api/microservices/dashboard/overview/state-split-wise', [
//                'sellerId' => $sellerId,
//                'start' => $startDate,
//                'end' => $endDate,
//            ]);
//
//        $responseData = $response->json()['data'];
//
//        $mapData = array_map(function ($item) {
//            return [
//                'id' => 'IN.' . strtoupper(str_replace(' ', '_', $item['s_state'])),
//                'value' => $item['total'],
//            ];
//        }, $responseData);
//
//        $data['mapData'] = json_encode($mapData);
//
//        return view('seller.Dashboard.d_statesplit', $data);
//    }




    function dashboardShipmentData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['zone_a'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'A')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//337.37
        $data['zone_b'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'B')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//270.38
        $data['zone_c'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'C')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//327.83
        $data['zone_d'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'D')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//356.67
        $data['zone_e'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('zone', 'E')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//352.13

        return view('seller.Dashboard.d_shipment', $data);
    }

    function dashboardOverviewRevenue()
    {
        $data = $this->info;

        $current_quarter = ceil(date('n') / 3);
        $first_date = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
        $last_date = date('Y-m-t', strtotime(date('Y') . '-' . (($current_quarter * 3)) . '-1'));
        $data['revenue_lifetime'] = Order::where('seller_id', Session()->get('MySeller')->id)->sum('invoice_amount');//354.33
        $data['revenue_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('invoice_amount');//251.82
        $data['revenue_month'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereMonth('awb_assigned_date', Carbon::now()->month)->sum('invoice_amount');//216.41
        $data['revenue_year'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('invoice_amount');//204.11
        $data['revenue_quarter'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereBetween('awb_assigned_date', [$first_date, $last_date])->sum('invoice_amount');//211.43

        return view('seller.Dashboard.d_overviewrevenue', $data);
    }

    function dashboardShipmentByCourierDataInfo()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }

        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.54
        $data['allPartners'] = Order::select('courier_partner',DB::raw('count(courier_partner) as total'))->where('seller_id',Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->groupBy('courier_partner')->orderBy('total','desc')->limit(4)->get()->pluck('courier_partner')->toArray(); //355.7
        foreach ($data['allPartners'] as $p){
            //for Courirer Partner 1 Overview
            $data['partner_unscheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->count();//100.95
            $data['partner_scheduled'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->count();//37.56
            $data['partner_intransit'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->count();//677.28
            $data['partner_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('rto_status','n')->where('ndr_status','n')->count();//630.36
            $data['partner_ndr_raised'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->count();//298.84
            //remove NDR Raised Column from Courier Overview
            $data['partner_ndr_delivered'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->count();//561.3
            $data['partner_ndr_pending'][$p] = $data['partner_ndr_raised'][$p] - $data['partner_ndr_delivered'][$p];
            $data['partner_ndr_rto'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->count();//84.4
            $data['partner_damaged'][$p] = Order::where('seller_id', Session()->get('MySeller')->id)->where('courier_partner', $p)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->count();//2.4
            $data['partner_total'][$p] = $data['partner_unscheduled'][$p] + $data['partner_scheduled'][$p] + $data['partner_intransit'][$p] + $data['partner_delivered'][$p] + $data['partner_ndr_delivered'][$p] + $data['partner_ndr_pending'][$p] + $data['partner_ndr_rto'][$p] + $data['partner_damaged'][$p];
        }
        $data['other_partner_unscheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['manifested','pickup_scheduled'])->whereNotIn('courier_partner', $data['allPartners'])->count();//57.0
        $data['other_partner_scheduled'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'picked_up')->whereNotIn('courier_partner', $data['allPartners'])->count();//19.77
        $data['other_partner_intransit'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['in_transit','out_for_delivery'])->where('rto_status','n')->where('ndr_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();//629.59
        $data['other_partner_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'n')->where('rto_status','n')->where('status', 'delivered')->whereNotIn('courier_partner', $data['allPartners'])->count();//741.43
        $data['other_partner_ndr_raised'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();//282.46
        $data['other_partner_ndr_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('status', 'delivered')->where('ndr_status', 'y')->where('rto_status','n')->whereNotIn('courier_partner', $data['allPartners'])->count();//469.8
        $data['other_partner_ndr_pending'] = $data['other_partner_ndr_raised'] - $data['other_partner_ndr_delivered'];
        $data['other_partner_ndr_rto'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->where('rto_status', 'y')->whereNotIn('courier_partner', $data['allPartners'])->count();//59.71
        $data['other_partner_damaged'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->whereIn('status', ['damaged', 'lost'])->whereNotIn('courier_partner', $data['allPartners'])->count();//1.95
        $data['other_partner_total'] = $data['other_partner_unscheduled'] + $data['other_partner_scheduled'] + $data['other_partner_intransit'] + $data['other_partner_delivered'] + $data['other_partner_ndr_delivered'] + $data['other_partner_ndr_pending'] + $data['other_partner_ndr_rto'] + $data['other_partner_damaged'];


        return view('seller.Dashboard.d_shipmentbycourier', $data);
    }

    //for display amazon direct form
    function addFlipkart()
    {
        $data = $this->info;
        return view('seller.add-flipkart', $data);
    }

    // for adding API details of shopify
    function submitFlipkart(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'channel_name' => $request->channel_name,
            'channel' => 'flipkart'
        );
        $channelDetail = Channels::create($data);
        $stateData = Session()->get('MySeller')->id."-".$channelDetail->id;
        // generating notification
        $redirectUrl = "https://api.flipkart.net/oauth-service/oauth/authorize?client_id=9879485a7106b73058680260ba2571b89aba&redirect_uri=https://www.twinnship.com/oauth/flipkart-redirect&response_type=code&scope=Seller_Api&state={$stateData}";
        return redirect($redirectUrl);
    }
    function flipkartRedirect(Request $request){
        $tokenResponse = Flipkart::GetRefreshToken($request->code,$request->state);
        $stateData = explode("-",$request->state);
        if(count($stateData) != 2){
            $this->utilities->generate_notification('Error', 'Invalid Response Received from Flipkart', 'error');
            return redirect(route('seller.channels'));
        }
        if(!empty($tokenResponse['refresh_token'])){
            $channelData = Channels::find($stateData[1]);
            if(empty($channelData)){
                $this->utilities->generate_notification('Error', 'Invalid Response Received from Flipkart', 'error');
                return redirect(route('seller.channels'));
            }
            $channelData->amazon_refresh_token = $tokenResponse['refresh_token'];
            $this->utilities->generate_notification('Success', 'Channel Integrated Successfully', 'success');
            return redirect(route('seller.channels'));
        }
        return redirect(route('seller.channels'));
    }

    function dashboardRtoDetailTabData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//332.52
        $data['rto_initiated'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//99.08
        $data['rto_undelivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','!=','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//154.67
        $data['rto_delivered'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->where('status','delivered')->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//521.81

        return view('seller.Dashboard.r_rtodetail', $data);
    }
    function dashboardRtoCountTabData()
    {
        $data = $this->info;

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
        $data['this_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->count();//337.07
        $data['two_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->count();//292.13
        $data['three_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->count();//
        $data['four_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->count();
        $data['five_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->count();

        return view('seller.Dashboard.r_rtocount', $data);
    }

    function dashboardRtoStatusTabData()
    {
        $data = $this->info;

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
        $data['this_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->count();//73.73
        $data['two_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->count();//127.44
        $data['three_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->count();//102.32
        $data['four_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->count();//124.96
        $data['five_week'] = Order::where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->count();//105.46

        $data['this_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $thisdate['start_date'])->whereDate('inserted', '<=', $thisdate['end_date'])->get();//0.46
        $data['two_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $two_date['start_date'])->whereDate('inserted', '<=', $two_date['end_date'])->get();//0.36
        $data['three_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $three_date['start_date'])->whereDate('inserted', '<=', $three_date['end_date'])->get();//0.35
        $data['four_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $four_date['start_date'])->whereDate('inserted', '<=', $four_date['end_date'])->get();//0.35
        $data['five_week_status'] = Order::select('rto_status', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('status', 'rto')->groupBy('rto_status')->whereDate('inserted', '>=', $five_date['start_date'])->whereDate('inserted', '<=', $five_date['end_date'])->get();//0.34

        return view('seller.Dashboard.r_rtostatus', $data);
    }

    function dashboardRtoReasonTabData()
    {
        $data = $this->info;

        $data['reason_split'] = Ndrattemps::select('reason', DB::raw('count(*) as total_reason'))->where('seller_id', Session()->get('MySeller')->id)->groupBy('reason')->get();//220.62

        return view('seller.Dashboard.r_rtoreason', $data);
    }

    function dashboardRtoPincodeTabData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//377.97
        $data['top_pincodes'] = Order::select('s_pincode', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('s_pincode')->get();//92.11
        $data['top_cities'] = Order::select('s_city', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('s_city')->limit(5)->get();//394.52

        return view('seller.Dashboard.r_rtopincode', $data);
    }

    function dashboardRtoCourierTabData()
    {
        $data = $this->info;

        if (!empty(session('d_start_date'))) {
            $start_date = session('d_start_date');
            $end_date = session('d_end_date');
        } else {
            $start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
            $end_date = date('Y-m-d');
        }
        $data['total_order'] = Order::where('seller_id', Session()->get('MySeller')->id)->whereDate('awb_assigned_date', '>=', $start_date)->whereDate('awb_assigned_date', '<=', $end_date)->count();//337.07
        $data['top_courier'] = Order::select('courier_partner', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('courier_partner')->limit(5)->get();//292.13
        $data['top_customer'] = Order::select('b_customer_name', DB::raw('count(*) as total_order'))->where('seller_id', Session()->get('MySeller')->id)->where('rto_status', 'y')->groupBy('b_customer_name')->limit(5)->get();//259.71
        $data['PartnerName'] = Partners::getPartnerKeywordList();//0.6

        return view('seller.Dashboard.r_rtocourier', $data);
    }

    function CCAvenueResponse(Request $request){
        try {
            $workingKey = '6A1FEDDFCF83661A555FA7A7EBFB0D16'; //Working Key should be provided here.
            $encResponse = $_POST["encResp"];

            $rcvdString = Utilities::decrypt($encResponse, $workingKey);        //Crypto Decryption used as per the specified working key.
            $decryptValues = explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);

            for($i = 0; $i < $dataSize; $i++)
            {
                $information=explode('=',$decryptValues[$i]);
                if($i==3)	$order_status=$information[1];
            }
            foreach ($decryptValues as $item) {
                // Split each item on '=' to separate key and value
                list($key, $value) = explode('=', $item, 2);

                // If the value is 'null', convert it to a null value
                $value = ($value === 'null') ? null : $value;

                // Assign key-value pairs to the associative array
                $assocArray[$key] = $value;
            }
            $seller = Seller::find($assocArray['merchant_param4']);
            $promocode = $assocArray['merchant_param1'];
            $checkData = CCAvenueTransaction::where('id',$assocArray['merchant_param5'])->where('order_id',$assocArray['order_id'])->where('seller_id',$assocArray['merchant_param4'])->first();
            Session(['MySeller' => $seller]);
            if($assocArray['order_id'] == $assocArray['order_id'] && $assocArray['order_status'] == "Success")
            {
                if(!empty($checkData)) {
                    if (!empty($seller)) {
                        $data = array(
                            'seller_id' => $seller->id,
                            'amount' => $checkData->amount,
                            'balance' => $checkData->amount + $seller->balance,
                            'type' => 'c',
                            'datetime' => date('Y-m-d H:i:s'),
                            'razorpay_payment_id' => $assocArray['tracking_id'],
                            'razorpay_order_id' => $assocArray['order_id'],
                            'method' => $assocArray['payment_mode'],
                            'description' => "Wallet Recharge"
                        );
                        Transactions::create($data);
                        Seller::where('id', $seller->id)->increment('balance', $data['amount']);
                        if (!empty($promocode)){
                            $this->apply_promo($promocode,$checkData->amount);
                        }
                        $this->utilities->generate_notification('Recharge Successful', 'Your Recharge has been completed successfully', 'success');
                    }
                    CCAvenueTransaction::where('id',$checkData->id)->update(['status' => $assocArray['order_status']]);
                }
                return redirect(route('seller.dashboard'));
            }
            $this->utilities->generate_notification('Recharge Unsuccessful', 'Payment Gateway under maintenance please try after some time..', 'error');
            return redirect(route('seller.dashboard'));
        }catch (Exception $e){
            $this->utilities->generate_notification('Recharge Unsuccessful', 'Payment Gateway under maintenance please try after some time..', 'error');
            return redirect(route('seller.dashboard'));
        }
    }
    function redirectToReAssign($id,Request $request){
        $data = Notifications::where('id',$id)->first();
        if(empty($data))
            return back();
        $awbList = json_decode($data->data,true);
        Session::put("reassign_order_awb_search", implode(",",$awbList));
        $data->read_at = date('Y-m-d H:i:s');
        $data->save();
        return redirect(route('seller.reassign_orders'));
    }
    function downloadZoneMappingSeller(Request $request){
        $sellerData = Seller::find($request->sellerId);
        if(empty($sellerData))
            return response()->json(['status' => false, 'message' => 'Seller data not found']);
        if(empty($request->pickup_pincode))
            return response()->json(['status' => false, 'message' => 'Pickup pincode not provided']);
        $name = "exports/zone_mapping";
        $filename = "zone_mapping";
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No', 'Pickup Pincode', 'Delivery Pincode', 'Zone');
        fputcsv($fp, $info);
        $cnt = 1;
        $zoneMapping = ZoneMapping::where('pincode','!=',$request->pickup_pincode)->pluck('pincode')->toArray();
        foreach ($zoneMapping as $z){
            $matchCriteria = MyUtility::findMatchCriteria($request->pickup_pincode, $z, $sellerData);
            $info = [$cnt++,$request->pickup_pincode, $z, $this->GetZone($matchCriteria)];
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
        exit;
    }
    function GetZone($criteria){
        $zone = 'D';
        switch ($criteria){
            case 'within_city':
                $zone = 'A';
                break;
            case 'within_state':
                $zone = 'B';
                break;
            case 'metro_to_metro':
                $zone = 'C';
                break;
            case 'north_j_k':
                $zone = 'E';
                break;
        }
        return $zone;
    }

    function loadAllWarehouse()
    {
        $data['warehouse'] = Warehouses::where('seller_id', Session()->get('MySeller')->id)->get();
        return view('seller.partial.load-warehouse',$data);
    }

    function createWarehouseOrder(Request $request)
    {
        $request->warehouse_name = preg_replace('/[^A-Za-z0-9\ ]/', ' ', $request->warehouse_name);
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'warehouse_name' => $request->warehouse_name,
            'contact_name' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'address_line1' => $request->address1,
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
        Warehouses::create($data)->id;
        if (Session()->get('MySeller')->warehouse_status == 'n')
            Seller::where('id', Session()->get('MySeller')->id)->update(['warehouse_status' => 'y']);

        //add Warehouse for Delhivery
        UtilityHelper::CreateWarehouseDelhivery($data);
        return response()->json(['status' => true, 'message' => 'Warehouse Created Successfully']);
    }
    function loadSellerBalance()
    {
        UtilityHelper::RefreshSellerSession();
        $holdBalance = WeightReconciliation::where('seller_id', Session()->get('MySeller')->id)->whereNotIn('status',['accepted', 'auto_accepted'])->sum(DB::raw('charged_amount - applied_amount'));
        return response()->json(['balance' => Session()->get('MySeller')->balance, 'hold_balance' => $holdBalance ?? 0]);
    }

    function apply_promo($code, $amount)
    {
        $code = Redeem_codes::where('code', $code)->get();
        if (count($code) == 0) {
//            echo json_encode(['status' => 'false', 'message' => 'Invalid Redeem Code please check the code you have entered']);
            return true;
        }
        $used = Redeems::where('seller_id', Session()->get('MySeller')->id)->where('code_id', $code[0]->id)->get();
        if (count($used) != 0) {
//            echo json_encode(['status' => 'false', 'message' => 'You have already redeemed this code']);
            return true;
        }
        if ($code[0]->limit == 0) {
//            echo json_encode(['status' => 'false', 'message' => 'This Redeem code has exceeded its use limit']);
            return true;
        }
        if ($amount < $code[0]->min_value){
            return true;
        }
        //
        $seller = Seller::find(Session()->get('MySeller')->id);
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'amount' => $code[0]->value,
            'balance' => $code[0]->value + $seller->balance,
            'type' => 'c',
            'datetime' => date('Y-m-d H:i:s'),
            'method' => 'PROMO',
            'utr_number' => $code[0]->code,
            'description' => "Wallet Recharge From Redeem Code"
        );
        Transactions::create($data);
        Seller::where('id', Session()->get('MySeller')->id)->increment('balance', $data['amount']);
        $data = [
            'seller_id' => Session()->get('MySeller')->id,
            'code_id' => $code[0]->id,
            'value' => $code[0]->value,
            'redeemed' => date('Y-m-d H:i:s')
        ];
        Redeems::create($data);
        Redeem_codes::where('id', $code[0]->id)->decrement('limit', 1);
        return true;
    }

}
