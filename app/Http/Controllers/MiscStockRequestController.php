<?php

namespace App\Http\Controllers;

use App\Services\MiscStockRequestService;
use App\Services\SupplierService;
use App\Services\SysConfigService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class MiscStockRequestController extends Controller
{
    private $miscStockRequestService;
    private $supplierService;
    private $warehouseService;
    private $sysConfigService;

    public function __construct(
        MiscStockRequestService $miscStockRequestService,
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        SysConfigService $sysConfigService
    ) {
        $this->miscStockRequestService = $miscStockRequestService;
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->sysConfigService = $sysConfigService;
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

        if ($request->missing('requestDateStart')) {
            $requestPayload['requestDateStart'] = today()->subMonths(2);
        }

        if ($request->missing('requestDateEnd')) {
            $requestPayload['requestDateEnd'] = today();
        }

        if ($request->missing('limit')) {
            $requestPayload['limit'] = 500;
        }

        $responsePayload = [
            'requestStatuses' => config('uec.options.misc_stock_requests.request_statuses.out'),
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
            'shipToName' => $this->sysConfigService->getConfigValue('LGST_REC_NAME'),
            'shipToMobile' => $this->sysConfigService->getConfigValue('LGST_REC_PHONE'),
            'shipToAddress' => $this->sysConfigService->getConfigValue('LGST_REC_ADDR'),
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
        $resquestPayload = $request->only([
            'saveType',
            'warehouseId',
            'tax',
            'remark',
            'shipToName',
            'shipToMobile',
            'shipToAddress',
            'items',
        ]);

        if (!$this->miscStockRequestService->createStockRequest($resquestPayload)) {
            return response()->json(['message' => '儲存失敗'], 500);
        }

        return response()->json([
            'message' => '儲存成功',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $responsePayload['miscStockRequest'] = $this->miscStockRequestService->getStockRequestForShowPage($id);
        $responsePayload['miscStockRequest'] = $this->miscStockRequestService->formatStockRequestForShowPage($responsePayload['miscStockRequest']);
        $response['payload'] = $responsePayload;

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $responsePayload = [
            'warehouses' => $this->warehouseService->getWarehouseList(),
            'taxes' => config('uec.options.taxes'),
        ];
        $responsePayload['miscStockRequest'] = $this->miscStockRequestService->getStockRequestForEditPage($id);
        $responsePayload['miscStockRequest'] = $this->miscStockRequestService->formatStockRequestForEditPage($responsePayload['miscStockRequest']);
        $response['payload'] = $responsePayload;

        return view('backend.misc_stock_request.edit', $response);
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
        $resquestPayload = $request->only([
            'saveType',
            'warehouseId',
            'remark',
            'shipToName',
            'shipToMobile',
            'shipToAddress',
            'items',
        ]);

        if (!$this->miscStockRequestService->updateStockRequest($id, $resquestPayload)) {
            return response()->json(['message' => '儲存失敗'], 500);
        }

        return response()->json([
            'message' => '儲存成功',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->miscStockRequestService->deleteStockRequest($id)) {
            return response()->json([
                'message' => '刪除失敗',
            ], 500);
        }

        return response()->noContent();
    }

    /**
     * 取得品項modal下拉選項
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductItemModalOptions()
    {
        $responsePayload = [
            'suppliers' => $this->supplierService->getSuppliers(),
        ];
        $response['payload'] = $responsePayload;

        return response()->json($response);
    }

    /**
     * 取得品項modal的列表
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductItemModalList(Request $request)
    {
        $requestPayload = $request->only([
            'productNo',
            'productName',
            'supplierId',
            'limit',
            'excludeProductItemIds',
            'warehouseId',
        ]);

        $list = $this->miscStockRequestService->getProductItemModalList($requestPayload);
        $list = $this->miscStockRequestService->formatProductItemModalList($list);
        $responsePayload = [
            'list' => $list,
        ];
        $response['payload'] = $responsePayload;

        return response()->json($response);
    }

    /**
     * 取得供應商modal的列表
     *
     * @param int $requestId
     * @return \Illuminate\Http\Response
     */
    public function getSupplierModalList($requestId)
    {
        $list = $this->miscStockRequestService->getSupplierModalList($requestId);
        $list = $this->miscStockRequestService->formatSupplierModalList($list);
        $responsePayload = [
            'list' => $list,
        ];
        $response['payload'] = $responsePayload;

        return response()->json($response);
    }

    /**
     * 取得供應商modal的明細
     *
     * @param int $requestSupplierId
     * @return \Illuminate\Http\Response
     */
    public function getSupplierModalDetail($requestSupplierId)
    {
        $detail = $this->miscStockRequestService->getSupplierModalDetail($requestSupplierId);
        $detail = $this->miscStockRequestService->formatSupplierModalDetail($detail);
        $responsePayload = [
            'detail' => $detail,
        ];
        $response['payload'] = $responsePayload;

        return response()->json($response);
    }
}
