<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Stats;
use Illuminate\Http\Request;
class StatsController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['stats']=Stats::all();
        return view('admin.stats',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'number' => $request->number,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id,
        );
        Stats::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Stats added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Stats::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Stats::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Stats::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'number' => $request->number,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MyAdmin')->id
        );
        Stats::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Stats updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
