<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction\CustomerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerTransController extends Controller
{

    public function index()
    {
        try {
            $data['customer_trans'] = CustomerTrans::get();
            return view('admin.transaction.customer_display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        $CustomerTrans = CustomerTrans::find($request->trans_id);

        $trans_details = $CustomerTrans->trans_details;

        $trans_details[$request->key]['admin_action'] = $request->admin_action;
        $trans_details[$request->key]['status']       = $request->status;
        $trans_details[$request->key]['updated']      = strtotime(date('Y-m-d H:i:s'));

        $CustomerTrans->trans_details = $trans_details;

        if (!$CustomerTrans->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

        if($CustomerTrans->trans_details[$request->key]['status'] == 'approved'){
        $amount = $CustomerTrans->trans_details[$request->key]['amount'];
        $receiver_name = $CustomerTrans->trans_details[$request->key]['receiver_name'];
        $payment_date = $CustomerTrans->trans_details[$request->key]['created'];
        $status = $CustomerTrans->trans_details[$request->key]['status'];
        $retailer_id = $CustomerTrans->_id;
        transferHistory($retailer_id,$amount, $receiver_name, $payment_date, $status);
        }else{
            //add toupup amount here
            $retailer_id = $CustomerTrans->retailer_id;
            $transaction_fees = $CustomerTrans->trans_details[$request->key]['transaction_fees'];
            $amount = $CustomerTrans->trans_details[$request->key]['amount'];
            addTopupAmount($retailer_id,$amount,$transaction_fees,1);
        }

        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($CustomerTrans->status) . ' Successfully!']);
    }


    public function edit(CustomerTrans $CustomerTrans)
    {

        try {
            die(json_encode($CustomerTrans));
        } catch (Exception $e) {
            return redirect('500');
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
        $totalRecords = CustomerTrans::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = CustomerTrans::LikeColumn($searchValue);
            $data = CustomerTrans::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = CustomerTrans::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="btn btn-info btn-sm customer_trans"  _id="' . $val->_id . '">Action</a>';
            // $action .= '<a href="javascript:void(0);" class="text-danger remove_bank_account"  data-toggle="tooltip" data-placement="bottom" title="Remove" bank_account_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';

            if ($val->status == 'approved') {
                $status = '<strong class="text-success">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'rejected') {
                $status = '<strong class="text-danger">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'pending') {
                $status = '<strong class="text-warning">' . ucwords($val->status) . '</strong>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'sender_name'       => ucwords($val->sender_name),
                'mobile_number'     => $val->mobile_number,
                'amount'            => mSign($val->amount),
                'receiver_name'     => $val->receiver_name,
                'payment_mode'      => ucwords(str_replace('_', ' ', $val->payment_mode)),
                'status'            => $status,
                'created_date'      => date('Y-m-d', $val->created),
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
