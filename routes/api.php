<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\IndexController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\StockController;
use App\Http\Controllers\api\MemberController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CheckoutController;
use App\Http\Controllers\api\DradviceController;
use App\Http\Controllers\api\MessagesController;
use App\Http\Controllers\api\ShoppingController;
use App\Http\Controllers\api\PointInfoController;
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
    Route::get('/qa', [IndexController::class, 'getQA']);

    Route::get('/product/category', [ProductController::class, 'getCategory']);
    Route::get('/product/{id}', [ProductController::class, 'getProduct']);
    Route::get('/campaign/{id}', [ProductController::class, 'getCampaignGift']);

    Route::get('/ad_slots', [IndexController::class, 'getAdSlots']);
    Route::post('/advanceSearch', [ProductController::class, 'getProductSearchResult']);
    Route::post('/advanceSearchCategory', [ProductController::class, 'getProductSearchResultCategory']);

    Route::get('/stock', [StockController::class, 'getItemStock']);

    //Breadcrumb for category
    Route::post('/BreadcrumbCategory', [ProductController::class, 'getBreadcrumbCategory']);

    //UTM
    Route::get('/utm', [IndexController::class, 'getUTM']);
});

// jwt驗證
Route::group(['middleware' => 'jwt.verify'], function () {
    Route::post('/members/logout', [AuthController::class, 'logout']);

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
    Route::post('/membership/collections', [MemberInfoController::class, 'setCollections']);
    Route::post('/membership/collections/batchDelete', [MemberInfoController::class, 'batchDeleteCollections']);

    Route::group(['prefix' => 'shopping'], function () {
        Route::post('/setMemberCart', [ShoppingController::class, 'setCart']);
        Route::post('/shoppingCartData', [ShoppingController::class, 'getShoppingCartData']);
        Route::post('/batchSetCart', [ShoppingController::class, 'setBatchCart']);
        Route::post('/addGoodsQty', [ShoppingController::class, 'addGoodsQty']);
    });

    Route::group(['prefix' => 'checkout'], function () {
        Route::post('/tmpOrder', [CheckoutController::class, 'setTmpOrder']);
        Route::post('/checkOrder', [CheckoutController::class, 'setOrder']);
    });
    Route::resource('/members/message', MessagesController::class, ['names' => ['index' => 'members.message']]);
    Route::get('/members/message-top/', [MessagesController::class, 'messageTop']);

    Route::get('/tapPayApp', [CheckoutController::class, 'tapPayApp']);

});

// jwt v2驗證
Route::group(['middleware' => 'jwt.verify'], function () {
    // 單一會員
    Route::group(['prefix' => 'member'], function () {
        // 取得會員訂單列表
        Route::get('/orders', [MemberController::class, 'getOrders']);

        // 取得會員訂單詳細內容
        Route::get('/orders/{order_no}', [MemberController::class, 'getOrderDetail']);

        // 取消訂單
        Route::post('/orders/{order_no}/cancel', [MemberController::class, 'cancelOrder']);

        // 申請退貨
        Route::post('/orders/{order_no}/return', [MemberController::class, 'returnOrder']);
    });
});

Route::get('area', [DradviceController::class, 'area']);
Route::get('area/{all}', [DradviceController::class, 'area']);

Route::group(['prefix' => 'members'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/registration', [AuthController::class, 'registration']);
    Route::post('/memberStatus', [AuthController::class, 'getMemberStatus']);
    Route::post('/sendSms', [AuthController::class, 'sendSMS']);
    Route::post('/verifySms', [AuthController::class, 'verifySMS']);
    Route::post('/memberBasic', [AuthController::class, 'memberBasic']);
});

// 單一會員
Route::group(['prefix' => 'member'], function () {
    // 重設會員密碼
    Route::post('/password', [MemberController::class, 'resetPassword']);
});

Route::group(['prefix' => 'order'], function () {
    // 取得訂單取消原因的選項
    Route::get('/cancel-reason-options', [OrderController::class, 'getCancelReasonOptions']);

    // 取得訂單退貨原因的選項
    Route::get('/return-reason-options', [OrderController::class, 'getReturnReasonOptions']);
});

Route::group(['prefix' => 'shopping'], function () {
    Route::get('/getCartCount', [ShoppingController::class, 'getCartCount']);
});

Route::group(['prefix' => 'checkout'], function () {
    Route::get('/donatedInstitution', [CheckoutController::class, 'getDonatedInstitution']);
    Route::post('/tapPayNotify', [CheckoutController::class, 'tapPayNotify']);
});
