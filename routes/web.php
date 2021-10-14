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
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierControllers;
use App\Http\Controllers\QuotationReviewController;
use App\Http\Controllers\ContactControllers ; 
use App\Models\Item;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
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
    Route::get('/', [AdminControllers::class, 'index']);
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
    Route::resource('/requisitions_purchase',RequisitionsPurchaseController::class, ['names' => ['index' => 'requisitions_purchase']]);
    Route::post('/requisitions_purchase/ajax',[RequisitionsPurchaseController::class,'ajax']);
    Route::resource('/quotation',QuotationController::class, ['names' => ['index' => 'quotation']]);
    Route::post('/quotation/ajax',[QuotationController::class,'ajax']);
    Route::resource('/quotation_review',QuotationReviewController::class, ['names' => ['index' => 'quotation_review']]);

    Route::get('/test', [TestController::class, 'index'])->name('test');
});

Route::get('/', [LoginAuthController::class, 'index'])->name('login');
Route::post('custom-login', [LoginAuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [LoginAuthController::class, 'customRegistration'])->name('register.custom');
