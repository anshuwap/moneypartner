<?php

namespace App\Http\Controllers\Admin\PaymentMode;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode\BankAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{

    public function index()
    {
        try {
            return view('admin.payment_mode.bank_account');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()] );;
        }
    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        $bank_account = new BankAccount();
        $bank_account->user_id              = Auth::user()->_id;
        $bank_account->bank_name            = $request->bank_name;
        $bank_account->account_number       = $request->account_number;
        $bank_account->ifsc_code            = $request->ifsc_code;
        $bank_account->account_holder_name  = $request->account_holder_name;
        $bank_account->status               = $request->status;

        if ($bank_account->save())
            return response(['status' => 'success', 'msg' => 'Bank Account Added Successfully!']);

            return response(['status' => 'error', 'msg' => 'Bank Account not Added Successfully!']);
    }


    public function show(BankAccount $BankAccount)
    {

    }


    public function edit(BankAccount $BankAccount)
    {

        try {
            die(json_encode($BankAccount));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, BankAccount $BankAccount)
    {

        $bank_account = $BankAccount;
        $bank_account->bank_name            = $request->bank_name;
        $bank_account->account_number       = $request->account_number;
        $bank_account->ifsc_code            = $request->ifsc_code;
        $bank_account->account_holder_name  = $request->account_holder_name;
        $bank_account->status               = $request->status;

        if ($bank_account->save())
            return response(['status' => 'success', 'msg' => 'Bank Account Updated Successfully!']);

            return response(['status' => 'error', 'msg' => 'Bank Account not Updated Successfully!']);
    }


    public function destroy($id)
    {
        try {
            $res = BankAccount::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'Bank Account Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Bank Account not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function bankAccountStatus(Request $request)
    {

        try {
            $outlet = BankAccount::find($request->id);
            $outlet->status = (int)$request->status;
            $outlet->save();
            if ($outlet->status == 1)
                return response(['status' => 'success', 'msg' => 'This Bank Account is Active!', 'val' => $outlet->status]);

            return response(['status' => 'success', 'msg' => 'This Bank Account is Inactive!', 'val' => $outlet->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = BankAccount::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = BankAccount::LikeColumn($searchValue);
            $data = BankAccount::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = BankAccount::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="text-info edit_bank_account" data-toggle="tooltip" data-placement="bottom" title="Edit" bank_account_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
            $action .= '<a href="javascript:void(0);" class="text-danger remove_bank_account"  data-toggle="tooltip" data-placement="bottom" title="Remove" bank_account_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';
            if ($val->status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'bank_name'         => ucwords($val->bank_name),
                'account_no'        => $val->account_number,
                'ifsc'              => $val->ifsc_code,
                'holder_name'       => $val->account_holder_name,
                'created_date'      => date('Y-m-d', $val->created),
                'status'            => $status,
                'action'            => $action
            ];
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" =>  $totalRecordswithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $dataArr
        );
        echo json_encode($response);
        exit;
    }

}
