<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Features;
use App\Models\Slider;
use App\Models\Why_choose;
use Illuminate\Http\Request;
class Why_chooseController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['why']=Why_choose::all();
        return view('admin.why',$data);
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
        Why_choose::create($data);
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
        Why_choose::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Why_choose::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Why_choose::where('id',$request->id)->update($data);
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
        Why_choose::where('id',$request->id)->update($data);
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
