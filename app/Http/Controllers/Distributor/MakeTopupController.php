<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Http\Validation\TopupValidation;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use App\Models\TransferHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MakeTopupController extends Controller
{

    public function index(Request $request)
    {
        try {
           try {
            $query = Topup::where('retailer_id', Auth::user()->_id);

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $topups = $query->whereBetween('created', [$start_date, $end_date])->paginate($perPage);

            $data['topup_request'] = $topups;

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            return view('distributor.make_topup.topup_history', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function outletPaymentMode(Request $request)
    {

        $retailer_id = Auth::user()->_id;

        $option = '<option value="">Select</option>';
        switch ($request->payment_mode) {
            case "bank_account":
                $query = BankAccount::query();
                $query->where(function ($q) use ($retailer_id) {
                    $q->where('retailer_ids', 'all', [$retailer_id]);
                });
                $selectPayment = $query->get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->bank_name . '</option>';
                }
                break;
            case "upi_id":
                $query = Upi::query();
                $query->where(function ($q) use ($retailer_id) {
                    $q->where('retailer_ids', 'all', [$retailer_id]);
                });
                $selectPayment = $query->get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->name . '</option>';
                }
                break;
            case "qr_code":
                $query = QrCode::query();
                $query->where(function ($q) use ($retailer_id) {
                    $q->where('retailer_ids', 'all', [$retailer_id]);
                });
                $selectPayment = $query->get();
                foreach ($selectPayment as $payment) {
                    $option .= '<option value="' . $payment->_id . '">' . $payment->name . '</option>';
                }
                break;
            default:
                $selectPayment = [];
                break;
        }
        die(json_encode($option));
    }


    public function paymentDetails(Request $request)
    {

        switch ($request->payment_mode) {
            case "bank_account":
                $payment = BankAccount::find($request->payment_id);
                $data = '<table class="table table-sm table-bordered">
                <tr>
                    <th>Bank Name</th>
                    <td>' . ucwords($payment->bank_name) . '</td>
                </tr>
                <tr>
                    <th>Account Number</th>
                    <td>' . $payment->account_number . '</td>
                </tr>
                <tr>
                    <th>IFSC Code</th>
                    <td>' . $payment->ifsc_code . '</td>
                </tr>
            </table>';
                break;
            case "upi_id":
                $payment = Upi::find($request->payment_id);
                $data = '<table class="table table-sm table-bordered">
                <tr>
                    <th>UPI ID Name</th>
                    <td>' . ucwords($payment->name) . '</td>
                </tr>
                <tr>
                    <th>UPI ID</th>
                    <td>' . $payment->upi_id . '</td>
                </tr>
            </table>';
                break;
            case "qr_code":
                $payment = QrCode::find($request->payment_id);
                $data = '<div class="card w-50 py-4 m-auto">
                <img src="' . asset('attachment/payment_mode/' . $payment->qr_code) . '">
                <div class="text-center"><span>' . ucwords($payment->name) . '</span></div>
                </div>';
                break;
            default:
                $data = '<div>Not Found Any Data!</div>';
                break;
        }
        die(json_encode($data));
    }


    public function store(TopupValidation $request)
    {
        $topup = new Topup();
        $topup->retailer_id            = Auth::user()->_id;
        $topup->outlet_id              = Auth::user()->outlet_id;
        $topup->payment_id             = uniqCode(3) . rand(1111, 9999);
        $topup->payment_has_code       = uniqCode(16);
        $topup->payment_mode           = $request->payment_mode;
        $topup->payment_reference_id   = $request->payment_reference_id;
        $topup->amount                 = $request->amount;
        $topup->comment                = $request->comment;
        $topup->attachment             = $request->attachment;
        $topup->status                 = 'pending';
        $topup->payment_date           = strtotime($request->payment_date);
        //for file uploade
        if (!empty($request->file('attachment')))
            $topup->attachment  = singleFile($request->file('attachment'), 'attachment/payment_request_proff');

        if ($topup->save())
            return response(['status' => 'success', 'msg' => 'Your Request is Successfully for Topup!']);

        return response(['status' => 'error', 'msg' => 'Your Topup Request is Failed, Please Try Again!']);
    }





    public function edit(Topup $Topup)
    {

        try {
            die(json_encode($Topup));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request, Topup $Topup)
    {

        $qr_code = $Topup;
        $qr_code->name         = $request->name;
        $qr_code->status       = $request->status;
        //for file uploade
        if (!empty($request->file('qr_code')))
            $qr_code->qr_code  = singleFile($request->file('qr_code'), 'attachment/payment_mode');


        if ($qr_code->save())
            return response(['status' => 'success', 'msg' => 'QR Code Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'QR Code not Updated Successfully!']);
    }


    public function destroy($id)
    {
        try {
            $res = Topup::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'QR Code Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'QR Code not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function TopupStatus(Request $request)
    {

        try {
            $Topup = Topup::find($request->id);
            $Topup->status = (int)$request->status;
            $Topup->save();
            if ($Topup->status == 1)
                return response(['status' => 'success', 'msg' => 'This QR Code is Active!', 'val' => $Topup->status]);

            return response(['status' => 'success', 'msg' => 'This QR Code is Inactive!', 'val' => $Topup->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }



    public function transactionHistory(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = TransferHistory::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = TransferHistory::LikeColumn($searchValue);
            $data = TransferHistory::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = TransferHistory::where('retailer_id', Auth::user()->_id)->offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $dataArr[] = [
                'sl_no'             => $i,
                'used_amount'       => mSign($val->amount),
                'reciever_name'     => ucwords($val->receiver_name),
                'payment_date'      => date('Y-m-d', $val->payment_date),
                'status'            => $val->status
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


    // public function topupHistory(Request $request)
    // {

    // }

    public function export(Request $request)
    {

        try {
            $file_name = 'topup-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Outlet', 'Amount', 'Payment Date', 'Payment Mode', 'Channel',
                'Status', 'Datetime'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Topup::where('retailer_id', Auth::user()->_id);

            if (!empty($request->transaction_id))
                $query->where('payment_id', $request->transaction_id);

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
            $topups = $query->get();

            if ($topups->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $transactionArr = [];
            foreach ($topups as $topup) {

                $topup_val[] = $topup->payment_id;
                $topup_val[] = !empty($topup->RetailerName['outlet_name']) ? $topup->RetailerName['outlet_name'] : '';
                $topup_val[] = $topup->amount;
                $topup_val[] = date('Y-m-d H:i:s A', $topup->payment_date);
                $topup_val[] = !empty($topup->payment_mode) ? ucwords(str_replace('_', ' ', $topup->payment_mode)) : '';
                $topup_val[] = !empty($topup->payment_channel) ? ucwords(str_replace('_', ' ', $topup->payment_channel)) : '';
                $topup_val[] = strtoupper($topup->status);
                $topup_val[] = date('Y-m-d H:i:s A', $topup->created);

                $transactionArr = $topup_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $topup_val = [];
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
