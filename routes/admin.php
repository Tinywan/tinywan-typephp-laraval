<?php

use App\Http\Controllers\Admin\v1\ActiveController;
use App\Http\Controllers\Admin\v1\Shop\ShopController;
use Illuminate\Http\Request;

Route::prefix('v1')
    -> middleware('check.admin')
    ->group(function () {
    // 1.0 活动管理
    Route::prefix('active')->group(function () {
        Route::get('index', [ActiveController::class, 'index']);
        Route::get('detail/{id}', [ActiveController::class, 'detail']);
    });
    // 2.0 商品管理
    Route::prefix('shop')->group(function () {
        Route::get('index', [ShopController::class, 'index']);
        Route::get('detail/{id}', [ShopController::class, 'detail']);
    });
});
