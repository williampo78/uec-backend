<?php

namespace App\Services\Api;
use App\Repositories\Api\ProductAttributeRepository ;
use App\Services\ProductAttributeLovService;
use App\Services\BrandsService;
/**
 * 篩選器
 * 搜尋產品上方的篩選器
 */
class SelectFilterService
{
    private $productAttributeRepository ;

    private $productAttributeLovService ;
    
    private $brandsService ;

    public function __construct(ProductAttributeRepository $productAttributeRepository,
    ProductAttributeLovService $productAttributeLovService,
    BrandsService $brandsService)
    {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeLovService = $productAttributeLovService;
        $this->brandsService              = $brandsService ; 
    }

    /**
     * 取得篩選結果
     *
     * @return void
     */
    public function getFilter($request){
        //取得條件下的產品
        $products = $this->productAttributeRepository->getAttributeFilter($request);
        //取得屬性
        $attributeLov = $this->productAttributeLovService->getAttributeForSearch();
        //取得品牌
        $brands = $this->brandsService->getBrandForSearch();

        $result = [] ; 
        foreach($attributeLov as $lov){
            $count = 0 ;
            foreach($products as $product){
                $count += $product->productAttribute->where('product_attribute_lov_id',$lov['id'])->count() ; 
            }
            $result[$lov['attribute_type']][] = [
                'code' => $lov['id'], //這邊不知道為什麼他輸出是給id 
                'id'   => $lov['id'],
                'name' => $lov['description'],
                'count'=> $count,
            ] ;
        }

        foreach($brands as $brand){
            $result[$brand['attribute_type']][] = [
                'code' => $brand['id'], //這邊不知道為什麼他輸出是給id 
                'id'   => $brand['id'],
                'name' => $brand['description'],
                'count'=> $products->where('brand_id',$brand['id'])->count(),
            ]  ;
        }

        return [
            'status'=>200,
            'result'=>$result,
        ];
    }
}
