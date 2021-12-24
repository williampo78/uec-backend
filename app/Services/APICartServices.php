<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ShoppingCartDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductItems;
use App\Models\Products;
use App\Services\APIService;
use App\Services\StockService;

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
        $result = ShoppingCartDetails::select("products.id as product_id", "products.product_no", "products.product_name", "products.list_price", "products.selling_price", "products.start_launched_at", "products.end_launched_at"
            , "product_items.id as item_id", "shopping_cart_details.qty as item_qty", "product_items.spec_1_value as item_spec1", "product_items.spec_2_value as item_spec2"
            , "product_items.item_no", "product_items.photo_name as item_photo", "product_items.status as item_status")
            ->where('shopping_cart_details.member_id', $member_id)
            ->where('shopping_cart_details.status_code', 0)//購物車
            ->join('product_items', 'product_items.id', '=', 'shopping_cart_details.product_item_id')
            ->join('products', 'products.id', '=', 'product_items.product_id')
            ->where('products.approval_status', '=', 'APPROVED')//核準上架
            ->orderBy('product_items.sort', 'asc')
            ->get();

        $data = [];
        foreach ($result as $datas) {
            $data[$datas->product_id] = $datas;
        }
        return $data;
    }

    /*
     * 取得會員目前購物車數量
     * @params:member_id
     */
    public function getCartCount($member_id)
    {
        $wordCount = ShoppingCartDetails::where('member_id', '=', $member_id)
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
        $item = ProductItems::where('id', $input['item_id'])->where('item_no', $input['item_no'])->get()->toArray();
        if (count($item) > 0) {
            $data = ShoppingCartDetails::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->get()->toArray();
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
                $webData['product_item_id'] = $input['item_id'];
                $webData['status_code'] = $input['status_code'];
                $webData['qty'] = $input['item_qty'];
                $webData['utm_source'] = $input['utm_source'];
                $webData['utm_medium'] = $input['utm_medium'];
                $webData['utm_campaign'] = $input['utm_campaign'];
                $webData['utm_sales'] = $input['utm_sales'];
                $webData['utm_time'] = $input['utm_time'];
                $webData['created_by'] = $member_id;
                $webData['updated_by'] = -1;
                $webData['created_at'] = $now;
                $webData['updated_at'] = $now;
                if ($input['status_code'] != 0) {
                    return '203';
                }
                $new_id = ShoppingCartDetails::insertGetId($webData);
            } else if ($act == 'upd') {
                $webData['qty'] = $input['item_qty'];
                $webData['status_code'] = $input['status_code'];
                $webData['updated_by'] = $member_id;
                $webData['updated_at'] = $now;
                $new_id = ShoppingCartDetails::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->update($webData);
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
    public function getCartData($member_id, $campaigns, $campaign_gift, $campaign_discount)
    {
        $now = Carbon::now();
        $productInfo = self::getProducts();
        //購物車內容
        $cartInfo = self::getCartInfo($member_id);
        $shippingFee = ShippingFeeRulesService::getShippingFee('HOME');
        $feeInfo = array(
            "notice" => $shippingFee['HOME']->notice_brief,
            "noticeDetail" => $shippingFee['HOME']->notice_detailed
        );
        if (count($cartInfo) == 0) {
            return json_encode(array("status" => 404, "result" => $feeInfo));
        } else {
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
                $cartQty[$item->product_id][$item->item_id] = $item->item_qty; //購物車數量
                $cartAmount[$item->product_id] = intval($item->selling_price); //商品售價
                $cartDetail[$item->product_id][$item->item_id] = $item; //購物車內容
            }
            //行銷活動
            foreach ($campaigns as $product_id => $item) {
                foreach ($item as $k => $v) {
                    if ($now >= $v->start_at and $now <= $v->end_at) { //活動時間內才做
                        //先取活動類型為單品或車子(贈品或折扣) $campaign['PRD']['DISCOUNT'] 單品折扣
                        $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                        if ($v->campaign_type == 'CART04') {
                            $CART04[$v->id][$product_id] = 1;
                            $CART04_n[$v->id] = $v->n_value;//CART04 同系列商品滿額條件
                        }
                    }
                }
            }
            $prod_gift = [];
            foreach ($cartQty as $product_id => $item) {
                $product = [];
                if ($now >= $cartInfo[$product_id]['start_launched_at'] && $now <= $cartInfo[$product_id]['end_launched_at']) { //在上架期間內
                    $product_type = "effective";
                    $qty = array_sum($item); //合併不同規格但同一商品的數量

                    //商品贈品
                    $giftAway = [];
                    if (isset($campaign['PRD']['GIFT'][$product_id])) { //在活動內 滿額贈禮
                        if ($campaign['PRD']['GIFT'][$product_id]->campaign_type == 'PRD05') {
                            foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                $giftAway[] = array(
                                    "productId" => $giftInfo->product_id,
                                    "productName" => $productInfo[$giftInfo->product_id]->product_name
                                );
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
                                foreach ($item as $item_id => $detail_qty) { //取得item規格數量
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
                                        $prod_gift = array(
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }
                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = $stock_info->specifiedQty;
                                    $product[] = array(
                                        "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "shortageOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += intval($amount);
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD02') { //﹝單品﹞第N件(含)以上，折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000-200
                                foreach ($item as $item_id => $detail_qty) { //取得item規格數量
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
                                        $prod_gift = array(
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }

                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = $stock_info->specifiedQty;
                                    $product[] = array(
                                        "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "shortageOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountStatus" => $return_type,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += intval($amount);
                                }
                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD03') { //﹝單品﹞滿N件，每件打X折
                                $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000*0.85

                                foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);

                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                        $prod_gift = array(
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }

                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = $stock_info->specifiedQty;
                                    $product[] = array(
                                        "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "shortageOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountStatus" => true,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += intval($amount);
                                }

                            } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD04') { //﹝單品﹞滿N件，每件折X元
                                $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000-200

                                foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                    $tmp_qty = $detail_qty;
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);
                                    //找符合的item放
                                    if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                        $prod_gift = array(
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }
                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = $stock_info->specifiedQty;
                                    $product[] = array(
                                        "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "shortageOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountName" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                        "campaignDiscountStatus" => true,
                                        "campaignGiftAway" => $prod_gift
                                    );
                                    $cartTotal += intval($amount);
                                }
                            }
                        } else { //沒有打折的件數
                            //找符合的item放
                            if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                $prod_gift = array(
                                    "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                    "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                    "campaignProdList" => $giftAway
                                );
                            }
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                                $stock = $stock_info->specifiedQty;
                                $product[] = array(
                                    "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                    "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                    "itemSpec1" => $spec1,
                                    "itemSpec2" => $spec2,
                                    "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                                    "itemQty" => $detail_qty,
                                    "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                                    "itemStock" => $stock,
                                    "shortageOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                                    "campaignDiscountName" => null,
                                    "campaignDiscountStatus" => false,
                                    "campaignGiftAway" => $prod_gift
                                );
                                $cartTotal += intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty);
                            };
                        }
                    } else { //不在活動內
                        foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                            $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                            $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                            $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                            $stock = $stock_info->specifiedQty;
                            $product[] = array(
                                "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                                "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                "itemSpec1" => $spec1,
                                "itemSpec2" => $spec2,
                                "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                                "itemQty" => $detail_qty,
                                "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                                "itemStock" => $stock,
                                "shortageOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                                "campaignDiscountName" => null,
                                "campaignDiscountStatus" => false,
                                "campaignGiftAway" => []
                            );
                            $cartTotal += intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty);
                        };
                    }

                    //指定商品贈品
                    foreach ($CART04 as $campaign_id => $prod) { //同系列只送一次
                        if (key_exists($product_id, $prod)) {
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $assigned[$campaign_id][$item_id] = intval($detail_qty);
                            }
                        }
                    }
                } else {
                    foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                        $product_type = 'expired';
                        $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                        $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                        $stock_info = $this->stockService->getStockByItem('WHS01', $cartDetail[$product_id][$item_id]->item_id);
                        $stock = $stock_info->specifiedQty;
                        $product[] = array(
                            "itemPhoto" => $cartDetail[$product_id][$item_id]->item_photo,
                            "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                            "itemSpec1" => $spec1,
                            "itemSpec2" => $spec2,
                            "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                            "itemQty" => $detail_qty,
                            "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                            "itemStock" => $stock,
                            "shortageOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                            "campaignDiscountName" => null,
                            "campaignDiscountStatus" => false,
                            "campaignGiftAway" => []
                        );
                        $cartTotal += 0;
                    }
                }
                $productDetail[] = array(
                    "productType" => $product_type,
                    "productID" => $product_id,
                    "productNo" => $cartInfo[$product_id]['product_no'],
                    "productName" => $cartInfo[$product_id]['product_name'],
                    "itemList" => $product
                );
            }
            //全車滿額贈
            $cartDiscount = 0;
            foreach ($campaign_discount as $items => $item) {
                if ($cartTotal >= $item->n_value) {
                    if ($item->campaign_type == 'CART01') { //﹝滿額﹞購物車滿N元，打X折
                        $cartDiscount += $cartTotal - ($cartTotal * $item->x_value); //打折10000-(10000*0.85)
                    } elseif ($item->campaign_type == 'CART02') { //﹝滿額﹞購物車滿N元，折X元
                        $cartDiscount += $item->x_value; //打折後10000-1000
                    }
                }
            }
            $total_amount = ($cartTotal - $cartDiscount);
            foreach ($campaign_gift['CART'] as $items => $item) {
                if ($total_amount >= $item->n_value) {
                    if ($now >= $item->start_launched_at && $now <= $item->end_launched_at) { //在上架期間內
                        if ($item->campaign_type == 'CART03') { //﹝滿額﹞購物車滿N元，送贈品
                            $cartGift[] = array(
                                "campaignName" => $item->campaign_name,
                                "productId" => $item->product_id,
                                "productName" => $item->product_name
                            );
                        }
                    }
                }
            }
            foreach ($assigned as $campaign_id => $item) {
                $assigned_qty = array_sum($assigned[$campaign_id]);
                if ($assigned_qty >= $CART04_n[$campaign_id]) {
                    foreach ($campaign_gift['PROD'][$campaign_id] as $prod_id => $value) {
                        $cartGift[] = array(
                            "campaignName" => $value->campaign_name,
                            "productId" => $prod_id,
                            "productName" => $value->product_name
                        );
                    }
                }
            }
            //會員可用點數
            $pointData = $this->apiService->getMemberPoint($member_id);
            $info = json_decode($pointData);
            $pointInfo = [];
            if ($info->data->point > 0) {
                $discountRate = $total_amount * $info->data->discountRate; //點數折抵上限率，若值為0.3，總金額1000元，折抵上限為300元(1000x0.3=300)
                //$exchangeRate = $total_amount * $info->data->exchangeRate; //點數兌換現金率，若值為0.01，100點等同1元現金(100x0.01=1)
                $pointInfo = array(
                    "point" => $info->data->point,
                    "discountRate" => $info->data->discountRate,
                    "exchangeRate" => $info->data->exchangeRate,
                    "discountMax" => $discountRate,
                );
            } else {
                $pointInfo = array(
                    "point" => 0,
                    "discountRate" => 0,
                    "exchangeRate" => 0,
                    "discountMax" => 0,
                );
            }

            //運費規則
            $fee = [];
            if ($total_amount < $shippingFee['HOME']->free_threshold) {
                $fee = $shippingFee['HOME']->shipping_fee;
            } else {
                $fee = 0;
            }
            $cart = [];
            $cart = array(
                "feeInfo" => $feeInfo,
                "productRow" => count($cartInfo),
                "list" => $productDetail,
                "totalprice" => $cartTotal,
                "discount" => $cartDiscount,
                "giftAway" => $cartGift,
                "point" => $pointInfo,
                "shippingFee" => $fee,
                "checkout" => $total_amount,
            );
            return json_encode(array("status" => 200, "result" => $cart));
        }
    }

    /*
     * 取得商品資訊 (上架審核通過 & 上架期間內)
     */
    public function getProducts()
    {
        $data = [];
        $products = Products::all();
        foreach ($products as $product) {
            $data[$product->id] = $product;
        }
        return $data;
    }
}
