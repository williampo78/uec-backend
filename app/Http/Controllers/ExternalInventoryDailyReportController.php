<?php

namespace App\Http\Controllers;

use App\Exports\ExternalInventoryDailyReportExport;
use App\Services\ExternalInventoryDailyReportService;
use App\Services\RoleService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class ExternalInventoryDailyReportController extends Controller
{
    private $supplierService;
    private $warehouseService;
    private $externalInventoryDailyReportService;
    private $role_service;

    public function __construct(
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        ExternalInventoryDailyReportService $externalInventoryDailyReportService,
        RoleService $role_service
    )
    {
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->externalInventoryDailyReportService = $externalInventoryDailyReportService;
        $this->role_service = $role_service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/20 上午 10:04
     */
    public function index(Request $request)
    {
        $dailyReports = collect();

        //有權限
        if($this->role_service->getOtherRoles()['auth_query']) {
            //有搜尋條件才會進行處理
            if (empty($request->toArray()) === false) {
                $dailyReports = $this->externalInventoryDailyReportService->getIndexData($request->toArray());
                $dailyReports = $this->externalInventoryDailyReportService->handleIndexData($dailyReports);
            }
        }

        $params = [];
        $params['dailyReports'] = $dailyReports;
        $params['supplier'] = $this->supplierService->getSuppliers();
        $params['warehouses'] = $this->warehouseService->getWarehouseList();

        return view('backend.external_inventory_daily_report.list', $params);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Author: Eric
     * @DateTime: 2022/1/20 上午 10:04
     */
    public function exportExcel(Request $request)
    {
        //無權限
        if($this->role_service->getOtherRoles()['auth_export'] == false) {
            return response('Forbidden', 403);
        }

        $dailyReports = $this->externalInventoryDailyReportService->getIndexData($request->toArray());
        $dailyReports = $this->externalInventoryDailyReportService->handleExcelData($dailyReports);

        return Excel::download(new ExternalInventoryDailyReportExport($dailyReports), 'external_inventory_daily_reports.xlsx');
    }
}
