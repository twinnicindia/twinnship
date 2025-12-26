<?php

namespace App\Http\Middleware;

use App\Models\Admin_rights;
use Closure;
use Illuminate\Http\Request;

class CheckSellerSession
{

    public function handle(Request $request, Closure $next)
    {

        if(!session()->has('MySeller')){
            return redirect(route('seller.login'));
        }
        //return response(view('maintenance')->render());
        // check permission
        if(session()->get('MySeller')->type == 'emp') {
            $permissions= explode(',', session()->get('MySeller')->permissions);
            if(((request()->routeIs('seller.orders') || request()->routeIs('seller.merge_orders') || request()->routeIs('seller.pod')) && !in_array('orders', $permissions))) {
                return redirect(route('seller.login'));
            } else if((request()->routeIs('seller.ndr_orders') && !in_array('shipments', $permissions))) {
                return redirect(route('seller.login'));
            } else if((request()->routeIs('seller.billing') && !in_array('billing', $permissions))) {
                return redirect(route('seller.login'));
            } else if(((request()->routeIs('seller.channels') || request()->routeIs('seller.oms') || request()->routeIs('seller.my_oms')) && !in_array('integrations', $permissions))) {
                return redirect(route('seller.login'));
            } else if((request()->routeIs('seller.mis_report') && !in_array('reports', $permissions))) {
                return redirect(route('seller.login'));
            } else if((request()->routeIs('seller.customer_support') && !in_array('customer_support', $permissions))) {
                return redirect(route('seller.login'));
            }
        }

        // return $next($request);
        return $next($request);

    }
}
