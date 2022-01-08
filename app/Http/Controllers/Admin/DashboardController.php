<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Topup;
use App\Models\Transaction\CustomerTrans;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        try {

            $topups = Topup::where('status', 'pending')->get();

            $topup_request = [];
            foreach ($topups as $topup) {

                $topup_request[] = (object)[
                    'id'           => $topup->_id,
                    'retailer_name'=> $topup->RetailerName['full_name'],
                    'amount'       => $topup->amount,
                    'payment_mode' => ucwords(str_replace('_', " ", $topup->payment_mode)),
                    'status'       => ucwords($topup->status),
                    'payment_date' => date('y-m-d h:i:s A', $topup->payment_date),
                    'comment'      => $topup->comment
                ];
            }
            $data['topup_request'] = $topup_request;

            $data['total_outlet']       = Outlet::count();

            //for topup amount
            $topups = Topup::select('amount')->where('status', 'approved')->get();
            $total_topup_amount = 0;
            foreach ($topups as $am) {
                $total_topup_amount += $am->amount;
            }
            $data['total_topup_amount'] = $total_topup_amount;

            //for dmt amount
            $dmts = CustomerTrans::select('total_amount')->get();
            $total_dmt_amount = 0;
            foreach ($dmts as $am) {
                $total_dmt_amount += $am->total_amount;
            }
            $data['total_dmt_amount']   = $total_dmt_amount;

            //for bulk amount
            $bulks = RetailerTrans::select('total_amount')->get();
            $total_bulk_amount = 0;
            foreach ($bulks as $am) {
                $total_bulk_amount += $am->total_amount;
            }
            $data['total_bulk_amount']  = $total_bulk_amount;

            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function serverError(){

        return view('admin.500');
    }
}
