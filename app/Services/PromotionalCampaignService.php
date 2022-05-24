<?php

namespace App\Services;

use App\Models\PromotionalCampaign;
use App\Models\PromotionalCampaignGiveaway;
use App\Models\PromotionalCampaignProduct;
use App\Models\PromotionalCampaignThreshold;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PromotionalCampaignService
{
    private $lookupValuesVService;
    private $sysConfigService;
    private $webCategoryHierarchyService;
    private const CART_UPLOAD_PATH_PREFIX = 'promotional_campaign/cart/';

    public function __construct(
        LookupValuesVService $lookupValuesVService,
        SysConfigService $sysConfigService,
        WebCategoryHierarchyService $webCategoryHierarchyService
    ) {
        $this->lookupValuesVService = $lookupValuesVService;
        $this->sysConfigService = $sysConfigService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
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

        $promotional_campaigns = PromotionalCampaign::select(
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
            DB::raw('ifnull(promotional_campaigns.campaign_brief,"") as campaign_brief')
        )
            ->leftJoin('lookup_values_v', 'promotional_campaigns.campaign_type', '=', 'lookup_values_v.code')
            ->where('promotional_campaigns.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id)
            ->where('lookup_values_v.type_code', 'CAMPAIGN_TYPE');

        if (isset($query_data['id'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.id', $query_data['id']);
        }

        // 活動階層查詢 (PRD：單品、CART：滿額)
        if (isset($query_data['level_code'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.level_code', $query_data['level_code']);
        }

        // 活動名稱查詢
        /*
        if (!empty($query_data['campaign_name'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.campaign_name', 'like', '%' . $query_data['campaign_name'] . '%');
        }
        */
        if (!empty($query_data['campaign_name'])) {
            $promotional_campaigns = $promotional_campaigns->where(function ($query) use ($query_data) {
                $query->where('promotional_campaigns.campaign_name', 'LIKE', '%' . $query_data['campaign_name'] . '%')
                    ->orWhere('promotional_campaigns.campaign_brief', 'LIKE', '%' . $query_data['campaign_name'] . '%');
            });
        }

        // 狀態查詢
        if (isset($query_data['active'])) {
            if ($query_data['active'] == 'enabled') {
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.active', 1);
            } elseif ($query_data['active'] == 'disabled') {
                $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.active', 0);
            }
        }

        // 行銷類型查詢
        if (isset($query_data['campaign_type'])) {
            $promotional_campaigns = $promotional_campaigns->where('promotional_campaigns.campaign_type', $query_data['campaign_type']);
        }

        // 上架開始時間起始日
        if (!empty($query_data['start_at_start'])) {
            $promotional_campaigns = $promotional_campaigns->whereDate('promotional_campaigns.start_at', '>=', $query_data['start_at_start']);
        }

        // 上架開始時間結束日
        if (!empty($query_data['start_at_end'])) {
            $promotional_campaigns = $promotional_campaigns->whereDate('promotional_campaigns.start_at', '<=', $query_data['start_at_end']);
        }

        $promotional_campaigns = $promotional_campaigns->orderBy("promotional_campaigns.start_at", "desc")
            ->orderBy("promotional_campaigns.id", "asc")
            ->get()
            ->keyBy('id');

        $promotional_campaign_products = PromotionalCampaignProduct::select(
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
            DB::raw('get_latest_product_cost(products.id, TRUE) AS item_cost'),

            'supplier.name AS supplier_name',
        )
            ->leftJoin('products', 'products.id', '=', 'promotional_campaign_products.product_id')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->get();

        $promotional_campaign_giveaways = PromotionalCampaignGiveaway::select(
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
            DB::raw('get_latest_product_cost(products.id, TRUE) AS item_cost'),

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
            $create_data['campaign_brief'] = $input_data['campaign_brief'] ?? null;
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

            $campaign_types = $this->lookupValuesVService->getLookupValuesVs([
                'type_code' => 'CAMPAIGN_TYPE',
                'code' => $create_data['campaign_type'],
            ]);
            $campaign_type = $campaign_types->first();
            $create_data['level_code'] = $campaign_type->udf_01;
            $create_data['category_code'] = $campaign_type->udf_03;
            $create_data['promotional_label'] = $campaign_type->udf_02;

            // 折扣
            if (in_array($create_data['campaign_type'], ['CART01', 'CART02', 'PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
                $create_data['x_value'] = $input_data['x_value'] ?? null;
            }

            $promotional_campaign_id = PromotionalCampaign::insertGetId($create_data);

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

                        PromotionalCampaignProduct::insert($create_prd_data);
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

                        PromotionalCampaignGiveaway::insert($create_gift_data);
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

            // 前台文案
            if (isset($input_data['campaign_brief'])) {
                $update_data['campaign_brief'] = $input_data['campaign_brief'];
            }

            PromotionalCampaign::findOrFail($promotional_campaign->id)->update($update_data);

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
                                PromotionalCampaignProduct::where('promotional_campaign_id', $promotional_campaign->id)
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

                            PromotionalCampaignProduct::insert($create_prd_data);
                        }
                        // 更新資料
                        else {
                            $update_prd_data = [];
                            $update_prd_data['sort'] = 1;
                            $update_prd_data['updated_by'] = $user_id;
                            $update_prd_data['updated_at'] = $now;

                            PromotionalCampaignProduct::where('promotional_campaign_id', $promotional_campaign->id)
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
                                PromotionalCampaignGiveaway::where('promotional_campaign_id', $promotional_campaign->id)
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

                            PromotionalCampaignGiveaway::insert($create_gift_data);
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

                            PromotionalCampaignGiveaway::where('promotional_campaign_id', $promotional_campaign->id)
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

    /**
     * 單品活動的狀態是否可啟用
     *
     * @param string $campaign_type
     * @param string $start_at
     * @param string $end_at
     * @param array $exist_products
     * @param string $slot_content_id
     * @return boolean
     */
    public function canPromotionalCampaignPrdActive($campaign_type, $start_at, $end_at, $exist_products, $promotional_campaign_id = null)
    {
        $agent_id = Auth::user()->agent_id;

        if (empty($campaign_type)
            || empty($start_at)
            || empty($end_at)
            || empty($exist_products)
        ) {
            return false;
        }

        try {
            $start_at_format = Carbon::parse($start_at)->format('Y-m-d H:i:s');
            $end_at_format = Carbon::parse($end_at)->format('Y-m-d H:i:s');
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());

            return false;
        }

        /*
         * 查詢上架開始、結束時間，是否在已存在的上下架時間範圍內且狀態為啟用，並檢查是否為同一個單品。
         * 如果要更新資料，則需排除要更新的該筆資料檢查。
         */
        $results = PromotionalCampaign::select(
            'promotional_campaigns.campaign_type',
            'promotional_campaign_products.product_id',
        )
            ->rightJoin('promotional_campaign_products', 'promotional_campaigns.id', '=', 'promotional_campaign_products.promotional_campaign_id')
            ->where('promotional_campaigns.agent_id', $agent_id)
            ->where('promotional_campaigns.active', 1)
            ->where('promotional_campaigns.level_code', 'PRD')
            ->where(function ($query) use ($start_at_format, $end_at_format) {
                $query->where(function ($query) use ($start_at_format, $end_at_format) {
                    $query->whereBetween('promotional_campaigns.start_at', [$start_at_format, $end_at_format]);
                })
                    ->orWhere(function ($query) use ($start_at_format, $end_at_format) {
                        $query->whereBetween('promotional_campaigns.end_at', [$start_at_format, $end_at_format]);
                    })
                    ->orWhere(function ($query) use ($start_at_format, $end_at_format) {
                        $query->where('promotional_campaigns.start_at', '<=', $start_at_format)
                            ->where('promotional_campaigns.end_at', '>=', $end_at_format);
                    });
            });

        if (!empty($promotional_campaign_id)) {
            $results = $results->where('promotional_campaigns.id', '!=', $promotional_campaign_id);
        }

        $results = $results->get();

        // 同一單品不可存在其他生效的﹝第N件(含)以上打X折﹞、﹝第N件(含)以上折X元﹞、﹝滿N件，每件打X折﹞、﹝滿N件，每件折X元﹞的行銷活動
        if (in_array($campaign_type, ['PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
            $results = $results->filter(function ($result) use ($exist_products) {
                if (!in_array($result->campaign_type, ['PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
                    return false;
                }

                if (!in_array($result->product_id, $exist_products)) {
                    return false;
                }

                return true;
            });
        }

        // 同一單品不可存在其他生效的﹝買N件，送贈品﹞的行銷活動
        if (in_array($campaign_type, ['PRD05'])) {
            $results = $results->filter(function ($result) use ($exist_products) {
                if (!in_array($result->campaign_type, ['PRD05'])) {
                    return false;
                }

                if (!in_array($result->product_id, $exist_products)) {
                    return false;
                }

                return true;
            });
        }

        if ($results->count() <= 0) {
            return true;
        }

        return false;
    }

    /**
     * 滿額活動的狀態是否可啟用
     *
     * @param array $datas
     * @return array
     */
    public function canPromotionalCampaignCartActive(array $datas): array
    {
        $agent_id = Auth::user()->agent_id;

        if (empty($datas['campaign_type'])
            || empty($datas['start_at'])
            || empty($datas['end_at'])
            || !isset($datas['n_value'])
        ) {
            return [
                'status' => false,
            ];
        }

        $start_at_format = Carbon::parse($datas['start_at']);
        $end_at_format = Carbon::parse($datas['end_at']);

        /*
         * 查詢上架開始、結束時間，是否在已存在的上下架時間範圍內，且狀態為啟用
         * 如果要更新資料，則需排除要更新的該筆資料檢查
         */
        $promotional_campaigns = PromotionalCampaign::where('agent_id', $agent_id)
            ->where('active', 1)
            ->where('level_code', 'CART')
            ->where('n_value', $datas['n_value'])
            ->where(function ($query) use ($start_at_format, $end_at_format) {
                $query->whereBetween('start_at', [$start_at_format, $end_at_format])
                    ->orWhereBetween('end_at', [$start_at_format, $end_at_format])
                    ->orWhere(function ($query) use ($start_at_format, $end_at_format) {
                        $query->where('start_at', '<=', $start_at_format)
                            ->where('end_at', '>=', $end_at_format);
                    });
            });

        if (!empty($datas['promotional_campaign_id'])) {
            $promotional_campaigns = $promotional_campaigns->where('id', '!=', $datas['promotional_campaign_id']);
        }

        $promotional_campaigns = $promotional_campaigns->get();

        // 只處理﹝購物車滿N元，打X折﹞、﹝購物車滿N元，折X元﹞的行銷活動
        if (in_array($datas['campaign_type'], ['CART01', 'CART02'])) {
            $promotional_campaigns = $promotional_campaigns->filter(function ($promotional_campaign) {
                return in_array($promotional_campaign->campaign_type, ['CART01', 'CART02']);
            });
        }

        // 只處理﹝購物車滿N元，送贈品﹞的行銷活動
        if (in_array($datas['campaign_type'], ['CART03'])) {
            $promotional_campaigns = $promotional_campaigns->filter(function ($promotional_campaign) {
                return in_array($promotional_campaign->campaign_type, ['CART03']);
            });
        }

        if ($promotional_campaigns->count() <= 0) {
            return [
                'status' => true,
            ];
        }

        return [
            'status' => false,
            'conflict_campaigns' => $promotional_campaigns,
        ];
    }

    /**
     * 新版滿額活動的狀態是否可啟用
     *
     * @param string $campaignType
     * @param string $startAt
     * @param string $endAt
     * @param array $productIds
     * @param integer|null $excludePromotionalCampaignId
     * @return array
     */
    public function canPromotionalCampaignCartV2Active(string $campaignType, string $startAt, string $endAt, array $productIds, int $excludePromotionalCampaignId = null): array
    {
        $user = Auth::user();

        /*
         * 查詢上架開始、結束時間，是否在已存在的上下架時間範圍內，且狀態為啟用
         * 如果要更新資料，則需排除要更新的該筆資料檢查
         */
        $promotionalCampaigns = PromotionalCampaign::with([
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
        ])
            ->where('agent_id', $user->agent_id)
            ->where('active', 1)
            ->where('level_code', 'CART_P')
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('start_at', [$startAt, $endAt])
                    ->orWhereBetween('end_at', [$startAt, $endAt])
                    ->orWhere(function ($query) use ($startAt, $endAt) {
                        $query->where('start_at', '<=', $startAt)
                            ->where('end_at', '>=', $endAt);
                    });
            });

        if (!empty($excludePromotionalCampaignId)) {
            $promotionalCampaigns = $promotionalCampaigns->where('id', '!=', $excludePromotionalCampaignId);
        }

        // ﹝指定商品滿N元，打X折﹞、﹝指定商品滿N元，折X元﹞
        if (in_array($campaignType, ['CART_P01', 'CART_P02'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['CART_P01', 'CART_P02']);
        }

        // ﹝指定商品滿N件，送贈品﹞、﹝指定商品滿N元，送贈品﹞
        if (in_array($campaignType, ['CART_P03', 'CART_P04'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['CART_P03', 'CART_P04']);
        }

        $promotionalCampaigns = $promotionalCampaigns->whereHas('promotionalCampaignProducts', function (Builder $query) use ($productIds) {
            return $query->whereIn('product_id', $productIds);
        });

        $promotionalCampaigns = $promotionalCampaigns->get();

        if ($promotionalCampaigns->count() < 1) {
            return [
                'status' => true,
            ];
        }

        // 衝突的內容
        $conflictContents = [];
        foreach ($promotionalCampaigns as $campaign) {
            $conflictContent = [
                'campaign_name' => $campaign->campaign_name,
                'product_no' => null,
            ];

            if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
                $productNos = [];

                foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
                    if (isset($campaignProduct->product) && in_array($campaignProduct->product_id, $productIds)) {
                        $productNos[] = $campaignProduct->product->product_no;
                    }
                }

                $conflictContent['product_no'] = implode(', ', $productNos);
            }

            $conflictContents[] = $conflictContent;
        }

        return [
            'status' => false,
            'conflict_contents' => $conflictContents,
        ];
    }

    /**
     * 取得滿額活動table列表
     *
     * @param array $queryData
     * @return Collection
     */
    public function getCartTableList(array $queryData = []): Collection
    {
        $user = Auth::user();
        $cartCampaigns = PromotionalCampaign::with(['campaignType'])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART_P');

        // 活動名稱 or 前台文案
        if (!empty($queryData['campaign_name_or_campaign_brief'])) {
            $cartCampaigns = $cartCampaigns->where(function ($query) use ($queryData) {
                return $query->where('campaign_name', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%')
                    ->orWhere('campaign_brief', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%');
            });
        }

        // 上下架狀態
        if (isset($queryData['launch_status'])) {
            switch ($queryData['launch_status']) {
                // 待上架
                case 'prepare_to_launch':
                    $cartCampaigns = $cartCampaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->whereDate('start_at', '>', now());
                    });
                    break;

                // 已上架
                case 'launched':
                    $cartCampaigns = $cartCampaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->whereDate('start_at', '<=', now())
                            ->whereDate('end_at', '>=', now());
                    });
                    break;

                // 下架
                case 'no_launch':
                    $cartCampaigns = $cartCampaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->whereDate('end_at', '<', now());
                    });
                    break;

                // 關閉
                case 'disabled':
                    $cartCampaigns = $cartCampaigns->where(function ($query) {
                        $query->where('active', 0);
                    });
                    break;
            }
        }

        // 活動類型
        if (isset($queryData['campaign_type'])) {
            $cartCampaigns = $cartCampaigns->where('campaign_type', $queryData['campaign_type']);
        }

        // 上架開始時間-起始日
        if (!empty($queryData['start_at_start'])) {
            $cartCampaigns = $cartCampaigns->whereDate('start_at', '>=', $queryData['start_at_start']);
        }

        // 上架開始時間-結束日
        if (!empty($queryData['start_at_end'])) {
            $cartCampaigns = $cartCampaigns->whereDate('start_at', '<=', $queryData['start_at_end']);
        }

        // 商品序號查詢
        if (!empty($queryData['product_no'])) {
            $cartCampaigns = $cartCampaigns->where(function ($query) use ($queryData) {
                return $query->whereHas('promotionalCampaignProducts.product', function (Builder $query) use ($queryData) {
                    return $query->where('product_no', $queryData['product_no']);
                })
                    ->orWhereHas('promotionalCampaignGiveaways.product', function (Builder $query) use ($queryData) {
                        return $query->where('product_no', $queryData['product_no']);
                    });
            });
        }

        $cartCampaigns = $cartCampaigns->latest('start_at')->get();

        return $cartCampaigns;
    }

    /**
     * 整理滿額活動table列表
     *
     * @param Collection $cartCampaigns
     * @return array
     */
    public function formatCartTableList(Collection $cartCampaigns): array
    {
        $result = [];

        foreach ($cartCampaigns as $cartCampaign) {
            $tmpCartCampaign = [
                'id' => $cartCampaign->id,
                'campaign_name' => $cartCampaign->campaign_name,
                'campaign_brief' => $cartCampaign->campaign_brief,
                'campaign_type' => null,
                'launch_status' => $cartCampaign->launch_status,
                'start_at' => Carbon::parse($cartCampaign->start_at)->format('Y-m-d H:i'),
                'end_at' => Carbon::parse($cartCampaign->end_at)->format('Y-m-d H:i'),
            ];

            // 活動類型
            if (isset($cartCampaign->campaignType)) {
                $tmpCartCampaign['campaign_type'] = $cartCampaign->campaignType->description;
            }

            $result[] = $tmpCartCampaign;
        }

        return $result;
    }

    /**
     * 取得滿額活動
     *
     * @param integer $id
     * @return Model
     */
    public function getPromotionalCampaignCartById(int $id): Model
    {
        $user = Auth::user();
        $warehouseNumber = $this->sysConfigService->getConfigValueByConfigKey('EC_WAREHOUSE_GOODS');
        $cartCampaign = PromotionalCampaign::with([
            'promotionalCampaignThresholds',
            'promotionalCampaignThresholds.promotionalCampaignGiveaways',
            'promotionalCampaignThresholds.promotionalCampaignGiveaways.product',
            'promotionalCampaignThresholds.promotionalCampaignGiveaways.product.supplier',
            'promotionalCampaignThresholds.promotionalCampaignGiveaways.product.productItems.warehouses' => function ($query) use ($warehouseNumber) {
                return $query->where('number', $warehouseNumber);
            },
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
            'promotionalCampaignProducts.product.webCategoryHierarchies' => function ($query) {
                return $query->oldest('web_category_products.sort')->oldest('web_category_products.id');
            },
        ])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART_P')
            ->find($id);

        return $cartCampaign;
    }

    /**
     * 整理滿額活動
     *
     * @param Model $cartCampaigns
     * @return array
     */
    public function formatPromotionalCampaignCart(Model $cartCampaign): array
    {
        $result = [
            'id' => $cartCampaign->id,
            'campaign_name' => $cartCampaign->campaign_name,
            'campaign_type' => $cartCampaign->campaign_type,
            'active' => $cartCampaign->active,
            'start_at' => $cartCampaign->start_at,
            'end_at' => $cartCampaign->end_at,
            'campaign_brief' => $cartCampaign->campaign_brief,
            'url_code' => $cartCampaign->url_code,
            'stock_type' => null,
            'supplier_id' => isset($cartCampaign->supplier_id) ? $cartCampaign->supplier_id : 'all',
            'banner_photo_desktop_url' => !empty($cartCampaign->banner_photo_desktop) ? config('filesystems.disks.s3.url') . $cartCampaign->banner_photo_desktop : null,
            'banner_photo_mobile_url' => !empty($cartCampaign->banner_photo_mobile) ? config('filesystems.disks.s3.url') . $cartCampaign->banner_photo_mobile : null,
            'thresholds' => null,
            'products' => null,
        ];

        // 庫存類型
        if ($cartCampaign->ship_from_whs == 'SELF') {
            $result['stock_type'] = 'A_B';
        } elseif ($cartCampaign->ship_from_whs == 'SUP') {
            $result['stock_type'] = 'T';
        }

        // 活動門檻
        if ($cartCampaign->promotionalCampaignThresholds->isNotEmpty()) {
            foreach ($cartCampaign->promotionalCampaignThresholds as $threshold) {
                $tmpThreshold = [
                    'id' => $threshold->id,
                    'n_value' => $threshold->n_value,
                    'x_value' => null,
                    'giveaways' => null,
                ];

                // ﹝指定商品滿N元，打X折﹞、﹝指定商品滿N元，折X元﹞
                if (in_array($cartCampaign->campaign_type, ['CART_P01', 'CART_P02'])) {
                    $tmpThreshold['x_value'] = $threshold->x_value;
                }
                // ﹝指定商品滿N件，送贈品﹞、﹝指定商品滿N元，送贈品﹞
                elseif (in_array($cartCampaign->campaign_type, ['CART_P03', 'CART_P04'])) {
                    // 贈品
                    if ($threshold->promotionalCampaignGiveaways->isNotEmpty()) {
                        foreach ($threshold->promotionalCampaignGiveaways as $giveaway) {
                            $tmpGiveaway = [
                                'id' => $giveaway->id,
                                'product_id' => $giveaway->product_id,
                                'product_no' => null,
                                'product_name' => null,
                                'assigned_qty' => $giveaway->assigned_qty,
                                'stock_type' => null,
                                'product_type' => null,
                                'supplier' => null,
                                'stock_qty' => 0,
                            ];

                            if (isset($giveaway->product)) {
                                $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                                $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                                $tmpGiveaway['stock_type'] = config('uec.stock_type_options')[$giveaway->product->stock_type] ?? null;

                                // 商品類型
                                if (isset($giveaway->product->product_type)) {
                                    $tmpGiveaway['product_type'] = config('uec.product_type_options')[$giveaway->product->product_type] ?? null;
                                }

                                // 供應商
                                if (isset($giveaway->product->supplier)) {
                                    $tmpGiveaway['supplier'] = $giveaway->product->supplier->name;
                                }

                                // 合計各個商品品項的庫存數
                                $stockQty = 0;
                                if ($giveaway->product->productItems->isNotEmpty()) {
                                    foreach ($giveaway->product->productItems as $productItem) {
                                        $warehouse = $productItem->warehouses->first();
                                        if (isset($warehouse)) {
                                            $stockQty += $warehouse->pivot->stock_qty;
                                        }
                                    }
                                }
                                $tmpGiveaway['stock_qty'] = $stockQty;
                            }

                            $tmpThreshold['giveaways'][] = $tmpGiveaway;
                        }
                    }
                }

                $result['thresholds'][] = $tmpThreshold;
            }
        }

        // 活動指定商品
        if ($cartCampaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($cartCampaign->promotionalCampaignProducts as $campaignProduct) {
                $tmpCampaignProduct = [
                    'id' => $campaignProduct->id,
                    'product_id' => $campaignProduct->product_id,
                    'product_no' => null,
                    'product_name' => null,
                    'selling_price' => null,
                    'start_launched_at' => null,
                    'launch_status' => null,
                    'gross_margin' => null,
                    'web_category_hierarchy' => null,
                ];

                if (isset($campaignProduct->product)) {
                    $tmpCampaignProduct['product_no'] = $campaignProduct->product->product_no;
                    $tmpCampaignProduct['product_name'] = $campaignProduct->product->product_name;
                    $tmpCampaignProduct['selling_price'] = number_format($campaignProduct->product->selling_price);
                    $tmpCampaignProduct['launch_status'] = $campaignProduct->product->launch_status;
                    $tmpCampaignProduct['gross_margin'] = $campaignProduct->product->gross_margin;

                    // 上架時間起
                    if (isset($campaignProduct->product->start_launched_at)) {
                        $tmpCampaignProduct['start_launched_at'] = Carbon::parse($campaignProduct->product->start_launched_at)->format('Y-m-d H:i');
                    }

                    // 前台分類
                    if ($campaignProduct->product->webCategoryHierarchies->isNotEmpty()) {
                        $webCategoryHierarchy = $campaignProduct->product->webCategoryHierarchies->first();

                        $webCategoryHierarchyContents = $this->webCategoryHierarchyService->getCategoryHierarchyContents([
                            'id' => $webCategoryHierarchy->id,
                        ]);

                        $tmpCampaignProduct['web_category_hierarchy'] = !empty($webCategoryHierarchyContents) ? $webCategoryHierarchyContents[0]->name : null;
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 新增滿額活動
     *
     * @param array $data
     * @return boolean
     */
    public function createPromotionalCampaignCart(array $data): bool
    {
        $user = Auth::user();
        $result = false;

        DB::beginTransaction();
        try {
            $categoryCode = null;
            // ﹝指定商品滿N元，打X折﹞、﹝指定商品滿N元，折X元﹞
            if (in_array($data['campaign_type'], ['CART_P01', 'CART_P02'])) {
                $categoryCode = 'DISCOUNT';
            }
            // ﹝指定商品滿N件，送贈品﹞、﹝指定商品滿N元，送贈品﹞
            elseif (in_array($data['campaign_type'], ['CART_P03', 'CART_P04'])) {
                $categoryCode = 'GIFT';
            }

            $shipFromWhs = null;
            // 買斷、寄售
            if ($data['stock_type'] == 'A_B') {
                $shipFromWhs = 'SELF';
            }
            // 轉單
            elseif ($data['stock_type'] == 'T') {
                $shipFromWhs = 'SUP';
            }

            $supplierId = null;
            if ($data['supplier_id'] != 'all') {
                $supplierId = $data['supplier_id'];
            }

            // 新增行銷活動
            $createdPromotionalCampaign = PromotionalCampaign::create([
                'agent_id' => $user->agent_id,
                'campaign_type' => $data['campaign_type'],
                'campaign_name' => $data['campaign_name'],
                'active' => $data['active'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'campaign_brief' => $data['campaign_brief'],
                'url_code' => $data['url_code'],
                'level_code' => 'CART_P',
                'category_code' => $categoryCode,
                'banner_photo_desktop' => null,
                'banner_photo_mobile' => null,
                'ship_from_whs' => $shipFromWhs,
                'supplier_id' => $supplierId,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 儲存desktop圖片
            if (isset($data['banner_photo_desktop'])) {
                $bannerPhotoDesktop = $data['banner_photo_desktop']->storePublicly(self::CART_UPLOAD_PATH_PREFIX . $createdPromotionalCampaign->id, 's3');
                PromotionalCampaign::findOrFail($createdPromotionalCampaign->id)->update([
                    'banner_photo_desktop' => $bannerPhotoDesktop,
                ]);
            }

            // 儲存mobile圖片
            if (isset($data['banner_photo_mobile'])) {
                $bannerPhotoMobile = $data['banner_photo_mobile']->storePublicly(self::CART_UPLOAD_PATH_PREFIX . $createdPromotionalCampaign->id, 's3');
                PromotionalCampaign::findOrFail($createdPromotionalCampaign->id)->update([
                    'banner_photo_mobile' => $bannerPhotoMobile,
                ]);
            }

            // 新增活動門檻
            if (isset($data['thresholds'])) {
                foreach ($data['thresholds'] as $threshold) {
                    $xValue = 0;
                    if (in_array($data['campaign_type'], ['CART_P01', 'CART_P02'])) {
                        $xValue = $threshold['x_value'];
                    }

                    $createdPromotionalCampaignThreshold = PromotionalCampaignThreshold::create([
                        'promotional_campaign_id' => $createdPromotionalCampaign->id,
                        'n_value' => $threshold['n_value'],
                        'x_value' => $xValue,
                        'threshold_brief' => $threshold['threshold_brief'],
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);

                    // 新增活動贈品
                    if (in_array($data['campaign_type'], ['CART_P03', 'CART_P04'])) {
                        if (isset($threshold['giveaways'])) {
                            foreach ($threshold['giveaways'] as $giveaway) {
                                PromotionalCampaignGiveaway::create([
                                    'promotional_campaign_id' => $createdPromotionalCampaign->id,
                                    'sort' => 1,
                                    'product_id' => $giveaway['product_id'],
                                    'assigned_qty' => $giveaway['assigned_qty'],
                                    'assigned_unit_price' => 0,
                                    'threshold_id' => $createdPromotionalCampaignThreshold->id,
                                    'created_by' => $user->id,
                                    'updated_by' => $user->id,
                                ]);
                            }
                        }
                    }
                }
            }

            // 新增活動指定商品
            if (isset($data['products'])) {
                foreach ($data['products'] as $product) {
                    PromotionalCampaignProduct::create([
                        'promotional_campaign_id' => $createdPromotionalCampaign->id,
                        'sort' => 1,
                        'product_id' => $product['product_id'],
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }
            }

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 更新滿額活動
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updatePromotionalCampaignCart(int $id, array $data): bool
    {
        $user = Auth::user();
        $result = false;
        $originPromotionalCampaign = PromotionalCampaign::findOrFail($id);

        DB::beginTransaction();
        try {
            if (now()->greaterThanOrEqualTo($originPromotionalCampaign->start_at)) {
                PromotionalCampaign::findOrFail($id)->update([
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'end_at' => $data['end_at'],
                    'updated_by' => $user->id,
                ]);
            } else {
                $promotionalCampaignData = [
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                    'campaign_brief' => $data['campaign_brief'],
                    'url_code' => $data['url_code'],
                    'updated_by' => $user->id,
                ];

                // 出貨倉
                if ($data['stock_type'] == 'A_B') {
                    $promotionalCampaignData['ship_from_whs'] = 'SELF';
                } elseif ($data['stock_type'] == 'T') {
                    $promotionalCampaignData['ship_from_whs'] = 'SUP';
                }

                // 供應商
                if ($data['supplier_id'] != 'all') {
                    $promotionalCampaignData['supplier_id'] = $data['supplier_id'];
                } else {
                    $promotionalCampaignData['supplier_id'] = null;
                }

                // Banner圖檔路徑-Desktop版
                if (isset($data['banner_photo_desktop'])) {
                    // 移除舊圖片
                    if (!empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_desktop'] = $data['banner_photo_desktop']->storePublicly(self::CART_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_desktop']) && $data['is_delete_banner_photo_desktop'] == 'true') {
                    // 移除舊圖片
                    if (!empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    $promotionalCampaignData['banner_photo_desktop'] = null;
                }

                // Banner圖檔路徑-Mobile版
                if (isset($data['banner_photo_mobile'])) {
                    // 移除舊圖片
                    if (!empty($originPromotionalCampaign->banner_photo_mobile)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_mobile)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_mobile);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_mobile'] = $data['banner_photo_mobile']->storePublicly(self::CART_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_mobile']) && $data['is_delete_banner_photo_mobile'] == 'true') {
                    // 移除舊圖片
                    if (!empty($originPromotionalCampaign->banner_photo_mobile)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_mobile)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_mobile);
                    }

                    $promotionalCampaignData['banner_photo_mobile'] = null;
                }

                PromotionalCampaign::findOrFail($id)->update($promotionalCampaignData);

                $updatedThresholdIds = [];
                // 新增或更新活動門檻
                if (isset($data['thresholds'])) {
                    $originThresholdIds = PromotionalCampaignThreshold::where('promotional_campaign_id', $id)->pluck('id');
                    foreach ($data['thresholds'] as $threshold) {
                        // 新增
                        if (!$originThresholdIds->contains($threshold['id'])) {
                            $xValue = 0;
                            if (in_array($originPromotionalCampaign->campaign_type, ['CART_P01', 'CART_P02'])) {
                                $xValue = $threshold['x_value'];
                            }

                            $createdThreshold = PromotionalCampaignThreshold::create([
                                'promotional_campaign_id' => $id,
                                'n_value' => $threshold['n_value'],
                                'x_value' => $xValue,
                                'threshold_brief' => $threshold['threshold_brief'],
                                'created_by' => $user->id,
                                'updated_by' => $user->id,
                            ]);

                            // 新增活動贈品
                            if (in_array($originPromotionalCampaign->campaign_type, ['CART_P03', 'CART_P04'])) {
                                if (isset($threshold['giveaways'])) {
                                    foreach ($threshold['giveaways'] as $giveaway) {
                                        PromotionalCampaignGiveaway::create([
                                            'promotional_campaign_id' => $id,
                                            'sort' => 1,
                                            'product_id' => $giveaway['product_id'],
                                            'assigned_qty' => $giveaway['assigned_qty'],
                                            'assigned_unit_price' => 0,
                                            'threshold_id' => $createdThreshold->id,
                                            'created_by' => $user->id,
                                            'updated_by' => $user->id,
                                        ]);
                                    }
                                }
                            }

                            $updatedThresholdIds[] = $createdThreshold->id;
                        }
                        // 更新
                        else {
                            $xValue = 0;
                            if (in_array($originPromotionalCampaign->campaign_type, ['CART_P01', 'CART_P02'])) {
                                $xValue = $threshold['x_value'];
                            }

                            PromotionalCampaignThreshold::findOrFail($threshold['id'])->update([
                                'n_value' => $threshold['n_value'],
                                'x_value' => $xValue,
                                'threshold_brief' => $threshold['threshold_brief'],
                                'updated_by' => $user->id,
                            ]);

                            $updatedGiveawayIds = [];
                            // 新增或更新活動贈品
                            if (in_array($originPromotionalCampaign->campaign_type, ['CART_P03', 'CART_P04'])) {
                                if (isset($threshold['giveaways'])) {
                                    $originGiveawayIds = PromotionalCampaignGiveaway::where('threshold_id', $threshold['id'])->pluck('id');
                                    foreach ($threshold['giveaways'] as $giveaway) {
                                        // 新增
                                        if (!$originGiveawayIds->contains($giveaway['id'])) {
                                            $createdGiveaway = PromotionalCampaignGiveaway::create([
                                                'promotional_campaign_id' => $id,
                                                'sort' => 1,
                                                'product_id' => $giveaway['product_id'],
                                                'assigned_qty' => $giveaway['assigned_qty'],
                                                'assigned_unit_price' => 0,
                                                'threshold_id' => $threshold['id'],
                                                'created_by' => $user->id,
                                                'updated_by' => $user->id,
                                            ]);

                                            $updatedGiveawayIds[] = $createdGiveaway->id;
                                        }
                                        // 更新
                                        else {
                                            PromotionalCampaignGiveaway::findOrFail($giveaway['id'])->update([
                                                'product_id' => $giveaway['product_id'],
                                                'assigned_qty' => $giveaway['assigned_qty'],
                                                'updated_by' => $user->id,
                                            ]);

                                            $updatedGiveawayIds[] = $giveaway['id'];
                                        }
                                    }
                                }
                            }

                            // 刪除活動贈品
                            PromotionalCampaignGiveaway::where('threshold_id', $threshold['id'])->whereNotIn('id', $updatedGiveawayIds)->delete();

                            $updatedThresholdIds[] = $threshold['id'];
                        }
                    }
                }

                // 刪除活動贈品
                PromotionalCampaignGiveaway::where('promotional_campaign_id', $id)->whereNotIn('threshold_id', $updatedThresholdIds)->delete();
                // 刪除活動門檻
                PromotionalCampaignThreshold::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedThresholdIds)->delete();

                $updatedProductIds = [];
                // 新增或更新活動指定商品
                if (isset($data['products'])) {
                    $originProductIds = PromotionalCampaignProduct::where('promotional_campaign_id', $id)->pluck('id');
                    foreach ($data['products'] as $product) {
                        // 新增
                        if (!$originProductIds->contains($product['id'])) {
                            $createdProduct = PromotionalCampaignProduct::create([
                                'promotional_campaign_id' => $id,
                                'sort' => 1,
                                'product_id' => $product['product_id'],
                                'created_by' => $user->id,
                                'updated_by' => $user->id,
                            ]);

                            $updatedProductIds[] = $createdProduct->id;
                        }
                        // 更新
                        else {
                            PromotionalCampaignProduct::findOrFail($product['id'])->update([
                                'product_id' => $product['product_id'],
                                'updated_by' => $user->id,
                            ]);

                            $updatedProductIds[] = $product['id'];
                        }
                    }
                }

                // 刪除活動指定商品
                PromotionalCampaignProduct::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedProductIds)->delete();
            }

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }
}
