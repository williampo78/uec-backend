<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebCategoryHierarchyService;
class WebCategoryProductsControllers extends Controller
{
    private $webCategoryHierarchyService;
    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService)
    {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result['category_products_list'] = $this->webCategoryHierarchyService->web_category_products() ; 
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
        $result['category_products'] = $this->webCategoryHierarchyService->web_category_products($in) ; 
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
