<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfflinePayoutController as offlinePayout;
use App\Http\Controllers\Api\LoginController;


Route::post('/login', [LoginController::class, 'store']);

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('payout',      [offlinePayout::class, 'payout']);
    Route::post('bulk-payout', [offlinePayout::class, 'bulkPayout']);
});
