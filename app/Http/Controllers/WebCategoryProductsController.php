<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Services\ProductsService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;

class WebCategoryProductsController extends Controller
{
    private $webCategoryHierarchyService;
    private $supplierService;
    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService,
        SupplierService $supplierService,
        ProductsService $productsService) {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->supplierService = $supplierService;
        $this->productsService = $productsService;
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
        foreach ($result['category_hierarchy_content'] as $content) {
            $content->product_counts = $this->webCategoryHierarchyService->categoryProductsHierarchyId($content->id)->count();
        }

        return view('backend.web_category_products.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.web_category_products.input');
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
        $result['category_products_list'] = $this->webCategoryHierarchyService->categoryProductsHierarchyId($id);
        // dd($result) ;
        $result['supplier'] = $this->supplierService->getSuppliers();

        return view('backend.web_category_products.input', $result);
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
        $act = 'upd';
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
    public function GetCategory()
    {

    }
    public function Ajax(Request $request)
    {
        $in = $request->input();
        $result = [];
        switch ($in['type']) {
            case 'getProductsList':
                $result['data'] = $this->productsService->getProducts($in) ;
                $this->productsService->restructureProducts($result['data']);
                foreach ($result['data'] as $key => $val) {
                    $result['data'][$key]->check_use = 0;
                };
                break;
            case 'DelProductsList':
                $result['data'] = $this->webCategoryHierarchyService->del_category_hierarchy_content($in['id']);
                break;
            case 'show_category_products':
                $result['data']['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content($in)[0];
                $result['data']['category_products_list'] = $this->webCategoryHierarchyService->categoryProductsHierarchyId($in['id']);
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
