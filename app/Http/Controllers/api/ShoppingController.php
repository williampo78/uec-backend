<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APICartServices;
use App\Services\APIService;
use App\Services\APIProductServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ShoppingController extends Controller
{

    private $apiCartService;

    public function __construct(APICartServices $apiCartService, APIService $apiService, APIProductServices $apiProductServices)
    {
        $this->apiCartService = $apiCartService;
        $this->apiService = $apiService;
        $this->apiProductServices = $apiProductServices;
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
            'item_qty.required' => '商品數量不能為空',
            'status_code.required' => '商品數量不能為空',
        ];

        $v = Validator::make($request->all(), [
            'item_id' => 'required',
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
    public function getShoppingCartData(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $member_id = Auth::guard('api')->user()->member_id;
        $campaign = $this->apiProductServices->getPromotion('product_card');
        $campaign_gift = $this->apiProductServices->getCampaignGift();
        $campaign_discount = $this->apiProductServices->getCampaignDiscount();
        $products = $this->apiProductServices->getProducts();
        $gtm = $this->apiProductServices->getProductItemForGTM($products, 'item');
        $stock_type = ($request->stock_type == "supplier" ? "supplier" : "dradvice");
        $response = $this->apiCartService->getCartData($member_id, $campaign, $campaign_gift, $campaign_discount, $gtm, $stock_type);
        $response = json_decode($response, true);
        $response['result']['paymentMethod'] = array_values($response['result']['paymentMethod']);
        if ($response['status'] == '200') {
            $status = true;
            $data = $response['result'];
        } else {
            $status = false;
            $err = '404';
            $data = $response['result'];
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => ($response['status'] == '200' ? null : $error_code[$err]), 'result' => $data]);
    }


    /*
     * 批次設定購物車資料 (商品編號)
     * @param  int  $id
     */
    public function setBatchCart(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'item_id.required' => '商品編號不能為空',
            'item_qty.required' => '商品數量不能為空',
            'status_code.required' => '商品數量不能為空',
        ];

        $v = Validator::make($request->all(), [
            'item_id' => 'required',
            'item_qty' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        if (count($request->item_id) != count($request->item_qty)) {
            $data = "商品編號與商品數量不符合";
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $data]);
        }
        $response = $this->apiCartService->setBatchCart($request);
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
     * 更新購物車商品數量 (商品編號)
     * @param  int  $id
     */
    public function addGoodsQty(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'item_id.required' => '商品編號不能為空',
            'item_qty.required' => '商品數量不能為空',
            'status_code.required' => '商品數量不能為空',
        ];

        $v = Validator::make($request->all(), [
            'item_id' => 'required',
            'item_qty' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $response = $this->apiCartService->setGoodsQty($request);
        if ($response == 'success') {
            $cartCount = 0;
            $status = true;
            $data['message'] = ($request['status'] == 0 ? '加入' : '移除') . '購物車成功';
            $login = Auth::guard('api')->check();
            if ($login) {
                $member_id = Auth::guard('api')->user()->member_id;
                if ($member_id > 0) {
                    $cartCount = $this->apiCartService->getCartCount($member_id);
                }
            }
            $data['cartCount'] = $cartCount;
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
     * 查詢會員購物車內容
     * @param
     */
    public function displayCartData()
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiCartService->getCartInfo($member_id);
        $results = $response;
        unset($results['items']);
        foreach ($results as $id => $result) {
            $data[] = $id;
        }
        if (count($data) > 0) {
            $status = true;
            $list = $data;
        } else {
            $status = false;
            $err = '404';
            $list = [];
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);

    }
}
