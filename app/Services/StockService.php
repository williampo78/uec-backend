<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\WarehouseStock;

class StockService
{
    public function getStock($number = null)
    {
        $stock = WarehouseStock::select("warehouse.name", "warehouse_stock.*")
            ->join("warehouse", "warehouse.id", "=", "warehouse_stock.warehouse_id")
            ->where("warehouse.delete", "=", "0")
            ->where("warehouse.number", "=", $number)->get();
        return $stock;
    }


    /*
     * 找出產品的庫存數
     * @params : $number = 倉別，$item_id = 商品規格id，$show = qty 只回傳庫存數)
     */
    public function getStockByItem($number = null, $item_id = null, $show = null)
    {
        $stock = WarehouseStock::select("warehouse.name", "warehouse_stock.*")
            ->join("warehouse", "warehouse.id", "=", "warehouse_stock.warehouse_id")
            ->where("warehouse.delete", "=", "0")
            ->where("warehouse.number", "=", $number)
            ->where("warehouse_stock.product_item_id", "=", $item_id)->first();
        if ($show == 'qty') {
            if ($stock) {
                return $stock->stock_qty;
            } else {
                return 0;
            }
        } else {
            if ($stock) {
                return $stock;
            } else {
                return 0;
            }
        }
    }

}
