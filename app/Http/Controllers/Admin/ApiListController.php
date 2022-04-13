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
            // $api = new ApiList();
            // $api->api = 'https://payout.merchantpe.in/Api/v2/Client/en/remittance/transaction';
            // $api->name = 'odinmo';
            // $api->sort = "1";
            // $api->status = 1;
            // $api->save();


            $data['apis'] = ApiList::get();
            $data['retailers'] = User::select('_id', 'full_name')->whereIn('role', ['retailer','distributor'])->get();
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
            if ($request->status == 'on')
                $status = 1;
            $api->status        = $status;
            $api->retailer_ids  = $request->retailer_ids;
            if ($api->save())
                return response(['status' => 'success', 'msg' => 'Changes Successfully Updated!']);

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


    public function allocateRetailer(Request $request)
    {
        try {
            $bank_id = $request->id;
            $apiList = ApiList::select('retailer_ids')->find($bank_id);

            $retailers = User::select('_id', 'outlet_name','role')->whereIn('role', ['retailer','distributor'])->get();

            $checkbox  = '<table class="table table-sm">';
            $checkbox .= '<tr><th>Outlet Name</th><th>Outlet Type</th></th><th>Action</th></tr>';
            foreach ($retailers as $retailer) {
                $checked = (!empty($apiList->retailer_ids) && is_array($apiList->retailer_ids) && in_array($retailer->id, $apiList->retailer_ids)) ? "checked" : "";

                $checkbox .= '<tr>';
                $checkbox .= '<td>' . ucwords($retailer->outlet_name) . '</td>';
                $checkbox .='<td>'.ucwords($retailer->role).'</td>';
                $checkbox .= '<td><input type="checkbox" value="' . $retailer->_id . '" name="retailers[]" ' . $checked . '></td>';
                $checkbox .= '</tr>';
            }
            $checkbox .= '</table>';

            die(json_encode($checkbox));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function saveAllocateRetailer(Request $request)
    {
        try {
            $apiList = ApiList::find($request->id);
            $apiList->retailer_ids = $request->retailers;
            if ($apiList->save())
                return response(['status' => 'success', 'msg' => 'Api Allocated Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }
}
