<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfflinePayoutController as offlinePayout;
use App\Http\Controllers\Api\LoginController;


Route::post('/login', [LoginController::class, 'store']);

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('offline-payout',      [offlinePayout::class, 'payout']);
    Route::post('bulk-offline-payout', [offlinePayout::class, 'bulkPayout']);
});
