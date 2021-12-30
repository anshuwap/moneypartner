<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Http\Validation\ProfileValidation;
use App\Models\Outlet;
use App\Models\User;
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

           if (!empty($request->old_password)){
            if(!$this->updateUser($request))
            return response(json_encode(array('validation' => ['old_password' => 'Old Password does not match current password'])));
           }


            return response(['status' => 'success', 'msg' => 'Profile Updated Successfully!']);
        } catch (Exception $e) {

            print_r($e->getMessage());
        }
    }


    private function updateUser($request)
    {
        $user = User::find(Auth::user()->_id);

        if (!Hash::check($request->old_password, $user->password))
          return false;

            $user->password = Hash::make($request->new_password);

        if ($user->save())
            return true;

        return false;
    }
}
