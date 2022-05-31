<?php

namespace App\Http\Controllers;

use App\Services\LookupValuesVService;
use App\Services\PromotionalCampaignService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PromotionalCampaignPrdController extends Controller
{
    private $promotionalCampaignService;
    private $lookupValuesVService;
    private $supplierService;
    private const LEVEL_CODE = 'PRD';

    public function __construct(
        PromotionalCampaignService $promotionalCampaignService,
        LookupValuesVService $lookupValuesVService,
        SupplierService $supplierService
    ) {
        $this->promotionalCampaignService = $promotionalCampaignService;
        $this->lookupValuesVService = $lookupValuesVService;
        $this->supplierService = $supplierService;
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
            $result['campaigns'] = $this->promotionalCampaignService->getPrdTableList($queryData);
            $result['campaigns'] = $this->promotionalCampaignService->formatPrdTableList($result['campaigns']);
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
        $payload = $request->all();

        if (!$this->promotionalCampaignService->createPrdCampaign($payload)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'promotional_campaign_prd',
            'act' => 'add',
        ];

        return view('backend.success', $result);
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
        $campaign = $this->promotionalCampaignService->getPrdCampaignForShowPage($id);
        $campaign = $this->promotionalCampaignService->formatPrdCampaignForShowPage($campaign);

        return response()->json($campaign);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = [];
        // 活動類型
        $result['campaignTypes'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);

        // 單品活動
        $result['campaign'] = $this->promotionalCampaignService->getPrdCampaignForEditPage($id);
        $result['campaign'] = $this->promotionalCampaignService->formatPrdCampaignForEditPage($result['campaign']);

        return view('backend.promotional_campaign.prd.edit', $result);
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
        $payload = $request->all();

        if (!$this->promotionalCampaignService->updatePrdCampaign($id, $payload)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'promotional_campaign_prd',
            'act' => 'upd',
        ];

        return view('backend.success', $result);
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
     * 是否可以生效
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function canActive(Request $request): JsonResponse
    {
        $result = $this->promotionalCampaignService->canPrdCampaignActive($request->campaign_type, $request->start_at, $request->end_at, $request->product_ids, $request->exclude_promotional_campaign_id);

        if ($result) {
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
        $payload = $request->only([
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
            'limit',
            'exclude_product_ids',
        ]);

        $products = $this->promotionalCampaignService->getProductModalProductsForPrdCampaign($payload);
        $products = $this->promotionalCampaignService->formatProductModalProductsForPrdCampaign($products);

        return response()->json($products);
    }
}
