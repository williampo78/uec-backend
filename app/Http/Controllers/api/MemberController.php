<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\MemberGetOrdersRequest;
use App\Http\Requests\api\MemberResetPasswordRequest;
use App\Services\APIService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    private $api_service;

    public function __construct(APIService $api_service)
    {
        $this->api_service = $api_service;
    }

    public function resetPassword(MemberResetPasswordRequest $request)
    {
        $token = $request->bearerToken();
        $request_payloads = $request->input();

        if (empty($token)) {
            return response()->json([
                'message' => '無效的Token',
            ], 401);
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

    public function getOrders(MemberGetOrdersRequest $request)
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
        // 沒有訂單
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
            $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y/m/d H:i');

            // 訂單狀態
            $order->order_status = $order->order_status_desc;

            // 物流方式
            if (isset(config('uec.lgst_method_options')[$order->lgst_method])) {
                $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method];
            }

            // 物流公司
            if (isset(config('uec.lgst_company_code_options')[$order->lgst_company_code])) {
                $order->lgst_company_code = config('uec.lgst_company_code_options')[$order->lgst_company_code];
            }

            // 配送方式
            $order->delivery_method = "{$order->lgst_method}-{$order->lgst_company_code}";

            // 出貨時間
            if (isset($order->shipped_at)) {
                $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y/m/d H:i');
            }

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

                // 結帳金額
                $order->paid_amount = number_format($order->paid_amount);

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
}
