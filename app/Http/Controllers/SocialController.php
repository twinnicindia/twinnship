<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Slider;
use App\Models\Socials;
use Illuminate\Http\Request;
use Exception;
class SocialController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['links']=Socials::all();
        return view('admin.social',$data);
    }
    function insert(Request $request){
        $data=array(
            'icon' => $request->icon,
            'link' => $request->link,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id,
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
        Socials::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Links added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Socials::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Socials::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Socials::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'icon' => $request->icon,
            'link' => $request->link,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MyAdmin')->id
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
//            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
//            try{
//                file_get_contents($apiToExecute."&from=15");
//            }catch(Exception $e){}
//            try{
//                file_get_contents($apiToExecute."&from=13");
//            }catch(Exception $e){}
        }
        Socials::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Links updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
