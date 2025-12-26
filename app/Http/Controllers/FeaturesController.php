<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Features;
use App\Models\Plan_features;
use App\Models\Slider;
use Illuminate\Http\Request;
class FeaturesController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['feature']=Plan_features::all();
        return view('admin.features',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'detail' => $request->detail,
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
        Plan_features::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Features added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Plan_features::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Plan_features::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Plan_features::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'detail' => $request->detail,
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
        Plan_features::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Features updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
