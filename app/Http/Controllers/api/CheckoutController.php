<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\APICartServices;
use App\Services\APIOrderService;
use App\Services\APIProductServices;
use App\Services\APIService;
use App\Services\APITapPayService;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    private $universalService;
    private $apiService;
    private $apiProductServices;
    private $apiCartService;
    private $apiOrderService;
    private $apiTapPay;

    public function __construct(
        UniversalService $universalService,
        APIService $apiService,
        APIProductServices $apiProductServices,
        APICartServices $apiCartService,
        APIOrderService $apiOrderService,
        APITapPayService $apiTapPay
    )
    {
        $this->universalService = $universalService;
        $this->apiService = $apiService;
        $this->apiProductServices = $apiProductServices;
        $this->apiCartService = $apiCartService;
        $this->apiOrderService = $apiOrderService;
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
            'required' => '不能為空',
            'numeric' => '必須為數值',
            'in' => '購物車類型必須為: :values',
            'string' => '購物車類型必須為: string',
        ];

        $v = Validator::make($request->all(), [
            'shipping_fee' => 'required|numeric',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'stock_type' => (config('uec.cart_billing_split') == 1 ? 'required|string|in:dradvice,supplier' : 'nullable'),
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $member_id = Auth::guard('api')->user()->member_id;
        $campaign = $this->apiProductServices->getPromotion('product_card');
        $campaign_gift = $this->apiProductServices->getCampaignGift();
        $campaign_discount = $this->apiProductServices->getCampaignDiscount();
        $stock_type = ($request->stock_type == "supplier" ? "supplier" : "dradvice");
        $response = $this->apiCartService->getCartData($member_id, $campaign, $campaign_gift, $campaign_discount, null, $stock_type);
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
                if (($response['result']['totalPrice'] - ($response['result']['discount'] + abs($response['result']['thresholdAmount'])) - $point_discount) < $response['result']['feeInfo']['free_threshold']) {
                    $shipping_fee = $response['result']['feeInfo']['shipping_fee'];
                } else {
                    $shipping_fee = 0;
                }
                if ($shipping_fee == $request->shipping_fee) {
                    if (isset($response['result']['paymentMethod'])) {
                        // dd($response['result']['paymentMethod']);
                        if (in_array('TAPPAY_INSTAL', $response['result']['paymentMethod'])) {
                            //是否有符合信用卡分期門檻
                            $paid_amount = ($response['result']['totalPrice'] - ($response['result']['discount'] + abs($response['result']['thresholdAmount'])) - $point_discount + $shipping_fee);
                            $installment = $this->apiProductServices->getInstallmentAmountInterestRatesWithBank($paid_amount);
                            $installment = $this->apiProductServices->handleInstallmentInterestRates($installment, $paid_amount);
                            $installment = isset($installment['details']) ? 1 : 0; //不符合回傳0
                            $data['paymentMethod'] = $response['result']['paymentMethod'];
                            $del_key = "Del";
                            foreach ($data['paymentMethod'] as $key => $method) {
                                if ($data['paymentMethod'][$key] == 'TAPPAY_INSTAL' && $installment == 0) $del_key = $key;
                            }
                            if ($del_key != 'Del') { //將分期付款條件移除
                                array_splice($data['paymentMethod'], $del_key, 1);
                            }
                        } else {
                            $data['paymentMethod'] = array_values($response['result']['paymentMethod']);
                        }
                    } else {
                        $data['paymentMethod'] = ['TAPPAY_CREDITCARD', 'TAPPAY_LINEPAY', 'TAPPAY_JKOPAY'];
                    }
                    $status = true;
                    $data['status'] = true;
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

        switch ($request->invoice['carrier_type']) {
            // 自然人憑證條碼
            case '2':
                $carrierNoRegexRule = '|regex:/^[A-Z]{2}[0-9]{14}$/';
                break;

            // 手機條碼
            case '3':
                $carrierNoRegexRule = '|regex:/^\/[0-9A-Z\.\-\+]{7}$/';
                break;

            default:
                $carrierNoRegexRule = '';
                break;
        }
        
        $messages = [
            'string' => '資料型態必須為string',
            'integer' => '資料型態必須為integer',
            'numeric' => '資料型態必須為numeric',
            'in' => '必須存在列表中的值: :values',
            'required' => '不能為空',
            'required_if' => '不能為空',
            'max' => '不可大於:max個字元',
            'regex' => '格式錯誤',
            'exists' => '不存在輸入的值',
            'email' => 'Email格式錯誤',
            'date' => '日期格式錯誤',
        ];

        $v = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:TAPPAY_CREDITCARD,TAPPAY_LINEPAY,TAPPAY_JKOPAY,TAPPAY_INSTAL',
            'tappay_prime' => 'required|string',
            'lgst_method' => 'required|string|in:HOME,FAMILY',
            'store_no' => 'string|nullable|max:30',
            'invoice.usage' => 'required|string|in:P,D,C',
            'invoice.carrier_type' => 'required_if:invoice.usage,P|string|nullable|in:1,2,3',
            'invoice.carrier_no' => 'required_if:invoice.carrier_type,2,3|string|nullable' . $carrierNoRegexRule,
            'invoice.donated_code' => [
                'required_if:invoice.usage,D',
                'string',
                'nullable',
                Rule::exists('lookup_values_v', 'code')->where('active', 1)->where('type_code', 'DONATED_INSTITUTION'),
            ],
            'invoice.buyer_gui_number' => 'required_if:invoice.usage,C|string|nullable|regex:/^[0-9]{8}$/',
            'invoice.buyer_title' => 'required_if:invoice.usage,C|string|nullable|max:100',
            'buyer.*' => 'required',
            'buyer.name' => 'string|max:50',
            'buyer.mobile' => 'string|regex:/^09[0-9]{8}$/',
            'buyer.email' => 'email:rfc,dns|string|max:50',
            'buyer.zip' => 'string|max:10',
            'buyer.city' => 'string|max:50',
            'buyer.district' => 'string|max:50',
            'buyer.address' => 'string|max:255',
            'receiver.*' => 'required',
            'receiver.name' => 'string|max:50',
            'receiver.mobile' => 'string|regex:/^09[0-9]{8}$/',
            'receiver.zip' => 'string|max:10',
            'receiver.city' => 'string|max:50',
            'receiver.district' => 'string|max:50',
            'receiver.address' => 'string|max:255',
            'total_price' => 'required|numeric',
            'cart_campaign_discount' => 'required|numeric',
            'point_discount' => 'required|numeric',
            'shipping_fee' => 'required|numeric',
            'points' => 'required|integer',
            'utm.source' => 'string|nullable|max:100',
            'utm.medium' => 'string|nullable|max:100',
            'utm.campaign' => 'string|nullable|max:100',
            'utm.sales' => 'string|nullable|max:100',
            'utm.time' => 'nullable',
            'stock_type' => (config('uec.cart_billing_split') == 1 ? 'required|string|in:dradvice,supplier' : 'nullable'),
            'installment_info.bank_id' => ($request->payment_method === 'TAPPAY_INSTAL' ? 'required|string' : 'nullable'),
            'installment_info.number_of_installments' => ($request->payment_method === 'TAPPAY_INSTAL' ? 'required|numeric' : 'nullable'),
            'installment_info.fee_of_installments' => ($request->payment_method === 'TAPPAY_INSTAL' ? 'required|numeric' : 'nullable'),
            'buyer_remark' => 'string|nullable|max:300',
        ], $messages);

        if ($v->fails()) {
            return ApiResponseHelper::failedValidation($v);
        }

        $errMsg = '';
        $member_id = Auth::guard('api')->user()->member_id;
        $campaign = $this->apiProductServices->getPromotion('product_card');
        $campaign_gift = $this->apiProductServices->getCampaignGift();
        $campaign_discount = $this->apiProductServices->getCampaignDiscount();
        $stock_type = ($request->stock_type == "supplier" ? "supplier" : "dradvice");
        $response = $this->apiCartService->getCartData($member_id, $campaign, $campaign_gift, $campaign_discount, null, $stock_type);
        $response = json_decode($response, true);
        //檢查付款方式
        if (empty($response['result']['paymentMethod']) || empty($response['result']['list'])) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => "請確認購物車資料"]);
        }

        $payment_method = in_array($request->payment_method, $response['result']['paymentMethod']);
        if (!$payment_method) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => "本購物車無此付款方式"]);
        }

        //驗算分期手續費
        if ($request->payment_method === 'TAPPAY_INSTAL') {
            $paid_amount = ($request->total_price + $request->cart_campaign_discount + $request->point_discount + $request->shipping_fee + $response['result']['thresholdAmount']);
            $installment_rate = $this->apiProductServices->getInstallmentAmountInterestRatesWithBank($paid_amount);
            $fee_of_installments = $this->apiProductServices->getInstallmentFee($installment_rate, $request->installment_info, $paid_amount);
            $response['result']['installments'] = $fee_of_installments;
            if ($request->installment_info['fee_of_installments'] != $fee_of_installments['interest_fee']) {
                return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => "分期手續費計算錯誤"]);
            }
        }
        if ($response['status'] == '404') {
            $data = [];
            $status = false;
            $err = $response['status'];
        } else {
            //Step1, 檢核金額
            $data = [];
            //使用會員點數折抵金額
            if ($request->points) {
                $points = $request->points;
            } else {
                $points = 0;
            }

            //超過最大可點數折抵
            if (abs($points) > $response['result']['point']['discountMax']) {
                $status = false;
                $err = '905';
                $errMsg = $data['points'] = "點數折抵超出本次可抵用點數";
            } elseif ($response['result']['totalPrice'] == $request->total_price && (-$response['result']['discount']) == $request->cart_campaign_discount) {
                //檢核使用點數折抵後，是否要運費
                if (abs($points) > 0) {
                    $points_discount = ($points * $response['result']['point']['exchangeRate']);
                } else {
                    $points_discount = 0;
                }

                if (($response['result']['totalPrice'] - $response['result']['discount'] - abs($points_discount) + $response['result']['thresholdAmount']) < $response['result']['feeInfo']['free_threshold']) {
                    $shipping_fee = $response['result']['feeInfo']['shipping_fee'];
                } else {
                    $shipping_fee = 0;
                }

                if ($shipping_fee == $request->shipping_fee) {
                    //Stet2, 產生訂單
                    $dataOrder = $this->apiOrderService->setOrders($response['result'], $request, $campaign, $campaign_gift, $campaign_discount);
                    switch ($dataOrder['status']) {
                        case 200:
                            $status = true;
                            $err = '200';
                            unset($dataOrder['status']);
                            $data = $dataOrder;
                            break;

                        case 404:
                            $status = false;
                            $err = '904';
                            $errMsg = $data['message'] = "會員點數扣點異常，無法成立訂單";
                            break;

                        case 402:
                            $status = false;
                            $err = '902';
                            $errMsg = $data['message'] = "第三方支付異常，無法成立訂單，" . $dataOrder['tappay_msg'];
                            break;

                        case 403:
                            $status = false;
                            $err = '903';
                            $errMsg = $data['message'] = "庫存不足，無法成立訂單";
                            break;

                        case 405:
                            $status = false;
                            $err = '906';
                            $errMsg = $data['message'] = "出貨單成立失敗";
                            break;

                        default:
                            $status = false;
                            $err = '401';
                            $errMsg = $data['message'] = "產生訂單時發生異常，無法成立訂單";
                            break;
                    }
                } else {
                    $status = false;
                    $err = '401';
                    $errMsg = $data['shipping_fee'] = "運費有誤";
                }
            } else {
                $status = false;
                $err = '401';
                if ($response['result']['totalPrice'] != $request->total_price) {
                    $errMsg = $data['total_price'] = "商品總價有誤";
                }

                if ($response['result']['discount'] != $request->cart_campaign_discount) {
                    $errMsg = $data['cart_campaign_discount'] = "滿額折抵有誤";
                }

                if ($response['result']['shippingFee'] != $request->shipping_fee) {
                    $errMsg = $data['shipping_fee'] = "運費有誤";
                }
            }
        }

        return ApiResponseHelper::message(
                                        $status,
                                        $err,
                                        ($err == '200' ? null : ($errMsg ?? $error_code[$err])),
                                        $data
        );
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

    /*
     *
     */
    public function getInstallment(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'required' => '不能為空',
            'numeric' => '必須為數值',
        ];

        $v = Validator::make($request->all(), [
            'total_price' => 'required|numeric',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }
        if ($request->total_price > 0) {
            $installment = $this->apiProductServices->getInstallmentAmountInterestRatesWithBank($request->total_price);
            $installment = $this->apiProductServices->handleInstallmentInterestRates($installment, $request->total_price);
            $data['installment'] = isset($installment['details']) ? $installment['details'] : [];
        } else {
            $data['installment'] = [];
        }
        return response()->json(['status' => 200, 'error_code' => $err, 'error_msg' => ($err == '200' ? null : $error_code[$err]), 'result' => $data]);
    }
}
