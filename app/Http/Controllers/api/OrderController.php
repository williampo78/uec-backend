<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\LookupValuesVService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $lookup_values_v_service;

    public function __construct(LookupValuesVService $lookup_values_v_service) {
        $this->lookup_values_v_service = $lookup_values_v_service;
    }

    /**
     * 取得訂單取消原因的選項
     *
     * @return json
     */
    public function getCancelReasonOptions()
    {
        $cancel_req_reasons = $this->lookup_values_v_service->getLookupValuesVs([
            'is_agent_id_disable' => true,
            'type_code' => 'CANCEL_REQ_REASON',
        ]);

        $cancel_req_reasons->transform(function ($cancel_req_reason) {
            return $cancel_req_reason->only([
                'code',
                'description',
            ]);
        });

        if (count($cancel_req_reasons) < 1) {
            return response()->json([
                'message' => '選項不存在',
            ], 404);
        }

        return response()->json([
            'message' => '取得成功',
            'results' => $cancel_req_reasons,
        ], 200);
    }
}
