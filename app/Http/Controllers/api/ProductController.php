<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIProductServices;
use Illuminate\Http\Request;
use App\Services\APIService;

class ProductController extends Controller
{
    //
    private $apiProductService;

    public function __construct(APIProductServices $apiProductService,APIService $apiService)
    {
        $this->apiProductService = $apiProductService;
        $this->apiService = $apiService;
    }

    /*
     * 取得產品分類資料
     */
    public function getCategory()
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->getCategory();
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
     * 依分類搜尋產品
     */
    public function getProductByCategory(Request $request, $id)
    {
        $err = false;
        $error_code = $this->apiService->getErrorCode();
        $result = $this->apiProductService->categorySearchResult($id, $request['size'], $request['page']);
        if ($result == '404') {
            $status = false;
            $err = '404';
            $list = [];
        } else {
            $status = true;
            $err = '';
            $list = $result;
        }
        return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }
}
