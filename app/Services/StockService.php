<?php


namespace App\Services;

use App\Models\ProductItems;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\WarehouseStock;
use App\Models\SysConfig;

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
     * @params : $number = 倉別，$item_id = 商品規格id)
     */
    public function getStockByItem($number = null, $item_id = null)
    {
        $stock = WarehouseStock::select("warehouse_stock.stock_qty as stockQty", "products.order_limited_qty as limitedQty", "warehouse_stock.warehouse_id", "warehouse_stock.id")
            ->join("warehouse", "warehouse.id", "=", "warehouse_stock.warehouse_id")
            ->join("product_items", "product_items.id", "=", "warehouse_stock.product_item_id")
            ->join("products", "products.id", "=", "product_items.product_id")
            ->where("warehouse.delete", "=", "0")
            ->where("warehouse.number", "=", $number)
            ->where("warehouse_stock.product_item_id", "=", $item_id)
            ->first();
        return $stock;
    }

    /*
     * 找出商城的倉庫代碼
     */
    public function getWarehouseConfig()
    {
        $value = SysConfig::select("sys_config.config_value")
            ->join("warehouse", "warehouse.number", "=", "sys_config.config_value")
            ->where("warehouse.delete", "=", "0")
            ->where("sys_config.config_key", "=", "EC_WAREHOUSE_GOODS")
            ->where("sys_config.active", "=", "1")->first();
        return $value->config_value;
    }

    /*
     * 找出產品的庫存數
     * @params : $number = 倉別，$prod_id = 商品id)
     */
    public function getStockByProd($number = null, $prod_id = null)
    {
        $stock = ProductItems::selectRaw("sum(warehouse_stock.stock_qty) as stock_qty")
            ->join("warehouse_stock", "warehouse_stock.product_item_id","=","product_items.id")
            ->join("warehouse","warehouse.id","=","warehouse_stock.warehouse_id")
            ->where("warehouse.number", "=", $number)
            ->where("product_items.product_id", "=", $prod_id)
            ->groupBy('product_items.product_id')->first();

        return $stock;
    }

}
