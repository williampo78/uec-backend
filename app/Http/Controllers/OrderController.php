<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\OrderService;
use App\Services\MoneyAmountService;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    private $orderService;
    private $roleService;

    public function __construct(
        OrderService $orderService,
        RoleService $roleService
    ) {
        $this->orderService = $orderService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payload = $request->query();

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        if (!$this->roleService->getOtherRoles()['auth_query'] || count($payload) < 1) {
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

            return $order->only([
                'id',
                'ordered_date',
                'order_status_desc',
                'order_no',
                'status_code',
                'payment_method',
                'lgst_method',
                'paid_amount',
                'member_account',
                'buyer_name',
                'shipments',
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
    public function show($id, MoneyAmountService $moneyAmountService)
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
            'shipped_at' => null,
            'arrived_store_at' => null,
            'home_dilivered_at' => null,
            'cvs_completed_at' => null,
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
                    'cart_p_discount' =>null,
                    'point_discount' => null,
                    'record_identity' => null,
                    'package_no' => null,
                    'returned_qty' => $orderDetail->returned_qty,
                    'returned_campaign_discount' => null,
                    'returned_subtotal' => null,
                    'returned_cart_p_discount' =>null,
                    'returned_point_discount' => null,
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

                // 累計已銷退的活動折扣金額
                $orderDetails['returned_campaign_discount'] = number_format($orderDetail->returned_campaign_discount);

                // 累計已銷退的小計
                $orderDetails['returned_subtotal'] = number_format($orderDetail->returned_subtotal);

                // 累計已銷退的購物車活動折扣金額
                $orderDetails['returned_cart_p_discount'] = number_format($orderDetail->returned_cart_p_discount);

                // 累計已銷退的會員點數扣抵金額
                $orderDetails['returned_point_discount'] = number_format($orderDetail->returned_point_discount);

                $payload['order_details'][] = $orderDetails;
            });
        }

        // 發票資訊
        if ($order->combineInvoices->isNotEmpty()) {
            $order->combineInvoices->each(function ($combineInvoice) use ($moneyAmountService, &$payload) {
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
                ];

                $tableName = $combineInvoice->getTable();

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
                        $moneyAmountService->setTaxType($combineInvoice->tax_type)
                            ->setPrice($combineInvoice->total_amount)
                            ->calculateNontaxPrice()
                            ->calculateTaxPrice();

                        $invoices['total_tax'] = $moneyAmountService->getTaxPrice();
                    }

                    // 金額
                    $invoices['amount'] = number_format($combineInvoice->total_amount);

                    // 備註
                    $invoices['remark'] = $combineInvoice->remark;

                    // 隨機碼
                    $invoices['random_no'] = $combineInvoice->random_no;

                    // 發票明細
                    if ($combineInvoice->invoiceDetails->isNotEmpty()) {
                        $combineInvoice->invoiceDetails->each(function ($invoiceDetail) use (&$invoices) {
                            $invoiceDetails = [
                                'seq' => $invoiceDetail->seq,
                                'item_name' => $invoiceDetail->item_name,
                                'unit_price' => number_format($invoiceDetail->unit_price),
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

                    // 課稅別
                    $invoices['tax_type'] = config('uec.tax_type_options')[$combineInvoice->invoice->tax_type] ?? null;

                    // 有統編，總稅額需要從總金額計算出來
                    if (isset($combineInvoice->invoice->cust_gui_number)) {
                        $moneyAmountService->setTaxType($combineInvoice->invoice->tax_type)
                            ->setPrice($combineInvoice->allowance_amount)
                            ->calculateNontaxPrice()
                            ->calculateTaxPrice();

                        $invoices['total_tax'] = $moneyAmountService->getTaxPrice();
                    }

                    // 金額
                    $invoices['amount'] = number_format($combineInvoice->allowance_amount);

                    // 備註
                    $invoices['remark'] = $combineInvoice->invoice->remark;

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
                    'item_no' => $orderCampaignDiscount->item_no,
                    'product_name' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'record_identity' => null,
                    'discount' => null,
                    'is_voided' => null,
                ];

                // if (isset($orderCampaignDiscount->promotionalCampaign->campaign_brief)) {
                //     $orderCampaignDiscounts['campaign_name'] = $orderCampaignDiscount->promotionalCampaign->campaign_brief." | ".$orderCampaignDiscount->promotionalCampaign->campaign_name;
                // }

                // 活動階層
                $orderCampaignDiscounts['level_code'] = config('uec.campaign_level_code_options')[$orderCampaignDiscount->promotionalCampaign->level_code] ?? null;

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

        return Excel::download(new OrderExport($orders), 'orders.xlsx');
    }
}
