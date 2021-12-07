<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\SupplierService;
use App\Services\LookupValuesVService;
use App\Services\PromotionalCampaignService;

class PromotionalCampaignCartController extends Controller
{
    private $promotional_campaign_service;
    private $lookup_values_v_service;
    private $role_service;
    private $supplier_service;

    public function __construct(
        PromotionalCampaignService $promotional_campaign_service,
        LookupValuesVService $lookup_values_v_service,
        RoleService $role_service,
        SupplierService $supplier_service
    ){
        $this->promotional_campaign_service = $promotional_campaign_service;
        $this->lookup_values_v_service = $lookup_values_v_service;
        $this->role_service = $role_service;
        $this->supplier_service = $supplier_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $promotional_campaigns = [];
        $query_data = [];

        $query_data = $request->only([
            'campaign_name',
            'active',
            'campaign_type',
            'start_at',
            'end_at',
            'product_no',
        ]);

        $query_data['level_code'] = 'CART';

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        // if (! $this->role_service->getOtherRoles()['auth_query'] || count($query_data) < 2) {
        //     return view('Backend.PromotionalCampaign.CART.list');
        // }

        $promotional_campaigns = $this->promotional_campaign_service->getPromotionalCampaigns($query_data);
        $campaign_types = $this->lookup_values_v_service->getCampaignTypes('CART');

        // $promotional_campaigns = $promotional_campaigns->map(function ($obj, $key) {
        //     /*
        //      * 列表狀態
        //      * 當前時間在上架時間內，且活動的狀態為啟用，列為生效
        //      * 其他為失效
        //      */
        //     $obj->launch_status = (Carbon::now()->between($obj->start_at, $obj->end_at) && $obj->active == 1) ? '生效' : '失效';

        //     return $obj;
        // });

        return view(
            'Backend.PromotionalCampaign.CART.list',
            compact('promotional_campaigns', 'campaign_types', 'query_data')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $campaign_types = $this->lookup_values_v_service->getCampaignTypes('CART');
        $suppliers = $this->supplier_service->getSuppliers();

        return view(
            'Backend.PromotionalCampaign.CART.add',
            compact('campaign_types', 'suppliers')
        );
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
