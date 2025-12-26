<?php

namespace App\Http\Controllers;

use App\Models\Account_informations;
use App\Models\Admin_employee;
use App\Models\Admin_rights;
use App\Models\Agreement_informations;
use App\Models\Basic_informations;
use App\Models\DefaultInvoiceAmount;
use App\Models\Kyc_informations;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Seller;
use App\Models\Configuration;
use App\Models\ZoneMapping;
use Illuminate\Http\Request;
// use App\Libraries\ImageCompress as Image;
use DB;
use Exception;

class SellerAdminController extends Controller
{
    protected $info;
    function __construct()
    {
        $this->info['config'] = Configuration::find(1);
    }

    function index(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        //  $data['kvc']=DB::table('sellers .* as s', 'kyc_information .* as k')->where('k.seller_id','s.id')->get();
        //  $data['kvc']=DB::table('kyc_information as k')->join('sellers as s','s.id', '=' , 'k.seller_id')->get();
        $seller = Seller::query();
        if($request->filled('q')) {
            $seller = $seller->where('id', $request->q)
                ->orWhere('code', $request->q)
                ->orWhere('email', 'like', "%{$request->q}%")
                ->orWhere('company_name', 'like', "%{$request->q}%");
        }
        $seller = $seller->orderBy('id','desc')->paginate(10);
        $data['seller'] = $seller;
        $data['config']=Configuration::find(1);
        return view('admin.seller', $data);
    }

    function view($id)
    {
        $response = DB::select("select k.*,k.id as kyc_id,s.*,b.*,a.document_upload as agreement_document,ac.* from kyc_information k,sellers s,basic_informations b,account_informations ac,agreement_informations a where s.id=k.seller_id and ac.seller_id=s.id and a.seller_id = s.id and b.seller_id = s.id and s.id = $id");
        $data['config']=Configuration::find(1);

        echo json_encode($response);
    }

    function viewSeller($id)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['seller'] = Seller::all();
        $data['sellerInfo'] = DB::select("select k.*,k.id as kyc_id,s.*,s.id as seller_id,b.*,a.document_upload as agreement_document,ac.* from kyc_information k,sellers s,basic_informations b,account_informations ac,agreement_informations a where s.id=k.seller_id and ac.seller_id=s.id and a.seller_id = s.id and b.seller_id = s.id and s.id = $id");
        $data['agreement_info'] = Agreement_informations::where('seller_id',$id)->first();
        $data['employee'] = Admin_employee::where('department','sales')->get();
        $data['courier_blocking'] = Partners::whereNotIn('id', function($query) use ($id) {
            $query->select('courier_partner_id')
                ->from('courier_blocking')
                ->where('is_blocked', 'y')
                ->where('seller_id',$id);
        })->where('status','y')->get();
        foreach ($data['courier_blocking'] as $a){
            $data['rates'][$a->id] = DefaultInvoiceAmount::where('seller_id',$id)->where('partner_id',$a->id)->first();
        }
        // $data['sellerInfo'] = DB::table('sellers')->join('kyc_information','sellers.id','=','kyc_information.seller_id')
        // ->join('basic_informations','sellers.id','=','basic_informations.seller_id')
        // ->join('account_informations','sellers.id','=','account_informations.seller_id')
        // ->join('agreement_informations','sellers.id','=','agreement_informations.seller_id')
        // ->select('sellers.*','kyc_information.*','kyc_information.id as kyc_id','basic_informations.*','agreement_informations.document_upload as agreement_document','account_informations.*')->where('sellers.id',$id)->get();
        // dd($data['sellerInfo']);
        // dd(DB::getQueryLog());
        if(empty($data['sellerInfo'])){
            $notification=array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Oops',
                    'message' => 'Seller Not Uploaded any Detail Yet',
                ),
            );
            Session($notification);
            return back();
        }
        $data['plan'] = Plans::where('status','y')->get();
        $data['config']=Configuration::find(1);
        return view('admin.seller', $data);
    }

    function verify(Request $request)
    {
        $data = array(
            'agreement_information' => 'y'
        );
        if ($request->hasFile('agreement_document')) {
            $oName = $request->agreement_document->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "CHQ." . $type[count($type) - 1];
            $filepath = "assets/admin/images/seller/$name";
            $request->agreement_document->move(public_path('assets/admin/images/seller/'), $name);
            $data['agreement_document'] = $filepath;
        }
        Seller::where('id', $request->seller_id)->update($data);
        $checkData = Admin_employee::where('department','operations')->whereRaw('FIND_IN_SET(?, seller_ids)', $request->seller_id)->first();
        if(empty($checkData)){
            $employee = Admin_employee::where('status','y')->where('department','operations')->inRandomOrder()->get();
            if(count($employee) != 0)
                Admin_employee::where('id',$employee[0]->id)->update(['seller_ids' => ($employee[0]->seller_ids).",".$request->seller_id ]);
        }
        return redirect()->back();
    }

    function seller_order_type(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->seller_order_type = $request->type;
            $seller->seller_order_type_updated_at = now();
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = $e->getMessage();
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function sellerIsAlpha(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->is_alpha = $request->type;
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = $e->getMessage();
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function zone_type(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->zone_type = $request->type;
            $seller->zone_type_updated_at = now();
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = $e->getMessage();
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function status(Request $request)
    {
        // $data = array(
        //     'status' => $request->status
        // );
        // Seller::where('id', $request->id)->update($data);
        // echo json_encode(array('status' => 'true'));
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->status = $request->status;
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Data not updated';
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function gst_status(Request $request)
    {
        $data = array(
            'gst_certificate_status' => $request->status
        );
        Seller::where('id', $request->id)->update($data);
        $b = Basic_informations::where('seller_id', $request->id)->first();
        if ($request->status == 'r') {
            Seller::where('id', $request->id)->update(['basic_information' => 'n']);
            @unlink($b->gst_certificate);
            Basic_informations::where('seller_id', $request->id)->update(['gst_certificate' => '']);
        }
        echo json_encode(array('status' => 'true'));
    }

    function cheque_status(Request $request)
    {
        $data = array(
            'cheque_status' => $request->status
        );
        Seller::where('id', $request->id)->update($data);
        $a = Account_informations::where('seller_id', $request->id)->first();
        if ($request->status == 'r') {
            Seller::where('id', $request->id)->update(['account_information' => 'n']);
            @unlink($a->cheque_image);
            Account_informations::where('seller_id', $request->id)->update(['cheque_image' => '']);
        }
        echo json_encode(array('status' => 'true'));
    }

    function document_status(Request $request)
    {
        $data = array(
            'document_status' => $request->status
        );
        Seller::where('id', $request->id)->update($data);
        $k =  Kyc_informations::where('seller_id', $request->id)->first();
        if ($request->status == 'r') {
            Seller::where('id', $request->id)->update(['kyc_information' => 'n']);
            @unlink($k->document_upload);
            Kyc_informations::where('seller_id', $request->id)->update(['document_upload' => '']);
        }
        echo json_encode(array('status' => 'true'));
    }

    function agreement_status(Request $request)
    {
        $data = array(
            'agreement_status' => $request->status
        );
        Seller::where('id', $request->id)->update($data);
        $a = Agreement_informations::where('seller_id', $request->id)->first();
        if ($request->status == 'r') {
            Seller::where('id', $request->id)->update(['agreement_information' => 'n']);
            @unlink($a->document_upload);
            Agreement_informations::where('seller_id', $request->id)->update(['document_upload' => '']);
        }
        echo json_encode(array('status' => 'true'));
    }

    function sms_status(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->sms_service = $request->status;
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Data not updated';
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function pincode_editable(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->pincode_editable = $request->status;
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Data not updated';
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function delete($id)
    {
        Seller::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

     function export_seller(){
        $name = "exports/Seller Details";
        $filename = "Seller Details";
        // $all_data = Seller::latest()->get();
        $all_data = DB::table('sellers')->leftJoin('basic_informations','basic_informations.seller_id','=','sellers.id')->leftJoin('account_informations','account_informations.seller_id','=','sellers.id')->select('basic_informations.*','account_informations.*','sellers.*')->get();
        $fp = fopen("$name.csv", 'w');
        $info = array('Sr.No','Seller Name','Seller Code','Wallet Balance','Company Name','Email Id','Contact Number','GST Number','Bank Name','Account Holder Name','Bank Account Number','Address','IFSC Number','KYC Status');
        fputcsv($fp, $info);
        $cnt = 1;
        foreach ($all_data as $e) {
            $kyc_status = $e->verified=='y' ? 'Verified' : 'Not Verified';
            $address = $e->street.','.$e->city.','.$e->state.','.$e->pincode;
            // dd($e);
            $info = array($cnt,$e->first_name.' '.$e->last_name,$e->code,$e->balance,$e->company_name,$e->email,$e->mobile,$e->gst_number,$e->bank_name,$e->account_holder_name,('`'.$e->account_number.'`'),$address,$e->ifsc_code,$kyc_status);
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

    function basic_information(Request $request)
    {
        // dd($request->all());
        $data = array(
            'company_name' => $request->company_name,
            'website_url' => $request->website_url,
            'email' => $request->email,
            'mobile' => $request->mobile_number,
            'pan_number' => $request->pan_number,
            'gst_number' => $request->gst_number,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->zipcode,
            'modified_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('logo')) {
            // Compress image file size
            // Image::compress($request->file('logo'));
            $oName = $request->logo->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "LOGO." . $type[count($type) - 1];
            $filepath = "assets/admin/images/seller/$name";
            $request->logo->move(public_path('assets/admin/images/seller/'), $name);
            $data['company_logo'] = $filepath;
        }

        if ($request->hasFile('gst_certificate')) {
            $oName = $request->gst_certificate->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "GST." . $type[count($type) - 1];
            $filepath = "assets/admin/images/seller/$name";
            $request->gst_certificate->move(public_path('assets/admin/images/seller/'), $name);
            $data['gst_certificate'] = $filepath;
        }
        Basic_informations::where('seller_id', $request->seller_id)->update($data);
        Seller::where('id',$request->seller_id)->update(['mobile' =>  $request->mobile_number,'email' => $request->email ]);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Basic Information Updated Successfully.',
            ),
        );
        Session($notification);
        return redirect()->back();
    }

    function account_information(Request $request)
    {
        $data = array(
            'account_holder_name' => $request->ac_holder_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'bank_branch' => $request->bank_branch,
            'modified_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('cheque_image')) {
            $oName = $request->cheque_image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "CHQ." . $type[count($type) - 1];
            $filepath = "assets/admin/images/seller/$name";
            $request->cheque_image->move(public_path('assets/admin/images/seller/'), $name);
            $data['cheque_image'] = $filepath;
        }
        Account_informations::where('seller_id', $request->seller_id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Account Information Updated Successfully.',
            ),
        );
        Session($notification);
        return redirect()->back();
    }

    function kyc_information(Request $request)
    {
        // dd($request->all());
        $data = array(
            'company_type' => $request->company_type,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'document_id' => $request->document_number,
            'modified_at' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('document_upload')) {
            $oName = $request->document_upload->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "CHQ." . $type[count($type) - 1];
            $filepath = "assets/admin/images/seller/$name";
            $request->document_upload->move(public_path('assets/admin/images/seller/'), $name);
            $data['document_upload'] = $filepath;
        }
        Kyc_informations::where('seller_id', $request->seller_id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'KYC Information Updated Successfully.',
            ),
        );
        Session($notification);
        return redirect()->back();
    }

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

    function get_ifsc_detail($ifsc)
    {
        $bankDetail = @file_get_contents("https://ifsc.razorpay.com/$ifsc");
        if ($bankDetail == "")
            echo json_encode(["status" => "false"]);
        else
            echo $bankDetail;
    }

    function sellerEmployee(Request $request)
    {
        $data['menus'] = Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        //dd();
        //  $data['kvc']=DB::table('sellers .* as s', 'kyc_information .* as k')->where('k.seller_id','s.id')->get();
        //  $data['kvc']=DB::table('kyc_information as k')->join('sellers as s','s.id', '=' , 'k.seller_id')->get();
        $seller = Seller::query();
        if($request->filled('q')) {
            $seller = $seller->where('id', $request->q)
                ->orWhere('code', $request->q)
                ->orWhere('email', 'like', "%{$request->q}%")
                ->orWhere('company_name', 'like', "%{$request->q}%");
        }
        $seller = $seller->where('verified','y');
        $seller = $seller->orderBy('id','desc')->get();
        $data['seller'] = $seller;
        $data['config']=Configuration::find(1);
        $data['employee'] = Admin_employee::where('status','y')->where('department','operations')->get();
        foreach ($data['seller'] as $s){
            $data['employee_name'][$s->id] = Admin_employee::whereRaw('FIND_IN_SET(?, seller_ids)', [$s->id])->first();
        }
        return view('admin.seller_employee', $data);
    }

    function assignMultipleSellerEmployee(Request $request){
        $sellerIDs = explode(',',$request->seller_ids);
        for($i=0;$i<count($sellerIDs);$i++){
            $oldEmp = Admin_employee::where('department','operations')->whereRaw('FIND_IN_SET(?, seller_ids)', $sellerIDs[$i])->first();
            $newEmp = Admin_employee::find($request->employee_id);
            if(!empty($oldEmp)){
                if($newEmp->id != $oldEmp->id){
                    $ids = explode(',',$oldEmp->seller_ids);
                    $key = array_search($sellerIDs[$i],$ids,true);
                    unset($ids[$key]);
                    $newIds = implode(',',$ids);
                    Admin_employee::where('id',$oldEmp->id)->update(['seller_ids' => $newIds]);
                    Admin_employee::where('id',$newEmp->id)->update(['seller_ids' => $newEmp->seller_ids.",".$sellerIDs[$i]]);
                }
            }
            else{
                Admin_employee::where('id',$newEmp->id)->update(['seller_ids' => $newEmp->seller_ids.",".$sellerIDs[$i]]);
            }
        }
    }

    function verified(Request $request)
    {
        try {
            $seller = Seller::findOrFail($request->id);
            $seller->verified = $request->status;
            $seller->save();
            $res['status'] = true;
            $res['message'] = 'Data updated successfully';
            $res['data'] = [];
        } catch (Exception $e) {
            $res['status'] = false;
            $res['message'] = 'Data not updated';
            $res['data'] = [];
        }
        return response()->json($res);
    }

    function seller_information(Request $request)
    {
        $data = array(
            'remittance_frequency' => $request->remittance_frequency,
            'remittanceWeekDay' => implode(",",$request->remittanceWeekDay),
            'onboarded_by' => $request->onboarded_by,
            'warehouse_status' => $request->warehouse_status,
            'google_id' => $request->google_id,
            'rto_charge' => $request->rto_charge,
            'early_cod_charge' => $request->early_cod_charge,
            'reconciliation_days' => $request->reconciliation_days,
            'invoice_date' => $request->invoice_date,
            'api_key' => $request->api_key,
            'reverse_charge' => $request->reverse_charge,
            'full_label_display' => $request->full_label_display,
            'sms_service' => $request->sms_service,
            'easyecom_token' => $request->easyecom_token,
            'remmitance_days' => $request->remmitance_days,
            'employee_flag_enabled' => $request->employee_flag_enabled,
            'webhook_enabled' => $request->webhook_enabled,
            'webhook_url' => $request->webhook_url,
            'modified_by' => Session()->get('MyAdmin')->id,
            'verified' => 'y',
            'basic_information' => 'y',
            'account_information' => 'y',
            'kyc_information' => 'y',
            'plan_id' => $request->plan,
            'agreement_information' => 'y',
            'migration_enabled' => $request->migration_enabled,
            'merge_order_number' => $request->merge_order_number,
            'modified_at' => date('Y-m-d h:i:s')
        );
        Seller::where('id', $request->id)->update($data);
        $notification = array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Seller Information Updated & Verified Successfully.',
            ),
        );
        Session($notification);
        return redirect()->back();
    }
}
