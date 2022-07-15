<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * 取得訂單table列表
     *
     * @param array $payload
     * @return Collection
     */
    public function getTableList(array $payload = []): Collection
    {
        $orders = Order::with(['shipments'])->where('is_latest', 1);

        // 訂單開始時間
        if (isset($payload['ordered_date_start'])) {
            $orders = $orders->whereDate('ordered_date', '>=', $payload['ordered_date_start']);
        }

        // 訂單結束時間
        if (isset($payload['ordered_date_end'])) {
            $orders = $orders->whereDate('ordered_date', '<=', $payload['ordered_date_end']);
        }

        // 訂單編號
        if (isset($payload['order_no'])) {
            $orders = $orders->where('order_no', $payload['order_no']);
        }

        // 會員帳號
        if (isset($payload['member_account'])) {
            $orders = $orders->where('member_account', $payload['member_account']);
        }

        // 訂單狀態
        if (isset($payload['order_status_code'])) {
            $orders = $orders->where('status_code', $payload['order_status_code']);
        }

        // 付款狀態
        if (isset($payload['pay_status'])) {
            $orders = $orders->where('pay_status', $payload['pay_status']);
        }

        // 出貨單狀態
        if (isset($payload['shipment_status_code'])) {
            $orders = $orders->whereRelation('shipments', 'status_code', $payload['shipment_status_code']);
        }

        // 商品序號
        if (isset($payload['product_no'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_no', $payload['product_no']);
        }

        // 商品名稱
        if (isset($payload['product_name'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_name', 'LIKE', "%{$payload['product_name']}%");
        }

        // 活動名稱
        if (isset($payload['campaign_name'])) {
            $orders = $orders->whereRelation('orderCampaignDiscounts.promotionalCampaign', 'campaign_name', 'LIKE', "%{$payload['campaign_name']}%");
        }

        return $orders->select()
            ->addSelect(DB::raw('get_order_status_desc(order_no) AS order_status_desc'))
            ->orderBy('ordered_date', 'desc')
            ->get();
    }

    /**
     * 取得訂單table明細
     *
     * @param integer $id
     * @return Model
     */
    public function getTableDetailById(int $id): Model
    {
        $order = Order::with([
            'shipments',
            'orderDetails' => function ($query) {
                $query->orderBy('order_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'orderDetails.product',
            'orderDetails.productItem',
            'orderDetails.shipmentDetail.shipment',
            'orderPayments' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'orderCampaignDiscounts' => function ($query) {
                $query->orderBy('group_seq', 'asc')
                    ->orderBy('order_detail_id', 'asc');
            },
            'orderCampaignDiscounts.product',
            'orderCampaignDiscounts.productItem',
            'orderCampaignDiscounts.promotionalCampaign',
            'invoices' => function ($query) {
                $query->select()
                    ->addSelect('invoice_date AS transaction_date');
            },
            'invoices.invoiceDetails' => function ($query) {
                $query->orderBy('invoice_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'invoiceAllowances' => function ($query) {
                $query->select()
                    ->addSelect('allowance_date AS transaction_date');
            },
            'invoiceAllowances.invoiceAllowanceDetails' => function ($query) {
                $query->orderBy('invoice_allowance_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'invoiceAllowances.invoice',
            'donatedInstitution',
        ]);

        $order = $order->find($id);

        // 合併發票開立、發票折讓
        $combineInvoices = collect();

        if ($order->invoices->isNotEmpty()) {
            $combineInvoices = $combineInvoices->concat($order->invoices);
        }

        if ($order->invoiceAllowances->isNotEmpty()) {
            $combineInvoices = $combineInvoices->concat($order->invoiceAllowances);
        }

        $combineInvoices = $combineInvoices->sortBy('transaction_date');

        $order->combineInvoices = $combineInvoices;

        return $order;
    }

    /**
     * 取得訂單excel列表
     *
     * @param array $payload
     * @return Collection
     */
    public function getExcelList(array $payload = []): Collection
    {
        $orders = Order::with([
            'orderDetails' => function ($query) {
                $query->orderBy('order_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'orderDetails.product',
            'orderDetails.productItem',
            'orderDetails.shipmentDetail.shipment',
        ])->where('is_latest', 1);

        // 訂單開始時間
        if (isset($payload['ordered_date_start'])) {
            $orders = $orders->whereDate('ordered_date', '>=', $payload['ordered_date_start']);
        }

        // 訂單結束時間
        if (isset($payload['ordered_date_end'])) {
            $orders = $orders->whereDate('ordered_date', '<=', $payload['ordered_date_end']);
        }

        // 訂單編號
        if (isset($payload['order_no'])) {
            $orders = $orders->where('order_no', $payload['order_no']);
        }

        // 會員帳號
        if (isset($payload['member_account'])) {
            $orders = $orders->where('member_account', $payload['member_account']);
        }

        // 訂單狀態
        if (isset($payload['order_status_code'])) {
            $orders = $orders->where('status_code', $payload['order_status_code']);
        }

        // 付款狀態
        if (isset($payload['pay_status'])) {
            $orders = $orders->where('pay_status', $payload['pay_status']);
        }

        // 出貨單狀態
        if (isset($payload['shipment_status_code'])) {
            $orders = $orders->whereRelation('shipments', 'status_code', $payload['shipment_status_code']);
        }

        // 商品序號
        if (isset($payload['product_no'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_no', $payload['product_no']);
        }

        // 商品名稱
        if (isset($payload['product_name'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_name', 'LIKE', "%{$payload['product_name']}%");
        }

        // 活動名稱
        if (isset($payload['campaign_name'])) {
            $orders = $orders->whereRelation('orderCampaignDiscounts.promotionalCampaign', 'campaign_name', 'LIKE', "%{$payload['campaign_name']}%");
        }

        return $orders->orderBy('ordered_date', 'desc')
            ->get();
    }

    /**
     * 取得會員訂單
     *
     * @param array $payload
     * @return Collection
     */
    public function getMemberOrders(array $payload = []): Collection
    {
        $member = auth('api')->user();

        $orders = Order::with([
            'shipments',
            'orderDetails' => function ($query) {
                $query->orderBy('order_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'orderDetails.product',
            'orderDetails.productItem',
            'orderDetails.product.productPhotos' => function ($query) {
                $query->orderBy('product_id', 'asc')
                    ->orderBy('sort', 'asc');
            },
        ])->where('revision_no', 0)
            ->where('member_id', $member->member_id)
            ->whereDate('ordered_date', '>=', $payload['ordered_date_start'])
            ->whereDate('ordered_date', '<=', $payload['ordered_date_end'])
            ->select()
            ->addSelect(DB::raw('get_order_status_desc(order_no) AS order_status_desc'))
            ->orderBy('ordered_date', 'desc')
            ->get();

        return $orders;
    }

    /**
     * 取得會員訂單詳細內容
     *
     * @param string $orderNo
     * @return Model|null
     */
    public function getMemberOrderDetailByOrderNo(string $orderNo): ?Model
    {
        $member = auth('api')->user();

        $order = Order::with([
            'shipments',
            'orderDetails' => function ($query) {
                $query->orderBy('order_id', 'asc')
                    ->orderBy('seq', 'asc');
            },
            'orderDetails.product',
            'orderDetails.productItem',
            'orderDetails.product.productPhotos' => function ($query) {
                $query->orderBy('product_id', 'asc')
                    ->orderBy('sort', 'asc');
            },
            'returnRequests' => function ($query) {
                $query->orderBy('id', 'desc');
            },
        ])->where('revision_no', 0)
            ->where('member_id', $member->member_id)
            ->where('order_no', $orderNo)
            ->select()
            ->addSelect(DB::raw('get_order_status_desc(order_no) AS order_status_desc'))
            ->first();

        return $order;
    }

    /**
     * 是否可以取消訂單
     *
     * @param string $status_code 訂單狀態
     * @param string $order_date 訂單成立時間
     * @param integer $cancel_limit_mins 訂單取消限制時間
     * @return boolean
     */
    public function canCancelOrder(string $status_code, string $order_date, int $cancel_limit_mins): bool
    {
        $now = Carbon::now();
        $cancel_limit_date = Carbon::parse($order_date)->addMinutes($cancel_limit_mins);

        if ($status_code != 'CREATED') {
            return false;
        }

        // 現在時間>訂單取消限制時間
        if ($now->greaterThan($cancel_limit_date)) {
            return false;
        }

        return true;
    }

    /**
     * 是否可以申請退貨
     *
     * @param string $status_code 訂單狀態
     * @param string|null $delivered_at 商品配達時間
     * @param string|null $cooling_off_due_date 鑑賞期截止時間
     * @param integer|null $return_request_id 退貨申請單id
     * @return boolean
     */
    public function canReturnOrder(string $status_code, ?string $delivered_at, ?string $cooling_off_due_date, ?int $return_request_id): bool
    {
        $now = Carbon::now();
        $cooling_off_due_date = Carbon::parse($cooling_off_due_date);

        if ($status_code != 'CLOSED') {
            return false;
        }

        if (isset($return_request_id)) {
            return false;
        }

        if (!isset($delivered_at) || !isset($cooling_off_due_date)) {
            return false;
        }

        // 現在時間>鑑賞期截止時間
        if ($now->greaterThan($cooling_off_due_date)) {
            return false;
        }

        return true;
    }
    /**
     * 取得該訂單有使用到的折扣
     *
     * @param string $orderId
     * @return Model|null
     * order_campaign_discounts
     */
    public function orderCampaignDiscountsByOrderId(string $orderId)
    {
        return OrderCampaignDiscount::with([
            'promotionalCampaign',
            'promotionalCampaignThreshold',
            'product',
            'product.productPhotos' => function ($query) {
                $query->orderBy('sort', 'asc');
            },
        ])->where('order_id', $orderId)->get();
    }
    /**
     * 新增有折扣商品到該商品 && 折扣優惠的商品
     * @param [type] $orders
     * @return void
     */
    public function addDiscountsToOrder($orders,$giveaway_qty = [] )
    {
    $order_details = $orders['results']['order_details'];
    $discount = $this->orderCampaignDiscountsByOrderId($orders['results']['order_id']); // order_campaign_discounts
    $void_group_seq = [];
    $thresholdAmount = 0;
    //滿額折
    $thresholdDiscounts = collect();

    foreach ($discount as $obj) {
        switch ($obj->promotionalCampaign->level_code) {
            case 'PRD':
                //主商品 且有折扣金額
                foreach ($order_details as $key => $val) {
                    if ($obj->record_identity == 'M' && $obj->discount !== 0.0) {
                        if ($val['id'] == $obj->order_detail_id) {
                            $order_details[$key]['discount_content'][$obj->group_seq] = [
                                'display' => true,
                                'campaignName' => $obj->promotionalCampaign->campaign_name,
                                'campaignBrief' => $obj->promotionalCampaign->campaign_brief,
                                'thresholdCampaignBrief' => $obj->promotionalCampaignThreshold ? $obj->promotionalCampaignThreshold->threshold_brief : '',
                                'discount' => $obj->discount,
                                'campaignProdList' => [],
                            ];
                            continue;
                        };
                    }
                }
                break;

            default:
                //送禮
                if ($obj->promotionalCampaign->category_code == 'GIFT' && $obj->record_identity !== 'M') {
                    $cart['gift'][$obj->group_seq]['campaignID'] = $obj->promotionalCampaign->id;
                    $cart['gift'][$obj->group_seq]['campaignName'] = $obj->promotionalCampaign->campaign_name;
                    $cart['gift'][$obj->group_seq]['campaignUrlCode'] = $obj->promotionalCampaign->url_code;
                    $cart['gift'][$obj->group_seq]['campaignBrief'] = $obj->promotionalCampaign->campaign_brief;
                    $cart['gift'][$obj->group_seq]['thresholdCampaignBrief'] = $obj->promotionalCampaignThreshold ? $obj->promotionalCampaignThreshold->threshold_brief : '';
                    $cart['gift'][$obj->group_seq]['campaignProdList'][$obj->product->id] = [
                        'productPhoto' => config('filesystems.disks.s3.url') . $obj->product->productPhotos[0]->photo_name,
                        'productId' => $obj->product->id,
                        'productName' => $obj->product->product_name,
                        'assignedQty' => $giveaway_qty[$obj->order_detail_id] ?? 0 ,
                    ]; //贈送的商品列表
                }
                //折扣
                if ($obj->discount < 0 && $obj->order_detail_id !== null && $obj->promotionalCampaign->level_code == 'CART_P') {

                    //滿額折-併入discount_content內使用
                    $thresholdDiscounts->push([
                        'product_id'             => $obj->product_id,
                        'product_item_id'        => $obj->product_item_id,
                        'campaignName'           => $obj->promotionalCampaign->campaign_name,
                        'campaignBrief'          => $obj->promotionalCampaign->campaign_brief,
                        'thresholdCampaignBrief' => $obj->promotionalCampaignThreshold->threshold_brief,
                    ]);

                    if (!isset($cart['discount'][$obj->group_seq]['campaignDiscount'])) {
                        $cart['discount'][$obj->group_seq]['campaignDiscount'] = 0;
                    }
                    $cart['discount'][$obj->group_seq]['campaignBrief'] = $obj->promotionalCampaign->campaign_brief;
                    $cart['discount'][$obj->group_seq]['thresholdCampaignBrief'] = $obj->promotionalCampaignThreshold ? $obj->promotionalCampaignThreshold->threshold_brief : '';
                    $cart['discount'][$obj->group_seq]['campaignName'] = $obj->promotionalCampaign->campaign_name;
                    $cart['discount'][$obj->group_seq]['campaignID'] = $obj->promotionalCampaign->id;
                    $cart['discount'][$obj->group_seq]['campaignUrlCode'] = $obj->promotionalCampaign->url_code;
                    $cart['discount'][$obj->group_seq]['campaignDiscount'] += $obj->discount;
                    $thresholdAmount += $obj->discount;
                }
                break;
        }

    }

    foreach($order_details as $key => $val){
        $findProductPRD_M = OrderCampaignDiscount::where('order_detail_id', '=', $val['id'])
                    ->where('order_id', $orders['results']['order_id'])
                    ->where('level_code', 'PRD')
                    ->where('record_identity' ,'M')
                    ->where('discount' ,0.0)
                    ->first();

        if($findProductPRD_M !== null)
        {
            $findProductPRD_G = OrderCampaignDiscount::with([
                'promotionalCampaign',
                'promotionalCampaignThreshold',
            ])
                ->with(['product' => function ($query) {
                    $query->with(['productPhotos' => function ($query) {
                        $query->select(['id', 'product_id', 'photo_name'])
                            ->orderBy('sort', 'ASC');
                    }]);
                }])

            ->where('order_detail_id', '<>', $val['id'])
            ->where('group_seq',$findProductPRD_M->group_seq)
            ->where('order_id', $orders['results']['order_id'])
            ->where('level_code', 'PRD')
            ->where('record_identity' ,'G')
            ->get();

            //取得需要的order_details資料
            $orderDetailIds = $findProductPRD_G->pluck('order_detail_id')->toArray();
            $OrderDetails   = collect();
            if (empty($OrderDetails) === false) {
                $OrderDetails = OrderDetail::select(['id', 'order_id', 'product_id', 'qty'])
                    ->whereIn('id', $orderDetailIds)
                    ->get();
            }

            foreach($findProductPRD_G as $PRD){

                $photo_name = optional($PRD->product->productPhotos->first())->photo_name;
                $photo_name = empty($photo_name) ? null : config('filesystems.disks.s3.url') . $photo_name;

                if (!isset($order_details[$key]['discount_content'][$PRD->group_seq])) {
                    $order_details[$key]['discount_content'][$PRD->group_seq] = [
                        'display' => true,
                        'campaignName' => $PRD->promotionalCampaign->campaign_name,
                        'campaignBrief' => $PRD->promotionalCampaign->campaign_brief,
                        'thresholdCampaignBrief' => $PRD->promotionalCampaignThreshold ? $PRD->promotionalCampaignThreshold->threshold_brief : '',
                        'campaignProdList' => [
                            [
                                'productId' => $PRD->product->id,
                                'productName' => $PRD->product->product_name,
                                'productPhoto' => $photo_name,
                                'qty' => optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty
                            ],
                        ],
                    ];
                } else {
                    $order_details[$key]['discount_content'][$PRD->group_seq]['campaignProdList'][]= [
                        'productId' => $PRD->product->id,
                        'productName' => $PRD->product->product_name,
                        'productPhoto' => $photo_name,
                        'qty' => optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty
                    ];
                }

            }
        }

        //滿額折
        $TargetThresholdDiscounts = $thresholdDiscounts
            ->where('product_id', $val['product_id'])
            ->where('product_item_id', $val['product_item_id']);

        foreach ($TargetThresholdDiscounts as $thresholdDiscount) {

            $order_details[$key]['discount_content'][] = [
                'display'                => config('uec.cart_p_discount_split') == 1,
                'type'                   => '滿額折',
                'campaignName'           => $thresholdDiscount['campaignName'],
                'campaignBrief'          => $thresholdDiscount['campaignBrief'],
                'thresholdCampaignBrief' => $thresholdDiscount['thresholdCampaignBrief'],
                'campaignProdList'       => []
            ];
        }
    }

    if (isset($cart['discount'])) {
        array_multisort($cart['discount'], SORT_ASC);
    }
    if (isset($cart['gift'])) {
        array_multisort($cart['gift'], SORT_ASC);
        foreach ($cart['gift'] as $key => $val) {
            array_multisort($cart['gift'][$key]['campaignProdList'], SORT_ASC);
        }
    }
    foreach ($order_details as $key => $val) {
        array_multisort($order_details[$key]['discount_content'], SORT_ASC);
    }
    $orders['results']['thresholdAmount'] = config('uec.cart_p_discount_split') == 1 ? 0 : $thresholdAmount;
    $orders['results']['order_details'] = $order_details;
    //是否顯示thresholdDiscount
    $orders['results']['displayThresholdDiscount'] = config('uec.cart_p_discount_split') != 1;
    $orders['results']['thresholdDiscount'] = $cart['discount'] ?? []; //折扣
    $orders['results']['thresholdGiftAway'] = $cart['gift'] ?? []; //送禮,
    return $orders;
}
}
