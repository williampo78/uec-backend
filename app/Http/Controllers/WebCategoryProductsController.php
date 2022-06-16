<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;

class WebCategoryProductsController extends Controller
{
    private $webCategoryHierarchyService;
    private $supplierService;
    private $productService;

    public function __construct(
        WebCategoryHierarchyService $webCategoryHierarchyService,
        SupplierService $supplierService,
        ProductService $productService
    ) {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->supplierService = $supplierService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request = $request->input();
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents($request);
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
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents($in)[0];
        //原生sql不加入RomotionalCampaigns join - 另外撈取 活動名稱
        if ($result['category_hierarchy_content']->promotion_campaign_id !== null) {
            $getRomotionalCampaigns = $this->webCategoryHierarchyService->getRomotionalCampaigns(['id' => $result['category_hierarchy_content']->promotion_campaign_id])[0] ?? null;
            $result['category_hierarchy_content']->campaign_brief = $getRomotionalCampaigns->campaign_brief;
        } else {
            $result['category_hierarchy_content']->campaign_brief = '';
        }

        // 網頁標題為空值時，預設為分類名稱的最小階層名稱
        if (empty($result['category_hierarchy_content']->meta_title)) {
            $split_names = explode('>', $result['category_hierarchy_content']->name);
            $result['category_hierarchy_content']->meta_title = trim(end($split_names));
        }

        $result['category_products_list'] = $this->webCategoryHierarchyService->categoryProductsHierarchyId($id);
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
    public function ajax(Request $request)
    {
        $in = $request->input();
        $result = [];
        switch ($in['type']) {
            case 'getProductsList':
                $result['data'] = $this->productService->getProducts($in);
                $this->productService->restructureProducts($result['data']);
                foreach ($result['data'] as $key => $val) {
                    $result['data'][$key]->check_use = 0;
                };
                break;
            case 'DelProductsList':
                $result['data'] = $this->webCategoryHierarchyService->del_category_hierarchy_content($in['id']);
                break;
            case 'show_category_products':
                $result['data']['category_hierarchy_content'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents($in)[0];
                $result['data']['category_products_list'] = $this->webCategoryHierarchyService->categoryProductsHierarchyId($in['id']);
                break;
            case 'promotionalCampaignsGetAjax':
                $result['data'] = $this->webCategoryHierarchyService->getRomotionalCampaigns($in);
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
