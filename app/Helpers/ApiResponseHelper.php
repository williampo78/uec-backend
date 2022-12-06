<?php

namespace App\Helpers;

use App\Enums\ApiResponseErrorsEnum;
use Illuminate\Http\JsonResponse;

class ApiResponseHelper
{
    /**
     * 資料正確時回傳資料
     *
     * @param $status
     * @param $errCode
     * @param $errMsg
     * @param $result
     *
     * @return JsonResponse
     */
    static public function message($status, $errCode, $errMsg, $result): JsonResponse
    {
        return response()->json([
                                    'status'     => $status,
                                    'error_code' => (string)($errCode),
                                    'error_msg'  => $errMsg,
                                    'result'     => $result
                                ], 200);
    }

    /**
     * api 傳入 Reuqest 資料驗證錯時回傳格式資訊
     *
     * @param $validator
     * @param null $result
     *
     * @return JsonResponse
     */
    static public function failedValidation($validator, $result=null): JsonResponse
    {
        $errors = $validator->errors();
        $code = $keyName = key(reset($errors));
        $message = $errors->first();

        return response()->json([
                                    'status'     => false,
                                    'error_code' => (string)(ApiResponseErrorsEnum::CHECKOUT_CODE[$code] ?? 401),
                                    'error_msg'  => $keyName . " ". $message,
                                    'result'     => $result
                                ], 400);
    }
}
