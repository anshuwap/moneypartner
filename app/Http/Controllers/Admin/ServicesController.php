<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Recharge;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{

    public function index(Request $request)
    {
        try {

            $query = Recharge::query();
            if (!empty($request->transaction_id))
                $query->where('txn_id', $request->transaction_id);

            if (!empty($request->mobile_no))
                $query->where('mobile_no', $request->mobile_no);

            if (!empty($request->operator))
                $query->where('operator', $request->operator);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->retailer_id))
                $query->where('retailer_id', $request->retailer_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            } else {
                // $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                // $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            // $query->whereBetween('created', [$start_date, $end_date]);
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['recharges'] = $query->orderBy('created', 'DESC')->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            $data['outlets'] = Outlet::select('_id', 'outlet_name')->where('account_status', 1)->orderBy('created', 'DESC')->get();

            return view('admin.services.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function serviceExport(Request $request)
    {
        try {
            $file_name = 'recharge-report';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Transaction ID', 'Outlet Name', 'Recharge Amount', 'Commission Amount', 'Paid Amount', 'Operator', 'Mobile/DTH No',
                'Requested Date',
                'Status',
                'Message'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query = Recharge::query();
            if (!empty($request->transaction_id))
                $query->where('txn_id', $request->transaction_id);

            if (!empty($request->mobile_no))
                $query->where('mobile_no', $request->mobile_no);

            if (!empty($request->operator))
                $query->where('operator', $request->operator);

            if (!empty($request->status))
                $query->where('status', $request->status);

            if (!empty($request->retailer_id))
                $query->where('retailer_id', $request->retailer_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            } else {
                // $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                // $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }
            // $query->whereBetween('created', [$start_date, $end_date]);
            $recharges = $query->orderBy('created', 'DESC')->get();

            if ($recharges->isEmpty())
                return back()->with('error', 'There is no any record for export!');;

            $transactionArr = [];
            foreach ($recharges as $val) {

                $r_val[] = $val->txn_id;
                $r_val[] = !empty($val->RetailerName['outlet_name']) ? $val->RetailerName['outlet_name'] : '';
                $r_val[] = $val->amount;
                $r_val[] = $val->commission_fees;
                $r_val[] = $val->paid_amount;
                $r_val[] = checkOperator($val->operator);
                $r_val[] = $val->mobile_no;
                $r_val[] = date('Y-m-d H:i:s', $val->payment_date);
                $r_val[] = strtoupper($val->status);
                $r_val[] = $val->msg;
                $transactionArr = $r_val;

                fputcsv($f, $transactionArr, $delimiter); //put heading here
                $r_val = [];
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
