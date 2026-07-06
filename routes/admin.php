<?php

use App\Http\Controllers\Admin\v1\ActiveController;
use App\Http\Controllers\Admin\v1\PostController;
use App\Http\Controllers\Admin\v1\Shop\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    $token = \auth('admin')->attempt($credentials);
//    $token = Auth::guard('admin')->attempt($credentials);
    if (!$token) {
        return response_json('账号或密码错误', 401, [], 401);
    }

    return response_json('登录成功', 0, [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('admin')->factory()->getTTL() * 60,
    ]);
});

Route::prefix('v1')
    -> middleware([
        'auth:admin',
        'check.admin'
    ])
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
        Route::post('student', [ShopController::class, 'student']);
        Route::post('teacher', [ShopController::class, 'teacher']);
    });
    // 3.0 文章管理
    Route::prefix('post')->group(function () {
        Route::get('update/{id}', [PostController::class, 'update']);
    });
});
