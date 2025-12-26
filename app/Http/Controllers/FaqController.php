<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
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

class FaqController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['faq']=Faq::all();
        return view('admin.web.faq',$data);
    }
    function insert(Request $request){
        $data=array(
            'answer' => $request->answer,
            'question' => $request->question,
            'status' => 'y',
        );
        Faq::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Faq added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Faq::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Faq::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Faq::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'answer' => $request->answer,
            'question' => $request->question,
            'status' => 'y',
        );
        Faq::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Faq updated successfully',
            ),
        );
        Session($notification);
        return back();
    }

}
