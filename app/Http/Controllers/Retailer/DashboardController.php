<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\Upi;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $data['transactions']    = Transaction::where('status', 'pending')->where('retailer_id', Auth::user()->_id)->get();

        $data['upis'] = Upi::where('retailer_ids','all',[Auth::user()->_id])->where('status',1)->get();
        $data['bank_accounts'] = BankAccount::where('retailer_ids','all',[Auth::user()->_id])->where('status',1)->get();

        return view('retailer.dashboard', $data);
    }
}
