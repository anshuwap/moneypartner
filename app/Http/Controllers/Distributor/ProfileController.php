<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Http\Validation\PasswordValidation;
use App\Http\Validation\PinValidation;
use App\Http\Validation\ProfileValidation;
use App\Models\Outlet;
use App\Models\User;
use App\Support\Email;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        try {

            $data['outlet'] = Outlet::find(Auth::user()->outlet_id);
            return view('distributor.profile', $data);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function sendLink(Request $request)
    {
        $validatedData = $request->validate([
            'email'  => 'required|email|exists:users,email'
        ]);

        if ($request->email != Auth::user()->email)
            return response(['status' => 'error', 'msg' => 'Allow Only Registered Email ID!']);

        $token =  uniqid(18) . uniqid(18);
        $email = $request->email;

        $user = User::where('email', $email)->first();
        $user->token = $token;
        $user->save();

        $msg = '<td>
         <h5 class="text-center">Forgot Password link here.</h3
          <p>Click Here&nbsp;&nbsp;<a href="' . url('/retailer/forgot-pin/' . $token) . '">' . $token . '</a> to Change Your Pin</p>
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
        $subject = 'Forgot Pin Link';
        $dataM = ['msg' => $message, 'subject' => $subject, 'email' => $email];
        $email = new Email();
        $res = $email->composeEmail($dataM);

        if ($res)
            return response(['status' => 'success', 'msg' => 'Please check your Email for reset pin.']);

        return response(['status' => 'error', 'msg' => 'Someting Went Wrong.']);
    }


    public function forgotPin($token)
    {
        $data['token'] = $token;
        return view('distributor.setting.forgot_pin', $data);
    }

    public function forgotPinSave(Request $request)
    {
        $validatedData = $request->validate([
            'confirm_pin'  => 'required|digits:4',
            'pin'  => 'required|digits:4|same:confirm_pin'
        ]);
        $pin = $request->pin;
        $token = $request->token;

        $user = User::where('token', $token)->first();
        $user->pin = $pin;
        if ($user->save())
            return redirect('/distributor/dashboard')->with('message', '<span class="text-success">Pin Reset Successfully, Please Login Here.</span>');

        return redirect()->back()->with('message', '<span class="text-danger">Pin not Reset, Please Try Again.</span>');
    }


    public function update(ProfileValidation $request, $id)
    {

        try {
            $outlet = Outlet::find($id);
            $outlet->retailer_name = $request->retailer_name;
            $outlet->mobile_no  = $request->mobile_no;
            $outlet->alternate_number = $request->alternate_number;
            $outlet->email    = $request->email;
            $outlet->gender = $request->gender;

            //for user photo
            if (!empty($request->file('profile_image')))
                $outlet->profile_image  = singleFile($request->file('profile_image'), 'attachment');

            if (!$outlet->save())
                return response(['status' => 'error', 'msg' => 'Profile not Updated!']);

            return response(['status' => 'success', 'msg' => 'Profile Updated Successfully!']);
        } catch (Exception $e) {

            print_r($e->getMessage());
        }
    }


    // private function updateUser($request)
    // {
    //     $user = User::find(Auth::user()->_id);

    //     if (!Hash::check($request->old_password, $user->password))
    //         return false;

    //     $user->password = Hash::make($request->new_password);

    //     if ($user->save())
    //         return true;

    //     return false;
    // }


    public function pinPassword()
    {
        return view('distributor.setting.pin_password');
    }


    public function changePassword(PasswordValidation $request)
    {

        if (!empty($request->old_password)) {
            $user = User::find(Auth::user()->_id);

            if (!Hash::check($request->old_password, $user->password))
                return response(json_encode(array('validation' => ['old_password' => 'Password does not match current password'])));

            $user->password = Hash::make($request->new_password);

            if ($user->save())
                return response(['status' => 'success', 'msg' => 'Password Changed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Password not Changed!']);
        }
    }


    public function changePin(PinValidation $request)
    {

        if (!empty($request->old_pin)) {
            $user = User::find(Auth::user()->_id);

            if (!$request->old_pin == $user->pin)
                return response(json_encode(array('validation' => ['old_pin' => 'Pin does not match current pin'])));

            $user->pin = $request->new_pin;

            if ($user->save())
                return response(['status' => 'success', 'msg' => 'Pin Changed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Pin not Changed!']);
        }
    }


    public function logout()
    {
        $user = User::find(Auth::user()->_id);
        if ($user->role == 'distributor') {
            $user->verify_otp = 0;
            $user->save();
        }
        Auth::logout();
        return redirect('/');
    }

}
