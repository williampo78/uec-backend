<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\IndexController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DradviceController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1'], function (){
    Route::get('/footer', [IndexController::class, 'index']);
    Route::get('/footer/{id}', [IndexController::class, 'getContent']);
    Route::post('/footer/contact', [IndexController::class, 'postContact']);
});

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\api'], function () {
    Route::get('/', 'AuthController@me')->name('me');
    Route::post('logout', 'AuthController@logout')->name('logout');
});

Route::get('area', [DradviceController::class, 'area']);

Route::post('login', [AuthController::class,'login']);
Route::post('members/login', [DradviceController::class, 'memberLogin']);
