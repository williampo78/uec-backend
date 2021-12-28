<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UniversalService;
use Illuminate\Support\Facades\Auth;
use App\Services\APIService;
use App\Services\APIProductServices;
use App\Services\APICartServices;

use Validator;

class CheckoutController extends Controller
{

    private $universalService;
    private $apiService;
    private $apiProductServices;
    private $apiCartService;

    public function __construct(UniversalService $universalService, APIService $apiService, APIProductServices $apiProductServices, APICartServices $apiCartService)
    {
        $this->universalService = $universalService;
        $this->apiService = $apiService;
        $this->apiProductServices = $apiProductServices;
        $this->apiCartService = $apiCartService;
    }

    /*
     * 取得發票捐贈機構
     *
     */
    public function getDonatedInstitution()
    {
        $institutin = $this->universalService->getLookupValues('DONATED_INSTITUTION');
        $data = [];
        foreach ($institutin as $code => $value) {
            $data[] = array(
                "code" => $code,
                "description" => $value
            );
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $data]);

    }

    /*
     * 產生暫存訂單
     */
    public function setTmpOrder(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'delivery.required' => '物流方式不能為空',
            'shipping_fee.required' => '運費不能為空',
            'total_price.required' => '商品總價不能為空',
            'total_price.numeric' => '商品總價必須為數值',
            'discount.required' => '滿額折抵不能為空',
            'discount.numeric' => '滿額折抵必須為數值',
            'point_discount.required' => '點數折抵不能為空',
            'point_discount.numeric' => '點數折抵必須為數值',
            'checkout.required' => '結帳金額不能為空',
            'checkout.numeric' => '結帳金額必須為數值',
            'points.required' => '會員點數不能為空',
            'points.numeric' => '會員點數必須為數值',
        ];

        $v = Validator::make($request->all(), [
            'delivery' => 'required',
            'shipping_fee' => 'required',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'point_discount' => 'required|numeric',
            'checkout' => 'required|numeric',
            'points' => 'required|numeric',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }
        $member_id = Auth::guard('api')->user()->member_id;
        $campaign = $this->apiProductServices->getPromotion('product_card');
        $campaign_gift = $this->apiProductServices->getCampaignGift();
        $campaign_discount = $this->apiProductServices->getCampaignDiscount();
        $response = $this->apiCartService->getCartData($member_id, $campaign, $campaign_gift, $campaign_discount);
        $response = json_decode($response, true);
        //Step1, 前端送出的商品總價與滿額折抵與購物車計算的一樣時才做
        if ($response['result']['totalPrice'] == $request->total_price && $response['result']['discount'] == $request->discount) {
            if ($response['status'] == '200') {
                $status = true;
                $data = 'api還沒處理完成，訂單尚未成立';
            } else {
                $status = false;
                $err = '404';
                $data = [];
            }
            return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => ($response['status'] == '200' ? null : $error_code[$err]), 'result' => $data]);
        } else {
            $data['total_price'] = "商品總價有誤";
            $data['discount'] = "滿額折抵有誤";
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $data]);
        }
    }
}
