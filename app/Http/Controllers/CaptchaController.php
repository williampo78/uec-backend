<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;
use Mews\Captcha\Captcha;

class CaptchaController extends Controller
{

    /**
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/7/18 上午 09:38
     */
    public function refreshCaptcha():JsonResponse
    {
        return response()->json([
            'status' => true,
            'status_code' => '200',
            'message' => 'created succeed',
            'url' => app('captcha')->create('flat', true)
        ]);
    }

}
