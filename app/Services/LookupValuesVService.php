<?php

namespace App\Services;

use App\Models\LookupValuesV;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LookupValuesVService
{
    /**
     * 取得共用設定檔
     *
     * @param array $query_datas
     * @return object
     */
    public function getLookupValuesVs($query_datas = [])
    {
        $results = LookupValuesV::where('active', 1);

        if (!isset($query_datas['disable_agent_id_auth'])) {
            $agent_id = Auth::user()->agent_id;
            $results = $results->where('agent_id', $agent_id);
        }

        if (isset($query_datas['type_code'])) {
            $results = $results->where('type_code', $query_datas['type_code']);
        }

        if (isset($query_datas['udf_01'])) {
            $results = $results->where('udf_01', $query_datas['udf_01']);
        }

        if (isset($query_datas['code'])) {
            $results = $results->where('code', $query_datas['code']);
        }

        $results = $results->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $results;
    }

    /**
     * 取得給Backend的共用設定檔
     *
     * @param array $queryData
     * @return Collection
     */
    public function getLookupValuesVsForBackend(array $queryData = []): Collection
    {
        $user = Auth::user();
        $results = LookupValuesV::where('active', 1)
            ->where('agent_id', $user->agent_id);

        if (isset($queryData['type_code'])) {
            $results = $results->where('type_code', $queryData['type_code']);
        }

        if (isset($queryData['udf_01'])) {
            $results = $results->where('udf_01', $queryData['udf_01']);
        }

        if (isset($queryData['code'])) {
            $results = $results->where('code', $queryData['code']);
        }

        $results = $results->orderBy("sort", "asc")
            ->orderBy("code", "asc")
            ->get();

        return $results;
    }
}
