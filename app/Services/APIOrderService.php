<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\OrderPaymentSecrets;
use App\Models\ProductItem;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\ShoppingCartDetail;
use App\Models\StockTransactionLog;
use App\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class APIOrderService
{
    private $sysConfigService;

    public function __construct(
        APITapPayService $apiTapPayService,
        StockService $stockService,
        APIService $apiService,
        SysConfigService $sysConfigService
    )
    {
        $this->apiTapPayService = $apiTapPayService;
        $this->stockService = $stockService;
        $this->apiService = $apiService;
        $this->sysConfigService = $sysConfigService;
    }

    /**
     * 訂單
     * @param 購物車清單, 前端的訂單資料
     * @return string
     */
    public function setOrders($cart, $order, $campaigns, $campaign_gift, $campaign_discount)
    {
        if (config('uec.cart_p_discount_split') == 1) return $this->setOrdersV2($cart, $order, $campaigns, $campaign_gift, $campaign_discount);
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
        $prod_info = [];
        $prod_items = [];
        $prod_gift = [];
        foreach ($product_items as $product_item) {
            $prod_info[$product_item->product_id] = $product_item;
            $prod_items[$product_item->product_id][$product_item->id] = $product_item;
            $stock = $this->stockService->getStockByItem($warehouseCode, $product_item->id);
            if (isset($stock)) {
                if ($stock['stockQty'] > 0) {
                    $prod_gift[$product_item->product_id] = $product_item;
                }
            }
        }
        //行銷活動
        $campaign_group = [];
        $campaign_group_code = [];
        $group_i = 0;
        foreach ($cart['list'] as $products) {
            foreach ($campaigns as $product_id => $item) {
                if ($products['productID'] == $product_id) {
                    foreach ($item as $k => $v) {
                        $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                        if ($v->level_code != 'CART_P') { //單品活動才做
                            $group_i++;
                            $campaign_group[$product_id][$v->id] = $group_i;    //群組ID (C002)
                            $campaign_group_code[$product_id][$v->id] = $v->level_code;
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
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;  //群組ID (C003)
            }
            $threshold_discount['discount'][$threshold['thresholdID']] = $threshold['campaignDiscount']; //門檻折扣
            $threshold_discount['price'][$threshold['thresholdID']] = $total; //符合門檻總金額
        }
        foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
            $group_i++;
            foreach ($threshold['products'] as $k => $product_id) {
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;  //群組ID (C003)
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
            $point_discount = 0;
            $detail_p_discount = 0;
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
                            $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($item['amount'] - round($item['amount'] * $threshold_prod['value'][$products['productID']])) * -1;
                        } else {
                            //500*(1800/1800)
                            $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($threshold_prod['value'][$products['productID']] * ($item['amount'] / $threshold_discount['price'][$threshold_prod['thresholdDiscount'][$products['productID']]])) * -1;
                        }
                    } else {
                        $cart_p_discount_prod[$products['productID']][$item['itemId']] = 0;
                    }

                    //有用點數折現金
                    if ($order['point_discount'] < 0) {
                        $discount_rate[$seq] = (($item['amount'] + $cart_p_discount_prod[$products['productID']][$item['itemId']]) / ($order['total_price'] + $cart_p_discount));
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
                        "cart_p_discount" => $cart_p_discount_prod[$products['productID']][$item['itemId']],
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
                    $point_discount += round($discount_rate[$seq] * $order['points']);
                    $detail_p_discount += $cart_p_discount_prod[$products['productID']][$item['itemId']];
                    $order_detail_id_M = OrderDetail::insertGetId($details[$seq]);
                    $order_detail_temp[$products['productID']][$item['itemId']] = $order_detail_id_M;
                    $campaign_id = 0;
                    //有單品滿額贈時，正貨也寫入discount
                    if (isset($campaign['PRD']['GIFT'][$products['productID']])) {
                        if (count($item['campaignGiftAway']) > 0) {
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
                    }

                    //訂單明細建立後，更新購物車中的商品狀態為 - 已轉為訂單
                    $updData['status_code'] = 1;
                    ShoppingCartDetail::where('member_id', '=', $member_id)->where('product_item_id', '=', $item['itemId'])->update($updData);

                    //有單品滿額贈品時先新增單身
                    if (isset($item['campaignGiftAway']['campaignProdList'])) {
                        //符合條件
                        if (count($item['campaignGiftAway']) > 0) {
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
                                    }
                                    $productID = $products['productID'];
                                }
                            }
                        }
                    }

                    //有折扣則寫入折扣資訊
                    if ($item['campaignDiscountId'] && $item['campaignDiscountStatus']) {
                        $campaign_details[$seq] = [
                            "order_id" => $newOrder->id,
                            "level_code" => $campaign_group_code[$products['productID']][$item['campaignDiscountId']],
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
                    $threshold_discount['discount'][$threshold['thresholdID']] = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        foreach ($cart['list'] as $products) {
                            if ($products['productID'] == $product_id) {
                                foreach ($products['itemList'] as $item) {
                                    $seq1++;
                                    //寫入折扣資訊
                                    $campaign_details[$seq1] = [
                                        "order_id" => $newOrder->id,
                                        "level_code" => 'CART_P',
                                        "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                                        "order_detail_id" => isset($order_detail_temp[$product_id][$item['itemId']]) ? $order_detail_temp[$product_id][$item['itemId']] : null,
                                        "promotion_campaign_id" => $threshold['campaignID'],
                                        "product_id" => $product_id,
                                        "product_item_id" => $item['itemId'],
                                        "item_no" => $item['itemNo'],
                                        "discount" => $cart_p_discount_prod[$product_id][$item['itemId']],
                                        "record_identity" => 'M',
                                        "campaign_threshold_id" => $threshold_prod['thresholdDiscount'][$product_id],
                                        "created_by" => $member_id,
                                        "updated_by" => $member_id,
                                        "created_at" => now(),
                                        "updated_at" => now(),
                                    ];
                                    OrderCampaignDiscount::insert($campaign_details[$seq1]);
                                    $threshold_discount['discount'][$threshold['thresholdID']] += $cart_p_discount_prod[$product_id][$item['itemId']];
                                }
                            }
                        }
                    }
                    $discountData = [];
                    //把最後一筆資料campaignDiscount的比例做修正
                    //$threshold_discount['discount'][$threshold['thresholdID']]
                    if (($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]) != 0) {
                        $tmp_discount = ($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]);
                        $detail = OrderCampaignDiscount::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')
                            ->where('level_code', 'CART_P')
                            ->where('campaign_threshold_id', $threshold['thresholdID'])
                            ->orderBy('id', 'DESC')->first();
                        $discountData['discount'] = $detail->discount + $tmp_discount;
                        OrderCampaignDiscount::where('id', $detail->id)->update($discountData);
                        //同時把同門檻訂單明細的比例做修正
                        OrderDetail::where('id', $detail->order_detail_id)->update(['cart_p_discount' => $discountData['discount']]);
                    }
                }
            }
            //滿額贈禮 C003
            if (isset($cart['thresholdGiftAway'])) {
                foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
                    $campaignID = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        foreach ($cart['list'] as $products) {
                            if ($products['productID'] == $product_id) {
                                foreach ($products['itemList'] as $item) {
                                    //$seq++;
                                    //寫入折扣資訊
                                    $campaign_details[$seq] = [
                                        "order_id" => $newOrder->id,
                                        "level_code" => 'CART_P',
                                        "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                                        "order_detail_id" => isset($order_detail_temp[$product_id][$item['itemId']]) ? $order_detail_temp[$product_id][$item['itemId']] : null,
                                        "promotion_campaign_id" => $threshold['campaignID'],
                                        "product_id" => $product_id,
                                        "product_item_id" => $item['itemId'],
                                        "item_no" => $item['itemNo'],
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
                    }
                }
            }
            //點數比例加總不等於1時，把最後一筆資料的比例做修正
            $pointData = [];
            if ($point_rate != 1 || ($order['points'] - $point_discount) != 0) {
                $detail = OrderDetail::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')->orderBy('seq', 'DESC')->first();
                if ($order['points'] > $point_discount) {
                    $pointData['point_discount'] = $detail->point_discount + ($order['points'] - $point_discount);
                } else {
                    $pointData['point_discount'] = $detail->point_discount - ($point_discount - $order['points']);
                }
                $pointData['points'] = ($pointData['point_discount'] / $cart['point']['exchangeRate']);
                OrderDetail::where('id', $detail->id)->update($pointData);
            }

            //庫存與LOG相關
            $order_details = OrderDetail::getOrderDetails($newOrder->id);
            foreach ($order_details as $detail) {
                $stock = $this->stockService->getStockByItem($warehouseCode, $detail->product_item_id);
                if ($stock['stockQty'] <= 0) {
                    $result['status'] = 403;
                    $result['payment_url'] = null;
                    Log::channel('tappay_api_log')->error('庫存不足 ! product_item_id :' . $detail->product_item_id);
                    DB::rollBack();
                    return $result;
                }
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
                    $result['status'] = 404;
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
                    } else {
                        $result['status'] = 402;
                        $result['payment_url'] = null;
                        Log::channel('tappay_api_log')->error('597:tappay error!' . json_encode($tapPayResult));
                        DB::rollBack();
                        return $result;
                    }
                } else {
                    $result['status'] = 402;
                    $result['payment_url'] = null;
                    $result['tappay_msg'] = $tapPayResult['status'] . ":" . $tapPayResult['msg'];
                    Log::channel('tappay_api_log')->error($tapPayResult['status'] . ':tappay error!' . json_encode($tapPayResult));
                    DB::rollBack();
                    return $result;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tappay_api_log')->error('訂單與結帳發生錯誤 ! ' . $e);
            $result['status'] = 401;
            $result['payment_url'] = null;
        }

        return $result;
    }

    /**
     * 訂單
     * @param 購物車清單, 前端的訂單資料
     * @return string
     */
    public function setOrdersV2($cart, $order, $campaigns, $campaign_gift, $campaign_discount)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();

        // 轉單時間 X分鐘
        $sup_trans_mins = (int)$this->sysConfigService->getConfigValue('SUP_ORDER_TRANS_MINS');

        // 最晚應出貨時間 X個工作天
        $ship_deadline = (int)$this->sysConfigService->getConfigValue('SUP_ORDER_SHIP_DEADLINE');

        $utms = ShoppingCartDetail::where('member_id', '=', $member_id)->where('status_code', '=', 0)->get();
        $utm_info = [];
        foreach ($utms as $utm) {
            $utm_info[$utm->product_item_id] = $utm;
        }

        $product_items = ProductItem::all();
        $prod_info = [];
        $prod_items = [];
        $prod_gift = [];
        foreach ($product_items as $product_item) {
            $prod_info[$product_item->product_id] = $product_item;
            $prod_items[$product_item->product_id][$product_item->id] = $product_item;
            $stock = $this->stockService->getStockByItem($warehouseCode, $product_item->id);
            if (isset($stock)) {
                if ($stock['stockQty'] > 0) {
                    $prod_gift[$product_item->product_id] = $product_item;
                }
            }
        }

        //取產品中for供應商使用相關資訊
        $product_with = [];
        $supplier_info = ProductItem::with('product:id,supplier_id,purchase_price')->get();
        foreach ($supplier_info as $supplier) {
            $product_with['supplier_id'][$supplier->product->id] = $supplier->product->supplier_id;
            $product_with['purchase_price'][$supplier->product->id] = ($supplier->product->purchase_price ?? null);
        }
        //行銷活動
        $campaign_group = [];
        $campaign_group_code = [];
        $group_i = 0;
        $tmp_product_id = "";
        foreach ($cart['list'] as $products) {
            foreach ($campaigns as $product_id => $item) {
                if ($products['productID'] == $product_id) {
                    foreach ($item as $k => $v) {
                        foreach ($products['itemList'] as $item_info) {
                            $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                            if ($v->level_code != 'CART_P') { //單品活動才做
                                if ($item_info['campaignDiscountStatus'] && $tmp_product_id != $products['productID']) {
                                    $group_i++;
                                    $campaign_group[$product_id][$v->id] = $group_i;    //群組ID (C002)
                                }
                                if (isset($item_info['campaignGiftAway']['campaignGiftStatus'])) {
                                    if ($item_info['campaignGiftAway']['campaignGiftStatus'] && $tmp_product_id != $products['productID']) {
                                        $group_i++;
                                        $campaign_group[$product_id][$v->id] = $group_i;    //群組ID (C002)
                                    }
                                }
                                $campaign_group_code[$product_id][$v->id] = $v->level_code;
                                $tmp_product_id = $products['productID'];
                            }
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
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;  //群組ID (C003)
            }
            $threshold_discount['discount'][$threshold['thresholdID']] = $threshold['campaignDiscount']; //門檻折扣
            $threshold_discount['price'][$threshold['thresholdID']] = $total; //符合門檻總金額
        }
        foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
            $group_i++;
            foreach ($threshold['products'] as $k => $product_id) {
                $campaign_group[$product_id][$threshold['campaignID']] = $group_i;  //群組ID (C003)
                $threshold_prod['thresholdGiftaway'][$product_id] = $threshold['thresholdID']; //門檻ID
            }
        }

        DB::beginTransaction();
        try {
            //訂單單頭
            $cart_campaign_discount = ($order['cart_campaign_discount'] < 0 ? ($order['cart_campaign_discount'] - ($cart['thresholdAmount'])) : 0);
            $cart_p_discount = $cart['thresholdAmount'];
            $interest_fee = isset($cart['installments']['interest_fee']) ? $cart['installments']['interest_fee'] : 0;
            $webData = [];
            $webData['agent_id'] = 1;
            $webData['order_no'] = ColumnNumberGenerator::make(new Order(), 'order_no')->generate('OD', 6, true, date("ymd"), 'number');
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
            $webData['total_amount'] = ($order['total_price'] + $cart_p_discount);
            $webData['cart_campaign_discount'] = $cart_campaign_discount; //原C002滿額折抵
            $webData['cart_p_discount'] = 0;//新C003滿額折抵
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
            $webData['ship_from_whs'] = ($order['stock_type'] == 'supplier' ? 'SUP' : 'SELF');
            $webData['sup_transferred_at'] = ($order['stock_type'] == 'supplier' ? Carbon::parse(Carbon::now())->addMinutes($sup_trans_mins) : null);
            $webData['ship_deadline'] = ($order['stock_type'] == 'supplier' ? Carbon::parse(Carbon::now())->addWeekday($ship_deadline)->format('Y-m-d 23:59:59') : null);
            $webData['number_of_instal'] = isset($order['installment_info']['number_of_installments']) ? $order['installment_info']['number_of_installments'] : 0;
            $webData['interest_rate_of_instal'] = isset($cart['installments']['interest_rate']) ? $cart['installments']['interest_rate'] : 0;
            $webData['min_consumption_of_instal'] = isset($cart['installments']['min_consumption']) ? $cart['installments']['min_consumption'] : 0;
            $webData['fee_of_instal'] = $interest_fee;
            $webData['buyer_remark'] = $order['buyer_remark'];
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
            $paymantData['amount'] = ($webData['paid_amount'] + $webData['fee_of_instal']);
            $paymantData['point_discount'] = $webData['point_discount'];
            $paymantData['points'] = $webData['points'];
            $paymantData['record_created_reason'] = 'ORDER_CREATED';
            $paymantData['created_by'] = $member_id;
            $paymantData['updated_by'] = $member_id;
            $paymantData['number_of_instal'] = $webData['number_of_instal'];
            $paymantData['interest_rate_of_instal'] = $webData['interest_rate_of_instal'];
            $paymantData['min_consumption_of_instal'] = $webData['min_consumption_of_instal'];
            $newOrderPayment = OrderPayment::create($paymantData);
            //訂單單身
            $seq = 0;
            $details = [];
            $point_rate = 0;
            $discount_group = 0;
            $productID = 0;
            $prod_info_detail = [];
            $point_discount = 0;
            $detail_p_discount = 0;
            foreach ($cart['list'] as $products) {
                foreach ($products['itemList'] as $item) {
                    //售價*數量-單品折抵(itemDiscount) = 單品折抵後的小計($tmp_subtotal)
                    $tmp_subtotal = ($products['sellingPrice'] * $item['itemQty'] + $item['itemDiscount']);

                    if ($prod_items[$products['productID']][$item['itemId']]) {
                        $prod_info_detail[$products['productID']] = $prod_items[$products['productID']][$item['itemId']];
                    }
                    $seq++;
                    //有活動折扣
                    if ($item['campaignDiscountStatus']) {
                        $discount = $item['itemDiscount'];
                    } else {
                        $discount = 0;
                    }

                    //滿額門檻計算$threshold_prod['price']
                    if (isset($threshold_prod['value'][$products['productID']])) {
                        if ($threshold_prod['value'][$products['productID']] < 1) { //折數
                            //1800-(1800*0.9)
                            $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($tmp_subtotal - round($tmp_subtotal * $threshold_prod['value'][$products['productID']])) * -1;
                        } else {
                            //500*(1800/1800)
                            $cart_p_discount_prod[$products['productID']][$item['itemId']] = round($threshold_prod['value'][$products['productID']] * ($tmp_subtotal / $threshold_discount['price'][$threshold_prod['thresholdDiscount'][$products['productID']]])) * -1;
                        }
                    } else {
                        $cart_p_discount_prod[$products['productID']][$item['itemId']] = 0;
                    }

                    //有用點數折現金
                    if ($order['point_discount'] < 0) {
                        //重算
                        $sub_point = $products['sellingPrice'] * $item['itemQty'] + $discount + $cart_p_discount_prod[$products['productID']][$item['itemId']];
                        $discount_rate[$seq] = ($sub_point / ($order['total_price'] + $cart_p_discount));
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
                        "cart_p_discount" => $cart_p_discount_prod[$products['productID']][$item['itemId']],
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
                        "main_product_id" => $products['productID'],
                        "purchase_price" => $product_with['purchase_price'][$products['productID']] ?? 0,
                    ];
                    $point_discount += round($discount_rate[$seq] * $order['points']);
                    $detail_p_discount += $cart_p_discount_prod[$products['productID']][$item['itemId']];
                    $order_detail_id_M = OrderDetail::insertGetId($details[$seq]);
                    $order_detail_temp[$products['productID']][$item['itemId']] = $order_detail_id_M;
                    $campaign_id = 0;
                    //有單品滿額贈時，正貨也寫入discount
                    if (isset($campaign['PRD']['GIFT'][$products['productID']])) {
                        if (count($item['campaignGiftAway']) > 0) {
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
                    }

                    //訂單明細建立後，更新購物車中的商品狀態為 - 已轉為訂單
                    $updData['status_code'] = 1;
                    ShoppingCartDetail::where('member_id', '=', $member_id)->where('product_item_id', '=', $item['itemId'])->update($updData);

                    //有單品滿額贈品時先新增單身
                    if (isset($item['campaignGiftAway']['campaignProdList'])) {
                        //符合條件
                        if (count($item['campaignGiftAway']) > 0) {
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
                                            "main_product_id" => $products['productID'],
                                            "purchase_price" => $product_with['purchase_price'][$gift['productId']] ?? 0,
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
                                    }
                                    $productID = $products['productID'];
                                }
                            }
                        }
                    }

                    //有折扣則寫入折扣資訊
                    if ($item['campaignDiscountId'] && $item['campaignDiscountStatus']) {
                        $campaign_details[$seq] = [
                            "order_id" => $newOrder->id,
                            "level_code" => $campaign_group_code[$products['productID']][$item['campaignDiscountId']],
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
                        "main_product_id" => 0,
                        "purchase_price" => 0,
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
                    $threshold_discount['discount'][$threshold['thresholdID']] = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        foreach ($cart['list'] as $products) {
                            if ($products['productID'] == $product_id) {
                                foreach ($products['itemList'] as $item) {
                                    $seq1++;
                                    //寫入折扣資訊
                                    $campaign_details[$seq1] = [
                                        "order_id" => $newOrder->id,
                                        "level_code" => 'CART_P',
                                        "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                                        "order_detail_id" => isset($order_detail_temp[$product_id][$item['itemId']]) ? $order_detail_temp[$product_id][$item['itemId']] : null,
                                        "promotion_campaign_id" => $threshold['campaignID'],
                                        "product_id" => $product_id,
                                        "product_item_id" => $item['itemId'],
                                        "item_no" => $item['itemNo'],
                                        "discount" => $cart_p_discount_prod[$product_id][$item['itemId']],
                                        "record_identity" => 'M',
                                        "campaign_threshold_id" => $threshold_prod['thresholdDiscount'][$product_id],
                                        "created_by" => $member_id,
                                        "updated_by" => $member_id,
                                        "created_at" => now(),
                                        "updated_at" => now(),
                                    ];
                                    OrderCampaignDiscount::insert($campaign_details[$seq1]);
                                    $threshold_discount['discount'][$threshold['thresholdID']] += $cart_p_discount_prod[$product_id][$item['itemId']];
                                }
                            }
                        }
                    }
                    $discountData = [];
                    //把最後一筆資料campaignDiscount的比例做修正
                    //$threshold_discount['discount'][$threshold['thresholdID']]
                    if (($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]) != 0) {
                        $tmp_discount = ($threshold['campaignDiscount'] - $threshold_discount['discount'][$threshold['thresholdID']]);
                        $detail = OrderCampaignDiscount::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')
                            ->where('level_code', 'CART_P')
                            ->where('campaign_threshold_id', $threshold['thresholdID'])
                            ->orderBy('id', 'DESC')->first();
                        $discountData['discount'] = $detail->discount + $tmp_discount;
                        OrderCampaignDiscount::where('id', $detail->id)->update($discountData);
                        //同時把同門檻訂單明細的比例做修正
                        OrderDetail::where('id', $detail->order_detail_id)->update(['cart_p_discount' => $discountData['discount']]);
                    }
                }
            }
            //滿額贈禮 C003
            if (isset($cart['thresholdGiftAway'])) {
                foreach ($cart['thresholdGiftAway'] as $key => $threshold) {
                    $campaignID = 0;
                    foreach ($threshold['products'] as $k => $product_id) {
                        foreach ($cart['list'] as $products) {
                            if ($products['productID'] == $product_id) {
                                foreach ($products['itemList'] as $item) {
                                    //$seq++;
                                    //寫入折扣資訊
                                    $campaign_details[$seq] = [
                                        "order_id" => $newOrder->id,
                                        "level_code" => 'CART_P',
                                        "group_seq" => $campaign_group[$product_id][$threshold['campaignID']],
                                        "order_detail_id" => isset($order_detail_temp[$product_id][$item['itemId']]) ? $order_detail_temp[$product_id][$item['itemId']] : null,
                                        "promotion_campaign_id" => $threshold['campaignID'],
                                        "product_id" => $product_id,
                                        "product_item_id" => $item['itemId'],
                                        "item_no" => $item['itemNo'],
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
                                                "main_product_id" => $products['productID'],
                                                "purchase_price" => $product_with['purchase_price'][$gift['productId']] ?? 0,
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
                    }
                }
            }
            //點數比例加總不等於1時，把最後一筆資料的比例做修正
            $pointData = [];
            if ($point_rate != 1 || ($order['points'] - $point_discount) != 0) {
                $detail = OrderDetail::where('order_id', '=', $newOrder->id)->where('record_identity', '=', 'M')->orderBy('seq', 'DESC')->first();
                if ($order['points'] > $point_discount) {
                    $pointData['point_discount'] = $detail->point_discount + ($order['points'] - $point_discount);
                } else {
                    $pointData['point_discount'] = $detail->point_discount - ($point_discount - $order['points']);
                }
                $pointData['points'] = ($pointData['point_discount'] / $cart['point']['exchangeRate']);
                OrderDetail::where('id', $detail->id)->update($pointData);
            }

            //庫存與LOG相關
            $order_details = OrderDetail::getOrderDetails($newOrder->id);
            foreach ($order_details as $detail) {
                $stock = $this->stockService->getStockByItem($warehouseCode, $detail->product_item_id);
                if ($stock['stockQty'] <= 0) {
                    $result['status'] = 403;
                    $result['payment_url'] = null;
                    Log::channel('tappay_api_log')->error('庫存不足 ! product_item_id :' . $detail->product_item_id);
                    DB::rollBack();
                    return $result;
                }
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
                    $result['status'] = 404;
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
                        if ($webData['payment_method'] == 'TAPPAY_INSTAL') {
                            $tapPayData['order_no'] = $webData['order_no'];
                            $tapPayData['card_key'] = Crypt::encrypt($tapPayResult['card_secret']['card_key']);
                            $tapPayData['card_token'] = Crypt::encrypt($tapPayResult['card_secret']['card_token']);
                            $tapPayData['created_by'] = $member_id;
                            $tapPayData['updated_by'] = $member_id;
                            $tapPayData['created_at'] = now();
                            $tapPayData['updated_at'] = now();
                            $secret = OrderPaymentSecrets::insertGetId($tapPayData);

                            if ($secret > 0) {
                                $result['status'] = 200;
                                $result['payment_url'] = $tapPayResult['payment_url'];
                            } else {
                                $result['status'] = 402;
                                $result['payment_url'] = null;
                                Log::channel('tappay_api_log')->error('加密資料失敗' . json_encode($tapPayResult));
                                DB::rollBack();
                                return $result;
                            }
                        } else {
                            $result['status'] = 200;
                            $result['payment_url'] = $tapPayResult['payment_url'];
                        }
                    } else {
                        $result['status'] = 402;
                        $result['payment_url'] = null;
                        Log::channel('tappay_api_log')->error('597:tappay error!' . json_encode($tapPayResult));
                        DB::rollBack();
                        return $result;
                    }
                } else {
                    $result['status'] = 402;
                    $result['payment_url'] = null;
                    $result['tappay_msg'] = $tapPayResult['status'] . ":" . $tapPayResult['msg'];
                    Log::channel('tappay_api_log')->error($tapPayResult['status'] . ':tappay error!' . json_encode($tapPayResult));
                    DB::rollBack();
                    return $result;
                }
            }

            //建立出貨單
            $webData['order_id'] = $newOrder->id; //訂單ID
            $webData['payment_id'] = $newOrderPayment->id; //付款ID
            $ship_status = $this->setShipment($webData, $product_with['supplier_id']);
            if (!$ship_status) {
                $result['status'] = 405;
                $result['payment_url'] = null;
                DB::rollBack();
                return $result;
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

    /**
     * 建立出貨單
     * @param 訂單資訊
     * @return string
     */
    public function setShipment($data, $supplier_id)
    {
        $status = Order::getOrder($data['order_id']);
        if ($status['status_code'] != 'CREATED') exit;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            if ($status['ship_from_whs'] == 'SELF') { //出貨倉，SELF-自有倉儲(秋雨倉)
                //建立出貨單頭
                $shipData = [];
                $shipData['agent_id'] = 1;
                $shipData['shipment_no'] = ColumnNumberGenerator::make(new Shipment(), 'shipment_no')->generate('SH', 6, true, date("ymd"), 'number');
                $shipData['shipment_date'] = $now;
                $shipData['status_code'] = 'CREATED';
                $shipData['payment_method'] = $data['payment_method'];
                $shipData['is_cash_on_delivery'] = $data['is_cash_on_delivery'];
                $shipData['lgst_method'] = $data['lgst_method'];
                $shipData['order_id'] = $data['order_id'];
                $shipData['order_no'] = $data['order_no'];
                $shipData['total_amount'] = $data['total_amount'];
                $shipData['paid_amount'] = $data['paid_amount'];
                $shipData['ship_to_name'] = $data['receiver_name'];
                $shipData['ship_to_mobile'] = $data['receiver_mobile'];
                $shipData['ship_to_zip_code'] = $data['receiver_zip_code'];
                $shipData['ship_to_city'] = $data['receiver_city'];
                $shipData['ship_to_district'] = $data['receiver_district'];
                $shipData['ship_to_address'] = $data['receiver_address'];
                $shipData['store_no'] = $data['store_no'];
                $shipData['remark'] = '';
                $shipData['created_by'] = -1;
                $shipData['created_at'] = $now;
                $shipData['updated_by'] = -1;
                $shipData['updated_at'] = $now;
                $ship_id = Shipment::insertGetId($shipData);
                $shipDetail = [];

                //出貨單單身
                $order_details = OrderDetail::getOrderDetails($data['order_id']);
                foreach ($order_details as $detail) {
                    $shipDetail['shipment_id'] = $ship_id;
                    $shipDetail['seq'] = $detail->seq;
                    $shipDetail['order_detail_id'] = $detail->id;
                    $shipDetail['product_item_id'] = $detail->product_item_id;
                    $shipDetail['item_no'] = $detail->item_no;
                    $shipDetail['qty'] = $detail->qty;
                    $shipDetail['created_by'] = -1;
                    $shipDetail['created_at'] = $now;
                    $shipDetail['updated_by'] = -1;
                    $shipDetail['updated_at'] = $now;
                    $shipDetail['order_detail_seq'] = $detail->seq; //新增加欄位，同$shipDetail['seq']
                    $shipDetail['selling_price'] = $detail['selling_price'];
                    $shipDetail['purchase_price'] = $detail['purchase_price'];
                    $shipDetail['record_identity'] = $detail['record_identity'];
                    $shipDetail_id = ShipmentDetail::insertGetId($shipDetail);
                }
            } else {//出貨倉，SUP-供應商自出
                //出貨單單身

                $order_details = OrderDetail::getOrderDetails($data['order_id'])->toArray();
                array_multisort(array_column($order_details, 'main_product_id'), SORT_ASC, $order_details);
                $seq = 0;
                $main_product_id = 0;
                $shipment = [];
                foreach ($order_details as $detail) {
                    if ($main_product_id != $detail['main_product_id']) {
                        $seq = 0;
                        $shipment['total_amount'][$detail['main_product_id']] = 0;
                        $shipment['paid_amount'][$detail['main_product_id']] = 0;
                    }
                    $seq++;
                    $shipment['detail'][$detail['main_product_id']][$seq] = $detail;
                    $main_product_id = $detail['main_product_id'];
                    $shipment['total_amount'][$detail['main_product_id']] += $detail['subtotal'];
                    $shipment['paid_amount'][$detail['main_product_id']] += ($detail['subtotal'] + $detail['point_discount']);
                    $shipment['supplier'][$main_product_id] = $supplier_id[$main_product_id];
                }
                //一品一單
                foreach ($shipment['detail'] as $main_product_id => $order_detail) {

                    //建立出貨單頭
                    $shipData = [];
                    $shipData['agent_id'] = 1;
                    $shipData['shipment_no'] = ColumnNumberGenerator::make(new Shipment(), 'shipment_no')->generate('SH', 6, true, date("ymd"), 'number');
                    $shipData['shipment_date'] = $now;
                    $shipData['status_code'] = 'CREATED';
                    $shipData['payment_method'] = $data['payment_method'];
                    $shipData['is_cash_on_delivery'] = $data['is_cash_on_delivery'];
                    $shipData['lgst_method'] = $data['lgst_method'];
                    $shipData['order_id'] = $data['order_id'];
                    $shipData['order_no'] = $data['order_no'];
                    $shipData['total_amount'] = $shipment['total_amount'][$main_product_id];
                    $shipData['paid_amount'] = $shipment['paid_amount'][$main_product_id];
                    $shipData['ship_to_name'] = $data['receiver_name'];
                    $shipData['ship_to_mobile'] = $data['receiver_mobile'];
                    $shipData['ship_to_zip_code'] = $data['receiver_zip_code'];
                    $shipData['ship_to_city'] = $data['receiver_city'];
                    $shipData['ship_to_district'] = $data['receiver_district'];
                    $shipData['ship_to_address'] = $data['receiver_address'];
                    $shipData['store_no'] = $data['store_no'];
                    $shipData['remark'] = '';
                    $shipData['created_by'] = -1;
                    $shipData['created_at'] = $now;
                    $shipData['updated_by'] = -1;
                    $shipData['updated_at'] = $now;
                    $shipData['supplier_id'] = $shipment['supplier'][$main_product_id];
                    $ship_id = Shipment::insertGetId($shipData);

                    //出貨單單身
                    foreach ($order_detail as $seq => $detail) {
                        $shipDetail['shipment_id'] = $ship_id;
                        $shipDetail['seq'] = $seq;
                        $shipDetail['order_detail_id'] = $detail['id'];
                        $shipDetail['product_item_id'] = $detail['product_item_id'];
                        $shipDetail['item_no'] = $detail['item_no'];
                        $shipDetail['qty'] = $detail['qty'];
                        $shipDetail['created_by'] = -1;
                        $shipDetail['created_at'] = $now;
                        $shipDetail['updated_by'] = -1;
                        $shipDetail['updated_at'] = $now;
                        $shipDetail['order_detail_seq'] = $detail['seq']; //新增加欄位，同$shipDetail['seq']
                        $shipDetail['selling_price'] = $detail['selling_price'];
                        $shipDetail['purchase_price'] = $detail['purchase_price'];
                        $shipDetail['record_identity'] = $detail['record_identity'];
                        ShipmentDetail::insertGetId($shipDetail);
                    }
                }

            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('shipment')->error("出貨單建立失敗：" . $e);
            $result = false;
        }
        return $result;
    }

}
