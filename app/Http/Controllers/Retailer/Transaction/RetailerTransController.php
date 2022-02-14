<?php

namespace App\Http\Controllers\Retailer\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerTransController extends Controller
{

    public function index()
    {
        try {
             $data['retailerTrans'] = RetailerTrans::where('retailer_id', Auth::user()->_id)->get();
            return view('retailer.transaction.retailer_display',$data);
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
            if (!empty($outlet)) {
                $charges = 0;
                foreach ($outlet->bank_charges as $charge) {
                    if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                        $charges = $charge['charges'];
                }
            }
            $total_amount = $amount + $charges;
            if ($total_amount >= Auth()->user()->available_amount)
                return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
            /*end check amount available in wallet or not*/

            $RetailerTrans = new RetailerTrans();
            $RetailerTrans->retailer_id     = Auth::user()->_id;
            $RetailerTrans->outlet_id       = Auth::user()->outlet_id;
            $RetailerTrans->mobile_number   = Auth::user()->mobile_number;
            $RetailerTrans->sender_name     = Auth::user()->full_name;
            $RetailerTrans->amount          = $request->amount;
            $RetailerTrans->transaction_fees = $charges;
            $RetailerTrans->receiver_name   = $request->receiver_name;
            $RetailerTrans->payment_mode    = $request->payment_mode;
            $RetailerTrans->payment_channel = $request->payment_channel;
            $RetailerTrans->status          = 'pending';
            $RetailerTrans->pancard_no      = $request->pancard_no;

            if (!empty($request->file('pancard')))
                $RetailerTrans->pancard  = singleFile($request->file('pancard'), 'attachment/transaction');

            if (!$RetailerTrans->save())
                return response(['status' => 'error', 'msg' => 'Transaction Request not  Created!']);

            //update toupup amount here
            if (!spentTopupAmount(Auth()->user()->_id, $total_amount))
                return response(['status' => 'error', 'msg' => 'Something went wrong!']);

            return response(['status' => 'success', 'msg' => 'Transaction Request Created Successfully!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function show(RetailerTrans $RetailerTrans)
    {
    }


    public function edit(RetailerTrans $RetailerTrans)
    {
        try {
            die(json_encode($RetailerTrans));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    //for export sample import csv file
    public function sampleCsv()
    {
        try {
            //file name here
            $file_name = 'payout-sample';
            $fields = ['Amount', 'Beneficiary Name', 'Payment Channel(UPI/Bank Account)', 'UPI ID', 'Bank Name', 'Account Number', 'IFSC Code'];

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


    // import csv file
    public function import(Request $request)
    {
        try {
            $filename = $_FILES['file']['name'];

            if (!empty($filename)) {
                $file = fopen($_FILES['file']['tmp_name'], "r");
                $ctr = 1;
                $csvError = FALSE;
                $csvImport = FALSE;
                $error = array();
                $responseArray[0] = ['Amount', 'Receiver Name', 'Payment Channel(UPI/Bank Account)', 'UPI ID', 'Bank Name', 'Account Number', 'IFSC Code'];
                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($ctr != 1) {
                        $responseArray[$ctr] = $getData;

                        /*start check amount available in wallet or not*/
                        $amount = $getData[0];
                        $outlet = Outlet::select('bank_charges')->where('_id', Auth::user()->outlet_id)->first();
                        if (!empty($outlet)) {
                            $charges = 0;
                            foreach ($outlet->bank_charges as $charge) {
                                if ($charge['from_amount'] <= $amount && $charge['to_amount'] >= $amount)
                                    $charges = $charge['charges'];
                            }
                        }
                        $total_amount = $amount + $charges;
                        if ($total_amount >= Auth()->user()->available_amount)
                            return response(['status' => 'error', 'msg' => 'You have not Sufficient Amount']);
                        /*end check amount available in wallet or not*/

                        $retailerTrans = new RetailerTrans(); // initialize retailerTrans model

                        /*start check payment mode here*/
                        $payment_channel = [];

                        if (strtolower($getData[2]) == 'upi')
                            $payment_channel = ['upi_id' => $getData[3]];

                        if (strtolower(str_replace('_',' ',$getData[2])) == 'bank account')
                            $payment_channel = ['bank_name' => $getData[4], 'account_number' => $getData[5], 'ifsc_code' => $getData[6]];
                        /*end check payment mode here*/

                        $retailerTrans->retailer_id      = Auth::user()->_id;
                        $retailerTrans->outlet_id        = Auth::user()->outlet_id;
                        $retailerTrans->mobile_number    = Auth::user()->mobile_number;
                        $retailerTrans->sender_name      = Auth::user()->full_name;
                        $retailerTrans->amount           = $getData[0];
                        $retailerTrans->transaction_fees = $charges;
                        $retailerTrans->receiver_name    = $getData[1];
                        $retailerTrans->payment_mode     = strtolower(str_replace(' ', '_', $getData[2]));
                        $retailerTrans->payment_channel  = $payment_channel;
                        $retailerTrans->status           = 'pending';

                        $csvImport =  $retailerTrans->save();

                        //update toupup amount here
                        if($csvImport){
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
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = RetailerTrans::AllCountRetailer();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = RetailerTrans::LikeColumnRetailer($searchValue);
            $data = RetailerTrans::GetResultRetailer($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = RetailerTrans::where('retailer_id', Auth::user()->_id)->offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            // $action = '<a href="javascript:void(0);" class="text-info edit_bank_account" data-toggle="tooltip" data-placement="bottom" title="Edit" bank_account_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
            // $action .= '<a href="javascript:void(0);" class="text-danger remove_bank_account"  data-toggle="tooltip" data-placement="bottom" title="Remove" bank_account_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';

            if ($val->status == 'approved') {
                $status = '<strong class="text-success">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'rejected') {
                $status = '<strong class="text-danger">' . ucwords($val->status) . '</strong>';
            } else if ($val->status == 'pending') {
                $status = '<strong class="text-warning">' . ucwords($val->status) . '</strong>';
            }

            $dataArr[] = [
                'sl_no'             => $i,
                'sender_name'       => ucwords($val->sender_name),
                'mobile_number'     => $val->mobile_number,
                'amount'            => $val->amount,
                'receiver_name'     => $val->receiver_name,
                'payment_mode'      => ucwords(str_replace('_', ' ', $val->payment_mode)),
                'status'            => $status,
                'created_date'      => date('Y-m-d', $val->created),
                // 'action'            => $action
            ];
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" =>  $totalRecordswithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $dataArr
        );
        echo json_encode($response);
        exit;
    }
}
