<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Validation\CreateEmployeeValidation;
use App\Http\Validation\UpdateEmployeeValidation;
use App\Models\User;
use App\Support\Email;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        try {

 $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['employees'] = User::whereIn('role', ['employee','admin'])->where('user_id',Auth::user()->_id)->orderBy('created_at', 'DESC')->paginate($perPage);

            return view('admin.employee.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function create()
    {
        try {
            return view('admin.employee.create');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(CreateEmployeeValidation $request)
    {
        try{
        $employee = new User();
        $employee->user_id       = Auth::user()->_id;
        $employee->full_name     = $request->full_name;
        $employee->email         = $request->email;
        $employee->mobile_number = $request->mobile_no;
        $employee->gender        = $request->gender;
        $employee->address       = $request->address;
        $employee->password      = Hash::make($request->password);
        $employee->status        = (int)$request->status;
        $employee->role          = $request->role;
        //for file uploade
        if (!empty($request->file('employee')))
            $employee->employee_img  = singleFile($request->file('employee'), 'attachment');

        if (!$employee->save())
            return response(['status' => 'error', 'msg' => 'Employee not Created Successfully!']);

        $msg = '<h3>Welcome in Moneypartner Panel</h3>';
        $msg .= '<p></p>Your User Id is-' . $request->email . '</p>';
        $msg .= '<p></p>Your Password is-' . $request->password . '</p>';
        $subject = 'Welcome Message';
        $dataM = ['msg' => $msg, 'subject' => $subject, 'email' => $request->email];
        $email = new Email();
        $email->composeEmail($dataM);

        return response(['status' => 'success', 'msg' => 'Employee Created Successfully!']);
        }catch(Exception $e){
             return response(['status' => 'error', 'msg' =>$e->getMessage()]);
        }
    }


    public function edit(User $User, $id)
    {

        try {
            $employee = User::find($id);
            return view('admin.employee.edit', compact('employee'));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(UpdateEmployeeValidation $request, User $User, $id)
    {

        $employee = User::find($id);
        $count = $employee->where('mobile_no', $request->mobile_no)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['mobile_no' => 'This Mobile Number used by Other.'])));

        $count = $employee->where('email', $request->email)->where('_id', '!=', $id)->count();
        if ($count > 0)
            return response(json_encode(array('validation' => ['email' => 'This Email used by Other.'])));

        $employee->full_name     = $request->full_name;
        $employee->email         = $request->email;
        $employee->mobile_number = $request->mobile_no;
        $employee->gender        = $request->gender;
        $employee->address       = $request->address;
        $employee->password      = Hash::make($request->password);
        $employee->status        = (int)$request->status;
        $employee->role          = $request->role;
        //for file uploade
        if (!empty($request->file('employee')))
            $employee->employee_img  = singleFile($request->file('employee'), 'attachment');

        if ($employee->save())
            return response(['status' => 'success', 'msg' => 'Employee Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'Employee not Updated Successfully!']);
    }


    public function employeeStatus(Request $request)
    {

        try {
            $user = User::find($request->id);
            $user->status = (int)$request->status;
            $user->save();
            if ($user->status == 1)
                return response(['status' => 'success', 'msg' => 'This QR Code is Active!', 'val' => $user->status]);

            return response(['status' => 'success', 'msg' => 'This QR Code is Inactive!', 'val' => $user->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }
}
