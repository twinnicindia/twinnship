<?php

namespace App\Http\Middleware;

use App\Models\Admin_rights;
use Closure;
use Illuminate\Http\Request;

class CheckReSellerSession
{

    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('MyReSeller')){
            return redirect(route('reseller.login'));
        }

        // check permission
        if(session()->get('MyReSeller')->type == 'emp') {
            $permissions= explode(',', session()->get('MyReSeller')->permissions);
            if(((request()->routeIs('reseller.orders') || request()->routeIs('reseller.merge_orders') || request()->routeIs('reseller.pod')) && !in_array('orders', $permissions))) {
                return redirect(route('reseller.login'));
            } else if((request()->routeIs('reseller.ndr_orders') && !in_array('shipments', $permissions))) {
                return redirect(route('reseller.login'));
            } else if((request()->routeIs('reseller.billing') && !in_array('billing', $permissions))) {
                return redirect(route('reseller.login'));
            } else if(((request()->routeIs('reseller.channels') || request()->routeIs('reseller.oms') || request()->routeIs('reseller.my_oms')) && !in_array('integrations', $permissions))) {
                return redirect(route('reseller.login'));
            } else if((request()->routeIs('reseller.mis_report') && !in_array('reports', $permissions))) {
                return redirect(route('reseller.login'));
            } else if((request()->routeIs('reseller.customer_support') && !in_array('customer_support', $permissions))) {
                return redirect(route('reseller.login'));
            }
        }

        // return $next($request);
        $response=$next($request);
        //for Clear Cache Data (Browser Reload)
        return $response->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','Sun, 02 Jan 1990 00:00:00 GMT');
    }
}
