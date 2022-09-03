<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\ApiList;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\NewTransaction;
use App\Support\ClicknCash;
use App\Support\OdnimoPaymentApi;
use App\Support\PaymentApi;
use App\Support\PayTel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        try {

            $query = Transaction::query()->with(['UserName'])->where('retailer_id', Auth::user()->_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->banficiary))
                $query->where('receiver_name', $request->banficiary);

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
            if ($request->filter_by == 'created_date')
                $query->whereBetween('created', [$start_date, $end_date]);
            else if ($request->filter_by == 'action_date')
                $query->whereBetween('response.action_date', [$start_date, $end_date]);
            else
                $query->whereBetween('created', [$start_date, $end_date]);

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['transactions']    = $query->orderBy('created', 'DESC')->paginate($perPage);

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('retailer.transaction.display', $data);

            Session::forget('importData');
            Session::forget('previewData');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function refundPending(Request $request)
    {
        try {

            $query = Transaction::query()->with(['UserName'])->where('retailer_id', Auth::user()->_id)->where('status', 'refund_pending');

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->banficiary))
                $query->where('receiver_name', $request->banficiary);

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
            if ($request->filter_by == 'created_date')
                $query->whereBetween('created', [$start_date, $end_date]);
            else if ($request->filter_by == 'action_date')
                $query->whereBetween('response.action_date', [$start_date, $end_date]);
            else
                $query->whereBetween('created', [$start_date, $end_date]);

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['transactions']    = $query->orderBy('created', 'DESC')->paginate($perPage);

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('retailer.transaction.refund_pending', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }

    public function dmtStore(Request $request)
    {
        try {

            /*start check amount available in wallet or not*/
            $amount  = $request->amount;
            $charges = 0;
            $outlet  = Outlet::select('bank_charges', 'security_amount')->where('_id', Auth::user()->outlet_id)->first();
            if (!empty($outlet->bank_charges)) {
                foreach ($outlet->bank_charges as $charge) {
                    if ($charge['type'] == 'inr') { // here all inr charges

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = $charge['charges'];
                            break;
                        }
                    } else if ($charge['type'] == 'persantage') { //calculate persantage here

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = ($charge['charges'] / 100) * $amount;
                            break;
                        }
                    }
                }
            } else {
                return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
            }
            $total_amount = $amount + $charges;

            //for preview page functionality
            if (!empty($request->preview) && $request->preview == 'preview') {
                $previewData = $request->all();
                $previewData['fees'] = number_format($charges, 2, ".", "");
                return  response(['status' => 'preview', 'response' => $previewData]);
            }

            /*start for verify pin*/
            if (empty($request->pin))
                return response(['status' => 'error', 'msg' => 'Pin is Required.']);

            if (empty($request->pin) || count($request->pin) != '4')
                return response(['status' => 'error', 'msg' => 'Please Enter Valid Pin']);

            $pin = implode('', $request->pin);
            if (trim($pin) != Auth::user()->pin)
                return response(['status' => 'error', 'msg' => 'Pin is not Verified']);
            /*end for verify pin*/

            $s_amount = !empty($outlet->security_amount) ? $outlet->security_amount : 0;
            $security_amount = ($total_amount) + ($s_amount);

            if ($security_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/

            $api_status = 'pending';
            $response = [];
            /*start api transfer functionality*/
            if ($amount <= 5000) {

                $payment_channel = (object)$request->payment_channel;

                $payment_para = [
                    'account_number' => $payment_channel->account_number,
                    'ifsc_code'     => $payment_channel->ifsc_code,
                    'amount'        => $amount,
                    'receiver_name' => $request->receiver_name,
                    'bank_name'     => $payment_channel->bank_name,
                ];
                $payment_api = new PaymentApi();

                $apiLists = ApiList::where('retailer_ids', 'all', [Auth::user()->_id])->orderBy('sort', 'ASC')->get();
                $res = '';
                if (!$apiLists->isEmpty()) {
                    foreach ($apiLists as $api) {

                        if ($api->status == 1 && $api->name == 'Payunie - Preet Kumar') {
                            $res = $payment_api->payunie($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Payunie -Rashid Ali') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res = $payment_api->payunie1($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Pay2All- Parveen') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res =  $payment_api->pay2All($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'odinmo') {
                            if (empty($res)) {
                                $OdnimoPaymentApi = new OdnimoPaymentApi();
                                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
                            }
                        }
                    }
                }

                $response = [];
                if (!empty($res)) {
                    $response = $res['response'];
                    $api_status = $res['status'];
                }
            }
            /*start api transfer functionality*/

            //uploade pancard docs
            $pancard = '';
            if (!empty($request->file('pancard')))
                $pancard   = singleFile($request->file('pancard'), 'attachment/transaction');

            //insert new record
            $transaction = new Transaction();
            $transaction->retailer_id    = Auth::user()->_id;
            $transaction->outlet_id      = Auth::user()->outlet_id;
            $transaction->otp            = $request->otp;
            $transaction->mobile_number  = $request->mobile_number;
            $transaction->customer_name  = $request->sender_name;

            $transaction->transaction_id = uniqCode(3) . rand(111111, 999999);
            $transaction->sender_name    = trim($request->sender_name);
            $transaction->amount         = $request->amount;
            $transaction->transaction_fees = number_format($charges, 2, ".", "");
            $transaction->receiver_name  = $request->receiver_name;
            $transaction->payment_mode   = 'bank_account'; //$request->payment_mode;
            $transaction->payment_channel = $request->payment_channel;
            $transaction->status         = $api_status;
            $transaction->type           = 'dmt_transfer';
            $transaction->pancard_no     = $request->pancard_no;
            $transaction->pancard        = $pancard;
            $transaction->ip_address     = ip_address();
            if (!empty($response))
                $transaction->response       = $response;
            $transaction->verified       = (session("otp-$request->mobile_number") == $request->otp) ? 1 : 0;

            $res = $transaction->save();

            if (!$res)
                return response(['status' => 'error',  'msg' => 'Transaction Request not Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            /*start passbook debit functionality*/
            $transaction_id   = $transaction->_id;
            $amount        = $transaction->amount;
            $receiver_name = $transaction->receiver_name;
            $payment_date  = $transaction->created;
            $status        = 'success';
            $payment_mode  = $transaction->payment_mode;
            $transaction_fees = $transaction->transaction_fees;
            $type          = $transaction->type;
            $retailer_id   = $transaction->retailer_id;

            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $transaction_id);
            /*end passbook debit functionality*/

            $newTransaction = [
                'old_trans_id' => $transaction->_id,
                'retailer_id' => $transaction->retailer_id,
                'outlet_id' => $transaction->outlet_id,
                'otp'     => $request->otp,
                'mobile_number' => $request->mobile_number,
                'customer_name' => $request->customer_name,
                'transaction_id' => $transaction->transaction_id,
                'sender_name' => trim($request->sender_name),
                'amount' => $request->amount,
                'transaction_fees' => $transaction->transaction_fees,
                'receiver_name' => $request->receiver_name,
                'payment_mode' => $transaction->payment_mode,
                'payment_channel' => $request->payment_channel,
                'status' => $transaction->status,
                'type' => $transaction->type,
                'pancard_no' => $request->pancard_no,
                'pancard' => $pancard,
                'response' => $response,
                'verified' => $transaction->verified
            ];

            $this->newTransaction($newTransaction);

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    //otp send functionality
    public function sendOtp(Request $request)
    {
        try {
            $mobile_no = $request->mobile_no;

            $res = Transaction::select('mobile_number', 'amount')->where('mobile_number', $mobile_no)->where('verified', 1)->where('retailer_id', Auth()->user()->_id)->first();
            if (!empty($res) && $res->mobile_number == $mobile_no) {
                $all_amount = 0;
                $respose = Transaction::select('amount')->where('mobile_number', $mobile_no)->get();
                foreach ($respose as $detail) {
                    $all_amount += (int)$detail->amount;
                }

                $res_data = '<div class="card mt-2 p-2">
            <div><strong>Month :</strong><span>' . date('F') . '</span></div>
            <div><strong>Transaction Amount :</strong><span>' . mSign($all_amount) . '</span></div>
            <div><strong>Limit Amount:</strong><span>' . mSign(200000) . '</span></div>
            </div>';

                return response(['status' => 'detail', 'data' => $res_data]);
            } else {

                $otp = rand(0000, 9999);
                session(["otp-$mobile_no" => $otp]);

                if ($otp == session("otp-$mobile_no"))
                    return response(['status' => 'success', 'otp' => $otp, 'msg' => 'Otp Sent Successfully!']);

                return response(['status' => 'error', 'msg' => 'Otp not snet!']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function verifyMobile(Request $request)
    {

        $mobile_no = $request->mobile_no;

        $otp = $request->otp;
        if ($otp == session("otp-$mobile_no"))
            return response(['status' => 'success', 'msg' => 'Mobile Number Verified Successfully!']);

        return response(['status' => 'error', 'msg' => 'Mobile Number not  Verified!']);
    }


    public function payoutStore(Request $request)
    {

        try {
            /*start check amount available in wallet or not*/
            $amount = $request->amount;
            $outlet = Outlet::select('bank_charges', 'security_amount')->where('_id', Auth::user()->outlet_id)->first();
            $charges = 0;
            if (!empty($outlet->bank_charges)) {
                foreach ($outlet->bank_charges as $charge) {
                    if ($charge['type'] == 'inr') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = $charge['charges'];
                            break;
                        }
                    } else if ($charge['type'] == 'persantage') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = ($charge['charges'] / 100) * $amount;
                            break;
                        }
                    }
                }
            } else {
                return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
            }

            $total_amount = $amount + $charges;
            $s_amount = !empty($outlet->security_amount) ? $outlet->security_amount : 0;
            $security_amount = ($total_amount) + ($s_amount);
            if ($security_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount.']);
            /*end check amount available in wallet or not*/


            //for preview page functionality
            if (!empty($request->preview) && $request->preview == 'preview') {
                $previewData = $request->all();
                $previewData['fees'] = number_format($charges, 2, ".", "");
                return  response(['status' => 'preview', 'response' => $previewData]);
            }

            /*start for verify pin*/
            if (empty($request->pin))
                return response(['status' => 'error', 'msg' => 'Pin is Required.']);

            if (empty($request->pin) || count($request->pin) != '4')
                return response(['status' => 'error', 'msg' => 'Please Enter Valid Pin!']);

            $pin = implode('', $request->pin);
            if (trim($pin) != Auth::user()->pin)
                return response(['status' => 'error', 'msg' => 'Pin is not Verified!']);
            /*end for verify pin*/


            $api_status = 'pending';
            $response = [];
            /*start api transfer functionality*/
            if ($amount <= 5000) {

                // $payunie_parveen = ApiList::where('status',1)->where()->find();
                $payment_channel = (object)$request->payment_channel;

                $payment_para = [
                    'mobile_number'  => Auth::user()->mobile_number,
                    'account_number' => $payment_channel->account_number,
                    'ifsc_code'      => $payment_channel->ifsc_code,
                    'amount'         => $amount,
                    'receiver_name'  => $request->receiver_name,
                    'bank_name'      => $payment_channel->bank_name,
                ];
                $payment_api = new PaymentApi();

                $apiLists = ApiList::where('retailer_ids', 'all', [Auth::user()->_id])->orderBy('sort', 'ASC')->get();

                if (!$apiLists->isEmpty()) {
                    $res = '';
                    foreach ($apiLists as $api) {

                        if ($api->status == 1 && $api->name == 'Payunie - Preet Kumar') {
                            $res = $payment_api->payunie($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Payunie -Rashid Ali') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res = $payment_api->payunie1($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Pay2All- Parveen') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res =  $payment_api->pay2All($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'odinmo') {
                            if (empty($res)) {
                                $OdnimoPaymentApi = new OdnimoPaymentApi();
                                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
                            }
                        }

                        if ($api->status == 1 && $api->name == 'CLICKnCASH') {
                            $clicknCash = new ClicknCash();
                            $res = $clicknCash->payout($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'PayTel') {
                            $PayTel = new PayTel();
                            $res = $PayTel->payout($payment_para);
                        }
                    }
                }

                $response = [];
                if (!empty($res)) {
                    $response = $res['response'];
                    $api_status = $res['status'];
                }
            }
            /*start api transfer functionality*/

            $Transaction = new Transaction();
            $Transaction->transaction_id  = uniqCode(3) . rand(111111, 999999);
            $Transaction->retailer_id     = Auth::user()->_id;
            $Transaction->outlet_id       = Auth::user()->outlet_id;
            $Transaction->mobile_number   = Auth::user()->mobile_number;
            $Transaction->sender_name     = Auth::user()->full_name;
            $Transaction->amount          = $request->amount;
            $Transaction->transaction_fees = number_format($charges, 2, ".", "");
            $Transaction->receiver_name   = $request->receiver_name;
            $Transaction->payment_mode    = 'bank_account'; //$request->payment_mode;
            $Transaction->payment_channel = $request->payment_channel;
            $Transaction->status          = $api_status;
            $Transaction->type            = 'payout';
            $Transaction->pancard_no      = $request->pancard_no;
            $Transaction->ip_address     = ip_address();
            if (!empty($response))
                $Transaction->response       = $response;

            if (!empty($request->file('pancard')))
                $Transaction->pancard  = singleFile($request->file('pancard'), 'attachment/transaction');

            if (!$Transaction->save())
                return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            /*start passbook debit functionality*/
            $transaction_id   = $Transaction->_id;
            $amount        = $Transaction->amount;
            $receiver_name = $Transaction->receiver_name;
            $payment_date  = $Transaction->created;
            $status        = 'success';
            $payment_mode  = $Transaction->payment_mode;
            $transaction_fees = $Transaction->transaction_fees;
            $type          = $Transaction->type;
            $retailer_id   = $Transaction->retailer_id;

            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $transaction_id);
            /*end passbook debit functionality*/

            $newTransaction = [
                'old_trans_id' => $Transaction->_id,
                'retailer_id' => $Transaction->retailer_id,
                'outlet_id' => $Transaction->outlet_id,
                'mobile_number' => $Transaction->mobile_number,
                'transaction_id' => $Transaction->transaction_id,
                'sender_name' => trim($Transaction->sender_name),
                'amount' => $request->amount,
                'transaction_fees' => $Transaction->transaction_fees,
                'receiver_name' => $request->receiver_name,
                'payment_mode' => $Transaction->payment_mode,
                'payment_channel' => $request->payment_channel,
                'status' => $Transaction->status,
                'type' => $Transaction->type,
                'response' => $response,
            ];

            $this->newTransaction($newTransaction);

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function feeDetails(Request $request)
    {
        try {
            $amount = $request->amount;

            $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();

            $charges = 0;
            if (!empty($outlet->bank_charges)) {

                foreach ($outlet->bank_charges as $charge) {
                    $charges = '';
                    if ($charge['type'] === 'inr') { // here all inr charges

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = $charge['charges'];
                            break;
                        }
                    } else if ($charge['type'] === 'persantage') { //calculate persantage here

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = ($charge['charges'] / 100) * $amount;
                            break;
                        }
                    }
                }

                return response(['status' => 'success', 'charges' => number_format((float)$charges, 2, '.', '')]);
            }
            return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function receipt($id)
    {
        $data['transaction'] = Transaction::find($id);
        return view('retailer/transaction/receipt', $data);
    }

    //for export sample import csv file
    public function sampleCsv()
    {
        try {
            //file name here
            $file_name = 'payout-sample';
            $fields = ['Amount', 'Beneficiary', 'Bank Name', 'Account Number', 'IFSC Code'];

            $delimiter = ",";

            if (!file_exists('sampleCsv')) //if folder not exit then create folder
                mkdir('sampleCsv', 0777, true);

            $f = fopen('sampleCsv/' . $file_name . '.csv', 'w');

            fputcsv($f, $fields, $delimiter);

            // Move back to beginning of file
            fseek($f, 0);

            // headers to download file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '.csv";');

            readfile('sampleCsv/' . $file_name . '.csv');
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function import(Request $request)
    {
        try {
            $filename = $_FILES['file']['name'];

            if (!empty($filename)) {
                $file = fopen($_FILES['file']['tmp_name'], "r");
                $ctr = 1;
                $importData = [];
                $previewData = [];
                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($ctr != 1) {

                        /*start check payment mode here*/
                        $payment_channel = ['bank_name' => $getData[2], 'account_number' => sprintf('%.0f', floatval($getData[3])), 'ifsc_code' => $getData[4]];
                        /*end check payment mode here*/

                        $importData[] = [
                            'transaction_id'  => uniqCode(3) . rand(111111, 999999),
                            'retailer_id'     => Auth::user()->_id,
                            'outlet_id'       => Auth::user()->outlet_id,
                            'mobile_number'   => Auth::user()->mobile_number,
                            'sender_name'     => Auth::user()->full_name,
                            'amount'          => $getData[0],
                            'receiver_name'   => $getData[1],
                            'payment_channel' => $payment_channel,
                            'status'          => 'pending',
                            'type'            => 'bulk_payout'
                        ];

                        $previewData[] = [
                            'amount'          => $getData[0],
                            'receiver_name'   => $getData[1],
                            'payment_channel' => $payment_channel,
                            'comment'         => ''
                        ];
                    }
                    $ctr++;
                }


                $validator = Validator::make($importData, [
                    '*.amount' => 'required|numeric|not_in:0',
                    '*.receiver_name' => 'required',
                    '*.payment_channel.bank_name' => 'required',
                    '*.payment_channel.account_number' => 'required|numeric',
                    '*.payment_channel.ifsc_code' => 'required'
                ]);

                if (!empty($validator->errors()->all())) {
                    $messages = $validator->errors()->messages();
                    $v_message = '';
                    foreach ($messages as $msg) {
                        $v_message .= '<p class="def">' . $msg[0] . '.</p>';
                    }
                    $mg = str_replace('The', ' ', $v_message);
                    return response(['status' => 'error', 'msg' => str_replace('.', ' ', $mg)]);
                }


                session()->put('importData', $importData);
                session()->put('previewData', $previewData);
                $dataV = '';
                if (!empty($previewData))
                    $dataV = self::previewData($previewData);


                return response(['status' => 'preview', 'data' => $dataV]);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private function previewData($previewData)
    {
        $table_data = '<table class="table table-sm" id="preview-table">';
        $table_data .= '<tr>
        <th>Beneficiary</th>
        <th>Bank Name</th>
        <th>A/C No.</th>
        <th>IFSC</th>
        <th>Amount</th>
        <th colspan="2">Status</th>

        </tr>';
        $total_amount = 0;
        $no = 0;;
        foreach ($previewData as $da) {
            $total_amount += $da['amount'];

            $table_data .= '<tr class="preview-table">
            <td>' . $da['receiver_name'] . '</td>
            <td>' . $da['payment_channel']['bank_name'] . '</td>
            <td>' . $da['payment_channel']['account_number'] . '</td>
            <td>' . $da['payment_channel']['ifsc_code'] . '</td>
            <td>' . mSign($da['amount']) . '</td>
            <td><span class="tag-small-warning">Pending</span></td>
            </tr>';
            $no++;
        }
        $table_data .= '<tr class="preview-table"><th colspan="4" style="text-align:end;">Total Amount</th>
        <th colspan="2">' . mSign($total_amount) . '</th></tr></table>';

        // session()->put('total_amount',$total_amount);
        // session()->put('no_of_record', $no);

        $data = ['table_data' => $table_data, 'total_amount' => $total_amount, 'no_of_record' => $no];
        return $data;
    }

    public function importSequence(Request $request)
    {

        $index = $request->index;
        // $previewData = session('previewData');
        $import = session('importData');
        $importData = $import[$index];
        $response = [];

        $payment_channel = ['bank_name' => $importData['payment_channel']['bank_name'], 'account_number' => $importData['payment_channel']['account_number'], 'ifsc_code' => $importData['payment_channel']['ifsc_code']];

        /*start check amount available in wallet or not*/
        $amount = $importData['amount'];
        $outlet = Outlet::select('bank_charges', 'security_amount')->where('_id', Auth::user()->outlet_id)->first();
        $charges = 0;
        if (!empty($outlet)) {

            foreach ($outlet->bank_charges as $charge) {
                if ($charge['type'] == 'inr') {

                    if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                        $charges = $charge['charges'];
                        break;
                    }
                } else if ($charge['type'] == 'persantage') {

                    if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                        $charges = ($charge['charges'] / 100) * $amount;
                        break;
                    }
                }
            }
        }
        $total_amount = $amount + $charges;
        $s_amount = !empty($outlet->security_amount) ? $outlet->security_amount : 0;
        $security_amount = ($total_amount) + ($s_amount);
        /*end check amount available in wallet or not*/

        if ($security_amount >= Auth()->user()->available_amount) {
            $comment = '<span class="text-danger">You have not Sufficient Amount.</span>';
            $status = '<span class="tag-small-danger">Failed</span>';
        } else {


            $api_status = 'pending';
            /*start api transfer functionality*/
            if ($amount <= 5000) {

                // $payunie_parveen = ApiList::where('status',1)->where()->find();
                $payment_channel1 = (object)$payment_channel;

                $payment_para = [
                    'mobile_number'  => Auth::user()->mobile_number,
                    'account_number' => trim($payment_channel1->account_number),
                    'ifsc_code'      => $payment_channel1->ifsc_code,
                    'amount'         => $amount,
                    'receiver_name'  => $importData['receiver_name'],
                    'bank_name'      => $payment_channel1->bank_name,
                ];
                $payment_api = new PaymentApi();

                $apiLists = ApiList::where('retailer_ids', 'all', [Auth::user()->_id])->orderBy('sort', 'ASC')->get();
                if (!$apiLists->isEmpty()) {
                    $res = '';
                    foreach ($apiLists as $api) {

                        if ($api->status == 1 && $api->name == 'Payunie - Preet Kumar') {
                            $res = $payment_api->payunie($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Payunie -Rashid Ali') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res = $payment_api->payunie1($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Pay2All- Parveen') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res =  $payment_api->pay2All($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'odinmo') {
                            if (empty($res)) {
                                $OdnimoPaymentApi = new OdnimoPaymentApi();
                                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
                            }
                        }
                    }
                }

                if (!empty($res)) {
                    $response = $res['response'];
                    $api_status = $res['status'];
                }
            }
            /*start api transfer functionality*/

            $transaction = new Transaction(); // initialize transaction model
            $transaction->transaction_id   = $importData['transaction_id'];
            $transaction->retailer_id      = $importData['retailer_id'];
            $transaction->outlet_id        = $importData['outlet_id'];
            $transaction->mobile_number    = $importData['mobile_number'];
            $transaction->sender_name      = $importData['sender_name'];
            $transaction->amount           = $importData['amount'];
            $transaction->transaction_fees = number_format($charges, 2, ".", "");
            $transaction->receiver_name    = $importData['receiver_name'];
            $transaction->payment_channel  = $payment_channel;
            $transaction->status           = $api_status;
            $transaction->type             = 'bulk_payout';
            $transaction->response         = $response;
            $transaction->ip_address     = ip_address();

            $csvImport =  $transaction->save();

            //update toupup amount here
            if ($csvImport) {
                if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                    return response(['status' => 'error', 'msg' => 'Something went wrong!']);

                /*start passbook debit functionality*/
                $transaction_id   = $transaction->_id;
                $amount        = $transaction->amount;
                $receiver_name = $transaction->receiver_name;
                $payment_date  = $transaction->created;
                $status        = 'success';
                $payment_mode  = $transaction->payment_mode;
                $transaction_fees = $transaction->transaction_fees;
                $type          = $transaction->type;
                $retailer_id   = $transaction->retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $transaction_id);
                /*end passbook debit functionality*/

                $newTransaction = [
                    'old_trans_id' => $transaction->_id,
                    'retailer_id' => $transaction->retailer_id,
                    'outlet_id' => $transaction->outlet_id,
                    'mobile_number' => $transaction->mobile_number,
                    'transaction_id' => $transaction->transaction_id,
                    'sender_name' => trim($transaction->sender_name),
                    'amount' => $transaction->amount,
                    'transaction_fees' => $transaction->transaction_fees,
                    'receiver_name' => $transaction->receiver_name,
                    'payment_mode' => $transaction->payment_mode,
                    'payment_channel' => $transaction->payment_channel,
                    'status' => $transaction->status,
                    'type' => $transaction->type,
                    'response' => $response,
                ];

                $this->newTransaction($newTransaction);
            }
            $comment = '<span class="text-success">Import Successfully!</span>';
            $status  = '<span class="tag-small">Success</span>';
        }
        // }

        $previewData = [
            'amount'          => $importData['amount'],
            'receiver_name'   => $importData['receiver_name'],
            'payment_channel' => $payment_channel,
            'status'          => $status,
            'comment'         => $comment
        ];

        $dataV = '';
        if (!empty($previewData))
            $dataV = self::previewData1($previewData);
        return response(['status' => 'preview', 'all_row' => count($import), 'index' => $index + 1, 'data' => $dataV]);
    }


    private function previewData1($previewData)
    {

        $table_data = '<tr>
            <td>' . $previewData['receiver_name'] . '</td>
            <td>' . $previewData['payment_channel']['bank_name'] . '</td>
            <td>' . $previewData['payment_channel']['account_number'] . '</td>
            <td>' . $previewData['payment_channel']['ifsc_code'] . '</td>
            <td>' . mSign($previewData['amount']) . '</td>
            <td>' . $previewData['status'] . '</td>
            <td>' . $previewData['comment'] . '</td>
            </tr>';

        return $table_data;
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
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];

            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query()->with(['UserName'])->where('retailer_id', Auth::user()->_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->banficiary))
                $query->where('receiver_name', $request->banficiary);

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

            $transactions = $query->get();


            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;

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
                $transaction_val[] = !empty($transaction->response['utr_number']) ? $transaction->response['utr_number'] : '-';
                $transaction_val[] = strtoupper(str_replace('_', ' ', $transaction->status));
                $transaction_val[] = !empty($transaction->created) ? date('Y-m-d H:i', $transaction->created) : '';
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
                'Transaction ID', 'Customer Name', 'Customer Phone', 'Mode', 'Amount', 'Fees', 'Beneficiary', 'IFSC', 'Account No.', 'Bank Name',
                'UTR Number', 'Status', 'Request Date', 'Action By', 'Action Date'
            ];

            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Transaction::query()->with(['UserName'])->where('status', 'refund_pending')->where('retailer_id', Auth::user()->_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->banficiary))
                $query->where('receiver_name', $request->banficiary);

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

            $transactions = $query->get();

            if ($transactions->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($transactions as $transaction) {

                $payment = (object)$transaction->payment_channel;

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
                $transaction_val[] = !empty($transaction->utrs) ? $transaction->utrs : (!empty($transaction->response['utr_number']) ? $transaction->response['utr_number'] : '-');
                $transaction_val[] = strtoupper(str_replace('_', ' ', $transaction->status));
                $transaction_val[] = !empty($transaction->created) ? date('Y-m-d H:i', $transaction->created) : '';
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

    public function payoutClame(Request $request)
    {
        try {

            $transaction = Transaction::find($request->trans_id);

            if ($transaction->status == 'refund_pending') {
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
                addTopupAmount($retailer_id, $amount, $transaction_fees, 1);
                //insert data in transfer history collection
                transferHistory($retailer_id, $amount + $transaction_fees, $receiver_name, $payment_date, $status, $payment_mode, $type, 0, 'credit', 0, $bank_details, $transaction_id);
            }
            $response['action_by']     = Auth::user()->_id;
            $response['action_date']   = time();
            $response['action']        = 'Payout Clame by Retailer';
            $transaction->response     = $response;
            $transaction->status = 'refund';
            if ($transaction->save())
                return response(['status' => 'success', 'msg' => 'Transaction Refund Successfully!']);

            return response(['status' => 'error', 'msg' => 'Transaction not Refund!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function report(Request $request)
    {
        try {
            $query = Transaction::query()->where('retailer_id', Auth::user()->_id);

            if (!empty($request->type))
                $query->where('type', $request->type);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->banficiary))
                $query->where('receiver_name', $request->banficiary);

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
                            'response'         => $val['response']
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
            return view('retailer.transaction.report', $data);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private function newTransaction($request)
    {
        $request = (object)$request;

        //insert new record
        $newTrans = new NewTransaction();
        $newTrans->old_trans_id     = $request->old_trans_id;
        $newTrans->retailer_id      = $request->retailer_id;
        $newTrans->outlet_id        = $request->outlet_id;
        if (!empty($request->otp))
            $newTrans->otp          = $request->otp;
        $newTrans->mobile_number    = $request->mobile_number;
        $newTrans->customer_name    = $request->sender_name;

        $newTrans->transaction_id   = $request->transaction_id;
        $newTrans->sender_name      = trim($request->sender_name);
        $newTrans->amount           = $request->amount;
        $newTrans->transaction_fees = $request->transaction_fees;
        $newTrans->receiver_name    = $request->receiver_name;
        $newTrans->payment_mode     = $request->payment_mode;
        $newTrans->payment_channel  = $request->payment_channel;
        $newTrans->status           = $request->status;
        $newTrans->type             = $request->type;
        $newTrans->ip_address     = ip_address();

        if (!empty($request->pancard_no))
            $newTrans->pancard_no     = $request->pancard_no;

        if (!empty($request->pancard))
            $newTrans->pancard        = $request->pancard;

        if (!empty($request->response))
            $newTrans->response       = $request->response;

        if (!empty($request->verified))
            $newTrans->verified       = $request->verified;

        $newTrans->save();
    }

    public function MRecharge(Request $request)
    {
        try {
            /*start check amount available in wallet or not*/
            $amount = $request->amount;
            $outlet = Outlet::select('bank_charges', 'security_amount')->where('_id', Auth::user()->outlet_id)->first();
            $charges = 0;
            if (!empty($outlet->bank_charges)) {
                foreach ($outlet->bank_charges as $charge) {
                    if ($charge['type'] == 'inr') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = $charge['charges'];
                            break;
                        }
                    } else if ($charge['type'] == 'persantage') {

                        if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount) {
                            $charges = ($charge['charges'] / 100) * $amount;
                            break;
                        }
                    }
                }
            } else {
                return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
            }

            $total_amount = $amount + $charges;
            $s_amount = !empty($outlet->security_amount) ? $outlet->security_amount : 0;
            $security_amount = ($total_amount) + ($s_amount);
            if ($security_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount.']);
            /*end check amount available in wallet or not*/

            /*start for verify pin*/
            if (empty($request->pin))
                return response(['status' => 'error', 'msg' => 'Pin is Required.']);

            if (empty($request->pin) || count($request->pin) != '4')
                return response(['status' => 'error', 'msg' => 'Please Enter Valid Pin!']);

            $pin = implode('', $request->pin);
            if (trim($pin) != Auth::user()->pin)
                return response(['status' => 'error', 'msg' => 'Pin is not Verified!']);
            /*end for verify pin*/


            $api_status = 'pending';
            $response = [];
            /*start api transfer functionality*/
            if ($amount <= 5000) {

                // $payunie_parveen = ApiList::where('status',1)->where()->find();
                $payment_channel = (object)$request->payment_channel;

                $payment_para = [
                    'mobile_number'  => Auth::user()->mobile_number,
                    'account_number' => $payment_channel->account_number,
                    'ifsc_code'      => $payment_channel->ifsc_code,
                    'amount'         => $amount,
                    'receiver_name'  => $request->receiver_name,
                    'bank_name'      => $payment_channel->bank_name,
                ];
                $payment_api = new PaymentApi();

                $apiLists = ApiList::where('retailer_ids', 'all', [Auth::user()->_id])->orderBy('sort', 'ASC')->get();
                if (!$apiLists->isEmpty()) {
                    $res = '';
                    foreach ($apiLists as $api) {

                        if ($api->status == 1 && $api->name == 'Payunie - Preet Kumar') {
                            $res = $payment_api->payunie($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Payunie -Rashid Ali') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res = $payment_api->payunie1($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'Pay2All- Parveen') {
                            if ((!empty($res['insufficient']) && $res['insufficient'] == 'Insufficient wallet Balance') || empty($res))
                                $res =  $payment_api->pay2All($payment_para);
                        }

                        if ($api->status == 1 && $api->name == 'odinmo') {
                            if (empty($res)) {
                                $OdnimoPaymentApi = new OdnimoPaymentApi();
                                $res = $OdnimoPaymentApi->AddBeneficiary($payment_para);
                            }
                        }

                        if ($api->status == 1 && $api->name == 'CLICKnCASH') {
                            $clicknCash = new ClicknCash();
                            $res = $clicknCash->payout($payment_para);
                        }
                    }
                }

                $PayTel = new PayTel();
                $res = $PayTel->payout($payment_para);
                pr($res, 1);
                die;
                $response = [];
                if (!empty($res)) {
                    $response = $res['response'];
                    $api_status = $res['status'];
                }
            }
            /*start api transfer functionality*/

            $Transaction = new Transaction();
            $Transaction->transaction_id  = uniqCode(3) . rand(111111, 999999);
            $Transaction->retailer_id     = Auth::user()->_id;
            $Transaction->outlet_id       = Auth::user()->outlet_id;
            $Transaction->mobile_number   = Auth::user()->mobile_number;
            $Transaction->sender_name     = Auth::user()->full_name;
            $Transaction->amount          = $request->amount;
            $Transaction->transaction_fees = number_format($charges, 2, ".", "");
            $Transaction->receiver_name   = $request->receiver_name;
            $Transaction->payment_mode    = 'bank_account'; //$request->payment_mode;
            $Transaction->payment_channel = $request->payment_channel;
            $Transaction->status          = $api_status;
            $Transaction->type            = 'payout';
            $Transaction->pancard_no      = $request->pancard_no;
            $Transaction->ip_address     = ip_address();
            if (!empty($response))
                $Transaction->response       = $response;

            if (!empty($request->file('pancard')))
                $Transaction->pancard  = singleFile($request->file('pancard'), 'attachment/transaction');

            if (!$Transaction->save())
                return response(['status' => 'error', 'msg' => 'Transaction Request not Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            /*start passbook debit functionality*/
            $transaction_id   = $Transaction->_id;
            $amount        = $Transaction->amount;
            $receiver_name = $Transaction->receiver_name;
            $payment_date  = $Transaction->created;
            $status        = 'success';
            $payment_mode  = $Transaction->payment_mode;
            $transaction_fees = $Transaction->transaction_fees;
            $type          = $Transaction->type;
            $retailer_id   = $Transaction->retailer_id;

            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $transaction_id);
            /*end passbook debit functionality*/

            $newTransaction = [
                'old_trans_id' => $Transaction->_id,
                'retailer_id' => $Transaction->retailer_id,
                'outlet_id' => $Transaction->outlet_id,
                'mobile_number' => $Transaction->mobile_number,
                'transaction_id' => $Transaction->transaction_id,
                'sender_name' => trim($Transaction->sender_name),
                'amount' => $request->amount,
                'transaction_fees' => $Transaction->transaction_fees,
                'receiver_name' => $request->receiver_name,
                'payment_mode' => $Transaction->payment_mode,
                'payment_channel' => $request->payment_channel,
                'status' => $Transaction->status,
                'type' => $Transaction->type,
                'response' => $response,
            ];

            $this->newTransaction($newTransaction);

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
