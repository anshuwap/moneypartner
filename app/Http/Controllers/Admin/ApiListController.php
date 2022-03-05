<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiList;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiListController extends Controller
{

    public function index()
    {
        try {

            $data['apis'] = ApiList::get();
            $data['retailers'] = User::select('_id', 'full_name')->where('role', 'retailer')->get();
            return view('admin.api_list.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function editApi(Request $request)
    {
        try {

            $apiList = ApiList::where('sort', $request->sort)->where('_id', '!=', $request->id)->get();

            if (!$apiList->isEmpty())
                return response(['status' => 'error', 'msg' => 'This priority Already Exist!']);

            $api = ApiList::find($request->id);
            $api->sort          = $request->sort;
            $status = 0;
            if($request->status == 'on')
            $status = 1;
            $api->status        = $status;
            $api->retailer_ids  = $request->retailer_ids;
            if ($api->save())
                return response(['status' => 'success', 'msg' => 'Changes Successfully!']);

            return response(['status' => 'error', 'msg' => 'Changes not Updated Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something Went Wrong!']);
        }
    }


    public function update(Request $request, ApiList $ApiList, $id)
    {
        $employee = ApiList::find($id);
        $employee->sort          = $request->sort;
        if ($employee->save())
            return response(['status' => 'success', 'msg' => 'Employee Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'Employee not Updated Successfully!']);
    }
}
