<?php


namespace App\Services;

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

class APIOrdersServices
{

    public function __construct()
    {

    }


    /**
     * 訂單
     * @param 購物車清單, 前端的訂單資料
     * @return string
     */
    public function setOrders($cart, $order, $campaign_gift, $campaign_discount)
    {
        dd($campaign_discount);
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        $random = Str::random(6);
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
            $webData['updated_by'] = $member_id;
            $webData['updated_at'] = $now;
            //$order_id = Order::insertGetId($webData);
            $order_id = 30;
            //訂單單身
            $addColumn = [
                "order_id", "seq", "product_id", "product_item_id", "item_no",
                "selling_price", "qty", "unit_price", "campaign_discount", "subtotal",
                "record_identity", "point_discount", "points",
                "utm_source", "utm_medium", "utm_campaign", "utm_sales", "utm_time",
                "created_by", "updated_by", "created_at", "updated_at",
                "returned_qty", "returned_campaign_discount", "returned_subtotal", "returned_point_discount", "returned_points"
            ];
            //活動折抵紀錄
            $addCampaign = [
                "order_id", "level_code", "group_seq", "order_detail_id", "promotion_campaign_id",
                "product_id", "product_item_id", "item_no", "discount", "record_identity",
                "is_voided",
                "created_by", "updated_by", "created_at", "updated_at"
            ];
            $seq = 0;
            $details = [];
            $detail_gift = [];
            $point_rate = 0;
            foreach ($cart['list'] as $products) {
                foreach ($products['itemList'] as $item) {
                    $seq++;
                    if ($item['campaignDiscountStatus']) { //有活動折扣
                        $discount = -($products['sellingPrice'] * $item['itemQty'] - $item['amount']);
                    } else {
                        $discount = 0;
                    }
                    if ($order['point_discount'] < 0) { //有用點數折現金
                        $discount_rate[$seq] = round(($item['amount'] / $order['total_price']) * 100);
                        $point_rate += $discount_rate[$seq];
                    } else {
                        $discount_rate[$seq] = 0;
                        $point_rate = 100;
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
                        "point_discount" => -$discount_rate[$seq],
                        "points" => (-$discount_rate[$seq] / $cart['point']['exchangeRate']),
                        "utm_source" => $utm_info[$item['itemId']]->utm_source,
                        "utm_medium" => $utm_info[$item['itemId']]->utm_medium,
                        "utm_campaign" => $utm_info[$item['itemId']]->utm_campaign,
                        "utm_sales" => $utm_info[$item['itemId']]->utm_sales,
                        "utm_time" => $utm_info[$item['itemId']]->utm_time,
                        "created_by" => $member_id,
                        "created_at" => $member_id,
                        "updated_by" => $now,
                        "updated_at" => $now,
                        "returned_qty" => 0,
                        "returned_campaign_discount" => 0,
                        "returned_subtotal" => 0,
                        "returned_point_discount" => 0,
                        "returned_points" => 0
                    ];
                    $order_detail_id = OrderDetail::insertGetId($details[$seq]);
                    if ($cart['campaignDiscountId']) {
                        $campaign_details[$seq] = [
                            "order_id" => $order_id,
                            "level_code" => $products['campaignDiscountLevel'],
                            "product_id" => $products['productID'],
                            "product_item_id" => $item['itemId'],
                            "item_no" => $item['itemNo'],
                            "selling_price" => $products['sellingPrice'],
                            "qty" => $item['itemQty'],
                            "unit_price" => $item['itemPrice'],
                            "campaign_discount" => $discount,
                            "subtotal" => $item['amount'],
                            "record_identity" => "M",
                            "point_discount" => -$discount_rate[$seq],
                            "points" => (-$discount_rate[$seq] / $cart['point']['exchangeRate']),
                            "utm_source" => $utm_info[$item['itemId']]->utm_source,
                            "utm_medium" => $utm_info[$item['itemId']]->utm_medium,
                            "utm_campaign" => $utm_info[$item['itemId']]->utm_campaign,
                            "utm_sales" => $utm_info[$item['itemId']]->utm_sales,
                            "utm_time" => $utm_info[$item['itemId']]->utm_time,
                            "created_by" => $member_id,
                            "created_at" => $member_id,
                            "updated_by" => $now,
                            "updated_at" => $now,
                            "returned_qty" => 0,
                            "returned_campaign_discount" => 0,
                            "returned_subtotal" => 0,
                            "returned_point_discount" => 0,
                            "returned_points" => 0
                        ];
                    }
                    if (isset($item['campaignGiftAway']['campaignProdList'])) {
                        if ($item['campaignGiftAway']['campaignGiftStatus']) { //符合條件
                            foreach ($item['campaignGiftAway']['campaignProdList'] as $gifts => $gift) {
                                $detail_gift[$seq] = [
                                    $order_id,
                                    ($seq + 1),
                                    $gift['productId'],
                                    $prod_info[$gift['productId']]['id'],
                                    $prod_info[$gift['productId']]['item_no'],
                                    0,
                                    $item['itemQty'],
                                    0,
                                    $discount,
                                    0,
                                    'G',
                                    0,
                                    0,
                                    $utm_info[$item['itemId']]->utm_source,
                                    $utm_info[$item['itemId']]->utm_medium,
                                    $utm_info[$item['itemId']]->utm_campaign,
                                    $utm_info[$item['itemId']]->utm_sales,
                                    $utm_info[$item['itemId']]->utm_time,
                                    $member_id,
                                    $member_id,
                                    $now,
                                    $now,
                                    0, 0, 0, 0, 0
                                ];
                            }
                        }
                    } else {
                        foreach ($item['campaignGiftAway'] as $gifts => $gift) {
                            $detail_gift[$seq] = [
                                $order_id,
                                ($seq + 1),
                                $gift['productId'],
                                $prod_info[$gift['productId']]['id'],
                                $prod_info[$gift['productId']]['item_no'],
                                0,
                                $gift['assignedQty'],
                                0,
                                $discount,
                                0,
                                'G',
                                0,
                                0,
                                $utm_info[$item['itemId']]->utm_source,
                                $utm_info[$item['itemId']]->utm_medium,
                                $utm_info[$item['itemId']]->utm_campaign,
                                $utm_info[$item['itemId']]->utm_sales,
                                $utm_info[$item['itemId']]->utm_time,
                                $member_id,
                                $member_id,
                                $now,
                                $now,
                                0, 0, 0, 0, 0
                            ];
                        }
                    }
                }
            }
            if ($point_rate != 100) { //點數比例加總不等於100時，把最後一筆資料的比例做修正
                $details[($seq - 1)][11] = $details[($seq - 1)][11] - 100 + $point_rate;
                $details[($seq - 1)][12] = ($details[($seq - 1)][11] / $cart['point']['exchangeRate']);
            }

            if ($details) {
                $arr_detail = array_merge($details, $detail_gift);
                $orderInstance = new OrderDetail();
                $batchSize = 500;
                $insert = Batch::insert($orderInstance, $addColumn, $arr_detail, $batchSize);
            }
            if ($insert['totalRows'] > 0) {
                DB::commit();
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
