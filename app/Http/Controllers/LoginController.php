<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class LoginController extends Controller
{


    function index(){
        return view('admin.login');
    }
    function check_login(Request $request){
        $resp=Admin::where('email',$request->username)->where('password',$request->password)->orWhere('mobile',$request->username)->where('password',$request->password)->get();
        if(count($resp)==1){
            if($resp[0]->status=='y'){
                $session=array(
                    'MyAdmin' => $resp[0]
                );
                Session()->put($session);
                if($resp[0]->email == 'admin@gmail.com')
                    return redirect(route('administrator'));
                else
                    return redirect(route('administrator.profile'));
            }
            else{
                $notification=array(
                    'notification' => array(
                        'type' => 'error',
                        'title' => 'Account Blocked',
                        'message' => 'Your account is not active please contact the administrator to activate',
                    ),
                );
                Session($notification);
                return back();
            }
        }else{
            $notification=array(
                'notification' => array(
                    'type' => 'error',
                    'title' => 'Invalid Credentials',
                    'message' => 'Invalid Username or Password',
                ),
            );
            Session($notification);
            return back();
        }
    }
    function logout(){
        Session()->forget('MyAdmin');
        return redirect(route('administrator.login'));
    }
}
