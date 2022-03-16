<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Topup;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $topups = Topup::where('status', 'pending')->get();

        $topup_request = [];
        foreach ($topups as $topup) {

            $topup_request[] = (object)[
                'id'           => $topup->_id,
                'payment_id'   => $topup->payment_id,
                'retailer_name' => !empty($topup->RetailerName['full_name']) ? $topup->RetailerName['full_name'] : '',
                'amount'       => $topup->amount,
                'payment_mode' => ucwords(str_replace('_', " ", $topup->payment_mode)),
                'status'       => ucwords($topup->status),
                'payment_date' => date('y-m-d h:i:s A', $topup->payment_date),
                'comment'      => $topup->comment
            ];
        }
        $data['topup_request'] = $topup_request;


        $que = Transaction::where('status', 'pending');
        if (!empty($request->mode))
            $que->where('payment_mode', $request->mode);

        $data['transaction']  = $que->get();
        $data['mode'] = $request->mode;
        //for payment channel
        $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
        return view('employee.dashboard',$data);
    }
}
