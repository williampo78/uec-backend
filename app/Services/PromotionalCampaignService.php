<?php

namespace App\Services;

use App\Models\Product;
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
    private const CART_V2_UPLOAD_PATH_PREFIX = 'promotional_campaign/cart_v2/';

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
     * 取得滿額活動table列表
     *
     * @param array $queryData
     * @return Collection
     */
    public function getCartTableList(array $queryData = []): Collection
    {
        $user = auth()->user();
        $campaigns = PromotionalCampaign::with(['campaignType'])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART');

        // 活動名稱
        if (!empty($queryData['campaign_name'])) {
            $campaigns = $campaigns->where('campaign_name', 'like', '%' . $queryData['campaign_name'] . '%');
        }

        // 狀態
        if (isset($queryData['active'])) {
            $campaigns = $campaigns->where('active', $queryData['active']);
        }

        // 活動類型
        if (isset($queryData['campaign_type'])) {
            $campaigns = $campaigns->where('campaign_type', $queryData['campaign_type']);
        }

        // 上架開始時間-起始日
        if (!empty($queryData['start_at_start'])) {
            $campaigns = $campaigns->whereDate('start_at', '>=', $queryData['start_at_start']);
        }

        // 上架開始時間-結束日
        if (!empty($queryData['start_at_end'])) {
            $campaigns = $campaigns->whereDate('start_at', '<=', $queryData['start_at_end']);
        }

        // 商品序號查詢
        if (!empty($queryData['product_no'])) {
            $campaigns = $campaigns->where(function ($query) use ($queryData) {
                return $query->whereHas('promotionalCampaignProducts.product', function (Builder $query) use ($queryData) {
                    $query->where('product_no', $queryData['product_no']);
                })
                    ->orWhereHas('promotionalCampaignGiveaways.product', function (Builder $query) use ($queryData) {
                        $query->where('product_no', $queryData['product_no']);
                    });
            });
        }

        return $campaigns->latest('start_at')->get();
    }

    /**
     * 整理滿額活動table列表
     *
     * @param Collection $campaigns
     * @return array
     */
    public function formatCartTableList(Collection $campaigns): array
    {
        $result = [];

        foreach ($campaigns as $campaign) {
            $tmpCampaign = [
                'id' => $campaign->id,
                'campaign_name' => $campaign->campaign_name,
                'campaign_type' => null,
                'active' => config('uec.options.actives.type2')[$campaign->active] ?? null,
                'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
                'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            ];

            // 活動類型
            if (isset($campaign->campaignType)) {
                $tmpCampaign['campaign_type'] = $campaign->campaignType->description;
            }

            $result[] = $tmpCampaign;
        }

        return $result;
    }

    /**
     * 取得檢視頁的滿額活動
     *
     * @param integer $id
     * @return Model
     */
    public function getCartCampaignForShowPage(int $id): Model
    {
        $user = auth()->user();
        $campaign = PromotionalCampaign::with([
            'campaignType',
            'promotionalCampaignGiveaways',
            'promotionalCampaignGiveaways.product',
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
        ])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART')
            ->find($id);

        return $campaign;
    }

    /**
     * 整理檢視頁的滿額活動
     *
     * @param Model $campaign
     * @return array
     */
    public function formatCartCampaignForShowPage(Model $campaign): array
    {
        $result = [
            'id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'active' => config('uec.options.actives.type2')[$campaign->active] ?? null,
            'campaign_type' => $campaign->campaign_type,
            'display_campaign_type' => null,
            'n_value' => $campaign->n_value,
            'x_value' => null,
            'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
            'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            'giveaways' => null,
            'products' => null,
        ];

        // 活動類型
        if (isset($campaign->campaignType)) {
            $result['display_campaign_type'] = $campaign->campaignType->description;
        }

        // x值(折扣)
        if (isset($campaign->x_value) && in_array($campaign->campaign_type, ['CART01', 'CART02'])) {
            $result['x_value'] = $campaign->x_value * 100 / 100;
        }

        // 活動贈品
        if ($campaign->promotionalCampaignGiveaways->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignGiveaways as $giveaway) {
                $tmpGiveaway = [
                    'id' => $giveaway->id,
                    'product_no' => null,
                    'product_name' => null,
                    'assigned_qty' => $giveaway->assigned_qty,
                ];

                // 商品
                if (isset($giveaway->product)) {
                    $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                    $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                }

                $result['giveaways'][] = $tmpGiveaway;
            }
        }

        // 活動主商品
        if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
                $tmpCampaignProduct = [
                    'id' => $campaignProduct->id,
                    'product_no' => null,
                    'product_name' => null,
                    'selling_price' => null,
                    'start_launched_at' => null,
                    'end_launched_at' => null,
                    'launch_status' => null,
                    'gross_margin' => null,
                ];

                // 商品
                if (isset($campaignProduct->product)) {
                    $tmpCampaignProduct['product_no'] = $campaignProduct->product->product_no;
                    $tmpCampaignProduct['product_name'] = $campaignProduct->product->product_name;
                    $tmpCampaignProduct['selling_price'] = number_format($campaignProduct->product->selling_price);
                    $tmpCampaignProduct['launch_status'] = $campaignProduct->product->launch_status;
                    $tmpCampaignProduct['gross_margin'] = $campaignProduct->product->gross_margin;

                    // 上架時間起
                    if (isset($campaignProduct->product->start_launched_at)) {
                        $tmpCampaignProduct['start_launched_at'] = Carbon::parse($campaignProduct->product->start_launched_at)->format('Y-m-d H:i:s');
                    }

                    // 上架時間訖
                    if (isset($campaignProduct->product->end_launched_at)) {
                        $tmpCampaignProduct['end_launched_at'] = Carbon::parse($campaignProduct->product->end_launched_at)->format('Y-m-d H:i:s');
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 取得編輯頁的滿額活動
     *
     * @param integer $id
     * @return Model
     */
    public function getCartCampaignForEditPage(int $id): Model
    {
        $user = auth()->user();
        $campaign = PromotionalCampaign::with([
            'promotionalCampaignGiveaways',
            'promotionalCampaignGiveaways.product',
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
        ])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART')
            ->find($id);

        return $campaign;
    }

    /**
     * 整理編輯頁的滿額活動
     *
     * @param Model $campaign
     * @return array
     */
    public function formatCartCampaignForEditPage(Model $campaign): array
    {
        $result = [
            'id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'active' => $campaign->active,
            'campaign_type' => $campaign->campaign_type,
            'n_value' => $campaign->n_value,
            'x_value' => null,
            'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
            'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            'giveaways' => null,
            'products' => null,
        ];

        // x值(折扣)
        if (isset($campaign->x_value) && in_array($campaign->campaign_type, ['CART01', 'CART02'])) {
            $result['x_value'] = $campaign->x_value * 100 / 100;
        }

        // 活動贈品
        if ($campaign->promotionalCampaignGiveaways->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignGiveaways as $giveaway) {
                $tmpGiveaway = [
                    'id' => $giveaway->id,
                    'product_id' => $giveaway->product_id,
                    'product_no' => null,
                    'product_name' => null,
                    'assigned_qty' => $giveaway->assigned_qty,
                ];

                // 商品
                if (isset($giveaway->product)) {
                    $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                    $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                }

                $result['giveaways'][] = $tmpGiveaway;
            }
        }

        // 活動主商品
        if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
                $tmpCampaignProduct = [
                    'id' => $campaignProduct->id,
                    'product_id' => $campaignProduct->product_id,
                    'product_no' => null,
                    'product_name' => null,
                    'selling_price' => null,
                    'start_launched_at' => null,
                    'end_launched_at' => null,
                    'launch_status' => null,
                    'gross_margin' => null,
                ];

                // 商品
                if (isset($campaignProduct->product)) {
                    $tmpCampaignProduct['product_no'] = $campaignProduct->product->product_no;
                    $tmpCampaignProduct['product_name'] = $campaignProduct->product->product_name;
                    $tmpCampaignProduct['selling_price'] = number_format($campaignProduct->product->selling_price);
                    $tmpCampaignProduct['launch_status'] = $campaignProduct->product->launch_status;
                    $tmpCampaignProduct['gross_margin'] = $campaignProduct->product->gross_margin;

                    // 上架時間起
                    if (isset($campaignProduct->product->start_launched_at)) {
                        $tmpCampaignProduct['start_launched_at'] = Carbon::parse($campaignProduct->product->start_launched_at)->format('Y-m-d H:i:s');
                    }

                    // 上架時間訖
                    if (isset($campaignProduct->product->end_launched_at)) {
                        $tmpCampaignProduct['end_launched_at'] = Carbon::parse($campaignProduct->product->end_launched_at)->format('Y-m-d H:i:s');
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 取得滿額活動的商品modal的商品
     *
     * @param array $data
     * @return Collection
     */
    public function getProductModalProductsForCartCampaign(array $data = []): Collection
    {
        $user = auth()->user();
        $products = Product::with([
            'supplier',
        ])
            ->where('agent_id', $user->agent_id);

        // 供應商
        if (isset($data['supplier_id'])) {
            $products = $products->where('supplier_id', $data['supplier_id']);
        }

        // 商品序號
        if (isset($data['product_no'])) {
            $productNos = explode(',', $data['product_no']);
            $productNos = array_unique($productNos);

            if (!empty($productNos)) {
                $products = $products->where(function ($query) use ($productNos) {
                    foreach ($productNos as $productNo) {
                        $query->orWhere('product_no', 'like', '%' . $productNo . '%');
                    }
                });
            }
        }

        // 商品名稱
        if (isset($data['product_name'])) {
            $products = $products->where('product_name', 'like', '%' . $data['product_name'] . '%');
        }

        // 最低售價
        if (isset($data['selling_price_min'])) {
            $products = $products->where('selling_price', '>=', $data['selling_price_min']);
        }

        // 最高售價
        if (isset($data['selling_price_max'])) {
            $products = $products->where('selling_price', '<=', $data['selling_price_max']);
        }

        // 建檔日-起始日期
        if (!empty($data['created_at_start'])) {
            $products = $products->whereDate('created_at', '>=', $data['created_at_start']);
        }

        // 建檔日-結束日期
        if (!empty($data['created_at_end'])) {
            $products = $products->whereDate('created_at', '<=', $data['created_at_end']);
        }

        // 上架時間起-起始日期
        if (!empty($data['start_launched_at_start'])) {
            $products = $products->whereDate('start_launched_at', '>=', $data['start_launched_at_start']);
        }

        // 上架時間起-結束日期
        if (!empty($data['start_launched_at_end'])) {
            $products = $products->whereDate('start_launched_at', '<=', $data['start_launched_at_end']);
        }

        // 商品類型
        if (isset($data['product_type'])) {
            $products = $products->where('product_type', $data['product_type']);
        }

        // 限制筆數
        if (isset($data['limit'])) {
            $products = $products->limit($data['limit']);
        }

        // 排除已存在的商品
        if (!empty($data['exclude_product_ids'])) {
            $products = $products->whereNotIn('id', $data['exclude_product_ids']);
        }

        return $products->get();
    }

    /**
     * 整理滿額活動的商品modal的商品
     *
     * @param Collection $products
     * @return array
     */
    public function formatProductModalProductsForCartCampaign(Collection $products): array
    {
        $result = [];

        foreach ($products as $product) {
            $tmpProduct = [
                'id' => $product->id,
                'product_no' => $product->product_no,
                'product_name' => $product->product_name,
                'selling_price' => number_format($product->selling_price),
                'start_launched_at' => null,
                'end_launched_at' => null,
                'launch_status' => $product->launch_status,
                'gross_margin' => $product->gross_margin,
                'supplier' => null,
            ];

            // 上架時間起
            if (isset($product->start_launched_at)) {
                $tmpProduct['start_launched_at'] = Carbon::parse($product->start_launched_at)->format('Y-m-d H:i:s');
            }

            // 上架時間訖
            if (isset($product->end_launched_at)) {
                $tmpProduct['end_launched_at'] = Carbon::parse($product->end_launched_at)->format('Y-m-d H:i:s');
            }

            // 供應商
            if (isset($product->supplier)) {
                $tmpProduct['supplier'] = $product->supplier->name;
            }

            $result[] = $tmpProduct;
        }

        return $result;
    }

    /**
     * 是否可以生效滿額活動
     *
     * @param string $campaignType
     * @param string $startAt
     * @param string $endAt
     * @param integer $nValue
     * @param integer|null $excludePromotionalCampaignId
     * @return array
     */
    public function canCartCampaignActive(string $campaignType, string $startAt, string $endAt, int $nValue, int $excludePromotionalCampaignId = null): array
    {
        $user = auth()->user();

        /*
         * 查詢上架開始、結束時間，是否在已存在的上下架時間範圍內，且狀態為啟用
         * 如果要更新資料，則需排除要更新的該筆資料檢查
         */
        $promotionalCampaigns = PromotionalCampaign::where('agent_id', $user->agent_id)
            ->where('active', 1)
            ->where('level_code', 'CART')
            ->where('n_value', $nValue)
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

        // ﹝購物車滿N元，打X折﹞、﹝購物車滿N元，折X元﹞
        if (in_array($campaignType, ['CART01', 'CART02'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['CART01', 'CART02']);
        }

        // ﹝購物車滿N元，送贈品﹞
        if (in_array($campaignType, ['CART03'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['CART03']);
        }

        // ﹝指定商品滿N件，送贈品﹞
        if (in_array($campaignType, ['CART04'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereNotIn('campaign_type', ['CART01', 'CART02', 'CART03', 'CART04']);
        }

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
            ];

            $conflictContents[] = $conflictContent;
        }

        return [
            'status' => false,
            'conflict_contents' => $conflictContents,
        ];
    }

    /**
     * 新增滿額活動
     *
     * ﹝滿額﹞購物車滿N元，打X折 => CART01
     * ﹝滿額﹞購物車滿N元，折X元 => CART02
     * ﹝滿額﹞購物車滿N元，送贈品 => CART03
     * ﹝滿額﹞指定商品滿N件，送贈品 => CART04
     *
     * @param array $data
     * @return boolean
     */
    public function createCartCampaign(array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $campaignType = $this->lookupValuesVService->getLookupValuesVsForBackend([
                'type_code' => 'CAMPAIGN_TYPE',
                'udf_01' => 'CART',
                'code' => $data['campaign_type'],
            ])->first();

            // 新增行銷活動
            $createdPromotionalCampaign = PromotionalCampaign::create([
                'agent_id' => $user->agent_id,
                'campaign_type' => $data['campaign_type'],
                'campaign_name' => $data['campaign_name'],
                'active' => $data['active'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'n_value' => $data['n_value'],
                'x_value' => in_array($data['campaign_type'], ['CART01', 'CART02']) ? $data['x_value'] : null,
                'level_code' => $campaignType->udf_01,
                'category_code' => $campaignType->udf_03,
                'promotional_label' => $campaignType->udf_02,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 新增單品
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

            // 新增贈品
            if (isset($data['giveaways'])) {
                foreach ($data['giveaways'] as $giveaway) {
                    PromotionalCampaignGiveaway::create([
                        'promotional_campaign_id' => $createdPromotionalCampaign->id,
                        'sort' => 1,
                        'product_id' => $giveaway['product_id'],
                        'assigned_qty' => $giveaway['assigned_qty'],
                        'assigned_unit_price' => 0,
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
     * ﹝滿額﹞購物車滿N元，打X折 => CART01
     * ﹝滿額﹞購物車滿N元，折X元 => CART02
     * ﹝滿額﹞購物車滿N元，送贈品 => CART03
     * ﹝滿額﹞指定商品滿N件，送贈品 => CART04
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updateCartCampaign(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $originPromotionalCampaign = PromotionalCampaign::findOrFail($id);
            if (now()->greaterThanOrEqualTo($originPromotionalCampaign->start_at)) {
                PromotionalCampaign::findOrFail($id)->update([
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'end_at' => $data['end_at'],
                    'updated_by' => $user->id,
                ]);
            } else {
                PromotionalCampaign::findOrFail($id)->update([
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                    'n_value' => $data['n_value'],
                    'x_value' => in_array($originPromotionalCampaign->campaign_type, ['CART01', 'CART02']) ? $data['x_value'] : null,
                    'updated_by' => $user->id,
                ]);

                $updatedProductIds = [];
                // 新增或更新單品
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

                // 刪除單品
                PromotionalCampaignProduct::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedProductIds)->delete();

                $updatedGiveawayIds = [];
                // 新增或更新贈品
                if (isset($data['giveaways'])) {
                    $originGiveawayIds = PromotionalCampaignGiveaway::where('promotional_campaign_id', $id)->pluck('id');
                    foreach ($data['giveaways'] as $giveaway) {
                        // 新增
                        if (!$originGiveawayIds->contains($giveaway['id'])) {
                            $createdGiveaway = PromotionalCampaignGiveaway::create([
                                'promotional_campaign_id' => $id,
                                'sort' => 1,
                                'product_id' => $giveaway['product_id'],
                                'assigned_qty' => $giveaway['assigned_qty'],
                                'assigned_unit_price' => 0,
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

                // 刪除贈品
                PromotionalCampaignGiveaway::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedGiveawayIds)->delete();
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
     * 取得單品活動table列表
     *
     * @param array $queryData
     * @return Collection
     */
    public function getPrdTableList(array $queryData = []): Collection
    {
        $user = auth()->user();
        $campaigns = PromotionalCampaign::with(['campaignType'])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'PRD');

        // 活動名稱 or 前台文案
        if (!empty($queryData['campaign_name_or_campaign_brief'])) {
            $campaigns = $campaigns->where(function ($query) use ($queryData) {
                $query->where('campaign_name', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%')
                    ->orWhere('campaign_brief', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%');
            });
        }

        // 上下架狀態
        if (isset($queryData['launch_status'])) {
            switch ($queryData['launch_status']) {
                    // 待上架
                case 'prepare_to_launch':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('start_at', '>', now());
                    });
                    break;

                    // 已上架
                case 'launched':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('start_at', '<=', now())
                            ->where('end_at', '>=', now());
                    });
                    break;

                    // 下架
                case 'no_launch':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('end_at', '<', now());
                    });
                    break;

                    // 關閉
                case 'disabled':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 0);
                    });
                    break;
            }
        }

        // 活動類型
        if (isset($queryData['campaign_type'])) {
            $campaigns = $campaigns->where('campaign_type', $queryData['campaign_type']);
        }

        // 上架開始時間-起始日
        if (!empty($queryData['start_at_start'])) {
            $campaigns = $campaigns->whereDate('start_at', '>=', $queryData['start_at_start']);
        }

        // 上架開始時間-結束日
        if (!empty($queryData['start_at_end'])) {
            $campaigns = $campaigns->whereDate('start_at', '<=', $queryData['start_at_end']);
        }

        // 商品序號查詢
        if (!empty($queryData['product_no'])) {
            $campaigns = $campaigns->where(function ($query) use ($queryData) {
                return $query->whereHas('promotionalCampaignProducts.product', function (Builder $query) use ($queryData) {
                    $query->where('product_no', $queryData['product_no']);
                })
                    ->orWhereHas('promotionalCampaignGiveaways.product', function (Builder $query) use ($queryData) {
                        $query->where('product_no', $queryData['product_no']);
                    });
            });
        }

        return $campaigns->latest('start_at')->get();
    }

    /**
     * 整理單品活動table列表
     *
     * @param Collection $campaigns
     * @return array
     */
    public function formatPrdTableList(Collection $campaigns): array
    {
        $result = [];

        foreach ($campaigns as $campaign) {
            $tmpCampaign = [
                'id' => $campaign->id,
                'campaign_name' => $campaign->campaign_name,
                'campaign_brief' => $campaign->campaign_brief,
                'campaign_type' => null,
                'launch_status' => $campaign->launch_status,
                'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
                'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            ];

            // 活動類型
            if (isset($campaign->campaignType)) {
                $tmpCampaign['campaign_type'] = $campaign->campaignType->description;
            }

            $result[] = $tmpCampaign;
        }

        return $result;
    }

    /**
     * 取得檢視頁的單品活動
     *
     * @param integer $id
     * @return Model
     */
    public function getPrdCampaignForShowPage(int $id): Model
    {
        $user = auth()->user();
        $warehouseNumber = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');
        $campaign = PromotionalCampaign::with([
            'campaignType',
            'promotionalCampaignGiveaways',
            'promotionalCampaignGiveaways.product',
            'promotionalCampaignGiveaways.product.productItems.warehouses' => function ($query) use ($warehouseNumber) {
                $query->where('number', $warehouseNumber);
            },
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
        ])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'PRD')
            ->find($id);

        return $campaign;
    }

    /**
     * 整理檢視頁的單品活動
     *
     * @param Model $campaign
     * @return array
     */
    public function formatPrdCampaignForShowPage(Model $campaign): array
    {
        $result = [
            'id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'active' => config('uec.options.actives.type2')[$campaign->active] ?? null,
            'campaign_type' => $campaign->campaign_type,
            'display_campaign_type' => null,
            'n_value' => $campaign->n_value,
            'x_value' => null,
            'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
            'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            'campaign_brief' => $campaign->campaign_brief,
            'giveaways' => null,
            'products' => null,
        ];

        // 活動類型
        if (isset($campaign->campaignType)) {
            $result['display_campaign_type'] = $campaign->campaignType->description;
        }

        // x值(折扣)
        if (isset($campaign->x_value) && in_array($campaign->campaign_type, ['PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
            $result['x_value'] = $campaign->x_value * 100 / 100;
        }

        // 活動贈品
        if ($campaign->promotionalCampaignGiveaways->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignGiveaways as $giveaway) {
                $tmpGiveaway = [
                    'id' => $giveaway->id,
                    'product_no' => null,
                    'product_name' => null,
                    'assigned_qty' => $giveaway->assigned_qty,
                    'stock_qty' => 0,
                    'launch_status' => null,
                ];

                // 商品
                if (isset($giveaway->product)) {
                    $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                    $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                    $tmpGiveaway['launch_status'] = $giveaway->product->launch_status;

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

                $result['giveaways'][] = $tmpGiveaway;
            }
        }

        // 活動主商品
        if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
                $tmpCampaignProduct = [
                    'id' => $campaignProduct->id,
                    'product_no' => null,
                    'product_name' => null,
                    'selling_price' => null,
                    'start_launched_at' => null,
                    'end_launched_at' => null,
                    'launch_status' => null,
                    'gross_margin' => null,
                ];

                // 商品
                if (isset($campaignProduct->product)) {
                    $tmpCampaignProduct['product_no'] = $campaignProduct->product->product_no;
                    $tmpCampaignProduct['product_name'] = $campaignProduct->product->product_name;
                    $tmpCampaignProduct['selling_price'] = number_format($campaignProduct->product->selling_price);
                    $tmpCampaignProduct['launch_status'] = $campaignProduct->product->launch_status;
                    $tmpCampaignProduct['gross_margin'] = $campaignProduct->product->gross_margin;

                    // 上架時間起
                    if (isset($campaignProduct->product->start_launched_at)) {
                        $tmpCampaignProduct['start_launched_at'] = Carbon::parse($campaignProduct->product->start_launched_at)->format('Y-m-d H:i:s');
                    }

                    // 上架時間訖
                    if (isset($campaignProduct->product->end_launched_at)) {
                        $tmpCampaignProduct['end_launched_at'] = Carbon::parse($campaignProduct->product->end_launched_at)->format('Y-m-d H:i:s');
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 取得編輯頁的單品活動
     *
     * @param integer $id
     * @return Model
     */
    public function getPrdCampaignForEditPage(int $id): Model
    {
        $user = auth()->user();
        $warehouseNumber = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');
        $campaign = PromotionalCampaign::with([
            'promotionalCampaignGiveaways',
            'promotionalCampaignGiveaways.product',
            'promotionalCampaignGiveaways.product.productItems.warehouses' => function ($query) use ($warehouseNumber) {
                $query->where('number', $warehouseNumber);
            },
            'promotionalCampaignProducts',
            'promotionalCampaignProducts.product',
        ])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'PRD')
            ->find($id);

        return $campaign;
    }

    /**
     * 整理編輯頁的單品活動
     *
     * @param Model $campaign
     * @return array
     */
    public function formatPrdCampaignForEditPage(Model $campaign): array
    {
        $result = [
            'id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'active' => $campaign->active,
            'campaign_type' => $campaign->campaign_type,
            'n_value' => $campaign->n_value,
            'x_value' => null,
            'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i:s'),
            'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i:s'),
            'campaign_brief' => $campaign->campaign_brief,
            'giveaways' => null,
            'products' => null,
        ];

        // x值(折扣)
        if (isset($campaign->x_value) && in_array($campaign->campaign_type, ['PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
            $result['x_value'] = $campaign->x_value * 100 / 100;
        }

        // 活動贈品
        if ($campaign->promotionalCampaignGiveaways->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignGiveaways as $giveaway) {
                $tmpGiveaway = [
                    'id' => $giveaway->id,
                    'product_id' => $giveaway->product_id,
                    'product_no' => null,
                    'product_name' => null,
                    'assigned_qty' => $giveaway->assigned_qty,
                    'stock_qty' => 0,
                    'launch_status' => null,
                ];

                // 商品
                if (isset($giveaway->product)) {
                    $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                    $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                    $tmpGiveaway['launch_status'] = $giveaway->product->launch_status;

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

                $result['giveaways'][] = $tmpGiveaway;
            }
        }

        // 活動主商品
        if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
                $tmpCampaignProduct = [
                    'id' => $campaignProduct->id,
                    'product_id' => $campaignProduct->product_id,
                    'product_no' => null,
                    'product_name' => null,
                    'selling_price' => null,
                    'start_launched_at' => null,
                    'end_launched_at' => null,
                    'launch_status' => null,
                    'gross_margin' => null,
                ];

                // 商品
                if (isset($campaignProduct->product)) {
                    $tmpCampaignProduct['product_no'] = $campaignProduct->product->product_no;
                    $tmpCampaignProduct['product_name'] = $campaignProduct->product->product_name;
                    $tmpCampaignProduct['selling_price'] = number_format($campaignProduct->product->selling_price);
                    $tmpCampaignProduct['launch_status'] = $campaignProduct->product->launch_status;
                    $tmpCampaignProduct['gross_margin'] = $campaignProduct->product->gross_margin;

                    // 上架時間起
                    if (isset($campaignProduct->product->start_launched_at)) {
                        $tmpCampaignProduct['start_launched_at'] = Carbon::parse($campaignProduct->product->start_launched_at)->format('Y-m-d H:i:s');
                    }

                    // 上架時間訖
                    if (isset($campaignProduct->product->end_launched_at)) {
                        $tmpCampaignProduct['end_launched_at'] = Carbon::parse($campaignProduct->product->end_launched_at)->format('Y-m-d H:i:s');
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 取得單品活動的商品modal的商品
     *
     * @param array $data
     * @return Collection
     */
    public function getProductModalProductsForPrdCampaign(array $data = []): Collection
    {
        $user = auth()->user();
        $warehouseNumber = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');
        $products = Product::with([
            'supplier',
            'productItems.warehouses' => function ($query) use ($warehouseNumber) {
                return $query->where('number', $warehouseNumber);
            },
        ])
            ->where('agent_id', $user->agent_id)
            // 不可選到<門市限定>的商品
            ->where('selling_channel', '!=', 'STORE');

        // 供應商
        if (isset($data['supplier_id']) && $data['supplier_id'] != 'all') {
            $products = $products->where('supplier_id', $data['supplier_id']);
        }

        // 商品序號
        if (isset($data['product_no'])) {
            $productNos = explode(',', $data['product_no']);
            $productNos = array_unique($productNos);

            if (!empty($productNos)) {
                $products = $products->where(function ($query) use ($productNos) {
                    foreach ($productNos as $productNo) {
                        $query->orWhere('product_no', 'like', '%' . $productNo . '%');
                    }
                });
            }
        }

        // 商品名稱
        if (isset($data['product_name'])) {
            $products = $products->where('product_name', 'like', '%' . $data['product_name'] . '%');
        }

        // 最低售價
        if (isset($data['selling_price_min'])) {
            $products = $products->where('selling_price', '>=', $data['selling_price_min']);
        }

        // 最高售價
        if (isset($data['selling_price_max'])) {
            $products = $products->where('selling_price', '<=', $data['selling_price_max']);
        }

        // 建檔日-起始日期
        if (!empty($data['created_at_start'])) {
            $products = $products->whereDate('created_at', '>=', $data['created_at_start']);
        }

        // 建檔日-結束日期
        if (!empty($data['created_at_end'])) {
            $products = $products->whereDate('created_at', '<=', $data['created_at_end']);
        }

        // 上架時間起-起始日期
        if (!empty($data['start_launched_at_start'])) {
            $products = $products->whereDate('start_launched_at', '>=', $data['start_launched_at_start']);
        }

        // 上架時間起-結束日期
        if (!empty($data['start_launched_at_end'])) {
            $products = $products->whereDate('start_launched_at', '<=', $data['start_launched_at_end']);
        }

        // 商品類型
        if (isset($data['product_type'])) {
            $products = $products->where('product_type', $data['product_type']);
        }

        // 限制筆數
        if (isset($data['limit'])) {
            $products = $products->limit($data['limit']);
        }

        // 庫存類型
        if (!empty($data['stock_types'])) {
            $products = $products->whereIn('stock_type', $data['stock_types']);
        }

        // 排除已存在的商品
        if (!empty($data['exclude_product_ids'])) {
            $products = $products->whereNotIn('id', $data['exclude_product_ids']);
        }

        return $products->get();
    }

    /**
     * 整理單品活動的商品modal的商品
     *
     * @param Collection $products
     * @return array
     */
    public function formatProductModalProductsForPrdCampaign(Collection $products): array
    {
        $result = [];

        foreach ($products as $product) {
            $tmpProduct = [
                'id' => $product->id,
                'product_no' => $product->product_no,
                'product_name' => $product->product_name,
                'selling_price' => $product->selling_price,
                'start_launched_at' => null,
                'end_launched_at' => null,
                'launch_status' => $product->launch_status,
                'gross_margin' => $product->gross_margin,
                'supplier' => null,
                'stock_qty' => 0,
            ];

            // 上架時間起
            if (isset($product->start_launched_at)) {
                $tmpProduct['start_launched_at'] = Carbon::parse($product->start_launched_at)->format('Y-m-d H:i:s');
            }

            // 上架時間訖
            if (isset($product->end_launched_at)) {
                $tmpProduct['end_launched_at'] = Carbon::parse($product->end_launched_at)->format('Y-m-d H:i:s');
            }

            // 供應商
            if (isset($product->supplier)) {
                $tmpProduct['supplier'] = $product->supplier->name;
            }

            // 合計各個商品品項的庫存數
            $stockQty = 0;
            if ($product->productItems->isNotEmpty()) {
                foreach ($product->productItems as $productItem) {
                    $warehouse = $productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty += $warehouse->pivot->stock_qty;
                    }
                }
            }
            $tmpProduct['stock_qty'] = $stockQty;

            $result[] = $tmpProduct;
        }

        return $result;
    }

    /**
     * 是否可以生效單品活動
     *
     * @param string $campaignType
     * @param string $startAt
     * @param string $endAt
     * @param array $productIds
     * @param integer|null $excludePromotionalCampaignId
     * @return array
     */
    public function canPrdCampaignActive(string $campaignType, string $startAt, string $endAt, array $productIds, int $excludePromotionalCampaignId = null): array
    {
        $user = auth()->user();

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
            ->where('level_code', 'PRD')
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

        // ﹝第N件(含)以上打X折﹞、﹝第N件(含)以上折X元﹞、﹝滿N件，每件打X折﹞、﹝滿N件，每件折X元﹞
        if (in_array($campaignType, ['PRD01', 'PRD02', 'PRD03', 'PRD04'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['PRD01', 'PRD02', 'PRD03', 'PRD04']);
        }

        // ﹝買N件，送贈品﹞
        if (in_array($campaignType, ['PRD05'])) {
            $promotionalCampaigns = $promotionalCampaigns->whereIn('campaign_type', ['PRD05']);
        }

        $promotionalCampaigns = $promotionalCampaigns->whereHas('promotionalCampaignProducts', function (Builder $query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
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
     * 新增單品活動
     *
     * ﹝單品﹞第N件(含)以上，打X折 => PRD01
     * ﹝單品﹞第N件(含)以上，折X元 => PRD02
     * ﹝單品﹞滿N件，每件打X折 => PRD03
     * ﹝單品﹞滿N件，每件折X元 => PRD04
     * ﹝單品﹞滿N件，送贈品 => PRD05
     *
     * @param array $data
     * @return boolean
     */
    public function createPrdCampaign(array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $campaignType = $this->lookupValuesVService->getLookupValuesVsForBackend([
                'type_code' => 'CAMPAIGN_TYPE',
                'udf_01' => 'PRD',
                'code' => $data['campaign_type'],
            ])->first();

            // 新增行銷活動
            $createdPromotionalCampaign = PromotionalCampaign::create([
                'agent_id' => $user->agent_id,
                'campaign_type' => $data['campaign_type'],
                'campaign_name' => $data['campaign_name'],
                'active' => $data['active'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'campaign_brief' => $data['campaign_brief'],
                'n_value' => $data['n_value'],
                'x_value' => in_array($data['campaign_type'], ['PRD01', 'PRD02', 'PRD03', 'PRD04']) ? $data['x_value'] : null,
                'level_code' => $campaignType->udf_01,
                'category_code' => $campaignType->udf_03,
                'promotional_label' => $campaignType->udf_02,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 新增單品
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

            // 新增贈品
            if (isset($data['giveaways'])) {
                foreach ($data['giveaways'] as $giveaway) {
                    PromotionalCampaignGiveaway::create([
                        'promotional_campaign_id' => $createdPromotionalCampaign->id,
                        'sort' => 1,
                        'product_id' => $giveaway['product_id'],
                        'assigned_qty' => $giveaway['assigned_qty'],
                        'assigned_unit_price' => 0,
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
     * 更新單品活動
     *
     * ﹝單品﹞第N件(含)以上，打X折 => PRD01
     * ﹝單品﹞第N件(含)以上，折X元 => PRD02
     * ﹝單品﹞滿N件，每件打X折 => PRD03
     * ﹝單品﹞滿N件，每件折X元 => PRD04
     * ﹝單品﹞滿N件，送贈品 => PRD05
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updatePrdCampaign(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $originPromotionalCampaign = PromotionalCampaign::findOrFail($id);
            if (now()->greaterThanOrEqualTo($originPromotionalCampaign->start_at)) {
                PromotionalCampaign::findOrFail($id)->update([
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'end_at' => $data['end_at'],
                    'campaign_brief' => $data['campaign_brief'],
                    'updated_by' => $user->id,
                ]);
            } else {
                PromotionalCampaign::findOrFail($id)->update([
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                    'campaign_brief' => $data['campaign_brief'],
                    'n_value' => $data['n_value'],
                    'x_value' => in_array($originPromotionalCampaign->campaign_type, ['PRD01', 'PRD02', 'PRD03', 'PRD04']) ? $data['x_value'] : null,
                    'updated_by' => $user->id,
                ]);

                $updatedProductIds = [];
                // 新增或更新單品
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

                // 刪除單品
                PromotionalCampaignProduct::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedProductIds)->delete();

                $updatedGiveawayIds = [];
                // 新增或更新贈品
                if (isset($data['giveaways'])) {
                    $originGiveawayIds = PromotionalCampaignGiveaway::where('promotional_campaign_id', $id)->pluck('id');
                    foreach ($data['giveaways'] as $giveaway) {
                        // 新增
                        if (!$originGiveawayIds->contains($giveaway['id'])) {
                            $createdGiveaway = PromotionalCampaignGiveaway::create([
                                'promotional_campaign_id' => $id,
                                'sort' => 1,
                                'product_id' => $giveaway['product_id'],
                                'assigned_qty' => $giveaway['assigned_qty'],
                                'assigned_unit_price' => 0,
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

                // 刪除贈品
                PromotionalCampaignGiveaway::where('promotional_campaign_id', $id)->whereNotIn('id', $updatedGiveawayIds)->delete();
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
     * 取得新版滿額活動table列表
     *
     * @param array $queryData
     * @return Collection
     */
    public function getCartV2TableList(array $queryData = []): Collection
    {
        $user = auth()->user();
        $campaigns = PromotionalCampaign::with(['campaignType'])
            ->where('agent_id', $user->agent_id)
            ->where('level_code', 'CART_P');

        // 活動名稱 or 前台文案
        if (!empty($queryData['campaign_name_or_campaign_brief'])) {
            $campaigns = $campaigns->where(function ($query) use ($queryData) {
                return $query->where('campaign_name', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%')
                    ->orWhere('campaign_brief', 'LIKE', '%' . $queryData['campaign_name_or_campaign_brief'] . '%');
            });
        }

        // 上下架狀態
        if (isset($queryData['launch_status'])) {
            switch ($queryData['launch_status']) {
                    // 待上架
                case 'prepare_to_launch':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('start_at', '>', now());
                    });
                    break;

                    // 已上架
                case 'launched':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('start_at', '<=', now())
                            ->where('end_at', '>=', now());
                    });
                    break;

                    // 下架
                case 'no_launch':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 1)
                            ->where('end_at', '<', now());
                    });
                    break;

                    // 關閉
                case 'disabled':
                    $campaigns = $campaigns->where(function ($query) {
                        $query->where('active', 0);
                    });
                    break;
            }
        }

        // 活動類型
        if (isset($queryData['campaign_type'])) {
            $campaigns = $campaigns->where('campaign_type', $queryData['campaign_type']);
        }

        // 上架開始時間-起始日
        if (!empty($queryData['start_at_start'])) {
            $campaigns = $campaigns->whereDate('start_at', '>=', $queryData['start_at_start']);
        }

        // 上架開始時間-結束日
        if (!empty($queryData['start_at_end'])) {
            $campaigns = $campaigns->whereDate('start_at', '<=', $queryData['start_at_end']);
        }

        // 商品序號查詢
        if (!empty($queryData['product_no'])) {
            $campaigns = $campaigns->where(function ($query) use ($queryData) {
                return $query->whereHas('promotionalCampaignProducts.product', function (Builder $query) use ($queryData) {
                    return $query->where('product_no', $queryData['product_no']);
                })
                    ->orWhereHas('promotionalCampaignGiveaways.product', function (Builder $query) use ($queryData) {
                        return $query->where('product_no', $queryData['product_no']);
                    });
            });
        }

        return $campaigns->latest('start_at')->get();
    }

    /**
     * 整理新版滿額活動table列表
     *
     * @param Collection $campaigns
     * @return array
     */
    public function formatCartV2TableList(Collection $campaigns): array
    {
        $result = [];

        foreach ($campaigns as $campaign) {
            $tmpCampaign = [
                'id' => $campaign->id,
                'campaign_name' => $campaign->campaign_name,
                'campaign_brief' => $campaign->campaign_brief,
                'campaign_type' => null,
                'launch_status' => $campaign->launch_status,
                'start_at' => Carbon::parse($campaign->start_at)->format('Y-m-d H:i'),
                'end_at' => Carbon::parse($campaign->end_at)->format('Y-m-d H:i'),
            ];

            // 活動類型
            if (isset($campaign->campaignType)) {
                $tmpCampaign['campaign_type'] = $campaign->campaignType->description;
            }

            $result[] = $tmpCampaign;
        }

        return $result;
    }

    /**
     * 取得新版滿額活動
     *
     * @param integer $id
     * @return Model
     */
    public function getCartV2Campaign(int $id): Model
    {
        $user = auth()->user();
        $warehouseNumber = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');
        $campaign = PromotionalCampaign::with([
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

        return $campaign;
    }

    /**
     * 整理新版滿額活動
     *
     * @param Model $campaign
     * @return array
     */
    public function formatCartV2Campaign(Model $campaign): array
    {
        $result = [
            'id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'campaign_type' => $campaign->campaign_type,
            'active' => $campaign->active,
            'start_at' => $campaign->start_at,
            'end_at' => $campaign->end_at,
            'campaign_brief' => $campaign->campaign_brief,
            'url_code' => $campaign->url_code,
            'stock_type' => null,
            'supplier_id' => isset($campaign->supplier_id) ? $campaign->supplier_id : 'all',
            'banner_photo_desktop_url' => !empty($campaign->banner_photo_desktop) ? config('filesystems.disks.s3.url') . $campaign->banner_photo_desktop : null,
            'banner_photo_mobile_url' => !empty($campaign->banner_photo_mobile) ? config('filesystems.disks.s3.url') . $campaign->banner_photo_mobile : null,
            'thresholds' => null,
            'products' => null,
        ];

        // 庫存類型
        if ($campaign->ship_from_whs == 'SELF') {
            $result['stock_type'] = 'A_B';
        } elseif ($campaign->ship_from_whs == 'SUP') {
            $result['stock_type'] = 'T';
        }

        // 活動門檻
        if ($campaign->promotionalCampaignThresholds->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignThresholds as $threshold) {
                $tmpThreshold = [
                    'id' => $threshold->id,
                    'n_value' => $threshold->n_value,
                    'x_value' => null,
                    'giveaways' => null,
                ];

                // ﹝指定商品滿N元，打X折﹞、﹝指定商品滿N元，折X元﹞
                if (in_array($campaign->campaign_type, ['CART_P01', 'CART_P02'])) {
                    $tmpThreshold['x_value'] = $threshold->x_value * 100 / 100;
                }
                // ﹝指定商品滿N件，送贈品﹞、﹝指定商品滿N元，送贈品﹞
                elseif (in_array($campaign->campaign_type, ['CART_P03', 'CART_P04'])) {
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
                                'launch_status' => null,
                            ];

                            if (isset($giveaway->product)) {
                                $tmpGiveaway['product_no'] = $giveaway->product->product_no;
                                $tmpGiveaway['product_name'] = $giveaway->product->product_name;
                                $tmpGiveaway['stock_type'] = config('uec.stock_type_options')[$giveaway->product->stock_type] ?? null;
                                $tmpGiveaway['launch_status'] = $giveaway->product->launch_status;

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
        if ($campaign->promotionalCampaignProducts->isNotEmpty()) {
            foreach ($campaign->promotionalCampaignProducts as $campaignProduct) {
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
                        $tmpCampaignProduct['web_category_hierarchy'] = isset($webCategoryHierarchy) ? $this->webCategoryHierarchyService->getAncestorsAndSelfName($webCategoryHierarchy->id) : null;
                    }
                }

                $result['products'][] = $tmpCampaignProduct;
            }
        }

        return $result;
    }

    /**
     * 取得新版滿額活動的商品modal的商品
     *
     * @param array $data
     * @return Collection
     */
    public function getProductModalProductsForCartV2Campaign(array $data = []): Collection
    {
        $user = auth()->user();
        $warehouseNumber = $this->sysConfigService->getConfigValue('EC_WAREHOUSE_GOODS');

        $products = Product::with([
            'supplier',
            'webCategoryHierarchies' => function ($query) {
                return $query->oldest('web_category_products.sort')->oldest('web_category_products.id');
            },
            'productItems.warehouses' => function ($query) use ($warehouseNumber) {
                return $query->where('number', $warehouseNumber);
            },
        ])
            ->where('agent_id', $user->agent_id)
            // 不可選到<門市限定>的商品
            ->where('selling_channel', '!=', 'STORE');

        // 供應商
        if (isset($data['supplier_id']) && $data['supplier_id'] != 'all') {
            $products = $products->where('supplier_id', $data['supplier_id']);
        }

        // 商品序號
        if (isset($data['product_no'])) {
            $productNos = explode(',', $data['product_no']);
            $productNos = array_unique($productNos);

            if (!empty($productNos)) {
                $products = $products->where(function ($query) use ($productNos) {
                    foreach ($productNos as $productNo) {
                        $query->orWhere('product_no', 'like', '%' . $productNo . '%');
                    }
                });
            }
        }

        // 商品名稱
        if (isset($data['product_name'])) {
            $products = $products->where('product_name', 'like', '%' . $data['product_name'] . '%');
        }

        // 最低售價
        if (isset($data['selling_price_min'])) {
            $products = $products->where('selling_price', '>=', $data['selling_price_min']);
        }

        // 最高售價
        if (isset($data['selling_price_max'])) {
            $products = $products->where('selling_price', '<=', $data['selling_price_max']);
        }

        // 建檔日-起始日期
        if (!empty($data['created_at_start'])) {
            $products = $products->whereDate('created_at', '>=', $data['created_at_start']);
        }

        // 建檔日-結束日期
        if (!empty($data['created_at_end'])) {
            $products = $products->whereDate('created_at', '<=', $data['created_at_end']);
        }

        // 上架時間起-起始日期
        if (!empty($data['start_launched_at_start'])) {
            $products = $products->whereDate('start_launched_at', '>=', $data['start_launched_at_start']);
        }

        // 上架時間起-結束日期
        if (!empty($data['start_launched_at_end'])) {
            $products = $products->whereDate('start_launched_at', '<=', $data['start_launched_at_end']);
        }

        // 商品類型
        if (isset($data['product_type'])) {
            $products = $products->where('product_type', $data['product_type']);
        }

        // 前台分類
        if (isset($data['web_category_hierarchy_id'])) {
            $products = $products->whereHas('webCategoryHierarchies', function (Builder $query) use ($data) {
                return $query->where('web_category_hierarchy.id', $data['web_category_hierarchy_id']);
            });
        }

        // 限制筆數
        if (isset($data['limit'])) {
            $products = $products->limit($data['limit']);
        }

        // 庫存類型
        if (!empty($data['stock_types'])) {
            $products = $products->whereIn('stock_type', $data['stock_types']);
        }

        // 排除已存在的商品
        if (!empty($data['exclude_product_ids'])) {
            $products = $products->whereNotIn('id', $data['exclude_product_ids']);
        }

        return $products->get();
    }

    /**
     * 整理新版滿額活動的商品modal的商品
     *
     * @param Collection $products
     * @return array
     */
    public function formatProductModalProductsForCartV2Campaign(Collection $products): array
    {
        $result = [];

        foreach ($products as $product) {
            $tmpProduct = [
                'id' => $product->id,
                'product_no' => $product->product_no,
                'product_name' => $product->product_name,
                'selling_price' => number_format($product->selling_price),
                'start_launched_at' => null,
                'launch_status' => $product->launch_status,
                'gross_margin' => $product->gross_margin,
                'product_type' => null,
                'web_category_hierarchy' => null,
                'supplier' => null,
                'stock_type' => config('uec.stock_type_options')[$product->stock_type] ?? null,
                'stock_qty' => 0,
            ];

            // 上架時間起
            if (isset($product->start_launched_at)) {
                $tmpProduct['start_launched_at'] = Carbon::parse($product->start_launched_at)->format('Y-m-d H:i');
            }

            // 商品類型
            if (isset($product->product_type)) {
                $tmpProduct['product_type'] = config('uec.product_type_options')[$product->product_type] ?? null;
            }

            // 供應商
            if (isset($product->supplier)) {
                $tmpProduct['supplier'] = $product->supplier->name;
            }

            // 前台分類
            if ($product->webCategoryHierarchies->isNotEmpty()) {
                $webCategoryHierarchy = $product->webCategoryHierarchies->first();
                $tmpProduct['web_category_hierarchy'] = isset($webCategoryHierarchy) ? $this->webCategoryHierarchyService->getAncestorsAndSelfName($webCategoryHierarchy->id) : null;
            }

            // 合計各個商品品項的庫存數
            $stockQty = 0;
            if ($product->productItems->isNotEmpty()) {
                foreach ($product->productItems as $productItem) {
                    $warehouse = $productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty += $warehouse->pivot->stock_qty;
                    }
                }
            }
            $tmpProduct['stock_qty'] = $stockQty;

            $result[] = $tmpProduct;
        }

        return $result;
    }

    /**
     * 是否可以生效新版滿額活動
     *
     * @param string $campaignType
     * @param string $startAt
     * @param string $endAt
     * @param array $productIds
     * @param integer|null $excludePromotionalCampaignId
     * @return array
     */
    public function canCartV2CampaignActive(string $campaignType, string $startAt, string $endAt, array $productIds, int $excludePromotionalCampaignId = null): array
    {
        $user = auth()->user();

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
     * 新增新版滿額活動
     *
     * @param array $data
     * @return boolean
     */
    public function createCartV2Campaign(array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $campaignType = $this->lookupValuesVService->getLookupValuesVsForBackend([
                'type_code' => 'CAMPAIGN_TYPE',
                'udf_01' => 'CART_P',
                'code' => $data['campaign_type'],
            ])->first();

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
                'level_code' => $campaignType->udf_01,
                'category_code' => $campaignType->udf_03,
                'promotional_label' => $campaignType->udf_02,
                'banner_photo_desktop' => null,
                'banner_photo_mobile' => null,
                'ship_from_whs' => $shipFromWhs,
                'supplier_id' => $supplierId,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 儲存desktop圖片
            if (isset($data['banner_photo_desktop'])) {
                $bannerPhotoDesktop = $data['banner_photo_desktop']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $createdPromotionalCampaign->id, 's3');
                PromotionalCampaign::findOrFail($createdPromotionalCampaign->id)->update([
                    'banner_photo_desktop' => $bannerPhotoDesktop,
                ]);
            }

            // 儲存mobile圖片
            if (isset($data['banner_photo_mobile'])) {
                $bannerPhotoMobile = $data['banner_photo_mobile']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $createdPromotionalCampaign->id, 's3');
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
     * 更新新版滿額活動
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updateCartV2Campaign(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;
        $originPromotionalCampaign = PromotionalCampaign::findOrFail($id);

        DB::beginTransaction();
        try {
            if (now()->greaterThanOrEqualTo($originPromotionalCampaign->start_at)) {
                $promotionalCampaignData = [
                    'campaign_name' => $data['campaign_name'],
                    'active' => $data['active'],
                    'end_at' => $data['end_at'],
                    'campaign_brief' => $data['campaign_brief'],
                    'updated_by' => $user->id,
                ];

                // Banner圖檔路徑-Desktop版
                if (isset($data['banner_photo_desktop'])) {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_desktop'] = $data['banner_photo_desktop']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_desktop']) && $data['is_delete_banner_photo_desktop'] == 'true') {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    $promotionalCampaignData['banner_photo_desktop'] = null;
                }

                // Banner圖檔路徑-Mobile版
                if (isset($data['banner_photo_mobile'])) {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_mobile)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_mobile)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_mobile);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_mobile'] = $data['banner_photo_mobile']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_mobile']) && $data['is_delete_banner_photo_mobile'] == 'true') {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_mobile)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_mobile)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_mobile);
                    }

                    $promotionalCampaignData['banner_photo_mobile'] = null;
                }

                PromotionalCampaign::findOrFail($id)->update($promotionalCampaignData);
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
                    if (
                        !empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_desktop'] = $data['banner_photo_desktop']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_desktop']) && $data['is_delete_banner_photo_desktop'] == 'true') {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_desktop)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_desktop)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_desktop);
                    }

                    $promotionalCampaignData['banner_photo_desktop'] = null;
                }

                // Banner圖檔路徑-Mobile版
                if (isset($data['banner_photo_mobile'])) {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_mobile)
                        && Storage::disk('s3')->exists($originPromotionalCampaign->banner_photo_mobile)
                    ) {
                        Storage::disk('s3')->delete($originPromotionalCampaign->banner_photo_mobile);
                    }

                    // 上傳新圖片
                    $promotionalCampaignData['banner_photo_mobile'] = $data['banner_photo_mobile']->storePublicly(self::CART_V2_UPLOAD_PATH_PREFIX . $id, 's3');
                } elseif (isset($data['is_delete_banner_photo_mobile']) && $data['is_delete_banner_photo_mobile'] == 'true') {
                    // 移除舊圖片
                    if (
                        !empty($originPromotionalCampaign->banner_photo_mobile)
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
