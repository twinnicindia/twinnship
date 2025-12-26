<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Blogs;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Courier;
use App\Models\Logistics;
use App\Models\Slider;
use Illuminate\Http\Request;
use Exception;
class CourierController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['courier']=Courier::all();
        return view('admin.web.courier',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => 'y'
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Courier::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Courier added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Courier::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Courier::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Courier::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Courier::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Courier updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
