<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{

    public function index(Request $request)
    {
        try {

            $data['employees'] = User::where('role', 'employee')->get();

            $query =  Withdrawal::query();

            if (!empty($request->employee_id))
                $query->where('employee_id', $request->employee_id);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->utr_no))
                $query->where('utr_no', $request->utr_no);

            if (!empty($request->status))
                $query->where('status', $request->status);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            }
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['withdrawals'] = $query->with(['EmployeeName'])->paginate($perPage);

            // foreach($data['withdrawals'] as $wt){
            //     echo $wt->amount.'<br>';
            //     echo $wt->EmployeeName['full_name'];
            // }
            // pr( $data['withdrawals']);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('admin.withdrawal.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {

            $id = $request->id;

            $withdrawal = Withdrawal::find($id);
            $withdrawal->status = $request->status;

            if ($request->status == 'approved')
                $withdrawal->utr_no = $request->utr_no;

            $withdrawal->action_by = Auth::user()->_id;
            $withdrawal->action_date = time();
            $withdrawal->admin_comment = $request->comment;
            if ($withdrawal->save()) {

                // debitEmpWallet(Auth::user()->_id, $withdrawal->amount);

                $employeeCms = [
                    'employee_id'    => $withdrawal->employee_id,
                    'amount'         => $withdrawal->amount,
                    'transaction_id' => '',
                    'outlet_id'      => '',
                    'retailer_id'    => '',
                    'action_by'      => Auth::user()->_id
                ];
                if ($withdrawal->status == 'approved') {
                    debitEmployeeCms($employeeCms); // debit amount in earned history
                } else {
                    employeeWallet($withdrawal->employee_id, $withdrawal->amount); // add amount in employee wallet
                    employeeCms($employeeCms); //credit amount in earned history
                }

                return response(['status' => 'success', 'msg' => 'Withdrawal Amount ' . ucfirst($request->status) . ' Successfully!']);
            }

            return response(['status' => 'error', 'msg' => 'Something went wrong!']);
        } catch (Exception $e) {
        }
    }


    public function export(Request $request)
    {
        try {
            $file_name = 'employee-pay';

            $delimiter = ","; //dfine delimiter

            if (!file_exists('exportCsv')) //
                mkdir('exportCsv', 0777, true);

            $f = fopen('exportCsv/' . $file_name . '.csv', 'w'); //open file

            $transactionArray = [
                'Employee Name', 'Outlet Name', 'Transaction Time', 'Transaction No', 'Action By', 'Amount', 'Type',
                'Closing Amount'
            ];
            fputcsv($f, $transactionArray, $delimiter); //put heading here

            $query =  Withdrawal::query();

            if (!empty($request->employee_id))
                $query->where('employee_id', $request->employee_id);

            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->utr_no))
                $query->where('utr_no', $request->utr_no);

            if (!empty($request->status))
                $query->where('status', $request->status);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {

                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $earns = $query->whereBetween('created', [$start_date, $end_date])->orderBy('created', 'DESC')->get();

            if ($earns->isEmpty())
                return back()->with('error', 'There is no any record for export!');;

            $transactionArr = [];
            foreach ($earns as $earn) {

                $topup_val[] = !empty($earn->EmpName['full_name']) ? $earn->EmpName['full_name'] : '-';
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
