<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Services\RoleService;
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
    private $role_service;

    public function __construct(
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        InventoryService $inventoryService,
        RoleService $role_service
    ) {
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->inventoryService = $inventoryService;
        $this->role_service = $role_service;
    }

    /**
     * @return View
     * @Author: Eric
     * @DateTime: 2022/1/12 下午 04:55
     */
    public function index(Request $request):view
    {
        $inventories = collect();

        //有權限
        if($this->role_service->getOtherRoles()['auth_query']) {
            //有搜尋條件才會進行處理
            if (empty($request->toArray()) === false) {
                $inventories = $this->inventoryService->getInventories($request->toArray());
                $inventories = $this->inventoryService->handleInventories($inventories);
            }
        }

        $params = [];
        $params['inventories'] = $inventories;
        $params['supplier'] = $this->supplierService->getSuppliers();
        $params['warehouses'] = $this->warehouseService->getWarehouseList();

        return view('Backend.Inventory.list', $params);
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
        //無權限
        if($this->role_service->getOtherRoles()['auth_export'] == false) {
            return response('Forbidden', 403);
        }

        $inventories = $this->inventoryService->getInventories($request->toArray());
        $inventories = $this->inventoryService->handleExcelData($inventories);

        return Excel::download(new InventoryExport($inventories), 'inventories.xlsx');
    }
}
