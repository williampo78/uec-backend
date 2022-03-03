<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Services\LookupValuesVService;
use App\Services\MoneyAmountService;
use App\Services\OrderService;
use App\Services\RoleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    private $order_service;
    private $role_service;

    public function __construct(
        OrderService $order_service,
        RoleService $role_service
    ) {
        $this->order_service = $order_service;
        $this->role_service = $role_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query_datas = [];
        $query_datas = $request->query();

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        if (!$this->role_service->getOtherRoles()['auth_query'] || count($query_datas) < 1) {
            return view('backend.order.list');
        }

        $query_datas['is_latest'] = 1;

        $orders = $this->order_service->getOrders($query_datas);

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
            if (isset($order->shipments)) {
                $order->shipments = $order->shipments->take(1)->map(function ($shipment) {
                    // 出貨單狀態
                    $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;

                    return $shipment->only([
                        'status_code',
                    ]);
                })
                    ->toArray();
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
        })
            ->toArray();

        return view(
            'backend.order.list',
            compact('orders')
        );
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
        //
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

    public function getDetail(Request $request)
    {
        $lookup_values_v_service = new LookupValuesVService;
        $money_amount_service = new MoneyAmountService;

        $order_id = $request->input('order_id');

        $order = $this->order_service->getOrders([
            'id' => $order_id,
        ])->first();

        // 訂單時間
        $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i');

        // 訂單狀態
        $order->status_code = config('uec.order_status_code_options')[$order->status_code] ?? null;

        // 付款方式
        $order->payment_method = config('uec.payment_method_options')[$order->payment_method] ?? null;

        // 付款狀態
        $order->pay_status = config('uec.order_pay_status_options')[$order->pay_status] ?? null;

        // 免運門檻
        $order->shipping_free_threshold = number_format($order->shipping_free_threshold);

        // 收件地址
        $address = '';
        $address .= $order->receiver_city ?? '';
        $address .= $order->receiver_district ?? '';
        $address .= $order->receiver_address ?? '';
        $order->receiver_address = $address;

        // 商品總價
        $order->total_amount = number_format($order->total_amount);

        // 滿額折抵
        $order->cart_campaign_discount = number_format($order->cart_campaign_discount);

        // 點數折抵
        $order->point_discount = number_format($order->point_discount);

        // 運費
        $order->shipping_fee = number_format($order->shipping_fee);

        // 結帳金額
        $order->paid_amount = number_format($order->paid_amount);

        // 載具類型
        $order->carrier_type = config('uec.carrier_type_options')[$order->carrier_type] ?? null;

        // 發票捐贈機構
        if (isset($order->donated_institution)
            && $order->invoice_usage == 'D'
        ) {
            $lookup_values_v = $lookup_values_v_service->getLookupValuesVs([
                'type_code' => 'DONATED_INSTITUTION',
                'code' => $order->donated_institution,
            ])->first();

            $order->donated_institution_name = isset($lookup_values_v) ? "{$order->donated_institution}-{$lookup_values_v->description}" : null;
        }

        // 發票用途
        $order->invoice_usage = config('uec.invoice_usage_options')[$order->invoice_usage] ?? null;

        // 取消 / 作廢時間
        if (isset($order->cancelled_at)) {
            $order->cancelled_voided_at = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i');
        } elseif (isset($order->voided_at)) {
            $order->cancelled_voided_at = Carbon::parse($order->voided_at)->format('Y-m-d H:i');
        } else {
            $order->cancelled_voided_at = null;
        }

        // 出貨時間
        if (isset($order->shipped_at)) {
            $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y-m-d H:i');
        }

        // 到店時間
        if (isset($order->arrived_store_at)) {
            $order->arrived_store_at = Carbon::parse($order->arrived_store_at)->format('Y-m-d H:i');
        }

        // (宅配)配達時間
        if ($order->lgst_method == 'HOME' && isset($order->delivered_at)) {
            $order->home_dilivered_at = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
        } else {
            $order->home_dilivered_at = null;
        }

        // (超取)取件時間
        if ($order->lgst_method != 'HOME' && isset($order->delivered_at)) {
            $order->cvs_completed_at = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
        } else {
            $order->cvs_completed_at = null;
        }

        // 物流方式
        $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

        if (isset($order->shipments)) {
            $order->shipments = $order->shipments->take(1)->map(function ($shipment) {
                // 出貨單狀態
                $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;

                return $shipment->only([
                    'status_code',
                ]);
            });
        }

        // 訂單明細
        if (isset($order->order_details)) {
            $order->order_details = $order->order_details->map(function ($order_detail) {
                // 單位售價 (商品主檔維護的售價)
                $order_detail->selling_price = number_format($order_detail->selling_price);

                // 單價 (單品折扣後的單價，即前台購物車呈現的單價)
                $order_detail->unit_price = number_format($order_detail->unit_price);

                // 活動折扣金額
                $order_detail->campaign_discount = number_format($order_detail->campaign_discount);

                // 小計
                $order_detail->subtotal = number_format($order_detail->subtotal);

                // 會員點數扣抵金額
                $order_detail->point_discount = number_format($order_detail->point_discount);

                // 訂單明細身分
                $order_detail->record_identity = config('uec.order_record_identity_options')[$order_detail->record_identity] ?? null;

                // 累計已銷退的活動折扣金額
                $order_detail->returned_campaign_discount = number_format($order_detail->returned_campaign_discount);

                // 累計已銷退的的小計
                $order_detail->returned_subtotal = number_format($order_detail->returned_subtotal);

                // 累計已銷退的會員點數扣抵金額
                $order_detail->returned_point_discount = number_format($order_detail->returned_point_discount);

                return $order_detail->only([
                    'seq',
                    'item_no',
                    'product_name',
                    'spec_1_value',
                    'spec_2_value',
                    'selling_price',
                    'unit_price',
                    'qty',
                    'campaign_discount',
                    'subtotal',
                    'point_discount',
                    'record_identity',
                    'package_no',
                    'returned_qty',
                    'returned_campaign_discount',
                    'returned_subtotal',
                    'returned_point_discount',
                ]);
            });
        }

        // 發票資訊
        if (isset($order->invoices)) {
            $order->invoices = $order->invoices->map(function ($invoice) use ($money_amount_service) {
                // 時間
                if (isset($invoice->transaction_date)) {
                    $invoice->transaction_date = Carbon::parse($invoice->transaction_date)->format('Y-m-d');
                }

                // 類型
                $invoice->type = isset($invoice->invoice_allowance_id) ? '發票折讓' : '發票開立';

                // 有統編，總稅額需要從總金額計算出來
                if (isset($invoice->cust_gui_number)) {
                    $money_amount_service->setTaxType($invoice->tax_type)
                        ->setPrice($invoice->amount)
                        ->calculateNontaxPrice()
                        ->calculateTaxPrice();

                    $invoice->total_tax = $money_amount_service->getTaxPrice();
                }
                // 無統編，總稅額為0
                else {
                    $invoice->total_tax = 0;
                }

                // 課稅別
                $invoice->tax_type = config('uec.tax_type_options')[$invoice->tax_type] ?? null;

                // 金額
                $invoice->amount = number_format($invoice->amount);

                // 發票明細
                if (isset($invoice->invoice_details)) {
                    $invoice->invoice_details = $invoice->invoice_details->map(function ($invoice_detail) {
                        // 單價
                        $invoice_detail->unit_price = number_format($invoice_detail->unit_price);

                        // 小計
                        $invoice_detail->amount = number_format($invoice_detail->amount);

                        return $invoice_detail->only([
                            'seq',
                            'item_name',
                            'unit_price',
                            'qty',
                            'amount',
                        ]);
                    });
                }

                return $invoice->only([
                    'transaction_date',
                    'type',
                    'invoice_no',
                    'tax_type',
                    'total_tax',
                    'amount',
                    'remark',
                    'random_no',
                    'order_no',
                    'invoice_details',
                ]);
            });
        }

        // 金流資訊
        if (isset($order->order_payments)) {
            $order->order_payments = $order->order_payments->map(function ($order_payment) {
                // 時間
                $order_payment->created_at_format = Carbon::parse($order_payment->created_at)->format('Y-m-d H:i');

                // 金流狀態
                if ($order_payment->payment_type == 'PAY') {
                    $order_payment->payment_status = config('uec.payment_pay_status_options')[$order_payment->payment_status] ?? null;
                } else {
                    $order_payment->payment_status = config('uec.payment_refund_status_options')[$order_payment->payment_status] ?? null;
                }

                // 類型
                $order_payment->payment_type = config('uec.payment_type_options')[$order_payment->payment_type] ?? null;

                // 金額
                $order_payment->amount = number_format($order_payment->amount);

                // 請款/退款API最近一次呼叫時間
                if (isset($order_payment->latest_api_date)) {
                    $order_payment->latest_api_date = Carbon::parse($order_payment->latest_api_date)->format('Y-m-d H:i');
                }

                return $order_payment->only([
                    'created_at_format',
                    'payment_type',
                    'amount',
                    'payment_status',
                    'latest_api_date',
                    'remark',
                ]);
            });
        }

        // 活動折抵
        if (isset($order->order_campaign_discounts)) {
            $order->order_campaign_discounts = $order->order_campaign_discounts->map(function ($order_campaign_discount) {
                // 活動階層
                $order_campaign_discount->level_code = config('uec.campaign_level_code_options')[$order_campaign_discount->level_code] ?? null;

                // 身分
                $order_campaign_discount->record_identity = config('uec.order_record_identity_options')[$order_campaign_discount->record_identity] ?? null;

                // 折扣金額
                $order_campaign_discount->discount = number_format($order_campaign_discount->discount);

                // 作廢
                $order_campaign_discount->is_voided = $order_campaign_discount->is_voided == 1 ? '是' : '否';

                return $order_campaign_discount->only([
                    'group_seq',
                    'level_code',
                    'campaign_name',
                    'item_no',
                    'product_name',
                    'spec_1_value',
                    'spec_2_value',
                    'record_identity',
                    'discount',
                    'is_voided',
                ]);
            });
        }

        $order = $order->only([
            'id',
            'order_no',
            'ordered_date',
            'status_code',
            'payment_method',
            'pay_status',
            'shipping_free_threshold',
            'member_account',
            'buyer_name',
            'buyer_email',
            'receiver_name',
            'receiver_mobile',
            'receiver_address',
            'lgst_method',
            'total_amount',
            'cart_campaign_discount',
            'point_discount',
            'shipping_fee',
            'paid_amount',
            'shipments',
            'order_details',
            'invoice_usage',
            'carrier_type',
            'carrier_no',
            'buyer_gui_number',
            'buyer_title',
            'donated_institution_name',
            'invoices',
            'order_payments',
            'order_campaign_discounts',
            'cancelled_voided_at',
            'shipped_at',
            'arrived_store_at',
            'home_dilivered_at',
            'cvs_completed_at',
        ]);

        return response()->json($order);
    }

    public function exportOrderExcel(Request $request)
    {
        $input_datas = $request->input();
        $input_datas['is_latest'] = 1;

        $orders = $this->order_service->getOrders($input_datas);

        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }
}
