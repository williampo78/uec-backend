<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\GetMemberOrderDetailsRequest;
use App\Http\Requests\api\GetMemberOrdersRequest;
use App\Http\Requests\api\ResetMemberPasswordRequest;
use App\Services\APIService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    private $api_service;

    public function __construct(APIService $api_service)
    {
        $this->api_service = $api_service;
    }

    public function resetPassword(ResetMemberPasswordRequest $request)
    {
        $token = $request->bearerToken();
        $request_payloads = $request->input();

        if (empty($token)) {
            return response()->json([
                'message' => 'token不存在',
            ], 404);
        }

        $results = $this->api_service->resetPassword($token, $request_payloads);

        // 發生錯誤
        if ($results['status_code'] != 200) {
            $payloads = [];
            $errors = [];

            if (isset($results['payloads']['error'])) {
                foreach ($results['payloads']['error'] as $key => $value) {
                    $errors[$key][] = $value;
                }
            }

            if (empty($errors)) {
                $payloads = [
                    'message' => $results['payloads']['message'],
                ];
            } else {
                $payloads = [
                    'message' => $results['payloads']['message'],
                    'errors' => $errors,
                ];
            }

            return response()->json($payloads, $results['status_code']);
        }

        return response()->noContent();
    }

    public function getOrders(GetMemberOrdersRequest $request)
    {
        try {
            $member_id = auth('api')->userOrFail()->member_id;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'message' => '會員不存在',
            ], 404);
        }

        $order_service = new OrderService;
        $date = $request->query('date');
        $two_years_ago_date = Carbon::now()->subYears(2);
        $ordered_date_start = Carbon::parse($date)->subDays(90)->startOfDay();
        $ordered_date_end = Carbon::parse($date)->endOfDay();

        /**
         * 可以顯示的訂單日期範圍
         * 目前時間: 2022/1/7，兩年前: 2020/1/7
         *
         * Example1:
         * 填寫時間: 2021/07/27，預計日期區間: 2021/04/28 ~ 2021/07/27
         * 能夠搜尋的日期區間 2021/04/28 ~ 2021/07/27
         *
         * Example2:
         * 填寫時間: 2020/2/7，預計日期區間: 2019/11/09 ~ 2020/2/7
         * 能夠搜尋的日期區間 2020/1/7 ~ 2020/2/7
         *
         * Example3:
         * 填寫時間: 2019/12/7
         * 不能搜尋到任何訂單
         */
        if ($ordered_date_end->lessThan($two_years_ago_date)) {
            return response()->json([
                'message' => '查詢成功',
                'results' => [
                    'order_totals' => 0,
                ],
            ], 200);
        }

        if ($ordered_date_start->lessThan($two_years_ago_date)) {
            $ordered_date_start = $two_years_ago_date;
        }

        // 取得訂單
        $orders = $order_service->getOrders([
            'revision_no' => 0,
            'ordered_date_start' => $ordered_date_start,
            'ordered_date_end' => $ordered_date_end,
            'member_id' => $member_id,
        ]);

        // 訂單總數
        $order_totals = count($orders);
        // 沒有任何訂單
        if ($order_totals < 1) {
            return response()->json([
                'message' => '查詢成功',
                'results' => [
                    'order_totals' => $order_totals,
                ],
            ], 200);
        }

        // 訂單
        $orders->transform(function ($order) {
            $order->product_totals = 0;

            // 訂單時間
            $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i:s');

            // 訂單狀態
            $order->order_status = $order->order_status_desc;

            // 物流方式
            $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

            // 物流公司
            $order->lgst_company_code = config('uec.lgst_company_code_options')[$order->lgst_company_code] ?? null;

            // 配送方式
            if (isset($order->lgst_method, $order->lgst_company_code)) {
                $order->delivery_method = "{$order->lgst_method}-{$order->lgst_company_code}";
            } else {
                $order->delivery_method = null;
            }

            // 出貨時間
            if (isset($order->shipped_at)) {
                $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y-m-d H:i:s');
            }

            // 結帳金額
            $order->paid_amount = number_format($order->paid_amount);

            // 訂單明細
            if (isset($order->order_details)) {
                // 商品總數
                $order->product_totals = count($order->order_details);
                // 取得第一筆訂單明細
                $order_detail = $order->order_details->first();

                // 單價 (單品折扣後的單價，即前台購物車呈現的單價)
                $order_detail->unit_price = number_format($order_detail->unit_price);

                // 小計
                $order_detail->subtotal = number_format($order_detail->subtotal);

                // 商品圖片
                if (isset($order_detail->product_photos)) {
                    $product_photo = $order_detail->product_photos->first();
                    $photo_url = config('filesystems.disks.s3.url') . $product_photo->photo_name;
                    $order->photo_url = $photo_url;
                }

                $order->product_name = $order_detail->product_name;
                $order->spec_1_value = $order_detail->spec_1_value;
                $order->spec_2_value = $order_detail->spec_2_value;
                $order->qty = $order_detail->qty;
                $order->unit_price = $order_detail->unit_price;
                $order->subtotal = $order_detail->subtotal;
            }

            // 出貨單
            if (isset($order->shipments)) {
                $shipment = $order->shipments->first();
                $order->package_no = $shipment->package_no;
            }

            return $order->only([
                'order_no',
                'ordered_date',
                'order_status',
                'photo_url',
                'product_name',
                'spec_1_value',
                'spec_2_value',
                'qty',
                'unit_price',
                'subtotal',
                'product_totals',
                'delivery_method',
                'shipped_at',
                'package_no',
                'invoice_no',
                'paid_amount',
            ]);
        });

        return response()->json([
            'message' => '查詢成功',
            'results' => [
                'order_totals' => $order_totals,
                'orders' => $orders,
            ],
        ], 200);
    }

    public function getOrderDetails(GetMemberOrderDetailsRequest $request)
    {
        try {
            $member_id = auth('api')->userOrFail()->member_id;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'message' => '會員不存在',
            ], 404);
        }

        $order_service = new OrderService;
        $order_no = $request->route('order_no');

        // 取得訂單
        $order = $order_service->getOrders([
            'revision_no' => 0,
            'order_no' => $order_no,
            'member_id' => $member_id,
        ])->first();

        // 沒有任何訂單
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 訂單時間
        $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i:s');

        // 付款完成時間
        if (isset($order->paid_at)) {
            $order->paid_at = Carbon::parse($order->paid_at)->format('Y-m-d H:i:s');
        }

        // 出貨時間
        if (isset($order->shipped_at)) {
            $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y-m-d H:i:s');
        }

        // 宅配配達時間/超商取件時間
        if (isset($order->delivered_at)) {
            $order->delivered_at = Carbon::parse($order->delivered_at)->format('Y-m-d H:i:s');
        }

        // 訂單狀態
        $order->order_status = $order->order_status_desc;

        // 付款方式
        $order->payment_method = config('uec.payment_method_options')[$order->payment_method] ?? null;

        // 收件者姓名
        if (isset($order->receiver_name)) {
            $order->receiver_name = (string)Str::of($order->receiver_name)->mask('*', 0, 1)->mask('*', -1, 1);
        }

        // 收件者手機
        if (isset($order->receiver_mobile)) {
            $order->receiver_mobile = (string)Str::of($order->receiver_mobile)->mask('*', -3);
        }

        // 收件者地址
        $address = '';
        $address .= $order->receiver_city ?? '';
        $address .= $order->receiver_district ?? '';
        $address .= $order->receiver_address ?? '';
        $order->receiver_address = $address;

        // 物流方式
        $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

        // 物流公司
        $order->lgst_company_code = config('uec.lgst_company_code_options')[$order->lgst_company_code] ?? null;

        // 配送方式
        if (isset($order->lgst_method, $order->lgst_company_code)) {
            $order->delivery_method = "{$order->lgst_method}-{$order->lgst_company_code}";
        } else {
            $order->delivery_method = null;
        }

        // 出貨時間
        if (isset($order->shipped_at)) {
            $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y-m-d H:i:s');
        }

        // 商品原總計
        $order->total_amount = number_format($order->total_amount);

        // 運費
        $order->shipping_fee = number_format($order->shipping_fee);

        // 會員點數折抵金額，由負數改為正數呈現
        $order->point_discount = number_format($order->point_discount * -1);

        // 滿額活動折抵金額，由負數改為正數呈現
        $order->cart_campaign_discount = number_format($order->cart_campaign_discount * -1);

        // 結帳時使用的會員點數，由負數改為正數呈現
        $order->points = $order->points * -1;

        // 實際支付金額
        $order->paid_amount = number_format($order->paid_amount);

        // 商品總數
        $order->product_totals = 0;

        // 發票用途
        $order->invoice_type = config('uec.invoice_usage_options')[$order->invoice_usage] ?? null;

        // 發票開立時間
        if (isset($order->invoice_date)) {
            $order->invoice_date = Carbon::parse($order->invoice_date)->format('Y-m-d H:i:s');
        }

        // 統編
        $order->invoice_gui_number = $order->buyer_gui_number;

        // 發票抬頭
        $order->invoice_title = $order->buyer_title;

        // 訂單取消時間
        if (isset($order->cancelled_at)) {
            $order->cancelled_at = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i:s');
        }

        // 訂單明細
        if (isset($order->order_details)) {
            // 商品總數
            $order->product_totals = count($order->order_details);

            // 取得第一筆訂單明細
            $order->order_details->transform(function ($order_detail) {
                // 單價 (單品折扣後的單價，即前台購物車呈現的單價)
                $order_detail->unit_price = number_format($order_detail->unit_price);

                // 小計
                $order_detail->subtotal = number_format($order_detail->subtotal);

                // 商品圖片
                if (isset($order_detail->product_photos)) {
                    $product_photo = $order_detail->product_photos->first();
                    $photo_url = config('filesystems.disks.s3.url') . $product_photo->photo_name;
                    $order_detail->photo_url = $photo_url;
                }

                return $order_detail->only([
                    'photo_url',
                    'product_name',
                    'spec_1_value',
                    'spec_2_value',
                    'qty',
                    'unit_price',
                    'subtotal',
                ]);
            });
        }

        // 出貨單
        if (isset($order->shipments)) {
            $shipment = $order->shipments->first();

            $order->package_no = $shipment->package_no;
        }

        // 退貨申請單
        if (isset($order->return_requests)) {
            $return_request = $order->return_requests->first();

            // 退貨申請時間
            if (isset($return_request->request_date)) {
                $return_request->request_date = Carbon::parse($return_request->request_date)->format('Y-m-d H:i:s');
            }

            $order->return_no = $return_request->request_no;
            $order->return_date = $return_request->request_date;
        }

        $order = $order->only([
            'ordered_date',
            'paid_at',
            'shipped_at',
            'delivered_at',
            'order_status',
            'cancelled_at',
            'order_no',
            'payment_method',
            'receiver_name',
            'receiver_mobile',
            'receiver_address',
            'return_no',
            'return_date',
            'order_details',
            'product_totals',
            'delivery_method',
            'package_no',
            'total_amount',
            'shipping_fee',
            'point_discount',
            'cart_campaign_discount',
            'points',
            'paid_amount',
            'invoice_type',
            'invoice_no',
            'invoice_date',
            'invoice_gui_number',
            'invoice_title',
        ]);

        return response()->json([
            'message' => '取得成功',
            'results' => $order,
        ], 200);
    }
}
