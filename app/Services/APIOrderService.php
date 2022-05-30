<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\ProductItem;
use App\Models\ShoppingCartDetail;
use App\Models\StockTransactionLog;
use App\Models\WarehouseStock;
use App\Services\APIService;
use App\Services\APITapPayService;
use App\Services\StockService;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class APIOrderService
{
    public function __construct(
        APITapPayService $apiTapPayService,
        StockService $stockService,
        APIService $apiService
    )
    {
        $this->apiTapPayService = $apiTapPayService;
        $this->stockService = $stockService;
        $this->apiService = $apiService;
    }

    /**
     * 訂單
     * @param 購物車清單, 前端的訂單資料
     * @return string
     */
    public function setOrders($cart, $order, $campaigns, $campaign_gift, $campaign_discount, $token = null)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $random = Str::random(6);
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();

        $utms = ShoppingCartDetail::where('member_id', '=', $member_id)->where('status_code', '=', 0)->get();
        $utm_info = [];
        foreach ($utms as $utm) {
            $utm_info[$utm->product_item_id] = $utm;
        }

        $product_items = ProductItem::all();
        $prod_main = [];
        $prod_info = [];
        $prod_items = [];
        $prod_gift = [];
        foreach ($product_items as $product_item) {
            $prod_main[$product_item->product_id] = ProductItem::with('product_id')->find($product_item->product_id);
            $prod_info[$product_item->product_id] = $product_item;
            $prod_items[$product_item->product_id][$product_item->id] = $product_item;
            $stock = $this->stockService->getStockByItem($warehouseCode, $product_item->id);
            if (isset($stock)) {
                if ($stock['stockQty'] > 0) {
                    $prod_gift[$product_item->product_id] = $product_item;
                }
            }
        }
        dd($prod_main);
        //行銷活動
        $campaign_group = [];
        $group_i = 0;
        foreach ($cart['list'] as $products) {
            foreach ($campaigns as $product_id => $item) {
                if ($products['productID'] == $product_id) {
                    foreach ($item as $k => $v) {
                        $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                        if ($v->level_code != 'CART_P') { //單品活動才做
                            $group_i++;
                            $campaign_group[$product_id][$v->id] = $group_i;
                        }
                    }
                }
            }
        }
        //滿額活動
        $threshold_prod = [];
        $threshold_discount = [];
        foreach ($cart['thresholdDiscount'] as $key => $threshold) {
            $group_i++;
            $total = 0;
            foreach ($threshold['products'] as $k => $product_id) {
                $threshold_prod['value'][$product_id] = $threshold['campaignXvalue']; //折數、折價金額
                $threshold_prod['price'][$product_id] = $threshold['productAmount'][$k]; //單品小計
                $threshold_prod['thresholdDiscount'][$product_id] = $threshold['thresholdID']; //門檻ID
                $total += $threshold['productAmount'][$k]; //單品金額加總
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;
            }
            $threshold_discount['discount'][$threshold['thresholdID']] = $threshold['campaignDiscount']; //門檻折扣
            $threshold_discount['price'][$threshold['thresholdID']] = $total; //符合門檻總金額
        }
        foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
            $group_i++;
            foreach ($threshold['products'] as $k => $product_id) {
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;
                $threshold_prod['thresholdGiftaway'][$product_id] = $threshold['thresholdID']; //門檻ID
            }
        }

        DB::beginTransaction();
        try {
            //訂單單頭
            $cart_campaign_discount = ($order['cart_campaign_discount'] < 0 ? ($order['cart_campaign_discount'] - ($cart['thresholdAmount'])) : 0);
            $cart_p_discount = $cart['thresholdAmount'];
            $webData = [];
            $webData['agent_id'] = 1;
            $webData['order_no'] = "OD" . date("ymd") . strtoupper($random);
            $webData['revision_no'] = 0;
            $webData['is_latest'] = 1;
            $webData['ordered_date'] = now();
            $webData['status_code'] = 'CREATED';
            $webData['member_id'] = $member_id;
            $webData['member_account'] = $order['buyer']['mobile'];
            $webData['payment_method'] = $order['payment_method'];
            $webData['is_cash_on_delivery'] = 0;
            $webData['lgst_method'] = $order['lgst_method'];
            $webData['shipping_fee'] = $order['shipping_fee'];
            $webData['is_shipping_free'] = ($order['shipping_fee'] < 1 ? true : false);
            $webData['shipping_free_threshold'] = $cart['feeInfo']['free_threshold'];
            $webData['total_amount'] = $order['total_price'];
            $webData['cart_campaign_discount'] = $cart_campaign_discount; //原C002滿額折抵
            $webData['cart_p_discount'] = $cart_p_discount;//新C003滿額折抵
            $webData['point_discount'] = $order['point_discount'];
            $webData['paid_amount'] = ($order['total_price'] + $order['cart_campaign_discount'] + $order['point_discount'] + $order['shipping_fee'] + $cart_p_discount);
            $webData['points'] = $order['points'];
            $webData['is_paid'] = 0;
            $webData['pay_status'] = 'PENDING';
            $webData['buyer_name'] = $order['buyer']['name'];
            $webData['buyer_mobile'] = $order['buyer']['mobile'];
            $webData['buyer_email'] = $order['buyer']['email'];
            $webData['buyer_zip_code'] = $order['buyer']['zip'];
            $webData['buyer_city'] = $order['buyer']['city'];
            $webData['buyer_district'] = $order['buyer']['district'];
            $webData['buyer_address'] = $order['buyer']['address'];
            $webData['invoice_usage'] = $order['invoice']['usage'];
            $webData['carrier_type'] = $order['invoice']['carrier_type'];
            $webData['carrier_no'] = $order['invoice']['carrier_no'];
            $webData['buyer_gui_number'] = $order['invoice']['buyer_gui_number'];
            $webData['buyer_title'] = $order['invoice']['buyer_title'];
            $webData['donated_institution'] = $order['invoice']['donated_code'];
            $webData['receiver_name'] = $order['receiver']['name'];
            $webData['receiver_mobile'] = $order['receiver']['mobile'];
            $webData['receiver_zip_code'] = $order['receiver']['zip'];
            $webData['receiver_city'] = $order['receiver']['city'];
            $webData['receiver_district'] = $order['receiver']['district'];
            $webData['receiver_address'] = $order['receiver']['address'];
            $webData['store_no'] = $order['store_no'];
            $webData['created_by'] = $member_id;
            $webData['updated_by'] = $member_id;
            $webData['utm_source'] = isset($order['utm']['source']) ? $order['utm']['source'] : null;
            $webData['utm_medium'] = isset($order['utm']['medium']) ? $order['utm']['medium'] : null;
            $webData['utm_campaign'] = isset($order['utm']['campaign']) ? $order['utm']['campaign'] : null;
            $webData['utm_sales'] = isset($order['utm']['sales']) ? $order['utm']['sales'] : null;
            $webData['utm_time'] = isset($order['utm']['time']) ? Carbon::createFromTimestamp($order['utm']['time'])->format('Y-m-d H:i:s') : null;
            $newOrder = Order::create($webData);
            //$newOrder = new Order();
            //$newOrder->id = 843;
            //建立一筆金流單
            $paymantData = [];
            $paymantData['source_table_name'] = 'orders';
            $paymantData['source_table_id'] = $newOrder->id;
            $paymantData['order_no'] = $webData['order_no'];
            $paymantData['payment_type'] = 'PAY';
            $paymantData['payment_method'] = $order['payment_method'];
            $paymantData['payment_status'] = 'PENDING';
            $paymantData['amount'] = $webData['paid_amount'];
            $paymantData['point_discount'] = $webData['point_discount'];
            $paymantData['points'] = $webData['points'];
            $paymantData['record_created_reason'] = 'ORDER_CREATED';
            $paymantData['created_by'] = $member_id;
            $paymantData['updated_by'] = $member_id;
            $newOrderPayment = OrderPayment::create($paymantData);
            //訂單單身
            $seq = 0;
            $details = [];
            $point_rate = 0;
            $discount_group = 0;
            $productID = 0;
            $prod_info_detail = [];
            foreach ($cart['list'] as $products) {
                foreach ($products['itemList'] as $item) {
                    if ($prod_items[$products['productID']][$item['itemId']]) {
                        $prod_info_detail[$products['productID']] = $prod_items[$products['productID']][$item['itemId']];
                    }
                    $seq++;
                    //有活動折扣
                    if ($item['campaignDiscountStatus']) {
                        $discount = -($products['sellingPrice'] * $item['itemQty'] - $item['amount']);
                    } else {
                        $discount = 0;
                    }

                    //滿額門檻計算$threshold_prod['price']
                    if (isset($threshold_prod['value'][$products['productID']])) {
                        if ($threshold_prod['value'][$products['productID']] < 1) { //折數
                            //1800-(1800*0.9)
                            $cart_p_discount_prod[$products['productID']] = round($item['amount'] - round($item['amount'] * $threshold_prod['value'][$products['productID']])) * -1;
                        } else {
                            //500*(1800/1800)
                            $cart_p_discount_prod[$products['productID']] = round($threshold_prod['value'][$products['productID']] * ($item['amount'] / $threshold_discount['price'][$threshold_prod['thresholdDiscount'][$products['productID']]])) * -1;
                        }
                    } else {
                        $cart_p_discount_prod[$products['productID']] = 0;
                    }

                    //有用點數折現金
                    if ($order['point_discount'] < 0) {
                        $discount_rate[$seq] = (($item['amount'] + $cart_p_discount_prod[$products['productID']]) / $order['total_price']);
                        $point_rate += $discount_rate[$seq];
                    } else {
                        $discount_rate[$seq] = 0;
                        $point_rate = 1;
                    }
                    $details[$seq] = [
                        "order_id" => $newOrder->id,
                        "seq" => $seq,
                        "product_id" => $products['productID'],
                        "product_item_id" => $item['itemId'],
                        "item_no" => $item['itemNo'],
                        "selling_price" => $products['sellingPrice'],
                        "qty" => $item['itemQty'],
                        "unit_price" => $item['itemPrice'],
                        "campaign_discount" => $discount,
                        "cart_p_discount" => $cart_p_discount_prod[$products['productID']],
                        "subtotal" => $item['amount'],
                        "record_identity" => "M",
                        "point_discount" => round($discount_rate[$seq] * $order['points']),
                        "points" => round($order['points'] * $discount_rate[$seq] / $cart['point']['exchangeRate']),
                        "utm_source" => $utm_info[$item['itemId']]->utm_source,
                        "utm_medium" => $utm_info[$item['itemId']]->utm_medium,
                        "utm_campaign" => $utm_info[$item['itemId']]->utm_campaign,
                        "utm_sales" => $utm_info[$item['itemId']]->utm_sales,
                        "utm_time" => $utm_info[$item['itemId']]->utm_time,
                        "created_by" => $member_id,
                        "updated_by" => $member_id,
                        "created_at" => now(),
                        "updated_at" => now(),
                        "returned_qty" => 0,
                        "returned_campaign_discount" => 0,
                        "returned_subtotal" => 0,
                        "returned_point_discount" => 0,
                        "returned_points" => 0,
                    ];
                    $order_detail_id_M = OrderDetail::insertGetId($details[$seq]);
                    $order_detail_temp[$products['productID']] = $order_detail_id_M;
                    $campaign_id = 0;
                    //有單品滿額贈時，正貨也寫入discount
                    if (isset($campaign['PRD']['GIFT'][$products['productID']])) {
                        if ($item['campaignGiftAway']['campaignGiftStatus']) {
                            $campaign_details[$seq] = [
                                "order_id" => $newOrder->id,
                                "level_code" => 'PRD',
                                "group_seq" => $campaign_group[$products['productID']][$campaign['PRD']['GIFT'][$products['productID']]->id],
                                "order_detail_id" => $order_detail_id_M,
                                "promotion_campaign_id" => $campaign['PRD']['GIFT'][$products['productID']]->id,
                                "product_id" => $products['productID'],
                                "product_item_id" => $item['itemId'],
                                "item_no" => $item['itemNo'],
                                "discount" => 0,
                                "record_identity" => "M",
                                "created_by" => $member_id,
                                "updated_by" => $member_id,
                                "created_at" => now(),
                                "updated_at" => now(),
                            ];
                            OrderCampaignDiscount::insert($campaign_details[$seq]);
                            $campaign_id = $campaign['PRD']['GIFT'][$products['productID']]->id;
                        }
                    }

                    //訂單明細建立後，更新購物車中的商品狀態為 - 已轉為訂單
                    $updData['status_code'] = 1;
                    ShoppingCartDetail::where('member_id', '=', $member_id)->where('product_item_id', '=', $item['itemId'])->update($updData);

                    //有單品滿額贈品時先新增單身
                    if (isset($item['campaignGiftAway']['campaignProdList'])) {
                        //符合條件

                        if ($item['campaignGiftAway']['campaignGiftStatus']) {
                            //同商品不累贈
                            if ($productID != $products['productID']) {
                                foreach ($item['campaignGiftAway']['campaignProdList'] as $gifts => $gift) {
                                    $seq++;
                                    $details[$seq] = [
                                        "order_id" => $newOrder->id,
                                        "seq" => $seq,
                                        "product_id" => $gift['productId'],
                                        "product_item_id" => $prod_gift[$gift['productId']]['id'],
                                        "item_no" => $prod_gift[$gift['productId']]['item_no'],
                                        "selling_price" => $gift['sellingPrice'],
                                        "qty" => $gift['assignedQty'],
                                        "unit_price" => 0,
                                        "campaign_discount" => 0,
                                        "subtotal" => 0,
                                        "record_identity" => "G",
                                        "point_discount" => 0,
                                        "points" => 0,
                                        "utm_source" => $utm_info[$item['itemId']]->utm_source,
                                        "utm_medium" => $utm_info[$item['itemId']]->utm_medium,
                                        "utm_campaign" => $utm_info[$item['itemId']]->utm_campaign,
                                        "utm_sales" => $utm_info[$item['itemId']]->utm_sales,
                                        "utm_time" => $utm_info[$item['itemId']]->utm_time,
                                        "created_by" => $member_id,
                                        "updated_by" => $member_id,
                                        "created_at" => now(),
                                        "updated_at" => now(),
                                        "returned_qty" => 0,
                                        "returned_campaign_discount" => 0,
                                        "returned_subtotal" => 0,
                                        "returned_point_discount" => 0,
                                        "returned_points" => 0,
                                    ];
                                    $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                                    //寫入折扣資訊
                                    $campaign_details[$seq] = [
                                        "order_id" => $newOrder->id,
                                        "level_code" => $campaign_gift['PROD'][$item['campaignGiftAway']['campaignGiftId']][$gift['productId']]['level_code'],
                                        "group_seq" => $campaign_group[$products['productID']][$item['campaignGiftAway']['campaignGiftId']],
                                        "order_detail_id" => $order_detail_id,
                                        "promotion_campaign_id" => $item['campaignGiftAway']['campaignGiftId'],
                                        "product_id" => $gift['productId'],
                                        "product_item_id" => $prod_info[$gift['productId']]['id'],
                                        "item_no" => $prod_info[$gift['productId']]['item_no'],
                                        "discount" => 0,
                                        "record_identity" => "G",
                                        "created_by" => $member_id,
                                        "updated_by" => $member_id,
                                        "created_at" => now(),
                                        "updated_at" => now(),
                                    ];
                                    OrderCampaignDiscount::insert($campaign_details[$seq]);
                                }
                                $productID = $products['productID'];
                            }
                        }
                    }

                    //有折扣則寫入折扣資訊
                    if ($item['campaignDiscountId'] && $item['campaignDiscountStatus']) {
                        $campaign_details[$seq] = [
                            "order_id" => $newOrder->id,
                            "level_code" => $campaigns[$products['productID']][0]->level_code,
                            "group_seq" => $campaign_group[$products['productID']][$item['campaignDiscountId']],
                            "order_detail_id" => $order_detail_id_M,
                            "promotion_campaign_id" => $item['campaignDiscountId'],
                            "product_id" => $products['productID'],
                            "product_item_id" => $item['itemId'],
                            "item_no" => $item['itemNo'],
                            "discount" => $discount,
                            "record_identity" => "M",
                            "created_by" => $member_id,
                            "updated_by" => $member_id,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ];
                        OrderCampaignDiscount::insert($campaign_details[$seq]);
                    }
                }
            }
            //購物車滿額新增單身
            $discount_group = $group_i;
            if ($cart['giftAway']) {
                $campaign_id_gift = 0;
                foreach ($cart['giftAway'] as $gift) {
                    $seq++;
                    $details[$seq] = [
                        "order_id" => $newOrder->id,
                        "seq" => $seq,
                        "product_id" => $gift['productId'],
                        "product_item_id" => $prod_gift[$gift['productId']]['id'],
                        "item_no" => $prod_gift[$gift['productId']]['item_no'],
                        "selling_price" => $gift['sellingPrice'],
                        "qty" => $gift['assignedQty'],
                        "unit_price" => 0,
                        "campaign_discount" => 0,
                        "subtotal" => 0,
                        "record_identity" => "G",
                        "point_discount" => 0,
                        "points" => 0,
                        "utm_source" => null,
                        "utm_medium" => null,
                        "utm_campaign" => null,
                        "utm_sales" => null,
                        "utm_time" => null,
                        "created_by" => $member_id,
                        "updated_by" => $member_id,
                        "created_at" => now(),
                        "updated_at" => now(),
                        "returned_qty" => 0,
                        "returned_campaign_discount" => 0,
                        "returned_subtotal" => 0,
                        "returned_point_discount" => 0,
                        "returned_points" => 0,
                    ];
                    $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                    if ($campaign_id_gift != $gift['campaignId']) {
                        $discount_group++;
                    }
                    //寫入折扣資訊
                    $campaign_details[$seq] = [
                        "order_id" => $newOrder->id,
                        "level_code" => $campaign_gift['PROD'][$gift['campaignId']][$gift['productId']]['level_code'],
                        "group_seq" => $discount_group,
                        "order_detail_id" => $order_detail_id,
                        "promotion_campaign_id" => $gift['campaignId'],
                        "product_id" => $gift['productId'],
                        "product_item_id" => $prod_gift[$gift['productId']]['id'],
                        "item_no" => $prod_gift[$gift['productId']]['item_no'],
                        "discount" => 0,
                        "record_identity" => "G",
                        "created_by" => $member_id,
                        "updated_by" => $member_id,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ];
                    OrderCampaignDiscount::insert($campaign_details[$seq]);
                    $campaign_id_gift = $gift['campaignId'];
                }
            }

            $cartTotal = $cart['totalPrice'];
            $cartDiscount = 0;
            $campaignID = '';
            $compare_n_value = 0;
            foreach ($campaign_discount as $items => $item) {
                if ($compare_n_value > $item->n_value) {
                    continue;
                }

                if ($cart['totalPrice'] >= $item->n_value) {
                    //﹝滿額﹞購物車滿N元，打X折
                    if ($item->campaign_type == 'CART01') {
                        $cartDiscount = $cartTotal - round(($cartTotal * $item->x_value)); //打折10000-(10000*0.85)
                        $campaignID = $item->id;
                    } //﹝滿額﹞購物車滿N元，折X元
                    elseif ($item->campaign_type == 'CART02') {
                        $cartDiscount = $item->x_value; //打折後10000-1000
                        $campaignID = $item->id;
                    }
                    $compare_n_value = $item->n_value;
                }
            }

            //購物車滿額活動
            if ($cartDiscount != 0) {
                $discount_group++;
                $seq++;
                //寫入折扣資訊
                $campaign_details[$seq] = [
                    "order_id" => $newOrder->id,
                    "level_code" => 'CART',
                    "group_seq" => $discount_group,
                    "order_detail_id" => null,
                    "promotion_campaign_id" => $campaignID,
                    "product_id" => null,
                    "product_item_id" => null,
                    "item_no" => null,
                    "discount" => ($cartDiscount * -1),
                    "record_identity" => null,
                    "created_by" => $member_id,
                    "updated_by" => $member_id,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                OrderCampaignDiscount::insert($campaign_details[$seq]);
            }

            //滿額折扣 C003
            if (isset($cart['thresholdDiscount'])) {
                $seq1 = 0;
                foreach ($cart['thresholdDiscount'] as $key => $threshold) {
                    foreach ($threshold['products'] as $k => $product_id) {
                        $seq1++;
                        //寫入折扣資訊
                        $campaign_details[$seq1] = [
                            "order_id" => $newOrder->id,
                            "level_code" => 'CART_P',
                            "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                            "order_detail_id" => isset($order_detail_temp[$product_id]) ? $order_detail_temp[$product_id] : null,
                            "promotion_campaign_id" => $threshold['campaignID'],
                            "product_id" => $product_id,
                            "product_item_id" => $prod_info_detail[$product_id]['id'],
                            "item_no" => $prod_info_detail[$product_id]['item_no'],
                            "discount" => $cart_p_discount_prod[$product_id],
                            "record_identity" => 'M',
                            "campaign_threshold_id" => $threshold_prod['thresholdDiscount'][$product_id],
                            "created_by" => $member_id,
                            "updated_by" => $member_id,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ];
                        OrderCampaignDiscount::insert($campaign_details[$seq1]);
                        $threshold_discount['discount'][$threshold['thresholdID']] -= $cart_p_discount_prod[$product_id];
                    }
                    $discountData = [];
                    //把最後一筆資料的比例做修正
                    if ($threshold_discount['discount'][$threshold['thresholdID']] != 0) {
                        $detail = OrderDetail::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')->where('id', $order_detail_temp[$product_id])->orderBy('seq', 'DESC')->first();
                        $discountData['discount'] = $detail->discount + $threshold_discount['discount'][$threshold['thresholdID']];
                        OrderCampaignDiscount::where('id', $detail->id)->update($discountData);
                    }
                }
            }
            //滿額贈禮 C003
            if (isset($cart['thresholdGiftAway'])) {
                foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
                    $campaignID = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        //$seq++;
                        //寫入折扣資訊
                        $campaign_details[$seq] = [
                            "order_id" => $newOrder->id,
                            "level_code" => 'CART_P',
                            "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                            "order_detail_id" => isset($order_detail_temp[$product_id]) ? $order_detail_temp[$product_id] : null,
                            "promotion_campaign_id" => $threshold['campaignID'],
                            "product_id" => $product_id,
                            "product_item_id" => $prod_info_detail[$product_id]['id'],
                            "item_no" => $prod_info_detail[$product_id]['item_no'],
                            "discount" => 0,
                            "record_identity" => 'M',
                            "campaign_threshold_id" => $threshold_prod['thresholdGiftaway'][$product_id],
                            "created_by" => $member_id,
                            "updated_by" => $member_id,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ];
                        OrderCampaignDiscount::insert($campaign_details[$seq]);

                        //同滿額活動不累贈
                        if ($campaignID != $threshold['campaignID']) {
                            foreach ($threshold['campaignProdList'] as $gift) {
                                $seq++;
                                $details[$seq] = [
                                    "order_id" => $newOrder->id,
                                    "seq" => $seq,
                                    "product_id" => $gift['productId'],
                                    "product_item_id" => $prod_gift[$gift['productId']]['id'],
                                    "item_no" => $prod_gift[$gift['productId']]['item_no'],
                                    "selling_price" => $gift['sellingPrice'],
                                    "qty" => $gift['assignedQty'],
                                    "unit_price" => 0,
                                    "campaign_discount" => 0,
                                    "subtotal" => 0,
                                    "record_identity" => "G",
                                    "point_discount" => 0,
                                    "points" => 0,
                                    "utm_source" => isset($order['utm']['source']) ? $order['utm']['source'] : null,
                                    "utm_medium" => isset($order['utm']['medium']) ? $order['utm']['medium'] : null,
                                    "utm_campaign" => isset($order['utm']['campaign']) ? $order['utm']['campaign'] : null,
                                    "utm_sales" => isset($order['utm']['sales']) ? $order['utm']['sales'] : null,
                                    "utm_time" => isset($order['utm']['time']) ? Carbon::createFromTimestamp($order['utm']['time'])->format('Y-m-d H:i:s') : null,
                                    "created_by" => $member_id,
                                    "updated_by" => $member_id,
                                    "created_at" => now(),
                                    "updated_at" => now(),
                                    "returned_qty" => 0,
                                    "returned_campaign_discount" => 0,
                                    "returned_subtotal" => 0,
                                    "returned_point_discount" => 0,
                                    "returned_points" => 0,
                                ];
                                $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                                //寫入折扣資訊
                                $campaign_details[$seq] = [
                                    "order_id" => $newOrder->id,
                                    "level_code" => 'CART_P',
                                    "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                                    "order_detail_id" => $order_detail_id,
                                    "promotion_campaign_id" => $threshold['campaignID'],
                                    "product_id" => $gift['productId'],
                                    "product_item_id" => $prod_gift[$gift['productId']]['id'],
                                    "item_no" => $prod_gift[$gift['productId']]['item_no'],
                                    "discount" => 0,
                                    "record_identity" => "G",
                                    "campaign_threshold_id" => $threshold_prod['thresholdGiftaway'][$product_id],
                                    "created_by" => $member_id,
                                    "updated_by" => $member_id,
                                    "created_at" => now(),
                                    "updated_at" => now(),
                                ];
                                OrderCampaignDiscount::insert($campaign_details[$seq]);
                            }
                        }
                        $campaignID = $threshold['campaignID'];
                    }
                }
            }

            $pointData = [];
            //點數比例加總不等於1時，把最後一筆資料的比例做修正
            if ($point_rate != 1) {
                $detail = OrderDetail::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')->orderBy('seq', 'DESC')->first();
                $pointData['point_discount'] = $detail->point_discount - 1 + $point_rate;
                $pointData['points'] = ($pointData['point_discount'] / $cart['point']['exchangeRate']);
                OrderDetail::where('id', $detail->id)->update($pointData);
            }

            //庫存與LOG相關
            $order_details = OrderDetail::getOrderDetails($newOrder->id);
            foreach ($order_details as $detail) {
                $stock = $this->stockService->getStockByItem($warehouseCode, $detail->product_item_id);
                if (isset($stock['id'])) {
                    $updStock = WarehouseStock::where('id', '=', $stock['id'])->update(['stock_qty' => ($stock['stockQty'] - $detail->qty)]);
                    if ($updStock) {
                        $logData['transaction_type'] = 'ORDER_SHIP';
                        $logData['transaction_date'] = now();
                        $logData['warehouse_id'] = $stock['warehouse_id'];
                        $logData['product_item_id'] = $detail->product_item_id;
                        $logData['item_no'] = $detail->item_no;
                        $logData['transaction_qty'] = -$detail->qty;
                        $logData['transaction_nontax_amount'] = $detail->unit_price;
                        $logData['transaction_amount'] = $detail->unit_price;
                        $logData['source_doc_no'] = $webData['order_no'];
                        $logData['source_table_name'] = 'order_details';
                        $logData['source_table_id'] = $detail->id;
                        $logData['remark'] = '';
                        $logData['created_by'] = -1;
                        $logData['created_at'] = now();
                        $logData['updated_by'] = -1;
                        $logData['updated_at'] = now();
                        StockTransactionLog::insert($logData);
                    }
                }
            }

            //更新會員點數
            if ($order['points'] != 0) {
                $pointData['point'] = $webData['points'];
                $pointData['orderId'] = $webData['order_no'];
                $pointData['type'] = 'USED';
                $pointData['callFrom'] = 'EC';
                $used_member = $webData['member_id'];
                $pointStatus = $this->apiService->changeMemberPoint($pointData, $used_member);
                $pointStatus = json_decode($pointStatus, true);
                $thisStatus = ($pointStatus['status'] == '200' ? 'S' : 'E');
                $order_payment = OrderPayment::where('id', '=', $newOrderPayment->id)->update(['point_api_status' => $thisStatus, 'point_api_date' => $pointStatus['timestamp'], 'point_api_log' => json_encode($pointStatus)]);

                if ($order_payment && $pointStatus['status'] == '200') {
                    $isTapPay = 1;
                } else {
                    $result['status'] = 401;
                    $result['payment_url'] = null;
                    Log::channel('changepoint')->error('扣點異常 ! webdata :' . json_encode($webData) . 'req:' . json_encode($pointData) . 'rep:' . json_encode($pointStatus));
                    DB::rollBack();
                    return $result;
                    $isTapPay = 0;
                }
            } else {
                $isTapPay = 1;
            }

            //TapPay
            if ($isTapPay) {
                $webData['prime'] = $order['tappay_prime'];
                $tapPay = $this->apiTapPayService->payByPrime($webData);
                $tapPayResult = json_decode($tapPay, true);
                if ($tapPayResult['status'] == 0) {
                    $payment = OrderPayment::where('id', $newOrderPayment->id)->update(['rec_trade_id' => $tapPayResult['rec_trade_id'], 'latest_api_date' => now()]);
                    if ($payment) {
                        $result['status'] = 200;
                        $result['payment_url'] = $tapPayResult['payment_url'];
                        $result['payment_token'] = $token;
                    } else {
                        $result['status'] = 402;
                        $result['payment_url'] = null;
                        $result['payment_token'] = null;
                        Log::channel('tappay_api_log')->error('597:tappay error!' . json_encode($tapPayResult));
                    }
                } else {
                    $result['status'] = $tapPayResult['status'];
                    $result['payment_url'] = null;
                    $result['payment_token'] = null;
                    Log::channel('tappay_api_log')->error('602:tappay error!' . json_encode($tapPayResult));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tappay_api_log')->error('結帳成立訂單錯誤 ! ' . $e);
            $result['status'] = 401;
            $result['payment_url'] = null;
        }

        return $result;
    }

}
