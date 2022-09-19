<?php

namespace App\Services;

use App\Models\LookupValuesV;
use App\Models\Quotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniversalService
{
    public function __construct()
    {
    }

    /**
     * 取得單號
     * @param $type '單號類型 , quotation:報價單 , order_supplier:採購單
     * @param $in 附加資訊 預設是null
     * @return string
     */
    public function getDocNumber($type, $in = array())
    {
        $dt = Carbon::now()->format('ymd');
        switch ($type) {
            case 'quotation':
                $table = 'quotation';
                $title = 'QU';
                $numberKey = 'doc_number';
                break;
            case 'order_supplier':
                $table = 'order_supplier';
                $title = 'PO';
                $numberKey = 'number';
                break;
            case 'requisitions_purchase':
                $table = 'requisitions_purchase';
                $title = 'PR';
                $numberKey = 'number';

                break;
            case 'products':
                $stock_type = $in['stock_type'];
                $title = $stock_type;
                $table = 'products';
                break;
        }

        //商品與其他不同
        switch ($type) {
            case 'products':
                $doc_number = $title . str_pad($in['id'], 6, "0", STR_PAD_LEFT);
                break;
            default:
                $rs = DB::table($table)->select(DB::raw('SUBSTRING(max(' . $numberKey . '), 9) as new_number'))
                    ->where($numberKey, 'like', $title . $dt . '%')
                    ->first();
                if ($rs->new_number == null) {
                    $add_doc_number = str_pad(1, 4, "0", STR_PAD_LEFT);
                } else {
                    $add_doc_number = str_pad($rs->new_number += 1, 4, "0", STR_PAD_LEFT);
                }
                $doc_number = $title . $dt . str_pad($add_doc_number, 4, "0", STR_PAD_LEFT);
                break;
        }

        return $doc_number;
    }

    public function idtokey($data)
    {
        $rs = [];

        foreach ($data as $v) {
            $rs[$v['id']] = $v;
        }

        return $rs;
    }

    public function getStatusCode()
    {
        $data = [
            'DRAFTED' => '草稿',
            'REVIEWING' => '簽核中',
            'APPROVED' => '已核准',
            'REJECTED' => '已駁回',
        ];

        return $data;
    }

    public function getTaxList()
    {
        return config('uec.tax_option');
    }

    /*
     * 取得使用者資料
     * Sample:
     * Author: Rowena
     */
    public function getUser()
    {
        $agent_id = Auth::user()->agent_id;
        $users = User::where('agent_id', $agent_id)->where('active', 1)->get();
        $data = [];
        foreach ($users as $k => $v) {
            $data[$v['id']] = $v;
        }
        return $data;
    }

    /*
     * 傳入分類代碼 顯示類別名稱
     * Sample: QA_CATEGORY or FOOTER_CATEGORY or DONATED_INSTITUTION
     * Author: Rowena
     */
    public function getLookupValues($category)
    {
        $lookup = LookupValuesV::where('type_code', '=', $category)->where('active', '=', '1')->orderBy('sort', 'ASC')->get();
        $data = [];
        foreach ($lookup as $k => $v) {
            $data[$v['code']] = $v['description'];
        }
        return $data;
    }

    /*
     * 取得FOOTER 類型
     * Author: Rowena
     * Return: string
     */
    public function getFooterContentTarget()
    {
        $data = [
            'S' => '站內連結',
            'B' => '另開視窗',
            'H' => '單一圖文',
        ];

        return $data;
    }

    /*
     * 取得付款 類型
     * Author: Rowena
     * Return: string
     */
    public function getPaymentType()
    {
        $data = [
            'TAPPAY_CREDITCARD' => '信用卡',
            'TAPPAY_LINEPAY' => 'LINE Pay',
            'TAPPAY_JKOPAY' => '街口支付',
            'TAPPAY_INSTAL' => '分期付款'
        ];

        return $data;
    }

    /*
     * 取得配送 類型
     * Author: Rowena
     * Return: string
     */
    public function getDeliveryType()
    {
        $data = [
            'HOME' => '宅配',
            'FAMILY' => '全家取貨',
            'STORE' => '門市取貨',
        ];

        return $data;
    }

    /*
     * 取得Lookup_values_v
     * Author: Rowena
     * Return: string
     */
    public function getLookUp($params = null)
    {
        $result = LookupValuesV::select('*')
            ->where('type_code', $params)
            ->where('active', 1)->get();
        $data = [];
        foreach ($result as $k => $v) {
            $data[$v['code']] = $v;
        }
        return $data;
    }

    /*
     * 截斷引號
     * Author: Rowena
     * Return: string
     */
    public function handleAddslashes($params = null)
    {
        $len = strlen($params);
        if ($len == 0) return "";
        if (strncmp($params, "'", 1) === 0 && substr($params, $len - 1) == "'") return $params; // already quoted
        return addslashes($params);
    }

    /*
     * 訂單狀態
     * params: 'CREATED', 'PENDING'
     * Author: Rowena
     * Return: string
     */
    public function getOrderStatus(string $orderStatus, string $payStatus, $refundStatus)
    {
        $result = null;
        if ($orderStatus == 'CREATED') {
            switch ($payStatus) {
                case 'PENDING':
                    $result = '待付款';
                    break;
                case 'FAILED':
                    $result = '付款失敗';
                    break;
                case 'COMPLETED':
                    $result = '已付款';
                    break;
            }
        } else if ($orderStatus == 'VOIDED') {
            $result = '訂單作廢';
        } else if ($orderStatus == 'CANCELLED') {
            switch ($refundStatus) {
                case 'COMPLETED':
                    $result = '已退款';
                    break;
                default:
                    $result = '已取消';
                    break;
            }
        }
        return $result;
    }

}
