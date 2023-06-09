<?php


namespace App\Services;

use App\Models\TapPayPayLog;
use App\Models\OrderPayment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\WarehouseStock;
use App\Models\StockTransactionLog;
use App\Services\StockService;
use App\Services\APIService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class APITapPayService
{
    public function __construct(StockService $stockService, APIService $APIService)
    {
        $this->stockService = $stockService;
        $this->apiService = $APIService;
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
        if ($input['payment_method'] == 'TAPPAY_CREDITCARD') {
            $data['three_domain_secure'] = true;
        }
        if ($input['payment_method'] == "TAPPAY_INSTAL") {
            $data['instalment'] = $input['number_of_instal'];
            $data['three_domain_secure'] = true;
            $data['remember'] = true;
        }
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
        $status = false;
        $pay_log_id = null;

        DB::beginTransaction();
        try {
            //先把TapPay回傳的資料都寫入
            $pay_log_id = TapPayPayLog::insertGetId($input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
        }

        if ($pay_log_id != null) {
            DB::beginTransaction();
            try {
                $info = TapPayPayLog::where('id', '=', $pay_log_id)->first();
                $orderPayment = $this->getOrderPayment($info);
                //交易代碼status成功時才檢查回傳交易資料跟訂單是否符合
                //調整為成立訂單時就產生出貨單
                if ($input['status'] == 0) {
                    if ($orderPayment) { //符合資料後，查詢tappay的交易紀錄
                        $data['partner_key'] = config('tappay.partner_key');
                        $data['filters'] = array('rec_trade_id' => $info->rec_trade_id);
                        $record = $this->tradeRecords($data);
                        $record = json_decode($record, true);
                        if ($record['status'] == 2) { //tappay打回來的最後資料
                            //檢查該筆資料的交易紀錄的狀態
                            foreach ($record['trade_records'] as $trade) {
                                if ($trade['record_status'] == 1 || $trade['record_status'] == 0) { //交易完成..更新金流狀態
                                    if (config('uec.cart_p_discount_split') == 1) {
                                        $result = $this->updateOrder($orderPayment);
                                    }
                                }
                            }
                        }
                        if ($result) {
                            DB::commit();
                            $status = true;
                        } else {
                            Log::channel('tappay_api_log')->error('3D驗證交易完成，更新金流狀態失敗 ! rec_trade_id :' . $info->rec_trade_id);
                            DB::rollBack();
                            $status = false;
                        }
                    } else {
                        //等排程檢查出貨
                        DB::rollBack();
                        Log::info($e->getMessage());
                        $status = false;
                    }
                } elseif (($input['status'] == 924 || $input['status'] == 925 || $input['status'] == 10003) ||
                    ($input['status'] == 10023 && (isset($input['bank_result_code']) && ($input['bank_result_code'] == 1292 || $input['bank_result_code'] == 'L140' || $input['bank_result_code'] == 'M040')))) {
                    if ($orderPayment) { //符合資料後，查詢tappay的交易紀錄
                        $result = $this->updateOrderFailed($orderPayment);
                        if ($result) {
                            DB::commit();
                            $status = true;
                        } else {
                            Log::channel('tappay_api_log')->error('授權失敗! rec_trade_id :' . $info->rec_trade_id);
                            DB::rollBack();
                            $status = false;
                        }
                    } else {
                        DB::rollBack();
                        Log::info($e->getMessage());
                        $status = false;
                    }
                } else {
                    DB::rollBack();
                    Log::info($e->getMessage());
                    $status = false;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::info($e->getMessage());
                $status = false;
            }
        }

        return $status;
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


    /**
     * 取得訂單金流單
     * @param 訂單編號，交易編號，金額
     * @return string
     */
    public function getOrderPayment($info)
    {
        $result = OrderPayment::select('order_payments.*', 'orders.is_cash_on_delivery'
            , 'orders.receiver_name', 'orders.receiver_mobile', 'orders.receiver_zip_code'
            , 'orders.receiver_city', 'orders.receiver_district', 'orders.receiver_address'
            , 'orders.store_no', 'orders.lgst_method')
            ->where('order_payments.order_no', '=', $info->order_no)
            ->where('order_payments.rec_trade_id', '=', $info->rec_trade_id)
            ->where('order_payments.amount', '=', $info->amount)
            ->join('orders', 'orders.id', '=', 'order_payments.source_table_id')
            ->first();
        return $result;
    }

    /**
     * 更新訂單金流單與建立出貨單
     * @param 訂單編號，交易編號，金額
     * @return string
     */
    public function setShipment($data)
    {
        $status = Order::getOrder($data->source_table_id);
        if ($status['status_code'] != 'CREATED') exit;
        $now = Carbon::now();
        $random = Str::random(6);
        //商城倉庫代碼
        $warehouseCode = $this->stockService->getWarehouseConfig();
        DB::beginTransaction();
        try {
            $order = Order::where('id', '=', $data->source_table_id)->update(['pay_status' => 'COMPLETED', 'is_paid' => 1, 'paid_at' => $now]);

            //更新付款狀態
            $order_payment = OrderPayment::where('id', '=', $data->id)->update(['payment_status' => 'COMPLETED', 'latest_api_status' => 'S']);

            //建立出貨單頭
            $shipData = [];
            $shipData['agent_id'] = 1;
            $shipData['shipment_no'] = "SH" . date("ymd") . strtoupper($random);
            $shipData['shipment_date'] = $now;
            $shipData['status_code'] = 'CREATED';
            $shipData['payment_method'] = $data->payment_method;
            $shipData['is_cash_on_delivery'] = $data->is_cash_on_delivery;
            $shipData['lgst_method'] = $data->lgst_method;
            $shipData['order_id'] = $data->source_table_id;
            $shipData['order_no'] = $data->order_no;
            $shipData['total_amount'] = ($data->amount + $data->point_discount);
            $shipData['paid_amount'] = $data->amount;
            $shipData['ship_to_name'] = $data->receiver_name;
            $shipData['ship_to_mobile'] = $data->receiver_mobile;
            $shipData['ship_to_zip_code'] = $data->receiver_zip_code;
            $shipData['ship_to_city'] = $data->receiver_city;
            $shipData['ship_to_district'] = $data->receiver_district;
            $shipData['ship_to_address'] = $data->receiver_address;
            $shipData['store_no'] = $data->store_no;
            $shipData['remark'] = '';
            $shipData['created_by'] = -1;
            $shipData['created_at'] = $now;
            $shipData['updated_by'] = -1;
            $shipData['updated_at'] = $now;
            $ship_id = Shipment::insertGetId($shipData);
            $shipDetail = [];
            $logData = [];
            //出貨單單身
            $order_details = OrderDetail::getOrderDetails($data->source_table_id);
            foreach ($order_details as $detail) {
                $shipDetail['shipment_id'] = $ship_id;
                $shipDetail['seq'] = $detail->seq;
                $shipDetail['order_detail_id'] = $detail->id;
                $shipDetail['product_item_id'] = $detail->product_item_id;
                $shipDetail['item_no'] = $detail->item_no;
                $shipDetail['qty'] = $detail->qty;
                $shipDetail['created_by'] = -1;
                $shipDetail['created_at'] = $now;
                $shipDetail['updated_by'] = -1;
                $shipDetail['updated_at'] = $now;
                $shipDetail_id = ShipmentDetail::insertGetId($shipDetail);

                //庫存與LOG相關
                /* 暫留，確認庫存什麼時間點扣
                $stock = $this->stockService->getStockByItem($warehouseCode, $detail->product_item_id);
                if (isset($stock['id'])) {
                    $updStock = WarehouseStock::where('id', '=', $stock['id'])->update(['stock_qty' => ($stock['stockQty'] - $detail->qty)]);
                    if ($updStock) {
                        $logData['transaction_type'] = 'ORDER_SHIP';
                        $logData['transaction_date'] = $now;
                        $logData['warehouse_id'] = $stock['warehouse_id'];
                        $logData['product_item_id'] = $detail->product_item_id;
                        $logData['item_no'] = $detail->item_no;
                        $logData['transaction_qty'] = -$detail->qty;
                        $logData['transaction_nontax_amount'] = $detail->unit_price;
                        $logData['transaction_amount'] = $detail->unit_price;
                        $logData['source_doc_no'] = $shipData['shipment_no'];
                        $logData['source_table_name'] = 'shipment_details';
                        $logData['source_table_id'] = $shipDetail_id;
                        $logData['remark'] = '';
                        $logData['created_by'] = -1;
                        $logData['created_at'] = $now;
                        $logData['updated_by'] = -1;
                        $logData['updated_at'] = $now;
                        StockTransactionLog::insert($logData);
                    }
                }
                */
            }
            if ($order && $order_payment) {
                DB::commit();
                //$result['status'] = 200;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            //$result['status'] = 401;
        }
        //return $result;
    }

    /**
     * 更新訂單金流單與建立出貨單
     * @param 訂單編號，交易編號，金額
     * @return string
     */
    public function updateOrder($data)
    {
        $status = Order::getOrder($data->source_table_id);
        if ($status['status_code'] != 'CREATED') exit;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $order = Order::where('id', '=', $data->source_table_id)->update(['pay_status' => 'COMPLETED', 'is_paid' => 1, 'paid_at' => $now]);
            //更新付款狀態
            $order_payment = OrderPayment::where('id', '=', $data->id)->update(['payment_status' => 'COMPLETED', 'latest_api_status' => 'S']);

            if ($order && $order_payment) {
                DB::commit();
                $result = true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }
        return $result;
    }

    /**
     * 授權失敗時更新商城訂單與訂單金流單
     * @return bool
     */
    public function updateOrderFailed($data)
    {
        $result = false;
        $now = Carbon::now();

        DB::beginTransaction();
        try {
            $order = Order::where('id', '=', $data->source_table_id)
                ->update([
                    'pay_status' => 'FAILED',
                    'is_paid' => 0,
                    'paid_at' => null,
                    'updated_at' => $now,
                    'updated_by' => -1
                ]);

            $order_payment = OrderPayment::where('id', '=', $data->id)
                ->update([
                    'payment_status' => 'FAILED',
                    'latest_api_status' => 'E',
                    'updated_at' => $now,
                    'updated_by' => -1
                ]);

            if ($order && $order_payment) {
                DB::commit();
                $result = true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }
}
