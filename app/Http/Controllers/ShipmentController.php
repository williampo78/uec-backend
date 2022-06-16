<?php

namespace App\Http\Controllers;

use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    private $shipmentService;

    public function __construct(
        ShipmentService $shipmentService
    ) {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payload = $request->only([
            'created_at_start',
            'created_at_end',
            'shipment_no',
            'member_account',
            'order_no',
            'status_code',
            'payment_method',
            'product_no',
            'product_name',
        ]);

        // 沒有查詢權限、網址列參數不足，直接返回列表頁
        if (!$request->share_role_auth['auth_query'] || empty($payload)) {
            return view('backend.shipment.list');
        }

        $shipments = $this->shipmentService->getShipments($payload);

        // 整理給前端的資料
        $shipments = $shipments->map(function ($shipment) {
            // 建單時間
            $shipment->created_at_format = Carbon::parse($shipment->created_at)->format('Y-m-d H:i');

            // 物流方式
            $shipment->lgst_method = config('uec.lgst_method_options')[$shipment->lgst_method] ?? null;

            // 出貨單狀態
            $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;

            // 出貨時間
            if (isset($shipment->shipped_at)) {
                $shipment->shipped_at = Carbon::parse($shipment->shipped_at)->format('Y-m-d H:i');
            }

            // 物流廠商
            $shipment->lgst_company_code = config('uec.lgst_company_code_options')[$shipment->lgst_company_code] ?? null;

            // 收件地址
            $address = '';
            $address .= $shipment->ship_to_city ?? '';
            $address .= $shipment->ship_to_district ?? '';
            $address .= $shipment->ship_to_address ?? '';
            $shipment->ship_to_address = $address;

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

        return view('backend.shipment.list', compact('shipments'));
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
        $shipment = $this->shipmentService->getShipments([
            'shipment_id' => $id,
        ])->first();

        // 建單時間
        $shipment->created_at_format = Carbon::parse($shipment->created_at)->format('Y-m-d H:i');

        // 出貨單狀態
        $shipment->status_code = config('uec.shipment_status_code_options')[$shipment->status_code] ?? null;

        // 物流廠商
        $shipment->lgst_company_code = config('uec.lgst_company_code_options')[$shipment->lgst_company_code] ?? null;

        // 收件地址
        $address = '';
        $address .= $shipment->ship_to_city ?? '';
        $address .= $shipment->ship_to_district ?? '';
        $address .= $shipment->ship_to_address ?? '';
        $shipment->ship_to_address = $address;

        // EDI轉出時間
        if (isset($shipment->edi_exported_at)) {
            $shipment->edi_exported_at = Carbon::parse($shipment->edi_exported_at)->format('Y-m-d H:i');
        }

        // 出貨時間
        if (isset($shipment->shipped_at)) {
            $shipment->shipped_at = Carbon::parse($shipment->shipped_at)->format('Y-m-d H:i');
        }

        // 到店時間
        if (isset($shipment->arrived_store_at)) {
            $shipment->arrived_store_at = Carbon::parse($shipment->arrived_store_at)->format('Y-m-d H:i');
        }

        // (超取)取件時間
        if ($shipment->lgst_method != 'HOME' && isset($shipment->delivered_at)) {
            $shipment->cvs_completed_at = Carbon::parse($shipment->delivered_at)->format('Y-m-d H:i');
        } else {
            $shipment->cvs_completed_at = null;
        }

        // (宅配)配達時間
        if ($shipment->lgst_method == 'HOME' && isset($shipment->delivered_at)) {
            $shipment->home_dilivered_at = Carbon::parse($shipment->delivered_at)->format('Y-m-d H:i');
        } else {
            $shipment->home_dilivered_at = null;
        }

        // 客拒收 / 未取時間
        if (isset($shipment->overdue_confirmed_at)) {
            $shipment->overdue_confirmed_at = Carbon::parse($shipment->overdue_confirmed_at)->format('Y-m-d H:i');
        }

        // 取消 / 作廢時間
        if (isset($shipment->cancelled_at)) {
            $shipment->cancelled_voided_at = Carbon::parse($shipment->cancelled_at)->format('Y-m-d H:i');
        } elseif (isset($shipment->voided_at)) {
            $shipment->cancelled_voided_at = Carbon::parse($shipment->voided_at)->format('Y-m-d H:i');
        } else {
            $shipment->cancelled_voided_at = null;
        }

        // 物流方式
        $shipment->lgst_method = config('uec.lgst_method_options')[$shipment->lgst_method] ?? null;

        // 出貨單明細
        if (isset($shipment->shipment_details)) {
            $shipment->shipment_details = $shipment->shipment_details->map(function ($shipment_detail) {
                return $shipment_detail->only([
                    'seq',
                    'item_no',
                    'product_name',
                    'spec_1_value',
                    'spec_2_value',
                    'qty',
                ]);
            });
        }

        $shipment = $shipment->only([
            'shipment_no',
            'created_at_format',
            'status_code',
            'lgst_method',
            'lgst_company_code',
            'order_no',
            'ship_to_name',
            'ship_to_mobile',
            'ship_to_address',
            'member_account',
            'edi_exported_at',
            'package_no',
            'shipped_at',
            'arrived_store_at',
            'cvs_completed_at',
            'home_dilivered_at',
            'overdue_confirmed_at',
            'cancelled_voided_at',
            'shipment_details',
        ]);

        return response()->json($shipment);
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
