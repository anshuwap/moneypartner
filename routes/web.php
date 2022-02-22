<?php

use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OutletController as AdminOutlet;
use App\Http\Controllers\Admin\PaymentMode\BankAccountController as AdminBankAccount;
use App\Http\Controllers\Admin\PaymentMode\QrCodeController as AdminQrCode;
use App\Http\Controllers\Admin\PaymentMode\UpiController as AdminUpi;
use App\Http\Controllers\Admin\TopupRequestController as AdminTopupRequest;
use App\Http\Controllers\Admin\Transaction\CustomerTransController as AdminCustomerTrans;
use App\Http\Controllers\Admin\Transaction\RetailerTransController as AdminRetailerTrans;
use App\Http\Controllers\Admin\Transaction\OfflinePayoutApiController as AdminOfflinePayout;
use App\Http\Controllers\Admin\PaymentChannelController as AdminPaymentChannel;
use App\Http\Controllers\Admin\TransactionCommentController as AdminComment;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployee;
use App\Http\Controllers\Admin\PassbookController as AdminPassbook;

//for retailer panel
use App\Http\Controllers\Retailer\WebhookApiController as WebhookApi;
use App\Http\Controllers\Retailer\ProfileController as RetailerProfile;
use App\Http\Controllers\Retailer\DashboardController as RetailerDashboard;
use App\Http\Controllers\Retailer\PassbookController as RetailerPassbook;
use App\Http\Controllers\Retailer\TopupController as RetailerTopup;
use App\Http\Controllers\Retailer\Transaction\OfflinePayoutApiController as OfflinePayout;
use App\Http\Controllers\Retailer\Transaction\CustomerTransController as RetailerCustomerTrans;
use App\Http\Controllers\Retailer\Transaction\RetailerTransController as RetailerRetailerTrans;

//for employee panel
use App\Http\Controllers\Employee\LoginController as EmployeeLogin;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfile;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboard;
use App\Http\Controllers\Employee\TopupRequestController as EmployeeTopupRequest;
use App\Http\Controllers\Employee\TopupController as EmployeeTopup;
use App\Http\Controllers\Employee\Transaction\OfflinePayoutApiController as EmployeeOfflinePayout;
use App\Http\Controllers\Employee\Transaction\CustomerTransController as EmployeeCustomerTrans;
use App\Http\Controllers\Employee\Transaction\RetailerTransController as EmployeeRetailerTrans;

use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('500',[AdminDashboard::class,'serverError']);
// Route::get('404', [AdminDashboard::class, 'notFound']);

 Route::group(['middleware' => 'adminRedirect'], function () {
    Route::get('/',[AdminLogin::class,'index']);
    Route::resource('/login', AdminLogin::class);
    Route::resource('/register', RegisterController::class);
});


Route::group(['middleware' => 'retailerRedirect'], function () {
   // Route::get('/retailer',      [RetailerLogin::class,'index']);

    //Route::post('retailer/login',[RetailerLogin::class,'store']);
});

Route::get('otp-sent',       [AdminLogin::class,'otpSent']);
Route::post('verify-mobile', [AdminLogin::class,'verifyMobile']);

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::resource('dashboard', AdminDashboard::class);

    Route::resource('outlets', AdminOutlet::class);
    Route::post('outlets-status', [AdminOutlet::class,'outletStatus']);
    // Route::get('outlet-bank-get/{id}', [AdminOutlet::class,'outletBankGet']);
    // Route::post('outlet-bank', [AdminOutlet::class,'outletBank']);
    Route::get('outlets-ajax',                  [AdminOutlet::class,'ajaxList']);
    Route::get('outlet-bank-charges/{id}',      [AdminOutlet::class,'outletBankCharges']);
    Route::get('outlet-bank-charges-list',      [AdminOutlet::class,'outletBankChargesList']);
    Route::post('outlet-add-bank-charges',      [AdminOutlet::class,'outletAddBankCharges']);
    Route::get('outlet-edit-bank-charges/{id}', [AdminOutlet::class,'outletEditBankCharges']);
    Route::post('outlet-update-bank-charges',   [AdminOutlet::class,'outletUpdateBankCharges']);
    Route::get('outlet-charges-status/{id}/{key}/{status}', [AdminOutlet::class,'bankChargesStatus']);

    Route::resource('bank-account', AdminBankAccount::class);
    Route::get('bank-account-ajax', [AdminBankAccount::class,'ajaxList']);
    Route::post('bank-account-status', [AdminBankAccount::class,'bankAccountStatus']);
    Route::get('b-allocate-retailer', [AdminBankAccount::class,'allocateRetailer']);
    Route::post('b-save-allocate-retailer', [AdminBankAccount::class,'saveAllocateRetailer']);

    Route::resource('qr-code', AdminQrCode::class);
    Route::get('qr-code-ajax', [AdminQrCode::class,'ajaxList']);
    Route::post('qr-code-status', [AdminQrCode::class,'qrCodeStatus']);
    Route::get('q-allocate-retailer', [AdminQrCode::class,'allocateRetailer']);
    Route::post('q-save-allocate-retailer', [AdminQrCode::class,'saveAllocateRetailer']);

    Route::resource('upi', AdminUpi::class);
    Route::get('upi-ajax', [AdminUpi::class,'ajaxList']);
    Route::post('upi-status', [AdminUpi::class,'upiStatus']);
    Route::get('u-allocate-retailer', [AdminUpi::class,'allocateRetailer']);
    Route::post('u-save-allocate-retailer', [AdminUpi::class,'saveAllocateRetailer']);

    Route::resource('payment-channel', AdminPaymentChannel::class);
    Route::get('payment-channel-ajax', [AdminPaymentChannel::class,'ajaxList']);
    Route::post('payment-channel-status', [AdminPaymentChannel::class,'paymentChannelStatus']);

    Route::resource('comment', AdminComment::class);
    Route::get('comment-ajax', [AdminComment::class,'ajaxList']);
    Route::post('comment-status', [AdminComment::class,'commentStatus']);

    Route::get('topup-list', [AdminTopupRequest::class,'index']);

    Route::post('topup-request', [AdminTopupRequest::class,'topupRequest']);
    Route::get('topup-request-details/{id}', [AdminTopupRequest::class,'topupRequestDetials']);

    Route::resource('a-customer-trans', AdminCustomerTrans::class);
    Route::get('a-customer-trans-ajax', [AdminCustomerTrans::class,'ajaxList']);
    Route::get('a-view-detail', [AdminCustomerTrans::class,'viewDetail']);
    Route::get('a-customer-comment', [AdminCustomerTrans::class,'customerComment']);

    Route::resource('a-retailer-trans', AdminRetailerTrans::class);
    Route::get('a-retailer-trans-ajax', [AdminRetailerTrans::class,'ajaxList']);
    Route::get('a-retailer-detail', [AdminRetailerTrans::class,'viewDetail']);
    Route::get('a-retailer-comment', [AdminRetailerTrans::class,'retailerComment']);

    Route::resource('a-offline-payout', AdminOfflinePayout::class);
    Route::get('a-offline-payout-detail', [AdminOfflinePayout::class,'viewDetail']);
    Route::get('a-offline-payout-comment', [AdminOfflinePayout::class,'Comment']);

    Route::resource('employee', AdminEmployee::class);
    Route::post('employee-status', [AdminEmployee::class,'employeeStatus']);

    Route::get('passbook',  [AdminPassbook::class, 'index']);
    Route::get('passbook-export',  [AdminPassbook::class, 'export']);

    Route::post('logout',  [AdminLogin::class, 'logout']);
});



//for retailer
Route::group(['prefix' => 'retailer', 'middleware' => 'retailer'], function () {

    Route::resource('profile', RetailerProfile::class);

    Route::get('dashboard',  [RetailerDashboard::class, 'index']);

    Route::get('passbook',  [RetailerPassbook::class, 'index']);


    Route::resource('topup', RetailerTopup::class);
    Route::get('outlet-payment-mode',  [RetailerTopup::class, 'outletPaymentMode']);
    Route::get('payment-details',      [RetailerTopup::class, 'paymentDetails']);
    Route::get('topup-history',        [RetailerTopup::class, 'topupHistory']);
    Route::get('transaction-history',  [RetailerTopup::class, 'transactionHistory']);
    Route::get('topup-history-ajax',   [RetailerTopup::class, 'topupHistoryAjax']);

    Route::resource('customer-trans', RetailerCustomerTrans::class);
    Route::get('verify-mobile',       [RetailerCustomerTrans::class,'verifyMobile']);
    Route::get('send-otp',            [RetailerCustomerTrans::class,'sendOtp']);
    Route::get('customer-trans-ajax', [RetailerCustomerTrans::class,'ajaxList']);
    Route::get('fee-details',         [RetailerCustomerTrans::class,'feeDetails']);

    Route::resource('retailer-trans', RetailerRetailerTrans::class);
    Route::get('retailer-trans-ajax', [RetailerRetailerTrans::class,'ajaxList']);
    Route::get('sample-csv', [RetailerRetailerTrans::class,'sampleCsv']);
    Route::post('payout-import', [RetailerRetailerTrans::class,'import']);

    Route::resource('offline-payout', OfflinePayout::class);

    Route::resource('webhook-api', WebhookApi::class);

    Route::post('logout',  [AdminLogin::class, 'logout']);
});


//for employee
Route::group(['prefix' => 'employee', 'middleware' => 'employee'], function () {

    Route::resource('e-profile', EmployeeProfile::class);

    Route::get('dashboard',  [EmployeeDashboard::class, 'index']);

    Route::get('topup-list', [EmployeeTopupRequest::class,'index']);

    Route::post('topup-request', [EmployeeTopupRequest::class,'topupRequest']);
    Route::get('topup-request-details/{id}', [EmployeeTopupRequest::class,'topupRequestDetials']);

    Route::resource('e-customer-trans', EmployeeCustomerTrans::class);
    Route::get('e-customer-trans-ajax', [EmployeeCustomerTrans::class,'ajaxList']);
    Route::get('e-view-detail',         [EmployeeCustomerTrans::class,'viewDetail']);
    Route::get('e-customer-comment',    [EmployeeCustomerTrans::class,'customerComment']);

    Route::resource('e-retailer-trans', EmployeeRetailerTrans::class);
    Route::get('e-retailer-trans-ajax', [EmployeeRetailerTrans::class,'ajaxList']);
    Route::get('e-retailer-detail',     [EmployeeRetailerTrans::class,'viewDetail']);
    Route::get('e-retailer-comment',    [EmployeeRetailerTrans::class,'retailerComment']);

    Route::resource('e-offline-payout',  EmployeeOfflinePayout::class);
    Route::get('e-offline-payout-detail', [EmployeeOfflinePayout::class,'viewDetail']);
    Route::get('e-offline-payout-comment', [EmployeeOfflinePayout::class,'Comment']);

    Route::post('logout',  [EmployeeLogin::class, 'logout']);
});