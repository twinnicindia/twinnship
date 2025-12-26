<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\Category;
use App\Models\Faq;
use App\Models\FooterCategory;
use App\Models\FooterSub;
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

class FooterSubController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['support'] = FooterCategory::where('status','y')->get();
        $data['supportsub'] = FooterSub::join('web_footercategory','web_footercategory.id','=','web_footer_sub.footer_id')->select('web_footercategory.title as support','web_footer_sub.*')->get();
        return view('admin.web.footersub',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'link' => $request->link,
            'footer_id' => $request->footer_id,
            'status' => 'y',
        );
        FooterSub::create($data);
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
        FooterSub::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=FooterSub::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        FooterSub::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'link' => $request->link,
            'footer_id' => $request->footer_id,
            'status' => 'y',
        );
        FooterSub::where('id',$request->id)->update($data);
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
