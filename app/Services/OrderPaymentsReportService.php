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
use Carbon\Carbon;

class OrderPaymentsReportService
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
        //開始日期
        if (empty($request['date_start']) === false) {
            $builder = $builder->where('op.created_at', '>=', date('Y-m-d 00:00:00', strtotime($request['date_start'])));
        }

        //結束日期
        if (empty($request['date_end']) === false) {
            $builder = $builder->where('op.created_at', '<=', date('Y-m-d 23:59:59', strtotime($request['date_end'])));
        }

        //金流方式
        if (empty($request['payment_method']) === false) {
            $builder = $builder->where('op.payment_method', $request['payment_method']);
        }

        //狀態
        if (empty($request['payment_status']) === false) {
            $builder = $builder->where('op.payment_status', $request['payment_status']);
        }

        $builder->orderBy('created_at', 'asc');
        $builder->orderBy('order_no', 'asc');
        $builder->orderBy('payment_type', 'asc');

        return $builder;
    }

    /**
     * 取得列表資料
     * @param array $request
     * @return mixed
     * @Author: Eric
     * @DateTime: 2022/1/18 下午 01:22
     */
    public function getOrderPaymentsReports(array $request = [])
    {
        $select1 = "op.created_at, op.order_no, op.payment_type, op.payment_method,
            (case when op.payment_status = 'PENDING' then '待退款'
                when op.payment_status = 'COMPLETED' then '退款成功'
                when op.payment_status = 'FAILED' then '退款失敗'
                when op.payment_status = 'VOIDED' then '已作廢'
            else '' end) as status_desc,
            op.amount,
            o.invoice_no, o.invoice_date, op.record_created_reason,
            '國泰世華' as bank_name";

        $select2 = "op.created_at, op.order_no, op.payment_type, op.payment_method,
            (case when op.payment_status = 'PENDING' then '待請款'
                when op.payment_status = 'COMPLETED' then '請款成功'
                when op.payment_status = 'FAILED' then '請款失敗'
                when op.payment_status = 'VOIDED' then '已作廢'
            else '' end) as status_desc,
            op.amount,
            o.invoice_no, o.invoice_date, op.record_created_reason,
            '國泰世華' as bank_name";

        $builder = DB::table('order_payments as op')
            ->selectRaw($select1)
            ->join('return_requests as rr', function ($join) {
                $join->on('op.source_table_id', '=', 'rr.id')
                    ->where('op.source_table_name', '=', 'return_requests');
            })
            ->join('orders as o', 'o.id', '=', 'rr.order_id')
            ->where('op.payment_type', 'REFUND');

        $builder = $this->handleBuilder($builder, $request);

        $builder2 = DB::table('order_payments as op')
            ->selectRaw($select2)
            ->join('orders as o', function ($join) {
                $join->where('op.source_table_name', '=', 'orders')
                    ->on('o.id', '=', 'op.source_table_id');

            })
            ->where('op.payment_type', 'PAY')
            ->unionAll($builder);

        $builder2 = $this->handleBuilder($builder2, $request);

        return $builder2->get();

    }

    /**
     * 處理列表資料
     * @Author: Eric
     * @DateTime: 2022/1/17 上午 11:23
     */
    public function handleOrderPaymentsReports(Collection $OrderPaymentsReports)
    {
        return $OrderPaymentsReports->map(function ($OrderPaymentsReport) {

            $OrderPaymentsReport->payment_type = config('uec.payment_type_options')[$OrderPaymentsReport->payment_type] ?? null;
            $OrderPaymentsReport->payment_method = config('uec.payment_method_options')[$OrderPaymentsReport->payment_method] ?? null;
            //資料新增原因
            $OrderPaymentsReport->record_created_reason = config('uec.order_payment_record_created_reason')[$OrderPaymentsReport->record_created_reason] ?? null;
            $OrderPaymentsReport->created_at = date('Y-m-d', strtotime($OrderPaymentsReport->created_at));
            $OrderPaymentsReport->amount = is_null($OrderPaymentsReport->amount) ? null : number_format($OrderPaymentsReport->amount);
            // 發票日期
            $OrderPaymentsReport->invoice_date = Carbon::parse($OrderPaymentsReport->invoice_date)->format('Y-m-d');

            return $OrderPaymentsReport;
        });
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

            $item->payment_type = config('uec.payment_type_options')[$item->payment_type] ?? null;
            $item->payment_method = config('uec.payment_method_options')[$item->payment_method] ?? null;
            //資料新增原因
            $item->record_created_reason = config('uec.order_payment_record_created_reason')[$item->record_created_reason] ?? null;
            $item->created_at = date('Y-m-d', strtotime($item->created_at));
            $item->amount = is_null($item->amount) ? null : $item->amount;
            // 發票日期
            $item->invoice_date = Carbon::parse($item->invoice_date)->format('Y-m-d');

            return [
                (string)$index + 1, //項次
                (string)$item->created_at, //日期
                (string)$item->order_no, //訂單編號
                (string)$item->payment_type, //類型
                (string)$item->payment_method, //金流方式
                '', //分期期數
                (string)$item->status_desc, //狀態
                $item->amount, //金額
                (string)$item->invoice_no, //發票號碼
                (string)$item->invoice_date, //發票日期
                (string)$item->record_created_reason, //備註
                (string)$item->bank_name //收款行
            ];
        });
    }
}
