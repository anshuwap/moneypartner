<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\Transaction\CustomerTrans;
use App\Models\Transaction\RetailerTrans;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        try {

            $topups = Topup::where('status', 'pending')->get();
            $data['topup_request'] = $topups;

            $data['total_outlet']  = Outlet::count();

            //for outlet amount
            $outlets = Outlet::select('amount')->get();
            $oids =[];
            foreach ($outlets as $am) {
                $oids[] = $am->_id;
            }
            // $data['oids'] = $oids;

            $users = User::select('available_amount')->whereIn('outlet_id', $oids)->get();
            $available_amount = 0;
            foreach ($users as $user) {
           $available_amount += $user->available_amount;
            }
            $data['total_outlet_amount'] = $available_amount;


            //amout for current month
            $dmts = CustomerTrans::select('total_amount')->get();
            $current_month_dmt_amount = 0;
            foreach ($dmts as $am) {
                $current_month_dmt_amount += $am->total_amount;
            }
            $data['current_month_dmt_amount']   = $current_month_dmt_amount;

            //for bulk amount
            $bulks = RetailerTrans::select('total_amount')->get();
            $current_month_bulk_amount = 0;
            foreach ($bulks as $am) {
                $current_month_bulk_amount += $am->total_amount;
            }
            $data['current_month_bulk_amount']  = $current_month_bulk_amount;


            //for dmt amount
            $dmts = CustomerTrans::select('total_amount')->get();
            $total_dmt_amount = 0;
            foreach ($dmts as $am) {
                $total_dmt_amount += $am->total_amount;
            }
            $data['total_dmt_amount']   = $total_dmt_amount;

            //for bulk amount
            $bulks = RetailerTrans::select('total_amount')->get();
            $total_bulk_amount = 0;
            foreach ($bulks as $am) {
                $total_bulk_amount += $am->total_amount;
            }
            $data['total_bulk_amount']  = $total_bulk_amount;

            $que = Transaction::where('status', 'pending');
            if (!empty($request->mode))
                $que->where('payment_mode', $request->mode);

            $data['transaction']  = $que->orderBy('created', 'DESC')->get();
            $data['mode'] = $request->mode;
            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            Session::forget('previewTransaction');
            Session::forget('transaction_ids');

            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }



    public function export(Request $request)
    {
        try {
            $file_name = 'transaction-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Has ID', 'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'Channel', 'UTR Number', 'Status', 'Datetime'
            ];

            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::where('status', 'pending');
            $transactions = $query->orderBy('created', 'DESC')->get();

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;

                $transaction_val[] = $transaction->_id;
                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = ucwords($transaction->sender_name);
                $transaction_val[] = $transaction->mobile_number;
                $transaction_val[] = ucwords(str_replace('_', ' ', $transaction->type));
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $payment->upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                $transaction_val[] = (!empty($transaction->response['utr_number'])) ? $transaction->response['utr_number'] : '';
                $transaction_val[] = strtoupper($transaction->status);
                $transaction_val[] = date('Y-m-d H:i:s A', $transaction->created);

                $transactionArr = $transaction_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $transaction_val = [];
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


    public function import()
    {
        try {
            $filename = $_FILES['file']['name'];

            if (!empty($filename)) {
                $file = fopen($_FILES['file']['tmp_name'], "r");
                $ctr = 1;
                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($ctr != 1) {
                        if (!empty($getData[11]) && !empty($getData[12])) {
                            $response = ['payment_mode' => $getData[11], 'utr_number' => $getData[12], 'msg' => 'This is Approved.'];
                            $transaction = Transaction::find($getData[0]);
                            $transaction->status       = 'success';
                            $transaction->response     =  $response;
                            $transaction->save();
                        }
                    }
                    $ctr++;
                }
                return response(['status' => 'success', 'msg' => 'Status Updated Succcessfully!']);
            }
            return response(['status' => 'error', 'msg' => 'File not Found.']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function serverError()
    {

        return view('admin.500');
    }
}
