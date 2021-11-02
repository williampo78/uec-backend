<?php


namespace App\Services;


class APIService
{


    /**
     * 取得錯誤代碼
     * @param
     * @return string
     */
    public function getErrorCode()
    {
        $code = [
            '' => null,
            '201' => '傳入無效的參數',
            '202' => '無效的Token',
            '203' => '無效的請求方式'
        ];
        return $code;
    }
}
