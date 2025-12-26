<?php

namespace App\Http\Controllers;

use App\Libraries\Gati;
use App\Models\Channels;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Warehouses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Libraries\Logger;
use Exception;

class ChannelsController extends Controller
{
    protected $amazonPartners,$amazonStatus,$insertOrders,$insertProducts;
    function __construct(){
        $this->insertOrders=[];
        $this->insertProducts=[];
        $this->amazonStatus=[
            "pending" => 2,
            "shipped" => 2,
            "manifested" => 2,
            "pickup_scheduled" => 18,
            "picked_up" => 19,
            "cancelled" => 6,
            "in_transit" => 2,
            "out_for_delivery" => 20,
            "rto_initated" => 17,
            "rto_delivered" => 9,
            "delivered" => 3,
            "ndr" => 16,
            "lost" => 2,
            "damaged" => 2
        ];
        $this->amazonPartners=[
            'amazon_swa' => 'Other',
            'amazon_swa_10kg' => 'Other',
            'amazon_swa_1kg' => 'Other',
            'amazon_swa_3kg' => 'Other',
            'amazon_swa_5kg' => 'Other',
            'bluedart' => 'BlueDart',
            'delhivery_surface' => 'Delhivery',
            'delhivery_surface_10kg' => 'Delhivery',
            'delhivery_surface_20kg' => 'Delhivery',
            'delhivery_surface_2kg' => 'Delhivery',
            'delhivery_surface_5kg' => 'Delhivery',
            'dtdc_10kg' => 'DTDC',
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
            'bluedart' => 'BlueDart',
            'shree_maruti' => 'Shree Maruti Courier',
            'xpressbees_sfc' => 'Xpressbees',
            'xpressbees_surface' => 'Xpressbees',
            'xpressbees_surface_10kg' => 'Xpressbees',
            'xpressbees_surface_1kg' => 'Xpressbees',
            'xpressbees_surface_3kg' => 'Xpressbees',
            'xpressbees_surface_5kg' => 'Xpressbees'
        ];
    }
    function _createCompanyAmazon($channel,$sellerData){
        $data=[
            'phone' => $sellerData->mobile,
            'company_name' => $sellerData->company_name,
            'email' => "twin_".$sellerData->email,
            'client_id' => "WF7UYeVe22GbPQ9vzksu",
            'branding_user_id' => $sellerData->email,
            'password' => "Twin@123#",
            'companyLevelTaxRate' => 0,
            'shipping_address' => [
                'address_line_1' =>"544, Ground Floor, Sector 29",
                'address_line_2' => "Faridabad",
                'state_code' => "HR",
                'pin_code' => "121008",
                'country' => "India"
            ],
            'billing_address' => [
                'address_line_1' =>"544, Ground Floor, Sector 29",
                'address_line_2' => "Faridabad",
                'state_code' => "HR",
                'pin_code' => "121008",
                'country' => "India"
            ]
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://api.easyecom.io/company/V2/create', $data);
        $responseData = $response->json();
        $this->_addLog($responseData,"CreateCompany");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'CreateCompany',
            'data' => $responseData
        ]);
        if(isset($responseData['data']['token']) && isset($responseData['data']['companyId'])){
            Channels::where('id',$channel)->update(['company_token' => $responseData['data']['token'],'company_id' => $responseData['data']['companyId']]);
            return true;
        }
        else
            return false;
    }
    function _getApiTokenAmazon($email,$password,$channelId){
        $returnData=[
            'status' => false,
            'api_token' => ''
        ];
        $data=[
            'email' => "twin_".$email,
            'password' => $password
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://api.easyecom.io/getApiToken',$data);
        $responseData=$response->json();
        $this->_addLog($responseData,"getAPIToken");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'getAPIToken',
            'data' => $responseData
        ]);
        if(isset($responseData['data']['api_token'])){
            if($responseData['data']['api_token']!=""){
                Channels::where('id',$channelId)->update(['amazon_token' => $responseData['data']['api_token']]);
                $returnData['status']=true;
                $returnData['api_token']=$responseData['data']['api_token'];
            }
        }
        return $returnData;
    }
    function _addMPCredentialsAmazon($token,$mwsToken,$sellerId){
        $returnData=false;
        $data=[
            'm_id' => 8,
            'seller_id' => $sellerId,
            'seller_user_id' => $mwsToken,
            'cp_auto_create' => 1
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post("https://api.easyecom.io/Credentials/addMPCredentials?api_token=$token",$data);
        $responseData=$response->json();
        $this->_addLog($responseData,"AddMPCredentials");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'AddMPCredentials',
            'data' => $responseData
        ]);
        if(isset($responseData['code'])){
            if($responseData['code']==200){
                $returnData=true;
            }
        }
        return $returnData;
    }
    function _addAmazonCarrier($token,$username,$password,$channelID){
        $returnData= false;
        $data=[
            'carrier_id' => 14199,
            'username' => $username,
            'password' => $password
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post("https://api.easyecom.io/Credentials/addCarrierCredentials?api_token=$token",$data);
        $responseData=$response->json();
        $this->_addLog($responseData,"addCarrier");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'addCarrier',
            'data' => $responseData
        ]);
        if(isset($responseData['code'])){
            if($responseData['code']==200){
                Channels::where('id',$channelID)->update(['company_carrier_id' => $responseData['data']['companyCarrierId']]);
                $returnData=true;
            }
        }
        return $returnData;
    }

    function _fetchAmazonOrders($channel,$sellerData,$from="",$to=""){
        $lastSync = ($from == "" ? date('Y-m-d H:i:s',strtotime($channel->last_sync." -45 minutes")) : $from );
        $toDate = ($to == "" ? date('Y-m-d H:i:s') : $to);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get("https://api.easyecom.io/orders/V2/getAllOrders?api_token=$channel->amazon_token&limit=250&status_id=1,2&updated_after=$lastSync&updated_before=".$toDate);
        //$this->_addLog([],"Amazon Fetch -- https://api.easyecom.io/orders/V2/getAllOrders?api_token=$channel->amazon_token&limit=250&status_id=1,2&updated_after=$lastSync&updated_before=".$toDate." ----");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => "Amazon Fetch for Seller {$channel->seller_id} -- https://api.easyecom.io/orders/V2/getAllOrders?api_token=$channel->amazon_token&limit=250&status_id=1,2&updated_after=$lastSync&updated_before=".$toDate." ----",
            'data' => []
        ]);
        $responseData=$response->json();
        if(isset($responseData['data']['orders'])){
            $this->_storeOrders($responseData['data']['orders'],$sellerData,$channel);
            //$this->_addLog([],"Total Orders Fetched : ".count($responseData['data']['orders']));
            Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
                'title' => "Total Orders Fetched for Seller {$channel->seller_id} : ".count($responseData['data']['orders']),
                'data' => []
            ]);
        }
        if(!empty($responseData['data']['nextUrl'])){
            $this->_FetchMoreOrdersAmazon($responseData['data']['nextUrl'],$sellerData,$channel);
        }
    }
    function _FetchMoreOrdersAmazon($link,$sellerData,$channel){
        $response= Http::get("https://api.easyecom.io{$link}");
        $responseData = $response->json();
        //dd($responseData);
        if(isset($responseData['data']['orders'])){
            $this->_storeOrders($responseData['data']['orders'],$sellerData,$channel);
            $this->_addLog([],"Total Orders Fetched : ".count($responseData['data']['orders']));
            Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
                'title' => "Total Orders Fetched : ".count($responseData['data']['orders']),
                'data' => []
            ]);
        }
        if(!empty($responseData['data']['nextUrl'])){
            $this->_FetchMoreOrdersAmazon($responseData['data']['nextUrl'],$sellerData,$channel);
        }
    }

    function _storeOrders($orders,$seller,$channel){
        $warehouse = Warehouses::where('seller_id',$seller->id)->where('default','y')->first();
        if(empty($seller)){
            return false;
        }
        $lastSync = date('Y-m-d H:i:s',strtotime('-7 days'));
        //DB::enableQueryLog();
        //$allChannelIds = Order::where('seller_id',$seller->id)->where('channel','amazon')->where('inserted','>',$lastSync)->pluck('channel_id')->toArray();
        //dd(DB::getQueryLog());
        foreach ($orders as $o){
            //echo $o['invoice_id']."<br>";
            //if(!in_array($o['invoice_id'],$allChannelIds)){
                if($this->_checkAndStoreEasyEcomOrder($o,$seller->id,$channel->id,$warehouse)){
                    $lastSync = $o['last_update_date'];
                }
            //}
        }
        Channels::where('id',$channel->id)->update(['last_sync' => $lastSync]);
        return true;
    }

    function _verifyAmazonOrders($channel,$order){
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get("https://api.easyecom.io/orders/V2/getAllOrders?api_token=$channel->amazon_token&invoice_id=$order->channel_id");
        $responseData=$response->json();
        if(isset($responseData['data']['orders'])){
            $order = $responseData['data']['orders'][0];
            if(strtolower($order['order_status'])!="open"){
                Order::where('id',$order->id)->update(['status' => 'cancelled']);
            }
        }
    }
    function _checkAndStoreEasyEcomOrder($order,$seller_id,$sellerChannelID,$warehouse,$allChannelIds=[]){
        echo $order['reference_code']."<bR>";
        $sellerDetails = Seller::find($seller_id);
        if(isset($order['Package Weight']) && is_numeric($order['Package Weight'])) {
            $weight = ($order['Package Weight'] > 0 ? round($order['Package Weight'], 2) : 100);
        } else {
            $weight = 100;
        }
        if(isset($order['Package Height']) && is_numeric($order['Package Height'])) {
            $height = ($order['Package Height'] > 0 ? round($order['Package Height'], 2) : 10);
        } else {
            $height = 10;
        }
        if(isset($order['Package Length']) && is_numeric($order['Package Length'])) {
            $length = ($order['Package Length'] > 0 ? round($order['Package Length'], 2) : 10);
        } else {
            $length = 10;
        }
        if(isset($order['Package Width']) && is_numeric($order['Package Width'])) {
            $breadth = ($order['Package Width'] > 0 ? round($order['Package Width'], 2) : 10);
        } else {
            $breadth = 10;
        }

        $channel = Channels::where('id', $sellerChannelID)->first();
        $data = array(
            'order_number' => $order['reference_code'] ?? 0,
            'customer_order_number' => $order['reference_code'] ?? 0,
            'channel_id' => $order['invoice_id'],
            'amazon_order_id' => $order['order_id'],
            'o_type' => "forward",
            'seller_id' => $seller_id,
            'seller_channel_id' => $sellerChannelID,
            'seller_channel_name' => $channel->channel_name,
            'order_type' => strtolower($order['payment_mode']) == "prepaid" ? "prepaid" : "cod",
            'b_customer_name' => $order['customer_name'] ?? "",
            'b_address_line1' => $order['address_line_1'] ?? "",
            'b_address_line2' => $order['address_line_2'] ?? "",
            'b_country' => $order['country'] ?? "",
            'b_state' => $order['state'] ?? "",
            'b_city' => $order['city'] ?? "",
            'b_pincode' => $order['pin_code'] ?? "",
            'b_contact' => $order['contact_num'] ?? "",
            'b_contact_code' => "91",
            's_customer_name' => $order['customer_name'] ?? "",
            's_address_line1' => $order['address_line_1'] ?? "",
            's_address_line2' => $order['address_line_2'] ?? "",
            's_country' => $order['country'] ?? "",
            's_state' => $order['state'] ?? "",
            's_city' => $order['city'] ?? "",
            's_pincode' => $order['pin_code'] ?? "",
            's_contact' => $order['contact_num'] ?? "",
            's_contact_code' => "91",
            'p_warehouse_name' => $warehouse->warehouse_name ?? "",
            'p_customer_name' => $warehouse->contact_name ?? "",
            'p_address_line1' => $warehouse->address_line1 ?? "",
            'p_address_line2' => $warehouse->address_line2 ?? "",
            'p_country' => $warehouse->country ?? "",
            'p_state' => $warehouse->state ?? "",
            'p_city' => $warehouse->city ?? "",
            'warehouse_id' => $warehouse->id ?? "",
            'p_pincode' => $warehouse->pincode ?? "",
            'p_contact' => $warehouse->contact_number ?? "",
            'p_contact_code' => $warehouse->code ?? "",
            'weight' => $weight,
            'height' => $height,
            'length' => $length,
            'breadth' => $breadth,
            'vol_weight' => (($height * $length * $breadth) / 5),
            'shipping_charges' => $order['total_shipping_charge'],
            'cod_charges' => 0,
            'discount' => 0,
            'invoice_amount' => $order['total_amount'],
            'channel' => 'amazon',
            'inserted' => $order['order_date'],
            'inserted_by' => $seller_id,
            'imported' => date('Y-m-d H:i:s')
        );
        //$resp = Order::where('channel_id',$data['channel_id'])->where('seller_id',$seller_id)->where('channel','amazon')->first();
        //if(empty($resp)){
            try{
                $orderID = Order::create($data)->id;
            }
            catch(Exception $e){
                return false;
            }
            $pname = [];
            $psku = [];
            $productQty = 0;
            foreach ($order['suborders'] as $p) {
                $product = array(
                    'order_id' => $orderID,
                    'product_sku' => $sellerDetails->product_name_as_sku == "y" ? $p['productName'] : $p['sku'],
                    'product_name' => $p['productName'],
                    'product_unitprice' => $p['mrp'],
                    'product_qty' => $p['item_quantity'],
                    'total_amount' => $p['mrp'] * $p['item_quantity'],
                );
                Product::create($product);
                $pname[] = $p['productName'];
                $psku[] = $p['sku'];
                $productQty+=intval($product['product_qty']);
            }
            Order::where('id', $orderID)->update(array('product_name' => implode(',', $pname),'product_qty' => $productQty,'product_sku' => implode(',', $psku)));
        //}
        return true;
    }
    function _fulfillAmazonOrders($order,$channel,$awb,$partner){
        //file_put_contents("courier_name.txt",$partner);
        $partnerName = $this->amazonPartners[$partner] ?? "Others";
        //file_put_contents("courier_name.txt",$partnerName);
        if($this->_assignAmazonShipmentDetail($order,$channel,$awb,$partnerName)){
            $this->_confirmAmazonOrders($order,$channel);
            $this->_pushStatusToAmazon($order,'pickup_scheduled',$awb);
            $this->_pushStatusToAmazon($order,'picked_up',$awb);
            $this->_pushStatusToAmazon($order,'shipped',$awb);
        }
        return true;
    }
    function _assignAmazonShipmentDetail($order,$channel,$awb,$partner){
        $partnerName = $partner;
        $data=[
            'invoiceId' => $order->channel_id,
            'courier' => $partnerName,
            'awbNum' => $awb,
            'companyCarrierId' => $channel->company_carrier_id
        ];
        $this->_addLog($data,"assignShipmentDetail RequestData");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'assignShipmentDetail RequestData',
            'data' => $data
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post("https://api.easyecom.io/Carrier/assignAWB?api_token=$channel->amazon_token",$data);
        $responseData = $response->json();
        $this->_addLog($responseData,"assignShipmentDetail");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => 'assignShipmentDetail',
            'data' => $responseData
        ]);
        $return = false;
        if(isset($responseData['code'])){
            if($responseData['code']==200)
                $return = true;
        }
        return $return;
    }
    function _confirmAmazonOrders($order,$channel){
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get("https://api.easyecom.io/orders/confirm_order?api_token=$channel->amazon_token&order_id=$order->amazon_order_id");
        $responseData = $response->json();
        $this->_addLog($responseData,"confirmOrder -> https://api.easyecom.io/orders/confirm_order?api_token=$channel->amazon_token&order_id=$order->amazon_order_id  == ");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => "confirmOrder -> https://api.easyecom.io/orders/confirm_order?api_token=$channel->amazon_token&order_id=$order->amazon_order_id  == ",
            'data' => $responseData
        ]);
        $return = false;
        if(isset($responseData['status'])){
            if($responseData['status']==200)
                $return = true;
        }
        return $return;
    }
    function _pushStatusToAmazon($orderData,$status,$awb=""){
        $awbNumber = $awb == "" ? $orderData->awb_number : $awb;
        $order=Order::find($orderData->id);
        // $channel = Channels::where('seller_id',$orderData->seller_id)->where('channel','amazon')->first();
        $channel = Channels::where('seller_id',$order->seller_id)->where('id',$order->seller_channel_id)->where('channel','amazon')->first();
        if(empty($order)){
            return false;
        }
        if(empty($channel)){
            return false;
        }
        if($status == 'delivered' && $order->rto_status == 'y')
            $status = 'rto_delivered';
        $data=[
            'current_shipment_status_id' => $this->amazonStatus[$status] ?? 2,
            'awb' => $awbNumber ?? "",
            'estimated_delivery_date' => $order->expected_delivery_date ?? date('Y-m-d',strtotime('+3 days')),
            'history_scans' => []
        ];
        $history = OrderTracking::where('awb_number',$awbNumber)->get();
        foreach ($history as $h){
            $data['history_scans'][]=[
                'status' => $h['status'],
                'time' => $h['updated_date'],
                'location' => $h['location']
            ];
        }
        if($status == 'delivered'){
            $data['delivery_date'] =$order->delivered_date;
        }
        $this->_addLog($data,"Update Tracking Status Request Payload ---");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => "Update Tracking Status Request Payload ---",
            'data' => $data
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post("https://api.easyecom.io/Carrier/V2/updateTrackingStatus?api_token=$channel->amazon_token",$data);
        $responseData = $response->json();
        $this->_addLog($responseData,"UpdateTrackingDetails -- Order Id = $order->amazon_order_id ---");
        Logger::write('logs/oms/amazon/amazon-'.date('Y-m-d').'.text', [
            'title' => "UpdateTrackingDetails -- Order Id = $order->amazon_order_id ---",
            'data' => $responseData
        ]);
        return true;
    }
    function _addLog($response,$text){
        // $date=date('Y-m-d');
        // if(!is_dir('logs/channels')) {
        //     @mkdir('logs/channels');
        // } else {
        //     if(!is_dir('logs/channels/amazon')) {
        //         @mkdir('logs/channels/amazon');
        //     }
        // }
        // $myfile = fopen("logs/channels/amazon/amazon-{$date}.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, "\n".date('Y-m-d H:i:s')."----". $text." ------- ".json_encode($response));
        // fclose($myfile);
    }
}
