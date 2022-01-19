<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Str;
use App\Models\OrderPayment;
use App\Services\APIService;
use App\Models\ReturnRequest;
use App\Models\WarehouseStock;
use App\Services\OrderService;
use App\Services\SysConfigService;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use App\Models\ReturnRequestDetail;
use App\Models\StockTransactionLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LookupValuesVService;
use App\Services\ReturnRequestService;
use App\Services\WarehouseStockService;
use App\Http\Requests\api\GetOrdersRequest;
use App\Http\Requests\api\CancelOrderRequest;
use App\Http\Requests\api\ReturnOrderRequest;
use App\Http\Requests\api\ResetPasswordRequest;
use App\Http\Requests\api\GetOrderDetailRequest;

class MemberController extends Controller
{
    private $api_service;
    private $sys_config_service;
    private $lookup_values_v_service;

    public function __construct(
        APIService $api_service,
        SysConfigService $sys_config_service,
        LookupValuesVService $lookup_values_v_service
    ) {
        $this->api_service = $api_service;
        $this->sys_config_service = $sys_config_service;
        $this->lookup_values_v_service = $lookup_values_v_service;
    }

    /**
     * 重設會員密碼
     *
     * @param ResetPasswordRequest $request
     * @return json
     */
    public function resetPassword(ResetPasswordRequest $request)
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

    /**
     * 取得會員訂單
     *
     * @param GetOrdersRequest $request
     * @return json
     */
    public function getOrders(GetOrdersRequest $request)
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

    /**
     * 取得會員訂單詳細內容
     *
     * @param GetOrderDetailRequest $request
     * @return json
     */
    public function getOrderDetail(GetOrderDetailRequest $request)
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

        // 系統設定檔
        $sys_configs = $this->sys_config_service->getSysConfigs();

        // 訂單成立後x分鐘內可取消
        $cancel_limit_mins = (int) $sys_configs->first(function ($config) {
            return $config->config_key == 'CANCEL_LIMIT_MINS';
        })->config_value;

        // 是否可以取消訂單
        $order->can_cancel_order = $order_service->canCancelOrder($order->status_code, $order->ordered_date, $cancel_limit_mins);

        // 是否可以申請退貨
        $order->can_return_order = $order_service->canReturnOrder($order->status_code, $order->delivered_at, $order->cooling_off_due_date, $order->return_request_id);

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
            $order->receiver_name = (string) Str::of($order->receiver_name)->myMask('*', 0, 1)->myMask('*', -1, 1);
        }

        // 收件者手機
        if (isset($order->receiver_mobile)) {
            $order->receiver_mobile = (string) Str::of($order->receiver_mobile)->myMask('*', -3);
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
                    'product_id',
                    'product_no',
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
            'can_cancel_order',
            'can_return_order',
        ]);

        return response()->json([
            'message' => '取得成功',
            'results' => $order,
        ], 200);
    }

    /**
     * 取消訂單
     *
     * @param CancelOrderRequest $request
     * @return json
     */
    public function cancelOrder(CancelOrderRequest $request)
    {
        try {
            $member_id = auth('api')->userOrFail()->member_id;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'message' => '會員不存在',
            ], 404);
        }

        $order_service = new OrderService;
        $warehouse_service = new WarehouseService;
        $warehouse_stock_service = new WarehouseStockService;
        $order_no = $request->route('order_no');
        $code = $request->input('code');
        $remark = $request->input('remark');
        $now = Carbon::now();

        // 取得訂單
        $order = $order_service->getOrders([
            'revision_no' => 0,
            'order_no' => $order_no,
            'member_id' => $member_id,
        ])->first();

        // 訂單不存在
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 系統設定檔
        $sys_configs = $this->sys_config_service->getSysConfigs();

        // 訂單成立後x分鐘內可取消
        $cancel_limit_mins = (int) $sys_configs->first(function ($config) {
            return $config->config_key == 'CANCEL_LIMIT_MINS';
        })->config_value;

        // 是否可以取消訂單
        if (!$order_service->canCancelOrder($order->status_code, $order->ordered_date, $cancel_limit_mins)) {
            return response()->json([
                'message' => '訂單已進入處理階段/已超過限制時間，不可取消訂單',
            ], 423);
        }

        // 取得訂單取消原因
        $cancel_req_reason = $this->lookup_values_v_service->getLookupValuesVs([
            'disable_agent_id_auth' => true,
            'type_code' => 'CANCEL_REQ_REASON',
            'code' => $code,
        ])->first();

        // 取消原因代碼不存在
        if (!isset($cancel_req_reason)) {
            return response()->json([
                'message' => '取消原因代碼不存在',
            ], 404);
        }

        // 商城良品倉
        $ec_warehouse_goods = $sys_configs->first(function ($config) {
            return $config->config_key == 'EC_WAREHOUSE_GOODS';
        })->config_value;

        // 取得倉庫
        $warehouse = $warehouse_service->getWarehouses([
            'number' => $ec_warehouse_goods,
        ])->first();

        DB::beginTransaction();

        try {
            // 已付款
            if ($order->is_paid == 1) {
                // 更新訂單
                Order::findOrFail($order->id)
                    ->update([
                        'status_code' => 'CANCELLED',
                        'refund_status' => 'PENDING',
                        'cancel_req_reason_code' => $code,
                        'cancel_req_remark' => $remark,
                        'cancelled_at' => $now,
                        'updated_by' => -1,
                    ]);

                // 新增待退款金流單
                OrderPayment::create([
                    'source_table_name' => 'orders',
                    'source_table_id' => $order->id,
                    'order_no' => $order->order_no,
                    'payment_type' => 'REFUND',
                    'payment_method' => $order->payment_method,
                    'payment_status' => 'PENDING',
                    'amount' => $order->paid_amount * -1,
                    'point_discount' => $order->point_discount * -1,
                    'points' => $order->points * -1,
                    'record_created_reason' => 'ORDER_CANCELLED',
                    'created_by' => -1,
                    'updated_by' => -1,
                ]);

            }
            // 未付款
            else {
                // 更新訂單
                Order::findOrFail($order->id)
                    ->update([
                        'status_code' => 'CANCELLED',
                        'pay_status' => 'VOIDED',
                        'refund_status' => 'NA',
                        'cancel_req_reason_code' => $code,
                        'cancel_req_remark' => $remark,
                        'cancelled_at' => $now,
                        'updated_by' => -1,
                    ]);

                // 作廢金流單
                OrderPayment::where('source_table_name', 'orders')
                    ->where('source_table_id', $order->id)
                    ->where('payment_type', 'PAY')
                    ->update([
                        'payment_status' => 'VOIDED',
                        'updated_by' => -1,
                    ]);

                // 有使用會員點數
                if ($order->points < 0) {
                    // 新增待退款金流單
                    OrderPayment::create([
                        'source_table_name' => 'orders',
                        'source_table_id' => $order->id,
                        'order_no' => $order->order_no,
                        'payment_type' => 'REFUND',
                        'payment_method' => $order->payment_method,
                        'payment_status' => 'NA',
                        'amount' => 0,
                        'point_discount' => $order->point_discount * -1,
                        'points' => $order->points * -1,
                        'record_created_reason' => 'ORDER_CANCELLED',
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);
                }
            }

            // 更新出貨單
            Shipment::where('order_id', $order->id)
                ->update([
                    'status_code' => 'CANCELLED',
                    'cancelled_at' => $now,
                    'updated_by' => -1,
                ]);

            // 訂單明細
            if (isset($order->order_details)) {
                foreach ($order->order_details as $order_detail) {
                    // 新增庫存異動紀錄
                    StockTransactionLog::create([
                        'transaction_type' => 'ORDER_CANCEL',
                        'transaction_date' => $now,
                        'warehouse_id' => $warehouse->id,
                        'product_item_id' => $order_detail->product_item_id,
                        'item_no' => $order_detail->item_no,
                        'transaction_qty' => $order_detail->qty,
                        'source_doc_no' => $order->order_no,
                        'source_table_name' => 'order_details',
                        'source_table_id' => $order_detail->id,
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);

                    // 取得倉庫庫存
                    $warehouse_stock = $warehouse_stock_service->getWarehouseStocks([
                        'warehouse_id' => $warehouse->id,
                        'product_item_id' => $order_detail->product_item_id,
                    ])->first();

                    // 新增庫存
                    if (!isset($warehouse_stock)) {
                        WarehouseStock::create([
                            'warehouse_id' => $warehouse->id,
                            'product_item_id' => $order_detail->product_item_id,
                            'stock_qty' => $order_detail->qty,
                            'created_by' => -1,
                            'updated_by' => -1,
                        ]);
                    }
                    // 更新庫存
                    else {
                        WarehouseStock::findOrFail($warehouse_stock->id)
                            ->update([
                                'stock_qty' => $warehouse_stock->stock_qty + $order_detail->qty,
                            ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'message' => '其他錯誤',
            ], 500);
        }

        return response()->json([
            'message' => '訂單取消成功',
            'results' => [
                'cancelled_at' => $now->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    /**
     * 申請退貨
     *
     * @param ReturnOrderRequest $request
     * @return json
     */
    public function returnOrder(ReturnOrderRequest $request)
    {
        try {
            $member_id = auth('api')->userOrFail()->member_id;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'message' => '會員不存在',
            ], 404);
        }

        $order_service = new OrderService;
        $return_request_service = new ReturnRequestService;
        $order_no = $request->route('order_no');
        $code = $request->input('code');
        $remark = $request->input('remark');
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $telephone = $request->input('telephone');
        $telephone_ext = $request->input('telephone_ext');
        $city = $request->input('city');
        $district = $request->input('district');
        $address = $request->input('address');
        $zip_code = $request->input('zip_code');
        $now = Carbon::now();

        // 取得訂單
        $order = $order_service->getOrders([
            'revision_no' => 0,
            'order_no' => $order_no,
            'member_id' => $member_id,
        ])->first();

        // 訂單不存在
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 是否可以申請退貨
        if (!$order_service->canReturnOrder($order->status_code, $order->delivered_at, $order->cooling_off_due_date, $order->return_request_id)) {
            return response()->json([
                'message' => '商品尚未配達/已超過鑑賞期/退貨處理中，不可申請退貨',
            ], 423);
        }

        // 取得訂單退貨原因
        $return_req_reason = $this->lookup_values_v_service->getLookupValuesVs([
            'disable_agent_id_auth' => true,
            'type_code' => 'RETURN_REQ_REASON',
            'code' => $code,
        ])->first();

        // 退貨原因代碼不存在
        if (!isset($return_req_reason)) {
            return response()->json([
                'message' => '退貨原因代碼不存在',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $request_no = $return_request_service->generateRequestNo();

            // 新增退貨申請單
            $return_request = ReturnRequest::create([
                'agent_id' => 1,
                'request_no' => $request_no,
                'request_date' => $now,
                'member_id' => $member_id,
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'status_code' => 'CREATED',
                'refund_method' => $order->payment_method,
                'lgst_method' => $order->lgst_method,
                'req_name' => $name,
                'req_mobile' => $mobile,
                'req_city' => $city,
                'req_district' => $district,
                'req_address' => $address,
                'req_zip_code' => $zip_code,
                'req_telephone' => $telephone,
                'req_telephone_ext' => $telephone_ext,
                'req_reason_code' => $code,
                'req_remark' => $remark,
                'created_by' => -1,
                'updated_by' => -1,
            ]);

            // 訂單明細
            if (isset($order->order_details)) {
                foreach ($order->order_details as $order_detail) {
                    // 新增退貨申請單明細
                    ReturnRequestDetail::create([
                        'return_request_id' => $return_request->id,
                        'seq' => $order_detail->seq,
                        'order_detail_id' => $order_detail->id,
                        'product_item_id' => $order_detail->product_item_id,
                        'item_no' => $order_detail->item_no,
                        'request_qty' => $order_detail->qty,
                        'passed_qty' => 0,
                        'failed_qty' => 0,
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);
                }
            }

            // 更新訂單
            Order::findOrFail($order->id)
                ->update([
                    'return_request_id' => $return_request->id,
                    'updated_by' => -1,
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'message' => '其他錯誤',
            ], 500);
        }

        return response()->json([
            'message' => '訂單退貨成功',
            'results' => [
                'return_no' => $request_no,
                'return_date' => $now->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
