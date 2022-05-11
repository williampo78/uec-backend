<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\LookupValuesVService;
use App\Services\PromotionalCampaignService;
use Symfony\Component\HttpFoundation\JsonResponse;

class PromotionalCampaignCartV2Controller extends Controller
{
    private $promotionalCampaignService;
    private $lookupValuesVService;
    private $supplierService;
    private $productService;
    private const LEVEL_CODE = 'CART_P';

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
            $result['cartCampaigns'] = $this->promotionalCampaignService->getCartTableList($queryData);
            $result['cartCampaigns'] = $this->promotionalCampaignService->formatCartTableList($result['cartCampaigns']);
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

        if (!$this->promotionalCampaignService->createPromotionalCampaignCart($payload)) {
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
        //
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
        $result['cartCampaign'] = $this->promotionalCampaignService->getPromotionalCampaignCartById($id);
        $result['cartCampaign'] = $this->promotionalCampaignService->formatPromotionalCampaignCart($result['cartCampaign']);

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
        // $input_data = $request->except('_token', '_method');
        // $input_data['promotional_campaign_id'] = $id;

        // if (!$this->promotional_campaign_service->updatePromotionalCampaign($input_data)) {
        //     return back()->withErrors(['message' => '儲存失敗']);
        // }

        // $route_name = 'promotional_campaign_cart';
        // $act = 'upd';

        // return view(
        //     'backend.success',
        //     compact('route_name', 'act')
        // );
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
     * 是否可以通過滿額活動的狀態驗證
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function canActive(Request $request): JsonResponse
    {
        $result = $this->promotionalCampaignService->canPromotionalCampaignCartV2Active($request->campaign_type, $request->start_at, $request->end_at, $request->product_ids);

        if ($result['status']) {
            return response()->json([
                'result' => true,
            ]);
        }

        return response()->json([
            'result' => false,
            'conflict_campaigns' => $result['conflict_campaigns'],
        ]);
    }
}
