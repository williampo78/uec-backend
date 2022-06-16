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

    public function __construct(
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        ExternalInventoryDailyReportService $externalInventoryDailyReportService
    ) {
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->externalInventoryDailyReportService = $externalInventoryDailyReportService;
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

        // 有權限
        if ($request->share_role_auth['auth_query']) {
            $payload = $request->only([
                'counting_date',
                'warehouse',
                'stock_type',
                'item_no_start',
                'item_no_end',
                'product_name',
                'is_dangerous',
                'supplier_id',
            ]);

            // 有搜尋條件才會進行處理
            if (!empty($payload)) {
                $dailyReports = $this->externalInventoryDailyReportService->getIndexData($payload);
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
        // 無權限
        if (!$request->share_role_auth['auth_export']) {
            return response('Forbidden', 403);
        }

        $payload = $request->only([
            'counting_date',
            'warehouse',
            'stock_type',
            'item_no_start',
            'item_no_end',
            'product_name',
            'is_dangerous',
            'supplier_id',
        ]);

        $dailyReports = $this->externalInventoryDailyReportService->getIndexData($payload);
        $dailyReports = $this->externalInventoryDailyReportService->handleExcelData($dailyReports);

        return Excel::download(new ExternalInventoryDailyReportExport($dailyReports), 'external_inventory_daily_reports.xlsx');
    }
}
