<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverStatusController;
use App\Http\Middleware\AdminAuthorization;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\TheDriverIsHim;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post("sign-in", "signIn");
    Route::post("refresh", "refresh");
});

Route::middleware([Authenticate::class, AdminAuthorization::class])->group(function () {
    Route::controller(DriverController::class)->group(function () {
        Route::get("driver", "index");
        Route::get("driver/{driver}", "show");
        Route::post("driver", "store");
        Route::put("driver/{driver}", "update");
        Route::delete("driver/{driver}", "destroy");
    });

    Route::controller(CredentialController::class)->group(function () {
        Route::get("account", "index");
        Route::patch("account/{credential}", "reset");
    });

    Route::controller(DriverStatusController::class)->group(function () {
        Route::get("driver-status", "index");
    });
});

Route::middleware([Authenticate::class, TheDriverIsHim::class])->group(function () {
    Route::controller(DriverStatusController::class)->group(function () {
        Route::get("driver-status/{driver}", "getOne");
        Route::post("driver-status/{driver}/upsert", "upsert");
    });
});
