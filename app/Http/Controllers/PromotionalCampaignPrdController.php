<?php

namespace App\Http\Controllers;

use App\Services\LookupValuesVService;
use App\Services\ProductService;
use App\Services\PromotionalCampaignService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PromotionalCampaignPrdController extends Controller
{
    private $promotionalCampaignService;
    private $lookupValuesVService;
    private $supplierService;
    private $productService;
    private const LEVEL_CODE = 'PRD';

    public function __construct(
        PromotionalCampaignService $promotionalCampaignService,
        LookupValuesVService $lookupValuesVService,
        SupplierService $supplierService,
        ProductService $productService
    ) {
        $this->promotionalCampaignService = $promotionalCampaignService;
        $this->lookupValuesVService = $lookupValuesVService;
        $this->supplierService = $supplierService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queryData = [];
        $queryData = $request->only([
            'campaign_name_or_campaign_brief',
            'active',
            'campaign_type',
            'start_at_start',
            'start_at_end',
            'product_no',
        ]);

        $result = [];
        // 活動類型
        $result['campaignTypes'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);

        // 狀態
        $result['activeOptions'] = config('uec.active2_options');

        // 網址列參數不足
        if (count($queryData) > 0) {
            $result['prdCampaigns'] = $this->promotionalCampaignService->getPrdTableList($queryData);
            $result['prdCampaigns'] = $this->promotionalCampaignService->formatPrdTableList($result['prdCampaigns']);
        }

        return view('backend.promotional_campaign.prd.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [];
        // 活動類型
        $result['campaignTypes'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);

        return view('backend.promotional_campaign.prd.create', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->except('_token');

        if (!$this->promotionalCampaignService->addPromotionalCampaign($payload)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'promotional_campaign_prd';
        $act = 'add';

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 單品活動
        $prdCampaign = $this->promotionalCampaignService->getPrdCampaignForShowPage($id);
        $prdCampaign = $this->promotionalCampaignService->formatPrdCampaignForShowPage($prdCampaign);

        return response()->json($prdCampaign);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promotional_campaign = $this->promotionalCampaignService->getPromotionalCampaigns([
            'id' => $id,
            'level_code' => self::LEVEL_CODE,
        ])->first();
        $suppliers = $this->supplierService->getSuppliers();

        if (isset($promotional_campaign->products)) {
            $this->productService->restructureProducts($promotional_campaign->products);
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
            $this->productService->restructureProducts($promotional_campaign->giveaways);
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

        return view('backend.promotional_campaign.prd.edit', compact('promotional_campaign', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input_data = $request->except('_token', '_method');
        $input_data['promotional_campaign_id'] = $id;

        if (!$this->promotionalCampaignService->updatePromotionalCampaign($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'promotional_campaign_prd';
        $act = 'upd';

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 是否可以通過單品活動的狀態驗證
     *
     * @param Request $request
     * @return boolean
     */
    public function canPassActiveValidation(Request $request)
    {
        $active = $request->input('active');
        $campaign_type = $request->input('campaign_type');
        $start_at = $request->input('start_at');
        $end_at = $request->input('end_at');
        $exist_products = $request->input('exist_products');
        $promotional_campaign_id = $request->input('promotional_campaign_id');

        if ($active == 0) {
            return response()->json([
                'status' => true,
            ]);
        }

        if ($this->promotionalCampaignService->canPromotionalCampaignPrdActive(
            $campaign_type,
            $start_at,
            $end_at,
            $exist_products,
            $promotional_campaign_id
        )) {
            return response()->json([
                'status' => true,
            ]);
        }

        return response()->json([
            'status' => false,
        ]);
    }

    /**
     * 取得商品modal下拉選項
     *
     * @return void
     */
    public function getProductModalOptions()
    {
        $result = [];
        // 供應商
        $result['suppliers'] = $this->supplierService->getSuppliers();
        // 商品類型
        $result['product_type_options'] = config('uec.product_type_options');

        return response()->json($result);
    }

    /**
     * 取得商品modal的商品
     *
     * @param Request $request
     * @return void
     */
    public function getProductModalProducts(Request $request)
    {
        $queryData = $request->only([
            'supplier_id',
            'product_no',
            'product_name',
            'selling_price_min',
            'selling_price_max',
            'created_at_start',
            'created_at_end',
            'start_launched_at_start',
            'start_launched_at_end',
            'product_type',
            'web_category_hierarchy_id',
            'limit',
            'stock_types',
            'exclude_product_ids',
        ]);

        $products = $this->productService->getModalProducts($queryData);
        $products = $this->productService->formatModalProducts($products);

        return response()->json($products);
    }
}
