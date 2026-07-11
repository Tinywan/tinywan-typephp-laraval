<?php

use App\Http\Controllers\User\v1\AuthController;
use App\Http\Controllers\User\v1\OrderController;

Route::post('auth/login', [AuthController::class,'login'])->middleware('check.mini');

Route::prefix('v1')
    -> middleware([
        'check.mini',
        'auth:mini',
    ])
    ->group(function () {
    // 1.0 订单管理
    Route::prefix('order')->group(function () {
        Route::get('index', [OrderController::class, 'index']);
        Route::get('userSync', [OrderController::class, 'userSync']);
        Route::post('create', [OrderController::class, 'create']);
        Route::put('{id}', [OrderController::class, 'update']);
        Route::delete('{id}', [OrderController::class, 'destroy']);
    });
});
