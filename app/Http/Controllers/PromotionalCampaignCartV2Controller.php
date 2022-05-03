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
    private $promotional_campaign_service;
    private $lookup_values_v_service;
    private $supplier_service;
    private $products_service;
    private const LEVEL_CODE = 'CART';

    public function __construct(
        PromotionalCampaignService $promotional_campaign_service,
        LookupValuesVService $lookup_values_v_service,
        SupplierService $supplier_service,
        ProductsService $products_service
    ) {
        $this->promotional_campaign_service = $promotional_campaign_service;
        $this->lookup_values_v_service = $lookup_values_v_service;
        $this->supplier_service = $supplier_service;
        $this->products_service = $products_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $promotional_campaigns = [];
        $queryData = [];

        $queryData = $request->only([
            'campaign_name',
            'active',
            'campaign_type',
            'start_at_start',
            'start_at_end',
            'product_no',
        ]);

        // 活動類型
        $campaign_types = $this->lookup_values_v_service->getLookupValuesVs([
            'type_code' => 'CAMPAIGN_TYPE',
            'udf_01' => self::LEVEL_CODE,
        ]);

        // 網址列參數不足，直接返回列表頁
        if (count($queryData) < 1) {
            return view('backend.promotional_campaign.cart.list', compact('campaign_types'));
        }

        $queryData['level_code'] = self::LEVEL_CODE;

        $promotional_campaigns = $this->promotional_campaign_service->getPromotionalCampaigns($queryData);

        return view('backend.promotional_campaign.cart_v2.list', compact('promotional_campaigns', 'campaign_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
