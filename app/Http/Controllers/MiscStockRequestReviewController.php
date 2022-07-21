<?php

namespace App\Http\Controllers;

use App\Services\MiscStockRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MiscStockRequestReviewController extends Controller
{
    private $miscStockRequestService;

    public function __construct(
        MiscStockRequestService $miscStockRequestService
    ) {
        $this->miscStockRequestService = $miscStockRequestService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestPayload = $request->only([
            'request_no',
            'submitted_at_start',
            'submitted_at_end',
        ]);

        $responsePayload = [
            'auth' => $request->share_role_auth,
        ];

        // 有編輯權限
        if ($request->share_role_auth['auth_update']) {
            // 進貨退出單
            $responsePayload['misc_stock_requests'] = $this->miscStockRequestService->getStockReviewTableList($requestPayload);
            $responsePayload['misc_stock_requests'] = $this->miscStockRequestService->formatStockReviewTableList($responsePayload['misc_stock_requests']);
        }

        return view('backend.misc_stock_request_review.list', [
            'payload' => $responsePayload,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->getStockRequestForReviewPage($id);
        $responsePayload['misc_stock_request'] = $this->miscStockRequestService->formatStockRequestForReviewPage($responsePayload['misc_stock_request']);

        return response()->json([
            'payload' => $responsePayload,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $requestPayload = $request->only([
            'supplier_ids',
            'review_result',
            'review_remark',
        ]);

        $reviewResult = $this->miscStockRequestService->reviewStockRequest($id, $requestPayload);

        if (!$reviewResult['is_success']) {
            return response()->json([
                'message' => '儲存失敗',
            ], 500);
        }

        return response()->json([
            'message' => '儲存成功',
            'payload' => [
                'remaining_supplier_count' => $reviewResult['remaining_supplier_count'],
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 取得審核modal的明細
     *
     * @param integer $requestId
     * @param integer $supplierId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReviewModalDetail(int $requestId, int $supplierId): JsonResponse
    {
        $detail = $this->miscStockRequestService->getReviewModalDetail($requestId, $supplierId);
        $detail = $this->miscStockRequestService->formatReviewModalDetail($detail);
        $responsePayload = [
            'detail' => $detail,
        ];

        return response()->json([
            'payload' => $responsePayload,
        ]);
    }
}
