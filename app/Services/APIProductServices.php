<?php


namespace App\Services;

use App\Models\ProductPhotos;
use App\Models\ProductItems;
use App\Models\PromotionalCampaigns;
use App\Services\APIWebService;
use App\Services\WebCategoryHierarchyService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\APICartServices;
use App\Services\BrandsService;
use App\Services\ShippingFeeRulesService;
use App\Services\UniversalService;
use App\Services\WebShippingInfoService;

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
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at`";
            if ($keyword) {
                $strSQL .= " and (prod.product_name like '%" . $keyword . "%'";
                $strSQL .= " or cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                if ($config_levels == 3) {
                    $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
                }
                $strSQL .= ")";
            }
            $strSQL .= " group by cate.`id`
                    order by cate2.`sort`, cate1.`sort`, cate.`sort`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.*, count(cate_prod.`product_id`) as pCount from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id` and cate.`category_level`=2
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    where cate.`active`=1
                    and current_timestamp() between prod.`start_launched_at` and prod.`end_launched_at` ";
            if ($keyword) {
                $strSQL .= " and (prod.product_name like '%" . $keyword . "%'";
                $strSQL .= " or cate.category_name like '%" . $keyword . "%'";
                $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
                if ($config_levels == 3) {
                    $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
                }
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
    public function getWebCategoryProducts($category = null, $selling_price_min = null, $selling_price_max = null, $keyword = null, $id = null)
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
                    inner join products p on p.id=web_category_products.product_id
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=web_category_products.`web_category_hierarchy_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id` ";
        if ($config_levels == 3) {
            $strSQL .= " inner join `web_category_hierarchy` cate3 on cate3.`id`=cate2.`parent_id` ";
        }
        $strSQL .= " where p.approval_status = 'APPROVED' and current_timestamp() between p.start_launched_at and p.end_launched_at ";

        if ($keyword) {//依關鍵字搜尋
            $strSQL .= " and (p.product_name like '%" . $keyword . "%'";
            $strSQL .= " or p.product_no like '%" . $keyword . "%'";
            $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
            $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
            if ($config_levels == 3) {
                $strSQL .= " or cate3.category_name like '%" . $keyword . "%'";
            }
            $strSQL .= ")";
        }

        if ($selling_price_min > 0 && $selling_price_max > 0) {//價格區間
            $strSQL .= " and p.selling_price between " . $selling_price_min . " and " . $selling_price_max;
        }

        if ($category) {//依分類搜尋
            $strSQL .= " and web_category_products.web_category_hierarchy_id in (" . $category . ")";
        }

        if ($id) {//依產品編號找相關分類
            $strSQL .= " and web_category_products.product_id=" . $id;
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
        $sort_flag = $input['sort'] == 'ASC' ? SORT_ASC : SORT_DESC;
        $products = self::getWebCategoryProducts($category, $selling_price_min, $selling_price_max, $keyword);
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
                    //$response = $this->apiCartService->getCartInfo($member_id);
                    //$is_cart = json_decode($response, true);
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
                            'promotion_label' => (count($promotional) > 0 ? $promotional : null),
                            'collections' => $collection,
                            'cart' => $cart
                        );

                        $product_id = $product->id;
                    }

                }
            }
            array_multisort(array_column($data, 'selling_price'), $sort_flag, $data);
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
        $strSQL = "select cate1.id, cate1.category_name
                    from web_category_products
                    inner join products p on p.id=web_category_products.product_id
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=web_category_products.`web_category_hierarchy_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id` ";
        if ($config_levels == 3) {
            $strSQL .= " inner join `web_category_hierarchy` cate3 on cate3.`id`=cate2.`parent_id` ";
        }

        $strSQL .= " where p.approval_status = 'APPROVED' and current_timestamp() between p.start_launched_at and p.end_launched_at ";

        if ($keyword) {//依關鍵字搜尋
            $strSQL .= " and (p.product_name like '%" . $keyword . "%'";
            $strSQL .= " or cate1.category_name like '%" . $keyword . "%'";
            $strSQL .= " or cate2.category_name like '%" . $keyword . "%'";
            if ($config_levels == 3) {
                $strSQL .= " or cate3.category_name like '%" . $keyword . "%'";
            }
            $strSQL .= ")";
        }
        if ($selling_price_min > 0 && $selling_price_max > 0) {//價格區間
            $strSQL .= " and p.selling_price between " . $selling_price_min . " and " . $selling_price_max;
        }

        if ($category) {//依分類搜尋
            $strSQL .= " and web_category_products.web_category_hierarchy_id in (" . $category . ")";
        }
        $products = DB::select($strSQL);
        if ($products) {
            $data = [];
            $product_id = 0;
            foreach ($products as $product) {
                if ($product_id == $product->id) continue;
                $data[] = array("category_id" => $product->id, 'category_name' => $product->category_name);
                $product_id = $product->id;
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
            $product_categorys = self::getWebCategoryProducts('', '', '', '', $id);

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
            $ProductPhotos = ProductPhotos::where('product_id', $id)->orderBy('sort', 'asc')->get();
            if (isset($ProductPhotos)) {
                foreach ($ProductPhotos as $photo) {
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
                            "campaign_name" => $item->campaign_name
                        );
                    }
                }
            }
            $data['campaignInfo'] = $promotion_type;


            //產品規格
            $item_spec = [];
            $ProductSpec = ProductItems::where('product_id', $id)->orderBy('sort', 'asc')->get();
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
            $item_spec['spec_1'] = ($item_spec['spec_1'] ? array_unique($item_spec['spec_1']) : null);
            $item_spec['spec_2'] = ($item_spec['spec_2'] ? array_unique($item_spec['spec_2']) : null);
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

            //認證標章
            $icon = [];
            $certificate = $this->getCertificateIcon();
            foreach ($certificate as $item) {
                if ($item->product_id == $id) {
                    $icon[] = array("icon" => $s3 . $item->photo_name);
                }
            }
            $data['certificate'] = $icon;

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
        $promotional = PromotionalCampaigns::select("promotional_campaign_giveaways.promotional_campaign_id", "promotional_campaign_giveaways.product_id", "promotional_campaign_giveaways.assigned_qty as assignedQty", "promotional_campaigns.*", "products.start_launched_at", "products.end_launched_at", "products.product_name")
            ->where("promotional_campaigns.start_at", "<=", $now)
            ->where("promotional_campaigns.end_at", ">=", $now)
            ->where("promotional_campaigns.active", "=", "1")
            ->join('promotional_campaign_giveaways', 'promotional_campaign_giveaways.promotional_campaign_id', '=', 'promotional_campaigns.id')
            ->join('products', 'products.id', '=', 'promotional_campaign_giveaways.product_id')
            ->where('products.approval_status', '=', 'APPROVED')->get();
        foreach ($promotional as $promotion) {
            $ProductPhotos = ProductPhotos::where('product_id', $promotion->product_id)->orderBy('sort', 'asc')->first();
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id] = $promotion; //取單品的贈品
            $data['PROD'][$promotion->promotional_campaign_id][$promotion->product_id]['photo'] = (isset($ProductPhotos->photo_name) ? $s3 . $ProductPhotos->photo_name : null);
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
        $promotional = PromotionalCampaigns::where('active', '=', '1')
            ->where("start_at", "<=", $now)
            ->where("end_at", ">=", $now)
            ->where("level_code", '=', 'CART')->get();
        return $promotional;
    }

    /*
     * 取得產品認證標章
     * @param $id
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

}
