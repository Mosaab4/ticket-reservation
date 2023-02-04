<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TripController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\StationController;
use App\Http\Controllers\API\V1\SessionController;


Route::group(['middleware' => 'json.response', 'prefix' => 'v1'], function () {
    Route::post('login', 'App\Http\Controllers\API\V1\LoginController');

    Route::get('frequent-trips', 'App\Http\Controllers\API\V1\FrequentTripController');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('trips', [TripController::class, 'index']);
        Route::get('trips/{trip}', [TripController::class, 'show']);

        Route::get('stations', [StationController::class, 'index']);

        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);
        Route::post('orders', [OrderController::class, 'store']);


        Route::get('sessions', [SessionController::class, 'index']);
        Route::post('sessions', [SessionController::class, 'store']);
    });
});
