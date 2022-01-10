<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\SupplierService ;
use App\Services\BrandsService ;
use App\Services\WebCategoryHierarchyService ;
use App\Services\CategoriesSerivce ;
class ProductsController extends Controller
{
    private $productsService;
    public function __construct(ProductsService $productsService,
    SupplierService $supplierService ,
    BrandsService $brandsService,
    WebCategoryHierarchyService $webCategoryHierarchyService,
    CategoriesSerivce $categoriesSerivce)
    {
        $this->productsService = $productsService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService ;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService ;
        $this->categoriesSerivce = $categoriesSerivce ; 
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
            $this->productsService->restructureProducts($result['products']);
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
        $result['pos'] =  $this->categoriesSerivce->getPosCategories();
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
        $result['pos'] =  $this->categoriesSerivce->getPosCategories();
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
        $result['product_audit_log'] = $this->productsService->getProductAuditLog($id) ; 
        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ; // 廠牌
        $result['pos'] =  $this->categoriesSerivce->getPosCategories();
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
        $result = $this->productsService->editProducts($request->input(), $request->file()) ;
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
    public function ajax(Request $request){
        $in = $request->input();
        switch ($in['type']) {
            case 'DelCategoryInProduct': 
                break;
            default:
                break;
        }

        
        return response()->json([
            'in' => $request->input(),
        ]);
    }

}
