<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('retailer.login');
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6|max:16',
            ]);

            $remember_me = $request->has('remember_me') ? true : false;
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                return redirect()->intended('retailer/dashboard')
                    ->withSuccess('Signed in');
            }
            return redirect()->back()->with('success', 'Invalid credentials!');
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
