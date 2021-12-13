<?php

namespace App\Http\Controllers;

use App\Services\LookupValuesVService;
use App\Services\ProductsService;
use App\Services\PromotionalCampaignService;
use App\Services\RoleService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PromotionalCampaignCartController extends Controller
{
    private $promotional_campaign_service;
    private $lookup_values_v_service;
    private $role_service;
    private $supplier_service;
    private $products_service;
    private const LEVEL_CODE = 'CART';

    public function __construct(
        PromotionalCampaignService $promotional_campaign_service,
        LookupValuesVService $lookup_values_v_service,
        RoleService $role_service,
        SupplierService $supplier_service,
        ProductsService $products_service
    ) {
        $this->promotional_campaign_service = $promotional_campaign_service;
        $this->lookup_values_v_service = $lookup_values_v_service;
        $this->role_service = $role_service;
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
        $query_data = [];

        $query_data = $request->only([
            'campaign_name',
            'active',
            'campaign_type',
            'start_at',
            'end_at',
            'product_no',
        ]);

        $query_data['level_code'] = self::LEVEL_CODE;

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        if (!$this->role_service->getOtherRoles()['auth_query'] || count($query_data) < 2) {
            return view('Backend.PromotionalCampaign.CART.list');
        }

        $promotional_campaigns = $this->promotional_campaign_service->getPromotionalCampaigns($query_data);
        $campaign_types = $this->lookup_values_v_service->getCampaignTypes(['udf_01' => self::LEVEL_CODE]);

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
        $campaign_types = $this->lookup_values_v_service->getCampaignTypes(['udf_01' => self::LEVEL_CODE]);
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
        $input_data = $request->except('_token');

        if (!$this->promotional_campaign_service->addPromotionalCampaign($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'promotional_campaign_cart';
        $act = 'add';

        return view(
            'Backend.success',
            compact('route_name', 'act')
        );
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promotional_campaign = $this->promotional_campaign_service->getPromotionalCampaigns([
            'id' => $id,
            'level_code' => 'CART',
        ])->first();
        $suppliers = $this->supplier_service->getSuppliers();

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

        return view(
            'Backend.PromotionalCampaign.CART.update',
            compact('promotional_campaign', 'suppliers')
        );
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

        if (!$this->promotional_campaign_service->updatePromotionalCampaign($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'promotional_campaign_cart';
        $act = 'upd';

        return view(
            'Backend.success',
            compact('route_name', 'act')
        );
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
