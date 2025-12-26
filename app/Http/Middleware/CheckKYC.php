<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Utilities;

class CheckKYC
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
        $this->utilities = new Utilities();
        if (Session()->get('MySeller')->basic_information == 'n' || Session()->get('MySeller')->account_information == 'n' || Session()->get('MySeller')->kyc_information == 'n' || Session()->get('MySeller')->agreement_information == 'n') {
            $this->utilities->generate_notification('Complete', 'Complete Your KYC Details First', 'error');
            return redirect(route('seller.kyc'));
        }
        if (Session()->get('MySeller')->verified == 'n') {
            $this->utilities->generate_notification('Not Approve', ' Your Document not Approve Yet Please Wait for Approval', 'error');
            return redirect(route('seller.kyc'));
        }
        return $next($request);
    }
}
