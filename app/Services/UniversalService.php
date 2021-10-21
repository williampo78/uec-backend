<?php

namespace App\Services;

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

}
