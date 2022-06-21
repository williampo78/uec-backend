<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use App\Services\MiscStockRequestService;

class MiscStockRequestController extends Controller
{
    private $miscStockRequestService;
    private $supplierService;
    private $warehouseService;

    public function __construct(
        MiscStockRequestService $miscStockRequestService,
        SupplierService $supplierService,
        WarehouseService $warehouseService
    ) {
        $this->miscStockRequestService = $miscStockRequestService;
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestPayload = $request->only([
            'requestDateStart',
            'requestDateEnd',
            'requestNo',
            'statusCode',
            'actualDateStart',
            'actualDateEnd',
            'productNo',
            'supplierId',
            'limit',
        ]);

        $responsePayload = [
            'statusCodes' => config('uec.options.misc_stock_requests.request_statuses.out'),
            'suppliers' => $this->supplierService->getSuppliers(),
            'auth' => $request->share_role_auth,
        ];
        // 進貨退出單
        $responsePayload['miscStockRequests'] = $this->miscStockRequestService->getStockRequestTableList($requestPayload);
        $responsePayload['miscStockRequests'] = $this->miscStockRequestService->formatStockRequestTableList($responsePayload['miscStockRequests']);
        $response['payload'] = $responsePayload;

        return view('backend.misc_stock_request.list', $response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responsePayload = [
            'warehouses' => $this->warehouseService->getWarehouseList(),
            'taxes' => config('uec.options.taxes'),
        ];
        $response['payload'] = $responsePayload;

        return view('backend.misc_stock_request.create', $response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
