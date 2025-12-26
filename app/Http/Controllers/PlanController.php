<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Courier_blocking;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Rates;
use App\Models\Seller;
use App\Exports\SellerRateCardExport;
use App\Exports\SellerRateCardSampleFileExport;
use App\Imports\SellerRateCardImport;
use App\Models\SellerRateChangeDetails;
use App\Models\SellerRateChanges;
use App\Models\XindusRates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class PlanController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['plans']=Plans::all();
        return view('admin.plans',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id
        );
        Plans::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Plans added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Plans::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Plans::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Plans::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MyAdmin')->id
        );
        Plans::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Plans updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function rates(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['plans']=Plans::all();
        $data['partners']=Partners::all();
        return view('admin.rates',$data);
    }
    function seller_rates(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['plans']=Plans::all();
        $data['partners']=Partners::where('status','y')->get();
        $data['sellers']=Seller::where('verified','y')->get();
        return view('admin.seller_rates',$data);
    }
    // function save_rates(Request $request){
    //     $plan=$request->plan;
    //     $partners=Partners::all();
    //     Rates::where('plan_id',$plan)->where('seller_id',$request->seller_id)->delete();
    //     $data=array();
    //     foreach ($partners as $p){
    //         $data[]=array(
    //             'plan_id' => $plan,
    //             'seller_id' => $request->seller_id,
    //             'partner_id' => $p->id,
    //             'within_city' => $request->input('within_city_'.$p->id."_".$plan),
    //             'within_state' => $request->input('within_state_'.$p->id."_".$plan),
    //             'metro_to_metro' => $request->input('metro_to_metro_'.$p->id."_".$plan),
    //             'rest_india' => $request->input('rest_india_'.$p->id."_".$plan),
    //             'north_j_k' => $request->input('north_j_k_'.$p->id."_".$plan),
    //             'cod_charge' => $request->input('cod_charge_'.$p->id."_".$plan),
    //             'cod_maintenance' => $request->input('cod_maintenance_'.$p->id."_".$plan),
    //             'extra_charge_a' => $request->input('extra_charge_a'.$p->id."_".$plan),
    //             'extra_charge_b' => $request->input('extra_charge_b'.$p->id."_".$plan),
    //             'extra_charge_c' => $request->input('extra_charge_c'.$p->id."_".$plan),
    //             'extra_charge_d' => $request->input('extra_charge_d'.$p->id."_".$plan),
    //             'extra_charge_e' => $request->input('extra_charge_e'.$p->id."_".$plan),
    //             'inserted' => date('Y-m-d H:i:s'),
    //             'inserted_by' => Session()->get('MyAdmin')->id
    //         );
    //     }
    //     Rates::insert($data);
    // }
    function save_rates(Request $request) {
        $plan=$request->plan;
        $partners=Partners::all();
        $this->DumpOldSellerRates($request);
        Rates::where('plan_id', $plan)->where('seller_id', $request->seller_id)->delete();
        Courier_blocking::where('seller_id',$request->seller_id)->delete();
        $allRates=array();
        foreach ($partners as $p) {
            if(!empty($request->input('within_city_'.$p->id."_".$plan)) && $request->input('within_state_'.$p->id."_".$plan) && $request->input('metro_to_metro_'.$p->id."_".$plan) && $request->input('rest_india_'.$p->id."_".$plan) && $request->input('north_j_k_'.$p->id."_".$plan))
            {
                $changedData = [
                    'plan_id' => $plan,
                    'seller_id' => $request->seller_id,
                    'partner_id' => $p->id,
                    'within_city' => $request->input('within_city_'.$p->id."_".$plan),
                    'within_state' => $request->input('within_state_'.$p->id."_".$plan),
                    'metro_to_metro' => $request->input('metro_to_metro_'.$p->id."_".$plan),
                    'rest_india' => $request->input('rest_india_'.$p->id."_".$plan),
                    'north_j_k' => $request->input('north_j_k_'.$p->id."_".$plan),
                    'cod_charge' => $request->input('cod_charge_'.$p->id."_".$plan),
                    'cod_maintenance' => $request->input('cod_maintenance_'.$p->id."_".$plan),
                    'extra_charge_a' => $request->input('extra_charge_a'.$p->id."_".$plan),
                    'extra_charge_b' => $request->input('extra_charge_b'.$p->id."_".$plan),
                    'extra_charge_c' => $request->input('extra_charge_c'.$p->id."_".$plan),
                    'extra_charge_d' => $request->input('extra_charge_d'.$p->id."_".$plan),
                    'extra_charge_e' => $request->input('extra_charge_e'.$p->id."_".$plan),
                ];

                $allRates[] = $changedData;
            }
            else{
                // Block the Courier Partner Here
                Courier_blocking::create([
                    'seller_id' => $request->seller_id,
                    'courier_partner_id' => $p->id,
                    'is_blocked' => 'y',
                    'is_approved' => 'y',
                    'zone_a' => 'y',
                    'zone_b' => 'y',
                    'zone_c' => 'y',
                    'zone_d' => 'y',
                    'zone_e' => 'y',
                    'cod' => 'y',
                    'prepaid' => 'y',
                    'remark' => 'Blocked Via Rate Card Submission',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
//            // Update all seller data
            if($request->update_to_all == 1 && $request->seller_id == 0 && count($changedData) > 0) {
                unset($changedData['seller_id']);
                foreach(Seller::all() as $row) {
                    Rates::updateOrCreate([
                        'plan_id' => $plan,
                        'seller_id' => $row->id,
                        'partner_id' => $p->id,
                    ], $changedData);
                }
            }
        }
        Rates::insert($allRates);
    }
    function DumpOldSellerRates(Request $request){
        $sellerRate = SellerRateChanges::create([
            'seller_id' => $request->seller_id,
            'modified' => date('Y-m-d H:i:s'),
            'modified_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
        ]);
        $rates = [];
        $oldRates = Rates::where('seller_id',$request->seller_id)->where('plan_id',$request->plan)->get();
        foreach ($oldRates as $o){
            $rates []= [
                'plan_id' => $request->plan,
                'seller_rate_change_id' => $sellerRate->id,
                'seller_id' => $request->seller_id,
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
    function get_rates(Request $request){
        $seller_id=$request->get('seller_id');
        $rates=Rates::where('seller_id',$seller_id)->get();
        echo json_encode($rates);
    }

    function xindusRates(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['sellers']=Seller::all();
        return view('admin.xindus-rates',$data);
    }
    function saveXindusRates(Request $request){
        $file = $_FILES['rates']['tmp_name'];
        $test = explode('.', $_FILES['rates']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                XindusRates::where('seller_id',$request->seller)->delete();
                $handle = fopen($file, "r");
                $rates = [];
                $cnt = 0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $rates[] = [
                                'seller_id' => $request->seller,
                                'weight' => isset($fileop[1]) ? $fileop[1] : "",
                                'rate' => isset($fileop[2]) ? $fileop[2] : "",
                                'is_additional' => isset($fileop[3]) ? $fileop[3] : "",
                                'initial_weight' => isset($fileop[4]) ? $fileop[4] : "",
                                'extra_charge' => isset($fileop[5]) ? $fileop[5] : "",
                                'extra_limit' => isset($fileop[6]) ? $fileop[6] : ""
                            ];
                        }
                    }
                    $cnt++;
                }
                // Insert Code Here
                XindusRates::insert($rates);
                $this->utilities->generate_notification("Success","Rates Imported Successfully","success");
                return back();
            }
            $this->utilities->generate_notification("Error","Invalid File Uploaded","error");
            return back();
        }
        $this->utilities->generate_notification("Error","Please Upload File","error");
        return back();
    }

    function aramexRates(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['sellers']=Seller::all();
        return view('admin.aramex-rates',$data);
    }
    function saveAramexRates(Request $request){
        $file = $_FILES['rates']['tmp_name'];
        $test = explode('.', $_FILES['rates']['name']);
        if (isset($test[1])) {
            if ($test[count($test) - 1] == "csv") {
                AramexRates::where('seller_id',$request->seller)->delete();
                $handle = fopen($file, "r");
                $rates = [];
                $cnt = 0;
                while (($fileop = fgetcsv($handle, 10000, ",")) !== false) {
                    if ($cnt > 0) {
                        if ($fileop[0] != "") {
                            $rates[] = [
                                'seller_id' => $request->seller,
                                'weight' => isset($fileop[1]) ? $fileop[1] : "",
                                'rate_1' => isset($fileop[3]) ? $fileop[3] : "",
                                'rate_2' => isset($fileop[4]) ? $fileop[4] : "",
                                'rate_3' => isset($fileop[5]) ? $fileop[5] : "",
                                'rate_4' => isset($fileop[6]) ? $fileop[6] : "",
                                'rate_5' => isset($fileop[7]) ? $fileop[7] : "",
                                'rate_6' => isset($fileop[8]) ? $fileop[8] : "",
                                'rate_7' => isset($fileop[9]) ? $fileop[9] : "",
                                'rate_8' => isset($fileop[10]) ? $fileop[10] : "",
                                'rate_9' => isset($fileop[11]) ? $fileop[11] : "",
                                'rate_10' => isset($fileop[12]) ? $fileop[12] : "",
                                'rate_11' => isset($fileop[13]) ? $fileop[13] : "",
                                'rate_12' => isset($fileop[14]) ? $fileop[14] : "",
                                'rate_13' => isset($fileop[15]) ? $fileop[15] : "",
                                'is_additional' => isset($fileop[2]) ? $fileop[2] : "",
                                'initial_weight' => isset($fileop[16]) ? $fileop[16] : "",
                                'extra_limit' => isset($fileop[17]) ? $fileop[17] : "",
                                'extra_charge_1' => isset($fileop[18]) ? $fileop[18] : "",
                                'extra_charge_2' => isset($fileop[19]) ? $fileop[19] : "",
                                'extra_charge_3' => isset($fileop[20]) ? $fileop[20] : "",
                                'extra_charge_4' => isset($fileop[21]) ? $fileop[21] : "",
                                'extra_charge_5' => isset($fileop[22]) ? $fileop[22] : "",
                                'extra_charge_6' => isset($fileop[23]) ? $fileop[23] : "",
                                'extra_charge_7' => isset($fileop[24]) ? $fileop[24] : "",
                                'extra_charge_8' => isset($fileop[25]) ? $fileop[25] : "",
                                'extra_charge_9' => isset($fileop[26]) ? $fileop[26] : "",
                                'extra_charge_10' => isset($fileop[27]) ? $fileop[27] : "",
                                'extra_charge_11' => isset($fileop[28]) ? $fileop[28] : "",
                                'extra_charge_12' => isset($fileop[29]) ? $fileop[29] : "",
                                'extra_charge_13' => isset($fileop[30]) ? $fileop[30] : "",
                            ];
                        }
                    }
                    $cnt++;
                }
                // Insert Code Here
                AramexRates::insert($rates);
                $this->utilities->generate_notification("Success","Rates Imported Successfully","success");
                return back();
            }
            $this->utilities->generate_notification("Error","Invalid File Uploaded","error");
            return back();
        }
        $this->utilities->generate_notification("Error","Please Upload File","error");
        return back();
    }

    /**
     * Import seller rate card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importSellerRateCard(Request $request)
    {
        try {
            if (!$request->hasfile('excel')) {
                $this->utilities->generate_notification('Error', 'Please upload excel file', 'error');
                return back();
            }
            Excel::import(new SellerRateCardImport, $request->file('excel')->store('temp'));
            // Generating notification
            $this->utilities->generate_notification('Success', 'Excel file imported successfully', 'success');
            return back();
        } catch (Exception $e) {
            // Generating notification
            $this->utilities->generate_notification('Error', $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * Export seller rate card data to excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportSellerRateCardSample(Request $request)
    {
        $fileName = 'seller-rate-card-sample';
        if ($request->exportType == 'csv') {
            $fileName .= '.csv';
        } else {
            $fileName .= '.xlsx';
        }
        return Excel::download(new SellerRateCardSampleFileExport($request->query()), $fileName);
    }

    /**
     * Export seller rate card data to excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportSellerRateCard(Request $request)
    {
        $fileName = 'seller-rate-card';
        if ($request->exportType == 'csv') {
            $fileName .= '.csv';
        } else {
            $fileName .= '.xlsx';
        }
        return Excel::download(new SellerRateCardExport($request->query()), $fileName);
    }
}
