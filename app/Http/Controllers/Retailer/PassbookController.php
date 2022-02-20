<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Http\Validation\TopupValidation;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
use App\Models\Topup;
use App\Models\TransferHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassbookController extends Controller
{

    public function index()
    {
        try {
            $data['passbook'] = TransferHistory::where('retailer_id',Auth::user()->_id)->paginate(10);
            return view('retailer.passbook.list',$data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


}
