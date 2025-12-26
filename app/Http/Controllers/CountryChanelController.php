<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\CountryChanel;
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

class CountryChanelController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['country']=CountryChanel::all();
        return view('admin.web.country-currency',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'currency' => $request->currency,
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
        CountryChanel::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'CountryChanel added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        CountryChanel::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=CountryChanel::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        CountryChanel::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'currency' => $request->currency,
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
        CountryChanel::where('id',$request->id)->update($data);
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
