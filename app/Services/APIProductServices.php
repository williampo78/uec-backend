<?php


namespace App\Services;

use App\Models\InstallmentInterestRate;
use App\Models\Product;
use App\Models\PromotionalCampaignGiveaway;
use App\Models\PromotionalCampaignProduct;
use App\Models\PromotionalCampaignThreshold;
use App\Models\WebCategoryHierarchy;
use Carbon\Carbon;
use App\Models\ProductItem;
use App\Models\ProductPhoto;
use GuzzleHttp\Psr7\Request;
use App\Models\RelatedProduct;
use App\Services\APIWebService;
use App\Services\BrandsService;
use App\Services\APICartServices;
use App\Services\UniversalService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\PromotionalCampaign;
use Illuminate\Support\Facades\Auth;
use App\Services\WebShippingInfoService;
use App\Services\ShippingFeeRulesService;
use App\Services\WebCategoryHierarchyService;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

class APIProductServices
{

    private $apiWebCategory;
    private $apiWebService;
    private $apiCartService;

    public function __construct(
        WebCategoryHierarchyService $apiWebCategory,
        APIWebService $apiWebService,
        APICartServices $apiCartService,
        BrandsService $brandsService,
        ShippingFeeRulesService $shippingFeeService,
        UniversalService $universalService,
        WebShippingInfoService $webShippingInfoService,
        ProductAttributeLovService $attributeLovService
    )
    {
        $this->apiWebCategory = $apiWebCategory;
        $this->apiWebService = $apiWebService;
        $this->apiCartService = $apiCartService;
        $this->brandsService = $brandsService;
        $this->shippingFeeService = $shippingFeeService;
        $this->universalService = $universalService;
        $this->webShippingInfoService = $webShippingInfoService;
        $this->ProductAttributeLovService = $attributeLovService;
    }

    public function getCategory($keyword = null)
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');
        $s3 = config('filesystems.disks.s3.url');

        //根據階層顯示層級資料
        if ($config_levels == '3') {
            $categorys = DB::table("web_category_products as cate_prod")
                ->join('frontend_products_v as prod', 'prod.id', '=', 'cate_prod.product_id')
                ->join("web_category_hierarchy as cate", function ($join) {
                    $join->on("cate.id", "=", "cate_prod.web_category_hierarchy_id")
                        ->where("cate.category_level", "=", 3);
                })
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'cate.parent_id')
                ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id')
                ->select(DB::raw("cate2.`lft` L1_LFT,cate1.`lft` L2_LFT, cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.*, count(cate_prod.`product_id`) as pCount,
                    '' as campaign_name, '' as url_code, '' as campaign_brief, cate2.`category_short_name` as L1_short_name, cate2.`icon_name` as L1_icon_name"))
                ->where('prod.approval_status', 'APPROVED')
                ->where('prod.start_launched_at', '<=', now())
                ->where('prod.end_launched_at', '>=', now())
                ->where('prod.product_type', 'N')
                ->where('cate.active', 1);
            if ($keyword) {
                $categorys = $categorys->where(function ($query) use ($keyword) {
                    $query->where('prod.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('prod.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('prod.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('prod.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('prod.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $keyword . '%');
                });
            }
            $categorys = $categorys->groupBy("cate.id")
                ->orderBy("cate2.lft", "asc")
                ->orderBy("cate1.lft", "asc")
                ->orderBy("cate.lft", "asc")
                ->get();
        } elseif ($config_levels == '2') {
            $categorys = DB::table("web_category_products as cate_prod")
                ->join('frontend_products_v as prod', 'prod.id', '=', 'cate_prod.product_id')
                ->join("web_category_hierarchy as cate", function ($join) {
                    $join->on("cate.id", "=", "cate_prod.web_category_hierarchy_id")
                        ->where("cate.category_level", "=", 2);
                })
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'cate.parent_id')
                ->select(DB::raw("cate1.`lft` L1_LFT, cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, count(cate_prod.`product_id`) as pCount,
                    '' as campaign_name, '' as url_code, '' as campaign_brief, cate1.`category_short_name` as L1_short_name, cate1.`icon_name` as L1_icon_name"))
                ->where('prod.approval_status', 'APPROVED')
                ->where('prod.start_launched_at', '<=', now())
                ->where('prod.end_launched_at', '>=', now())
                ->where('prod.product_type', 'N')
                ->where('cate.active', 1);
            if ($keyword) {
                $categorys = $categorys->where(function ($query) use ($keyword) {
                    $query->where('prod.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('prod.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('prod.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('prod.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('prod.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%');
                });
            }
            $categorys = $categorys->groupBy("cate.id")
                ->orderBy("cate1.lft", "asc")
                ->orderBy("cate.lft", "asc")
                ->get();
        }

        foreach ($categorys as $category) {
            $L1_data[$category->L1_LFT]["id"] = $category->L1ID;
            $L1_data[$category->L1_LFT]["name"] = $category->L1_NAME;
            $L1_data[$category->L1_LFT]["shortName"] = $category->L1_short_name;
            $L1_data[$category->L1_LFT]["icon"] = ($category->L1_icon_name ? $s3 . $category->L1_icon_name : null);

            if ($config_levels == '3') {
                $L2_data[$category->L1_LFT][$category->L2_LFT]["id"] = $category->L2ID;
                $L2_data[$category->L1_LFT][$category->L2_LFT]["name"] = $category->L2_NAME;

                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['id'] = $category->id;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['name'] = $category->category_name;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['type'] = $category->content_type;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_title'] = $category->meta_title;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_description'] = $category->meta_description;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_keywords'] = $category->meta_keywords;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['count'] = $category->pCount;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['promotion_campaign_id'] = $category->promotion_campaign_id;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['campaign_name'] = $category->campaign_name;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['url_code'] = $category->url_code;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['campaign_brief'] = $category->campaign_brief;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['lft'] = $category->lft;

            } else if ($config_levels == '2') {

                $L2_data[$category->L1_LFT][$category->id]['id'] = $category->id;
                $L2_data[$category->L1_LFT][$category->id]['name'] = $category->category_name;
                $L2_data[$category->L1_LFT][$category->id]['type'] = $category->content_type;
                $L2_data[$category->L1_LFT][$category->id]['meta_title'] = $category->meta_title;
                $L2_data[$category->L1_LFT][$category->id]['meta_description'] = $category->meta_description;
                $L2_data[$category->L1_LFT][$category->id]['meta_keywords'] = $category->meta_keywords;
                $L2_data[$category->L1_LFT][$category->id]['count'] = $category->pCount;
                $L2_data[$category->L1_LFT][$category->id]['promotion_campaign_id'] = $category->promotion_campaign_id;
                $L2_data[$category->L1_LFT][$category->id]['campaign_name'] = $category->campaign_name;
                $L2_data[$category->L1_LFT][$category->id]['url_code'] = $category->url_code;
                $L2_data[$category->L1_LFT][$category->id]['campaign_brief'] = $category->campaign_brief;
                $L2_data[$category->L1_LFT][$category->id]['lft'] = $category->lft;
            }

        }

        //根據階層顯示層級資料(賣場)
        if ($config_levels == '3') {
            $strSQL = "select cate2.`lft` L1_LFT,cate1.`lft` L2_LFT, cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.*,0 as pCount,
                    pcc.`campaign_name`, pcc.`url_code`, pcc.`campaign_brief`, cate2.`category_short_name` as L1_short_name, cate2.`icon_name` as L1_icon_name
                    from `web_category_hierarchy` cate
                    inner join `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    inner join `promotional_campaigns` pcc on pcc.`id`=cate.`promotion_campaign_id`
                    where cate.`active`=1 and pcc.`active`=1
                    and current_timestamp() between pcc.`start_at` and pcc.`end_at` and cate.content_type='M' ";
            if ($keyword) {
                $strSQL .= " and (cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
                $strSQL .= " or pcc.campaign_name like '%" . $keyword . "%'";
                $strSQL .= " or pcc.campaign_brief like '%" . $keyword . "%'";
                $strSQL .= ")";
            }
            $strSQL .= " group by cate.`id`
                    order by cate2.`lft`, cate1.`lft`, cate.`lft`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`lft` L1_LFT, cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, 0 as 'pCount',
                    pcc.`campaign_name`, pcc.`url_code`, pcc.`campaign_brief`, cate1.`category_short_name` as L1_short_name, cate1.`icon_name` as L1_icon_name
                    from web_category_hierarchy cate
                    inner join `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join `promotional_campaigns` pcc on pcc.`id`=cate.`promotion_campaign_id`
                    where cate.`active`=1 and pcc.`active`=1
                    and current_timestamp() between pcc.`start_at` and pcc.`end_at` and cate.content_type='M' ";
            if ($keyword) {
                $strSQL .= " and (cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                $strSQL .= " or pcc.campaign_name like '%" . $keyword . "%'";
                $strSQL .= " or pcc.campaign_brief like '%" . $keyword . "%'";
                $strSQL .= ")";
            }
            $strSQL .= " group by cate.`id`
                    order by cate1.`lft`, cate.`lft`";
        }
        $categorys = DB::select($strSQL);

        foreach ($categorys as $category) {
            $L1_data[$category->L1_LFT]["id"] = $category->L1ID;
            $L1_data[$category->L1_LFT]["name"] = $category->L1_NAME;
            $L1_data[$category->L1_LFT]["shortName"] = $category->L1_short_name;
            $L1_data[$category->L1_LFT]["icon"] = ($category->L1_icon_name ? $s3 . $category->L1_icon_name : null);
            if ($config_levels == '3') {
                $L2_data[$category->L1_LFT][$category->L2_LFT]["id"] = $category->L2ID;
                $L2_data[$category->L1_LFT][$category->L2_LFT]["name"] = $category->L2_NAME;

                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['id'] = $category->id;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['name'] = $category->category_name;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['type'] = $category->content_type;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_title'] = $category->meta_title;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_description'] = $category->meta_description;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['meta_keywords'] = $category->meta_keywords;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['count'] = $category->pCount;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['promotion_campaign_id'] = $category->promotion_campaign_id;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['campaign_name'] = $category->campaign_name;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['url_code'] = $category->url_code;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['campaign_brief'] = $category->campaign_brief;
                $L3_data[$category->L1_LFT][$category->L2_LFT][$category->id]['lft'] = $category->lft;

            } else if ($config_levels == '2') {
                $L2_data[$category->L1_LFT][$category->id]['id'] = $category->id;
                $L2_data[$category->L1_LFT][$category->id]['name'] = $category->category_name;
                $L2_data[$category->L1_LFT][$category->id]['type'] = $category->content_type;
                $L2_data[$category->L1_LFT][$category->id]['meta_title'] = $category->meta_title;
                $L2_data[$category->L1_LFT][$category->id]['meta_description'] = $category->meta_description;
                $L2_data[$category->L1_LFT][$category->id]['meta_keywords'] = $category->meta_keywords;
                $L2_data[$category->L1_LFT][$category->id]['count'] = $category->pCount;
                $L2_data[$category->L1_LFT][$category->id]['promotion_campaign_id'] = $category->promotion_campaign_id;
                $L2_data[$category->L1_LFT][$category->id]['campaign_name'] = $category->campaign_name;
                $L2_data[$category->L1_LFT][$category->id]['url_code'] = $category->url_code;
                $L2_data[$category->L1_LFT][$category->id]['campaign_brief'] = $category->campaign_brief;
                $L2_data[$category->L1_LFT][$category->id]['lft'] = $category->lft;
            }

        }

        $data = [];
        if (!isset($L1_data)) {
            return 404;
        } else {
            foreach ($L1_data as $key1 => $value1) {
                $data2 = [];
                $data[$key1]["id"] = $value1["id"];
                $data[$key1]["name"] = $value1["name"];
                $data[$key1]["shortName"] = $value1["shortName"];
                $data[$key1]["icon"] = $value1["icon"];
                if ($config_levels == 2) {
                    array_multisort(array_column($L2_data[$key1], 'lft'), SORT_ASC, $L2_data[$key1]);
                }
                foreach ($L2_data[$key1] as $key2 => $value2) {
                    $data2[$key2]["id"] = $value2["id"];
                    $data2[$key2]["name"] = $value2["name"];
                    if ($config_levels == 3) {
                        $data3 = [];
                        array_multisort(array_column($L3_data[$key1][$key2], 'lft'), SORT_ASC, $L3_data[$key1][$key2]);
                        foreach ($L3_data[$key1][$key2] as $key3 => $value3) {
                            $data3[$key3]["id"] = $value3["id"];
                            $data3[$key3]["name"] = $value3["name"];
                            $data3[$key3]["type"] = $value3["type"];
                            $data3[$key3]["count"] = $value3["count"];
                            $data3[$key3]["meta_title"] = $value3["meta_title"];
                            $data3[$key3]["meta_description"] = $value3["meta_description"];
                            $data3[$key3]["meta_keywords"] = $value3["meta_keywords"];
                            $data3[$key3]["campaignID"] = $value3["promotion_campaign_id"];
                            $data3[$key3]["campaignName"] = $value3["campaign_name"];
                            $data3[$key3]["campaignUrlCode"] = $value3["url_code"];
                            $data3[$key3]["campaignBrief"] = $value3["campaign_brief"];
                        }
                        $data2[$key2]["cateInfo"] = $data3;
                    } elseif ($config_levels == 2) {
                        $data2[$key2]["count"] = $value2["count"];
                        $data2[$key2]["type"] = $value2["type"];
                        $data2[$key2]["meta_title"] = $value2["meta_title"];
                        $data2[$key2]["meta_description"] = $value2["meta_description"];
                        $data2[$key2]["meta_keywords"] = $value2["meta_keywords"];
                        $data2[$key2]["campaignID"] = $value2["promotion_campaign_id"];
                        $data2[$key2]["campaignName"] = $value2["campaign_name"];
                        $data2[$key2]["campaignUrlCode"] = $value2["url_code"];
                        $data2[$key2]["campaignBrief"] = $value2["campaign_brief"];
                    }
                }
                $data[$key1]["cateInfo"] = $data2;
            }
            return $data;
        }
    }

    /*
     * 取得商品資訊 (上架審核通過 & 上架期間內)
     */
    public function getProducts($product_id = null)
    {
        $products = Product::select("*",
            DB::raw("(SELECT photo_name
                     FROM product_photos
                     WHERE products.id = product_photos.product_id order by sort limit 0, 1) AS displayPhoto"))
            ->where('approval_status', 'APPROVED');
        if ($product_id) {
            $products = $products->where('id', $product_id);
        } else {
            $products = $products->where('start_launched_at', '<=', now());
            $products = $products->where('end_launched_at', '>=', now());
        }
        $products = $products->get();
        $data = [];
        foreach ($products as $product) {
            $data[$product->id] = $product;
        }
        return $data;
    }

    /*
     * 取得分類總覽的商品資訊 (上架審核通過 & 上架期間內)
     */
    public function getWebCategoryProducts($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null, $id = null, $order_by = null, $sort_flag = null, $attribute = null, $brand = null, $filter = null)
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');
        $products = DB::table('web_category_products')
            ->join('frontend_products_v as p', 'p.id', '=', 'web_category_products.product_id')
            ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id');

        if ($config_levels == 3) {
            $products = $products->join('web_category_hierarchy as cate3', 'cate3.id', '=', 'cate2.parent_id');
        }

        $products = $products->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'p.id')
            ->leftJoin('product_attribute_lov', 'product_attribute_lov.id', '=', 'product_attributes.product_attribute_lov_id')
            ->leftJoin("brands", 'brands.id', '=', 'p.brand_id');

        $products = $products->select("web_category_products.web_category_hierarchy_id", 'p.*'
            , DB::raw("(select photo_name from product_photos where p.id = product_photos.product_id order by sort limit 0, 1) as displayPhoto")
            , "product_attribute_lov.id as attribute_id", "product_attribute_lov.attribute_type"
        );

        if ($config_levels == 3) {
            $products = $products->addSelect("cate1.category_name as L3", "cate2.category_name as L2", "cate3.category_name as L1");
        } else {
            $products = $products->addSelect("cate1.category_name as L2", "cate2.category_name as L1");
        }

        $products = $products->where('p.approval_status', 'APPROVED')
            ->where('p.start_launched_at', '<=', now())
            ->where('p.end_launched_at', '>=', now())
            ->where('p.product_type', 'N')
            ->where('cate1.active', 1);

        if (($keyword)) { //依關鍵字搜尋
            if ($config_levels == 3) {
                $products = $products->where(function ($query) use ($keyword) {
                    $query->where('p.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('p.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate3.category_name', 'like', '%' . $keyword . '%');
                });
            } else {
                $products = $products->where(function ($query) use ($keyword) {
                    $query->where('p.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('p.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $keyword . '%');
                });
            }
        }

        if ($selling_price_min >= 0 && $selling_price_max > 0) {//價格區間
            $products = $products->whereBetween('p.selling_price', [$selling_price_min, $selling_price_max]);
        }

        if ($category) {//依分類搜尋
            if ($config_levels == 3) {
                $hasChild = $this->apiWebCategory->hasChildCategories($category);
                if ($hasChild) {
                    $products = $products->where('cate3.id', '=', $category);
                } else {
                    $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $category);
                }
            } else {
                $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $category);
            }
        }

        if ($id) {//依產品編號找相關分類
            $products = $products->where('web_category_products.product_id', '=', $id);
            $products = $products->orderBy('web_category_products.sort', 'asc');
        }

        if ($brand) { //品牌
            $brand = explode(',', $brand);
            $products = $products->whereIn('p.brand_id', $brand);
        }
        if ($attribute) {//進階篩選條件
            $attribute = explode(',', $attribute);
            $attribute = array_unique($attribute);
            $products = $products->whereIn('product_attributes.product_attribute_lov_id', $attribute);
        }

        if ($order_by == 'launched') {
            $products = $products->orderBy('p.start_launched_at', $sort_flag);
        } else if ($order_by == 'price') {
            $products = $products->orderBy('p.selling_price', $sort_flag);
        } else if ($order_by == 'attribute') {
            $products = $products->orderBy('brands.id', 'asc');
            $products = $products->orderBy('product_attribute_lov.id', 'asc');
        } else {
            $products = $products->orderBy('p.id', 'asc');
        }

        $products = $products->get();
        $data = [];
        $product_id = 0;
        $web_category_hierarchy_id = 0;

        if ($filter) {
            foreach ($products as $product) {
                $data[$product->web_category_hierarchy_id][$product->id][$product->attribute_id ?? 0] = $product;
            }
        } else {
            foreach ($products as $product) {
                if (!$id) {//依產品編號找相關分類不進此判斷
                    if ($product->id == $product_id && $product->web_category_hierarchy_id == $web_category_hierarchy_id) continue;
                }
                $data[$product->web_category_hierarchy_id][] = $product;
                $product_id = $product->id;
                $web_category_hierarchy_id = $product->web_category_hierarchy_id;
            }
        }
        return $data;
    }

    /*
     * 分類總覽的商品搜尋結果 (上架審核通過 & 上架期間內 & 登入狀態)
     * 關鍵字的商品搜尋結果 (上架審核通過 & 上架期間內 & 登入狀態)
     */
    public function searchResult($input)
    {
        $now = Carbon::now();
        $s3 = config('filesystems.disks.s3.url');
        $keyword = ($input['keyword'] ? $this->universalService->handleAddslashes($input['keyword']) : '');
        $category = (int)$input['category'];
        $size = $input['size'];
        $page = $input['page'];
        $selling_price_min = $input['price_min'];
        $selling_price_max = $input['price_max'];
        $order_by = ($input['order_by'] ? $input['order_by'] : 'launched');
        $sort_flag = ($input['sort'] ? $input['sort'] : 'DESC');
        //$sort_flag = $input['sort'] == 'ASC' ? SORT_ASC : SORT_DESC;
        $attribute = '';
        $attribute .= ($input['group'] ? $input['group'] : '');
        $attribute .= ($attribute != '' && $input['ingredient'] != '' ? ', ' : '') . ($input['ingredient'] ? $input['ingredient'] : '');
        $attribute .= ($attribute != '' && $input['dosage_form'] != '' ? ', ' : '') . ($input['dosage_form'] ? $input['dosage_form'] : '');
        $attribute .= ($attribute != '' && $input['certificate'] != '' ? ', ' : '') . ($input['certificate'] ? $input['certificate'] : '');
        $brand = '';
        $brand .= ($input['brand'] ? $input['brand'] : '');
        $products = self::getWebCategoryProducts($category, $selling_price_min, $selling_price_max, $keyword, null, $order_by, $sort_flag, $attribute, $brand);
        $product_info = $this->getProducts();
        $gtm = $this->getProductItemForGTM($product_info);
        if ($products) {
            $promotion = self::getPromotion('product_card');
            $promotion_threshold = self::getPromotionThreshold();
            foreach ($promotion as $k => $v) {
                $promotion_txt = '';
                foreach ($v as $label) {
                    if ($label->promotional_label == '') continue;
                    if ($label->campaign_type == 'CART_P03' || $label->campaign_type == 'CART_P04') { //檢查多門檻的商品是否為正常上架
                        if (isset($promotion_threshold[$k])) {
                            if ($promotion_threshold[$k]) {
                                if ($promotion_txt != $label->promotional_label) {
                                    $promotional[$k][] = $label->promotional_label;
                                    $promotion_txt = $label->promotional_label;
                                }
                            }
                        }
                    } elseif ($label->campaign_type == 'PRD05') { //單品
                        $campaign_gift = $this->getCampaignGiftByID($label->id);
                        if ($campaign_gift['result']) {
                            if ($promotion_txt != $label->promotional_label) {
                                $promotional[$k][] = $label->promotional_label;
                                $promotion_txt = $label->promotional_label;
                            }
                        }
                    } else {
                        if ($promotion_txt != $label->promotional_label) {
                            $promotional[$k][] = $label->promotional_label;
                            $promotion_txt = $label->promotional_label;
                        }
                    }
                }
            }
            $login = Auth::guard('api')->check();
            $collection = false;
            $cart = false;
            $is_collection = [];
            $is_cart = [];
            if ($login) {
                $member_id = Auth::guard('api')->user()->member_id;
                if ($member_id > 0) {
                    $response = $this->apiWebService->getMemberCollections();
                    $is_collection = json_decode($response, true);
                }
            }
            $product_id = 0;
            foreach ($products as $cateID => $prod) {
                foreach ($prod as $product) {
                    if ($now >= $product->promotion_start_at && $now <= $product->promotion_end_at) {
                        $promotion_desc = $product->promotion_desc;
                    } else {
                        $promotion_desc = null;
                    }
                    $discount = ($product->list_price == 0 ? 0 : ceil(($product->selling_price / $product->list_price) * 100));


                    if (isset($is_collection)) {
                        foreach ($is_collection as $k => $v) {
                            if ($v['product_id'] == $product->id) {
                                $collection = true;
                            }
                        }
                    }
                    if ($product->id != $product_id) {
                        $data[$product->id] = array(
                            'product_id' => $product->id,
                            'product_no' => $product->product_no,
                            'product_name' => $product->product_name,
                            'list_price' => $product->list_price,
                            'selling_price' => $product->selling_price,
                            'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                            'promotion_desc' => $promotion_desc,
                            'promotion_label' => (isset($promotional[$product->id]) ? $promotional[$product->id] : null),
                            'collections' => $collection,
                            'cart' => $cart,
                            'selling_channel' => $product->selling_channel,
                            'start_selling' => $product->start_selling_at,
                            'gtm' => isset($gtm[$product->id]) ? $gtm[$product->id] : ""
                        );

                        $product_id = $product->id;
                    }

                }
            }
            $return_data = [];
            foreach ($data as $key => $product) {
                $return_data[] = $product;
            }
            //array_multisort(array_column($data, 'selling_price'), $sort_flag, $data);
            $searchResult = self::getPages($return_data, $size, $page);
        } else {
            $searchResult = '404';
        }
        return $searchResult;

    }

    /*
     * 取分頁處理
     */
    public function getPages($data, $size, $page)
    {
        $amountOfPage = $size; //每頁顯示筆數
        $totalRows = count($data);
        $totalPages = ceil($totalRows / $amountOfPage);
        $currentPage = $page < 0 ? 1 : $page;
        $startRow = (($currentPage - 1) * $amountOfPage);
        $endRow = (($currentPage) * $amountOfPage) - 1;
        if ($currentPage > $totalPages) {//防止頁數錯誤
            $p = $totalPages;
            $startRow = (($p - 1) * $amountOfPage) - 1;
            $endRow = $startRow;//(($p) * $amountOfPage) - 1;
        }
        $startRow = ($startRow < 0) ? 0 : $startRow;
        $endRow = ($endRow > $totalRows - 1) ? $totalRows - 1 : $endRow;//處理最後一頁
        $endRow = ($endRow < 0) ? 0 : $endRow;

        for ($i = $startRow; $i <= $endRow; $i++) {
            $list[] = $data[$i];
        }

        $result = array('totalRows' => $totalRows, 'totalPages' => $totalPages, 'currentPage' => $currentPage, 'list' => $list);
        return $result;

    }

    /*
     * 取得行銷促案資訊
     */
    public function getPromotion($type = null, $event = null)
    {
        $strSQL = "select pcp.product_id, p.approval_status, pc.*
                from promotional_campaigns pc
                inner join  promotional_campaign_products pcp on pcp.promotional_campaign_id=pc.id
                inner join frontend_products_v p on p.id=pcp.product_id
                where current_timestamp() between pc.start_at and pc.end_at and pc.active=1
                and current_timestamp() between p.start_launched_at and p.end_launched_at and p.approval_status='APPROVED' ";

        if ($event) {
            $strSQL .= " and pc.id=" . (int)$event;
        }
        $strSQL .= " order by pcp.product_id, pc.promotional_label";
        $promotional = DB::select($strSQL);
        $data = [];
        $label = '';
        foreach ($promotional as $promotion) {
            if ($type == 'product_card') {
                $data[$promotion->product_id][] = $promotion;
            } else if ($type == 'product_content') {
                $data[$promotion->category_code][] = $promotion;
            }
        }
        return $data;

    }

    /*
     * 取得搜尋結果的上方分類
     */
    public function getSearchResultForCategory($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null, $attribute = null, $brand = null)
    {

        $s3 = config('filesystems.disks.s3.url');
        $hasChild = false;
        $show_level = false;
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');
        $products = DB::table('web_category_products')
            ->join('frontend_products_v as p', 'p.id', '=', 'web_category_products.product_id')
            ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id');

        if ($config_levels == 3) {
            $products = $products->join('web_category_hierarchy as cate3', 'cate3.id', '=', 'cate2.parent_id');
        }

        $products = $products->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'p.id')
            ->leftJoin('product_attribute_lov', 'product_attribute_lov.id', '=', 'product_attributes.product_attribute_lov_id')
            ->leftJoin("brands", 'brands.id', '=', 'p.brand_id');

        $products = $products->select("cate1.id", "cate1.category_name", DB::raw("count(cate1.id) as count"), "p.id as prod_id");

        if ($config_levels == 3) {
            $products = $products->addSelect("cate2.id as L2", "cate2.category_name as L2_Name", "cate3.id as L1", "cate3.category_name as L1_Name");
            $products = $products->addSelect("cate3.category_short_name as L1_category_short_name", "cate3.icon_name as L1_icon_name");
        } else {
            $products = $products->addSelect("cate2.id as L1", "cate2.category_name as L1_Name");
            $products = $products->addSelect("cate2.category_short_name as L1_category_short_name", "cate2.icon_name as L1_icon_name");
        }

        $products = $products->where('p.approval_status', 'APPROVED')
            ->where('p.start_launched_at', '<=', now())
            ->where('p.end_launched_at', '>=', now())
            ->where('p.product_type', 'N')
            ->where('cate1.active', 1);

        if (($keyword)) { //依關鍵字搜尋
            if ($config_levels == 3) {
                $products = $products->where(function ($query) use ($keyword) {
                    $query->where('p.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('p.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate3.category_name', 'like', '%' . $keyword . '%');
                });
            } else {
                $products = $products->where(function ($query) use ($keyword) {
                    $query->where('p.product_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.product_no', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('p.keywords', 'like', '%' . $keyword . '%')
                        ->orWhere('p.supplier_name', 'like', '%' . $keyword . '%')
                        ->orWhere('p.brand_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate1.category_name', 'like', '%' . $keyword . '%')
                        ->orWhere('cate2.category_name', 'like', '%' . $keyword . '%');
                });
            }
        }

        if ($selling_price_min >= 0 && $selling_price_max > 0) {//價格區間
            $products = $products->whereBetween('p.selling_price', [$selling_price_min, $selling_price_max]);
        }

        if ($category) {//依分類搜尋
            if ($config_levels == 3) {
                $show_level = true;
                $hasChild = $this->apiWebCategory->hasChildCategories($category);
                if ($hasChild) {
                    $products = $products->where('cate3.id', '=', $category);
                } else {
                    $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $category);
                }
            } else {
                $products = $products->where('web_category_products.web_category_hierarchy_id', '=', $category);
            }
        }

        if ($brand) { //品牌
            $brand = explode(',', $brand);
            $products = $products->whereIn('p.brand_id', $brand);
        }

        if ($attribute) {//進階篩選條件
            $attribute = explode(',', $attribute);
            $attribute = array_unique($attribute);
            $products = $products->whereIn('product_attributes.product_attribute_lov_id', $attribute);
        }
        $products = $products->groupBy('cate1.id')->groupBy('p.id');

        if ($config_levels == 2) {
            $products = $products->orderBy('cate2.lft', 'asc');
            $products = $products->orderBy('cate1.lft', 'asc');
        } else {
            $products = $products->orderBy('cate3.lft', 'asc');
            $products = $products->orderBy('cate2.lft', 'asc');
            $products = $products->orderBy('cate1.lft', 'asc');
        }

        $products = $products->get();

        foreach ($products as $cateID => $product) {
            $category_count[$product->id] = 0;
            $category_array[$product->id][$product->prod_id] = 0;
        }
        foreach ($products as $cateID => $product) {
            $category_array[$product->id][$product->prod_id]++;
        }
        if (isset($category_array)) {
            foreach ($category_array as $cate_id => $prod) {
                foreach ($prod as $prod_id => $count) {
                    $category_count[$cate_id]++;
                }
            }
        }
        if ($products) {
            $data = [];
            $cate = 0;
            $subCate = 0;
            foreach ($products as $category) {
                if ($config_levels == 2) {
                    if ($cate == $category->id) continue;
                    $sub[$category->L1][] = array(
                        'id' => $category->id,
                        'name' => $category->category_name,
                        'count' => $category_count[$category->id]
                    );
                    $cate = $category->id;
                } elseif ($config_levels == 3) {
                    if ($subCate == $category->id) continue;
                    $sub[$category->L1][$category->L2][] = array(
                        'id' => $category->id,
                        'name' => $category->category_name,
                        'count' => $category_count[$category->id]
                    );
                    $subCate = $category->id;
                }
            }
            $cate = 0;
            if ($config_levels == 2) {
                foreach ($products as $category) {
                    if ($cate == $category->L1) continue;
                    $data[] = array(
                        'id' => $category->L1,
                        'name' => $category->L1_Name,
                        'shortName' => $category->L1_category_short_name,
                        'icon' => ($category->L1_icon_name ? $s3 . $category->L1_icon_name : null),
                        'sub' => $sub[$category->L1]
                    );
                    $cate = $category->L1;
                }
            } elseif ($config_levels == 3) {
                $subCate = 0;
                foreach ($products as $category) {
                    if ($subCate == $category->L2) continue;
                    $main[$category->L1][] = array(
                        'id' => $category->L2,
                        'name' => $category->L2_Name,
                        'sub' => $sub[$category->L1][$category->L2]
                    );
                    $subCate = $category->L2;
                }
                foreach ($products as $category) {
                    if ($cate == $category->L1) continue;
                    $data[] = array(
                        'level' => ($show_level ? ($hasChild ? 1 : 3) : null),
                        'id' => $category->L1,
                        'name' => $category->L1_Name,
                        'shortName' => $category->L1_category_short_name,
                        'icon' => ($category->L1_icon_name ? $s3 . $category->L1_icon_name : null),
                        'sub' => $main[$category->L1]
                    );
                    $cate = $category->L1;
                }
            }
            return $data;
        } else {
            return '404';
        }
    }

    /*
     * 取得商品資料內容頁
     */
    public function getProduct($id, $detail = null)
    {
        $s3 = config('filesystems.disks.s3.url');
        $config_levels = config('uec.web_category_hierarchy_levels');
        $payment_text = $this->universalService->getPaymentType();
        $delivery_text = $this->universalService->getDeliveryType();
        $now = Carbon::now();
        $data = [];
        //產品主檔基本資訊
        $product = self::getProducts($id);

        if (sizeof($product) > 0) {
            if (strtotime($now) > strtotime($product[$id]->end_launched_at)) return 902; //此商品已被下架
            $product_categorys = self::getWebCategoryProducts('', '', '', '', $id, '', '');
            $rel_category = [];
            if (sizeof($product_categorys) > 0) {
                $web_category_hierarchy_id = 0;
                foreach ($product_categorys as $key => $category) {
                    foreach ($category as $kk => $vv) {
                        if ($web_category_hierarchy_id != $vv->web_category_hierarchy_id) {
                            $rel_category[] = array(
                                "category_id" => $vv->web_category_hierarchy_id,
                                "category_name" => $vv->L1 . ", " . $vv->L2 . ($config_levels == 3 ? ", " . $vv->L3 : "")
                            );
                            $web_category_hierarchy_id = $vv->web_category_hierarchy_id;
                        }
                    }
                }
            }
            if (!$rel_category) return 901; //此商品沒有前台分類
            $discount = ($product[$id]->list_price == 0 ? 0 : ceil(($product[$id]->selling_price / $product[$id]->list_price) * 100));
            $brand = $this->brandsService->getBrand($product[$id]->brand_id);
            $shipping_fee = $this->shippingFeeService->getShippingFee($product[$id]->lgst_method);
            $shipping_info = $this->webShippingInfoService->getShippingInfo($product[$id]->web_shipping_info_code);

            //促銷小標
            if ($now >= $product[$id]->promotion_start_at && $now <= $product[$id]->promotion_end_at) {
                $promotional = $product[$id]->promotion_desc;
            } else {
                $promotional = null;
            }

            //付款方式
            $payment_method = [];
            $payment_way = $product[$id]->payment_method;

            $methods = explode(',', $payment_way);

            foreach ($methods as $method) {
                //畫面上不會顯示分期付款字樣
                if ($method === 'TAPPAY_INSTAL') {
                    continue;
                }

                $payment_method[] = $payment_text[$method];
            }

            //配送方式
            $delivery_method = [];
            if ($product[$id]->lgst_method != '') {
                $methods = explode(',', $product[$id]->lgst_method);
                foreach ($methods as $method) {
                    $delivery_method[] = $delivery_text[$method];
                }
            }

            //運費描述
            $fee = [];
            if ($shipping_fee) {
                $fee = array(
                    "fee" => $shipping_fee[$product[$id]->lgst_method]->shipping_fee,
                    "fee_threshold" => $shipping_fee[$product[$id]->lgst_method]->free_threshold,
                    "notice" => $shipping_fee[$product[$id]->lgst_method]->notice_brief,
                    "notice_detail" => $shipping_fee[$product[$id]->lgst_method]->notice_detailed
                );
            }

            //出貨描述
            $shipping_info = array(
                "info" => $shipping_info[$product[$id]->web_shipping_info_code]->info_name,
                "notice" => $shipping_info[$product[$id]->web_shipping_info_code]->notice_brief,
                "notice_detail" => $shipping_info[$product[$id]->web_shipping_info_code]->notice_detailed
            );

            $product_info = array(
                "product_id" => $id,
                "product_name" => $product[$id]->product_name,
                "selling_price" => intval($product[$id]->selling_price),
                "list_price" => intval($product[$id]->list_price),
                "product_discount" => ($discount > 100 ? 100 : intval($discount)),
                "delivery" => $delivery_method,
                "payment" => $payment_method,
                "brand" => $brand[0]->brand_name,
                "promotion_label" => $promotional,
                "selling_channel" => $product[$id]->selling_channel,
                "start_selling" => $product[$id]->start_selling_at,
                "cart_type" => $product[$id]->stock_type == 'T' ? 'supplier' : 'dradvice'
            );
            $data['productInfo'] = $product_info;

            //產品圖檔
            $photos = [];
            $productPhotos = ProductPhoto::where('product_id', $id)->orderBy('sort', 'asc')->get();
            if (isset($productPhotos)) {
                foreach ($productPhotos as $photo) {
                    $photos[] = $s3 . $photo->photo_name;
                }
            }
            $data['productPhotos'] = $photos;

            //運費描述
            $data['shippingFee'] = $fee;

            //出貨描述
            $data['shippingInfo'] = $shipping_info;

            //行銷促案資訊
            $promotion_type = [];
            $promotions = self::getPromotion('product_content');
            $promotion_threshold = self::getPromotionThreshold();
            foreach ($promotions as $category => $promotion) {
                foreach ($promotion as $item) {
                    if ($item->product_id == $id) {
                        $promotion_type[($category == 'GIFT' ? '贈品' : '優惠')][] = array(
                            "campaign_id" => $item->id,
                            "campaign_name" => $item->campaign_brief ? $item->campaign_brief : $item->campaign_name,
                            "more_detail" => true
                        );
                    }
                }
            }

            $data['campaignInfo'] = $promotion_type;

            $login = Auth::guard('api')->check();
            $collection = false;
            $is_collection = [];
            if ($login) {
                $member_id = Auth::guard('api')->user()->member_id;
                if ($member_id > 0) {
                    $response = $this->apiWebService->getMemberCollections();
                    $is_collection = json_decode($response, true);
                }
            }
            if (isset($is_collection)) {
                foreach ($is_collection as $k => $v) {
                    if ($v['product_id'] == $id) {
                        $collection = true;
                    }
                }
            }
            $data['collection'] = $collection;

            //產品規格
            $item_spec = [];
            $ProductSpec = ProductItem::where('product_id', $id)->where('status', 1)->orderBy('sort', 'asc')->get();
            $item_spec['spec_dimension'] = $product[$id]->spec_dimension; //維度
            $item_spec['spec_title'] = array($product[$id]->spec_1, $product[$id]->spec_2); //規格名稱
            $spec_info = [];
            $spec1 = '';
            $spec2 = '';
            foreach ($ProductSpec as $item) {
                if ($spec1 != $item['spec_1_value']) {
                    $item_spec['spec_1'][] = $item['spec_1_value'];//規格1
                }
                if ($spec2 != $item['spec_2_value']) {
                    $item_spec['spec_2'][] = $item['spec_2_value'];//規格2
                }
                $spec_info[] = array(
                    "item_id" => $item['id'],
                    "item_no" => $item['item_no'],
                    "item_photo" => ($item['photo_name'] ? $s3 . $item['photo_name'] : null),
                    "item_spec1" => $item['spec_1_value'],
                    "item_spec2" => $item['spec_2_value'],
                );
                $spec1 = $item['spec_1_value'];
                $spec2 = $item['spec_2_value'];
            }
            $item_spec['spec_1'] = (isset($item_spec['spec_1']) ? array_unique($item_spec['spec_1']) : []);
            $item_spec['spec_2'] = (isset($item_spec['spec_2']) ? array_unique($item_spec['spec_2']) : []);
            $item_spec['spec_info'] = $spec_info;
            $data['orderSpec'] = $item_spec;
            if ($detail == 'true') {
                $data["productDesc"] = $product[$id]->description;
                $data["productSpec"] = $product[$id]->specification;
            }
            if ($rel_category) {
                $data['relateCategory'] = $rel_category;
            }
            $data['googleShop'] = $s3 . $product[$id]->google_shop_photo_name;
            $meta = [];
            $meta['meta_title'] = ($product[$id]->meta_title ? $product[$id]->meta_title : $product[$id]->product_name);
            $meta['mata_description'] = ($product[$id]->mata_description ? $product[$id]->mata_description : $product[$id]->product_name);
            $meta['mata_keywords'] = ($product[$id]->mata_keywords ? $product[$id]->mata_keywords : $product[$id]->product_name);
            $meta['mata_image'] = ($product[$id]->displayPhoto ? $s3 . $product[$id]->displayPhoto : null);
            $meta['meta_type'] = 'website';
            $data['metaData'] = $meta;

            //商品簡述
            $data['product_brief'] = array(
                'brief_1' => $product[$id]->product_brief_1,
                'brief_2' => $product[$id]->product_brief_2,
                'brief_3' => $product[$id]->product_brief_3,
            );

            //認證標章
            $icon = [];
            $certificate = $this->getCertificateIcon();
            foreach ($certificate as $item) {
                if ($item->product_id == $id) {
                    $icon[] = array("icon" => $s3 . $item->photo_name);
                }
            }
            $data['certificate'] = $icon;


            //相關推薦
            $rel_prod = RelatedProduct::getRelated($id);
            $promotion = self::getPromotion('product_card');
            $rel_data = [];
            $products = $this->getProducts();

            $login = Auth::guard('api')->check();
            $is_collection = [];
            if ($login) {
                $member_id = Auth::guard('api')->user()->member_id;
                if ($member_id > 0) {
                    $response = $this->apiWebService->getMemberCollections();
                    $is_collection = json_decode($response, true);
                }
            }
            foreach ($rel_prod as $rel) {
                if (isset($products[$rel->related_product_id])) {
                    $collection = false;
                    //echo $products[$rel->related_product_id]->promotion_start_at;
                    $promotional = [];
                    if ($now >= $products[$rel->related_product_id]->promotion_start_at && $now <= $products[$rel->related_product_id]->promotion_end_at) {
                        $promotion_desc = $products[$rel->related_product_id]->promotion_desc;
                    } else {
                        $promotion_desc = null;
                    }


                    if (isset($promotion[$rel->related_product_id])) {
                        $promotion_txt = '';
                        foreach ($promotion[$rel->related_product_id] as $k => $Label) { //取活動標籤
                            if ($Label->promotional_label == '') continue;
                            if ($promotion_txt != $Label->promotional_label) {
                                $promotional[] = $Label->promotional_label;
                                $promotion_txt = $Label->promotional_label;
                            }
                        }
                    }

                    if (isset($is_collection)) {
                        foreach ($is_collection as $k => $v) {
                            if ($v['product_id'] == $rel->related_product_id) {
                                $collection = true;
                            }
                        }
                    }
                    $rel_data[] = array(
                        "product_id" => $rel->related_product_id,
                        "product_no" => $products[$rel->related_product_id]->product_no,
                        "product_name" => $products[$rel->related_product_id]->product_name,
                        "product_unit" => $products[$rel->related_product_id]->uom,
                        "product_photo" => ($products[$rel->related_product_id]->displayPhoto ? $s3 . $products[$rel->related_product_id]->displayPhoto : null),
                        "selling_price" => intval($products[$rel->related_product_id]->selling_price),
                        "list_price" => intval($products[$rel->related_product_id]->list_price),
                        'promotion_desc' => $promotion_desc,
                        "promotion_label" => $promotional,
                        "collection" => $collection,
                        "selling_channel" => $products[$rel->related_product_id]->selling_channel,
                        "start_selling" => $products[$rel->related_product_id]->start_selling_at
                    );

                }
            }
            $data['rel_product'] = $rel_data;

            //此商品可分期付款
            if (strpos($product[$id]->payment_method, 'TAPPAY_INSTAL') !== false) {
                //取得分期資料(不檢查消費門檻)
                $installmentInterestRates = $this->getInstallmentAmountInterestRatesWithBank();
                $installmentInterestRates = $this->handleInstallmentInterestRates($installmentInterestRates, $product[$id]->selling_price);
            }

            //分期資訊
            $data['installment_interest_rates'] = $installmentInterestRates ?? [];

            return json_encode($data);
        } else {
            return 903;
        }
    }

    /**
     * 取得信用卡分期資料
     * $min_consumption有值則判斷門檻
     * @param int|null $min_consumption
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/8/5 上午 10:56
     */
    public function getInstallmentAmountInterestRatesWithBank(int $min_consumption = null): Collection
    {
        $today = Carbon::today()->toDateString();

        return InstallmentInterestRate::with('bank:id,bank_no,short_name')
            ->whereHas('bank', function ($query) {
                $query->where('active', 1);
            })
            ->where('active', 1)
            ->whereDate('started_at', '<=', $today)
            ->whereDate('ended_at', '>=', $today)
            ->when(isset($min_consumption), function ($query) use ($min_consumption) {
                $query->where('min_consumption', '<=', $min_consumption);
            })
            ->orderBy('interest_rate', 'asc')
            ->orderBy('number_of_installments', 'asc')
            ->orderBy('issuing_bank_no', 'asc')
            ->get([
                'id',
                'issuing_bank_no',
                'number_of_installments',
                'interest_rate',
                'min_consumption'
            ]);
    }

    /**
     * 整理分期的顯示資訊
     * @param Collection $collection
     * @param int $price
     * @return array
     * @Author: Eric
     * @DateTime: 2022/8/4 下午 05:21
     */
    public function handleInstallmentInterestRates(Collection $collection, int $price): array
    {
        if ($collection->isEmpty()) {
            return [];
        }

        //根據分期和利率group by
        $details = $collection->groupBy(function ($item) {
            return sprintf('%s_%s', $item['number_of_installments'], $item['interest_rate']);
        });

        $details = $details->map(function ($Entity) use ($price) {
            return [
                'interest_rate' => $Entity->first()->interest_rate,
                'number_of_installments' => $Entity->first()->number_of_installments,
                'amount' => $this->getInstallmentAmount($price, $Entity->first()->interest_rate, $Entity->first()->number_of_installments),
                'banks' => $Entity->pluck('bank.short_name'),
                'bank_id' => $Entity->pluck('bank.bank_no'),
            ];
        })->values();

        return [
            'interest_rate' => $collection->first()->interest_rate,
            'number_of_installments' => $collection->first()->number_of_installments,
            'amount' => $this->getInstallmentAmount($price, $collection->first()->interest_rate, $collection->first()->number_of_installments),
            'details' => $details
        ];
    }

    /**
     * 分期付款金額
     * @param int $price
     * @param float $interestRate
     * @param int $numberOfInstallments
     * @return string
     * @Author: Eric
     * @DateTime: 2022/8/4 下午 05:20
     */
    public function getInstallmentAmount(int $price, float $interestRate, int $numberOfInstallments): string
    {
        return number_format($price * (1 + ($interestRate / 100)) / $numberOfInstallments);
    }

    /*
     * 取得行銷促案 - 滿額贈禮
     */
    public function getCampaignGift()
    {
        //已審核上架與活動期間內
        $now = Carbon::now();
        $data = [];
        $s3 = config('filesystems.disks.s3.url');
        $promotional = PromotionalCampaign::select("promotional_campaign_giveaways.promotional_campaign_id", "promotional_campaign_giveaways.product_id", "promotional_campaign_giveaways.assigned_qty as assignedQty", "promotional_campaigns.*", "products.start_launched_at", "products.end_launched_at", "products.product_name", "products.selling_price"
            , DB::raw("(SELECT photo_name FROM product_photos WHERE products.id = product_photos.product_id order by sort limit 0, 1) AS photo_name"))
            ->where("promotional_campaigns.start_at", "<=", $now)
            ->where("promotional_campaigns.end_at", ">=", $now)
            ->where("promotional_campaigns.active", "=", "1")
            ->join('promotional_campaign_giveaways', 'promotional_campaign_giveaways.promotional_campaign_id', '=', 'promotional_campaigns.id')
            ->join('products', 'products.id', '=', 'promotional_campaign_giveaways.product_id')
            ->where('products.approval_status', '=', 'APPROVED')->get();
        foreach ($promotional as $promotion) {
            //$productPhotos = ProductPhoto::where('product_id', $promotion->product_id)->orderBy('sort', 'asc')->first();
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id] = $promotion; //取單品的贈品
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id]['photo'] = (isset($promotion->photo_name) ? $s3 . $promotion->photo_name : null);
            if ($promotion->level_code == 'CART') {
                $data['CART'][] = $promotion; //取全站贈品
            }
        }
        return $data;

    }

    /*
     * 取得活動滿額折扣
     */
    public function getCampaignDiscount()
    {
        //已審核上架與活動期間內
        $now = Carbon::now();
        $promotional = PromotionalCampaign::where('active', '=', '1')
            ->where("start_at", "<=", $now)
            ->where("end_at", ">=", $now)
            ->where("level_code", '=', 'CART')->get();
        return $promotional;
    }

    /*
     * 取得產品認證標章
     * @param
     */
    public function getCertificateIcon()
    {
        $strSQL = "select lov.photo_name, pa.product_id
                from product_attributes pa
                join product_attribute_lov lov on pa.attribute_type = 'CERTIFICATE' and lov.id = pa.product_attribute_lov_id
                where lov.active =1 and lov.attribute_type='CERTIFICATE'
                order by lov.sort;";
        $certificate = DB::select($strSQL);
        return $certificate;
    }


    /*
     * 取得相推薦產品
     * @param
     */
    public function getRelated($product_id)
    {
        $rel_prod = RelatedProduct::select('related_products.*', 'porducts.product_no')
            ->join('porducts', 'products.id', '=', 'related_products.product_id')
            ->where('related_products.product_id', '=', $product_id)->get();
        return $rel_prod;
    }

    /*
     * 取得活動贈品內容
     * @param
     */
    public function getCampaignGiftByID($campaigns)
    {
        $explode_campaign = explode(",", $campaigns);
        $s3 = config('filesystems.disks.s3.url');
        $gifts = $this->getCampaignGift();
        $now = Carbon::now();
        $giftAway = [];
        foreach ($explode_campaign as $k => $campaign_id) {
            if (isset($gifts['PROD'][$campaign_id])) {
                $giftCount = 0;
                $giveaways = $this->promotionalCampaignGiveaways($campaign_id);
                foreach ($gifts['PROD'][$campaign_id] as $gift) {
                    if ($now >= $gift->start_at && $now <= $gift->end_at) {
                        $giftCount++;
                    }
                }
                if ($giftCount == count($giveaways)) {
                    foreach ($gifts['PROD'][$campaign_id] as $gift) {
                        $giftAway[] = array(
                            "productId" => $gift->product_id,
                            "productName" => $gift->product_name,
                            "productPhoto" => ($gift->photo ? $gift->photo : null),
                            "assignedQty" => $gift->assignedQty,
                        );
                    }
                }
            }
        }

        if (count($giftAway) > 0) {
            $result['status'] = 200;
            $result['result'] = $giftAway;
        } else {
            $result['status'] = 404;
            $result['result'] = null;
        }

        return $result;
    }

    /*
     * 麵包屑 - 分類
     */
    public function getBreadcrumbCategory($category = null)
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');

        //根據階層顯示層級資料
        if ($config_levels == '3') {
            $categorys = DB::table("web_category_products as cate_prod")
                ->join('frontend_products_v as prod', 'prod.id', '=', 'cate_prod.product_id')
                ->join("web_category_hierarchy as cate", function ($join) {
                    $join->on("cate.id", "=", "cate_prod.web_category_hierarchy_id")
                        ->where("cate.category_level", "=", 3);
                })
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'cate.parent_id')
                ->join('web_category_hierarchy as cate2', 'cate2.id', '=', 'cate1.parent_id')
                ->select(DB::raw("cate2.id L1ID , cate2.category_name L1_NAME, cate1.id L2ID , cate1.category_name L2_NAME, cate.*, count(cate_prod.product_id) as pCount"))
                ->where('prod.approval_status', 'APPROVED')
                ->where('prod.start_launched_at', '<=', now())
                ->where('prod.end_launched_at', '>=', now())
                ->where('prod.product_type', 'N')
                ->where('cate.active', 1);
            if ($category) {
                $categorys = $categorys->where('cate.id', $category);
            }
            $categorys = $categorys->groupBy("cate.id")
                ->orderBy("cate2.lft", "asc")
                ->orderBy("cate1.lft", "asc")
                ->orderBy("cate.lft", "asc")
                ->get();
        } elseif ($config_levels == '2') {
            $categorys = DB::table("web_category_products as cate_prod")
                ->join('frontend_products_v as prod', 'prod.id', '=', 'cate_prod.product_id')
                ->join("web_category_hierarchy as cate", function ($join) {
                    $join->on("cate.id", "=", "cate_prod.web_category_hierarchy_id")
                        ->where("cate.category_level", "=", 2);
                })
                ->join('web_category_hierarchy as cate1', 'cate1.id', '=', 'cate.parent_id')
                ->select(DB::raw("cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, count(cate_prod.`product_id`) as pCount"))
                ->where('prod.approval_status', 'APPROVED')
                ->where('prod.start_launched_at', '<=', now())
                ->where('prod.end_launched_at', '>=', now())
                ->where('prod.product_type', 'N')
                ->where('cate.active', 1);
            if ($category) {
                $categorys = $categorys->where('cate.id', $category);
            }
            $categorys = $categorys->groupBy("cate.id")
                ->orderBy("cate1.lft", "asc")
                ->orderBy("cate.lft", "asc")
                ->get();
        }

        $data = [];
        foreach ($categorys as $category) {
            $data['level1']["id"] = $category->L1ID;
            $data['level1']["name"] = $category->L1_NAME;
            if ($config_levels == '3') {
                $data['level2']["id"] = $category->L2ID;
                $data['level2']["name"] = $category->L2_NAME;
                $data['level3']['id'] = $category->id;
                $data['level3']['name'] = $category->category_name;
                $data['level3']['meta_title'] = ($category->meta_title ? $category->meta_title : $category->category_name);
                $data['level3']['meta_description'] = $category->meta_description;
                $data['level3'][$category->id]['meta_keywords'] = $category->meta_keywords;

            } else if ($config_levels == '2') {
                $data['level2']['id'] = $category->id;
                $data['level2']['name'] = $category->category_name;
                $data['level2']['meta_title'] = ($category->meta_title ? $category->meta_title : $category->category_name);
                $data['level2']['meta_description'] = $category->meta_description;
                $data['level2']['meta_keywords'] = $category->meta_keywords;
            }
        }
        return $data;

    }

    /*
     * 滿額活動 V2
     * $id 活動代碼
     */
    public function getEventStore($input)
    {
        $now = Carbon::now();
        $s3 = config('filesystems.disks.s3.url');
        $products = $this->getProducts();
        $gtm = $this->getProductItemForGTM($products);
        $id = $input['event'];
        $page = $input['page'];
        $size = $input['size'];

        $promotion = $this->getPromotion('product_card');
        $promotion_threshold = $this->getPromotionThreshold();
        foreach ($promotion as $product_id => $campaign) {
            $promotion_txt = '';
            foreach ($campaign as $label) {
                if ($label->promotional_label == '') continue;
                if ($promotion_txt != $label->promotional_label) {
                    $promotional[$product_id][] = $label->promotional_label;
                    $promotion_txt = $label->promotional_label;
                }
            }
        }
        $login = Auth::guard('api')->check();
        $is_collection = [];
        $is_cart = [];
        if ($login) {
            $member_id = Auth::guard('api')->user()->member_id;
            if ($member_id > 0) {
                $response = $this->apiWebService->getMemberCollections();
                $is_collection = json_decode($response, true);
                $response = $this->apiCartService->getCartInfo($member_id);
                unset($response['items']);
                $is_cart = $response;
            }
        }

        $product_info = [];

        //取得目前滿額活動
        $campaigns = $this->getPromotion('product_card', $id);
        if (count($campaigns) > 0) {
            //排列產品卡 - 並確認產品有分類
            foreach ($campaigns as $product_id => $campaign) {
                foreach ($campaign as $item) {
                    if ($now >= $item->start_at && $now <= $item->end_at) {
                        $photoDesktop = ($item->banner_photo_desktop ? $s3 . $item->banner_photo_desktop : null);
                        $photoMobile = ($item->banner_photo_mobile ? $s3 . $item->banner_photo_mobile : null);
                        $campaignName = $item->campaign_name;
                        $campaignBrief = $item->campaign_brief;
                        $product_check = $this->getWebCategoryProducts('', '', '', '', $product_id, '', '');
                        if (count($product_check) > 0) {
                            if ($now >= $products[$product_id]->promotion_start_at && $now <= $products[$product_id]->promotion_end_at) {
                                $promotion_desc = $products[$product_id]->promotion_desc;
                            } else {
                                $promotion_desc = null;
                            }
                            $collection = false;
                            if (isset($is_collection)) {
                                foreach ($is_collection as $k => $v) {
                                    if ($v['product_id'] == $product_id) {
                                        $collection = true;
                                    }
                                }
                            }
                            $cart = false;
                            if (isset($is_cart)) {
                                foreach ($is_cart as $k => $v) {
                                    if ($k == $product_id) {
                                        $cart = true;
                                    }
                                }
                            }

                            $product_info[] = array(
                                'product_id' => $product_id,
                                'product_no' => $products[$product_id]->product_no,
                                'product_name' => $products[$product_id]->product_name,
                                'product_unit' => $products[$product_id]->uom,
                                'list_price' => $products[$product_id]->list_price,
                                'selling_price' => $products[$product_id]->selling_price,
                                'product_photo' => ($products[$product_id]->displayPhoto ? $s3 . $products[$product_id]->displayPhoto : null),
                                'promotion_desc' => $promotion_desc,
                                'promotion_label' => (isset($promotional[$product_id]) ? $promotional[$product_id] : null),
                                "collection" => $collection,
                                'cart' => $cart,
                                "selling_channel" => $products[$product_id]->selling_channel,
                                "start_selling" => $products[$product_id]->start_selling_at,
                                "gtm" => (isset($gtm[$product_id]) ? $gtm[$product_id] : "")
                            );
                        }
                    }
                }
            }
            $searchResult = self::getPages($product_info, $size, $page);

            if (isset($searchResult)) {
                $searchResult['bannerPhotoDesktop'] = $photoDesktop;
                $searchResult['bannerPhotoMobile'] = $photoMobile;
                $searchResult['breadcrumbCampaignName'] = $campaignName;
                $searchResult['breadcrumbCampaignBrief'] = $campaignBrief;
                $result['status'] = 200;
                $result['result'] = $searchResult;
            } else {
                $result['status'] = 401;
                $result['result'] = null;
            }
        } else {
            $result['status'] = 401;
            $result['result'] = null;
        }
        return $result;
    }

    /*
     * 取得滿額活動折扣內容
     * @param
     */
    public function getCampaignDiscountByID($campaigns)
    {
        $explode_campaign = explode(",", $campaigns);
        $now = Carbon::now();

        $now = Carbon::now();
        $promotional = PromotionalCampaign::where('active', '=', '1')
            ->where("start_at", "<=", $now)
            ->where("end_at", ">=", $now)->get();
        foreach ($promotional as $promotion) {
            $discount[$promotion->id] = $promotion;
        }
        $discountArray = [];
        $campaignThreshold_brief = [];
        foreach ($explode_campaign as $k => $campaign_id) {
            if (isset($discount[$campaign_id])) {
                $campaignThresholds = PromotionalCampaignThreshold::where('promotional_campaign_id', $campaign_id)->orderBy('n_value')->get();
                foreach ($campaignThresholds as $threshold) {
                    $campaignThreshold_brief[$campaign_id][] = $threshold->threshold_brief;
                }
                $discountArray[] = array(
                    "campaignID" => $campaign_id,
                    "campaignUrlCode" => $discount[$campaign_id]['url_code'],
                    "campaignBrief" => $discount[$campaign_id]['campaign_brief'] ?? $discount[$campaign_id]['campaign_name'],
                    "campaignName" => $discount[$campaign_id]['campaign_name'],
                    "expireDate" => $discount[$campaign_id]['end_at'],
                    "gotoEvent" => ($discount[$campaign_id]['level_code'] == 'CART_P' ? true : false),
                    "campaignThreshold" => (isset($campaignThreshold_brief[$campaign_id]) ? $campaignThreshold_brief[$campaign_id] : [])
                );
            }
        }

        if (count($discountArray) > 0) {
            $result['status'] = 200;
            $result['result'] = $discountArray;
        } else {
            $result['status'] = 401;
            $result['result'] = null;
        }

        return $result;
    }

    /*
     * 取得商品資料規格
     */
    public function getProductItem($id)
    {
        $s3 = config('filesystems.disks.s3.url');
        $config_levels = config('uec.web_category_hierarchy_levels');
        $payment_text = $this->universalService->getPaymentType();
        $delivery_text = $this->universalService->getDeliveryType();
        $now = Carbon::now();
        $data = [];
        //產品主檔基本資訊
        $product = self::getProducts($id);

        if (sizeof($product) > 0) {
            if (strtotime($now) > strtotime($product[$id]->end_launched_at)) return 902; //此商品已被下架
            $product_categorys = self::getWebCategoryProducts('', '', '', '', $id, '', '');
            $rel_category = [];
            if (sizeof($product_categorys) > 0) {
                foreach ($product_categorys as $key => $category) {
                    foreach ($category as $kk => $vv) {
                        $rel_category[] = array(
                            "category_id" => $vv->web_category_hierarchy_id,
                            "category_name" => $vv->L1 . ", " . $vv->L2 . ($config_levels == 3 ? ", " . $vv->L3 : "")
                        );

                    }
                }
            }
            if (!$rel_category) return 901; //此商品沒有前台分類

            $product_info = array(
                "product_id" => $id,
                "product_name" => $product[$id]->product_name,
                "selling_price" => intval($product[$id]->selling_price),
                "list_price" => intval($product[$id]->list_price),
                "selling_channel" => $product[$id]->selling_channel,
                "start_selling" => $product[$id]->start_selling_at,
                "cart_type" => $product[$id]->stock_type == 'T' ? 'supplier' : 'dradvice'
            );
            $data['productInfo'] = $product_info;

            //產品圖檔
            $photos = [];
            $productPhotos = ProductPhoto::where('product_id', $id)->orderBy('sort', 'asc')->get();
            if (isset($productPhotos)) {
                foreach ($productPhotos as $photo) {
                    $photos[] = $s3 . $photo->photo_name;
                }
            }
            $data['productPhotos'] = $photos;

            //產品規格
            $item_spec = [];
            $ProductSpec = ProductItem::where('product_id', $id)->where('status', 1)->orderBy('sort', 'asc')->get();
            $item_spec['spec_dimension'] = $product[$id]->spec_dimension; //維度
            $item_spec['spec_title'] = array($product[$id]->spec_1, $product[$id]->spec_2); //規格名稱
            $spec_info = [];
            $spec1 = '';
            $spec2 = '';
            foreach ($ProductSpec as $item) {
                if ($spec1 != $item['spec_1_value']) {
                    $item_spec['spec_1'][] = $item['spec_1_value'];//規格1
                }
                if ($spec2 != $item['spec_2_value']) {
                    $item_spec['spec_2'][] = $item['spec_2_value'];//規格2
                }
                $spec_info[] = array(
                    "item_id" => $item['id'],
                    "item_no" => $item['item_no'],
                    "item_photo" => ($item['photo_name'] ? $s3 . $item['photo_name'] : null),
                    "item_spec1" => $item['spec_1_value'],
                    "item_spec2" => $item['spec_2_value'],
                );
                $spec1 = $item['spec_1_value'];
                $spec2 = $item['spec_2_value'];
            }
            $item_spec['spec_1'] = (isset($item_spec['spec_1']) ? array_unique($item_spec['spec_1']) : []);
            $item_spec['spec_2'] = (isset($item_spec['spec_2']) ? array_unique($item_spec['spec_2']) : []);
            $item_spec['spec_info'] = $spec_info;
            $data['orderSpec'] = $item_spec;

            return json_encode($data);
        } else {
            return 903;
        }
    }

    /*
     * 取得上架期間內有庫存的商品
     */
    public function getProductInStock()
    {
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $data = [];
        $strSQL = "SELECT p.id product_id,p.product_no, p.product_name
                ,(SELECT photo_name FROM product_photos WHERE p.id = product_photos.product_id order by sort limit 0, 1) AS product_photo_name
                ,p_item.id product_item_id, p_item.item_no product_item_no, p_item.spec_1_value product_item_spec1, p_item.spec_2_value product_item_spec2, p_item.photo_name product_item_photo_name
                ,ws.stock_qty
                FROM products AS p
                inner join product_items p_item on p_item.product_id=p.id
                inner join warehouse_stock ws on ws.product_item_id=p_item.id and ws.warehouse_id=" . $warehouseCode . " and ws.stock_qty >0
                where p.approval_status = 'APPROVED'
                and current_timestamp() between p.start_launched_at and p.end_launched_at";
        $products = DB::select($strSQL);
        foreach ($products as $product) {
            $data[$product['product_id']] = $product;
        }
    }

    /*
     * 進階搜尋商品欄位
     */
    public function getProductFilter($request)
    {

        $keyword = ($request['keyword'] ? $this->universalService->handleAddslashes($request['keyword']) : '');
        $category = (int)$request['category'];
        $selling_price_min = $request['price_min'];
        $selling_price_max = $request['price_max'];
        $order_by = 'attribute';
        $sort_flag = 'ASC';
        $attribute = '';
        $attribute .= ($request['group'] ? $request['group'] : '');
        $attribute .= ($attribute != '' && $request['ingredient'] != '' ? ', ' : '') . ($request['ingredient'] ? $request['ingredient'] : '');
        $attribute .= ($attribute != '' && $request['dosage_form'] != '' ? ', ' : '') . ($request['dosage_form'] ? $request['dosage_form'] : '');
        $attribute .= ($attribute != '' && $request['certificate'] != '' ? ', ' : '') . ($request['certificate'] ? $request['certificate'] : '');
        $brand = '';
        $brand .= ($request['brand'] ? $request['brand'] : '');

        $brands = $this->brandsService->getBrandForSearch();
        $attributeLov = $this->ProductAttributeLovService->getAttributeForSearch();
        $merge = array_merge($brands, $attributeLov);
        $filter = [];
        foreach ($merge as $data) {
            $filter[$data['attribute_type']][$data['id']] = array(
                'id' => $data['id'],
                'code' => $data['code'],
                'name' => $data['description'],
                'count' => 0
            );
        }
        $condition = ['BRAND', 'GROUP', 'INGREDIENT', 'CERTIFICATE'];
        foreach ($condition as $key) {
            $attribute_array[$key] = [];
        }
        $products = self::getWebCategoryProducts($category, $selling_price_min, $selling_price_max, $keyword, null, $order_by, $sort_flag, $attribute, $brand, 1);
        if ($products) {
            foreach ($products as $cateID => $prod) {
                foreach ($prod as $attribute) {
                    foreach ($attribute as $product) {
                        //品牌(商品)
                        if (!key_exists($product->brand_id, $attribute_array['BRAND'])) $attribute_array['BRAND'][$product->brand_id][$product->id] = 0;
                        $attribute_array['BRAND'][$product->brand_id][$product->id] = 1;
                        //屬性(商品)
                        if (isset($attribute_array[$product->attribute_type])) {
                            if (!key_exists($product->attribute_id, $attribute_array[$product->attribute_type])) $attribute_array[$product->attribute_type][$product->attribute_id][$product->id] = 0;
                            $attribute_array[$product->attribute_type][$product->attribute_id][$product->id] = 1;
                        }
                    }
                }
            }
        }
        foreach ($attribute_array as $data => $item) {
            foreach ($item as $attribute_id => $attribute_item) {
                if (isset($attribute_array[$data][$attribute_id])) {
                    //將同屬性的不同商品加總
                    $filter[$data][$attribute_id]['count'] = array_sum($attribute_array[$data][$attribute_id]);
                }
            }
        }
        $filter_display = [];

        foreach ($filter as $type => $data) {
            foreach ($data as $info) {
                $filter_display[$type][] = array(
                    'id' => $info['id'],
                    'code' => $info['code'],
                    'name' => $info['name'],
                    'count' => $info['count']
                );
            }
        }
        if ($filter) {
            $result['status'] = 200;
            $result['result'] = $filter_display;
        } else {
            $result['status'] = 401;
            $result['result'] = null;
        }
        return $result;
    }

    /**
     * 多門檻活動贈品狀態
     */
    public function getPromotionThreshold()
    {
        $strSQL = "select pcg.promotional_campaign_id,pcg.threshold_id, p.approval_status, p.id gift_id, pcp.product_id
                    from frontend_products_v p
                    inner join promotional_campaign_giveaways pcg on pcg.product_id=p.id
                    inner join promotional_campaigns pc on pc.id=pcg.promotional_campaign_id
                    inner join  promotional_campaign_products pcp on pcp.promotional_campaign_id=pc.id
                    where current_timestamp() between pc.start_at and pc.end_at and pc.active=1";
        $strSQL .= " order by pcg.promotional_campaign_id, pcg.threshold_id,p.approval_status, pcg.sort;";

        $promotional = DB::select($strSQL);
        $data = [];
        $result = [];
        foreach ($promotional as $promotion) {
            //取得在活動門檻中的贈品是否有效上架(同活動門檻最後一筆不是APPROVED該活動門檻就不算有效)
            if ($promotion->approval_status == 'APPROVED') {
                $result[$promotion->promotional_campaign_id][$promotion->threshold_id] = true;
            } else {
                $result[$promotion->promotional_campaign_id][$promotion->threshold_id] = false;
            }
        }
        array_multisort(array_column($promotional, 'product_id'), SORT_ASC, $promotional);
        foreach ($promotional as $promotion) {
            //指定單品的活動門檻如果是有效的就回傳
            if ($result[$promotion->promotional_campaign_id][$promotion->threshold_id]) {
                $data[$promotion->product_id] = true;
            }
        }
        return $data;

    }

    /*
     * 活動贈品相關
     */
    public function promotionalCampaignGiveaways($campaign_id)
    {
        $data = PromotionalCampaign::find($campaign_id)->promotionalCampaignGiveaways;
        foreach ($data as $item) {
            $result[] = $item->product_id;
        }
        return $result;
    }

    /*
     * 取得活動贈品內容
     * @param
     */
    public function getCampaignGiftByIDWithThreshold($campaigns)
    {
        $s3 = config('filesystems.disks.s3.url');
        $explode_campaign = explode(',', $campaigns);
        $now = Carbon::now();
        $promotional = PromotionalCampaign::with(['promotionalCampaignThresholds', 'promotionalCampaignGiveaways',
            'promotionalCampaignGiveaways.product',
            'promotionalCampaignGiveaways.product.productPhotos' => function ($query) {
                $query->orderBy('product_id', 'asc')
                    ->orderBy('sort', 'asc');
            },])
            ->where('active', '=', '1')
            ->where("category_code", "GIFT")
            ->where("start_at", "<=", $now)
            ->where("end_at", ">=", $now)
            ->whereIn('id', $explode_campaign)->get();
        $campaignGive = [];
        $giftArray = [];
        foreach ($promotional as $campaign) {
            $campaign->promotionalCampaignGiveaways->each(function ($giftDetail) use (&$giftArray, &$s3) {
                $giftArray[$giftDetail->promotional_campaign_id][$giftDetail->threshold_id][] = array(
                    "productName" => $giftDetail->product->product_name,
                    "productPhoto" => $s3 . optional($giftDetail->product->productPhotos->first())->photo_name,
                    "assignedQty" => $giftDetail->assigned_qty
                );
            });
            if ($campaign->promotionalCampaignThresholds->isNotEmpty()) {
                $campaign->promotionalCampaignThresholds->each(function ($thresholdDetail) use (&$campaignGive, $giftArray) {
                    $campaignGive[$thresholdDetail->promotional_campaign_id][] = array(
                        "thresholdId" => $thresholdDetail->id,
                        "thresholdBrief" => $thresholdDetail->threshold_brief,
                        "qualified" => $thresholdDetail->is_qualified_to_sent == 1 ? true : false,
                        "giveList" => $giftArray[$thresholdDetail->promotional_campaign_id][$thresholdDetail->id]
                    );
                });
            } else {
                $campaignGive[$campaign->id][] = array(
                    "giveList" => $giftArray[$campaign->id][0]
                );
            }

            $giveArray[] = array(
                "campaignID" => $campaign->id,
                "campaignUrlCode" => $campaign->url_code,
                "campaignBrief" => $campaign->campaign_brief,
                "campaignName" => $campaign->campaign_name,
                "expireDate" => $campaign->end_at,
                "gotoEvent" => ($campaign->level_code == 'CART_P' ? true : false),
                "qualified" => $campaign->is_qualified_to_sent == 1 ? true : false,
                "campaignGive" => $campaignGive[$campaign->id]
            );
        }

        if (isset($giveArray)) {
            $result['status'] = 200;
            $result['result'] = $giveArray;
        } else {
            $result['status'] = 404;
            $result['result'] = null;
        }

        return $result;
    }

    /*
     * 回傳有效上架商品
     */
    public function getEffectProduct($keywords)
    {
        $s3 = config('filesystems.disks.s3.url');
        $now = Carbon::now();
        $data = [];
        $keywords = explode(',', $keywords);
        //產品主檔基本資訊
        $product = self::getProducts();
        $promotion = self::getPromotion('product_card');
        foreach ($keywords as $id) {
            if (!key_exists($id, $product)) continue;   //不存在的商品
            if (strtotime($now) > strtotime($product[$id]->end_launched_at)) continue; //此商品已被下架
            $product_categorys = self::getWebCategoryProducts('', '', '', '', $id, '', '');
            if (sizeof($product_categorys) == 0) continue;  //此商品沒有前台分類

            //促銷小標
            if ($now >= $product[$id]->promotion_start_at && $now <= $product[$id]->promotion_end_at) {
                $promotional = $product[$id]->promotion_desc;
            } else {
                $promotional = null;
            }
            if (isset($product[$id])) {
                $collection = false;
                $promotional = [];
                if ($now >= $product[$id]->promotion_start_at && $now <= $product[$id]->promotion_end_at) {
                    $promotion_desc = $product[$id]->promotion_desc;
                } else {
                    $promotion_desc = null;
                }

                $login = Auth::guard('api')->check();
                $is_collection = [];
                if ($login) {
                    $member_id = Auth::guard('api')->user()->member_id;
                    if ($member_id > 0) {
                        $response = $this->apiWebService->getMemberCollections();
                        $is_collection = json_decode($response, true);
                    }
                }

                if (isset($promotion[$id])) {
                    $promotion_txt = '';
                    foreach ($promotion[$id] as $k => $Label) { //取活動標籤
                        if ($Label->promotional_label == '') continue;
                        if ($promotion_txt != $Label->promotional_label) {
                            $promotional[] = $Label->promotional_label;
                            $promotion_txt = $Label->promotional_label;
                        }
                    }
                }

                if (isset($is_collection)) {
                    foreach ($is_collection as $k => $v) {
                        if ($v['product_id'] == $id) {
                            $collection = true;
                        }
                    }
                }
                $data[] = array(
                    "product_id" => $id,
                    "product_no" => $product[$id]->product_no,
                    "product_name" => $product[$id]->product_name,
                    "product_unit" => $product[$id]->uom,
                    "product_photo" => ($product[$id]->displayPhoto ? $s3 . $product[$id]->displayPhoto : null),
                    "selling_price" => intval($product[$id]->selling_price),
                    "list_price" => intval($product[$id]->list_price),
                    'promotion_desc' => $promotion_desc,
                    "promotion_label" => $promotional,
                    "collection" => $collection,
                    "selling_channel" => $product[$id]->selling_channel,
                    "start_selling" => $product[$id]->start_selling_at
                );
            }
        }

        return $data;
    }

    /*
     * 取得商品資料(GTM)
     * $multi = 'item' 顯示獨立item規格
     */
    public function getProductItemForGTM($products, $multi = null)
    {
        $config_levels = config('uec.web_category_hierarchy_levels');
        $now = Carbon::now();
        $data = [];
        //產品主檔基本資訊
        $gtm = [];
        $data = [];
        $product_categorys = self::getWebCategoryProducts('', '', '', '', '', '', '');
        foreach ($product_categorys as $key => $category) {
            foreach ($category as $kk => $vv) {
                $rel_category[$vv->id] = array(
                    "brand_name" => $vv->brand_name,
                    "category_id" => $vv->web_category_hierarchy_id,
                    "category_name" => $vv->L1 . ", " . $vv->L2 . ($config_levels == 3 ? ", " . $vv->L3 : "")
                );

            }
        }
        $product_spec = $this->getProductItems();
        if (sizeof($products) > 0) {
            foreach ($products as $product) {
                $item_spec = [];
                if (strtotime($now) > strtotime($product->end_launched_at)) continue;
                if (!isset($rel_category[$product->id])) continue;
                if (!isset($product_spec[$product->id])) continue;
                //產品規格
                $gtm['item_name'] = $product->product_name;
                $gtm['currency'] = "TWD";
                $item_spec['spec_dimension'] = $product->spec_dimension; //維度
                //品牌
                $gtm['item_brand'] = $rel_category[$product->id]['brand_name'];
                //分類
                $item_category = explode(', ', $rel_category[$product->id]['category_name']);
                $gtm['item_category'] = $item_category[0];
                $gtm['item_category2'] = $item_category[1];
                $gtm['item_category3'] = isset($item_category[2]) ? $item_category[2] : "";
                $gtm['item_category4'] = "";
                $gtm['item_category5'] = "";

                if ($multi == 'item') {
                    foreach ($product_spec[$product->id] as $item) {
                        $gtm['item_id'] = $item['item_no'];
                        if ($item_spec['spec_dimension'] > 0) {
                            $gtm['item_variant'] = $item['spec_1_value'] . ($item['spec_2_value'] ? "_" . $item['spec_2_value'] : "");
                        } else {
                            $gtm['item_variant'] = "";
                        }
                        $data[$product->id][$item['id']] = $gtm;
                    }
                } else {
                    $gtm['item_id'] = $product_spec[$product->id][0]['item_no'];
                    $spec_info = "";
                    foreach ($product_spec[$product->id] as $item) {
                        if ($spec_info != "") {
                            $spec_info .= "、";
                        }
                        $spec_info .= $item['spec_1_value'] . ($item['spec_2_value'] ? "_" . $item['spec_2_value'] : "");
                    }
                    if ($item_spec['spec_dimension'] > 0) {
                        $gtm['item_variant'] = $spec_info;
                    } else {
                        $gtm['item_variant'] = "";
                    }
                    $gtm['price'] = intval($product->selling_price);
                    $gtm['quantity'] = 1;
                    $gtm['discount'] = "0";

                    $data[$product->id] = $gtm;
                }
            }

            return $data;
        } else {
            return 903;
        }
    }


    /**
     * 計算銀行分期手續費
     * @param Collection $collection
     * @param array $installment_info
     * @param int $paid_amount
     * @return Integer
     */
    public function getInstallmentFee(Collection $collection, array $installment_info, int $paid_amount): array
    {
        if ($collection->isEmpty()) {
            return [];
        }
        $interest_rate = $collection->where('issuing_bank_no', $installment_info['bank_id'])
            ->where('number_of_installments', $installment_info['number_of_installments'])
            ->first();
        $result = [
            "interest_rate" => $interest_rate->interest_rate,
            "interest_fee" => (int)round($paid_amount * $interest_rate->interest_rate / 100),
            "min_consumption" => $interest_rate->min_consumption,
        ];
        return $result;

    }

    /*
     * 取得商品item
     */
    public function getProductItems(): array
    {
        $productItem = ProductItem::where('status', 1)->orderBy('sort', 'asc')->get();
        foreach ($productItem as $item) {
            $productSpec[$item->product_id][] = $item;
        }
        return $productSpec;
    }

    /*
     * 用POS商品編號取得商品 
     */
    public function getProductByPosItemNo($posItemNo) {
        try{
            $result['status'] = 404;
            $result['result'] = null;
            $productItem = ProductItem::where('pos_item_no', $posItemNo)->where('status', 1)->first();
            if(isset($productItem)) {
                $product = Product::find($productItem->product_id);
                if (isset($product)) {
                    $result['status'] = 200;
                    $result['result'] = $product;                    
                }
            }
        } catch(Exception $e) {
            $result['status'] = 404;
            $result['result'] = null;
        }
        return $result;    
    }
}
