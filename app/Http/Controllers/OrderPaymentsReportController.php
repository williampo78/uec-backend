<?php

namespace App\Http\Controllers;

use App\Exports\OrderPaymentsReportExport;
use App\Services\OrderPaymentsReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\RoleService;

class OrderPaymentsReportController extends Controller
{
    private $orderPaymentsReportService;
    private $role_service;

    public function __construct(
        OrderPaymentsReportService $OrderPaymentsReportService,
        RoleService $role_service
    )
    {
        $this->orderPaymentsReportService = $OrderPaymentsReportService;
        $this->role_service = $role_service;
    }

    public function index(Request $request): view
    {
        $orderPaymentsReports = collect();
        //有權限
        if($this->role_service->getOtherRoles()['auth_query']){
            //有搜尋條件才會進行處理
            if (empty($request->toArray()) === false) {
                $orderPaymentsReports = $this->orderPaymentsReportService->getOrderPaymentsReports($request->toArray());
                $orderPaymentsReports = $this->orderPaymentsReportService->handleOrderPaymentsReports($orderPaymentsReports);
            }
        }

        $params = [];
        $params['orderPaymentsReports'] = $orderPaymentsReports;

        return view('Backend.OrderPaymentsReport.list', $params);
    }

    /**
     * 匯出excel
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Author: Eric
     * @DateTime: 2022/1/18 下午 02:02
     */
    public function exportExcel(Request $request)
    {
        //無權限
        if($this->role_service->getOtherRoles()['auth_export'] == false) {
            return response('Forbidden', 403);
        }

        $orderPaymentsReports = collect();
        $request = $request->toArray();

        $request_is_empty = empty($request['date_start']) && empty($request['date_end']) && empty($request['payment_method']) && empty($request['payment_status']);

        //有搜尋條件才會進行處理
        if ($request_is_empty === false) {
            $orderPaymentsReports = $this->orderPaymentsReportService->getOrderPaymentsReports($request);
            $orderPaymentsReports = $this->orderPaymentsReportService->handleExcelData($orderPaymentsReports);
        }

        return Excel::download(new OrderPaymentsReportExport($orderPaymentsReports), 'order_payments_reports.xlsx');
    }
}
