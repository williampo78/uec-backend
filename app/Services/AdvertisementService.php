<?php

namespace App\Services;

use App\Models\AdSlots;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdvertisementService
{
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

        if (!empty($query_data['status'])) {
            if ($query_data['status'] == 'enabled') {
                $result = $result->where('ad_slots.active', 1);
            } elseif ($query_data['status'] == 'disabled') {
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

    public function getSlotTypeOption()
    {
        //版位類型，I：圖檔(image)、II：母子圖檔(image+image)、T：文字(text)、S：商品、IS：圖檔+商品、X：非人工上稿
        return [
            'I' => '圖檔',
            'II' => '母子圖檔',
            'T' => '文字',
            'S' => '商品',
            'IS' => '圖檔 + 商品',
            'X' => '非人工上稿',
        ];
    }

    public function getActiveOption()
    {
        return [
            1 => '啟用',
            0 => '關閉',
        ];
    }
}
