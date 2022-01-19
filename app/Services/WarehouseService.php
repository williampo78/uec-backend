<?php

namespace App\Services;

use App\Models\supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    public function getWarehouseList(){
        $agent_id = Auth::user()->agent_id;
        return Warehouse::where('agent_id', $agent_id)->orderBy('id')->get();
    }

    /**
     * 取得倉庫資料
     *
     * @param array $datas
     * @return Collection
     */
    public function getWarehouses(array $datas = []) :Collection
    {
        $warehouses = Warehouse::where('delete', 0);

        // 代碼
        if (isset($datas['number'])) {
            $warehouses = $warehouses->where('number', $datas['number']);
        }

        return $warehouses->get();
    }
}
