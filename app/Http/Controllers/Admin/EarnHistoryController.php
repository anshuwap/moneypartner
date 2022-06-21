<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\EmployeeCommission;
use App\Models\User;
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

          $data['employees'] = User::where('role', 'employee')->get();

            $query =  EmployeeCommission::query();
            if (!empty($request->transaction_id))
                $query->where('transaction_id', $request->transaction_id);

            if (!empty($request->outlet_id))
                $query->where('outlet_id', $request->outlet_id);
         
            if (!empty($request->employee_id))
                $query->where('employee_id', $request->employee_id);
            

            $start_date = $request->start_date;
            $end_date   = $request->end_date;

            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
                $query->whereBetween('created', [$start_date, $end_date]);
            }
            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['earnHistory'] = $query->orderBy('created','desc')->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();

            return view('admin.earnHistory', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }
}
