<?php

namespace App\Http\Controllers;

use App\Models\Aboutus;
use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
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

class AboutusController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['about']=Aboutus::all();
        return view('admin.web.about_us',$data);
    }
    function insert(Request $request){
        $data=array(
            'name' => $request->name,
            'post' => $request->post,
            'link' => $request->link,
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
        Aboutus::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Aboutus added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Aboutus::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Aboutus::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Aboutus::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'name' => $request->name,
            'post' => $request->post,
            'link' => $request->link,
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
        Aboutus::where('id', $request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Aboutus updated successfully',
            ),
        );
        Session($notification);
        return back();
    }


}
