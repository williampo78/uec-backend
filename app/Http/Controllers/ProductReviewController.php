<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\SupplierService ;
use App\Services\BrandsService ;
use App\Services\WebCategoryHierarchyService ;

class ProductReviewController extends Controller
{
    public function __construct(ProductService $productService,
    SupplierService $supplierService ,
    BrandsService $brandsService,
    WebCategoryHierarchyService $webCategoryHierarchyService)
    {
        $this->productService = $productService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService ;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in = $request->input() ;

        $result = [
            'products' => [],
        ] ;
        $in = array_merge($in , ['approval_status' => 'REVIEWING']) ;  // 固定撈出未審核狀態
        $result['products'] = $this->productService->getProducts($in) ;
        $this->productService->restructureProducts($result['products']);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents();//供應商
        return view('backend.product_review.list',$result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $products = $this->productService->showProducts($id);
        switch ($products->product_type) {
            case 'N':
                $products->product_type_cn = '一般品';
                break;

            case 'G':
                $products->product_type_cn = '贈品';
                break;

            case 'A':
                $products->product_type_cn = '加購品';
                break;
        }
        $result['products']  = $products;
        $result['product_review_log'] = $this->productService->getProductReviewLog($id);
        //付款方式
        $result['payment_method_options'] = config('uec.payment_method_options');
        $result['payment_method_options_lock'] = config('uec.payment_method_options_lock');
        $result['payment_method'] = $products->payment_method ? explode(',',$products->payment_method) : []; //付款方式

        return view('backend.product_review.input', $result);
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
        $result = [] ;
        $in = $request->input();
        $result['status'] = $this->productService->addProductReview($in , $id) ;
        $act = 'review_success';
        $route_name = 'product_review';
        return view('backend.success', compact('route_name', 'act'));
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
}
