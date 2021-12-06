<?php

namespace App\Services;

use App\Models\Lookup_values_v;
use Illuminate\Support\Facades\Auth;

class LookupValuesVService
{
    /**
     * 取得廣告版位適用頁面清單
     *
     * @return object
     */
    public function getApplicablePages()
    {
        $agent_id = Auth::user()->agent_id;

        $result = Lookup_values_v::select('code', 'description')
            ->where('agent_id', $agent_id)
            ->where('type_code', 'APPLICABLE_PAGE')
            ->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $result;
    }

    /**
     * 取得行銷活動類型
     *
     * @param string $level_code 活動階層(ALL:全部、PRD：單品、CART：滿額)
     * @return object
     */
    public function getCampaignTypes($level_code = 'ALL')
    {
        $agent_id = Auth::user()->agent_id;

        $result = Lookup_values_v::select('code', 'description')
            ->where('agent_id', $agent_id)
            ->where('type_code', 'CAMPAIGN_TYPE');

        if ($level_code == 'PRD') {
            $result = $result->where('udf_01', 'PRD');
        } elseif ($level_code == 'CART') {
            $result = $result->where('udf_01', 'CART');
        }

        $result = $result->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $result;
    }
}
