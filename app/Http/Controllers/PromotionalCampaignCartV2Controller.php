<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\ProductsService;
use App\Services\SupplierService;
use App\Services\LookupValuesVService;
use App\Services\PromotionalCampaignService;

class PromotionalCampaignCartV2Controller extends Controller
{
    private $promotionalCampaignService;
    private $lookupValuesVService;
    private $supplierService;
    private $products_service;
    private const LEVEL_CODE = 'CART';

    public function __construct(
        PromotionalCampaignService $promotionalCampaignService,
        LookupValuesVService $lookupValuesVService,
        SupplierService $supplierService,
        ProductsService $products_service
    ) {
        $this->promotionalCampaignService = $promotionalCampaignService;
        $this->lookupValuesVService = $lookupValuesVService;
        $this->supplierService = $supplierService;
        $this->products_service = $products_service;
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
        //
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
        //
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
        //
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
}
