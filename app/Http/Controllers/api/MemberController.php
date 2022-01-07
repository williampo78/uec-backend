<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\MemberResetPasswordRequest;
use App\Services\APIService;

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
}
