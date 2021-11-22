<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\AdSlots;
use App\Models\AdSlotContents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdvertisementService
{
    /**
     * 取得廣告版位全部資料
     *
     * @param array $query_data 查詢參數
     * @return object ORM物件
     */
    public function getSlots($query_data = [])
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

    /**
     * 取得廣告版位單筆資料
     *
     * @param int $id
     * @return object ORM物件
     */
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

    /**
     * 更新廣告版位資料
     *
     * @param array $input_data
     * @return void
     */
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
            Log::warning($e);
        }
    }

    public function getSlotContents($query_data = [])
    {
        $agent_id = Auth::user()->agent_id;

        $result = AdSlots::select(
                'ad_slots.slot_code',
                'ad_slots.slot_desc',
                'ad_slots.applicable_page',
                'ad_slots.is_mobile_applicable',
                'ad_slots.is_desktop_applicable',
                'ad_slots.slot_type',
                'ad_slots.remark',
                'ad_slots.is_user_defined',
                'ad_slots.id AS slot_id',
                'ad_slots.active AS slot_active',
                'ad_slots.agent_id AS slot_agent_id',
                'ad_slots.created_by AS slot_created_by',
                'ad_slots.updated_by AS slot_updated_by',
                'ad_slots.created_at AS slot_created_at',
                'ad_slots.updated_at AS slot_updated_at',
                'ad_slot_contents.start_at',
                'ad_slot_contents.end_at',
                'ad_slot_contents.slot_color_code',
                'ad_slot_contents.slot_icon_name',
                'ad_slot_contents.slot_title',
                'ad_slot_contents.product_assigned_type',
                'ad_slot_contents.id AS slot_content_id',
                'ad_slot_contents.active AS slot_content_active',
                'ad_slot_contents.agent_id AS slot_content_agent_id',
                'ad_slot_contents.created_by AS slot_content_created_by',
                'ad_slot_contents.updated_by AS slot_content_updated_by',
                'ad_slot_contents.created_at AS slot_content_created_at',
                'ad_slot_contents.updated_at AS slot_content_updated_at',
                'lookup_values_v.description',
            )
            ->join('ad_slot_contents', 'ad_slot_contents.slot_id', '=', 'ad_slots.id')
            ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
            ->where('ad_slots.agent_id', $agent_id)
            ->where('ad_slot_contents.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE');

        // 版位查詢
        if (!empty($query_data['block'])) {
            $result = $result->where('ad_slots.id', $query_data['block']);
        }

        // 上下架狀態查詢
        if (!empty($query_data['launch_status'])) {
            if ($query_data['launch_status'] == 'enabled') {
                $result = $result->where('ad_slot_contents.start_at', '<=', Carbon::now());
                $result = $result->where('ad_slot_contents.end_at', '>=', Carbon::now());
                $result = $result->where('ad_slot_contents.active', 1);
            } else {
                $result = $result->where(function ($query) {
                    $query->where('ad_slot_contents.start_at', '>=', Carbon::now())
                          ->orWhere('ad_slot_contents.end_at', '<=', Carbon::now())
                          ->orWhere('ad_slot_contents.active', 0);
                });
            }
        }

        // 上架起始日查詢
        if (!empty($query_data['start_at'])) {
            try {
                $start_at = Carbon::parse($query_data['start_at'])->format('Y-m-d H:i:s');
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                Log::warning($e);
            }

            if (isset($start_at)) {
                $result = $result->where('ad_slot_contents.start_at', '>=', $start_at);
            }
        }

        // 上架結束日查詢
        if (!empty($query_data['end_at'])) {
            try {
                $end_at = Carbon::parse($query_data['end_at'])->format('Y-m-d H:i:s');
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                Log::warning($e);
            }

            if (isset($end_at)) {
                $result = $result->where('ad_slot_contents.end_at', '<=', $end_at);
            }
        }

        $result = $result->orderBy("ad_slots.applicable_page", "asc")
            ->orderBy("ad_slots.slot_code", "asc")
            ->get();

        return $result;
    }
}
