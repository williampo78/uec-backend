<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    public function getWarehouseList()
    {
        $agent_id = Auth::user()->agent_id;
        return Warehouse::where('agent_id', $agent_id)->orderBy('id')->get();
    }

    /**
     * 取得單筆倉庫資料
     *
     * @param string $number
     * @return Model|null
     */
    public function getWarehouseByNumber(string $number): ?Model
    {
        return Warehouse::where('delete', 0)->where('number', $number)->first();
    }
}
