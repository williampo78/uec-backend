<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\SupplierService ;
use App\Services\BrandsService ;
use App\Services\WebCategoryHierarchyService ;

class ProductsController extends Controller
{
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

        if(count($in) !== 0 ){
            $result['products'] = $this->productsService->getProducts($in) ;
        }
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();//供應商

        return view('Backend.Products.list',$result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [] ;
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ;
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();
        return view('Backend.Products.input',$result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dump($request->input() , $request->file()) ;
        $this->productsService->addProducts($request->input(), $request->file()) ;
        $act = 'add';
        $route_name = 'products';
        return view('Backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = [] ; 
        $result['products'] = $this->productsService->showProducts($id) ; 
        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ; // 廠牌
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();//前台分類
        $result['product_photos'] = $this->productsService->getProductsPhoto($id) ; 
        $result['spac_list'] = $this->productsService->getProductSpac($id) ; 
        // dump($result['spac_list']) ; exit ;
        return view('Backend.Products.show',$result) ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = [] ; 
        $result['products'] = $this->productsService->showProducts($id) ; 
        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ; // 廠牌
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();//前台分類
        $result['product_photos'] = $this->productsService->getProductsPhoto($id) ; 
        $result['spac_list'] = $this->productsService->getProductSpac($id) ; 
        $result['product_spec_info'] = $this->productsService->getProduct_spec_info($id) ; 
        return view('Backend.Products.update',$result) ;
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
        $this->productsService->editProducts($request->input(), $request->file()) ;
        $act = 'upd';
        $route_name = 'products';
        return view('Backend.success', compact('route_name', 'act'));
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
