<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello World';
});

//Route::middleware(['auth:admin'])->prefix('user')->group(function (){
//    Route::get('/index', 'UserController@index');
//    Route::get('/detail/{id}', 'UserController@detail');
//});

Route::prefix('user')->group(function (){
    Route::get('/index', 'UserController@index');
    Route::get('/detail/{id}/{name}', 'UserController@detail')->where('name', '\w+');
    Route::get('/export', [UserController::class, 'export']);
    Route::get('/queue', [UserController::class, 'queue']);
    Route::get('/event', [UserController::class, 'event']);
});

Route::resource('photos', 'PhotoController');

