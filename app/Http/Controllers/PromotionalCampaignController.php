<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\PromotionalCampaignService;

class PromotionalCampaignController extends Controller
{
    private $promotional_campaign_service;
    private $products_service;

    public function __construct(
        PromotionalCampaignService $promotional_campaign_service,
        ProductsService $products_service
    ) {
        $this->promotional_campaign_service = $promotional_campaign_service;
        $this->products_service = $products_service;
    }

    /**
     * 取得商品資料
     *
     * @param Request $request
     * @return json
     */
    public function getProducts(Request $request)
    {
        $input_data = $request->input();
        $products = $this->products_service->getProducts($input_data);

        if (!empty($input_data['exist_products'])) {
            // 過濾已存在的商品
            $products = $products->filter(function ($obj, $key) use ($input_data) {
                return !in_array($obj->id, $input_data['exist_products']);
            });
        }

        $this->products_service->restructureProducts($products);

        $products = $products->mapWithKeys(function ($product) {
            return [
                $product->id => $product->only([
                    'launched_at',
                    'product_name',
                    'product_no',
                    'selling_price',
                    'supplier_name',
                    'launched_status',
                    'gross_margin',
                ]),
            ];
        });

        return response()->json($products);
    }

    public function getDetail(Request $request)
    {
        $promotional_campaign_id = $request->input('promotional_campaign_id');
        $level_code = $request->input('level_code');

        $promotional_campaign = $this->promotional_campaign_service->getPromotionalCampaigns([
            'id' => $promotional_campaign_id,
            'level_code' => $level_code,
        ])->first();

        if (isset($promotional_campaign->products)) {
            $this->products_service->restructureProducts($promotional_campaign->products);
            $promotional_campaign->products = $promotional_campaign->products->mapWithKeys(function ($product) {
                return [
                    $product->product_id => $product->only([
                        'launched_at',
                        'product_name',
                        'product_no',
                        'selling_price',
                        'supplier_name',
                        'launched_status',
                        'gross_margin',
                    ]),
                ];
            });
        }

        if (isset($promotional_campaign->giveaways)) {
            $this->products_service->restructureProducts($promotional_campaign->giveaways);
            $promotional_campaign->giveaways = $promotional_campaign->giveaways->mapWithKeys(function ($giveaway) {
                return [
                    $giveaway->product_id => $giveaway->only([
                        'launched_at',
                        'product_name',
                        'product_no',
                        'selling_price',
                        'supplier_name',
                        'launched_status',
                        'gross_margin',
                        'assigned_qty',
                    ]),
                ];
            });
        }

        return response()->json($promotional_campaign);
    }
}
