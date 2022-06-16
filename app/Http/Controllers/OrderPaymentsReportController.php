<?php

namespace App\Http\Controllers;

use App\Exports\OrderPaymentsReportExport;
use App\Services\OrderPaymentsReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OrderPaymentsReportController extends Controller
{
    private $orderPaymentsReportService;

    public function __construct(
        OrderPaymentsReportService $OrderPaymentsReportService
    ) {
        $this->orderPaymentsReportService = $OrderPaymentsReportService;
    }

    public function index(Request $request): view
    {
        $orderPaymentsReports = collect();
        //有權限
        if ($request->share_role_auth['auth_query']) {
            $payload = $request->only([
                'date_start',
                'date_end',
                'payment_method',
                'payment_status',
            ]);

            // 有搜尋條件才會進行處理
            if (!empty($payload)) {
                $orderPaymentsReports = $this->orderPaymentsReportService->getOrderPaymentsReports($payload);
                $orderPaymentsReports = $this->orderPaymentsReportService->handleOrderPaymentsReports($orderPaymentsReports);
            }
        }

        $params = [];
        $params['orderPaymentsReports'] = $orderPaymentsReports;

        return view('backend.order_payments_report.list', $params);
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
        // 無權限
        if (!$request->share_role_auth['auth_export']) {
            return response('Forbidden', 403);
        }

        $payload = $request->only([
            'date_start',
            'date_end',
            'payment_method',
            'payment_status',
        ]);

        $orderPaymentsReports = $this->orderPaymentsReportService->getOrderPaymentsReports($payload);
        $orderPaymentsReports = $this->orderPaymentsReportService->handleExcelData($orderPaymentsReports);

        return Excel::download(new OrderPaymentsReportExport($orderPaymentsReports), 'order_payments_reports.xlsx');
    }
}
