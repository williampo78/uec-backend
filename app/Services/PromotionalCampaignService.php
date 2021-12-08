<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PromotionalCampaigns;
use Illuminate\Support\Facades\Auth;
use App\Models\PromotionalCampaignProducts;
use App\Models\PromotionalCampaignGiveaways;

class PromotionalCampaignService
{
    private $lookup_values_v_service;

    public function __construct() {
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

        $result = PromotionalCampaigns::select(
            'promotional_campaigns.id AS promotional_campaigns_id',
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

            'lookup_values_v.description AS lookup_values_v_description',

            'promotional_campaign_products.id AS promotional_campaign_products_id',
            'promotional_campaign_products.sort AS promotional_campaign_products_sort',

            'promotional_campaign_giveaways.id AS promotional_campaign_giveaways_id',
            'promotional_campaign_giveaways.sort AS promotional_campaign_giveaways_sort',
            'promotional_campaign_giveaways.assigned_qty',
            'promotional_campaign_giveaways.assigned_unit_price',

            'pcp_products.id AS pcp_products_id',
            'pcp_products.product_no AS pcp_products_product_no',
            'pcp_products.product_name AS pcp_products_product_name',
            'pcp_products.product_type AS pcp_products_product_type',

            'pcg_products.id AS pcg_products_id',
            'pcg_products.product_no AS pcg_products_product_no',
            'pcg_products.product_name AS pcg_products_product_name',
            'pcg_products.product_type AS pcg_products_product_type'
        )
            ->leftJoin('lookup_values_v', 'promotional_campaigns.campaign_type', '=', 'lookup_values_v.code')

            ->leftJoin('promotional_campaign_products', 'promotional_campaign_products.promotional_campaign_id', '=', 'promotional_campaigns.id')
            ->leftJoin('products AS pcp_products', 'pcp_products.id', '=', 'promotional_campaign_products.product_id')

            ->leftJoin('promotional_campaign_giveaways', 'promotional_campaign_giveaways.promotional_campaign_id', '=', 'promotional_campaigns.id')
            ->leftJoin('products AS pcg_products', 'pcg_products.id', '=', 'promotional_campaign_giveaways.product_id')

            ->where('promotional_campaigns.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'CAMPAIGN_TYPE');

        // 活動階層查詢 (PRD：單品、CART：滿額)
        if (!empty($query_data['level_code'])) {
            $result = $result->where('promotional_campaigns.level_code', $query_data['level_code']);
        }

        // 活動名稱查詢
        if (!empty($query_data['campaign_name'])) {
            $result = $result->where('promotional_campaigns.campaign_name', 'like', '%' . $query_data['campaign_name'] . '%');
        }

        // 狀態查詢
        if (!empty($query_data['active'])) {
            if ($query_data['active'] == 'enabled') {
                $result = $result->where('promotional_campaigns.active', 1);
            } elseif ($query_data['active'] == 'disabled') {
                $result = $result->where('promotional_campaigns.active', 0);
            }
        }

        // 行銷類型查詢
        if (!empty($query_data['campaign_type'])) {
            $result = $result->where('promotional_campaigns.campaign_type', $query_data['campaign_type']);
        }

        try {
            // 上架起始日查詢
            if (!empty($query_data['start_at'])) {
                $start_at = Carbon::parse($query_data['start_at'])->format('Y-m-d H:i:s');
                $result = $result->where('promotional_campaigns.start_at', '>=', $start_at);
            }

            // 上架結束日查詢
            if (!empty($query_data['end_at'])) {
                $end_at = Carbon::parse($query_data['end_at'])->format('Y-m-d H:i:s');
                $result = $result->where('promotional_campaigns.end_at', '<=', $end_at);
            }
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());
        }

        // 商品序號查詢
        if (!empty($query_data['product_no'])) {
            $result = $result->where(function ($query) use ($query_data) {
                $query->where('pcp_products.product_no', $query_data['product_no'])
                    ->orWhere('pcg_products.product_no', $query_data['product_no']);
            });
        }

        $result = $result->orderBy("promotional_campaigns.start_at", "desc")
            ->orderBy("promotional_campaigns.id", "asc")
            ->get();

        return $result;
    }

    /**
     * 新增行銷活動資料
     *
     * ﹝滿額﹞購物車滿N元，打X折 => CART01
     * ﹝滿額﹞購物車滿N元，折X元 => CART02
     * ﹝滿額﹞購物車滿N元，送贈品 => CART03
     * ﹝滿額﹞指定商品滿N件，送贈品 => CART04
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
                $start_at = Carbon::parse($input_data['start_at'])->format('Y-m-d H:i:s');
                $create_data['start_at'] = $start_at;
            }

            if (!empty($input_data['end_at'])) {
                $end_at = Carbon::parse($input_data['end_at'])->format('Y-m-d H:i:s');
                $create_data['end_at'] = $end_at;
            }

            $campaign_types = $this->lookup_values_v_service->getCampaignTypes(['code' => $create_data['campaign_type']]);
            $campaign_type = $campaign_types->first();
            $create_data['level_code'] = $campaign_type->udf_01;
            $create_data['category_code'] = $campaign_type->udf_03;
            $create_data['promotional_label'] = $campaign_type->udf_02;

            // 折扣
            if ($create_data['campaign_type'] == 'CART01' || $create_data['campaign_type'] == 'CART02') {
                $create_data['x_value'] = $input_data['x_value'] ?? null;
            }

            $promotional_campaign_id = PromotionalCampaigns::insertGetId($create_data);

            // 新增單品
            if ($create_data['campaign_type'] == 'CART04') {
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
            if ($create_data['campaign_type'] == 'CART03' || $create_data['campaign_type'] == 'CART04') {
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
}