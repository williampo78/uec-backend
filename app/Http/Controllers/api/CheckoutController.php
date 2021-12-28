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
     * step1 立即結帳時，先檢查金額
     */
    public function setTmpOrder(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'shipping_fee.required' => '運費不能為空',
            'total_price.required' => '商品總價不能為空',
            'total_price.numeric' => '商品總價必須為數值',
            'discount.required' => '滿額折抵不能為空',
            'discount.numeric' => '滿額折抵必須為數值',
        ];

        $v = Validator::make($request->all(), [
            'shipping_fee' => 'required',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
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
        //Step1, 檢核金額
        if ($response['result']['totalPrice'] == $request->total_price && $response['result']['discount'] == $request->discount && $response['result']['shippingFee'] == $request->shipping_fee) {
            $status = true;
            $data = true;
            $err = '200';
        } else {
            $status = false;
            $err = '401';
            if ($response['result']['totalPrice'] != $request->total_price) {
                $data['total_price'] = "商品總價有誤";
            }
            if ($response['result']['discount'] != $request->discount) {
                $data['discount'] = "滿額折抵有誤";
            }
            if ($response['result']['shippingFee'] != $request->shipping_fee) {
                $data['shipping_fee'] = "運費有誤";
            }
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => ($err == '200' ? null : $error_code[$err]), 'result' => $data]);
    }
}
