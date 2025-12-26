<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Blogs;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Slider;
use Illuminate\Http\Request;
use Exception;
class BlogsController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['blogs']=Blogs::all();
        return view('admin.web.blogs',$data);
    }
    function insert(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'long_description' => $request->long_description,
            'by_name' => $request->by_name,
            'from_url' => $request->from_url,
            'date' => date('Y-m-d H:i:s'),
            'status' => 'y'
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
//            $apiToExecute = "https://Twinnship.in/utility/copy-image?path={$filepath}";
//            try{
//                file_get_contents($apiToExecute."&from=15");
//            }catch(Exception $e){}
//            try{
//                file_get_contents($apiToExecute."&from=13");
//            }catch(Exception $e){}
        }
        Blogs::create($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Blog added successfully',
            ),
        );
        Session($notification);
        return back();
    }
    function delete($id){
        Blogs::where('id',$id)->delete();
        echo json_encode(array('status' => 'true'));
    }
    function modify($id){
        $response=Blogs::find($id);
        echo json_encode($response);
    }
    function status(Request $request){
        $data=array(
            'status' => $request->status
        );
        Blogs::where('id',$request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }
    function update(Request $request){
        $data=array(
            'title' => $request->title,
            'description' => $request->description,
            'long_description' => $request->long_description,
            'by_name' => $request->by_name,
            'from_url' => $request->from_url,
            'date' => date('Y-m-d H:i:s')
        );
        if($request->hasFile('image')){
            $oName=$request->image->getClientOriginalName();
            $type=explode('.',$oName);
            $name=date('YmdHis').".".$type[count($type)-1];
            $filepath="assets/admin/images/$name";
            $request->image->move(public_path('assets/admin/images/'),$name);
            $data['image']=$filepath;
        }
        Blogs::where('id',$request->id)->update($data);
        $notification=array(
            'notification' => array(
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Blog updated successfully',
            ),
        );
        Session($notification);
        return back();
    }
}
