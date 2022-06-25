<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        try {
            $data['total_outlet']  = Outlet::count();

            //for outlet amount
            $outlets = Outlet::select('amount', 'outlet_name', '_id')->get();
            $oids = [];
            foreach ($outlets as $am) {
                $oids[] = $am->_id;
            }
            // $data['oids'] = $oids;

            $users = User::select('available_amount', 'outlet_id')->whereIn('outlet_id', $oids)->get();
            $available_amount = 0;
            $retailers_ids = [];
            foreach ($users as $user) {
                $available_amount += $user->available_amount;
                $retailers_ids[] = $user->_id;
            }
            $data['total_outlet_amount'] = $available_amount;


            $que = Transaction::where('status', 'pending');
            if (!empty($request->mode))
                $que->where('payment_mode', $request->mode);

            if ($request->outlet_id)
                $que->where('outlet_id', $request->outlet_id);

            $min_amount = $request->min_amount;
            $max_amount = $request->max_amount;
            if (!empty($min_amount) && !empty($max_amount)) {
                $que->where('amount', '>=', "$min_amount");
                $que->where('amount', '<=', "$max_amount");
            }


            $data['transaction']  = $que->orderBy('created', 'DESC')->with(['OutletName','UserName'])->get();

            $data['mode'] = $request->mode;
            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            Session::forget('previewTransaction');
            Session::forget('transaction_ids');


            $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
            $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            // $start_date = strtotime('-50days',$start_date);

            $allTopup = Topup::select('amount')->whereIn('retailer_id', $retailers_ids)->whereBetween('created', [$start_date, $end_date])->get();

            $alltopup = 0;
            $ac_topup = 0;
            foreach ($allTopup as $topup) {
                $alltopup += (int)$topup->amount;
                ++$ac_topup;
            }
            $data['total_topup'] = $alltopup;
            $data['ac_topup']   = $ac_topup;

            $pendingTopup = Topup::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'pending')->whereBetween('created', [$start_date, $end_date])->get();
            $ptopup = 0;
            $pc_topup = 0;
            foreach ($pendingTopup as $topup) {
                $ptopup +=  (int)$topup->amount;
                ++$pc_topup;
            }
            $data['p_topup'] = $ptopup;
            $data['pc_topup'] = $pc_topup;

            $rejectedTopup = Topup::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'rejected')->whereBetween('created', [$start_date, $end_date])->get();
            $rtopup = 0;
            $rc_topup = 0;
            foreach ($rejectedTopup as $topup) {
                $rtopup +=  (int)$topup->amount;
                ++$rc_topup;
            }
            $data['r_topup'] = $rtopup;
            $data['rc_topup'] = $rc_topup;


            $approveTopup = Topup::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'success')->whereBetween('created', [$start_date, $end_date])->get();
            $atopup = 0;
            $apc_topup = 0;
            foreach ($approveTopup as $topup) {
                $atopup +=  (int)$topup->amount;
                ++$apc_topup;
            }
            $data['a_topup'] = $atopup;
            $data['apc_topup'] = $apc_topup;

            $allTransacation = Transaction::select('amount')->whereIn('retailer_id', $retailers_ids)->whereBetween('created', [$start_date, $end_date])->get();
            $allTrans = 0;
            $ac_trans = 0;
            foreach ($allTransacation as $trans) {
                $allTrans += (int)$trans->amount;
                ++$ac_trans;
            }
            $data['total_trans'] = $allTrans;
            $data['ac_trans'] = $ac_trans;

            $pendingTrnasaction = Transaction::select('amount')->whereIn('retailer_id', $retailers_ids)->whereIn('status', ['pending', 'process'])->whereBetween('created', [$start_date, $end_date])->get();
            $pTrans = 0;
            $pc_trans = 0;
            foreach ($pendingTrnasaction as $trans) {
                $pTrans +=  (int)$trans->amount;
                ++$pc_trans;
            }
            $data['p_trans'] = $pTrans;
            $data['pc_trans'] = $pc_trans;

            $failedTrnasaction = Transaction::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'failed')->whereBetween('created', [$start_date, $end_date])->get();
            $fTrans = 0;
            $fc_trans = 0;
            foreach ($failedTrnasaction as $trans) {
                $fTrans +=  (int)$trans->amount;
                ++$fc_trans;
            }
            $data['f_trans'] = $fTrans;
            $data['fc_trans'] = $fc_trans;

            $rejectedTrnasaction = Transaction::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'rejected')->whereBetween('created', [$start_date, $end_date])->get();
            $rTrans = 0;
            $rc_trans = 0;
            foreach ($rejectedTrnasaction as $trans) {
                $rTrans +=  (int)$trans->amount;
                ++$rc_trans;
            }
            $data['r_trans'] = $rTrans;
            $data['rc_trans'] = $rc_trans;

            $approveTransaction = Transaction::select('amount')->whereIn('retailer_id', $retailers_ids)->where('status', 'success')->whereBetween('created', [$start_date, $end_date])->get();
            $aTrans = 0;
            $apc_trans = 0;
            foreach ($approveTransaction as $trans) {
                $aTrans +=  (int)$trans->amount;
                ++$apc_trans;
            }
            $data['a_trans'] = $aTrans;
            $data['apc_trans'] = $apc_trans;

            $data['outlets'] = $outlets;
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

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

                            if ($transaction->status == 'success') {
                                /*start save employee Commission functionality*/
                                $empCmsg = getEmpCommision($transaction->outlet_id, $transaction->amount);
                                if (!empty($empCmsg)) {
                                    $employeeCms = [
                                        'employee_id'    => $empCmsg['employee_id'],
                                        'amount'         => $empCmsg['amount'],
                                        'transaction_id' => $transaction->_id,
                                        'outlet_id'      => $transaction->outlet_id,
                                        'retailer_id'    => $transaction->retailer_id,
                                        'action_by'      => Auth::user()->_id
                                    ];
                                    employeeCms($employeeCms);
                                }
                                /*end save employee Commission functionality*/
                            }
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
