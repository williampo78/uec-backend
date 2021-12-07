<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIProductServices;
use Illuminate\Http\Request;
use App\Services\APIService;
use Validator;

class ProductController extends Controller
{
    //
    private $apiProductService;

    public function __construct(APIProductServices $apiProductService, APIService $apiService)
    {
        $this->apiProductService = $apiProductService;
        $this->apiService = $apiService;
    }

    /*
     * 取得產品分類資料
     */
    public function getCategory(Request $request)
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $keyword = ($request['keyword'] ? $request['keyword'] : '');
        $result = $this->apiProductService->getCategory($keyword);
        if ($result == '404') {
            $status = false;
            $err = '404';
            $list = [];
        } else {
            $status = true;
            $err = '';
            $list = $result;
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }

    /*
     * 搜尋產品
     * 關鍵字，分類，價格區間
     */
    public function getProductSearchResult(Request $request)
    {
        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'price_min.numeric' => '最低價必須是數字',
            'price_max.numeric' => '最高價必須是數字',
        ];

        $in = '';
        if ($request['price_min'] > 0 && $request['price_max'] > 0) {//價格區間
            if ($request['price_max'] < $request['price_min']) {
                $messages = [
                    'price_max.in' => '最高價不得低於最低價',
                ];
                $in = '|in';
            }
        }

        $v = Validator::make($request->all(), [
            'price_min' => 'numeric',
            'price_max' => 'numeric' . $in,
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }
        $result = $this->apiProductService->searchResult($request);
        if ($result == '404') {
            $status = false;
            $err = '404';
            $list = [];
        } else {
            $status = true;
            $err = null;
            $list = $result;
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }

}
