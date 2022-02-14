<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectEmployee
{

    public function handle(Request $request, Closure $next)
    {
        $role = (!empty(Auth::user()))?Auth::user()->isEmployee():false;//check role

        if($role){

        return redirect(url('employee/dashboard'));
        }

        return $next($request);
    }
}
