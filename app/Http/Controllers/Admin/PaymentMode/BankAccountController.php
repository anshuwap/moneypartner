<?php

namespace App\Http\Controllers\Admin\PaymentMode;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode\BankAccount;
use Exception;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{

    public function index()
    {
        try {
            return view('admin.payment_mode.bank_account');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()] );;
        }
    }


    public function create()
    {

    }


    public function store(Request $request)
    {

    }


    public function show(BankAccount $BankAccount)
    {

    }


    public function edit(BankAccount $BankAccount)
    {

    }


    public function update(Request $request, BankAccount $BankAccount)
    {

    }


    public function destroy(BankAccount $BankAccount)
    {

    }
}
