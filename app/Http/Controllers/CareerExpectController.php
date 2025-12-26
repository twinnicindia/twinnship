<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\CareerExpect;
use App\Models\Faq;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Rates;
use App\Models\Seller;
use App\Exports\SellerRateCardExport;
use App\Exports\SellerRateCardSampleFileExport;
use App\Imports\SellerRateCardImport;
use App\Models\XindusRates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class CareerExpectController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['expect']=CareerExpect::all();
        return view('admin.web.career_expect',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'y',
        );
        if ($request->hasFile('image')) {
            $oName = $request->image->getClientOriginalName();
            $type = explode('.', $oName);
            $name = date('YmdHis') . "." . $type[count($type) - 1];
            $filepath = "assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'), $name);
            $data['image'] = $filepath;
        }
        CareerExpect::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Career added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        CareerExpect::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=CareerExpect::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        CareerExpect::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'y',
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
        CareerExpect::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Career updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

}
