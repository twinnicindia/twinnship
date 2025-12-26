<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Admin_rights;
use App\Models\Master;
use Illuminate\Http\Request;
use MongoDB\Driver\Session;

class MasterController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['master']=Master::all();
        return view('admin.master',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'link' => $request->link,
            'icon' => $request->icon,
            'position' => $request->position,
            'parent_id' => $request->parent,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s')
        );
        Master::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Master added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Master::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Master::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Master::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'link' => $request->link,
            'icon' => $request->icon,
            'position' => $request->position,
            'parent_id' => $request->parent,
            'modified' => date('Y-m-d H:i:s')
        );
        Master::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Master updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
