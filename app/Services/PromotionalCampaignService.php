<?php

namespace App\Services;

use App\Models\PromotionalCampaigns;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PromotionalCampaignService
{
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
}
