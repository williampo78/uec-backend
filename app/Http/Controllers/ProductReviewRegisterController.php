<?php

namespace App\Http\Controllers;

use App\Services\BrandsService;
use App\Services\ProductsService;
use App\Services\SupplierService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;

class ProductReviewRegisterController extends Controller
{
    public function __construct(ProductsService $productsService,
        SupplierService $supplierService,
        BrandsService $brandsService,
        WebCategoryHierarchyService $webCategoryHierarchyService) {
        $this->productsService = $productsService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in = $request->input();

        $result = [
            'products' => [],
        ];

        if (count($in) !== 0) {
            $result['products'] = $this->productsService->getProducts($in);
            $this->productsService->restructureProducts($result['products']);
        }
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content(); //供應商
        return view('backend.product_review_register.list', $result);
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
        $result['products'] = $this->productsService->showProducts($id);
        $result['product_review_log'] = $this->productsService->getProductReviewLog($id);
        return view('backend.product_review_register.show', $result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $result['products'] = $this->productsService->showProducts($id);
        $result['product_review_log'] = $this->productsService->getProductReviewLog($id);
        return view('backend.product_review_register.input', $result);
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
        $in = $request->input();
        $result['status'] = $this->productsService->addProductReviewLog($in, $id);
        $act = 'product_reviewing';
        $route_name = 'product_review_register';
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
    public function ajax(Request $request)
    {
        $in = $request->input();
        $status = true;

        switch ($in['type']) {
            case 'offProduct':
                $status = $this->productsService->offProduct($in);
                break;
            case 'checkProductReady':
                if($in['stock_type'] == 'N'){
                    $status = $this->productsService->checkProductReady($in) ;
                }else{
                    $status = true ; 
                }
                break;
            default:
                break;
        }

        return response()->json([
            'status' => $status,
            'in' => $request->input(),
        ]);
    }
}
