<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Transaction\CustomerTrans;
use App\Models\Transaction\RetailerTrans;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $data['customer_trans'] = CustomerTrans::select('trans_details')->where('retailer_id', Auth::user()->_id)->get();
        $data['retailerTrans'] = RetailerTrans::where('status', 'pending')->where('retailer_id', Auth::user()->_id)->get();
        $data['offlinePayouts'] = OfflinePayoutApi::where('status', 'pending')->where('retailer_id', Auth::user()->_id)->get();

        return view('retailer.dashboard', $data);
    }
}
