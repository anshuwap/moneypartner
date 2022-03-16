<?php

namespace App\Http\Controllers\Admin\Action;

use App\Http\Controllers\Controller;
use App\Models\CreditDebit;
use App\Models\PaymentChannel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{

    public function index()
    {
        try {
            $data['outlets'] = User::select('_id', 'outlet_name')->where('role', 'retailer')->get();
            $data['credits'] = CreditDebit::where('type', 'credit')->get();
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            return view('admin.action.credit', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }

    public function store(Request $request)
    {

        try {
            $retailer_id  = $request->retailer_id;
            $amount       = $request->amount;
            $status       = 'success';
            $payment_mode = 'by_admin';
            $remark       = $request->remark;
            $payment_date = time();

            $creditDebit = new CreditDebit();
            $creditDebit->retailer_id = $retailer_id;
            $creditDebit->user_id     = Auth::user()->_id;
            $creditDebit->transaction_id = uniqCode(3) . rand(111111, 999999);
            $creditDebit->amount      = $amount;
            $creditDebit->payment_date= $payment_date;
            $creditDebit->status      = $status;
            $creditDebit->payment_mode= $payment_mode;
            $creditDebit->remark      = $remark;
            $creditDebit->channel     = $request->payment_channel;
            $creditDebit->paid_status = 'due';
            $creditDebit->type        = 'credit';
            if ($creditDebit->save()) {

                //add topup amount in retailer wallet
                addTopupAmount($retailer_id, $amount);

                $retailer_id      = $retailer_id;
                $amount           = $amount;
                $receiver_name    = '';
                $payment_date     = $payment_date;
                $status           = $status;
                $payment_mode     = $payment_mode;
                $type             = $payment_mode;
                $transaction_fees = 0;
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'credit', $remark);

                return response(['status' => 'success', 'msg' => mSign($amount) . ' Credited Successfully', 'status_msg' => ucwords($status)]);
            } else {

                return response(['status' => 'error', 'msg' => 'Something went Wrong!']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function edit($id)
    {

        try {
            $credit = CreditDebit::find($id);
            die(json_encode(['status' => 'success', 'data' => $credit]));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $remark       = $request->remark;
            $creditDebit = CreditDebit::find($id);
            $creditDebit->remark      = $remark;
            $creditDebit->channel     = $request->payment_channel;

            if ($creditDebit->save()) {
                return response(['status' => 'success', 'msg' => 'Updated Successfully']);
            } else {

                return response(['status' => 'error', 'msg' => 'Something went Wrong!']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function creditPaidStatus(Request $request)
    {
        $creditDebit = CreditDebit::find($request->id);
        $creditDebit->paid_status = $request->status;

        if ($creditDebit->save())
        return response(['status' => 'success', 'msg' => 'Paid Status Updated Successfully']);

        return response(['status' => 'error', 'msg' => 'Paid Status not updated']);
    }

    public function showBlance($id)
    {
        $amount = User::select('available_amount')->where('_id', $id)->first();
        $available_amount = 0;
        if (!empty($amount))
            $available_amount = $amount->available_amount;

        die(json_encode(['amount' => mSign($available_amount)]));
    }
}
