<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $order_service;

    public function __construct(
        OrderService $order_service
    ) {
        $this->order_service = $order_service;
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
        $query_datas['is_latest'] = 1;

        $orders = $this->order_service->getOrders($query_datas);

        // 整理給前端的資料
        $orders = $orders->map(function ($order) {
            if (isset($order->shipments)) {
                $order->shipments = $order->shipments->take(1)->map(function ($shipment) {
                    return $shipment->only([
                        'status_code',
                    ]);
                })
                    ->toArray();
            }

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
            ]);
        })
            ->toArray();

        return view(
            'Backend.Order.list',
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
        $order_id = $request->input('order_id');

        $order = $this->order_service->getOrders([
            'id' => $order_id,
        ])->first();

        // 訂單時間
        $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i');

        // 訂單狀態
        if (isset(config('uec.order_status_code_options')[$order->status_code])) {
            $order->status_code = config('uec.order_status_code_options')[$order->status_code];
        }

        // 付款方式
        if (isset(config('uec.order_payment_method_options')[$order->payment_method])) {
            $order->payment_method = config('uec.order_payment_method_options')[$order->payment_method];
        }

        // 付款狀態
        if (isset(config('uec.order_pay_status_options')[$order->pay_status])) {
            $order->pay_status = config('uec.order_pay_status_options')[$order->pay_status];
        }

        // 免運門檻
        $order->shipping_free_threshold = number_format($order->shipping_free_threshold);

        // 收件地址
        $order->receiver_address = $order->receiver_city . $order->receiver_district . $order->receiver_address;

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

        // 發票用途
        if (isset(config('uec.invoice_usage_options')[$order->invoice_usage])) {
            $order->invoice_usage = config('uec.invoice_usage_options')[$order->invoice_usage];
        }

        // 載具類型
        if (isset(config('uec.carrier_type_options')[$order->carrier_type])) {
            $order->carrier_type = config('uec.carrier_type_options')[$order->carrier_type];
        }

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
        if (isset(config('uec.order_lgst_method_options')[$order->lgst_method])) {
            $order->lgst_method = config('uec.order_lgst_method_options')[$order->lgst_method];
        }

        if (isset($order->shipments)) {
            $order->shipments = $order->shipments->take(1)->map(function ($shipment) {
                // 出貨單狀態
                if (isset(config('uec.shipment_status_code_options')[$shipment->status_code])) {
                    $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code];
                }

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
                if (isset(config('uec.order_record_identity_options')[$order_detail->record_identity])) {
                    $order_detail->record_identity = config('uec.order_record_identity_options')[$order_detail->record_identity];
                }

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
            $order->invoices = $order->invoices->map(function ($invoice) {
                // 時間
                if (isset($invoice->transaction_date)) {
                    $invoice->transaction_date = Carbon::parse($invoice->transaction_date)->format('Y-m-d');
                }

                // 類型
                $invoice->type = isset($invoice->invoice_allowance_id) ? '發票折讓' : '發票開立';

                // 課稅別
                if (isset(config('uec.tax_type_options')[$invoice->tax_type])) {
                    $invoice->tax_type = config('uec.tax_type_options')[$invoice->tax_type];
                }

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

                // 類型
                if (isset(config('uec.payment_type_options')[$order_payment->payment_type])) {
                    $order_payment->payment_type = config('uec.payment_type_options')[$order_payment->payment_type];
                }

                // 金額
                $order_payment->amount = number_format($order_payment->amount);

                // 金流狀態
                if (isset(config('uec.payment_status_options')[$order_payment->payment_status])) {
                    $order_payment->payment_status = config('uec.payment_status_options')[$order_payment->payment_status];
                }

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
                if (isset(config('uec.campaign_level_code_options')[$order_campaign_discount->level_code])) {
                    $order_campaign_discount->level_code = config('uec.campaign_level_code_options')[$order_campaign_discount->level_code];
                }

                // 身分
                if (isset(config('uec.order_record_identity_options')[$order_campaign_discount->record_identity])) {
                    $order_campaign_discount->record_identity = config('uec.order_record_identity_options')[$order_campaign_discount->record_identity];
                }

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
}
