<?php

namespace App\Services;

use App\Models\ReturnExamination;
use App\Models\ReturnRequest;
use App\Services\ReturnGoodsService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRefundService
{

    private $returnGoodsService;

    public function __construct(ReturnGoodsService $returnGoodsService)
    {
        $this->returnGoodsService = $returnGoodsService;
    }

    /**
     * 處理和搜尋有關的sql
     * @param $builder
     * @param array $request
     * @return mixed
     * @Author: Eric
     * @DateTime: 2022/1/17 下午 01:17
     */
    private function handleBuilder($builder, array $request = [])
    {
        $builder = $builder->where('rr.agent_id', Auth::user()->agent_id);

        if (empty($request['order_refund_date_start']) === false) {
            $builder = $builder->where('rr.request_date', '>=', date('Y-m-d 00:00:00', strtotime($request['order_refund_date_start'])));
        }

        //退貨申請時間 結束
        if (empty($request['order_refund_date_end']) === false) {
            $builder = $builder->where('rr.request_date', '<=', date('Y-m-d 23:59:59', strtotime($request['order_refund_date_end'])));
        }

        //退貨申請單號
        if (empty($request['request_no']) === false) {
            $builder = $builder->where('rr.request_no', $request['request_no']);
        }

        //會員帳號
        if (empty($request['member_account']) === false) {
            $builder = $builder->where('o.member_account', $request['member_account']);
        }

        //退貨申請狀態
        if (empty($request['status_code']) === false) {
            $builder = $builder->where('rr.status_code', $request['status_code']);
        }

        //訂單編號
        if (empty($request['order_no']) === false) {
            $builder = $builder->where('rr.order_no', $request['order_no']);
        }

        //會員姓名
        if (empty($request['member_name']) === false) {
            $builder = $builder->where('o.buyer_name', $request['member_name']);
        }

        //訂單類型
        if (empty($request['ship_from_whs']) === false) {
            $builder = $builder->where('rr.ship_from_whs', $request['ship_from_whs']);
        }

        //待辦項目
        if (empty($request['to_do_item']) === false) {

            switch($request['to_do_item']){
                //檢驗異常
                case 'check_exception':

                    $builder->whereExists(function ($query) {
                        $query->select(['id'])
                            ->from('return_examinations as re')
                            ->whereColumn('re.return_request_id', 'rr.id')
                            ->where('status_code', 'FAILED');
                    });
                    break;
                //退款異常
                case 'refund_failed':
                    $builder = $builder->where('rr.refund_status', 'FAILED');
                    break;
            }
        }

        return $builder;
    }

    /**
     * 取得列表資料
     * @param array $request
     * @return Collection|mixed
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:22
     */
    public function getOrderRefunds($request = [])
    {
        $select = "rr.id,
            rr.request_date,
            rr.request_no,
            rr.order_no,
            rr.status_code,
            rr.lgst_method,
            rr.ship_from_whs,
            rr.refund_method,
            rr.completed_at,
            rr.req_name,
            rr.req_mobile,
            rr.req_city,
            rr.req_district,
            rr.req_address";

        $builder = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.order_id');

        //處理where
        $builder = $this->handleBuilder($builder, $request);
        $builder->orderBy('rr.request_date', 'desc');

        return $builder->get();

    }

    /**
     * 處理列表資料
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:23
     */
    public function handleOrderRefunds(Collection $orderRefunds)
    {
        return $orderRefunds->map(function ($orderRefund) {
            // 退貨申請時間
            $orderRefund->request_date = Carbon::parse($orderRefund->request_date)->format('Y-m-d H:i');

            // 退貨完成時間
            if (isset($orderRefund->completed_at)) {
                $orderRefund->completed_at = Carbon::parse($orderRefund->completed_at)->format('Y-m-d H:i');
            }

            $orderRefund->status_code   = config('uec.return_request_status_options')[$orderRefund->status_code] ?? null;
            $orderRefund->refund_method = config('uec.payment_method_options')[$orderRefund->refund_method] ?? null;
            $orderRefund->lgst_method   = config('uec.lgst_method_options')[$orderRefund->lgst_method] ?? null;
            $orderRefund->ship_from_whs = $orderRefund->ship_from_whs == 'SELF' ? '商城出貨' : '供應商出貨';

            return $orderRefund;
        });
    }

    /**
     * 檢驗單內容
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @Author: Eric
     * @DateTime: 2022/9/6 上午 11:36
     */
    public function getReturnExaminationWithDetails(int $id)
    {
        return ReturnExamination::with([
            'supplier:id,name',
            'returnRequest:id,lgst_company_code,ship_from_whs',
            'returnExaminationDetails:id,return_examination_id,return_request_detail_id,product_item_id,request_qty',
            'returnExaminationDetails.productItem:id,product_id,spec_1_value,spec_2_value,item_no',
            'returnExaminationDetails.productItem.product:id,product_name,supplier_product_no',
            'returnExaminationDetails.returnRequestDetail:id,selling_price,campaign_discount,cart_p_discount,subtotal,record_identity,point_discount'
        ])
            ->where('return_request_id', $id)
            ->orderBy('examination_no')
            ->get([
                'id',
                'examination_no',
                'supplier_id',
                'return_request_id',
                'status_code',
                'sup_lgst_company',
                'lgst_dispatched_at',
                'lgst_doc_no',
                'examination_reported_at',
                'is_examination_passed',
                'examination_remark',
                'nego_result',
                'nego_refund_amount',
                'nego_remark',
                'pkg_ret_reported_at',
                'is_pkg_returned',
                'pkg_ret_remark',
                'returnable_point_discount',
                'returnable_amount',
                'pkg_ret_failed_reason',
                'examination_failed_reason'
            ]);
    }

    /**
     * @param Collection $returnExaminations
     * @param Collection $lookupValuesVs
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/9/7 上午 11:35
     */
    public function handleReturnExaminations(Collection $returnExaminations, Collection $lookupValuesVs, array $permission, $ReturnRequest): Collection
    {
        return $returnExaminations->map(function ($returnExamination) use ($lookupValuesVs, $permission, $ReturnRequest) {

            $numberOrLogisticsName = optional($returnExamination->returnRequest)->lgst_company_code;
            $numberOrLogisticsName = config('uec.lgst_company_code_options')[$numberOrLogisticsName] ?? '';
            //供應商自出
            if ($returnExamination->returnRequest->ship_from_whs == 'SUP') {
                $code                  = $returnExamination->sup_lgst_company;
                $numberOrLogisticsName = optional($lookupValuesVs->where('type_code', 'SUP_LGST_COMPANY')->where('code', $code)->first())->description;
            }

            //細項
            $details = $returnExamination->returnExaminationDetails->map(function ($detail) {

                return [
                    'item_no'             => $detail->productItem->item_no ?? '',
                    'product_name'        => $detail->productItem->product->product_name ?? '',
                    'spec_1_value'        => $detail->productItem->spec_1_value ?? '',
                    'spec_2_value'        => $detail->productItem->spec_2_value ?? '',
                    'request_qty'         => $detail->request_qty ?? '',
                    'supplier_product_no' => $detail->productItem->product->supplier_product_no ?? '',
                    'selling_price'       => number_format($detail->returnRequestDetail->selling_price),
                    'discount'            => number_format($detail->returnRequestDetail->campaign_discount + $detail->returnRequestDetail->cart_p_discount),
                    'subtotal'            => number_format($detail->returnRequestDetail->subtotal),
                    'point_discount'      => number_format($detail->returnRequestDetail->point_discount),
                    'record_identity'     => config('uec.order_record_identity_options')[$detail->returnRequestDetail->record_identity] ?? '',
                ];
            });

            $buttons = [];

            //作廢按鈕
            if ($permission['auth_void'] == 1) {
                //可以作廢的狀態
                $voidableState = [
                    'CREATED', //接獲申請
                    'DISPATCHED' //派車回收
                ];

                //作廢按鈕判斷
                if (in_array($returnExamination->status_code, $voidableState, true)) {
                    $buttons[] = 'void';
                }
            }

            //協商回報按鈕
            if ($permission['auth_update'] == 1) {
                //檢驗異常
                if ($returnExamination->status_code == 'FAILED') {
                    $buttons[] = 'negotiate';

                //商城出貨 && 派車回收
                } elseif ($ReturnRequest->ship_from_whs == 'SELF' && $returnExamination->status_code == 'DISPATCHED') {
                    $buttons[] = 'negotiate';
                }
            }

            //回件/檢驗結果
            $isExaminationPassed = '';

            if(!is_null($returnExamination->is_pkg_returned)){
                $isExaminationPassed = $returnExamination->is_pkg_returned == 1 ? '已回件' : '未回件';
            }

            if(!is_null($returnExamination->is_examination_passed)){
                $isExaminationPassed = $returnExamination->is_examination_passed == 1 ? '合格' : '不合格';
            }

            //協商結果
            $negoResult = '';
            if(!is_null($returnExamination->nego_result)){
                $negoResult = $returnExamination->nego_result == 1 ? '允許退貨' : '不允許退貨';
            }

            //回件/檢驗結果說明
            //備註
            $examinationRemark = $returnExamination->examination_remark ?? $returnExamination->pkg_ret_remark ?? '';
            //未回件原因
            $pkgReturnFailedRsn = optional($lookupValuesVs
                ->where('type_code', 'PKG_RETURN_FAILED_RSN')
                ->where('code', $returnExamination->pkg_ret_failed_reason)
                ->first())
                ->description;
            //不合格原因
            $examinationFailedRsn = optional($lookupValuesVs
                ->where('type_code', 'EXAMINATION_FAILED_RSN')
                ->where('code', $returnExamination->examination_failed_reason)
                ->first())
                ->description;

            if (is_null($pkgReturnFailedRsn) === false) {
                $examinationRemark = "【{$pkgReturnFailedRsn}】{$examinationRemark}";
            } elseif (is_null($examinationFailedRsn) === false) {
                $examinationRemark = "【{$examinationFailedRsn}】{$examinationRemark}";
            }

            return [
                'buttons'                  => $buttons,
                'return_examination_id'    => $returnExamination->id,
                //檢驗單號
                'examination_no'           => $returnExamination->examination_no ?? '',
                //檢驗單狀態
                'status_code'              => config('uec.return_examination_status_codes')[$returnExamination->status_code] ?? '',
                //供應商
                'supplier_name'            => optional($returnExamination)->supplier->name ?? '',
                //派車確認時間
                'lgst_dispatched_at'       => $returnExamination->lgst_dispatched_at ?? '',
                //物流單號或是派車物流
                'number_or_logistics_name' => $numberOrLogisticsName ?? '',
                //取件單號
                'lgst_doc_no'              => $returnExamination->lgst_doc_no ?? '',
                //回件/檢驗回報時間
                'examination_reported_at'  => $returnExamination->examination_reported_at ?? $returnExamination->pkg_ret_reported_at ?? '',
                //回件/檢驗結果
                'is_examination_passed'    => $isExaminationPassed,
                //回件/檢驗結果說明
                'examination_remark'       => $examinationRemark,
                //協商結果
                'nego_result'              => $negoResult,
                //協商退款金額
                'nego_refund_amount'       => is_null($returnExamination->nego_refund_amount) ? '' : number_format($returnExamination->nego_refund_amount),
                //協商內容備註
                'nego_remark'              => $returnExamination->nego_remark ?? '',
                'refundable_amount'        => (int)abs($returnExamination->returnable_amount),
                'details'                  => $details ?? []
            ];
        });
    }

    /**
     * 退貨明細
     * @param int $id
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/14 下午 04:01
     */
    public function getReturnDetails(int $id)
    {
        return DB::table('return_request_details as rrd')
            ->selectRaw('p.product_name, pi.item_no,  pi.spec_1_value spec_1, pi.spec_2_value spec_2, rrd.*')
            ->join('product_items as pi', 'pi.id', '=', 'rrd.product_item_id')
            ->join('products as p', 'p.id', '=', 'pi.product_id')
            ->where('rrd.return_request_id', $id)
            ->get();
    }

    /**
     * 退款資訊
     * @param $request
     * @Author: Eric
     * @DateTime: 2022/1/14 下午 05:04
     */
    public function getReturnInformation(int $id)
    {
        $order_payments = DB::table('order_payments')
            ->where('source_table_name', 'return_requests')
            ->where('source_table_id', $id)
            ->get([
                'id', 'amount', 'payment_status', 'payment_type', 'payment_method', 'remark', 'created_at'
            ]);

        //金流類型
        $paymentTypes = config('uec.payment_type_options');
        //金流狀態-請款
        $paymentPayStatus = config('uec.payment_pay_status_options');
        //金流狀態-退款
        $paymentRefundStatus = config('uec.payment_refund_status_options');
        //金流方式
        $paymentMethods = config('uec.payment_method_options');

        $order_payments->transform(function ($order_payment) use ($paymentTypes, $paymentPayStatus, $paymentRefundStatus, $paymentMethods) {
            //金流類型
            $order_payment->payment_type_desc = $paymentTypes[$order_payment->payment_type] ?? '';
            //預設為請款
            $order_payment->payment_status_desc = $paymentPayStatus[$order_payment->payment_status] ?? '';
            //為退款
            if ($order_payment->payment_type === 'REFUND') {
                $order_payment->payment_status_desc = $paymentRefundStatus[$order_payment->payment_status] ?? '';
            }

            // 建立時間
            $order_payment->created_at = Carbon::parse($order_payment->created_at)->format('Y-m-d H:i');
            //金流方式
            $order_payment->payment_method = $paymentMethods[$order_payment->payment_method] ?? '';

            return $order_payment;
        });

        return $order_payments;
    }

    public function getReturnRequest(int $id, array $permission)
    {
        $agent_id = Auth::user()->agent_id;

        $select = 'rr.*,
            rr.request_no,
            rr.request_date,
            rr.order_no,
            rr.status_code,
            rr.completed_at,
            rr.lgst_method,
            rr.req_remark,
            rr.req_name,
            rr.req_mobile,
            rr.req_city,
            rr.req_district,
            rr.req_address,
            rr.lgst_company_code,
            rr.ship_from_whs,
            rr.refund_status,
            o.member_account, o.buyer_name,
            lvv.description as req_reason_description,
            re.id as re_id';

        $returnRequest = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.order_id')
            ->leftJoin('lookup_values_v as lvv', 'lvv.code', '=', 'rr.req_reason_code')
            ->leftJoin('return_examinations as re', function ($query) {
                $query->on('re.return_request_id', '=', 'rr.id')
                    ->on('re.id', '=',
                        //檢驗異常
                        DB::raw('(select id from return_examinations where status_code = "FAILED" limit 1)'));
            })
            ->where('rr.id', $id)
            ->where('rr.agent_id', $agent_id)
            ->where('lvv.agent_id', $agent_id)
            ->where('lvv.type_code', 'RETURN_REQ_REASON')
            ->first();

        if (empty($returnRequest)) {
            return null;
        }

        // 退貨申請時間
        $returnRequest->request_date = Carbon::parse($returnRequest->request_date)->format('Y-m-d H:i');

        // 退貨完成時間
        if (isset($returnRequest->completed_at)) {
            $returnRequest->completed_at = Carbon::parse($returnRequest->completed_at)->format('Y-m-d H:i');
        }

        //退貨單狀態
        $returnRequest->status_code = config('uec.return_request_status_options')[$returnRequest->status_code] ?? null;
        //物流方式
        $returnRequest->lgst_method = config('uec.lgst_method_options')[$returnRequest->lgst_method] ?? null;
        //物流廠商
        $returnRequest->lgst_company = config('uec.lgst_company_code_options')[$returnRequest->lgst_company_code] ?? null;
        //是否能人工退款
        $returnRequest->can_manual_refund = false;
        $returnRequest->prompt_text = null;
        //有任一檢驗單狀態為檢驗異常
        if(!empty($returnRequest->re_id)){
            $returnRequest->prompt_text = '(待協商)';
        }

        //退款異常
        if ($returnRequest->refund_status == 'FAILED') {

            $returnRequest->prompt_text = '(退款異常)';
            //有編輯權限
            if ($permission['auth_update'] == 1) {
                $returnRequest->can_manual_refund = true;
            }
        }

        return $returnRequest;
    }

    /**
     * 取得匯出excel資料
     * @param array $request
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:22
     */
    public function getExcelData(array $request = [])
    {
        $select = "rr.request_date,
           rr.request_no,
           rr.order_no,
           o.member_account,
           rr.status_code as rr_status_code,
           rr.lgst_method,
           rr.completed_at,
           rr.refund_method,
           rr.refund_status,
           o.buyer_name,
           rr.req_name,
           rr.req_mobile,
           CONCAT(rr.req_city, rr.req_district, rr.req_address) as full_address,
           re.examination_no,
           re.status_code as re_status_code,
           (select supplier.name from supplier where supplier.id = re.supplier_id) as supplier_name,
           re.sup_lgst_company,
           re.lgst_doc_no,
           re.is_pkg_returned,
           re.is_examination_passed,
           pi.item_no,
           p.product_name,
           pi.spec_1_value,
           pi.spec_2_value,
           red.request_qty,
           rrd.record_identity";

        $builder = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.order_id')
            ->join('return_examinations as re', 're.return_request_id', '=', 'rr.id')
            ->join('return_examination_details as red', 'red.return_examination_id', '=', 're.id')
            ->join('return_request_details as rrd', 'rrd.id', '=', 'red.return_request_detail_id')
            ->join('product_items as pi', 'pi.id', '=', 'red.product_item_id')
            ->join('products as p', 'p.id', '=', 'pi.product_id');

        //處理where
        $builder = $this->handleBuilder($builder, $request);
        $builder->orderBy('rr.request_date', 'asc')
            ->orderBy('rr.request_no', 'asc')
            ->orderBy('re.examination_no', 'asc')
            ->orderBy('pi.item_no', 'asc');

        return $builder->get();
    }

    /**
     * 處理匯出excel的資料
     * @param Collection $collection
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:23
     */
    public function handleExcelData(Collection $collection, Collection $lookupValuesV)
    {
        return $collection->map(function ($item, $index) use ($lookupValuesV) {
            // 退貨申請時間
            $item->request_date = Carbon::parse($item->request_date)->format('Y-m-d H:i');

            // 退貨完成時間
            if (isset($item->completed_at)) {
                $item->completed_at = Carbon::parse($item->completed_at)->format('Y-m-d H:i');
            }

            //退貨申請單狀態
            $item->rr_status_code = config('uec.return_request_status_options')[$item->rr_status_code] ?? null;
            //付款方式
            $item->refund_method = config('uec.payment_method_options')[$item->refund_method] ?? null;
            //物流方式
            $item->lgst_method = config('uec.lgst_method_options')[$item->lgst_method] ?? null;
            //退款狀態
            $item->refund_status = config('uec.order_refund_status_options')[$item->refund_status] ?? null;

            //取件結果
            if(isset($item->is_pkg_returned)){
                $item->is_pkg_returned = $item->is_pkg_returned == 1 ? '取件成功' : '取件失敗';
            }

            //檢驗結果
            if(isset($item->is_examination_passed)){
                $item->is_examination_passed = $item->is_examination_passed == 1 ? '合格' : '不合格';
            }

            //訂單身份
            $item->record_identity = config('uec.order_record_identity_options')[$item->record_identity] ?? null;
            //退貨檢驗單狀態
            $item->re_status_code = config('uec.return_examination_status_codes')[$item->re_status_code] ?? null;

            return [
                (string)$index + 1, //項次
                (string)$item->request_date, //退貨申請時間
                (string)$item->request_no, //退貨申請單號
                (string)$item->order_no, //訂單編號
                (string)$item->member_account, //會員帳號
                (string)$item->rr_status_code, //退貨申請單狀態
                (string)$item->lgst_method, //物流方式
                (string)$item->completed_at, //退貨完成時間
                (string)$item->refund_method, //退款方式
                (string)$item->refund_status, //退款狀態
                (string)$item->buyer_name, //訂購人
                (string)$item->req_name, //取件聯絡人
                (string)$item->req_mobile, //取件聯絡手機
                (string)$item->full_address, //取件地址
                (string)$item->examination_no,//退貨檢驗單號
                (string)$item->re_status_code,//退貨檢驗單狀態
                (string)$item->supplier_name,//供應商
                (string)optional($lookupValuesV->where('code', $item->sup_lgst_company)->first())->description,//物流公司
                (string)$item->lgst_doc_no,//取件單號
                (string)$item->is_pkg_returned,//取件結果
                (string)$item->is_examination_passed,//檢驗結果
                (string)$item->item_no, //Item編號
                (string)$item->product_name, //商品名稱
                (string)$item->spec_1_value, //規格一
                (string)$item->spec_2_value, //規格二
                (string)$item->request_qty, //申請數量
                $item->record_identity //訂單身份
            ];
        });
    }

    /**
     * 更新協商資料
     * @param array $payload
     * @return array
     * @Author: Eric
     * @DateTime: 2022/9/13 下午 05:36
     */
    public function updateNegotiatedReturn(array $payload): array
    {
        //取得檢驗單相關資料
        $returnExamination = ReturnExamination::with([
            'returnExaminationDetails:id,return_examination_id,return_request_detail_id,request_qty',
            'returnExaminationDetails.returnRequestDetail:id,return_request_id,request_qty,point_discount,points',
            'returnRequest:id,ship_from_whs'
        ])
        ->find($payload['return_examination_id'], ['id', 'return_request_id', 'returnable_point_discount', 'returnable_amount','status_code']);
        
        $returnExaminationStatus = false ; // 檢驗單狀態檢查

        if(!empty($returnExamination)){
            if($returnExamination->status_code == 'FAILED'){
                $returnExaminationStatus = true; 
            }
            if($returnExamination->returnRequest->ship_from_whs == 'SELF' && $returnExamination->status_code == 'DISPATCHED'){
                $returnExaminationStatus = true;
            }
        }

        if (!$returnExaminationStatus) {
            return [
                'status'  => false,
                'message' => '發生錯誤，檢驗單不存在'
            ];
        }
        
        //退款金額大於可退金額
        if ($payload['nego_refund_amount'] > abs($returnExamination->returnable_amount)) {
            return [
                'status'  => false,
                'message' => '發生錯誤，退款金額大於可退款金額'
            ];
        }

        //申請單身的點數合計
        $pointDiscounts = $returnExamination->returnExaminationDetails->sum('returnRequestDetail.point_discount');
        $points         = $returnExamination->returnExaminationDetails->sum('returnRequestDetail.points');
        $now            = now();
        $userId         = Auth()->user()->id;

        $updateData = [
            'nego_result'               => $payload['nego_result'],
            'nego_refund_amount'        => $payload['nego_refund_amount'] * (-1),
            'nego_remark'               => $payload['nego_remark'],
            'nego_reported_at'          => $now,
            'nego_reported_by'          => $userId,
            'returnable_confirmed_at'   => $now,
            'returnable_points'         => 0,
            'returnable_point_discount' => 0
        ];

        try {

            //允許退貨
            if ($payload['nego_result'] == 1) {
                $updateData['status_code']               = 'NEGO_COMPLETED';
                $updateData['returnable_amount']         = $payload['nego_refund_amount'] * (-1);
                $updateData['is_returnable']             = 1;
                $updateColumn = 'passed_qty';

            } else {
                $updateData['status_code']               = 'M_CLOSED';
                $updateData['returnable_amount']         = 0;
                $updateData['is_returnable']             = 0;
                $updateColumn = 'failed_qty';
            }

            DB::beginTransaction();
            //更新檢驗單頭
            $returnExamination->update($updateData);

            $returnExamination->returnExaminationDetails->each(function ($returnExaminationDetail) use ($userId, $updateColumn) {
                //更新檢驗單身
                $returnExaminationDetail->update([
                    $updateColumn => $returnExaminationDetail->request_qty,
                    'updated_by'  => $userId
                ]);

                //更新申請單身
                $returnExaminationDetail->returnRequestDetail->update([
                    $updateColumn => $returnExaminationDetail->returnRequestDetail->request_qty,
                    'updated_by'  => $userId
                ]);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [
                'status'  => false,
                'message' => '發生錯誤'
            ];
        }

        //檢查退貨申請單所有檢驗單的is_returnable 是否皆有值(0或1)
        $unconfirmedReturnExamination = ReturnExamination::where('return_request_id', $returnExamination->return_request_id)
            ->whereNull('is_returnable')
            ->first();

        //所有檢驗單皆驗證完成，呼叫退貨api
        if (empty($unconfirmedReturnExamination)) {

            $payload = [
                'return_request_id' => $returnExamination->return_request_id,
                'type'              => 'backend'
            ];

            return $this->returnGoodsService
                ->setParameters($payload)
                ->handle();
        }

        return [
            'status'  => true,
            'message' => '資料更新成功',
        ];
    }

    /**
     * 人工退款
     * @param array $payload
     * @return array
     * @Author: Eric
     * @DateTime: 2022/9/15 下午 03:23
     */
    public function updateManualRefund(array $payload): array
    {
        $returnRequest = ReturnRequest::where('refund_status', 'FAILED')
            ->find($payload['return_request_id'], ['id']);

        if (empty($returnRequest)) {
            return [
                'status'  => false,
                'message' => '此申請單不存在',
            ];
        }

        $returnRequest->update([
            'refund_status'          => 'COMPLETED',
            'refund_at'              => $payload['refund_at'],
            'is_manually_refund'     => 1,
            'manually_refunded_by'   => auth()->user()->id,
            'manually_refund_remark' => $payload['manually_refund_remark'],
        ]);

        return [
            'status'  => true,
            'message' => '資料更新成功',
        ];
    }
}
