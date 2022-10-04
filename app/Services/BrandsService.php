<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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
        $user = auth()->user();
        $brand = new Brand ;
        if($user){
            $brand = $brand->where('agent_id',$user->agent_id) ;
        }
        return $brand->where('active', 1)->get();
    }

    public function getBrand($id)
    {
        return Brand::where('id', $id)->where('active', 1)->get();
    }

    public function getBrandForSearch()
    {
        $result = Brand::select(
            DB::raw('id'),
            DB::raw('"BRAND" as attribute_type'),
            DB::raw('brand_code as code'),
            DB::raw('brand_name as description')
        )
            ->where('active', 1)->orderBy('sort')->get()->toArray();
        return $result;
    }
}
