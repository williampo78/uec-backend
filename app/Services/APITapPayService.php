<?php


namespace App\Services;

use App\Models\TapPayPayLog;
use App\Models\OrderPayment;

class APITapPayService
{

    /*
     * Pay by Prime API
     * method: POST
     * @return json
     */
    public function payByPrime($input)
    {

        $data = [];
        $data['prime'] = $input['prime'];
        $data['partner_key'] = config('tappay.partner_key');
        $data['merchant_id'] = config('tappay.merchant_id')[$input['payment_method']];
        $data['amount'] = $input['paid_amount'];
        $data['currency'] = 'TWD';
        $data['details'] = config('tappay.details');
        $buyer['phone_number'] = $input['buyer_mobile'];
        $buyer['name'] = $input['buyer_name'];
        $buyer['email'] = $input['buyer_email'];
        $data['cardholder'] = ($buyer);
        $data['order_number'] = $input['order_no'];
        $data['remember'] = false;
        $data['three_domain_secure'] = true;
        $url['frontend_redirect_url'] = config('tappay.frontend_redirect_url');
        $url['backend_notify_url'] = config('tappay.backend_notify_url');
        $data['result_url'] = ($url);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('tappay.url') . '/tpc/payment/pay-by-prime',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . config('tappay.partner_key'),
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
        //先把TapPay回傳的資料都寫入
        //$pay_log_id = TapPayPayLog::insertGetId($input);
        $info = TapPayPayLog::where('id', '=', 2)->first();
        //交易代碼status成功時才檢查回傳交易資料跟訂單是否符合
        $input['status'] = 0;
        if ($input['status'] == 0) {
            $result = OrderPayment::where('order_no', '=', $info->order_no)
                ->where('rec_trade_id', '=', $info->rec_trade_id)
                ->where('amount', '=', $info->amount)
                ->first();
            if ($result) { //符合資料後，查詢tappay的交易紀錄
                $data['partner_key'] = config('uec.partner_key');
                $data['filters'] = array('rec_trade_id' => $info->rec_trade_id);
                $record = self::tradeRecords($data);
                $record = json_decode($record,true);
                if ($record['status'] == 2) { //tappay打回來的最後資料
                    //檢查該筆資料的交易紀錄的狀態
                    foreach ($record['trade_records'] as $record) {
                        dd($record['trade_records']);
                    }
                }
            } else {
                return "XX";
            }
        }
    }

    /*
     * TapPay 進行查詢交易紀錄 Record API
     * method: POST
     * @return json
     * https://docs.tappaysdk.com/tutorial/zh/back.html#record-api
     */
    public function tradeRecords($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('tappay.url') . '/tpc/transaction/query',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . config('tappay.partner_key'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

}
