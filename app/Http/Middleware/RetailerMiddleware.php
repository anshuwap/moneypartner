<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerMiddleware
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
        if( Auth::check() )
        {

            // if user is not admin take him to his dashboard
            if ( Auth::user()->isAdmin() ) {

                 return redirect(url('admin/dashboard'));
            }

            // allow admin to proceed with request
            else if ( Auth::user()->isRetailer() ) {

                 return $next($request);
            }else{

                return redirect(url('/retailer'));
            }
        }

        //abort(404);  // for other user throw 404 error
        return redirect('/retailer');

    }
}
