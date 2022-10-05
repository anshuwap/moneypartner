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
            $query = Recharge::where('retailer_id', Auth::user()->_id);
            if (!empty($request->transaction_id))
                $query->where('txn_id', $request->transaction_id);

            if (!empty($request->mobile_no))
                $query->where('mobile_no', $request->mobile_no);

            if (!empty($request->operator))
                $query->where('operator', $request->operator);

            if (!empty($request->status))
                $query->where('status', $request->status);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            } else {
                // $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                // $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            // $query->whereBetween('created', [$start_date, $end_date]);
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['recharges'] = $query->orderBy('created', 'DESC')->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

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



    public function serviceExport(Request $request)
    {
        try {
            $file_name = 'recharge-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Recharge Amount', 'Commission Amount', 'Paid Amount', 'Operator', 'Mobile/DTH No',
                'Requested Date',
                'Status',
                'Message'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Recharge::query();
            if (!empty($request->transaction_id))
                $query->where('txn_id', $request->transaction_id);

            if (!empty($request->mobile_no))
                $query->where('mobile_no', $request->mobile_no);

            if (!empty($request->operator))
                $query->where('operator', $request->operator);

            if (!empty($request->status))
                $query->where('status', $request->status);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            } else {
                // $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                // $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            // $query->whereBetween('created', [$start_date, $end_date]);
            $recharges = $query->orderBy('created', 'DESC')->get();

            if ($recharges->isEmpty())
                return back()->with('error', 'There is no any record for export!');;

            $transactionArr = [];
            foreach ($recharges as $val) {

                $r_val[] = $val->txn_id;
                $r_val[] = $val->amount;
                $r_val[] = $val->commission_fees;
                $r_val[] = $val->paid_amount;
                $r_val[] = checkOperator($val->operator);
                $r_val[] = $val->mobile_no;
                $r_val[] = date('Y-m-d H:i:s', $val->payment_date);
                $r_val[] = strtoupper($val->status);
                $r_val[] = $val->msg;
                $transactionArr = $r_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $r_val = [];
            }
            // Move back to beginning of file
            fseek($f, 0);

            // headers to download file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
            readfile('exportCsv/' . $file_name . '.csv');

            //remove file form server
            $path = 'exportCsv/' . $file_name . '.csv';
            if (file_exists($path))
                unlink($path);
        } catch (Exception $e) {
            return redirect('500');
        }
    }
}