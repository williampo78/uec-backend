<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Services\BrandsService;
use App\Services\CategoriesSerivce;
use App\Services\OrderSupplierService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    private $productService;
    private $supplierService;
    private $brandsService;
    private $webCategoryHierarchyService;
    private $categoriesSerivce;
    private $orderSupplierService;

    public function __construct(
        ProductService $productService,
        SupplierService $supplierService,
        BrandsService $brandsService,
        WebCategoryHierarchyService $webCategoryHierarchyService,
        CategoriesSerivce $categoriesSerivce,
        OrderSupplierService $orderSupplierService
    ) {
        $this->productService = $productService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->categoriesSerivce = $categoriesSerivce;
        $this->orderSupplierService = $orderSupplierService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in = $request->input();
        // dd(URL::current() ,  ,) ;
        $result = [
            'products' => [],
        ];

        if (count($in) !== 0) {
            $result['products'] = $this->productService->getProducts($in);
            $this->productService->restructureProducts($result['products']);

        }
        $q = empty($in) ? '?' : '&';
        $result['excel_url'] = url("/") . $request->getRequestUri() . $q . 'export=true';
        if (isset($in['export'])) { //匯出報表
            return $this->export($in);
        }
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents(); //供應商

        return view('backend.products.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [];
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands();
        $result['pos'] = $this->categoriesSerivce->getPosCategories();
        return view('backend.products.input', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [];
        $execution = $this->productService->addProducts($request->input(), $request->file());
        $result['status'] = $execution['status'];
        $result['route_name'] = 'products';
        $result['act'] = 'add';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            $result['message'] = '新增時發生未預期的錯誤';
            if (isset($execution['error_code'])) {
                $result['error_code'] = $execution['error_code'];
            };
            return view('backend.error', $result);
        };
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = [];
        $result['products'] = $this->productService->showProducts($id);
        $result['product_audit_log'] = $this->productService->getProductAuditLog($id);
        $result['products_item'] = $this->productService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['pos'] = $this->categoriesSerivce->getPosCategories();
        $result['product_photos'] = $this->productService->getProductsPhoto($id);
        $result['spac_list'] = $this->productService->getProductSpac($id);
        $result['finallyOrderSupplier'] = $this->orderSupplierService->getFinallyOrderSupplier($id);
        // dump($result['spac_list']) ; exit ;
        return view('backend.products.show', $result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = [];
        $products = $this->productService->showProducts($id);
        $products->launched_status = '';
        $products->launched_at = ($products->start_launched_at || $products->end_launched_at) ? "{$products->start_launched_at} ~ {$products->end_launched_at}" : '';
        switch ($products->approval_status) {
            // 未設定
            case 'NA':
                $products->edit_readonly = '0';
                break;

            // 上架申請
            case 'REVIEWING':
                $products->edit_readonly = '1';
                break;

            // 上架駁回
            case 'REJECTED':
                $products->edit_readonly = '0';
                break;

            // 商品下架
            case 'CANCELLED':
                $products->edit_readonly = '0';
                break;

            case 'APPROVED':
                // 在時間範圍內: 商品上架，其他: 商品下架
                $products->edit_readonly = Carbon::now()->between($products->start_launched_at, $products->end_launched_at) ? '1' : '0';
                break;
        }
        $result['products'] = $products;
        $result['product_audit_log'] = $this->productService->getProductAuditLog($id);
        $result['products_item'] = $this->productService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['pos'] = $this->categoriesSerivce->getPosCategories();
        $result['product_photos'] = $this->productService->getProductsPhoto($id);
        $result['spac_list'] = $this->productService->getProductSpac($id);
        $result['product_spec_info'] = $this->productService->getProduct_spec_info($id);
        $result['finallyOrderSupplier'] = $this->orderSupplierService->getFinallyOrderSupplier($id);

        return view('backend.products.update', $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = [];
        $execution = $this->productService->editProducts($request->input(), $request->file());
        $result['status'] = $execution['status'];
        $result['route_name'] = 'products';
        $result['act'] = 'upd';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            $result['message'] = '新增時發生未預期的錯誤';
            if (isset($execution['error_code'])) {
                $result['error_code'] = $execution['error_code'];
            };
            return view('backend.error', $result);
        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function ajax(Request $request)
    {
        $in = $request->input();
        switch ($in['type']) {
            case 'checkPosItemNo':
                if ($in['pos_item_no'] !== '') {
                    $result = $this->productService->checkPosItemNo($in['pos_item_no'], $in['item_no']);
                } else {
                    $result = false;
                }
                break;
            default:
                break;
        }

        return response()->json([
            'requestData' => $in,
            'result' => $result,
        ]);
    }
    public function export($in)
    {
        $data = $this->productService->getItemsJoinProducts($in);
        $pos = $this->categoriesSerivce->getPosCategories()->keyBy('id');
        $this->productService->restructureItemsProducts($data, $pos);
        $title = [
            "stock_type_cn" => "庫存類型",
            "product_no" => "商品序號",
            "supplier_name" => "供應商",
            "product_name" => "商品名稱",
            "tax_type_cn" => "課稅別",
            "primary_category" => "POS大分類",
            "category_name" => "POS中分類",
            "tertiary_categories_name" => "POS小分類",
            "brand_name" => "品牌",
            "model" => "商品型號",
            "selling_channel_cn" => "商品通路", //商品通路
            "lgst_temperature_cn" => "溫層",
            "lgst_method_cn" => "配送方式",
            "delivery_type_cn" => "商品交期",
            "has_expiry_date_cn" => "效期控管",
            "expiry_days" => "效期天數",
            "product_type_cn" => "商品類型",
            "is_discontinued_cn" => "停售",
            "length" => "材積-長(公分)",
            "width" => "材積-寬(公分)",
            "height" => "材積-高(公分)",
            "weight" => "重量(公克)",
            "list_price" => "市價(含稅)",
            "selling_price" => "售價(含稅)",
            "item_cost" => "成本(含稅)", // products_v
            "gross_margin" => "毛利(%)", // products_v
            "patent_no" => "專利字號",
            "is_with_warranty_cn" => "是否保固",
            "warranty_days" => "保固期限(天)",
            "warranty_scope" => "保固範圍",
            "web_category_products_category_name" => "前台分類", //需要對多拿單欄位逗號分隔
            "keywords" => "關聯關鍵字",
            "related_product_name" => "關聯性商品", //需要對多拿單欄位逗號分隔
            "order_limited_qty" => "每單限購數量", //每單限購數量
            "promotion_desc" => "促銷小標",
            "promotion_start_at" => "促銷小標生效時間起",
            "promotion_end_at" => "促銷小標生效時間訖",
            "start_launched_at" => "上架時間起",
            "end_launched_at" => "上架時間訖",
            "launched_status" => "上架狀態",
            "spec_dimension_cn" => "規格類型",
            "spec_1_value" => "規格一",
            "spec_2_value" => "規格二",
            "item_no" => "Item編號",
            "supplier_item_no" => "廠商貨號",
            "ean" => "國際條碼",
            "pos_item_no" => "POS品號",
            "safty_qty" => "安全庫存量",
            "is_additional_purchase_cn" => "是否追加",
            "status_cn" => "狀態",
        ];
        $export = new ReportExport($title, $data->toArray());
        return Excel::download($export, '商品主檔' . date('Y-m-d') . '.xlsx');
    }

    /**
     * 取得商品modal下拉選項
     *
     * @return void
     */
    public function getModalOptions()
    {
        // 供應商
        $result['suppliers'] = $this->supplierService->getSuppliers();
        // 商品類型
        $result['product_type_options'] = config('uec.product_type_options');
        // 前台分類
        $result['web_category_hierarchies'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents();

        return response()->json($result);
    }

    /**
     * 取得modal的商品
     *
     * @param Request $request
     * @return void
     */
    public function getModalProducts(Request $request)
    {
        $queryData = $request->only([
            'supplier_id',
            'product_no',
            'product_name',
            'selling_price_min',
            'selling_price_max',
            'created_at_start',
            'created_at_end',
            'start_launched_at_start',
            'start_launched_at_end',
            'product_type',
            'web_category_hierarchy_id',
            'limit',
            'stock_types',
            'exclude_product_ids',
        ]);

        $products = $this->productService->getModalProducts($queryData);
        $products = $this->productService->formatModalProducts($products);

        return response()->json($products);
    }
}