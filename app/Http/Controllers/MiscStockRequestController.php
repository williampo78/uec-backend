<?php

namespace App\Http\Controllers;

use App\Services\MiscStockRequestService;
use App\Services\SupplierService;
use App\Services\SysConfigService;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
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
            'request_date_start',
            'request_date_end',
            'request_no',
            'request_status',
            'actual_date_start',
            'actual_date_end',
            'product_no',
            'supplier_id',
            'limit',
        ]);

        if ($request->missing('request_date_start')) {
            $requestPayload['request_date_start'] = today()->subMonths(2);
        }

        if ($request->missing('request_date_end')) {
            $requestPayload['request_date_end'] = today();
        }

        if ($request->missing('limit')) {
            $requestPayload['limit'] = 500;
        }

        $responsePayload = [
            'request_statuses' => config('uec.options.misc_stock_requests.request_statuses.out'),
            'suppliers' => $this->supplierService->getSuppliers(),
            'auth' => $request->share_role_auth,
        ];
        // 進貨退出單
        $responsePayload['misc_stock_requests'] = $this->miscStockRequestService->getStockRequestTableList($requestPayload);
        $responsePayload['misc_stock_requests'] = $this->miscStockRequestService->formatStockRequestTableList($responsePayload['misc_stock_requests']);

        return view('backend.misc_stock_request.list', [
            'payload' => $responsePayload,
        ]);
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
            'ship_to_name' => $this->sysConfigService->getConfigValue('LGST_REC_NAME'),
            'ship_to_mobile' => $this->sysConfigService->getConfigValue('LGST_REC_PHONE'),
            'ship_to_address' => $this->sysConfigService->getConfigValue('LGST_REC_ADDR'),
        ];

        return view('backend.misc_stock_request.create', [
            'payload' => $responsePayload,
        ]);
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
            'save_type',
            'warehouse_id',
            'tax',
            'remark',
            'ship_to_name',
            'ship_to_mobile',
            'ship_to_address',
            'items',
        ]);

        if (!$this->miscStockRequestService->createStockRequest($resquestPayload)) {
            return response()->json([
                'message' => '儲存失敗',
            ], 500);
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
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->getStockRequestForShowPage($id);
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->formatStockRequestForShowPage($responsePayload['misc_stock_request']);

        return response()->json([
            'payload' => $responsePayload,
        ]);
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
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->getStockRequestForEditPage($id);
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->formatStockRequestForEditPage($responsePayload['misc_stock_request']);

        return view('backend.misc_stock_request.edit', [
            'payload' => $responsePayload,
        ]);
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
            'save_type',
            'warehouse_id',
            'remark',
            'ship_to_name',
            'ship_to_mobile',
            'ship_to_address',
            'items',
        ]);

        if (!$this->miscStockRequestService->updateStockRequest($id, $resquestPayload)) {
            return response()->json([
                'message' => '儲存失敗',
            ], 500);
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

        return response()->json([
            'payload' => $responsePayload,
        ]);
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
            'product_no',
            'product_name',
            'supplier_id',
            'limit',
            'exclude_product_item_ids',
            'warehouse_id',
        ]);

        $list = $this->miscStockRequestService->getProductItemModalList($requestPayload);
        $list = $this->miscStockRequestService->formatProductItemModalList($list);
        $responsePayload = [
            'list' => $list,
        ];

        return response()->json([
            'payload' => $responsePayload,
        ]);
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

        return response()->json([
            'payload' => $responsePayload,
        ]);
    }

    /**
     * 取得供應商modal的明細
     *
     * @param integer $requestId
     * @param integer $supplierId
     * @return JsonResponse
     */
    public function getSupplierModalDetail(int $requestId, int $supplierId): JsonResponse
    {
        $detail = $this->miscStockRequestService->getSupplierModalDetail($requestId, $supplierId);
        $detail = $this->miscStockRequestService->formatSupplierModalDetail($detail);
        $responsePayload = [
            'detail' => $detail,
        ];

        return response()->json([
            'payload' => $responsePayload,
        ]);
    }

    /**
     * 更新預出日
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateExpectedDate(Request $request, $id)
    {
        $resquestPayload = $request->only([
            'expected_date',
            'ship_to_name',
            'ship_to_mobile',
            'ship_to_address',
        ]);

        if (!$this->miscStockRequestService->updateExpectedDate($id, $resquestPayload)) {
            return response()->json([
                'message' => '儲存失敗',
            ], 500);
        }

        return response()->json([
            'message' => '儲存成功',
        ]);
    }
}
