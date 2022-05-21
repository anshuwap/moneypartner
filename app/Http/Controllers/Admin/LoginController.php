<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\LoginValidation;
use App\Models\User;
use App\Support\Email;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }


    public function store(LoginValidation $request)
    {
        try {

            $remember_me = $request->has('remember_me') ? true : false;
            $credentials = $request->only('email', 'password');
            $otp = mt_rand(1111, 9999);

            if (Auth::attempt($credentials)) {

                if (Auth::user()->role == 'retailer') {

                    if (!empty($_COOKIE['logged_in']) && $_COOKIE['logged_in'] == 'logged')
                        return redirect()->intended('retailer/dashboard');

                    $user = User::where('_id', Auth::user()->_id)->where('mobile_number', Auth::user()->mobile_number)->first();
                    $user->otp = $otp;
                    if ($user->save()) {
                        $email = $user->email;
                        $source = $this->sendOtp($otp, $email, $user->mobile_number, $user->full_name);

                        $data  = ['otp' => $otp, 'msg' => '<span class="text-success">Otp Sent Successfully in this ' . $source . ' !</span>'];

                        return redirect()->intended('otp-sent')->with('message', $data);
                    }
                } else if (Auth::user()->role == 'employee') {
                    return redirect()->intended('employee/dashboard')
                        ->withSuccess('Signed in');
                } else if (Auth::user()->role == 'distributor') {
                    return redirect()->intended('distributor/dashboard')
                        ->withSuccess('Signed in');
                } else if (Auth::user()->role == 'admin') {

                   $user = User::where('_id', Auth::user()->_id)->where('mobile_number', Auth::user()->mobile_number)->first();
                    $user->otp = $otp;
                    if ($user->save()) {
                        $email = $user->email;
                        $source = $this->sendOtp($otp, $email, $user->mobile_number, $user->full_name);
                        $data  = ['otp' => $otp, 'msg' => '<span class="text-success">Otp Sent Successfully in this ' . $source . ' !</span>'];
                        return redirect()->intended('otp-sent')->with('message', $data);
                    }
                    // return redirect()->intended('admin/dashboard')
                    //     ->withSuccess('Signed in');
                }
            }
            return redirect()->back()->with('success', 'Invalid credentials!');
        } catch (Exception $e) {
            return redirect('500')->with('error', $e->getMessage());
        }
    }


    public function otpSent()
    {
        return view('admin.send_otp');
    }

    public function resendOtp()
    {
        $email = Auth::user()->email;
        $otp = mt_rand(1111, 9999);

        //resend otp sent functioality
        $user = User::where('email', $email)->where('_id', Auth::user()->_id)->first();
        $user->otp = $otp;
        $res1 = $user->save();

        $res = $this->sendOtp($otp, $email, $user->mobile_number, $user->full_name);

        if ($res && $res1) {

            $data  = ['otp' => $otp, 'msg' => '<span class="text-success">Otp Sent Successfully in this ' . $res . ' !</span>'];
            return redirect()->intended('otp-sent')->with('message', $data);
        }

        $data  = ['msg' => '<span class="text-danger">Someting went wrong, Otp not Sent Successfully in this ' . $email . ' !</span>'];
        return redirect()->intended('otp-sent')->with('message', $data);
    }

    public function sendOtp($otp, $email, $mobile, $name)
    {
        $msg = '<td>
         <h5 class="text-center">Signin - Your OTP/Verification code is</h3>
            <h3 class="otp">' . $otp . '</h3>
            <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
            <td class="text-center">
            <!-- <a href="#" class="btn btn-primary" target="_blank">Click here</a> -->
            <!-- <span style="font-size: 16px; font-weight:500;"> to complete the verification</span> -->
            </td>
            </tr>
            </tbody>
            </table>
        </td>';

        $message = $this->emailTemplate($msg);
        $subject = 'OTP by Moneypartner';
        $eemail = $email;
        $dataM = ['msg' => $message, 'subject' => $subject, 'email' => $email];
        $email = new Email();
        $res = $email->composeEmail($dataM);

        if (!$res) {
            $mobile = $this->sendMobileOtp($otp, $mobile, $name);
            if ($mobile)
                return $mobile;

            return false;
        }

        return $eemail;
    }


    public function sendMobileOtp($otp, $mobile, $name)
    {
        $url = 'http://164.52.195.161/API/SendMsg.aspx?uname=20191682&pass=Cool@2020&send=WEBDUN&dest=' . $mobile . '&msg=Hi ' . $name . ', Your OTP for phone verification is ' . $otp . '.';

        $ressponse = Http::get($url);

        if (!$ressponse)
            return false;

        // setcookie('logged_in', 'logged', time() + 10800, "/");
        return $mobile;
    }

    public function verifyMobile(Request $request)
    {
        $validatedData = $request->validate([
            'otp'  => 'required'
        ]);

        $otp = implode(',', $request->otp);
        $otp = trim(str_replace(',', '', $otp));
        $id  = Auth::user()->_id;

        $count = User::where('otp', (int)$otp)->where('_id', $id)->count();
        if ($count > 0) {
            $user = User::find($id);
            $user->verify_otp = 1;

            if ($user->save()) {

                if ($user->verify_otp == 1)
                    setcookie('logged_in', 'logged', time() + 10800, "/");

                return redirect()->intended('retailer/dashboard');
            }

            return redirect()->back()->with('message', array('otp' => '', 'msg' => '<span class="custom-text-danger">Somthing went wrong, Please contact to Admin.</span>'));
        }

        return redirect()->back()->with('message', array('otp' => '', 'msg' => '<span class="custom-text-danger">OTP not Verified, Please try again.</span>'));
    }


    public function sendLinkview()
    {
        return view('admin.send_link');
    }


    public function sendLink(Request $request)
    {
        $validatedData = $request->validate([
            'email'  => 'required|email|exists:users,email'
        ]);

        $token =  uniqid(18) . uniqid(18);
        $email = $request->email;

        $user = User::where('email', $email)->first();
        $user->token = $token;
        $user->save();

        $msg = '<td>
         <h5 class="text-center">Forgot Password link here.</h3
           <p>Click Here&nbsp;&nbsp;<a href="' . url('forgot-password/' . $token) . '">' . $token . '</a> to Change Your Password</p>
            <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
            <td class="text-center">
            <!-- <a href="#" class="btn btn-primary" target="_blank">Click here</a> -->
            <!-- <span style="font-size: 16px; font-weight:500;"> to complete the verification</span> -->
            </td>
            </tr>
            </tbody>
            </table>
        </td>';

        $message = $this->emailTemplate($msg);
        $subject = 'Forgot password Link';
        $dataM = ['msg' => $message, 'subject' => $subject, 'email' => $email];
        $email = new Email();
        $res = $email->composeEmail($dataM);

        if ($res)
            return redirect()->back()->with('message', '<span class="text-success">Please check your Email for reset password.</span>');

        return redirect()->back()->with('message', '<span class="text-danger">Someting Went Wrong.</span>');
    }

    public function forgotPassword($token)
    {
        $data['token'] = $token;
        return view('admin.forgot_password', $data);
    }

    public function forgotPasswordSave(Request $request)
    {
        $validatedData = $request->validate([
            'confirm_password'  => 'required|min:6|max:16',
            'password'  => 'required|max:16|min:6|same:confirm_password'
        ]);
        $password = $request->password;
        $token = $request->token;

        $user = User::where('token', $token)->first();
        if (empty($user))
            return redirect()->back()->with('message', '<span class="text-danger">Token Expire, Please Try Again.</span>');

        $user->password = Hash::make($password);
        if ($user->save())
            return redirect('/')->with('message', '<span class="text-success">Password Reset Successfully, Please Login Here.</span>');

        return redirect()->back()->with('message', '<span class="text-danger">Password not Reset, Please Try Again.</span>');
    }


    public function removeOtp()
    {
        $user = User::find(Auth::user()->_id);
        $user->otp = '';
        $user->save();
    }

    public function logout()
    {
        $user = User::find(Auth::user()->_id);
        if ($user->role == 'retailer') {
            $user->verify_otp = 0;
            $user->save();
        }
        Auth::logout();
        return redirect('/');
    }
}
