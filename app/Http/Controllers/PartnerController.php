<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Partners;
use App\Models\Preferences;
use App\Models\Rates;
use App\Models\Seller;
use App\Models\Slider;
use App\Models\Socials;
use App\Models\ZoneMapping;
use Illuminate\Http\Request;
use MongoDB\Driver\Session;

class PartnerController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['partner']=Partners::all();
        return view('admin.partner',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'keyword' => $request->keyword,
            'api_key' => $request->api_key,
            'other_key' => $request->other_key,
            'ship_url' => $request->ship_url,
            'track_url' => $request->track_url,
            'status' => 'n',
            'weight_initial' => $request->weight_initial,
            'extra_limit' => $request->extra_limit,
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        $partnerId = Partners::create($data)->id;
        //dd($partnerId,$request->all());
        $defaultRate = [
            'plan_id' => 1,
            'seller_id' => 0,
            'partner_id' => $partnerId,
            'within_city' => $request->zone_a,
            'within_state' => $request->zone_b,
            'metro_to_metro' => $request->zone_c,
            'rest_india' => $request->zone_d,
            'north_j_k' => $request->zone_e,
            'cod_charge' => $request->cod_charge,
            'cod_maintenance' => $request->cod_maintenance,
            'extra_charge_a' => $request->extra_zone_a,
            'extra_charge_b' => $request->extra_zone_b,
            'extra_charge_c' => $request->extra_zone_c,
            'extra_charge_d' => $request->extra_zone_d,
            'extra_charge_e' => $request->extra_zone_e,
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id ?? 0,
        ];
        Rates::create($defaultRate);
        $allSellers = Seller::all();
        $allRates = [];
        foreach ($allSellers as $s){
            $sellerRates = $defaultRate;
            $sellerRates['seller_id'] = $s->id;
            $allRates[] = $sellerRates;
            if(count($allRates) == 50)
            {
                Rates::insert($allRates);
                $allRates = [];
            }
        }
        Rates::insert($allRates);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Partners added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Partners::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Partners::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Partners::where('id',$request->id)->update($data);
        if($data['status'] == 'n')
        {
            $partnerDetail = Partners::find($request->id);
            Seller::where('courier_priority_1',$partnerDetail->keyword)->update(['courier_priority_1' => null]);
            Seller::where('courier_priority_2',$partnerDetail->keyword)->update(['courier_priority_2' => null]);
            Seller::where('courier_priority_3',$partnerDetail->keyword)->update(['courier_priority_3' => null]);
            Seller::where('courier_priority_4',$partnerDetail->keyword)->update(['courier_priority_4' => null]);
            Preferences::where('priority1',$partnerDetail->keyword)->update(['priority1' => null]);
            Preferences::where('priority2',$partnerDetail->keyword)->update(['priority2' => null]);
            Preferences::where('priority3',$partnerDetail->keyword)->update(['priority3' => null]);
            Preferences::where('priority4',$partnerDetail->keyword)->update(['priority4' => null]);
        }
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'keyword' => $request->keyword,
            'api_key' => $request->api_key,
            'other_key' => $request->other_key,
            'ship_url' => $request->ship_url,
            'track_url' => $request->track_url
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Partners::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Partners updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

    function upload_zone_mapping(Request $request){
        ini_set('max_execution_time', '1800');
        ZoneMapping::where('partner_id',$request->id)->delete();
        $test = explode('.', $_FILES['zones']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                $cnt = 0;
                $file = $_FILES['zones']['tmp_name'];
                $handle = fopen($file, "r");
                $data=[];
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $data []= array(
                                'partner_id' => $request->id,
                                'courier_partner' => $request->keyword,
                                //  'order_number' =>Carbon::now()->month;
                                'city' => isset($fileop[0]) ? $fileop[0] : "",
                                'cod_limit' => isset($fileop[1]) ? $fileop[1] : "",
                                'has_cod' => isset($fileop[2]) ? $fileop[2] : "",
                                'has_dg' => isset($fileop[3]) ? $fileop[3] : "",
                                'has_prepaid' => isset($fileop[4]) ? $fileop[4] : "",
                                'has_reverse' => isset($fileop[5]) ? $fileop[5] : "",
                                'picker_zone' => isset($fileop[6]) ? $fileop[6] : "",
                                'pincode' => isset($fileop[7]) ? $fileop[7] : "",
                                'routing_code' => isset($fileop[8]) ? $fileop[8] : "",
                                'state' => isset($fileop[9]) ? $fileop[9] : ""
                            );
                        }
                    }
                    $cnt++;
                    if(count($data)==500){
                        ZoneMapping::insert($data);
                        $data=[];
                    }
                }
                ZoneMapping::insert($data);
                return redirect()->back();
            } else {
                $notification=array(
                    'notification' => array(
                        'type' => 'error',
                        'title' => 'Oopss..',
                        'message' => 'Invalid File',
                    ),
                );
                Session($notification);
                return back();
            }
        }
    }
}
