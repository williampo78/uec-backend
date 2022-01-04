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

class APIOrdersServices
{

    public function __construct()
    {

    }


    /**
     * 訂單
     * @param
     * @return string
     */
    public function setOrders($cart, $order)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        $random = Str::random(6);
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
                "record_identity", "point_discount",
                "utm_source", "utm_medium", "utm_campaign", "utm_sales", "utm_time",
                "created_by", "updated_by", "created_at", "updated_at",
                "returned_qty", "returned_campaign_discount", "returned_subtotal", "returned_point_discount", "returned_points"
            ];
            $i = 0;
            $details = [];
            foreach ($cart['list'] as $products) {
                print_r($products['itemList']);
                foreach ($products['itemList'] as $item) {
                    $i++;
                    $details[$i] = [
                        $order_id,
                        $i,
                        $products['productID'],
                        $item['itemId'],

                    ];
                }
            }

            DB::commit();

            if ($order_id > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }

            $result = $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }


}
