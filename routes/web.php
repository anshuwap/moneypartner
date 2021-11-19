<?php

use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use Illuminate\Support\Facades\Route;


 Route::group(['middleware' => 'adminRedirect'], function () {
    Route::get('/', [AdminLogin::class, 'index']);
    Route::resource('/login', AdminLogin::class);
    Route::resource('/register', RegisterController::class);
});


Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::resource('dashboard', AdminDashboard::class);

    Route::post('logout',  [AdminLogin::class, 'logout']);
});