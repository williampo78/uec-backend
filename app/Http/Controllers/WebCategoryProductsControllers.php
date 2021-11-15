<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebCategoryHierarchyService;
use App\Services\SupplierService;

class WebCategoryProductsControllers extends Controller
{
    private $webCategoryHierarchyService;
    private $supplierService ;
    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService,
    SupplierService $supplierService)
    {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->supplierService = $supplierService ; 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request = $request->input() ; 
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($request) ; 
        // dd($result) ; 
        return view('Backend.WebCategoryProducts.list',$result) ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.WebCategoryProducts.input');
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
        $result = [] ; 
        $in = [] ; 
        $in['id'] = $id ; 
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($in)[0] ; 
        $result['category_products_list']  =  $this->webCategoryHierarchyService->category_products($id) ; 
        $result['supplier'] = $this->supplierService->getSupplier() ;
        return view('Backend.WebCategoryProducts.input',$result) ;
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
    public function GetCategory()
    {

    }

}
