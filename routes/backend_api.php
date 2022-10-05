<?php

use App\Http\Controllers\api\ReturnRequestController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    //退款
    Route::put('/refund', [ReturnRequestController::class, 'refund']);
});
