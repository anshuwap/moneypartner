<?php

namespace App\Http\Controllers\Retailer\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Api\OfflinePayoutApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflinePayoutApiController extends Controller
{

    public function index()
    {
        try {
            $data['retailerTrans'] = OfflinePayoutApi::where('retailer_id', Auth::user()->_id)->get();
            return view('retailer.transaction.offline_payout_api', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


}
