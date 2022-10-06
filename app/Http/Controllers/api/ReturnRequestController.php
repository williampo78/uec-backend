<?php

namespace App\Http\Controllers\api;

use App\Services\ReturnGoodsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    private $returnGoodsService;

    public function __construct(ReturnGoodsService $returnGoodsService)
    {
        $this->returnGoodsService = $returnGoodsService;
    }

    /**
     * 退款
     * @param Request $request
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/9/28 下午 02:35
     */
    public function refund(Request $request): JsonResponse
    {
        $payload = $request->only(
            [
                'return_request_id',
                'type'
            ]
        );

        $result = $this->returnGoodsService
            ->setParameters($payload)
            ->handle();

        return response()->json($result, $result['http_status_code'], [], JSON_UNESCAPED_UNICODE);
    }
}
