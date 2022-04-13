<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use App\Models\TransactionComment;
use App\Support\OdnimoPaymentApi;
use App\Support\PaymentApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('user_id', Auth::user()->_id)->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = Transaction::query();

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->mode))
                $query->where('payment_mode', $request->mode);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->account_no))
                $query->where('payment_channel.account_number', $request->account_no);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            $query->whereBetween('created', [$start_date, $end_date]);

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['transaction'] = $query->whereIn('retailer_id',Auth::user()->retailers)->where('status', '!=', 'pending')->orderBy('created', 'DESC')->paginate($perPage);
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('distributor.transaction.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        $transaction = Transaction::find($request->trans_id);

        if ($transaction->status == 'rejected')
            return response(['status' => 'error', 'msg' => 'This status is already Rejected!']);

        $transaction->status       = $request->status;
        $transaction->response     = $request->response;

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

        if ($transaction->type == 'payout_api')
            webhook($transaction);

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
        } else if ($transaction->status == 'rejected') {
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
            transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit');
        }
        return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($transaction->status) . ' Successfully!']);
    }


    public function bulkAction(Request $request)
    {

        if (empty($request->trans_id))
            return response(['status' => 'error', 'msg' => 'Please select any transaction record!']);

        foreach (explode(',', $request->trans_id) as $tran_id) {
            $transaction = Transaction::find($tran_id);

            /*start transafer functionality*/
            $payment = (object)$transaction->payment_channel;

            $payment_para = [
                'mobile_number' => $transaction->mobile_number,
                'account_number' => $payment->account_number,
                'ifsc_code'     => $payment->ifsc_code,
                'amount'        => $transaction->amount,
                'receiver_name' => $transaction->receiver_name,
                'bank_name'     => $payment->bank_name,
            ];
            $payment_api = new PaymentApi();

            $api_status = 'pending';
            if ($request->api == 'payunie_preet_kumar') //for PREET KUMAR
                $res = $payment_api->payunie($payment_para);

            if ($request->api == 'payunie_rashid_ali')
                $res = $payment_api->payunie1($payment_para);

            if ($request->api == 'pay2all') {
                // return response(['status' => 'error', 'msg' => 'This Api Functionality is Under Working!']);
                $res = $payment_api->pay2all($payment_para);
            }
            if ($request->api == 'odnimo') {
                $OdnimoPaymentApi = new OdnimoPaymentApi();
                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
            }

            if (!empty($res) && $res['status'] == 'error')
                // return response(['status' => 'error', 'msg' => $res['msg']]);

                $response = [];
            if (!empty($res)) {
                $response = $res['response'];
                $api_status = $res['status'];
            }
            /*start transafer functionality*/

            $transaction->status       = $api_status;
            $transaction->response     = $response;

            if (!$transaction->save())
                return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

            if ($transaction->type == 'payout_api')
                webhook($transaction);
        }
        return response(['status' => 'success', 'msg' => 'Transaction Made Successfully!']);
    }


    public function storeApi(Request $request)
    {

        $transaction = Transaction::find($request->trans_id);

        /*start transafer functionality*/
        if ($request->type == 'api') {

            $payment = (object)$transaction->payment_channel;

            $payment_para = [
                'mobile_number' => $transaction->mobile_number,
                'account_number' => $payment->account_number,
                'ifsc_code'     => $payment->ifsc_code,
                'amount'        => $transaction->amount,
                'receiver_name' => $transaction->receiver_name,
                'bank_name'     => $payment->bank_name,
            ];
            $payment_api = new PaymentApi();

            $api_status = 'pending';
            if ($request->api == 'payunie_preet_kumar') //for PREET KUMAR
                $res = $payment_api->payunie($payment_para);

            if ($request->api == 'payunie_rashid_ali')
                $res = $payment_api->payunie1($payment_para);

            if ($request->api == 'pay2all') {
                $res = $payment_api->pay2all($payment_para);
                // return response(['status' => 'error', 'msg' => 'This Api Functionality is Under Working!']);
            }
            if ($request->api == 'odnimo') {
                $OdnimoPaymentApi = new OdnimoPaymentApi();
                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
            }

            if (!empty($res) && ($res['status'] == 'error' || $res['status'] == 'process'))
                return response(['status' => 'error', 'msg' => $res['response']['msg']]);

            $response = [];
            if (!empty($res)) {
                $response = $res['response'];
                $api_status = $res['status'];
            }
        }
        /*start transafer functionality*/

        $transaction->status       = $api_status;
        $transaction->response     = $response;
        // $transaction->admin_action = [];

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

        if ($transaction->type == 'payout_api')
            webhook($transaction);

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
        } else if ($transaction->status == 'rejected') {
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
            transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit');
        }
        return response(['status' => 'success', 'msg' => 'Transaction Made Successfully!']);
    }

    public function viewDetail(Request $request)
    {

        try {
            $details = Transaction::find($request->id);

            $table = '<table class="table table-sm">

                <tr>
                <td>Transaction Id :</td>
                <td>' . $details->transaction_id . '</td>
                </tr>

                <tr>
                <td>Sender Name :</td>
                <td>' . $details->sender_name . '</td>
                </tr>';

            if (!empty($details->mobile_number))
                $table .= '<tr>
                <td>Mobile Nmber :</td>
                <td>' . $details->mobile_number . '</td>
                </tr>';

            $clicka = "copyToClipboard('#text-t','#copy-t')";
            $amount = (int)$details->amount;

            $table .= '<tr><th>Amount :</th>
                <td><span class="text-success text" id="text-t">' . $amount . '</span>&nbsp;&nbsp;
                <span><a href="javascript:void(0);" onClick="' . $clicka . '" class="text-danger"><i class="fas fa-copy"></i></a></span>
                <span class="ml-4 d-none" id="copy-t"><i class="fas fa-check-circle text-success"></i>Copied</span></td></tr>

               <tr><td>Transaction Fees :</td>
                <td>' . mSign($details->transaction_fees) . '</td>
                </tr>

                <tr><td>Receiver Name :</td>
                <td>' . $details->receiver_name . '</td>
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

            // $clicka = "copyToClipboard('#text-t','#copy-t')";
            $total_amount = (int)$details->amount + $details->transaction_fees;
            $table .= '<td>Total Amount :</td>
                <td>' . $total_amount . '
                </tr>';

            $table .= ' <td>Mode:</td>
                <td><span class="tag-small">' . ucwords(str_replace('_', ' ', $details->type)) . '</span></td>
                </tr>';

            switch ($details->status) {
                case 'success':
                    $status = '<span class="badge badge-success">' . ucwords($details->status) . '</span>';
                    break;
                case 'reject':
                    $status = '<span class="badge badge-danger">' . ucwords($details->status) . '</span>';
                    break;
                case 'failed':
                    $status = '<span class="badge badge-danger">' . ucwords($details->status) . '</span>';
                case 'process':
                    $status = '<span class="badge badge-info">' . ucwords($details->status) . '</span>';
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


            $table .= ' <td>Created Date:</td>
                <td>' . date('d M Y H:i', $details->created) . '</td>
                </tr>';

            if ($details->status == 'failed' || $details->status == 'process' || $details->status == 'pending')
                $table .= '<td>Action:</td>
                <td>
                        <a href="javascript:void(0);" payment_mode="' . $details->payment_mode . '" class="btn btn-danger btn-xs retailer_trans" _id="' . $details->_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>
                </td>';

            // $update_utr = '';
            // if (
            //     !empty($details->response['payment_mode']) && $details->response['payment_mode'] != 'payunie-Preet Kumar'
            //     && $details->response['payment_mode'] != 'payunie-Rashid Ali' &&  $details->response['payment_mode'] != 'Pay2All-Parveen' &&
            //     $details->response['payment_mode'] != 'Odnimo - api'
            // ) {
            //     $update_utr = '<a href="javascript:void(0);" class="btn btn-xs btn-success utrupdate"><i class="fas fa-edit"></i>&nbsp;Edit UTR</a>';
            // }
            // if ($details->status == 'success')
            //     $table .= '<td>Action:</td>
            //     <td>
            //             <a href="javascript:void(0);" payment_mode="' . $details->payment_mode . '" class="btn btn-danger btn-xs success-action" _id="' . $details->_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>
            //            ' . $update_utr . '
            //     </td>';

            // $table .= '</table>';

            $utr_no = !empty($details->response['utr_number']) ? $details->response['utr_number'] : '';

            if (
                !empty($details->response['payment_mode']) && $details->response['payment_mode'] != 'payunie-Preet Kumar'
                && $details->response['payment_mode'] != 'payunie-Rashid Ali' &&  $details->response['payment_mode'] != 'Pay2All-Parveen' &&
                $details->response['payment_mode'] != 'Odnimo - api'
            ) {

                $table .= '<div class="row" style="display:none;" id="utr">
                    <div class="col-md-12 form-group">
                        <label>Update UTR No.</label>
                        <input type="hidden" value="' . $details->_id . '" id="trnsaction-id" name="id">
                        <div class="input-group input-group-sm">
                            <input type="text" value="' . $utr_no . '" name="utr_no" id="utr_no" class="form-control form-control-sm">
                            <span class="input-group-append ">
                                <button type="button" class="btn btn-danger btn-flat" id="update_utr">Update</button>
                            </span>
                        </div>
                    </div>
                </div>';
            }

            die(json_encode(['table' => $table, 'id' => $details->_id]));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'something went wrong']);
        }
    }


    public function updateUtrNo(Request $request)
    {
        try{
        $transaction_id = $request->id;
        $utr = $request->utr;
        $transaction = Transaction::find($transaction_id);

        if(!empty($transaction->response))
        $response = $transaction->response;
        $response['utr_number'] = $utr;
        $transaction->response = $response;

        if($transaction->save())
         return response(['status' => 'success', 'msg' => 'UTR No updated successfully!']);

          return response(['status' => 'error', 'msg' => 'UTR No not updated!']);
          } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
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


    public function ChangeChannel(Request $request)
    {
        try {

            $transaction_id = $request->id;
            $transaction = Transaction::find($transaction_id);

            $exist_response = $transaction->response;
            $exist_response['payment_mode'] = $request->channel;
            $transaction->response = $exist_response;
            if ($transaction->save())
                return response(['status' => 'success', 'msg' => 'Channel changed successfully!']);

            return response(['status' => 'error', 'msg' => 'Channel not changed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }



    public function paymentStatus(Request $request)
    {
        try {
            $transaction = Transaction::find($request->_id);
            $response = $transaction->response;

            if (!empty($response['transaction_id']) || !empty($response['client_id'])) {
                $tran_id = (!empty($response['transaction_id'])) ? $response['transaction_id'] : 0;

                $client_id = (!empty($response['client_id'])) ? $response['client_id'] : 0;

                $paymentApi = new PaymentApi();

                $type = $request->type;
                if ($type === 'payunie-Preet Kumar') {
                    $res = $paymentApi->checkStatusPayunie($tran_id);
                } else if ($type === 'payunie-Rashid Ali') {
                    $res = $paymentApi->checkStatusPayunie1($tran_id);
                } else if ($type === 'Pay2ALL-Parveen') {
                    $res = $paymentApi->checkStatusPay2All($client_id);
                }

                $response =  $response;
                if (!empty($res)) {
                    if (!empty($res['response']['utr_number']))
                        $response['utr_number'] = $res['response']['utr_number'];

                    $response['msg'] = $res['response']['msg'];
                    $response['payment_mode'] = $res['response']['payment_mode'];
                    $api_status = $res['status'];
                }

                $transaction->response = $response;
                $transaction->status   = $api_status;
                if ($transaction->save())
                    return response(['status' => 'success']);
            }
            return response(['status' => 'error', 'msg' => 'Transaction Id not Found!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function checkBulkStatus()
    {
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
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Datetime'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query();

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            $start_date1 = $request->start_date;
            $end_date1   = $request->end_date;

            if (!empty($start_date1) && !empty($end_date1)) {
                $start_date = strtotime(trim($start_date1) . " 00:00:00");
                $end_date   = strtotime(trim($end_date1) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            $query->whereBetween('created', [$start_date, $end_date]);

            $transactions = $query->whereIn('retailer_id',Auth::user()->retailers)->orderBy('created', 'DESC')->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;

                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = ucwords($transaction->sender_name);
                $transaction_val[] = $transaction->mobile_number;
                $transaction_val[] = ucwords(str_replace('_', ' ', $transaction->type));
                $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $payment->upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
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
}
