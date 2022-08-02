<?php

namespace App\Http\Controllers;

use App\Services\StockTransactionLogService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransactionLogController extends Controller
{
    private $warehouseService;
    private $supplierService;
    private $stockTransactionLogService;

    public function __construct(
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        StockTransactionLogService $stockTransactionLogService
    )
    {
        $this->warehouseService           = $warehouseService;
        $this->supplierService            = $supplierService;
        $this->stockTransactionLogService = $stockTransactionLogService;
    }

    /**
     * @return View
     * @Author: Eric
     * @DateTime: 2022/7/26 上午 11:34
     */
    public function index(Request $request): View
    {
        $payload = $request->only(
            [
                'dateStart',
                'dateEnd',
                'warehouse',
                'supplierId',
                'itemNoStart',
                'itemNoEnd',
                'sourceTableName',
                'sourceDocNo',
                'productNoStart',
                'productNoEnd',
                'stockType',
                'limit',
            ]
        );

        //代理商id
        $supplierId = auth()->user()->supplier_id;

        //供應商列表
        $suppliers = $this->supplierService->getSuppliers()->map(function ($supplier) {
            return [
                'id'   => $supplier->id,
                'text' => $supplier->name,
            ];
        });

        //如果為供應商，僅能查看本身供應商的資料
        if (empty($supplierId) === false) {
            $suppliers             = $suppliers->where('id', $supplierId);
            $payload['supplierId'] = $supplierId;
        }

        //倉庫列表
        $warehouses = $this->warehouseService->getWarehouseList()->map(function ($warehouse) {
            return [
                'id'   => $warehouse->id,
                'text' => $warehouse->name,
            ];
        });

        //庫存類型
        $stockTypes = [];
        foreach (config('uec.stock_type_options') as $id => $text) {
            $stockTypes[] = [
                'id'   => $id,
                'text' => $text,
            ];
        }

        //來源單據名稱列表
        $sourceTableNames = [];
        foreach (config('uec.source_table_name') as $tableNameEnglish => $tableName) {
            $sourceTableNames[] = [
                'id'   => sprintf('%s-%s', $tableNameEnglish, $tableName['transaction_type']),
                'text' => $tableName['chinese_name']
            ];
        }

        $stockTransactionLogs = collect();

        //權限判斷
        if ($request->share_role_auth['auth_query']) {
            $stockTransactionLogs = $this->stockTransactionLogService->getIndexData($payload);
            $stockTransactionLogs = $this->stockTransactionLogService->handleIndexData($stockTransactionLogs);
        }

        $parameters['stockTransactionLogs'] = $stockTransactionLogs;
        $parameters['suppliers']            = $suppliers;
        $parameters['warehouses']           = $warehouses;
        $parameters['stockTypes']           = $stockTypes;
        $parameters['sourceTableNames']     = $sourceTableNames;
        $parameters['supplierId']     = $supplierId;

        return view('backend.stock_transaction_log.list', $parameters);
    }
}
