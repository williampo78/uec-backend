<?php

namespace App\Services;

use App\Models\PromotionalCampaignGiveaways;
use App\Models\PromotionalCampaignProducts;
use App\Models\PromotionalCampaigns;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromotionalCampaignService
{
    private $lookup_values_v_service;

    public function __construct()
    {
        $this->lookup_values_v_service = new LookupValuesVService;
    }

    /**
     * 取得行銷活動資料
     *
     * @param array $query_data
     * @return object
     */
    public function getPromotionalCampaigns($query_data = [])
    {
        $agent_id = Auth::user()->agent_id;
        $promotional_campaigns = null;

        $promotional_campaigns = PromotionalCampaigns::select(
            'promotional_campaigns.id',
            'promotional_campaigns.campaign_type',
            'promotional_campaigns.campaign_name',
            'promotional_campaigns.active',
            'promotional_campaigns.start_at',
            'promotional_campaigns.end_at',
            'promotional_campaigns.n_value',
            'promotional_campaigns.x_value',
            'promotional_campaigns.target_groups',
            'promotional_campaigns.level_code',
            'promotional_campaigns.category_code',
            'promotional_campaigns.promotional_label',
            'promotional_campaigns.remark',

            'lookup_values_v.description',
        )
            ->leftJoin('lookup_values_v', 'promotional_campaigns.campaign_type', '=', 'lookup_values_v.code')
            ->where('promotional_campaigns.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'CAMPAIGN_TYPE');

        if (!empty($query_data['id'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.id', $query_data['id']);
        }

        // 活動階層查詢 (PRD：單品、CART：滿額)
        if (!empty($query_data['level_code'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.level_code', $query_data['level_code']);
        }

        // 活動名稱查詢
        if (!empty($query_data['campaign_name'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.campaign_name', 'like', '%' . $query_data['campaign_name'] . '%');
        }

        // 狀態查詢
        if (!empty($query_data['active'])) {
            if ($query_data['active'] == 'enabled') {
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.active', 1);
            } elseif ($query_data['active'] == 'disabled') {
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.active', 0);
            }
        }

        // 行銷類型查詢
        if (!empty($query_data['campaign_type'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.campaign_type', $query_data['campaign_type']);
        }

        try {
            // 上架起始日查詢
            if (!empty($query_data['start_at'])) {
                $start_at = Carbon::parse($query_data['start_at'])->format('Y-m-d H:i:s');
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.start_at', '>=', $start_at);
            }

            // 上架結束日查詢
            if (!empty($query_data['end_at'])) {
                $end_at = Carbon::parse($query_data['end_at'])->format('Y-m-d H:i:s');
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.end_at', '<=', $end_at);
            }
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());
        }

        $promotional_campaigns = $promotional_campaigns->orderBy("promotional_campaigns.start_at", "desc")
            ->orderBy("promotional_campaigns.id", "asc")
            ->get()
            ->keyBy('id');

        $promotional_campaign_products = PromotionalCampaignProducts::select(
            'promotional_campaign_products.promotional_campaign_id',
            'promotional_campaign_products.sort',
            'promotional_campaign_products.product_id',

            'products.product_no',
            'products.product_name',
            'products.product_type',
            'products.start_launched_at',
            'products.end_launched_at',
            'products.selling_price',
            'products.approval_status',

            'supplier.name AS supplier_name',
        )
            ->leftJoin('products', 'products.id', '=', 'promotional_campaign_products.product_id')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->get();

        $promotional_campaign_giveaways = PromotionalCampaignGiveaways::select(
            'promotional_campaign_giveaways.promotional_campaign_id',
            'promotional_campaign_giveaways.sort',
            'promotional_campaign_giveaways.product_id',
            'promotional_campaign_giveaways.assigned_qty',
            'promotional_campaign_giveaways.assigned_unit_price',

            'products.product_no',
            'products.product_name',
            'products.product_type',
            'products.start_launched_at',
            'products.end_launched_at',
            'products.selling_price',
            'products.approval_status',

            'supplier.name AS supplier_name',
        )
            ->leftJoin('products', 'products.id', '=', 'promotional_campaign_giveaways.product_id')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->get();

        // 將活動主商品加入行銷活動
        foreach ($promotional_campaign_products as $product) {
            if (isset($promotional_campaigns[$product->promotional_campaign_id])) {
                // 檢查是否有宣告products
                if (!isset($promotional_campaigns[$product->promotional_campaign_id]->products)) {
                    $promotional_campaigns[$product->promotional_campaign_id]->products = collect();
                }

                $promotional_campaigns[$product->promotional_campaign_id]->products->push($product);
            }
        }

        // 將贈品加入行銷活動
        foreach ($promotional_campaign_giveaways as $giveaway) {
            if (isset($promotional_campaigns[$giveaway->promotional_campaign_id])) {
                // 檢查是否有宣告giveaways
                if (!isset($promotional_campaigns[$giveaway->promotional_campaign_id]->giveaways)) {
                    $promotional_campaigns[$giveaway->promotional_campaign_id]->giveaways = collect();
                }

                $promotional_campaigns[$giveaway->promotional_campaign_id]->giveaways->push($giveaway);
            }
        }

        // 商品序號查詢
        if (!empty($query_data['product_no'])) {
            $promotional_campaigns = $promotional_campaigns->filter(function ($obj) use ($query_data) {
                $has_product_no = false;

                if (isset($obj->products)) {
                    if ($obj->products->contains('product_no', $query_data['product_no'])) {
                        $has_product_no = true;
                    }
                }

                if (isset($obj->giveaways)) {
                    if ($obj->giveaways->contains('product_no', $query_data['product_no'])) {
                        $has_product_no = true;
                    }
                }

                return $has_product_no;
            });
        }

        return $promotional_campaigns;
    }

    /**
     * 新增行銷活動資料
     *
     * ﹝滿額﹞購物車滿N元，打X折 => CART01
     * ﹝滿額﹞購物車滿N元，折X元 => CART02
     * ﹝滿額﹞購物車滿N元，送贈品 => CART03
     * ﹝滿額﹞指定商品滿N件，送贈品 => CART04
     *
     * ﹝單品﹞第N件(含)以上，打X折 => PRD01
     * ﹝單品﹞第N件(含)以上，折X元 => PRD02
     * ﹝單品﹞滿N件，每件打X折 => PRD03
     * ﹝單品﹞滿N件，每件折X元 => PRD04
     * ﹝單品﹞滿N件，送贈品 => PRD05
     *
     * @param array $input_data
     * @return boolean
     */
    public function addPromotionalCampaign($input_data)
    {
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        $now = Carbon::now();

        DB::beginTransaction();

        try {
            $create_data = [];
            $create_data['agent_id'] = $agent_id;
            $create_data['campaign_type'] = $input_data['campaign_type'] ?? null;
            $create_data['campaign_name'] = $input_data['campaign_name'] ?? '';
            $create_data['active'] = $input_data['active'] ?? 1;
            $create_data['n_value'] = $input_data['n_value'] ?? null;
            $create_data['target_groups'] = null;
            $create_data['remark'] = null;
            $create_data['created_by'] = $user_id;
            $create_data['updated_by'] = $user_id;
            $create_data['created_at'] = $now;
            $create_data['updated_at'] = $now;

            if (!empty($input_data['start_at'])) {
                $create_data['start_at'] = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
            }

            if (!empty($input_data['end_at'])) {
                $create_data['end_at'] = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
            }

            $campaign_types = $this->lookup_values_v_service->getCampaignTypes(['code' => $create_data['campaign_type']]);
            $campaign_type = $campaign_types->first();
            $create_data['level_code'] = $campaign_type->udf_01;
            $create_data['category_code'] = $campaign_type->udf_03;
            $create_data['promotional_label'] = $campaign_type->udf_02;

            // 折扣
            if (in_array($create_data['campaign_type'], ['CART01', 'CART02', 'PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
                $create_data['x_value'] = $input_data['x_value'] ?? null;
            }

            $promotional_campaign_id = PromotionalCampaigns::insertGetId($create_data);

            // 新增單品
            if (in_array($create_data['campaign_type'], ['CART04', 'PRD01', 'PRD02', 'PRD03', 'PRD04', 'PRD05'])) {
                if (isset($input_data['prd_block_id'])) {
                    foreach ($input_data['prd_block_id'] as $key => $value) {
                        $create_prd_data = [];
                        $create_prd_data['promotional_campaign_id'] = $promotional_campaign_id;
                        $create_prd_data['sort'] = 1;
                        $create_prd_data['product_id'] = $key;
                        $create_prd_data['created_by'] = $user_id;
                        $create_prd_data['updated_by'] = $user_id;
                        $create_prd_data['created_at'] = $now;
                        $create_prd_data['updated_at'] = $now;

                        PromotionalCampaignProducts::insert($create_prd_data);
                    }
                }
            }

            // 新增贈品
            if (in_array($create_data['campaign_type'], ['CART03', 'CART04', 'PRD05'])) {
                if (isset($input_data['gift_block_id'])) {
                    foreach ($input_data['gift_block_id'] as $key => $value) {
                        $create_gift_data = [];
                        $create_gift_data['promotional_campaign_id'] = $promotional_campaign_id;
                        $create_gift_data['sort'] = 1;
                        $create_gift_data['product_id'] = $key;
                        $create_gift_data['assigned_qty'] = $input_data['gift_block_assigned_qty'][$key] ?? 1;
                        $create_gift_data['assigned_unit_price'] = 0;
                        $create_gift_data['created_by'] = $user_id;
                        $create_gift_data['updated_by'] = $user_id;
                        $create_gift_data['created_at'] = $now;
                        $create_gift_data['updated_at'] = $now;

                        PromotionalCampaignGiveaways::insert($create_gift_data);
                    }
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
     * 更新行銷活動資料
     *
     * ﹝滿額﹞購物車滿N元，打X折 => CART01
     * ﹝滿額﹞購物車滿N元，折X元 => CART02
     * ﹝滿額﹞購物車滿N元，送贈品 => CART03
     * ﹝滿額﹞指定商品滿N件，送贈品 => CART04
     *
     * ﹝單品﹞第N件(含)以上，打X折 => PRD01
     * ﹝單品﹞第N件(含)以上，折X元 => PRD02
     * ﹝單品﹞滿N件，每件打X折 => PRD03
     * ﹝單品﹞滿N件，每件折X元 => PRD04
     * ﹝單品﹞滿N件，送贈品 => PRD05
     *
     * @param array $input_data
     * @return boolean
     */
    public function updatePromotionalCampaign($input_data)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $promotional_campaign = $this->getPromotionalCampaigns([
            'id' => $input_data['promotional_campaign_id'],
        ])->first();

        DB::beginTransaction();

        try {
            $update_data = [];

            // 活動名稱
            if (isset($input_data['campaign_name'])) {
                $update_data['campaign_name'] = $input_data['campaign_name'];
            }

            // 狀態
            if (isset($input_data['active'])) {
                $update_data['active'] = $input_data['active'];
            }

            // N (滿額)
            if (isset($input_data['n_value'])) {
                $update_data['n_value'] = $input_data['n_value'];
            }

            // X (折扣)
            if (isset($input_data['x_value'])) {
                $update_data['x_value'] = $input_data['x_value'];
            }

            // 適用對象
            if (isset($input_data['target_groups'])) {
                $update_data['target_groups'] = null;
            }

            // 備註
            if (isset($input_data['remark'])) {
                $update_data['remark'] = null;
            }

            $update_data['updated_by'] = $user_id;
            $update_data['updated_at'] = $now;

            // 上架開始時間
            if (!empty($input_data['start_at'])) {
                $update_data['start_at'] = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
            }

            // 上架結束時間
            if (!empty($input_data['end_at'])) {
                $update_data['end_at'] = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
            }

            PromotionalCampaigns::findOrFail($promotional_campaign->id)->update($update_data);

            // 處理單品
            if (in_array($promotional_campaign->campaign_type, ['CART04', 'PRD01', 'PRD02', 'PRD03', 'PRD04', 'PRD05'])) {
                if (isset($input_data['prd_block_id'])) {
                    $new_ids = array_keys($input_data['prd_block_id']);
                    $old_ids = [];

                    if (isset($promotional_campaign->products)) {
                        $old_ids = $promotional_campaign->products->pluck('product_id')->all();
                    }

                    $delete_ids = array_diff($old_ids, $new_ids);
                    $add_ids = array_diff($new_ids, $old_ids);

                    // 移除資料
                    if (isset($promotional_campaign->products) && !empty($delete_ids)) {
                        foreach ($promotional_campaign->products as $obj) {
                            if (in_array($obj->product_id, $delete_ids)) {
                                PromotionalCampaignProducts::where('promotional_campaign_id', $promotional_campaign->id)
                                    ->where('product_id', $obj->product_id)
                                    ->delete();
                            }
                        }
                    }

                    // 新增、更新資料
                    foreach ($input_data['prd_block_id'] as $id => $value) {
                        // 新增資料
                        if (in_array($id, $add_ids)) {
                            $create_prd_data = [];
                            $create_prd_data['promotional_campaign_id'] = $promotional_campaign->id;
                            $create_prd_data['sort'] = 1;
                            $create_prd_data['product_id'] = $id;
                            $create_prd_data['created_by'] = $user_id;
                            $create_prd_data['updated_by'] = $user_id;
                            $create_prd_data['created_at'] = $now;
                            $create_prd_data['updated_at'] = $now;

                            PromotionalCampaignProducts::insert($create_prd_data);
                        }
                        // 更新資料
                        else {
                            $update_prd_data = [];
                            $update_prd_data['sort'] = 1;
                            $update_prd_data['updated_by'] = $user_id;
                            $update_prd_data['updated_at'] = $now;

                            PromotionalCampaignProducts::where('promotional_campaign_id', $promotional_campaign->id)
                                ->where('product_id', $id)
                                ->update($update_prd_data);
                        }
                    }
                }
            }

            // 處理贈品
            if (in_array($promotional_campaign->campaign_type, ['CART03', 'CART04', 'PRD05'])) {
                if (isset($input_data['gift_block_id'])) {
                    $new_ids = array_keys($input_data['gift_block_id']);
                    $old_ids = [];

                    if (isset($promotional_campaign->giveaways)) {
                        $old_ids = $promotional_campaign->giveaways->pluck('product_id')->all();
                    }

                    $delete_ids = array_diff($old_ids, $new_ids);
                    $add_ids = array_diff($new_ids, $old_ids);

                    // 移除資料
                    if (isset($promotional_campaign->giveaways) && !empty($delete_ids)) {
                        foreach ($promotional_campaign->giveaways as $obj) {
                            if (in_array($obj->product_id, $delete_ids)) {
                                PromotionalCampaignGiveaways::where('promotional_campaign_id', $promotional_campaign->id)
                                    ->where('product_id', $obj->product_id)
                                    ->delete();
                            }
                        }
                    }

                    // 新增、更新資料
                    foreach ($input_data['gift_block_id'] as $id => $value) {
                        // 新增資料
                        if (in_array($id, $add_ids)) {
                            $create_gift_data = [];
                            $create_gift_data['promotional_campaign_id'] = $promotional_campaign->id;
                            $create_gift_data['sort'] = 1;
                            $create_gift_data['product_id'] = $id;
                            $create_gift_data['assigned_qty'] = $input_data['gift_block_assigned_qty'][$id] ?? 1;
                            $create_gift_data['assigned_unit_price'] = 0;
                            $create_gift_data['created_by'] = $user_id;
                            $create_gift_data['updated_by'] = $user_id;
                            $create_gift_data['created_at'] = $now;
                            $create_gift_data['updated_at'] = $now;

                            PromotionalCampaignGiveaways::insert($create_gift_data);
                        }
                        // 更新資料
                        else {
                            $update_gift_data = [];
                            $update_gift_data['sort'] = 1;

                            if (isset($input_data['gift_block_assigned_qty'][$id])) {
                                $update_gift_data['assigned_qty'] = $input_data['gift_block_assigned_qty'][$id];
                            }

                            $update_gift_data['assigned_unit_price'] = 0;
                            $update_gift_data['updated_by'] = $user_id;
                            $update_gift_data['updated_at'] = $now;

                            PromotionalCampaignGiveaways::where('promotional_campaign_id', $promotional_campaign->id)
                                ->where('product_id', $id)
                                ->update($update_gift_data);
                        }
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
}
