<?php

namespace App\Http\Controllers\api;

use App\Services\APIService;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\MemberGetOrdersRequest;
use App\Http\Requests\api\MemberResetPasswordRequest;
use Carbon\Carbon;

class MemberController extends Controller
{
    private $api_service;

    public function __construct(APIService $api_service)
    {
        $this->api_service = $api_service;
    }

    public function resetPassword(MemberResetPasswordRequest $request)
    {
        $token = $request->bearerToken();
        $request_payloads = $request->input();

        if (empty($token)) {
            return response()->json([
                'message' => '無效的Token',
            ], 401);
        }

        $results = $this->api_service->resetPassword($token, $request_payloads);

        // 發生錯誤
        if ($results['status_code'] != 200) {
            $payloads = [];
            $errors = [];

            if (isset($results['payloads']['error'])) {
                foreach ($results['payloads']['error'] as $key => $value) {
                    $errors[$key][] = $value;
                }
            }

            if (empty($errors)) {
                $payloads = [
                    'message' => $results['payloads']['message'],
                ];
            } else {
                $payloads = [
                    'message' => $results['payloads']['message'],
                    'errors' => $errors,
                ];
            }

            return response()->json($payloads, $results['status_code']);
        }

        return response()->noContent();
    }

    public function getOrders(MemberGetOrdersRequest $request)
    {
        $order_service = new OrderService;
        $date = $request->query('date');
        $ordered_start_date = Carbon::parse($date)->subDays(90);
        $ordered_end_date = Carbon::parse($date);

        $orders = $order_service->getOrders([
            'revision_no' => 0,
            'ordered_start_date' => $ordered_start_date,
            'ordered_end_date' => $ordered_end_date,
        ]);
        // dump($date);

        return 'end';
    }
}
