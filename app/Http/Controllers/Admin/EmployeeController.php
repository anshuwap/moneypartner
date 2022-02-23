<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{

    public function index()
    {
        try {

            $data['employees'] = User::where('role','employee')->paginate(config('constants.perPage'));

            return view('admin.employee.list',$data);
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


    public function store(Request $request)
    {
        $employee = new User();
        $employee->user_id       = Auth::user()->_id;
        $employee->full_name     = $request->full_name;
        $employee->email         = $request->email;
        $employee->mobile_number = $request->mobile_no;
        $employee->gender        = $request->gender;
        $employee->address       = $request->address;
        $employee->password      = Hash::make($request->password);
        $employee->status        = (int)$request->status;
        $employee->role          = 'employee';
        //for file uploade
        if (!empty($request->file('employee')))
            $employee->employee_img  = singleFile($request->file('employee'), 'attachment');

        if ($employee->save())
            return response(['status' => 'success', 'msg' => 'Employee Created Successfully!']);

        return response(['status' => 'error', 'msg' => 'Employee not Created Successfully!']);
    }


    public function show(User $User)
    {
    }


    public function edit(User $User,$id)
    {

        try {
            $employee = User::find($id);
           return view('admin.employee.edit', compact('employee'));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, User $User,$id)
    {

        $employee = User::find($id);

        $employee->full_name     = $request->full_name;
        $employee->email         = $request->email;
        $employee->mobile_number = $request->mobile_no;
        $employee->gender        = $request->gender;
        $employee->address       = $request->address;
        $employee->password      = Hash::make($request->password);
        $employee->status        = (int)$request->status;
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
