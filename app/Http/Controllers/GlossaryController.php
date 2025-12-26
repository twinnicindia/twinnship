<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Glossary;
use App\Models\Logistics;
use App\Models\Slider;
use App\Models\Socials;
use App\Models\WebConfig;
use Illuminate\Http\Request;
class GlossaryController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['glossary']=Glossary::find(1);
        return view('admin.web.glossary',$data);
    }


    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'name' => $request->name,
            'description' => $request->description,
            'wpr_description' => $request->wpr_description,
            'lease_description' => $request->lease_description,
            'guide_description' => $request->guide_description,
            'storage_description' => $request->storage_description,
            'termcondiction' => $request->termcondiction,
            'privacypolicy' => $request->privacypolicy,
            'status' => 'y',
            'date' => date('Y-m-d H:i:s')
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        if ($request->hasFile('image1')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image1->move(public_path('assets/admin/images/'), $name);
            $data['image1'] = $filepath;
            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
            try{
                file_get_contents($apiToExecute."&from=15");
            }catch(Exception $e){}
            try{
                file_get_contents($apiToExecute."&from=13");
            }catch(Exception $e){}
        }
        //dd($data);
        Glossary::updateOrCreate(['id' => 1],$data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Glossary added successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
