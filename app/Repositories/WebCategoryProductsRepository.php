<?php

namespace App\Repositories;

use App\Enums\CacheSaveSecEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Services\WebCategoryHierarchyService;

class WebCategoryProductsRepository
{
    /*
     * cache Sec 為暫存秒數，此 Class 由程式限定，依目前需使用的情境暫定為 3 分鐘，依情況條整
     */
    private int $cacheSec = CacheSaveSecEnum::WebCategoryProducts;
    private $configLevels;
    private int $useQueryType = 0;

    private WebCategoryHierarchyService $apiWebCategory;

    // query params
    private $category = null;
    private $sellingPriceMin = null;
    private $sellingPriceMax = null;
    private $keyword = null;
    private $id = null;
    private $orderBy = null;
    private $sortFlag = null;
    private $attribute = null;
    private $brand = null;
    private $filter = null;
    private $attributeAry = null;

    /**
     * @param WebCategoryHierarchyService $apiWebCategory
     */
    public function __construct(WebCategoryHierarchyService $apiWebCategory)
    {
        $this->apiWebCategory = $apiWebCategory;

        $this->configLevels = config('uec.web_category_hierarchy_levels');
    }

    /**
     * 設定後續要使用的查詢條件，未設置，將預設為無條件
     * 並依設置的條件選擇 Query 方式
     * @param $category
     * @param $selling_price_min
     * @param $selling_price_max
     * @param $keyword
     * @param $id
     * @param $order_by
     * @param $sort_flag
     * @param $attribute
     * @param $brand
     * @param $filter
     *
     * @return void
     */
    public function setParams($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null, $id = null, $order_by = null, $sort_flag = null, $attribute = null, $brand = null, $filter = null, $attributeAry = null)
    {
        if ( !empty($category) || !empty($selling_price_min) || !empty(($selling_price_max)) || !empty($keyword) || !empty($order_by) || !empty($sort_flag) || !empty($attribute) || !empty($brand) || !empty($filter)) {
            $this->useQueryType = 2;

            $this->category = $category;
            $this->sellingPriceMin = $selling_price_min;
            $this->sellingPriceMax = $selling_price_max;
            $this->keyword = $keyword;
            $this->orderBy = $order_by;
            $this->sortFlag = $sort_flag;
            $this->attribute = $attribute;
            $this->brand = $brand;
            $this->filter = $filter;
            $this->id = $id;
            $this->attributeAry = $attributeAry ;
        } elseif ( !empty($id)) {
            $this->useQueryType = 1;

            $this->id = $id;
        }
    }

    /**
     * 依上述的判別的 Query 方式，直接使用對應的條件式
     * TODO 改善空間，下方的 Query 有部份重覆程式碼，因目前使用 mysql query 短時間無法優化，後續應優化
     * @return mixed|void|null
     */
    public function getProducts()
    {
        switch ($this->useQueryType) {
            case 2:
                return $this->getAllProducts();
                break;
            case 1:
                return $this->getIdProducts();
                break;
            default:
                return $this->getNoneProducts();
        }
    }

    /**
     * 無任何條件查詢，依 levels 查詢，並放入 Cache
     * @return mixed
     */
    protected function getNoneProducts()
    {
        return \Cache::remember('noneWebCategoryProducts' . $this->configLevels, $this->cacheSec, function () {
            $products = DB::table('web_category_products')
                ->join('frontend_products_v as p', 'p.id', '=', 'web_category_products.product_id')
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'web_category_products.web_category_hierarchy_id')
                ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id');

            if ($this->configLevels == 3) {
                $products = $products->join('web_category_hierarchy as cate3', 'cate3.id', '=', 'cate2.parent_id');
            }

            $products = $products->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'p.id')
                ->leftJoin('product_attribute_lov', 'product_attribute_lov.id', '=', 'product_attributes.product_attribute_lov_id')
                ->leftJoin("brands", 'brands.id', '=', 'p.brand_id');
            $products = $products->select("web_category_products.web_category_hierarchy_id",
                                          'p.*',
                                          DB::raw("(select photo_name from product_photos where p.id = product_photos.product_id order by sort limit 0, 1) as displayPhoto"),
                                          "product_attribute_lov.id as attribute_id",
                                          "product_attribute_lov.attribute_type",
            );

            if ($this->configLevels == 3) {
                $products = $products->addSelect("cate1.category_name as L3", "cate2.category_name as L2", "cate3.category_name as L1");
            } else {
                $products = $products->addSelect("cate1.category_name as L2", "cate2.category_name as L1");
            }

            $products = $products->where('p.approval_status', 'APPROVED')
                ->where('p.start_launched_at', '<=', now())
                ->where('p.end_launched_at', '>=', now())
                ->where('p.product_type', 'N')
                ->where('cate1.active', 1);

            $products = $products->orderBy('p.id', 'asc');
            return $products->get();
        });
    }

    /**
     * 依產品 id 做為條件式查詢，並放入 cache
     * @return mixed
     */
    protected function getIdProducts()
    {
        return \Cache::remember('idWebCategoryProducts' . $this->configLevels . "_". $this->id , $this->cacheSec, function () {
            $products = DB::table('web_category_products')
                ->join('frontend_products_v as p', 'p.id', '=', 'web_category_products.product_id')
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'web_category_products.web_category_hierarchy_id')
                ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id');

            if ($this->configLevels == 3) {
                $products = $products->join('web_category_hierarchy as cate3', 'cate3.id', '=', 'cate2.parent_id');
            }

            $products = $products->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'p.id')
                ->leftJoin('product_attribute_lov', 'product_attribute_lov.id', '=', 'product_attributes.product_attribute_lov_id')
                ->leftJoin("brands", 'brands.id', '=', 'p.brand_id');

            $products = $products->select("web_category_products.web_category_hierarchy_id",
                                          'p.*',
                                          DB::raw("(select photo_name from product_photos where p.id = product_photos.product_id order by sort limit 0, 1) as displayPhoto"),
                                          "product_attribute_lov.id as attribute_id",
                                          "product_attribute_lov.attribute_type",
            );

            if ($this->configLevels == 3) {
                $products = $products->addSelect("cate1.category_name as L3", "cate2.category_name as L2", "cate3.category_name as L1");
            } else {
                $products = $products->addSelect("cate1.category_name as L2", "cate2.category_name as L1");
            }

            $products = $products->where('p.approval_status', 'APPROVED')
                ->where('p.start_launched_at', '<=', now())
                ->where('p.end_launched_at', '>=', now())
                ->where('p.product_type', 'N')
                ->where('cate1.active', 1);

            if ($this->id) {//依產品編號找相關分類
                $products = $products->where('web_category_products.product_id', '=', $this->id);
                $products = $products->orderBy('web_category_products.sort', 'asc');
            }

            $products = $products->orderBy('p.id', 'asc');

            return $products->get();
        });
    }


    /**
     * 依所有條件式查詢，無法放入 cache
     * @return mixed
     */
    protected function getAllProducts()
    {
        $products = DB::table('web_category_products')
            ->join('frontend_products_v as p', 'p.id', '=', 'web_category_products.product_id')
            ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id');
            
        if ($this->configLevels == 3) {
            $products = $products->join('web_category_hierarchy as cate3', 'cate3.id', '=', 'cate2.parent_id');
        }

        $products = $products->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'p.id')
            ->leftJoin('product_attribute_lov', 'product_attribute_lov.id', '=', 'product_attributes.product_attribute_lov_id')
            ->leftJoin("brands", 'brands.id', '=', 'p.brand_id');
        if($this->filter){
            $products = $products->leftJoin('product_attributes as pa', 'pa.product_id', '=', 'p.id');
        }
        $products = $products->select("web_category_products.web_category_hierarchy_id",
                                      'p.*',
                                      DB::raw("(select photo_name from product_photos where p.id = product_photos.product_id order by sort limit 0, 1) as displayPhoto"),
                                      "product_attribute_lov.id as attribute_id",
                                      "product_attribute_lov.attribute_type",
        );
        if($this->filter){
            $products->addSelect(
                "pa.product_attribute_lov_id as pa_product_attribute_lov_id",
                "pa.attribute_type as pa_attribute_type",
            );
        }

        if ($this->configLevels == 3) {
            $products = $products->addSelect("cate1.category_name as L3", "cate2.category_name as L2", "cate3.category_name as L1");
        } else {
            $products = $products->addSelect("cate1.category_name as L2", "cate2.category_name as L1");
        }

        $products = $products->where('p.approval_status', 'APPROVED')
            ->where('p.start_launched_at', '<=', now())
            ->where('p.end_launched_at', '>=', now())
            ->where('p.product_type', 'N');

        if (($this->keyword)) { //依關鍵字搜尋
            if ($this->configLevels == 3) {
                $products = $products->where(function ($query) {
                    $query->where('p.product_name', 'LIKE', '%' . $this->keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $this->keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $this->keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $this->keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $this->keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $this->keyword . '%')
                        ->orWhere('cate3.category_name', 'like', '%' . $this->keyword . '%')
                        ->where('cate3.active', 1)
                        ;
                });
            } else {
                $products = $products->where(function ($query) {
                    $query->where('p.product_name', 'LIKE', '%' . $this->keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $this->keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $this->keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $this->keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $this->keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $this->keyword . '%')
                        ->where('cate2.active', 1);
                });
            }
        }

        if ($this->sellingPriceMin >= 0 && $this->sellingPriceMax > 0) {//價格區間
            $products = $products->whereBetween('p.selling_price', [$this->sellingPriceMin, $this->sellingPriceMax]);
        }

        if ($this->category) {//依分類搜尋
            if ($this->configLevels == 3) {
                $hasChild = $this->apiWebCategory->hasChildCategories($this->category);
                if ($hasChild) {
                    $products = $products->where('cate3.id', '=', $this->category);
                } else {
                    $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $this->category);
                }
            } else {
                $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $this->category);
            }
        }

        if ($this->id) {//依產品編號找相關分類
            $products = $products->where('web_category_products.product_id', '=', $this->id);
            $products = $products->orderBy('web_category_products.sort', 'asc');
        }

        if ($this->brand) { //品牌
            $brand = explode(',', $this->brand);
            $products = $products->whereIn('p.brand_id', $brand);
        }
        if($this->attributeAry){
            //適用族群
            if ($this->attributeAry['group']) {
                $group = explode(',', $this->attributeAry['group']);
                $products = $products->whereExists(function (Builder $query) use ($group) {
                    $query->select('*')
                          ->from('product_attributes')
                          ->whereColumn('product_attributes.product_id', 'p.id')
                          ->whereIn('product_attributes.product_attribute_lov_id', $group);
                });
            }
            
            
         
            //成分
            if ($this->attributeAry['ingredient']) {
                $ingredient = explode(',', $this->attributeAry['ingredient']);
                $products = $products->whereExists(function (Builder $query) use ($ingredient) {
                    $query->select('*')
                          ->from('product_attributes')
                          ->whereColumn('product_attributes.product_id', 'p.id')
                          ->whereIn('product_attributes.product_attribute_lov_id', $ingredient);
                });
            }
            //認證
            if ($this->attributeAry['certificate']) {
                $certificate = explode(',', $this->attributeAry['certificate']);
                $products = $products->whereExists(function (Builder $query) use ($certificate) {
                    $query->select('*')
                          ->from('product_attributes')
                          ->whereColumn('product_attributes.product_id', 'p.id')
                          ->whereIn('product_attributes.product_attribute_lov_id', $certificate);
                });
            }
            //劑型
            if($this->attributeAry['dosage_form']){
                if ($this->attributeAry['dosage_form']) {
                    $dosage_form = explode(',', $this->attributeAry['dosage_form']);
                    $products = $products->whereExists(function (Builder $query) use ($dosage_form) {
                        $query->select('*')
                              ->from('product_attributes')
                              ->whereColumn('product_attributes.product_id', 'p.id')
                              ->whereIn('product_attributes.product_attribute_lov_id', $dosage_form);
                    });
                }
            }
        }else if($this->attribute) {//進階篩選條件
            $attribute = explode(',', $this->attribute);
            $attribute = array_unique($attribute);
                $products = $products->whereIn('product_attributes.product_attribute_lov_id', $attribute);
                $products = $products->whereIn('product_attributes.product_attribute_lov_id', $attribute);
            $products = $products->whereIn('product_attributes.product_attribute_lov_id', $attribute);
        }
        // foreach($products->get() as $p){
        //     dump("product_name:{$p->product_name}|pa_product_attribute_lov_id:{$p->pa_product_attribute_lov_id}") ;
        // }
        // dd($products->get());
        // dd($this->attributeAry) ;
        // dd('STOP');
        if ($this->orderBy == 'launched') {
            $products = $products->orderBy('p.start_launched_at', $this->sortFlag);
        } else if ($this->orderBy == 'price') {
            $products = $products->orderBy('p.selling_price', $this->sortFlag);
        } else if ($this->orderBy == 'attribute') {
            $products = $products->orderBy('brands.id', 'asc');
            $products = $products->orderBy('product_attribute_lov.id', 'asc');
        } else {
            $products = $products->orderBy('p.id', 'asc');
        }

        return $products->get();
    }
}