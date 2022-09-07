<?php

namespace App\Http\Controllers;

use App\Exports\OrderRefundExport;
use App\Models\LookupValuesV;
use App\Services\OrderRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OrderRefundController extends Controller
{
    private $orderRefundService;

    public function __construct(
        OrderRefundService $OrderRefundService
    ) {
        $this->orderRefundService = $OrderRefundService;
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

        // 有權限
        if ($request->share_role_auth['auth_query']) {
            $payload = $request->only([
                'order_refund_date_start',
                'order_refund_date_end',
                'request_no',
                'member_account',
                'status_code',
                'order_no',
                'member_name',
                'ship_from_whs',
                'to_do_item'
            ]);

            // 有搜尋條件才會進行處理
            if (!empty($payload)) {
                $orderRefunds = $this->orderRefundService->getOrderRefunds($payload);
                $orderRefunds = $this->orderRefundService->handleOrderRefunds($orderRefunds);
            }
        }

        $params = [];
        $params['orderRefunds'] = $orderRefunds;

        //訂單類型
        $params['shipFromWhs'] = [
            [
                'id'   => 'SELF',
                'text' => '商城出貨'
            ],
            [
                'id'   => 'SUP',
                'text' => '供應商出貨'
            ]
        ];

        //待辦事項
        $params['toDoItems'] = [
            [
                'id'   => 'check_exception',
                'text' => '檢驗異常，待協商'
            ],
            [
                'id'   => 'refund_failed',
                'text' => '退款失敗'
            ]
        ];

        return view('backend.order_refund.list', $params);
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
        $id             = $request->id;
        //檢驗單資料
        $returnExaminations = $this->orderRefundService->getReturnExaminationWithDetails($id);
        //設定的資料
        $lookupValuesVs = LookupValuesV::where('type_code', 'SUP_LGST_COMPANY')
            ->where('agent_id', Auth::user()->agent_id)
            ->get(['description', 'code']);
        //整理檢驗單資料
        $returnExaminations = $this->orderRefundService->handleReturnExaminations($returnExaminations, $lookupValuesVs);
        //退貨申請單資料
        $ReturnRequest = $this->orderRefundService->getReturnRequest($id);

        return response()->json([
            'status' => true,
            'data' => [
                'number_or_logistics_name_column_name' => $ReturnRequest->ship_from_whs == 'SELF' ? '取件單號' : '物流單號',
                //退貨資料
                'return_request' => $ReturnRequest,
                //退貨明細
                'return_details' => $returnExaminations,
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
        // 無權限
        if (!$request->share_role_auth['auth_export']) {
            return response('Forbidden', 403);
        }

        $payload = $request->only([
            'order_refund_date_start',
            'order_refund_date_end',
            'request_no',
            'member_account',
            'status_code',
            'order_no',
            'member_name',
        ]);

        $orderRefunds = $this->orderRefundService->getExcelData($payload);
        $orderRefunds = $this->orderRefundService->handleExcelData($orderRefunds);

        return Excel::download(new OrderRefundExport($orderRefunds), 'orderRefunds.xlsx');
    }
}
