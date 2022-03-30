<?php


namespace App\Services;

use App\Models\StockTransactionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\StockMonthlySummary;
use Batch;
use function Symfony\Component\Translation\t;

class SummaryStockService
{
    public function getSummaryStock($data)
    {
        $result = StockMonthlySummary::select("stock_monthly_summary.*", "products.product_name", "product_items.item_no", "product_items.spec_2_value",
            DB::raw("FORMAT(stock_monthly_summary.begin_amount, 2) as begin_amount_display"),
            DB::raw("FORMAT(stock_monthly_summary.end_amount, 2) as end_amount_display"),
            DB::raw("FORMAT(stock_monthly_summary.sales_amount, 2) as sales_amount_display"),
            DB::raw("FORMAT(stock_monthly_summary.sales_return_amount, 2) as sales_return_amount_display"),
            DB::raw("FORMAT(stock_monthly_summary.item_cost, 2) as item_cost_display"),
            DB::raw("(CASE WHEN product_items.spec_1_value = '0' THEN '' ELSE product_items.spec_1_value END) AS spec_1_value"),
            DB::raw("0 as adj_qty"),
            DB::raw("0 as adj_amount"),
            DB::raw("0 as shift_qty"),
            DB::raw("0 as shift_amount"),
            )
            ->join("products", "products.id", "=", "stock_monthly_summary.product_id")
            ->where("stock_monthly_summary.transaction_month", $data['smonth'])
            ->join("product_items", "product_items.id", "=", "stock_monthly_summary.product_item_id");

        if ($data['item_id_start'] != '' && $data['item_id_end'] != '') {
            $result = $result->whereBetween('product_items.item_no', [$data['item_id_start'] . '%', $data['item_id_end'] . '%']);
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
            DB::raw('format(sum(stock_monthly_summary.begin_amount),2) begin_amount'),
            DB::raw('sum(stock_monthly_summary.end_qty) end_qty'),
            DB::raw('format(sum(stock_monthly_summary.end_amount),2) end_amount'),
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
        $lastRecordDate = StockMonthlySummary::max('transaction_month'); //01
        //檢查欲滾算月分-前月是否有資料
        $previous_month = date("Y-m", strtotime($smonth . " -1 month"));//12
        $previousCount = StockMonthlySummary::getMonthData($previous_month);
        $data = [];
        if (strtotime($lastRecordDate) > strtotime($smonth)) {
            $data['message'] = '不允許執行滾算';
            $data['result'] = '已有' . $lastRecordDate . '的滾算記錄，不允許重算' . $smonth . '的資料';
            $data['status'] = false;
            $data['alert'] = false;
        } elseif ($lastRecordDate == "" or $lastRecordDate == null) {
            $insert = $this->insertStockMonthlySummary($smonth); //先新增期初
            if ($insert) {
                //取得進貨資訊
                $update = $this->updateStockMonthlySummary($smonth); //更新推算其他數量與金額
                if ($update) {
                    $data['message'] = '執行滾算完成1';
                    $data['result'] = $smonth . "資料已經滾算完成！";
                    $data['status'] = true;
                    $data['alert'] = false;
                }
            } else {
                $data['message'] = '執行滾算失敗';
                $data['result'] = $smonth . "執行滾算失敗！";
                $data['status'] = false;
                $data['alert'] = false;
            }
        } elseif (strtotime($lastRecordDate) < strtotime($smonth)) {
            if ($previousCount > 0) { //上月有資料
                $insert = $this->insertStockMonthlySummary($smonth); //先新增期初
                if ($insert) {
                    //取得進貨資訊
                    $update = $this->updateStockMonthlySummary($smonth); //更新推算其他數量與金額
                    if ($update) {
                        $data['message'] = '執行滾算完成2';
                        $data['result'] = $smonth . "資料已經滾算完成！";
                        $data['status'] = true;
                        $data['alert'] = false;
                    }
                } else {
                    $data['message'] = '執行滾算失敗2';
                    $data['result'] = $smonth . "執行滾算失敗！";
                    $data['status'] = false;
                    $data['alert'] = false;
                }
            } else {
                $data['message'] = '不允許執行滾算3';
                $data['result'] = '目前最大滾算月份為' . $lastRecordDate . '，不允許滾算' . $smonth . '的資料';
                $data['status'] = false;
                $data['alert'] = false;
            }
        } elseif (strtotime($lastRecordDate) == strtotime($smonth)) {//01=01
            $delPrevious = $this->deleteStockMonthlySummary($smonth); //先刪除本次有新增的
            if ($delPrevious) {
                $insert = $this->insertStockMonthlySummary($smonth); //先新增期初
                if ($insert) {
                    //取得進貨資訊
                    $update = $this->updateStockMonthlySummary($smonth); //更新推算其他數量與金額
                    if ($update) {
                        $data['message'] = '執行滾算完成1';
                        $data['result'] = $smonth . "資料已經滾算完成！";
                        $data['status'] = true;
                        $data['alert'] = true;
                    }
                } else {
                    $data['message'] = '執行滾算失敗';
                    $data['result'] = $smonth . "執行滾算失敗！";
                    $data['status'] = false;
                    $data['alert'] = false;
                }
            }
        }


        return $data;

    }

    /*
     * step 1A 前期尚有餘額的品項：以前期期末數，當作本期期初數
     * step 1B 本期有交易，但前期無餘額的品項
     */
    public function insertStockMonthlySummary($smonth)
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

            $data = $data->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('product_items')
                    ->whereRaw('product_items.id = stock_monthly_summary.product_item_id ');
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
                    round($value->begin_amount, 2),
                    $value->item_cost,
                    $value->rcv_qty,
                    $value->rcv_amount,
                    $value->rtv_qty,
                    $value->rtv_amount,
                    $value->sales_qty,
                    round($value->sales_amount, 2),
                    $value->sales_return_qty,
                    round($value->sales_return_amount, 2),
                    $value->end_qty,
                    round($value->end_amount, 2),
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
                //$new += $add['totalRows'];
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
                    round($value->begin_amount, 2),
                    $value->item_cost,
                    $value->rcv_qty,
                    $value->rcv_amount,
                    $value->rtv_qty,
                    $value->rtv_amount,
                    $value->sales_qty,
                    round($value->sales_amount, 2),
                    $value->sales_return_qty,
                    round($value->sales_return_amount, 2),
                    $value->end_qty,
                    round($value->end_amount, 2),
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
                //$new += $add['totalRows'];
            }
            /* 1b */
            DB::commit();
            $status = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $status = false;
        }
        return $status;

    }

    /*
     * 確認重新滾算時，刪除該月分的資料
     */
    public function deleteStockMonthlySummary($smonth)
    {
        DB::beginTransaction();
        try {
            $reslut = StockMonthlySummary::where('transaction_month', $smonth)->delete();
            DB::commit();
            $status = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $status = false;
        }
        return $status;
    }

    /*
     * 取得進銷資訊
     */
    public function updateStockMonthlySummary($smonth)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $next_month = date("Y-m", strtotime($smonth . " +1 month"));

        $data = StockMonthlySummary::select(
            'id',
            'begin_qty',
            'begin_amount',
            DB::raw("IFNULL((select sum(transaction_qty)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type = 'PO_RCV'
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as rcv_qty"),
            DB::raw("IFNULL((select sum(transaction_nontax_amount)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type = 'PO_RCV'
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as rcv_amount"),
            DB::raw("IFNULL((select sum(transaction_qty)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type = 'PO_RTV'
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as rtv_qty"),
            DB::raw("IFNULL((select sum(transaction_nontax_amount)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type = 'PO_RTV'
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as rtv_amount"),
            DB::raw("IFNULL((select sum(transaction_qty)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type = 'ORDER_SHIP'
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as sales_qty"),
            DB::raw("IFNULL((select sum(transaction_qty)
                    from stock_transaction_log stl
                    where stl.transaction_date >= STR_TO_DATE(concat('" . $smonth . "-01'), '%Y-%m-%d')
                    and stl.transaction_date < STR_TO_DATE(concat('" . $next_month . "-01'), '%Y-%m-%d')
                    and stl.transaction_type in('ORDER_CANCEL', 'ORDER_VOID', 'ORDER_RTN')
                    and stl.product_item_id = stock_monthly_summary.product_item_id),0) as sales_return_qty")
        )
            ->where('transaction_month', $smonth)->orderBy('id', 'asc')->get();
        $webDataUpd = [];
        foreach ($data as $key => $value) {
            $end_qty = ($value->begin_qty + $value->rcv_qty + $value->rtv_qty + $value->sales_qty + $value->sales_return_qty);//推算期末數
            $net_qty = ($value->begin_qty + $value->rcv_qty + $value->rtv_qty);//本期進貨淨量
            $item_cost = round((round($value->begin_amount, 2) + $value->rcv_amount + $value->rtv_amount) / ($net_qty == 0 ? 1 : $net_qty), 2);//推算單位成本
            $sales_amount = round($value->sales_qty * $item_cost, 2);//推算銷貨金額
            $sales_return_amount = round($value->sales_return_qty * $item_cost, 2);//推算銷退金額
            $end_amount = round(($end_qty * $item_cost), 2);
            $webDataUpd[$key] = [
                "id" => $value->id,
                "rcv_qty" => ($value->rcv_qty == 0 ? 0 : $value->rcv_qty),
                "rcv_amount" => ($value->rcv_amount == 0 ? 0 : $value->rcv_amount),
                "rtv_qty" => ($value->rtv_qty == 0 ? 0 : $value->rtv_qty),
                "rtv_amount" => ($value->rtv_amount == 0 ? 0 : $value->rtv_amount),
                "sales_qty" => ($value->sales_qty == 0 ? 0 : $value->sales_qty),
                "sales_return_qty" => ($value->sales_return_qty == 0 ? 0 : $value->sales_return_qty),
                "end_qty" => ($end_qty == 0 ? 0 : $end_qty),
                "end_amount" => ($end_amount == 0 ? 0 : $end_amount),
                "item_cost" => ($item_cost == 0 ? 0 : $item_cost),
                "sales_amount" => ($sales_amount == 0 ? 0 : $sales_amount),
                "sales_return_amount" => ($sales_return_amount == 0 ? 0 : $sales_return_amount),
                "updated_by" => $user_id,
                "updated_at" => $now
            ];
        }
        DB::beginTransaction();
        try {
            if ($webDataUpd) {
                $instance = new StockMonthlySummary();
                $upd = Batch::update($instance, $webDataUpd, 'id');
            }
            DB::commit();
            $status = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $status = false;
        }
        return $status;
    }

}
