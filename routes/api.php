<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\IndexController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DradviceController;
use App\Http\Controllers\api\TestInfoController;
use App\Http\Controllers\api\MemberInfoController;
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

Route::group(['prefix' => 'v1'], function () {
    Route::get('/footer', [IndexController::class, 'index']);
    Route::get('/footer/{id}', [IndexController::class, 'getContent']);
    Route::post('/footer/contact', [IndexController::class, 'postContact']);
});

Route::group(['middleware' => 'jwt.member'], function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('/members/logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::post('/membership', [MemberInfoController::class, 'profile']);
    Route::put('/membership', [MemberInfoController::class, 'updateProfile']);
   // Route::resource('/membership',MemberInfoController::class, ['names' => ['index' => 'membership']]);
});

Route::get('area', [DradviceController::class, 'area']);
Route::get('area/{all}', [DradviceController::class, 'area']);

//Route::post('members/login', [AuthController::class, 'login']);
//Route::post('members/login', [DradviceController::class, 'memberLogin']);


Route::group(['prefix' => 'members'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});
