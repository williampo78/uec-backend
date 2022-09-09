<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\ReturnRequest;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;
use function PHPUnit\Framework\isEmpty;

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
    public function addDiscountsToOrder($orders, $giveaway_qty = [])
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
                            'productPhoto' => $productPhoto,
                            'productPhoto' => $photo_name,
                            'productId' => $obj->product->id,
                            'productName' => $obj->product->product_name,
                            'assignedQty' => $giveaway_qty[$obj->order_detail_id] ?? 0,
                            'spec_1_value' => optional($obj->productItem)->spec_1_value,
                            'spec_2_value' => optional($obj->productItem)->spec_2_value
                        ]; //贈送的商品列表
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
                        $thresholdAmount += $obj->discount;
                    }
                    break;
            }

        }

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
                    $OrderDetails = OrderDetail::select(['id', 'order_id', 'product_id', 'qty'])
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
                                    'qty' => optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty,
                                    'spec_1_value' => optional($PRD->productItem)->spec_1_value,
                                    'spec_2_value' => optional($PRD->productItem)->spec_2_value
                                ],
                            ],
                        ];
                    } else {
                        $order_details[$key]['discount_content'][$PRD->group_seq]['campaignProdList'][] = [
                            'productId' => $PRD->product->id,
                            'productName' => $PRD->product->product_name,
                            'productPhoto' => $photo_name,
                            'qty' => optional($OrderDetails->where('id', $PRD->order_detail_id)->first())->qty,
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
    public function getShippedStatus($order): array
    {
        $status = [];
        $return_status = [];
        // 有退貨申請單
        if (count($order->returnRequests) > 0) {
            $return_examination_info = $this->getReturnExaminationsByOrderNo($order->order_no);
            foreach ($order->returnRequests as $returnRequest) {
                if (isset($return_examination_info)) {
                    foreach ($return_examination_info as $return_detail) {
                        $T21 = (is_null($returnRequest->created_at)) ? null : Carbon::parse($returnRequest->created_at)->format('Y-m-d H:i');//退貨檢驗單 產生時間
                        if ($order->ship_from_whs == 'SELF') {
                            $T22 = (is_null($returnRequest->lgst_dispatched_at)) ? null : Carbon::parse($returnRequest->lgst_dispatched_at)->format('Y-m-d H:i');//拋轉秋雨時間
                        } else {
                            $T22 = (is_null($returnRequest->lgst_dispatched_at)) ? null : Carbon::parse($returnRequest->lgst_dispatched_at)->format('Y-m-d H:i');//退貨檢驗單 派車時間
                        }
                        $T23 = (is_null($returnRequest->returnable_confirmed_at)) ? null : Carbon::parse($returnRequest->returnable_confirmed_at)->format('Y-m-d H:i');//退貨檢驗單檢驗回報時間
                        $T24 = (is_null($returnRequest->refund_at)) ? null : Carbon::parse($returnRequest->refund_at)->format('Y-m-d H:i');//退款成功時間 / 退款失敗時間
                        $T25 = (is_null($returnRequest->examination_reported_at)) ? null : Carbon::parse($returnRequest->examination_reported_at)->format('Y-m-d H:i');//退貨檢驗單 檢驗異常時間
                        $req_mobile = isset($returnRequest->req_mobile) ? substr($returnRequest->req_mobile, 0, 7) . '***' : "";
                        $status[$return_detail->order_detail_id][$return_detail->product_item_id] = [
                            "status_code" => $return_detail->examinations_status,
                            "is_returnable" => $return_detail->is_returnable,
                            "examination_no" => $returnRequest->examination_no,
                            "req_name" => $this->privacyCode($returnRequest->req_name),
                            "req_mobile" => $req_mobile,
                            "req_address" => $returnRequest->req_city . $returnRequest->req_district . $returnRequest->req_address,
                            "T21" => $T21,
                            "T22" => $T22,
                            "T23" => $T23,
                            "T24" => $T24,
                            "T25" => $T25
                        ];
                    }
                }
            }
            //重新組合for前端
            if (isset($status)) {
                foreach ($status as $order_detail_id => $examination_detail) {
                    $info_array =[];
                    $show_array = [];
                    foreach ($examination_detail as $item_id => $detail) {
                        $info_array[] = [
                            'number_desc' => '退貨單號',
                            'number' => $detail['examination_no'],
                            'req_name' =>$detail['req_name'],
                            'req_mobile'=>$detail['req_mobile'],
                            'req_address'=>$detail['req_address']
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
                        if ($detail['status_code'] == 'VOIDED' || ($detail['status_code'] == 'M_CLOSED' && $detail['is_returnable'] === 0)) {
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
                        $return_status['shipped_info'][$order_detail_id][$item_id] = $info_array;
                        $return_status['shipped_status'][$order_detail_id][$item_id] = $show_array;
                    }
                }
            }
            return $return_status;
        } else {
            $T01 = null; //出貨單 產生時間
            $T02 = null; //出貨單 取消時間/作廢時間
            $T03 = null; //退款成功時間
            $T04 = (is_null($order->paid_at)) ? null : Carbon::parse($order->paid_at)->format('Y-m-d H:i'); //請款成功時間
            $T05 = null; //出貨單 出貨時間 (出貨確認)
            $T06 = (is_null($order->delivered_at)) ? null : Carbon::parse($order->delivered_at)->format('Y-m-d H:i'); //出貨單 配達時間
            $T07 = (is_null($order->overdue_confirmed_at)) ? null : Carbon::parse($order->overdue_confirmed_at)->format('Y-m-d H:i'); //出貨單 配送異常時間
            // 金流單
            if ($order->status_code == 'CANCELLED' || $order->status_code == 'VOIDED') {
                $payment = OrderPayment::select("latest_api_date")
                    ->where('source_table_name', 'return_requests')
                    ->where('payment_type', 'REFUND')
                    ->where('payment_status', 'COMPLETED')
                    ->where('order_no', $order->order_no)
                    ->first();
                $T03 = isset($payment->latest_api_date) ? Carbon::parse($payment->latest_api_date)->format('Y-m-d H:i') : $T03;
            }
            if ($order->shipments->isNotEmpty()) {
                //出貨單
                foreach ($order->shipments as $detail) {
                    $T01 = Carbon::parse($detail->shipment_date)->format('Y-m-d H:i');
                    $T02 = !is_null($detail->voided_at) ? Carbon::parse($detail->voided_at)->format('Y-m-d H:i') : Carbon::parse($detail->cancelled_at)->format('Y-m-d H:i');
                    $T05 = (is_null($detail->shipped_at)) ? $T05 : Carbon::parse($detail->shipped_at)->format('Y-m-d H:i');
                    $shipments = Shipment::with('shipmentDetails')->where('id', $detail->id)->get();
                    foreach ($shipments as $shipment) {
                        foreach ($shipment->shipmentDetails as $shipment_detail) {
                            $status[$shipment_detail->order_detail_id][$shipment_detail->product_item_id] = [
                                "order_status" => $order->status_code,
                                "payment_status" => $order->pay_status,
                                "shipment_status" => $detail->status_code,
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
                    }
                }
                //重新組合
                foreach ($status as $order_detail_id => $shipment_detail) {
                    $info_array =[];
                    $show_array = [];
                    foreach ($shipment_detail as $item_id => $detail) {
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
                            if ($detail['payment_status'] == 'COMPLETED') {
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
                    }
                }
            } else {
                $shipment_status['shipped_info'] = null;
                $shipment_status['shipped_status'] = null;
            }
            return $shipment_status;
        }
    }

    /**
     * 檢查會員訂單前一版訂單
     *
     * @param string $orderNo
     * @return Model|null
     */
    public function getMemberPreRevisionByOrderNo(string $orderNo,  $vision): ?Model
    {
        $vision_no = ($vision-1);
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
        $data = ReturnRequest::select('return_requests.request_no', 'return_requests.status_code as return_status'
            , 'order_details.id as order_detail_id', 'order_details.product_item_id'
            , 'return_examinations.*', 'return_examinations.status_code as examinations_status')
            ->join('order_details','order_details.order_id','return_requests.new_order_id')
            ->Leftjoin('return_request_details', 'return_request_details.return_request_id', 'return_requests.id')
            ->Leftjoin('return_examinations', 'return_examinations.return_request_id', 'return_requests.id')
            ->where('return_requests.order_no', $orderNo)->where('return_request_details.record_identity','M')->get();
        return $data;
    }

}
