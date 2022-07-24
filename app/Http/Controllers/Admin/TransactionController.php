<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewTransaction;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use App\Models\TransactionComment;
use App\Support\ClicknCash;
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

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();

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

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);
            // $start_date = '';
            // $end_date   = '';
            // if (!empty($request->date_range)) {
            //     $date = explode('-', $request->date_range);
            //     $start_date = $date[0];
            //     $end_date   = $date[1];
            // }
            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            // if ($request->filter_by == 'created_date')
            //     $query->whereBetween('created', [$start_date, $end_date]);
            // else if ($request->filter_by == 'action_date')
            //     $query->whereBetween('response.action_date', [$start_date, $end_date]);
            // else
            $query->whereBetween('created', [$start_date, $end_date]);

            // else {
            //     $crrMonth = (date('Y-m-d'));
            //     $start_date = strtotime(trim(date("d-m-Y", strtotime('-30 days', strtotime($crrMonth)))) . " 00:00:00");
            //     $end_date   = strtotime(trim(date('Y-m-d')) . " 23:59:59");
            // }

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['transaction'] = $query->where('status', '!=', 'pending')->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->paginate($perPage);
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('admin.transaction.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function refundPending(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = Transaction::query()->where('status', 'refund_pending');

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->mode))
                $query->where('payment_mode', $request->mode);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->account_no))
                $query->where('payment_channel.account_number', $request->account_no);

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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
            $data['transaction'] = $query->where('status', '!=', 'pending')->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->paginate($perPage);
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('admin.transaction.refund_pending', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {
        try {
            $transaction = Transaction::find($request->trans_id);

            if ($transaction->status == 'rejected')
                return response(['status' => 'error', 'msg' => 'This status is already Rejected!']);

            $response =  $request->response;
            $response['action_by']     = Auth::user()->_id;
            $response['action_date']   = time();
            $response['action']        = 'manual update Payment Status';
            $transaction->status       = $request->status;
            $transaction->response     = $response;

            if (!$transaction->save())
                return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

            $this->updateNewTransaction($transaction->_id, $transaction->status, $transaction->response);

            if ($transaction->type == 'payout_api')
                webhook($transaction);

            if ($transaction->status == 'success') {
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
            } else if ($transaction->status == 'rejected') {
                //add toupup amount here
                $transaction_id   = $transaction->_id;
                $receiver_name    = $transaction->receiver_name;
                $payment_date     = $transaction->created;
                $status           = 'success';
                $payment_mode     = $transaction->payment_mode;
                $type             = 'refund';
                $retailer_id      = $transaction->retailer_id;
                $transaction_fees = $transaction->transaction_fees;
                $amount           = $transaction->amount;
                $bank_details     = $transaction->payment_channel;
                $source           = 'Credited By Reject Transaction';
                addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit', 0, $bank_details, $transaction_id, $source);
            }

            return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($transaction->status) . ' Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function bulkAction(Request $request)
    {
        if (empty($request->trans_id))
            return response(['status' => 'error', 'msg' => 'Please select any transaction record!']);

        $api = $request->api;

        foreach (explode(',', $request->trans_id) as $tran_id) {
            $transaction = Transaction::find($tran_id);
            $status = '<span class="tag-small-warning"><a href="javascript:void(0)" class="text-dark" data-toggle="tooltip" data-placement="bottom">' . ucwords($transaction->status) . '</a></span>';
            $outlet = '<span data-toggle="tooltip" data-placement="bottom" title="' . $transaction->transaction_id . '">' . (!empty($transaction->OutletName['outlet_name'])) ? $transaction->OutletName['outlet_name'] : '-' . '</span>';
            $mode = '<span class="tag-small">' . ucwords(str_replace('_', ' ', $transaction->type)) . '</span>';
            $previewTransaction[] = [
                'outlet' => $outlet,
                'mode' => $mode,
                'amount' => $transaction->amount,
                'beneficiary' => $transaction->receiver_name,
                'ifsc' => (!empty($transaction->payment_channel['ifsc_code'])) ? $transaction->payment_channel['ifsc_code'] : '',
                'account_no' => (!empty($transaction->payment_channel['account_number'])) ? $transaction->payment_channel['account_number'] : '',
                'status' => $status
            ];
        }
        session()->put('previewTransaction', $previewTransaction);
        session()->put('transaction_ids', explode(',', $request->trans_id));
        $dataV = '';
        if (!empty($previewTransaction))
            $dataV = self::previewData($previewTransaction);
        return response(['status' => 'preview', 'api' => $api, 'data' => $dataV]);
    }

    private function previewData($previewData)
    {
        $table_data = '<table class="table table-sm" id="preview-table">';
        $table_data .= '<tr>
        <th>Sr.No.</th>
        <th>Outlet</th>
        <th>Mode</th>
        <th>Amount</th>
        <th>Beneficiary</th>
        <th>IFSC</th>
        <th>Account No</th>
        <th colspan="2">Status</th>
       <th></th>
        </tr>';
        $total_amount = 0;
        $no = 0;
        $sr = 0;
        foreach ($previewData as $key => $da) {
            $total_amount += $da['amount'];

            $table_data .= '<tr id="fade-' . $key . '" class="preview-table">
            <td>' . ++$sr . '</td>
            <td>' . $da['outlet'] . '</td>
            <td>' . $da['mode'] . '</td>
            <td>' . mSign($da['amount']) . '</td>
            <td>' . $da['beneficiary'] . '</td>
            <td>' . $da['ifsc'] . '</td>
            <td>' . $da['account_no'] . '</td>
            <td>' . $da['status'] . '</td>
            <td><a href="javascript:void(0);" key="' . $key . '" class="btn btn-danger btn-xs remove-record">Remove</a></td>
            </tr>';
            $no++;
        }
        $table_data .= '<tr class="preview-table"><th colspan="3" style="text-align:end;">Total Amount</th>
        <th colspan="6" id="tl-amount">' . mSign($total_amount) . '</th></tr></table>';

        $data = ['table_data' => $table_data, 'total_amount' => $total_amount, 'no_of_record' => $no];
        return $data;
    }


    public function removeIndex($index = ' ')
    {
        try {

            if ($index == ' ')
                return response(['status' => 'error', 'msg' => 'invalid Index']);

            $ids = session('transaction_ids');

            $flag = 0;
            if (!empty($ids[$index])) {
                unset($ids[$index]);
                $flag = 1;
            }

            $total_amount = 0;
            $no = 0;
            foreach ($ids as $tran_id) {
                $transaction = Transaction::find($tran_id);
                $total_amount += $transaction->amount;
                $no++;
            }
            session()->put('transaction_ids', $ids);

            return response(['flag' => $flag, 'total_amount' => $total_amount, 'tl_amount' => mSign($total_amount), 'no_of_record' => $no]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function payToApi(Request $request)
    {
        // print_r($_POST);die;
        if (empty($request->api))
            return response(['status' => 'error', 'msg' => 'Please Select api!']);

        $index = $request->index;
        $transaction_ids = session('transaction_ids');

        if (empty($transaction_ids[$index])) {
            return response(['status' => 'preview', 'all_row' => count($transaction_ids) + 1, 'index' => $index + 1, 'data' => '']);
        }
        $tran_id = $transaction_ids[$index];

        if (empty($tran_id))
            return response(['status' => 'error', 'msg' => 'Invaliad Transaction!']);

        // foreach (explode(',', $request->trans_id) as $tran_id) {
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
        if ($request->api == 'clickncash') {
            $clicknCash = new ClicknCash();
            $res = $clicknCash->payout($payment_para);
        }

        // if (!empty($res) && $res['status'] == 'error')
        // return response(['status' => 'error', 'msg' => $res['msg']]);

        $response = [];
        if (!empty($res)) {
            $response = $res['response'];
            $api_status = $res['status'];
        }
        /*start transafer functionality*/
        $response['action_by']     = Auth::user()->_id;
        $response['action_date']  = time();
        $response['action']       = 'update by API';
        $transaction->status       = $api_status;
        $transaction->response     = $response;

        if ($api_status) {

            //for update new transaction status
            $this->updateNewTransaction($transaction->_id, $transaction->status, $transaction->response);

            if ($api_status == 'success') {
                $class = 'text-success';
                $class1 = 'tag-small';
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
            } else if ($api_status == 'pending') {
                $class = 'text-warning';
                $class1 = 'tag-small-warning';
            } else if ($api_status == 'process') {
                $class = 'text-warning';
                $class1 = 'tag-small-purple';
            } else {
                $class = 'text-danger';
                $class1 = 'tag-small-danger';
            }
            $msg = (!empty($response['msg'])) ? $response['msg'] : 'System error somthing went wrong';

            $comment = '<span class="' . $class . '">' . $msg . '</span>';
            $status  = '<span class="' . $class1 . '">' . ucwords(str_replace(' ', '_', $api_status)) . '</span>';
        }

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

        if ($transaction->type == 'payout_api')
            webhook($transaction);

        //  $transaction = Transaction::find($tran_id);
        $modi_index = $index + 1;
        $outlet = '<span data-toggle="tooltip" data-placement="bottom" title="' . $transaction->transaction_id . '">' . (!empty($transaction->OutletName['outlet_name'])) ? $transaction->OutletName['outlet_name'] : '-' . '</span>';
        $mode = '<span class="tag-small">' . ucwords(str_replace('_', ' ', $transaction->type)) . '</span>';
        $previewTransaction = [
            'sr_no' => $modi_index,
            'outlet' => $outlet,
            'mode' => $mode,
            'amount' => $transaction->amount,
            'beneficiary' => $transaction->receiver_name,
            'ifsc' => (!empty($transaction->payment_channel['ifsc_code'])) ? $transaction->payment_channel['ifsc_code'] : '',
            'account_no' => (!empty($transaction->payment_channel['account_number'])) ? $transaction->payment_channel['account_number'] : '',
            'status' => $status,
            'comment' => $comment,
        ];

        $dataV = '';
        if (!empty($previewTransaction))
            $dataV = self::previewData1($previewTransaction);
        return response(['status' => 'preview', 'all_row' => count($transaction_ids), 'index' => $modi_index, 'data' => $dataV]);
    }


    private function previewData1($previewData)
    {
        $table_data = '<tr class="preview-table">
            <td>' . $previewData['sr_no'] . '</td>
            <td>' . $previewData['outlet'] . '</td>
            <td>' . $previewData['mode'] . '</td>
            <td>' . $previewData['amount'] . '</td>
            <td>' . $previewData['beneficiary'] . '</td>
            <td>' . $previewData['ifsc'] . '</td>
            <td>' . $previewData['account_no'] . '</td>
            <td>' . $previewData['status'] . '</td>
            <td>' . $previewData['comment'] . '</td>
            </tr>';

        return $table_data;
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

            if ($request->api == 'clickncash') {
                $clicknCash = new ClicknCash();
                $res = $clicknCash->payout($payment_para);
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
        $response['action_by']     = Auth::user()->_id;
        $response['action_date']   = time();
        $response['action']        = 'Paid from API';
        $transaction->status       = $api_status;
        $transaction->response     = $response;
        // $transaction->admin_action = [];

        if (!$transaction->save())
            return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

        //for update new transaction status
        $this->updateNewTransaction($transaction->_id, $transaction->status, $transaction->response);

        if ($transaction->type == 'payout_api')
            webhook($transaction);

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
        } else if ($transaction->status == 'rejected') {
            //add toupup amount here
            $transaction_id   = $transaction->_id;
            $receiver_name    = $transaction->receiver_name;
            $payment_date     = $transaction->created;
            $status           = 'success';
            $payment_mode     = $transaction->payment_mode;
            $type             = 'refund';
            $retailer_id      = $transaction->retailer_id;
            $transaction_fees = $transaction->transaction_fees;
            $amount           = $transaction->amount;
            $bank_details     = $transaction->payment_channel;
            $source           = 'Credited By Reject Transaction';
            addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
            //insert data in transfer history collection
            transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit', '', $bank_details, $transaction_id, $source);
        }
        return response(['status' => 'success', 'msg' => 'Transaction Made Successfully!']);
    }

    public function viewDetail(Request $request)
    {

        try {
            $prev_url = parse_url(url()->previous());

            if (!empty($prev_url['path']) && $prev_url['path'] == '/admin/new-transaction') {
                $details = NewTransaction::find($request->id);
                $transaction1_id = $details->_id;
                $old_id = $details->old_trans_id;
            } else {
                $details = Transaction::find($request->id);
                $transaction1_id = $details->_id;
                $old_id = $details->_id;
            }

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
                        <a href="javascript:void(0);" payment_mode="' . $details->payment_mode . '" class="btn btn-danger btn-xs retailer_trans" _id="' .$transaction1_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>
                </td>';

            $update_utr = '';
            if (!empty($details->response['payment_mode']) && empty($details->splits)) {
                $update_utr = '<a href="javascript:void(0);" class="btn btn-xs btn-success utrupdate"><i class="fas fa-edit"></i>&nbsp;Edit UTR</a>';
            }

            $split = '';
            if (
                !empty($details->response['payment_mode']) && $details->response['payment_mode'] != 'payunie-Preet Kumar'
                && $details->response['payment_mode'] != 'payunie-Rashid Ali' &&  $details->response['payment_mode'] != 'Pay2All-Parveen' &&
                $details->response['payment_mode'] != 'Odnimo - api' && $details->status == 'success' && $details->amount >= 5000 && $details->trans_type != 'split'
            )
                $split = '<a href="javascript:void(0);" class=" btn btn-success btn-xs split" _id="' . $old_id . '"><i class="fas fa-solid fa-splotch"></i>&nbsp;Split</a>';


            if ($details->status == 'success')
                $table .= '<td>Action:</td>
                <td>
                        <a href="javascript:void(0);" payment_mode="' . $details->payment_mode . '" class="btn btn-danger btn-xs success-action" _id="' .  $transaction1_id . '"><i class="fas fa-radiation-alt"></i>&nbsp;Action</a>
                       ' . $update_utr . '
                       ' . $split . '
                </td>';

            $table .= '</table>';

            $utr_no = !empty($details->response['utr_number']) ? $details->response['utr_number'] : '';

            // if (
            //   !empty($details->response['payment_mode']) && $details->response['payment_mode'] != 'payunie-Preet Kumar'
            // && $details->response['payment_mode'] != 'payunie-Rashid Ali' &&  $details->response['payment_mode'] != 'Pay2All-Parveen' &&
            //$details->response['payment_mode'] != 'Odnimo - api'
            //)
            $channel = '';
            if (!empty($details->response['payment_mode']) && empty($details->splits)) {
                $type = 0;
                $channel = $details->response['payment_mode'];
                $table .= '<div class="row" style="display:none;" id="utr">
                    <div class="col-md-12 form-group">
                        <label>Update UTR No.</label>
                        <input type="hidden" value="' . $transaction1_id . '" id="trnsaction-id" name="id">
                        <div class="input-group input-group-sm">
                            <input type="text" value="' . $utr_no . '" name="utr_no" id="utr_no" class="form-control form-control-sm">
                            <span class="input-group-append ">
                                <button type="button" class="btn btn-danger btn-flat" id="update_utr">Update</button>
                            </span>
                        </div>
                    </div>
                </div>';
            } else {
                $type = 1;
            }

            die(json_encode(['table' => $table, 'id' => $details->_id, 'channel' => $channel, 'type' => $type]));
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'something went wrong']);
        }
    }


    public function updateUtrNo(Request $request)
    {
        try {
            $transaction_id = $request->id;
            $utr = $request->utr;
            $transaction = Transaction::find($transaction_id);

            if (!empty($transaction->response))
                $response = $transaction->response;
            $response['utr_number']  = $utr;
            $response['action_by']   = Auth::user()->_id;
            $response['action_date'] = time();
            $response['action']      = 'update UTR No';
            $transaction->response   = $response;

            if ($transaction->save()) {
                $this->updateUtrNewTransaction($transaction_id, $utr);
                return response(['status' => 'success', 'msg' => 'UTR No updated successfully!']);
            }

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
            $exist_response['action_by']     = Auth::user()->_id;
            $exist_response['action_date']   = time();
            $exist_response['action']        = 'update payment channel';
            $transaction->response = $exist_response;
            if ($transaction->save()) {

                $this->updateChannelNewTransaction($transaction_id, $request->channel);
                return response(['status' => 'success', 'msg' => 'Channel changed successfully!']);
            }

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

                $response['action_by']     = Auth::user()->_id;
                $response['action_date']   = time();
                $response['action']        = 'update pednding API amount';

                $transaction->response = $response;
                $transaction->status   = $api_status;
                if ($transaction->save()) {

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
                        return response(['status' => 'success']);
                    }
                }
            }
            return response(['status' => 'error', 'msg' => 'Transaction Id not Found!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
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
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
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

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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

            $transactions = $query->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;
                $upi_id = (!empty($payment->upi_id)) ? $payment->upi_id : '';

                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = ucwords($transaction->sender_name);
                $transaction_val[] = $transaction->mobile_number;
                $transaction_val[] = ucwords(str_replace('_', ' ', $transaction->type));
                $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                $transaction_val[] = (!empty($transaction->response['utr_number'])) ? $transaction->response['utr_number'] : '';
                $transaction_val[] = strtoupper(str_replace('_', ' ', $transaction->status));
                $transaction_val[] = !empty(date('Y-m-d H:i', $transaction->created)) ? date('Y-m-d H:i', $transaction->created) : '';
                $transaction_val[] = !empty($transaction->UserName['full_name']) ? $transaction->UserName['full_name'] : '';
                $transaction_val[] = !empty($transaction->response['action_date']) ? date('Y-m-d H:i', $transaction->response['action_date']) : '';

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


    public function refundPendingExport(Request $request)
    {
        try {
            $file_name = 'refund-pending-transaction';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query()->where('status', 'refund_pending');

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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

            $transactions = $query->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;
                $upi_id = (!empty($payment->upi_id)) ? $payment->upi_id : '';

                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = ucwords($transaction->sender_name);
                $transaction_val[] = $transaction->mobile_number;
                $transaction_val[] = ucwords(str_replace('_', ' ', $transaction->type));
                $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                $transaction_val[] = (!empty($transaction->response['utr_number'])) ? $transaction->response['utr_number'] : '';
                $transaction_val[] = strtoupper(str_replace('_', ' ', $transaction->status));
                $transaction_val[] = !empty(date('Y-m-d H:i', $transaction->created)) ? date('Y-m-d H:i', $transaction->created) : '';
                $transaction_val[] = !empty($transaction->UserName['full_name']) ? $transaction->UserName['full_name'] : '';
                $transaction_val[] = !empty($transaction->response['action_date']) ? date('Y-m-d H:i', $transaction->response['action_date']) : '';

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

    public function exportSplit(Request $request)
    {
        try {
            $file_name = 'split-trans';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query()->where('trans_type', 'split');

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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

            $transactions = $query->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $splits = (object)$transaction->splits;

                if (!empty($splits)) {
                    foreach ($splits as $split) {
                        $split = (object)$split;
                        $sResp = (object)$split->response;

                        $payment = (object)$transaction->payment_channel;
                        $upi_id = (!empty($payment->upi_id)) ? $payment->upi_id : '';

                        $transaction_val[] = $transaction->transaction_id;
                        $transaction_val[] = ucwords($transaction->sender_name);
                        $transaction_val[] = $transaction->mobile_number;
                        $transaction_val[] = ucwords(str_replace('_', ' ', $split->payment_mode));
                        $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                        $transaction_val[] = $split->amount;
                        $transaction_val[] = (!empty($split->transaction_fees)) ? $split->transaction_fees : '';
                        $transaction_val[] = ucwords($transaction->receiver_name);
                        $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                        $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $upi_id;
                        $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                        $transaction_val[] = (!empty($sResp->utr_number)) ? $sResp->utr_number : '';
                        $transaction_val[] = strtoupper(str_replace('_', ' ', $split->status));
                        $transaction_val[] = !empty(date('Y-m-d H:i', $transaction->created)) ? date('Y-m-d H:i', $transaction->created) : '';
                        $transaction_val[] = !empty($transaction->UserName['full_name']) ? $transaction->UserName['full_name'] : '';
                        $transaction_val[] = !empty($sResp->action_date) ? date('Y-m-d H:i', $sResp->action_date) : '';

                        $transactionArr = $transaction_val;

                        fputcsv($f, $transactionArr, $delimiter); //put heading here
                        $transaction_val = [];
                    }
                }
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


    public function refundPendingExportSplit(Request $request)
    {
        try {
            $file_name = 'refund-pending-split-trans';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query()->where('trans_type', 'split')->where('status', 'refund_pending');

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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

            $transactions = $query->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $splits = (object)$transaction->splits;

                if (!empty($splits)) {
                    foreach ($splits as $split) {
                        $split = (object)$split;
                        $sResp = (object)$split->response;

                        $payment = (object)$transaction->payment_channel;
                        $upi_id = (!empty($payment->upi_id)) ? $payment->upi_id : '';

                        $transaction_val[] = $transaction->transaction_id;
                        $transaction_val[] = ucwords($transaction->sender_name);
                        $transaction_val[] = $transaction->mobile_number;
                        $transaction_val[] = ucwords(str_replace('_', ' ', $split->payment_mode));
                        $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                        $transaction_val[] = $split->amount;
                        $transaction_val[] = (!empty($split->transaction_fees)) ? $split->transaction_fees : '';
                        $transaction_val[] = ucwords($transaction->receiver_name);
                        $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                        $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $upi_id;
                        $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                        $transaction_val[] = (!empty($sResp->utr_number)) ? $sResp->utr_number : '';
                        $transaction_val[] = strtoupper(str_replace('_', ' ', $split->status));
                        $transaction_val[] = !empty(date('Y-m-d H:i', $transaction->created)) ? date('Y-m-d H:i', $transaction->created) : '';
                        $transaction_val[] = !empty($transaction->UserName['full_name']) ? $transaction->UserName['full_name'] : '';
                        $transaction_val[] = !empty($sResp->action_date) ? date('Y-m-d H:i', $sResp->action_date) : '';

                        $transactionArr = $transaction_val;

                        fputcsv($f, $transactionArr, $delimiter); //put heading here
                        $transaction_val = [];
                    }
                }
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

    public function splitTransaction(Request $request)
    {
        try {

            $id = $request->trans_id;
            $transaction = Transaction::find($id);

            $amount = $transaction->amount;
            $charges = $transaction->transaction_fees;
            $responseData = $request->response;

            $total_amount = 0;
            $total_charges = 0;
            foreach ($responseData as $res) {
                $total_amount += !empty($res['amount']) ? $res['amount'] : 0;
                $total_charges += $res['charges'];
            }
            // echo '/'.$total_amount;
            if ($amount != $total_amount)
                return response(['status' => 'error', 'msg' => 'Total Amount must be- ' . $amount]);

            if ($charges != $total_charges)
                return response(['status' => 'error', 'msg' => 'Total Charges must be- ' . $charges]);

            $splitTrans = [];
            $utrs = [];
            foreach ($responseData as $key => $res) {

                $resData['action_by']     = Auth::user()->_id;
                $resData['action_date']   = !empty($responseData[$key]['date']) ? strtotime($responseData[$key]['date']) : time();
                $resData['payment_mode']  = !empty($responseData[$key]['payment_mode']) ? $responseData[$key]['payment_mode'] : '';
                $resData['utr_number']    = !empty($responseData[$key]['utr_number']) ? $responseData[$key]['utr_number'] : '';
                $resData['msg']           = !empty($responseData[$key]['msg']) ? $responseData[$key]['msg'] : '';

                $splitTrans[] = [
                    'amount'           => !empty($responseData[$key]['amount']) ? $responseData[$key]['amount'] : $amount,
                    'transaction_fees' => !empty($responseData[$key]['charges']) ? $responseData[$key]['charges'] : '',
                    'payment_mode'     => 'bank_account',
                    'status'           => !empty($responseData[$key]['status']) ? $responseData[$key]['status'] : 'pending',
                    'type'             => 'payout',
                    'response'         => $resData
                ];

                $utrs[] = $resData['utr_number'];
            }

            /*start first transaction Update*/
            $response['action_by']     = Auth::user()->_id;
            $response['action_date']   = !empty($responseData[0]['date']) ? strtotime($responseData[0]['date']) : time();
            $response['action']        = 'manual update Payment Status';
            $response['payment_mode']  = !empty($responseData[0]['payment_mode']) ? $responseData[0]['payment_mode'] : '';
            $response['utr_number']    = !empty($responseData[0]['utr_number']) ? $responseData[0]['utr_number'] : '';
            $response['msg']           = !empty($responseData[0]['msg']) ? $responseData[0]['msg'] : '';

            $transaction->status            = !empty($responseData[0]['status']) ? $responseData[0]['status'] : 'pending';
            $transaction->response          = $response;
            $transaction->trans_type        = 'split';
            $transaction->splits            = $splitTrans;
            $transaction->utrs              = implode(',', $utrs);
            /*end first transaction update*/

            if ($transaction->save()) {

                $newData = [
                    'old_id' => $transaction->_id,
                    'response' => $request->response
                ];
                $this->newTransactionSave($newData);

                return response(['status' => 'success', 'msg' => 'Transaction Success!']);
            }

            return response(['status' => 'error', 'msg' => 'Transaction Failed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }



    public function report(Request $request)
    {
        try {
            $data['outlets'] = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();
            $query = Transaction::query();

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $crrMonth = (date('Y-m-d'));
                $start_date = strtotime(trim(date("d-m-Y", strtotime('-15 days', strtotime($crrMonth)))) . " 00:00:00");
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            $query->whereBetween('created', [$start_date, $end_date]);

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $transactions = $query->orderBy('created', 'DESC')->get();

            $transData = [];
            foreach ($transactions as $transaction) {

                $transData[date('d M,Y', $transaction->created)][] = [
                    'id'               => $transaction->_id,
                    'transaction_id'   => $transaction->transaction_id,
                    'sender_name'      => $transaction->sender_name,
                    'mobile_number'    => $transaction->mobile_number,
                    'receiver_name'    => $transaction->receiver_name,
                    'amount'           => $transaction->amount,
                    'transaction_fees' => $transaction->transaction_fees,
                    'status'           => $transaction->status,
                    'payment_channel'  => $transaction->payment_channel,
                    'created'          => date('d M,Y', $transaction->created),
                    'response'         => $transaction->response

                ];
            }

            $report = [];
            foreach ($transData as $key => $value) {

                $transactionsIteration = [];
                $count = 0;
                $success_count = 0;
                $pending_count = 0;
                $rejeced_count = 0;
                $failed_count  = 0;
                $refund_count  = 0;
                $total_amount  = 0;
                $failed_a      = 0;
                $success_a     = 0;
                $pending_a     = 0;
                $rejected_a    = 0;
                $refund_a      = 0;
                foreach ($value as $val) {
                    if ($key == $val['created']) {
                        $transactionsIteration[] = [
                            'id'               => $val['id'],
                            'transaction_id'   => $val['transaction_id'],
                            'sender_name'      => $val['sender_name'],
                            'receiver_name'    => $val['receiver_name'],
                            'mobile_number'    => $val['mobile_number'],
                            'amount'           => $val['amount'],
                            'transaction_fees' => $val['transaction_fees'],
                            'status'           => $val['status'],
                            'payment_channel'  => $val['payment_channel'],
                            'created'          => $val['created'],
                            'response'         => $val['response'],
                            'username'         => '-' //!empty($val['response']->UserName['full_name']) ?$val['response']->UserName['full_name'] : '';
                        ];
                    }
                    $total_amount += $val['amount'];
                    if (!empty($val['status']) && $val['status'] == 'success') {
                        $success_a += $val['amount'];
                        $success_count++;
                    }

                    if (!empty($val['status']) && ($val['status'] == 'pending' || $val['status'] == 'process')) {
                        $pending_a += $val['amount'];
                        $pending_count++;
                    }

                    if (!empty($val['status']) && ($val['status'] == 'failed')) {
                        $failed_a += $val['amount'];
                        $failed_count++;
                    }


                    if (!empty($val['status']) && ($val['status'] == 'refund_pending')) {
                        $refund_a += $val['amount'];
                        $refund_count++;
                    }


                    if (!empty($val['status']) && $val['status'] == 'rejected') {
                        $rejected_a += $val['amount'];
                        $rejeced_count++;
                    }

                    $count++;
                }
                $report[] = [
                    'date'            => $key,
                    'total_count'     => $count,
                    'total_amount'    => $total_amount,
                    'success_amount'  => $success_a,
                    'success_count'   => $success_count,
                    'pending_amount'  => $pending_a,
                    'pending_count'   => $pending_count,
                    'failed_amount'   => $failed_a,
                    'failed_count'    => $failed_count,
                    'refund_amount'   => $refund_a,
                    'refund_count'    => $refund_count,
                    'rejected_amount' => $rejected_a,
                    'rejected_count'  => $rejeced_count,
                    'transactions'    => $transactionsIteration
                ];
            }

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            $data['trnasReport'] = $report;
            $data['transactions'] = $transactions;
            return view('admin.transaction.report', $data);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    //for new transaction collection

    //show new Transaction list
    public function newTransaction(Request $request)
    {
        try {

            $outlets = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();

            $query = NewTransaction::query();

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

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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
            $data['transaction'] = $query->where('status', '!=', 'pending')->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->paginate($perPage);
            $data['outlets']   = $outlets;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            return view('admin.transaction.new_transaction', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }

    //create new Transaction list by split Transaction
    private function newTransactionSave($request)
    {
        try {
            $request = (object)$request;

            $id = $request->old_id;
            $existTrans = NewTransaction::where('old_trans_id', $id)->first();

            $amount = $existTrans->amount;
            $responseData = $request->response;

            /*start first transaction Update*/
            $response['action_by']     = Auth::user()->_id;
            $response['action_date']   = time();
            $response['action']        = 'manual update Payment Status (Split Transaction) - Parent Txn';
            $response['payment_mode']  = !empty($responseData[0]['payment_mode']) ? $responseData[0]['payment_mode'] : '';
            $response['utr_number']    = !empty($responseData[0]['utr_number']) ? $responseData[0]['utr_number'] : '';
            $response['msg']           = !empty($responseData[0]['msg']) ? $responseData[0]['msg'] : '';

            $existTrans->transaction_fees = !empty($responseData[0]['charges']) ? $responseData[0]['charges'] : '';
            $existTrans->amount         = !empty($responseData[0]['amount']) ? $responseData[0]['amount'] : $amount;
            $existTrans->status         = !empty($responseData[0]['status']) ? $responseData[0]['status'] : 'pending';
            $existTrans->response       = $response;
            $existTrans->trans_type     = 'split';
            $existTrans->split_index    = 0;
            $existTrans->save();
            /*end first transaction update*/

            $result = false;
            foreach ($responseData as $key => $res) {

                if ($key == 0)
                    continue;

                $transactionN = new NewTransaction();
                $transactionN->old_trans_id      = $existTrans->old_trans_id;
                $transactionN->transaction_id    = uniqCode(3) . rand(111111, 999999);
                $transactionN->retailer_id       = $existTrans->retailer_id;
                $transactionN->outlet_id         = $existTrans->outlet_id;
                $transactionN->mobile_number     = $existTrans->mobile_number;
                $transactionN->sender_name       = $existTrans->sender_name;
                $transactionN->amount            = !empty($responseData[$key]['amount']) ? $responseData[$key]['amount'] : $amount;
                $transactionN->transaction_fees  = !empty($responseData[$key]['charges']) ? $responseData[$key]['charges'] : '';
                $transactionN->receiver_name     = $existTrans->receiver_name;
                $transactionN->payment_mode      = 'bank_account'; //$request->payment_mode;
                $transactionN->payment_channel   = $existTrans->payment_channel;
                $transactionN->status            = !empty($responseData[$key]['status']) ? $responseData[$key]['status'] : 'pending';
                $transactionN->type              = 'payout';
                $transactionN->pancard_no        = $existTrans->pancard_no;

                $response['action_by']     = Auth::user()->_id;
                $response['action_date']   = !empty($responseData[$key]['date']) ? strtotime($responseData[$key]['date']) : time();
                $response['parent_txn_id'] = $existTrans->_id;
                $response['action']        = 'manual update Payment Status (Split Transaction) - Parent Txn no. ' . $existTrans->transaction_id;
                $response['payment_mode']  = !empty($responseData[$key]['payment_mode']) ? $responseData[$key]['payment_mode'] : '';
                $response['utr_number']    = !empty($responseData[$key]['utr_number']) ? $responseData[$key]['utr_number'] : '';
                $response['msg']           = !empty($responseData[$key]['msg']) ? $responseData[$key]['msg'] : '';

                $transactionN->response     = $response;
                $transactionN->referance_trans = $id;
                $transactionN->trans_type   = 'split';
                $transactionN->split_created = $existTrans->created;
                $transactionN->split_index   = $key;
                $result = $transactionN->save();
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    //for update status for new Transaction
    private function updateNewTransaction($old_id, $status, $response)
    {
        $trans = NewTransaction::where('old_trans_id', $old_id)->get();

        if (empty($trans))
            return false;

        foreach ($trans as $tran) {
            $transaction = NewTransaction::find($tran->_id);
            $transaction->status = $status;
            $transaction->response = $response;
            $transaction->save();
        }
    }

    //for update old transction status by new tranasction also self
    public function changeTransactionStatus(Request $request)
    {
        try {
            $NewTransaction = NewTransaction::find($request->trans_id);

            if ($NewTransaction->status == 'rejected')
                // return response(['status' => 'error', 'msg' => 'This status is already Rejected!']);

                $response1 =  $request->response;
            $response1['action_by']     = Auth::user()->_id;
            $response1['action_date']   = time();
            $response1['action']        = 'manual update Payment Status';
            $NewTransaction->status       = $request->status;
            $NewTransaction->response     = $response1;


            if (!$NewTransaction->save())
                return response(['status' => 'error', 'msg' => 'Transaction not Made!']);

            /** start update old transaction status*/
            $transaction = Transaction::find($NewTransaction->old_trans_id);
            if (empty($transaction))
                return false;

            if (!empty($transaction->splits)) {
                $index = $NewTransaction->split_index;
                $splits = $transaction->splits;

                if ($request->status == 'rejected') {
                    $response1['utr_number']    = !empty($splits[$index]['response']['utr_number']) ? $splits[$index]['response']['utr_number'] : '';
                    $response1['payment_mode']  = !empty($splits[$index]['response']['payment_mode']) ? $splits[$index]['response']['payment_mode'] : '';
                } else {
                    $response1['utr_number']    = '';
                    $response1['payment_mode']  = '';
                }

                $response1['action_by']     = Auth::user()->_id;
                $response1['action_date']   = time();
                $response1['action']        = 'update UTR No';
                $response1['msg']           = !empty($splits[$index]['response']['msg']) ? $splits[$index]['response']['msg'] : '';

                $splits[$index]['response'] = $response1;
                $splits[$index]['status']    = $request->status;
                $transaction->splits = $splits;
                $transaction->save();
            } else {

                $response =  $request->response;
                $response['action_by']     = Auth::user()->_id;
                $response['action_date']   = time();
                $response['action']        = 'manual update Payment Status';
                $transaction->status       = $request->status;
                $transaction->response     = $response;
                $transaction->save();
            }
            /** end update old transaction status*/

            if ($transaction->type == 'payout_api')
                webhook($transaction);

            if ($transaction->status == 'success') {
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
            } else if ($transaction->status == 'rejected') {
                //add toupup amount here
                $transaction_id   = $transaction->_id;
                $receiver_name    = $transaction->receiver_name;
                $payment_date     = $transaction->created;
                $status           = 'success';
                $payment_mode     = $transaction->payment_mode;
                $type             = 'refund';
                $retailer_id      = $transaction->retailer_id;
                $transaction_fees = $transaction->transaction_fees;
                $amount           = $transaction->amount;
                $bank_details     = $transaction->payment_channel;
                $source           = 'Credited By Reject Transaction';
                addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit', 0, $bank_details, $transaction_id, $source);
            }

            return response(['status' => 'success', 'msg' => 'Transaction ' . ucwords($transaction->status) . ' Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    //for export new collection
    public function newTransactionExport(Request $request)
    {

        try {
            $file_name = 'new-transaction-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Channel', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = NewTransaction::query();

            if ($request->outlet_id)
                $query->where('outlet_id', $request->outlet_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->channel))
                $query->where('response.payment_mode', $request->channel);

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

            $transactions = $query->orderBy('created', 'DESC')->with(['OutletName', 'UserName'])->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;
                $upi_id = (!empty($payment->upi_id)) ? $payment->upi_id : '';

                $transaction_val[] = $transaction->transaction_id;
                $transaction_val[] = ucwords($transaction->sender_name);
                $transaction_val[] = $transaction->mobile_number;
                $transaction_val[] = ucwords(str_replace('_', ' ', $transaction->type));
                $transaction_val[] = (!empty($transaction->response['payment_mode'])) ? $transaction->response['payment_mode'] : '';
                $transaction_val[] = $transaction->amount;
                $transaction_val[] = (!empty($transaction->transaction_fees)) ? $transaction->transaction_fees : '';
                $transaction_val[] = ucwords($transaction->receiver_name);
                $transaction_val[] = (!empty($payment->ifsc_code)) ? $payment->ifsc_code : '';
                $transaction_val[] = (!empty($payment->account_number)) ? $payment->account_number : $upi_id;
                $transaction_val[] = (!empty($payment->bank_name)) ? $payment->bank_name : '';
                $transaction_val[] = (!empty($transaction->response['utr_number'])) ? $transaction->response['utr_number'] : '';
                $transaction_val[] = strtoupper(str_replace('_', ' ', $transaction->status));
                $transaction_val[] = !empty(date('Y-m-d H:i', $transaction->created)) ? date('Y-m-d H:i', $transaction->created) : '';
                $transaction_val[] = !empty($transaction->UserName['full_name']) ? $transaction->UserName['full_name'] : '';
                $transaction_val[] = !empty($transaction->response['action_date']) ? date('Y-m-d H:i', $transaction->response['action_date']) : '';

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

    //for update new Transaction UTR NO by old transaction.
    private function updateUtrNewTransaction($old_id = false, $utr = false)
    {
        if (!$old_id || !$utr)
            return false;

        $transaction = NewTransaction::where('old_trans_id', $old_id)->first();

        if (!empty($transaction->response))
            $response = $transaction->response;

        $response['utr_number']  = $utr;
        $response['action_by']   = Auth::user()->_id;
        $response['action_date'] = time();
        $response['action']      = 'update UTR No';
        $transaction->response   = $response;
        $transaction->save();
    }

    //for update new Transaction UTR NO By New transaction.
    public function updateNewUtr(Request $request)
    {
        try {
            $transaction_id = $request->id;
            $utr = $request->utr;
            $transaction = NewTransaction::find($transaction_id);

            $response = [];
            if (!empty($transaction->response))
                $response = $transaction->response;

            $response['utr_number']  = $utr;
            $response['action_by']   = Auth::user()->_id;
            $response['action_date'] = time();
            $response['action']      = 'update UTR No';
            $transaction->response   = $response;

            if ($transaction->save()) {
                $this->updateUtrOldTransaction($transaction->old_trans_id, $utr, $transaction->split_index);
                return response(['status' => 'success', 'msg' => 'UTR No updated successfully!']);
            }

            return response(['status' => 'error', 'msg' => 'UTR No not updated!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    //for updated old Transaction UTR By New transaction
    private function updateUtrOldTransaction($id = false, $utr = false, $split_index = false)
    {
        if (!$id || !$utr)
            return false;

        $transaction = Transaction::find($id);

        $response = [];
        if ($transaction->trans_type != 'split') {
            if (!empty($transaction->response))
                $response = $transaction->response;

            $response['utr_number']  = $utr;
            $response['action_by']   = Auth::user()->_id;
            $response['action_date'] = time();
            $response['action']      = 'update UTR No';
            $transaction->response   = $response;
            $transaction->save();
        } else {

            if (!$split_index) {
                if (!empty($transaction->response))
                    $response = $transaction->response;

                $response['utr_number']  = $utr;
                $transaction->response   = $response;
            }
            $splits = $transaction->splits;

            $response1['utr_number']  = $utr;
            $response1['payment_mode'] =   !empty($splits[$split_index]['response']['payment_mode']) ? $splits[$split_index]['response']['payment_mode'] : '';
            $response1['action_by']   = Auth::user()->_id;
            $response1['action_date'] = time();
            $response1['action']      = 'update UTR No';
            $response1['msg'] =  !empty($splits[$split_index]['response']['msg']) ? $splits[$split_index]['response']['msg'] : '';

            $splits[$split_index]['response'] = $response1;
            $transaction->splits = $splits;
            $transaction->save();
        }
    }

    //for updated new Transaction Channel by old Transaction
    private function updateChannelNewTransaction($old_id = false, $channel = false)
    {
        if (!$old_id || !$channel)
            return false;

        $transaction = NewTransaction::where('old_trans_id', $old_id)->first();

        $response = [];
        if (!empty($transaction->response))
            $response = $transaction->response;

        $response['payment_mode']  = $channel;
        $response['action_by']     = Auth::user()->_id;
        $response['action_date']   = time();
        $response['action']        = 'update payment channel';
        $transaction->response = $response;
        $transaction->save();
    }

    //for updated new Transaction Channel by New Transaction
    public function updateNewChannel(Request $request)
    {
        try {
            $transaction_id = $request->id;
            $transaction = NewTransaction::find($transaction_id);

            $exist_response = $transaction->response;
            $exist_response['payment_mode']  = $request->channel;
            $exist_response['action_by']     = Auth::user()->_id;
            $exist_response['action_date']   = time();
            $exist_response['action']        = 'update payment channel';
            $transaction->response = $exist_response;
            if ($transaction->save()) {

                $this->updateChannelOldTransaction($transaction->old_trans_id, $request->channel, $transaction->split_index);
                return response(['status' => 'success', 'msg' => 'Channel changed successfully!']);
            }

            return response(['status' => 'error', 'msg' => 'Channel not changed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    //for updated old Transaction Channel by new Transaction
    private function updateChannelOldTransaction($id = false, $channel = false, $split_index = false)
    {
        if (!$id || !$channel)
            return false;

        $transaction = Transaction::find($id);

        $response = [];
        if ($transaction->trans_type != 'split') {
            if (!empty($transaction->response))
                $response = $transaction->response;

            $response['payment_mode']  = $channel;
            $response['action_by']   = Auth::user()->_id;
            $response['action_date'] = time();
            $response['action']      = 'update UTR No';
            $transaction->response   = $response;
            $transaction->save();
        } else {

            if (!$split_index) {
                if (!empty($transaction->response))
                    $response = $transaction->response;

                $response['payment_mode']  = $channel;
                $transaction->response   = $response;
            }
            $splits = $transaction->splits;

            $response1['utr_number']   = !empty($splits[$split_index]['response']['utr_number']) ? $splits[$split_index]['response']['utr_number'] : '';
            $response1['payment_mode'] = $channel;
            $response1['action_by']    = Auth::user()->_id;
            $response1['action_date']  = time();
            $response1['action']       = 'update UTR No';
            $response1['msg']          =  !empty($splits[$split_index]['response']['msg']) ? $splits[$split_index]['response']['msg'] : '';

            $splits[$split_index]['response'] = $response1;
            $transaction->splits = $splits;
            $transaction->save();
        }
    }
}
