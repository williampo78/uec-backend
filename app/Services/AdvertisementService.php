<?php

namespace App\Services;

use App\Models\AdSlots;
use Illuminate\Support\Facades\Auth;

class AdvertisementService
{
    public function getSlots()
    {
        $agent_id = Auth::user()->agent_id;

        return AdSlots::select('ad_slots.*', 'lookup_values_v.description')
                                ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
                                ->where('ad_slots.agent_id', $agent_id)
                                ->where('lookup_values_v.agent_id', $agent_id)
                                ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE')
                                ->orderBy("ad_slots.applicable_page", "asc")
                                ->orderBy("ad_slots.slot_code", "asc")
                                ->get();
    }
}
