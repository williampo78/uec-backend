<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Services\APIService;
class DradviceController extends Controller
{

    private $apiService;
    private $apiProductService;

    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * 縣市鄉鎮下拉選單
     */
    public function area($all = null)
    {
        $status = false;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->getArea($all);
        if ($response) {
            $status= true;
            $response = json_decode($response, true);
        } else {
            $response = [];
            $status = false;
            $err = '201';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $response]);
    }

    /*
     * 會員登入
     * @param : 帳號, 密碼
     * @author Rowena
     */
    public function memberLogin(Request $request)
    {
        $status = false;
        $err = null;
        $input['mobile'] = $request->mobile;
        $input['password'] = $request->password;
        $input['channel'] = "EC";
        $fields = json_encode($input);
        $response = $this->apiService->memberLogin($fields);
        $result = json_decode($response, true);
        $error_code = $this->apiService->getErrorCode();
        if ($result['status']=='200') {
            $status= true;
        } else {
            $status = false;
            $err = $result['status'];
            $result['data'] = [];
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $result['data']]);
    }

    /**
     * 門市代碼清單
     */
    public function store()
    {
        $status = false;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->getStores();
        if ($response) {
            $status= true;
            $err = '';
            $response = json_decode($response, true);
        } else {
            $response = [];
            $status = false;
            $err = '201';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $response]);
    }

}
