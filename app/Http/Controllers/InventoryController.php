<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Services\InventoryService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    private $supplierService;
    private $warehouseService;
    private $inventoryService;

    public function __construct(
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        InventoryService $inventoryService
    ) {
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * @return View
     * @Author: Eric
     * @DateTime: 2022/1/12 下午 04:55
     */
    public function index(Request $request): view
    {
        $inventories = collect();

        // 有權限
        if ($request->share_role_auth['auth_query']) {
            $payload = $request->only([
                'warehouse',
                'stock_type',
                'stock_status',
                'item_no_start',
                'item_no_end',
                'product_name',
                'supplier',
            ]);

            // 有搜尋條件才會進行處理
            if (!empty($payload)) {
                $inventories = $this->inventoryService->getInventories($payload);
                $inventories = $this->inventoryService->handleInventories($inventories);
            }
        }

        $params = [];
        $params['inventories'] = $inventories;
        $params['supplier'] = $this->supplierService->getSuppliers();
        $params['warehouses'] = $this->warehouseService->getWarehouseList();

        return view('backend.inventory.list', $params);
    }

    /**
     * 匯出excel
     * @param Request $request
     * @return mixed
     * @Author: Eric
     * @DateTime: 2022/1/13 上午 11:46
     */
    public function exportExcel(Request $request)
    {
        // 無權限
        if (!$request->share_role_auth['auth_export']) {
            return response('Forbidden', 403);
        }

        $payload = $request->only([
            'warehouse',
            'stock_type',
            'stock_status',
            'item_no_start',
            'item_no_end',
            'product_name',
            'supplier',
        ]);

        $inventories = $this->inventoryService->getInventories($payload);
        $inventories = $this->inventoryService->handleExcelData($inventories);

        return Excel::download(new InventoryExport($inventories), 'inventories.xlsx');
    }
}
