<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

class ReturnGoodsService
{
    private $returnRequest;
    private $returnRequestEntity;
    private $orderEntity;
    private $orderNo;
    private $newOrder;
    private $oldOrder;
    private $requestNo;
    private $returnExamination;
    private $returnRequestDetails;
    private $oldOrderDetails;
    private $oldOrderCampaignDiscounts;
    private $createOrderDetailData;
    private $createReturnOrderDetailData;
    private $newOrderDetails;
    private $newOrderCampaignDiscounts;
    private $oldOrderPayment;
    private array $orderDetailIdToNewId = [];
    private array $verifyResult = [];
    private array $params = [];
    private array $updateReturnRequestData = [];

    public function __construct(
        ReturnRequest $returnRequestEntity,
        Order $orderEntity
    )
    {
        $this->returnRequestEntity         = $returnRequestEntity;
        $this->orderEntity                 = $orderEntity;
        $this->newOrderDetails             = collect();
        $this->newOrderCampaignDiscounts   = collect();
        $this->createReturnOrderDetailData = collect();
        $this->oldOrderCampaignDiscounts   = collect();
    }

    /**
     * @return int
     * @Author: Eric
     * @DateTime: 2022/10/3 下午 04:11
     */
    private function getUserId(): int
    {
        return Auth()->check() ? Auth()->user()->id : -1;
    }

    /**
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:05
     */
    private function getReturnRequest()
    {
        $this->returnRequest = $this->returnRequestEntity
            ->with([
                'returnExamination.returnExaminationDetails',
                'returnRequestDetails'
            ])
            ->find($this->params['return_request_id']);

        $this->returnExamination    = optional($this->returnRequest)->returnExamination;
        $this->returnRequestDetails = optional($this->returnRequest)->returnRequestDetails;
        $this->orderNo              = optional($this->returnRequest)->order_no;
        $this->requestNo            = optional($this->returnRequest)->request_no;
    }

    /**
     * 取得最新的訂單
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:56
     */
    private function getLatestOrder()
    {
        $this->oldOrder = $this->orderEntity
            ->with([
                'orderDetails',
                'orderCampaignDiscounts',
                'orderPayments' => function ($query) {
                    $query->where('payment_type', 'PAY')
                        ->orderBy('id', 'desc');
                }
            ])
            ->where('order_no', $this->orderNo)
            ->where('is_latest', 1)
            ->first();

        $this->oldOrderDetails           = optional($this->oldOrder)->orderDetails;
        $this->oldOrderCampaignDiscounts = optional($this->oldOrder)->orderCampaignDiscounts;
        //最新一筆請款資料

        $this->oldOrderPayment = optional(optional($this->oldOrder)->orderPayments)->first();
    }

    /**
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:47
     */
    private function verify()
    {
        if (empty($this->returnRequest)) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E401',
                'http_status_code' => 400,
                'message'          => '更新失敗，退貨申請單不存在'
            ];
            return;
        }

        if ($this->returnRequest->status_code == 'VOIDED') {
            $this->verifyResult = [
                'status'           => true,
                'code'             => 'S201',
                'http_status_code' => 200,
                'message'          => '更新失敗，此退貨申請單已作廢'
            ];
            return;
        }

        if (is_null($this->returnRequest->new_order_id) === false) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E402',
                'http_status_code' => 400,
                'message'          => '更新失敗，退貨申請單不允許重複退款'
            ];
            return;
        }

        if ($this->returnExamination->isEmpty()) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E403',
                'http_status_code' => 400,
                'message'          => '更新失敗，退貨檢驗單不存在'
            ];
            return;
        }

        if ($this->returnExamination->whereNull('is_returnable')->isNotEmpty()) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E404',
                'http_status_code' => 400,
                'message'          => '更新失敗，退貨檢驗單有未確認的資料'
            ];
            return;
        }

        if (empty($this->oldOrder)) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E405',
                'http_status_code' => 400,
                'message'          => '更新失敗，訂單不存在'
            ];
            return;
        }

        if ($this->oldOrderDetails->isEmpty()) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E406',
                'http_status_code' => 400,
                'message'          => '更新失敗，訂單詳細資料不存在'
            ];
            return;
        }

        if (empty($this->oldOrderPayment)) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E407',
                'http_status_code' => 400,
                'message'          => '更新失敗，訂單金流單不存在'
            ];
            return;
        }

        $this->verifyResult = [
            'status'           => true,
            'code'             => 'S200',
            'http_status_code' => 200,
            'message'          => '更新成功'
        ];
    }

    /**
     * 正負轉換，防止0.0 * (-1) = -0
     * @param $number
     * @return float|int|mixed
     * @Author: Eric
     * @DateTime: 2022/9/27 上午 11:38
     */
    private function invertSign($number)
    {
        return $number == 0 ? $number : $number * (-1);
    }

    /**
     * 更新退貨申請單
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:41
     */
    private function updateReturnRequest()
    {
        $this->updateReturnRequestData['new_order_id'] = $this->newOrder->id;

        $this->returnRequest
            ->update($this->updateReturnRequestData);
    }

    /**
     * 更新檢驗單
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:43
     */
    private function updateReturnExamination()
    {
        //退貨申請單金額為空
        if (empty($this->returnRequest->refund_amount)) {
            $this->returnExamination->each(function ($returnExamination) {
                if ($returnExamination->is_returnable == 1) {
                    $returnExamination->update([
                        'status_code' => $returnExamination->is_examination_passed == 0 ? 'M_CLOSED' : 'CLOSED'
                    ]);
                }
            });
        }
    }

    /**
     * 更新訂單
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:43
     */
    private function updateOrder()
    {
        $this->orderEntity
            ->where('order_no', $this->orderNo)
            ->update([
                'is_latest' => 0
            ]);
    }

    /**
     * 產生訂單
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:43
     */
    private function createOrder()
    {
        //從舊單複製資料
        $this->newOrder = $this->oldOrder->replicate();
        $totalAmount = $this->createOrderDetailData->sum('subtotal');
        $pointDiscount = $this->createOrderDetailData->sum('point_discount');
        $paidAmount = $totalAmount + $this->newOrder->cart_campaign_discount + $this->newOrder->cart_p_discount + $pointDiscount + $this->newOrder->shipping_fee;

        //與舊單不同的資料
        $createData = [
            //修訂版號
            'revision_no'              => $this->newOrder->revision_no + 1,
            //最新版本
            'is_latest'                => 1,
            //商品原總計，sum(單身的小計)
            'total_amount'             => $totalAmount,
            //點數折扣
            'point_discount'           => $pointDiscount,
            //實際付款金額
            'paid_amount'              => $paidAmount,
            //使用點數
            'points'                   => $this->createOrderDetailData->sum('points'),
            //已退購物車滿額折扣
            'returned_cart_p_discount' => 0,
            //已退點數折扣
            'returned_point_discount'  => $this->createOrderDetailData->sum('returned_point_discount'),
            //已退付款金額
            'returned_paid_amount'     => $this->newOrder->returned_paid_amount + $paidAmount - $this->newOrder->paid_amount,
            //已退點數
            'returned_points'          => $this->createOrderDetailData->sum('returned_points'),
            //退款狀態
            'refund_status'            => $this->updateReturnRequestData['refund_status'],
            'created_by'               => $this->getUserId(),
            'updated_by'               => $this->getUserId(),
        ];

        //如果為分期付款
        if ($this->newOrder->payment_method == 'TAPPAY_INSTAL') {

            //金額符合最低分期門檻
            if ($createData['paid_amount'] >= $this->newOrder->min_consumption_of_instal) {
                //手續費
                $createData['fee_of_instal'] = $this->getFee($createData['paid_amount'], $this->newOrder->interest_rate_of_instal);
                //實付金額
                $createData['paid_amount'] = $createData['paid_amount'] + $createData['fee_of_instal'];
                //未達門檻，指定為一次付清
            } else {
                $createData['payment_method']            = 'TAPPAY_CREDITCARD';
                $createData['number_of_instal']          = null;
                $createData['interest_rate_of_instal']   = null;
                $createData['min_consumption_of_instal'] = null;
                $createData['fee_of_instal']             = null;
            }
        }

        //新增order
        $this->newOrder
            ->fill($createData)
            ->save();
    }

    /**
     * 整理新增order_details和return_order_details的資料
     * @Author: Eric
     * @DateTime: 2022/9/27 下午 01:39
     */
    private function handleCreateOrderDetailAndReturnOrderDetailData()
    {
        $this->createOrderDetailData = $this->oldOrderDetails->map(function ($orderDetail) {

            //取得被退貨的項目
            $targetReturnRequestDetail = $this->returnRequestDetails
                ->where('order_detail_id', $orderDetail->id)
                ->where('passed_qty', '>', 0)
                ->first();

            //退貨項目，整理新增return_order_details資料
            if (!empty($targetReturnRequestDetail)) {
                //目前退貨會全退，直接設定為0
                $orderDetail->qty               = 0;
                $orderDetail->campaign_discount = 0;
                $orderDetail->subtotal          = 0;
                $orderDetail->point_discount    = 0;
                $orderDetail->points            = 0;
                $orderDetail->cart_p_discount   = 0;
                //與已退相關的項目，會累計舊order_detail的值
                //已退數量
                $orderDetail->returned_qty = $orderDetail->returned_qty + $this->invertSign($targetReturnRequestDetail->passed_qty);
                //已退-單品活動折扣
                $orderDetail->returned_campaign_discount = $orderDetail->returned_campaign_discount + $this->invertSign($targetReturnRequestDetail->campaign_discount);
                //已退-購物車滿額折扣
                $orderDetail->returned_cart_p_discount = $orderDetail->returned_cart_p_discount + $this->invertSign($targetReturnRequestDetail->cart_p_discount);
                //已退-小計
                $orderDetail->returned_subtotal = $orderDetail->returned_subtotal + $this->invertSign($targetReturnRequestDetail->subtotal);
                //已退-點數折抵
                $orderDetail->returned_point_discount = $orderDetail->returned_point_discount + $this->invertSign($targetReturnRequestDetail->point_discount);
                //已退-點數
                $orderDetail->returned_points = $orderDetail->returned_points + $this->invertSign($targetReturnRequestDetail->points);

                $sellingPrice = $orderDetail->selling_price;
                //贈品
                if($orderDetail->record_identity == 'G'){
                    $sellingPrice = 0;
                }

                $subtotal = $this->invertSign($targetReturnRequestDetail->passed_qty * $sellingPrice);
                $returnRequestDetailId = $targetReturnRequestDetail->id;
                //經過協商的項目(return_examinations.nego_reported_at !=null)
                $negotiatedReturnExamination = $this->returnExamination
                    ->first(function ($returnExamination) use ($returnRequestDetailId) {
                        $target = $returnExamination
                            ->returnExaminationDetails
                            ->firstWhere('return_request_detail_id', $returnRequestDetailId);
                        return !empty($returnExamination->nego_reported_at) && !empty($target);
                    });

                //整理新增return_order_details的資料
                $this->createReturnOrderDetailData->push([
                    'request_no'              => $this->requestNo,
                    'order_no'                => $this->orderNo,
                    'data_type'               => 'PRD',
                    'product_item_id'         => $orderDetail->product_item_id,
                    'promotional_campaign_id' => null,
                    'selling_price'           => $sellingPrice,
                    'qty'                     => $this->invertSign($targetReturnRequestDetail->passed_qty),
                    'subtotal'                => $subtotal,
                    'points'                  => $this->invertSign($targetReturnRequestDetail->points),
                    'point_discount'          => $this->invertSign($targetReturnRequestDetail->point_discount),
                    'refund_amount'           => $subtotal + $this->invertSign($targetReturnRequestDetail->point_discount),
                    'is_negotiated'           => empty($negotiatedReturnExamination) ? 0 : 1,
                    'created_by'              => $this->getUserId(),
                    'updated_by'              => $this->getUserId(),
                ]);
            }

            //新增order_details資料
            return [
                //建立order_campaign_discounts資料，需要order_detail_id比對
                'order_detail_id'            => $orderDetail->id,
                'seq'                        => $orderDetail->seq,
                'product_id'                 => $orderDetail->product_id,
                'product_item_id'            => $orderDetail->product_item_id,
                'item_no'                    => $orderDetail->item_no,
                'selling_price'              => $orderDetail->selling_price,
                'qty'                        => $orderDetail->qty,
                'unit_price'                 => $orderDetail->unit_price,
                'campaign_discount'          => $orderDetail->campaign_discount,
                'subtotal'                   => $orderDetail->subtotal,
                'record_identity'            => $orderDetail->record_identity,
                'cart_p_discount'            => $orderDetail->cart_p_discount,
                'point_discount'             => $orderDetail->point_discount,
                'points'                     => $orderDetail->points,
                'utm_source'                 => $orderDetail->utm_source,
                'utm_medium'                 => $orderDetail->utm_medium,
                'utm_campaign'               => $orderDetail->utm_campaign,
                'utm_sales'                  => $orderDetail->utm_sales,
                'utm_time'                   => $orderDetail->utm_time,
                'created_by'                 => $this->getUserId(),
                'updated_by'                 => $this->getUserId(),
                'returned_qty'               => $orderDetail->returned_qty,
                'returned_campaign_discount' => $orderDetail->returned_campaign_discount,
                'returned_subtotal'          => $orderDetail->returned_subtotal,
                'returned_cart_p_discount'   => $orderDetail->returned_cart_p_discount,
                'returned_point_discount'    => $orderDetail->returned_point_discount,
                'returned_points'            => $orderDetail->returned_points,
                'main_product_id'            => $orderDetail->main_product_id,
                'purchase_price'             => $orderDetail->purchase_price,
            ];
        });
    }

    /**
     * 產生訂單詳細
     * @return $this
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:43
     */
    private function createOrderDetail()
    {
        //新增order_details and order_campaign_discounts
        $this->createOrderDetailData->each(function ($orderDetail) {

            $oldOrderDetailId = $orderDetail['order_detail_id'];
            unset($orderDetail['order_detail_id']);

            $newOrderDetail = $this->newOrder
                ->orderDetails()
                ->create($orderDetail);

            $this->newOrderDetails->push($newOrderDetail);

            //舊detail_id對照新detail_id，新增order_campaign_discounts資料需要用到
            $this->orderDetailIdToNewId[$oldOrderDetailId] = $newOrderDetail->id;
        });
    }

    /**
     * 產生折扣資訊
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:44
     */
    private function createOrderCampaignDiscount()
    {
        $this->oldOrderCampaignDiscounts
            ->each(function ($orderCampaignDiscount) {

                //取得舊order_detail_id對應的新id
                $orderDetailId = $this->orderDetailIdToNewId[$orderCampaignDiscount->order_detail_id];
                //對應的order_detail 數量為零，則作廢此項目
                $isVoided = $this->newOrderDetails->where('id', $orderDetailId)->first()->qty == 0 ? 1 : 0;

                $newOrderCampaignDiscount = $this->newOrder
                    ->orderCampaignDiscounts()
                    ->create([
                        'level_code'            => $orderCampaignDiscount->level_code,
                        'group_seq'             => $orderCampaignDiscount->group_seq,
                        'order_detail_id'       => $orderDetailId,
                        'promotion_campaign_id' => $orderCampaignDiscount->promotion_campaign_id,
                        'product_id'            => $orderCampaignDiscount->product_id,
                        'product_item_id'       => $orderCampaignDiscount->product_item_id,
                        'item_no'               => $orderCampaignDiscount->item_no,
                        'discount'              => $orderCampaignDiscount->discount,
                        'record_identity'       => $orderCampaignDiscount->record_identity,
                        'campaign_threshold_id' => $orderCampaignDiscount->campaign_threshold_id,
                        'is_voided'             => $isVoided,
                        'created_by'            => $this->getUserId(),
                        'updated_by'            => $this->getUserId(),
                    ]);
                //如果是此次作廢的資料，才做紀錄
                if ($orderCampaignDiscount->is_voided != $isVoided) {
                    $this->newOrderCampaignDiscounts->push($newOrderCampaignDiscount);
                }
            });
    }

    /**
     * 產生銷退明細
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:44
     */
    private function createReturnOrderDetail()
    {
        //新增商品銷退資料
        $this->createReturnOrderDetailData->each(function ($detail) {

            $this->returnRequest
                ->returnOrderDetails()
                ->create($detail);
        });

        //取得需要新增折扣加收的資料
        $orderCampaignDiscounts = $this->newOrderCampaignDiscounts
            ->where('discount', '!=', 0)
            ->where('is_voided', 1)
            ->groupBy('group_seq');

        //新增折扣加收的資料
        $orderCampaignDiscounts->each(function ($orderCampaignDiscount) {

            $discounts = $orderCampaignDiscount->sum('discount');
            $discounts = $this->invertSign($discounts);

            $this->returnRequest
                ->returnOrderDetails()
                ->create([
                    'request_no'              => $this->requestNo,
                    'order_no'                => $this->orderNo,
                    'data_type'               => 'CAMPAIGN',
                    'product_item_id'         => null,
                    'promotional_campaign_id' => $orderCampaignDiscount->first()->promotion_campaign_id,
                    'selling_price'           => $discounts,
                    'qty'                     => 1,
                    'subtotal'                => $discounts,
                    'points'                  => 0,
                    'point_discount'          => 0,
                    'refund_amount'           => $discounts,
                    'created_by'              => $this->getUserId(),
                    'updated_by'              => $this->getUserId(),
                ]);
        });

        //舊單和新單分期手續費有落差，新增利息收入資料
        if($this->newOrder->fee_of_instal != $this->oldOrder->fee_of_instal){

            $oldFeeOfInstal = $this->invertSign($this->oldOrder->fee_of_instal);
            //退掉舊單分期手續費
            $this->returnRequest
                ->returnOrderDetails()
                ->create([
                        'request_no'              => $this->requestNo,
                        'order_no'                => $this->orderNo,
                        'data_type'               => 'INSTAL_FEE',
                        'product_item_id'         => null,
                        'promotional_campaign_id' => null,
                        'selling_price'           => $oldFeeOfInstal,
                        'qty'                     => 1,
                        'subtotal'                => $oldFeeOfInstal,
                        'points'                  => 0,
                        'point_discount'          => 0,
                        'refund_amount'           => $oldFeeOfInstal,
                        'created_by'              => $this->getUserId(),
                        'updated_by'              => $this->getUserId(),
                ]);
            //新增新單分期手續費
            if ($this->newOrder->fee_of_instal > 0) {
                $this->returnRequest
                    ->returnOrderDetails()
                    ->create([
                            'request_no'              => $this->requestNo,
                            'order_no'                => $this->orderNo,
                            'data_type'               => 'INSTAL_FEE',
                            'product_item_id'         => null,
                            'promotional_campaign_id' => null,
                            'selling_price'           => $this->newOrder->fee_of_instal,
                            'qty'                     => 1,
                            'subtotal'                => $this->newOrder->fee_of_instal,
                            'points'                  => 0,
                            'point_discount'          => 0,
                            'refund_amount'           => $this->newOrder->fee_of_instal,
                            'created_by'              => $this->getUserId(),
                            'updated_by'              => $this->getUserId(),
                        ]);
            }
        }
    }

    /**
     * 處理退款相關
     * @Author: Eric
     * @DateTime: 2022/9/26 下午 02:47
     */
    private function handleRefund()
    {
        //沒有退款金額和點數則不處理
        if (empty($this->returnRequest->refund_amount) && empty($this->returnRequest->refund_points)) {
            return;
        }

        //如果為分期付款
        if ($this->oldOrder->payment_method == 'TAPPAY_INSTAL') {
            //產生退款單和請款單
            $this->refundForInstallment();
            return;
        }

        //非分期付款，僅產生一筆退款單
        $this->refundForPayOff();
    }

    /**
     * 退款-一次付清用
     * @Author: Eric
     * @DateTime: 2022/9/26 下午 03:11
     */
    private function refundForPayOff()
    {
        $this->newOrder
            ->orderPayments()
            ->create([
                'source_table_name'         => 'return_requests',
                'source_table_id'           => $this->returnRequest->id,
                'order_no'                  => $this->orderNo,
                'payment_type'              => 'REFUND',
                'payment_method'            => $this->newOrder->payment_method,
                'payment_status'            => 'PENDING',
                'amount'                    => $this->returnRequest->refund_amount,
                'latest_api_status'         => null,
                'latest_api_date'           => null,
                'point_discount'            => $this->returnRequest->refund_point_discount,
                'points'                    => $this->returnRequest->refund_points,
                'point_api_status'          => null,
                'point_api_date'            => null,
                'point_api_log'             => null,
                'record_created_reason'     => 'RETURNED',
                'number_of_instal'          => $this->newOrder->number_of_instal,
                'interest_rate_of_instal'   => $this->newOrder->interest_rate_of_instal,
                'min_consumption_of_instal' => $this->newOrder->min_consumption_of_instal,
                'remark'                    => null,
                'created_by'                => $this->getUserId(),
                'updated_by'                => $this->getUserId(),
                'rec_trade_id'              => null,
                'wallet_balance'            => null,
                'wallet_point'              => null
            ]);
    }

    /**
     * 退款-分期付款用
     * @Author: Eric
     * @DateTime: 2022/9/26 下午 03:11
     */
    private function refundForInstallment()
    {
        //新增退款單，退掉全部金額
        $this->newOrder
            ->orderPayments()
            ->create([
                'source_table_name'         => 'return_requests',
                'source_table_id'           => $this->returnRequest->id,
                'order_no'                  => $this->orderNo,
                'payment_type'              => 'REFUND',
                'payment_method'            => $this->oldOrderPayment->payment_method,
                'payment_status'            => 'PENDING',
                'amount'                    => $this->invertSign($this->oldOrderPayment->amount),
                'latest_api_status'         => null,
                'latest_api_date'           => null,
                'point_discount'            => $this->returnRequest->refund_point_discount,
                'points'                    => $this->returnRequest->refund_points,
                'point_api_status'          => null,
                'point_api_date'            => null,
                'point_api_log'             => null,
                'record_created_reason'     => 'RETURNED',
                'number_of_instal'          => $this->oldOrderPayment->number_of_instal,
                'interest_rate_of_instal'   => $this->oldOrderPayment->interest_rate_of_instal,
                'min_consumption_of_instal' => $this->oldOrderPayment->min_consumption_of_instal,
                'fee_of_instal'             => $this->invertSign($this->oldOrderPayment->fee_of_instal),
                'remark'                    => null,
                'created_by'                => $this->getUserId(),
                'updated_by'                => $this->getUserId(),
                'rec_trade_id'              => null,
                'wallet_balance'            => null,
                'wallet_point'              => null
            ]);

        //金額
        $amount = $this->oldOrderPayment->amount - $this->oldOrderPayment->fee_of_instal + $this->returnRequest->refund_amount;
        $feeOfInstal = 0;
        //如果為分期，計算手續費
        if ($this->newOrder->payment_method == 'TAPPAY_INSTAL') {
            //手續費
            $feeOfInstal = $this->getFee($amount, $this->oldOrderPayment->interest_rate_of_instal);
        }

        $amount = $amount + $feeOfInstal;

        // 分期全退時，不須產生新的請款金流單
        if ($amount > 0) {
            //新增請款單
            $this->newOrder
                ->orderPayments()
                ->create([
                    'source_table_name'         => 'return_requests',
                    'source_table_id'           => $this->returnRequest->id,
                    'order_no'                  => $this->orderNo,
                    'payment_type'              => 'PAY',
                    'payment_method'            => $this->newOrder->payment_method,
                    'payment_status'            => 'PENDING',
                    'amount'                    => $amount,
                    'latest_api_status'         => null,
                    'latest_api_date'           => null,
                    'point_discount'            => 0,
                    'points'                    => 0,
                    'point_api_status'          => null,
                    'point_api_date'            => null,
                    'point_api_log'             => null,
                    'record_created_reason'     => 'RETURNED',
                    'number_of_instal'          => $this->newOrder->number_of_instal,
                    'interest_rate_of_instal'   => $this->newOrder->interest_rate_of_instal,
                    'min_consumption_of_instal' => $this->newOrder->min_consumption_of_instal,
                    'fee_of_instal'             => $feeOfInstal,
                    'remark'                    => null,
                    'created_by'                => $this->getUserId(),
                    'updated_by'                => $this->getUserId(),
                    'rec_trade_id'              => null,
                    'wallet_balance'            => null,
                    'wallet_point'              => null
                ]);
        }
    }

    private function getFee(int $amount, $rate)
    {
        return round($amount * ($rate * 0.01));
    }

    /**
     * @param array $params
     * @return $this
     * @Author: Eric
     * @DateTime: 2022/10/3 上午 09:26
     */
    public function setParameters(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @Author: Eric
     * @DateTime: 2022/10/3 下午 04:12
     */
    private function verifyParameters()
    {
        $validator = \Validator::make($this->params,
            [
                'return_request_id' => 'required|int',
                'type'              => 'required|string',
            ], [
                'required' => ':attribute必填',
                'integer'  => ':attribute格式錯誤',
                'string'   => ':attribute格式錯誤',
            ], [
                'return_request_id' => '退貨申請單id',
                'type'              => '類型'
            ]);

        if ($validator->fails()) {
            $this->verifyResult = [
                'status'           => false,
                'code'             => 'E400',
                'http_status_code' => 400,
                'message'          => $validator->errors()->first()
            ];
            return;
        }

        $this->verifyResult = [
            'status'           => true,
            'code'             => 'S200',
            'http_status_code' => 200,
            'message'          => null
        ];
    }

    /**
     * 整理更新return_request資料
     * @Author: Eric
     * @DateTime: 2022/10/25 下午 05:25
     */
    private function handleUpdateReturnRequestData()
    {
        $targetReturnExaminations = $this->returnExamination->where('is_returnable', 1);

        $refundAmount = $targetReturnExaminations->sum('returnable_amount');
        $statusCode   = 'PROCESSING';
        $completedAt  = $this->returnRequest->completed_at;
        $refundStatus = 'PENDING';

        //退貨申請單金額為空
        if (empty($refundAmount)) {
            $statusCode   = 'COMPLETED';
            $completedAt  = now();
            $refundStatus = 'NA';
        }

        $this->updateReturnRequestData = [
            //處理中
            'status_code'           => $statusCode,
            //退款金額
            'refund_amount'         => $refundAmount,
            //歸還點數
            'refund_points'         => $targetReturnExaminations->sum('returnable_points'),
            //歸還點數價值
            'refund_point_discount' => $targetReturnExaminations->sum('returnable_point_discount'),
            //待退款
            'refund_status'         => $refundStatus,
            //'new_order_id'          => $this->newOrder->id,
            'completed_at'          => $completedAt
        ];
    }

    /**
     * 紀錄參數與回傳資料
     * @Author: Eric
     * @DateTime: 2022/11/7 上午 11:25
     */
    private function saveLog()
    {
        $log = [
            'ip'       => request()->ip(),
            'params'   => $this->params,
            'response' => $this->verifyResult,
        ];

        Log::channel('refund_params')->info(json_encode($log, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 處理退款流程
     * @return array
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:46
     */
    public function handle(): array
    {
        try {
            //驗證參數
            $this->verifyParameters();

            if ($this->verifyResult['status'] === false) {
                $this->saveLog();
                return $this->verifyResult;
            }

            //取得退貨申請單
            $this->getReturnRequest();
            //取得最新一筆訂單
            $this->getLatestOrder();
            //驗證資料
            $this->verify();

            if ($this->verifyResult['status'] === false || $this->verifyResult['code'] === 'S201') {
                $this->saveLog();
                return $this->verifyResult;
            }

            DB::beginTransaction();
            $this->updateOrder();
            $this->handleCreateOrderDetailAndReturnOrderDetailData();
            $this->handleUpdateReturnRequestData();
            $this->createOrder();
            $this->updateReturnRequest();
            $this->updateReturnExamination();
            $this->createOrderDetail();
            $this->createOrderCampaignDiscount();
            $this->createReturnOrderDetail();
            $this->handleRefund();
            DB::commit();

            $this->verifyResult = [
                'status'           => true,
                'code'             => 'S200',
                'http_status_code' => 200,
                'message'          => '更新成功'
            ];

            $this->saveLog();
            return $this->verifyResult;

        } catch (Throwable $e) {

            DB::rollBack();
            Log::channel('refund_error')->info(sprintf('IP:%s;參數:%s;錯誤:%s', request()->ip(), json_encode($this->params), $e));

            return [
                'status'           => false,
                'code'             => 'E500',
                'http_status_code' => 500,
                'message'          => '更新失敗，發生錯誤'
            ];
        }
    }
}
