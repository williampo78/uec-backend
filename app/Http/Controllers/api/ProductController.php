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

    /*
     * 搜尋產品結果分類
     * 關鍵字，分類，價格區間
     */
    public function getProductSearchResultCategory(Request $request)
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

        $keyword = $request['keyword'];
        $category = $request['category'];
        $selling_price_min = $request['price_min'];
        $selling_price_max = $request['price_max'];
        $result = $this->apiProductService->getSearchResultForCategory($category, $selling_price_min, $selling_price_max, $keyword);
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

    /*
     * 取得產品內容頁
     * @param  int  $id
     */
    public function getProduct($id, Request $request)
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $params = $request['detail'];
        $result = $this->apiProductService->getProduct($id, $params);

        if ($result == '201') {
            $status = false;
            $err = '201';
            $list = [];
        } else {
            $status = true;
            $err = '';
            $list = json_decode($result,true);
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }

    /*
     * 取得活動贈品內容
     * @param int $id
     */
    public function getCampaignGift($id)
    {
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->getCampaignGiftByID($id);
        if ($result['status'] == '200') {
            $status = true;
            $msg = null;
        } else {
            $status = false;
            $msg = $error_code[$result['status']];
        }

        return response()->json(['status' => true, 'error_code' => $result['status'], 'error_msg' => $msg, 'result' => $result['result']]);

    }

    /*
     * 取得產品分類資料 - 麵包屑用
     */
    public function getBreadcrumbCategory(Request $request)
    {
        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'category_id.required' => '分類編號不能為空',
            'category_id.numeric' => '分類編號必須為數字'
        ];


        $v = Validator::make($request->all(), [
            'category_id' => 'required|numeric'
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $keyword = ($request['category_id'] ? $request['category_id'] : '');
        $result = $this->apiProductService->getBreadcrumbCategory($keyword);
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

}
