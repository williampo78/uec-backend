<?php

namespace App\Services;

use App\Models\Quotation;
use Carbon\Carbon;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

class UniversalService
{
    public function __construct()
    {
    }

    public function getDocNumber()
    {
        $dt = Carbon::now()->format('ymd');
        $quotation = Quotation::orderBy('id', 'DESC')->first();
        if (empty($quotation)) {
            $serial = 1;
        } else {
            $serial = $quotation->id + 1;
        }

        $doc_number = 'QU' . $dt . str_pad($serial, 4, "0", STR_PAD_LEFT);;

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
            'REJECTED' => '已駁回'
        ];

        return $data;
    }

    public function getTaxList()
    {
        return [
            0 => '未稅',
            1 => '應稅',
            2 => '內含',
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
