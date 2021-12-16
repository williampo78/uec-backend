<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APICartServices;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ShoppingController extends Controller
{

    private $apiCartService;

    public function __construct(APICartServices $apiCartService, APIService $apiService)
    {
        $this->apiCartService = $apiCartService;
        $this->apiService = $apiService;
    }

    /*
     * 購物車 icon 上方的購物車數量 badge
     */
    public function getCartCount()
    {
        $error_code = $this->apiService->getErrorCode();
        $login = Auth::guard('api')->check();
        if ($login) {
            $member_id = Auth::guard('api')->user()->member_id;
            if ($member_id > 0) {
                $cartCount = $this->apiCartService->getCartCount($member_id);
            }
            $result = '200';
        } else {
            $result = '404';
        }
        if ($result == '404') {
            $status = false;
            $err = '404';
            $list = array("member" => false, "count" => 0);
        } else {
            $status = true;
            $err = null;
            $list = array("member" => true, "count" => $cartCount);
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }


    /*
     * 設定購物車資料 (商品編號)
     * @param  int  $id
     */
    public function setCart(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'item_id.required' => '商品編號不能為空',
            'item_no.required' => '商品代碼不能為空',
            'item_qty.required' => '商品數量不能為空',
            'status_code.required' => '商品數量不能為空',
        ];

        $v = Validator::make($request->all(), [
            'item_id' => 'required',
            'item_no' => 'required',
            'item_qty' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $response = $this->apiCartService->setMemberCart($request);
        if ($response == 'success') {
            $status = true;
            $data = ($request['status'] == 0 ? '加入' : '移除') . '購物車成功';
        } elseif ($response == '203') {
            $status = false;
            $err = $response;
            $data = '';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }

    /*
     * 購物車清單
     */
    public function getShoppingCartData()
    {
        $error_code = $this->apiService->getErrorCode();
        $member_id = Auth::guard('api')->user()->member_id;
        $response = $this->apiCartService->getCartData($member_id);
        //$response = json_encode($response, true);
        if ($response == 'success') {
            $status = true;
            $data = '';
        } elseif ($response == '203') {
            $status = false;
            $err = $response;
            $data = '';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $response]);
    }
}
