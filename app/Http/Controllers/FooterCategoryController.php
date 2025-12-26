<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\Faq;
use App\Models\FooterCategory;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Rates;
use App\Models\Seller;
use App\Exports\SellerRateCardExport;
use App\Exports\SellerRateCardSampleFileExport;
use App\Imports\SellerRateCardImport;
use App\Models\Support;
use App\Models\XindusRates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class FooterCategoryController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['footerCategory']=FooterCategory::all();
        return view('admin.web.footercategory',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'status' => 'y',
        );
        FooterCategory::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'FooterCategory added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        FooterCategory::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=FooterCategory::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        FooterCategory::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'status' => 'y',
        );
        FooterCategory::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'FooterCategory updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

}
