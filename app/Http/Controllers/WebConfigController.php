<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Slider;
use App\Models\Socials;
use App\Models\WebConfig;
use Illuminate\Http\Request;
class WebConfigController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['config']=WebConfig::all();
        return view('admin.web.webconfig',$data);
    }


    function insert(Request $request){
        $data=array(
            'address1' => $request->address1,
            'address2' => $request->address2,
            'email' => $request->email,
            'mobile1' => $request->mobile1,
            'mobile2' => $request->mobile2,
            'page' => $request->page,
            'whatsapp_number' => $request->whatsapp_number,
        );
        if($request->hasFile('whatsapp_image')){
            $oName=$request->whatsapp_image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->whatsapp_image->move(public_path('assets/admin/images/'),$name);
            $data['whatsapp_image']=$filepath;
            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if($request->hasFile('footer_image')){
            $oName=$request->footer_image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->footer_image->move(public_path('assets/admin/images/'),$name);
            $data['footer_image']=$filepath;
            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        WebConfig::where('id', 1)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Config added successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
