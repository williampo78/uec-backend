<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductPhoto;
use App\Models\PromotionalCampaignGiveaway;
use App\Models\PromotionalCampaignThreshold;
use App\Models\ShoppingCartDetail;
use App\Models\PromotionalCampaign;
use App\Services\APIService;
use App\Services\StockService;
use Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APICartServices
{

    public function __construct(APIService $apiService, StockService $stockService)
    {
        $this->apiService = $apiService;
        $this->stockService = $stockService;
    }

    /*
     * 取得購物車內容
     *
     */
    public function getCartInfo($member_id)
    {
        $s3 = config('filesystems.disks.s3.url');
        $result = ShoppingCartDetail::select("products.id as product_id", "products.product_no", "products.product_name", "products.list_price", "products.selling_price", "products.start_launched_at", "products.end_launched_at"
            , "product_items.id as item_id", "shopping_cart_details.qty as item_qty", "product_items.spec_1_value as item_spec1", "product_items.spec_2_value as item_spec2"
            , "product_items.item_no", "product_items.photo_name as item_photo")
            ->join('product_items', 'product_items.id', '=', 'shopping_cart_details.product_item_id')
            ->join('products', 'products.id', '=', 'product_items.product_id')
            ->where('shopping_cart_details.status_code', 0) //購物車
            ->where('shopping_cart_details.member_id', $member_id);
        $result = $result->where(function ($query) {
            $query->where('products.approval_status', '=', 'APPROVED'); //核準上架
            $query->orWhere('products.approval_status', '=', 'CANCELLED'); //被下架
        });
        $result = $result->orderBy('shopping_cart_details.latest_added_at', 'desc')->get();
        $data = [];
        foreach ($result as $datas) {
            //商品圖以規格圖為主為空，則取商品封面圖
            $itemPhoto = empty($datas->item_photo) ? optional(ProductPhoto::where('product_id', $datas->product_id)->orderBy('sort', 'asc')->first())->photo_name : $datas->item_photo;
            $itemPhoto = empty($itemPhoto) ? null : $s3 . $itemPhoto;

            $data[$datas->product_id] = $datas;
            $data[$datas->product_id]['item_photo'] = $itemPhoto;
            $data['items'][$datas->product_id][$datas->item_id] = $datas;
        }
        return $data;
    }

    /*
     * 取得會員目前購物車數量
     * @params:member_id
     */
    public function getCartCount($member_id)
    {
        $wordCount = ShoppingCartDetail::where('member_id', '=', $member_id)
            ->where('status_code', '=', 0)->count();
        return $wordCount;
    }

    /**
     * 會員購物車(新增/刪除/編輯)
     * @param
     * @return string
     */
    public function setMemberCart($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        //確認是否有該品項
        $item = ProductItem::where('id', $input['item_id'])->get()->toArray();
        if (count($item) > 0) {
            $data = ShoppingCartDetail::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->get()->toArray();
            if (count($data) > 0) {
                $act = 'upd';
            } else {
                $act = 'add';
            }
        } else {
            return '401';
        }

        DB::beginTransaction();
        try {
            $webData = [];
            if ($act == 'add') {
                $webData['member_id'] = $member_id;
                $webData['product_id'] = $item[0]['product_id'];
                $webData['product_item_id'] = $input['item_id'];
                $webData['status_code'] = $input['status_code'];
                $webData['qty'] = $input['item_qty'];
                $webData['utm_source'] = $input['utm_source'];
                $webData['utm_medium'] = $input['utm_medium'];
                $webData['utm_campaign'] = $input['utm_campaign'];
                $webData['utm_sales'] = $input['utm_sales'];
                $webData['latest_added_at'] = $now;
                $webData['utm_time'] = Carbon::createFromTimestamp($input['utm_time'])->format('Y-m-d H:i:s');
                $webData['created_by'] = $member_id;
                $webData['updated_by'] = -1;
                $webData['created_at'] = $now;
                $webData['updated_at'] = $now;
                if ($input['status_code'] != 0) {
                    return '203';
                }
                $new_id = ShoppingCartDetail::insertGetId($webData);
            } else if ($act == 'upd') {

                $webData['product_id'] = $item[0]['product_id'];
                $webData['qty'] = ($input['status_code'] == 0 ? $input['item_qty'] : 0);
                $webData['status_code'] = $input['status_code'];

                //如果資料原本的狀態為1(已轉為訂單)或-1(已自購物車刪除)，則要更新時間
                if ($data[0]['status_code'] == 1 || $data[0]['status_code'] == -1) {
                    $webData['latest_added_at'] = $now;
                }

                $webData['updated_by'] = $member_id;
                $webData['updated_at'] = $now;
                $new_id = ShoppingCartDetail::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->update($webData);
            }
            DB::commit();
            if ($new_id > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }

    /*
     * 取得購物車內容
     *
     */
    public function getCartData($member_id, $campaigns, $campaign_gift, $campaign_discount, $gtm = null)
    {
        if (config('uec.cart_p_discount_split') == 1) return $this->getCartDataV2($member_id, $campaigns, $campaign_gift, $campaign_discount, $gtm);

        $now = Carbon::now();
        //購物車內容
        $cartInfo = self::getCartInfo($member_id);
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $shippingFee = ShippingFeeRulesService::getShippingFee('HOME');
        $feeInfo = array(
            "shipping_fee" => $shippingFee['HOME']->shipping_fee,
            "free_threshold" => $shippingFee['HOME']->free_threshold,
            "notice" => $shippingFee['HOME']->notice_brief,
            "noticeDetail" => $shippingFee['HOME']->notice_detailed,
        );
        if (count($cartInfo) == 0) {
            return json_encode(array("status" => 404, "result" => $feeInfo));
        } else {
            $stock_gift_check = $this->stockService->getProductInStock($warehouseCode);
            $cartQty = [];
            $cartAmount = [];
            $cartDetail = [];
            $cartTotal = 0;
            $product = [];
            $productDetail = [];
            $CART04 = [];
            $CART04_n = [];
            $cartGift = [];
            $assigned = [];
            foreach ($cartInfo as $items => $item) {
                if ($items == 'item_photo') {
                    continue;
                }

                if ($items == 'items') {
                    continue;
                }

                $cartQty[$items][$item['item_id']] = $item->item_qty; //購物車數量
                $cartAmount[$items] = round($item->selling_price); //商品售價
                //$cartDetail[$items][$item['item_id']] = $item; //購物車內容
            }
            $prodQty = [];
            foreach ($cartInfo['items'] as $prdouct_id => $items) {
                foreach ($items as $item_id => $item) {
                    $cartDetail[$prdouct_id][$item_id] = $item; //購物車內容
                    $prodQty[$prdouct_id][$item_id] = $item['item_qty'];
                }
            }
            //行銷活動
            foreach ($campaigns as $product_id => $item) {
                foreach ($item as $k => $v) {
                    if ($now >= $v->start_at and $now <= $v->end_at) { //活動時間內才做
                        //先取活動類型為單品或車子(贈品或折扣) $campaign['PRD']['DISCOUNT'] 單品折扣
                        $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                        if ($v->campaign_type == 'CART04') {
                            $CART04[$v->id][$product_id] = 1;
                            $CART04_n[$v->id] = $v->n_value; //CART04 同系列商品滿額條件
                        }
                    }
                }
            }
            //活動滿額門檻資料 (活動時間內才做)
            $campaignThreshold = [];
            $campaignThresholdItem = [];
            $campaignThresholdGift = [];
            if (isset($campaign['CART_P'])) {
                foreach ($campaign['CART_P'] as $type => $items) {
                    foreach ($items as $product_id => $data) {
                        $campaignThreshold_brief = [];
                        $campaignThreshold_item = [];
                        $campaignThresholds = PromotionalCampaignThreshold::where('promotional_campaign_id', $data->id)->orderBy('n_value')->get();
                        foreach ($campaignThresholds as $threshold) {
                            $campaignThreshold_brief[] = $threshold->threshold_brief;
                            $campaignThreshold_item[] = $threshold;
                            $thresholdGift = PromotionalCampaignThreshold::find($threshold->id)->promotionalCampaignGiveaways;
                            $campaignThresholdGift[$data->id][$threshold->id][] = $thresholdGift;

                        }
                        //畫面顯示用
                        $campaignThreshold[$type][$product_id] = array(
                            "campaignId" => $data->id,
                            "campaignName" => $data->campaign_name,
                            "campaignBrief" => $data->campaign_brief,
                            "campaignUrlCode" => $data->url_code,
                            "campaignThreshold" => $campaignThreshold_brief
                        );
                        //滿額計算用
                        $campaignThresholdItem[$data->id] = $campaignThreshold_item;//活動門檻資料
                        $campaignThresholdMain[$data->id] = $data; //活動主檔
                    }
                }
            }
            //活動滿額門檻資料 (活動時間內才做)
            //重組活動贈品
            $threshold_prod = [];
            foreach ($campaign_gift['PROD'] as $campaign_id => $item) {
                foreach ($item as $product_id => $data) {
                    if ($now >= $data['start_launched_at'] && $now <= $data['end_launched_at']) {
                        $threshold_prod[$product_id] = $data;
                    }
                }
            }
            $productRow = 0;
            $cartDiscount = 0;
            $prod_campaign = [];//活動下的單品
            foreach ($cartQty as $product_id => $item) {
                $product = [];
                $prod_gift = [];
                $prod_amount[$product_id] = 0;
                $prod_qty[$product_id] = 0;
                if ($now >= $cartInfo[$product_id]['start_launched_at'] && $now <= $cartInfo[$product_id]['end_launched_at']) { //在上架期間內
                    $product_type = "effective";
                    $qty = array_sum($prodQty[$product_id]); //合併不同規格但同一商品的數量
                    //商品贈品
                    $giftAway = [];
                    $giftCount = 0;
                    $giftCheck = 0;
                    $giftCalc = 0;
                    if (isset($campaign['PRD']['GIFT'][$product_id])) { //在活動內 滿額贈禮
                        if ($campaign['PRD']['GIFT'][$product_id]->campaign_type == 'PRD05') {
                            $dataCount = PromotionalCampaign::find($campaign['PRD']['GIFT'][$product_id]->id)->promotionalCampaignGiveaways; //單品有幾個贈品
                            if (isset($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id])) {
                                foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                    $giftCount++; //計算滿額贈禮數
                                    if (isset($stock_gift_check[$giftInfo->product_id])) {
                                        $giftCalc = ($stock_gift_check[$giftInfo->product_id]->stock_qty - $giftInfo->assignedQty - ($product_id == $giftInfo->product_id ? $qty : 0));
                                        if ($giftCalc >= 0) {
                                            $giftCheck++;//計算滿額贈禮有庫存的
                                        }
                                    }
                                }
                            }
                            if ($giftCheck >= 0 && $giftCount == $giftCheck && $giftCount == count($dataCount)) {
                                if (isset($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id])) {
                                    foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                        if (isset($stock_gift_check[$giftInfo->product_id])) {
                                            $giftCalc = ($stock_gift_check[$giftInfo->product_id]->stock_qty - $giftInfo->assignedQty - ($product_id == $giftInfo->product_id ? $qty : 0));
                                            if ($giftCalc >= 0) {
                                                $giftAway[] = array(
                                                    "productPhoto" => $giftInfo['photo'],
                                                    "productId" => $giftInfo->product_id,
                                                    "productName" => $giftInfo->product_name,
                                                    "sellingPrice" => $giftInfo->selling_price,
                                                    "assignedQty" => $giftInfo->assignedQty,
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //商品折扣
                    if (isset($campaign['PRD']['DISCOUNT'][$product_id])) { //在活動內 滿額折扣
                        //ex: n=2, x=0.85, qty=4, price = 1000
                        $unit_qty = ($qty - $campaign['PRD']['DISCOUNT'][$product_id]->n_value + 1); //有幾件可以打折(4-2+1)
                        if ($qty >= $campaign['PRD']['DISCOUNT'][$product_id]->n_value && $unit_qty > 0) {
                            if ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD01') { //﹝單品﹞第N件(含)以上，打X折
                                $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000*0.85
                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                        //item_id折多少
                                        $amount = $tmp_qty * $price;
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        if ($unit_qty > 0) {
                                            if ($unit_qty < $detail_qty) {
                                                $tmp1 = $unit_qty; //要打折的數量
                                            } else {
                                                $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                            }
                                            $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                            $price1 = $tmp1 * $price; //1*850
                                            $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                            $amount = ($price1 + $price2);
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / ($tmp1 + $tmp2));
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        } else {
                                            $amount = $tmp_qty * $cartAmount[$product_id];
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / $return_qty);
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        }
                                    }

                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id]) && $return_type) {
                                        if (count($giftAway) > 0) {
                                            $prod_gift = array(
                                                "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                                "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                                "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                                "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                                "campaignProdList" => $giftAway,
                                            );
                                        }
                                    }
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD02') { //﹝單品﹞第N件(含)以上，折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000-200
                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                        //item_id折多少
                                        $amount = $tmp_qty * $price;
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        if ($unit_qty > 0) {
                                            if ($unit_qty < $detail_qty) {
                                                $tmp1 = $unit_qty; //要打折的數量
                                            } else {
                                                $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                            }
                                            $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                            $price1 = $tmp1 * $price; //1*850
                                            $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                            $return_qty = $tmp_qty;
                                            $amount = ($price1 + $price2);
                                            $unit_price = round($amount / ($tmp1 + $tmp2));
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        } else {
                                            $amount = $tmp_qty * $cartAmount[$product_id];
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / $return_qty);
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        }
                                    }
                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id]) && $return_type) {
                                        if (count($giftAway) > 0) {
                                            $prod_gift = array(
                                                "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                                "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                                "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                                "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                                "campaignProdList" => $giftAway,
                                            );
                                        }
                                    }

                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD03') { //﹝單品﹞滿N件，每件打X折
                                $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000*0.85
                                //找符合的item放
                                if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                    if (count($giftAway) > 0) {
                                        $prod_gift = array(
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway,
                                        );
                                    }
                                }

                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);

                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => true,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD04') { //﹝單品﹞滿N件，每件折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000-200

                                //找符合的item放
                                if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                    if (count($giftAway) > 0) {
                                        $prod_gift = array(
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway,
                                        );
                                    }
                                }

                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => true,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            }
                        } else { //沒有打折的件數
                            //找符合的item放
                            if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                if (count($giftAway) > 0) {
                                    $prod_gift = array(
                                        "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                        "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                        "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                        "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                        "campaignProdList" => $giftAway,
                                    );
                                }
                            }
                            //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                            foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                $detail_qty = $item_info->item_qty;
                                $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                $stock = 0;
                                if ($stock_info) {
                                    $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                }
                                //處理給前端的可下訂庫存
                                if ($stock == 0) { //可訂購數 =0
                                    $outOfStock = true;
                                } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                    $outOfStock = false;
                                } else {
                                    $outOfStock = false;
                                }
                                $product[] = array(
                                    "itemId" => $item_info->item_id,
                                    "itemNo" => $item_info->item_no,
                                    "itemSpec1" => $item_info->item_spec1,
                                    "itemSpec2" => $item_info->item_spec2,
                                    "itemPrice" => round($item_info->selling_price),
                                    "itemQty" => $detail_qty,
                                    "amount" => round($item_info->selling_price * $detail_qty),
                                    "itemStock" => $stock,
                                    "outOfStock" => $outOfStock,
                                    'itemPhoto' => $item_info->item_photo ?? null,
                                    "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                                    "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name : null),
                                    "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief : null),
                                    "campaignDiscountStatus" => false,
                                    "campaignDiscount" => 0,
                                    "campaignGiftAway" => $prod_gift
                                );
                                $cartTotal += round($item_info->selling_price * $detail_qty);
                                $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                                $prod_qty[$product_id] += $detail_qty;
                                if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                    $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                }
                                if (isset($campaignThreshold['GIFT'][$product_id])) {
                                    $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                }
                                if ($product_type == 'effective') {
                                    $productRow++;
                                }
                            };
                        }
                    } else { //不在活動內
                        //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                        //找符合的item放
                        if (isset($campaign['PRD']['GIFT'][$product_id])) {
                            if (count($giftAway) > 0) {
                                $prod_gift = array(
                                    "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                    "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                    "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                    "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                    "campaignProdList" => $giftAway,
                                );
                            }
                        }
                        foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                            $detail_qty = $item_info->item_qty;

                            $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                            $stock = 0;
                            if ($stock_info) {
                                $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                            }
                            //處理給前端的可下訂庫存
                            if ($stock == 0) { //可訂購數 =0
                                $outOfStock = true;
                            } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                $outOfStock = false;
                            } else {
                                $outOfStock = false;
                            }
                            $product[] = array(
                                "itemId" => $item_info->item_id,
                                "itemNo" => $item_info->item_no,
                                "itemSpec1" => $item_info->item_spec1,
                                "itemSpec2" => $item_info->item_spec2,
                                "itemPrice" => round($item_info->selling_price),
                                "itemQty" => $detail_qty,
                                "amount" => round($item_info->selling_price * $detail_qty),
                                "itemStock" => $stock,
                                "outOfStock" => $outOfStock,
                                'itemPhoto' => $item_info->item_photo ?? null,
                                "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                                "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaignDiscountName : null),
                                "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaignDiscountBrief : null),
                                "campaignDiscountStatus" => false,
                                "campaignDiscount" => 0,
                                "campaignGiftAway" => $prod_gift
                            );
                            $cartTotal += round($item_info->selling_price * $detail_qty);
                            $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                            $prod_qty[$product_id] += $detail_qty;
                            if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                            }
                            if (isset($campaignThreshold['GIFT'][$product_id])) {
                                $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                            }
                            if ($product_type == 'effective') {
                                $productRow++;
                            }
                        };
                    }

                    //指定商品贈品
                    foreach ($CART04 as $campaign_id => $prod) { //同系列只送一次
                        if (key_exists($product_id, $prod)) {
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $assigned[$campaign_id][$item_id] = round($detail_qty);
                            }
                        }
                    }
                } else {
                    //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                    foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                        $detail_qty = $item_info->item_qty;
                        $product_type = 'expired';
                        $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                        $stock = 0;
                        if ($stock_info) {
                            $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                        }
                        //處理給前端的可下訂庫存
                        if ($stock == 0) { //可訂購數 =0
                            $outOfStock = true;
                        } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                            $outOfStock = false;
                        } else {
                            $outOfStock = false;
                        }
                        $product[] = array(
                            "itemId" => $item_info->item_id,
                            "itemNo" => $item_info->item_no,
                            "itemSpec1" => $item_info->item_spec1,
                            "itemSpec2" => $item_info->item_spec2,
                            "itemPrice" => round($item_info->selling_price),
                            "itemQty" => $detail_qty,
                            "amount" => round($item_info->selling_price * $detail_qty),
                            "itemStock" => $stock,
                            "outOfStock" => $outOfStock,
                            'itemPhoto' => $item_info->item_photo ?? null,
                            "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->id) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                            "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->campaign_name) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name : null),
                            "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief : null),
                            "campaignDiscountStatus" => false,
                            "campaignDiscount" => 0,
                            "campaignGiftAway" => []
                        );
                        $cartTotal += 0;
                        $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                        $prod_qty[$product_id] += $detail_qty;
                        if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                            $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                        }
                        if (isset($campaignThreshold['GIFT'][$product_id])) {
                            $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                        }
                        $productRow++;
                    }
                }
                $productDetail[] = array(
                    "productType" => $product_type,
                    "productID" => $product_id,
                    "productNo" => $cartInfo[$product_id]['product_no'],
                    "productName" => $cartInfo[$product_id]['product_name'],
                    "sellingPrice" => $cartInfo[$product_id]['selling_price'],
                    "productPhoto" => $cartInfo[$product_id]['item_photo'],
                    "itemList" => $product,
                    "campaignThresholdDiscount" => (isset($campaignThreshold['DISCOUNT'][$product_id]) ? $campaignThreshold['DISCOUNT'][$product_id] : [])
                );

            }
            $calc_amount = [];
            $calc_qty[] = 0;
            $thresholdDiscount_display = [];
            $thresholdDiscount = [];
            $thresholdAmount = 0;
            //滿額折扣 CART_P01 & CART_P02
            if (isset($prod_campaign['DISCOUNT'])) {
                foreach ($prod_campaign['DISCOUNT'] as $campaign_id => $product_in_campaign) {
                    $pid = [];
                    $subAmount = [];
                    $price = 0;
                    $quantity = 0;
                    $tmp_product_id = 0;
                    foreach ($product_in_campaign as $key => $product_id) {
                        if ($tmp_product_id != $product_id) {
                            $price += $prod_amount[$product_id];
                            $quantity += $prod_qty[$product_id];
                            $pid[] = $product_id;
                            $subAmount[] = $prod_amount[$product_id];
                            $tmp_product_id = $product_id;
                            $calc_discount[$product_id] = $prod_amount[$product_id];
                        }
                    }
                    foreach ($pid as $pid_k => $pid_v) {
                        $tmp_calc[$pid_v] = 0;
                    }
                    $calc_amount[$campaign_id] = $price;
                    $calc_qty[$campaign_id] = $quantity;
                    $compare_n_value = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($calc_amount[$campaign_id] < $item->n_value) continue;
                        if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P01') { //﹝滿額﹞指定商品滿N元，打X折
                            $prodDiscount = $calc_amount[$campaign_id] - round($calc_amount[$campaign_id] * $item->x_value); //打折3000-(3000*0.95)
                            foreach ($pid as $pid_k => $pid_v) {
                                if (isset($calc_discount[$pid_v])) {
                                    $tmp_calc[$pid_v] = ($calc_discount[$pid_v] - round($calc_discount[$pid_v] * $item->x_value)) * -1;
                                } else {
                                    $tmp_calc[$pid_v] = 0;
                                }
                            }
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P02') { //﹝滿額﹞指定商品滿N元，折X元
                            $prodDiscount = $item->x_value;
                            foreach ($pid as $pid_k => $pid_v) {
                                $tmp_calc[$pid_v] = $item->x_value * -1;
                            }
                        }
                        foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                            $thresholdDiscount[$campaign_id] = array(
                                "thresholdID" => $item->id,
                                "campaignID" => $campaign_id,
                                "campaignName" => $campaignThresholdMain[$campaign_id]->campaign_name,
                                "campaignBrief" => $campaignThresholdMain[$campaign_id]->campaign_brief,
                                "campaignUrlCode" => $campaignThresholdMain[$campaign_id]->url_code,
                                "thresholdBrief" => $item->threshold_brief,
                                "campaignNvalue" => $item->n_value,
                                "campaignXvalue" => $item->x_value,
                                "campaignDiscount" => ($prodDiscount * -1),
                                "products" => $pid,
                                "productAmount" => $subAmount
                            );
                        }
                        $compare_n_value = $item->n_value;
                    }
                }

                $thresholdDiscount_display = []; //重新調整結構for前端使用
                if (isset($thresholdDiscount)) {
                    foreach ($thresholdDiscount as $campaign_id => $data) {
                        $thresholdAmount += $data['campaignDiscount'];
                        $thresholdDiscount_display[] = $data;
                    }
                }
            }
            //滿額折扣CART_P01 & CART_P02
            $calc_amount = [];
            $calc_qty[] = 0;
            //滿額送贈 CART_P03 & CART_P04
            if (isset($prod_campaign['GIFT'])) {
                foreach ($prod_campaign['GIFT'] as $campaign_id => $product_in_campaign) {
                    $pid = [];
                    $subAmount = [];
                    $price = 0;
                    $quantity = 0;
                    $tmp_product_id = 0;
                    $tmp_thresholdDiscount[$campaign_id] = 0;
                    foreach ($product_in_campaign as $key => $product_id) {
                        if ($tmp_product_id != $product_id) {
                            $price += $prod_amount[$product_id];
                            $quantity += $prod_qty[$product_id];
                            $pid[] = $product_id;
                            $subAmount[] = $prod_amount[$product_id];
                            $tmp_product_id = $product_id;
                            if (isset($tmp_calc)) {
                                if (array_key_exists($product_id, $tmp_calc)) {
                                    if ($tmp_thresholdDiscount[$campaign_id] == 0) {
                                        $tmp_thresholdDiscount[$campaign_id] += $tmp_calc[$product_id];
                                    }
                                }
                            }
                        }
                    }
                    $calc_amount[$campaign_id] = ($price + $tmp_thresholdDiscount[$campaign_id]);
                    $calc_qty[$campaign_id] = $quantity;
                    $compare_n_value = 0;
                    $campaign_threshold = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P03') { //﹝滿額﹞指定商品滿N元，送贈
                            if ($calc_amount[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P04') { //﹝滿額﹞指定商品滿N件送贈
                            if ($calc_qty[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_qty[$campaign_id]) continue;
                        }
                        $compare_n_value = $item->n_value;
                        $campaign_threshold = $item->id;
                    }
                    $prods = [];
                    $compare_n_value = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P03') { //﹝滿額﹞指定商品滿N元，送贈
                            if ($calc_amount[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P04') { //﹝滿額﹞指定商品滿N件送贈
                            if ($calc_qty[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_qty[$campaign_id]) continue;
                        }
                        $compare_n_value = $item->n_value;
                        if ($item->id == $campaign_threshold) {
                            $prd = 0;
                            foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                                $gift_array[$item->id] = 0;
                                $gift_check[$item->id] = 0;
                                foreach ($giftawayInfo as $giftInfo => $v) {
                                    $prd = isset($prod_qty[$v['product_id']]) ? $prod_qty[$v['product_id']] : 0; //購物車中單品的數量
                                    //if (isset($threshold_prod[$v['product_id']])) {
                                        $gift_array[$item->id]++;
                                        if (isset($stock_gift_check[$v['product_id']])) {
                                            if (($stock_gift_check[$v['product_id']]->stock_qty - $v->assigned_qty - $prd) >= 0) {
                                                $gift_check[$item->id]++;
                                            }
                                        }
                                    //}
                                }
                            }
                            if ($gift_check[$item->id] > 0 && $gift_array[$item->id] == $gift_check[$item->id]) {//只要有缺貨就全部不呈現
                                foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                                    unset($prods[$v['product_id']]);
                                    if ($gift_array[$item->id] > 0 && $gift_array[$item->id] == $gift_check[$item->id]) {
                                        foreach ($giftawayInfo as $giftInfo => $v) {
                                            $prd = isset($prod_qty[$v['product_id']]) ? $prod_qty[$v['product_id']] : 0; //購物車中單品的數量
                                            if (isset($threshold_prod[$v['product_id']])) {
                                                if (isset($stock_gift_check[$v['product_id']])) {
                                                    if (($stock_gift_check[$v['product_id']]->stock_qty - $v->assigned_qty - $prd) >= 0) {
                                                        $prods[$v['product_id']] = array(
                                                            "productPhoto" => $threshold_prod[$v['product_id']]->photo,
                                                            "productId" => $v['product_id'],
                                                            "productName" => $threshold_prod[$v['product_id']]->product_name,
                                                            "sellingPrice" => $threshold_prod[$v['product_id']]->selling_price,
                                                            "assignedQty" => $v->assigned_qty,
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $prods_display = [];//重新調整結構for前端使用
                        if (isset($prods)) {
                            foreach ($prods as $prod) {
                                $prods_display[] = $prod;
                            }
                        }
                        if (isset($prods)) {
                            if (count($prods) > 0) {
                                $thresholdGiftAway[$campaign_id] = array(
                                    "thresholdID" => $item->id,
                                    "campaignID" => $campaign_id,
                                    "campaignName" => $campaignThresholdMain[$campaign_id]->campaign_name,
                                    "campaignBrief" => $campaignThresholdMain[$campaign_id]->campaign_brief,
                                    "campaignUrlCode" => $campaignThresholdMain[$campaign_id]->url_code,
                                    "thresholdBrief" => $item->threshold_brief,
                                    "campaignNvalue" => $item->n_value,
                                    "campaignXvalue" => $item->x_value,
                                    "campaignProdList" => $prods_display,
                                    "products" => $pid,
                                    "productAmount" => $subAmount
                                );
                            }
                        }
                    }
                }
                $thresholdGiftAway_display = []; //重新調整結構for前端使用
                if (isset($thresholdGiftAway)) {
                    foreach ($thresholdGiftAway as $campaign_id => $data) {
                        $thresholdGiftAway_display[] = $data;
                    }
                }
            }
            //滿額送贈 CART_P03 & CART_P04

            //全車滿額贈
            $cartDiscount = 0;
            $compare_n_value = 0;
            foreach ($campaign_discount as $items => $item) {
                if ($compare_n_value > $item->n_value) {
                    continue;
                }
                if ($cartTotal >= $item->n_value) {
                    if ($item->campaign_type == 'CART01') { //﹝滿額﹞購物車滿N元，打X折
                        $cartDiscount += $cartTotal - ($cartTotal * $item->x_value); //打折10000-(10000*0.85)
                    } elseif ($item->campaign_type == 'CART02') { //﹝滿額﹞購物車滿N元，折X元
                        $cartDiscount += $item->x_value; //打折後10000-1000
                    }
                    $compare_n_value = $item->n_value;
                }
            }
            if ($cartDiscount > 0) {
                $cartDiscount = $cartDiscount + $thresholdAmount;
            }
            $total_amount = ($cartTotal - $cartDiscount);
            if (isset($campaign_gift['CART'])) {
                $compare_value = 0;
                foreach ($campaign_gift['CART'] as $items => $item) {
                    if ($total_amount >= $item->n_value) {
                        if ($now >= $item->start_launched_at && $now <= $item->end_launched_at) { //在上架期間內
                            if ($item->campaign_type == 'CART03') { //﹝滿額﹞購物車滿N元，送贈品
                                if ($item->assignedQty > 0) {
                                    $stock_check = $this->stockService->getStockByProd($warehouseCode, $item->product_id);
                                    if (isset($stock_check)) {
                                        if ($this->stockService->getStockByProd($warehouseCode, $item->product_id)->stock_qty > 0) { //有足夠庫存
                                            if ($compare_value > $item->n_value) {
                                                continue;
                                            }

                                            $cartGift[] = array(
                                                "campaignId" => $item->promotional_campaign_id,
                                                "campaignName" => $item->campaign_name,
                                                "campaignBrief" => $item->campaign_brief,
                                                "productId" => $item->product_id,
                                                "productName" => $item->product_name,
                                                "sellingPrice" => $item->selling_price,
                                                "productPhoto" => $campaign_gift['PROD'][$item->promotional_campaign_id][$item->product_id]['photo'],
                                                "assignedQty" => $item->assignedQty,
                                            );
                                            $compare_value = $item->n_value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($assigned as $campaign_id => $item) {
                $assigned_qty = array_sum($assigned[$campaign_id]);
                if ($assigned_qty >= $CART04_n[$campaign_id]) {
                    foreach ($campaign_gift['PROD'][$campaign_id] as $prod_id => $value) {
                        if ($value->assignedQty > 0) {
                            if (($this->stockService->getStockByProd($warehouseCode, $prod_id)->stock_qty - $value->assignedQty) >= 0) { //有足夠庫存
                                $cartGift[] = array(
                                    "campaignId" => $value->promotional_campaign_id,
                                    "campaignName" => $value->campaign_name,
                                    "campaignBrief" => $value->campaign_brief,
                                    "productId" => $prod_id,
                                    "sellingPrice" => $value->selling_price,
                                    "productName" => $value->product_name,
                                    "productPhoto" => $value->photo,
                                    "assignedQty" => $value->assignedQty,
                                );
                            }
                        }
                    }
                }
            }

            //會員可用點數
            $pointData = $this->apiService->getMemberPoint($member_id);
            $info = json_decode($pointData);
            $pointInfo = [];

            $pointInfo = array(
                "point" => $info->data->point,
                "discountRate" => $info->data->discountRate,
                "exchangeRate" => $info->data->exchangeRate,
                "discountMax" => ($info->data->point > 0 ? floor(($total_amount + $thresholdAmount) * $info->data->discountRate) : 0),
            );

            //運費規則
            $fee = [];
            if (($cartTotal - round($cartDiscount) + round($thresholdAmount)) < $shippingFee['HOME']->free_threshold) {
                $fee = $shippingFee['HOME']->shipping_fee;
            } else {
                $fee = 0;
            }
            $cart = [];


            $cart = array(
                "feeInfo" => $feeInfo,
                "productRow" => $productRow,
                "list" => $productDetail,
                "totalPrice" => $cartTotal,
                "thresholdDiscountDisplay" => true,
                "thresholdDiscount" => isset($thresholdDiscount_display) ? $thresholdDiscount_display : [],
                "thresholdAmount" => round($thresholdAmount),
                "thresholdGiftAway" => isset($thresholdGiftAway_display) ? $thresholdGiftAway_display : [],
                "discount" => round($cartDiscount),
                "giftAway" => $cartGift,
                "point" => $pointInfo,
                "shippingFee" => $fee,
                "checkout" => $cartTotal - round($cartDiscount) + round($thresholdAmount),
            );
            return json_encode(array("status" => 200, "result" => $cart));
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
                    where p.approval_status = 'APPROVED' ";
        $products = DB::select($strSQL);
        $data = [];
        foreach ($products as $product) {
            $data[$product->id] = $product;
        }
        return $data;
    }

    /**
     * 會員購物車(批次新增)
     * @param
     * @return string
     */
    public function setBatchCart($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        $webDataAdd = [];
        $webDataUpd = [];
        foreach ($input['item_id'] as $key => $value) {

            //確認是否有該品項
            $product_item = ProductItem::where('id', $value)->get()->toArray();
            //確認是否有該品項
            $item = ShoppingCartDetail::where('member_id', '=', $member_id)->where('product_item_id', '=', $value)->first();

            if ($item) {

                $latest_added_at = $item->latest_added_at;
                //如果資料原本的狀態為1(已轉為訂單)或-1(已自購物車刪除)，則要更新時間
                if ($item->status_code == 1 || $item->status_code == -1) {
                    $latest_added_at = $now;
                }

                $webDataUpd[$key] = [
                    "id" => $item->id,
                    "product_id" => $product_item[0]['product_id'],
                    "product_item_id" => $input['item_id'][$key],
                    "qty" => ($input['item_qty'][$key] + $item->qty),
                    "status_code" => $input['status_code'],
                    "utm_source" => $input['utm_source'],
                    "utm_medium" => $input['utm_medium'],
                    "utm_campaign" => $input['utm_campaign'],
                    "utm_sales" => $input['utm_sales'],
                    "utm_time" => Carbon::createFromTimestamp($input['utm_time'])->format('Y-m-d H:i:s'),
                    "latest_added_at" => $latest_added_at,
                    "updated_by" => $member_id,
                    "updated_at" => $now,
                ];
            } else {
                $webDataAdd[$key] = [
                    $member_id,
                    $product_item[0]['product_id'],
                    $value,
                    $input['item_qty'][$key],
                    $input['status_code'],
                    $input['utm_source'],
                    $input['utm_medium'],
                    $input['utm_campaign'],
                    $input['utm_sales'],
                    date("Y-m-d H:i:s", $input['utm_time']),
                    $now,
                    $member_id,
                    $member_id,
                    $now,
                    $now,
                ];
            }

        }
        $addColumn = [
            "member_id", "product_id", "product_item_id", "qty", "status_code",
            "utm_source", "utm_medium", "utm_campaign", "utm_sales", "utm_time", "latest_added_at",
            "created_by", "updated_by", "created_at", "updated_at",
        ];

        DB::beginTransaction();
        try {

            if ($webDataUpd) {
                $cartInstance = new ShoppingCartDetail();
                $upd = Batch::update($cartInstance, $webDataUpd, 'id');
            }

            if ($webDataAdd) {
                $cartInstance = new ShoppingCartDetail();
                $batchSize = 50;
                $add = Batch::insert($cartInstance, $addColumn, $webDataAdd, $batchSize);
            }

            DB::commit();
            $result = 'success';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }

    /**
     * 更新購物車商品數量(增加)
     * @param
     * @return string
     */
    public function setGoodsQty($input)
    {
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        //確認是否有該品項
        $item = ProductItem::where('id', $input['item_id'])->get()->toArray();

        $stock_info = $this->stockService->getStockByItem($warehouseCode, $input['item_id']);
        $stock = 0;
        if ($stock_info) {
            $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
        }
        if (count($item) > 0) {
            $data = ShoppingCartDetail::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->get()->toArray();
            if (count($data) > 0) {
                $act = 'upd';
                if ($data[0]['status_code'] == 0) {
                    $qty = ($input['item_qty'] + (isset($data[0]['qty']) ? $data[0]['qty'] : 0));
                } else {
                    $qty = $input['item_qty'];
                }
            } else {
                $act = 'add';
            }
        } else {
            return '401';
        }
        DB::beginTransaction();
        try {
            $webData = [];
            if ($act == 'add') {
                $webData['member_id'] = $member_id;
                $webData['product_id'] = $item[0]['product_id'];
                $webData['product_item_id'] = $input['item_id'];
                $webData['status_code'] = $input['status_code'];
                $webData['qty'] = ($input['item_qty'] > $stock ? $stock : $input['item_qty']);
                $webData['utm_source'] = $input['utm_source'];
                $webData['utm_medium'] = $input['utm_medium'];
                $webData['utm_campaign'] = $input['utm_campaign'];
                $webData['utm_sales'] = $input['utm_sales'];
                $webData['utm_time'] = Carbon::createFromTimestamp($input['utm_time'])->format('Y-m-d H:i:s');
                $webData['latest_added_at'] = $now;
                $webData['created_by'] = $member_id;
                $webData['updated_by'] = -1;
                $webData['created_at'] = $now;
                $webData['updated_at'] = $now;
                if ($input['status_code'] != 0) {
                    return '203';
                }
                $new_id = ShoppingCartDetail::insertGetId($webData);
            } else if ($act == 'upd') {

                $latest_added_at = $data[0]['latest_added_at'];
                //如果資料原本的狀態為1(已轉為訂單)或-1(已自購物車刪除)，則要更新時間
                if ($data[0]['status_code'] == 1 || $data[0]['status_code'] == -1) {
                    $latest_added_at = $now;
                }

                $webData['product_id'] = $item[0]['product_id'];
                $webData['qty'] = ($qty > $stock ? $stock : $qty);
                $webData['status_code'] = $input['status_code'];
                $webData['utm_source'] = $input['utm_source'];
                $webData['utm_medium'] = $input['utm_medium'];
                $webData['utm_campaign'] = $input['utm_campaign'];
                $webData['utm_sales'] = $input['utm_sales'];
                $webData['utm_time'] = Carbon::createFromTimestamp($input['utm_time'])->format('Y-m-d H:i:s');
                $webData['latest_added_at'] = $latest_added_at;
                $webData['updated_by'] = $member_id;
                $webData['updated_at'] = $now;
                $new_id = ShoppingCartDetail::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->update($webData);
            }
            DB::commit();
            if ($new_id > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }

    /*
     * 取得購物車內容_V2 (滿額折分攤回單品)
     * 滿額折-改版版本
     */
    public function getCartDataV2($member_id, $campaigns, $campaign_gift, $campaign_discount, $gtm)
    {
        $now = Carbon::now();
        //購物車內容
        $cartInfo = self::getCartInfo($member_id);
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $shippingFee = ShippingFeeRulesService::getShippingFee('HOME');
        $feeInfo = array(
            "shipping_fee" => $shippingFee['HOME']->shipping_fee,
            "free_threshold" => $shippingFee['HOME']->free_threshold,
            "notice" => $shippingFee['HOME']->notice_brief,
            "noticeDetail" => $shippingFee['HOME']->notice_detailed,
        );
        if (count($cartInfo) == 0) {
            return json_encode(array("status" => 404, "result" => $feeInfo));
        } else {
            $stock_gift_check = $this->stockService->getProductInStock($warehouseCode);
            $cartQty = [];
            $cartAmount = [];
            $cartDetail = [];
            $cartTotal = 0;
            $product = [];
            $productDetail = [];
            $CART04 = [];
            $CART04_n = [];
            $cartGift = [];
            $assigned = [];
            foreach ($cartInfo as $items => $item) {
                if ($items == 'item_photo') {
                    continue;
                }

                if ($items == 'items') {
                    continue;
                }

                $cartQty[$items][$item['item_id']] = $item->item_qty; //購物車數量
                $cartAmount[$items] = round($item->selling_price); //商品售價
            }
            $prodQty = [];
            foreach ($cartInfo['items'] as $prdouct_id => $items) {
                foreach ($items as $item_id => $item) {
                    $cartDetail[$prdouct_id][$item_id] = $item; //購物車內容
                    $prodQty[$prdouct_id][$item_id] = $item['item_qty'];
                }
            }
            //行銷活動
            foreach ($campaigns as $product_id => $item) {
                foreach ($item as $k => $v) {
                    if ($now >= $v->start_at and $now <= $v->end_at) { //活動時間內才做
                        //先取活動類型為單品或車子(贈品或折扣) $campaign['PRD']['DISCOUNT'] 單品折扣
                        $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                        if ($v->campaign_type == 'CART04') {
                            $CART04[$v->id][$product_id] = 1;
                            $CART04_n[$v->id] = $v->n_value; //CART04 同系列商品滿額條件
                        }
                    }
                }
            }
            //活動滿額門檻資料 (活動時間內才做)
            $campaignThreshold = [];
            $campaignThresholdItem = [];
            $campaignThresholdGift = [];
            if (isset($campaign['CART_P'])) {
                foreach ($campaign['CART_P'] as $type => $items) {
                    foreach ($items as $product_id => $data) {
                        $campaignThreshold_brief = [];
                        $campaignThreshold_item = [];
                        $campaignThresholds = PromotionalCampaignThreshold::where('promotional_campaign_id', $data->id)->orderBy('n_value')->get();
                        foreach ($campaignThresholds as $threshold) {
                            $campaignThreshold_brief[] = $threshold->threshold_brief;
                            $campaignThreshold_item[] = $threshold;
                            $thresholdGift = PromotionalCampaignThreshold::find($threshold->id)->promotionalCampaignGiveaways;
                            $campaignThresholdGift[$data->id][$threshold->id][] = $thresholdGift;

                        }
                        //畫面顯示用
                        $campaignThreshold[$type][$product_id] = array(
                            "campaignId" => $data->id,
                            "campaignName" => $data->campaign_name,
                            "campaignBrief" => $data->campaign_brief,
                            "campaignUrlCode" => $data->url_code,
                            "campaignThreshold" => $campaignThreshold_brief
                        );
                        //滿額計算用
                        $campaignThresholdItem[$data->id] = $campaignThreshold_item;//活動門檻資料
                        $campaignThresholdMain[$data->id] = $data; //活動主檔
                    }
                }
            }
            //活動滿額門檻資料 (活動時間內才做)
            //重組活動贈品
            $threshold_prod = [];
            foreach ($campaign_gift['PROD'] as $campaign_id => $item) {
                foreach ($item as $product_id => $data) {
                    if ($now >= $data['start_launched_at'] && $now <= $data['end_launched_at']) {
                        $threshold_prod[$product_id] = $data;
                    }
                }
            }
            $productRow = 0;
            $cartDiscount = 0;
            $prod_campaign = [];//活動下的單品
            foreach ($cartQty as $product_id => $item) {
                $product = [];
                $prod_gift = [];
                $prod_amount[$product_id] = 0;
                $prod_qty[$product_id] = 0;
                if ($now >= $cartInfo[$product_id]['start_launched_at'] && $now <= $cartInfo[$product_id]['end_launched_at']) { //在上架期間內
                    $product_type = "effective";
                    $qty = array_sum($prodQty[$product_id]); //合併不同規格但同一商品的數量
                    //商品贈品
                    $giftAway = [];
                    $giftCount = 0;
                    $giftCheck = 0;
                    $giftCalc = 0;
                    if (isset($campaign['PRD']['GIFT'][$product_id])) { //在活動內 滿額贈禮
                        if ($campaign['PRD']['GIFT'][$product_id]->campaign_type == 'PRD05') {
                            $dataCount = PromotionalCampaign::find($campaign['PRD']['GIFT'][$product_id]->id)->promotionalCampaignGiveaways; //單品有幾個贈品
                            if (isset($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id])) {
                                foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                    $giftCount++; //計算滿額贈禮數
                                    if (isset($stock_gift_check[$giftInfo->product_id])) {
                                        $giftCalc = ($stock_gift_check[$giftInfo->product_id]->stock_qty - $giftInfo->assignedQty - ($product_id == $giftInfo->product_id ? $qty : 0));
                                        if ($giftCalc >= 0) {
                                            $giftCheck++;//計算滿額贈禮有庫存的
                                        }
                                    }
                                }
                            }
                            if ($giftCheck >= 0 && $giftCount == $giftCheck && $giftCount == count($dataCount)) {
                                if (isset($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id])) {
                                    foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                        if (isset($stock_gift_check[$giftInfo->product_id])) {
                                            $giftCalc = ($stock_gift_check[$giftInfo->product_id]->stock_qty - $giftInfo->assignedQty - ($product_id == $giftInfo->product_id ? $qty : 0));
                                            if ($giftCalc >= 0) {
                                                $giftAway[] = array(
                                                    "productPhoto" => $giftInfo['photo'],
                                                    "productId" => $giftInfo->product_id,
                                                    "productName" => $giftInfo->product_name,
                                                    "sellingPrice" => $giftInfo->selling_price,
                                                    "assignedQty" => $giftInfo->assignedQty,
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //商品折扣
                    if (isset($campaign['PRD']['DISCOUNT'][$product_id])) { //在活動內 滿額折扣
                        //ex: n=2, x=0.85, qty=4, price = 1000
                        $unit_qty = ($qty - $campaign['PRD']['DISCOUNT'][$product_id]->n_value + 1); //有幾件可以打折(4-2+1)
                        if ($qty >= $campaign['PRD']['DISCOUNT'][$product_id]->n_value && $unit_qty > 0) {
                            if ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD01') { //﹝單品﹞第N件(含)以上，打X折
                                $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000*0.85
                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                        //item_id折多少
                                        $amount = $tmp_qty * $price;
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        if ($unit_qty > 0) {
                                            if ($unit_qty < $detail_qty) {
                                                $tmp1 = $unit_qty; //要打折的數量
                                            } else {
                                                $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                            }
                                            $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                            $price1 = $tmp1 * $price; //1*850
                                            $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                            $amount = ($price1 + $price2);
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / ($tmp1 + $tmp2));
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        } else {
                                            $amount = $tmp_qty * $cartAmount[$product_id];
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / $return_qty);
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        }
                                    }

                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id]) && $return_type) {
                                        if (count($giftAway) > 0) {
                                            $prod_gift = array(
                                                "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                                "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                                "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                                "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                                "campaignProdList" => $giftAway,
                                            );
                                        }
                                    }
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($amount)) * -1,
                                        "itemCartDiscount" => 0,
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift,
                                        "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD02') { //﹝單品﹞第N件(含)以上，折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000-200
                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                        //item_id折多少
                                        $amount = $tmp_qty * $price;
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        if ($unit_qty > 0) {
                                            if ($unit_qty < $detail_qty) {
                                                $tmp1 = $unit_qty; //要打折的數量
                                            } else {
                                                $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                            }
                                            $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                            $price1 = $tmp1 * $price; //1*850
                                            $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                            $return_qty = $tmp_qty;
                                            $amount = ($price1 + $price2);
                                            $unit_price = round($amount / ($tmp1 + $tmp2));
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        } else {
                                            $amount = $tmp_qty * $cartAmount[$product_id];
                                            $return_qty = $tmp_qty;
                                            $unit_price = round($amount / $return_qty);
                                            $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                            $return_type = true;
                                        }
                                    }
                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id]) && $return_type) {
                                        if (count($giftAway) > 0) {
                                            $prod_gift = array(
                                                "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                                "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                                "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                                "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                                "campaignProdList" => $giftAway,
                                            );
                                        }
                                    }

                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($amount)) * -1,
                                        "itemCartDiscount" => 0,
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift,
                                        "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD03') { //﹝單品﹞滿N件，每件打X折
                                $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000*0.85
                                //找符合的item放
                                if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                    if (count($giftAway) > 0) {
                                        $prod_gift = array(
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway,
                                        );
                                    }
                                }

                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);

                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($amount)) * -1,
                                        "itemCartDiscount" => 0,
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => true,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift,
                                        "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD04') { //﹝單品﹞滿N件，每件折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000-200

                                //找符合的item放
                                if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                    if (count($giftAway) > 0) {
                                        $prod_gift = array(
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway,
                                        );
                                    }
                                }

                                //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                    $detail_qty = $item_info->item_qty;
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    //處理給前端的可下訂庫存
                                    if ($stock == 0) { //可訂購數 =0
                                        $outOfStock = true;
                                    } else if ($return_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                        $outOfStock = false;
                                    } else {
                                        $outOfStock = false;
                                    }
                                    $product[] = array(
                                        "itemId" => $item_info->item_id,
                                        "itemNo" => $item_info->item_no,
                                        "itemSpec1" => $item_info->item_spec1,
                                        "itemSpec2" => $item_info->item_spec2,
                                        "itemPrice" => round($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => round($amount),
                                        "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($amount)) * -1,
                                        "itemCartDiscount" => 0,
                                        "itemStock" => $stock,
                                        "outOfStock" => $outOfStock,
                                        'itemPhoto' => $item_info->item_photo ?? null,
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountBrief" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief,
                                        "campaignDiscountStatus" => true,
                                        "campaignDiscount" => ((round($item_info->selling_price) * $return_qty) - round($amount)) * -1,
                                        "campaignGiftAway" => $prod_gift,
                                        "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                                    );
                                    $cartTotal += round($amount);
                                    $prod_amount[$product_id] += round($amount);
                                    $prod_qty[$product_id] += $return_qty;
                                    if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                        $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if (isset($campaignThreshold['GIFT'][$product_id])) {
                                        $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                    }
                                    if ($product_type == 'effective') {
                                        $productRow++;
                                    }
                                }
                            }
                        } else { //沒有打折的件數
                            //找符合的item放
                            if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                if (count($giftAway) > 0) {
                                    $prod_gift = array(
                                        "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                        "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                        "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                        "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                        "campaignProdList" => $giftAway,
                                    );
                                }
                            }
                            //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                            foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                                $detail_qty = $item_info->item_qty;
                                $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                                $stock = 0;
                                if ($stock_info) {
                                    $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                }
                                //處理給前端的可下訂庫存
                                if ($stock == 0) { //可訂購數 =0
                                    $outOfStock = true;
                                } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                    $outOfStock = false;
                                } else {
                                    $outOfStock = false;
                                }
                                $product[] = array(
                                    "itemId" => $item_info->item_id,
                                    "itemNo" => $item_info->item_no,
                                    "itemSpec1" => $item_info->item_spec1,
                                    "itemSpec2" => $item_info->item_spec2,
                                    "itemPrice" => round($item_info->selling_price),
                                    "itemQty" => $detail_qty,
                                    "amount" => round($item_info->selling_price * $detail_qty),
                                    "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($item_info->selling_price * $detail_qty)) * -1,
                                    "itemCartDiscount" => 0,
                                    "itemStock" => $stock,
                                    "outOfStock" => $outOfStock,
                                    'itemPhoto' => $item_info->item_photo ?? null,
                                    "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                                    "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name : null),
                                    "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief : null),
                                    "campaignDiscountStatus" => false,
                                    "campaignDiscount" => 0,
                                    "campaignGiftAway" => $prod_gift,
                                    "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                                );
                                $cartTotal += round($item_info->selling_price * $detail_qty);
                                $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                                $prod_qty[$product_id] += $detail_qty;
                                if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                    $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                                }
                                if (isset($campaignThreshold['GIFT'][$product_id])) {
                                    $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                                }
                                if ($product_type == 'effective') {
                                    $productRow++;
                                }
                            };
                        }
                    } else { //不在活動內
                        //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                        //找符合的item放
                        if (isset($campaign['PRD']['GIFT'][$product_id])) {
                            if (count($giftAway) > 0) {
                                $prod_gift = array(
                                    "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                    "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                    "campaignGiftBrief" => $campaign['PRD']['GIFT'][$product_id]->campaign_brief,
                                    "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                    "campaignProdList" => $giftAway,
                                );
                            }
                        }
                        foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                            $detail_qty = $item_info->item_qty;

                            $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                            $stock = 0;
                            if ($stock_info) {
                                $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                            }
                            //處理給前端的可下訂庫存
                            if ($stock == 0) { //可訂購數 =0
                                $outOfStock = true;
                            } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                                $outOfStock = false;
                            } else {
                                $outOfStock = false;
                            }
                            $product[] = array(
                                "itemId" => $item_info->item_id,
                                "itemNo" => $item_info->item_no,
                                "itemSpec1" => $item_info->item_spec1,
                                "itemSpec2" => $item_info->item_spec2,
                                "itemPrice" => round($item_info->selling_price),
                                "itemQty" => $detail_qty,
                                "amount" => round($item_info->selling_price * $detail_qty),
                                "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($item_info->selling_price * $detail_qty)) * -1,
                                "itemCartDiscount" => 0,
                                "itemStock" => $stock,
                                "outOfStock" => $outOfStock,
                                'itemPhoto' => $item_info->item_photo ?? null,
                                "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                                "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaignDiscountName : null),
                                "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaignDiscountBrief : null),
                                "campaignDiscountStatus" => false,
                                "campaignDiscount" => 0,
                                "campaignGiftAway" => $prod_gift,
                                "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                            );
                            $cartTotal += round($item_info->selling_price * $detail_qty);
                            $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                            $prod_qty[$product_id] += $detail_qty;
                            if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                                $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                            }
                            if (isset($campaignThreshold['GIFT'][$product_id])) {
                                $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                            }
                            if ($product_type == 'effective') {
                                $productRow++;
                            }
                        };
                    }

                    //指定商品贈品
                    foreach ($CART04 as $campaign_id => $prod) { //同系列只送一次
                        if (key_exists($product_id, $prod)) {
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $assigned[$campaign_id][$item_id] = round($detail_qty);
                            }
                        }
                    }
                } else {
                    //foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                    foreach ($cartDetail[$product_id] as $item_id => $item_info) {
                        $detail_qty = $item_info->item_qty;
                        $product_type = 'expired';
                        $stock_info = $this->stockService->getStockByItem($warehouseCode, $item_info->item_id);
                        $stock = 0;
                        if ($stock_info) {
                            $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                        }
                        //處理給前端的可下訂庫存
                        if ($stock == 0) { //可訂購數 =0
                            $outOfStock = true;
                        } else if ($detail_qty > $stock && $stock > 0) { //訂購數大於庫存數 & 可訂購數 >0
                            $outOfStock = false;
                        } else {
                            $outOfStock = false;
                        }
                        $product[] = array(
                            "itemId" => $item_info->item_id,
                            "itemNo" => $item_info->item_no,
                            "itemSpec1" => $item_info->item_spec1,
                            "itemSpec2" => $item_info->item_spec2,
                            "itemPrice" => round($item_info->selling_price),
                            "itemQty" => $detail_qty,
                            "amount" => round($item_info->selling_price * $detail_qty),
                            "itemDiscount" => (($cartAmount[$product_id] * $detail_qty) - round($item_info->selling_price * $detail_qty)) * -1,
                            "itemCartDiscount" => 0,
                            "itemStock" => $stock,
                            "outOfStock" => $outOfStock,
                            'itemPhoto' => $item_info->item_photo ?? null,
                            "campaignDiscountId" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->id) ? $campaign['PRD']['DISCOUNT'][$product_id]->id : null),
                            "campaignDiscountName" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->campaign_name) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name : null),
                            "campaignDiscountBrief" => (isset($campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief) ? $campaign['PRD']['DISCOUNT'][$product_id]->campaign_brief : null),
                            "campaignDiscountStatus" => false,
                            "campaignDiscount" => 0,
                            "campaignGiftAway" => [],
                            "gtm" => (isset($gtm[$product_id][$item_info->item_id]) ? $gtm[$product_id][$item_info->item_id] : "")
                        );
                        $cartTotal += 0;
                        $prod_amount[$product_id] += round($item_info->selling_price * $detail_qty);
                        $prod_qty[$product_id] += $detail_qty;
                        if (isset($campaignThreshold['DISCOUNT'][$product_id])) {
                            $prod_campaign['DISCOUNT'][$campaignThreshold['DISCOUNT'][$product_id]['campaignId']][] = $product_id;
                        }
                        if (isset($campaignThreshold['GIFT'][$product_id])) {
                            $prod_campaign['GIFT'][$campaignThreshold['GIFT'][$product_id]['campaignId']][] = $product_id;
                        }
                        $productRow++;
                    }
                }
                $productDetail[] = array(
                    "productType" => $product_type,
                    "productID" => $product_id,
                    "productNo" => $cartInfo[$product_id]['product_no'],
                    "productName" => $cartInfo[$product_id]['product_name'],
                    "sellingPrice" => $cartInfo[$product_id]['selling_price'],
                    "productPhoto" => $cartInfo[$product_id]['item_photo'],
                    "itemList" => $product,
                    "campaignThresholdDiscount" => (isset($campaignThreshold['DISCOUNT'][$product_id]) ? $campaignThreshold['DISCOUNT'][$product_id] : []),
                );

            }
            $calc_amount = [];
            $calc_qty[] = 0;
            $thresholdDiscount_display = [];
            $thresholdDiscount = [];
            $thresholdAmount = 0;
            //滿額折扣 CART_P01 & CART_P02
            if (isset($prod_campaign['DISCOUNT'])) {
                foreach ($prod_campaign['DISCOUNT'] as $campaign_id => $product_in_campaign) {
                    $pid = [];
                    $subAmount = [];
                    $price = 0;
                    $quantity = 0;
                    $tmp_product_id = 0;
                    foreach ($product_in_campaign as $key => $product_id) {
                        if ($tmp_product_id != $product_id) {
                            $price += $prod_amount[$product_id];
                            $quantity += $prod_qty[$product_id];
                            $pid[] = $product_id;
                            $subAmount[] = $prod_amount[$product_id];
                            $tmp_product_id = $product_id;
                            $calc_discount[$product_id] = $prod_amount[$product_id];
                        }
                    }
                    foreach ($pid as $pid_k => $pid_v) {
                        $tmp_calc[$pid_v] = 0;
                    }
                    $calc_amount[$campaign_id] = $price;
                    $calc_qty[$campaign_id] = $quantity;
                    $compare_n_value = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($calc_amount[$campaign_id] < $item->n_value) continue;
                        if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P01') { //﹝滿額﹞指定商品滿N元，打X折
                            $prodDiscount = $calc_amount[$campaign_id] - round($calc_amount[$campaign_id] * $item->x_value); //打折3000-(3000*0.95)
                            foreach ($pid as $pid_k => $pid_v) {
                                if (isset($calc_discount[$pid_v])) {
                                    $tmp_calc[$pid_v] = ($calc_discount[$pid_v] - round($calc_discount[$pid_v] * $item->x_value)) * -1;
                                } else {
                                    $tmp_calc[$pid_v] = 0;
                                }
                            }
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P02') { //﹝滿額﹞指定商品滿N元，折X元
                            $prodDiscount = $item->x_value;
                            foreach ($pid as $pid_k => $pid_v) {
                                $tmp_calc[$pid_v] = $item->x_value * -1;
                            }
                        }
                        foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                            $thresholdDiscount[$campaign_id] = array(
                                "thresholdID" => $item->id,
                                "campaignID" => $campaign_id,
                                "campaignName" => $campaignThresholdMain[$campaign_id]->campaign_name,
                                "campaignBrief" => $campaignThresholdMain[$campaign_id]->campaign_brief,
                                "campaignUrlCode" => $campaignThresholdMain[$campaign_id]->url_code,
                                "thresholdBrief" => $item->threshold_brief,
                                "campaignNvalue" => $item->n_value,
                                "campaignXvalue" => $item->x_value,
                                "campaignDiscount" => ($prodDiscount * -1),
                                "products" => $pid,
                                "productAmount" => $subAmount
                            );
                        }
                        $compare_n_value = $item->n_value;
                    }
                }

                $thresholdDiscount_display = []; //重新調整結構for前端使用
                if (isset($thresholdDiscount)) {
                    foreach ($thresholdDiscount as $campaign_id => $data) {
                        $thresholdAmount += $data['campaignDiscount'];
                        $thresholdDiscount_display[] = $data;
                    }
                }
            }
            //滿額折扣CART_P01 & CART_P02
            $calc_amount = [];
            $calc_qty[] = 0;
            //滿額送贈 CART_P03 & CART_P04
            if (isset($prod_campaign['GIFT'])) {
                foreach ($prod_campaign['GIFT'] as $campaign_id => $product_in_campaign) {
                    $pid = [];
                    $subAmount = [];
                    $price = 0;
                    $quantity = 0;
                    $tmp_product_id = 0;
                    $tmp_thresholdDiscount[$campaign_id] = 0;
                    foreach ($product_in_campaign as $key => $product_id) {
                        if ($tmp_product_id != $product_id) {
                            $price += $prod_amount[$product_id];
                            $quantity += $prod_qty[$product_id];
                            $pid[] = $product_id;
                            $subAmount[] = $prod_amount[$product_id];
                            $tmp_product_id = $product_id;
                            if (isset($tmp_calc)) {
                                if (array_key_exists($product_id, $tmp_calc)) {
                                    if ($tmp_thresholdDiscount[$campaign_id] == 0) {
                                        $tmp_thresholdDiscount[$campaign_id] += $tmp_calc[$product_id];
                                    }
                                }
                            }
                        }
                    }
                    $calc_amount[$campaign_id] = ($price + $tmp_thresholdDiscount[$campaign_id]);
                    $calc_qty[$campaign_id] = $quantity;
                    $compare_n_value = 0;
                    $campaign_threshold = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P03') { //﹝滿額﹞指定商品滿N元，送贈
                            if ($calc_amount[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P04') { //﹝滿額﹞指定商品滿N件送贈
                            if ($calc_qty[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_qty[$campaign_id]) continue;
                        }
                        $compare_n_value = $item->n_value;
                        $campaign_threshold = $item->id;
                    }
                    $prods = [];
                    $compare_n_value = 0;
                    foreach ($campaignThresholdItem[$campaign_id] as $threshold => $item) {
                        if ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P03') { //﹝滿額﹞指定商品滿N元，送贈
                            if ($calc_amount[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_amount[$campaign_id]) continue;
                        } elseif ($campaignThresholdMain[$campaign_id]->campaign_type == 'CART_P04') { //﹝滿額﹞指定商品滿N件送贈
                            if ($calc_qty[$campaign_id] < $item->n_value) continue;
                            if ($compare_n_value >= $calc_qty[$campaign_id]) continue;
                        }
                        $compare_n_value = $item->n_value;
                        if ($item->id == $campaign_threshold) {
                            $prd = 0;
                            foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                                $gift_array[$item->id] = 0;
                                $gift_check[$item->id] = 0;
                                foreach ($giftawayInfo as $giftInfo => $v) {
                                    $prd = isset($prod_qty[$v['product_id']]) ? $prod_qty[$v['product_id']] : 0; //購物車中單品的數量
                                    //if (isset($threshold_prod[$v['product_id']])) {
                                        $gift_array[$item->id]++;
                                        if (isset($stock_gift_check[$v['product_id']])) {
                                            if (($stock_gift_check[$v['product_id']]->stock_qty - $v->assigned_qty - $prd) >= 0) {
                                                $gift_check[$item->id]++;
                                            }
                                        }
                                    //}
                                }
                            }
                            if ($gift_check[$item->id] > 0 && $gift_array[$item->id] == $gift_check[$item->id]) {//只要有缺貨就全部不呈現
                                foreach ($campaignThresholdGift[$campaign_id][$item->id] as $key => $giftawayInfo) {
                                    unset($prods[$v['product_id']]);
                                    if ($gift_array[$item->id] > 0 && $gift_array[$item->id] == $gift_check[$item->id]) {
                                        foreach ($giftawayInfo as $giftInfo => $v) {
                                            $prd = isset($prod_qty[$v['product_id']]) ? $prod_qty[$v['product_id']] : 0; //購物車中單品的數量
                                            if (isset($threshold_prod[$v['product_id']])) {
                                                if (isset($stock_gift_check[$v['product_id']])) {
                                                    if (($stock_gift_check[$v['product_id']]->stock_qty - $v->assigned_qty - $prd) >= 0) {
                                                        $prods[$v['product_id']] = array(
                                                            "productPhoto" => $threshold_prod[$v['product_id']]->photo,
                                                            "productId" => $v['product_id'],
                                                            "productName" => $threshold_prod[$v['product_id']]->product_name,
                                                            "sellingPrice" => $threshold_prod[$v['product_id']]->selling_price,
                                                            "assignedQty" => $v->assigned_qty,
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $prods_display = [];//重新調整結構for前端使用
                        if (isset($prods)) {
                            foreach ($prods as $prod) {
                                $prods_display[] = $prod;
                            }
                        }
                        if (isset($prods)) {
                            if (count($prods) > 0) {
                                $thresholdGiftAway[$campaign_id] = array(
                                    "thresholdID" => $item->id,
                                    "campaignID" => $campaign_id,
                                    "campaignName" => $campaignThresholdMain[$campaign_id]->campaign_name,
                                    "campaignBrief" => $campaignThresholdMain[$campaign_id]->campaign_brief,
                                    "campaignUrlCode" => $campaignThresholdMain[$campaign_id]->url_code,
                                    "thresholdBrief" => $item->threshold_brief,
                                    "campaignNvalue" => $item->n_value,
                                    "campaignXvalue" => $item->x_value,
                                    "campaignProdList" => $prods_display,
                                    "products" => $pid,
                                    "productAmount" => $subAmount
                                );
                            }
                        }
                    }
                }
                $thresholdGiftAway_display = []; //重新調整結構for前端使用
                if (isset($thresholdGiftAway)) {
                    foreach ($thresholdGiftAway as $campaign_id => $data) {
                        $thresholdGiftAway_display[] = $data;
                    }
                }
            }
            //滿額送贈 CART_P03 & CART_P04

            //全車滿額贈
            $cartDiscount = 0;
            $compare_n_value = 0;
            foreach ($campaign_discount as $items => $item) {
                if ($compare_n_value > $item->n_value) {
                    continue;
                }
                if ($cartTotal >= $item->n_value) {
                    if ($item->campaign_type == 'CART01') { //﹝滿額﹞購物車滿N元，打X折
                        $cartDiscount += $cartTotal - ($cartTotal * $item->x_value); //打折10000-(10000*0.85)
                    } elseif ($item->campaign_type == 'CART02') { //﹝滿額﹞購物車滿N元，折X元
                        $cartDiscount += $item->x_value; //打折後10000-1000
                    }
                    $compare_n_value = $item->n_value;
                }
            }
            if ($cartDiscount > 0) {
                $cartDiscount = $cartDiscount + $thresholdAmount;
            }
            $total_amount = ($cartTotal - $cartDiscount);
            if (isset($campaign_gift['CART'])) {
                $compare_value = 0;
                foreach ($campaign_gift['CART'] as $items => $item) {
                    if ($total_amount >= $item->n_value) {
                        if ($now >= $item->start_launched_at && $now <= $item->end_launched_at) { //在上架期間內
                            if ($item->campaign_type == 'CART03') { //﹝滿額﹞購物車滿N元，送贈品
                                if ($item->assignedQty > 0) {
                                    $stock_check = $this->stockService->getStockByProd($warehouseCode, $item->product_id);
                                    if (isset($stock_check)) {
                                        if ($this->stockService->getStockByProd($warehouseCode, $item->product_id)->stock_qty > 0) { //有足夠庫存
                                            if ($compare_value > $item->n_value) {
                                                continue;
                                            }

                                            $cartGift[] = array(
                                                "campaignId" => $item->promotional_campaign_id,
                                                "campaignName" => $item->campaign_name,
                                                "campaignBrief" => $item->campaign_brief,
                                                "productId" => $item->product_id,
                                                "productName" => $item->product_name,
                                                "sellingPrice" => $item->selling_price,
                                                "productPhoto" => $campaign_gift['PROD'][$item->promotional_campaign_id][$item->product_id]['photo'],
                                                "assignedQty" => $item->assignedQty,
                                            );
                                            $compare_value = $item->n_value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($assigned as $campaign_id => $item) {
                $assigned_qty = array_sum($assigned[$campaign_id]);
                if ($assigned_qty >= $CART04_n[$campaign_id]) {
                    foreach ($campaign_gift['PROD'][$campaign_id] as $prod_id => $value) {
                        if ($value->assignedQty > 0) {
                            if (($this->stockService->getStockByProd($warehouseCode, $prod_id)->stock_qty - $value->assignedQty) >= 0) { //有足夠庫存
                                $cartGift[] = array(
                                    "campaignId" => $value->promotional_campaign_id,
                                    "campaignName" => $value->campaign_name,
                                    "campaignBrief" => $value->campaign_brief,
                                    "productId" => $prod_id,
                                    "sellingPrice" => $value->selling_price,
                                    "productName" => $value->product_name,
                                    "productPhoto" => $value->photo,
                                    "assignedQty" => $value->assignedQty,
                                );
                            }
                        }
                    }
                }
            }

            //會員可用點數
            $pointData = $this->apiService->getMemberPoint($member_id);
            $info = json_decode($pointData);
            $pointInfo = [];

            $pointInfo = array(
                "point" => $info->data->point,
                "discountRate" => $info->data->discountRate,
                "exchangeRate" => $info->data->exchangeRate,
                "discountMax" => ($info->data->point > 0 ? floor(($total_amount + $thresholdAmount) * $info->data->discountRate) : 0),
            );

            //運費規則
            $fee = [];
            if (($cartTotal - round($cartDiscount) + round($thresholdAmount)) < $shippingFee['HOME']->free_threshold) {
                $fee = $shippingFee['HOME']->shipping_fee;
            } else {
                $fee = 0;
            }
            $cart = [];

            $cart = array(
                "feeInfo" => $feeInfo,
                "productRow" => $productRow,
                "list" => $productDetail,
                "totalPrice" => $cartTotal,
                "thresholdDiscountDisplay" => false,
                "thresholdDiscount" => isset($thresholdDiscount_display) ? $thresholdDiscount_display : [],
                "thresholdAmount" => round($thresholdAmount),
                "thresholdGiftAway" => isset($thresholdGiftAway_display) ? $thresholdGiftAway_display : [],
                "discount" => round($cartDiscount),
                "giftAway" => $cartGift,
                "point" => $pointInfo,
                "shippingFee" => $fee,
                "checkout" => $cartTotal - round($cartDiscount) + round($thresholdAmount),
            );

            //有符合的滿額折扣時
            if (count($cart['thresholdDiscount']) > 0) {
                //取得滿額門檻內容
                foreach ($cart['thresholdDiscount'] as $key => $threshold) {
                    $total = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        $threshold_prod['value'][$product_id] = $threshold['campaignXvalue']; //折數、折價金額
                        $threshold_prod['price'][$product_id] = $threshold['productAmount'][$k]; //單品小計
                        $threshold_prod['thresholdDiscount'][$product_id] = $threshold['thresholdID']; //商品對應的門檻ID
                        $threshold_prod['thresholdBrief'][$product_id] = $threshold['thresholdBrief']; //門檻文案
                        $total += $threshold['productAmount'][$k]; //單品金額加總
                    }
                    $threshold_discount['discount'][$threshold['thresholdID']] = $threshold['campaignDiscount']; //門檻折扣
                    $threshold_discount['price'][$threshold['thresholdID']] = $total; //符合門檻總金額
                }
                //計算門檻折扣
                foreach ($cart['list'] as $products) { //主產品
                    foreach ($products['itemList'] as $item) { //細項多規
                        //售價*數量-單品折抵(itemDiscount) = 單品折抵後的小計($tmp_subtotal)
                        $tmp_subtotal = ($products['sellingPrice'] * $item['itemQty'] + $item['itemDiscount']);
                        //滿額門檻計算$threshold_prod['price']
                        if (isset($threshold_prod['value'][$products['productID']])) {
                            if ($threshold_prod['value'][$products['productID']] < 1) { //折數
                                $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($tmp_subtotal - round($tmp_subtotal * $threshold_prod['value'][$products['productID']])) * -1;
                            } else {
                                $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($threshold_prod['value'][$products['productID']] * ($tmp_subtotal / $threshold_discount['price'][$threshold_prod['thresholdDiscount'][$products['productID']]])) * -1;
                            }
                        } else {
                            $cart_p_discount_prod[$products['productID']][$item['itemId']] = 0;
                        }
                    }
                }
                //把最後一筆資料比例做修正
                foreach ($cart['thresholdDiscount'] as $key => $threshold) {
                    $threshold_discount['discount'][$threshold['thresholdID']] = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        foreach ($cart['list'] as $products) {
                            if ($products['productID'] == $product_id) {
                                foreach ($products['itemList'] as $item) {
                                    $threshold_discount['discount'][$threshold['thresholdID']] += $cart_p_discount_prod[$product_id][$item['itemId']];
                                    $threshold_discount['end'][$threshold['thresholdID']] = array($product_id, $item['itemId']);
                                }
                            }
                        }
                    }
                    if (($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]) != 0) {
                        //要修正的差額
                        $tmp_discount = ($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]);
                        //找出該門檻最後一筆的商品
                        $last_threshold_productID = $threshold_discount['end'][$threshold['thresholdID']][0];   //product_id
                        $last_threshold_itemId = $threshold_discount['end'][$threshold['thresholdID']][1];      //prouct_item_id
                        $cart_p_discount_prod[$last_threshold_productID][$last_threshold_itemId] = $cart_p_discount_prod[$last_threshold_productID][$last_threshold_itemId] + $tmp_discount;
                    }
                }

                //重構商品折扣
                foreach ($cart['list'] as $productKey => $products) { //主產品
                    $products['campaignThresholdDiscount']['campaignThresholdStatus'] = false;  //不滿足活動時狀態為false
                    foreach ($products['itemList'] as $key => $item) { //細項多規
                        //活動折抵 = C002單品折抵 + C003滿額折抵 (負數)
                        $products['itemList'][$key]['campaignDiscount'] = $item['campaignDiscount'] + $cart_p_discount_prod[$products['productID']][$item['itemId']];
                        //小計 = 售價 x 數量 - 活動折抵 (如上,C002單品折抵 + C003滿額折抵) 因為活動折抵計算時為負數，所以用加的
                        $products['itemList'][$key]['amount'] = $products['sellingPrice'] * $item['itemQty'] + $products['itemList'][$key]['campaignDiscount'];
                        //活動價 = 小計 / 數量 (四捨五入至整數位)
                        $products['itemList'][$key]['itemPrice'] = round($products['itemList'][$key]['amount'] / $item['itemQty']);
                        $products['itemList'][$key]['itemCartDiscount'] = $cart_p_discount_prod[$products['productID']][$item['itemId']];
                    }
                    if (count($products['campaignThresholdDiscount']) > 0) {    //如果有門檻活動
                        //重構門檻活動
                        $check = 0;
                        if (isset($products['campaignThresholdDiscount']['campaignThreshold'])) {
                            foreach ($products['campaignThresholdDiscount']['campaignThreshold'] as $thresholdKey => $thresholdBrief) {
                                if (isset($threshold_prod['thresholdBrief'][$products['productID']])) {
                                    if ($threshold_prod['thresholdBrief'][$products['productID']] == $thresholdBrief) {
                                        $check++;
                                        $products['campaignThresholdDiscount']['campaignThreshold'] = $thresholdBrief;
                                    }
                                }
                            }
                        }
                        if ($check > 0) {
                            $products['campaignThresholdDiscount']['campaignThresholdStatus'] = true;   //滿足活動時狀態為true
                        }
                    }
                    $cart['list'][$productKey] = $products;
                }
            } else {
                //重構門檻活動
                foreach ($cart['list'] as $productKey => $products) { //主產品
                    $products['campaignThresholdDiscount']['campaignThresholdStatus'] = false;  //不滿足活動時狀態為false
                    $cart['list'][$productKey] = $products;
                }
            }

            return json_encode(array("status" => 200, "result" => $cart));
        }
    }

}
