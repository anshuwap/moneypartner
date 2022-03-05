<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Support\PaymentApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{

    public function index()
    {
        try {
            $data['transactions'] = Transaction::where('retailer_id', Auth::user()->_id)->get();
            return view('retailer.transaction.display', $data);

            Session::forget('importData');
            Session::forget('previewData');
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
            $outlet  = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
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
            }
            $total_amount = $amount + $charges;

            //for preview page functionality
            if (!empty($request->preview) && $request->preview == 'preview') {
                $previewData = $request->all();
                $previewData['fees'] = $charges;
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

            if ($total_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/

            $api_status = 'pending';
            /*start api transfer functionality*/
            if ($amount <= 5000 && $request->payment_mode == 'bank_account') {

                $payment_channel = (object)$request->payment_channel;

                $payment_para = [
                    'account_number' => $payment_channel->account_number,
                    'ifsc_code'     => $payment_channel->ifsc_code,
                    'amount'        => $request->amount,
                    'receiver_name' => $request->receiver_name,
                    'bank_name'     => $payment_channel->bank_name,
                ];
                $payment_api = new PaymentApi();

                $res = $payment_api->payunie($payment_para);

                if (!empty($res) && $res['status'] == 'error')
                    $res = $payment_api->payunie1($payment_para);
                //     return response(['status' => 'error', 'msg' => $res['msg']]);

                $response = [];
                if (!empty($res) && $res['status'] == 'success') {
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
            $transaction->sender_name    = $request->sender_name;
            $transaction->amount         = $request->amount;
            $transaction->transaction_fees = $charges;
            $transaction->receiver_name  = $request->receiver_name;
            $transaction->payment_mode   = 'bank_account'; //$request->payment_mode;
            $transaction->payment_channel= $request->payment_channel;
            $transaction->status         = $api_status;
            $transaction->type           = 'dmt_transfer';
            $transaction->pancard_no     = $request->pancard_no;
            $transaction->pancard        = $pancard;
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
            $amount        = $transaction->amount;
            $receiver_name = $transaction->receiver_name;
            $payment_date  = $transaction->created;
            $status        = 'success';
            $payment_mode  = $transaction->payment_mode;
            $transaction_fees = $transaction->transaction_fees;
            $type          = $transaction->type;
            $retailer_id   = $transaction->retailer_id;

            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit');
            /*end passbook debit functionality*/

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
            $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
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
            if ($total_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/


            //for preview page functionality
            if (!empty($request->preview) && $request->preview == 'preview') {
                $previewData = $request->all();
                $previewData['fees'] = $charges;
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
            /*start api transfer functionality*/
            if ($amount <= 5000 && $request->payment_mode == 'bank_account') {

                $payment_channel = (object)$request->payment_channel;

                $payment_para = [
                    'account_number' => $payment_channel->account_number,
                    'ifsc_code'     => $payment_channel->ifsc_code,
                    'amount'        => $request->amount,
                    'receiver_name' => $request->receiver_name,
                    'bank_name'     => $payment_channel->bank_name,
                ];
                $payment_api = new PaymentApi();

                $res = $payment_api->payunie($payment_para);

                if (!empty($res) && $res['status'] == 'error')
                    $res = $payment_api->payunie1($payment_para);
                //     return response(['status' => 'error', 'msg' => $res['msg']]);

                $response = [];
                if (!empty($res) && $res['status'] == 'success') {
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
            $Transaction->transaction_fees = $charges;
            $Transaction->receiver_name   = $request->receiver_name;
            $Transaction->payment_mode    = 'bank_account'; //$request->payment_mode;
            $Transaction->payment_channel = $request->payment_channel;
            $Transaction->status          = $api_status;
            $Transaction->type            = 'payout';
            $Transaction->pancard_no      = $request->pancard_no;
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
            $amount        = $Transaction->amount;
            $receiver_name = $Transaction->receiver_name;
            $payment_date  = $Transaction->created;
            $status        = 'success';
            $payment_mode  = $Transaction->payment_mode;
            $transaction_fees = $Transaction->transaction_fees;
            $type          = $Transaction->type;
            $retailer_id   = $Transaction->retailer_id;

            transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit');
            /*end passbook debit functionality*/

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

                return response(['status' => 'success', 'charges' => $charges]);
            }
            return response(['status' => 'error', 'msg' => 'There are no any Slab Avaliable.']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
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
                // $previewData = [];
                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($ctr != 1) {

                        /*start check payment mode here*/
                        $payment_channel = ['bank_name' => $getData[2], 'account_number' => $getData[3], 'ifsc_code' => $getData[4]];
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
        $table_data .= '<tr><th>Amount</th>
        <th>Beneficiary</th>
        <th>Bank Name</th>
        <th>A/C No.</th>
        <th>IFSC</th>
        <th colspan="2">Status</th>

        </tr>';
        foreach ($previewData as $da) {
            $table_data .= '<tr class="preview-table">
            <td>' . mSign($da['amount']) . '</td>
            <td>' . $da['receiver_name'] . '</td>
            <td>' . $da['payment_channel']['bank_name'] . '</td>
            <td>' . $da['payment_channel']['account_number'] . '</td>
            <td>' . $da['payment_channel']['ifsc_code'] . '</td>
            <td><span class="tag-small-warning">Pending</span></td>
            </tr>';
        }
        $table_data .= '</table>';
        return $table_data;
    }


    public function importSequence(Request $request)
    {

        $index = $request->index;
        // $previewData = session('previewData');
        $import = session('importData');
        $importData = $import[$index];

        /*start check amount available in wallet or not*/
        $amount = $importData['amount'];
        $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
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
        /*end check amount available in wallet or not*/

        $payment_channel = ['bank_name' => $importData['payment_channel']['bank_name'], 'account_number' => $importData['payment_channel']['account_number'], 'ifsc_code' => $importData['payment_channel']['ifsc_code']];

        if ($total_amount >= Auth()->user()->available_amount) {
            $comment = '<span class="text-danger">You have not Sufficient Amount.</span>';
            $status = '<span class="tag-small-danger">Failed</span>';
        } else {
            $transaction = new Transaction(); // initialize transaction model
            $transaction->transaction_id   = $importData['transaction_id'];
            $transaction->retailer_id      = $importData['retailer_id'];
            $transaction->outlet_id        = $importData['outlet_id'];
            $transaction->mobile_number    = $importData['mobile_number'];
            $transaction->sender_name      = $importData['sender_name'];
            $transaction->amount           = $importData['amount'];
            $transaction->transaction_fees = $charges;
            $transaction->receiver_name    = $importData['receiver_name'];
            $transaction->payment_channel  = $payment_channel;
            $transaction->status           = 'pending';
            $transaction->type             = 'bulk_payout';

            $csvImport =  $transaction->save();

            //update toupup amount here
            if ($csvImport) {
                if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                    return response(['status' => 'error', 'msg' => 'Something went wrong!']);

                /*start passbook debit functionality*/
                $amount        = $transaction->amount;
                $receiver_name = $transaction->receiver_name;
                $payment_date  = $transaction->created;
                $status        = 'success';
                $payment_mode  = $transaction->payment_mode;
                $transaction_fees = $transaction->transaction_fees;
                $type          = $transaction->type;
                $retailer_id   = $transaction->retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit');
                /*end passbook debit functionality*/
            }
            $comment = '<span class="text-success">Import Successfully!</span>';
            $status  = '<span class="tag-small">Success</span>';
        }

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
            <td>' . mSign($previewData['amount']) . '</td>
            <td>' . $previewData['receiver_name'] . '</td>
            <td>' . $previewData['payment_channel']['bank_name'] . '</td>
            <td>' . $previewData['payment_channel']['account_number'] . '</td>
            <td>' . $previewData['payment_channel']['ifsc_code'] . '</td>
            <td>' . $previewData['status'] . '</td>
            <td>' . $previewData['comment'] . '</td>
            </tr>';

        return $table_data;
    }


    // import csv file
    public function importSequence1(Request $request)
    {
        try {
            $filename = $_FILES['file']['name'];

            if (!empty($filename)) {
                $file = fopen($_FILES['file']['tmp_name'], "r");
                $ctr = 1;
                $csvError = FALSE;
                $csvImport = FALSE;
                $error = array();
                $responseArray[0] =  ['Amount', 'Beneficiary', 'Bank Name', 'Account Number', 'IFSC Code'];
                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($ctr != 1) {
                        $responseArray[$ctr] = $getData;

                        /*start check amount available in wallet or not*/
                        $amount = $getData[0];
                        $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
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
                        if ($total_amount >= Auth()->user()->available_amount)
                            return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
                        /*end check amount available in wallet or not*/

                        $transaction = new Transaction(); // initialize transaction model

                        /*start check payment mode here*/
                        $payment_channel = [];

                        // if (strtolower($getData[2]) == 'upi')
                        //     $payment_channel = ['upi_id' => $getData[3]];

                        // if (strtolower(str_replace('_', ' ', $getData[2])) == 'bank account')
                        $payment_channel = ['bank_name' => $getData[2], 'account_number' => $getData[3], 'ifsc_code' => $getData[4]];
                        /*end check payment mode here*/

                        $transaction->transaction_id   = uniqCode(3) . rand(111111, 999999);
                        $transaction->retailer_id      = Auth::user()->_id;
                        $transaction->outlet_id        = Auth::user()->outlet_id;
                        $transaction->mobile_number    = Auth::user()->mobile_number;
                        $transaction->sender_name      = Auth::user()->full_name;
                        $transaction->amount           = $getData[0];
                        $transaction->transaction_fees = $charges;
                        $transaction->receiver_name    = $getData[1];
                        // $transaction->payment_mode     = strtolower(str_replace(' ', '_', $getData[2]));
                        $transaction->payment_channel  = $payment_channel;
                        $transaction->status           = 'pending';
                        $transaction->type             = 'bulk_payout';

                        $csvImport =  $transaction->save();

                        //update toupup amount here
                        if ($csvImport) {
                            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                                return response(['status' => 'error', 'msg' => 'Something went wrong!']);
                        }
                    }

                    $ctr++;
                    $error = array();
                }
                fclose($file);

                //store error list in csv file on server
                // if ($csvError == TRUE) {
                //     if (!file_exists('csvErrorList'))//if folder not exit then create folder
                //         mkdir('csvErrorList', 0777, true);

                //     $fileName = 'agentCsv' . uniqCode(3) . '.csv';
                //     $fp = fopen('csvErrorList/' . $fileName, 'w');
                //     foreach ($responseArray as $fields) {
                //         fputcsv($fp, $fields);
                //     }
                //     fclose($fp);
                //     $path = url('admin/csv-error-list/'.$fileName);
                //     $msg = 'Some record not imported. please download file and check error. <a href='.$path.'><b>Download</b></a>';
                //     return response(['status' => 'error', 'msg' => $msg, 'file_path' => $fileName]);
                // }

                if ($csvImport)
                    return response(['status' => 'success', 'msg' => 'Bulk Payout Imported successfully!']);
            }
            return response(['status' => 'error', 'msg' => 'File Not Found!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
