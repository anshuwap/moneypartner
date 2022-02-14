<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Validation\EmployeeProfileValidation;
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

            $data['user'] = User::find(Auth::user()->_id);
            return view('employee.profile', $data);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function update(EmployeeProfileValidation $request, $id)
    {

        try {
            $user = User::find($id);
            $user->full_name      = $request->full_name;
            $user->mobile_number  = $request->mobile_no;
            $user->email          = $request->email;
            $user->gender         = $request->gender;

            //for user photo
            if (!empty($request->file('profile_image')))
                $user->employee_img  = singleFile($request->file('profile_image'), 'attachment');

            if (!$user->save())
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
