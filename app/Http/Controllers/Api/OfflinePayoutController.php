<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Validation\OfflineBulkPayoutValidation;
use App\Http\Validation\OfflinePayoutValidation;
use App\Models\Outlet;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Auth;

class OfflinePayoutController extends Controller
{

    //for single payout system
    public function payout(OfflinePayoutValidation $request)
    {
        try {
            /*start check amount available in wallet or not*/
            $amount = $request->amount;
            $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
            $charges = 0;
            if (!empty($outlet)) {
                foreach ($outlet->bank_charges as $charge) {
                    if ($charge['type'] == 'inr') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                            $charges = $charge['charges'];
                    } else if ($charge['type'] == 'persantage') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                            $charges = ($charge['charges'] / 100) * $amount;
                    }
                }
            }


            $total_amount = $amount + $charges;

            if ($total_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'flag'=>'insufficent_amount', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/

            $transaction = new Transaction();
            $transaction->transaction_id  = uniqCode(3) . rand(111111, 999999);
            $transaction->retailer_id     = Auth::user()->_id;
            $transaction->outlet_id       = Auth::user()->outlet_id;
            $transaction->mobile_number   = Auth::user()->mobile_number;
            $transaction->sender_name     = Auth::user()->full_name;
            $transaction->amount          = $request->amount;
            $transaction->transaction_fees= $charges;
            $transaction->receiver_name   = $request->receiver_name;
            $transaction->payment_mode    = $request->payment_mode;
            $transaction->payment_channel = $request->payment_channel;
            $transaction->status          = 'pending';
            $transaction->type            = 'payout_api';

            if (!$transaction->save())
                return response(['status' => 'error', 'flag'=>'transaction_not_created', 'msg' => 'Transaction Request not  Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'flag'=>'not_debited', 'msg' => 'Something went wrong!']);

                /*start passbook debit functionality*/
                $amount        = $transaction->amount;
                $receiver_name = $transaction->receiver_name;
                $payment_date  = $transaction->created;
                $status        = 'success';
                $payment_mode  = $transaction->payment_mode;
                $transaction_fees = $transaction->transaction_fees;
                $type          = $transaction->type;
                $retailer_id   = $transaction->retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit');
                /*end passbook debit functionality*/

            return response(['status' => 'success', 'flag'=>'transaction_created', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'flag'=>'system_error', 'msg' => $e->getMessage()]);
        }
    }



    public function bulkPayout(OfflineBulkPayoutValidation $request)
    {

        try {

            foreach ($request->all() as $res) {
                $request = (object)$res;
// print_r($request->base_url);die;
               if(verify_url($request->base_url))
                return response(['status' => 'error', 'flag'=>'authentication_error','msg' => 'Please Enter valid base url!']);

                /*start check amount available in wallet or not*/
                $amount = $request->amount;
                $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
                $charges = 0;
                if (!empty($outlet)) {
                    foreach ($outlet->bank_charges as $charge) {
                        if ($charge['type'] == 'inr') {

                            if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                                $charges = $charge['charges'];
                        } else if ($charge['type'] == 'persantage') {

                            if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                                $charges = ($charge['charges'] / 100) * $amount;
                        }
                    }
                }
                $total_amount = $amount + $charges;

                if ($total_amount >= Auth()->user()->available_amount)
                    return response(['status' => 'error', 'flag'=>'insufficent_amount','msg' => 'Some Transaction Request not Created, Because you have not Sufficient Amount']);
                /*end check amount available in wallet or not*/

                $transaction = new Transaction();
                $transaction->transaction_id  = uniqCode(3) . rand(111111, 999999);
                $transaction->retailer_id     = Auth::user()->_id;
                $transaction->outlet_id       = Auth::user()->outlet_id;
                $transaction->mobile_number   = Auth::user()->mobile_number;
                $transaction->sender_name     = Auth::user()->full_name;
                $transaction->amount          = $request->amount;
                $transaction->transaction_fees= $charges;
                $transaction->receiver_name   = $request->receiver_name;
                $transaction->payment_mode    = $request->payment_mode;
                $transaction->payment_channel = $request->payment_channel;
                $transaction->status          = 'pending';
                $transaction->type            = 'payout_api';

                if(!$transaction->save())
                 return response(['status' => 'error', 'flag'=>'transaction_not_created', 'msg' => 'Somthing went wrong, Transaction Request not Created!']);

                 //update toupup amount here
                if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                 return response(['status' => 'error', 'flag'=>'not_debited', 'msg' => 'Something went wrong, Amount Not Debited!']);

                 /*start passbook debit functionality*/
                $amount        = $transaction->amount;
                $receiver_name = $transaction->receiver_name;
                $payment_date  = $transaction->created;
                $status        = 'success';
                $payment_mode  = $transaction->payment_mode;
                $transaction_fees = $transaction->transaction_fees;
                $type          = $transaction->type;
                $retailer_id   = $transaction->retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit');
                /*end passbook debit functionality*/
            }

                return response(['status' => 'success', 'flag'=>'transaction_created', 'msg' => 'Transaction Request Created Successfully!']);

        } catch (Exception $e) {
            return response(['status' => 'error', 'flag'=>'system_error', 'msg' => $e->getMessage()]);
        }
    }
}
