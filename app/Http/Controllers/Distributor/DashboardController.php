<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Api\ECollection;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Outlet;
use App\Models\PaymentChannel;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\Transaction\CustomerTrans;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        try {

            $topups = Topup::where('status', 'pending')->get();
            $data['topup_request'] = $topups;

            $que = Transaction::where('status', 'pending')->whereIn('retailer_id', Auth::user()->retailers);
            if (!empty($request->mode))
                $que->where('payment_mode', $request->mode);

            $data['transaction']  = $que->orderBy('created', 'DESC')->get();
            $data['mode'] = $request->mode;
            //for payment channel
            $data['payment_channel'] = PaymentChannel::select('_id', 'name')->get();

            // $settlements = ECollection::where('outlet_id', Auth::user()->outlet_id)->where('status', 'SUCCESS')->get();
            // $settlement_amount = 0;
            // foreach ($settlements as $settlement) {
            //     $settlement_amount += $settlement->amount;
            // }
            // $data['settlement_amount'] = $settlement_amount;

            // $un_settlements = ECollection::where('outlet_id', Auth::user()->outlet_id)->where('status', 'pending')->get();

            // $un_settlement_amount = 0;
            // foreach ($un_settlements as $settlement1) {
            //     $un_settlement_amount += $settlement1->amount;
            // }
            // $data['un_settlement_amount'] = $un_settlement_amount;

            return view('distributor.dashboard', $data);
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function serverError()
    {

        return view('admin.500');
    }
}
