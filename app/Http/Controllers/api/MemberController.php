<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CancelOrderRequest;
use App\Http\Requests\Api\GetOrderDetailRequest;
use App\Http\Requests\Api\GetOrdersRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\ReturnOrderRequest;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestDetail;
use App\Models\Shipment;
use App\Models\StockTransactionLog;
use App\Models\WarehouseStock;
use App\Services\APIProductServices;
use App\Services\APIService;
use App\Services\OrderService;
use App\Services\ReturnRequestService;
use App\Services\SysConfigService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use App\Services\WarehouseStockService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MemberController extends Controller
{
    private $apiService;
    private $sysConfigService;
    private $orderService;
    private $warehouseService;
    private $warehouseStockService;
    private $returnRequestService;

    public function __construct(
        APIService $apiService,
        SysConfigService $sysConfigService,
        OrderService $orderService,
        WarehouseService $warehouseService,
        WarehouseStockService $warehouseStockService,
        ReturnRequestService $returnRequestService,
        APIProductServices $apiProductServices,
        UniversalService $universalService
    )
    {
        $this->apiService = $apiService;
        $this->sysConfigService = $sysConfigService;
        $this->orderService = $orderService;
        $this->warehouseService = $warehouseService;
        $this->warehouseStockService = $warehouseStockService;
        $this->returnRequestService = $returnRequestService;
        $this->apiProductServices = $apiProductServices;
        $this->universalService = $universalService;
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

        if (empty($token)) {
            return response()->json([
                'message' => 'token不存在',
            ], 404);
        }

        $results = $this->apiService->resetPassword($token, [
            'password' => $request->input('pwd'),
        ]);

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
        $date = $request->date;
        $twoYearsAgoDate = now()->subYears(2);
        $orderedDateStart = Carbon::parse($date)->subDays(90)->startOfDay();
        $orderedDateEnd = Carbon::parse($date)->endOfDay();

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
        if ($orderedDateEnd->lessThan($twoYearsAgoDate)) {
            return response()->json([
                'message' => '查詢成功',
                'results' => [
                    'order_totals' => 0,
                ],
            ], 200);
        }

        if ($orderedDateStart->lessThan($twoYearsAgoDate)) {
            $orderedDateStart = $twoYearsAgoDate;
        }

        // 取得訂單
        $orders = $this->orderService->getMemberOrders([
            'ordered_date_start' => $orderedDateStart,
            'ordered_date_end' => $orderedDateEnd,
        ]);

        // 訂單總數
        $orderTotals = $orders->count();
        // 沒有任何訂單
        if ($orderTotals < 1) {
            return response()->json([
                'message' => '查詢成功',
                'results' => [
                    'order_totals' => $orderTotals,
                ],
            ], 200);
        }

        $payload = [
            'message' => '查詢成功',
            'results' => [
                'order_totals' => $orderTotals,
                'orders' => null,
            ],
        ];

        // 訂單
        $orders->each(function ($order) use (&$payload) {
            //訂單狀態
            $order_status_desc = $this->universalService->getOrderStatus($order->status_code, $order->pay_status);
            $orderPayload = [
                'order_no' => $order->order_no,
                'ordered_date' => Carbon::parse($order->ordered_date)->format('Y-m-d H:i:s'),
                'order_status' => $order_status_desc,
                'photo_url' => null,
                'product_name' => null,
                'spec_1_value' => null,
                'spec_2_value' => null,
                'qty' => null,
                'unit_price' => null,
                'subtotal' => null,
                'product_totals' => $order->orderDetails->where('record_identity', 'M')->count(),
                'delivery_method' => null,
                'shipped_at' => null,
                'package_no' => null,
                'invoice_no' => $order->invoice_no,
                'paid_amount' => number_format($order->paid_amount),
            ];

            // 物流方式
            $lgstMethod = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

            // 物流公司
            $lgstCompanyCode = config('uec.lgst_company_code_options')[$order->lgst_company_code] ?? null;

            // 配送方式
            if (isset($lgstMethod)) {
                $orderPayload['delivery_method'] = "{$lgstMethod}";
            }

            // 出貨時間
            if (isset($order->shipped_at)) {
                $orderPayload['shipped_at'] = Carbon::parse($order->shipped_at)->format('Y-m-d H:i:s');
            }

            // 訂單明細
            if ($order->orderDetails->isNotEmpty()) {
                // 取得第一筆訂單明細
                $orderDetail = $order->orderDetails->first();

                // 商品名稱
                $orderPayload['product_name'] = $orderDetail->product->product_name;

                // 規格一設定值
                $orderPayload['spec_1_value'] = $orderDetail->productItem->spec_1_value;

                // 規格一設定值
                $orderPayload['spec_2_value'] = $orderDetail->productItem->spec_2_value;

                // 數量
                $orderPayload['qty'] = $orderDetail->qty;

                // 單價 (單品折扣後的單價，即前台購物車呈現的單價)
                $orderPayload['unit_price'] = number_format($orderDetail->unit_price);

                // 小計
                $orderPayload['subtotal'] = number_format($orderDetail->subtotal);


                //商品圖 以規格圖為優先，否則取商品封面圖
                $productPhoto = empty(optional($orderDetail->productItem)->photo_name) ? optional($orderDetail->product->productPhotos->first())->photo_name : optional($orderDetail->productItem)->photo_name;
                $orderPayload['photo_url'] = empty($productPhoto) ? null : config('filesystems.disks.s3.url') . $productPhoto;

                //售價
                $orderPayload['selling_price'] = number_format($orderDetail->selling_price);
                //折抵合計
                $orderPayload['total_discount'] = number_format($orderDetail->campaign_discount + $orderDetail->cart_p_discount);

            }

            // 出貨單
            if ($order->shipments->isNotEmpty()) {
                // 第一階段，一筆訂單只會有一筆出貨單
                $shipment = $order->shipments->first();

                // 託運單號
                $orderPayload['package_no'] = $shipment->package_no;
            }

            $payload['results']['orders'][] = $orderPayload;
        });

        return response()->json($payload, 200);
    }

    /**
     * 取得會員訂單詳細內容
     *
     * @param GetOrderDetailRequest $request
     * @return json
     */
    public function getOrderDetail(GetOrderDetailRequest $request)
    {
        // 取得訂單
        $order = $this->orderService->getMemberOrderDetailByOrderNo($request->order_no);

        // 沒有任何訂單
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 訂單成立後x分鐘內可取消
        $cancelLimitMins = (int)$this->sysConfigService->getConfigValue('CANCEL_LIMIT_MINS');

        //訂單狀態
        $order_status_desc = $this->universalService->getOrderStatus($order->status_code, $order->pay_status);

        $payload = [
            'message' => '取得成功',
            'results' => [
                'order_id' => $order->id,
                'ordered_date' => Carbon::parse($order->ordered_date)->format('Y-m-d H:i:s'),
                'paid_at' => null,
                'prepared_shipment_at' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'order_status' => $order_status_desc,
                'cancelled_at' => null,
                'order_no' => $order->order_no,
                'payment_method' => config('uec.payment_method_options')[$order->payment_method] ?? null,
                'receiver_name' => $order->receiver_name,
                'receiver_mobile' => $order->receiver_mobile,
                'receiver_zip_code' => $order->receiver_zip_code,
                'receiver_city' => $order->receiver_city,
                'receiver_district' => $order->receiver_district,
                'receiver_address' => $order->receiver_address,
                'return_no' => null,
                'return_date' => null,
                'order_details' => null,
                'product_totals' => 0,
                'delivery_method' => null,
                'package_no' => null,
                'total_amount' => $order->total_amount,
                'shipping_fee' => number_format($order->shipping_fee),
                'point_discount' => number_format($order->point_discount * -1),
                'cart_campaign_discount' => $order->cart_campaign_discount * -1,
                'points' => $order->points * -1,
                'paid_amount' => number_format($order->paid_amount),
                'fee_of_instal' => (int)number_format($order->fee_of_instal),
                'invoice_type' => config('uec.invoice_usage_options')[$order->invoice_usage] ?? null,
                'invoice_no' => $order->invoice_no,
                'invoice_date' => null,
                'invoice_gui_number' => $order->buyer_gui_number,
                'invoice_title' => $order->buyer_title,
                'buyer_remark' => $order->buyer_remark,
                'can_cancel_order' => $this->orderService->canCancelOrder($order->status_code, $order->ordered_date, $cancelLimitMins, $order->ship_from_whs),
                'can_return_order' => $this->orderService->canReturnOrderV2($order->status_code, $order->delivered_at, $order->cooling_off_due_date, $order->return_request_id),
            ],
        ];
        // 付款完成時間
        if (isset($order->paid_at)) {
            $payload['results']['paid_at'] = Carbon::parse($order->paid_at)->format('Y-m-d H:i:s');
        }

        // 出貨時間
        if (isset($order->shipped_at)) {
            $payload['results']['shipped_at'] = Carbon::parse($order->shipped_at)->format('Y-m-d H:i:s');
        }

        // 宅配配達時間/超商取件時間
        if (isset($order->delivered_at)) {
            $payload['results']['delivered_at'] = Carbon::parse($order->delivered_at)->format('Y-m-d H:i:s');
        }

        // 物流方式
        $lgstMethod = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

        // 物流公司
        $lgstCompanyCode = config('uec.lgst_company_code_options')[$order->lgst_company_code] ?? null;

        // 配送方式
        if (isset($lgstMethod)) {
            $payload['results']['delivery_method'] = "{$lgstMethod}";
        }

        // 發票開立時間
        if (isset($order->invoice_date)) {
            $payload['results']['invoice_date'] = Carbon::parse($order->invoice_date)->format('Y-m-d H:i:s');
        }

        // 訂單取消時間
        if (isset($order->cancelled_at)) {
            $payload['results']['cancelled_at'] = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i:s');
        }
        $giveaway_qty = [];

        $products = $this->apiProductServices->getProducts();
        $gtm = $this->apiProductServices->getProductItemForGTM($products, 'item');
        // 貨態進度
        $shippedStatus = $this->orderService->getShippedStatus($order);
        $order->orderDetails->each(function ($orderDetail) use (&$payload, &$giveaway_qty, &$gtm, &$shippedStatus) {
            if ($orderDetail->record_identity == 'M') {
                $orderDetailPayload = [
                    'id' => $orderDetail->id,
                    'photo_url' => null,
                    'product_name' => $orderDetail->product->product_name,
                    'spec_1_value' => $orderDetail->productItem->spec_1_value,
                    'spec_2_value' => $orderDetail->productItem->spec_2_value,
                    'qty' => $orderDetail->qty,
                    'unit_price' => number_format($orderDetail->unit_price),
                    'subtotal' => number_format($orderDetail->subtotal),
                    'product_id' => $orderDetail->product_id,
                    'product_item_id' => $orderDetail->product_item_id,
                    'product_no' => $orderDetail->product->product_no,
                    'can_buy' => $orderDetail->record_identity == 'M' ? true : false,
                    'selling_price' => number_format($orderDetail->selling_price),
                    'total_discount' => number_format($orderDetail->campaign_discount + $orderDetail->cart_p_discount),
                    'discount_content' => [],
                    'gtm' => isset($gtm[$orderDetail->product_id][$orderDetail->product_item_id]) ? $gtm[$orderDetail->product_id][$orderDetail->product_item_id] : "",
                    'shipped_info' => isset($shippedStatus['shipped_info'][$orderDetail->id][$orderDetail->product_item_id]) ? $shippedStatus['shipped_info'][$orderDetail->id][$orderDetail->product_item_id] : "",
                    'shipped_status' => isset($shippedStatus['shipped_status'][$orderDetail->id][$orderDetail->product_item_id]) ? $shippedStatus['shipped_status'][$orderDetail->id][$orderDetail->product_item_id] : "",
                ];
                //商品圖 以規格圖為優先，否則取商品封面圖
                $productPhoto = empty(optional($orderDetail->productItem)->photo_name) ? optional($orderDetail->product->productPhotos->first())->photo_name : optional($orderDetail->productItem)->photo_name;
                $orderDetailPayload['photo_url'] = empty($productPhoto) ? null : config('filesystems.disks.s3.url') . $productPhoto;

                $payload['results']['order_details'][] = $orderDetailPayload;
                $payload['results']['product_totals'] += 1;
            } else {
                //order_details 非商品的數量
                $giveaway_qty[$orderDetail->id] = $orderDetail->qty;
            }
        });
        $payload = $this->orderService->addDiscountsToOrder($payload, $giveaway_qty, $shippedStatus);

        // 出貨單
        if ($order->shipments->isNotEmpty()) {
            // 第一階段，一筆訂單只會有一筆出貨單
            $shipment = $order->shipments->first();

            // 託運單號
            $payload['results']['package_no'] = $shipment->package_no;

            // 待出貨時間
            if (isset($shipment->edi_exported_at)) {
                $payload['results']['prepared_shipment_at'] = Carbon::parse($shipment->edi_exported_at)->format('Y-m-d H:i:s');
            }
        }

        // 退貨申請單
        if ($order->returnRequests->isNotEmpty()) {
            $returnRequest = $order->returnRequests->first();

            // 退貨申請單號
            $payload['results']['return_no'] = $returnRequest->request_no;

            // 退貨申請時間
            if (isset($returnRequest->request_date)) {
                $payload['results']['return_date'] = Carbon::parse($returnRequest->request_date)->format('Y-m-d H:i:s');
            }
        }

        //金流相關數字
        if ($order->revision_no > 0 && $order->refund_status != 'COMPLETED') { //還沒完成退款，須抓前一版本的order資訊呈現
            //最新版訂單
            $preOrder = $this->orderService->getMemberPreRevisionByOrderNo($request->order_no, $order->revision_no);
            $payload['results']['total_amount'] = $preOrder->total_amount;
            $payload['results']['shipping_fee'] = number_format($preOrder->shipping_fee);
            $payload['results']['point_discount'] = number_format($preOrder->point_discount * -1);
            $payload['results']['cart_campaign_discount'] = $preOrder->cart_campaign_discount == 0 ? 0 : ($preOrder->cart_campaign_discount * -1);
            $payload['results']['points'] = $preOrder->points * -1;
            $payload['results']['paid_amount'] = number_format($preOrder->paid_amount);
        }

        return response()->json($payload, 200);
    }

    /**
     * 取消訂單
     *
     * @param CancelOrderRequest $request
     * @return json
     */
    public function cancelOrder(CancelOrderRequest $request)
    {
        // 取得訂單
        $order = $this->orderService->getMemberOrderDetailByOrderNo($request->order_no);

        // 訂單不存在
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 訂單成立後x分鐘內可取消
        $cancelLimitMins = (int)$this->sysConfigService->getConfigValue('CANCEL_LIMIT_MINS');

        // 是否可以取消訂單
        if (!$this->orderService->canCancelOrder($order->status_code, $order->ordered_date, $cancelLimitMins, $order->ship_from_whs)) {
            return response()->json([
                'message' => '訂單已進入處理階段/已超過限制時間，不可取消訂單',
            ], 423);
        }

        // 商城良品倉
        $ecWarehouseGoods = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');

        // 取得倉庫
        $warehouse = $this->warehouseService->getWarehouseByNumber($ecWarehouseGoods);

        // 倉庫不存在
        if (!isset($warehouse)) {
            return response()->json([
                'message' => '倉庫不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // (-)退款
            $amount = $order->paid_amount == 0 ? $order->paid_amount : $order->paid_amount * -1;
            // (+)歸還點數折抵金額
            $pointDiscount = $order->point_discount == 0 ? $order->point_discount : $order->point_discount * -1;
            // (+)歸還點數
            $points = $order->points == 0 ? $order->points : $order->points * -1;

            // 已付款
            if ($order->is_paid == 1) {
                // 更新訂單
                Order::findOrFail($order->id)
                    ->update([
                        'status_code' => 'CANCELLED',
                        'refund_status' => 'PENDING',
                        'cancel_req_reason_code' => $request->code,
                        'cancel_req_remark' => $request->remark,
                        'cancelled_at' => now(),
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
                    'amount' => $amount,
                    'point_discount' => $pointDiscount,
                    'points' => $points,
                    'record_created_reason' => 'ORDER_CANCELLED',
                    'created_by' => -1,
                    'updated_by' => -1,
                ]);
            } // 未付款
            else {
                // 更新訂單
                Order::findOrFail($order->id)
                    ->update([
                        'status_code' => 'CANCELLED',
                        'pay_status' => 'VOIDED',
                        'refund_status' => 'NA',
                        'cancel_req_reason_code' => $request->code,
                        'cancel_req_remark' => $request->remark,
                        'cancelled_at' => now(),
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
                        'point_discount' => $pointDiscount,
                        'points' => $points,
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
                    'cancelled_at' => now(),
                    'updated_by' => -1,
                ]);

            // 訂單明細
            if ($order->orderDetails->isNotEmpty()) {
                $order->orderDetails->each(function ($orderDetail) use ($order, $warehouse) {
                    // 新增庫存異動紀錄
                    StockTransactionLog::create([
                        'transaction_type' => 'ORDER_CANCEL',
                        'transaction_date' => now(),
                        'warehouse_id' => $warehouse->id,
                        'product_item_id' => $orderDetail->product_item_id,
                        'item_no' => $orderDetail->item_no,
                        'transaction_qty' => $orderDetail->qty,
                        'source_doc_no' => $order->order_no,
                        'source_table_name' => 'order_details',
                        'source_table_id' => $orderDetail->id,
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);

                    // 取得倉庫庫存
                    $warehouseStock = $this->warehouseStockService->getWarehouseStock($warehouse->id, $orderDetail->product_item_id);

                    // 新增庫存
                    if (!isset($warehouseStock)) {
                        WarehouseStock::create([
                            'warehouse_id' => $warehouse->id,
                            'product_item_id' => $orderDetail->product_item_id,
                            'stock_qty' => $orderDetail->qty,
                            'created_by' => -1,
                            'updated_by' => -1,
                        ]);
                    } // 更新庫存
                    else {
                        WarehouseStock::findOrFail($warehouseStock->id)
                            ->update([
                                'stock_qty' => $warehouseStock->stock_qty + $orderDetail->qty,
                            ]);
                    }
                });
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
                'cancelled_at' => now()->format('Y-m-d H:i:s'),
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
        // 取得訂單
        $order = $this->orderService->getMemberOrderDetailByOrderNo($request->order_no);

        // 訂單不存在
        if (!isset($order)) {
            return response()->json([
                'message' => '訂單不存在',
            ], 404);
        }

        // 是否可以申請退貨
        $canReturn = $this->orderService->canReturnOrderV2($order->status_code, $order->delivered_at, $order->cooling_off_due_date, $order->return_request_id);
        if (!$canReturn['status']) {
            return response()->json([
                'message' => '商品尚未配達/已超過鑑賞期/退貨處理中，不可申請退貨',
            ], 423);
        }

        DB::beginTransaction();

        try {
            $requestNo = $this->returnRequestService->generateRequestNo();

            // 新增退貨申請單
            $returnRequest = ReturnRequest::create([
                'agent_id' => 1,
                'request_no' => $requestNo,
                'request_date' => now(),
                'member_id' => auth('api')->user()->member_id,
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'status_code' => 'CREATED',
                'refund_method' => $order->payment_method,
                'lgst_method' => $order->lgst_method,
                'req_name' => $request->name,
                'req_mobile' => $request->mobile,
                'req_city' => $request->city,
                'req_district' => $request->district,
                'req_address' => $request->address,
                'req_zip_code' => $request->zip_code,
                'req_telephone' => $request->telephone,
                'req_telephone_ext' => $request->telephone_ext,
                'req_reason_code' => $request->code,
                'req_remark' => $request->remark,
                'created_by' => -1,
                'updated_by' => -1,
            ]);

            // 訂單明細
            if ($order->orderDetails->isNotEmpty()) {
                $order->orderDetails->each(function ($orderDetail) use ($returnRequest) {
                    // 新增退貨申請單明細
                    ReturnRequestDetail::create([
                        'return_request_id' => $returnRequest->id,
                        'seq' => $orderDetail->seq,
                        'order_detail_id' => $orderDetail->id,
                        'product_item_id' => $orderDetail->product_item_id,
                        'item_no' => $orderDetail->item_no,
                        'request_qty' => $orderDetail->qty,
                        'passed_qty' => 0,
                        'failed_qty' => 0,
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);
                });
            }

            // 更新訂單
            Order::findOrFail($order->id)
                ->update([
                    'return_request_id' => $returnRequest->id,
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
                'return_no' => $requestNo,
                'return_date' => now()->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
