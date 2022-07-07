<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\Admin\LoginController     as AdminLogin;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OutletController    as AdminOutlet;
use App\Http\Controllers\Admin\PaymentMode\BankAccountController as AdminBankAccount;
use App\Http\Controllers\Admin\PaymentMode\QrCodeController      as AdminQrCode;
use App\Http\Controllers\Admin\PaymentMode\UpiController    as AdminUpi;
use App\Http\Controllers\Admin\TopupRequestController       as AdminTopupRequest;
use App\Http\Controllers\Admin\ApiListController            as AdminApiList;
use App\Http\Controllers\Admin\PaymentChannelController     as AdminPaymentChannel;
use App\Http\Controllers\Admin\TransactionCommentController as AdminComment;
use App\Http\Controllers\Admin\TransactionController   as AdminTransaction;
use App\Http\Controllers\Admin\EmployeeController      as AdminEmployee;
use App\Http\Controllers\Admin\EcollectionController   as AdminECollection;
use App\Http\Controllers\Api\EcollectionController;
use App\Http\Controllers\Admin\PassbookController      as AdminPassbook;
use App\Http\Controllers\Admin\Action\CreditController as AdminCredit;
use App\Http\Controllers\Admin\Action\DebitController  as AdminDebit;
use App\Http\Controllers\Admin\ProfileController       as AdminProfile;
use App\Http\Controllers\Admin\EarnHistoryController   as AdminEarnHistory;
use App\Http\Controllers\Admin\WithdrawalController    as AdminWithdrawal;
use App\Http\Controllers\Controller;

//for retailer panel
use App\Http\Controllers\Retailer\WebhookApiController as WebhookApi;
use App\Http\Controllers\Retailer\ProfileController    as RetailerProfile;
use App\Http\Controllers\Retailer\DashboardController  as RetailerDashboard;
use App\Http\Controllers\Retailer\PassbookController   as RetailerPassbook;
use App\Http\Controllers\Retailer\TopupController      as RetailerTopup;
use App\Http\Controllers\Retailer\Transaction\OfflinePayoutApiController as OfflinePayout;
use App\Http\Controllers\Retailer\Transaction\RetailerTransController    as RetailerRetailerTrans;
use App\Http\Controllers\Retailer\TransactionController      as RetailerTransaction;
use App\Http\Controllers\Retailer\EcollectionController      as RetailerECollection;
use App\Http\Controllers\Retailer\BankAutoCompleteController as BankAuto;
use App\Http\Controllers\Retailer\Action\CreditController    as RetailerCredit;
use App\Http\Controllers\Retailer\Action\DebitController     as RetailerDebit;

//for employee panel
use App\Http\Controllers\Employee\LoginController           as EmployeeLogin;
use App\Http\Controllers\Employee\ProfileController         as EmployeeProfile;
use App\Http\Controllers\Employee\DashboardController       as EmployeeDashboard;
use App\Http\Controllers\Employee\TopupRequestController    as EmployeeTopupRequest;
use App\Http\Controllers\Employee\TransactionController     as EmployeeTransaction;
use App\Http\Controllers\Employee\EarnHistoryController     as EarnHistory;
use App\Http\Controllers\Employee\PassbookController        as EmployeePassbook;
use App\Http\Controllers\Employee\PaymentChannelController  as EmployeePaymentChannel;
use App\Http\Controllers\Employee\TransactionCommentController      as EmployeeComment;
use App\Http\Controllers\Employee\PaymentMode\BankAccountController as EmployeeBankAccount;
use App\Http\Controllers\Employee\PaymentMode\QrCodeController      as EmployeeQrCode;
use App\Http\Controllers\Employee\PaymentMode\UpiController as EmployeeUpi;
use App\Http\Controllers\Employee\Action\CreditController   as EmployeeCredit;
use App\Http\Controllers\Employee\Action\DebitController    as EmployeeDebit;
use App\Http\Controllers\Employee\WithdrawalController      as EmployeeWithdrawal;
use App\Http\Controllers\Employee\OutletController          as EmployeeOutlet;


//for Distributor panel
use App\Http\Controllers\Distributor\LoginController           as DistributorLogin;
use App\Http\Controllers\Distributor\ProfileController         as DistributorProfile;
use App\Http\Controllers\Distributor\DashboardController       as DistributorDashboard;
use App\Http\Controllers\Distributor\TopupRequestController    as DistributorTopupRequest;
use App\Http\Controllers\Distributor\TransactionController     as DistributorTransaction;
use App\Http\Controllers\Distributor\OutletController          as DistributorOutlet;
use App\Http\Controllers\Distributor\PassbookController        as DistributorPassbook;
use App\Http\Controllers\Distributor\MakeTransactionController as MakeTransaction;
use App\Http\Controllers\Distributor\MakeTopupController       as MakeTopup;
use App\Http\Controllers\Distributor\WebhookApiController      as DWebhookApi;

use App\Http\Controllers\PHPMailerController;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get("email",       [Controller::class, "show"]);
Route::get("get-blance0", [Controller::class, "getBlance0"]);
Route::get("get-blance1", [Controller::class, "getBlance1"]);
Route::get("get-blance2", [Controller::class, "getBlance2"]);

Route::get('500', [AdminDashboard::class, 'serverError']);
// Route::get('404', [AdminDashboard::class, 'notFound']);

Route::group(['middleware' => 'adminRedirect'], function () {
  Route::get('/',                     [AdminLogin::class, 'index']);
  Route::resource('/login',           AdminLogin::class);
  Route::resource('/register',        RegisterController::class);
  Route::get('/send-link',            [AdminLogin::class, 'sendLinkview']);
  Route::get('/resend-otp',           [AdminLogin::class, 'resendOtp']);
  Route::get('/remove-otp',           [AdminLogin::class, 'removeOtp']);
  Route::post('/send-link',           [AdminLogin::class, 'sendLink']);
  Route::get('/forgot-password/{id}', [AdminLogin::class, 'forgotPassword']);
  Route::post('/forgot-password',     [AdminLogin::class, 'forgotPasswordSave']);
  // Route::post('verify-mobile', [AdminLogin::class, 'verifyMobile']);
});


Route::group(['middleware' => 'retailerRedirect'], function () {
  // Route::get('/retailer',      [RetailerLogin::class,'index']);

  //Route::post('retailer/login',[RetailerLogin::class,'store']);
  //Route::post('verify-mobile', [AdminLogin::class, 'verifyMobile']);
});

Route::get('otp-sent',       [AdminLogin::class, 'otpSent']);
Route::post('verify-mobile', [AdminLogin::class, 'verifyMobile']);
Route::get('verify-mobile',  [AdminLogin::class, 'dashboardRedirect']);


Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

  Route::resource('dashboard', AdminDashboard::class);

  Route::resource('profile',     AdminProfile::class);
  Route::get('pin-password',     [AdminProfile::class, 'pinPassword']);
  Route::post('change-password', [AdminProfile::class, 'changePassword']);


  Route::get('export',         [AdminDashboard::class, 'export']);
  Route::post('import',        [AdminDashboard::class, 'import']);

  Route::resource('outlets',     AdminOutlet::class);
  Route::post('outlets-status', [AdminOutlet::class, 'outletStatus']);

  Route::get('outlets-ajax',                  [AdminOutlet::class, 'ajaxList']);
  Route::get('outlet-bank-charges/{id}',      [AdminOutlet::class, 'outletBankCharges']);
  Route::get('outlet-bank-charges-list',      [AdminOutlet::class, 'outletBankChargesList']);
  Route::post('outlet-add-bank-charges',      [AdminOutlet::class, 'outletAddBankCharges']);
  Route::get('outlet-edit-bank-charges/{id}', [AdminOutlet::class, 'outletEditBankCharges']);
  Route::post('outlet-update-bank-charges',   [AdminOutlet::class, 'outletUpdateBankCharges']);
  Route::get('outlet-charges-status/{id}/{key}/{status}', [AdminOutlet::class, 'bankChargesStatus']);
  Route::get('employee-list',                 [AdminOutlet::class, 'EmployeeList']);
  Route::post('assign-outlet',                [AdminOutlet::class, 'assignOutlet']);

  Route::resource('bank-account',         AdminBankAccount::class);
  Route::get('bank-account-ajax',         [AdminBankAccount::class, 'ajaxList']);
  Route::post('bank-account-status',      [AdminBankAccount::class, 'bankAccountStatus']);
  Route::get('b-allocate-retailer',       [AdminBankAccount::class, 'allocateRetailer']);
  Route::post('b-save-allocate-retailer', [AdminBankAccount::class, 'saveAllocateRetailer']);

  Route::resource('qr-code',              AdminQrCode::class);
  Route::get('qr-code-ajax',              [AdminQrCode::class, 'ajaxList']);
  Route::post('qr-code-status',           [AdminQrCode::class, 'qrCodeStatus']);
  Route::get('q-allocate-retailer',       [AdminQrCode::class, 'allocateRetailer']);
  Route::post('q-save-allocate-retailer', [AdminQrCode::class, 'saveAllocateRetailer']);

  Route::resource('upi',                  AdminUpi::class);
  Route::get('upi-ajax',                  [AdminUpi::class, 'ajaxList']);
  Route::post('upi-status',               [AdminUpi::class, 'upiStatus']);
  Route::get('u-allocate-retailer',       [AdminUpi::class, 'allocateRetailer']);
  Route::post('u-save-allocate-retailer', [AdminUpi::class, 'saveAllocateRetailer']);

  Route::resource('payment-channel',    AdminPaymentChannel::class);
  Route::get('payment-channel-ajax',    [AdminPaymentChannel::class, 'ajaxList']);
  Route::post('payment-channel-status', [AdminPaymentChannel::class, 'paymentChannelStatus']);

  Route::resource('comment',    AdminComment::class);
  Route::get('comment-ajax',    [AdminComment::class, 'ajaxList']);
  Route::post('comment-status', [AdminComment::class, 'commentStatus']);

  Route::get('topup-list',                 [AdminTopupRequest::class, 'index']);
  Route::get('topup-list-export',          [AdminTopupRequest::class, 'export']);
  Route::post('topup-request',             [AdminTopupRequest::class, 'topupRequest']);
  Route::get('topup-request-details/{id}', [AdminTopupRequest::class, 'topupRequestDetials']);
  Route::get('topup-payment-channel',      [AdminTopupRequest::class, 'updatePaymentChannel']);
  Route::get('topup-payment-channel2',     [AdminTopupRequest::class, 'updatePaymentChannel2']);
  Route::get('pending-topup',              [AdminTopupRequest::class, 'pendingRequest']);
  Route::get('pending-topup-export',       [AdminTopupRequest::class, 'pendingExport']);


  Route::resource('a-transaction',  AdminTransaction::class);
  Route::get('payToApi',            [AdminTransaction::class, 'payToApi']);
  Route::post('a-store-api',        [AdminTransaction::class, 'storeApi']);
  Route::get('a-trans-detail',      [AdminTransaction::class, 'viewDetail']);
  Route::get('a-trans-comment',     [AdminTransaction::class, 'Comment']);
  Route::get('a-transaction-export', [AdminTransaction::class, 'export']);
  Route::post('bulk-action',        [AdminTransaction::class, 'bulkAction']);
  Route::get('change-channel',      [AdminTransaction::class, 'ChangeChannel']);
  Route::get('payment-status',      [AdminTransaction::class, 'PaymentStatus']);
  Route::get('check-bulk-status',   [AdminTransaction::class, 'checkBulkStatus']);
  Route::get('update-utr',          [AdminTransaction::class, 'updateUtrNo']);
  Route::post('split-transaction',  [AdminTransaction::class, 'splitTransaction']);
  Route::get('refund-pending',      [AdminTransaction::class, 'refundPending']);
  Route::get('refund-pending-export', [AdminTransaction::class, 'refundPendingExport']);
  Route::get('removeIndex/{key}',   [AdminTransaction::class, 'removeIndex']);
  Route::get('transaction-report',  [AdminTransaction::class, 'report']);


  Route::resource('credit',           AdminCredit::class);
  Route::get('credit-export',         [AdminCredit::class, 'export']);
  Route::get('credit-show/{id}',      [AdminCredit::class, 'showBlance']);
  Route::resource('debit',            AdminDebit::class);
  Route::get('debit-show/{id}',       [AdminDebit::class, 'showBlance']);
  Route::get('debit-export',          [AdminDebit::class, 'export']);
  Route::post('credit-paid-status',   [AdminCredit::class, 'creditPaidStatus']);

  Route::resource('api-list',             AdminApiList::class);
  Route::post('api-list-editApi',         [AdminApiList::class, 'editApi']);
  Route::get('a-allocate-retailer',       [AdminApiList::class, 'allocateRetailer']);
  Route::post('a-save-allocate-retailer', [AdminApiList::class, 'saveAllocateRetailer']);

  Route::resource('e-collection', AdminECollection::class);

  Route::resource('employee',    AdminEmployee::class);
  Route::get('employee-commission/{id}',      [AdminEmployee::class, 'commission']);
  // Route::get('outlet-bank-charges-list',      [AdminEmployee::class, 'outletBankChargesList']);
  Route::post('employee-add-commission',      [AdminEmployee::class, 'employeeAddCommission']);
  Route::get('employee-edit-commission/{id}', [AdminEmployee::class, 'employeeEditCommission']);
  Route::post('employee-update-commission',   [AdminEmployee::class, 'employeeUpdateCommission']);
  Route::get('employee-commission-status/{id}/{key}/{status}', [AdminEmployee::class, 'employeeCommissionStatus']);
  Route::post('employee-status', [AdminEmployee::class, 'employeeStatus']);

  Route::get('passbook',         [AdminPassbook::class, 'index']);
  Route::get('passbook-export',  [AdminPassbook::class, 'export']);

  Route::resource('earn-history',   AdminEarnHistory::class);
  Route::get('earn-history-export', [AdminEarnHistory::class, 'export']);

  Route::resource('withdrawal',     AdminWithdrawal::class);

  Route::post('logout',  [AdminLogin::class, 'logout']);
});



//for retailer
Route::group(['prefix' => 'retailer', 'middleware' => 'retailer'], function () {

  Route::resource('profile',     RetailerProfile::class);
  Route::get('pin-password',     [RetailerProfile::class, 'pinPassword']);
  Route::post('change-password', [RetailerProfile::class, 'changePassword']);
  Route::post('change-pin',      [RetailerProfile::class, 'ChangePin']);
  Route::post('send-email-link', [RetailerProfile::class, 'sendLink']);
  Route::get('/forgot-pin/{id}', [RetailerProfile::class, 'forgotPin']);
  Route::post('/forgot-pin',     [RetailerProfile::class, 'forgotPinSave']);

  Route::post('/fetch',        [BankAuto::class, 'fetch'])->name('autocomplete.fetch');
  Route::post('/fetchAccount', [BankAuto::class, 'fetchAccount'])->name('autocomplete.fetchAccount');
  Route::post('/fetchIfsc',    [BankAuto::class, 'fetchIfsc'])->name('autocomplete.fetchIfsc');

  Route::get('dashboard',  [RetailerDashboard::class, 'index']);

  Route::get('passbook',         [RetailerPassbook::class, 'index']);
  Route::get('passbook-export',  [RetailerPassbook::class, 'export']);


  Route::resource('e-collection', RetailerECollection::class);

  Route::resource('topup',            RetailerTopup::class);
  Route::get('outlet-payment-mode',  [RetailerTopup::class, 'outletPaymentMode']);
  Route::get('payment-details',      [RetailerTopup::class, 'paymentDetails']);
  Route::get('topup-history',        [RetailerTopup::class, 'topupHistory']);
  Route::get('transaction-history',  [RetailerTopup::class, 'transactionHistory']);
  Route::get('topup-history-ajax',   [RetailerTopup::class, 'topupHistoryAjax']);
  Route::get('topup-history-export', [RetailerTopup::class, 'export']);
  Route::get('pending-topup',        [RetailerTopup::class, 'pendingRequest']);
  Route::get('pending-topup-export', [RetailerTopup::class, 'pendingExport']);


  Route::resource('transaction',    RetailerTransaction::class);
  Route::post('dmt-trans',          [RetailerTransaction::class, 'dmtStore']);
  Route::post('payout-trans',       [RetailerTransaction::class, 'payoutStore']);
  Route::post('payout-api',         [RetailerTransaction::class, 'payoutApiStore']);
  Route::get('sample-csv',          [RetailerTransaction::class, 'sampleCsv']);
  Route::post('payout-import',      [RetailerTransaction::class, 'import']);
  Route::get('import-sequence',     [RetailerTransaction::class, 'importSequence']);
  Route::get('verify-mobile',       [RetailerTransaction::class, 'verifyMobile']);
  Route::get('send-otp',            [RetailerTransaction::class, 'sendOtp']);
  Route::get('fee-details',         [RetailerTransaction::class, 'feeDetails']);
  Route::get('transaction-export',  [RetailerTransaction::class, 'export']);
  Route::get('transaction/receipt/{id}',  [RetailerTransaction::class, 'receipt']);
  Route::post('payout-clame',       [RetailerTransaction::class, 'payoutClame']);
  Route::get('refund-pending',      [RetailerTransaction::class, 'refundPending']);
  Route::get('refund-pending-export', [RetailerTransaction::class, 'refundPendingExport']);
  Route::get('transaction-report',   [RetailerTransaction::class, 'report']);

  Route::resource('retailer-trans', RetailerRetailerTrans::class);
  Route::get('retailer-trans-ajax', [RetailerRetailerTrans::class, 'ajaxList']);
  // Route::get('sample-csv', [RetailerRetailerTrans::class,'sampleCsv']);
  // Route::post('payout-import', [RetailerRetailerTrans::class,'import']);

  Route::resource('offline-payout', OfflinePayout::class);

  Route::resource('webhook-api', WebhookApi::class);
  Route::post('base-url-api',    [WebhookApi::class, 'baseUrlApi']);

  Route::resource('credit-report',           RetailerCredit::class);
  Route::get('credit-export',              [RetailerCredit::class, 'export']);
  Route::resource('debit-report',            RetailerDebit::class);

  Route::post('logout',  [AdminLogin::class, 'logout']);
});


//for employee
Route::group(['prefix' => 'employee', 'middleware' => 'employee'], function () {

  Route::resource('e-profile', EmployeeProfile::class);

  Route::get('dashboard',  [EmployeeDashboard::class, 'index']);
  Route::get('export',     [EmployeeDashboard::class, 'export']);
  Route::post('import',    [EmployeeDashboard::class, 'import']);

  Route::resource('outlets',     EmployeeOutlet::class);
  Route::post('outlets-status', [EmployeeOutlet::class, 'outletStatus']);
  Route::get('outlets-ajax',                  [EmployeeOutlet::class, 'ajaxList']);
  Route::get('outlet-bank-charges/{id}',      [EmployeeOutlet::class, 'outletBankCharges']);
  Route::get('outlet-bank-charges-list',      [EmployeeOutlet::class, 'outletBankChargesList']);
  Route::post('outlet-add-bank-charges',      [EmployeeOutlet::class, 'outletAddBankCharges']);
  Route::get('outlet-edit-bank-charges/{id}', [EmployeeOutlet::class, 'outletEditBankCharges']);
  Route::post('outlet-update-bank-charges',   [EmployeeOutlet::class, 'outletUpdateBankCharges']);
  Route::get('outlet-charges-status/{id}/{key}/{status}', [EmployeeOutlet::class, 'bankChargesStatus']);

  Route::resource('payment-channel',    EmployeePaymentChannel::class);
  Route::get('payment-channel-ajax',    [EmployeePaymentChannel::class, 'ajaxList']);
  Route::post('payment-channel-status', [EmployeePaymentChannel::class, 'paymentChannelStatus']);

  Route::resource('comment',    EmployeeComment::class);
  Route::get('comment-ajax',    [EmployeeComment::class, 'ajaxList']);
  Route::post('comment-status', [EmployeeComment::class, 'commentStatus']);

  Route::get('topup-list',                 [EmployeeTopupRequest::class, 'index']);
  Route::get('topup-list-export',          [EmployeeTopupRequest::class, 'export']);
  Route::post('topup-request',             [EmployeeTopupRequest::class, 'topupRequest']);
  Route::get('topup-request-details/{id}', [EmployeeTopupRequest::class, 'topupRequestDetials']);
  Route::get('topup-payment-channel',      [EmployeeTopupRequest::class, 'updatePaymentChannel']);
  Route::get('pending-topup',              [EmployeeTopupRequest::class, 'pendingRequest']);
  Route::get('pending-topup-export',       [EmployeeTopupRequest::class, 'pendingExport']);

  Route::resource('a-transaction',  EmployeeTransaction::class);
  Route::post('a-store-api',        [EmployeeTransaction::class, 'storeApi']);
  Route::get('a-trans-detail',      [EmployeeTransaction::class, 'viewDetail']);
  Route::get('a-trans-comment',     [EmployeeTransaction::class, 'Comment']);
  Route::get('a-transaction-export', [EmployeeTransaction::class, 'export']);
  Route::post('bulk-action',        [EmployeeTransaction::class, 'bulkAction']);
  Route::get('change-channel',      [EmployeeTransaction::class, 'ChangeChannel']);
  Route::get('payment-status',      [EmployeeTransaction::class, 'PaymentStatus']);
  Route::get('check-bulk-status',   [EmployeeTransaction::class, 'checkBulkStatus']);
  Route::get('update-utr',          [EmployeeTransaction::class, 'updateUtrNo']);
  Route::post('split-transaction',  [EmployeeTransaction::class, 'splitTransaction']);
  Route::get('refund-pending',      [EmployeeTransaction::class, 'refundPending']);
  Route::get('refund-pending-export', [EmployeeTransaction::class, 'refundPendingExport']);
  Route::get('transaction-report',  [EmployeeTransaction::class, 'report']);

  Route::resource('earn-history',  EarnHistory::class);
  Route::get('earn-history-export', [EarnHistory::class, 'export']);

  Route::get('passbook',         [EmployeePassbook::class, 'index']);
  Route::get('passbook-export',  [EmployeePassbook::class, 'export']);

  Route::resource('bank-account',         EmployeeBankAccount::class);
  Route::get('bank-account-ajax',         [EmployeeBankAccount::class, 'ajaxList']);
  Route::post('bank-account-status',      [EmployeeBankAccount::class, 'bankAccountStatus']);
  Route::get('b-allocate-retailer',       [EmployeeBankAccount::class, 'allocateRetailer']);
  Route::post('b-save-allocate-retailer', [EmployeeBankAccount::class, 'saveAllocateRetailer']);

  Route::resource('credit',           EmployeeCredit::class);
  Route::get('credit-export',         [EmployeeCredit::class, 'export']);
  Route::get('credit-show/{id}',      [EmployeeCredit::class, 'showBlance']);
  Route::resource('debit',            EmployeeDebit::class);
  Route::get('debit-show/{id}',       [EmployeeDebit::class, 'showBlance']);
  Route::get('debit-export',          [EmployeeDebit::class, 'export']);
  Route::post('credit-paid-status',   [EmployeeDebit::class, 'creditPaidStatus']);

  Route::resource('qr-code',              EmployeeQrCode::class);
  Route::get('qr-code-ajax',              [EmployeeQrCode::class, 'ajaxList']);
  Route::post('qr-code-status',           [EmployeeQrCode::class, 'qrCodeStatus']);
  Route::get('q-allocate-retailer',       [EmployeeQrCode::class, 'allocateRetailer']);
  Route::post('q-save-allocate-retailer', [EmployeeQrCode::class, 'saveAllocateRetailer']);

  Route::resource('upi',                  EmployeeUpi::class);
  Route::get('upi-ajax',                  [EmployeeUpi::class, 'ajaxList']);
  Route::post('upi-status',               [EmployeeUpi::class, 'upiStatus']);
  Route::get('u-allocate-retailer',       [EmployeeUpi::class, 'allocateRetailer']);
  Route::post('u-save-allocate-retailer', [EmployeeUpi::class, 'saveAllocateRetailer']);

  Route::resource('withdrawal',  EmployeeWithdrawal::class);

  Route::post('logout',  [EmployeeLogin::class, 'logout']);
});


//for Distributor
Route::group(['prefix' => 'distributor', 'middleware' => 'distributor'], function () {

  // Route::resource('e-profile', DistributorProfile::class);

  Route::post('logout',  [DistributorProfile::class, 'logout']);

  Route::resource('profile',     DistributorProfile::class);
  Route::get('pin-password',     [DistributorProfile::class, 'pinPassword']);
  Route::post('change-password', [DistributorProfile::class, 'changePassword']);
  Route::post('change-pin',      [DistributorProfile::class, 'ChangePin']);
  Route::post('send-email-link', [DistributorProfile::class, 'sendLink']);
  Route::get('/forgot-pin/{id}', [DistributorProfile::class, 'forgotPin']);
  Route::post('/forgot-pin',     [DistributorProfile::class, 'forgotPinSave']);

  Route::resource('outlets', DistributorOutlet::class);
  Route::post('outlets-status', [DistributorOutlet::class, 'outletStatus']);
  Route::get('outlets-ajax',                  [DistributorOutlet::class, 'ajaxList']);

  Route::get('dashboard',  [DistributorDashboard::class, 'index']);
  Route::get('export',     [DistributorDashboard::class, 'export']);
  Route::post('import',    [DistributorDashboard::class, 'import']);

  Route::get('passbook',  [DistributorPassbook::class, 'index']);
  Route::get('passbook-export',  [DistributorPassbook::class, 'export']);

  Route::get('topup-list',                 [DistributorTopupRequest::class, 'index']);
  Route::get('topup-list-export',          [DistributorTopupRequest::class, 'export']);
  Route::post('topup-request',             [DistributorTopupRequest::class, 'topupRequest']);
  Route::get('topup-request-details/{id}', [DistributorTopupRequest::class, 'topupRequestDetials']);
  Route::get('topup-payment-channel',      [DistributorTopupRequest::class, 'updatePaymentChannel']);
  Route::get('pending-topup',              [DistributorTopupRequest::class, 'pendingRequest']);
  Route::get('pending-topup-export',       [DistributorTopupRequest::class, 'pendingExport']);

  Route::resource('a-transaction',  DistributorTransaction::class);
  Route::post('a-store-api',        [DistributorTransaction::class, 'storeApi']);
  Route::get('a-trans-detail',      [DistributorTransaction::class, 'viewDetail']);
  Route::get('a-trans-comment',     [DistributorTransaction::class, 'Comment']);
  Route::get('a-transaction-export', [DistributorTransaction::class, 'export']);
  Route::post('bulk-action',        [DistributorTransaction::class, 'bulkAction']);
  Route::get('change-channel',      [DistributorTransaction::class, 'ChangeChannel']);
  Route::get('payment-status',      [DistributorTransaction::class, 'PaymentStatus']);
  Route::get('check-bulk-status',   [DistributorTransaction::class, 'checkBulkStatus']);
  Route::get('update-utr',          [DistributorTransaction::class, 'updateUtrNo']);
  Route::post('split-transaction',  [DistributorTransaction::class, 'splitTransaction']);


  //for make payment
  Route::resource('make-transaction', MakeTransaction::class);
  Route::post('dmt-trans',          [MakeTransaction::class, 'dmtStore']);
  Route::post('payout-trans',       [MakeTransaction::class, 'payoutStore']);
  Route::post('payout-api',         [MakeTransaction::class, 'payoutApiStore']);
  Route::get('sample-csv',          [MakeTransaction::class, 'sampleCsv']);
  Route::post('payout-import',      [MakeTransaction::class, 'import']);
  Route::get('import-sequence',     [MakeTransaction::class, 'importSequence']);
  Route::get('verify-mobile',       [MakeTransaction::class, 'verifyMobile']);
  Route::get('send-otp',            [MakeTransaction::class, 'sendOtp']);
  Route::get('fee-details',         [MakeTransaction::class, 'feeDetails']);
  Route::get('transaction-export',  [MakeTransaction::class, 'export']);

  //for make topup
  Route::resource('make-topup',      MakeTopup::class);
  Route::get('outlet-payment-mode',  [MakeTopup::class, 'outletPaymentMode']);
  Route::get('payment-details',      [MakeTopup::class, 'paymentDetails']);
  Route::get('topup-history',        [MakeTopup::class, 'topupHistory']);
  Route::get('transaction-history',  [MakeTopup::class, 'transactionHistory']);
  Route::get('topup-history-ajax',   [MakeTopup::class, 'topupHistoryAjax']);
  Route::get('topup-history-export', [MakeTopup::class, 'export']);

  Route::resource('webhook-api', DWebhookApi::class);
  Route::post('base-url-api', [DWebhookApi::class, 'baseUrlApi']);

  // Route::post('logout',  [DistributorLogin::class, 'logout']);
});

// test payment

Route::get('/test-payment', function () {
  return view('test-payment');
})->name('payment');

Route::get('/eCollectionProcess/{amount}/{txn}/{id}/{hash}', [EcollectionController::class, 'eCollectionNew']);
