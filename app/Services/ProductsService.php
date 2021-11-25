<?php

namespace App\Services;

use App\Models\Products;
use Illuminate\Support\Facades\Auth;

class ProductsService
{
    public function __construct()
    {

    }

    public function getProducts($data = [])
    {
        $agent_id = Auth::user()->agent_id;

        $result = Products::where('agent_id', $agent_id)
            ->get();

        return $result;
    }
}
