<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Mews\Captcha\Captcha;

class CaptchaController extends Controller
{
    //

    public function getCapcha()
    {
        return response()->json([
            'status_code' => '200',
            'message' => 'created succeed',
            'url' => app('captcha')->create('default', true)
        ]);
    }

}
