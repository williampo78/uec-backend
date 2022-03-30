<?php

namespace App\Services;

use Log;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandsService
{
    public function __construct()
    {

    }

    public function getBrands()
    {
        $agent_id = Auth::user()->agent_id;
        return Brand::where('agent_id', $agent_id)->where('active', 1)->get();
    }

    public function getBrand($id)
    {
        return Brand::where('id', $id)->where('active', 1)->get();
    }
}
