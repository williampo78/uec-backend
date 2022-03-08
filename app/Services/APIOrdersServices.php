<?php


namespace App\Services;

use App\Models\StockTransactionLog;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Batch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderCampaignDiscount;
use App\Models\ShoppingCartDetails;
use App\Models\ProductItems;
use App\Models\OrderPayment;
use App\Services\APITapPayService;
use App\Services\StockService;
use App\Services\APIService;

class APIOrdersServices
{

    public function __construct(APITapPayService $apiTapPayService, StockService $stockService, APIService $apiService)
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
    public function setOrders($cart, $order, $campaigns, $campaign_gift, $campaign_discount)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        $random = Str::random(6);
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        /* test
        $webData = [];
        $webData['agent_id'] = 1;
        $webData['order_no'] = "OD220107GFAZYW";
        $webData['member_id'] = $member_id;
        $webData['member_account'] = $order['buyer']['mobile'];
        $webData['ordered_date'] = $now;
        $webData['is_latest'] = 1;
        $webData['is_cash_on_delivery'] = 0;
        $webData['status_code'] = $order['status_code'];
        $webData['payment_method'] = $order['payment_method'];
        $webData['lgst_method'] = $order['lgst_method'];
        $webData['is_shipping_free'] = ($order['shipping_fee'] == 0 ? 1 : 0);
        $webData['shipping_fee'] = $order['shipping_fee'];
        $webData['shipping_free_threshold'] = 0;
        $webData['total_amount'] = $order['total_price'];
        $webData['cart_campaign_discount'] = $order['discount'];
        $webData['point_discount'] = $order['point_discount'];
        $webData['paid_amount'] = ($order['total_price'] + $order['discount'] + $order['point_discount'] + $order['shipping_fee']);
        $webData['points'] = $order['points'];
        $webData['pay_status'] = 'PENDING';
        $webData['buyer_name'] = $order['buyer']['name'];
        $webData['buyer_mobile'] = $order['buyer']['mobile'];
        $webData['buyer_email'] = $order['buyer']['email'];
        $webData['buyer_zip_code'] = $order['buyer']['zip'];
        $webData['buyer_city'] = $order['buyer']['city'];
        $webData['buyer_district'] = $order['buyer']['district'];
        $webData['buyer_address'] = $order['buyer']['address'];
        $webData['receiver_name'] = $order['receiver']['name'];
        $webData['receiver_mobile'] = $order['receiver']['mobile'];
        $webData['receiver_zip_code'] = $order['receiver']['zip'];
        $webData['receiver_city'] = $order['receiver']['city'];
        $webData['receiver_district'] = $order['receiver']['district'];
        $webData['receiver_address'] = $order['receiver']['address'];
        $webData['invoice_usage'] = $order['invoice']['usage'];
        $webData['carrier_type'] = $order['invoice']['carrier_type'];
        $webData['carrier_no'] = $order['invoice']['carrier_no'];
        $webData['donated_institution'] = $order['invoice']['donated_code'];
        $webData['buyer_gui_number'] = $order['invoice']['buyer_gui_number'];
        $webData['buyer_title'] = $order['invoice']['buyer_title'];
        $webData['created_by'] = $member_id;
        $webData['created_at'] = $now;
        $webData['updated_by'] = -1;
        $webData['updated_at'] = $now;

        $payment_id = 26;
        $webData['prime'] = $order['taypay_prime'];
        $tapPay = $this->apiTapPayService->payByPrime($webData);
        $tapPayResult = json_decode($tapPay, true);
        if ($tapPayResult['status'] == 0) {
            $payment = OrderPayment::where('id', $payment_id)->update(['rec_trade_id' => $tapPayResult['rec_trade_id']]);
            if ($payment) {
                $result['status'] = 200;
                $result['payment_url'] = $tapPayResult['payment_url'];
                $result['rec_trade_id'] = $tapPayResult['rec_trade_id'];
            } else {
                $result['status'] = 401;
            }
        } else {
            $result['status'] = $tapPayResult['status'];
            $result['msg'] = $tapPayResult['msg'];
        }
        return $result;
        */

        $utms = ShoppingCartDetails::where('member_id', '=', $member_id)->where('status_code', '=', 0)->get();
        $utm_info = [];
        foreach ($utms as $utm) {
            $utm_info[$utm->product_item_id] = $utm;
        }

        $product_items = ProductItems::all();
        $prod_info = [];
        foreach ($product_items as $product_item) {
            $prod_info[$product_item->product_id] = $product_item;
        }

        //行銷活動
        foreach ($campaigns as $product_id => $item) {
            foreach ($item as $k => $v) {
                $campaign[$v->level_code][$v->category_code][$product_id] = $v;
            }
        }
        DB::beginTransaction();
        try {
            //訂單單頭
            $webData = [];
            $webData['agent_id'] = 1;
            $webData['order_no'] = "OD" . date("ymd") . strtoupper($random);
            $webData['member_id'] = $member_id;
            $webData['member_account'] = $order['buyer']['mobile'];
            $webData['ordered_date'] = $now;
            $webData['is_latest'] = 1;
            $webData['is_cash_on_delivery'] = 0;
            $webData['status_code'] = $order['status_code'];
            $webData['payment_method'] = $order['payment_method'];
            $webData['lgst_method'] = $order['lgst_method'];
            $webData['is_shipping_free'] = ($order['shipping_fee'] == 0 ? 1 : 0);
            $webData['shipping_fee'] = $order['shipping_fee'];
            $webData['shipping_free_threshold'] = $cart['feeInfo']['free_threshold'];
            $webData['total_amount'] = $order['total_price'];
            $webData['cart_campaign_discount'] = $order['discount'];
            $webData['point_discount'] = $order['point_discount'];
            $webData['paid_amount'] = ($order['total_price'] + $order['discount'] + $order['point_discount'] + $order['shipping_fee']);
            $webData['points'] = $order['points'];
            $webData['pay_status'] = 'PENDING';
            $webData['buyer_name'] = $order['buyer']['name'];
            $webData['buyer_mobile'] = $order['buyer']['mobile'];
            $webData['buyer_email'] = $order['buyer']['email'];
            $webData['buyer_zip_code'] = $order['buyer']['zip'];
            $webData['buyer_city'] = $order['buyer']['city'];
            $webData['buyer_district'] = $order['buyer']['district'];
            $webData['buyer_address'] = $order['buyer']['address'];
            $webData['receiver_name'] = $order['receiver']['name'];
            $webData['receiver_mobile'] = $order['receiver']['mobile'];
            $webData['receiver_zip_code'] = $order['receiver']['zip'];
            $webData['receiver_city'] = $order['receiver']['city'];
            $webData['receiver_district'] = $order['receiver']['district'];
            $webData['receiver_address'] = $order['receiver']['address'];
            $webData['invoice_usage'] = $order['invoice']['usage'];
            $webData['carrier_type'] = $order['invoice']['carrier_type'];
            $webData['carrier_no'] = $order['invoice']['carrier_no'];
            $webData['donated_institution'] = $order['invoice']['donated_code'];
            $webData['buyer_gui_number'] = $order['invoice']['buyer_gui_number'];
            $webData['buyer_title'] = $order['invoice']['buyer_title'];
            $webData['created_by'] = $member_id;
            $webData['created_at'] = $now;
            $webData['updated_by'] = -1;
            $webData['updated_at'] = $now;

            $order_id = Order::insertGetId($webData);

            //建立一筆金流單
            $paymantData = [];
            $paymantData['source_table_name'] = 'orders';
            $paymantData['source_table_id'] = $order_id;
            $paymantData['order_no'] = $webData['order_no'];
            $paymantData['payment_type'] = 'PAY';
            $paymantData['payment_method'] = $order['payment_method'];
            $paymantData['payment_status'] = 'PENDING';
            $paymantData['amount'] = $webData['paid_amount'];
            $paymantData['point_discount'] = $webData['point_discount'];
            $paymantData['points'] = $webData['points'];
            $paymantData['record_created_reason'] = 'ORDER_CREATED';
            $paymantData['created_by'] = $member_id;
            $paymantData['created_at'] = $now;
            $paymantData['updated_by'] = -1;
            $paymantData['updated_at'] = $now;
            $paymantData['rec_trade_id'] = '';
            $payment_id = OrderPayment::insertGetId($paymantData);

            //訂單單身
            $seq = 0;
            $details = [];
            $detail_count = 0;
            $point_rate = 0;
            $discount_group = 0;
            foreach ($cart['list'] as $products) {
                foreach ($products['itemList'] as $item) {
                    $seq++;
                    $discount_group ++;
                    if ($item['campaignDiscountStatus']) { //有活動折扣
                        $discount = -($products['sellingPrice'] * $item['itemQty'] - $item['amount']);
                    } else {
                        $discount = 0;
                    }
                    if ($order['point_discount'] < 0) { //有用點數折現金
                        $discount_rate[$seq] = ($item['amount'] / $order['total_price']);
                        $point_rate += $discount_rate[$seq];
                    } else {
                        $discount_rate[$seq] = 0;
                        $point_rate = 1;
                    }
                    $details[$seq] = [
                        "order_id" => $order_id,
                        "seq" => $seq,
                        "product_id" => $products['productID'],
                        "product_item_id" => $item['itemId'],
                        "item_no" => $item['itemNo'],
                        "selling_price" => $products['sellingPrice'],
                        "qty" => $item['itemQty'],
                        "unit_price" => $item['itemPrice'],
                        "campaign_discount" => $discount,
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
                        "created_at" => $now,
                        "updated_at" => $now,
                        "returned_qty" => 0,
                        "returned_campaign_discount" => 0,
                        "returned_subtotal" => 0,
                        "returned_point_discount" => 0,
                        "returned_points" => 0
                    ];
                    $order_detail_id = OrderDetail::insertGetId($details[$seq]);

                    $campaign_id = 0;
                    if (isset($campaign['PRD']['GIFT'][$products['productID']])){//有單品滿額贈時，正貨也寫入discount
                        $campaign_details[$seq] = [
                            "order_id" => $order_id,
                            "level_code" => 'PRD',
                            "group_seq" => $discount_group,
                            "order_detail_id" => $order_detail_id,
                            "promotion_campaign_id" => $campaign['PRD']['GIFT'][$products['productID']]->id,
                            "product_id" => $products['productID'],
                            "product_item_id" => $prod_info[$products['productID']]['id'],
                            "item_no" => $prod_info[$products['productID']]['item_no'],
                            "discount" => 0,
                            "record_identity" => "M",
                            "created_by" => $member_id,
                            "updated_by" => $member_id,
                            "created_at" => $now,
                            "updated_at" => $now,
                        ];
                        OrderCampaignDiscount::insert($campaign_details[$seq]);
                        $campaign_id = $campaign['PRD']['GIFT'][$products['productID']]->id;
                    }
                    if ($order_detail_id > 0) {
                        $detail_count++;
                    }

                    //訂單明細建立後，更新購物車中的商品狀態為 - 已轉為訂單
                    $updData['status_code'] = 1;
                    ShoppingCartDetails::where('member_id', '=', $member_id)->where('product_item_id', '=', $item['itemId'])->update($updData);


                    //有單品滿額贈品時先新增單身
                    if (isset($item['campaignGiftAway']['campaignProdList'])) {
                        if ($item['campaignGiftAway']['campaignGiftStatus']) { //符合條件
                            foreach ($item['campaignGiftAway']['campaignProdList'] as $gifts => $gift) {
                                $seq++;
                                $details[$seq] = [
                                    "order_id" => $order_id,
                                    "seq" => $seq,
                                    "product_id" => $gift['productId'],
                                    "product_item_id" => $prod_info[$gift['productId']]['id'],
                                    "item_no" => $prod_info[$gift['productId']]['item_no'],
                                    "selling_price" => $products['sellingPrice'],
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
                                    "created_at" => $now,
                                    "updated_at" => $now,
                                    "returned_qty" => 0,
                                    "returned_campaign_discount" => 0,
                                    "returned_subtotal" => 0,
                                    "returned_point_discount" => 0,
                                    "returned_points" => 0
                                ];
                                $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                                //寫入折扣資訊
                                if ($campaign_id != $item['campaignGiftAway']['campaignGiftId']){
                                    $discount_group ++;
                                }
                                $campaign_details[$seq] = [
                                    "order_id" => $order_id,
                                    "level_code" => $campaign_gift['PROD'][$item['campaignGiftAway']['campaignGiftId']][$gift['productId']]['level_code'],
                                    "group_seq" => $discount_group,
                                    "order_detail_id" => $order_detail_id,
                                    "promotion_campaign_id" => $item['campaignGiftAway']['campaignGiftId'],
                                    "product_id" => $gift['productId'],
                                    "product_item_id" => $prod_info[$gift['productId']]['id'],
                                    "item_no" => $prod_info[$gift['productId']]['item_no'],
                                    "discount" => 0,
                                    "record_identity" => "G",
                                    "created_by" => $member_id,
                                    "updated_by" => $member_id,
                                    "created_at" => $now,
                                    "updated_at" => $now,
                                ];
                                OrderCampaignDiscount::insert($campaign_details[$seq]);
                            }
                        }
                    } else {
                        foreach ($item['campaignGiftAway'] as $gifts => $gift) {
                            $seq++;
                            $details[$seq] = [
                                "order_id" => $order_id,
                                "seq" => $seq,
                                "product_id" => $gift['productId'],
                                "product_item_id" => $prod_info[$gift['productId']]['id'],
                                "item_no" => $prod_info[$gift['productId']]['item_no'],
                                "selling_price" => $products['sellingPrice'],
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
                                "created_at" => $now,
                                "updated_at" => $now,
                                "returned_qty" => 0,
                                "returned_campaign_discount" => 0,
                                "returned_subtotal" => 0,
                                "returned_point_discount" => 0,
                                "returned_points" => 0
                            ];
                            $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                            //寫入折扣資訊
                            if ($campaign_id != $item['campaignGiftAway']['campaignGiftId']){
                                $discount_group ++;
                            }
                            $campaign_details[$seq] = [
                                "order_id" => $order_id,
                                "level_code" => $campaign_gift['PROD'][$item['campaignGiftAway']['campaignGiftId']][$gift['productId']]['level_code'],
                                "group_seq" => $discount_group,
                                "order_detail_id" => $order_detail_id,
                                "promotion_campaign_id" => $item['campaignGiftAway']['campaignGiftId'],
                                "product_id" => $gift['productId'],
                                "product_item_id" => $prod_info[$gift['productId']]['id'],
                                "item_no" => $prod_info[$gift['productId']]['item_no'],
                                "discount" => 0,
                                "record_identity" => "G",
                                "created_by" => $member_id,
                                "updated_by" => $member_id,
                                "created_at" => $now,
                                "updated_at" => $now,
                            ];
                            OrderCampaignDiscount::insert($campaign_details[$seq]);
                        }
                    }

                    //有折扣則寫入折扣資訊
                    if ($item['campaignDiscountId'] && $item['campaignDiscountStatus']) {
                        if ($campaign_id != $item['campaignDiscountId']){
                            $discount_group ++;
                        }
                        $campaign_details[$seq] = [
                            "order_id" => $order_id,
                            "level_code" => $campaigns[$products['productID']][0]->level_code,
                            "group_seq" => $discount_group,
                            "order_detail_id" => $order_detail_id,
                            "promotion_campaign_id" => $item['campaignDiscountId'],
                            "product_id" => $products['productID'],
                            "product_item_id" => $item['itemId'],
                            "item_no" => $item['itemNo'],
                            "discount" => $discount,
                            "record_identity" => "M",
                            "created_by" => $member_id,
                            "updated_by" => $member_id,
                            "created_at" => $now,
                            "updated_at" => $now,
                        ];
                        OrderCampaignDiscount::insert($campaign_details[$seq]);
                    }
                }
            }

            //購物車滿額新增單身
            if ($cart['giftAway']) {
                foreach ($cart['giftAway'] as $gift) {
                    $seq++;
                    $details[$seq] = [
                        "order_id" => $order_id,
                        "seq" => $seq,
                        "product_id" => $gift['productId'],
                        "product_item_id" => $prod_info[$gift['productId']]['id'],
                        "item_no" => $prod_info[$gift['productId']]['item_no'],
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
                        "created_at" => $now,
                        "updated_at" => $now,
                        "returned_qty" => 0,
                        "returned_campaign_discount" => 0,
                        "returned_subtotal" => 0,
                        "returned_point_discount" => 0,
                        "returned_points" => 0
                    ];
                    $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                    $discount_group++;
                    //寫入折扣資訊
                    $campaign_details[$seq] = [
                        "order_id" => $order_id,
                        "level_code" => $campaign_gift['PROD'][$gift['campaignId']][$gift['productId']]['level_code'],
                        "group_seq" => $discount_group,
                        "order_detail_id" => $order_detail_id,
                        "promotion_campaign_id" => $gift['campaignId'],
                        "product_id" => $gift['productId'],
                        "product_item_id" => $prod_info[$gift['productId']]['id'],
                        "item_no" => $prod_info[$gift['productId']]['item_no'],
                        "discount" => 0,
                        "record_identity" => "G",
                        "created_by" => $member_id,
                        "updated_by" => $member_id,
                        "created_at" => $now,
                        "updated_at" => $now,
                    ];
                    OrderCampaignDiscount::insert($campaign_details[$seq]);
                }
            }

            $cartTotal = $cart['totalPrice'];
            $cartDiscount = 0;
            $campaignID = '';
            $compare_n_value = 0;
            foreach ($campaign_discount as $items => $item) {
                if ($compare_n_value > $item->n_value) continue;
                if ($cart['totalPrice'] >= $item->n_value) {
                    if ($item->campaign_type == 'CART01') { //﹝滿額﹞購物車滿N元，打X折
                        $cartDiscount = $cartTotal - ($cartTotal * $item->x_value); //打折10000-(10000*0.85)
                        $campaignID = $item->id;
                    } elseif ($item->campaign_type == 'CART02') { //﹝滿額﹞購物車滿N元，折X元
                        $cartDiscount = $item->x_value; //打折後10000-1000
                        $campaignID = $item->id;
                    }
                    $compare_n_value = $item->n_value;
                }
            }
            //購物車滿額活動
            if ($cartDiscount != 0) {
                $discount_group++;
                $seq ++;
                //寫入折扣資訊
                $campaign_details[$seq] = [
                    "order_id" => $order_id,
                    "level_code" => 'CART',
                    "group_seq" => $discount_group,
                    "order_detail_id" => null,
                    "promotion_campaign_id" => $campaignID,
                    "product_id" => null,
                    "product_item_id" => null,
                    "item_no" => null,
                    "discount" => ($cartDiscount*-1),
                    "record_identity" => null,
                    "created_by" => $member_id,
                    "updated_by" => $member_id,
                    "created_at" => $now,
                    "updated_at" => $now,
                ];
                OrderCampaignDiscount::insert($campaign_details[$seq]);
            }

            $pointData = [];
            if ($point_rate != 1) { //點數比例加總不等於1時，把最後一筆資料的比例做修正
                $detail = OrderDetail::where('order_id', '=', $order_id)->where('record_identity', '=', 'M')->orderBy('seq', 'DESC')->first();
                $pointData['point_discount'] = $detail->point_discount - 1 + $point_rate;
                $pointData['points'] = ($pointData['point_discount'] / $cart['point']['exchangeRate']);
                OrderDetail::where('id', $detail->id)->update($pointData);
            }


            //庫存與LOG相關
            $order_details = OrderDetail::getOrderDetails($order_id);
            foreach ($order_details as $detail) {
                $stock = $this->stockService->getStockByItem($warehouseCode, $detail->product_item_id);
                if (isset($stock['id'])) {
                    $updStock = WarehouseStock::where('id', '=', $stock['id'])->update(['stock_qty' => ($stock['stockQty'] - $detail->qty)]);
                    if ($updStock) {
                        $logData['transaction_type'] = 'ORDER_SHIP';
                        $logData['transaction_date'] = $now;
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
                        $logData['created_at'] = $now;
                        $logData['updated_by'] = -1;
                        $logData['updated_at'] = $now;
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
                $order_payment = OrderPayment::where('id', '=', $payment_id)->update(['point_api_status' => $thisStatus, 'point_api_date' => $pointStatus['timestamp'], 'point_api_log' => json_encode($pointStatus)]);
                if ($order_payment && $pointStatus['status'] == '200') {
                    $isTapPay = 1;
                } else {
                    $result['status'] = 401;
                    $result['payment_url'] = null;
                    Log::channel('changepoint')->info('扣點異常 ! webdata :' . json_encode($webData) . 'req:' . json_encode($pointData) . 'rep:' . json_encode($pointStatus));
                    DB::rollBack();
                    return $result;
                    $isTapPay = 0;
                }
            } else {
                $isTapPay = 1;
            }

            //TapPay
            if ($isTapPay) {
                $webData['prime'] = $order['taypay_prime'];
                $tapPay = $this->apiTapPayService->payByPrime($webData);
                $tapPayResult = json_decode($tapPay, true);
                if ($tapPayResult['status'] == 0) {
                    $payment = OrderPayment::where('id', $payment_id)->update(['rec_trade_id' => $tapPayResult['rec_trade_id'], 'latest_api_date' => $now]);
                    if ($payment) {
                        $result['status'] = 200;
                        $result['payment_url'] = $tapPayResult['payment_url'];
                    } else {
                        $result['status'] = 401;
                        $result['payment_url'] = null;
                    }
                } else {
                    $result['status'] = $tapPayResult['status'];
                    $result['payment_url'] = null;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result['status'] = 401;
            $result['payment_url'] = null;
        }

        return $result;
    }

}
