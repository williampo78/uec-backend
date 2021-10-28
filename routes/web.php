<?php

use App\Http\Controllers\AdminControllers;
use App\Http\Controllers\ItemControllers;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\DepartmentControllers;
use App\Http\Controllers\SupplierTypeControllers ;
use App\Http\Controllers\WarehouseController ;
use App\Http\Controllers\PrimaryCategoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RequisitionsPurchaseController;
use App\Http\Controllers\RequisitionsPurchaseReviewController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierControllers;
use App\Http\Controllers\QuotationReviewController;
use App\Http\Controllers\OrderSupplierController;
use App\Http\Controllers\ContactControllers ;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
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
Route::group(['prefix' => 'backend', 'middleware' => ['admin']], function () {
    Route::get('/', [AdminControllers::class, 'index'])->name('backend-home');
    Route::get('registration', [LoginAuthController::class, 'registration'])->name('register-user');
    Route::get('/signOut', [AdminControllers::class, 'signOut'])->name('signOut');
    Route::resource('/admin', AdminControllers::class);
    Route::resource('/item', ItemControllers::class, ['names' => ['index' => 'item']]);
    Route::resource('/supplier', SupplierControllers::class, ['names' => ['index' => 'supplier']]);
    Route::post('/item/ajaxphoto/del',[ItemControllers::class,'ajax_del_Item_photo']); //刪除照片ajax
    Route::post('/contact/ajax/del',[ContactControllers::class,'ajax_del_contact']); //刪除聯絡人
    Route::resource('/warehouse',WarehouseController::class, ['names' => ['index' => 'warehouse']]);
    Route::resource('/department', DepartmentControllers::class, ['names' => ['index' => 'department']]);
    Route::resource('/supplier_type', SupplierTypeControllers::class, ['names' => ['index' => 'supplier_type']]);
    Route::resource('/primary_category',PrimaryCategoryController::class, ['names' => ['index' => 'primary_category']]);
    Route::resource('/category',CategoryController::class, ['names' => ['index' => 'category']]);
    Route::resource('/requisitions_purchase',RequisitionsPurchaseController::class, ['names' => ['index' => 'requisitions_purchase']]); //請購單

    Route::get('/getItemLastPrice',[RequisitionsPurchaseController::class, 'getItemLastPrice']); //請購單

    Route::resource('/requisitions_purchase_review',RequisitionsPurchaseReviewController::class, ['names' => ['index' => 'requisitions_purchase_review']]);
    Route::post('/requisitions_purchase/ajax',[RequisitionsPurchaseController::class,'ajax']);
    Route::resource('/quotation',QuotationController::class, ['names' => ['index' => 'quotation']]);
    Route::post('/quotation/ajax',[QuotationController::class,'ajax']);
    Route::resource('/quotation_review',QuotationReviewController::class, ['names' => ['index' => 'quotation_review']]);
    Route::post('/quotation/ajaxDelItem' , [QuotationController::class,'ajaxDelItem']);
    Route::resource('/order_supplier',OrderSupplierController::class, ['names' => ['index' => 'order_supplier']]);
    Route::post('/order_supplier/ajax' , [OrderSupplierController::class, 'ajax']);

    Route::resource('/test',TestController::class, ['names' => ['index' => 'test']]);
    Route::resource('/profile',UsersController::class, ['names' => ['index' => 'profile']]);
    Route::resource('/roles',RolesController::class, ['names' => ['index' => 'roles']]);
    Route::resource('/users',UsersController::class, ['names' => ['index' => 'users']]);
    Route::get('/usersAjax', [UsersController::class, 'ajax']);     //驗證使用者帳號
    Route::get('/user_profile', [UsersController::class, 'profile']);
    Route::post('/user_profile', [UsersController::class, 'updateProfile']);
    Route::post('/users/ajax',[UsersController::class,'ajaxDetail']);
    Route::resource('/qa',AdminControllers::class, ['names' => ['index' => 'qa']]);
});

Route::get('/', [LoginAuthController::class, 'index'])->name('login');
Route::post('custom-login', [LoginAuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [LoginAuthController::class, 'customRegistration'])->name('register.custom');
