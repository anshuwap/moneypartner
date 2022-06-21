<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{

    public function index(Request $request)
    {
        try {

            // $outlet_ids = [];
            // if (!empty(Auth::user()->outlets))
            // $outlet_ids = Auth::user()->outlets;
            // $data['outlets'] = Outlet::select('amount', 'outlet_name', '_id')->whereIn('_id', $outlet_ids)->get();

            $query =  Withdrawal::query();
            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            }
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['withdrawals'] = $query->where('employee_id', Auth::user()->_id)->paginate($perPage);

            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('employee.withdrawal.list', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {

            if ($request->amount > Auth::user()->wallet_amount)
                return response(['status' => 'error', 'msg' => 'You have not sufficient Amount in wallet!']);

            $withdrawal = new Withdrawal();
            $withdrawal->employee_id    = Auth::user()->_id;
            $withdrawal->transaction_id = uniqCode(3) . rand(111111, 999999);
            $withdrawal->amount         = $request->amount;
            $withdrawal->account_holder = $request->account_holder;
            $withdrawal->account_number = $request->account_number;
            $withdrawal->ifsc_code      = $request->ifsc_code;
            $withdrawal->comment        = $request->comment;
            $withdrawal->status         = 'pending';

            if ($withdrawal->save()) {

                debitEmpWallet(Auth::user()->_id, $withdrawal->amount);
                return response(['status' => 'success', 'msg' => 'Withdrawal Request Submitted Successfully!']);
            }

            return response(['status' => 'error', 'msg' => 'Something went wrong!']);
        } catch (Exception $e) {
        }
    }
}
