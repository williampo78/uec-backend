<?php

namespace App\Http\Controllers;

use App\Services\LookupValuesVService;
use App\Services\PromotionalCampaignService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PromotionalCampaignCartV2Controller extends Controller
{
    private $promotionalCampaignService;
    private $lookupValuesVService;
    private $supplierService;
    private const LEVEL_CODE = 'CART_P';

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
        $result = [];
        $queryData = [];
        $queryData = $request->only([
            'campaign_name_or_campaign_brief',
            'launch_status',
            'campaign_type',
            'start_at_start',
            'start_at_end',
            'product_no',
        ]);

        // 上下架狀態
        $result['launchStatusOptions'] = config('uec.launch_status_options');

        // 活動類型
        $result['campaignTypes'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);

        if (count($queryData) > 0) {
            $result['campaigns'] = $this->promotionalCampaignService->getCartV2TableList($queryData);
            $result['campaigns'] = $this->promotionalCampaignService->formatCartV2TableList($result['campaigns']);
        }

        return view('backend.promotional_campaign.cart_v2.list', $result);
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
        // 供應商
        $result['suppliers'] = $this->supplierService->getSuppliers();

        return view('backend.promotional_campaign.cart_v2.create', $result);
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

        if (!$this->promotionalCampaignService->createCartV2Campaign($payload)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'promotional_campaign_cart_v2',
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
        $result = [];
        // 活動類型
        $result['campaignTypes'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);
        // 供應商
        $result['suppliers'] = $this->supplierService->getSuppliers();
        // 滿額活動
        $result['campaign'] = $this->promotionalCampaignService->getCartV2Campaign($id);
        $result['campaign'] = $this->promotionalCampaignService->formatCartV2Campaign($result['campaign']);

        return view('backend.promotional_campaign.cart_v2.show', $result);
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
        // 供應商
        $result['suppliers'] = $this->supplierService->getSuppliers();
        // 滿額活動
        $result['campaign'] = $this->promotionalCampaignService->getCartV2Campaign($id);
        $result['campaign'] = $this->promotionalCampaignService->formatCartV2Campaign($result['campaign']);

        return view('backend.promotional_campaign.cart_v2.edit', $result);
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

        if (!$this->promotionalCampaignService->updateCartV2Campaign($id, $payload)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'promotional_campaign_cart_v2',
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
        $result = $this->promotionalCampaignService->canCartV2CampaignActive($request->campaign_type, $request->start_at, $request->end_at, $request->product_ids, $request->exclude_promotional_campaign_id);

        if ($result['status']) {
            return response()->json([
                'result' => true,
            ]);
        }

        return response()->json([
            'result' => false,
            'conflict_contents' => $result['conflict_contents'],
        ]);
    }
}
