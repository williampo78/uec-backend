<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\IndexController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DradviceController;
use App\Http\Controllers\api\TestInfoController;
use App\Http\Controllers\api\MemberInfoController;
use App\Http\Controllers\api\PointInfoController;
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
    //Route::get('me', [AuthController::class, 'me']);
    Route::post('/members/logout', [AuthController::class, 'logout']);
    //Route::post('refresh', [AuthController::class, 'refresh']);

    Route::post('/membership', [MemberInfoController::class, 'profile']);
    Route::put('/membership', [MemberInfoController::class, 'updateProfile']);
    Route::put('/membership/changePassWord', [MemberInfoController::class, 'changePassWord']);

    Route::post('/membership/point', [PointInfoController::class, 'point']);
    Route::post('/membership/expiringPoint', [PointInfoController::class, 'expiringPoint']);

    Route::post('/membership/notes', [MemberInfoController::class, 'createNotes']);
    Route::get('/membership/notes', [MemberInfoController::class, 'notes']);
    Route::put('/membership/{id}/notes', [MemberInfoController::class, 'updateNotes']);
    Route::delete('/membership/{id}/notes', [MemberInfoController::class, 'deleteNotes']);

    Route::get('/membership/collections', [MemberInfoController::class, 'collections']);
    Route::delete('/membership/collections', [MemberInfoController::class, 'deleteCollections']);
    Route::post('/membership/collections', [MemberInfoController::class, 'createCollections']);
});

Route::get('area', [DradviceController::class, 'area']);
Route::get('area/{all}', [DradviceController::class, 'area']);

Route::group(['prefix' => 'members'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});
