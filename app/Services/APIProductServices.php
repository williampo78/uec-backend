<?php


namespace App\Services;

use Carbon\Carbon;
use App\Models\ProductItem;
use App\Models\ProductPhoto;
use GuzzleHttp\Psr7\Request;
use App\Models\RelatedProduct;
use App\Services\APIWebService;
use App\Services\BrandsService;
use App\Services\APICartServices;
use App\Services\UniversalService;
use Illuminate\Support\Facades\DB;
use App\Models\PromotionalCampaign;
use Illuminate\Support\Facades\Auth;
use App\Services\WebShippingInfoService;
use App\Services\ShippingFeeRulesService;
use App\Services\WebCategoryHierarchyService;

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
        WebShippingInfoService $webShippingInfoService
    )
    {
        $this->apiWebCategory = $apiWebCategory;
        $this->apiWebService = $apiWebService;
        $this->apiCartService = $apiCartService;
        $this->brandsService = $brandsService;
        $this->shippingFeeService = $shippingFeeService;
        $this->universalService = $universalService;
        $this->webShippingInfoService = $webShippingInfoService;
    }

    public function getCategory($keyword = null)
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');

        //根據階層顯示層級資料
        if ($config_levels == '3') {
            $strSQL = "select cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.*, count(cate_prod.`product_id`) as pCount from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id`  and cate.`category_level`=3
                    inner join `frontend_products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at` and prod.product_type = 'N'";
            if ($keyword) {
                $strSQL .= " and (prod.product_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.product_no like '%" . $keyword . "%'";
                $strSQL .= " or cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.keywords like '%" . $keyword . "%'";
                $strSQL .= " or prod.supplier_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.brand_name like '%" . $keyword . "%'";
                $strSQL .= ")";
            }
            $strSQL .= " group by cate.`id`
                    order by cate2.`sort`, cate1.`sort`, cate.`sort`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, count(cate_prod.`product_id`) as pCount from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id` and cate.`category_level`=2
                    inner join `frontend_products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at` and prod.product_type = 'N' ";
            if ($keyword) {
                $strSQL .= " and (prod.product_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.product_no like '%" . $keyword . "%'";
                $strSQL .= " or cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.keywords like '%" . $keyword . "%'";
                $strSQL .= " or prod.supplier_name like '%" . $keyword . "%'";
                $strSQL .= " or prod.brand_name like '%" . $keyword . "%'";
                $strSQL .= ")";
            }
            $strSQL .= " group by cate.`id`
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
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['count'] = $category->pCount;

            } else if ($config_levels == '2') {

                $L2_data[$category->L1ID][$category->id]['id'] = $category->id;
                $L2_data[$category->L1ID][$category->id]['name'] = $category->category_name;
                $L2_data[$category->L1ID][$category->id]['type'] = $category->content_type;
                $L2_data[$category->L1ID][$category->id]['meta_title'] = $category->meta_title;
                $L2_data[$category->L1ID][$category->id]['meta_description'] = $category->meta_description;
                $L2_data[$category->L1ID][$category->id]['meta_keywords'] = $category->meta_keywords;
                $L2_data[$category->L1ID][$category->id]['count'] = $category->pCount;
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
                            $data3[$key3]["count"] = $value3["count"];
                            $data3[$key3]["meta_title"] = $value3["meta_title"];
                            $data3[$key3]["meta_description"] = $value3["meta_description"];
                            $data3[$key3]["meta_keywords"] = $value3["meta_keywords"];
                        }
                        $data2[$key2]["cateInfo"] = $data3;
                    } elseif ($config_levels == 2) {
                        $data2[$key2]["count"] = $value2["count"];
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
    public function getProducts($product_id = null)
    {
        $strSQL = "SELECT p.*,
                    (SELECT photo_name
                     FROM product_photos
                     WHERE p.id = product_photos.product_id order by sort limit 0, 1) AS displayPhoto
                    FROM products AS p
                    where p.approval_status = 'APPROVED'
                    and current_timestamp() between p.start_launched_at and p.end_launched_at ";
        if ($product_id) {
            $strSQL .= " and p.id=" . $product_id;
        }
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
    public function getWebCategoryProducts($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null, $id = null, $order_by = null, $sort_flag = null)
    {

        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');
        $strSQL = "select web_category_products.web_category_hierarchy_id";
        if ($config_levels == 3) {
            $strSQL .= ",cate1.category_name L3, cate2.category_name L2, cate3.category_name L1";
        } else {
            $strSQL .= ",cate1.category_name L2, cate2.category_name L1";
        }
        $strSQL .= ",p.*,
                    (SELECT photo_name
                    FROM product_photos
                    WHERE p.id = product_photos.product_id order by sort limit 0, 1) AS displayPhoto
                    from web_category_products
                    inner join frontend_products_v p on p.id=web_category_products.product_id
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=web_category_products.`web_category_hierarchy_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id` ";
        if ($config_levels == 3) {
            $strSQL .= " inner join `web_category_hierarchy` cate3 on cate3.`id`=cate2.`parent_id` ";
        }
        $strSQL .= " where p.approval_status = 'APPROVED' and current_timestamp() between p.start_launched_at and p.end_launched_at and p.product_type = 'N' ";

        if ($keyword) {//依關鍵字搜尋
            $strSQL .= " and (p.product_name like '%" . $keyword . "%'";
            $strSQL .= " or p.product_no like '%" . $keyword . "%'";
            $strSQL .= " or p.keywords like '%" . $keyword . "%'";
            $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
            $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
            $strSQL .= " or p.supplier_name like '%" . $keyword . "%'";
            $strSQL .= " or p.brand_name like '%" . $keyword . "%'";
            if ($config_levels == 3) {
                $strSQL .= " or cate3.category_name like '%" . $keyword . "%'";
            }
            $strSQL .= ")";
        }

        if ($selling_price_min >= 0 && $selling_price_max > 0) {//價格區間
            $strSQL .= " and p.selling_price between " . $selling_price_min . " and " . $selling_price_max;
        }

        if ($category) {//依分類搜尋
            $strSQL .= " and web_category_products.web_category_hierarchy_id in (" . $category . ")";
        }

        if ($id) {//依產品編號找相關分類
            $strSQL .= " and web_category_products.product_id=" . $id;
            $strSQL .= " order by web_category_products.sort ";
        }

        if ($order_by == 'launched') {
            $strSQL .= " order by p.start_launched_at " . $sort_flag . ", p.id";
        } else if ($order_by == 'price') {
            $strSQL .= " order by p.selling_price " . $sort_flag . ", p.id";
        }
        $products = DB::select($strSQL);
        $data = [];
        $product_id = 0;
        foreach ($products as $product) {
            if (!$id) {//依產品編號找相關分類不進此判斷
                if ($product->id == $product_id) continue;
            }
            $data[$product->web_category_hierarchy_id][] = $product;
            $product_id = $product->id;
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
        $keyword = $input['keyword'];
        $category = $input['category'];
        $size = $input['size'];
        $page = $input['page'];
        $selling_price_min = $input['price_min'];
        $selling_price_max = $input['price_max'];
        $order_by = ($input['order_by'] ? $input['order_by'] : 'launched');
        $sort_flag = ($input['sort'] ? $input['sort'] : 'DESC');
        //$sort_flag = $input['sort'] == 'ASC' ? SORT_ASC : SORT_DESC;
        $products = self::getWebCategoryProducts($category, $selling_price_min, $selling_price_max, $keyword, null, $order_by, $sort_flag);
        if ($products) {
            $promotion = self::getPromotion('product_card');
            foreach ($promotion as $k => $v) {
                $promotion_txt = '';
                foreach ($v as $label) {
                    if ($promotion_txt != $label->promotional_label) {
                        $promotional[$k][] = $label->promotional_label;
                        $promotion_txt = $label->promotional_label;
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
                    //$response = $this->apiCartService->getCartInfo($member_id);
                    //$is_cart = json_decode($response, true);
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
                    /*加入購物車未定版，待確認後資料要改成取 shopping_cart_details => item_id
                    if (isset($is_cart)) {
                        foreach ($is_cart as $k => $v) {
                            if ($v['product_id'] == $product->id) {
                                $cart = true;
                            } else {
                                $cart = false;
                            }
                        }
                    }*/
                    if ($product->id != $product_id) {
                        $data[] = array(
                            'product_id' => $product->id,
                            'product_no' => $product->product_no,
                            'product_name' => $product->product_name,
                            'selling_price' => intval($product->selling_price),
                            'product_discount' => intval($discount),
                            'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                            'promotion_desc' => $promotion_desc,
                            'promotion_label' => (isset($promotional[$product->id]) ? $promotional[$product->id] : null),
                            'collections' => $collection,
                            'cart' => $cart
                        );

                        $product_id = $product->id;
                    }

                }
            }
            //array_multisort(array_column($data, 'selling_price'), $sort_flag, $data);
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
                where current_timestamp() between pc.start_at and pc.end_at and pc.active=1 order by pcp.product_id";

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
    public function getSearchResultForCategory($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null)
    {

        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');

        $strSQL = "select cate1.id , cate1.category_name ,count(cate1.id) as count";
        if ($config_levels == 3) {
            $strSQL .= ",cate2.id as L2, cate2.category_name as L2_Name,cate3.id as L1, cate3.category_name as L1_Name ";
        } else {
            $strSQL .= ",cate2.id as L1, cate2.category_name as L1_Name ";
        }

        $strSQL .= "from web_category_products
                    inner join frontend_products_v p on p.id=web_category_products.product_id
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=web_category_products.`web_category_hierarchy_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id` ";
        if ($config_levels == 3) {
            $strSQL .= " inner join `web_category_hierarchy` cate3 on cate3.`id`=cate2.`parent_id` ";
        }

        $strSQL .= " where p.approval_status = 'APPROVED' and current_timestamp() between p.start_launched_at and p.end_launched_at and p.product_type = 'N'";

        if ($keyword) {//依關鍵字搜尋
            $strSQL .= " and (p.product_name like '%" . $keyword . "%'";
            $strSQL .= " or p.product_no like '%" . $keyword . "%'";
            $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
            $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
            $strSQL .= " or p.keywords like '%" . $keyword . "%'";
            $strSQL .= " or p.supplier_name like '%" . $keyword . "%'";
            $strSQL .= " or p.brand_name like '%" . $keyword . "%'";
            if ($config_levels == 3) {
                $strSQL .= " or cate3.category_name like '%" . $keyword . "%'";
            }
            $strSQL .= ")";
        }
        if ($selling_price_min >= 0 && $selling_price_max > 0) {//價格區間
            $strSQL .= " and p.selling_price between " . $selling_price_min . " and " . $selling_price_max;
        }

        if ($category) {//依分類搜尋
            $strSQL .= " and web_category_products.web_category_hierarchy_id in (" . $category . ")";
        }
        $strSQL .= " group by cate1.id";
        if ($config_levels == 2) {
            $strSQL .= " order by cate2.sort, cate1.sort";
        } else {
            $strSQL .= " order by cate3.sort, cate2.sort, cate1.sort";
        }
        $products = DB::select($strSQL);
        if ($products) {
            $data = [];
            $product_id = 0;
            foreach ($products as $category) {
                if ($config_levels == 2) {
                    $sub[$category->L1][] = array(
                        'id' => $category->id,
                        'name' => $category->category_name,
                        'count' => $category->count
                    );
                } elseif ($config_levels == 3) {
                    $sub[$category->L2][$category->L2][] = array(
                        'id' => $category->id,
                        'name' => $category->category_name,
                        'count' => $category->count
                    );
                }
            }
            $cate = 0;
            if ($config_levels == 2) {
                foreach ($products as $category) {
                    if ($cate == $category->L1) continue;
                    $data[] = array(
                        'id' => $category->L1,
                        'name' => $category->L1_Name,
                        'sub' => $sub[$category->L1]
                    );
                    $cate = $category->L1;
                }
            } elseif ($config_levels == 3) {
                $subCate = 0;
                foreach ($products as $category) {
                    if ($subCate == $category->L2) continue;
                    $main[] = array(
                        'id' => $category->L2,
                        'name' => $category->L2_Name,
                        'sub' => $sub[$category->L1][$category->L2]
                    );
                    $subCate = $category->L2;
                }
                foreach ($products as $category) {
                    if ($cate == $category->L1) continue;
                    $data[] = array(
                        'id' => $category->L2,
                        'name' => $category->L2_Name,
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
            $payment_way = "TAPPAY_CREDITCARD,TAPPAY_LINEPAY";//本階段沒有欄位寫固定的付款方式
            //if ($product[$id]->payment_method != '') {
            $methods = explode(',', $payment_way);
            foreach ($methods as $method) {
                $payment_method[] = $payment_text[$method];
            }
            //}

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
                "promotion_label" => $promotional
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
            foreach ($promotions as $category => $promotion) {
                foreach ($promotion as $item) {
                    if ($item->product_id == $id) {
                        $promotion_type[($category == 'GIFT' ? '贈品' : '優惠')][] = array(
                            "campaign_id" => $item->id,
                            "campaign_name" => $item->campaign_name,
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
            $ProductSpec = ProductItem::where('product_id', $id)->orderBy('sort', 'asc')->get();
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
                    "itme_id" => $item['id'],
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
                        foreach ($promotion[$rel->related_product_id] as $k => $Label) { //取活動標籤
                            $promotional[] = $Label->promotional_label;
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
                    );

                }
            }
            $data['rel_product'] = $rel_data;

            return json_encode($data);
        } else {
            return 201;
        }
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
        $promotional = PromotionalCampaign::select("promotional_campaign_giveaways.promotional_campaign_id", "promotional_campaign_giveaways.product_id", "promotional_campaign_giveaways.assigned_qty as assignedQty", "promotional_campaigns.*", "products.start_launched_at", "products.end_launched_at", "products.product_name", "products.selling_price")
            ->where("promotional_campaigns.start_at", "<=", $now)
            ->where("promotional_campaigns.end_at", ">=", $now)
            ->where("promotional_campaigns.active", "=", "1")
            ->join('promotional_campaign_giveaways', 'promotional_campaign_giveaways.promotional_campaign_id', '=', 'promotional_campaigns.id')
            ->join('products', 'products.id', '=', 'promotional_campaign_giveaways.product_id')
            ->where('products.approval_status', '=', 'APPROVED')->get();
        foreach ($promotional as $promotion) {
            $productPhotos = ProductPhoto::where('product_id', $promotion->product_id)->orderBy('sort', 'asc')->first();
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id] = $promotion; //取單品的贈品
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id]['photo'] = (isset($productPhotos->photo_name) ? $s3 . $productPhotos->photo_name : null);
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
                where lov.active =1
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
                foreach ($gifts['PROD'][$campaign_id] as $gift) {
                    if ($now >= $gift->start_at && $now <= $gift->end_at) {
                        $giftAway[] = array(
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
            $result['status'] = 401;
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
            $strSQL = "select cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.*, count(cate_prod.`product_id`) as pCount from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id`  and cate.`category_level`=3
                    inner join `frontend_products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at` and prod.product_type = 'N'";
            if ($category) {
                $strSQL .= " and cate.`id`=" . $category;
            }
            $strSQL .= " group by cate.`id`
                    order by cate2.`sort`, cate1.`sort`, cate.`sort`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, count(cate_prod.`product_id`) as pCount from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id` and cate.`category_level`=2
                    inner join `frontend_products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at` and prod.product_type = 'N' ";
            if ($category) {
                $strSQL .= " and cate.`id`=" . $category;
            }
            $strSQL .= " group by cate.`id`
                    order by cate1.`sort`, cate.`sort`";
        }
        $categorys = DB::select($strSQL);
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
}
