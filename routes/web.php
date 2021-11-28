<?php

use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OutletController as AdminOutlet;
use Illuminate\Support\Facades\Route;


 Route::group(['middleware' => 'adminRedirect'], function () {
    Route::get('/', [AdminLogin::class, 'index']);
    Route::resource('/login', AdminLogin::class);
    Route::resource('/register', RegisterController::class);
});


Route::group(['middleware' => 'retailerRedirect'], function () {
    Route::get('/', [RetailerLogin::class, 'index']);
    Route::resource('/login', RetailerLogin::class);
    Route::resource('/register', RegisterController::class);
});

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::resource('dashboard', AdminDashboard::class);

    Route::resource('outlets', AdminOutlet::class);
    Route::post('outlets-status', [AdminOutlet::class,'outletStatus']);
    Route::get('outlet-bank-get/{id}', [AdminOutlet::class,'outletBankGet']);
    Route::post('outlet-bank', [AdminOutlet::class,'outletBank']);
    Route::get('outlets-ajax', [AdminOutlet::class,'ajaxList']);

    Route::post('logout',  [AdminLogin::class, 'logout']);
});


Route::group(['prefix' => 'retailer', 'middleware' => 'retailer'], function () {
    Route::resource('dashboard', RetailerDashboard::class);

    Route::post('logout',  [RetailerLogin::class, 'logout']);
});