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

        // 物流方式
        if (isset(config('uec.order_lgst_method_options')[$order->lgst_method])) {
            $order->lgst_method = config('uec.order_lgst_method_options')[$order->lgst_method];
        }

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

        if (isset($order->order_details)) {
            $order->order_details = $order->order_details->map(function ($order_detail) {
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
            })
                ->toArray();
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
            'order_details',
            'shipments',
        ]);

        return response()->json($order);
    }
}
