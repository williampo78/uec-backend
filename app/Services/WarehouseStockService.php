<?php
namespace App\Services;

use App\Models\WarehouseStock;
use Illuminate\Database\Eloquent\Collection;

class WarehouseStockService
{
    /**
     * 取得倉庫庫存
     *
     * @param array $datas
     * @return Collection
     */
    public function getWarehouseStocks(array $datas = []) :Collection
    {
        $warehouse_stocks = new WarehouseStock;

        if (isset($datas['warehouse_id'])) {
            $warehouse_stocks = $warehouse_stocks->where('warehouse_id', $datas['warehouse_id']);
        }

        if (isset($datas['product_item_id'])) {
            $warehouse_stocks = $warehouse_stocks->where('product_item_id', $datas['product_item_id']);
        }

        return $warehouse_stocks->get();
    }
}
