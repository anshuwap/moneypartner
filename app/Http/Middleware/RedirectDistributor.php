<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectDistributor
{

    public function handle(Request $request, Closure $next)
    {
        $role = (!empty(Auth::user()))?Auth::user()->isDistributor():false;//check role

        if($role){

        return redirect(url('distributor/dashboard'));
        }

        return $next($request);
    }
}
