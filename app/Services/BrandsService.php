<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\Brands;
use Log;
class BrandsService
{
    public function __construct()
    {
        
    }
    public function getBrands(){
        $agent_id = Auth::user()->agent_id;
        return Brands::where('agent_id', $agent_id)->where('active', 1)->get();
    }
}
