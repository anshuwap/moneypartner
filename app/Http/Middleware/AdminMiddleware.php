<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
            if ( Auth::user()->isRetailer() ) {

                //check retialer otp verify or not
                if(empty(Auth::user()->verify_otp) || !Auth::user()->verify_otp)
                return redirect(url('otp-sent'));

                return redirect(url('retailer/dashboard'));
            }

            // allow admin to proceed with request
            else if ( Auth::user()->isAdmin() ) {

                 return $next($request);
            }else{

                return redirect(url('/'));
            }
        }

        //abort(404);  // for other user throw 404 error
        return redirect('/');

    }
}
