<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\DashboardController;

Route::group(['namespace' => 'Customer', 'as' => 'customer.'], function () {
    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['namespace' => 'Auth', 'as' => 'auth.'], function () {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit'])->name('login.submit');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auth:customer']], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('lang/{locale}', [DashboardController::class, 'lang'])->name('lang');
        Route::get('settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('settings', [DashboardController::class, 'settings_update']);
        Route::post('settings/password', [DashboardController::class, 'settings_password_update'])->name('settings-password');
    });
});
