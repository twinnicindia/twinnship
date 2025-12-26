<?php

namespace App\Http\Middleware;

use App\Models\Admin_rights;
use Closure;
use Illuminate\Http\Request;

class AuthCRM {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Session()->has('MyCrm')) {
            return redirect(route('crm.login'));
        }
        // return $next($request);
        $response=$next($request);
        //for Clear Cache Data (Browser Reload)
        return $response->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','Sun, 02 Jan 1990 00:00:00 GMT');
    }
}
