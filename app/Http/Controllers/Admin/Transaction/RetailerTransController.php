<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerTransController extends Controller
{

    public function index()
    {
        try {
            return view('admin.transaction.retailer_display');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()] );;
        }
    }


    public function store(Request $request){

        $retailerTrans = RetailerTrans::find($request->trans_id);
        $retailerTrans->status       = $request->status;
        $retailerTrans->admin_action = $request->admin_action;

        if( !$retailerTrans->save())
        return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

        if($retailerTrans->status == 'approved'){
            $amount = $retailerTrans->amount;
            $receiver_name = $retailerTrans->receiver_name;
            $payment_date = $retailerTrans->created;
            $status = $retailerTrans->status;
            $retailer_id = $retailerTrans->_id;
            transferHistory($retailer_id,$amount, $receiver_name, $payment_date, $status);
            }else{
            //add toupup amount here
            $retailer_id = $retailerTrans->retailer_id;
            $transaction_fees = $retailerTrans->transaction_fees;
            $amount       = $retailerTrans->amount;
            addTopupAmount($retailer_id,$amount,$transaction_fees,1);
            }
        return response(['status' => 'success', 'msg' => 'Transaction '.ucwords($retailerTrans->status).' Successfully!']);
    }



    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = RetailerTrans::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = RetailerTrans::LikeColumn($searchValue);
            $data = RetailerTrans::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = RetailerTrans::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="btn btn-info btn-sm retailer_trans"  _id="' . $val->_id . '">Action</a>';
            // $action .= '<a href="javascript:void(0);" class="text-danger remove_bank_account"  data-toggle="tooltip" data-placement="bottom" title="Remove" bank_account_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';

            if ($val->status == 'approved') {
                $status = '<strong class="text-success">'.ucwords($val->status).'</strong>';
            } else if($val->status =='rejected') {
                $status = '<strong class="text-danger">'.ucwords($val->status).'</strong>';
            }else if($val->status == 'pending'){
                $status = '<strong class="text-warning">'.ucwords($val->status).'</strong>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'sender_name'       => ucwords($val->sender_name),
                'mobile_number'     => $val->mobile_number,
                'amount'            => mSign($val->amount),
                'receiver_name'     => $val->receiver_name,
                'payment_mode'      => ucwords(str_replace('_',' ',$val->payment_mode)),
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
