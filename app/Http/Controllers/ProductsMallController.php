<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BrandsService ;
use App\Services\ProductsService;
use App\Services\SupplierService ;
use Illuminate\Support\Facades\Log;
use App\Services\WebCategoryHierarchyService ;

class ProductsMallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $productsService;
    public function __construct(ProductsService $productsService,
    SupplierService $supplierService ,
    BrandsService $brandsService,
    WebCategoryHierarchyService $webCategoryHierarchyService)
    {
        $this->productsService = $productsService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService ;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService ;
    }

    public function index(Request $request)
    {
        $in = $request->input() ;

        $result = [
            'products' => [],
        ] ;

        if(count($in) !== 0 ){
            $result['products'] = $this->productsService->getProducts($in) ;
        }
    
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();//供應商

        return view('Backend.ProductsMall.list',$result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('Backend.ProductsMall.input');
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
        //
        $result = [] ; 
        $result['products'] = $this->productsService->showProducts($id) ; 
        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ; // 廠牌
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content();
        $result['web_category_hierarchy'] = $this->webCategoryHierarchyService->categoryProductsId($id);//前台分類
        $result['product_photos'] = $this->productsService->getProductsPhoto($id) ; 
        $result['spac_list'] = $this->productsService->getProductSpac($id) ; 
        $result['product_spec_info'] = $this->productsService->getProduct_spec_info($id) ; 
        return view('Backend.ProductsMall.input',$result) ;
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
        //
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
    public function ajax(Request $request){
        $in = $request->input();
        $status = true ; 
        switch ($in['type']) {
            case 'DelCategoryInProduct': 
                try {
                    $this->webCategoryHierarchyService->DelCategoryInProduct($in) ; 
                } catch (\Throwable $th) {
                    $status = false ; 
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
