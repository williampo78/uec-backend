<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\SupplierService ; 
use App\Services\BrandsService ; 
use ImageUpload ;

class ProductsController extends Controller
{
    private $productsService;
    public function __construct(ProductsService $productsService,
    SupplierService $supplierService , 
    BrandsService $brandsService)
    {
        $this->productsService = $productsService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService ; 
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
    
        // $result = $this->productsService->get_Products($request) ; 

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
        dd($request->input()) ; 

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
