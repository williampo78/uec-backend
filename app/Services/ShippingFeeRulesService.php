<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Log;

class ShippingFeeRulesService
{

    public function getShippingFee($method)
    {
        $result = DB::select("SELECT * FROM shipping_fee_rules where lgst_method = '" . $method . "' and current_timestamp() between start_at and end_at and active=1");
        $data = [];
        foreach ($result as $rule) {
            $data[$method] = $rule;
        }
        return $data;
    }

}
