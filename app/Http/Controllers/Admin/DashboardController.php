<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        try {

            $topups = Topup::where('status','pending')->get();

            $topup_request = [];
            foreach ($topups as $topup) {

                $topup_request[] = (object)[
                    'id'           => $topup->_id,
                    'retailer_name'=> $topup->RetailerName['full_name'],
                    'amount'       => $topup->amount,
                    'payment_mode' => ucwords(str_replace('_'," ",$topup->payment_mode)),
                    'status'       => ucwords($topup->status),
                    'payment_date' => date('y-m-d h:i:s A', $topup->payment_date),
                    'comment'      => $topup->comment
                ];
            }
            $data['topup_request'] = $topup_request;



            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }
}
