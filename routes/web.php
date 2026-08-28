<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'welcome']);
Route::get('/index/json', [IndexController::class, 'json']);
Route::get('/index/benchmark', [IndexController::class, 'benchmark']);
Route::get('/index/view', [IndexController::class, 'welcome']);
