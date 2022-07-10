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
}
