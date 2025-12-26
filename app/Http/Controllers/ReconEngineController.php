<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\RecommendationEngine;
use App\Models\Stats;
use Illuminate\Http\Request;
class ReconEngineController extends Controller
{
    function __construct()
    {
    }
    
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['recon_engine']=RecommendationEngine::all();
        return view('admin.reconengine',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'y',
        );
        RecommendationEngine::create($data);
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
        RecommendationEngine::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=RecommendationEngine::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        RecommendationEngine::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'y',
        );
        RecommendationEngine::where('id',$request->id)->update($data);
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
