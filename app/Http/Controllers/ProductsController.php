<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\SupplierService ; 
use App\Services\BrandsService ; 
use App\Services\WebCategoryHierarchyService ; 
use ImageUpload ;

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
        
        $result = [] ; 
        
        $result['products'] = $this->productsService->getProducts($request) ; 
        // dd($result) ; 
        // dd($result) ;
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
        $result['supplier'] = $this->supplierService->getSupplier(); //供應商
        $result['brands'] = $this->brandsService->getBrands() ;
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content();
        // dd($result) ; 
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
    public function testview(){  
        $result = [] ; 
        $filename = '' ; 
        $result['web_url'] = ImageUpload::getImage($filename) ? '' : '' ; 
        return view('Backend.Products.test',$result);

    }
    public function upload_img(Request $request){
        $file = $request->file('photo') ; 
        $path = '/photo/1' ; 
        $upload = ImageUpload::uploadImage($file,$path) ; 
    }

}
