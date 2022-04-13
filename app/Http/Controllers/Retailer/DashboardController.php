<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Api\ECollection;
use App\Models\Api\OfflinePayoutApi;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\Upi;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->where('retailer_id', Auth::user()->_id);

        if (!empty($request->mode))
            $query->where('payment_mode', $request->mode);

        if (!empty($request->status)) {

            if ($request->status == 'success')
                $query->whereIn('status', ["success", "pending"]);

            if ($request->status == 'pending1')
                $query->where('status', 'pending')->whereNotNull('response');

            if ($request->status == 'reject')
                $query->where('status', $request->status);
        }

        if (!empty($request->transaction_id))
            $query->where('transaction_id', $request->transaction_id);

        if (!empty($request->banficiary))
            $query->where('receiver_name', $request->banficiary);

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
        $data['transactions']    = $query->orderBy('created', 'DESC')->paginate($perPage);

        $request->request->remove('page');
        $request->request->remove('perPage');

        $data['filter']  = $request->all();

        $data['upis'] = Upi::where('retailer_ids', 'all', [Auth::user()->_id])->where('status', 1)->get();
        $data['bank_accounts'] = BankAccount::where('retailer_ids', 'all', [Auth::user()->_id])->where('status', 1)->get();

        $settlements = ECollection::where('outlet_id', Auth::user()->outlet_id)->where('status','SUCCESS')->get();
        $settlement_amount = 0;
        foreach ($settlements as $settlement) {
            $settlement_amount += $settlement->amount;
        }
        $data['settlement_amount'] = $settlement_amount;

        $un_settlements = ECollection::where('outlet_id', Auth::user()->outlet_id)->where('status','pending')->get();

        $un_settlement_amount = 0;
        foreach ($un_settlements as $settlement1) {
            $un_settlement_amount += $settlement1->amount;
        }
        $data['un_settlement_amount'] = $un_settlement_amount;

        return view('retailer.dashboard', $data);
    }
}
