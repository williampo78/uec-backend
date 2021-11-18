<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIProductServices;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    private $apiProductService;

    public function __construct(APIProductServices $apiProductService)
    {
        $this->apiProductService = $apiProductService;
    }

    /*
     * 取得產品分類資料
     */
    public function getCategory()
    {
       $result = $this->apiProductService->getCategory();
       return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $result]);
    }
}
