<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\AdSlots;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdvertisementService
{
    /**
     * 取得廣告版位資料
     *
     * @param array $query_data 查詢參數
     * @return object ORM物件
     */
    public function getSlots($query_data)
    {
        $agent_id = Auth::user()->agent_id;

        $result = AdSlots::select('ad_slots.*', 'lookup_values_v.description')
            ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
            ->where('ad_slots.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE');

        if (!empty($query_data['applicable_page'])) {
            $result = $result->where('ad_slots.applicable_page', $query_data['applicable_page']);
        }

        if (!empty($query_data['device'])) {
            if ($query_data['device'] == 'desktop') {
                $result = $result->where('ad_slots.is_desktop_applicable', 1);
            } elseif ($query_data['device'] == 'mobile') {
                $result = $result->where('ad_slots.is_mobile_applicable', 1);
            }
        }

        if (!empty($query_data['active'])) {
            if ($query_data['active'] == 'enabled') {
                $result = $result->where('ad_slots.active', 1);
            } elseif ($query_data['active'] == 'disabled') {
                $result = $result->where('ad_slots.active', 0);
            }
        }

        $result = $result->orderBy("ad_slots.applicable_page", "asc")
            ->orderBy("ad_slots.slot_code", "asc")
            ->get();

        return $result;
    }

    public function getSlotById($id)
    {
        $agent_id = Auth::user()->agent_id;

        $result = AdSlots::select('ad_slots.*', 'lookup_values_v.description')
            ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
            ->where('ad_slots.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE')
            ->find($id);

        return $result;
    }

    public function updateSlot($input_data)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now()->timestamp;

        try {
            AdSlots::findOrFail($input_data['id'])->update([
                'active' => $input_data['active'],
                'remark' => $input_data['remark'],
                'updated_by' => $user_id,
                'updated_at' => $now,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::info($e);
        }
    }
}
