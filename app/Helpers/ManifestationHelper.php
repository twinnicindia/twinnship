<?php

namespace App\Helpers;

use App\Http\Controllers\EcomExpress3kgController;
use App\Http\Controllers\EcomExpressController;
use App\Http\Controllers\ShippingController;
use App\Libraries\Aramex;
use App\Libraries\Ekart;
use App\Libraries\Gati;
use App\Libraries\MarutiEcom;
use App\Libraries\Movin;
use App\Libraries\Shadowfax;
use App\Libraries\Smartr;
use App\Libraries\Xindus;
use App\Models\MPS_AWB_Number;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Transactions;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Exception;

class ManifestationHelper
{
    public static function SendManifestation($orderData){
        $returnValue = false;
        if($orderData->manifest_sent == 'y')
            return true;
        $sellerDetail = Seller::find($orderData->seller_id);
        if(empty($sellerDetail))
            return false;
        switch ($orderData->courier_partner){
            case 'ecom_express':
            case 'ecom_express_rvp':
                $ecom = new EcomExpressController();
                if($ecom->_ManifestEcomExpressOrder($orderData)){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'ecom_express_3kg':
            case 'ecom_express_3kg_rvp':
                $ecom = new EcomExpress3kgController();
                if($ecom->_ManifestEcomExpressOrder($orderData)){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'xpressbees_surface':
                $shipping = new ShippingController();
                if($orderData->seller_order_type == 'NSE'){
                    if($orderData->o_type == 'forward'){
                        if($shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail))
                            $returnValue = true;
                    }
                }
                else{
                    if($orderData->o_type == 'forward'){
                        if($shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'Twinnship','admin@Twinnship.com','$Twinnship$','e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc','kEVUGEG3450nSssVzZQ',$sellerDetail))
                            $returnValue = true;
                    }
                    else if($orderData->o_type == 'reverse'){
                        if($shipping->_SendManiferstationXpressBeesReverse($orderData->id,$orderData->awb_number,'Twinnship','admin@Twinnship.com','$Twinnship$','e0e1b64ce8226efcdcba57e5ff26f9e9aa02db2a6e316227c150caa5bb102cdc','kEVUGEG3450nSssVzZQ',$sellerDetail))
                            $returnValue = true;
                    }
                }
                break;
            case 'dtdc_surface':
            case 'dtdc_2kg':
            case 'dtdc_3kg':
            case 'dtdc_5kg':
            case 'dtdc_6kg':
            case 'dtdc_1kg':
            case 'dtdc_10kg':
                $shipping = new ShippingController();
                if($shipping->_SendManifestationDTDCSurface($orderData)){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'ekart':
            case 'ekart_2kg':
            case 'ekart_1kg':
            case 'ekart_3kg':
            case 'ekart_5kg':
                $shipping = new Ekart();
                if($shipping->shipOrder($orderData)){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'shadow_fax':
                $shadowFax = new Shadowfax();
                $res = $shadowFax->manifestOrder($orderData,$sellerDetail);
                if(!empty($res) && $res['message'] == 'Success'){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
//                    case 'xpressbees_sfc':
//                        $shipping = new ShippingController();
//                        if($orderData->seller_order_type == 'NSE'){
//                            if($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail);
//                        }
//                        else{
//                            if($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'Twinnship SFC','admin@Twinnshipsf.com','$Twinnshipsf$','ca2bb361da9f7211059059fcc171ecb5683d0f462abd4cfb6d4d5cdb3e845578','SsNLds3552adLSIpksnPSKsK',$sellerDetail);
//                            else if($orderData->o_type == 'reverse')
//                                $shipping->_SendManiferstationXpressBeesReverse($orderData->id,$orderData->awb_number,'Twinnship SFC','admin@Twinnshipsf.com','$Twinnshipsf$','ca2bb361da9f7211059059fcc171ecb5683d0f462abd4cfb6d4d5cdb3e845578','SsNLds3552adLSIpksnPSKsK',$sellerDetail);
//                        }
//                        break;
//                    case 'xpressbees_surface_1kg':
//                        $shipping = new ShippingController();
//                        if($orderData->seller_order_type == 'NSE'){
//                            if($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail);
//                        }
//                        else {
//                            if ($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id, $orderData->awb_number, 'TwinnshipSFC1', 'admin@shipesfc1.com', '$shipesfc1$', 'ee864bbf835e2b6902d116cc294c3c569af8ab965a1e414a8fc24fb844a5a2ee', 'JuJDsd3585sdfnuemsjsqISk', $sellerDetail);
//                            else if ($orderData->o_type == 'reverse')
//                                $shipping->_SendManiferstationXpressBeesReverse($orderData->id, $orderData->awb_number, 'TwinnshipSFC1', 'admin@shipesfc1.com', '$shipesfc1$', 'ee864bbf835e2b6902d116cc294c3c569af8ab965a1e414a8fc24fb844a5a2ee', 'JuJDsd3585sdfnuemsjsqISk', $sellerDetail);
//                        }
//                        break;
            case 'xpressbees_sfc':
            case 'xpressbees_surface_1kg':
            case 'xpressbees_surface_3kg':
                $shipping = new ShippingController();
                if($orderData->seller_order_type == 'NSE'){
                    if($orderData->o_type == 'forward'){
                        if($shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail))
                            $returnValue = true;
                    }
                }
                else {
                    if ($orderData->o_type == 'forward'){
                        if($shipping->_SendManifestationXpressBees($orderData->id, $orderData->awb_number, 'Twinnship SFC 3', 'admin@shipesfc3.com', '$shipesfc3$', '58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd', 'aSNDKedk3586OIPdSKsIESSK', $sellerDetail))
                            $returnValue = true;
                    }
                    else if ($orderData->o_type == 'reverse'){
                        if($shipping->_SendManiferstationXpressBeesReverse($orderData->id, $orderData->awb_number, 'Twinnship SFC 3', 'admin@shipesfc3.com', '$shipesfc3$', '58e66f06bd8209ec3c1037e05277d847c193e483486d6b37d6f5d8d5714b64bd', 'aSNDKedk3586OIPdSKsIESSK', $sellerDetail))
                            $returnValue = true;
                    }
                }
                break;
            case 'xpressbees_surface_5kg':
            case 'xpressbees_surface_10kg':
                $shipping = new ShippingController();
                if($orderData->seller_order_type == 'NSE'){
                    if($orderData->o_type == 'forward'){
                        if($shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail))
                            $returnValue = true;
                    }
                }
                else {
                    if ($orderData->o_type == 'forward')
                    {
                        if($shipping->_SendManifestationXpressBees($orderData->id, $orderData->awb_number, 'Twinnship SFC 5', 'admin@shipesfc5.com', '$shipesfc5$', '4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4', 'fsSEKs3587kdPKDAkdrSNsSJ', $sellerDetail))
                            $returnValue = true;
                    }
                    else if ($orderData->o_type == 'reverse'){
                        if($shipping->_SendManiferstationXpressBeesReverse($orderData->id, $orderData->awb_number, 'Twinnship SFC 5', 'admin@shipesfc5.com', '$shipesfc5$', '4b7ce668cfc872a833fb197165f55ac2034e12e360edaf9a99768c6149827de4', 'fsSEKs3587kdPKDAkdrSNsSJ', $sellerDetail))
                            $returnValue = true;
                    }
                }
                break;
//                    case 'xpressbees_surface_10kg':
//                        $shipping = new ShippingController();
//                        if($orderData->seller_order_type == 'NSE'){
//                            if($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id,$orderData->awb_number,'UNIQUEENTERPRISES','admin@uniqusurfa.com','$uniqusurfa$','3f7b3946f350363f60606b0f95a3033f3d1c21cded45d1d0bd47d35340fbc8d0','apv12843wcu',$sellerDetail);
//                        }
//                        else {
//                            if ($orderData->o_type == 'forward')
//                                $shipping->_SendManifestationXpressBees($orderData->id, $orderData->awb_number, 'Twinnship SFC 10', 'admin@shipesfc10.com', '$shipesfc10$', '1abffc9d9e8811b9042f073da94532a737b90d95c3c4ac43e0517e1faa99ba4a', 'ndkPSKD3588ndKSILSKsoeSd', $sellerDetail);
//                            else if ($orderData->o_type == 'reverse')
//                                $shipping->_SendManiferstationXpressBeesReverse($orderData->id, $orderData->awb_number, 'Twinnship SFC 10', 'admin@shipesfc10.com', '$shipesfc10$', '1abffc9d9e8811b9042f073da94532a737b90d95c3c4ac43e0517e1faa99ba4a', 'ndkPSKD3588ndKSILSKsoeSd', $sellerDetail);
//                        }
//                        break;
            case 'smartr':
                $smartr=new Smartr();
                $res = $smartr->manifestOrder($orderData);
                if($res){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'delhivery_surface':
            case 'delhivery_air':
            case 'delhivery_surface_2kg':
            case 'delhivery_surface_5kg':
                $shipping = new ShippingController();
                if($sellerDetail->is_alpha == 'NSE'){
                    if($shipping->SendManifestationDelhivery($orderData->id,"HAMARA BAZAAR B2C","be6d002daeb8bf53fc5e6dd25bf33a4d03a45891"))
                        $returnValue = true;
                }
                else {
                    if($orderData->courier_partner != 'delhivery_air'){
                        if($shipping->SendManifestationDelhivery($orderData->id, "TwinnshipIN SURFACE", "894217b910b9e60d3d12cab20a3c5e206b739c8b"))
                            $returnValue = true;
                    }
                }
                break;
            case 'delhivery_surface_10kg':
                $shipping = new ShippingController();
                if($sellerDetail->is_alpha == 'NSE'){
                    if($shipping->SendManifestationDelhivery($orderData->id,"HAMARA BAZAAR SURFACE","9c6bb4a5969f73ce2bfe937a10140ce843f8096f"))
                        $returnValue = true;
                }
                else{
                    if($shipping->SendManifestationDelhivery($orderData->id,"Twinnship SURFACE","3141800ec51f036f997cd015fdb00e8aeb38e126"))
                        $returnValue = true;
                }
                break;
            case 'delhivery_surface_20kg':
                $shipping = new ShippingController();
                if($sellerDetail->is_alpha == 'NSE')
                {
                    if($shipping->SendManifestationDelhivery($orderData->id,"HAMARA BAZAAR SURFACE","9c6bb4a5969f73ce2bfe937a10140ce843f8096f"))
                        $returnValue = true;
                }
                else{
                    if($shipping->SendManifestationDelhivery($orderData->id,"TwinnshipHEAVY2 SURFACE","18765103684ead7f379ec3af5e585d16241fdb94"))
                        $returnValue = true;
                }
                break;
            case 'delhivery_lite':
                $shipping = new ShippingController();
                if($shipping->SendManifestationDelhivery($orderData->id,"Twinnship WH SURFACE","3c3f230a7419777f2a1f6b57933785a7e93ff43d"))
                    $returnValue = true;
                break;
            case 'gati':
                $gati=new Gati();
                $res = $gati->createOrder($orderData);
                if($res){
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'shree_maruti_ecom':
            case 'shree_maruti_ecom_1kg':
            case 'shree_maruti_ecom_3kg':
            case 'shree_maruti_ecom_5kg':
            case 'shree_maruti_ecom_10kg':
                $maruti=new MarutiEcom();
                $response = $maruti->createOrder($orderData);
                if($response['success'] == 1) {
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                break;
            case 'movin':
            case 'movin_a':
                $movin = new Movin();
                $response = $movin->ShipmentCreate($orderData,$sellerDetail);
                if($response['status'] == 200){
                    if($orderData->shipment_type == 'mps'){
                        // code to read and store awb number
                        try{
                            for($i=1;$i<=$orderData->number_of_packets;$i++){
                                $awbNumber = $response['response']['success'][$orderData->id][$orderData->id."-".$i][0];
                                MPS_AWB_Number::create(['order_id' => $orderData->id,'awb_number' => $awbNumber,'inserted' => date('Y-m-d H:i:s')]);
                            }
                        }catch(Exception $e){}
                    }
                    Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                    $returnValue = true;
                }
                else{
                    if(!empty( $response['response']['errors'][0]['package'][$orderData->id][$orderData->id."-1"][0]['error'])){
                        if(str_contains(strtolower($response['response']['errors'][0]['package'][$orderData->id][$orderData->id."-1"][0]['error']),"number is already taken")){
                            Order::where('id',$orderData->id)->update(['manifest_sent' => 'y']);
                            $returnValue = true;
                        }
                    }
                }
                break;
            default:
                break;
        }
        return $returnValue;
    }
}
