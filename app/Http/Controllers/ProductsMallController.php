<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\BrandsService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\ProductAttributesService;
use App\Services\ProductAttributeLovService;
use App\Services\WebCategoryHierarchyService;

class ProductsMallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $productService;
    public function __construct(ProductService $productService,
        SupplierService $supplierService,
        BrandsService $brandsService,
        WebCategoryHierarchyService $webCategoryHierarchyService,
        ProductAttributeLovService $productAttributeLovService,
        ProductAttributesService $productAttributesService) {
        $this->productService = $productService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->productAttributeLovService = $productAttributeLovService;
        $this->productAttributesService = $productAttributesService;
    }

    public function index(Request $request)
    {
        $in = $request->input();

        $result = [
            'products' => [],
        ];
        if (count($in) > 1) {
            $result['products'] = $this->productService->getProducts($in);
            $this->productService->restructureProducts($result['products']);
        }

        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents(); //供應商

        return view('backend.products_mall.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('backend.products_mall.input');
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
        $result = [];
        $result['products'] =  $this->arrangeProduct($this->productService->showProducts($id));
        $result['products_item'] = $this->productService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents();
        $result['web_category_hierarchy'] = $this->webCategoryHierarchyService->categoryProductsId($id); //前台分類
        $result['product_photos'] = $this->productService->getProductsPhoto($id);
        $result['spac_list'] = $this->productService->getProductSpac($id);
        $result['product_spec_info'] = $this->productService->getProduct_spec_info($id);
        $result['related_products'] = $this->productService->getRelatedProducts($id);
        return view('backend.products_mall.show', $result);
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
        $result['products'] = $this->arrangeProduct($this->productService->showProducts($id)) ;
        $result['products_item'] = $this->productService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->getCategoryHierarchyContents(array("exclude_content_type"=>"'M'"));
        $result['web_category_hierarchy'] = $this->webCategoryHierarchyService->categoryProductsId($id); //前台分類
        $result['product_photos'] = $this->productService->getProductsPhoto($id);
        $result['product_photos_count'] =  $result['product_photos']->count();
        $result['spac_list'] = $this->productService->getProductSpac($id);
        $result['product_spec_info'] = $this->productService->getProduct_spec_info($id);
        $result['related_products'] = $this->productService->getRelatedProducts($id);
        $result['product_attribute_lov'] = $this->productAttributeLovService->assembleAttributeLov(); //取checkbox 設定
        $result['product_attributes'] = $this->productAttributesService->getProductAttributes([
            'product_id' => $id,
            'attribute_type' => 'CERTIFICATE',
        ])->keyBy('product_attribute_lov_id')
        ->toArray();
        return view('backend.products_mall.input', $result);
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
        $result = [] ;
        $in = $request->input();
        $file = $request->file();
        $execution =  $this->productService->updateProductSmall($in, $file, $id);
        $result['status'] = $execution['status'] ;
        $result['route_name'] = 'products_mall';
        $result['act'] = 'upd';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            $result['message']  = '編輯時發生未預期的錯誤';
            if(isset($execution['error_code'])){
                $result['error_code'] = $execution['error_code'] ;
            };
            return view('backend.error', $result);
        };
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
    public function ajax(Request $request)
    {
        $in = $request->input();
        $status = true;
        switch ($in['type']) {
            case 'DelCategoryInProduct':
                try {
                    $this->webCategoryHierarchyService->DelCategoryInProduct($in);
                } catch (\Throwable $th) {
                    $status = false;
                }
                break;
            case 'DelRelatedProducts':
                try {
                    $this->webCategoryHierarchyService->DelRelatedProducts($in['id']);
                } catch (\Throwable $th) {
                    $status = false;
                }
                break;
            case 'DelGoogleShopPhoto':
                try {
                    $this->productService->delGoogleShopPhoto($in['id']);
                } catch (\Throwable $th) {
                    $status = false;
                }
                break;
            case 'DelItemPhotos':
                // try {
                $this->productService->delItemPhotos($in['item_id']);
                // } catch (\Throwable $th) {
                //     $status = false;
                // }
                break;
            default:
                break;
        }

        return response()->json([
            'status' => $status,
            'in' => $request->input(),
        ]);
    }
    public function arrangeProduct($products){
        $products->launched_status = '' ;
        $products->launched_at = ($products->start_launched_at || $products->end_launched_at) ? "{$products->start_launched_at} ~ {$products->end_launched_at}" : '';
        switch ($products->approval_status) {
            case 'NA':
                $products->edit_readonly = '0' ;
                break;

            case 'REVIEWING':
                $products->edit_readonly = '1' ;
                break;

            case 'REJECTED':
                $products->edit_readonly = '0' ;
                break;

            case 'CANCELLED':
                $products->edit_readonly = '0' ;
                break;
            case 'APPROVED':
                $products->edit_readonly = Carbon::now()->between($products->start_launched_at, $products->end_launched_at) ? '1' : '0';
                break;
        }

        if($products->meta_title == null){
            $products->meta_title = $products->product_name ;
        }
        if($products->mata_description == null){
            $products->mata_description = $products->product_brief_1 . $products->product_brief_2 . $products->product_brief_3;
        }
        if($products->mata_keywords == null){
            $cp = $this->webCategoryHierarchyService->categoryProductsId($products->id) ;
            if($cp->count() > 0){
                $cpName = $this->webCategoryHierarchyService->getCategoryHierarchyContents(['id' => $cp[0]->web_category_hierarchy_id]);
                if(count($cpName) > 0){
                    $products->mata_keywords = $cpName[0]->name ;
                }
            }
        }
        return $products ;
    }
}
