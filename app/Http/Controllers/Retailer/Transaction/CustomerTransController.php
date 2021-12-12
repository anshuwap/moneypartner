<?php

namespace App\Http\Controllers\Retailer\Transaction;

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
            $data['customer_trans'] = CustomerTrans::where('retailer_id',Auth::user()->_id)->get();
            return view('retailer.transaction.customer_display',$data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function create()
    {
    }


    public function store(Request $request)
    {

        //uploade pancard docs
        $pancard = '';
        if (!empty($request->file('pancard')))
        $pancard   = singleFile($request->file('pancard'), 'attachment/transaction');


        $trans = CustomerTrans::select('_id','mobile_number')->where('mobile_number',$request->mobile_number)->first();
        if(!empty($trans) && $trans->mobile_number == $request->mobile_number){
            //update record
        $UpdateCustomerTrans =CustomerTrans::find($trans->_id);

        $trans_details = [];
        if(!empty($UpdateCustomerTrans->trans_details) && is_array($UpdateCustomerTrans->trans_details))
        $trans_details = $UpdateCustomerTrans->trans_details;


        $trans_details[] = [
            'sender_name'     => $request->sender_name,
            'amount'          => $request->amount,
            'receiver_name'   => $request->receiver_name,
            'payment_mode'    => $request->payment_mode,
            'payment_channel' => $request->payment_channel,
            'status'          =>'pending',
            'pancard_no'      => $request->pancard_no,
            'pancard'         => $pancard,
            'created'         => strtotime(date('Y-m-d H:i:s'))
        ];

        $UpdateCustomerTrans->trans_details = $trans_details;
        $res = $UpdateCustomerTrans->save();

        }else{
       //insert new record
        $CustomerTrans = new CustomerTrans();
        $CustomerTrans->retailer_id    = Auth::user()->_id;
        $CustomerTrans->otp            = $request->otp;
        $CustomerTrans->mobile_number  = $request->mobile_number;
        $CustomerTrans->customer_name  = $request->sender_name;


        $trans_details[] = [
            'sender_name'     => $request->sender_name,
            'amount'          => $request->amount,
            'receiver_name'   => $request->receiver_name,
            'payment_mode'    => $request->payment_mode,
            'payment_channel' => $request->payment_channel,
            'status'          =>'pending',
            'pancard_no'      => $request->pancard_no,
            'pancard'         => $pancard,
            'created'         => strtotime(date('Y-m-d H:i:s'))
        ];

        $CustomerTrans->trans_details  = $trans_details;
        $CustomerTrans->verified       = (session("otp-$request->mobile_number") == $request->otp)?1:0;

        $res = $CustomerTrans->save();
        }

        if ($res)
            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);

        return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);
    }


    //otp send functionality
    public function sendOtp(Request $request){

         $mobile_no = $request->mobile_no;

         $res = CustomerTrans::where('mobile_number',$mobile_no)->where('verified',1)->first();
         if (!empty($res) && $res->mobile_number == $request->mobile_no) {

            $all_amount = 0;
            foreach($res->trans_details as $detail){
              $all_amount += $detail['amount'];
            }

            $res_data ='<div class="card mt-2 p-2">
            <div><strong>Month :</strong><span>'.date('M').'</span></div>
            <div><strong>Transaction Amount :</strong><span>'.$all_amount.'</span></div>
            <div><strong>Limit Amount:</strong><span>2,00,000</span></div>
            </div>';

            return response(['status'=>'detail','data'=>$res_data]);
        }else{
        $otp = rand(0000,9999);
        session(["otp-$mobile_no"=>$otp]);

        if($otp == session("otp-$mobile_no"))
        return response(['status' => 'success', 'otp'=>$otp, 'msg' => 'Otp Sent Successfully!']);

        return response(['status' => 'error', 'msg' => 'Otp not snet!']);
        }

    }


    public function verifyMobile(Request $request)
    {

        $mobile_no = $request->mobile_no;

        $otp = $request->otp;
        if ($otp == session("otp-$mobile_no"))
        return response(['status' => 'success', 'msg' => 'Mobile Number Verified Successfully!']);

        return response(['status' => 'error', 'msg' => 'Mobile Number not  Verified!']);
    }


    public function edit(CustomerTrans $CustomerTrans)
    {

        try {
            die(json_encode($CustomerTrans));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function destroy($id)
    {
        try {
            $res = Customer::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'Customer Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Customer not Removed!']);
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
            // $action = '<a href="javascript:void(0);" class="text-info edit_bank_account" data-toggle="tooltip" data-placement="bottom" title="Edit" bank_account_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
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
                'amount'            => $val->amount,
                'receiver_name'     => $val->receiver_name,
                'payment_mode'      => ucwords(str_replace('_', ' ', $val->payment_mode)),
                'status'            => $status,
                'created_date'      => date('Y-m-d', $val->created),
                // 'action'            => $action
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
