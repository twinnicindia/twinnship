<?php

namespace App\Http\Controllers;
use App\Libraries\Logger;
use App\Mail\MyCustomMail;
use App\Models\Basic_informations;
use App\Models\Configuration;
use App\Models\DownloadReport;
use App\Models\InvalidContact;
use App\Models\LabelCustomization;
use App\Models\Manifest;
use App\Models\Order;
use App\Models\OrderSMSLogs;
use App\Models\OrderWhatsAppMessageLogs;
use App\Models\Partners;
use App\Models\Product;
use App\Models\Seller;
use DateTime;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Exception;

class Utilities extends Controller
{
    public $partnerNames;

    function __construct()
    {
        $this->partnerNames = [
            'amazon_swa' => 'AmazonSwa',
            'amazon_swa_10kg' => 'AmazonSwa',
            'amazon_swa_1kg' => 'AmazonSwa',
            'amazon_swa_3kg' => 'AmazonSwa',
            'amazon_swa_5kg' => 'AmazonSwa',
            'bluedart' => 'BlueDart',
            'bluedart_surface' => 'BlueDart',
            'delhivery_surface' => 'Delhivery',
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
            'shadow_fax' => 'Shadowfax',
            'smartr' => 'Smartrlogistics',
            'udaan' => 'Udaan',
            'udaan_10kg' => 'Udaan',
            'udaan_1kg' => 'Udaan',
            'udaan_2kg' => 'Udaan',
            'udaan_3kg' => 'Udaan',
            'wow_express' => 'WowExpress',
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
    function generate_notification($title = "", $message = "", $type = "")
    {
        $notification = array(
            'notification' => array(
                'type' => $type,
                'title' => $title,
                'message' => $message,
            ),
        );
        Session($notification);
    }
    function send_email($email, $name, $title, $message,$subject="Twinnship",$attachment=[])
    {
        $data = array(
            'name' => $title,
            'body' => $message
        );
        try{
            Mail::send('seller.mail', $data, function ($message) use ($email, $name,$subject,$attachment) {
                $message->to($email, $name)
                    ->subject($subject);
                if(!empty($attachment))
                {
                    foreach ($attachment as $a){
                        $message->attach($a,[
                            'as' => basename($a), // Using the base name of the file
                            'mime' => mime_content_type($a)]);
                    }
                }
            });
        }
        catch(Exception $e){
            return $e->getMessage();
        }

        return true;
    }

    function sendCustomMail($data){
        Mail::to($data['email'])->send(new MyCustomMail($data['attachment'],$data['data']));
    }

    function send_sms($order)
    {
        try {
           $user = "Twinnship";
           $password = "PASSWORD";
           $senderid = "TWINN";
           $seller = Seller::find($order->seller_id);

           $apiKey = "APIKEY";
           $senderId = "TWINN";

           if(empty($seller))
                return false;
           if($seller->sms_service == 'n')
                return false;
           $order = Order::find($order->id);
           if(empty($order))
                return true;
           if (strlen($order->s_contact) > 10 || !preg_match('/^[0-9]{10}$/', $order->s_contact))
           {
               $data=array(
                   'seller_id' => $order->seller_id,
                   'awb_number' => $order->awb_number,
                   'contact' => $order->s_contact,
                   'date' => date('Y-m-d H:i:s'),
                   'status' => 'y',
               );
               InvalidContact::create($data);
               return false;
           }
           else
           {
               $mobile_number = substr($order->s_contact, -10);
               $channel = strtoupper($order->channel);
               $PartnerName = Partners::getPartnerKeywordList();
               //$base_url = "http://sms.platinumsms.co.in/sendsms.jsp";
               $base_url = "http://msg.mtalkz.com/V2/http-api-post.php";
               $sentFlag = false;
               if(in_array($order->status,['manifested','picked_up','in_transit','out_for_delivery','delivered'])){
                   $sentFlag = OrderSMSLogs::CheckAndStoreSMS($order);
               }
               if(!$sentFlag)
                   return false;
               //dd($order);
               $payload = [
                   "apikey" => $apiKey,
                   "senderid" => $senderId,
                   "number" => $mobile_number,
                   "message" => "",
                   "format" => "json"
               ];
               switch($order->status){
                   case 'manifested' :
                       //$tempid = "1207162105669662680";
                       $tempid = "1207166322504304237";
                       $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                       $product = strlen($order->product_name) > 15 ? substr($order->product_name, 0, 15) : $order->product_name;
                       $message = "Order Packed: Hi {$order->s_customer_name}, your : {$order->customer_order_number} order containing {$product} is packed. ".env('appTitle');
                       // $message = "Hi {$order->s_customer_name},  your : {$order->customer_order_number}  order containing {$order->produt_name}  is packed. \nTwinnship";
                       // $message = "Hello $order->b_customer_name,  Your order no. $order->customer_order_number is ready for dispatch %26 will be shipped shortly via $courier AWB No: $order->awb_number%0aTwinnship";
                       //$response = Http::get("$base_url?user=$user&password=$password&senderid=$senderid&tempid=$tempid&mobiles=$mobile_number&sms={$message}");
                       $payload['message'] = $message;
                       $response = Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
                       @$this->_addLog("Message for AWB : {$order->awb_number}\nSeller : {$order->seller_id}\nMessage : {$message}\nStatus : {$order->status}","SMS Request");
                       @$this->_addLog(json_encode($response)," SMS Response");
                       break;
                   case 'picked_up' :
                       //$tempid = "1207162027879262460";
                       $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                       //$tempid = "1207166322492900787";
                       $message = "Order Picked Up: Your order :{$order->customer_order_number} from :{$courier} has been shipped. Track here : https://www.Twinnship.in/track-order/{$order->awb_number} ".env('appTitle');
                       // $message = "Your order :{$order->customer_order_number} from :{$courier} has been shipped. Track here :https://www.Twinnship.in/track-order/{$order->awb_number}\nTwinnship";
                       // $message = "Order:$order->customer_order_number.. from TwinnshipSE of your product,AWB:$order->awb_number of Rs.$order->invoice_amount has been picked up. For query please call:9399262217.%0aTwinnship";
                       //$response = Http::get("$base_url?user=$user&password=$password&senderid=$senderid&tempid=$tempid&mobiles=$mobile_number&sms={$message}");
                       $payload['message'] = $message;
                       $response = Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
                       @$this->_addLog("Message for AWB : {$order->awb_number}\nSeller : {$order->seller_id}\nMessage : {$message}\nStatus : {$order->status}","SMS Request");
                       @$this->_addLog(json_encode($response)," SMS Response");
                       break;
                   case 'in_transit' :
                       //$tempid = "1207162105675788029";
                       $tempid = "1207166322508749248";
                       $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                       $message = "Order In Transit: Your order :{$order->customer_order_number} from :{$courier} is in transit. Track here : https://www.Twinnship.in/track-order/{$order->awb_number} Twinnship";
                       //$message = "Your order :{$order->customer_order_number} from :{$courier} is in transit. Track here :https://www.Twinnship.in/track-order/{$order->awb_number}\nTwinnship";
                       //$message = "Hello $order->b_customer_name,  Your order no. $order->customer_order_number is in transit via $courier AWB No: $order->awb_number %26 will be delivered to you shortly.  To track your package please click this link -  Twinnship.in/order_track%0aTwinnship";
                       //$response = Http::get("$base_url?user=$user&password=$password&senderid=$senderid&tempid=$tempid&mobiles=$mobile_number&sms={$message}");
                       $payload['message'] = $message;
                       $response = Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
                       @$this->_addLog("Message for AWB : {$order->awb_number}\nSeller : {$order->seller_id}\nMessage : {$message}\nStatus : {$order->status}","SMS Request");
                       @$this->_addLog(json_encode($response)," SMS Response");
                       break;
                   case 'out_for_delivery' :
                       $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                       //$tempid = "1207162027865170807";
                       $tempid = "1207166322487889545";
                       $message = "Out for Delivery: Your order :{$order->customer_order_number} from :{$courier} is out for delivery. Package will be delivered today. Track here : https://www.Twinnship.in/track-order/{$order->awb_number} Twinnship";
                       // $message = "Your order :{$order->customer_order_number} from :{$courier} is out for delivery. Package will be delivered today. Track here :https://www.Twinnship.in/track-order/{$order->awb_number}\nTwinnship";
                       // $message = "Order:$order->customer_order_number.. via TwinnshipSE of your product,AWB:$order->awb_number of Rs.$order->invoice_amount will reach today.For query please call:9399262217.%0aTwinnship";
                       //$response = Http::get("$base_url?user=$user&password=$password&senderid=$senderid&tempid=$tempid&mobiles=$mobile_number&sms={$message}");
                       $payload['message'] = $message;
                       $response = Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
                       @$this->_addLog("Message for AWB : {$order->awb_number}\nSeller : {$order->seller_id}\nMessage : {$message}\nStatus : {$order->status}","SMS Request");
                       @$this->_addLog(json_encode($response)," SMS Response");
                       break;
                   case 'delivered' :
                       $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                       //$tempid = "1207162027872669125";
                       //$tempid = "1207166218016329332";
                       $tempid = "1207166322499011174";
                       $delivered_date = date('d-m-Y',strtotime($order->delivered_date));
                       //s$message = "Your order : {$order->customer_order_number}  from :{$courier}  is delivered. Twinnship";
                       $message = "Order Delivered: Your order : {$order->customer_order_number} from :{$courier}  is delivered. Twinnship";
                       //$message = "We have delivered your $channel order:$order->customer_order_number on $delivered_date to $order->b_customer_name.%0aTwinnship";
                       //$response = Http::get("$base_url?user=$user&password=$password&senderid=$senderid&tempid=$tempid&mobiles=$mobile_number&sms={$message}");
                       $payload['message'] = $message;
                       $response = Http::post("http://msg.mtalkz.com/V2/http-api-post.php",$payload)->json();
                       @$this->_addLog("Message for AWB : {$order->awb_number}\nSeller : {$order->seller_id}\nMessage : {$message}\nStatus : {$order->status}","SMS Request");
                       @$this->_addLog(json_encode($response)," SMS Response for {$mobile_number} : ");
                       break;
               }
           }
        } catch(Exception $e) {
            @$this->_addLog($e->getMessage(),"SMS Response for {$mobile_number} : ");
        }
        return true;
    }

    function send_whatsapp_message($order){
        try {
            $seller = Seller::find($order->seller_id);
            if(empty($seller))
                return false;
            if($seller->whatsapp_service == 0)
                return false;
            $order = Order::find($order->id);
            if(empty($order))
                return true;
            $PartnerName = Partners::getPartnerKeywordList();
            $sentFlag = false;
            if(in_array($order->status,['manifested','picked_up','in_transit','out_for_delivery','delivered'])){
                $sentFlag = OrderWhatsAppMessageLogs::CheckAndStoreWhatsAppMessage($order);
            }
            if(!$sentFlag)
                return false;
            switch($order->status){
                case 'manifested' :
                    $product = strlen($order->product_name) > 15 ? substr($order->product_name, 0, 15) : $order->product_name;
                    $message = "Order Packed: Hi {$order->s_customer_name},  your : {$order->customer_order_number}  order containing {$order->product_name}  is packed. Twinnship";
                    $parameter = [
                        "0" => $order->s_customer_name,
                        "1" => $order->customer_order_number,
                        "2" => $product
                    ];
                    @$this->sendWhatsAppMessage("order_packed_new", $order->s_contact, $parameter,$order->awb_number);
                    break;
                case 'picked_up' :
                    $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                    $message = "Order Picked Up: Your order :{$order->customer_order_number} from :{$courier} has been shipped. Track here : https://www.Twinnship.in/track-order/{$order->awb_number}%0ATwinnship";
                    $parameter = [
                        "0" => $order->customer_order_number,
                        "1" => $courier,
                        "2" => $order->awb_number
                    ];
                    @$this->sendWhatsAppMessage("order_picked_upnew", $order->s_contact, $parameter,$order->awb_number);
                    break;
                case 'in_transit' :
                    $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                    $message = "Order In Transit: Your order :{$order->customer_order_number} from :{$courier} is in transit. Track here : https://www.Twinnship.in/track-order/{$order->awb_number}%0ATwinnship";
                    $parameter = [
                        "0" => $order->customer_order_number,
                        "1" => $courier,
                        "2" => $order->awb_number
                    ];
                    @$this->sendWhatsAppMessage("order_in_transitnew", $order->s_contact, $parameter,$order->awb_number);
                    break;
                case 'out_for_delivery' :
                    $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                    $message = "Out for Delivery: Your order :{$order->customer_order_number} from :{$courier} is out for delivery. Package will be delivered today. Track here : https://www.Twinnship.in/track-order/{$order->awb_number}%0ATwinnship";
                    $parameter = [
                        "0" => $order->customer_order_number,
                        "1" => $courier,
                        "2" => $order->awb_number
                    ];
                    @$this->sendWhatsAppMessage("out_for_deliverynew", $order->s_contact, $parameter,$order->awb_number);
                    break;
                case 'delivered' :
                    $courier = $this->partnerNames[$order->courier_partner] ?? $PartnerName[$order->courier_partner];
                    $message = "Order Delivered: Your order : {$order->customer_order_number} from :{$courier}  is delivered. Twinnship";
                    $parameter = [
                        "0" => $order->customer_order_number,
                        "1" => $courier,
                    ];
                    @$this->sendWhatsAppMessage("order_delivered", $order->s_contact, $parameter,$order->awb_number);
                    break;
            }
        } catch(Exception $e) {
            //
        }
        return true;
    }

    function _addLog($response, $text)
    {
        try{
//            if(trim($text) == "SMS Response"){
//                if($response["message"] == "You have insufficient credit"){
//                    $message = "You have insufficient credit for SMS";
//                    $response = self::send_email("ajay.k@Twinnship.in", "Twinnship Corporation", "Insufficient SMS", $message);
//                }
//            }
            $date = date('Y-m-d');
            $myfile = fopen("logs/sms-log-{$date}.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . date('Y-m-d H:i:s') .$text . " ------- " . $response);
            fclose($myfile);
        }catch(Exception $e){}
    }
    //download label of shipped data (manifest)
    function LablePDF($id)
    {
        $order = Order::find($id);
        $data['manifest_order']=DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->select('manifest_order.*', 'orders.*')->where('order_id', $id)->get();
        $data['config'] = Configuration::find(1);
        $data['seller'] = Seller::find($order->seller_id);
        $data['basic_info'] = Basic_informations::where('seller_id', $order->seller_id)->first();
        $data['manifest'] = Manifest::where('id', $data['manifest_order'][0]->manifest_id)->first();
        $data['PartnerName'] = Partners::getPartnerKeywordList();
//        $data['manifest_order'] = DB::table('manifest_order')->join('orders', 'manifest_order.order_id', '=', 'orders.id')->select('manifest_order.*', 'orders.*')->where('manifest_id', $id)->get();
        $data['product'] = Product::where('order_id', $id)->get();

        // Get label configuration
        $label = LabelCustomization::where('seller_id', $order->seller_id)->first();
        if($label == null) {
            $label = new LabelCustomization();
            // Store label configuration
            $label->seller_id = $order->seller_id;
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
            $label->save();
        }
        $data['label'] = $label;

        if($data['manifest']->courier == 'amazon_swa' || $data['manifest']->courier == 'amazon_swa_1kg' || $data['manifest']->courier == 'amazon_swa_3kg' || $data['manifest']->courier == 'amazon_swa_5kg' || $data['manifest']->courier == 'amazon_swa_10kg'){
            $zipFile = "AmazonLabel.zip";
            $zip = new \ZipArchive();
            if($zip->open($zipFile,\ZipArchive::CREATE) !== true){
                exit('Unable to create File');
            }
            foreach ($data['manifest_order'] as $mo){
                $labelUrl = Order::where('id',$mo->order_id)->first();
                if(!empty($labelUrl))
                    $zip->addFile($labelUrl->amazon_label,basename($labelUrl->amazon_label));
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

    function getNextCodRemitDate($id)
    {
        $sellerData = Seller::where('id', $id)->first();
        $remDays = 7;
        if (!empty($sellerData)){
            $remDays = $sellerData->remmitance_days ?? 7;
            $day = explode(",", $sellerData->remittanceWeekDay) ?? ["Wednesday"];
            $referenceDate = new DateTime(date('Y-m-d'));
            $dateArray = [];

            foreach ($day as $d)
                array_push($dateArray, new DateTime(date('Y-m-d', strtotime("next $d"))));

    //    function isWeekday($date) {
    //        return $date->format('N') < 6; // Monday to Friday are weekdays (1 to 5)
    //    }

            $closestWeekday = null;
            $closestInterval = PHP_INT_MAX;

            foreach ($dateArray as $date) {
    //        if (!isWeekday($date)) {
    //            continue; // Skip non-weekdays
    //        }

                $interval = abs($referenceDate->getTimestamp() - $date->getTimestamp());
                if ($interval < $closestInterval) {
                    $closestInterval = $interval;
                    $closestWeekday = $date;
                }
            }
        }
        $remDays++;
        if ($closestWeekday !== null) {
            $nextRemit = $closestWeekday->format('Y-m-d');
            $nextRemitAmount = Order::where('seller_id', $sellerData->id)
                ->where('order_type', 'cod')
                ->where('rto_status','n')
                ->where('status', 'delivered')
                ->where('cod_remmited', 'n')
                ->whereDate('delivered_date','<',date('Y-m-d',strtotime($nextRemit."- $remDays days")))
                ->sum(DB::raw('IF(collectable_amount > 0, collectable_amount, invoice_amount)'));
            return ['nextRemitDate' => $nextRemit,'nextRemitCod' => $nextRemitAmount ];
        } else {
            $nextRemit = date('Y-m-d',strtotime("next Wednesday"));
            $nextRemitAmount = Order::where('seller_id', $sellerData->id)
                ->where('order_type', 'cod')
                ->where('rto_status','n')
                ->where('status', 'delivered')
                ->where('cod_remmited', 'n')
                ->whereDate('delivered_date','<',date('Y-m-d',strtotime($nextRemit."- $remDays days")))
                ->sum(DB::raw('IF(collectable_amount > 0.0, collectable_amount, invoice_amount)'));
            return ['nextRemitDate' => $nextRemit,'nextRemitCod' => $nextRemitAmount ];
        }
    }

    function sendWhatsAppMessage($templateId,$mobile,$parameter,$awbNumber = null){
        $obj = (object) $parameter;
        $payload = [
            "message" => [
                "channel" => "WABA",
                "content" => [
                    "preview_url" => false,
                    "shorten_url" => false,
                    "type" => "TEMPLATE",
                    "template" => [
                        "templateId" => $templateId,
                        "parameterValues" => $obj,
                    ],
                ],
                "recipient" => [
                    "to" => "91".$mobile,
                    "recipient_type" => "individual"
                ],
                "sender" => [
                    "from" => "919999999999"
                ],
                "preferences" => [
                    "webHookDNId" => "1001"
                ]
            ],
            "metaData" => [
                "version" => "v1.0.9"
            ]
        ];

        Logger::write('logs/whatsApp-message-'.date('Y-m-d').'.text', [
            'title' => "Request For Awb $awbNumber",
            'data' => $payload
        ]);

        $response = Http::withHeaders(['Authentication' => "Bearer 11111=="])->post("https://rcmapi.instaalerts.zone/services/rcm/sendMessage",$payload)->json();
        Logger::write('logs/whatsApp-message-'.date('Y-m-d').'.text', [
            'title' => "Response For Awb $awbNumber",
            'data' => $response
        ]);
    }
    function downloadLabelOrInvoice()
    {
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
        return true;
    }

    //cron job failed notification
    function cronJobFailedStatus($cron_name,$status,$success,$errors,$started_at,$finished_at)
    {
        $Content = "<table border='1'><th>Sr.No</th><th>Cron Name</th><th>Status</th><th>Success</th><th>Error</th><th>Started At</th><th>Fineshed At</th></tr>";
        $cnt=1;
        $Content .= "<tr>
            <td>{$cnt}</td>
            <td>{$cron_name}</td>
            <td style='color: " . ($status == 'success' ? '#28a745' : 'red') . "; font-weight: bold'>" . ($status == 'success' ? 'Success' : 'Failed') . "</td>
            <td>{$success}</td>
            <td>{$errors}</td>
            <td>{$started_at}</td>
            <td>{$finished_at}</td>
        </tr>";
        $Content .= "</table>";
        $data = array('name' => env('appTitle'), 'mailContent' => $Content);
        $email = "dummy@gmail.com";
        $subject = "Cron Job Failed ";
        $this->send_email($email,env('appTitle'),$subject,$Content,$subject);
    }

    static function encrypt($plainText,$key)
    {
        $key = self::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

    /*
    * @param1 : Encrypted String
    * @param2 : Working key provided by CCAvenue
    * @return : Plain String
    */
    static function decrypt($encryptedText,$key)
    {
        $key = self::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = self::hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    static function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString="";
        $count=0;
        while($count<$length)
        {
            $subString =substr($hexString,$count,2);
            $packedString = pack("H*",$subString);
            if ($count==0)
            {
                $binString=$packedString;
            }

            else
            {
                $binString.=$packedString;
            }

            $count+=2;
        }
        return $binString;
    }

    function whatsAppPickupOrderMessage($mobile,$parameter,$awbNumber = null){
        $obj = (object) $parameter;
        $payload = [
            "message" => [
                "channel" => "WABA",
                "content" => [
                    "preview_url" => false,
                    "shorten_url" => true,
                    "type" => "MEDIA_TEMPLATE",
                    "mediaTemplate" => [
                        "templateId" => 'order_picked_up_by_seller_name',
                        "bodyParameterValues" => $obj,
                        "buttons" => [
                            "actions" => [
                                [
                                    "index" => 0,
                                    "payload" => "https://Twinnship.in/order-tracking/".$awbNumber,
                                    "type" => "url"
                                ]
                            ]
                        ]
                    ],
                ],
                "recipient" => [
                    "to" => "91".$mobile,
                    "recipient_type" => "individual"
                ],
                "sender" => [
                    "from" => "919399262217"
                ],
                "preferences" => [
                    "webHookDNId" => "1001"
                ]
            ],
            "metaData" => [
                "version" => "v1.0.9"
            ]
        ];

//        dd(json_encode($payload));

        $response = Http::withHeaders(['Authentication' => "Bearer P0EuQlNH06fXQFndrqYZeA=="])->post("https://rcmapi.instaalerts.zone/services/rcm/sendMessage",$payload)->json();
        Logger::write('logs/whatsApp-message-'.date('Y-m-d').'.text', [
            'title' => "Response For Awb $awbNumber",
            'data' => $response
        ]);
    }

    function testPickupMessage($awb, $mobile)
    {
        $order = Order::where('awb_number',$awb)->first();
        if(!empty($order)) {
            $seller = Seller::find($order->seller_id);
            $parameter = [
                "0" => $order->s_customer_name,
                "1" => $seller->first_name,
                "2" => $this->partnerNames[$order->courier_partner] ?? $order->courier_partner,
                "3" => "3-4",
                "4" => strlen($order->product_name) > 20 ? substr($order->product_name, 0, 20) . "..." : $order->product_name,
                "5" => $order->invoice_amount,
                "6" => ucfirst($order->order_type),
            ];

            $awb_number = $order->awb_number;
            $util = new Utilities();
            $util->whatsAppPickupOrderMessage($mobile, $parameter, $awb_number);
        }
    }
}



