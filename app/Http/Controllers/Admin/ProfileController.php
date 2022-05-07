<?php

namespace App\Http\Controllers\Admin;

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

            $data['user'] = User::find(Auth::user()->_id);
            return view('admin.profile', $data);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $user->full_name      = $request->full_name;
            $user->mobile_number  = $request->mobile_number;
            //for user photo
            if (!empty($request->file('profile_image')))
                $user->profile_image  = singleFile($request->file('profile_image'), 'attachment');

            if (!$user->save())
                return response(['status' => 'error', 'msg' => 'Profile not Updated!']);

            return response(['status' => 'success', 'msg' => 'Profile Updated Successfully!']);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function changePassword(PasswordValidation $request)
    {
        if (!empty($request->old_password)) {
            $user = User::find(Auth::user()->_id);

            if (!Hash::check($request->old_password, $user->password))
                return response(json_encode(array('validation' => ['old_password' => 'Password does not match current password'])));

            $user->password = Hash::make($request->new_password);

            if ($user->save()) {
                Auth::logout();
                return response(['status' => 'success', 'msg' => 'Password Changed Successfully!']);
            }

            return response(['status' => 'error', 'msg' => 'Password not Changed!']);
        }
    }
}
