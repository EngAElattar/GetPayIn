<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Profiler\Profile;
use App\Http\Controllers\Api\V1\Order\HoldController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Auth\AuthJwtController;
use App\Http\Controllers\Api\V1\User\profileController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Product\ProductController;

Route::prefix('v1')
    ->middleware(['throttle:60,1'])->group(function () {

        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        Route::controller(HoldController::class)->prefix('holds')->group(function () {
            Route::post('/', 'store');
        });

        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::post('/', 'store');
        });
        Route::controller(PaymentController::class)->prefix('payments')->group(function () {
            Route::post('/webhook', 'handle');
        });
    });
