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

        $results = Lookup_values_v::select('code', 'description')
            ->where('agent_id', $agent_id)
            ->where('type_code', 'APPLICABLE_PAGE')
            ->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $results;
    }

    /**
     * 取得行銷活動類型
     *
     * @param array $query_datas
     * @return object
     */
    public function getCampaignTypes($query_datas = [])
    {
        $agent_id = Auth::user()->agent_id;

        $results = Lookup_values_v::where('agent_id', $agent_id)
            ->where('type_code', 'CAMPAIGN_TYPE');

        if (!empty($query_datas['udf_01'])) {
            $results = $results->where('udf_01', $query_datas['udf_01']);
        }

        if (!empty($query_datas['code'])) {
            $results = $results->where('code', $query_datas['code']);
        }

        $results = $results->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $results;
    }

    /**
     * 取得發票捐贈機構
     *
     * @param array $query_datas
     * @return object
     */
    public function getDonatedInstitutions($query_datas = [])
    {
        $agent_id = Auth::user()->agent_id;

        $results = Lookup_values_v::where('agent_id', $agent_id)
            ->where('type_code', 'DONATED_INSTITUTION');

        if (isset($query_datas['code'])) {
            $results = $results->where('code', $query_datas['code']);
        }

        $results = $results->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $results;
    }
}
