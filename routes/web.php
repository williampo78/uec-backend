<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertisementBlockController;
use App\Http\Controllers\AdvertisementLaunchController;
use App\Http\Controllers\BuyoutProductsReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExternalInventoryDailyReportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPaymentsReportController;
use App\Http\Controllers\OrderRefundController;
use App\Http\Controllers\OrderSupplierController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\PrimaryCategoryController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductReviewRegisterController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductsMallController;
use App\Http\Controllers\PromotionalCampaignCartController;
use App\Http\Controllers\PromotionalCampaignController;
use App\Http\Controllers\PromotionalCampaignPrdController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationReviewController;
use App\Http\Controllers\RequisitionsPurchaseController;
use App\Http\Controllers\RequisitionsPurchaseReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SummaryStockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierTypeController;
use App\Http\Controllers\TertiaryCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WebCategoryHierarchyController;
use App\Http\Controllers\WebCategoryProductsController;
use App\Http\Controllers\WebContentsController;
use CKSource\CKFinderBridge\Controller\CKFinderController;
use Illuminate\Support\Facades\Route;

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
    Route::get('registration', [LoginAuthController::class, 'registration'])->name('register-user');
    Route::get('/', [AdminController::class, 'index'])->name('backend-home');
    Route::get('/signOut', [AdminController::class, 'signOut'])->name('signOut');
    Route::resource('/admin', AdminController::class);

    // 倉庫管理
    Route::resource('/warehouse', WarehouseController::class, ['names' => ['index' => 'warehouse']]);

    // 供應商類別管理
    Route::resource('/supplier_type', SupplierTypeController::class, ['names' => ['index' => 'supplier_type']]);

    //刪除聯絡人
    Route::post('/contact/ajax/del', [ContactController::class, 'ajax_del_contact']);

    // 供應商主檔管理
    Route::post('/supplier/ajax', [SupplierController::class, 'ajax']);
    Route::resource('/supplier', SupplierController::class, ['names' => ['index' => 'supplier']]);

    // 商品主檔 - 基本資訊管理
    Route::post('/products/ajax', [ProductsController::class, 'ajax']);
    Route::resource('/products', ProductsController::class, ['names' => ['index' => 'products']]);

    // 商品主檔 - 商城資訊管理
    Route::post('/products_mall/ajax', [ProductsMallController::class, 'ajax']);
    Route::resource('/products_mall', ProductsMallController::class, ['names' => ['index' => 'products_mall']]);

    // 商品主檔 - 上下架申請
    Route::post('/product_review_register/ajax', [ProductReviewRegisterController::class, 'ajax']);
    Route::resource('/product_review_register', ProductReviewRegisterController::class, ['names' => ['index' => 'product_review_register']]);

    // 商品主檔 - 上架審核
    Route::resource('/product_review', ProductReviewController::class, ['names' => ['index' => 'product_review']]);

    //刪除圖片
    Route::post('/del_photos', [PhotosController::class, 'delPhoto']);

    // 部門管理
    Route::resource('/department', DepartmentController::class, ['names' => ['index' => 'department']]);

    // POS大分類管理
    Route::resource('/primary_category', PrimaryCategoryController::class, ['names' => ['index' => 'primary_category']]);

    // POS中分類管理
    Route::resource('/category', CategoryController::class, ['names' => ['index' => 'category']]);

    // POS小分類管理
    // 列表
    Route::get('/tertiary_category', [TertiaryCategoryController::class, 'index'])->name('tertiary_category');
    // 新增
    Route::get('/tertiary_category/create', [TertiaryCategoryController::class, 'create'])->name('tertiary_category.create');
    // 新增post
    Route::post('/tertiary_category/store', [TertiaryCategoryController::class, 'store'])->name('tertiary_category.store');
    // 更新
    Route::get('/tertiary_category/{id}/edit', [TertiaryCategoryController::class, 'edit'])->name('tertiary_category.edit');
    // 更新post
    Route::put('/tertiary_category/{id}', [TertiaryCategoryController::class, 'update'])->name('tertiary_category.update');

    // 報價單
    Route::post('/quotation/ajax', [QuotationController::class, 'ajax']);
    Route::post('/quotation/ajaxDelItem', [QuotationController::class, 'ajaxDelItem']);
    Route::resource('/quotation', QuotationController::class, ['names' => ['index' => 'quotation']]);

    // 報價單審核
    Route::resource('/quotation_review', QuotationReviewController::class, ['names' => ['index' => 'quotation_review']]);

    //請購單
    Route::post('/requisitions_purchase/ajax', [RequisitionsPurchaseController::class, 'ajax']);
    Route::get('/getItemLastPrice', [RequisitionsPurchaseController::class, 'getItemLastPrice']);
    Route::resource('/requisitions_purchase', RequisitionsPurchaseController::class, ['names' => ['index' => 'requisitions_purchase']]);

    // 請購單簽核
    Route::resource('/requisitions_purchase_review', RequisitionsPurchaseReviewController::class, ['names' => ['index' => 'requisitions_purchase_review']]);

    // 採購單
    Route::post('/order_supplier/ajax', [OrderSupplierController::class, 'ajax']);
    Route::resource('/order_supplier', OrderSupplierController::class, ['names' => ['index' => 'order_supplier']]);

    // 進貨單
    Route::post('/purchase/ajax', [PurchaseController::class, 'ajax']);
    Route::resource('/purchase', PurchaseController::class, ['names' => ['index' => 'purchase']]);

    // 庫存單
    // 列表
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    // 匯出excel
    Route::get('/inventory/ajax/excel', [InventoryController::class, 'exportExcel'])->name('inventory.export_excel');

    // 外倉庫存日報表
    // 列表
    Route::get('/external_inventory_daily_report', [ExternalInventoryDailyReportController::class, 'index'])->name('external_inventory_daily_report');
    // 匯出excel
    Route::get('/external_inventory_daily_report/ajax/excel', [ExternalInventoryDailyReportController::class, 'exportExcel'])->name('external_inventory_daily_report.export_excel');

    // 退貨申請單管理
    Route::group(['prefix' => 'order_refund'], function () {
        // 列表
        Route::get('/', [OrderRefundController::class, 'index'])->name('order_refund');
        // 匯出excel
        Route::get('/ajax/excel', [OrderRefundController::class, 'exportExcel'])->name('order_refund.export_excel');
        // 詳細資料
        Route::get('/ajax/detail', [OrderRefundController::class, 'getDetail'])->name('order_refund.detail');
    });

    // 金流對帳單
    Route::group(['prefix' => 'order_payments_report'], function () {
        // 列表
        Route::get('/', [OrderPaymentsReportController::class, 'index'])->name('order_payments_report');
        // 匯出excel
        Route::get('/ajax/excel', [OrderPaymentsReportController::class, 'exportExcel'])->name('order_payments_report.export_excel');
    });

    Route::resource('/test', TestController::class, ['names' => ['index' => 'test']]);

    // 角色管理
    Route::resource('/roles', RoleController::class, ['names' => ['index' => 'roles']]);

    // 使用者管理
    Route::resource('/profile', UserController::class, ['names' => ['index' => 'profile']]);
    Route::resource('/users', UserController::class, ['names' => ['index' => 'users']]);
    Route::post('/users/ajax/is-user-account-repeat', [UserController::class, 'isUserAccountRepeat']); //驗證使用者帳號是否重複
    Route::get('/user_profile', [UserController::class, 'profile']);
    Route::put('/user_profile', [UserController::class, 'updateProfile'])->name('user_profile.update');

    // 常見問題Q&A
    Route::resource('/qa', QAController::class, ['names' => ['index' => 'qa']]);

    // 分類階層管理
    Route::resource('/web_category_hierarchy', WebCategoryHierarchyController::class, ['names' => ['index' => 'web_category_hierarchy']]);
    Route::post('/web_category_hierarchy/ajax', [WebCategoryHierarchyController::class, 'ajax']);

    // 分類階層內容管理
    Route::resource('/web_category_products', WebCategoryProductsController::class, ['names' => ['index' => 'web_category_products']]);
    Route::post('/web_category_products/ajax', [WebCategoryProductsController::class, 'ajax']);

    // 廣告版位
    Route::resource('/advertisemsement_block', AdvertisementBlockController::class, [
        'names' => [
            'index' => 'advertisemsement_block',
        ],
    ]);

    // 廣告上架
    Route::post('/advertisemsement_launch/ajax/can-pass-active-validation', [AdvertisementLaunchController::class, 'canPassActiveValidation']);
    Route::resource('/advertisemsement_launch', AdvertisementLaunchController::class, [
        'names' => [
            'index' => 'advertisemsement_launch',
        ],
    ]);

    // 滿額活動
    Route::resource('/promotional_campaign_cart', PromotionalCampaignCartController::class, [
        'names' => [
            'index' => 'promotional_campaign_cart',
        ],
    ]);
    Route::post('/promotional_campaign_cart/ajax/can-pass-active-validation', [PromotionalCampaignCartController::class, 'canPassActiveValidation']);

    // 單品活動
    Route::resource('/promotional_campaign_prd', PromotionalCampaignPrdController::class, [
        'names' => [
            'index' => 'promotional_campaign_prd',
        ],
    ]);
    Route::post('/promotional_campaign_prd/ajax/can-pass-active-validation', [PromotionalCampaignPrdController::class, 'canPassActiveValidation']);

    // 行銷活動
    Route::post('/promotional_campaign/ajax/products', [PromotionalCampaignController::class, 'getProducts']);
    Route::post('/promotional_campaign/ajax/detail', [PromotionalCampaignController::class, 'getDetail']);

    // 訂單管理
    Route::get('/order/ajax/excel', [OrderController::class, 'exportOrderExcel']);
    Route::resource('/order', OrderController::class, [
        'names' => [
            'index' => 'order',
        ],
    ]);

    // 出貨單管理
    Route::resource('/shipment', ShipmentController::class, [
        'names' => [
            'index' => 'shipment',
        ],
    ]);

    // 商城頁面內容管理
    Route::resource('/webcontents', WebContentsController::class, ['names' => ['index' => 'webcontents']]);

    // 買斷商品對帳單
    Route::resource('/buyout_products_report', BuyoutProductsReportController::class, ['names' => ['index' => 'buyout_products_report']]);

    // 進銷存彙總表
    Route::post('/summary_stock/ajax', [SummaryStockController::class, 'ajaxDetail']);
    Route::resource('/summary_stock', SummaryStockController::class, ['names' => ['index' => 'summary_stock']]);
});

Route::get('/', [LoginAuthController::class, 'index'])->name('login');
Route::post('custom-login', [LoginAuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [LoginAuthController::class, 'customRegistration'])->name('register.custom');
