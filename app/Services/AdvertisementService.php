<?php

namespace App\Services;

use App\Models\AdSlotContentDetails;
use App\Models\AdSlotContents;
use App\Models\AdSlots;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $now = Carbon::now();

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

    /**
     * 取得廣告上架資料
     *
     * @param array $query_data
     * @return object ORM物件
     */
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

    /**
     * 新增廣告上架資料
     *
     * @param array $input_data
     * @return void
     */
    public function addSlotContents($input_data)
    {
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $slot = $this->getSlotById($input_data['slot']);
        $file_path = "advertisement/{$input_data['slot']}";

        try {
            $start_at = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e);
        }

        try {
            $end_at = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e);
        }

        DB::beginTransaction();

        try {
            $content_data = [];
            $content_data['agent_id'] = $agent_id;
            $content_data['slot_id'] = $input_data['slot'];

            if (isset($start_at)) {
                $content_data['start_at'] = $start_at;
            }

            if (isset($end_at)) {
                $content_data['end_at'] = $end_at;
            }

            $content_data['active'] = $input_data['active'];

            // 使用者自定義版位，可自訂主色、icon、標題
            if ($slot['is_user_defined'] == 1) {
                if (isset($input_data['slot_color_code'])) {
                    $content_data['slot_color_code'] = $input_data['slot_color_code'];
                }

                if (isset($input_data['slot_icon_name'])) {
                    try {
                        $slot_icon_name_path = $input_data['slot_icon_name']->storePublicly($file_path, 's3');
                    } catch (\Exception $e) {
                        Log::error($e);
                    }

                    if (isset($slot_icon_name_path)) {
                        $content_data['slot_icon_name'] = $slot_icon_name_path;
                    }
                }

                if (isset($input_data['slot_title'])) {
                    $content_data['slot_title'] = $input_data['slot_title'];
                }
            }

            if (isset($input_data['product_assigned_type'])) {
                $content_data['product_assigned_type'] = $input_data['product_assigned_type'];
            }

            $content_data['created_by'] = $user_id;
            $content_data['updated_by'] = $user_id;
            $content_data['created_at'] = $now;
            $content_data['updated_at'] = $now;

            $slot_contents_id = AdSlotContents::insertGetId($content_data);

            // 新增圖檔資料 (圖檔 or 圖檔+商品)
            if ($slot['slot_type'] == 'I' || $slot['slot_type'] == 'IS') {
                if (isset($input_data['image_block_id'])) {
                    foreach ($input_data['image_block_id'] as $key => $value) {
                        $detail_data = [];
                        $detail_data['ad_slot_content_id'] = $slot_contents_id;
                        $detail_data['data_type'] = 'IMG';
                        $detail_data['created_by'] = $user_id;
                        $detail_data['updated_by'] = $user_id;
                        $detail_data['created_at'] = $now;
                        $detail_data['updated_at'] = $now;

                        if (isset($input_data['image_block_sort'][$key])) {
                            $detail_data['sort'] = $input_data['image_block_sort'][$key];
                        }

                        if (isset($input_data['image_block_image_alt'][$key])) {
                            $detail_data['image_alt'] = $input_data['image_block_image_alt'][$key];
                        }

                        if (isset($input_data['image_block_image_title'][$key])) {
                            $detail_data['image_title'] = $input_data['image_block_image_title'][$key];
                        }

                        if (isset($input_data['image_block_image_abstract'][$key])) {
                            $detail_data['image_abstract'] = $input_data['image_block_image_abstract'][$key];
                        }

                        if (isset($input_data['image_block_image_action'][$key])) {
                            $detail_data['image_action'] = $input_data['image_block_image_action'][$key];

                            switch ($input_data['image_block_image_action'][$key]) {
                                // URL
                                case 'U':
                                    if (isset($input_data['image_block_target_url'][$key])) {
                                        $detail_data['target_url'] = $input_data['image_block_target_url'][$key];
                                    }
                                    break;
                                // 商品分類頁
                                case 'C':
                                    if (isset($input_data['image_block_target_cate_hierarchy_id'][$key])) {
                                        $detail_data['target_cate_hierarchy_id'] = $input_data['image_block_target_cate_hierarchy_id'][$key];
                                    }
                                    break;
                            }
                        }

                        if (isset($input_data['image_block_is_target_blank'][$key])
                            && $input_data['image_block_is_target_blank'][$key] == 'enabled') {
                            $detail_data['is_target_blank'] = 1;
                        }

                        if (isset($input_data['image_block_image_name'][$key])) {
                            try {
                                $image_name_path = $input_data['image_block_image_name'][$key]->storePublicly($file_path, 's3');
                            } catch (\Exception $e) {
                                Log::error($e);
                            }

                            if (isset($image_name_path)) {
                                $detail_data['image_name'] = $image_name_path;
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
                        $detail_data['created_by'] = $user_id;
                        $detail_data['updated_by'] = $user_id;
                        $detail_data['created_at'] = $now;
                        $detail_data['updated_at'] = $now;

                        if (isset($input_data['text_block_sort'][$key])) {
                            $detail_data['sort'] = $input_data['text_block_sort'][$key];
                        }

                        if (isset($input_data['text_block_texts'][$key])) {
                            $detail_data['texts'] = $input_data['text_block_texts'][$key];
                        }

                        if (isset($input_data['text_block_image_action'][$key])) {
                            $detail_data['image_action'] = $input_data['text_block_image_action'][$key];

                            switch ($input_data['text_block_image_action'][$key]) {
                                // URL
                                case 'U':
                                    if (isset($input_data['text_block_target_url'][$key])) {
                                        $detail_data['target_url'] = $input_data['text_block_target_url'][$key];
                                    }
                                    break;
                                // 商品分類頁
                                case 'C':
                                    if (isset($input_data['text_block_target_cate_hierarchy_id'][$key])) {
                                        $detail_data['target_cate_hierarchy_id'] = $input_data['text_block_target_cate_hierarchy_id'][$key];
                                    }
                                    break;
                            }
                        }

                        if (isset($input_data['text_block_is_target_blank'][$key])
                            && $input_data['text_block_is_target_blank'][$key] == 'enabled') {
                            $detail_data['is_target_blank'] = 1;
                        }

                        AdSlotContentDetails::insert($detail_data);
                    }
                }
            }

            // 新增商品資料 (商品 or 圖檔+商品)
            if ($slot['slot_type'] == 'S' || $slot['slot_type'] == 'IS') {
                if (isset($input_data['product_assigned_type'])) {
                    switch ($input_data['product_assigned_type']) {
                        // 指定商品
                        case 'P':
                            if (isset($input_data['product_block_product_id'])) {
                                foreach ($input_data['product_block_product_id'] as $key => $value) {
                                    $detail_data = [];
                                    $detail_data['ad_slot_content_id'] = $slot_contents_id;
                                    $detail_data['data_type'] = 'PRD';
                                    $detail_data['created_by'] = $user_id;
                                    $detail_data['updated_by'] = $user_id;
                                    $detail_data['created_at'] = $now;
                                    $detail_data['updated_at'] = $now;

                                    if (isset($input_data['product_block_product_sort'][$key])) {
                                        $detail_data['sort'] = $input_data['product_block_product_sort'][$key];
                                    }

                                    if (isset($input_data['product_block_product_product_id'][$key])) {
                                        $detail_data['product_id'] = $input_data['product_block_product_product_id'][$key];
                                    }

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
                                    $detail_data['created_by'] = $user_id;
                                    $detail_data['updated_by'] = $user_id;
                                    $detail_data['created_at'] = $now;
                                    $detail_data['updated_at'] = $now;

                                    if (isset($input_data['product_block_category_sort'][$key])) {
                                        $detail_data['sort'] = $input_data['product_block_category_sort'][$key];
                                    }

                                    if (isset($input_data['product_block_product_web_category_hierarchy_id'][$key])) {
                                        $detail_data['web_category_hierarchy_id'] = $input_data['product_block_product_web_category_hierarchy_id'][$key];
                                    }

                                    AdSlotContentDetails::insert($detail_data);
                                }
                            }
                            break;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e);
        }
    }
}
