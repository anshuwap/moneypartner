<?php

namespace App\Http\Controllers\Admin\Action;

use App\Http\Controllers\Controller;
use App\Models\CreditDebit;
use App\Models\PaymentChannel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebitController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data['outlets'] = User::select('_id', 'outlet_name')->whereIn('role', ['retailer', 'distributor'])->get();

            $query = CreditDebit::where('type', 'debit');
            if (!empty($request->outlet_id))
                $query->where('retailer_id', $request->outlet_id);

            if (!empty($request->payment_channel))
                $query->where('channel', $request->payment_channel);

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
            $data['credits'] = $query->paginate($perPage);
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            return view('admin.action.debit', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }

    public function store(Request $request)
    {

        try {
            $retailer_id  = $request->retailer_id;
            $amount       = $request->amount;
            $status       = 'success';
            $payment_mode = 'by_admin';
            $remark       = $request->remark;
            $payment_date = time();

            $user_amount = User::select('available_amount')->where('_id', $retailer_id)->first();
            $available_amount = 0;
            if (!empty($user_amount))
                $available_amount = $user_amount->available_amount;

            if ($amount > $available_amount)
                return response(['status' => 'error', 'msg' => 'Debited Failed, debited Amount is greater then available amount.']);

            $creditDebit = new CreditDebit();
            $creditDebit->retailer_id = $retailer_id;
            $creditDebit->user_id     = Auth::user()->_id;
            $creditDebit->transaction_id = uniqCode(3) . rand(111111, 999999);
            $creditDebit->amount      = $amount;
            $creditDebit->payment_date = $payment_date;
            $creditDebit->status      = $status;
            $creditDebit->payment_mode = $payment_mode;
            $creditDebit->remark      = $remark;
            $creditDebit->channel     = $request->payment_channel;
            $creditDebit->type        = 'debit';

            if ($creditDebit->save()) {
                if (!spentTopupAmount($retailer_id, $amount))
                    return response(['status' => 'error', 'msg' => 'Something went wrong!']);

                /*start passbook debit functionality*/
                $amount        = $amount;
                $receiver_name = '';
                $payment_date  = time();
                $status        = 'success';
                $payment_mode  =  $payment_mode;
                $transaction_fees = 0;
                $type          = $payment_mode;
                $retailer_id   = $retailer_id;

                transferHistory($retailer_id, $amount, $receiver_name, $payment_date, $status, $payment_mode, $type, $transaction_fees, 'debit', $remark);
                /*end passbook debit functionality*/

                return response(['status' => 'success', 'msg' => mSign($amount) . ' Debited Successfully', 'status_msg' => ucwords($status)]);
            } else {
                return response(['status' => 'error', 'msg' => 'Something went Wrong!']);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


    public function showBlance($id)
    {
        $amount = User::select('available_amount')->where('_id', $id)->first();
        $available_amount = 0;
        if (!empty($amount))
            $available_amount = $amount->available_amount;

        die(json_encode(['amount' => mSign($available_amount)]));
    }


    public function export(Request $request)
    {
        try {
            $file_name = 'manual-debit';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $passbookArray = ['Outlet Name', 'Transaction Id', 'Channel', 'Amount', 'Datetime'];
            fputcsv($f, $passbookArray, $delimiter); //put heading here

            $query = CreditDebit::where('type', 'debit');
            if (!empty($request->outlet_id))
                $query->where('retailer_id', $request->outlet_id);

            if (!empty($request->payment_channel))
                $query->where('channel', $request->payment_channel);

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
            $credits = $query->paginate($perPage);

            if ($credits->isEmpty())
                return back()->with('error', 'There is no any record for export!');

            $passbookArr = [];
            foreach ($credits as $credit) {

                $passbook_val[] = (!empty($credit->RetailerName['outlet_name'])) ? ucwords($credit->RetailerName['outlet_name']) : '';
                $passbook_val[] = $credit->transaction_id;
                $passbook_val[] = $credit->channel;
                $passbook_val[] = $credit->amount;
                $passbook_val[] = date('Y-m-d H:i', $credit->created);

                $passbookArr = $passbook_val;

                fputcsv($f, $passbookArr, $delimiter); //put heading here
                $passbook_val = [];
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
