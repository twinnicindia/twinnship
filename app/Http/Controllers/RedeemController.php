<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Redeem_codes;
use App\Models\Slider;
use Illuminate\Http\Request;
class RedeemController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['code']=Redeem_codes::all();
        return view('admin.redeem',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'code' => $request->code,
            'value' => $request->value,
            'limit' => $request->limit,
            'min_amount' => $request->min_amount,
            'status' => 'y',
            'inserted' => date('Y-m-d H:i:s'),
            'inserted_by' => Session()->get('MyAdmin')->id,
        );
        Redeem_codes::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Code added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Redeem_codes::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Redeem_codes::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Redeem_codes::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'code' => $request->code,
            'value' => $request->value,
            'limit' => $request->limit,
            'min_amount' => $request->min_amount,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => Session()->get('MyAdmin')->id
        );
        Redeem_codes::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Codes updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
