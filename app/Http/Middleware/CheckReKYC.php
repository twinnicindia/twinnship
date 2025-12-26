<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Utilities;

class CheckReKYC
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
        if (Session()->get('MyReSeller')->basic_information == 'n' || Session()->get('MyReSeller')->account_information == 'n' || Session()->get('MyReSeller')->kyc_information == 'n' || Session()->get('MyReSeller')->agreement_information == 'n') {
            $this->utilities->generate_notification('Complete', 'Complete Your KYC Details First', 'error');
            return redirect(route('reseller.kyc'));
        }
        if (Session()->get('MyReSeller')->verified == 'n') {
            $this->utilities->generate_notification('Not Approve', ' Your Document not Approve Yet Please Wait for Approval', 'error');
            return redirect(route('reseller.kyc'));
        }
        return $next($request);
    }
}
