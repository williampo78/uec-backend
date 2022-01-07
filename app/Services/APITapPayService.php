<?php


namespace App\Services;

use App\Models\TmpTapPay;
class APITapPayService
{

    /*
     * 取得 api url
     * @return string
     */
    public function getURL()
    {
        if (config('uec.isTesting')) {
            return 'https://sandbox.tappaysdk.com/tpc';
        } else {
            return 'https://prod.tappaysdk.com/tpc';
        }
    }

    /*
     * Pay by Prime API
     * method: POST
     * @return json
     */
    public function payByPrime($input)
    {

        $data = [];
        $data['prime'] = $input['prime'];
        $data['partner_key'] = env('TAP_PAY_PARTNER_KEY');
        $data['merchant_id'] = config('uec.merchant_id')[$input['payment_method']];
        $data['amount'] = $input['paid_amount'];
        $data['currency'] = 'TWD';
        $data['details'] = '';
        $buyer['phone_number'] = $input['buyer_mobile'];
        $buyer['name'] = $input['buyer_name'];
        $buyer['email'] = $input['buyer_email'];
        $data['cardholder'] = ($buyer);
        $data['order_number'] = $input['order_no'];
        $data['remember'] = false;
        $data['three_domain_secure'] = true;
        $url['frontend_redirect_url'] = env('TAP_PAY_RESULT_URL');
        $url['backend_notify_url'] = env('TAP_PAY_NOTIFY_URL');
        $data['result_url'] = ($url);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/payment/pay-by-prime',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: '.env('TAP_PAY_PARTNER_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }


    /*
     * TapPay backend_notify 3D驗證交易完成後進行通知
     * method: POST
     * @return json
     */
    public function tapPayNotifyLog($input)
    {
        $log_id = TmpTapPay::insertGetId($input);
        $tap = TmpTapPay::where('id', '=', $log_id)->first();
        $test = explode('\r\n',$tap->info);
        dd($test);
    }

}
