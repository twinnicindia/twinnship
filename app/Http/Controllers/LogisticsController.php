<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Logistics;
use App\Models\Slider;
use Illuminate\Http\Request;
class LogisticsController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['partner']=Logistics::all();
        return view('admin.logistic',$data);
    }
    function insert(Request $request){
        $data=array(
            'link' => $request->link,
            'position' => $request->position,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id,
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Logistics::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Logistics added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Logistics::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Logistics::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Logistics::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'link' => $request->link,
            'position' => $request->position,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MyAdmin')->id
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Logistics::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Logistics updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
