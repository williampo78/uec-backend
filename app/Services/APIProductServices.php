<?php


namespace App\Services;

use App\Services\APIWebService;
use App\Services\WebCategoryHierarchyService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\APICartServices;

class APIProductServices
{

    private $apiWebCategory;
    private $apiWebService;
    private $apiCartService;

    public function __construct(WebCategoryHierarchyService $apiWebCategory, APIWebService $apiWebService, APICartServices $apiCartService)
    {
        $this->apiWebCategory = $apiWebCategory;
        $this->apiWebService = $apiWebService;
        $this->apiCartService = $apiCartService;
    }

    public function getCategory()
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');

        //根據階層顯示層級資料
        if ($config_levels == '3') {
            $strSQL = "select cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.* from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id`  and cate.`category_level`=3
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at`
                    group by cate.`id`
                    order by cate2.`sort`, cate1.`sort`, cate.`sort`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.* from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id` and cate.`category_level`=2
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at`
                    group by cate.`id`
                    order by cate1.`sort`, cate.`sort`";
        }
        $categorys = DB::select($strSQL);
        foreach ($categorys as $category) {

            $L1_data[$category->L1ID]["id"] = $category->L1ID;
            $L1_data[$category->L1ID]["name"] = $category->L1_NAME;

            if ($config_levels == '3') {
                $L2_data[$category->L1ID][$category->L2ID]["id"] = $category->L2ID;
                $L2_data[$category->L1ID][$category->L2ID]["name"] = $category->L2_NAME;

                $L3_data[$category->L1ID][$category->L2ID][$category->id]['id'] = $category->id;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['name'] = $category->category_name;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['type'] = $category->content_type;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_title'] = $category->meta_title;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_description'] = $category->meta_description;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_keywords'] = $category->meta_keywords;

            } else if ($config_levels == '2') {

                $L2_data[$category->L1ID][$category->id]['id'] = $category->id;
                $L2_data[$category->L1ID][$category->id]['name'] = $category->category_name;
                $L2_data[$category->L1ID][$category->id]['type'] = $category->content_type;
                $L2_data[$category->L1ID][$category->id]['meta_title'] = $category->meta_title;
                $L2_data[$category->L1ID][$category->id]['meta_description'] = $category->meta_description;
                $L2_data[$category->L1ID][$category->id]['meta_keywords'] = $category->meta_keywords;
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
                foreach ($L2_data[$key1] as $key2 => $value2) {
                    $data2[$key2]["id"] = $value2["id"];
                    $data2[$key2]["name"] = $value2["name"];
                    if ($config_levels == 3) {
                        $data3 = [];
                        foreach ($L3_data[$key1][$key2] as $key3 => $value3) {
                            $data3[$key3]["id"] = $value3["id"];
                            $data3[$key3]["name"] = $value3["name"];
                            $data3[$key3]["type"] = $value3["type"];
                            $data3[$key3]["meta_title"] = $value3["meta_title"];
                            $data3[$key3]["meta_description"] = $value3["meta_description"];
                            $data3[$key3]["meta_keywords"] = $value3["meta_keywords"];
                        }
                        $data2[$key2]["cateInfo"] = $data3;
                    } elseif ($config_levels == 2) {
                        $data2[$key2]["type"] = $value2["type"];
                        $data2[$key2]["meta_title"] = $value2["meta_title"];
                        $data2[$key2]["meta_description"] = $value2["meta_description"];
                        $data2[$key2]["meta_keywords"] = $value2["meta_keywords"];
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
    public function getProducts()
    {
        $strSQL = "SELECT p.*,
                    (SELECT photo_name
                     FROM product_photos
                     WHERE p.id = product_photos.product_id order by sort limit 0, 1) AS displayPhoto
                    FROM products AS p
                    where p.approval_status = 'APPROVED'
                    and current_timestamp() between p.start_launched_at and p.end_launched_at ";
        $products = DB::select($strSQL);
        $data = [];
        foreach ($products as $product) {
            $data[$product->id] = $product;
        }
        return $data;
    }

    /*
     * 取得分類總覽的商品資訊 (上架審核通過 & 上架期間內)
     */
    public function getWebCategoryProducts($category = null, $selling_price_min = null, $selling_price_max = null)
    {
        $strSQL = "select web_category_products.web_category_hierarchy_id, p.*,
                    (SELECT photo_name
                    FROM product_photos
                    WHERE p.id = product_photos.product_id order by sort limit 0, 1) AS displayPhoto
                    from web_category_products
                    inner join products p on p.id=web_category_products.product_id
                    where p.approval_status = 'APPROVED' and current_timestamp() between p.start_launched_at and p.end_launched_at ";
        if ($category) {
            $strSQL .= "and web_category_products.web_category_hierarchy_id in (" . $category . ")";
        }
        if ($selling_price_min > 0 && $selling_price_max > 0) {
            $strSQL .= "and p.selling_price between " . $selling_price_min . " and " . $selling_price_max;
        }
        $products = DB::select($strSQL);
        $data = [];
        foreach ($products as $product) {
            $data[$product->web_category_hierarchy_id][] = $product;
        }
        return $data;
    }

    /*
     * 分類總覽的商品搜尋結果 (上架審核通過 & 上架期間內 & 登入狀態)
     */
    public function categorySearchResult($input)
    {
        $now = Carbon::now();
        $s3 = config('filesystems.disks.s3.url');
        $category = $input['category'];
        $size = $input['size'];
        $page = $input['page'];
        $selling_price_min = $input['price_min'];
        $selling_price_max = $input['price_max'];
        $products = self::getWebCategoryProducts($category, $selling_price_min, $selling_price_max);
        if ($products) {
            $promotion = self::getPromotion('product_card');
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
                    $response = $this->apiCartService->getCartInfo($member_id);
                    $is_cart = json_decode($response, true);
                }
            }

            $product_id = 0;
            foreach ($products as $cateID => $prod) {
                foreach ($prod as $product) {
                    $promotional = [];
                    if ($now >= $product->promotion_start_at && $now <= $product->promotion_end_at) {
                        $promotion_desc = $product->promotion_desc;
                    } else {
                        $promotion_desc = null;
                    }
                    $discount = ($product->list_price == 0 ? 0 : ceil(($product->selling_price / $product->list_price) * 100));

                    if (isset($promotion[$product->id])) {
                        foreach ($promotion[$product->id] as $k => $Label) { //取活動標籤
                            $promotional[] = $Label->promotional_label;
                        }
                    }

                    if (isset($is_collection)) {
                        foreach ($is_collection as $k => $v) {
                            if ($v['product_id'] == $product->id) {
                                $collection = true;
                            } else {
                                $collection = false;
                            }
                        }
                    }
                    if (isset($is_cart)) {
                        foreach ($is_cart as $k => $v) {
                            if ($v['product_id'] == $product->id) {
                                $cart = true;
                            } else {
                                $cart = false;
                            }
                        }
                    }
                    if ($product->id != $product_id) {
                        $data[] = array(
                            'product_id' => $product->id,
                            'product_no' => $product->product_no,
                            'product_name' => $product->product_name,
                            'selling_price' => intval($product->selling_price),
                            'product_discount' => intval($discount),
                            'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                            'promotion_desc' => $promotion_desc,
                            'promotion_label' => (count($promotional) > 0 ? $promotional : null),
                            'collections' => $collection,
                            'cart' => $cart
                        );

                        $product_id = $product->id;
                    }


                }
            }
            array_multisort(array_column($data, 'selling_price'), SORT_DESC, $data);
            $searchResult = self::getPages($data, $size, $page);
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

        for ($i = $startRow; $i <= $endRow; $i++) {
            $list[] = $data[$i];
        }

        $result = array('totalRows' => $totalRows, 'totalPages' => $totalPages, 'currentPage' => $currentPage, 'list' => $list);
        return $result;

    }

    /*
     * 取得行銷促案資訊
     */
    public function getPromotion($type)
    {
        $strSQL = "select pcp.product_id, pc.*
                from promotional_campaigns pc
                inner join  promotional_campaign_products pcp on pcp.promotional_campaign_id=pc.id
                where current_timestamp() between pc.start_at and pc.end_at and pc.active=1 ";

        $promotional = DB::select($strSQL);
        $data = [];
        foreach ($promotional as $promotion) {
            //if ($type == 'product_card')
            $data[$promotion->product_id][] = $promotion;
        }
        return $data;

    }
}
