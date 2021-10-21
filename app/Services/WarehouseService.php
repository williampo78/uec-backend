<?php

namespace App\Services;

use App\Models\supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    public function __construct()
    {
    }

    public function getWarehouseList(){
        $agent_id = Auth::user()->agent_id;
        return Warehouse::where('agent_id', $agent_id)->orderBy('id')->get();
    }
}
