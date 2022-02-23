<?php

namespace App\Http\Controllers\Retailer\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Api\OfflinePayoutApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflinePayoutApiController extends Controller
{

    public function index()
    {
        try {
            $data['retailerTrans'] = OfflinePayoutApi::where('retailer_id', Auth::user()->_id)->get();
            return view('retailer.transaction.offline_payout_api', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
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

            $res = $this->pushRequest($request);
            if ($res)
                return response(['status' => 'error', 'msg' => $res[0]->statusMessage]);

            $payment_channel = [
                'bank_name'      => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code'      => $request->ifsc_code
            ];
            $OfflinePayoutApi = new OfflinePayoutApi();
            $OfflinePayoutApi->transaction_id  = uniqCode(3) . rand(111111, 999999);
            $OfflinePayoutApi->retailer_id     = Auth::user()->_id;
            $OfflinePayoutApi->outlet_id       = Auth::user()->outlet_id;
            $OfflinePayoutApi->mobile_number   = Auth::user()->mobile_number;
            $OfflinePayoutApi->sender_name     = Auth::user()->full_name;
            $OfflinePayoutApi->amount          = $request->amount;
            $OfflinePayoutApi->transaction_fees= $charges;
            $OfflinePayoutApi->receiver_name   = $request->receiver_name;
            $OfflinePayoutApi->payment_mode    = 'bank_account';
            $OfflinePayoutApi->payment_channel = $payment_channel;
            $OfflinePayoutApi->status          = 'approved';

            if (!$OfflinePayoutApi->save())
                return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            //for genrate passbook
            if ($OfflinePayoutApi->status == 'approved') {
                $amount        = $OfflinePayoutApi->amount;
                $receiver_name = $OfflinePayoutApi->receiver_name;
                $payment_date  = $OfflinePayoutApi->created;
                $status        = $OfflinePayoutApi->status;
                $payment_mode  = $OfflinePayoutApi->payment_mode;
                $transaction_fees = $OfflinePayoutApi->transaction_fees;

                $retailer_id   = $OfflinePayoutApi->retailer_id;

                //insert data in transfer history collection
                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $transaction_fees, 'debit');
            }

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function pushRequest($input)
    {

        $post_data   = array(
            'key'           => 'vPxce3A8W23XTokxvBbj34Co',
            'AccountNumber' => $input->account_number,
            'IFSC'          => $input->ifsc_code,
            'Amount'        => $input->amount,
            'HolderName'    => $input->receiver_name,
            'BankName'      => $input->bank_name,
            'TransactionID' => uniqCode(7)
        );

        $url = "https://payunie.com/api/v1/payout";

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $http_result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($http_result);
        return $result;
    }
}
