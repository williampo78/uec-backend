<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\StockMonthlySummary;


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

}
