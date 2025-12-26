<?php

namespace App\Http\Middleware;

use App\Models\Admin_rights;
use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        return response()->json(['status' => false,'message' => 'Site is under maintenance mode and will be live shortly']);
        if(!Session()->has('MyAdmin')){
            return redirect(route('administrator.login'));
        }
        $url=basename($_SERVER['REQUEST_URI']);
        if(Session()->get('MyAdmin')->type != "admin"){
            if($url=="administrator-profile" || $url =="administrator-dashboard" || $url = 'export_csv_cod_remittance'){
                // Do nothing common page for all users
            }
            else{
                $allowed=Admin_rights::check_permission($url,Session()->get('MyAdmin')->id);
                if(count($allowed)==0)
                {
                    echo json_encode(array('message' => 'You are not allowed to access this page'));
                    exit;
                }
                else{
                    Session(['ins' => $allowed[0]->ins]);
                    Session(['del' => $allowed[0]->del]);
                    Session(['modi' => $allowed[0]->modi]);
                }
            }
        }
        return $next($request);
    }
}
