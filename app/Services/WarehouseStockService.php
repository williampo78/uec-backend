<?php
namespace App\Services;

use App\Models\WarehouseStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class WarehouseStockService
{
    /**
     * 取得倉庫庫存
     *
     * @param integer $warehouseId
     * @param integer $productItemId
     * @return Model|null
     */
    public function getWarehouseStock(int $warehouseId, int $productItemId): ?Model
    {
        return WarehouseStock::where('warehouse_id', $warehouseId)->where('product_item_id', $productItemId)->first();
    }
}
