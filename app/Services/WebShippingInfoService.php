<?php


namespace App\Services;

use App\Models\WebShippingInfo;

class WebShippingInfoService
{

    public function getShippingInfo($method)
    {
       $result = WebShippingInfo::where('info_code', $method)->get();

        foreach ($result as $rule) {
            $data[$method] = $rule;
        }
        return $data;
    }

}
