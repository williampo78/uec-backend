<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\WarehouseService;
use App\Services\InventoryService;
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
    public function index(Request $request):view
    {
        $inventories = $this->inventoryService->getInventories($request->toArray());
        $inventories = $this->inventoryService->handleInventories($inventories);

        $params = [];
        $params['inventories'] = $inventories;
        $params['supplier'] = $this->supplierService->getSuppliers();
        $params['warehouses'] = $this->warehouseService->getWarehouseList();

        return view('Backend.inventory.list', $params);
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
        $inventories = $this->inventoryService->getInventories($request->toArray());
        $inventories = $this->inventoryService->handleInventories($inventories);

        return Excel::download(new InventoryExport($inventories), 'inventories.xlsx');
    }
}
