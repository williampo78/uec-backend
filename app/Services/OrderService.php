<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\ProductItem;
use App\Models\ReturnExamination;
use App\Models\ReturnExaminationDetail;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestDetail;
use App\Models\Shipment;
use App\Models\ReturnOrderDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Integer;
use function PHPUnit\Framework\isEmpty;
use function Symfony\Component\Translation\t;

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

        // 訂單類型
        if (isset($payload['order_ship_from_whs'])) {
            $orders = $orders->where('ship_from_whs', $payload['order_ship_from_whs']);
        }

        // 資料範圍
        if (isset($payload['data_range'])) {
            if ($payload['data_range'] == 'SHIPPED_AT_NULL') {
                $orders = $orders->whereRelation('shipments', 'shipped_at', null);
            } elseif ($payload['data_range'] == 'DELIVERED_AT_NULL') {
                $orders = $orders->whereRelation('shipments', 'delivered_at', null);
            }
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
            'orderDetails.product.supplier',
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
            'returnRequests',
            'returnOrderDetails',
            'returnOrderDetails.productItem',
            'returnOrderDetails.productItem.product',
            'returnOrderDetails.promotionalCampaign',
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

        // 退貨成功 排序
        $order->returnOrderDetails = $order->returnOrderDetails->sortBy('Priority');

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
        ])->where('is_latest', 1)
            ->where('member_id', $member->member_id)
            ->whereDate('ordered_date', '>=', $payload['ordered_date_start'])
            ->whereDate('ordered_date', '<=', $payload['ordered_date_end'])
            ->select()
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
        ])->where('is_latest', 1)
            ->where('member_id', $member->member_id)
            ->where('order_no', $orderNo)
            ->select()
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
    public function canCancelOrder(string $status_code, string $order_date, int $cancel_limit_mins, string $ship_from_whs): bool
    {
        $now = Carbon::now();
        $cancel_limit_date = Carbon::parse($order_date)->addMinutes($cancel_limit_mins);
        $is_split = config('uec.cart_p_discount_split');
        switch ($is_split) {
            case 1:
                if ($ship_from_whs == 'SUP') {
                    if ($status_code != 'CREATED') {
                        return false;
                    }
                } else {
                    if ($status_code != 'CREATED') {
                        return false;
                    }
                    // 現在時間>訂單取消限制時間
                    if ($now->greaterThan($cancel_limit_date)) {
                        return false;
                    }
                }
                break;
            default:
                if ($status_code != 'CREATED') {
                    return false;
                }

                // 現在時間>訂單取消限制時間
                if ($now->greaterThan($cancel_limit_date)) {
                    return false;
                }
                break;

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
            'productItem' => function ($query) {
                $query->select(['id', 'spec_1_value', 'spec_2_value', 'photo_name']);
            },
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
    public function addDiscountsToOrder($orders, $giveaway_qty = [], $shippedStatus)
    {
        $order_details = $orders['results']['order_details'];
        $discount = $this->orderCampaignDiscountsByOrderId($orders['results']['order_id']); // order_campaign_discounts
        $void_group_seq = [];
        $thresholdAmount = 0;
        //滿額折
        $thresholdDiscounts = collect();

        //滿額贈
        $campaignBrief = collect();

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

                    //規格圖
                    $photo_name = optional($obj->productItem)->photo_name;
                    //規格圖為空，取商品封面圖
                    if (empty($photo_name)) {
                        $photo_name = optional($obj->product->productPhotos->first())->photo_name;
                    }

                    $photo_name = empty($photo_name) ? null : config('filesystems.disks.s3.url') . $photo_name;

                    //送禮
                    if ($obj->promotionalCampaign->category_code == 'GIFT' && $obj->record_identity !== 'M') {
                        //商品圖 以規格圖為優先，否則取商品封面圖
                        $productPhoto = empty(optional($obj->productItem)->photo_name) ? $obj->product->productPhotos[0]->photo_name : optional($obj->productItem)->photo_name;
                        $productPhoto = empty($productPhoto) ? null : config('filesystems.disks.s3.url') . $productPhoto;

                        $cart['gift'][$obj->group_seq]['campaignID'] = $obj->promotionalCampaign->id;
                        $cart['gift'][$obj->group_seq]['campaignName'] = $obj->promotionalCampaign->campaign_name;
                        $cart['gift'][$obj->group_seq]['campaignUrlCode'] = $obj->promotionalCampaign->url_code;
                        $cart['gift'][$obj->group_seq]['campaignBrief'] = $obj->promotionalCampaign->campaign_brief;
                        $cart['gift'][$obj->group_seq]['thresholdCampaignBrief'] = $obj->promotionalCampaignThreshold ? $obj->promotionalCampaignThreshold->threshold_brief : '';
                        $cart['gift'][$obj->group_seq]['campaignProdList'][$obj->product->id] = [
                            'id' => $obj->order_detail_id,
                            'productPhoto' => $productPhoto,
                            'productPhoto' => $photo_name,
                            'productId' => $obj->product->id,
                            'productName' => $obj->product->product_name,
                            'assignedQty' => $giveaway_qty[$obj->order_detail_id] ?? 0,
                            'spec_1_value' => optional($obj->productItem)->spec_1_value,
                            'spec_2_value' => optional($obj->productItem)->spec_2_value,
                        ]; //贈送的商品列表

                        $cart['gift'][$obj->group_seq]['can_return'] = isset($shippedStatus['can_return'][$obj->order_detail_id][$obj->product_item_id]) ? $shippedStatus['can_return'][$obj->order_detail_id][$obj->product_item_id] : "";
                        $cart['gift'][$obj->group_seq]['shipped_info'] = isset($shippedStatus['shipped_info'][$obj->order_detail_id][$obj->product_item_id]) ? $shippedStatus['shipped_info'][$obj->order_detail_id][$obj->product_item_id] : "";
                        $cart['gift'][$obj->group_seq]['shipped_status'] = isset($shippedStatus['shipped_status'][$obj->order_detail_id][$obj->product_item_id]) ? $shippedStatus['shipped_status'][$obj->order_detail_id][$obj->product_item_id] : "";
                    }
                    //折扣
                    if ($obj->discount < 0 && $obj->order_detail_id !== null && $obj->promotionalCampaign->level_code == 'CART_P') {

                        //滿額折-併入discount_content內使用
                        $thresholdDiscounts->push([
                            'product_id' => $obj->product_id,
                            'product_item_id' => $obj->product_item_id,
                            'campaignName' => $obj->promotionalCampaign->campaign_name,
                            'campaignBrief' => $obj->promotionalCampaign->campaign_brief,
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
                        $cart['discount'][$obj->group_seq]['idList'][] = $obj->order_detail_id; //贈送的detail id;
                        $thresholdAmount += $obj->discount;
                    }
                    //滿額贈
                    if ($obj->promotionalCampaign->category_code == 'GIFT' && $obj->record_identity == 'M' && $obj->level_code == 'CART_P') {
                        //滿額贈-併入discount_content內使用
                        $campaignBrief->push([
                            'product_id' => $obj->product_id,
                            'product_item_id' => $obj->product_item_id,
                            'campaignName' => $obj->promotionalCampaign->campaign_name,
                            'campaignBrief' => $obj->promotionalCampaign->campaign_brief,
                            'thresholdCampaignBrief' => $obj->promotionalCampaignThreshold->threshold_brief,
                        ]);
                        //$cart['gift'][$obj->group_seq]['itemList'][] = $obj->product_item_id; //贈送的商品;
                        $cart['gift'][$obj->group_seq]['idList'][] = $obj->order_detail_id; //贈送的detail id;
                    }
                    break;
            }

        }
        $tmp_group = "";
        foreach ($order_details as $key => $val) {
            $findProductPRD_M = OrderCampaignDiscount::where('order_detail_id', '=', $val['id'])
                ->where('order_id', $orders['results']['order_id'])
                ->where('level_code', 'PRD')
                ->where('record_identity', 'M')
                ->where('discount', 0.0)
                ->first();
            if ($findProductPRD_M !== null) {
                $findProductPRD_G = OrderCampaignDiscount::with([
                    'promotionalCampaign',
                    'promotionalCampaignThreshold',
                ])
                    ->with(['productItem' => function ($query) {
                        $query->select(['id', 'spec_1_value', 'spec_2_value', 'photo_name']);
                    }])
                    ->with(['product' => function ($query) {
                        $query->with(['productPhotos' => function ($query) {
                            $query->select(['id', 'product_id', 'photo_name'])
                                ->orderBy('sort', 'ASC');
                        }]);
                    }])
                    ->where('order_detail_id', '<>', $val['id'])
                    ->where('group_seq', $findProductPRD_M->group_seq)
                    ->where('order_id', $orders['results']['order_id'])
                    ->where('level_code', 'PRD')
                    ->where('record_identity', 'G')
                    ->get();

                //取得需要的order_details資料
                $orderDetailIds = $findProductPRD_G->pluck('order_detail_id')->toArray();
                $OrderDetails = collect();
                if (empty($OrderDetails) === false) {
                    $OrderDetails = OrderDetail::select(['id', 'order_id', 'product_id', 'qty', 'returned_qty'])
                        ->whereIn('id', $orderDetailIds)
                        ->get();
                }

                foreach ($findProductPRD_G as $PRD) {

                    //商品圖 以規格圖為優先，否則取商品封面圖
                    $productPhoto = empty(optional($PRD->productItem)->photo_name) ? optional($PRD->product->productPhotos->first())->photo_name : optional($PRD->productItem)->photo_name;
                    $productPhoto = empty($productPhoto) ? null : config('filesystems.disks.s3.url') . $productPhoto;
                    //規格圖
                    $photo_name = optional($PRD->productItem)->photo_name;
                    //規格圖為空，取商品封面圖
                    if (empty($photo_name)) {
                        $photo_name = optional($PRD->product->productPhotos->first())->photo_name;
                    }

                    $photo_name = empty($photo_name) ? null : config('filesystems.disks.s3.url') . $photo_name;
                    if (!isset($order_details[$key]['discount_content'][$PRD->group_seq])) {
                        $qty = optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty - optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->returned_qty;

                        if ($tmp_group != $PRD->order_detail_id) {
                            $order_details[$key]['discount_content'][$PRD->group_seq] = [
                                'display' => true,
                                'campaignName' => $PRD->promotionalCampaign->campaign_name,
                                'campaignBrief' => $PRD->promotionalCampaign->campaign_brief,
                                'thresholdCampaignBrief' => $PRD->promotionalCampaignThreshold ? $PRD->promotionalCampaignThreshold->threshold_brief : '',
                                'campaignProdList' => [
                                    [
                                        'id' => $PRD->order_detail_id,
                                        'productId' => $PRD->product->id,
                                        'productName' => $PRD->product->product_name,
                                        'productPhoto' => $photo_name,
                                        'qty' => $qty,
                                        'spec_1_value' => optional($PRD->productItem)->spec_1_value,
                                        'spec_2_value' => optional($PRD->productItem)->spec_2_value
                                    ],
                                ],
                            ];
                            $tmp_group = $PRD->order_detail_id;
                        } else {
                            $order_details[$key]['discount_content'][$PRD->group_seq] = [
                                'display' => true,
                                'campaignName' => $PRD->promotionalCampaign->campaign_name,
                                'campaignBrief' => $PRD->promotionalCampaign->campaign_brief,
                                'thresholdCampaignBrief' => $PRD->promotionalCampaignThreshold ? $PRD->promotionalCampaignThreshold->threshold_brief : '',
                                'campaignProdList' => [],
                            ];
                        }
                    } else {
                        $qty = optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty - optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->returned_qty;
                        $order_details[$key]['discount_content'][$PRD->group_seq]['campaignProdList'][] = [
                            'id' => $PRD->order_detail_id,
                            'productId' => $PRD->product->id,
                            'productName' => $PRD->product->product_name,
                            'productPhoto' => $photo_name,
                            'qty' => $qty,
                            'spec_1_value' => optional($PRD->productItem)->spec_1_value,
                            'spec_2_value' => optional($PRD->productItem)->spec_2_value
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
                    'display' => config('uec.cart_p_discount_split') == 1,
                    'type' => '滿額折',
                    'campaignName' => $thresholdDiscount['campaignName'],
                    'campaignBrief' => $thresholdDiscount['campaignBrief'],
                    'thresholdCampaignBrief' => $thresholdDiscount['thresholdCampaignBrief'],
                    'campaignProdList' => []
                ];
            }
            //滿額贈
            $TargetcampaignBrief = $campaignBrief
                ->where('product_id', $val['product_id'])
                ->where('product_item_id', $val['product_item_id']);
            foreach ($TargetcampaignBrief as $thresholdDiscount) {
                $order_details[$key]['discount_content'][] = [
                    'display' => config('uec.cart_p_discount_split') == 1,
                    'type' => '滿額贈',
                    'campaignName' => $thresholdDiscount['campaignName'],
                    'campaignBrief' => $thresholdDiscount['campaignBrief'],
                    'thresholdCampaignBrief' => $thresholdDiscount['thresholdCampaignBrief'],
                    'campaignProdList' => []
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
        //清除索引，回傳時統一為陣列格式
        foreach ($order_details as $key => $val) {
            $order_details[$key]['discount_content'] = array_values($val['discount_content']);
        }

        $orders['results']['thresholdAmount'] = config('uec.cart_p_discount_split') == 1 ? 0 : $thresholdAmount;
        $orders['results']['order_details'] = $order_details;
        //是否顯示thresholdDiscount
        $orders['results']['displayThresholdDiscount'] = config('uec.cart_p_discount_split') != 1;
        $orders['results']['thresholdDiscount'] = $cart['discount'] ?? []; //折扣
        $orders['results']['thresholdGiftAway'] = $cart['gift'] ?? []; //送禮,
        return $orders;
    }


    /**
     * 取得訂單單品出貨貨態
     *
     * @param array $payload
     * @return array
     */
    public function getShippedStatus($order, $can_return_order): array
    {
        $status = [];
        $return_status = [];
        $T01 = null; //出貨單 產生時間
        $T02 = null; //出貨單 取消時間/作廢時間
        $T03 = null; //退款成功時間
        $T04 = (is_null($order->paid_at)) ? null : Carbon::parse($order->paid_at)->format('Y-m-d H:i'); //請款成功時間
        $T05 = null; //出貨單 出貨時間 (出貨確認)
        // 金流單
        if ($order->status_code == 'CANCELLED' || $order->status_code == 'VOIDED') {
            $payment = OrderPayment::select("latest_api_date")
                ->where('source_table_name', 'orders')
                ->where('payment_type', 'REFUND')
                ->where('payment_status', 'COMPLETED')
                ->where('order_no', $order->order_no)
                ->first();
            $T03 = isset($payment->latest_api_date) ? Carbon::parse($payment->latest_api_date)->format('Y-m-d H:i') : $T03;
        }
        // 出貨貨態
        $shipments = $this->getShipmentDetailByOrderNo($order->order_no);
        if (isset($shipments)) {
            foreach ($shipments as $shipment) {
                $T01 = Carbon::parse($shipment->shipment_date)->format('Y-m-d H:i');
                $T02 = ($shipment->voided_at) ? Carbon::parse($shipment->voided_at)->format('Y-m-d H:i') : Carbon::parse($shipment->cancelled_at)->format('Y-m-d H:i');
                $T05 = ($shipment->shipped_at) ? Carbon::parse($shipment->shipped_at)->format('Y-m-d H:i') : null;
                $T06 = ($shipment->delivered_at) ? Carbon::parse($shipment->delivered_at)->format('Y-m-d H:i') : null; //出貨單 配達時間
                $T07 = ($shipment->overdue_confirmed_at) ? Carbon::parse($shipment->overdue_confirmed_at)->format('Y-m-d H:i') : null; //出貨單 配送異常時間
                $status[$shipment->new_order_detail_id][$shipment->product_item_id] = [
                    "order_status" => $order->status_code,
                    "payment_status" => $order->pay_status,
                    "shipment_status" => $shipment->status_code,
                    "package_no" => $shipment->package_no,
                    "T01" => $T01,
                    "T02" => $T02,
                    "T03" => $T03,
                    "T04" => $T04,
                    "T05" => $T05,
                    "T06" => $T06,
                    "T07" => $T07
                ];
            }
            //重新組合
            foreach ($status as $order_detail_id => $shipment_detail) {
                $info_array = [];
                $show_array = [];
                foreach ($shipment_detail as $item_id => $detail) {
                    //$ship_return = true($detail['T06'] ? true : false);
                    $info_array[] = [
                        'number_desc' => '配送單號',
                        'number' => $detail['package_no']
                    ];
                    $show_array[] = [
                        "status_desc" => "訂單成立",
                        "status_time" => $detail['T01'],
                        "status_display" => true
                    ];
                    if ($detail['order_status'] == 'CANCELLED' || $detail['order_status'] == 'VOIDED') {
                        $show_array[] = [
                            "status_desc" => "已取消",
                            "status_time" => $detail['T02'],
                            "status_display" => false
                        ];
                        if ($detail['T03']) {
                            $show_array[] = [
                                "status_desc" => "已退款",
                                "status_time" => $detail['T03'],
                                "status_display" => false
                            ];
                        }
                    } else {
                        $show_array[] = [
                            "status_desc" => "待出貨",
                            "status_time" => $detail['T04'],
                            "status_display" => true
                        ];
                        $show_array[] = [
                            "status_desc" => "出貨中",
                            "status_time" => $detail['T05'],
                            "status_display" => true
                        ];
                        if ($detail['payment_status'] == 'COMPLETED') {
                            if (isset($detail['T07'])) {
                                $show_array[] = [
                                    "status_desc" => "配送失敗",
                                    "status_time" => $detail['T07'],
                                    "status_display" => true
                                ];
                            } else {
                                $show_array[] = [
                                    "status_desc" => "已到貨",
                                    "status_time" => $detail['T06'],
                                    "status_display" => true
                                ];
                            }
                        } else {
                            $show_array[] = [
                                "status_desc" => "已到貨",
                                "status_time" => $detail['T06'],
                                "status_display" => true
                            ];
                        }
                    }
                    $shipment_status['shipped_info'][$order_detail_id][$item_id] = $info_array;
                    $shipment_status['shipped_status'][$order_detail_id][$item_id] = $show_array;
                    $shipment_status['can_return'][$order_detail_id][$item_id] = true;
                }
            }
        }

        // 退貨貨態
        $return_examination_info = $this->getReturnExaminationsByOrderNo($order->order_no);
        //可否申請退貨 B
        if ($can_return_order['type'] == 2 && $return_examination_info->count() == 0) { //至少一張出貨單配達、尚有出貨單未配達，沒有退貨檢驗單
            $shipment_status['can_return_order']['type'] = 3;
            $shipment_status['can_return_order']['status'] = true;
        } else {
            $shipment_status['can_return_order']['type'] = $can_return_order['type'];
            $shipment_status['can_return_order']['status'] = $can_return_order['status'];
        }

        if (isset($return_examination_info)) {
            $voided_count = 0;
            $exam_count = 0;
            foreach ($return_examination_info as $return_detail) {
                $return_type = false;
                if ($return_detail->return_requests_status != 'COMPLETED' && $return_detail->return_requests_status != 'VOIDED') { //未結案，退貨取消
                    $exam_count++;
                }
                if ($return_detail->return_requests_status == 'VOIDED') { //退貨取消，狀態回復出貨狀態
                    $voided_count++;
                    continue;
                }
                $T21 = ($return_detail->created_at) ? Carbon::parse($return_detail->created_at)->format('Y-m-d H:i') : null;//退貨檢驗單 產生時間
                if ($order->ship_from_whs == 'SELF') {
                    $T22 = ($return_detail->lgst_dispatched_at) ? Carbon::parse($return_detail->lgst_dispatched_at)->format('Y-m-d H:i') : null;//拋轉秋雨時間
                } else {
                    $T22 = ($return_detail->lgst_dispatched_at) ? Carbon::parse($return_detail->lgst_dispatched_at)->format('Y-m-d H:i') : null;//退貨檢驗單 派車時間
                }
                $T23 = ($return_detail->returnable_confirmed_at) ? Carbon::parse($return_detail->returnable_confirmed_at)->format('Y-m-d H:i') : null;//退貨檢驗單檢驗回報時間
                $T24 = ($return_detail->refund_at) ? Carbon::parse($return_detail->refund_at)->format('Y-m-d H:i') : null;//退款成功時間 / 退款失敗時間
                $T25 = ($return_detail->examination_reported_at) ? Carbon::parse($return_detail->examination_reported_at)->format('Y-m-d H:i') : null;//退貨檢驗單 檢驗異常時間
                $req_mobile = isset($return_detail->req_mobile) ? substr($return_detail->req_mobile, 0, 7) . '***' : "";
                $return_status[$return_detail->new_order_detail_id][$return_detail->product_item_id] = [
                    "can_return" => $return_type,
                    "status_code" => $return_detail->examinations_status,
                    "is_returnable" => $return_detail->is_returnable,
                    "examination_no" => $return_detail->examination_no,
                    "req_name" => $this->privacyCode($return_detail->req_name),
                    "req_mobile" => $req_mobile,
                    "req_address" => $return_detail->req_address,
                    "T21" => $T21,
                    "T22" => $T22,
                    "T23" => $T23,
                    "T24" => $T24,
                    "T25" => $T25
                ];
            }
            //重新組合for前端
            if (isset($return_status)) {
                foreach ($return_status as $order_detail_id => $examination_detail) {
                    $info_array = [];
                    $show_array = [];
                    foreach ($examination_detail as $item_id => $detail) {
                        if ($detail['status_code'] == 'VOIDED') continue; //退貨檢驗作廢，狀態回復出貨狀態
                        $info_array[] = [
                            'number_desc' => '退貨單號',
                            'number' => $detail['examination_no'],
                            'req_name' => $detail['req_name'],
                            'req_mobile' => $detail['req_mobile'],
                            'req_address' => $detail['req_address']
                        ];
                        $show_array[] = [
                            "status_desc" => "退貨成立",
                            "status_time" => $detail['T21'],
                            "status_display" => false
                        ];
                        $show_array[] = [
                            "status_desc" => "派車回收",
                            "status_time" => $detail['T22'],
                            "status_display" => false
                        ];

                        if (($detail['status_code'] == 'M_CLOSED' && $detail['is_returnable'] === 0)) {
                            $show_array[] = [
                                "status_desc" => "退貨失敗",
                                "status_time" => $detail['T23'],
                                "status_display" => false
                            ];
                        } else if ($detail['status_code'] == 'FAILED') {
                            $show_array[] = [
                                "status_desc" => "退貨處理中",
                                "status_time" => $detail['T25'],
                                "status_display" => false
                            ];
                        } else if ($detail['status_code'] == 'FAILED' && $detail['is_returnable'] === 1) {
                            $show_array[] = [
                                "status_desc" => "退貨完成",
                                "status_time" => $detail['T23'],
                                "status_display" => false
                            ];
                            $show_array[] = [
                                "status_desc" => "退款失敗",
                                "status_time" => $detail['T24'],
                                "status_display" => false
                            ];
                        } else {
                            $show_array[] = [
                                "status_desc" => "退貨完成",
                                "status_time" => $detail['T23'],
                                "status_display" => false
                            ];
                            $show_array[] = [
                                "status_desc" => "已退款",
                                "status_time" => $detail['T24'],
                                "status_display" => false
                            ];
                        }
                        $shipment_status['shipped_info'][$order_detail_id][$item_id] = $info_array;
                        $shipment_status['shipped_status'][$order_detail_id][$item_id] = $show_array;
                        $shipment_status['can_return'][$order_detail_id][$item_id] = false;
                    }
                }
            }
            if ($can_return_order['type'] == 2 && $return_examination_info->count() == $voided_count) { //已取消退貨 (進入挑品頁)
                $shipment_status['can_return_order']['type'] = 3;
                $shipment_status['can_return_order']['status'] = true;
            } elseif ($exam_count > 0) { //有退貨申請未完成的
                $shipment_status['can_return_order']['type'] = 2;
                $shipment_status['can_return_order']['status'] = false;
            }
        }

        if (!isset($shipment_status)) {
            $shipment_status['shipped_info'] = null;
            $shipment_status['shipped_status'] = null;
            $shipment_status['can_return'] = false;
        }
        return $shipment_status;
    }

    /**
     * 檢查會員訂單前一版訂單
     *
     * @param string $orderNo
     * @return Model|null
     */
    public function getMemberPreRevisionByOrderNo(string $orderNo, $vision): ?Model
    {
        $vision_no = ($vision - 1);
        $member = auth('api')->user();
        $order = Order::where('member_id', $member->member_id)
            ->where('order_no', $orderNo)
            ->where('revision_no', $vision_no)
            ->select('revision_no', 'refund_status', 'total_amount', 'shipping_fee', 'point_discount', 'cart_campaign_discount', 'points', 'paid_amount', 'fee_of_instal')
            ->first();
        return $order;
    }

    /*
     * 姓名個資隱碼
     * Author: Rowena
     * Return: string
     */
    public function privacyCode($params = null)
    {
        $len = strlen($params);
        if ($len == 0) return "";
        $str = "";
        if ($len < 3) {
            $str = '*' . mb_substr($params, -1);
        } elseif ($len == 3) {
            $str = '*' . mb_substr($params, 1, 1) . '*';
        } else {
            $str = '*' . mb_substr($params, 1, ($len - 1)) . '*';
        }
        return $str;
    }

    /*
     * 依訂單編號找出退貨貨態
     * param string $orderNo
     * Author: Rowena
     * Return: string
     */
    public function getReturnExaminationsByOrderNo(string $orderNo)
    {
        $data = Order::select('return_examinations.status_code as examinations_status', 'return_examinations.is_returnable', 'return_examinations.examination_no'
            , 'return_requests.status_code as return_requests_status', 'return_requests.req_name', 'return_requests.req_mobile', DB::raw('concat(return_requests.req_city, return_requests.req_district, return_requests.req_address) as req_address')
            , 'return_examinations.created_at', 'return_examinations.lgst_dispatched_at', 'return_examinations.returnable_confirmed_at', 'return_examinations.examination_reported_at'
            , 'return_requests.refund_at', 'return_request_details.product_item_id', 'order_details.id as new_order_detail_id', 'return_request_details.order_detail_id as ori_order_detail_id'
            , 'return_request_details.product_item_id', 'order_details.id as new_order_detail_id', 'return_request_details.order_detail_id as ori_order_detail_id')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('return_requests', 'return_requests.order_no', 'orders.order_no')
            ->join("return_request_details", function ($join) {
                $join->on('return_request_details.return_request_id', 'return_requests.id')
                    ->on('return_request_details.order_detail_seq', 'order_details.seq');
            })
            ->join('return_examinations', 'return_examinations.return_request_id', 'return_requests.id')
            ->join("return_examination_details", function ($join) {
                $join->on('return_examination_details.return_examination_id', 'return_examinations.id')
                    ->on('return_examination_details.return_request_detail_id', 'return_request_details.id');
            })
            ->where('orders.order_no', $orderNo)
            ->where('orders.is_latest', 1)
            ->get();
        return $data;
    }

    /**
     * 是否可以申請退貨
     *
     * @param string $status_code 訂單狀態
     * @param string|null $delivered_at 商品配達時間
     * @param string|null $cooling_off_due_date 鑑賞期截止時間
     * @param integer|null $return_request_id 退貨申請單id
     * @return array
     */
    public function canReturnOrderV2(string $status_code, ?string $delivered_at, ?string $cooling_off_due_date, ?int $return_request_id): array
    {
        $now = Carbon::now();
        $cooling_off_due_date = ($cooling_off_due_date) ? Carbon::parse($cooling_off_due_date) : null;
        $data = [];
        if ($status_code == 'PROCESSING' && !isset($cooling_off_due_date)) { //出貨準備中，無出貨單配達
            $data['status'] = true;
            $data['type'] = 1;
        } elseif ($status_code == 'PROCESSING' && isset($cooling_off_due_date)) { //至少一張出貨單配達、尚有出貨單未配達
            $data['status'] = true;
            $data['type'] = 2;
        } elseif ($status_code == 'CLOSED' && !$now->greaterThan($cooling_off_due_date)) { //出貨單全數配達，且未超過鑑賞期
            $data['status'] = true;
            $data['type'] = 3;
        } else {
            $data['status'] = false;
            $data['type'] = 0;
        }
        return $data;
    }

    /*
     * 前台退貨申請
     * param $order, $request
     * Author: Rowena
     * Return: string
     */
    public function setReturnByOrderNo($order, $request, $requestNo)
    {
        $now = Carbon::now();
        //取供應商ID
        $product_with = [];
        $supplier_info = ProductItem::with('product:id,supplier_id')->get();
        foreach ($supplier_info as $supplier) {
            $product_with['supplier_id'][$supplier->id] = $supplier->product->supplier_id;
        }
        $product_with['ship_from_whs'] = $order->ship_from_whs;
        DB::beginTransaction();
        try {
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
                'ship_from_whs' => $order->ship_from_whs,
                'created_by' => -1,
                'updated_by' => -1,
            ]);
            // 訂單明細
            if ($order->orderDetails->isNotEmpty()) {
                $returnable['amount'] = [];
                $returnable['points'] = [];
                $returnable['point_discount'] = [];
                $returnable['item_id'] = [];
                $returnable['detail_id'] = [];
                $returnable['giveaway'] = [];
                $returnable['main_product'] = [];
                $returnable['return_id'] = $request->return_id;
                $order->orderDetails->each(function ($orderDetail) use (&$returnable) {
                    if ($orderDetail->record_identity == "G") { //找出贈品
                        $returnable['giveaway'][$orderDetail->main_product_id][] = $orderDetail->id;
                    }
                });
                $order->orderDetails->each(function ($orderDetail) use ($returnRequest, &$request, &$returnable, &$product_with) {
                    if (in_array($orderDetail->id, $returnable['return_id'])) {
                        if (isset($returnable['giveaway'][$orderDetail->product_id])) {
                            foreach ($returnable['giveaway'][$orderDetail->product_id] as $give_key => $give_value) {
                                if (!in_array($returnable['giveaway'][$orderDetail->product_id][$give_key], $returnable['return_id'])) {//漏傳單品贈
                                    $returnable['return_id'][] = $give_value;
                                }
                            }
                        }
                        $supplier = ($product_with['ship_from_whs'] == 'SUP') ? $product_with['supplier_id'][$orderDetail->product_item_id] : 0;
                        if (!key_exists($supplier, $returnable['amount'])) $returnable['amount'][$supplier] = 0;
                        if (!key_exists($supplier, $returnable['points'])) $returnable['points'][$supplier] = 0;
                        if (!key_exists($supplier, $returnable['point_discount'])) $returnable['point_discount'][$supplier] = 0;
                        if (!key_exists($supplier, $returnable['item_id'])) $returnable['item_id'][$supplier] = 0;
                        // 新增退貨申請單明細
                        $return_request_detail = ReturnRequestDetail::create([
                            'return_request_id' => $returnRequest->id,
                            'seq' => $orderDetail->seq,
                            'order_detail_id' => $orderDetail->id,
                            'product_item_id' => $orderDetail->product_item_id,
                            'item_no' => $orderDetail->item_no,
                            'request_qty' => $orderDetail->qty,
                            'passed_qty' => 0,
                            'failed_qty' => 0,
                            'selling_price' => $orderDetail->selling_price,
                            'unit_price' => $orderDetail->unit_price,
                            'campaign_discount' => $orderDetail->campaign_discount,
                            'cart_p_discount' => $orderDetail->cart_p_discount,
                            'subtotal' => $orderDetail->subtotal,
                            'point_discount' => $orderDetail->point_discount,
                            'points' => $orderDetail->points,
                            'record_identity' => $orderDetail->record_identity,
                            'purchase_price' => $orderDetail->purchase_price,
                            'order_detail_seq' => $orderDetail->seq,
                            'created_by' => -1,
                            'updated_by' => -1,
                        ]);
                        $returnable['amount'][$supplier] += ($orderDetail->subtotal + $orderDetail->point_discount);
                        $returnable['points'][$supplier] += $orderDetail->points;
                        $returnable['point_discount'][$supplier] += $orderDetail->point_discount;
                        $returnable['item_id'][$orderDetail->product_item_id] = $supplier;
                        $returnable['detail_id'][$orderDetail->product_item_id] = $return_request_detail->id;
                    }
                });
            }
            if (count($returnable['amount']) == 0) {
                $result['status'] = 401;
                $result['message'] = '資料錯誤';
                $result['results'] = [];
                return $result;
            }
            $msg['examination'] = [];
            $msg['examination_count'] = [];
            $exam_payload = [];
            foreach ($returnable['amount'] as $supplier_id => $returnableVal) {
                // 新增退貨檢驗單
                $random_string = Str::upper(Str::random(6));
                $examination_no = 'RX' . $now->format('ymd') . $random_string;
                $returnExamination = ReturnExamination::create([
                    'return_request_id' => $returnRequest->id,
                    'examination_no' => $examination_no,
                    'request_no' => $requestNo,
                    'supplier_id' => $supplier_id,
                    'status_code' => 'CREATED',
                    'lgst_dispatched_deadline' => Carbon::parse($returnRequest->create_at)->addWeekday(2)->format('Y-m-d 23:59:59'),
                    'examination_deadline' => Carbon::parse($returnRequest->create_at)->addWeekday(7)->format('Y-m-d 23:59:59'),
                    'returnable_amount' => ($returnable['amount'][$supplier_id] * -1),
                    'returnable_points' => $returnable['points'][$supplier_id],
                    'returnable_point_discount' => $returnable['point_discount'][$supplier_id],
                    'created_by' => -1,
                    'updated_by' => -1,
                ]);
                $msg['examination_count'][] = [
                    'examination_no' => $examination_no,
                ];
                // 退貨檢驗單明細
                if ($order->orderDetails->isNotEmpty()) {
                    $order->orderDetails->each(function ($orderDetail) use ($returnExamination, &$request, &$returnable, &$supplier_id, &$msg) {
                        if (in_array($orderDetail->id, $returnable['return_id'])) {
                            if ($returnable['item_id'][$orderDetail->product_item_id] == $supplier_id) {
                                // 新增退貨檢驗單明細
                                $returnExaminationDetail = ReturnExaminationDetail::create([
                                    'return_examination_id' => $returnExamination->id,
                                    'return_request_detail_id' => $returnable['detail_id'][$orderDetail->product_item_id],
                                    'product_item_id' => $orderDetail->product_item_id,
                                    'item_no' => $orderDetail->item_no,
                                    'request_qty' => $orderDetail->qty,
                                    'passed_qty' => 0,
                                    'failed_qty' => 0,
                                    'created_by' => -1,
                                    'updated_by' => -1,
                                ]);
                                $msg['examination'][$returnExamination->examination_no][] = [
                                    'id' => $orderDetail->id,
                                    'product_name' => $orderDetail->product->product_name,
                                    'spec_1_value' => ($orderDetail->product->spec_dimension > 0 ? $orderDetail->productItem->spec_1_value : ''),
                                    'spec_2_value' => ($orderDetail->product->spec_dimension > 1 ? $orderDetail->productItem->spec_2_value : ''),
                                    'return_qty' => $orderDetail->qty,
                                    'record_identity' => $orderDetail->record_identity
                                ];
                            }
                        }
                    });
                }
                //依主從商品排序
                array_multisort(array_column($msg['examination'][$returnExamination->examination_no], 'record_identity'), SORT_DESC,$msg['examination'][$returnExamination->examination_no]);
            }
            // 更新訂單
            Order::findOrFail($order->id)
                ->update([
                    'return_request_id' => $returnRequest->id,
                    'updated_by' => -1,
                ]);
            $result['status'] = 200;
            $result['message'] = '訂單退貨成功';
            $result['results']['return_count'] = count($msg['examination_count']);
            $result['results']['return_data'] = $msg['examination'];
            $result['results']['return_date'] = now()->format('Y-m-d H:i:s');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $result['status'] = 500;
            $result['message'] = '其他錯誤';
            $result['results'] = [];
        }
        return $result;
    }


    /*
     * 依訂單編號找出出貨貨態
     * param string $orderNo
     * Author: Rowena
     * Return: string
     */
    public function getShipmentDetailByOrderNo(string $orderNo)
    {
        $data = Order::select('shipments.package_no', 'shipments.status_code', 'shipments.shipment_date', 'shipments.voided_at', 'shipments.cancelled_at', 'shipments.shipped_at', 'shipments.delivered_at', 'shipments.overdue_confirmed_at'
            , 'shipment_details.product_item_id', 'order_details.id as new_order_detail_id', 'shipment_details.order_detail_id as ori_order_detail_id')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('shipments', 'shipments.order_no', 'orders.order_no')
            ->join("shipment_details", function ($join) {
                $join->on('shipment_details.shipment_id', 'shipments.id')
                    ->on('shipment_details.order_detail_seq', 'order_details.seq');
            })
            ->where('orders.order_no', $orderNo)
            ->where('orders.is_latest', 1)
            ->get();
        return $data;
    }
}
