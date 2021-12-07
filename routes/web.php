<?php

use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OutletController as AdminOutlet;
use App\Http\Controllers\Admin\PaymentMode\BankAccountController as AdminBankAccount;
use App\Http\Controllers\Admin\PaymentMode\QrCodeController as AdminQrCode;
use App\Http\Controllers\Admin\PaymentMode\UpiController as AdminUpi;
use App\Http\Controllers\Admin\PaymentMode\TopupRequestController as AdminTopupRequest;


use App\Http\Controllers\Retailer\LoginController as RetailerLogin;
use App\Http\Controllers\Retailer\DashboardController as RetailerDashboard;
use App\Http\Controllers\Retailer\TopupController as RetailerTopup;

use Illuminate\Support\Facades\Route;


 Route::group(['middleware' => 'adminRedirect'], function () {
    Route::get('/', [AdminLogin::class, 'index']);
    Route::resource('/login', AdminLogin::class);
    Route::resource('/register', RegisterController::class);
});


Route::group(['middleware' => 'retailerRedirect'], function () {
    // Route::resource('/', RetailerLogin::class);
    Route::get('/retailer/login',[RetailerLogin::class,'index']);

    Route::post('retailer/login',[RetailerLogin::class,'store']);
});

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::resource('dashboard', AdminDashboard::class);

    Route::resource('outlets', AdminOutlet::class);
    Route::post('outlets-status', [AdminOutlet::class,'outletStatus']);
    // Route::get('outlet-bank-get/{id}', [AdminOutlet::class,'outletBankGet']);
    // Route::post('outlet-bank', [AdminOutlet::class,'outletBank']);
    Route::get('outlets-ajax', [AdminOutlet::class,'ajaxList']);
    Route::get('outlet-bank-charges/{id}', [AdminOutlet::class,'outletBankCharges']);
    Route::get('outlet-bank-charges-list', [AdminOutlet::class,'outletBankChargesList']);
    Route::post('outlet-add-bank-charges', [AdminOutlet::class,'outletAddBankCharges']);
    Route::get('outlet-edit-bank-charges/{id}', [AdminOutlet::class,'outletEditBankCharges']);
    Route::post('outlet-update-bank-charges', [AdminOutlet::class,'outletUpdateBankCharges']);
    Route::get('outlet-charges-status/{id}/{key}/{status}', [AdminOutlet::class,'bankChargesStatus']);

    Route::resource('bank-account', AdminBankAccount::class);
    Route::get('bank-account-ajax', [AdminBankAccount::class,'ajaxList']);
    Route::post('bank-account-status', [AdminBankAccount::class,'bankAccountStatus']);

    Route::resource('qr-code', AdminQrCode::class);
    Route::get('qr-code-ajax', [AdminQrCode::class,'ajaxList']);
    Route::post('qr-code-status', [AdminQrCode::class,'qrCodeStatus']);

    Route::resource('upi', AdminUpi::class);
    Route::get('upi-ajax', [AdminUpi::class,'ajaxList']);
    Route::post('upi-status', [AdminUpi::class,'upiStatus']);

    Route::post('topup-request', [AdminTopupRequest::class,'topupRequest']);
    Route::get('topup-request-details/{id}', [AdminTopupRequest::class,'topupRequestDetials']);

    Route::post('logout',  [AdminLogin::class, 'logout']);
});


Route::group(['prefix' => 'retailer', 'middleware' => 'retailer'], function () {

    Route::get('dashboard',  [RetailerDashboard::class, 'index']);


    Route::resource('topup', RetailerTopup::class);
    Route::get('outlet-payment-mode',  [RetailerTopup::class, 'outletPaymentMode']);
    Route::get('payment-details',  [RetailerTopup::class, 'paymentDetails']);
    Route::get('topup-history',  [RetailerTopup::class, 'topupHistory']);
    Route::get('topup-history-ajax',  [RetailerTopup::class, 'topupHistoryAjax']);

    Route::post('logout',  [RetailerLogin::class, 'logout']);
});