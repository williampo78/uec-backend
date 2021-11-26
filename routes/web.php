<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QAController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController ;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController ;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\OrderSupplierController;
use App\Http\Controllers\SupplierTypeController ;
use App\Http\Controllers\PrimaryCategoryController;
use App\Http\Controllers\QuotationReviewController;
use App\Http\Controllers\AdvertisementBlockController;
use App\Http\Controllers\AdvertisementLaunchController;
use App\Http\Controllers\RequisitionsPurchaseController;
use App\Http\Controllers\WebCategoryProductsController ;
use App\Http\Controllers\WebCategoryHierarchyController ;
use CKSource\CKFinderBridge\Controller\CKFinderController;
use App\Http\Controllers\RequisitionsPurchaseReviewController;
use App\Http\Controllers\WebContentsController;
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

Route::any('/ckfinder/connector', [CKFinderController::class, 'requestAction'])->name('ckfinder_connector');
Route::group(['prefix' => 'backend', 'middleware' => ['admin']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('backend-home');
    Route::get('registration', [LoginAuthController::class, 'registration'])->name('register-user');
    Route::get('/signOut', [AdminController::class, 'signOut'])->name('signOut');
    Route::resource('/products', ProductsController::class, ['names' => ['index' => 'products']]);
    Route::get('/productsTest',[ProductsController::class,'testview']) ;
    Route::post('/upload_img',[ProductsController::class,'upload_img']) ;
    Route::resource('/admin', AdminController::class);
    Route::resource('/item', ItemController::class, ['names' => ['index' => 'item']]);
    Route::resource('/supplier', SupplierController::class, ['names' => ['index' => 'supplier']]);
    Route::post('/item/ajaxphoto/del',[ItemController::class,'ajax_del_Item_photo']); //刪除照片ajax
    Route::post('/contact/ajax/del',[ContactController::class,'ajax_del_contact']); //刪除聯絡人
    Route::resource('/warehouse',WarehouseController::class, ['names' => ['index' => 'warehouse']]);
    Route::resource('/department', DepartmentController::class, ['names' => ['index' => 'department']]);
    Route::resource('/supplier_type', SupplierTypeController::class, ['names' => ['index' => 'supplier_type']]);
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
    Route::resource('/qa',QAController::class, ['names' => ['index' => 'qa']]);
    Route::resource('/web_category_hierarchy',WebCategoryHierarchyController::class, ['names' => ['index' => 'web_category_hierarchy']]) ;
    Route::post('/web_category_hierarchy/ajax',[WebCategoryHierarchyController::class,'ajax']) ;
    Route::resource('/web_category_products',WebCategoryProductsController::class,['names' => ['index' => 'web_category_products']]) ;
    Route::post('/web_category_products/ajax',[WebCategoryProductsController::class,'ajax']) ;

    // 廣告版位
    Route::resource('/advertisemsement_block', AdvertisementBlockController::class, ['names' => ['index' => 'advertisemsement_block']]);
    Route::post('/advertisemsement_block/ajax/detail', [AdvertisementBlockController::class, 'getDetailByAjax']);

    // 廣告上架
    Route::resource('/advertisemsement_launch', AdvertisementLaunchController::class, ['names' => ['index' => 'advertisemsement_launch']]);
    Route::post('/advertisemsement_launch/ajax/detail', [AdvertisementLaunchController::class, 'getDetailByAjax']);

    Route::resource('/webcontents',WebContentsController::class, ['names' => ['index' => 'webcontents']]);
});

Route::get('/', [LoginAuthController::class, 'index'])->name('login');
Route::post('custom-login', [LoginAuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [LoginAuthController::class, 'customRegistration'])->name('register.custom');

