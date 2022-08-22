<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIProductServices;
use Illuminate\Http\Request;
use App\Services\APIService;
use Validator;
use App\Services\ProductAttributeLovService;
use App\Services\UniversalService;

class ProductController extends Controller
{
    //
    private $apiProductService;

    public function __construct(APIProductServices $apiProductService, APIService $apiService, ProductAttributeLovService $apiProductAttributeLovService, UniversalService $universalService)
    {
        $this->apiProductService = $apiProductService;
        $this->apiService = $apiService;
        $this->apiProductAttributeLovService = $apiProductAttributeLovService;
        $this->universalService = $universalService;
    }

    /*
     * 取得產品分類資料
     */
    public function getCategory(Request $request)
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $keyword = ($request['keyword'] ? $this->universalService->handleAddslashes($request['keyword']) : '');
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
            'category' => '分類必須是數字'
        ];

        $in = '';
        if ($request['price_min'] >= 0 && $request['price_max'] >= 0) {//價格區間
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
            'category' => 'numeric|nullable',
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
            'category' => '分類必須是數字'
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
            'category' => 'numeric|nullable',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $keyword = ($request['keyword'] ? $this->universalService->handleAddslashes($request['keyword']) : '');
        $category = (int) $request['category'];
        $selling_price_min = $request['price_min'];
        $selling_price_max = $request['price_max'];
        $attribute = '';
        $attribute .= ($request['group'] ? $request['group'] : '');
        $attribute .= ($attribute != '' && $request['ingredient'] != '' ? ', ' : '') . ($request['ingredient'] ? $request['ingredient'] : '');
        $attribute .= ($attribute != '' && $request['dosage_form'] != '' ? ', ' : '') . ($request['dosage_form'] ? $request['dosage_form'] : '');
        $attribute .= ($attribute != '' && $request['certificate'] != '' ? ', ' : '') . ($request['certificate'] ? $request['certificate'] : '');
        $brand = '';
        $brand .= ($request['brand'] ? $request['brand'] : '');
        $result = $this->apiProductService->getSearchResultForCategory($category, $selling_price_min, $selling_price_max, $keyword, $attribute, $brand);
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
        $params = ($request['detail'] ? $this->universalService->handleAddslashes($request['detail']) : '');
        $result = $this->apiProductService->getProduct($id, $params);

        if ($result == '903') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "商品不存在", 'result' => $list]);
        } else if ($result == '901') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "此商品沒有前台分類", 'result' => $list]);
        } else if ($result == '902') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "此商品已被下架", 'result' => $list]);
        } else {
            $status = true;
            $err = '';
            $list = json_decode($result, true);
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

    /*
     * 取得活動賣場頁
     * @param int $id
     */
    public function getEvent(Request $request)
    {

        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'event.numeric' => '活動代碼必須是數字',
            'event.required' => '活動代碼不能為空',
            "size.numeric"=>'每頁筆數必須是數字',
            "size.required"=>'每頁筆數不能為空',
            "page.numeric"=>'當前頁數必須是數字',
            "page.required"=>'當前頁數不能為空',
        ];

        $v = Validator::make($request->all(), [
            'event' => 'numeric|required',
            'size' => 'numeric|required',
            'page' => 'numeric|required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $result = $this->apiProductService->getEventStore($request);
        if ($result['status'] == '200') {
            $status = true;
            $msg = null;
        } else {
            $status = false;
            $msg = $error_code[$result['status']];
        }

        return response()->json(['status' => $status, 'error_code' => $result['status'], 'error_msg' => $msg, 'result' => $result['result']]);

    }


    /*
     * 取得滿額活動折扣內容
     * @param int $id
     */
    public function getCampaignDiscount($id)
    {
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->getCampaignDiscountByID($id);
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
     * 取得進階搜尋篩選器 (產品搜尋結果頁用)
     */
    public function getFilter(Request $request)
    {
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'price_min.numeric' => '最低價必須是數字',
            'price_max.numeric' => '最高價必須是數字',
            'category' => '分類必須是數字'
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
            'category' => 'numeric|nullable',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }
        $result = $this->apiProductService->getProductFilter($request);

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
     * 取得產品規格
     * @param  int  $id
     */
    public function getProductItem($id)
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->getProductItem($id);

        if ($result == '903') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "商品不存在", 'result' => $list]);
        } else if ($result == '901') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "此商品沒有前台分類", 'result' => $list]);
        } else if ($result == '902') {
            $status = false;
            $err = '901';
            $list = [];
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => "此商品已被下架", 'result' => $list]);
        } else {
            $status = true;
            $err = '';
            $list = json_decode($result, true);
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }

    /*
     * 取得活動贈品內容(含門檻)
     * @param int $id
     */
    public function getCampaignThresholdGift($id)
    {
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->getCampaignGiftByIDWithThreshold($id);
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
     * 前台最近瀏覽商品
     * 回傳有效上架商品
     */
    public function getEffectProduct(Request $request)
    {

        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'product.required' => '產品編號不能為空'
        ];


        $v = Validator::make($request->all(), [
            'product' => 'required'
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $keyword = ($request['product'] ? $request['product'] : '');
        $result = $this->apiProductService->getEffectProduct($keyword);
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
