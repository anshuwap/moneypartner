<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\Transaction\CustomerTrans;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        try {

            $topups = Topup::where('status', 'pending')->get();
            $data['topup_request'] = $topups;

            $data['total_outlet']  = Outlet::count();

            //for topup amount
            $topups = Topup::select('amount')->where('status', 'success')->get();
            $total_topup_amount = 0;
            foreach ($topups as $am) {
                $total_topup_amount += $am->amount;
            }
            $data['total_topup_amount'] = $total_topup_amount;



            //amout for current month
            $dmts = CustomerTrans::select('total_amount')->get();
            $current_month_dmt_amount = 0;
            foreach ($dmts as $am) {
                $current_month_dmt_amount += $am->total_amount;
            }
            $data['current_month_dmt_amount']   = $current_month_dmt_amount;

            //for bulk amount
            $bulks = RetailerTrans::select('total_amount')->get();
            $current_month_bulk_amount = 0;
            foreach ($bulks as $am) {
                $current_month_bulk_amount += $am->total_amount;
            }
            $data['current_month_bulk_amount']  = $current_month_bulk_amount;


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


            //             $dmt_date = CustomerTrans::select('updated')->get();

            // $month = ['Jan','Feb'];

            // foreach($dmt_date as $date){
            // echo date('M',$date->updated);
            // echo "<br/>";
            //  }
            // die;

            //    $data['customer_trans'] = CustomerTrans::select('trans_details','customer_name','mobile_number')->get();
            //    $data['retailerTrans']  = RetailerTrans::where('status','pending')->get();
            //    $data['offlinePayouts'] = OfflinePayoutApi::where('status','pending')->get();

            $que = Transaction::where('status', 'pending');
            if (!empty($request->mode))
                $que->where('payment_mode', $request->mode);

            $data['transaction']  = $que->orderBy('created', 'DESC')->get();
            $data['mode'] = $request->mode;
            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();
            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }

    public function serverError()
    {

        return view('admin.500');
    }
}
