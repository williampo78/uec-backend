<?php

namespace App\Http\Controllers;

use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    private $shipment_service;

    public function __construct(
        ShipmentService $shipment_service
    ) {
        $this->shipment_service = $shipment_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query_datas = [];
        $query_datas = $request->query();

        $shipments = $this->shipment_service->getShipments($query_datas);

        // 整理給前端的資料
        $shipments = $shipments->map(function ($shipment) {
            // 建單時間
            $shipment->created_at_format = Carbon::parse($shipment->created_at)->format('Y-m-d H:i');

            // 物流方式
            if (isset(config('uec.lgst_method_options')[$shipment->lgst_method])) {
                $shipment->lgst_method = config('uec.lgst_method_options')[$shipment->lgst_method];
            }

            // 出貨單狀態
            if (isset(config('uec.shipment_status_code_options')[$shipment->status_code])) {
                $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code];
            }

            // 出貨時間
            $shipment->shipped_at = Carbon::parse($shipment->shipped_at)->format('Y-m-d H:i');

            // 物流廠商
            if (isset(config('uec.lgst_company_code_options')[$shipment->lgst_company_code])) {
                $shipment->lgst_company_code = config('uec.lgst_company_code_options')[$shipment->lgst_company_code];
            }

            // 收件地址
            $shipment->ship_to_address = $shipment->ship_to_city . $shipment->ship_to_district . $shipment->ship_to_address;

            return $shipment->only([
                'shipments_id',
                'created_at_format',
                'shipment_no',
                'order_no',
                'lgst_method',
                'status_code',
                'shipped_at',
                'lgst_company_code',
                'member_account',
                'buyer_name',
                'ship_to_name',
                'ship_to_mobile',
                'ship_to_address',
            ]);
        })
            ->toArray();

        return view(
            'Backend.Shipment.list',
            compact('shipments')
        );
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
