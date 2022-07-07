<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\EmployeeCommission;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarnHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {

            $data['outlets'] = Outlet::select('amount', 'outlet_name', '_id')->get();

            $query =  EmployeeCommission::query()->with(['Transaction', 'OutletName', 'ActionBy']);
            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

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
            $data['earnHistory'] = $query->where('employee_id', Auth::user()->_id)->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('employee.earnHistory', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function export(Request $request)
    {
        try {
            $file_name = 'employee-earn';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
               'Outlet Name', 'Transaction Time', 'Transaction No', 'Action By', 'Amount', 'Type',
                'Closing Amount'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query =  EmployeeCommission::query()->with(['Transaction', 'OutletName', 'ActionBy']);
            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $earns = $query->whereBetween('created', [$start_date, $end_date])->where('employee_id', Auth::user()->_id)->orderBy('created', 'DESC')->get();

            if ($earns->isEmpty())
                return back()->with('error', 'There is no any record for export!');;

            $transactionArr = [];
            foreach ($earns as $earn) {

                $topup_val[] = !empty($earn->OutletName['outlet_name']) ? $earn->OutletName['outlet_name'] : '-';
                $topup_val[] = date('Y-m-d H:i:s', $earn->created);
                $topup_val[] = !empty($earn->Transaction['transaction_id']) ? $earn->Transaction['transaction_id'] : '-';
                $topup_val[] = !empty($earn->ActionBy['full_name']) ? $earn->ActionBy['full_name'] : '-';
                $topup_val[] = $earn->amount;
                $topup_val[] = strtoupper($earn->type);
                $topup_val[] = $earn->closing_amount;
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
