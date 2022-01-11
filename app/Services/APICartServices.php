<?php


namespace App\Services;

use App\Models\ProductPhotos;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ShoppingCartDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductItems;
use App\Models\Products;
use App\Services\APIService;
use App\Services\StockService;
use Batch;

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
        $result = ShoppingCartDetails::select("products.id as product_id", "products.product_no", "products.product_name", "products.list_price", "products.selling_price", "products.start_launched_at", "products.end_launched_at"
            , "product_items.id as item_id", "shopping_cart_details.qty as item_qty", "product_items.spec_1_value as item_spec1", "product_items.spec_2_value as item_spec2"
            , "product_items.item_no")
            ->where('shopping_cart_details.member_id', $member_id)
            ->where('shopping_cart_details.status_code', 0)//購物車
            ->join('product_items', 'product_items.id', '=', 'shopping_cart_details.product_item_id')
            ->join('products', 'products.id', '=', 'product_items.product_id')
            ->where('products.approval_status', '=', 'APPROVED')//核準上架
            ->orderBy('product_items.sort', 'asc')
            ->get();

        $data = [];
        foreach ($result as $datas) {
            $ProductPhotos = ProductPhotos::where('product_id', $datas->product_id)->orderBy('sort', 'asc')->first();
            $data[$datas->product_id] = $datas;
            $data[$datas->product_id]['item_photo'] = (isset($ProductPhotos->photo_name) ? $s3 . $ProductPhotos->photo_name : null);
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
        $item = ProductItems::where('id', $input['item_id'])->get()->toArray();
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
                $webData['qty'] = ($input['status_code'] == 0 ? $input['item_qty'] : 0);
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
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $shippingFee = ShippingFeeRulesService::getShippingFee('HOME');
        $feeInfo = array(
            "shipping_fee" => $shippingFee['HOME']->shipping_fee,
            "free_threshold" => $shippingFee['HOME']->free_threshold,
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
            foreach ($cartQty as $product_id => $item) {
                $product = [];
                $prod_gift = [];
                if ($now >= $cartInfo[$product_id]['start_launched_at'] && $now <= $cartInfo[$product_id]['end_launched_at']) { //在上架期間內
                    $product_type = "effective";
                    $qty = array_sum($item); //合併不同規格但同一商品的數量

                    //商品贈品
                    $giftAway = [];
                    if (isset($campaign['PRD']['GIFT'][$product_id])) { //在活動內 滿額贈禮
                        if ($campaign['PRD']['GIFT'][$product_id]->campaign_type == 'PRD05') {
                            foreach ($campaign_gift['PROD'][$campaign['PRD']['GIFT'][$product_id]->id] as $giftInfo) {
                                $giftAway[] = array(
                                    "productPhoto" => $giftInfo['photo'],
                                    "productId" => $giftInfo->product_id,
                                    "productName" => $productInfo[$giftInfo->product_id]->product_name,
                                    "assignedQty" => $giftInfo->assignedQty
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
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }
                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    $product[] = array(
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
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
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }

                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    $product[] = array(
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
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
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }

                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    $product[] = array(
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
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
                                            "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                            "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                            "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                            "campaignProdList" => $giftAway
                                        );
                                    }
                                    $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                    $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                    $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                                    $stock = 0;
                                    if ($stock_info) {
                                        $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                    }
                                    $product[] = array(
                                        "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                        "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                        "itemSpec1" => $spec1,
                                        "itemSpec2" => $spec2,
                                        "itemPrice" => intval($unit_price),
                                        "itemQty" => $return_qty,
                                        "amount" => intval($amount),
                                        "itemStock" => $stock,
                                        "outOfStock" => (($stock - $return_qty) < 0 ? true : false),
                                        "campaignDiscountId" => $campaign['PRD']['DISCOUNT'][$product_id]->id,
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
                                    "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                    "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                    "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                    "campaignProdList" => $giftAway
                                );
                            }
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                                $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                                $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                                $stock = 0;
                                if ($stock_info) {
                                    $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                                }
                                $product[] = array(
                                    "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                    "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                    "itemSpec1" => $spec1,
                                    "itemSpec2" => $spec2,
                                    "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                                    "itemQty" => $detail_qty,
                                    "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                                    "itemStock" => $stock,
                                    "outOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                                    "campaignDiscountId" => null,
                                    "campaignDiscountName" => null,
                                    "campaignDiscountStatus" => false,
                                    "campaignGiftAway" => $prod_gift
                                );
                                $cartTotal += intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty);
                            };
                        }
                    } else { //不在活動內
                        foreach ($item as $item_id => $detail_qty) { //取得item規格數量

                            //找符合的item放
                            if (isset($campaign['PRD']['GIFT'][$product_id])) {
                                $prod_gift = array(
                                    "campaignGiftId" => $campaign['PRD']['GIFT'][$product_id]->id,
                                    "campaignGiftName" => $campaign['PRD']['GIFT'][$product_id]->campaign_name,
                                    "campaignGiftStatus" => ($qty >= $campaign['PRD']['GIFT'][$product_id]->n_value ? true : false),
                                    "campaignProdList" => $giftAway
                                );
                            }

                            $spec1 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec1 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec1);
                            $spec2 = ($cartDetail[$product_id][$item_id]->item_spec1 == 0 || $cartDetail[$product_id][$item_id]->item_spec2 == '' ? null : $cartDetail[$product_id][$item_id]->item_spec2);
                            $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                            $stock = 0;
                            if ($stock_info) {
                                $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                            }
                            $product[] = array(
                                "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                                "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                                "itemSpec1" => $spec1,
                                "itemSpec2" => $spec2,
                                "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                                "itemQty" => $detail_qty,
                                "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                                "itemStock" => $stock,
                                "outOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                                "campaignDiscountId" => null,
                                "campaignDiscountName" => null,
                                "campaignDiscountStatus" => false,
                                "campaignGiftAway" => $prod_gift
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
                        $stock_info = $this->stockService->getStockByItem($warehouseCode, $cartDetail[$product_id][$item_id]->item_id);
                        $stock = 0;
                        if ($stock_info) {
                            $stock = ($stock_info->stockQty <= $stock_info->limitedQty ? $stock_info->stockQty : $stock_info->limitedQty);
                        }
                        $product[] = array(
                            "itemId" => $cartDetail[$product_id][$item_id]->item_id,
                            "itemNo" => $cartDetail[$product_id][$item_id]->item_no,
                            "itemSpec1" => $spec1,
                            "itemSpec2" => $spec2,
                            "itemPrice" => intval($cartDetail[$product_id][$item_id]->selling_price),
                            "itemQty" => $detail_qty,
                            "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                            "itemStock" => $stock,
                            "outOfStock" => (($stock - $detail_qty) < 0 ? true : false),
                            "campaignDiscountId" => null,
                            "campaignDiscountName" => null,
                            "campaignDiscountStatus" => false,
                            "campaignGiftAway7" => []
                        );
                        $cartTotal += 0;
                    }
                }
                $productDetail[] = array(
                    "productType" => $product_type,
                    "productID" => $product_id,
                    "productNo" => $cartInfo[$product_id]['product_no'],
                    "productName" => $cartInfo[$product_id]['product_name'],
                    "sellingPrice" => $cartInfo[$product_id]['selling_price'],
                    "productPhoto" => $cartInfo[$product_id]['item_photo'],
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
            if (isset($campaign_gift['CART'])) {
                foreach ($campaign_gift['CART'] as $items => $item) {
                    if ($total_amount >= $item->n_value) {
                        if ($now >= $item->start_launched_at && $now <= $item->end_launched_at) { //在上架期間內
                            if ($item->campaign_type == 'CART03') { //﹝滿額﹞購物車滿N元，送贈品
                                if ($item->assignedQty > 0) {
                                    $stock_check = $this->stockService->getStockByProd($warehouseCode, $item->product_id);
                                    if (isset($stock_check)) {
                                        if ($this->stockService->getStockByProd($warehouseCode, $item->product_id)->stock_qty > 0) { //有足夠庫存
                                            $cartGift[] = array(
                                                "campaignId" => $item->promotional_campaign_id,
                                                "campaignName" => $item->campaign_name,
                                                "productId" => $item->product_id,
                                                "productName" => $item->product_name,
                                                "sellingPrice" => $item->selling_price,
                                                "productPhoto" => $campaign_gift['PROD'][$item->promotional_campaign_id][$item->product_id]['photo'],
                                                "assignedQty" => $item->assignedQty
                                            );
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
                            if ($this->stockService->getStockByProd($warehouseCode, $prod_id)->stock_qty > 0) { //有足夠庫存
                                $cartGift[] = array(
                                    "campaignId" => $value->promotional_campaign_id,
                                    "campaignName" => $value->campaign_name,
                                    "productId" => $prod_id,
                                    "productName" => $value->product_name,
                                    "productPhoto" => $value->photo,
                                    "assignedQty" => $value->assignedQty
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
                "discountMax" => ($info->data->point > 0 ? $total_amount * $info->data->discountRate : 0),
            );

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
                "totalPrice" => $cartTotal,
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
            $item = ShoppingCartDetails::where('member_id', '=', $member_id)->where('product_item_id', '=', $value)->first();
            if ($item) {
                $webDataUpd[$key] = [
                    "id" => $item->id,
                    "product_item_id" => $input['item_id'][$key],
                    "qty" => ($input['item_qty'][$key] + $item->qty),
                    "status_code" => $input['status_code'],
                    "updated_by" => $member_id,
                    "updated_at" => $now
                ];
            } else {
                $webDataAdd[$key] = [
                    $member_id,
                    $value,
                    $input['item_qty'][$key],
                    $input['status_code'],
                    $input['utm_source'],
                    $input['utm_medium'],
                    $input['utm_campaign'],
                    $input['utm_sales'],
                    $input['utm_time'],
                    $member_id,
                    $member_id,
                    $now,
                    $now
                ];
            }

        }
        $addColumn = [
            "member_id", "product_item_id", "qty", "status_code",
            "utm_source", "utm_medium", "utm_campaign", "utm_sales", "utm_time",
            "created_by", "updated_by", "created_at", "updated_at"
        ];
        DB::beginTransaction();
        try {

            if ($webDataUpd) {
                $cartInstance = new ShoppingCartDetails();
                $upd = Batch::update($cartInstance, $webDataUpd, 'id');
            }

            if ($webDataAdd) {
                $cartInstance = new ShoppingCartDetails();
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
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        //確認是否有該品項
        $item = ProductItems::where('id', $input['item_id'])->get()->toArray();
        if (count($item) > 0) {
            $data = ShoppingCartDetails::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->get()->toArray();
            if (count($data) > 0) {
                $act = 'upd';
                $qty = ($input['item_qty'] + (isset($data[0]['qty']) ? $data[0]['qty'] : 0));
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
                $webData['qty'] = $qty;
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


}
