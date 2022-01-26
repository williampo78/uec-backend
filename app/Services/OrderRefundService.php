<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestDetail;
use App\Models\Shipment;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\InvoiceDetail;
use App\Models\ProductPhotos;
use App\Models\ShipmentDetail;
use App\Models\InvoiceAllowance;
use App\Models\SysConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderCampaignDiscount;
use App\Models\InvoiceAllowanceDetail;

class OrderRefundService
{
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
        $select = "rr.id, rr.request_date, rr.request_no, rr.order_no, rr.status_code, rr.lgst_method, rr.refund_method, rr.completed_at, rr.req_name, rr.req_mobile, rr.req_city, rr.req_district, rr.req_address";

        $builder = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.order_id'); //join條件調整

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
    public function handleOrderRefunds(Collection $OrderRefunds)
    {
        return $OrderRefunds->map(function ($OrderRefund) {
            $OrderRefund->status_code = config('uec.return_request_status_options')[$OrderRefund->status_code] ?? null;
            $OrderRefund->refund_method = config('uec.payment_method_options')[$OrderRefund->refund_method] ?? null;
            $OrderRefund->lgst_method = config('uec.lgst_method_options')[$OrderRefund->lgst_method] ?? null;

            return $OrderRefund;
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
        $select = "created_at, '退款'  as payment_type_desc,
               amount,
                (case when payment_status = 'PENDING' then '待退款'
             when payment_status = 'COMPLETED' then '退款成功'
             when payment_status = 'FAILED' then '退款失敗'
             when payment_status = 'VOIDED' then '已作廢'
             else '' end) as payment_status_desc,
            latest_api_date,
            remark";

        $result = DB::table('order_payments')
            ->selectRaw($select)
            ->where('source_table_name', 'return_requests')
            ->where('source_table_id', $id);

        return DB::table('order_payments')
            ->selectRaw($select)
            ->where('source_table_name', 'orders')
            ->where('source_table_id', $id)
            ->unionAll($result)
            ->get();
    }

    public function getReturnRequest(int $id)
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
            o.member_account, o.buyer_name,
            lvv.description as req_reason_description';

        $ReturnRequest = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.new_order_id')
            ->leftJoin('lookup_values_v as lvv', 'lvv.code', '=', 'rr.req_reason_code')
            ->where('rr.id', $id)
            ->where('rr.agent_id', $agent_id)
            ->where('lvv.agent_id', $agent_id)
            ->where('lvv.type_code', 'RETURN_REQ_REASON')
            ->first();

        if (empty($ReturnRequest)) {
            return null;
        }

        //退貨單狀態
        $ReturnRequest->status_code = config('uec.return_request_status_options')[$ReturnRequest->status_code] ?? null;
        //物流方式
        $ReturnRequest->lgst_method = config('uec.lgst_method_options')[$ReturnRequest->lgst_method] ?? null;
        //物流廠商
        $ReturnRequest->lgst_company = config('uec.lgst_company_code_options')[$ReturnRequest->lgst_company_code] ?? null;

        return $ReturnRequest;
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
            rr.status_code,
            rr.lgst_method,
            rr.completed_at,
            rr.refund_method,
            o_new.refund_status,
            o.buyer_name,
            rr.req_name,
            rr.req_mobile,
            rr.req_city,
            rr.req_district,
            rr.req_address,
            pi.item_no,
            p.product_name,
            pi.spec_1_value,
            pi.spec_2_value,
            rrd.request_qty,
            rrd.passed_qty,
            rrd.failed_qty";

        $builder = DB::table('return_requests as rr')
            ->selectRaw($select)
            ->join('orders as o', 'o.id', '=', 'rr.order_id')  //join條件調整
            ->join('return_request_details as rrd', 'rrd.return_request_id', '=', 'rr.id')
            ->join('product_items as pi', 'pi.id', '=', 'rrd.product_item_id')
            ->join('products as p', 'p.id', '=', 'pi.product_id')
            ->leftJoin('orders as o_new', 'o.id', '=', 'rr.new_order_id')  //add
            ->orderBy('rr.id');

        //處理where
        $builder = $this->handleBuilder($builder, $request);
        $builder->orderBy('rr.request_date', 'asc')
            ->orderBy('rr.request_no', 'asc');

        return $builder->get();
    }

    /**
     * 處理匯出excel的資料
     * @param Collection $collection
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:23
     */
    public function handleExcelData(Collection $collection)
    {
        return $collection->map(function ($item, $index) {

            //退貨申請單狀態
            $item->status_code = config('uec.return_request_status_options')[$item->status_code] ?? null;
            //付款方式
            $item->refund_method = config('uec.payment_method_options')[$item->refund_method] ?? null;
            //物流方式
            $item->lgst_method = config('uec.lgst_method_options')[$item->lgst_method] ?? null;
            //退款狀態
            $item->refund_status = config('uec.order_refund_status_options')[$item->refund_status] ?? null;

            return [
                (string)$index + 1, //項次
                (string)$item->request_date, //退貨申請時間
                (string)$item->request_no, //退貨申請單號
                (string)$item->order_no, //訂單編號
                (string)$item->member_account, //會員帳號
                (string)$item->status_code, //狀態
                (string)$item->lgst_method, //物流方式
                (string)$item->completed_at, //退貨完成時間
                (string)$item->refund_method, //退款方式
                (string)$item->refund_status, //退款狀態
                (string)$item->buyer_name, //訂購人
                (string)$item->req_name, //取件聯絡人
                (string)$item->req_mobile, //取件聯絡手機
                sprintf('%s%s%s', $item->req_city, $item->req_district, $item->req_address), //取件地址
                (string)$item->item_no, //Item編號
                (string)$item->product_name, //商品名稱
                (string)$item->spec_1_value, //規格一
                (string)$item->spec_2_value, //規格二
                (string)$item->request_qty, //申請數量
                (string)$item->passed_qty, //檢驗合格數量
                (string)$item->failed_qty, //檢驗不合格數
            ];
        });
    }
}
