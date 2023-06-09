<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertisementBlockController;
use App\Http\Controllers\AdvertisementLaunchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyoutProductsReportController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExternalInventoryDailyReportController;
use App\Http\Controllers\InstallmentInterestRateController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockTransactionLogController;
use App\Http\Controllers\MiscStockRequestController;
use App\Http\Controllers\MiscStockRequestReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPaymentsReportController;
use App\Http\Controllers\OrderRefundController;
use App\Http\Controllers\OrderSupplierController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\PrimaryCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductReviewRegisterController;
use App\Http\Controllers\ProductsMallController;
use App\Http\Controllers\PromotionalCampaignCartController;
use App\Http\Controllers\PromotionalCampaignCartV2Controller;
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
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\DownloadSampleController;
use App\Models\Product;
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

Route::get('/', function () {
    return redirect()->route('backend_home');
});

// 未登入的user才能造訪
Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', [AuthController::class, 'showLoginPage'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::any('/ckfinder/connector', [CKFinderController::class, 'requestAction'])->name('ckfinder_connector');

//驗證碼
Route::get('/refresh-captcha', [CaptchaController::class, 'refreshCaptcha'])->name('captcha.refresh');

// 已登入的user才能造訪
Route::group(['prefix' => 'backend', 'middleware' => ['admin','nocache']], function () {
    Route::resource('/', AdminController::class, ['names' => ['index' => 'backend_home']]);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // 倉庫管理
    Route::resource('/warehouse', WarehouseController::class, ['names' => ['index' => 'warehouse']]);

    // 供應商類別管理
    Route::resource('/supplier_type', SupplierTypeController::class, ['names' => ['index' => 'supplier_type']]);

    // 供應商主檔管理
    Route::post('/supplier/display-number-exists', [SupplierController::class, 'displayNumberExists']);
    Route::resource('/supplier', SupplierController::class, ['names' => ['index' => 'supplier']]);

    // 商品主檔 - 基本資訊管理
    Route::post('/products/ajax', [ProductController::class, 'ajax']);
    Route::post('/products/export', [ProductController::class, 'export']);
    Route::resource('/products', ProductController::class, ['names' => ['index' => 'products']]);

    // 商品主檔 - 批次上傳

    Route::get('/product-batch-upload/download/{id}', [ProductBatchController::class, 'download'])->name("product-batch-upload.download");
    Route::resource('/product-batch-upload',ProductBatchController::class , ['names' => ['index' => 'product-batch-upload']]);
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
        //更新協商退貨回報
        Route::put('/ajax/update-negotiated-return', [OrderRefundController::class, 'updateNegotiatedReturn'])->name('order_refund.update_negotiated_return');
        //人工退款
        Route::put('/ajax/update-manual-refund', [OrderRefundController::class, 'updateManualRefund'])->name('order_refund.update_manual_refund');
        //作廢
        Route::put('/ajax/void-return-examination', [OrderRefundController::class, 'voidReturnExamination'])->name('order_refund.void_return_examination');
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
    Route::resource('/users', UserController::class, ['names' => ['index' => 'users']]);
    // 驗證使用者帳號是否重複
    Route::post('/users/ajax/is-user-account-repeat', [UserController::class, 'isUserAccountRepeat']);
    Route::get('/user_profile', [UserController::class, 'profile']);
    Route::put('/user_profile', [UserController::class, 'updateProfile'])->name('user_profile.update');

    // 常見問題Q&A
    Route::resource('/qa', QAController::class, ['names' => ['index' => 'qa']]);

    // 分類階層管理
    Route::get('/web_category_hierarchy/categories', [WebCategoryHierarchyController::class, 'getCategories']);
    Route::put('/web_category_hierarchy/sort', [WebCategoryHierarchyController::class, 'sortCategories']);
    Route::resource('/web_category_hierarchy', WebCategoryHierarchyController::class, [
        'names' => [
            'index' => 'web_category_hierarchy',
        ],
    ]);

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
    Route::post('/advertisemsement_launch/ajax/search-promotion-campaign', [AdvertisementLaunchController::class, 'searchPromotionCampaign']);
    Route::resource('/advertisemsement_launch', AdvertisementLaunchController::class, [
        'names' => [
            'index' => 'advertisemsement_launch',
        ],
    ]);

    // 滿額活動
    Route::get('/promotional-campaign-cart/product-modal/options', [PromotionalCampaignCartController::class, 'getProductModalOptions']);
    Route::get('/promotional-campaign-cart/product-modal/products', [PromotionalCampaignCartController::class, 'getProductModalProducts']);
    Route::post('/promotional-campaign-cart/can-active', [PromotionalCampaignCartController::class, 'canActive']);
    Route::resource('/promotional-campaign-cart', PromotionalCampaignCartController::class, [
        'names' => [
            'index' => 'promotional_campaign_cart',
            'create' => 'promotional_campaign_cart.create',
            'store' => 'promotional_campaign_cart.store',
            'edit' => 'promotional_campaign_cart.edit',
            'update' => 'promotional_campaign_cart.update',
            'show' => 'promotional_campaign_cart.show',
        ],
    ]);

    // 新版滿額活動
    Route::get('/promotional-campaign-cart-v2/product-modal/options', [PromotionalCampaignCartV2Controller::class, 'getProductModalOptions']);
    Route::post('/promotional-campaign-cart-v2/product-modal/products', [PromotionalCampaignCartV2Controller::class, 'getProductModalProducts']);
    Route::post('/promotional-campaign-cart-v2/can-active', [PromotionalCampaignCartV2Controller::class, 'canActive']);
    Route::resource('/promotional-campaign-cart-v2', PromotionalCampaignCartV2Controller::class, [
        'names' => [
            'index' => 'promotional_campaign_cart_v2',
            'create' => 'promotional_campaign_cart_v2.create',
            'store' => 'promotional_campaign_cart_v2.store',
            'edit' => 'promotional_campaign_cart_v2.edit',
            'update' => 'promotional_campaign_cart_v2.update',
            'show' => 'promotional_campaign_cart_v2.show',
        ],
    ]);

    // 單品活動
    Route::get('/promotional-campaign-prd/product-modal/options', [PromotionalCampaignPrdController::class, 'getProductModalOptions']);
    Route::get('/promotional-campaign-prd/product-modal/products', [PromotionalCampaignPrdController::class, 'getProductModalProducts']);
    Route::post('/promotional-campaign-prd/can-active', [PromotionalCampaignPrdController::class, 'canActive']);
    Route::resource('/promotional-campaign-prd', PromotionalCampaignPrdController::class, [
        'names' => [
            'index' => 'promotional_campaign_prd',
            'create' => 'promotional_campaign_prd.create',
            'store' => 'promotional_campaign_prd.store',
            'edit' => 'promotional_campaign_prd.edit',
            'update' => 'promotional_campaign_prd.update',
            'show' => 'promotional_campaign_prd.show',
        ],
    ]);

    // 訂單管理
    Route::get('/order/excel', [OrderController::class, 'exportExcel']);
    Route::resource('/order', OrderController::class, [
        'names' => [
            'index' => 'order',
        ],
    ]);

    // 出貨單管理
    Route::get('/shipment/{shipment}/progress-logs', [ShipmentController::class, 'getProgressLogs']);
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

    //信用卡分期設定
    Route::group(['prefix' => 'installment-interest-rates', 'as' => 'installment_interest_rates'], function () {
        Route::get('/check-existed', [InstallmentInterestRateController::class, 'checkExisted'])->name('.check_existed');
        Route::get('/', [InstallmentInterestRateController::class, 'index']);
        Route::post('/', [InstallmentInterestRateController::class, 'store'])->name('.store');
        Route::get('/{id}', [InstallmentInterestRateController::class, 'show'])->name('.show');
        Route::put('/{id}', [InstallmentInterestRateController::class, 'update'])->name('.update');
    });

    // 進貨退出單
    Route::group(['prefix' => 'misc-stock-requests'], function () {
        Route::get('/product-item-modal/options', [MiscStockRequestController::class, 'getProductItemModalOptions']);
        Route::get('/product-item-modal/list', [MiscStockRequestController::class, 'getProductItemModalList']);
        Route::get('/{id}/supplier-modal/suppliers', [MiscStockRequestController::class, 'getSupplierModalList']);
        Route::get('/{request_id}/supplier-modal/suppliers/{supplier_id}', [MiscStockRequestController::class, 'getSupplierModalDetail']);
        Route::patch('/{id}/expected-date', [MiscStockRequestController::class, 'updateExpectedDate']);
    });
    Route::resource('/misc-stock-requests', MiscStockRequestController::class, [
        'names' => [
            'index' => 'misc_stock_requests',
            'create' => 'misc_stock_requests.create',
            'store' => 'misc_stock_requests.store',
            'edit' => 'misc_stock_requests.edit',
            'update' => 'misc_stock_requests.update',
            'show' => 'misc_stock_requests.show',
            'destroy' => 'misc_stock_requests.destroy',
        ],
    ]);

    // 進貨退出單審核
    Route::get('/misc-stock-request-reviews/{request_id}/review-modal/suppliers/{supplier_id}', [MiscStockRequestReviewController::class, 'getReviewModalDetail']);
    Route::resource('/misc-stock-request-reviews', MiscStockRequestReviewController::class, [
        'names' => [
            'index' => 'misc_stock_request_reviews',
            'edit' => 'misc_stock_request_reviews.edit',
            'update' => 'misc_stock_request_reviews.update',
        ],
    ]);

    //庫存異動歷程查詢
    Route::group(['prefix' => 'stock-transaction-logs', 'as' => 'backend_stock_transaction_logs'], function () {
        Route::get('/', [StockTransactionLogController::class, 'index']);
    });
    Route::get('/download-sample/{fileName}', [DownloadSampleController::class, 'index']);

});
