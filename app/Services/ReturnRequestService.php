<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\ReturnRequest;

class ReturnRequestService
{
    /**
     * 產生退貨申請單號
     *
     * @return string
     */
    public function generateRequestNo() :string
    {
        $now = Carbon::now();

        do {
            $random_string = Str::upper(Str::random(6));
            $request_no = 'RT' . $now->format('ymd') . $random_string;
        } while ($this->requestNoExists($request_no));

        return $request_no;
    }

    /**
     * 退貨申請單號是否已存在
     *
     * @param string $request_no
     * @return boolean
     */
    public function requestNoExists(string $request_no) :bool
    {
        $return_request_count = ReturnRequest::where('request_no', $request_no)->count();

        if ($return_request_count < 1) {
            return false;
        }

        return true;
    }
}
