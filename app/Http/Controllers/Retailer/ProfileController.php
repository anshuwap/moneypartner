<?php

namespace App\Http\Controllers\Retailer;

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
            return view('retailer.profile', $data);
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

        $msg = '<h3>Welcome in Moneypartner Panel</h3>';
        $msg .= '<p>Click Here&nbsp;&nbsp;<a href="' . url('/retailer/forgot-pin/' . $token) . '">' . $token . '</a> to Change Your Pin</p>';
        $subject = 'Forgot Pin Link';
        $dataM = ['msg' => $msg, 'subject' => $subject, 'email' => $email];
        $email = new Email();
        $res = $email->composeEmail($dataM);

        if ($res)
            return response(['status' => 'success', 'msg' => 'Please check your Email for reset pin.']);

        return response(['status' => 'error', 'msg' => 'Someting Went Wrong.']);
    }


    public function forgotPin($token)
    {
        $data['token'] = $token;
        return view('retailer.setting.forgot_pin', $data);
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
            return redirect('/retailer/dashboard')->with('message', '<span class="text-success">Pin Reset Successfully, Please Login Here.</span>');

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
        return view('retailer.setting.pin_password');
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
}
