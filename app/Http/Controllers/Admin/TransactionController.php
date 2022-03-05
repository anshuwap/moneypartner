<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use App\Models\TransactionComment;
use App\Support\PaymentApi;
use Exception;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'ASC')->get();
            $outlet_id = $request->outlet_id;
            if (empty($request->outlet_id))
                $outlet_id = $outlets[0]->_id;

            $query = Transaction::query()->where('outlet_id', $outlet_id);

            if ($request->outlet_id == 'all')
                $query = Transaction::query();

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->mode))
                $query->where('payment_mode', $request->mode);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            $start_date = '';
            $end_date   = '';
            if (!empty($request->date_range)) {
                $date = explode('-', $request->date_range);
                $start_date = $date[0];
                $end_date   = $date[1];
            }
            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date   = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $query->whereBetween('created', [$start_date, $end_date]);

            $data['transaction'] = $query->get();
            $data['outlets']   = $outlets;
            $data['outlet_id'] = $outlet_id;
            $request->request->remove('page');
            $data['filter']  = $request->all();

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('admin.transaction.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        $transaction = Transaction::find($request->trans_id);
        $transaction->status       = $request->status;
        $transaction->response     = $request->response;

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

        if ($transaction->status == 'success') {
            // $amount        = $transaction->amount;
            // $receiver_name = $transaction->receiver_name;
            // $payment_date  = $transaction->created;
            // $status        = $transaction->status;
            // $payment_mode  = $transaction->payment_mode;
            // $type          = $transaction->type;
            // $transaction_fees = $transaction->transaction_fees;

            // $retailer_id   = $transaction->retailer_id;

            // //insert data in transfer history collection
            // transferHistory($retailer_id, $amount, $receiver_name, $payment_date,$type, $status, $payment_mode, $transaction_fees, 'debit');
        } else if($transaction->status == 'rejected'){
            //add toupup amount here
            $receiver_name    = $transaction->receiver_name;
            $payment_date     = $transaction->created;
            $status           = 'success';
            $payment_mode     = $transaction->payment_mode;
            $type             = 'refund';
            $retailer_id      = $transaction->retailer_id;
            $transaction_fees = $transaction->transaction_fees;
            $amount           = $transaction->amount;
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
            //insert data in transfer history collection
            transferHistory($retailer_id, $amount+$transaction_fees, $receiver_name, $payment_date,$status, $payment_mode, $type, 0, 'credit');
        }
        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($transaction->status) . ' Successfully!']);
    }


    public function storeApi(Request $request)
    {

        $transaction = Transaction::find($request->trans_id);

        /*start transafer functionality*/
        if ($request->type == 'api') {

            $payment = (object)$transaction->payment_channel;

            $payment_para = [
                'account_number'=> $payment->account_number,
                'ifsc_code'     => $payment->ifsc_code,
                'amount'        => $transaction->amount,
                'receiver_name' => $transaction->receiver_name,
                'bank_name'     => $payment->bank_name,
            ];
            $payment_api = new PaymentApi();

            $api_status = 'pending';
            if ($request->api == 'payunie_preet_kumar')//for PREET KUMAR
                $res = $payment_api->payunie($payment_para);

            if ($request->api == 'payunie_parveen')
                $res = $payment_api->payunie1($payment_para);

            if (!empty($res) && $res['status'] == 'error')
                    return response(['status' => 'error', 'msg' => $res['msg']]);

                $response = [];
                if (!empty($res) && $res['status'] == 'success') {
                    $response = $res['response'];
                    $api_status = $res['status'];
                }
        }
        /*start transafer functionality*/

        $transaction->status       = $api_status;
        $transaction->response     = $response;
        // $transaction->admin_action = [];

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

        if ($transaction->status == 'success') {
            // $amount        = $transaction->amount;
            // $receiver_name = $transaction->receiver_name;
            // $payment_date  = $transaction->created;
            // $status        = $transaction->status;
            // $payment_mode  = $transaction->payment_mode;
            // $transaction_fees = $transaction->transaction_fees;
            // $type           = $transaction->type;

            // $retailer_id   = $transaction->retailer_id;

            // //insert data in transfer history collection
            // transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode,$type, $transaction_fees, 'debit');
        } else if($transaction->status == 'rejected') {
            //add toupup amount here
            $receiver_name    = $transaction->receiver_name;
            $payment_date     = $transaction->created;
            $status           = 'success';
            $payment_mode     = $transaction->payment_mode;
            $type             = 'refund';
            $retailer_id      = $transaction->retailer_id;
            $transaction_fees = $transaction->transaction_fees;
            $amount           = $transaction->amount;
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
            //insert data in transfer history collection
            transferHistory($retailer_id, $amount+$transaction_fees, $receiver_name, $payment_date,$status, $payment_mode,$type,0, 'credit');
        }
        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($transaction->status) . ' Successfully!']);
    }

    public function viewDetail(Request $request)
    {

        try {
            $details = Transaction::find($request->id);

            $table = '<table class="table table-sm">
                <tr>
                <td>Sender Name :</td>
                <td>' . $details->sender_name . '</td>
                </tr>';

            if (!empty($details->mobile_number))
                $table .= '<tr>
                <td>Mobile Nmber :</td>
                <td>' . $details->mobile_number . '</td>
                </tr>';

            $table .= '<td>Amount :</td>
                <td>' . mSign($details->amount) . '</td>
                </tr>

                <td>Transaction Fees :</td>
                <td>' . mSign($details->transaction_fees) . '</td>
                </tr>

                <td>Receiver Name :</td>
                <td>' . $details->receiver_name . '</td>
                </tr>

                <td>Payment Mode :</td>
                <td>' . ucwords(str_replace('_', ' ', $details->payment_mode)) . '</td>
                </tr>';

            $payment = $details->payment_channel;
            $i = 1;
            foreach ($payment as $key => $paym) {
                $click = "copyToClipboard('#text-" . $i . "','#copy-" . $i . "')";
                $table .= '<th>' . ucwords(trim(str_replace('_', ' ', $key), "'")) . ' :</th>
                <td><span class="text-success text" id="text-' . $i . '">' . $paym . '</span>&nbsp;&nbsp;
                <span><a href="javascript:void(0);" onClick="' . $click . '" class="text-danger"><i class="fas fa-copy"></i></a></span>
                <span class="ml-4 d-none" id="copy-' . $i . '"><i class="fas fa-check-circle text-success"></i>Copied</span></td>
                </tr>';
                $i++;
            }

            $clicka = "copyToClipboard('#text-t','#copy-t')";
            $table .= '<th>Total Amount :</th>
                <td><span class="text-success text" id="text-t">' . $details->amount + $details->transaction_fees . '</span>&nbsp;&nbsp;
                <span><a href="javascript:void(0);" onClick="' . $clicka . '" class="text-danger"><i class="fas fa-copy"></i></a></span>
                <span class="ml-4 d-none" id="copy-t"><i class="fas fa-check-circle text-success"></i>Copied</span></td>
                </tr>';

            switch ($details->status) {
                case 'success':
                    $status = '<span class="badge badge-success">' . ucwords($details->status) . '</span>';
                    break;
                case 'reject':
                    $status = '<span class="badge badge-danger">' . ucwords($details->status) . '</span>';
                    break;
                default:
                    $status = '<span class="badge badge-warning">' . ucwords($details->status) . '</span>';
                    break;
            }

            $table .= '<td>Status :</td>
                <td>' . $status . '</td>
                </tr>';

            if (!empty($details->pancard_no))
                $table .= '<td>Pancard No :</td>
                <td>' . $details->pancard_no . '</td>
                </tr>';

            if (!empty($details->pancard))
                $table .= '<td>Pancard :</td>
                <td>' . $details->pancard . '</td>
                </tr>';

            $table .= ' <td>Created :</td>
                <td>' . date('d M Y', $details->created) . '</td>
                </tr>
                </table>';

            die(json_encode($table));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'something went wrong']);
        }
    }


    public function Comment(Request $request)
    {

        $type = $request->type;
        $commments = TransactionComment::where('type', $type)->get();

        $option = '<option value="">Select</option>';
        foreach ($commments as $comment) {
            $option .= '<option value="' . $comment->comment . '">' . $comment->comment . '</option>';
        }

        die(json_encode($option));
    }


     public function export(Request $request)
    {
        try {
            $file_name = 'transaction-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = ['Transaction ID', 'UTR Number', 'Amount', 'Fees', 'Beneficiary','IFSC','Account No.','Bank Name',
            'Status','Datetime'];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'ASC')->first();
            $outlet_id = $request->outlet_id;
            if (empty($request->outlet_id))
                $outlet_id = $outlets->_id;

            $query = Transaction::query()->where('outlet_id', $outlet_id);

            if ($request->outlet_id == 'all')
                $query = Transaction::query();

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            $start_date = '';
            $end_date   = '';
            if (!empty($request->date_range)) {
                $date = explode('-', $request->date_range);
                $start_date = $date[0];
                $end_date   = $date[1];
            }
            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date   = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            }

            $query->whereBetween('created', [$start_date, $end_date]);

            $transactions = $query->get();

            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

 $payment = (object)$transaction->payment_channel;

                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = (!empty($transaction->response['utr_number']))?$transaction->response['utr_number']:'';
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code))?$payment->ifsc_code:'';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number :$payment->upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
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
}
