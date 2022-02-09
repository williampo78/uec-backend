<?php

namespace App\Http\Controllers;

use App\Exports\OrderRefundExport;
use App\Services\OrderRefundService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OrderRefundController extends Controller
{
    private $orderRefundService;
    private $role_service;

    public function __construct(
        OrderRefundService $OrderRefundService,
        RoleService $role_service
    ) {
        $this->orderRefundService = $OrderRefundService;
        $this->role_service = $role_service;
    }

    /**
     * @param Request $request
     * @return View
     * @Author: Eric
     * @DateTime: 2022/1/14 上午 11:18
     */
    public function index(Request $request): view
    {
        $orderRefunds = collect();

        //有權限
        if ($this->role_service->getOtherRoles()['auth_query']) {
            //有搜尋條件才會進行處理
            if (empty($request->toArray()) === false) {
                $orderRefunds = $this->orderRefundService->getOrderRefunds($request->toArray());
                $orderRefunds = $this->orderRefundService->handleOrderRefunds($orderRefunds);
            }
        }

        $params = [];
        $params['orderRefunds'] = $orderRefunds;

        return view('Backend.OrderRefund.list', $params);
    }

    /**
     * 退貨詳細資料
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author: Eric
     * @DateTime: 2022/1/17 下午 01:42
     */
    public function getDetail(Request $request)
    {
        if (empty($request->id)) {
            return response()->json([
                'status' => false,
                'message' => '發生錯誤，缺少參數',
            ]);
        }

        $id = $request->id;

        return response()->json([
            'status' => true,
            'data' => [
                //退貨資料
                'return_request' => $this->orderRefundService->getReturnRequest($id),
                //退貨明細
                'return_details' => $this->orderRefundService->getReturnDetails($id),
                //退款資訊
                'return_information' => $this->orderRefundService->getReturnInformation($id),
            ],
            'message' => '',
        ]);
    }

    /**
     * 匯出excel
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Author: Eric
     * @DateTime: 2022/1/14 上午 11:18
     */
    public function exportExcel(Request $request)
    {
        //無權限
        if ($this->role_service->getOtherRoles()['auth_export'] == false) {
            return response('Forbidden', 403);
        }

        $orderRefunds = $this->orderRefundService->getExcelData($request->toArray());
        $orderRefunds = $this->orderRefundService->handleExcelData($orderRefunds);

        return Excel::download(new OrderRefundExport($orderRefunds), 'orderRefunds.xlsx');
    }
}
