<?php

namespace App\Http\Controllers;

use App\Exports\CartPDiscountSplitOrderExport;
use App\Exports\OrderExport;
use App\Services\MoneyAmount;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(
        OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payload = $request->only([
            'ordered_date_start',
            'ordered_date_end',
            'order_no',
            'member_account',
            'order_status_code',
            'pay_status',
            'shipment_status_code',
            'product_no',
            'product_name',
            'campaign_name',
            'order_ship_from_whs',
            'data_range',
        ]);

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        if (!$request->share_role_auth['auth_query'] || count($payload) < 1) {
            return view('backend.order.list');
        }

        $orders = $this->orderService->getTableList($payload);

        // 整理給前端的資料
        $orders = $orders->map(function ($order) {
            // 訂單時間
            $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i');

            // 訂單狀態
            $order->status_code = config('uec.order_status_code_options')[$order->status_code] ?? null;

            // 付款方式
            $order->payment_method = config('uec.payment_method_options')[$order->payment_method] ?? null;

            // 物流方式
            $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

            // 出貨單明細
            if ($order->shipments->isNotEmpty()) {
                // 第一階段，一筆訂單只會有一筆出貨單
                $order->shipments = $order->shipments->take(1)->map(function ($shipment) {
                    // 出貨單狀態
                    $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;

                    return $shipment->only([
                        'status_code',
                    ]);
                });
            }

            // 訂單類型
            $order->ship_from_whs = config('uec.order_ship_from_whs_options')[$order->ship_from_whs] ?? null;

            return $order->only([
                'id',
                'ordered_date',
                'order_no',
                'status_code',
                'payment_method',
                'lgst_method',
                'paid_amount',
                'member_account',
                'buyer_name',
                'shipments',
                'ship_from_whs',
            ]);
        });

        return view('backend.order.list', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->orderService->getTableDetailById($id);

        $payload = [
            'id' => $order->id,
            'order_no' => $order->order_no,
            'ordered_date' => Carbon::parse($order->ordered_date)->format('Y-m-d H:i'),
            'status_code' => config('uec.order_status_code_options')[$order->status_code] ?? null,
            'payment_method' => config('uec.payment_method_options')[$order->payment_method] ?? null,
            'pay_status' => config('uec.order_pay_status_options')[$order->pay_status] ?? null,
            'shipping_free_threshold' => number_format($order->shipping_free_threshold),
            'member_account' => $order->member_account,
            'buyer_name' => $order->buyer_name,
            'buyer_email' => $order->buyer_email,
            'receiver_name' => $order->receiver_name,
            'receiver_mobile' => $order->receiver_mobile,
            'receiver_address' => null,
            'lgst_method' => config('uec.lgst_method_options')[$order->lgst_method] ?? null,
            'total_amount' => number_format($order->total_amount + $order->cart_p_discount),
            'cart_campaign_discount' => number_format($order->cart_campaign_discount),
            'cart_p_discount' => number_format($order->cart_p_discount),
            'point_discount' => number_format($order->point_discount),
            'shipping_fee' => number_format($order->shipping_fee),
            'paid_amount' => number_format($order->paid_amount),
            'buyer_remark' => $order->buyer_remark,
            'shipment' => null,
            'order_details' => null,
            'invoice_usage' => config('uec.invoice_usage_options')[$order->invoice_usage] ?? null,
            'carrier_type' => config('uec.carrier_type_options')[$order->carrier_type] ?? null,
            'carrier_no' => $order->carrier_no,
            'buyer_gui_number' => $order->buyer_gui_number,
            'buyer_title' => $order->buyer_title,
            'donated_institution_name' => null,
            'invoices' => null,
            'order_payments' => null,
            'order_campaign_discounts' => null,
            'cancelled_voided_at' => null,
            'cancel_req_reason_code' => null,
            'cancel_req_remark' => null,
            'shipped_at' => null,
            'arrived_store_at' => null,
            'home_dilivered_at' => null,
            'cvs_completed_at' => null,
            'is_return' => 0,
            'return_status_code' => null,
            'is_negotiated' => 0,
            'return_order_details' => null,
        ];

        // 收件地址
        $address = '';
        $address .= $order->receiver_city ?? '';
        $address .= $order->receiver_district ?? '';
        $address .= $order->receiver_address ?? '';
        $payload['receiver_address'] = $address;

        // 發票捐贈機構
        if (isset($order->donatedInstitution)
            && $order->invoice_usage == 'D'
        ) {
            $payload['donated_institution_name'] = "{$order->donated_institution}-{$order->donatedInstitution->description}";
        }

        // 取消 / 作廢時間
        if (isset($order->cancelled_at)) {
            $payload['cancelled_voided_at'] = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i');
        } elseif (isset($order->voided_at)) {
            $payload['cancelled_voided_at'] = Carbon::parse($order->voided_at)->format('Y-m-d H:i');
        }

        // 取消原因
        if (isset($order->cancel_req_reason_code)) {
            $payload['cancel_req_reason_code'] = $order->cancel_req_reason_code ?? '';
        }

        // 取消備註
        if (isset($order->cancel_req_remark)) {
            $payload['cancel_req_remark'] = $order->cancel_req_remark ?? '';
        }

        // 出貨時間
        if (isset($order->shipped_at)) {
            $payload['shipped_at'] = Carbon::parse($order->shipped_at)->format('Y-m-d H:i');
        }

        // 到店時間
        if (isset($order->arrived_store_at)) {
            $payload['arrived_store_at'] = Carbon::parse($order->arrived_store_at)->format('Y-m-d H:i');
        }

        // (宅配)配達時間
        if ($order->lgst_method == 'HOME' && isset($order->delivered_at)) {
            $payload['home_dilivered_at'] = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
        }

        // (超取)取件時間
        if ($order->lgst_method != 'HOME' && isset($order->delivered_at)) {
            $payload['cvs_completed_at'] = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
        }

        if ($order->shipments->isNotEmpty()) {
            // 第一階段，一筆訂單只會有一筆出貨單
            $shipment = $order->shipments->first();

            // 出貨單狀態
            $payload['shipment']['status_code'] = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;
        }

        // 訂單明細
        if ($order->orderDetails->isNotEmpty()) {
            $order->orderDetails->each(function ($orderDetail) use (&$payload) {
                $orderDetails = [
                    'seq' => $orderDetail->seq,
                    'item_no' => $orderDetail->item_no,
                    'product_name' => $orderDetail->product->product_name,
                    'spec_1_value' => $orderDetail->productItem->spec_1_value,
                    'spec_2_value' => $orderDetail->productItem->spec_2_value,
                    'selling_price' => null,
                    'unit_price' => null,
                    'qty' => $orderDetail->qty,
                    'campaign_discount' => null,
                    'subtotal' => null,
                    'cart_p_discount' => null,
                    'point_discount' => null,
                    'supplier_item_no' => $orderDetail->productItem->supplier_item_no ?? '',
                    'supplier_product_no' => $orderDetail->product->supplier_product_no ?? '',
                    'record_identity' => '',
                    'package_no' => null,
                    'supplier_name' => '',
                    'product_type' => config('uec.product_type_options')[$orderDetail->product->product_type] ?? '',
                    'returned_qty' => $orderDetail->returned_qty,
                    'returned_campaign_discount' => null,
                    'returned_subtotal' => null,
                    'returned_cart_p_discount' => null,
                    'returned_point_discount' => null,
                    'shipment_no' => '',
                    'status_code' => '',
                    'shipped_at' => '',
                    'delivered_at' => '',
                ];

                // 單位售價 (商品主檔維護的售價)
                $orderDetails['selling_price'] = number_format($orderDetail->selling_price);

                // 單價 (單品折扣後的單價，即前台購物車呈現的單價)
                $orderDetails['unit_price'] = number_format($orderDetail->unit_price);

                // 活動折扣金額
                $orderDetails['campaign_discount'] = number_format($orderDetail->campaign_discount);

                // 小計
                $orderDetails['subtotal'] = number_format($orderDetail->subtotal);

                // 購物車滿額折抵金額
                $orderDetails['cart_p_discount'] = number_format($orderDetail->cart_p_discount);

                // 會員點數扣抵金額
                $orderDetails['point_discount'] = number_format($orderDetail->point_discount);

                // 訂單明細身分
                $orderDetails['record_identity'] = config('uec.order_record_identity_options')[$orderDetail->record_identity] ?? null;

                // 託運單號
                if (isset($orderDetail->shipmentDetail)) {
                    $orderDetails['package_no'] = $orderDetail->shipmentDetail->shipment->package_no;
                }

                // 供應商名稱
                if (isset($orderDetail->product->supplier)) {
                    $orderDetails['supplier_name'] = $orderDetail->product->supplier->name ?? '';
                }

                // 累計已銷退的活動折扣金額
                $orderDetails['returned_campaign_discount'] = number_format($orderDetail->returned_campaign_discount);

                // 累計已銷退的小計
                $orderDetails['returned_subtotal'] = number_format($orderDetail->returned_subtotal);

                // 累計已銷退的購物車活動折扣金額
                $orderDetails['returned_cart_p_discount'] = number_format($orderDetail->returned_cart_p_discount);

                // 累計已銷退的會員點數扣抵金額
                $orderDetails['returned_point_discount'] = number_format($orderDetail->returned_point_discount);

                $shipmentData = $this->orderService->getShipmentByOrderNoAndSeq($payload['order_no'], $orderDetail->seq);


                if (!empty($shipmentData)) {
                    // 出貨單號
                    $orderDetails['shipment_no'] = $shipmentData->shipment_no;
                    // 出貨單狀態
                    $orderDetails['status_code'] = config('uec.shipment_status_code_options')[$shipmentData->status_code];
                    // 出貨時間
                    $orderDetails['shipped_at'] = $shipmentData->shipped_at ?? '';
                    // (宅配)配達時間
                    $orderDetails['delivered_at'] = $shipmentData->delivered_at ?? '';
                }

                $payload['order_details'][] = $orderDetails;
            });
        }

        // 發票資訊
        if ($order->combineInvoices->isNotEmpty()) {
            $order->combineInvoices->each(function ($combineInvoice) use (&$payload) {
                $invoices = [
                    'transaction_date' => null,
                    'type' => null,
                    'invoice_no' => $combineInvoice->invoice_no,
                    'tax_type' => null,
                    'total_tax' => 0,
                    'amount' => null,
                    'remark' => null,
                    'random_no' => null,
                    'order_no' => $combineInvoice->order_no,
                    'invoice_details' => null,
                    'type_en' => null,
                    'allowance_no' => null,
                    'allowance_date' => null,
                    'allowance_amount' => null,
                ];

                $tableName = $combineInvoice->getTable();
                $invoices['type_en'] = $tableName;

                if ($tableName == 'invoices') {
                    // 交易時間
                    if (isset($combineInvoice->invoice_date)) {
                        $invoices['transaction_date'] = Carbon::parse($combineInvoice->invoice_date)->format('Y-m-d');
                    }

                    // 類型
                    $invoices['type'] = '發票開立';

                    // 課稅別
                    $invoices['tax_type'] = config('uec.tax_type_options')[$combineInvoice->tax_type] ?? null;

                    // 有統編，總稅額需要從總金額計算出來
                    if (isset($combineInvoice->cust_gui_number)) {
                        $moneyAmount = MoneyAmount::makeByPrice($combineInvoice->total_amount, $combineInvoice->tax_type)->calculate('local', true);
                        $invoices['total_tax'] = $moneyAmount->getTaxPrice();
                    }

                    // 金額
                    $invoices['amount'] = number_format($combineInvoice->total_amount);

                    // 備註
                    $invoices['remark'] = '發票拋轉狀態 : ' . $combineInvoice->rtn_code . '-' . $combineInvoice->rtn_msg;

                    // 隨機碼
                    $invoices['random_no'] = $combineInvoice->random_no;

                    // 發票明細
                    if ($combineInvoice->invoiceDetails->isNotEmpty()) {
                        $combineInvoice->invoiceDetails->each(function ($invoiceDetail) use (&$invoices) {
                            $invoiceDetails = [
                                'seq' => $invoiceDetail->seq,
                                'item_name' => $invoiceDetail->item_name,
                                'unit_price' => $invoiceDetail->unit_price,
                                'qty' => $invoiceDetail->qty,
                                'amount' => number_format($invoiceDetail->amount),
                            ];

                            $invoices['invoice_details'][] = $invoiceDetails;
                        });
                    }
                } else {
                    // 交易時間
                    if (isset($combineInvoice->allowance_date)) {
                        $invoices['transaction_date'] = Carbon::parse($combineInvoice->allowance_date)->format('Y-m-d');
                    }

                    // 類型
                    $invoices['type'] = '發票折讓';

                    // 折讓單號
                    $invoices['allowance_no'] = $combineInvoice->allowance_no ?? '';

                    // 折讓日期
                    $invoices['allowance_date'] = $combineInvoice->allowance_date ?? '';

                    // 折讓總金額
                    $invoices['allowance_amount'] = $combineInvoice->allowance_amount ?? '';

                    // 課稅別
                    $invoices['tax_type'] = config('uec.tax_type_options')[$combineInvoice->invoice->tax_type] ?? null;

                    // 有統編，總稅額需要從總金額計算出來
                    if (isset($combineInvoice->invoice->cust_gui_number)) {
                        $moneyAmount = MoneyAmount::makeByPrice($combineInvoice->allowance_amount, $combineInvoice->invoice->tax_type)->calculate('local', true);
                        $invoices['total_tax'] = $moneyAmount->getTaxPrice();
                    }

                    // 金額
                    $invoices['amount'] = number_format($combineInvoice->allowance_amount);

                    // 備註
                    $invoices['remark'] = '折讓拋轉狀態 : ' . $combineInvoice->rtn_code . '-' . $combineInvoice->rtn_msg;

                    // 隨機碼
                    $invoices['random_no'] = $combineInvoice->invoice->random_no;

                    // 發票明細
                    if ($combineInvoice->invoiceAllowanceDetails->isNotEmpty()) {
                        $combineInvoice->invoiceAllowanceDetails->each(function ($invoiceAllowanceDetail) use (&$invoices) {
                            $invoiceAllowanceDetails = [
                                'seq' => $invoiceAllowanceDetail->seq,
                                'item_name' => $invoiceAllowanceDetail->item_name,
                                'unit_price' => number_format($invoiceAllowanceDetail->unit_price),
                                'qty' => $invoiceAllowanceDetail->qty,
                                'amount' => number_format($invoiceAllowanceDetail->amount),
                            ];

                            $invoices['invoice_details'][] = $invoiceAllowanceDetails;
                        });
                    }
                }

                $payload['invoices'][] = $invoices;
            });
        }

        // 金流資訊
        if ($order->orderPayments->isNotEmpty()) {
            $order->orderPayments->each(function ($orderPayment) use (&$payload) {
                $orderPayments = [
                    'created_at_format' => Carbon::parse($orderPayment->created_at)->format('Y-m-d H:i'),
                    'payment_type' => config('uec.payment_type_options')[$orderPayment->payment_type] ?? null,
                    'amount' => number_format($orderPayment->amount),
                    'payment_status' => null,
                    'latest_api_date' => null,
                    'remark' => $orderPayment->remark,
                ];

                // 金流狀態
                if ($orderPayment->payment_type == 'PAY') {
                    $orderPayments['payment_status'] = config('uec.payment_pay_status_options')[$orderPayment->payment_status] ?? null;
                } else {
                    $orderPayments['payment_status'] = config('uec.payment_refund_status_options')[$orderPayment->payment_status] ?? null;
                }

                // 請款/退款API最近一次呼叫時間
                if (isset($orderPayment->latest_api_date)) {
                    $orderPayments['latest_api_date'] = Carbon::parse($orderPayment->latest_api_date)->format('Y-m-d H:i');
                }

                $payload['order_payments'][] = $orderPayments;
            });
        }

        // 活動折抵
        if ($order->orderCampaignDiscounts->isNotEmpty()) {
            $order->orderCampaignDiscounts->each(function ($orderCampaignDiscount) use (&$payload) {
                $orderCampaignDiscounts = [
                    'group_seq' => $orderCampaignDiscount->group_seq,
                    'level_code' => null,
                    'campaign_name' => $orderCampaignDiscount->promotionalCampaign->campaign_name,
                    'campaign_brief' => $orderCampaignDiscount->promotionalCampaign->campaign_brief,
                    'item_no' => $orderCampaignDiscount->item_no,
                    'product_name' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'record_identity' => null,
                    'discount' => null,
                    'is_voided' => null,
                ];
                // 活動階層
                $orderCampaignDiscounts['level_code'] = config('uec.campaign_level_code_options')[$orderCampaignDiscount->promotionalCampaign->level_code] ?? null;
                if ($orderCampaignDiscount->promotionalCampaignThreshold !== null) {
                    $orderCampaignDiscounts['show_campaign_name'] = "{$orderCampaignDiscount->promotionalCampaign->campaign_brief} - {$orderCampaignDiscount->promotionalCampaignThreshold->threshold_brief}";
                } else {
                    $orderCampaignDiscounts['show_campaign_name'] = "{$orderCampaignDiscount->promotionalCampaign->campaign_brief}";
                }
                if (isset($orderCampaignDiscount->product)) {
                    $orderCampaignDiscounts['product_name'] = $orderCampaignDiscount->product->product_name;
                }

                if (isset($orderCampaignDiscount->productItem)) {
                    $orderCampaignDiscounts['spec_1_value'] = $orderCampaignDiscount->productItem->spec_1_value;
                    $orderCampaignDiscounts['spec_2_value'] = $orderCampaignDiscount->productItem->spec_2_value;
                }

                // 身分
                $orderCampaignDiscounts['record_identity'] = config('uec.order_record_identity_options')[$orderCampaignDiscount->record_identity] ?? null;

                // 折扣金額
                $orderCampaignDiscounts['discount'] = number_format($orderCampaignDiscount->discount);

                // 作廢
                $orderCampaignDiscounts['is_voided'] = $orderCampaignDiscount->is_voided == 1 ? '是' : '否';

                $payload['order_campaign_discounts'][] = $orderCampaignDiscounts;
            });
        }

        // 退貨
        if ($order->returnRequests->isNotEmpty()) {
            // 可能多筆, 取最新一筆
            $returnRequests = $order->returnRequests->last();
            $payload['is_return'] = isset($returnRequests->order_no) ? 1 : 0;
            $payload['return_status_code'] = isset($returnRequests->status_code) ? $returnRequests->status_code : null;
        }

        // 退貨成功
        if ($order->returnOrderDetails->isNotEmpty()) {
            $order->returnOrderDetails->each(function ($returnOrderDetail) use (&$payload) {
                $returnOrderDetails = [
                    'request_no' => $returnOrderDetail->request_no,
                    'data_type' => config('uec.data_type_options')[$returnOrderDetail->data_type] ?? null,
                    'dtl_desc' => '',
                    'selling_price' => null,
                    'qty' => $returnOrderDetail->qty,
                    'subtotal' => null,
                    'point_discount' => $returnOrderDetail->point_discount,
                    'refund_amount' => null,
                    'remark' => '',
                ];

                if ($returnOrderDetail->data_type == 'PRD') {
                    $temp = [];

                    if (isset($returnOrderDetail->productItem)) {
                        $temp['item_no'] = $returnOrderDetail->productItem->item_no;
                        $temp['spec_1_value'] = $returnOrderDetail->productItem->spec_1_value;
                        $temp['spec_2_value'] = $returnOrderDetail->productItem->spec_2_value;
                    }
                    if (isset($returnOrderDetail->productItem->product)) {
                        $temp['product_name'] = $returnOrderDetail->productItem->product->product_name;
                        $temp['spec_dimension'] = $returnOrderDetail->productItem->product->spec_dimension;
                    }

                    if ($temp['spec_dimension'] == 0) {
                        $returnOrderDetails['dtl_desc'] = ($temp['item_no'] . '_' . $temp['product_name']) ?? '';
                    } elseif ($temp['spec_dimension'] == 1) {
                        $returnOrderDetails['dtl_desc'] = ($temp['item_no'] . '_' . $temp['product_name'] . '_' . $temp['spec_1_value']) ?? '';
                    } elseif ($temp['spec_dimension'] == 2) {
                        $returnOrderDetails['dtl_desc'] = ($temp['item_no'] . '_' . $temp['product_name'] . '_' . $temp['spec_1_value'] . '/' . $temp['spec_2_value']) ?? '';
                    }
                } elseif ($returnOrderDetail->data_type == 'CAMPAIGN') {
                    if (isset($returnOrderDetail->promotionalCampaign)) {
                        $returnOrderDetails['dtl_desc'] = ($returnOrderDetail->promotionalCampaign->campaign_brief) ?? '';
                    }
                }

                $returnOrderDetails['selling_price'] = number_format($returnOrderDetail->selling_price);
                $returnOrderDetails['subtotal'] = number_format($returnOrderDetail->subtotal);
                $returnOrderDetails['refund_amount'] = number_format($returnOrderDetail->refund_amount);
                $returnOrderDetails['remark'] = config('uec.return_remark_options')[$returnOrderDetail->is_negotiated] ?? '';

                if ($returnOrderDetail->is_negotiated == 1 && $payload['is_negotiated'] == 0) {
                    $payload['is_negotiated'] = 1;
                }

                $payload['return_order_details'][] = $returnOrderDetails;
            });
        }

        return response()->json($payload);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportExcel(Request $request)
    {
        $payload = $request->query();

        $orders = $this->orderService->getExcelList($payload);

        //購物車滿額折扣，攤提回單品計算
        if (config('uec.cart_p_discount_split') == 1) {
            return Excel::download(new CartPDiscountSplitOrderExport($orders), 'orders.xlsx');
        }

        return Excel::download(new OrderExport($orders), 'orders.xlsx');
    }
}
