<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\OTPValidation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
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

                if (Auth::user()->role == 'retailer') {

                    $otp = rand(0000, 9999);

                    $user = User::where('_id', Auth::user()->_id)->where('mobile_number', Auth::user()->mobile_number)->first();
                    $user->otp = $otp;
                    $data  = ['otp' => $otp, 'msg' => '<span class="text-success">Otp Sent Successfully!</span>'];
                    if ($user->save())
                        return redirect()->intended('otp-sent')->with('message', $data);
                } else {
                    return redirect()->intended('admin/dashboard')
                        ->withSuccess('Signed in');
                }
            }
            return redirect()->back()->with('success', 'Invalid credentials!');
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function otpSent()
    {
        return view('admin.send_otp');
    }


    public function verifyMobile(Request $request)
    {

        $validatedData = $request->validate([
            'otp'  => 'required'
        ]);

        $otp = implode(',',$request->otp);
        $otp = trim(str_replace(',', '', $otp));
        $id  = Auth::user()->_id;

        $count = User::where('otp', (int)$otp)->where('_id', $id)->count();
        if ($count > 0) {
            $user = User::find($id);
            $user->verify_otp = 1;

            if ($user->save())
                return redirect()->intended('retailer/dashboard');

            return redirect()->back()->with('message', array('otp' => '', 'msg' => '<span class="custom-text-danger">Somthing went wrong, Please contact to Admin.</span>'));
        }

        return redirect()->back()->with('message', array('otp' => '', 'msg' => '<span class="custom-text-danger">OTP not Verified, Please try again.</span>'));
    }


    public function logout()
    {
        $user = User::find(Auth::user()->_id);
        if($user->role == 'retailer'){
        $user->verify_otp = 0;
        $user->save();
        }
        Auth::logout();
        return redirect('/');
    }
}
