<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;

class WebCategoryProductsControllers extends Controller
{
    private $webCategoryHierarchyService;
    private $supplierService;
    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService,
        SupplierService $supplierService) {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->supplierService = $supplierService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request = $request->input();
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($request);
        // dd($result) ;
        return view('Backend.WebCategoryProducts.list', $result);
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
        $result = [];
        $in = [];
        $in['id'] = $id;
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($in)[0];
        $result['category_products_list'] = $this->webCategoryHierarchyService->category_products($id);
        $result['supplier'] = $this->supplierService->getSupplier();
        return view('Backend.WebCategoryProducts.input', $result);
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
        $in = $request->input();
        $result = $this->webCategoryHierarchyService->edit_category_hierarchy_content($in, $id);
        $route_name = 'web_category_products';
        $act = 'add';
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
    public function GetCategory()
    {

    }
    public function Ajax(Request $request)
    {
        $in = $request->input();
        $result = [];
        switch ($in['type']) {
            case 'getProductsList':
                $result['data'] = $this->webCategoryHierarchyService->get_products_v($in);
                foreach ($result['data'] as $key => $val) {
                    $result['data'][$key]->check_use = 0;
                };
                break;
            case 'DelProductsList':
                $result['data'] = $this->webCategoryHierarchyService->del_category_hierarchy_content($in['id']);
                break;
            case 'show_category_products':
                $result['data']['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($in)[0];
                $result['data']['category_products_list'] = $this->webCategoryHierarchyService->category_products($in['id']);
                break;
            default:
                # code...
                break;
        }

        return response()->json([
            'status' => true,
            'in' => $request->input(),
            'result' => $result,
        ]);
    }

}
