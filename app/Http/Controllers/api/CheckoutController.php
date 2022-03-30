<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APICartServices;
use App\Services\APIOrdersServices;
use App\Services\APIProductServices;
use App\Services\APIService;
use App\Services\APITapPayService;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CheckoutController extends Controller
{

    private $universalService;
    private $apiService;
    private $apiProductServices;
    private $apiCartService;
    private $apiOrdersService;
    private $apiTapPay;

    public function __construct(UniversalService $universalService, APIService $apiService, APIProductServices $apiProductServices, APICartServices $apiCartService, APIOrdersServices $apiOrdersService, APITapPayService $apiTapPay)
    {
        $this->universalService = $universalService;
        $this->apiService = $apiService;
        $this->apiProductServices = $apiProductServices;
        $this->apiCartService = $apiCartService;
        $this->apiOrdersService = $apiOrdersService;
        $this->apiTapPay = $apiTapPay;
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
                "description" => $value,
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

        if ($request->points) { //結帳時使用的會員點數
            $points = $request->points;
        } else {
            $points = 0;
        }
        //Step1, 檢核金額
        if (isset($response['result']['totalPrice'])) {
            //
            if ($points > $response['result']['point']['discountMax']) { //超過最大可點數折抵
                $status = false;
                $err = '401';
                $data['points'] = "點數折抵超出本次可抵用點數";
            } elseif ($response['result']['totalPrice'] == $request->total_price && $response['result']['discount'] == $request->discount) {
                //檢核使用點數折抵後，運費是否要運費
                if ($points > 0) {
                    $point_discount = ($points * $response['result']['point']['exchangeRate']);
                } else {
                    $point_discount = 0;
                }
                if (($response['result']['totalPrice'] - $response['result']['discount'] - $point_discount) < $response['result']['feeInfo']['free_threshold']) {
                    $shipping_fee = $response['result']['feeInfo']['shipping_fee'];
                } else {
                    $shipping_fee = 0;
                }
                if ($shipping_fee == $request->shipping_fee) {
                    $status = true;
                    $data = true;
                    $err = '200';
                } else {
                    $status = false;
                    $err = '401';
                    $data['shipping_fee'] = "運費有誤";
                }
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
        } else {
            $status = false;
            $err = '401';
            $data['total_price'] = "商品總價有誤";
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => ($err == '200' ? null : $error_code[$err]), 'result' => $data]);
    }

    /*
     * step2 確認結帳，建立訂單與金流
     */
    public function setOrder(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'payment_method.required' => '付款方式不能為空',
            'taypay_prime.required' => '未取得TapPay Prime',
            'invoice.usage.required' => '請選擇發票開立方式',
            'buyer.*.required' => '訂購人資訊未填寫完整',
            'buyer.email.email' => 'Email格式錯誤',
            'receiver.*.required' => '收件人資訊未填寫完整',
            'receiver.email.email' => 'Email格式錯誤',
            'total_price.required' => '商品總價不能為空',
            'total_price.numeric' => '商品總價必須為數值',
            'discount.required' => '滿額折抵不能為空',
            'discount.numeric' => '滿額折抵必須為數值',
            'point_discount.required' => '點數折抵不能為空',
            'point_discount.numeric' => '點數折抵必須為數值',
            'shipping_fee.required' => '運費不能為空',
            'shipping_fee.numeric' => '運費必須為數值',
            'payment.required' => '結帳金額不能為空',
            'payment.numeric' => '結帳金額必須為數值',
            'points.required' => '會員使用的點數不能為空',
            'points.numeric' => '會員使用的點數必須為數值',
        ];

        $v = Validator::make($request->all(), [
            'payment_method' => 'required',
            'taypay_prime' => 'required',
            'invoice.usage' => 'required',
            'buyer.*' => 'required',
            'buyer.email' => 'email',
            'receiver.*' => 'required',
            'receiver.email' => 'email',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'point_discount' => 'required|numeric',
            'shipping_fee' => 'required|numeric',
            'payment' => 'required|numeric',
            'points' => 'required|numeric',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        if ($request->invoice['usage'] == 'P') { //二聯式，個人電子發票
            if ($request->invoice['carrier_type'] == 3) {
                $errInvoice = [
                    'carrier_no.required' => '手機條碼載具必填',
                    'carrier_no.min' => '手機條碼載具最少8碼',
                    'carrier_no.max' => '手機條碼載具最多8碼',
                ];
                $v_invocie = Validator::make($request->invoice, [
                    'carrier_no' => 'required|min:8|max:8',
                ], $errInvoice);
            } else {
                $errInvoice = [];
                $v_invocie = Validator::make($request->invoice, [], $errInvoice);

            }
        } elseif ($request->invoice['usage'] == 'C') { //三聯式，公司戶電子發票
            $errInvoice = [
                'buyer_gui_number.required' => '統一編號必填',
                'buyer_title.required' => '發票抬頭必填',
            ];
            $v_invocie = Validator::make($request->invoice, [
                'buyer_gui_number' => 'required',
                'buyer_title' => 'required',
            ], $errInvoice);

        } elseif ($request->invoice['usage'] == 'D') { //發票捐贈
            $errInvoice = [
                'donated_code.required' => '機構捐贈碼必填',
            ];
            $v_invocie = Validator::make($request->invoice, [
                'donated_code' => 'required',
            ], $errInvoice);

        }

        if ($v_invocie) {
            if ($v_invocie->fails()) {
                return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v_invocie->errors()]);
            }
        }

        $member_id = Auth::guard('api')->user()->member_id;
        $campaign = $this->apiProductServices->getPromotion('product_card');
        $campaign_gift = $this->apiProductServices->getCampaignGift();
        $campaign_discount = $this->apiProductServices->getCampaignDiscount();
        $response = $this->apiCartService->getCartData($member_id, $campaign, $campaign_gift, $campaign_discount);
        $response = json_decode($response, true);

        /* test
        $data = $this->apiOrdersService->setOrders($response['result'], $request, $campaign, $campaign_gift);
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $data['payment_url']]);
         */
        if ($response['status'] == '404') {
            $data = [];
            $status = false;
            $err = $response['status'];
        } else {
            //Step1, 檢核金額
            $data = [];
            if ($request->points) { //使用會員點數折抵金額
                $points = $request->points;
            } else {
                $points = 0;
            }
            if (abs($points) > $response['result']['point']['discountMax']) { //超過最大可點數折抵
                $status = false;
                $err = '401';
                $data['points'] = "點數折抵超出本次可抵用點數";
            } elseif ($response['result']['totalPrice'] == $request->total_price && (-$response['result']['discount']) == $request->discount) {
                //檢核使用點數折抵後，運費是否要運費
                if (abs($points) > 0) {
                    $points_discount = ($points * $response['result']['point']['exchangeRate']);
                } else {
                    $points_discount = 0;
                }
                if (($response['result']['totalPrice'] - $response['result']['discount'] - abs($points_discount)) < $response['result']['feeInfo']['free_threshold']) {
                    $shipping_fee = $response['result']['feeInfo']['shipping_fee'];
                } else {
                    $shipping_fee = 0;
                }
                if ($shipping_fee == $request->shipping_fee) {
                    //Stet2, 產生訂單
                    $dataOrder = $this->apiOrdersService->setOrders($response['result'], $request, $campaign, $campaign_gift, $campaign_discount);
                    switch ($dataOrder['status']) {
                        case 200:
                            $status = true;
                            $err = '200';
                            $data = $dataOrder['payment_url'];
                            break;
                        case 401:
                            $status = false;
                            $err = '401';
                            $data['message'] = "會員點數扣點異常，無法成立訂單";
                            break;
                        case 402:
                            $status = false;
                            $err = '401';
                            $data['message'] = "第三方支付異常，無法成立訂單";
                            break;
                        default:
                            $status = false;
                            $err = '401';
                            $data['message'] = "產生訂單時發生異常，無法成立訂單";
                            break;
                    }
                } else {
                    $status = false;
                    $err = '401';
                    $data['shipping_fee'] = "運費有誤";
                }
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
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => ($err == '200' ? null : $error_code[$err]), 'result' => $data]);
    }

    /*
     * step3 訂單完成後，TapPay 確認交易狀態
     */
    public function tapPayNotify(Request $request)
    {
        $data['order_no'] = $request['order_number'];
        $data['rec_trade_id'] = $request['rec_trade_id'];
        $data['amount'] = ($request['amount'] ? $request['amount'] : 0);
        $data['status'] = $request['status'];
        $data['payment_type'] = 'PAY';
        $data['response_info'] = $request->getContent();
        $data['ip'] = $request->getClientIp();
        $result = $this->apiTapPay->tapPayNotifyLog($data);

        return response()->json(['status' => $result]);
    }

    /*
     * 取得TapPay的APP ID & KEY
     */
    public function tapPayApp()
    {
        $data['APP_ID'] = config('tappay.app_id');
        $data['APP_KEY'] = config('tappay.app_key');
        return $data;
    }
}
