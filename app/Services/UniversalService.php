<?php

namespace App\Services;

use App\Models\Quotation;
use Carbon\Carbon;

class UniversalService
{
    public function __construct()
    {
    }

    public function getDocNumber(){
        $dt = Carbon::now()->format('ymd');
        $quotation = Quotation::first();
        if (empty($serial)){
            $serial = 1;
        }else{
            $serial = $quotation->id + 1;
        }

        $doc_number = 'QU' . $dt . str_pad($serial,4,"0",STR_PAD_LEFT);;

        return $doc_number;
    }

    public function idtokey($data){
        $rs = [];

        foreach ($data as $v){
            $rs[$v['id']] = $v;
        }

        return $rs;
    }
}
