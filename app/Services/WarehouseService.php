<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    /**
     * 取得倉庫列表
     *
     * @return Collection
     */
    public function getWarehouseList(): Collection
    {
        $user = auth()->user();
        return Warehouse::where('agent_id', $user->agent_id)
            ->where('delete', 0)
            ->get();
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
