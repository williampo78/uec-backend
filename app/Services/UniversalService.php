<?php

namespace App\Services;

use App\Models\Lookup_values_v;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

class UniversalService
{
    public function __construct()
    {
    }

    /**
     * 取得單號
     * @param $type '單號類型 , quotation:報價單 , order_supplier:採購單
     * @return string
     */
    public function getDocNumber($type){
        $dt = Carbon::now()->format('ymd');
        switch ($type){
            case 'quotation':
                $table = 'quotation';
                $title = 'QU';
                break;
            case 'order_supplier':
                $table = 'order_supplier';
                $title = 'PO';
                break;
            case 'requisitions_purchase':
                $table = 'requisitions_purchase' ;
                $title = 'PR' ;
                break;
        }
        $rs = DB::table($table)->orderBy('id','DESC')->first();

        if (empty($rs)){
            $serial = 1;
        }else{
            $serial = $rs->id + 1;
        }

        $doc_number = $title . $dt . str_pad($serial,4,"0",STR_PAD_LEFT);;

        return $doc_number;
    }

    public function idtokey($data){
        $rs = [];

        foreach ($data as $v){
            $rs[$v['id']] = $v;
        }

        return $rs;
    }

    public function getStatusCode(){
        $data =  [
            'DRAFTED' => '草稿' ,
            'REVIEWING' => '簽核中' ,
            'APPROVED' => '已核准' ,
            'REJECTED' => '已駁回'
        ];

        return $data;
    }

    public function getTaxList(){
        return [
            0 => '未稅' ,
            1 => '應稅' ,
            2 => '內含' ,
            3 => '零稅率'
        ];
    }

    /*
     * 取得使用者資料
     * Sample:
     * Author: Rowena
     */
    public function getUser()
    {
        $agent_id = Auth::user()->agent_id;
        $users = Users::where('agent_id', $agent_id)->where('active', 1)->get();
        $data = [];
        foreach ($users as $k => $v) {
            $data[$v['id']] = $v;
        }
        return $data;
    }

    /*
     * 傳入分類代碼 顯示類別名稱
     * Sample: QA_CATEGORY or FOOTER_CATEGORY
     * Author: Rowena
     */
    public function getFooterCategory($category)
    {
        $lookup = Lookup_values_v::where('type_code', '=', $category)->where('active', '=', '1')->orderBy('sort', 'ASC')->get();
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
        $data =  [
            'S' => '站內連結' ,
            'B' => '另開視窗' ,
            'H' => '單一圖文'
        ];

        return $data;
    }

}
