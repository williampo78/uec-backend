<?php

namespace App\Services;


use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QuotationService
{
    public function __construct()
    {
    }

    public function getQuotation()
    {
        $agent_id = Auth::user()->agent_id;
        return Quotation::where('agent_id' , $agent_id)->get();
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
}
