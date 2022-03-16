<?php

namespace App\Http\Controllers\Admin\Action;

use App\Http\Controllers\Controller;
use App\Models\CreditDebit;
use App\Models\PaymentChannel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebitController extends Controller
{

    public function index()
    {
        try {
            $data['outlets'] = User::select('_id', 'outlet_name')->where('role', 'retailer')->get();
            $data['credits'] = CreditDebit::where('type', 'debit')->get();
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            return view('admin.action.debit', $data);
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

            $user_amount = User::select('available_amount')->where('_id', $retailer_id)->first();
            $available_amount = 0;
            if (!empty($user_amount))
                $available_amount = $user_amount->available_amount;

            if ($amount > $available_amount)
                return response(['status' => 'error', 'msg' => 'Debited Failed, debited Amount is greater then available amount.']);

            $creditDebit = new CreditDebit();
            $creditDebit->retailer_id = $retailer_id;
            $creditDebit->user_id     = Auth::user()->_id;
            $creditDebit->transaction_id = uniqCode(3) . rand(111111, 999999);
            $creditDebit->amount      = $amount;
            $creditDebit->payment_date = $payment_date;
            $creditDebit->status      = $status;
            $creditDebit->payment_mode = $payment_mode;
            $creditDebit->remark      = $remark;
            $creditDebit->channel     = $request->payment_channel;
            $creditDebit->type        = 'debit';

            if ($creditDebit->save()) {
                if (!spentTopupAmount($retailer_id, $amount))
                    return response(['status' => 'error', 'msg' => 'Something went wrong!']);

                /*start passbook debit functionality*/
                $amount        = $amount;
                $receiver_name = '';
                $payment_date  = time();
                $status        = 'success';
                $payment_mode  =  $payment_mode;
                $transaction_fees = 0;
                $type          = $payment_mode;
                $retailer_id   = $retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $remark);
                /*end passbook debit functionality*/

                return response(['status' => 'success', 'msg' => mSign($amount) . ' Debited Successfully', 'status_msg' => ucwords($status)]);
            } else {
                return response(['status' => 'error', 'msg' => 'Something went Wrong!']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
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
