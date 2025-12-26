<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Rates;
use App\Models\Seller;
use App\Exports\SellerRateCardExport;
use App\Exports\SellerRateCardSampleFileExport;
use App\Imports\SellerRateCardImport;
use App\Models\SubCategory;
use App\Models\Support;
use App\Models\SupportSub;
use App\Models\XindusRates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class SupportSubController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['support'] = Support::where('status','y')->get();
        $data['supportsub'] = SupportSub::join('web_support','web_support.id','=','support_sub.support_id')->select('web_support.title as support','support_sub.*')->get();
        return view('admin.web.supportsub',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'support_id' => $request->support_id,
            'status' => 'y',
        );
        SupportSub::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Support added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        SupportSub::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=SupportSub::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        SupportSub::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'support_id' => $request->support_id,
            'status' => 'y',
        );
        SupportSub::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Support updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

}
