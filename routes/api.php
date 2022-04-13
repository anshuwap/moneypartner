<?php

use App\Http\Controllers\Api\EcollectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfflinePayoutController as offlinePayout;
use App\Http\Controllers\Api\LoginController;


Route::post('/login', [LoginController::class, 'store']);

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('payout',      [offlinePayout::class, 'payout']);
    Route::post('bulk-payout', [offlinePayout::class, 'bulkPayout']);
    Route::post('eCollection', [EcollectionController::class, 'eCollection']);
});


Route::get('eCollectionTemp/{outlet_name}/{amount}/{payer_name}', [EcollectionController::class, 'eCollectionTemp']);

Route::post('e-collection-webhook',      [EcollectionController::class, 'webhookUrl']);
