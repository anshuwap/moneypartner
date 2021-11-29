<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectRetailer
{

    public function handle(Request $request, Closure $next)
    {
        $role = (!empty(Auth::user()))?Auth::user()->isRetailer():false;//check role

        if($role){

        return redirect(url('retailer/dashboard'));
        }

        return $next($request);
    }
}
