<?php

namespace App\Services;

use App\Models\AdSlotContentDetails;
use App\Models\AdSlotContents;
use App\Models\AdSlots;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class AdvertisementService
{
    private const SLOT_CONTENTS_UPLOAD_PATH_PREFIX = 'advertisement/launch/';

    /**
     * 取得廣告版位全部資料
     *
     * @param array $query_data 查詢參數
     * @return object
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
     * @return object
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
     * @return boolean
     */
    public function updateSlot($input_data)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();

        $update_data = [];

        if (isset($input_data['active'])) {
            $update_data['active'] = $input_data['active'];
        }

        $update_data['remark'] = $input_data['remark'];
        $update_data['updated_by'] = $user_id;
        $update_data['updated_at'] = $now;

        try {
            AdSlots::findOrFail($input_data['id'])->update($update_data);
        } catch (ModelNotFoundException $e) {
            Log::warning($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * 取得廣告上架全部資料
     *
     * @param array $query_datas
     * @return Collection
     */
    public function getSlotContents($query_datas = []): Collection
    {
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();

        $result = AdSlotContents::select(
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
            'ad_slot_contents.start_at',
            'ad_slot_contents.end_at',
            'ad_slot_contents.slot_color_code',
            'ad_slot_contents.slot_icon_name',
            'ad_slot_contents.slot_title',
            'ad_slot_contents.product_assigned_type',
            'ad_slot_contents.id AS slot_content_id',
            'ad_slot_contents.active AS slot_content_active',
            'lookup_values_v.description',
        )
            ->join('ad_slots', 'ad_slots.id', '=', 'ad_slot_contents.slot_id')
            ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
            ->where('ad_slots.agent_id', $agent_id)
            ->where('ad_slot_contents.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE');

        // 版位查詢
        if (!empty($query_datas['slot_id'])) {
            $result = $result->where('ad_slots.id', $query_datas['slot_id']);
        }

        // 上下架狀態查詢
        if (!empty($query_datas['launch_status'])) {
            switch ($query_datas['launch_status']) {
                // 待上架
                case 'prepare_to_launch':
                    $result = $result->where(function ($query) use ($now) {
                        $query->where('ad_slot_contents.active', 1)
                            ->where('ad_slot_contents.start_at', '>', $now);
                    });
                    break;

                // 已上架
                case 'launched':
                    $result = $result->where(function ($query) use ($now) {
                        $query->where('ad_slot_contents.active', 1)
                            ->where('ad_slot_contents.start_at', '<=', $now)
                            ->where('ad_slot_contents.end_at', '>=', $now);
                    });
                    break;

                // 下架
                case 'not_launch':
                    $result = $result->where(function ($query) use ($now) {
                        $query->where('ad_slot_contents.active', 1)
                            ->where('ad_slot_contents.end_at', '<', $now);
                    });
                    break;

                // 關閉
                case 'disabled':
                    $result = $result->where(function ($query) {
                        $query->where('ad_slot_contents.active', 0);
                    });
                    break;
            }
        }

        // 上架起始日開始時間
        if (!empty($query_datas['start_at_start'])) {
            $result = $result->whereDate('ad_slot_contents.start_at', '>=', $query_datas['start_at_start']);
        }

        // 上架起始日結束時間
        if (!empty($query_datas['start_at_end'])) {
            $result = $result->whereDate('ad_slot_contents.start_at', '<=', $query_datas['start_at_end']);
        }

        // 版位標題
        if (isset($query_datas['slot_title'])) {
            $result = $result->where('ad_slot_contents.slot_title', 'like', '%' . $query_datas['slot_title'] . '%');
        }

        $result = $result->orderBy("ad_slots.applicable_page", "asc")
            ->orderBy("ad_slots.slot_code", "asc")
            ->get();

        return $result;
    }

    /**
     * 取得廣告上架單筆資料
     *
     * @param int $id
     * @return array
     */
    public function getSlotContentById($id)
    {
        $agent_id = Auth::user()->agent_id;
        $result = [];

        $result['content'] = AdSlotContents::select(
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
            'ad_slots.photo_width',
            'ad_slots.photo_height',
            'ad_slot_contents.start_at',
            'ad_slot_contents.end_at',
            'ad_slot_contents.slot_color_code',
            'ad_slot_contents.slot_icon_name',
            'ad_slot_contents.slot_title',
            'ad_slot_contents.product_assigned_type',
            'ad_slot_contents.id AS slot_content_id',
            'ad_slot_contents.active AS slot_content_active',
            'ad_slot_contents.agent_id AS slot_content_agent_id',
            'lookup_values_v.description',
        )
            ->join('ad_slots', 'ad_slots.id', '=', 'ad_slot_contents.slot_id')
            ->leftJoin('lookup_values_v', 'ad_slots.applicable_page', '=', 'lookup_values_v.code')
            ->where('ad_slots.agent_id', $agent_id)
            ->where('ad_slot_contents.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'APPLICABLE_PAGE')
            ->find($id);

        $result['details'] = AdSlotContentDetails::where('ad_slot_content_id', $id)
            ->orderBy('sort', 'ASC')
            ->get();

        return $result;
    }

    /**
     * 新增廣告上架資料
     *
     * @param array $input_data
     * @return boolean
     */
    public function addSlotContents($input_data)
    {
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $slot = $this->getSlotById($input_data['slot_id']);

        DB::beginTransaction();

        try {
            $content_data = [];
            $content_data['agent_id'] = $agent_id;
            $content_data['slot_id'] = $slot->id;
            $content_data['active'] = $input_data['active'] ?? 1;
            $content_data['created_by'] = $user_id;
            $content_data['updated_by'] = $user_id;
            $content_data['created_at'] = $now;
            $content_data['updated_at'] = $now;

            if (!empty($input_data['start_at'])) {
                $content_data['start_at'] = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
            }

            if (!empty($input_data['end_at'])) {
                $content_data['end_at'] = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
            }

            // 使用者自定義版位，新增主色、標題
            if ($slot['is_user_defined'] == 1) {
                $content_data['slot_color_code'] = $input_data['slot_color_code'] ?? null;
                $content_data['slot_title'] = $input_data['slot_title'] ?? null;
            }
            // 使用者自定義版位，新增主色
            elseif ($slot['is_user_defined'] == 2) {
                $content_data['slot_color_code'] = $input_data['slot_color_code'] ?? null;
            }

            if ($slot['slot_type'] == 'S' || $slot['slot_type'] == 'IS') {
                $content_data['product_assigned_type'] = $input_data['product_assigned_type'] ?? null;
            }

            $slot_contents_id = AdSlotContents::insertGetId($content_data);

            // 使用者自定義版位，新增icon
            if ($slot['is_user_defined'] == 1) {
                $update_content_data = [];

                // 上傳圖片
                if (isset($input_data['slot_icon_name'])) {
                    $update_content_data['slot_icon_name'] = $input_data['slot_icon_name']->storePublicly(self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $slot_contents_id, 's3');
                }

                AdSlotContents::findOrFail($slot_contents_id)->update($update_content_data);
            }

            // 新增圖檔資料 (圖檔 or 圖檔+商品)
            if ($slot['slot_type'] == 'I' || $slot['slot_type'] == 'IS') {
                if (isset($input_data['image_block_id'])) {
                    foreach ($input_data['image_block_id'] as $key => $value) {
                        $detail_data = [];
                        $detail_data['ad_slot_content_id'] = $slot_contents_id;
                        $detail_data['data_type'] = 'IMG';
                        $detail_data['sort'] = $input_data['image_block_sort'][$key] ?? null;
                        $detail_data['image_alt'] = $input_data['image_block_image_alt'][$key] ?? null;
                        $detail_data['image_title'] = $input_data['image_block_image_title'][$key] ?? null;
                        $detail_data['image_abstract'] = $input_data['image_block_image_abstract'][$key] ?? null;
                        $detail_data['image_action'] = $input_data['image_block_image_action'][$key] ?? null;
                        $detail_data['target_url'] = $input_data['image_block_target_url'][$key] ?? null;
                        $detail_data['target_cate_hierarchy_id'] = $input_data['image_block_target_cate_hierarchy_id'][$key] ?? null;
                        $detail_data['is_target_blank'] = (isset($input_data['image_block_is_target_blank'][$key]) && $input_data['image_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $detail_data['created_by'] = $user_id;
                        $detail_data['updated_by'] = $user_id;
                        $detail_data['created_at'] = $now;
                        $detail_data['updated_at'] = $now;
                        // 上傳圖片
                        if (isset($input_data['image_block_image_name'][$key])) {
                            if ($slot->photo_width > 0 || $slot->photo_height > 0) { //如果有設置寬高 直接裁切圖片
                                $detail_data['image_name'] = $this->cropImageBlockUpload($input_data['image_block_image_name'][$key], $slot_contents_id, $slot);
                            } else {
                                $detail_data['image_name'] = $input_data['image_block_image_name'][$key]->storePublicly(self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $slot_contents_id, 's3');
                            }
                        }

                        AdSlotContentDetails::insert($detail_data);
                    }
                }
            }

            // 新增文字資料
            if ($slot['slot_type'] == 'T') {
                if (isset($input_data['text_block_id'])) {
                    foreach ($input_data['text_block_id'] as $key => $value) {
                        $detail_data = [];
                        $detail_data['ad_slot_content_id'] = $slot_contents_id;
                        $detail_data['data_type'] = 'TXT';
                        $detail_data['sort'] = $input_data['text_block_sort'][$key] ?? null;
                        $detail_data['texts'] = $input_data['text_block_texts'][$key] ?? null;
                        $detail_data['image_action'] = $input_data['text_block_image_action'][$key] ?? null;
                        $detail_data['target_url'] = $input_data['text_block_target_url'][$key] ?? null;
                        $detail_data['target_cate_hierarchy_id'] = $input_data['text_block_target_cate_hierarchy_id'][$key] ?? null;
                        $detail_data['is_target_blank'] = (isset($input_data['text_block_is_target_blank'][$key]) && $input_data['text_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $detail_data['created_by'] = $user_id;
                        $detail_data['updated_by'] = $user_id;
                        $detail_data['created_at'] = $now;
                        $detail_data['updated_at'] = $now;

                        AdSlotContentDetails::insert($detail_data);
                    }
                }
            }

            // 新增商品資料 (商品 or 圖檔+商品)
            if ($slot['slot_type'] == 'S' || $slot['slot_type'] == 'IS') {
                switch ($input_data['product_assigned_type']) {
                    // 指定商品
                    case 'P':
                        if (isset($input_data['product_block_product_id'])) {
                            foreach ($input_data['product_block_product_id'] as $key => $value) {
                                $detail_data = [];
                                $detail_data['ad_slot_content_id'] = $slot_contents_id;
                                $detail_data['data_type'] = 'PRD';
                                $detail_data['sort'] = $input_data['product_block_product_sort'][$key] ?? null;
                                $detail_data['product_id'] = $input_data['product_block_product_product_id'][$key] ?? null;
                                $detail_data['created_by'] = $user_id;
                                $detail_data['updated_by'] = $user_id;
                                $detail_data['created_at'] = $now;
                                $detail_data['updated_at'] = $now;

                                AdSlotContentDetails::insert($detail_data);
                            }
                        }
                        break;
                    // 指定分類
                    case 'C':
                        if (isset($input_data['product_block_category_id'])) {
                            foreach ($input_data['product_block_category_id'] as $key => $value) {
                                $detail_data = [];
                                $detail_data['ad_slot_content_id'] = $slot_contents_id;
                                $detail_data['data_type'] = 'PRD';
                                $detail_data['sort'] = $input_data['product_block_category_sort'][$key] ?? null;
                                $detail_data['web_category_hierarchy_id'] = $input_data['product_block_product_web_category_hierarchy_id'][$key] ?? null;
                                $detail_data['created_by'] = $user_id;
                                $detail_data['updated_by'] = $user_id;
                                $detail_data['created_at'] = $now;
                                $detail_data['updated_at'] = $now;

                                AdSlotContentDetails::insert($detail_data);
                            }
                        }
                        break;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * 更新廣告上架資料
     *
     * @param array $input_data
     * @return boolean
     */
    public function updateSlotContents($input_data)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $slot_content = $this->getSlotContentById($input_data['slot_content_id']);
        $slot = $this->getSlotById($slot_content['content']->slot_id);
        DB::beginTransaction();
        try {
            $update_content_data = [];

            if (isset($input_data['active'])) {
                $update_content_data['active'] = $input_data['active'];
            }

            if (isset($input_data['slot_color_code'])) {
                $update_content_data['slot_color_code'] = $input_data['slot_color_code'];
            }

            if (isset($input_data['slot_title'])) {
                $update_content_data['slot_title'] = $input_data['slot_title'];
            }

            if (isset($input_data['product_assigned_type'])) {
                $update_content_data['product_assigned_type'] = $input_data['product_assigned_type'];
            }

            $update_content_data['updated_by'] = $user_id;
            $update_content_data['updated_at'] = $now;

            if (isset($input_data['start_at'])) {
                $update_content_data['start_at'] = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
            }

            if (isset($input_data['end_at'])) {
                $update_content_data['end_at'] = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
            }

            if (isset($input_data['slot_icon_name'])) {
                // 移除舊圖片
                if (!empty($slot_content['content']->slot_icon_name)
                    && Storage::disk('s3')->exists($slot_content['content']->slot_icon_name)
                ) {
                    Storage::disk('s3')->delete($slot_content['content']->slot_icon_name);
                }

                // 上傳新圖片
                $update_content_data['slot_icon_name'] = $input_data['slot_icon_name']->storePublicly(self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $slot_content['content']->slot_content_id, 's3');
            }

            AdSlotContents::findOrFail($slot_content['content']->slot_content_id)->update($update_content_data);

            $details_image_name = $slot_content['details']->pluck('image_name', 'id')->all();

            // 處理圖檔 (圖檔 or 圖檔+商品)
            if ($slot_content['content']->slot_type == 'I' || $slot_content['content']->slot_type == 'IS') {
                $input_data['image_block_id'] = $input_data['image_block_id'] ?? [];
                $new_ids = array_keys($input_data['image_block_id']);

                $old_ids = $slot_content['details']->filter(function ($obj, $key) {
                    return $obj->data_type == 'IMG';
                })->pluck('id')->all();

                $delete_ids = array_diff($old_ids, $new_ids);
                $add_ids = array_diff($new_ids, $old_ids);

                // 移除資料
                if (!empty($delete_ids)) {
                    foreach ($slot_content['details'] as $obj) {
                        if (in_array($obj->id, $delete_ids)) {
                            // 移除圖片
                            if (!empty($obj->image_name)
                                && Storage::disk('s3')->exists($obj->image_name)
                            ) {
                                Storage::disk('s3')->delete($obj->image_name);
                            }

                            AdSlotContentDetails::destroy($obj->id);
                        }
                    }
                }

                // 新增、更新資料
                foreach ($input_data['image_block_id'] as $key => $value) {
                    // 新增資料
                    if (in_array($key, $add_ids)) {
                        $create_detail_data = [];
                        $create_detail_data['ad_slot_content_id'] = $slot_content['content']->slot_content_id;
                        $create_detail_data['data_type'] = 'IMG';
                        $create_detail_data['sort'] = $input_data['image_block_sort'][$key] ?? null;
                        $create_detail_data['image_alt'] = $input_data['image_block_image_alt'][$key] ?? null;
                        $create_detail_data['image_title'] = $input_data['image_block_image_title'][$key] ?? null;
                        $create_detail_data['image_abstract'] = $input_data['image_block_image_abstract'][$key] ?? null;
                        $create_detail_data['image_action'] = $input_data['image_block_image_action'][$key] ?? null;
                        $create_detail_data['target_url'] = $input_data['image_block_target_url'][$key] ?? null;
                        $create_detail_data['target_cate_hierarchy_id'] = $input_data['image_block_target_cate_hierarchy_id'][$key] ?? null;
                        $create_detail_data['is_target_blank'] = (isset($input_data['image_block_is_target_blank'][$key]) && $input_data['image_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $create_detail_data['created_by'] = $user_id;
                        $create_detail_data['updated_by'] = $user_id;
                        $create_detail_data['created_at'] = $now;
                        $create_detail_data['updated_at'] = $now;

                        if (isset($input_data['image_block_image_name'][$key])) {
                            if ($slot->photo_width > 0 || $slot->photo_height > 0) { //如果有設置寬高 直接裁切圖片
                                $create_detail_data['image_name'] = $this->cropImageBlockUpload($input_data['image_block_image_name'][$key], $input_data['slot_content_id'], $slot);
                            } else {
                                $create_detail_data['image_name'] = $input_data['image_block_image_name'][$key]->storePublicly(self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $input_data['slot_content_id'], 's3');
                            }
                        }

                        AdSlotContentDetails::insert($create_detail_data);
                    }
                    // 更新資料
                    else {
                        $update_detail_data = [];
                        $update_detail_data['sort'] = $input_data['image_block_sort'][$key];
                        $update_detail_data['image_alt'] = $input_data['image_block_image_alt'][$key];
                        $update_detail_data['image_title'] = $input_data['image_block_image_title'][$key];
                        $update_detail_data['image_abstract'] = $input_data['image_block_image_abstract'][$key];
                        $update_detail_data['image_action'] = $input_data['image_block_image_action'][$key];
                        $update_detail_data['target_url'] = $input_data['image_block_target_url'][$key];
                        $update_detail_data['target_cate_hierarchy_id'] = $input_data['image_block_target_cate_hierarchy_id'][$key];
                        $update_detail_data['is_target_blank'] = (isset($input_data['image_block_is_target_blank'][$key]) && $input_data['image_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $update_detail_data['updated_by'] = $user_id;
                        $update_detail_data['updated_at'] = $now;

                        if (isset($input_data['image_block_image_name'][$key])) {
                            // 移除圖片
                            if (!empty($details_image_name[$key])
                                && Storage::disk('s3')->exists($details_image_name[$key])
                            ) {
                                Storage::disk('s3')->delete($details_image_name[$key]);
                            }
                            if ($slot->photo_width > 0 || $slot->photo_height > 0) { //如果有設置寬高 直接裁切圖片
                                $update_detail_data['image_name'] = $this->cropImageBlockUpload($input_data['image_block_image_name'][$key], $input_data['slot_content_id'], $slot);
                            } else {
                                $update_detail_data['image_name'] = $input_data['image_block_image_name'][$key]->storePublicly(self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $input_data['slot_content_id'], 's3');
                            }
                        }
                        AdSlotContentDetails::findOrFail($key)->update($update_detail_data);
                    }
                }
            }

            // 處理文字 (文字)
            if ($slot_content['content']->slot_type == 'T') {
                $input_data['text_block_id'] = $input_data['text_block_id'] ?? [];
                $new_ids = array_keys($input_data['text_block_id']);

                $old_ids = $slot_content['details']->filter(function ($obj, $key) {
                    return $obj->data_type == 'TXT';
                })->pluck('id')->all();

                $delete_ids = array_diff($old_ids, $new_ids);
                $add_ids = array_diff($new_ids, $old_ids);

                // 移除資料
                if (!empty($delete_ids)) {
                    foreach ($slot_content['details'] as $obj) {
                        if (in_array($obj->id, $delete_ids)) {
                            AdSlotContentDetails::destroy($obj->id);
                        }
                    }
                }

                // 新增、更新資料
                foreach ($input_data['text_block_id'] as $key => $value) {
                    // 新增資料
                    if (in_array($key, $add_ids)) {
                        $create_detail_data = [];
                        $create_detail_data['ad_slot_content_id'] = $slot_content['content']->slot_content_id;
                        $create_detail_data['data_type'] = 'TXT';
                        $create_detail_data['sort'] = $input_data['text_block_sort'][$key] ?? null;
                        $create_detail_data['texts'] = $input_data['text_block_texts'][$key] ?? null;
                        $create_detail_data['image_action'] = $input_data['text_block_image_action'][$key] ?? null;
                        $create_detail_data['target_url'] = $input_data['text_block_target_url'][$key] ?? null;
                        $create_detail_data['target_cate_hierarchy_id'] = $input_data['text_block_target_cate_hierarchy_id'][$key] ?? null;
                        $create_detail_data['is_target_blank'] = (isset($input_data['text_block_is_target_blank'][$key]) && $input_data['text_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $create_detail_data['created_by'] = $user_id;
                        $create_detail_data['updated_by'] = $user_id;
                        $create_detail_data['created_at'] = $now;
                        $create_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::insert($create_detail_data);
                    }
                    // 更新資料
                    else {
                        $update_detail_data = [];
                        $update_detail_data['sort'] = $input_data['text_block_sort'][$key];
                        $update_detail_data['texts'] = $input_data['text_block_texts'][$key];
                        $update_detail_data['image_action'] = $input_data['text_block_image_action'][$key];
                        $update_detail_data['target_url'] = $input_data['text_block_target_url'][$key];
                        $update_detail_data['target_cate_hierarchy_id'] = $input_data['text_block_target_cate_hierarchy_id'][$key];
                        $update_detail_data['is_target_blank'] = (isset($input_data['text_block_is_target_blank'][$key]) && $input_data['text_block_is_target_blank'][$key] == 'enabled') ? 1 : 0;
                        $update_detail_data['updated_by'] = $user_id;
                        $update_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::findOrFail($key)->update($update_detail_data);
                    }
                }
            }

            // 處理商品 (商品 or 圖檔+商品)
            if ($slot_content['content']->slot_type == 'S' || $slot_content['content']->slot_type == 'IS') {
                $input_data['product_block_product_id'] = $input_data['product_block_product_id'] ?? [];
                $input_data['product_block_category_id'] = $input_data['product_block_category_id'] ?? [];

                $product_new_ids = array_keys($input_data['product_block_product_id']);
                $category_new_ids = array_keys($input_data['product_block_category_id']);

                $old_ids = $slot_content['details']->filter(function ($obj, $key) {
                    return $obj->data_type == 'PRD';
                })->pluck('id')->all();

                $tmp_delete_ids = array_diff($old_ids, $product_new_ids);
                $delete_ids = array_diff($tmp_delete_ids, $category_new_ids);

                $product_add_ids = array_diff($product_new_ids, $old_ids);
                $category_add_ids = array_diff($category_new_ids, $old_ids);

                // 移除資料
                if (!empty($delete_ids)) {
                    foreach ($slot_content['details'] as $obj) {
                        if (in_array($obj->id, $delete_ids)) {
                            AdSlotContentDetails::destroy($obj->id);
                        }
                    }
                }

                // 新增、更新資料 (指定商品)
                foreach ($input_data['product_block_product_id'] as $key => $value) {
                    // 新增資料 (指定商品)
                    if (in_array($key, $product_add_ids)) {
                        $create_detail_data = [];
                        $create_detail_data['ad_slot_content_id'] = $slot_content['content']->slot_content_id;
                        $create_detail_data['data_type'] = 'PRD';
                        $create_detail_data['sort'] = $input_data['product_block_product_sort'][$key] ?? null;
                        $create_detail_data['product_id'] = $input_data['product_block_product_product_id'][$key] ?? null;
                        $create_detail_data['created_by'] = $user_id;
                        $create_detail_data['updated_by'] = $user_id;
                        $create_detail_data['created_at'] = $now;
                        $create_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::insert($create_detail_data);
                    }
                    // 更新資料 (指定商品)
                    else {
                        $update_detail_data = [];
                        $update_detail_data['sort'] = $input_data['product_block_product_sort'][$key];
                        $update_detail_data['product_id'] = $input_data['product_block_product_product_id'][$key];
                        $update_detail_data['updated_by'] = $user_id;
                        $update_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::findOrFail($key)->update($update_detail_data);
                    }
                }

                // 新增、更新資料 (指定分類)
                foreach ($input_data['product_block_category_id'] as $key => $value) {
                    // 新增資料 (指定分類)
                    if (in_array($key, $category_add_ids)) {
                        $create_detail_data = [];
                        $create_detail_data['ad_slot_content_id'] = $slot_content['content']->slot_content_id;
                        $create_detail_data['data_type'] = 'PRD';
                        $create_detail_data['sort'] = $input_data['product_block_category_sort'][$key] ?? null;
                        $create_detail_data['web_category_hierarchy_id'] = $input_data['product_block_product_web_category_hierarchy_id'][$key] ?? null;
                        $create_detail_data['created_by'] = $user_id;
                        $create_detail_data['updated_by'] = $user_id;
                        $create_detail_data['created_at'] = $now;
                        $create_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::insert($create_detail_data);
                    }
                    // 更新資料 (指定分類)
                    else {
                        $update_detail_data = [];
                        $update_detail_data['sort'] = $input_data['product_block_category_sort'][$key];
                        $update_detail_data['web_category_hierarchy_id'] = $input_data['product_block_product_web_category_hierarchy_id'][$key];
                        $update_detail_data['updated_by'] = $user_id;
                        $update_detail_data['updated_at'] = $now;

                        AdSlotContentDetails::findOrFail($key)->update($update_detail_data);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * 廣告上架的狀態是否可啟用
     *
     * @param int $slot_id
     * @param string $start_at
     * @param string $end_at
     * @param string $slot_content_id
     * @return boolean
     */
    public function canSlotContentActive($slot_id, $start_at, $end_at, $slot_content_id = null)
    {
        $agent_id = Auth::user()->agent_id;

        try {
            /*
             * 查詢上架開始、結束時間，是否在已存在的上下架時間範圍內且狀態為啟用。
             * 如果要更新廣告上架資料，則需排除要更新的該筆資料檢查。
             */
            if (!empty($slot_id) && !empty($start_at) && !empty($end_at)) {
                $start_at_format = Carbon::parse($start_at)->format('Y-m-d H:i:s');
                $end_at_format = Carbon::parse($end_at)->format('Y-m-d H:i:s');

                $result = AdSlotContents::where('agent_id', $agent_id)
                    ->where('slot_id', $slot_id)
                    ->where('active', 1)
                    ->where(function ($query) use ($start_at_format, $end_at_format) {
                        $query->where(function ($query) use ($start_at_format, $end_at_format) {
                            $query->whereBetween('start_at', [$start_at_format, $end_at_format]);
                        })
                            ->orWhere(function ($query) use ($start_at_format, $end_at_format) {
                                $query->whereBetween('end_at', [$start_at_format, $end_at_format]);
                            })
                            ->orWhere(function ($query) use ($start_at_format, $end_at_format) {
                                $query->where('start_at', '<=', $start_at_format)
                                    ->where('end_at', '>=', $end_at_format);
                            });
                    });

                if (!empty($slot_content_id)) {
                    $result = $result->where('id', '!=', $slot_content_id);
                }

                if ($result->count() <= 0) {
                    return true;
                }
            }
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());
        }

        return false;
    }

    /**
     * 重組廣告上架內容
     *
     * @param Collection $ad_slot_contents
     * @return void
     */
    public function restructureAdSlotContents(Collection $ad_slot_contents): void
    {
        $now = Carbon::now();

        $ad_slot_contents->transform(function ($ad_slot_content) use ($now) {
            $start_at_format = Carbon::parse($ad_slot_content->start_at);
            $end_at_format = Carbon::parse($ad_slot_content->end_at);

            /*
             * 上下架狀態
             *
             * 待上架：狀態為啟用，且查詢日 小於上架起日
             * 已上架：狀態為啟用，且查詢日 介於上架起訖日之間
             * 下架：狀態為啟用，且查詢日 大於 上架迄日
             * 關閉：狀態為關閉
             */
            if ($ad_slot_content->slot_content_active == 1) {
                if ($now->lessThan($start_at_format)) {
                    $ad_slot_content->launch_status = '待上架';
                } elseif ($now->between($start_at_format, $end_at_format)) {
                    $ad_slot_content->launch_status = '已上架';
                } else {
                    $ad_slot_content->launch_status = '下架';
                }
            } else {
                $ad_slot_content->launch_status = '關閉';
            }

            return $ad_slot_content;
        });
    }
    /**
     * image_block_image_name upload
     * slot_contents_id  slot
     * return filename
     */
    public function cropImageBlockUpload($file, $slot_contents_id, $slot)
    {
        $imageName = $slot->photo_width . 'x' . $slot->photo_height . '_' . uniqid(date('YmdHis')) . $file->getClientOriginalName();
        $path = self::SLOT_CONTENTS_UPLOAD_PATH_PREFIX . $slot_contents_id . '/' . $imageName;
        $img = Image::make($file);
        $img->resize($slot->photo_width, $slot->photo_height);
        $resource = $img->stream()->detach();
        $storagePath = Storage::disk('s3')->put('/'.$path , $resource, 'public');
        if ($storagePath) {
            return $path;
        } else {
            return null;
        }

    }

}
