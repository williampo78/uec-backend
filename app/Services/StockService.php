<?php


namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use App\Models\SysConfig;
use App\Models\ProductItem;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $stock = ProductItem::selectRaw("sum(warehouse_stock.stock_qty) as stock_qty")
            ->join("warehouse_stock", "warehouse_stock.product_item_id", "=", "product_items.id")
            ->join("warehouse", "warehouse.id", "=", "warehouse_stock.warehouse_id")
            ->where("warehouse.number", "=", $number)
            ->where("product_items.product_id", "=", $prod_id)
            ->groupBy('product_items.product_id')->first();

        return $stock;
    }

    /*
     * 取得上架期間內有庫存的商品
     */
    public function getProductInStock($warehouseCode)
    {
        $now = Carbon::now();
        $data = [];
        $products = Product::select(
            DB::raw('products.id as product_id'),
            DB::raw('products.product_no'),
            DB::raw('products.product_name'),
            DB::raw('(SELECT photo_name FROM product_photos WHERE products.id = product_photos.product_id order by sort limit 0, 1) AS product_photo_name'),
            DB::raw('product_items.id as product_item_id'),
            DB::raw('product_items.item_no as product_item_no'),
            DB::raw('product_items.spec_1_value as product_item_spec1'),
            DB::raw('product_items.spec_2_value as product_item_spec2'),
            DB::raw('product_items.photo_name as product_item_photo_name'),
            DB::raw('warehouse_stock.stock_qty')
        )
            ->join('product_items', 'product_items.product_id', '=', 'products.id')
            ->join('warehouse_stock', 'warehouse_stock.product_item_id', '=', 'product_items.id')
            ->join('warehouse', 'warehouse.id', '=', 'warehouse_stock.warehouse_id')
            ->where('warehouse_stock.stock_qty', '>', 0)
            ->where('warehouse.number', $warehouseCode)
            ->where('products.approval_status', 'APPROVED')
            ->where('products.start_launched_at', '<=', $now)
            ->where('products.end_launched_at', '>=', $now)
            ->where('product_items.status', 1)
            ->orderBy('products.id')
            ->orderBy('product_items.sort')
            ->get();
        foreach ($products as $product) {
            $data[$product->product_id] = $product;
        }
        return $data;
    }
}
