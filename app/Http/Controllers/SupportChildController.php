<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\Category;
use App\Models\ChildCategory;
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
use App\Models\SupportChild;
use App\Models\SupportSub;
use App\Models\XindusRates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class SupportChildController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['support'] = Support::where('status','y')->get();
        $data['supportchild'] = SupportChild::join('web_support','web_support.id','=','web_support_child.support_id')->join('support_sub','support_sub.id','=','web_support_child.supportsub_id')->select('web_support.title as category','support_sub.title as support_sub','web_support_child.*')->get();
        return view('admin.web.supportchild',$data);
    }
    function insert(Request $request){
        $titles = explode(',', $request->title);
        $data = [];

        foreach ($titles as $title) {
            $data[] = [
                'title' => $title,
                'support_id' => $request->category,
                'supportsub_id' => $request->subcategory,
                'description' => $request->description,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'date' => date('Y-m-d H:i:s'),
                'status' => 'y',
            ];
        }
        SupportChild::insert($data);
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
        SupportChild::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=SupportChild::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        SupportChild::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'support_id' => $request->category,
            'supportsub_id' => $request->subcategory,
            'description' => $request->description,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'date' => date('Y-m-d H:i:s'),
            'status' => 'y',
        );
        SupportChild::where('id',$request->id)->update($data);
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
    function getCategorySubCategory(Request $request){
        $data = SupportSub::where('support_id',$request->category)->get();
        return json_decode($data);
    }
    function getSubCategoryChildCategory(Request $request){
        $data = SupportChild::where('supportsub_id',$request->subcategory)->get();
        return json_decode($data);
    }

}
