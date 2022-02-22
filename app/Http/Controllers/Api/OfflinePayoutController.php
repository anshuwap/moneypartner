<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Validation\OfflineBulkPayoutValidation;
use App\Http\Validation\OfflinePayoutValidation;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Outlet;
use Exception;
use Illuminate\Http\Request;
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
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/

            $OfflinePayoutApi = new OfflinePayoutApi();
            $OfflinePayoutApi->transaction_id  = uniqCode(3) . rand(111111, 999999);
            $OfflinePayoutApi->retailer_id     = Auth::user()->_id;
            $OfflinePayoutApi->outlet_id       = Auth::user()->outlet_id;
            $OfflinePayoutApi->mobile_number   = Auth::user()->mobile_number;
            $OfflinePayoutApi->sender_name     = Auth::user()->full_name;
            $OfflinePayoutApi->amount          = $request->amount;
            $OfflinePayoutApi->transaction_fees = $charges;
            $OfflinePayoutApi->receiver_name   = $request->receiver_name;
            $OfflinePayoutApi->payment_mode    = $request->payment_mode;
            $OfflinePayoutApi->payment_channel = $request->payment_channel;
            $OfflinePayoutApi->status          = 'pending';

            if (!$OfflinePayoutApi->save())
                return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }



    public function bulkPayout(OfflineBulkPayoutValidation $request)
    {

        try {

            foreach ($request->all() as $res) {
                $request = (object)$res;

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
                    return response(['status' => 'error', 'msg' => 'Some Transaction Request not Created, Because you have not Sufficient Amount']);
                /*end check amount available in wallet or not*/

                $OfflinePayoutApi = new OfflinePayoutApi();
                $OfflinePayoutApi->transaction_id  = uniqCode(3) . rand(111111, 999999);
                $OfflinePayoutApi->retailer_id     = Auth::user()->_id;
                $OfflinePayoutApi->outlet_id       = Auth::user()->outlet_id;
                $OfflinePayoutApi->mobile_number   = Auth::user()->mobile_number;
                $OfflinePayoutApi->sender_name     = Auth::user()->full_name;
                $OfflinePayoutApi->amount          = $request->amount;
                $OfflinePayoutApi->transaction_fees= $charges;
                $OfflinePayoutApi->receiver_name   = $request->receiver_name;
                $OfflinePayoutApi->payment_mode    = $request->payment_mode;
                $OfflinePayoutApi->payment_channel = $request->payment_channel;
                $OfflinePayoutApi->status          = 'pending';

                if(!$OfflinePayoutApi->save())
                 return response(['status' => 'error', 'msg' => 'Somthing went wrong, Transaction Request not Created!']);

                 //update toupup amount here
                if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                 return response(['status' => 'error', 'msg' => 'Something went wrong!']);
            }

                return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);

        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
