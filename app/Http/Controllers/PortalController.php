<?php

namespace App\Http\Controllers;

use App\Helpers\TrackingHelper;
use App\Imports\AmazonFeedFlatFileImport;
use App\Imports\AmazonReportFileImport;
use App\Libraries\AmazonDirect;
use App\Libraries\AmazonSWA;
use App\Libraries\Ekart;
use App\Libraries\Smartr;
use App\Libraries\Gati;
use App\Libraries\Maruti;
use App\Libraries\MarutiEcom;
use App\Libraries\Bombax;
use App\Libraries\BlueDart;
use App\Libraries\Xindus;
use App\Models\Aboutus;
use App\Models\Admin;
use App\Models\Admin_rights;
use App\Models\BillReceipt;
use App\Models\Associate_Information;
use App\Models\Blogs;
use App\Models\BrandedTracking;
use App\Models\Brands;
use App\Models\Career;
use App\Models\CareerExpect;
use App\Models\Category;
use App\Models\Channel_partners;
use App\Models\ChildCategory;
use App\Models\Configuration;
use App\Models\Countries;
use App\Models\Courier;
use App\Models\FooterSub;
use App\Models\Coverage;
use App\Models\EarlyCod;
use App\Models\Faq;
use App\Models\Features;
use App\Models\Generated_awb;
use App\Models\Glossary;
use App\Models\Logistics;
use App\Models\Master;
use App\Models\Ndrattemps;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Recharge_request;
use App\Models\Seller;
use App\Models\Admin_employee;
use App\Models\Basic_informations;
use App\Models\COD_transactions;
use App\Models\SKU;
use App\Models\Slider;
use App\Models\SmartrAwbs;
use App\Models\GatiAwbs;
use App\Models\Socials;
use App\Models\Stats;
use App\Models\Steps;
use App\Models\SubCategory;
use App\Models\Support;
use App\Models\SupportChild;
use App\Models\SupportSub;
use App\Models\Testimonial;
use App\Models\Transactions;
use App\Models\WebConfig;
use App\Models\WebContactUs;
use App\Models\WebSubscribe;
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
use App\Models\Why_choose;
use App\Models\XbeesAwbnumber;
use App\Models\EkartAwbNumbers;
use App\Models\ZoneMapping;
use App\Models\SettlementWeightReconciliation;
use App\Notifications\DisputeNotification;
use App\Models\CountryChanel;
use App\Models\FooterCategory;
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

class PortalController extends Controller
{
    protected $info, $utilities,$easyEcomStatus;
    public function __construct()
    {
        $this->utilities = new Utilities();
        $this->info['config'] = Configuration::find(1);
        $this->info['stats'] = Stats::get();
        $this->info['brand'] = Career::get();
    }

    function index()
    {
        $data = $this->info;
        return view('portal.home', $data);
    }

    function contactUs()
    {
        $data = $this->info;
        return view('portal.contact-us', $data);
    }

    function emailSubmit(Request $request)
    {
        $data =array(
            "email"=> $request->email,
            "status"=> "y"
        );
        WebSubscribe::create($data);
        return back();
    }

    function tracking()
    {
        $data = $this->info;
        return view('portal.tracking', $data);
    }

    function singleOrderTracking(Request $request)
    {
        $track = Order::where('awb_number', $request->awb_number)->count();
        if ($track > 0) {
            return redirect()->route('portal.track-order-detail', $request->awb_number);
        } else {
            $this->utilities->generate_notification('Error', "AWB Number not Found in our System", 'error');
            return redirect()->back();
        }
    }
    public function trackOrderDetail($awb)
    {
        $data = $this->info;
        //$data['country'] = CountryChanel::where('status', 'y')->get();
        $data['order'] = Order::where('awb_number', $awb)->first();

        // Check if the order exists
        if (empty($data['order'])) {
            // Redirect back if the order is not found
            return back()->withErrors(['error' => 'Order not found']);
        }

        // Check for NDR status
        if ($data['order']['ndr_status'] == 'y') {
            $data['ndrattemps'] = Ndrattemps::where('order_id', $data['order']['id'])
                ->whereNotIn('action_by', ['Twinnship', 'Seller'])
                ->where('ndr_data_type', 'auto')
                ->get();
        }
        $courier = $data['order']->courier_partner;
        TrackingHelper::PerformTracking($data['order']);

        $data['order']->refresh();
        $data['order_tracking'] = OrderTracking::where('awb_number', $awb)->get();
        $data['partner'] = Partners::where('keyword', $courier)->first();
        $seller = Seller::find($data['order']->seller_id);

        // Check for brand tracking
        if ($seller && $seller->brand_tracking_enabled == 1) {
            $data['brand'] = BrandedTracking::where('seller_id', $data['order']->seller_id)
                ->where('status', 'y')
                ->first();
            return view('portal.brand-tracking', $data);
        }
        return view('portal.tracking', $data);
    }



    function pricing()
    {
        $data = $this->info;
        return view('portal.pricing', $data);
    }

    function login()
    {
        $data = $this->info;
        return view('portal.login', $data);
    }

    function register()
    {
        $data = $this->info;
        return view('portal.register', $data);
    }

    function termsOfServices(){
        $data = $this->info;
        $data['term'] = Glossary::where('status', 'y')->first();
        return view('portal.term',$data);
    }

    function privacyPolicy(){
        $data = $this->info;
        $data['privacy'] = Glossary::where('status', 'y')->first();
        return view('portal.privacy',$data);
    }

    function cancellation(){
        $data = $this->info;
        $data['term'] = Glossary::where('status', 'y')->first();
        return view('portal.cancel',$data);
    }

    function disclaimer()
    {
        $data = $this->info;
        $data['term'] = Glossary::where('status', 'y')->first();
        return view('portal.disclaimer',$data);
    }

    function submitContactUs(Request $request){
        $pattern = '/[^\w\s\d]/';
        if(preg_match($pattern,$request->firstName) || preg_match($pattern,$request->mobile) || preg_match($pattern,$request->channelData))
        {
            Session()->put(['notification' => [ 'title' => 'Failed','message' => 'Error in provided data','type' => 'error']]);
            return back();
        }
        $data = [
            'first_name' => $request->first_name ?? "",
            'email' => $request->email ?? "",
            'type' => $request->type ?? "",
            'mobile' => $request->mobile ?? "",
            'company_name' => $request->company_name ?? "",
            'monthly_shipment' => $request->monthly_shipment ?? "",
            'website' => $request->website ?? "",
            'orderid' => $request->orderid ?? "",
            'purchasedate' => date('Y-m-d H:i:s') ?? "",
            'amount' => $request->amount ?? "",
            'channel_name' => $request->channelData ?? "",
            'message' => $request->message ?? "",
            'inserted' => date('Y-m-d H:i:s') ?? "",
            'inserted_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ""
        ];
        WebContactUs::create($data);
        Session()->put(['notification' => [ 'title' => 'Success','message' => 'Your Message Sent Successfully','type' => 'success']]);
        return back();
    }
}
