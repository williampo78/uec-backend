<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\BrandsService;
use App\Services\ProductsService;
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
    private $productsService;
    public function __construct(ProductsService $productsService,
        SupplierService $supplierService,
        BrandsService $brandsService,
        WebCategoryHierarchyService $webCategoryHierarchyService,
        ProductAttributeLovService $productAttributeLovService,
        ProductAttributesService $productAttributesService) {
        $this->productsService = $productsService;
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

        if (count($in) !== 0) {
            $result['products'] = $this->productsService->getProducts($in);
            $this->productsService->restructureProducts($result['products']);
        }

        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['pos'] = $this->webCategoryHierarchyService->category_hierarchy_content(); //供應商

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
        $result['products'] = $this->productsService->showProducts($id);
        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content();
        $result['web_category_hierarchy'] = $this->webCategoryHierarchyService->categoryProductsId($id); //前台分類
        $result['product_photos'] = $this->productsService->getProductsPhoto($id);
        $result['spac_list'] = $this->productsService->getProductSpac($id);
        $result['product_spec_info'] = $this->productsService->getProduct_spec_info($id);
        $result['related_products'] = $this->productsService->getRelatedProducts($id);
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
        $products = $this->productsService->showProducts($id) ;
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
        $result['products'] = $products ;

        $result['products_item'] = $this->productsService->getProductItems($id);
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        $result['brands'] = $this->brandsService->getBrands(); // 廠牌
        $result['category_hierarchy_content'] = $this->webCategoryHierarchyService->category_hierarchy_content();
        $result['web_category_hierarchy'] = $this->webCategoryHierarchyService->categoryProductsId($id); //前台分類
        $result['product_photos'] = $this->productsService->getProductsPhoto($id);
        $result['spac_list'] = $this->productsService->getProductSpac($id);
        $result['product_spec_info'] = $this->productsService->getProduct_spec_info($id);
        $result['related_products'] = $this->productsService->getRelatedProducts($id);
        $result['product_attribute_lov'] = $this->productAttributeLovService->getProductAttributeLov(['attribute_type' => 'CERTIFICATE']); //取checkbox 設定
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

        $result['status'] =  $this->productsService->updateProductSmall($in, $file, $id);
        $result['route_name'] = 'product_small';
        $result['act'] = 'upd';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            return view('backend.error', $result);
        };
        // return view('backend.success', compact('route_name', 'act'));
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
                    $this->productsService->delGoogleShopPhoto($in['id']);
                } catch (\Throwable $th) {
                    $status = false;
                }
                break;
            case 'DelItemPhotos':
                // try {
                $this->productsService->delItemPhotos($in['item_id']);
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
}
