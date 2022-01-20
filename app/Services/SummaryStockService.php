<?php


namespace App\Services;

use App\Models\StockTransactionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\StockMonthlySummary;
use Batch;

class SummaryStockService
{
    public function getSummaryStock($data)
    {
        $result = StockMonthlySummary::select("stock_monthly_summary.*", "products.product_name",
            DB::raw("0 as adj_qty"),
            DB::raw("0 as adj_amount"),
            DB::raw("0 as shift_qty"),
            DB::raw("0 as shift_amount"),
            )
            ->join("products", "products.id", "=", "stock_monthly_summary.product_id")
            ->where("stock_monthly_summary.transaction_month", $data['smonth']);

        if ($data['item_id_start'] != '' && $data['item_id_end'] != '') {
            $result = $result->whereBetween('stock_monthly_summary.product_item_id', [$data['item_id_start'], $data['item_id_end']]);
        }

        if ($data['product_name'] != '') {
            $result = $result->where('products.product_name', 'like', '%' . $data['product_name'] . '%');
        }
        $result = $result->orderBy("stock_monthly_summary.product_item_id", "asc")->get();

        return $result;
    }

    public function getSummarySum($data)
    {
        $result = StockMonthlySummary::select(
            DB::raw('stock_monthly_summary.transaction_month as month'),
            DB::raw('sum(stock_monthly_summary.begin_qty) begin_qty'),
            DB::raw('sum(stock_monthly_summary.begin_amount) begin_amount'),
            DB::raw('sum(stock_monthly_summary.end_qty) end_qty'),
            DB::raw('sum(stock_monthly_summary.end_amount) end_amount'),
            )
            ->join("products", "products.id", "=", "stock_monthly_summary.product_id")
            ->where("stock_monthly_summary.transaction_month", $data['smonth']);

        if ($data['item_id_start'] != '' && $data['item_id_end'] != '') {
            $result = $result->whereBetween('stock_monthly_summary.product_item_id', [$data['item_id_start'], $data['item_id_end']]);
        }

        if ($data['product_name'] != '') {
            $result = $result->where('products.product_name', 'like', '%' . $data['product_name'] . '%');
        }
        $result = $result->groupBy("stock_monthly_summary.transaction_month")->get();

        return $result;
    }

    public function setSummaryCost($smonth)
    {
        $lastRecordDate = StockMonthlySummary::max('transaction_month');
        $data = [];
        if (strtotime($lastRecordDate) > strtotime($smonth)) {
            $data['message'] = '不允許執行滾算';
            $data['result'] = '已有' . $lastRecordDate . '的滾算記錄，不允許重算' . $smonth . '的資料';
            $data['status'] = false;
        } elseif (strtotime($lastRecordDate) == strtotime($smonth)) {
            //$result = $this->execCost($smonth);
            $data['message'] = '執行滾算成功';
            $data['result'] = $smonth."資料已經滾算完成！";
            $data['status'] = true;
        }
        return $data;

    }

    /*
     * step 1A 前期尚有餘額的品項：以前期期末數，當作本期期初數
     * step 1B 本期有交易，但前期無餘額的品項
     */
    public function execCost($smonth)
    {
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        $now = Carbon::now();

        $previous_month = date("Y-m", strtotime($smonth . " -1 month"));
        $next_month = date("Y-m", strtotime($smonth . " +1 month"));

        $addColumn = [
            "agent_id", "transaction_month", "product_id", "product_item_id",
            "begin_qty", "begin_amount", "item_cost",
            "rcv_qty", "rcv_amount", "rtv_qty", "rtv_amount",
            "sales_qty", "sales_amount", "sales_return_qty", "sales_return_amount",
            "end_qty", "end_amount",
            "created_by", "updated_by", "created_at", "updated_at"
        ];
        $new = 0;
        DB::beginTransaction();
        try {
            /* 1a */
            $data = StockMonthlySummary::select(
                DB::raw("(select pi.product_id from product_items pi where pi.id = stock_monthly_summary.product_item_id) as product_id"),
                'product_item_id',
                DB::raw('end_qty as begin_qty'),
                DB::raw('end_amount as begin_amount'),
                DB::raw('0 as item_cost'),
                DB::raw("0 as rcv_qty"),
                DB::raw("0 as rcv_amount"),
                DB::raw("0 as rtv_qty"),
                DB::raw("0 as rtv_amount"),
                DB::raw("0 as sales_qty"),
                DB::raw("0 as sales_amount"),
                DB::raw("0 as sales_return_qty"),
                DB::raw("0 as sales_return_amount"),
                DB::raw("0 as end_qty"),
                DB::raw("0 as end_amount")
            )
                ->where('transaction_month', $previous_month);
            $data = $data->where(function ($query) {
                $query->where('end_qty', '<>', 0);
                $query->orwhere('end_amount', '<>', 0);
            });
            $data = $data->orderBy('product_item_id', 'asc')->get();
            $webDataAdd = [];
            foreach ($data as $key => $value) {
                $webDataAdd[$key] = [
                    $agent_id,
                    $smonth,
                    $value->product_id,
                    $value->product_item_id,
                    $value->begin_qty,
                    $value->begin_amount,
                    $value->item_cost,
                    $value->rcv_qty,
                    $value->rcv_amount,
                    $value->rtv_qty,
                    $value->rtv_amount,
                    $value->sales_qty,
                    $value->sales_amount,
                    $value->sales_return_qty,
                    $value->sales_return_amount,
                    $value->end_qty,
                    $value->end_amount,
                    $user_id,
                    $user_id,
                    $now,
                    $now
                ];
            }
            if ($webDataAdd) {
                $instance = new StockMonthlySummary();
                $batchSize = 50;
                $add = Batch::insert($instance, $addColumn, $webDataAdd, $batchSize);
                $new += $add['totalRows'];
            }

            /* 1b */
            $data = StockTransactionLog::select(
                DB::raw("distinct (select pi.product_id from product_items pi where pi.id = stock_transaction_log.product_item_id) as product_id"),
                'product_item_id',
                DB::raw('0 as begin_qty'),
                DB::raw('0 as begin_amount'),
                DB::raw('0 as item_cost'),
                DB::raw("0 as rcv_qty"),
                DB::raw("0 as rcv_amount"),
                DB::raw("0 as rtv_qty"),
                DB::raw("0 as rtv_amount"),
                DB::raw("0 as sales_qty"),
                DB::raw("0 as sales_amount"),
                DB::raw("0 as sales_return_qty"),
                DB::raw("0 as sales_return_amount"),
                DB::raw("0 as end_qty"),
                DB::raw("0 as end_amount")
            )
                ->where('transaction_date', '>=', date("Y-m-01", strtotime($smonth . "-01")))
                ->where('transaction_date', '<', date("Y-m-01", strtotime($next_month . "-01")))
                ->whereNotExists(function ($query) use ($smonth) {
                    $query->select(DB::raw(1))
                        ->from('stock_monthly_summary')
                        ->where('stock_monthly_summary.transaction_month', date("Y-m", strtotime($smonth)))
                        ->whereRaw('stock_monthly_summary.product_item_id = stock_transaction_log.product_item_id ');
                })
                ->orderBy('product_item_id', 'asc')->get();
            $webDataAdd = [];
            foreach ($data as $key => $value) {
                $webDataAdd[$key] = [
                    $agent_id,
                    $smonth,
                    $value->product_id,
                    $value->product_item_id,
                    $value->begin_qty,
                    $value->begin_amount,
                    $value->item_cost,
                    $value->rcv_qty,
                    $value->rcv_amount,
                    $value->rtv_qty,
                    $value->rtv_amount,
                    $value->sales_qty,
                    $value->sales_amount,
                    $value->sales_return_qty,
                    $value->sales_return_amount,
                    $value->end_qty,
                    $value->end_amount,
                    $user_id,
                    $user_id,
                    $now,
                    $now
                ];
            }
            if ($webDataAdd) {
                $instance = new StockMonthlySummary();
                $batchSize = 50;
                $add = Batch::insert($instance, $addColumn, $webDataAdd, $batchSize);
                $new += $add['totalRows'];
            }
            /* 1b */
            DB::commit();
            if ($new > 0) {
                $status = true;
            } else {
                $status = false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $status = false;
        }
        return $status;

    }

}
