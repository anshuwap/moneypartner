<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Recharge;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data['recharges'] = Recharge::where('retailer_id', Auth::user()->_id)->get();
            return view('retailer.services.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function Mrecharge(Request $request)
    {
        $outlet = Outlet::select('recharge_charges', 'security_amount')->where('_id', Auth::user()->outlet_id)->first();
        $charges = 0;
        $amount = $request->amount;
        if (!empty($outlet->recharge_charges)) {
            foreach ($outlet->recharge_charges as $charge) {
                if ($charge['type'] == 'inr') {

                    if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                        $charges = $charge['charges'];
                        break;
                    }
                } else {
                    return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
                }
            }
        }
        $total_amount = $amount - $charges;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://jskfintech.com/Api/Service/Recharge2?ApiToken=a4d83388-05ae-486d-9e5d-4591e65e56ad&MobileNo=' . $request->mobile_no . '&Amount=' . $request->amount . '&OpId=5&RefTxnId=' . rand(11111, 99999),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if (!empty($res->STATUS) && $res->STATUS == 1 && $res->HTTPCODE === 200) {
            $status = 'success';
            $msg = $res->MESSAGE;
        } else if (!empty($res->STATUS) && $res->STATUS == 1) {
            $status = 'failed';
            $msg = $res->MESSAGE;
        } else {
            $status = 'failed';
            $msg = 'Something went wrong in Api';
        }

        $recharge = new Recharge();
        $recharge->txn_id = 'REC' . rand(111111, 999999);
        $recharge->retailer_id  = Auth::user()->_id;
        $recharge->operator = $request->operator;
        $recharge->mobile_no = $request->mobile_no;
        $recharge->commission_fees = $charges;
        $recharge->amount = $request->amount;
        $recharge->paid_amount = $total_amount;
        $recharge->status = $status;
        $recharge->msg = $msg;
        $recharge->api_response = $res;
        if ($recharge->save()) {
            return response(['status' => 'success', 'msg' => 'Recharge Completed.']);
            //update toupup amount here
            // if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
            //     return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            // /*start passbook debit functionality*/
            // $transaction_id   = $recharge->_id;
            // $amount           = $recharge->amount;
            // $receiver_name    = $recharge->receiver_name;
            // $payment_date     = $recharge->created;
            // $status           = 'success';
            // $payment_mode     = $recharge->payment_mode;
            // $transaction_fees = $recharge->transaction_fees;
            // $type             = $recharge->type;
            // $retailer_id      = $recharge->retailer_id;

            // transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $transaction_id);
            /*end passbook debit functionality*/
        } else {
            return response(['status' => 'error', 'msg' => 'Recharge Failed.']);
        }
    }
}
